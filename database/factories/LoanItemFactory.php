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
        $equipmentTypes = ['Laptop', 'Projector', 'Camera', 'Tablet', 'Printer'];
        $unitValue = \random_int(50000, 1500000) / 100;
        $quantity = 1; // Most loans are single items

        return [
            'loan_application_id' => LoanApplication::factory(),
            'equipment_type' => $equipmentTypes[\array_rand($equipmentTypes)],
            'asset_id' => Asset::factory(),
            'quantity' => $quantity,
            'notes' => \random_int(0, 1) ? 'Standard equipment loan' : null,
            'brand_model' => null,
            'serial_number' => null,
            'unit_value' => $unitValue,
            'total_value' => $unitValue * $quantity,
            'condition_before' => null, // Set when issued
            'condition_after' => null, // Set when returned
            'accessories_issued' => null,
            'other_accessories' => null,
            'accessories_returned' => null,
            'damage_report' => null,
        ];
    }

    /**
     * State: Issued (with condition before)
     */
    public function issued(): static
    {
        $accessoriesPool = [
            'Power Adapter',
            'Carrying Case',
            'Wireless Mouse',
            'USB-C Hub',
            'HDMI Cable',
        ];
        $conditions = [AssetCondition::EXCELLENT, AssetCondition::GOOD, AssetCondition::FAIR];
        
        return $this->state(function (array $attributes) use ($accessoriesPool, $conditions) {
            $keys = \array_rand($accessoriesPool, \random_int(2, 4));
            if (!\is_array($keys)) {
                $keys = [$keys];
            }
            $accessories = [];
            foreach ($keys as $k) {
                $accessories[] = $accessoriesPool[$k];
            }

            return [
                'condition_before' => $conditions[\array_rand($conditions)],
                'accessories_issued' => $accessories,
            ];
        });
    }

    /**
     * State: Returned (with condition after)
     */
    public function returned(): static
    {
        $accessoriesPool = [
            'Power Adapter',
            'Carrying Case',
            'Wireless Mouse',
            'USB-C Hub',
            'HDMI Cable',
        ];
        $conditions = [AssetCondition::EXCELLENT, AssetCondition::GOOD, AssetCondition::FAIR];
        
        return $this->state(function (array $attributes) use ($accessoriesPool, $conditions) {
            $keys = \array_rand($accessoriesPool, \random_int(2, 4));
            if (!\is_array($keys)) {
                $keys = [$keys];
            }
            $accessories = [];
            foreach ($keys as $k) {
                $accessories[] = $accessoriesPool[$k];
            }

            $conditionBefore = $conditions[\array_rand($conditions)];

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
        $accessoriesPool = [
            'Power Adapter',
            'Carrying Case',
            'Wireless Mouse',
            'USB-C Hub',
            'HDMI Cable',
        ];
        $conditionsBefore = [AssetCondition::EXCELLENT, AssetCondition::GOOD];
        $conditionsAfter = [AssetCondition::POOR, AssetCondition::DAMAGED];
        
        return $this->state(function (array $attributes) use ($accessoriesPool, $conditionsBefore, $conditionsAfter) {
            $count = \random_int(2, 4);
            $keys = \array_rand($accessoriesPool, $count);
            if (!\is_array($keys)) {
                $keys = [$keys];
            }
            $accessories = [];
            foreach ($keys as $k) {
                $accessories[] = $accessoriesPool[$k];
            }

            $returnedCount = \random_int(1, \count($accessories));
            $returnedKeys = \array_rand($accessories, $returnedCount);
            if (!\is_array($returnedKeys)) {
                $returnedKeys = [$returnedKeys];
            }
            $returned = [];
            foreach ($returnedKeys as $k) {
                $returned[] = $accessories[$k];
            }

            return [
                'condition_before' => $conditionsBefore[\array_rand($conditionsBefore)],
                'condition_after' => $conditionsAfter[\array_rand($conditionsAfter)],
                'accessories_issued' => $accessories,
                'accessories_returned' => $returned,
                'damage_report' => 'Equipment returned in damaged condition',
            ];
        });
    }

    /**
     * State: Missing accessories
     */
    public function missingAccessories(): static
    {
        $accessories = [
            'Power Adapter',
            'Carrying Case',
            'Wireless Mouse',
            'USB-C Hub',
            'HDMI Cable',
        ];
        
        return $this->state(function (array $attributes) use ($accessories) {
            $issuedKeys = \array_rand($accessories, 4);
            if (!\is_array($issuedKeys)) {
                $issuedKeys = [$issuedKeys];
            }
            $issued = [];
            foreach ($issuedKeys as $k) {
                $issued[] = $accessories[$k];
            }

            $returnedKeys = \array_rand($issued, 2);
            if (!\is_array($returnedKeys)) {
                $returnedKeys = [$returnedKeys];
            }
            $returned = [];
            foreach ($returnedKeys as $k) {
                $returned[] = $issued[$k];
            }

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

