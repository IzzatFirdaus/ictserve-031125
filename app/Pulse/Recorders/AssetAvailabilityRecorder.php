<?php

declare(strict_types=1);

namespace App\Pulse\Recorders;

use App\Events\AssetAvailabilityChecked;
use App\Events\AssetCheckedIn;
use App\Events\AssetCheckedOut;
use App\Events\AssetStatusChanged;
use Carbon\CarbonImmutable;
use Illuminate\Config\Repository;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\Ignores;
use Laravel\Pulse\Recorders\Concerns\Sampling;

/**
 * Asset Availability Recorder for Laravel Pulse.
 *
 * Tracks performance metrics for asset availability operations including:
 * - Availability check latency
 * - Check-out/check-in processing times
 * - Asset utilization rates
 * - Overdue asset tracking
 *
 * @trace D03-SRS-ASSET-001, Requirements 16.4 (ICTServe-specific metrics)
 * @trace Requirements 4.1, 4.2, 14.1, 14.2, 16.1, 16.2, 16.3, 16.4, 16.5
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.0
 */
class AssetAvailabilityRecorder
{
    use Ignores;
    use Sampling;

    /**
     * The events to listen for.
     *
     * @var array<int, class-string>
     */
    public array $listen = [
        AssetCheckedOut::class,
        AssetCheckedIn::class,
        AssetAvailabilityChecked::class,
        AssetStatusChanged::class,
    ];

    public function __construct(
        protected Pulse $pulse,
        protected Repository $config
    ) {}

    /**
     * Record asset availability metrics.
     */
    public function record(
        AssetCheckedOut|AssetCheckedIn|AssetAvailabilityChecked|AssetStatusChanged $event
    ): void {
        if (! $this->shouldSample()) {
            return;
        }

        $timestamp = CarbonImmutable::now()->getTimestamp();

        match (true) {
            $event instanceof AssetCheckedOut => $this->recordCheckOut($event, $timestamp),
            $event instanceof AssetCheckedIn => $this->recordCheckIn($event, $timestamp),
            $event instanceof AssetAvailabilityChecked => $this->recordAvailabilityCheck($event, $timestamp),
            $event instanceof AssetStatusChanged => $this->recordStatusChange($event, $timestamp),
        };
    }

    /**
     * Record asset check-out metrics.
     */
    protected function recordCheckOut(AssetCheckedOut $event, int $timestamp): void
    {
        $asset = $event->asset;

        // Record check-out count
        $this->pulse->record(
            type: 'asset_checked_out',
            key: 'total',
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record by asset category
        if ($asset->category_id) {
            $this->pulse->record(
                type: 'asset_checkout_by_category',
                key: (string) $asset->category_id,
                value: 1,
                timestamp: $timestamp
            )->count()->onlyBuckets();
        }

        // Record processing time if available
        if (isset($event->processingTimeMs)) {
            $this->pulse->record(
                type: 'asset_checkout_time',
                key: 'processing',
                value: $event->processingTimeMs,
                timestamp: $timestamp
            )->avg()->onlyBuckets();
        }
    }

    /**
     * Record asset check-in metrics.
     */
    protected function recordCheckIn(AssetCheckedIn $event, int $timestamp): void
    {
        $asset = $event->asset;

        // Record check-in count
        $this->pulse->record(
            type: 'asset_checked_in',
            key: 'total',
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record return condition
        $condition = $event->returnCondition ?? 'good';
        $this->pulse->record(
            type: 'asset_return_condition',
            key: $condition,
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record if overdue
        if ($event->wasOverdue ?? false) {
            $this->pulse->record(
                type: 'asset_overdue_returns',
                key: 'total',
                value: 1,
                timestamp: $timestamp
            )->count()->onlyBuckets();

            // Record days overdue
            if (isset($event->daysOverdue)) {
                $this->pulse->record(
                    type: 'asset_overdue_days',
                    key: 'average',
                    value: $event->daysOverdue,
                    timestamp: $timestamp
                )->avg()->onlyBuckets();
            }
        }

        // Record processing time if available
        if (isset($event->processingTimeMs)) {
            $this->pulse->record(
                type: 'asset_checkin_time',
                key: 'processing',
                value: $event->processingTimeMs,
                timestamp: $timestamp
            )->avg()->onlyBuckets();
        }

        // Record loan duration (in days)
        if (isset($event->loanDurationDays)) {
            $this->pulse->record(
                type: 'asset_loan_duration',
                key: 'days',
                value: $event->loanDurationDays,
                timestamp: $timestamp
            )->avg()->onlyBuckets();
        }
    }

    /**
     * Record availability check metrics.
     */
    protected function recordAvailabilityCheck(AssetAvailabilityChecked $event, int $timestamp): void
    {
        // Record availability check count
        $this->pulse->record(
            type: 'asset_availability_check',
            key: 'total',
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record check latency
        if (isset($event->checkLatencyMs)) {
            $this->pulse->record(
                type: 'asset_availability_latency',
                key: 'ms',
                value: $event->checkLatencyMs,
                timestamp: $timestamp
            )->avg()->onlyBuckets();

            // Track slow availability checks (>500ms threshold)
            if ($event->checkLatencyMs > 500) {
                $this->pulse->record(
                    type: 'asset_slow_availability_check',
                    key: 'total',
                    value: 1,
                    timestamp: $timestamp
                )->count()->onlyBuckets();
            }
        }

        // Record availability result
        $result = $event->isAvailable ? 'available' : 'unavailable';
        $this->pulse->record(
            type: 'asset_availability_result',
            key: $result,
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();
    }

    /**
     * Record asset status change metrics.
     */
    protected function recordStatusChange(AssetStatusChanged $event, int $timestamp): void
    {
        // Record status transition
        $transitionKey = "{$event->oldStatus}_to_{$event->newStatus}";
        $this->pulse->record(
            type: 'asset_status_transition',
            key: $transitionKey,
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Track maintenance-related transitions
        if ($event->newStatus === 'maintenance') {
            $this->pulse->record(
                type: 'asset_maintenance_started',
                key: 'total',
                value: 1,
                timestamp: $timestamp
            )->count()->onlyBuckets();
        }

        if ($event->oldStatus === 'maintenance' && $event->newStatus === 'available') {
            $this->pulse->record(
                type: 'asset_maintenance_completed',
                key: 'total',
                value: 1,
                timestamp: $timestamp
            )->count()->onlyBuckets();
        }
    }
}
