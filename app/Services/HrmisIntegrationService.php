<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * HRMIS Integration Service
 *
 * Manages integration with HRMIS (Human Resource Management Information System)
 * for user data synchronization, organizational structure, and approval authority mapping.
 *
 * @see D03-FR-002.1 Grade-based approval matrix
 * @see D03-FR-006.1 Automated approval routing
 * @see D04 §6.3 External system integration
 */
class HrmisIntegrationService
{
    private string $baseUrl;

    private string $apiKey;

    private int $timeout;

    private int $cacheMinutes;

    public function __construct()
    {
        $this->baseUrl = config('services.hrmis.base_url', '');
        $this->apiKey = config('services.hrmis.api_key', '');
        $this->timeout = config('services.hrmis.timeout', 30);
        $this->cacheMinutes = config('services.hrmis.cache_minutes', 60);
    }

    /**
     * Sync user profile data from HRMIS
     *
     * @param  string  $staffId  Staff ID to sync
     * @return array User data from HRMIS
     *
     * @throws \Exception If HRMIS API is unavailable or returns error
     */
    public function syncUserProfile(string $staffId): array
    {
        $cacheKey = "hrmis_user_{$staffId}";

        return Cache::remember($cacheKey, $this->cacheMinutes * 60, function () use ($staffId) {
            try {
                // HTTP headers (not credentials - API key loaded from config)
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->apiKey}",
                        'Accept' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/api/v1/employees/{$staffId}");

                if ($response->successful()) {
                    $data = $response->json();

                    Log::info('HRMIS user profile synced successfully', [
                        'staff_id' => $staffId,
                        'name' => $data['name'] ?? null,
                    ]);

                    return $data;
                }

                throw new \Exception("HRMIS API returned error: {$response->status()}");
            } catch (\Exception $e) {
                Log::error('Failed to sync user profile from HRMIS', [
                    'staff_id' => $staffId,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }

    /**
     * Get user's organizational data from HRMIS
     *
     * @param  string  $staffId  Staff ID
     * @return array Organizational data (department, division, grade, etc.)
     */
    public function getUserOrganizationalData(string $staffId): array
    {
        $cacheKey = "hrmis_org_{$staffId}";

        return Cache::remember($cacheKey, $this->cacheMinutes * 60, function () use ($staffId) {
            try {
                // HTTP headers (not credentials - API key loaded from config)
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->apiKey}",
                        'Accept' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/api/v1/employees/{$staffId}/organization");

                if ($response->successful()) {
                    return $response->json();
                }

                Log::warning('Failed to fetch organizational data from HRMIS', [
                    'staff_id' => $staffId,
                    'status' => $response->status(),
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error('HRMIS organizational data fetch error', [
                    'staff_id' => $staffId,
                    'error' => $e->getMessage(),
                ]);

                return [];
            }
        });
    }

    /**
     * Map HRMIS grade to approval authority level
     *
     * @param  string  $grade  HRMIS grade code (e.g., "41", "44", "48", "JUSA")
     * @return string Approval authority level
     */
    public function mapGradeToApprovalAuthority(string $grade): string
    {
        // Grade mapping based on MOTAC approval matrix
        // @see D03-FR-002.1 Grade-based approval matrix
        $officerGrades = config('hrmis.officer_grades', ['54', '52', '48', '44', '41']);
        $jusaGrades = config('hrmis.jusa_grades', ['JUSA A', 'JUSA B', 'JUSA C']);
        $executiveGrades = config('hrmis.executive_grades', ['PTK', 'KSU']);

        return match (true) {
            in_array($grade, $officerGrades) => 'officer',
            in_array($grade, $jusaGrades) => 'jusa',
            in_array($grade, $executiveGrades) => 'executive',
            default => 'staff',
        };
    }

    /**
     * Get approval authority for staff based on grade and asset value
     *
     * @param  string  $staffId  Staff ID
     * @param  float  $assetValue  Total asset value
     * @return array Approver details
     */
    public function determineApprover(string $staffId, float $assetValue): array
    {
        $orgData = $this->getUserOrganizationalData($staffId);
        $applicantGrade = $orgData['grade'] ?? null;

        if (! $applicantGrade) {
            throw new \Exception('Unable to determine applicant grade from HRMIS');
        }

        // Approval matrix logic
        // @see D03-FR-002.1 Grade-based approval matrix
        $assetThreshold = config('hrmis.asset_value_threshold', 5000);
        $gradeThresholds = config('hrmis.grade_thresholds', [
            'max_officer' => 54,
            'min_senior' => 52,
            'max_senior' => 48,
            'min_manager' => 44,
            'max_manager' => 41,
            'min_executive' => 40,
        ]);
        $approverGrades = config('hrmis.approver_grades', [
            'junior' => '41',
            'senior' => '44',
            'manager' => '48',
            'executive' => 'JUSA',
        ]);

        $requiredGrade = match (true) {
            $applicantGrade <= $gradeThresholds['max_officer'] && $assetValue < $assetThreshold => $approverGrades['junior'],
            $applicantGrade <= $gradeThresholds['max_officer'] && $assetValue >= $assetThreshold => $approverGrades['senior'],
            $applicantGrade >= $gradeThresholds['min_senior'] && $applicantGrade <= $gradeThresholds['max_senior'] => $approverGrades['senior'],
            $applicantGrade >= $gradeThresholds['min_manager'] && $applicantGrade <= $gradeThresholds['max_manager'] => $approverGrades['manager'],
            $applicantGrade >= $gradeThresholds['min_executive'] => $approverGrades['executive'],
            default => $approverGrades['junior'],
        };

        return $this->findApproverByGrade($requiredGrade, $orgData['department_id'] ?? null);
    }

    /**
     * Find approver by grade level in department
     *
     * @param  string  $requiredGrade  Required grade level
     * @param  int|null  $departmentId  Department ID
     * @return array Approver details
     */
    private function findApproverByGrade(string $requiredGrade, ?int $departmentId): array
    {
        $cacheKey = "hrmis_approver_{$requiredGrade}_{$departmentId}";

        return Cache::remember($cacheKey, $this->cacheMinutes * 60, function () use ($requiredGrade, $departmentId) {
            try {
                // HTTP headers (not credentials - API key loaded from config)
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->apiKey}",
                        'Accept' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/api/v1/approvers", [
                        'grade' => $requiredGrade,
                        'department_id' => $departmentId,
                    ]);

                if ($response->successful()) {
                    $approvers = $response->json('data', []);

                    if (empty($approvers)) {
                        throw new \Exception("No approver found for grade {$requiredGrade}");
                    }

                    return $approvers[0]; // Return first available approver
                }

                throw new \Exception("Failed to fetch approvers from HRMIS: {$response->status()}");
            } catch (\Exception $e) {
                Log::error('Failed to find approver from HRMIS', [
                    'required_grade' => $requiredGrade,
                    'department_id' => $departmentId,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }

    /**
     * Sync department and division structure from HRMIS
     *
     * @return array Department and division data
     */
    public function syncOrganizationalStructure(): array
    {
        $cacheKey = 'hrmis_org_structure';

        return Cache::remember($cacheKey, $this->cacheMinutes * 60, function () {
            try {
                // HTTP headers (not credentials - API key loaded from config)
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->apiKey}",
                        'Accept' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/api/v1/organization/structure");

                if ($response->successful()) {
                    $structure = $response->json();

                    Log::info('HRMIS organizational structure synced successfully', [
                        'departments_count' => count($structure['departments'] ?? []),
                        'divisions_count' => count($structure['divisions'] ?? []),
                    ]);

                    return $structure;
                }

                throw new \Exception("Failed to sync organizational structure: {$response->status()}");
            } catch (\Exception $e) {
                Log::error('HRMIS organizational structure sync failed', [
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }

    /**
     * Validate staff ID against HRMIS
     *
     * @param  string  $staffId  Staff ID to validate
     * @return bool True if valid, false otherwise
     */
    public function validateStaffId(string $staffId): bool
    {
        try {
            // HTTP headers (not credentials - API key loaded from config)
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/employees/{$staffId}/validate");

            return $response->successful() && $response->json('valid', config('services.hrmis.default_validation_result', false));
        } catch (\Exception $e) {
            Log::error('HRMIS staff ID validation failed', [
                'staff_id' => $staffId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Clear HRMIS cache for specific staff ID
     *
     * @param  string  $staffId  Staff ID
     */
    public function clearUserCache(string $staffId): void
    {
        Cache::forget(config('services.hrmis.cache_prefix_user', 'hrmis_user_').$staffId);
        Cache::forget(config('services.hrmis.cache_prefix_org', 'hrmis_org_').$staffId);

        Log::info('HRMIS cache cleared for user', ['staff_id' => $staffId]);
    }

    /**
     * Clear all HRMIS caches
     */
    public function clearAllCaches(): void
    {
        Cache::forget('hrmis_org_structure');

        Log::info('All HRMIS caches cleared');
    }

    /**
     * Check HRMIS API health status
     *
     * @return array Health status information
     */
    public function checkHealthStatus(): array
    {
        try {
            $healthCheckTimeout = config('services.hrmis.health_check_timeout', 5);
            // HTTP headers (not credentials - API key loaded from config)
            $response = Http::timeout($healthCheckTimeout)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/health");

            // Status strings (not credentials - configuration values only)
            $healthyStatus = config('services.hrmis.status_healthy', 'healthy');
            $unhealthyStatus = config('services.hrmis.status_unhealthy', 'unhealthy');

            return [
                'status' => $response->successful() ? $healthyStatus : $unhealthyStatus,
                'response_time' => $response->transferStats?->getTransferTime() ?? null,
                'last_checked' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            // Status string (not credentials - configuration value only)
            $unavailableStatus = config('services.hrmis.status_unavailable', 'unavailable');

            return [
                'status' => $unavailableStatus,
                'error' => $e->getMessage(),
                'last_checked' => now()->toIso8601String(),
            ];
        }
    }
}
