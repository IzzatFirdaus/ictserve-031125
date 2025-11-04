<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for HelpdeskTicket hybrid support methods
 *
 * Tests Requirements 1.3, 3.1 - Hybrid architecture support
 *
 * @see D03 Software Requirements Specification - Requirement 1.3
 * @see D04 Software Design Document - Hybrid Architecture
 */
class HelpdeskTicketHybridTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test isGuestSubmission() returns true for guest tickets
     */
    public function test_is_guest_submission_returns_true_for_guest_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_name' => 'John Doe',
            'guest_email' => 'john@example.com',
        ]);

        $this->assertTrue($ticket->isGuestSubmission());
    }

    /**
     * Test isGuestSubmission() returns false for authenticated tickets
     */
    public function test_is_guest_submission_returns_false_for_authenticated_tickets(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'guest_name' => null,
            'guest_email' => null,
        ]);

        $this->assertFalse($ticket->isGuestSubmission());
    }

    /**
     * Test isAuthenticatedSubmission() returns true for authenticated tickets
     */
    public function test_is_authenticated_submission_returns_true_for_authenticated_tickets(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($ticket->isAuthenticatedSubmission());
    }

    /**
     * Test isAuthenticatedSubmission() returns false for guest tickets
     */
    public function test_is_authenticated_submission_returns_false_for_guest_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_name' => 'Jane Doe',
            'guest_email' => 'jane@example.com',
        ]);

        $this->assertFalse($ticket->isAuthenticatedSubmission());
    }

    /**
     * Test getSubmitterName() returns guest name for guest tickets
     */
    public function test_get_submitter_name_returns_guest_name_for_guest_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_name' => 'Alice Smith',
            'guest_email' => 'alice@example.com',
        ]);

        $this->assertEquals('Alice Smith', $ticket->getSubmitterName());
    }

    /**
     * Test getSubmitterName() returns user name for authenticated tickets
     */
    public function test_get_submitter_name_returns_user_name_for_authenticated_tickets(): void
    {
        $user = User::factory()->create(['name' => 'Bob Johnson']);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals('Bob Johnson', $ticket->getSubmitterName());
    }

    /**
     * Test getSubmitterEmail() returns guest email for guest tickets
     */
    public function test_get_submitter_email_returns_guest_email_for_guest_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_name' => 'Charlie Brown',
            'guest_email' => 'charlie@example.com',
        ]);

        $this->assertEquals('charlie@example.com', $ticket->getSubmitterEmail());
    }

    /**
     * Test getSubmitterEmail() returns user email for authenticated tickets
     */
    public function test_get_submitter_email_returns_user_email_for_authenticated_tickets(): void
    {
        $user = User::factory()->create(['email' => 'david@example.com']);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals('david@example.com', $ticket->getSubmitterEmail());
    }

    /**
     * Test canBeClaimedBy() returns true when email matches
     */
    public function test_can_be_claimed_by_returns_true_when_email_matches(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'test@example.com',
        ]);

        $this->assertTrue($ticket->canBeClaimedBy($user));
    }

    /**
     * Test canBeClaimedBy() returns false when email does not match
     */
    public function test_can_be_claimed_by_returns_false_when_email_does_not_match(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'different@example.com',
        ]);

        $this->assertFalse($ticket->canBeClaimedBy($user));
    }

    /**
     * Test canBeClaimedBy() returns false for authenticated tickets
     */
    public function test_can_be_claimed_by_returns_false_for_authenticated_tickets(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertFalse($ticket->canBeClaimedBy($user));
    }

    /**
     * Test getSubmitterIdentifier() returns correct format for guest
     */
    public function test_get_submitter_identifier_returns_correct_format_for_guest(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'guest@example.com',
        ]);

        $this->assertEquals('guest:guest@example.com', $ticket->getSubmitterIdentifier());
    }

    /**
     * Test getSubmitterIdentifier() returns correct format for authenticated user
     */
    public function test_get_submitter_identifier_returns_correct_format_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals("user:{$user->id}", $ticket->getSubmitterIdentifier());
    }

    /**
     * Test getSubmitterGrade() returns guest grade for guest tickets
     */
    public function test_get_submitter_grade_returns_guest_grade_for_guest_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_grade' => '41',
        ]);

        $this->assertEquals('41', $ticket->getSubmitterGrade());
    }

    /**
     * Test getSubmitterDivision() returns guest division for guest tickets
     */
    public function test_get_submitter_division_returns_guest_division_for_guest_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_division' => 'IT Department',
        ]);

        $this->assertEquals('IT Department', $ticket->getSubmitterDivision());
    }
}
