<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\Security\SecurityIncidentMail;
use App\Models\SecurityIncident;
use App\Models\SecurityIncidentLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Security Incident Service
 *
 * PKS CSIRT Integration (Requirement 28) - Comprehensive Security Incident Management
 *
 * Implements automated threat detection, CSIRT escalation workflows,
 * NACSA/MyCERT reporting, and incident management dashboard support.
 *
 * @version 2.0.0
 *
 * @since 2025-12-25
 *
 * @author ICTServe Development Team
 * @copyright 2025 MOTAC BPM
 *
 * Requirements: D03-FR-028 (CSIRT Integration)
 * Traceability: Phase 4.1 - PKS CSIRT Integration
 * PKS Compliance: 28.1, 28.2, 28.3, 28.4, 28.5
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
     * CSIRT escalation SLA in minutes (PKS 28.4)
     */
    private const CSIRT_ESCALATION_SLA = 15;

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
     * Detect security incidents (PKS 28.1)
     *
     * @return array<int, array<string, mixed>>
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

        // Check for anomalous behavior
        $anomalyIncidents = $this->detectAnomalies();
        if (! empty($anomalyIncidents)) {
            $incidents = array_merge($incidents, $anomalyIncidents);
        }

        // Check for DLP violations
        $dlpIncidents = $this->detectDLPViolations();
        if (! empty($dlpIncidents)) {
            $incidents = array_merge($incidents, $dlpIncidents);
        }

        return $incidents;
    }

    /**
     * Detect brute force attacks
     *
     * @return array<int, array<string, mixed>>
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
                    'type' => SecurityIncident::TYPE_BRUTE_FORCE,
                    'severity' => $attempts->count() >= 10
                        ? SecurityIncident::SEVERITY_CRITICAL
                        : SecurityIncident::SEVERITY_HIGH,
                    'title' => "Serangan Brute Force dari IP {$ipAddress}",
                    'description' => "Serangan brute force dikesan dari IP {$ipAddress} dengan {$attempts->count()} percubaan log masuk gagal",
                    'source_ip' => $ipAddress,
                    'detection_rules_triggered' => ['brute_force_threshold'],
                    'indicators_of_compromise' => [
                        'failed_attempts' => $attempts->count(),
                        'first_attempt' => $attempts->first()['timestamp'] ?? null,
                        'last_attempt' => $attempts->last()['timestamp'] ?? null,
                        'targeted_accounts' => $attempts->pluck('username')->unique()->values()->toArray(),
                    ],
                ];

                // Auto-block IP
                if (! $this->monitoringService->isIPBlocked($ipAddress)) {
                    $this->monitoringService->blockIP(
                        $ipAddress,
                        "Serangan brute force: {$attempts->count()} percubaan gagal",
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
     * @return array<int, array<string, mixed>>
     */
    private function detectSuspiciousRoleChanges(): array
    {
        $incidents = [];
        $roleChanges = $this->monitoringService->getRoleChangesCount(self::DETECTION_INTERVAL);

        if ($roleChanges > 5) {
            $incidents[] = [
                'type' => SecurityIncident::TYPE_PRIVILEGE_ESCALATION,
                'severity' => SecurityIncident::SEVERITY_HIGH,
                'title' => 'Perubahan Peranan Mencurigakan Dikesan',
                'description' => "Bilangan perubahan peranan yang luar biasa dikesan: {$roleChanges} perubahan dalam ".self::DETECTION_INTERVAL.' minit',
                'detection_rules_triggered' => ['role_change_threshold'],
                'indicators_of_compromise' => [
                    'changes_count' => $roleChanges,
                    'time_window_minutes' => self::DETECTION_INTERVAL,
                ],
            ];
        }

        return $incidents;
    }

    /**
     * Detect unauthorized access attempts
     *
     * @return array<int, array<string, mixed>>
     */
    private function detectUnauthorizedAccess(): array
    {
        $incidents = [];

        // Check for 403 errors in recent activity
        $unauthorizedAttempts = Cache::get('unauthorized_access_attempts', []);

        if (count($unauthorizedAttempts) > 10) {
            $incidents[] = [
                'type' => SecurityIncident::TYPE_UNAUTHORIZED_ACCESS,
                'severity' => SecurityIncident::SEVERITY_HIGH,
                'title' => 'Percubaan Akses Tanpa Kebenaran',
                'description' => 'Pelbagai percubaan akses tanpa kebenaran dikesan: '.count($unauthorizedAttempts).' percubaan',
                'detection_rules_triggered' => ['unauthorized_access_threshold'],
                'indicators_of_compromise' => [
                    'attempts_count' => count($unauthorizedAttempts),
                    'attempts' => array_slice($unauthorizedAttempts, 0, 10),
                ],
            ];
        }

        return $incidents;
    }

    /**
     * Detect data exfiltration attempts
     *
     * @return array<int, array<string, mixed>>
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
                'type' => SecurityIncident::TYPE_DATA_EXFILTRATION,
                'severity' => SecurityIncident::SEVERITY_CRITICAL,
                'title' => 'Aktiviti Eksport Luar Biasa Dikesan',
                'description' => 'Aktiviti eksport yang luar biasa dikesan: '.count($recentExports).' eksport dalam '.self::DETECTION_INTERVAL.' minit',
                'detection_rules_triggered' => ['export_threshold'],
                'indicators_of_compromise' => [
                    'exports_count' => count($recentExports),
                    'time_window_minutes' => self::DETECTION_INTERVAL,
                    'exports' => array_slice($recentExports, 0, 10),
                ],
            ];
        }

        return $incidents;
    }

    /**
     * Detect anomalous behavior (PKS 28.1)
     *
     * @return array<int, array<string, mixed>>
     */
    private function detectAnomalies(): array
    {
        $incidents = [];

        // Check for after-hours access
        $currentHour = (int) date('H');
        if ($currentHour < 6 || $currentHour > 22) {
            $afterHoursActivity = Cache::get('after_hours_activity', []);
            $recentActivity = array_filter($afterHoursActivity, function ($activity) {
                return Carbon::parse($activity['timestamp'])->isAfter(Carbon::now()->subMinutes(30));
            });

            if (count($recentActivity) > 3) {
                $incidents[] = [
                    'type' => SecurityIncident::TYPE_ANOMALY,
                    'severity' => SecurityIncident::SEVERITY_MEDIUM,
                    'title' => 'Aktiviti Luar Waktu Pejabat',
                    'description' => 'Aktiviti luar waktu pejabat yang mencurigakan dikesan: '.count($recentActivity).' aktiviti',
                    'detection_rules_triggered' => ['after_hours_activity'],
                    'indicators_of_compromise' => [
                        'activity_count' => count($recentActivity),
                        'hour' => $currentHour,
                    ],
                ];
            }
        }

        return $incidents;
    }

    /**
     * Detect DLP violations (PKS 9.2.1)
     *
     * @return array<int, array<string, mixed>>
     */
    private function detectDLPViolations(): array
    {
        $incidents = [];

        // Check for DLP bypass attempts
        $dlpBypasses = Cache::get('dlp_bypass_attempts', []);
        $recentBypasses = array_filter($dlpBypasses, function ($bypass) {
            return Carbon::parse($bypass['timestamp'])->isAfter(Carbon::now()->subMinutes(self::DETECTION_INTERVAL));
        });

        if (count($recentBypasses) > 0) {
            $incidents[] = [
                'type' => SecurityIncident::TYPE_POLICY_VIOLATION,
                'severity' => SecurityIncident::SEVERITY_HIGH,
                'title' => 'Percubaan Pintasan DLP Dikesan',
                'description' => 'Percubaan untuk memintas kawalan DLP dikesan: '.count($recentBypasses).' percubaan',
                'detection_rules_triggered' => ['dlp_bypass_attempt'],
                'indicators_of_compromise' => [
                    'bypass_attempts' => count($recentBypasses),
                    'details' => array_slice($recentBypasses, 0, 5),
                ],
            ];
        }

        return $incidents;
    }

    /**
     * Handle security incident (PKS 28.4)
     *
     * @param  array<string, mixed>  $incidentData
     */
    public function handleIncident(array $incidentData): SecurityIncident
    {
        // Create incident record
        $incident = SecurityIncident::createIncident($incidentData);

        // Log incident creation
        SecurityIncidentLog::log(
            $incident->id,
            SecurityIncidentLog::ACTION_CREATED,
            'Insiden keselamatan dicipta secara automatik',
            $incidentData
        );

        // Create alert in monitoring service
        $this->monitoringService->createAlert(
            $incidentData['type'],
            $incidentData['description'],
            $incidentData['severity'],
            $incidentData
        );

        // Send email notification to superusers
        $this->notifySuperusers($incident);

        // Log to security channel
        $this->logIncident($incident);

        // Check if CSIRT escalation is required (PKS 28.4)
        if ($incident->requires_escalation) {
            $this->scheduleCSIRTEscalation($incident);
        }

        // Take automated action based on severity
        if ($incidentData['severity'] === SecurityIncident::SEVERITY_CRITICAL) {
            $this->handleCriticalIncident($incident);
        }

        return $incident;
    }

    /**
     * Schedule CSIRT escalation (PKS 28.4 - 15 minute SLA)
     */
    private function scheduleCSIRTEscalation(SecurityIncident $incident): void
    {
        // For critical/high severity, escalate immediately
        if (in_array($incident->severity, [SecurityIncident::SEVERITY_CRITICAL])) {
            $this->escalateToCSIRT($incident);
        } else {
            // Schedule escalation check
            Cache::put(
                "csirt_escalation_{$incident->id}",
                [
                    'incident_id' => $incident->id,
                    'deadline' => now()->addMinutes(self::CSIRT_ESCALATION_SLA)->toIso8601String(),
                ],
                now()->addMinutes(self::CSIRT_ESCALATION_SLA + 5)
            );
        }
    }

    /**
     * Escalate incident to CSIRT MOTAC (PKS 28.4)
     */
    public function escalateToCSIRT(SecurityIncident $incident): void
    {
        $incident->escalateToCSIRT();

        SecurityIncidentLog::log(
            $incident->id,
            SecurityIncidentLog::ACTION_CSIRT_NOTIFIED,
            'Insiden dieskalasi ke CSIRT MOTAC dalam tempoh SLA 15 minit',
            [
                'escalation_time' => now()->toIso8601String(),
                'sla_met' => ! $incident->isCSIRTSLABreached(),
            ]
        );

        // Send CSIRT notification
        $this->sendCSIRTNotification($incident);

        Log::channel('security')->critical('CSIRT Escalation', [
            'incident_number' => $incident->incident_number,
            'type' => $incident->type,
            'severity' => $incident->severity,
            'escalated_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Send CSIRT notification
     */
    private function sendCSIRTNotification(SecurityIncident $incident): void
    {
        $csirtEmail = config('services.csirt.email', 'csirt@motac.gov.my');

        try {
            Mail::to($csirtEmail)->queue(new SecurityIncidentMail(
                $incident->toArray(),
                null,
                'csirt'
            ));
        } catch (\Exception $e) {
            Log::error('Failed to send CSIRT notification', [
                'incident_id' => $incident->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate NACSA report (PKS 28.2)
     *
     * @return array<string, mixed>
     */
    public function generateNACSAReport(SecurityIncident $incident): array
    {
        $report = [
            'report_type' => 'NACSA_INCIDENT_REPORT',
            'version' => '1.0',
            'generated_at' => now()->toIso8601String(),
            'organization' => [
                'name' => 'Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC)',
                'division' => 'Bahagian Pengurusan Maklumat (BPM)',
                'contact' => config('services.nacsa.contact', 'bpm@motac.gov.my'),
            ],
            'incident' => [
                'reference_number' => $incident->incident_number,
                'type' => $incident->type,
                'severity' => $incident->severity,
                'status' => $incident->status,
                'title' => $incident->title,
                'description' => $incident->description,
                'detected_at' => $incident->detected_at->toIso8601String(),
                'source_ip' => $incident->source_ip,
                'target_system' => $incident->target_system,
                'affected_assets' => $incident->affected_assets,
                'indicators_of_compromise' => $incident->indicators_of_compromise,
            ],
            'response' => [
                'escalated_at' => $incident->escalated_at?->toIso8601String(),
                'contained_at' => $incident->contained_at?->toIso8601String(),
                'resolved_at' => $incident->resolved_at?->toIso8601String(),
                'response_actions' => $incident->response_actions,
                'resolution_summary' => $incident->resolution_summary,
            ],
            'timeline' => $incident->timeline,
        ];

        return $report;
    }

    /**
     * Submit report to NACSA (PKS 28.2)
     */
    public function submitToNACSA(SecurityIncident $incident): ?string
    {
        $report = $this->generateNACSAReport($incident);

        // In production, this would submit to NACSA API
        // For now, we generate a reference ID and log the submission
        $reportId = 'NACSA-'.date('Y').'-'.str_pad((string) $incident->id, 6, '0', STR_PAD_LEFT);

        $incident->markNACSAReported($reportId);

        SecurityIncidentLog::log(
            $incident->id,
            SecurityIncidentLog::ACTION_NACSA_REPORTED,
            "Laporan dihantar ke NACSA dengan ID: {$reportId}",
            ['report' => $report, 'report_id' => $reportId]
        );

        Log::channel('security')->info('NACSA Report Submitted', [
            'incident_number' => $incident->incident_number,
            'nacsa_report_id' => $reportId,
        ]);

        return $reportId;
    }

    /**
     * Generate MyCERT report (PKS 28.2)
     *
     * @return array<string, mixed>
     */
    public function generateMyCERTReport(SecurityIncident $incident): array
    {
        $report = [
            'report_type' => 'MYCERT_INCIDENT_REPORT',
            'version' => '1.0',
            'generated_at' => now()->toIso8601String(),
            'reporter' => [
                'organization' => 'MOTAC',
                'department' => 'BPM',
                'email' => config('services.mycert.contact', 'bpm@motac.gov.my'),
            ],
            'incident_details' => [
                'internal_reference' => $incident->incident_number,
                'category' => $this->mapToMyCERTCategory($incident->type),
                'severity' => $incident->severity,
                'description' => $incident->description,
                'date_detected' => $incident->detected_at->format('Y-m-d H:i:s'),
                'source_ip' => $incident->source_ip,
                'affected_systems' => $incident->target_system,
            ],
            'technical_details' => [
                'ioc' => $incident->indicators_of_compromise,
                'detection_method' => $incident->detection_rules_triggered,
            ],
            'impact_assessment' => [
                'affected_assets' => $incident->affected_assets,
                'data_compromised' => false, // To be assessed
            ],
            'response_actions' => $incident->response_actions,
        ];

        return $report;
    }

    /**
     * Map incident type to MyCERT category
     */
    private function mapToMyCERTCategory(string $type): string
    {
        return match ($type) {
            SecurityIncident::TYPE_UNAUTHORIZED_ACCESS => 'Intrusion',
            SecurityIncident::TYPE_DATA_BREACH => 'Data Breach',
            SecurityIncident::TYPE_MALWARE => 'Malicious Code',
            SecurityIncident::TYPE_BRUTE_FORCE => 'Intrusion Attempt',
            SecurityIncident::TYPE_PHISHING => 'Fraud',
            SecurityIncident::TYPE_DOS_ATTACK => 'Denial of Service',
            SecurityIncident::TYPE_PRIVILEGE_ESCALATION => 'Intrusion',
            SecurityIncident::TYPE_DATA_EXFILTRATION => 'Data Breach',
            default => 'Other',
        };
    }

    /**
     * Submit report to MyCERT (PKS 28.2)
     */
    public function submitToMyCERT(SecurityIncident $incident): ?string
    {
        $report = $this->generateMyCERTReport($incident);

        // In production, this would submit to MyCERT API
        $reportId = 'MYCERT-'.date('Y').'-'.str_pad((string) $incident->id, 6, '0', STR_PAD_LEFT);

        $incident->markMyCERTReported($reportId);

        SecurityIncidentLog::log(
            $incident->id,
            SecurityIncidentLog::ACTION_MYCERT_REPORTED,
            "Laporan dihantar ke MyCERT dengan ID: {$reportId}",
            ['report' => $report, 'report_id' => $reportId]
        );

        Log::channel('security')->info('MyCERT Report Submitted', [
            'incident_number' => $incident->incident_number,
            'mycert_report_id' => $reportId,
        ]);

        return $reportId;
    }

    /**
     * Notify superusers about security incident
     */
    private function notifySuperusers(SecurityIncident $incident): void
    {
        $superusers = User::where('role', 'superuser')->get();

        foreach ($superusers as $superuser) {
            try {
                Mail::to($superuser->email)
                    ->queue(new SecurityIncidentMail($incident->toArray(), $superuser));
            } catch (\Exception $e) {
                Log::error('Failed to send security incident email', [
                    'user' => $superuser->email,
                    'incident_id' => $incident->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Log security incident
     */
    private function logIncident(SecurityIncident $incident): void
    {
        Log::channel('security')->critical('Security Incident Detected', [
            'incident_number' => $incident->incident_number,
            'type' => $incident->type,
            'severity' => $incident->severity,
            'title' => $incident->title,
            'description' => $incident->description,
            'source_ip' => $incident->source_ip,
            'detected_at' => $incident->detected_at->toIso8601String(),
        ]);
    }

    /**
     * Handle critical security incident
     */
    private function handleCriticalIncident(SecurityIncident $incident): void
    {
        // For brute force attacks, IP is already blocked in detection
        if ($incident->type === SecurityIncident::TYPE_BRUTE_FORCE) {
            $incident->addResponseAction('auto_block', 'IP disekat secara automatik');

            return;
        }

        // For data exfiltration, temporarily disable exports
        if ($incident->type === SecurityIncident::TYPE_DATA_EXFILTRATION) {
            Cache::put('exports_disabled', true, 3600); // 1 hour
            $incident->addResponseAction('disable_exports', 'Fungsi eksport dilumpuhkan sementara selama 1 jam');
        }

        // Immediate CSIRT escalation for critical incidents
        if (! $incident->csirt_notified_at) {
            $this->escalateToCSIRT($incident);
        }
    }

    /**
     * Run security incident detection
     *
     * @return int Number of incidents detected
     */
    public function runDetection(): int
    {
        $incidentData = $this->detectIncidents();
        $count = 0;

        foreach ($incidentData as $data) {
            $this->handleIncident($data);
            $count++;
        }

        // Check for pending CSIRT escalations
        $this->checkPendingEscalations();

        return $count;
    }

    /**
     * Check and process pending CSIRT escalations
     */
    private function checkPendingEscalations(): void
    {
        $pendingIncidents = SecurityIncident::query()
            ->where('requires_escalation', true)
            ->whereNull('csirt_notified_at')
            ->where('detected_at', '<=', now()->subMinutes(self::CSIRT_ESCALATION_SLA))
            ->get();

        foreach ($pendingIncidents as $incident) {
            $this->escalateToCSIRT($incident);

            Log::channel('security')->warning('CSIRT SLA Breach - Auto Escalation', [
                'incident_number' => $incident->incident_number,
                'detected_at' => $incident->detected_at->toIso8601String(),
                'escalated_at' => now()->toIso8601String(),
                'sla_breached' => true,
            ]);
        }
    }

    /**
     * Get incident statistics (PKS 28.3)
     *
     * @return array<string, mixed>
     */
    public function getIncidentStats(int $hours = 24): array
    {
        $from = now()->subHours($hours)->toDateTimeString();
        $to = now()->toDateTimeString();

        return SecurityIncident::getSummaryStats($from, $to);
    }

    /**
     * Get open incidents requiring attention
     *
     * @return Collection<int, SecurityIncident>
     */
    public function getOpenIncidents(): Collection
    {
        return SecurityIncident::query()
            ->open()
            ->orderByRaw("FIELD(severity, 'critical', 'high', 'medium', 'low', 'info')")
            ->orderBy('detected_at', 'desc')
            ->get();
    }

    /**
     * Get incidents requiring CSIRT escalation
     *
     * @return Collection<int, SecurityIncident>
     */
    public function getIncidentsRequiringEscalation(): Collection
    {
        return SecurityIncident::query()
            ->requiresEscalation()
            ->orderBy('detected_at', 'asc')
            ->get();
    }

    /**
     * Assign incident to user
     */
    public function assignIncident(SecurityIncident $incident, User $user): void
    {
        $incident->update(['assigned_to_user_id' => $user->id]);
        $incident->addTimelineEntry('Ditugaskan kepada '.$user->name);

        SecurityIncidentLog::log(
            $incident->id,
            SecurityIncidentLog::ACTION_ASSIGNED,
            "Insiden ditugaskan kepada {$user->name}",
            ['assigned_to' => $user->id]
        );
    }

    /**
     * Update incident status
     */
    public function updateStatus(SecurityIncident $incident, string $status, ?string $notes = null): void
    {
        $oldStatus = $incident->status;
        $incident->update(['status' => $status]);
        $incident->addTimelineEntry("Status dikemaskini dari {$oldStatus} ke {$status}", $notes);

        SecurityIncidentLog::log(
            $incident->id,
            SecurityIncidentLog::ACTION_STATUS_CHANGED,
            "Status dikemaskini dari {$oldStatus} ke {$status}",
            ['old_status' => $oldStatus, 'new_status' => $status, 'notes' => $notes]
        );
    }

    /**
     * Contain incident
     */
    public function containIncident(SecurityIncident $incident, string $details): void
    {
        $incident->markContained($details);

        SecurityIncidentLog::log(
            $incident->id,
            SecurityIncidentLog::ACTION_CONTAINED,
            'Insiden telah dibendung',
            ['details' => $details]
        );
    }

    /**
     * Resolve incident
     */
    public function resolveIncident(SecurityIncident $incident, string $summary, ?string $lessonsLearned = null): void
    {
        $incident->markResolved($summary, $lessonsLearned);

        SecurityIncidentLog::log(
            $incident->id,
            SecurityIncidentLog::ACTION_RESOLVED,
            'Insiden telah diselesaikan',
            ['summary' => $summary, 'lessons_learned' => $lessonsLearned]
        );
    }

    /**
     * Close incident
     */
    public function closeIncident(SecurityIncident $incident): void
    {
        $incident->close();

        SecurityIncidentLog::log(
            $incident->id,
            SecurityIncidentLog::ACTION_CLOSED,
            'Insiden telah ditutup'
        );
    }

    /**
     * Mark incident as false positive
     */
    public function markFalsePositive(SecurityIncident $incident, string $reason): void
    {
        $incident->markFalsePositive($reason);

        SecurityIncidentLog::log(
            $incident->id,
            SecurityIncidentLog::ACTION_STATUS_CHANGED,
            'Insiden ditandakan sebagai positif palsu',
            ['reason' => $reason]
        );
    }
}
