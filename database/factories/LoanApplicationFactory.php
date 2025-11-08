<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Loan Application Factory
 *
 * Comprehensive factory with realistic data and state variations for testing.
 *
 * @see D03-FR-005.1 Model factories for testing
 * @see D04 §2.2 Model relationships
 *
 * @extends Factory<LoanApplication>
 */
class LoanApplicationFactory extends Factory
{
    protected $model = LoanApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+30 days');
        $endDate = fake()->dateTimeBetween($startDate, '+60 days');

        return [
            'application_number' => LoanApplication::generateApplicationNumber(),
            'user_id' => null, // Default to guest submission
            // Guest applicant fields (always populated)
            'applicant_name' => fake()->name(),
            'applicant_email' => fake()->unique()->safeEmail(),
            'applicant_phone' => fake()->numerify('01#-### ####'),
            'staff_id' => fake()->numerify('MOTAC####'),
            'grade' => fake()->randomElement(['41', '44', '48', '52', '54']),
            'division_id' => Division::factory(),
            // Application details
            'purpose' => fake()->sentence(10),
            'location' => fake()->randomElement(['Putrajaya', 'Kuala Lumpur', 'Cyberjaya', 'Shah Alam']),
            'return_location' => fake()->randomElement(['Putrajaya', 'Kuala Lumpur', 'Cyberjaya', 'Shah Alam']),
            'loan_start_date' => $startDate,
            'loan_end_date' => $endDate,
            'status' => LoanStatus::SUBMITTED,
            'priority' => LoanPriority::NORMAL,
            'total_value' => fake()->randomFloat(2, 500, 15000),
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
            // Cross-module integration
            'related_helpdesk_tickets' => null,
            'maintenance_required' => false,
        ];
    }

    /**
     * State: Authenticated submission (with user_id)
     */
    public function authenticated(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
        ]);
    }

    /**
     * State: Guest submission (no user_id)
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
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
            'approver_email' => fake()->safeEmail(),
            'approval_token' => Str::random(64),
            'approval_token_expires_at' => now()->addDays(7),
        ]);
    }

    /**
     * State: Approved
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::APPROVED,
            'approver_email' => fake()->safeEmail(),
            'approved_by_name' => fake()->name(),
            'approved_at' => now()->subDays(fake()->numberBetween(1, 5)),
            'approval_token' => null,
            'approval_token_expires_at' => null,
        ]);
    }

    /**
     * State: Rejected
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::REJECTED,
            'approver_email' => fake()->safeEmail(),
            'approved_by_name' => fake()->name(),
            'rejected_reason' => fake()->sentence(15),
            'approval_token' => null,
            'approval_token_expires_at' => null,
        ]);
    }

    /**
     * State: Issued (assets given to user)
     */
    public function issued(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::ISSUED,
            'approver_email' => fake()->safeEmail(),
            'approved_by_name' => fake()->name(),
            'approved_at' => now()->subDays(fake()->numberBetween(5, 10)),
        ]);
    }

    /**
     * State: In use (currently borrowed)
     */
    public function inUse(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::IN_USE,
            'loan_start_date' => now()->subDays(fake()->numberBetween(1, 10)),
            'loan_end_date' => now()->addDays(fake()->numberBetween(5, 20)),
            'approver_email' => fake()->safeEmail(),
            'approved_by_name' => fake()->name(),
            'approved_at' => now()->subDays(fake()->numberBetween(10, 15)),
        ]);
    }

    /**
     * State: Overdue
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::OVERDUE,
            'loan_start_date' => now()->subDays(fake()->numberBetween(20, 40)),
            'loan_end_date' => now()->subDays(fake()->numberBetween(1, 10)),
            'approver_email' => fake()->safeEmail(),
            'approved_by_name' => fake()->name(),
            'approved_at' => now()->subDays(fake()->numberBetween(30, 50)),
        ]);
    }

    /**
     * State: Returned
     */
    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::RETURNED,
            'loan_start_date' => now()->subDays(fake()->numberBetween(20, 40)),
            'loan_end_date' => now()->subDays(fake()->numberBetween(5, 15)),
            'approver_email' => fake()->safeEmail(),
            'approved_by_name' => fake()->name(),
            'approved_at' => now()->subDays(fake()->numberBetween(30, 50)),
        ]);
    }

    /**
     * State: Completed
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::COMPLETED,
            'loan_start_date' => now()->subDays(fake()->numberBetween(30, 60)),
            'loan_end_date' => now()->subDays(fake()->numberBetween(10, 25)),
            'approver_email' => fake()->safeEmail(),
            'approved_by_name' => fake()->name(),
            'approved_at' => now()->subDays(fake()->numberBetween(40, 70)),
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
                fake()->numberBetween(1000, 9999),
                fake()->numberBetween(1000, 9999),
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
            'approver_email' => fake()->safeEmail(),
            'approval_token' => Str::random(64),
            'approval_token_expires_at' => now()->subDays(1),
        ]);
    }
}
