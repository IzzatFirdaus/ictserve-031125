<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\Security\SecurityIncidentMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

/**
 * Security Incident Service
 *
 * Detects and responds to security incidents with automated
 * alerting to superusers within 60 seconds.
 *
 * @version 1.0.0
 *
 * @since 2025-01-06
 *
 * @author ICTServe Development Team
 * @copyright 2025 MOTAC BPM
 *
 * Requirements: D03-FR-010 (Security Incidents), D11 §8 (Security)
 * Traceability: Phase 9.4 - Security Incident Service
 * WCAG 2.2 AA: N/A (Backend service)
 * Bilingual: N/A (Backend service)
 */
class SecurityIncidentService
{
    /**
     * Detection interval in minutes
     */
    private const DETECTION_INTERVAL = 5;

    /**
     * Alert SLA in seconds
     */
    private const ALERT_SLA = 60;

    /**
     * Security monitoring service
     */
    private SecurityMonitoringService $monitoringService;

    /**
     * Constructor
     */
    public function __construct(SecurityMonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Detect security incidents
     *
     * @return array<int, array>
     */
    public function detectIncidents(): array
    {
        $incidents = [];

        // Check for brute force attacks
        $bruteForceIncidents = $this->detectBruteForceAttacks();
        if (! empty($bruteForceIncidents)) {
            $incidents = array_merge($incidents, $bruteForceIncidents);
        }

        // Check for suspicious role changes
        $roleChangeIncidents = $this->detectSuspiciousRoleChanges();
        if (! empty($roleChangeIncidents)) {
            $incidents = array_merge($incidents, $roleChangeIncidents);
        }

        // Check for unauthorized access attempts
        $accessIncidents = $this->detectUnauthorizedAccess();
        if (! empty($accessIncidents)) {
            $incidents = array_merge($incidents, $accessIncidents);
        }

        // Check for data exfiltration attempts
        $exfiltrationIncidents = $this->detectDataExfiltration();
        if (! empty($exfiltrationIncidents)) {
            $incidents = array_merge($incidents, $exfiltrationIncidents);
        }

        return $incidents;
    }

    /**
     * Detect brute force attacks
     *
     * @return array<int, array>
     */
    private function detectBruteForceAttacks(): array
    {
        $incidents = [];
        $failedLogins = $this->monitoringService->getFailedLoginAttempts(100);

        // Group by IP address
        $ipGroups = $failedLogins->groupBy('ip_address');

        foreach ($ipGroups as $ipAddress => $attempts) {
            if ($attempts->count() >= 5) {
                $incidents[] = [
                    'type' => 'brute_force',
                    'severity' => 'critical',
                    'ip_address' => $ipAddress,
                    'attempts_count' => $attempts->count(),
                    'first_attempt' => $attempts->first()['timestamp'],
                    'last_attempt' => $attempts->last()['timestamp'],
                    'description' => "Brute force attack detected from IP {$ipAddress} with {$attempts->count()} failed login attempts",
                ];

                // Auto-block IP
                if (! $this->monitoringService->isIPBlocked($ipAddress)) {
                    $this->monitoringService->blockIP(
                        $ipAddress,
                        "Brute force attack: {$attempts->count()} failed attempts",
                        60 // 60 minutes
                    );
                }
            }
        }

        return $incidents;
    }

    /**
     * Detect suspicious role changes
     *
     * @return array<int, array>
     */
    private function detectSuspiciousRoleChanges(): array
    {
        $incidents = [];
        $roleChanges = $this->monitoringService->getRoleChangesCount(self::DETECTION_INTERVAL);

        if ($roleChanges > 5) {
            $incidents[] = [
                'type' => 'suspicious_role_changes',
                'severity' => 'high',
                'changes_count' => $roleChanges,
                'time_window' => self::DETECTION_INTERVAL.' minutes',
                'description' => "Unusual number of role changes detected: {$roleChanges} changes in ".self::DETECTION_INTERVAL.' minutes',
            ];
        }

        return $incidents;
    }

    /**
     * Detect unauthorized access attempts
     *
     * @return array<int, array>
     */
    private function detectUnauthorizedAccess(): array
    {
        $incidents = [];

        // Check for 403 errors in recent activity
        $unauthorizedAttempts = Cache::get('unauthorized_access_attempts', []);

        if (count($unauthorizedAttempts) > 10) {
            $incidents[] = [
                'type' => 'unauthorized_access',
                'severity' => 'high',
                'attempts_count' => count($unauthorizedAttempts),
                'description' => 'Multiple unauthorized access attempts detected: '.count($unauthorizedAttempts).' attempts',
            ];
        }

        return $incidents;
    }

    /**
     * Detect data exfiltration attempts
     *
     * @return array<int, array>
     */
    private function detectDataExfiltration(): array
    {
        $incidents = [];

        // Check for unusual export activity
        $exportActivity = Cache::get('export_activity', []);
        $recentExports = array_filter($exportActivity, function ($export) {
            return Carbon::parse($export['timestamp'])->isAfter(Carbon::now()->subMinutes(self::DETECTION_INTERVAL));
        });

        if (count($recentExports) > 5) {
            $incidents[] = [
                'type' => 'data_exfiltration',
                'severity' => 'critical',
                'exports_count' => count($recentExports),
                'time_window' => self::DETECTION_INTERVAL.' minutes',
                'description' => 'Unusual export activity detected: '.count($recentExports).' exports in '.self::DETECTION_INTERVAL.' minutes',
            ];
        }

        return $incidents;
    }

    /**
     * Handle security incident
     *
     * @param  array<string, mixed>  $incident
     */
    public function handleIncident(array $incident): void
    {
        // Create alert
        $this->monitoringService->createAlert(
            $incident['type'],
            $incident['description'],
            $incident['severity'],
            $incident
        );

        // Send email notification to superusers
        $this->notifySuperusers($incident);

        // Log incident
        $this->logIncident($incident);

        // Take automated action based on severity
        if ($incident['severity'] === 'critical') {
            $this->handleCriticalIncident($incident);
        }
    }

    /**
     * Notify superusers about security incident
     *
     * @param  array<string, mixed>  $incident
     */
    private function notifySuperusers(array $incident): void
    {
        $superusers = User::role('superuser')->get();

        foreach ($superusers as $superuser) {
            try {
                Mail::to($superuser->email)
                    ->queue(new SecurityIncidentMail($incident, $superuser));
            } catch (\Exception $e) {
                \Log::error('Failed to send security incident email', [
                    'user' => $superuser->email,
                    'incident' => $incident,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Log security incident
     *
     * @param  array<string, mixed>  $incident
     */
    private function logIncident(array $incident): void
    {
        \Log::channel('security')->critical('Security Incident Detected', [
            'type' => $incident['type'],
            'severity' => $incident['severity'],
            'description' => $incident['description'],
            'metadata' => $incident,
            'detected_at' => Carbon::now()->toIso8601String(),
        ]);
    }

    /**
     * Handle critical security incident
     *
     * @param  array<string, mixed>  $incident
     */
    private function handleCriticalIncident(array $incident): void
    {
        // For brute force attacks, IP is already blocked in detection
        if ($incident['type'] === 'brute_force') {
            return;
        }

        // For data exfiltration, temporarily disable exports
        if ($incident['type'] === 'data_exfiltration') {
            Cache::put('exports_disabled', true, 3600); // 1 hour
        }

        // Additional automated responses can be added here
    }

    /**
     * Run security incident detection
     *
     * @return int Number of incidents detected
     */
    public function runDetection(): int
    {
        $incidents = $this->detectIncidents();

        foreach ($incidents as $incident) {
            $this->handleIncident($incident);
        }

        return count($incidents);
    }

    /**
     * Get incident statistics
     *
     * @param  int  $hours  Time window in hours
     * @return array<string, mixed>
     */
    public function getIncidentStats(int $hours = 24): array
    {
        $alerts = $this->monitoringService->getAlerts();
        $threshold = Carbon::now()->subHours($hours);

        $recentAlerts = array_filter($alerts, function ($alert) use ($threshold) {
            return Carbon::parse($alert['created_at'])->isAfter($threshold);
        });

        $byType = [];
        $bySeverity = [];

        foreach ($recentAlerts as $alert) {
            $type = $alert['type'] ?? 'unknown';
            $severity = $alert['severity'] ?? 'unknown';

            $byType[$type] = ($byType[$type] ?? 0) + 1;
            $bySeverity[$severity] = ($bySeverity[$severity] ?? 0) + 1;
        }

        return [
            'total_incidents' => count($recentAlerts),
            'by_type' => $byType,
            'by_severity' => $bySeverity,
            'time_window' => $hours.' hours',
        ];
    }
}
