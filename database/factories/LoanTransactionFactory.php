<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AssetCondition;
use App\Enums\TransactionType;
use App\Models\Asset;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Loan Transaction Factory
 *
 * Factory for loan transaction audit records.
 *
 * @see D03-FR-005.1 Model factories for testing
 * @see D03-FR-010.2 Comprehensive audit logging
 *
 * @extends Factory<LoanTransaction>
 */
class LoanTransactionFactory extends Factory
{
    protected $model = LoanTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'loan_application_id' => LoanApplication::factory(),
            'asset_id' => Asset::factory(),
            'transaction_type' => TransactionType::ISSUE,
            'processed_by' => User::factory(),
            'processed_at' => now(),
            'condition_before' => null,
            'condition_after' => null,
            'accessories' => null,
            'damage_report' => null,
            'notes' => fake()->optional(0.3)->sentence(10),
        ];
    }

    /**
     * State: Issue transaction
     */
    public function issue(): static
    {
        return $this->state(function (array $attributes) {
            $accessories = fake()->randomElements([
                'Power Adapter',
                'Carrying Case',
                'Wireless Mouse',
                'USB-C Hub',
                'HDMI Cable',
            ], fake()->numberBetween(2, 4));

            return [
                'transaction_type' => TransactionType::ISSUE,
                'condition_before' => fake()->randomElement([
                    AssetCondition::EXCELLENT,
                    AssetCondition::GOOD,
                    AssetCondition::FAIR,
                ]),
                'accessories' => $accessories,
                'notes' => 'Asset issued to borrower',
            ];
        });
    }

    /**
     * State: Return transaction
     */
    public function return(): static
    {
        return $this->state(function (array $attributes) {
            $conditionBefore = fake()->randomElement([
                AssetCondition::EXCELLENT,
                AssetCondition::GOOD,
                AssetCondition::FAIR,
            ]);

            $accessories = fake()->randomElements([
                'Power Adapter',
                'Carrying Case',
                'Wireless Mouse',
                'USB-C Hub',
                'HDMI Cable',
            ], fake()->numberBetween(2, 4));

            return [
                'transaction_type' => TransactionType::RETURN,
                'condition_before' => $conditionBefore,
                'condition_after' => $conditionBefore, // Same condition
                'accessories' => $accessories,
                'notes' => 'Asset returned in good condition',
            ];
        });
    }

    /**
     * State: Return with damage
     */
    public function returnDamaged(): static
    {
        return $this->state(function (array $attributes) {
            $conditionBefore = fake()->randomElement([
                AssetCondition::EXCELLENT,
                AssetCondition::GOOD,
            ]);

            return [
                'transaction_type' => TransactionType::RETURN,
                'condition_before' => $conditionBefore,
                'condition_after' => fake()->randomElement([
                    AssetCondition::POOR,
                    AssetCondition::DAMAGED,
                ]),
                'damage_report' => fake()->sentence(15),
                'notes' => 'Asset returned with damage - maintenance ticket created',
            ];
        });
    }

    /**
     * State: Extend transaction
     */
    public function extend(): static
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => TransactionType::EXTEND,
            'notes' => 'Loan period extended by '.fake()->numberBetween(7, 14).' days',
        ]);
    }

    /**
     * State: Recall transaction
     */
    public function recall(): static
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => TransactionType::RECALL,
            'notes' => 'Asset recalled due to urgent requirement',
        ]);
    }

    /**
     * State: Processed recently
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_at' => now()->subHours(fake()->numberBetween(1, 24)),
        ]);
    }

    /**
     * State: Processed in the past
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_at' => now()->subDays(fake()->numberBetween(7, 90)),
        ]);
    }

    /**
     * State: With detailed notes
     */
    public function withNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => fake()->paragraph(3),
        ]);
    }
}
