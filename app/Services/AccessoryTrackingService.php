<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AccessoryTrackingServiceInterface;
use App\Enums\TransactionType;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionAccessory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Accessory Tracking Service for ICTServe v3.5.0
 *
 * Manages the tracking of asset accessories during loan check-out and check-in
 * operations as per PK.(S).MOTAC.07.(L3) requirements.
 *
 * @see D03 SRS-LOAN-007 Accessory Tracking Requirements
 * @see PK.(S).MOTAC.07.(L3) - Asset Loan Form Reference
 * @see Requirements 26.1, 26.2, 26.4, 26.5, 26.6
 */
class AccessoryTrackingService implements AccessoryTrackingServiceInterface
{
    /**
     * Standard accessory types as defined in PK.(S).MOTAC.07.(L3)
     */
    private const STANDARD_ACCESSORIES = [
        'POWER_ADAPTER' => [
            'key' => 'POWER_ADAPTER',
            'label_en' => 'Power Adapter',
            'label_ms' => 'Penyesuai Kuasa',
        ],
        'BAG' => [
            'key' => 'BAG',
            'label_en' => 'Bag',
            'label_ms' => 'Beg',
        ],
        'MOUSE' => [
            'key' => 'MOUSE',
            'label_en' => 'Mouse',
            'label_ms' => 'Tetikus',
        ],
        'USB_CABLE' => [
            'key' => 'USB_CABLE',
            'label_en' => 'USB Cable',
            'label_ms' => 'Kabel USB',
        ],
        'HDMI_VGA_CABLE' => [
            'key' => 'HDMI_VGA_CABLE',
            'label_en' => 'HDMI/VGA Cable',
            'label_ms' => 'Kabel HDMI/VGA',
        ],
        'REMOTE' => [
            'key' => 'REMOTE',
            'label_en' => 'Remote',
            'label_ms' => 'Alat Kawalan Jauh',
        ],
        'OTHERS' => [
            'key' => 'OTHERS',
            'label_en' => 'Others',
            'label_ms' => 'Lain-lain',
        ],
    ];

    /**
     * Get the list of standard accessory types
     *
     * @return array<string, array{key: string, label_en: string, label_ms: string}>
     *
     * @see Requirements 26.1 - Display accessory checklist at check-out
     */
    public function getStandardAccessories(): array
    {
        return self::STANDARD_ACCESSORIES;
    }

    /**
     * Record accessories present at check-out
     *
     * @param  LoanTransaction  $transaction  The check-out transaction
     * @param  array<int, array{accessory_type: string, accessory_name?: string|null, present: bool, condition_notes?: string|null}>  $accessories
     *
     * @see Requirements 26.2 - Allow marking accessories as included/not included
     * @see Requirements 26.6 - Store accessory data in loan_transaction_accessories
     */
    

/**
 * @param array<string, mixed> $accessories
 */
public function recordCheckoutAccessories(LoanTransaction $transaction, array $accessories): void
    {
        DB::transaction(function () use ($transaction, $accessories): void {
            foreach ($accessories as $accessoryData) {
                LoanTransactionAccessory::create([
                    'loan_transaction_id' => $transaction->id,
                    'accessory_type' => $accessoryData['accessory_type'],
                    'accessory_name' => $accessoryData['accessory_name'] ?? null,
                    'present_at_checkout' => $accessoryData['present'],
                    'present_at_checkin' => null, // Will be set during check-in
                    'condition_notes' => $accessoryData['condition_notes'] ?? null,
                ]);
            }

            Log::info("Checkout accessories recorded for transaction {$transaction->id}", [
                'transaction_id' => $transaction->id,
                'loan_application_id' => $transaction->loan_application_id,
                'accessory_count' => count($accessories),
            ]);
        });
    }

    /**
     * Record accessories present at check-in
     *
     * @param  LoanTransaction  $transaction  The check-in transaction
     * @param  array<int, array{accessory_type: string, accessory_name?: string|null, present: bool, condition_notes?: string|null}>  $accessories
     *
     * @see Requirements 26.4 - Pre-populate checklist from check-out data
     * @see Requirements 26.6 - Store accessory data in loan_transaction_accessories
     */
    

/**
 * @param array<string, mixed> $accessories
 */
public function recordCheckinAccessories(LoanTransaction $transaction, array $accessories): void
    {
        DB::transaction(function () use ($transaction, $accessories): void {
            // Get checkout transaction for this loan to find existing accessories
            $checkoutTransaction = LoanTransaction::where('loan_application_id', $transaction->loan_application_id)
                ->where('transaction_type', TransactionType::ISSUE)
                ->latest('performed_at')
                ->first();

            foreach ($accessories as $accessoryData) {
                // Try to find matching checkout accessory to update
                $existingAccessory = null;
                if ($checkoutTransaction) {
                    $existingAccessory = LoanTransactionAccessory::where('loan_transaction_id', $checkoutTransaction->id)
                        ->where('accessory_type', $accessoryData['accessory_type'])
                        ->when(
                            $accessoryData['accessory_type'] === 'OTHERS',
                            fn ($q) => $q->where('accessory_name', $accessoryData['accessory_name'] ?? null)
                        )
                        ->first();
                }

                if ($existingAccessory) {
                    // Update existing accessory with check-in data
                    $existingAccessory->update([
                        'present_at_checkin' => $accessoryData['present'],
                        'condition_notes' => $accessoryData['condition_notes'] ?? $existingAccessory->condition_notes,
                    ]);
                } else {
                    // Create new accessory record for check-in transaction
                    LoanTransactionAccessory::create([
                        'loan_transaction_id' => $transaction->id,
                        'accessory_type' => $accessoryData['accessory_type'],
                        'accessory_name' => $accessoryData['accessory_name'] ?? null,
                        'present_at_checkout' => false, // Not present at checkout
                        'present_at_checkin' => $accessoryData['present'],
                        'condition_notes' => $accessoryData['condition_notes'] ?? null,
                    ]);
                }
            }

            Log::info("Checkin accessories recorded for transaction {$transaction->id}", [
                'transaction_id' => $transaction->id,
                'loan_application_id' => $transaction->loan_application_id,
                'accessory_count' => count($accessories),
            ]);
        });
    }

    /**
     * Get discrepancies between check-out and check-in accessories
     *
     * @param  LoanTransaction  $checkoutTx  The check-out transaction
     * @param  LoanTransaction  $checkinTx  The check-in transaction
     * @return array<int, array{accessory_type: string, accessory_name: string|null, checkout_present: bool, checkin_present: bool|null, checkout_condition: string|null, checkin_condition: string|null, discrepancy_type: string}>
     *
     * @see Requirements 26.5 - Highlight discrepancies (missing items, condition changes)
     */
    public function getAccessoryDiscrepancies(LoanTransaction $checkoutTx, LoanTransaction $checkinTx): array
    {
        $discrepancies = [];

        $checkoutAccessories = $this->getAccessoriesForTransaction($checkoutTx);
        $checkinAccessories = $this->getAccessoriesForTransaction($checkinTx);

        // Index check-in accessories by type and name for quick lookup
        $checkinIndex = [];
        foreach ($checkinAccessories as $accessory) {
            $key = $this->getAccessoryKey($accessory);
            $checkinIndex[$key] = $accessory;
        }

        // Check each checkout accessory for discrepancies
        foreach ($checkoutAccessories as $checkoutAccessory) {
            $key = $this->getAccessoryKey($checkoutAccessory);
            $checkinAccessory = $checkinIndex[$key] ?? null;

            // Determine discrepancy type
            $discrepancyType = $this->determineDiscrepancyType($checkoutAccessory, $checkinAccessory);

            if ($discrepancyType !== 'none') {
                $discrepancies[] = [
                    'accessory_type' => $checkoutAccessory->accessory_type,
                    'accessory_name' => $checkoutAccessory->accessory_name,
                    'checkout_present' => (bool) $checkoutAccessory->present_at_checkout,
                    'checkin_present' => $checkinAccessory?->present_at_checkin,
                    'checkout_condition' => $checkoutAccessory->condition_notes,
                    'checkin_condition' => $checkinAccessory?->condition_notes,
                    'discrepancy_type' => $discrepancyType,
                ];
            }
        }

        Log::info('Accessory discrepancies calculated', [
            'checkout_transaction_id' => $checkoutTx->id,
            'checkin_transaction_id' => $checkinTx->id,
            'discrepancy_count' => count($discrepancies),
        ]);

        return $discrepancies;
    }

    /**
     * Get accessories for a specific transaction
     *
     * @param  LoanTransaction  $transaction  The transaction to query
     * @return Collection<int, LoanTransactionAccessory>
     *
     * @see Requirements 26.4 - Pre-populate checklist from check-out data
     */
    public function getAccessoriesForTransaction(LoanTransaction $transaction): Collection
    {
        return $transaction->transactionAccessories()->get();
    }

    /**
     * Get checkout accessories for a loan application
     *
     * @param  int  $loanApplicationId  The loan application ID
     * @return Collection<int, LoanTransactionAccessory>
     */
    public function getCheckoutAccessoriesForLoan(int $loanApplicationId): Collection
    {
        $checkoutTransaction = LoanTransaction::where('loan_application_id', $loanApplicationId)
            ->where('transaction_type', TransactionType::ISSUE)
            ->latest('performed_at')
            ->first();

        if (! $checkoutTransaction) {
            return collect();
        }

        return $this->getAccessoriesForTransaction($checkoutTransaction);
    }

    /**
     * Validate accessory data
     *
     * @param  array<int, array{accessory_type: string, accessory_name?: string|null, present: bool, condition_notes?: string|null}>  $accessories
     * @return bool True if data is valid
     */
    

/**
 * @param array<string, mixed> $accessories
 */
public function validateAccessoryData(array $accessories): bool
    {
        $validTypes = array_keys(self::STANDARD_ACCESSORIES);

        foreach ($accessories as $accessory) {
            // Check required fields
            if (! isset($accessory['accessory_type']) || ! isset($accessory['present'])) {
                return false;
            }

            // Validate accessory type
            if (! in_array($accessory['accessory_type'], $validTypes, true)) {
                return false;
            }

            // For OTHERS type, accessory_name should be provided
            if ($accessory['accessory_type'] === 'OTHERS') {
                if (empty($accessory['accessory_name'])) {
                    return false;
                }
            }

            // Validate present is boolean
            if (! is_bool($accessory['present'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if any accessories have discrepancies
     *
     * @param  LoanTransaction  $checkoutTx  The check-out transaction
     * @param  LoanTransaction  $checkinTx  The check-in transaction
     * @return bool True if discrepancies exist
     */
    public function hasDiscrepancies(LoanTransaction $checkoutTx, LoanTransaction $checkinTx): bool
    {
        $discrepancies = $this->getAccessoryDiscrepancies($checkoutTx, $checkinTx);

        return count($discrepancies) > 0;
    }

    /**
     * Get accessory summary for display
     *
     * @param  LoanTransaction  $transaction  The transaction to summarize
     * @return array{total: int, present: int, missing: int, with_notes: int}
     */
    public function getAccessorySummary(LoanTransaction $transaction): array
    {
        $accessories = $this->getAccessoriesForTransaction($transaction);

        $total = $accessories->count();
        $present = 0;
        $missing = 0;
        $withNotes = 0;

        foreach ($accessories as $accessory) {
            // Determine presence based on transaction type
            $isPresent = $transaction->transaction_type === TransactionType::ISSUE
                ? $accessory->present_at_checkout
                : $accessory->present_at_checkin;

            if ($isPresent) {
                $present++;
            } else {
                $missing++;
            }

            if (! empty($accessory->condition_notes)) {
                $withNotes++;
            }
        }

        return [
            'total' => $total,
            'present' => $present,
            'missing' => $missing,
            'with_notes' => $withNotes,
        ];
    }

    /**
     * Generate a unique key for an accessory
     */
    private function getAccessoryKey(LoanTransactionAccessory $accessory): string
    {
        if ($accessory->accessory_type === 'OTHERS') {
            return "OTHERS:{$accessory->accessory_name}";
        }

        return $accessory->accessory_type;
    }

    /**
     * Determine the type of discrepancy between checkout and checkin
     *
     * @return string 'missing', 'condition_change', 'not_returned', or 'none'
     */
    private function determineDiscrepancyType(
        LoanTransactionAccessory $checkoutAccessory,
        ?LoanTransactionAccessory $checkinAccessory
    ): string {
        // If accessory was present at checkout but no checkin record exists
        if ($checkoutAccessory->present_at_checkout && $checkinAccessory === null) {
            return 'not_returned';
        }

        // If accessory was present at checkout but not at checkin
        if ($checkoutAccessory->present_at_checkout && $checkinAccessory !== null) {
            if ($checkinAccessory->present_at_checkin === false) {
                return 'missing';
            }

            // Check for condition changes
            if ($checkoutAccessory->condition_notes !== $checkinAccessory->condition_notes
                && ! empty($checkinAccessory->condition_notes)) {
                return 'condition_change';
            }
        }

        return 'none';
    }
}
