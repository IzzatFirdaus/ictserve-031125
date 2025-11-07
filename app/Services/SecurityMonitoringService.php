<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Models\Audit;

/**
 * Security Monitoring Service
 *
 * Provides comprehensive security monitoring and incident detection for ICTServe.
 * Tracks failed logins, suspicious activities, role changes, and configuration modifications.
 *
 * Requirements: 9.2, 9.3, 9.4
 *
 * @see D03-FR-007.3 Security monitoring
 * @see D11 §8 Security implementation
 */
class SecurityMonitoringService
{
    private const CACHE_TTL = 300; // 5 minutes

    private const FAILED_LOGIN_THRESHOLD = 5;

    private const SUSPICIOUS_ACTIVITY_THRESHOLD = 10;

    /**
     * Get security dashboard statistics
     */
    public function getSecurityStats(): array
    {
        return Cache::remember('security_stats', self::CACHE_TTL, function () {
            return [
                'failed_logins_today' => $this->getFailedLoginsToday(),
                'failed_logins_week' => $this->getFailedLoginsThisWeek(),
                'suspicious_activities_today' => $this->getSuspiciousActivitiesToday(),
                'role_changes_today' => $this->getRoleChangesToday(),
                'config_changes_today' => $this->getConfigChangesToday(),
                'active_sessions' => $this->getActiveSessionsCount(),
                'security_incidents_today' => $this->getSecurityIncidentsToday(),
                'last_security_scan' => $this->getLastSecurityScanTime(),
            ];
        });
    }

    /**
     * Get failed login attempts
     */
    public function getFailedLoginAttempts(int $days = 7): Collection
    {
        return Cache::remember("failed_logins_{$days}d", self::CACHE_TTL, function () use ($days) {
            return DB::table('failed_jobs')
                ->where('failed_at', '>=', now()->subDays($days))
                ->where('payload', 'like', '%login%')
                ->orderBy('failed_at', 'desc')
                ->get()
                ->map(function ($job) {
                    $payload = json_decode($job->payload, true);

                    return [
                        'id' => $job->id,
                        'failed_at' => Carbon::parse($job->failed_at),
                        'exception' => $job->exception,
                        'ip_address' => $this->extractIpFromPayload($payload),
                        'user_agent' => $this->extractUserAgentFromPayload($payload),
                    ];
                });
        });
    }

    /**
     * Get suspicious activities
     */
    public function getSuspiciousActivities(int $days = 7): Collection
    {
        return Cache::remember("suspicious_activities_{$days}d", self::CACHE_TTL, function () use ($days) {
            return Audit::query()
                ->where('created_at', '>=', now()->subDays($days))
                ->where(function ($query) {
                    $query->where('event', 'deleted')
                        ->orWhere('auditable_type', 'App\\Models\\User')
                        ->orWhere('ip_address', 'like', '%192.168.%') // Internal network
                        ->orWhereJsonContains('new_values->role', 'superuser');
                })
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($audit) {
                    return [
                        'id' => $audit->id,
                        'timestamp' => $audit->created_at,
                        'user_id' => $audit->user_id,
                        'user_name' => $audit->user?->name ?? 'System',
                        'action' => $audit->event,
                        'entity_type' => class_basename($audit->auditable_type),
                        'entity_id' => $audit->auditable_id,
                        'ip_address' => $audit->ip_address,
                        'risk_level' => $this->calculateRiskLevel($audit),
                        'description' => $this->generateActivityDescription($audit),
                    ];
                });
        });
    }

    /**
     * Get role change history
     */
    public function getRoleChangeHistory(int $days = 30): Collection
    {
        return Cache::remember("role_changes_{$days}d", self::CACHE_TTL, function () use ($days) {
            return Audit::query()
                ->where('created_at', '>=', now()->subDays($days))
                ->where('auditable_type', 'App\\Models\\User')
                ->where(function ($query) {
                    $query->whereJsonContains('old_values->role', 'admin')
                        ->orWhereJsonContains('old_values->role', 'superuser')
                        ->orWhereJsonContains('new_values->role', 'admin')
                        ->orWhereJsonContains('new_values->role', 'superuser');
                })
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($audit) {
                    $oldRole = $audit->old_values['role'] ?? 'unknown';
                    $newRole = $audit->new_values['role'] ?? 'unknown';

                    return [
                        'id' => $audit->id,
                        'timestamp' => $audit->created_at,
                        'changed_by_user_id' => $audit->user_id,
                        'changed_by_name' => $audit->user?->name ?? 'System',
                        'target_user_id' => $audit->auditable_id,
                        'target_user_name' => User::find($audit->auditable_id)?->name ?? 'Unknown',
                        'old_role' => $oldRole,
                        'new_role' => $newRole,
                        'ip_address' => $audit->ip_address,
                        'risk_level' => $this->calculateRoleChangeRisk($oldRole, $newRole),
                    ];
                });
        });
    }

    /**
     * Get configuration modification logs
     */
    public function getConfigurationChanges(int $days = 30): Collection
    {
        return Cache::remember("config_changes_{$days}d", self::CACHE_TTL, function () use ($days) {
            return Audit::query()
                ->where('created_at', '>=', now()->subDays($days))
                ->whereIn('auditable_type', [
                    'App\\Models\\Division',
                    'App\\Models\\Grade',
                    'App\\Models\\AssetCategory',
                    'App\\Models\\TicketCategory',
                ])
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($audit) {
                    return [
                        'id' => $audit->id,
                        'timestamp' => $audit->created_at,
                        'user_id' => $audit->user_id,
                        'user_name' => $audit->user?->name ?? 'System',
                        'action' => $audit->event,
                        'config_type' => class_basename($audit->auditable_type),
                        'config_id' => $audit->auditable_id,
                        'changes' => $this->formatConfigChanges($audit->old_values, $audit->new_values),
                        'ip_address' => $audit->ip_address,
                    ];
                });
        });
    }

    /**
     * Detect security incidents
     */
    public function detectSecurityIncidents(): Collection
    {
        $incidents = collect();

        // Check for multiple failed logins from same IP
        $failedLogins = $this->getFailedLoginAttempts(1);
        $ipGroups = $failedLogins->groupBy('ip_address');

        foreach ($ipGroups as $ip => $attempts) {
            if ($attempts->count() >= self::FAILED_LOGIN_THRESHOLD) {
                $incidents->push([
                    'type' => 'multiple_failed_logins',
                    'severity' => 'high',
                    'ip_address' => $ip,
                    'count' => $attempts->count(),
                    'first_attempt' => $attempts->min('failed_at'),
                    'last_attempt' => $attempts->max('failed_at'),
                    'description' => "Multiple failed login attempts ({$attempts->count()}) from IP {$ip}",
                ]);
            }
        }

        // Check for suspicious role elevations
        $roleChanges = $this->getRoleChangeHistory(1);
        foreach ($roleChanges as $change) {
            if ($change['risk_level'] === 'high') {
                $incidents->push([
                    'type' => 'suspicious_role_elevation',
                    'severity' => 'critical',
                    'user_id' => $change['target_user_id'],
                    'changed_by' => $change['changed_by_name'],
                    'old_role' => $change['old_role'],
                    'new_role' => $change['new_role'],
                    'timestamp' => $change['timestamp'],
                    'description' => "Suspicious role elevation: {$change['target_user_name']} elevated to {$change['new_role']} by {$change['changed_by_name']}",
                ]);
            }
        }

        // Check for unusual activity patterns
        $suspiciousActivities = $this->getSuspiciousActivities(1);
        $userActivityCounts = $suspiciousActivities->groupBy('user_id')->map->count();

        foreach ($userActivityCounts as $userId => $count) {
            if ($count >= self::SUSPICIOUS_ACTIVITY_THRESHOLD) {
                $user = User::find($userId);
                $incidents->push([
                    'type' => 'unusual_activity_pattern',
                    'severity' => 'medium',
                    'user_id' => $userId,
                    'user_name' => $user?->name ?? 'Unknown',
                    'activity_count' => $count,
                    'description' => 'Unusual activity pattern: '.$count.' suspicious activities by '.($user?->name ?? 'Unknown User'),
                ]);
            }
        }

        return $incidents;
    }

    /**
     * Get security metrics for charts
     */
    public function getSecurityMetrics(int $days = 30): array
    {
        $metrics = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');

            $metrics[$dateKey] = [
                'date' => $dateKey,
                'failed_logins' => $this->getFailedLoginsForDate($date),
                'suspicious_activities' => $this->getSuspiciousActivitiesForDate($date),
                'role_changes' => $this->getRoleChangesForDate($date),
                'config_changes' => $this->getConfigChangesForDate($date),
            ];
        }

        return $metrics;
    }

    /**
     * Private helper methods
     */
    private function getFailedLoginsToday(): int
    {
        return $this->getFailedLoginsForDate(now());
    }

    private function getFailedLoginsThisWeek(): int
    {
        return DB::table('failed_jobs')
            ->where('failed_at', '>=', now()->subWeek())
            ->where('payload', 'like', '%login%')
            ->count();
    }

    private function getSuspiciousActivitiesToday(): int
    {
        return $this->getSuspiciousActivitiesForDate(now());
    }

    private function getRoleChangesToday(): int
    {
        return $this->getRoleChangesForDate(now());
    }

    private function getConfigChangesToday(): int
    {
        return $this->getConfigChangesForDate(now());
    }

    private function getActiveSessionsCount(): int
    {
        return DB::table('sessions')
            ->where('last_activity', '>=', now()->subMinutes(30)->timestamp)
            ->count();
    }

    private function getSecurityIncidentsToday(): int
    {
        return $this->detectSecurityIncidents()->count();
    }

    private function getLastSecurityScanTime(): Carbon
    {
        return now(); // In production, this would be from actual security scan logs
    }

    private function getFailedLoginsForDate(Carbon $date): int
    {
        return DB::table('failed_jobs')
            ->whereDate('failed_at', $date)
            ->where('payload', 'like', '%login%')
            ->count();
    }

    private function getSuspiciousActivitiesForDate(Carbon $date): int
    {
        return Audit::query()
            ->whereDate('created_at', $date)
            ->where(function ($query) {
                $query->where('event', 'deleted')
                    ->orWhere('auditable_type', 'App\\Models\\User');
            })
            ->count();
    }

    private function getRoleChangesForDate(Carbon $date): int
    {
        return Audit::query()
            ->whereDate('created_at', $date)
            ->where('auditable_type', 'App\\Models\\User')
            ->where(function ($query) {
                $query->whereJsonContains('old_values->role', 'admin')
                    ->orWhereJsonContains('new_values->role', 'admin');
            })
            ->count();
    }

    private function getConfigChangesForDate(Carbon $date): int
    {
        return Audit::query()
            ->whereDate('created_at', $date)
            ->whereIn('auditable_type', [
                'App\\Models\\Division',
                'App\\Models\\Grade',
                'App\\Models\\AssetCategory',
            ])
            ->count();
    }

    private function calculateRiskLevel(Audit $audit): string
    {
        if ($audit->event === 'deleted' && $audit->auditable_type === 'App\\Models\\User') {
            return 'high';
        }

        if (isset($audit->new_values['role']) && $audit->new_values['role'] === 'superuser') {
            return 'critical';
        }

        return 'medium';
    }

    private function calculateRoleChangeRisk(string $oldRole, string $newRole): string
    {
        if ($newRole === 'superuser') {
            return 'high';
        }

        if ($oldRole === 'staff' && $newRole === 'admin') {
            return 'medium';
        }

        return 'low';
    }

    private function generateActivityDescription(Audit $audit): string
    {
        $entityType = class_basename($audit->auditable_type);
        $action = ucfirst($audit->event);

        return "{$action} {$entityType} (ID: {$audit->auditable_id})";
    }

    private function formatConfigChanges(?array $oldValues, ?array $newValues): array
    {
        $changes = [];

        if ($oldValues && $newValues) {
            foreach ($newValues as $key => $newValue) {
                $oldValue = $oldValues[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[] = [
                        'field' => $key,
                        'old_value' => $oldValue,
                        'new_value' => $newValue,
                    ];
                }
            }
        }

        return $changes;
    }

    private function extractIpFromPayload(array $payload): ?string
    {
        // Extract IP from job payload - implementation depends on job structure
        return $payload['ip_address'] ?? null;
    }

    private function extractUserAgentFromPayload(array $payload): ?string
    {
        // Extract User Agent from job payload - implementation depends on job structure
        return $payload['user_agent'] ?? null;
    }
}
