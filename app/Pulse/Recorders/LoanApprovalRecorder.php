<?php

declare(strict_types=1);

namespace App\Pulse\Recorders;

use App\Events\LoanApplicationApproved;
use App\Events\LoanApplicationCreated;
use App\Events\LoanApplicationRejected;
use App\Events\LoanApprovalRequested;
use Carbon\CarbonImmutable;
use Illuminate\Config\Repository;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\Ignores;
use Laravel\Pulse\Recorders\Concerns\Sampling;

/**
 * Loan Approval Recorder for Laravel Pulse.
 *
 * Tracks performance metrics for asset loan approval workflows including:
 * - Application processing time
 * - Approval workflow duration
 * - Email vs portal approval rates
 * - Approval/rejection ratios
 *
 * @trace D03-SRS-LOAN-001, Requirements 16.4 (ICTServe-specific metrics)
 * @trace Requirements 4.1, 4.2, 14.1, 14.2, 16.1, 16.2, 16.3, 16.4, 16.5
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.0
 */
class LoanApprovalRecorder
{
    use Ignores;
    use Sampling;

    /**
     * The events to listen for.
     *
     * @var array<int, class-string>
     */
    public array $listen = [
        LoanApplicationCreated::class,
        LoanApplicationApproved::class,
        LoanApplicationRejected::class,
        LoanApprovalRequested::class,
    ];

    public function __construct(
        protected Pulse $pulse,
        protected Repository $config
    ) {}

    /**
     * Record loan approval metrics.
     */
    public function record(
        LoanApplicationCreated|LoanApplicationApproved|LoanApplicationRejected|LoanApprovalRequested $event
    ): void {
        if (! $this->shouldSample()) {
            return;
        }

        $timestamp = CarbonImmutable::now()->getTimestamp();

        match (true) {
            $event instanceof LoanApplicationCreated => $this->recordApplicationCreation($event, $timestamp),
            $event instanceof LoanApplicationApproved => $this->recordApproval($event, $timestamp),
            $event instanceof LoanApplicationRejected => $this->recordRejection($event, $timestamp),
            $event instanceof LoanApprovalRequested => $this->recordApprovalRequest($event, $timestamp),
        };
    }

    /**
     * Record loan application creation metrics.
     */
    protected function recordApplicationCreation(LoanApplicationCreated $event, int $timestamp): void
    {
        $application = $event->application;

        // Record application count
        $this->pulse->record(
            type: 'loan_application_created',
            key: 'total',
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record guest vs authenticated submission
        $submissionType = $application->user_id ? 'authenticated' : 'guest';
        $this->pulse->record(
            type: 'loan_submission_type',
            key: $submissionType,
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record by asset category if available
        if ($application->asset && $application->asset->category_id) {
            $this->pulse->record(
                type: 'loan_by_asset_category',
                key: (string) $application->asset->category_id,
                value: 1,
                timestamp: $timestamp
            )->count()->onlyBuckets();
        }

        // Record processing time if available
        if (isset($event->processingTimeMs)) {
            $this->pulse->record(
                type: 'loan_creation_time',
                key: 'processing',
                value: $event->processingTimeMs,
                timestamp: $timestamp
            )->avg()->onlyBuckets();
        }
    }

    /**
     * Record loan approval metrics.
     */
    protected function recordApproval(LoanApplicationApproved $event, int $timestamp): void
    {
        $application = $event->application;

        // Record approval count
        $this->pulse->record(
            type: 'loan_approved',
            key: 'total',
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record approval method (email vs portal)
        $approvalMethod = $application->approval_method ?? 'unknown';
        $this->pulse->record(
            type: 'loan_approval_method',
            key: $approvalMethod,
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Calculate and record approval workflow duration (in hours)
        if ($application->created_at && $application->approved_at) {
            $durationHours = (int) $application->created_at->diffInHours($application->approved_at);
            $this->pulse->record(
                type: 'loan_approval_duration',
                key: 'hours',
                value: $durationHours,
                timestamp: $timestamp
            )->avg()->onlyBuckets();
        }
    }

    /**
     * Record loan rejection metrics.
     */
    protected function recordRejection(LoanApplicationRejected $event, int $timestamp): void
    {
        $application = $event->application;

        // Record rejection count
        $this->pulse->record(
            type: 'loan_rejected',
            key: 'total',
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record rejection method (email vs portal)
        $approvalMethod = $application->approval_method ?? 'unknown';
        $this->pulse->record(
            type: 'loan_rejection_method',
            key: $approvalMethod,
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Calculate and record time to rejection (in hours)
        if ($application->created_at && $application->rejected_at) {
            $durationHours = (int) $application->created_at->diffInHours($application->rejected_at);
            $this->pulse->record(
                type: 'loan_rejection_duration',
                key: 'hours',
                value: $durationHours,
                timestamp: $timestamp
            )->avg()->onlyBuckets();
        }
    }

    /**
     * Record approval request metrics.
     */
    protected function recordApprovalRequest(LoanApprovalRequested $event, int $timestamp): void
    {
        // Record approval request sent
        $this->pulse->record(
            type: 'loan_approval_request_sent',
            key: 'total',
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record email delivery time if available
        if (isset($event->emailDeliveryTimeMs)) {
            $this->pulse->record(
                type: 'loan_email_delivery_time',
                key: 'approval_request',
                value: $event->emailDeliveryTimeMs,
                timestamp: $timestamp
            )->avg()->onlyBuckets();
        }
    }
}
