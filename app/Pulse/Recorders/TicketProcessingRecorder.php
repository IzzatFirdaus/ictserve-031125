<?php

declare(strict_types=1);

namespace App\Pulse\Recorders;

use App\Events\HelpdeskTicketCreated;
use App\Events\HelpdeskTicketResolved;
use App\Events\HelpdeskTicketStatusChanged;
use Carbon\CarbonImmutable;
use Illuminate\Config\Repository;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\Ignores;
use Laravel\Pulse\Recorders\Concerns\Sampling;

/**
 * Ticket Processing Recorder for Laravel Pulse.
 *
 * Tracks performance metrics for helpdesk ticket operations including:
 * - Ticket creation time
 * - Resolution time
 * - SLA compliance
 * - Status transition times
 *
 * @trace D03-SRS-HELP-001, Requirements 16.4 (ICTServe-specific metrics)
 * @trace Requirements 4.1, 4.2, 14.1, 14.2, 16.1, 16.2, 16.3, 16.4, 16.5
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.0
 */
class TicketProcessingRecorder
{
    use Ignores;
    use Sampling;

    /**
     * The events to listen for.
     *
     * @var array<int, class-string>
     */
    public array $listen = [
        HelpdeskTicketCreated::class,
        HelpdeskTicketResolved::class,
        HelpdeskTicketStatusChanged::class,
    ];

    public function __construct(
        protected Pulse $pulse,
        protected Repository $config
    ) {}

    /**
     * Record ticket processing metrics.
     */
    public function record(HelpdeskTicketCreated|HelpdeskTicketResolved|HelpdeskTicketStatusChanged $event): void
    {
        if (! $this->shouldSample()) {
            return;
        }

        $timestamp = CarbonImmutable::now()->getTimestamp();

        match (true) {
            $event instanceof HelpdeskTicketCreated => $this->recordTicketCreation($event, $timestamp),
            $event instanceof HelpdeskTicketResolved => $this->recordTicketResolution($event, $timestamp),
            $event instanceof HelpdeskTicketStatusChanged => $this->recordStatusChange($event, $timestamp),
        };
    }

    /**
     * Record ticket creation metrics.
     */
    protected function recordTicketCreation(HelpdeskTicketCreated $event, int $timestamp): void
    {
        $ticket = $event->ticket;

        // Record ticket creation count by priority
        $this->pulse->record(
            type: 'ticket_created',
            key: $ticket->priority ?? 'medium',
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record ticket creation count by category
        if ($ticket->category_id) {
            $this->pulse->record(
                type: 'ticket_by_category',
                key: (string) $ticket->category_id,
                value: 1,
                timestamp: $timestamp
            )->count()->onlyBuckets();
        }

        // Record guest vs authenticated submission
        $submissionType = $ticket->user_id ? 'authenticated' : 'guest';
        $this->pulse->record(
            type: 'ticket_submission_type',
            key: $submissionType,
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record processing time if available
        if (isset($event->processingTimeMs)) {
            $this->pulse->record(
                type: 'ticket_creation_time',
                key: 'processing',
                value: $event->processingTimeMs,
                timestamp: $timestamp
            )->avg()->onlyBuckets();
        }
    }

    /**
     * Record ticket resolution metrics.
     */
    protected function recordTicketResolution(HelpdeskTicketResolved $event, int $timestamp): void
    {
        $ticket = $event->ticket;

        // Record resolution count
        $this->pulse->record(
            type: 'ticket_resolved',
            key: $ticket->priority ?? 'medium',
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Calculate and record resolution time (in minutes)
        if ($ticket->created_at && $ticket->resolved_at) {
            $resolutionMinutes = (int) $ticket->created_at->diffInMinutes($ticket->resolved_at);
            $this->pulse->record(
                type: 'ticket_resolution_time',
                key: $ticket->priority ?? 'medium',
                value: $resolutionMinutes,
                timestamp: $timestamp
            )->avg()->onlyBuckets();
        }

        // Record SLA compliance
        $slaStatus = $this->determineSLAStatus($ticket);
        $this->pulse->record(
            type: 'ticket_sla_status',
            key: $slaStatus,
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();
    }

    /**
     * Record status change metrics.
     */
    protected function recordStatusChange(HelpdeskTicketStatusChanged $event, int $timestamp): void
    {
        // Record status transition
        $transitionKey = "{$event->oldStatus}_to_{$event->newStatus}";
        $this->pulse->record(
            type: 'ticket_status_transition',
            key: $transitionKey,
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record time in previous status (in minutes)
        if (isset($event->timeInPreviousStatusMinutes)) {
            $this->pulse->record(
                type: 'ticket_status_duration',
                key: $event->oldStatus,
                value: $event->timeInPreviousStatusMinutes,
                timestamp: $timestamp
            )->avg()->onlyBuckets();
        }
    }

    /**
     * Determine SLA status for a ticket.
     */
    protected function determineSLAStatus(mixed $ticket): string
    {
        if (! $ticket->sla_due_at) {
            return 'no_sla';
        }

        $resolvedAt = $ticket->resolved_at ?? now();

        if ($resolvedAt->lte($ticket->sla_due_at)) {
            return 'met';
        }

        return 'breached';
    }
}
