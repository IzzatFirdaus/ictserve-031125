<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Loan Application Factory
 *
 * PKS 5.2.1 Compliant - All applications require authenticated user_id.
 * Guest submission functionality has been removed per PKS Accountability requirements.
 *
 * @see D03-FR-001.4 (Authenticated loan application submission)
 * @see D04 §6.2 (Authentication Architecture)
 *
 * @extends Factory<LoanApplication>
 */
class LoanApplicationFactory extends Factory
{
    protected $model = LoanApplication::class;

    /**
     * Configure the factory to create a LoanItem with an Asset after creation.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (LoanApplication $application) {
            // Check if the model has a flag to skip loan item creation (set by withoutLoanItems state)
            if ($application->skipLoanItemsCreation ?? false) {
                return;
            }

            // Create a dedicated asset for this loan item to avoid clashing
            // with assets that tests explicitly attach to the application.
            // Prefer to reuse existing categories to prevent creating new
            // AssetCategory rows which may introduce duplicate name collisions.
            $existingCategoryId = AssetCategory::query()->inRandomOrder()->value('id') ?? AssetCategory::factory()->create()->id;
            $asset = Asset::factory()->create(['category_id' => $existingCategoryId]);

            // Create a LoanItem linking this application to an asset
            LoanItem::factory()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'quantity' => 1,
            ]);
        });
    }

    /**
     * State: Skip automatic LoanItem creation (for tests that manually create them).
     */
    public function withoutLoanItems(): static
    {
        return $this->afterMaking(function (LoanApplication $application) {
            // Set a temporary property on the model to signal configure() to skip loan item creation
            $application->skipLoanItemsCreation = true;
        });
    }

    /**
     * Define the model's default state.
     *
     * PKS 5.2.1: All loan applications must have authenticated user_id (NOT NULL).
     * Guest submission functionality has been removed per PKS Accountability requirements.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $positions = ['Pegawai Tadbir', 'Penolong Pegawai Tadbir', 'Pembantu Tadbir', 'Juruteknik'];
        $grades = ['41', '44', '48', '52', '54'];
        $locations = ['Putrajaya', 'Kuala Lumpur', 'Cyberjaya', 'Shah Alam'];

        $startDate = now()->addDays(\random_int(1, 30));
        $endDate = (clone $startDate)->addDays(\random_int(1, 60));

        return [
            'application_number' => LoanApplication::generateApplicationNumber(),
            'user_id' => User::factory(), // PKS 5.2.1: Mandatory authenticated user
            // Applicant details (populated from authenticated user)
            'applicant_position' => $positions[\array_rand($positions)],
            'applicant_grade' => $grades[\array_rand($grades)],
            'staff_id' => 'MOTAC'.\random_int(1000, 9999),
            'grade' => $grades[\array_rand($grades)],
            'division_id' => Division::factory(),
            // Application details
            'purpose' => 'Loan request for '.['conference', 'training', 'exhibition', 'project', 'event'][\array_rand(['conference', 'training', 'exhibition', 'project', 'event'])],
            'location' => $locations[\array_rand($locations)],
            'return_location' => $locations[\array_rand($locations)],
            'loan_start_date' => $startDate,
            'loan_end_date' => $endDate,
            'expected_return_date' => $endDate,
            'status' => LoanStatus::SUBMITTED->value,
            'priority' => LoanPriority::NORMAL->value,
            'total_value' => \random_int(50000, 1500000) / 100,
            // Email approval workflow (null by default)
            'approver_email' => null,
            'approved_by_name' => null,
            'approved_at' => null,
            'approval_token' => null,
            'approval_token_expires_at' => null,
            'approval_method' => null,
            'approval_remarks' => null,
            'rejected_reason' => null,
            'special_instructions' => null,
            // Responsible Officer sponsorship (null by default)
            'responsible_officer_name' => null,
            'responsible_officer_email' => null,
            'sponsorship_token' => null,
            'sponsorship_token_expires_at' => null,
            // Cross-module integration
            'related_helpdesk_tickets' => null,
            'maintenance_required' => false,
        ];
    }

    /**
     * State: With specific user
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * State: Draft status
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::DRAFT,
        ]);
    }

    /**
     * State: Submitted status
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::SUBMITTED,
        ]);
    }

    /**
     * State: Under review with approval token
     */
    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::UNDER_REVIEW,
            'approver_email' => 'approver'.\random_int(1000, 9999).'@motac.gov.my',
            'approval_token' => Str::random(64),
            'approval_token_expires_at' => now()->addDays(7),
        ]);
    }

    /**
     * State: Approved
     */
    public function approved(): static
    {
        $names = ['Dato Ahmad', 'Dr. Siti', 'Tuan Hassan', 'Pn. Zainab', 'Encik Ibrahim'];

        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::APPROVED,
            'approver_email' => 'approver'.\random_int(1000, 9999).'@motac.gov.my',
            'approved_by_name' => $names[\array_rand($names)],
            'approved_at' => now()->subDays(\random_int(1, 5)),
            'approval_token' => null,
            'approval_token_expires_at' => null,
        ]);
    }

    /**
     * State: Rejected
     */
    public function rejected(): static
    {
        $reasons = ['Budget not approved', 'Equipment not available', 'Invalid purpose', 'Incorrect documentation'];
        $names = ['Dato Ahmad', 'Dr. Siti', 'Tuan Hassan', 'Pn. Zainab', 'Encik Ibrahim'];

        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::REJECTED,
            'approver_email' => 'approver'.\random_int(1000, 9999).'@motac.gov.my',
            'approved_by_name' => $names[\array_rand($names)],
            'rejected_reason' => $reasons[\array_rand($reasons)],
            'approval_token' => null,
            'approval_token_expires_at' => null,
        ]);
    }

    /**
     * State: Issued (assets given to user)
     */
    public function issued(): static
    {
        $names = ['Dato Ahmad', 'Dr. Siti', 'Tuan Hassan', 'Pn. Zainab', 'Encik Ibrahim'];

        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::ISSUED,
            'approver_email' => 'approver'.\random_int(1000, 9999).'@motac.gov.my',
            'approved_by_name' => $names[\array_rand($names)],
            'approved_at' => now()->subDays(\random_int(5, 10)),
        ]);
    }

    /**
     * State: In use (currently borrowed)
     */
    public function inUse(): static
    {
        $names = ['Dato Ahmad', 'Dr. Siti', 'Tuan Hassan', 'Pn. Zainab', 'Encik Ibrahim'];

        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::IN_USE,
            'loan_start_date' => now()->subDays(\random_int(1, 10)),
            'loan_end_date' => now()->addDays(\random_int(5, 20)),
            'approver_email' => 'approver'.\random_int(1000, 9999).'@motac.gov.my',
            'approved_by_name' => $names[\array_rand($names)],
            'approved_at' => now()->subDays(\random_int(10, 15)),
        ]);
    }

    /**
     * State: Overdue
     */
    public function overdue(): static
    {
        $names = ['Dato Ahmad', 'Dr. Siti', 'Tuan Hassan', 'Pn. Zainab', 'Encik Ibrahim'];

        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::OVERDUE,
            'loan_start_date' => now()->subDays(\random_int(20, 40)),
            'loan_end_date' => now()->subDays(\random_int(1, 10)),
            'approver_email' => 'approver'.\random_int(1000, 9999).'@motac.gov.my',
            'approved_by_name' => $names[\array_rand($names)],
            'approved_at' => now()->subDays(\random_int(30, 50)),
        ]);
    }

    /**
     * State: Returned
     */
    public function returned(): static
    {
        $names = ['Dato Ahmad', 'Dr. Siti', 'Tuan Hassan', 'Pn. Zainab', 'Encik Ibrahim'];

        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::RETURNED,
            'loan_start_date' => now()->subDays(\random_int(20, 40)),
            'loan_end_date' => now()->subDays(\random_int(5, 15)),
            'approver_email' => 'approver'.\random_int(1000, 9999).'@motac.gov.my',
            'approved_by_name' => $names[\array_rand($names)],
            'approved_at' => now()->subDays(\random_int(30, 50)),
        ]);
    }

    /**
     * State: Completed
     */
    public function completed(): static
    {
        $names = ['Dato Ahmad', 'Dr. Siti', 'Tuan Hassan', 'Pn. Zainab', 'Encik Ibrahim'];

        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::COMPLETED,
            'loan_start_date' => now()->subDays(\random_int(30, 60)),
            'loan_end_date' => now()->subDays(\random_int(10, 25)),
            'approver_email' => 'approver'.\random_int(1000, 9999).'@motac.gov.my',
            'approved_by_name' => $names[\array_rand($names)],
            'approved_at' => now()->subDays(\random_int(40, 70)),
        ]);
    }

    /**
     * State: High priority
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => LoanPriority::HIGH,
        ]);
    }

    /**
     * State: Urgent priority
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => LoanPriority::URGENT,
        ]);
    }

    /**
     * State: With helpdesk integration
     */
    public function withHelpdeskIntegration(): static
    {
        return $this->state(fn (array $attributes) => [
            'related_helpdesk_tickets' => [
                \random_int(1000, 9999),
                \random_int(1000, 9999),
            ],
            'maintenance_required' => true,
        ]);
    }

    /**
     * State: Expired approval token
     */
    public function expiredToken(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::UNDER_REVIEW,
            'approver_email' => 'approver'.\random_int(1000, 9999).'@motac.gov.my',
            'approval_token' => Str::random(64),
            'approval_token_expires_at' => now()->subDays(1),
        ]);
    }
}
