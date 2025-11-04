<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

/**
 * Approval Matrix Service
 *
 * Determines appropriate approver based on applicant grade and asset value.
 *
 * @see D03-FR-002.1 Approval matrix logic
 * @see D04 §2.2 Approval routing
 */
class ApprovalMatrixService
{
    /**
     * Approval matrix configuration
     * Format: [grade => [max_value => approver_grade]]
     */
    private array $approvalMatrix = [
        '41' => [
            5000 => '44',
            10000 => '48',
            PHP_FLOAT_MAX => '52',
        ],
        '44' => [
            10000 => '48',
            20000 => '52',
            PHP_FLOAT_MAX => '54',
        ],
        '48' => [
            20000 => '52',
            PHP_FLOAT_MAX => '54',
        ],
        '52' => [
            PHP_FLOAT_MAX => '54',
        ],
    ];

    /**
     * Determine appropriate approver based on grade and asset value
     *
     * @return array Approver details [name, email, grade]
     */
    public function determineApprover(string $applicantGrade, float $totalValue): array
    {
        // Get required approver grade based on matrix
        $requiredGrade = $this->getRequiredApproverGrade($applicantGrade, $totalValue);

        // Find user with required grade who can approve
        $approver = User::where('grade', $requiredGrade)
            ->where(function ($query) {
                $query->where('role', 'approver')
                    ->orWhere('role', 'admin')
                    ->orWhere('role', 'superuser');
            })
            ->first();

        // Fallback to any Grade 54 user if no specific approver found
        if (! $approver) {
            $approver = User::where('grade', '54')
                ->where(function ($query) {
                    $query->where('role', 'approver')
                        ->orWhere('role', 'admin')
                        ->orWhere('role', 'superuser');
                })
                ->first();
        }

        // Final fallback to any superuser
        if (! $approver) {
            $approver = User::where('role', 'superuser')->first();
        }

        if (! $approver) {
            throw new \RuntimeException('No approver found in the system');
        }

        return [
            'name' => $approver->name,
            'email' => $approver->email,
            'grade' => $approver->grade ?? 'N/A',
            'user_id' => $approver->id,
        ];
    }

    /**
     * Get required approver grade based on applicant grade and value
     *
     * @return string Required approver grade
     */
    private function getRequiredApproverGrade(string $applicantGrade, float $totalValue): string
    {
        // If applicant grade not in matrix, default to Grade 54
        if (! isset($this->approvalMatrix[$applicantGrade])) {
            return '54';
        }

        $gradeMatrix = $this->approvalMatrix[$applicantGrade];

        // Find appropriate approver grade based on value
        foreach ($gradeMatrix as $maxValue => $approverGrade) {
            if ($totalValue <= $maxValue) {
                return $approverGrade;
            }
        }

        // Default to highest grade
        return '54';
    }

    /**
     * Check if user can approve based on grade and value
     */
    public function canUserApprove(User $user, string $applicantGrade, float $totalValue): bool
    {
        if (! $user->canApprove()) {
            return false;
        }

        $requiredGrade = $this->getRequiredApproverGrade($applicantGrade, $totalValue);

        // User can approve if their grade is >= required grade
        return $user->grade >= $requiredGrade;
    }
}
