<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuthenticationLog;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * LDAP/Active Directory Authentication Service
 *
 * PKS 5.2.1 Compliance - Mandatory SSO Authentication
 *
 * Handles LDAP authentication, user synchronization, and password policy
 * enforcement per PKS 5.4.3 requirements.
 *
 * @see D03-FR-001.1 (Authenticated access only)
 * @see D04 §6.2 (Authentication Architecture)
 *
 * @trace Requirements 1.1, 2.3, 5.2, 27.1, 27.2, 27.3
 */
class LdapAuthenticationService
{
    /** @var array<string, mixed> */
    private array $config;

    private ?object $connection = null;

    public function __construct()
    {
        $this->config = config('ldap', []);
    }

    /**
     * Authenticate user via LDAP/Active Directory
     *
     * @param  array{remember?: bool}  $options
     */
    public function authenticate(string $username, string $password, array $options = []): ?User
    {
        // Check lockout status first
        if ($this->isLockedOut($username)) {
            $remainingMinutes = $this->getLockoutRemainingMinutes($username);

            Log::warning('LDAP authentication blocked - account locked', [
                'username' => $username,
                'lockout_remaining' => $remainingMinutes,
            ]);

            // Log the blocked attempt
            AuthenticationLog::logFailure(
                username: $username,
                authMethod: AuthenticationLog::METHOD_LDAP,
                reason: AuthenticationLog::REASON_ACCOUNT_LOCKED,
                failedAttempts: 3,
            );

            return null;
        }

        try {
            // Attempt LDAP bind
            $ldapUser = $this->ldapBind($username, $password);

            if ($ldapUser === null) {
                $failedAttempts = $this->recordFailedAttempt($username);

                Log::warning('LDAP authentication failed - invalid credentials', [
                    'username' => $username,
                    'failed_attempts' => $failedAttempts,
                ]);

                // Log the failed attempt
                AuthenticationLog::logFailure(
                    username: $username,
                    authMethod: AuthenticationLog::METHOD_LDAP,
                    reason: AuthenticationLog::REASON_INVALID_CREDENTIALS,
                    failedAttempts: $failedAttempts,
                );

                return null;
            }

            // Clear failed attempts on successful auth
            $this->clearFailedAttempts($username);

            // Sync or create local user
            $user = $this->syncUser($ldapUser);

            // Log successful authentication
            AuthenticationLog::logSuccess(
                username: $username,
                authMethod: AuthenticationLog::METHOD_LDAP,
                userId: $user->id,
            );

            Log::info('LDAP authentication successful', [
                'user_id' => $user->id,
                'username' => $username,
                'ldap_guid' => $user->ldap_guid,
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('LDAP authentication error', [
                'username' => $username,
                'error' => $e->getMessage(),
            ]);

            // Log the error
            AuthenticationLog::logFailure(
                username: $username,
                authMethod: AuthenticationLog::METHOD_LDAP,
                reason: AuthenticationLog::REASON_LDAP_ERROR,
            );

            // Check if fallback is enabled
            if ($this->isFallbackEnabled()) {
                return $this->fallbackAuthenticate($username, $password);
            }

            return null;
        }
    }

    /**
     * Bind to LDAP server and retrieve user data
     *
     * @return array<string, mixed>|null
     */
    private function ldapBind(string $username, string $password): ?array
    {
        $connectionConfig = $this->config['connections']['default'] ?? [];
        $hosts = $connectionConfig['hosts'] ?? ['localhost'];
        $port = $connectionConfig['port'] ?? 389;
        $baseDn = $connectionConfig['base_dn'] ?? '';
        $useTls = $connectionConfig['use_tls'] ?? true;

        // Determine bind DN based on username format
        $bindDn = $this->resolveBindDn($username, $baseDn);

        foreach ($hosts as $host) {
            try {
                $ldapUri = sprintf('ldap://%s:%d', $host, $port);
                $connection = @ldap_connect($ldapUri);

                if ($connection === false) {
                    continue;
                }

                // Set LDAP options
                ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
                ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, $connectionConfig['timeout'] ?? 5);

                // Start TLS if configured
                if ($useTls) {
                    @ldap_start_tls($connection);
                }

                // Attempt bind
                $bound = @ldap_bind($connection, $bindDn, $password);

                if ($bound === false) {
                    ldap_unbind($connection);

                    continue;
                }

                // Search for user attributes
                $userData = $this->searchUser($connection, $username, $baseDn);

                ldap_unbind($connection);

                if ($userData !== null) {
                    return $userData;
                }
            } catch (\Exception $e) {
                Log::debug('LDAP connection attempt failed', [
                    'host' => $host,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }
        }

        return null;
    }

    /**
     * Resolve the bind DN from username
     */
    private function resolveBindDn(string $username, string $baseDn): string
    {
        // If username contains @, it's a UPN
        if (str_contains($username, '@')) {
            return $username;
        }

        // If username contains \, it's domain\username format
        if (str_contains($username, '\\')) {
            return $username;
        }

        // Otherwise, construct DN
        return sprintf('cn=%s,%s', $username, $baseDn);
    }

    /**
     * Search for user in LDAP directory
     *
     * @return array<string, mixed>|null
     */
    private function searchUser(mixed $connection, string $username, string $baseDn): ?array
    {
        $authConfig = $this->config['authentication'] ?? [];
        $filter = $authConfig['filter'] ?? '(&(objectClass=user)(objectCategory=person))';
        $attributeMap = $this->config['user_attributes'] ?? [];

        // Build search filter
        $searchFilter = str_contains($username, '@')
            ? sprintf('(&%s(mail=%s))', $filter, ldap_escape($username, '', LDAP_ESCAPE_FILTER))
            : sprintf('(&%s(sAMAccountName=%s))', $filter, ldap_escape($username, '', LDAP_ESCAPE_FILTER));

        $attributes = array_values($attributeMap);
        $search = @ldap_search($connection, $baseDn, $searchFilter, $attributes);

        if ($search === false) {
            return null;
        }

        $entries = ldap_get_entries($connection, $search);

        if ($entries === false || $entries['count'] === 0) {
            return null;
        }

        $entry = $entries[0];

        // Map LDAP attributes to user data
        $userData = [];
        foreach ($attributeMap as $localKey => $ldapKey) {
            $value = $entry[strtolower($ldapKey)][0] ?? null;

            // Handle binary GUID
            if ($localKey === 'guid' && $value !== null) {
                $value = $this->convertGuidToString($value);
            }

            $userData[$localKey] = $value;
        }

        // Get group memberships
        $userData['groups'] = $this->getUserGroups($entry);

        return $userData;
    }

    /**
     * Convert binary GUID to string format
     */
    private function convertGuidToString(string $binaryGuid): string
    {
        $hex = bin2hex($binaryGuid);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 6, 2).substr($hex, 4, 2).substr($hex, 2, 2).substr($hex, 0, 2),
            substr($hex, 10, 2).substr($hex, 8, 2),
            substr($hex, 14, 2).substr($hex, 12, 2),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    /**
     * Extract group memberships from LDAP entry
     *
     * @param  array<string, mixed>  $entry
     * @return array<int, string>
     */
    private function getUserGroups(array $entry): array
    {
        $groups = [];
        $memberOf = $entry['memberof'] ?? [];

        if (! is_array($memberOf)) {
            return $groups;
        }

        foreach ($memberOf as $key => $dn) {
            if ($key === 'count' || ! is_string($dn)) {
                continue;
            }

            // Extract CN from DN
            if (preg_match('/^CN=([^,]+)/i', $dn, $matches)) {
                $groups[] = $matches[1];
            }
        }

        return $groups;
    }

    /**
     * Sync LDAP user data to local database
     *
     * @param  array<string, mixed>  $ldapUser
     */
    private function syncUser(array $ldapUser): User
    {
        $email = $ldapUser['email'] ?? null;
        $guid = $ldapUser['guid'] ?? null;

        // Find existing user by LDAP GUID or email
        $user = User::where('ldap_guid', $guid)
            ->orWhere('email', $email)
            ->first();

        $userData = [
            'name' => $ldapUser['name'] ?? $ldapUser['username'] ?? 'Unknown',
            'email' => $email,
            'ldap_guid' => $guid,
            'staff_id' => $ldapUser['staff_id'] ?? null,
            'phone' => $ldapUser['phone'] ?? null,
            'mobile' => $ldapUser['mobile'] ?? null,
            'is_active' => true,
            'email_verified_at' => now(),
        ];

        if ($user === null) {
            // Create new user
            $authConfig = $this->config['authentication'] ?? [];
            if (! ($authConfig['create_user'] ?? true)) {
                throw new \Exception('User creation disabled - user not found in local database');
            }

            $userData['password'] = Hash::make(Str::random(32));
            $userData['role'] = $this->mapGroupsToRole($ldapUser['groups'] ?? []);

            $user = User::create($userData);

            Log::info('LDAP user created', [
                'user_id' => $user->id,
                'email' => $email,
                'ldap_guid' => $guid,
            ]);
        } else {
            // Update existing user
            $authConfig = $this->config['authentication'] ?? [];
            if ($authConfig['sync_on_login'] ?? true) {
                $user->update($userData);
            }

            // Update LDAP GUID if not set
            if ($user->ldap_guid === null && $guid !== null) {
                $user->update(['ldap_guid' => $guid]);
            }
        }

        return $user;
    }

    /**
     * Map LDAP groups to application role
     *
     * @param  array<int, string>  $groups
     */
    private function mapGroupsToRole(array $groups): string
    {
        $groupMapping = $this->config['group_mapping'] ?? [];

        // Check groups in priority order
        $rolePriority = ['superuser', 'admin', 'approver', 'staff'];

        foreach ($rolePriority as $role) {
            foreach ($groupMapping as $groupName => $mappedRole) {
                if ($mappedRole === $role && in_array($groupName, $groups, true)) {
                    return $role;
                }
            }
        }

        return 'staff';
    }

    /**
     * Check if account is locked out
     */
    public function isLockedOut(string $username): bool
    {
        $key = $this->getLockoutKey($username);
        $lockoutUntil = Cache::get($key);

        if ($lockoutUntil === null) {
            return false;
        }

        if (now()->timestamp >= $lockoutUntil) {
            Cache::forget($key);
            Cache::forget($this->getFailedAttemptsKey($username));

            return false;
        }

        return true;
    }

    /**
     * Get remaining lockout time in minutes
     */
    public function getLockoutRemainingMinutes(string $username): int
    {
        $key = $this->getLockoutKey($username);
        $lockoutUntil = Cache::get($key);

        if ($lockoutUntil === null) {
            return 0;
        }

        $remaining = $lockoutUntil - now()->timestamp;

        return max(0, (int) ceil($remaining / 60));
    }

    /**
     * Record a failed authentication attempt
     *
     * @return int The current number of failed attempts
     */
    private function recordFailedAttempt(string $username): int
    {
        $key = $this->getFailedAttemptsKey($username);
        $attempts = (int) Cache::get($key, 0) + 1;

        $authConfig = $this->config['authentication'] ?? [];
        $passwordPolicy = $authConfig['password_policy'] ?? [];
        $threshold = $passwordPolicy['lockout_threshold'] ?? 3;
        $lockoutMinutes = $passwordPolicy['lockout_duration_minutes'] ?? 30;

        Cache::put($key, $attempts, now()->addMinutes($lockoutMinutes));

        if ($attempts >= $threshold) {
            $lockoutKey = $this->getLockoutKey($username);
            $lockoutUntil = now()->addMinutes($lockoutMinutes);
            Cache::put($lockoutKey, $lockoutUntil->timestamp, $lockoutUntil);

            // Log the lockout event
            AuthenticationLog::logLockout(
                username: $username,
                authMethod: AuthenticationLog::METHOD_LDAP,
                failedAttempts: $attempts,
                lockoutUntil: $lockoutUntil,
            );

            Log::warning('LDAP account locked due to failed attempts', [
                'username' => $username,
                'attempts' => $attempts,
                'lockout_minutes' => $lockoutMinutes,
            ]);
        }

        return $attempts;
    }

    /**
     * Clear failed authentication attempts
     */
    private function clearFailedAttempts(string $username): void
    {
        Cache::forget($this->getFailedAttemptsKey($username));
        Cache::forget($this->getLockoutKey($username));
    }

    private function getFailedAttemptsKey(string $username): string
    {
        return 'ldap:failed_attempts:'.md5($username);
    }

    private function getLockoutKey(string $username): string
    {
        return 'ldap:lockout:'.md5($username);
    }

    /**
     * Check if fallback authentication is enabled
     */
    private function isFallbackEnabled(): bool
    {
        $fallback = $this->config['fallback'] ?? [];

        return (bool) ($fallback['enabled'] ?? false);
    }

    /**
     * Fallback to local database authentication
     */
    private function fallbackAuthenticate(string $username, string $password): ?User
    {
        Log::info('LDAP fallback authentication attempted', ['username' => $username]);

        $user = User::where('email', $username)
            ->orWhere('staff_id', $username)
            ->first();

        if ($user === null) {
            return null;
        }

        if (! Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }

    /**
     * Validate user exists in LDAP
     */
    public function validateUser(string $username): bool
    {
        $cacheKey = 'ldap:user_exists:'.md5($username);

        return (bool) Cache::remember($cacheKey, 300, function () use ($username): bool {
            try {
                $connectionConfig = $this->config['connections']['default'] ?? [];
                $hosts = $connectionConfig['hosts'] ?? ['localhost'];
                $port = $connectionConfig['port'] ?? 389;
                $baseDn = $connectionConfig['base_dn'] ?? '';
                $bindUsername = $connectionConfig['username'] ?? null;
                $bindPassword = $connectionConfig['password'] ?? null;

                if ($bindUsername === null || $bindPassword === null) {
                    return false;
                }

                foreach ($hosts as $host) {
                    $ldapUri = sprintf('ldap://%s:%d', $host, $port);
                    $connection = @ldap_connect($ldapUri);

                    if ($connection === false) {
                        continue;
                    }

                    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);

                    $bound = @ldap_bind($connection, $bindUsername, $bindPassword);

                    if ($bound === false) {
                        ldap_unbind($connection);

                        continue;
                    }

                    $userData = $this->searchUser($connection, $username, $baseDn);
                    ldap_unbind($connection);

                    return $userData !== null;
                }

                return false;
            } catch (\Exception $e) {
                Log::error('LDAP user validation error', [
                    'username' => $username,
                    'error' => $e->getMessage(),
                ]);

                return false;
            }
        });
    }

    /**
     * Get LDAP connection health status
     *
     * @return array<string, mixed>
     */
    public function getHealthStatus(): array
    {
        try {
            $connectionConfig = $this->config['connections']['default'] ?? [];
            $hosts = $connectionConfig['hosts'] ?? ['localhost'];
            $port = $connectionConfig['port'] ?? 389;

            foreach ($hosts as $host) {
                $ldapUri = sprintf('ldap://%s:%d', $host, $port);
                $connection = @ldap_connect($ldapUri);

                if ($connection === false) {
                    continue;
                }

                ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, 3);

                // Anonymous bind test
                $bound = @ldap_bind($connection);
                ldap_unbind($connection);

                return [
                    'status' => 'healthy',
                    'host' => $host,
                    'port' => $port,
                    'last_checked' => now()->toIso8601String(),
                ];
            }

            return [
                'status' => 'unhealthy',
                'error' => 'No LDAP hosts available',
                'last_checked' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'last_checked' => now()->toIso8601String(),
            ];
        }
    }

    /**
     * Get password policy configuration
     *
     * PKS 5.4.3 Compliance - Password Policy Display
     *
     * @return array<string, mixed>
     */
    public function getPasswordPolicy(): array
    {
        $authConfig = $this->config['authentication'] ?? [];
        $policy = $authConfig['password_policy'] ?? [];

        return [
            'min_length' => $policy['min_length'] ?? 8,
            'max_age_days' => $policy['max_age_days'] ?? 90,
            'lockout_threshold' => $policy['lockout_threshold'] ?? 3,
            'lockout_duration_minutes' => $policy['lockout_duration_minutes'] ?? 30,
        ];
    }

    /**
     * Get password policy messages in Bahasa Melayu
     *
     * PKS 5.4.3 Compliance - Localized Policy Display
     *
     * @return array<string, string>
     */
    public function getPasswordPolicyMessages(): array
    {
        return AuthenticationLog::getPasswordPolicyMessages();
    }

    /**
     * Get authentication error message in Bahasa Melayu
     */
    public function getAuthErrorMessage(string $username): string
    {
        if ($this->isLockedOut($username)) {
            $minutes = $this->getLockoutRemainingMinutes($username);

            return str_replace(
                ':minutes',
                (string) $minutes,
                'Akaun anda telah dikunci kerana terlalu banyak percubaan log masuk yang gagal. Sila cuba lagi selepas :minutes minit.'
            );
        }

        $attempts = (int) Cache::get($this->getFailedAttemptsKey($username), 0);
        $threshold = $this->config['authentication']['password_policy']['lockout_threshold'] ?? 3;
        $remaining = $threshold - $attempts;

        if ($remaining > 0) {
            return sprintf(
                'Nama pengguna atau kata laluan tidak sah. Anda mempunyai %d percubaan lagi sebelum akaun dikunci.',
                $remaining
            );
        }

        return 'Pengesahan gagal. Sila hubungi pentadbir sistem.';
    }
}
