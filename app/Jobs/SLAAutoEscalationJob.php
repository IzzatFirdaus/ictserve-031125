<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Notifications\SLABreachNotification;
use App\Services\SLABreachDetector;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * SLA Auto Escalation Job
 *
 * Automatically detects and processes SLA breaches, marking tickets
 * as breached and notifying admin/superuser roles.
 *
 * @see D03-FR-008 SLA management requirements
 * @see D04 §5.3 SLA escalation workflow
 * @see Requirements 18.2, 18.3
 */
class SLAAutoEscalationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out
     */
    public int $timeout = 300;

    /**
     * The backoff delays between retry attempts (exponential backoff)
     *
     * @var array<int>
     */
    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance
     */
    public function __construct()
    {
        $this->onQueue('helpdesk');
    }

    /**
     * Execute the job
     */
    public function handle(SLABreachDetector $detector): void
    {
        $startTime = microtime(true);

        Log::info('SLAAutoEscalationJob started', [
            'attempt' => $this->attempts(),
        ]);

        try {
            $breachedTickets = $detector->getNewBreaches();
            $processedCount = 0;

            foreach ($breachedTickets as $ticket) {
                $this->processBreachedTicket($ticket, $detector);
                $processedCount++;
            }

            $processingTime = microtime(true) - $startTime;

            Log::info('SLAAutoEscalationJob completed successfully', [
                'tickets_processed' => $processedCount,
                'processing_time' => $processingTime,
            ]);
        } catch (\Exception $e) {
            $this->handleFailure($e, microtime(true) - $startTime);
            throw $e;
        }
    }

    /**
     * Process a single breached ticket
     */
    private function processBreachedTicket(HelpdeskTicket $ticket, SLABreachDetector $detector): void
    {
        // Determine breach type
        $breachType = $detector->determineBreachType($ticket);

        if ($breachType === 'none') {
            Log::debug('Ticket no longer in breach state', [
                'ticket_number' => $ticket->ticket_number,
            ]);

            return;
        }

        // Mark ticket as breached
        $detector->markAsBreached($ticket, $breachType);

        // Escalate priority if not already urgent
        $this->escalatePriority($ticket);

        // Notify admin/superuser roles
        $this->notifyAdministrators($ticket, $breachType);

        Log::warning('SLA breach processed and escalated', [
            'ticket_number' => $ticket->ticket_number,
            'breach_type' => $breachType,
            'new_priority' => $ticket->priority,
        ]);
    }

    /**
     * Escalate ticket priority to urgent if not already
     */
    private function escalatePriority(HelpdeskTicket $ticket): void
    {
        if ($ticket->priority !== 'urgent') {
            $previousPriority = $ticket->priority;

            $ticket->update([
                'priority' => 'urgent',
            ]);

            Log::info('Ticket priority escalated due to SLA breach', [
                'ticket_number' => $ticket->ticket_number,
                'previous_priority' => $previousPriority,
                'new_priority' => 'urgent',
            ]);
        }
    }

    /**
     * Notify administrators about the SLA breach
     */
    private function notifyAdministrators(HelpdeskTicket $ticket, string $breachType): void
    {
        $administrators = User::query()
            ->whereIn('role', ['admin', 'superuser'])
            ->where('is_active', true)
            ->get();

        if ($administrators->isEmpty()) {
            Log::warning('No active administrators found for SLA breach notification', [
                'ticket_number' => $ticket->ticket_number,
            ]);

            return;
        }

        Notification::send(
            $administrators,
            new SLABreachNotification($ticket, $breachType)
        );

        Log::info('SLA breach notification sent to administrators', [
            'ticket_number' => $ticket->ticket_number,
            'breach_type' => $breachType,
            'admin_count' => $administrators->count(),
        ]);
    }

    /**
     * Handle job failure
     */
    private function handleFailure(\Exception $e, float $processingTime): void
    {
        Log::error('SLAAutoEscalationJob failed', [
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
            'processing_time' => $processingTime,
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * Handle permanent job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('SLAAutoEscalationJob permanently failed', [
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<string>
     */
    public function tags(): array
    {
        return [
            'helpdesk',
            'sla-escalation',
            'priority:high',
        ];
    }
}
