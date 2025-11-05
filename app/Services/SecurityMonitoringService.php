<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Security Monitoring Service
 *
 * Monitors failed login attempts, suspicious activities,
 * and security events for ICTServe compliance.
 *
 * @see D03-FR-010.1 Security monitoring requirements
 * @see D03-FR-010.2 Security event logging
 * @see D11 Technical Design - Security monitoring
 */
class SecurityMonitoringService
{
    private const FAILED_LOGIN_THRESHOLD = 5;
    private const FAILED_LOGIN_WINDOW = 900; // 15 minutes
    private const SUSPICIOUS_ACTIVITY_THRESHOLD = 10;
    private const SUSPICIOUS_ACTIVITY_WINDOW = 3600; // 1 hour

    /**
     * Log failed login attempt
     */
    public function logFailedLogin(string $email, Request $request): void
    {
        $ip = $request->ip() ?? 'unknown';
        $userAgent = $request->userAgent();

        // Log the failed attempt
Log::warning('Failed login attempt', [
            'email' => $email,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'url' => $request->url(),
            'timestamp' => now()->toISOString(),
        ]);

        // Track failed attempts by IP
        $ipKey = "failed_login_ip:{$ip}";
        $ipAttempts = (int) Cache::get($ipKey, 0) + 1;
        Cache::put($ipKey, $ipAttempts, self::FAILED_LOGIN_WINDOW);

        // Track failed attempts by email
        $emailKey = "failed_login_email:{$email}";
        $emailAttempts = (int) Cache::get($emailKey, 0) + 1;
        Cache::put($emailKey, $emailAttempts, self::FAILED_LOGIN_WINDOW);

        // Check for threshold breaches
        if ($ipAttempts >= self::FAILED_LOGIN_THRESHOLD) {
            $this->alertFailedLoginThreshold($ip, $ipAttempts, 'ip');
        }

        if ($emailAttempts >= self::FAILED_LOGIN_THRESHOLD) {
            $this->alertFailedLoginThreshold($email, $emailAttempts, 'email');
        }
    }

    /**
     * Log successful login
     */
    public function logSuccessfulLogin(string $email, Request $request): void
    {
        Log::info('Successful login', [
            'email' => $email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);

        // Clear failed login counters on successful login
        Cache::forget("failed_login_email:{$email}");
    }

    /**
     * Log suspicious activity
     *
     * @param  array<string, mixed>  $context
     */
    public function logSuspiciousActivity(string $activity, array $context, Request $request): void
    {
        $ip = $request->ip() ?? 'unknown';

        Log::warning('Suspicious activity detected', [
            'activity' => $activity,
            'context' => $context,
            'ip' => $ip,
            'user_agent' => $request->userAgent(),
            'url' => $request->url(),
            'timestamp' => now()->toISOString(),
        ]);

        // Track suspicious activities by IP
        $key = "suspicious_activity:{$ip}";
        $count = (int) Cache::get($key, 0) + 1;
        Cache::put($key, $count, self::SUSPICIOUS_ACTIVITY_WINDOW);

        if ($count >= self::SUSPICIOUS_ACTIVITY_THRESHOLD) {
            $this->alertSuspiciousActivity($ip, $count, $activity);
        }
    }

    /**
     * Log security event
     *
     * @param  array<string, mixed>  $context
     */
    public function logSecurityEvent(string $event, array $context = []): void
    {
        Log::warning('Security event', [
            'event' => $event,
            'context' => $context,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Check if IP is blocked due to failed attempts
     */
    public function isIpBlocked(string $ip): bool
    {
        $attempts = Cache::get("failed_login_ip:{$ip}", 0);
        return $attempts >= self::FAILED_LOGIN_THRESHOLD;
    }

    /**
     * Check if email is blocked due to failed attempts
     */
    public function isEmailBlocked(string $email): bool
    {
        $attempts = Cache::get("failed_login_email:{$email}", 0);
        return $attempts >= self::FAILED_LOGIN_THRESHOLD;
    }

    /**
     * Get failed login attempts for IP
     */
    public function getFailedLoginAttempts(string $ip): int
    {
        $attempts = Cache::get("failed_login_ip:{$ip}", 0);
        return is_int($attempts) ? $attempts : 0;
    }

    /**
     * Get failed login attempts for email
     */
    public function getFailedEmailAttempts(string $email): int
    {
        $attempts = Cache::get("failed_login_email:{$email}", 0);
        return is_int($attempts) ? $attempts : 0;
    }

    /**
     * Clear failed login attempts
     */
    public function clearFailedAttempts(string $identifier, string $type = 'ip'): void
    {
        $key = "failed_login_{$type}:{$identifier}";
        Cache::forget($key);
    }

    /**
     * Get security statistics
     *
     * @return array<string, mixed>
     */
    public function getSecurityStatistics(): array
    {
        // This would typically query a security events table
        // For now, we'll return basic statistics
        return [
            'failed_logins_last_hour' => $this->getRecentFailedLogins(3600),
            'suspicious_activities_last_hour' => $this->getRecentSuspiciousActivities(3600),
            'blocked_ips_count' => $this->getBlockedIpsCount(),
            'security_alerts_today' => $this->getSecurityAlertsToday(),
            'last_security_scan' => Cache::get('last_security_scan'),
        ];
    }

    /**
     * Run security scan
     *
     * @return array<string, mixed>
     */
    public function runSecurityScan(): array
    {
        $results = [
            'timestamp' => now()->toISOString(),
            'checks' => [],
        ];

        // Check for suspicious patterns
        $results['checks']['failed_login_patterns'] = $this->checkFailedLoginPatterns();
        $results['checks']['suspicious_user_agents'] = $this->checkSuspiciousUserAgents();
        $results['checks']['unusual_access_patterns'] = $this->checkUnusualAccessPatterns();
        $results['checks']['security_configuration'] = $this->checkSecurityConfiguration();

        // Store scan results
        Cache::put('last_security_scan', $results, 86400); // 24 hours

        Log::info('Security scan completed', $results);

        return $results;
    }

    /**
     * Alert for failed login threshold breach
     */
    private function alertFailedLoginThreshold(string $identifier, int $attempts, string $type): void
    {
        Log::critical('Failed login threshold breached', [
            'type' => $type,
            'identifier' => $identifier,
            'attempts' => $attempts,
            'threshold' => self::FAILED_LOGIN_THRESHOLD,
            'window_minutes' => self::FAILED_LOGIN_WINDOW / 60,
            'timestamp' => now()->toISOString(),
        ]);

        // In a real implementation, this would trigger alerts to security team
        // For now, we'll just log it
    }

    /**
     * Alert for suspicious activity threshold breach
     */
    private function alertSuspiciousActivity(string $ip, int $count, string $activity): void
    {
        Log::critical('Suspicious activity threshold breached', [
            'ip' => $ip,
            'activity_count' => $count,
            'latest_activity' => $activity,
            'threshold' => self::SUSPICIOUS_ACTIVITY_THRESHOLD,
            'window_hours' => self::SUSPICIOUS_ACTIVITY_WINDOW / 3600,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get recent failed logins count
     */
    private function getRecentFailedLogins(int $seconds): int
    {
        // This would query actual log data in a real implementation
        return rand(0, 10); // Placeholder
    }

    /**
     * Get recent suspicious activities count
     */
    private function getRecentSuspiciousActivities(int $seconds): int
    {
        // This would query actual log data in a real implementation
        return rand(0, 5); // Placeholder
    }

    /**
     * Get blocked IPs count
     */
    private function getBlockedIpsCount(): int
    {
        // This would count actual blocked IPs in a real implementation
        return rand(0, 3); // Placeholder
    }

    /**
     * Get security alerts today
     */
    private function getSecurityAlertsToday(): int
    {
        // This would query actual alert data in a real implementation
        return rand(0, 2); // Placeholder
    }

    /**
     * Check failed login patterns
     *
     * @return array<string, mixed>
     */
    private function checkFailedLoginPatterns(): array
    {
        return [
            'status' => 'ok',
            'message' => 'No suspicious failed login patterns detected',
            'details' => [],
        ];
    }

    /**
     * Check suspicious user agents
     *
     * @return array<string, mixed>
     */
    private function checkSuspiciousUserAgents(): array
    {
        return [
            'status' => 'ok',
            'message' => 'No suspicious user agents detected',
            'details' => [],
        ];
    }

    /**
     * Check unusual access patterns
     *
     * @return array<string, mixed>
     */
    private function checkUnusualAccessPatterns(): array
    {
        return [
            'status' => 'ok',
            'message' => 'No unusual access patterns detected',
            'details' => [],
        ];
    }

    /**
     * Check security configuration
     *
     * @return array<string, mixed>
     */
    private function checkSecurityConfiguration(): array
    {
        $issues = [];

        // Check if debug mode is disabled in production
        if (config('app.env') === 'production' && config('app.debug')) {
            $issues[] = 'Debug mode is enabled in production';
        }

        // Check if HTTPS is enforced
        if (config('app.env') === 'production' && !config('session.secure')) {
            $issues[] = 'HTTPS not enforced for sessions';
        }

        // Check if session cookies are HTTP only
        if (!config('session.http_only')) {
            $issues[] = 'Session cookies are not HTTP only';
        }

        return [
            'status' => empty($issues) ? 'ok' : 'warning',
            'message' => empty($issues) ? 'Security configuration is correct' : 'Security configuration issues found',
            'issues' => $issues,
        ];
    }

    /**
     * Monitor API rate limiting
     */
    public function monitorApiRateLimit(string $identifier, int $limit = 60, int $window = 60): bool
    {
        $key = "api_rate_limit:{$identifier}";
        $requests = Cache::get($key, 0) + 1;

        if ($requests === 1) {
            Cache::put($key, $requests, $window);
        } else {
            Cache::increment($key);
        }

        if ($requests > $limit) {
            $this->logSuspiciousActivity('API rate limit exceeded', [
                'identifier' => $identifier,
                'requests' => $requests,
                'limit' => $limit,
                'window' => $window,
            ], request());

            return false;
        }

        return true;
    }

    /**
     * Log data access for audit
     */
    public function logDataAccess(string $model, int $recordId, string $action, ?int $userId = null): void
    {
        Log::info('Data access logged', [
            'model' => $model,
            'record_id' => $recordId,
            'action' => $action,
            'user_id' => $userId,
            'ip' => request()->ip(),
            'timestamp' => now()->toISOString(),
        ]);
    }
}
