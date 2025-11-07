<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * SLA Threshold Management Service
 *
 * Manages SLA thresholds for response times, resolution times,
 * escalation rules, and notification settings for SLA breaches.
 *
 * @trace Requirements 12.2, 5.2, 5.5
 */
class SLAThresholdService
{
    private const CACHE_TTL = 3600; // 1 hour

    private const CACHE_KEY = 'sla_thresholds_config';

    public function getSLAThresholds(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->loadDefaultThresholds();
        });
    }

    public function updateSLAThresholds(array $thresholds): void
    {
        // Validate thresholds
        $this->validateThresholds($thresholds);

        // Store in cache
        Cache::put(self::CACHE_KEY, $thresholds, self::CACHE_TTL);

        // Log configuration change
        Log::info('SLA thresholds updated', [
            'user_id' => auth()->id(),
            'updated_at' => now(),
            'thresholds_count' => count($thresholds['categories'] ?? []),
        ]);
    }

    public function getSLAForTicket(string $priority, string $category = 'general'): array
    {
        $thresholds = $this->getSLAThresholds();
        $categoryConfig = $thresholds['categories'][$category] ?? $thresholds['categories']['general'];

        return [
            'response_time_hours' => $categoryConfig['response_times'][$priority] ?? $categoryConfig['response_times']['normal'],
            'resolution_time_hours' => $categoryConfig['resolution_times'][$priority] ?? $categoryConfig['resolution_times']['normal'],
            'escalation_threshold_percent' => $thresholds['escalation']['threshold_percent'],
            'escalation_enabled' => $thresholds['escalation']['enabled'],
            'notification_intervals' => $thresholds['notifications']['intervals'],
        ];
    }

    public function calculateSLADeadlines(string $priority, string $category = 'general', ?\Carbon\Carbon $startTime = null): array
    {
        $startTime = $startTime ?? now();
        $sla = $this->getSLAForTicket($priority, $category);

        $responseDeadline = $startTime->copy()->addHours($sla['response_time_hours']);
        $resolutionDeadline = $startTime->copy()->addHours($sla['resolution_time_hours']);

        // Calculate escalation time (25% before breach by default)
        $escalationPercent = $sla['escalation_threshold_percent'] / 100;
        $escalationTime = $sla['resolution_time_hours'] * (1 - $escalationPercent);
        $escalationDeadline = $startTime->copy()->addHours($escalationTime);

        return [
            'response_deadline' => $responseDeadline,
            'resolution_deadline' => $resolutionDeadline,
            'escalation_deadline' => $escalationDeadline,
            'response_time_hours' => $sla['response_time_hours'],
            'resolution_time_hours' => $sla['resolution_time_hours'],
            'escalation_enabled' => $sla['escalation_enabled'],
        ];
    }

    public function checkSLABreach(\Carbon\Carbon $startTime, string $priority, string $category = 'general'): array
    {
        $deadlines = $this->calculateSLADeadlines($priority, $category, $startTime);
        $now = now();

        $responseBreached = $now->isAfter($deadlines['response_deadline']);
        $resolutionBreached = $now->isAfter($deadlines['resolution_deadline']);
        $escalationNeeded = $now->isAfter($deadlines['escalation_deadline']);

        // Calculate time remaining or overdue
        $responseTimeRemaining = $responseBreached ? 0 : $now->diffInMinutes($deadlines['response_deadline']);
        $resolutionTimeRemaining = $resolutionBreached ? 0 : $now->diffInMinutes($deadlines['resolution_deadline']);

        $responseOverdue = $responseBreached ? $now->diffInMinutes($deadlines['response_deadline']) : 0;
        $resolutionOverdue = $resolutionBreached ? $now->diffInMinutes($deadlines['resolution_deadline']) : 0;

        return [
            'response_breached' => $responseBreached,
            'resolution_breached' => $resolutionBreached,
            'escalation_needed' => $escalationNeeded && $deadlines['escalation_enabled'],
            'response_time_remaining_minutes' => $responseTimeRemaining,
            'resolution_time_remaining_minutes' => $resolutionTimeRemaining,
            'response_overdue_minutes' => $responseOverdue,
            'resolution_overdue_minutes' => $resolutionOverdue,
            'severity' => $this->calculateBreachSeverity($responseBreached, $resolutionBreached, $escalationNeeded),
        ];
    }

    protected function calculateBreachSeverity(bool $responseBreached, bool $resolutionBreached, bool $escalationNeeded): string
    {
        if ($resolutionBreached) {
            return 'critical';
        }

        if ($responseBreached) {
            return 'high';
        }

        if ($escalationNeeded) {
            return 'medium';
        }

        return 'low';
    }

    public function getSLACompliance(array $tickets): array
    {
        $total = count($tickets);
        $responseCompliant = 0;
        $resolutionCompliant = 0;

        foreach ($tickets as $ticket) {
            $breach = $this->checkSLABreach(
                $ticket['created_at'],
                $ticket['priority'],
                $ticket['category'] ?? 'general'
            );

            if (! $breach['response_breached']) {
                $responseCompliant++;
            }

            if (! $breach['resolution_breached']) {
                $resolutionCompliant++;
            }
        }

        return [
            'total_tickets' => $total,
            'response_compliance_count' => $responseCompliant,
            'resolution_compliance_count' => $resolutionCompliant,
            'response_compliance_percent' => $total > 0 ? round(($responseCompliant / $total) * 100, 2) : 0,
            'resolution_compliance_percent' => $total > 0 ? round(($resolutionCompliant / $total) * 100, 2) : 0,
        ];
    }

    protected function validateThresholds(array $thresholds): void
    {
        if (! isset($thresholds['categories']) || ! is_array($thresholds['categories'])) {
            throw new \InvalidArgumentException('SLA thresholds must contain categories array');
        }

        foreach ($thresholds['categories'] as $category => $config) {
            if (! isset($config['response_times']) || ! is_array($config['response_times'])) {
                throw new \InvalidArgumentException("Category '{$category}' must have response_times array");
            }

            if (! isset($config['resolution_times']) || ! is_array($config['resolution_times'])) {
                throw new \InvalidArgumentException("Category '{$category}' must have resolution_times array");
            }

            // Validate that resolution times are greater than response times
            foreach (['low', 'normal', 'high', 'urgent'] as $priority) {
                $responseTime = $config['response_times'][$priority] ?? 0;
                $resolutionTime = $config['resolution_times'][$priority] ?? 0;

                if ($resolutionTime <= $responseTime) {
                    throw new \InvalidArgumentException("Resolution time must be greater than response time for {$category}:{$priority}");
                }
            }
        }

        // Validate escalation configuration
        if (isset($thresholds['escalation']['threshold_percent'])) {
            $percent = $thresholds['escalation']['threshold_percent'];
            if ($percent < 1 || $percent > 50) {
                throw new \InvalidArgumentException('Escalation threshold must be between 1% and 50%');
            }
        }
    }

    protected function loadDefaultThresholds(): array
    {
        return [
            'version' => '1.0',
            'updated_at' => now()->toISOString(),
            'categories' => [
                'general' => [
                    'name' => 'Am (Umum)',
                    'description' => 'SLA untuk tiket am',
                    'response_times' => [
                        'low' => 168,      // 7 days
                        'normal' => 72,    // 3 days
                        'high' => 24,      // 1 day
                        'urgent' => 4,     // 4 hours
                    ],
                    'resolution_times' => [
                        'low' => 336,      // 14 days
                        'normal' => 168,   // 7 days
                        'high' => 72,      // 3 days
                        'urgent' => 24,    // 1 day
                    ],
                ],
                'hardware' => [
                    'name' => 'Perkakasan',
                    'description' => 'SLA untuk isu perkakasan',
                    'response_times' => [
                        'low' => 72,       // 3 days
                        'normal' => 24,    // 1 day
                        'high' => 8,       // 8 hours
                        'urgent' => 2,     // 2 hours
                    ],
                    'resolution_times' => [
                        'low' => 168,      // 7 days
                        'normal' => 72,    // 3 days
                        'high' => 24,      // 1 day
                        'urgent' => 8,     // 8 hours
                    ],
                ],
                'software' => [
                    'name' => 'Perisian',
                    'description' => 'SLA untuk isu perisian',
                    'response_times' => [
                        'low' => 96,       // 4 days
                        'normal' => 48,    // 2 days
                        'high' => 12,      // 12 hours
                        'urgent' => 4,     // 4 hours
                    ],
                    'resolution_times' => [
                        'low' => 240,      // 10 days
                        'normal' => 120,   // 5 days
                        'high' => 48,      // 2 days
                        'urgent' => 12,    // 12 hours
                    ],
                ],
                'network' => [
                    'name' => 'Rangkaian',
                    'description' => 'SLA untuk isu rangkaian',
                    'response_times' => [
                        'low' => 48,       // 2 days
                        'normal' => 12,    // 12 hours
                        'high' => 4,       // 4 hours
                        'urgent' => 1,     // 1 hour
                    ],
                    'resolution_times' => [
                        'low' => 120,      // 5 days
                        'normal' => 48,    // 2 days
                        'high' => 12,      // 12 hours
                        'urgent' => 4,     // 4 hours
                    ],
                ],
                'security' => [
                    'name' => 'Keselamatan',
                    'description' => 'SLA untuk isu keselamatan',
                    'response_times' => [
                        'low' => 24,       // 1 day
                        'normal' => 8,     // 8 hours
                        'high' => 2,       // 2 hours
                        'urgent' => 0.5,   // 30 minutes
                    ],
                    'resolution_times' => [
                        'low' => 72,       // 3 days
                        'normal' => 24,    // 1 day
                        'high' => 8,       // 8 hours
                        'urgent' => 4,     // 4 hours
                    ],
                ],
            ],
            'escalation' => [
                'enabled' => true,
                'threshold_percent' => 25, // Escalate when 25% time remaining
                'escalation_roles' => ['admin', 'superuser'],
                'auto_assign' => true,
            ],
            'notifications' => [
                'enabled' => true,
                'intervals' => [
                    'warning' => 60,    // 1 hour before breach
                    'critical' => 15,   // 15 minutes before breach
                    'breach' => 0,      // Immediate on breach
                    'overdue' => 240,   // Every 4 hours when overdue
                ],
                'recipients' => [
                    'assignee' => true,
                    'supervisor' => true,
                    'admin' => true,
                ],
            ],
            'business_hours' => [
                'enabled' => true,
                'timezone' => 'Asia/Kuala_Lumpur',
                'working_days' => [1, 2, 3, 4, 5], // Monday to Friday
                'start_time' => '08:00',
                'end_time' => '17:00',
                'exclude_weekends' => true,
                'exclude_holidays' => true,
            ],
        ];
    }

    public function getAvailableCategories(): array
    {
        $thresholds = $this->getSLAThresholds();
        $categories = [];

        foreach ($thresholds['categories'] as $key => $category) {
            $categories[$key] = $category['name'];
        }

        return $categories;
    }

    public function getAvailablePriorities(): array
    {
        return [
            'low' => 'Rendah',
            'normal' => 'Biasa',
            'high' => 'Tinggi',
            'urgent' => 'Segera',
        ];
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function exportThresholds(): array
    {
        return [
            'thresholds' => $this->getSLAThresholds(),
            'exported_at' => now()->toISOString(),
            'exported_by' => auth()->user()?->name,
        ];
    }

    public function importThresholds(array $data): void
    {
        if (! isset($data['thresholds'])) {
            throw new \InvalidArgumentException('Import data must contain thresholds');
        }

        $this->updateSLAThresholds($data['thresholds']);

        Log::info('SLA thresholds imported', [
            'user_id' => auth()->id(),
            'imported_at' => now(),
            'categories_count' => count($data['thresholds']['categories'] ?? []),
        ]);
    }
}
