<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AssetCondition;
use App\Models\Asset;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Loan Item Factory
 *
 * Factory for loan item junction records with condition tracking.
 *
 * @see D03-FR-005.1 Model factories for testing
 * @see D03-FR-003.2 Asset issuance tracking
 *
 * @extends Factory<LoanItem>
 */
class LoanItemFactory extends Factory
{
    protected $model = LoanItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitValue = fake()->randomFloat(2, 500, 15000);
        $quantity = 1; // Most loans are single items

        return [
            'loan_application_id' => LoanApplication::factory(),
            'asset_id' => Asset::factory(),
            'quantity' => $quantity,
            'unit_value' => $unitValue,
            'total_value' => $unitValue * $quantity,
            'condition_before' => null, // Set when issued
            'condition_after' => null, // Set when returned
            'accessories_issued' => null,
            'accessories_returned' => null,
            'damage_report' => null,
        ];
    }

    /**
     * State: Issued (with condition before)
     */
    public function issued(): static
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
                'condition_before' => fake()->randomElement([
                    AssetCondition::EXCELLENT,
                    AssetCondition::GOOD,
                    AssetCondition::FAIR,
                ]),
                'accessories_issued' => $accessories,
            ];
        });
    }

    /**
     * State: Returned (with condition after)
     */
    public function returned(): static
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
                'condition_before' => $conditionBefore,
                'condition_after' => $conditionBefore, // Same condition (no damage)
                'accessories_issued' => $accessories,
                'accessories_returned' => $accessories, // All returned
            ];
        });
    }

    /**
     * State: Returned with damage
     */
    public function damaged(): static
    {
        return $this->state(function (array $attributes) {
            $conditionBefore = fake()->randomElement([
                AssetCondition::EXCELLENT,
                AssetCondition::GOOD,
            ]);

            $accessories = fake()->randomElements([
                'Power Adapter',
                'Carrying Case',
                'Wireless Mouse',
                'USB-C Hub',
                'HDMI Cable',
            ], fake()->numberBetween(2, 4));

            return [
                'condition_before' => $conditionBefore,
                'condition_after' => fake()->randomElement([
                    AssetCondition::POOR,
                    AssetCondition::DAMAGED,
                ]),
                'accessories_issued' => $accessories,
                'accessories_returned' => fake()->randomElements($accessories, fake()->numberBetween(1, count($accessories))),
                'damage_report' => fake()->sentence(15),
            ];
        });
    }

    /**
     * State: Missing accessories
     */
    public function missingAccessories(): static
    {
        return $this->state(function (array $attributes) {
            $accessories = [
                'Power Adapter',
                'Carrying Case',
                'Wireless Mouse',
                'USB-C Hub',
                'HDMI Cable',
            ];

            $issued = fake()->randomElements($accessories, 4);
            $returned = fake()->randomElements($issued, 2); // Only return 2 out of 4

            return [
                'condition_before' => AssetCondition::GOOD,
                'condition_after' => AssetCondition::GOOD,
                'accessories_issued' => $issued,
                'accessories_returned' => $returned,
            ];
        });
    }

    /**
     * State: Excellent condition maintained
     */
    public function excellentCondition(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition_before' => AssetCondition::EXCELLENT,
            'condition_after' => AssetCondition::EXCELLENT,
        ]);
    }

    /**
     * State: Good condition maintained
     */
    public function goodCondition(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition_before' => AssetCondition::GOOD,
            'condition_after' => AssetCondition::GOOD,
        ]);
    }
}
