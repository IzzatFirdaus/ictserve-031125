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
        return match (true) {
            in_array($grade, ['54', '52', '48', '44', '41']) => 'officer',
            in_array($grade, ['JUSA A', 'JUSA B', 'JUSA C']) => 'jusa',
            in_array($grade, ['PTK', 'KSU']) => 'executive',
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
        $requiredGrade = match (true) {
            $applicantGrade <= 54 && $assetValue < 5000 => '41',
            $applicantGrade <= 54 && $assetValue >= 5000 => '44',
            $applicantGrade >= 52 && $applicantGrade <= 48 => '44',
            $applicantGrade >= 44 && $applicantGrade <= 41 => '48',
            $applicantGrade >= 40 => 'JUSA',
            default => '41',
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
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/employees/{$staffId}/validate");

            return $response->successful() && $response->json('valid', false);
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
        Cache::forget("hrmis_user_{$staffId}");
        Cache::forget("hrmis_org_{$staffId}");

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
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/health");

            return [
                'status' => $response->successful() ? 'healthy' : 'unhealthy',
                'response_time' => $response->transferStats?->getTransferTime() ?? null,
                'last_checked' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unavailable',
                'error' => $e->getMessage(),
                'last_checked' => now()->toIso8601String(),
            ];
        }
    }
}
