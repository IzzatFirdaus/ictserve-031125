<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Carbon\Carbon;

/**
 * Helpdesk Service Interface
 *
 * Defines the contract for helpdesk ticket management operations.
 * Supports hybrid architecture (guest + authenticated submissions).
 *
 * @see D03 SRS-HELP-001, SRS-HELP-002, SRS-HELP-003
 * @see Requirements 1.5, 2.1, 5.3, 5.4
 */
interface HelpdeskServiceInterface
{
    /**
     * Create a new helpdesk ticket with hybrid user_id logic
     *
     * @param  array<string, mixed>  $data  Ticket data
     * @return HelpdeskTicket The created ticket
     */
    

/**
 * @param array<string, mixed> $data
 */
public function createTicket(array $data): HelpdeskTicket;

    /**
     * Update ticket status with required comment
     *
     * @param  HelpdeskTicket  $ticket  The ticket to update
     * @param  string  $status  New status value
     * @param  string  $comment  Required comment explaining the status change
     */
    public function updateStatus(HelpdeskTicket $ticket, string $status, string $comment): void;

    /**
     * Assign ticket to an admin user
     *
     * @param  HelpdeskTicket  $ticket  The ticket to assign
     * @param  User  $admin  The admin user to assign to
     */
    public function assignTicket(HelpdeskTicket $ticket, User $admin): void;

    /**
     * Retrieve ticket by status token
     *
     * @param  string  $token  The plain status token
     * @return HelpdeskTicket|null The ticket if found, null otherwise
     */
    public function getByStatusToken(string $token): ?HelpdeskTicket;

    /**
     * Calculate SLA due date based on category
     *
     * @param  string  $category  The ticket category
     * @return Carbon The calculated SLA due date
     */
    public function calculateSLADueDate(string $category): Carbon;

    /**
     * Check if ticket has breached SLA
     *
     * @param  HelpdeskTicket  $ticket  The ticket to check
     * @return bool True if SLA is breached
     */
    public function checkSLABreach(HelpdeskTicket $ticket): bool;
}
