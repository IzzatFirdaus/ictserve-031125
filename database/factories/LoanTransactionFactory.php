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
            'notes' => \random_int(0, 100) < 30 ? 'Standard transaction' : null,
        ];
    }

    /**
     * State: Issue transaction
     */
    public function issue(): static
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
                'transaction_type' => TransactionType::ISSUE,
                'condition_before' => $conditions[\array_rand($conditions)],
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
        $conditionsBefore = [AssetCondition::EXCELLENT, AssetCondition::GOOD];
        $conditionsAfter = [AssetCondition::POOR, AssetCondition::DAMAGED];
        
        return $this->state(function (array $attributes) use ($conditionsBefore, $conditionsAfter) {
            return [
                'transaction_type' => TransactionType::RETURN,
                'condition_before' => $conditionsBefore[\array_rand($conditionsBefore)],
                'condition_after' => $conditionsAfter[\array_rand($conditionsAfter)],
                'damage_report' => 'Damage detected upon return',
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
            'notes' => 'Loan period extended by ' . \random_int(7, 14) . ' days',
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
            'processed_at' => now()->subHours(\random_int(1, 24)),
        ]);
    }

    /**
     * State: Processed in the past
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_at' => now()->subDays(\random_int(7, 90)),
        ]);
    }

    /**
     * State: With detailed notes
     */
    public function withNotes(): static
    {
        $notes = [
            'Processed with special handling due to high-value equipment',
            'Equipment required inspection before issuance',
            'Borrower provided additional security deposit',
            'Extended loan due to project requirements',
            'Multiple follow-ups required during loan period',
        ];
        return $this->state(fn (array $attributes) => [
            'notes' => $notes[\array_rand($notes)],
        ]);
    }
}
