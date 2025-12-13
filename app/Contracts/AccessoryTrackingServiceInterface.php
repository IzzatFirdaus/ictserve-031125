<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\LoanTransaction;
use Illuminate\Support\Collection;

/**
 * Accessory Tracking Service Interface for ICTServe v3.5.0
 *
 * Manages the tracking of asset accessories during loan check-out and check-in
 * operations as per PK.(S).MOTAC.07.(L3) requirements.
 *
 * Standard accessories tracked:
 * - Power Adapter (Penyesuai Kuasa)
 * - Bag (Beg)
 * - Mouse (Tetikus)
 * - USB Cable (Kabel USB)
 * - HDMI/VGA Cable (Kabel HDMI/VGA)
 * - Remote (Alat Kawalan Jauh)
 * - Others (Lain-lain) - Custom accessories
 *
 * Key Features:
 * - Get standard accessory types for checklist display
 * - Record accessories present at check-out
 * - Record accessories present at check-in
 * - Detect discrepancies between check-out and check-in
 * - Retrieve accessories for a specific transaction
 *
 * @see D03 SRS-LOAN-007 Accessory Tracking Requirements
 * @see PK.(S).MOTAC.07.(L3) - Asset Loan Form Reference
 * @see Requirements 26.1, 26.2, 26.4, 26.5, 26.6
 */
interface AccessoryTrackingServiceInterface
{
    /**
     * Get the list of standard accessory types
     *
     * Returns the enum values for standard accessories that should be
     * displayed in the check-out/check-in checklist.
     *
     * @return array<string, array{key: string, label_en: string, label_ms: string}>
     *
     * @see Requirements 26.1 - Display accessory checklist at check-out
     */
    public function getStandardAccessories(): array;

    /**
     * Record accessories present at check-out
     *
     * Creates LoanTransactionAccessory records for each accessory
     * included with the asset during check-out.
     *
     * @param  LoanTransaction  $transaction  The check-out transaction
     * @param  array<int, array{accessory_type: string, accessory_name?: string|null, present: bool, condition_notes?: string|null}>  $accessories  Accessory data
     *
     * @see Requirements 26.2 - Allow marking accessories as included/not included
     * @see Requirements 26.6 - Store accessory data in loan_transaction_accessories
     */
    public function recordCheckoutAccessories(LoanTransaction $transaction, array $accessories): void;

    /**
     * Record accessories present at check-in
     *
     * Updates LoanTransactionAccessory records with check-in status
     * for each accessory returned with the asset.
     *
     * @param  LoanTransaction  $transaction  The check-in transaction
     * @param  array<int, array{accessory_type: string, accessory_name?: string|null, present: bool, condition_notes?: string|null}>  $accessories  Accessory data
     *
     * @see Requirements 26.4 - Pre-populate checklist from check-out data
     * @see Requirements 26.6 - Store accessory data in loan_transaction_accessories
     */
    public function recordCheckinAccessories(LoanTransaction $transaction, array $accessories): void;

    /**
     * Get discrepancies between check-out and check-in accessories
     *
     * Compares accessories from check-out transaction with check-in transaction
     * and returns a list of discrepancies (missing items, condition changes).
     *
     * @param  LoanTransaction  $checkoutTx  The check-out transaction
     * @param  LoanTransaction  $checkinTx  The check-in transaction
     * @return array<int, array{accessory_type: string, accessory_name: string|null, checkout_present: bool, checkin_present: bool|null, checkout_condition: string|null, checkin_condition: string|null, discrepancy_type: string}>
     *
     * @see Requirements 26.5 - Highlight discrepancies (missing items, condition changes)
     */
    public function getAccessoryDiscrepancies(LoanTransaction $checkoutTx, LoanTransaction $checkinTx): array;

    /**
     * Get accessories for a specific transaction
     *
     * Retrieves all LoanTransactionAccessory records associated with
     * the given transaction.
     *
     * @param  LoanTransaction  $transaction  The transaction to query
     * @return Collection<int, \App\Models\LoanTransactionAccessory>
     *
     * @see Requirements 26.4 - Pre-populate checklist from check-out data
     */
    public function getAccessoriesForTransaction(LoanTransaction $transaction): Collection;

    /**
     * Get checkout accessories for a loan application
     *
     * Retrieves accessories from the check-out transaction of a loan application.
     * Useful for pre-populating the check-in form.
     *
     * @param  int  $loanApplicationId  The loan application ID
     * @return Collection<int, \App\Models\LoanTransactionAccessory>
     */
    public function getCheckoutAccessoriesForLoan(int $loanApplicationId): Collection;

    /**
     * Validate accessory data
     *
     * Validates that the provided accessory data meets the minimum requirements.
     *
     * @param  array<int, array{accessory_type: string, accessory_name?: string|null, present: bool, condition_notes?: string|null}>  $accessories
     * @return bool True if data is valid
     */
    public function validateAccessoryData(array $accessories): bool;

    /**
     * Check if any accessories have discrepancies
     *
     * Quick check to determine if there are any missing or damaged accessories
     * between check-out and check-in.
     *
     * @param  LoanTransaction  $checkoutTx  The check-out transaction
     * @param  LoanTransaction  $checkinTx  The check-in transaction
     * @return bool True if discrepancies exist
     */
    public function hasDiscrepancies(LoanTransaction $checkoutTx, LoanTransaction $checkinTx): bool;

    /**
     * Get accessory summary for display
     *
     * Returns a formatted summary of accessories for a transaction,
     * suitable for display in admin panels and reports.
     *
     * @param  LoanTransaction  $transaction  The transaction to summarize
     * @return array{total: int, present: int, missing: int, with_notes: int}
     */
    public function getAccessorySummary(LoanTransaction $transaction): array;
}
