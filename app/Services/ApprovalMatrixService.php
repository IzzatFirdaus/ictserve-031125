<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Approval Matrix Service
 *
 * Manages approval matrix configuration with grade-based routing rules,
 * asset value thresholds, and approver assignment logic.
 *
 * @trace Requirements 12.1, 5.1, 5.5
 */
class ApprovalMatrixService
{
    private const CACHE_TTL = 3600; // 1 hour

    private const CACHE_KEY = 'approval_matrix_config';

    public function getApprovalMatrix(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->loadDefaultMatrix();
        });
    }

    public function updateApprovalMatrix(array $matrix): void
    {
        // Validate matrix structure
        $this->validateMatrix($matrix);

        // Store in cache and configuration
        Cache::put(self::CACHE_KEY, $matrix, self::CACHE_TTL);

        // Log configuration change
        Log::info('Approval matrix updated', [
            'user_id' => auth()->id(),
            'matrix_rules' => count($matrix['rules'] ?? []),
            'updated_at' => now(),
        ]);
    }

    public function getApproversForLoan(array $loanData): array
    {
        $matrix = $this->getApprovalMatrix();
        $approvers = [];

        foreach ($matrix['rules'] as $rule) {
            if ($this->matchesRule($rule, $loanData)) {
                $approvers = array_merge($approvers, $this->getApproversFromRule($rule));
            }
        }

        return array_unique($approvers, SORT_REGULAR);
    }

    protected function matchesRule(array $rule, array $loanData): bool
    {
        // Check asset value threshold
        if (isset($rule['asset_value_min']) && $loanData['total_value'] < $rule['asset_value_min']) {
            return false;
        }

        if (isset($rule['asset_value_max']) && $loanData['total_value'] > $rule['asset_value_max']) {
            return false;
        }

        // Check applicant grade
        if (isset($rule['applicant_grade_min']) && $loanData['applicant_grade'] < $rule['applicant_grade_min']) {
            return false;
        }

        if (isset($rule['applicant_grade_max']) && $loanData['applicant_grade'] > $rule['applicant_grade_max']) {
            return false;
        }

        // Check loan duration
        if (isset($rule['duration_days_min']) && $loanData['duration_days'] < $rule['duration_days_min']) {
            return false;
        }

        if (isset($rule['duration_days_max']) && $loanData['duration_days'] > $rule['duration_days_max']) {
            return false;
        }

        // Check asset category
        if (isset($rule['asset_categories']) && ! empty($rule['asset_categories'])) {
            $loanCategories = $loanData['asset_categories'] ?? [];
            if (empty(array_intersect($rule['asset_categories'], $loanCategories))) {
                return false;
            }
        }

        return true;
    }

    protected function getApproversFromRule(array $rule): array
    {
        $approvers = [];

        // Get approvers by role
        if (! empty($rule['approver_roles'])) {
            $roleApprovers = User::whereHas('roles', function ($query) use ($rule) {
                $query->whereIn('name', $rule['approver_roles']);
            })->where('is_active', true)->get();

            foreach ($roleApprovers as $approver) {
                $approvers[] = [
                    'user_id' => $approver->id,
                    'name' => $approver->name,
                    'email' => $approver->email,
                    'role' => $approver->role,
                    'level' => $rule['approval_level'] ?? 1,
                    'required' => $rule['required'] ?? true,
                ];
            }
        }

        // Get approvers by grade
        if (! empty($rule['approver_grades'])) {
            $gradeApprovers = User::whereIn('grade_id', $rule['approver_grades'])
                ->where('is_active', true)
                ->get();

            foreach ($gradeApprovers as $approver) {
                $approvers[] = [
                    'user_id' => $approver->id,
                    'name' => $approver->name,
                    'email' => $approver->email,
                    'role' => $approver->role,
                    'level' => $rule['approval_level'] ?? 1,
                    'required' => $rule['required'] ?? true,
                ];
            }
        }

        // Get specific approvers
        if (! empty($rule['specific_approvers'])) {
            $specificApprovers = User::whereIn('id', $rule['specific_approvers'])
                ->where('is_active', true)
                ->get();

            foreach ($specificApprovers as $approver) {
                $approvers[] = [
                    'user_id' => $approver->id,
                    'name' => $approver->name,
                    'email' => $approver->email,
                    'role' => $approver->role,
                    'level' => $rule['approval_level'] ?? 1,
                    'required' => $rule['required'] ?? true,
                ];
            }
        }

        return $approvers;
    }

    protected function validateMatrix(array $matrix): void
    {
        if (! isset($matrix['rules']) || ! is_array($matrix['rules'])) {
            throw new \InvalidArgumentException('Approval matrix must contain rules array');
        }

        foreach ($matrix['rules'] as $index => $rule) {
            if (! is_array($rule)) {
                throw new \InvalidArgumentException("Rule at index {$index} must be an array");
            }

            // Validate required fields
            if (empty($rule['name'])) {
                throw new \InvalidArgumentException("Rule at index {$index} must have a name");
            }

            // Validate approver configuration
            if (empty($rule['approver_roles']) && empty($rule['approver_grades']) && empty($rule['specific_approvers'])) {
                throw new \InvalidArgumentException("Rule '{$rule['name']}' must specify at least one approver type");
            }

            // Validate value ranges
            if (isset($rule['asset_value_min'], $rule['asset_value_max']) && $rule['asset_value_min'] > $rule['asset_value_max']) {
                throw new \InvalidArgumentException("Rule '{$rule['name']}' has invalid asset value range");
            }

            if (isset($rule['duration_days_min'], $rule['duration_days_max']) && $rule['duration_days_min'] > $rule['duration_days_max']) {
                throw new \InvalidArgumentException("Rule '{$rule['name']}' has invalid duration range");
            }
        }
    }

    protected function loadDefaultMatrix(): array
    {
        return [
            'version' => '1.0',
            'updated_at' => now()->toISOString(),
            'rules' => [
                [
                    'id' => 'low_value_standard',
                    'name' => 'Pinjaman Nilai Rendah (Standard)',
                    'description' => 'Pinjaman aset bernilai rendah untuk staf biasa',
                    'priority' => 1,
                    'asset_value_min' => 0,
                    'asset_value_max' => 5000,
                    'applicant_grade_min' => 1,
                    'applicant_grade_max' => 40,
                    'duration_days_max' => 30,
                    'approver_roles' => ['admin'],
                    'approval_level' => 1,
                    'required' => true,
                    'auto_approve' => false,
                ],
                [
                    'id' => 'medium_value_standard',
                    'name' => 'Pinjaman Nilai Sederhana (Standard)',
                    'description' => 'Pinjaman aset bernilai sederhana untuk staf biasa',
                    'priority' => 2,
                    'asset_value_min' => 5001,
                    'asset_value_max' => 15000,
                    'applicant_grade_min' => 1,
                    'applicant_grade_max' => 40,
                    'duration_days_max' => 60,
                    'approver_roles' => ['admin', 'superuser'],
                    'approval_level' => 2,
                    'required' => true,
                    'auto_approve' => false,
                ],
                [
                    'id' => 'high_value_any',
                    'name' => 'Pinjaman Nilai Tinggi (Semua Staf)',
                    'description' => 'Pinjaman aset bernilai tinggi memerlukan kelulusan superuser',
                    'priority' => 3,
                    'asset_value_min' => 15001,
                    'asset_value_max' => null,
                    'approver_roles' => ['superuser'],
                    'approval_level' => 3,
                    'required' => true,
                    'auto_approve' => false,
                ],
                [
                    'id' => 'senior_staff_expedited',
                    'name' => 'Staf Kanan (Dipercepat)',
                    'description' => 'Pinjaman untuk staf gred 41 ke atas dengan proses dipercepat',
                    'priority' => 4,
                    'asset_value_max' => 10000,
                    'applicant_grade_min' => 41,
                    'duration_days_max' => 90,
                    'approver_roles' => ['admin'],
                    'approval_level' => 1,
                    'required' => true,
                    'auto_approve' => false,
                ],
                [
                    'id' => 'long_term_special',
                    'name' => 'Pinjaman Jangka Panjang (Khas)',
                    'description' => 'Pinjaman lebih dari 90 hari memerlukan kelulusan khas',
                    'priority' => 5,
                    'duration_days_min' => 91,
                    'approver_roles' => ['superuser'],
                    'approval_level' => 3,
                    'required' => true,
                    'auto_approve' => false,
                ],
            ],
        ];
    }

    public function getApprovalLevels(): array
    {
        return [
            1 => [
                'name' => 'Tahap 1 - Pentadbir',
                'description' => 'Kelulusan oleh pentadbir sistem',
                'roles' => ['admin'],
            ],
            2 => [
                'name' => 'Tahap 2 - Pentadbir Kanan',
                'description' => 'Kelulusan oleh pentadbir kanan',
                'roles' => ['admin', 'superuser'],
            ],
            3 => [
                'name' => 'Tahap 3 - Superuser',
                'description' => 'Kelulusan oleh superuser sahaja',
                'roles' => ['superuser'],
            ],
        ];
    }

    public function getAvailableRoles(): array
    {
        return [
            'admin' => 'Pentadbir',
            'superuser' => 'Superuser',
        ];
    }

    public function getAvailableGrades(): array
    {
        return [
            1 => 'Gred 1-10',
            2 => 'Gred 11-20',
            3 => 'Gred 21-30',
            4 => 'Gred 31-40',
            5 => 'Gred 41-50',
            6 => 'Gred 51+',
        ];
    }

    public function testApprovalMatrix(array $testData): array
    {
        $results = [];

        foreach ($testData as $test) {
            $approvers = $this->getApproversForLoan($test['loan_data']);
            $results[] = [
                'test_name' => $test['name'],
                'loan_data' => $test['loan_data'],
                'expected_approvers' => $test['expected_approvers'] ?? [],
                'actual_approvers' => $approvers,
                'passed' => $this->compareApprovers($test['expected_approvers'] ?? [], $approvers),
            ];
        }

        return $results;
    }

    protected function compareApprovers(array $expected, array $actual): bool
    {
        if (count($expected) !== count($actual)) {
            return false;
        }

        $expectedIds = array_column($expected, 'user_id');
        $actualIds = array_column($actual, 'user_id');

        sort($expectedIds);
        sort($actualIds);

        return $expectedIds === $actualIds;
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function exportMatrix(): array
    {
        return [
            'matrix' => $this->getApprovalMatrix(),
            'levels' => $this->getApprovalLevels(),
            'roles' => $this->getAvailableRoles(),
            'grades' => $this->getAvailableGrades(),
            'exported_at' => now()->toISOString(),
            'exported_by' => auth()->user()?->name,
        ];
    }

    public function importMatrix(array $data): void
    {
        if (! isset($data['matrix'])) {
            throw new \InvalidArgumentException('Import data must contain matrix');
        }

        $this->updateApprovalMatrix($data['matrix']);

        Log::info('Approval matrix imported', [
            'user_id' => auth()->id(),
            'imported_at' => now(),
            'rules_count' => count($data['matrix']['rules'] ?? []),
        ]);
    }

    /**
     * Determine appropriate approver based on applicant grade and asset value
     *
     * @param  string  $applicantGrade  Applicant's grade level
     * @param  float  $assetValue  Total asset value
     * @return array Approver information (user_id, name, email, grade)
     *
     * @throws \RuntimeException When no suitable approver found
     */
    public function determineApprover(string $applicantGrade, float $assetValue): array
    {
        $applicantGradeLevel = (int) $applicantGrade;
        $requiredApproverGrade = $this->getRequiredApproverGrade($applicantGradeLevel, $assetValue);

        // Try to find approver with exact grade
        $approver = User::whereHas('grade', function ($query) use ($requiredApproverGrade) {
            $query->where('level', $requiredApproverGrade);
        })
            ->whereIn('role', ['approver', 'admin', 'superuser'])
            ->where('is_active', true)
            ->first();

        // Fallback to Grade 54 if specific grade not found
        if (! $approver) {
            $approver = User::whereHas('grade', function ($query) {
                $query->where('level', 54);
            })
                ->whereIn('role', ['approver', 'admin', 'superuser'])
                ->where('is_active', true)
                ->first();
        }

        // Fallback to any superuser
        if (! $approver) {
            $approver = User::where('role', 'superuser')
                ->where('is_active', true)
                ->first();
        }

        if (! $approver) {
            throw new \RuntimeException('No approver found in the system');
        }

        return [
            'user_id' => $approver->id,
            'name' => $approver->name,
            'email' => $approver->email,
            'grade' => (string) ($approver->grade->level ?? $requiredApproverGrade),
            'role' => $approver->role,
        ];
    }

    /**
     * Check if user can approve based on grade and asset value
     *
     * @param  User  $user  User to check
     * @param  string  $applicantGrade  Applicant's grade level
     * @param  float  $assetValue  Total asset value
     * @return bool True if user can approve
     */
    public function canUserApprove(User $user, string $applicantGrade, float $assetValue): bool
    {
        // Non-approvers cannot approve
        if (! in_array($user->role, ['approver', 'admin', 'superuser'])) {
            return false;
        }

        $applicantGradeLevel = (int) $applicantGrade;
        $requiredApproverGrade = $this->getRequiredApproverGrade($applicantGradeLevel, $assetValue);
        $userGradeLevel = $user->grade->level ?? 0;

        // User must meet or exceed required grade
        return $userGradeLevel >= $requiredApproverGrade;
    }

    /**
     * Get required approver grade based on applicant grade and asset value
     *
     * @param  int  $applicantGrade  Applicant's grade level
     * @param  float  $assetValue  Total asset value
     * @return int Required approver grade level
     */
    protected function getRequiredApproverGrade(int $applicantGrade, float $assetValue): int
    {
        // Grade 52 applicants always require Grade 54 approval
        if ($applicantGrade >= 52) {
            return 54;
        }

        // Grade 44 applicants
        if ($applicantGrade >= 44) {
            if ($assetValue > 20000) {
                return 54;
            }
            if ($assetValue > 10000) {
                return 52;
            }

            return 48;
        }

        // Grade 41-43 applicants (same as Grade 44)
        if ($applicantGrade >= 41) {
            if ($assetValue > 10000) {
                return 52;
            }
            if ($assetValue > 5000) {
                return 48;
            }

            return 44;
        }

        // Default for unknown grades: Grade 54
        return 54;
    }
}
