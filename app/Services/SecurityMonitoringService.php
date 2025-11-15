<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Models\Audit;

/**
 * Security Monitoring Service
 *
 * Tracks and monitors security events including failed logins,
 * suspicious activity, role changes, and configuration modifications.
 *
 * @version 1.0.0
 *
 * @since 2025-01-06
 *
 * @author ICTServe Development Team
 * @copyright 2025 MOTAC BPM
 *
 * Requirements: D03-FR-010 (Security Monitoring), D11 §8 (Security)
 * Traceability: Phase 9.2 - Security Monitoring Implementation
 * WCAG 2.2 AA: N/A (Backend service)
 * Bilingual: N/A (Backend service)
 */
class SecurityMonitoringService
{
    /**
     * Cache duration for security metrics (5 minutes)
     */
    private const CACHE_DURATION = 300;

    /**
     * Alert threshold for failed login attempts
     */
    private const FAILED_LOGIN_THRESHOLD = 5;

    /**
     * Time window for failed login detection (minutes)
     */
    private const FAILED_LOGIN_WINDOW = 15;

    /**
     * Get security dashboard statistics
     *
     * @return array<string, mixed>
     */
    public function getDashboardStats(): array
    {
        return Cache::remember('security_dashboard_stats', self::CACHE_DURATION, function () {
            return [
                'failed_logins_24h' => $this->getFailedLoginsCount(24),
                'suspicious_activities_24h' => $this->getSuspiciousActivitiesCount(24),
                'role_changes_24h' => $this->getRoleChangesCount(24),
                'config_modifications_24h' => $this->getConfigModificationsCount(24),
                'active_sessions' => $this->getActiveSessionsCount(),
                'blocked_ips' => $this->getBlockedIPsCount(),
                'critical_alerts' => $this->getCriticalAlertsCount(),
                'last_security_scan' => $this->getLastSecurityScanTime(),
            ];
        });
    }

    /**
     * Get failed login attempts count
     *
     * @param  int  $hours  Time window in hours
     */
    public function getFailedLoginsCount(int $hours = 24): int
    {
        return DB::table('failed_login_attempts')
            ->where('created_at', '>=', Carbon::now()->subHours($hours))
            ->count();
    }

    /**
     * Get suspicious activities count
     *
     * @param  int  $hours  Time window in hours
     */
    public function getSuspiciousActivitiesCount(int $hours = 24): int
    {
        return Audit::query()
            ->where('created_at', '>=', Carbon::now()->subHours($hours))
            ->where(function ($query) {
                $query->where('tags', 'like', '%suspicious%')
                    ->orWhere('tags', 'like', '%security%')
                    ->orWhere('event', 'deleted')
                    ->orWhereIn('auditable_type', [
                        'App\\Models\\User',
                        'Spatie\\Permission\\Models\\Role',
                        'Spatie\\Permission\\Models\\Permission',
                    ]);
            })
            ->count();
    }

    /**
     * Get role changes count
     *
     * @param  int  $hours  Time window in hours
     */
    public function getRoleChangesCount(int $hours = 24): int
    {
        return Audit::query()
            ->where('created_at', '>=', Carbon::now()->subHours($hours))
            ->whereIn('auditable_type', [
                'Spatie\\Permission\\Models\\Role',
                'App\\Models\\User',
            ])
            ->where(function ($query) {
                $query->where('event', 'updated')
                    ->whereRaw("JSON_EXTRACT(new_values, '$.role') IS NOT NULL")
                    ->orWhereRaw("JSON_EXTRACT(old_values, '$.role') IS NOT NULL");
            })
            ->count();
    }

    /**
     * Get configuration modifications count
     *
     * @param  int  $hours  Time window in hours
     */
    public function getConfigModificationsCount(int $hours = 24): int
    {
        return Audit::query()
            ->where('created_at', '>=', Carbon::now()->subHours($hours))
            ->where('tags', 'like', '%config%')
            ->count();
    }

    /**
     * Get active sessions count
     */
    public function getActiveSessionsCount(): int
    {
        return DB::table('sessions')
            ->where('last_activity', '>=', Carbon::now()->subMinutes(30)->timestamp)
            ->count();
    }

    /**
     * Get blocked IPs count
     */
    public function getBlockedIPsCount(): int
    {
        return Cache::get('blocked_ips_count', 0);
    }

    /**
     * Get critical alerts count
     */
    public function getCriticalAlertsCount(): int
    {
        return Cache::get('critical_security_alerts_count', 0);
    }

    /**
     * Get last security scan time
     */
    public function getLastSecurityScanTime(): ?string
    {
        $lastScan = Cache::get('last_security_scan_time');

        return $lastScan ? Carbon::parse($lastScan)->diffForHumans() : null;
    }

    /**
     * Get recent security events
     *
     * @param  int  $limit  Number of events to retrieve
     */
    public function getRecentSecurityEvents(int $limit = 50): \Illuminate\Support\Collection
    {
        return Audit::query()
            ->with('user')
            ->where(function ($query) {
                $query->where('tags', 'like', '%security%')
                    ->orWhere('tags', 'like', '%suspicious%')
                    ->orWhereIn('auditable_type', [
                        'App\\Models\\User',
                        'Spatie\\Permission\\Models\\Role',
                        'Spatie\\Permission\\Models\\Permission',
                    ]);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($audit) {
                return [
                    'id' => $audit->id,
                    'timestamp' => $audit->created_at,
                    'user' => $audit->user?->name ?? 'System',
                    'event' => $audit->event,
                    'entity_type' => class_basename($audit->auditable_type),
                    'entity_id' => $audit->auditable_id,
                    'ip_address' => $audit->ip_address,
                    'severity' => $this->calculateSeverity($audit),
                    'description' => $this->generateEventDescription($audit),
                ];
            });
    }

    /**
     * Get failed login attempts
     *
     * @param  int  $limit  Number of attempts to retrieve
     */
    public function getFailedLoginAttempts(int $limit = 50): \Illuminate\Support\Collection
    {
        if (! DB::getSchemaBuilder()->hasTable('failed_login_attempts')) {
            return collect([]);
        }

        return DB::table('failed_login_attempts')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'timestamp' => Carbon::parse($attempt->created_at),
                    'email' => $attempt->email,
                    'ip_address' => $attempt->ip_address,
                    'user_agent' => $attempt->user_agent,
                    'attempts_count' => $this->getIPAttemptCount($attempt->ip_address),
                ];
            });
    }

    /**
     * Get IP attempt count within time window
     */
    private function getIPAttemptCount(string $ipAddress): int
    {
        if (! DB::getSchemaBuilder()->hasTable('failed_login_attempts')) {
            return 0;
        }

        return DB::table('failed_login_attempts')
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', Carbon::now()->subMinutes(self::FAILED_LOGIN_WINDOW))
            ->count();
    }

    /**
     * Calculate event severity
     */
    private function calculateSeverity(Audit $audit): string
    {
        // Critical: User/role deletions, permission changes
        if ($audit->event === 'deleted' && in_array($audit->auditable_type, [
            'App\\Models\\User',
            'Spatie\\Permission\\Models\\Role',
        ])) {
            return 'critical';
        }

        // High: Role changes, permission modifications
        if (in_array($audit->auditable_type, [
            'Spatie\\Permission\\Models\\Role',
            'Spatie\\Permission\\Models\\Permission',
        ])) {
            return 'high';
        }

        // Medium: User updates, config changes
        if ($audit->auditable_type === 'App\\Models\\User' || str_contains($audit->tags ?? '', 'config')) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Generate human-readable event description
     */
    private function generateEventDescription(Audit $audit): string
    {
        $user = $audit->user?->name ?? 'System';
        $entity = class_basename($audit->auditable_type);
        $event = ucfirst($audit->event);

        return "{$user} {$audit->event} {$entity} #{$audit->auditable_id}";
    }

    /**
     * Check for suspicious activity patterns
     */
    public function isSuspiciousActivity(string $ipAddress): bool
    {
        $attemptCount = $this->getIPAttemptCount($ipAddress);

        return $attemptCount >= self::FAILED_LOGIN_THRESHOLD;
    }

    /**
     * Block IP address
     *
     * @param  int  $duration  Duration in minutes
     */
    public function blockIP(string $ipAddress, string $reason, int $duration = 60): void
    {
        $blockedIPs = Cache::get('blocked_ips', []);
        $blockedIPs[$ipAddress] = [
            'reason' => $reason,
            'blocked_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMinutes($duration),
        ];

        Cache::put('blocked_ips', $blockedIPs, $duration * 60);
        Cache::increment('blocked_ips_count');
    }

    /**
     * Unblock IP address
     */
    public function unblockIP(string $ipAddress): void
    {
        $blockedIPs = Cache::get('blocked_ips', []);
        unset($blockedIPs[$ipAddress]);

        Cache::put('blocked_ips', $blockedIPs);
        Cache::decrement('blocked_ips_count');
    }

    /**
     * Check if IP is blocked
     */
    public function isIPBlocked(string $ipAddress): bool
    {
        $blockedIPs = Cache::get('blocked_ips', []);

        if (! isset($blockedIPs[$ipAddress])) {
            return false;
        }

        $blockInfo = $blockedIPs[$ipAddress];
        $expiresAt = Carbon::parse($blockInfo['expires_at']);

        if ($expiresAt->isPast()) {
            $this->unblockIP($ipAddress);

            return false;
        }

        return true;
    }

    /**
     * Get blocked IPs list
     *
     * @return array<string, array>
     */
    public function getBlockedIPs(): array
    {
        return Cache::get('blocked_ips', []);
    }

    /**
     * Create security alert
     *
     * @param  array<string, mixed>  $metadata
     */
    public function createAlert(string $type, string $message, string $severity = 'medium', array $metadata = []): void
    {
        $alerts = Cache::get('security_alerts', []);
        $alerts[] = [
            'id' => d('alert_', true),
            'type' => $type,
            'message' => $message,
            'severity' => $severity,
            'metadata' => $metadata,
            'created_at' => Carbon::now(),
            'acknowledged' => false,
        ];

        Cache::put('security_alerts', $alerts, 86400); // 24 hours

        if ($severity === 'critical') {
            Cache::increment('critical_security_alerts_count');
        }
    }

    /**
     * Get security alerts
     *
     * @return array<int, array>
     */
    public function getAlerts(bool $unacknowledgedOnly = false): array
    {
        $alerts = Cache::get('security_alerts', []);

        if ($unacknowledgedOnly) {
            return array_filter($alerts, fn ($alert) => ! $alert['acknowledged']);
        }

        return $alerts;
    }

    /**
     * Acknowledge security alert
     */
    public function acknowledgeAlert(string $alertId): void
    {
        $alerts = Cache::get('security_alerts', []);

        foreach ($alerts as &$alert) {
            if ($alert['id'] === $alertId) {
                $alert['acknowledged'] = true;
                $alert['acknowledged_at'] = Carbon::now();
                $alert['acknowledged_by'] = auth()->user()?->name ?? 'System';

                if ($alert['severity'] === 'critical') {
                    Cache::decrement('critical_security_alerts_count');
                }
                break;
            }
        }

        Cache::put('security_alerts', $alerts, 86400);
    }

    /**
     * Clear old alerts
     *
     * @param  int  $hours  Age threshold in hours
     * @return int Number of alerts cleared
     */
    public function clearOldAlerts(int $hours = 24): int
    {
        $alerts = Cache::get('security_alerts', []);
        $threshold = Carbon::now()->subHours($hours);
        $originalCount = count($alerts);

        $alerts = array_filter($alerts, function ($alert) use ($threshold) {
            $createdAt = Carbon::parse($alert['created_at']);

            return $createdAt->isAfter($threshold) || ! $alert['acknowledged'];
        });

        Cache::put('security_alerts', array_values($alerts), 86400);

        return $originalCount - count($alerts);
    }
}
