<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
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
     * Test nullable user_id FK behavior for guest submissions (v3.6.0 Requirement 2.4)
     */
    #[Test]
    public function nullable_user_id_foreign_key_behavior_for_guest_submissions(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null, // Critical: NULL FK for guest submissions
            'guest_name' => 'Ahmad Bin Salleh',
            'guest_email' => 'ahmad.salleh@motac.gov.my',
        ]);

        // Verify nullable user_id FK behavior (Requirement 2.4)
        $this->assertNull($ticket->user_id, 'Guest submissions must have user_id=NULL for v3.6.0 hybrid architecture');
        $this->assertTrue($ticket->isGuestSubmission());

        // Verify database constraint allows NULL user_id
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'user_id' => null,
            'guest_name' => 'Ahmad Bin Salleh',
            'guest_email' => 'ahmad.salleh@motac.gov.my',
        ]);
    }

    /**
     * Test user_id FK linking for authenticated submissions (v3.6.0 Requirement 2.4)
     */
    #[Test]
    public function user_id_foreign_key_linking_for_authenticated_submissions(): void
    {
        $user = User::factory()->create([
            'name' => 'Siti Fatimah',
            'email' => 'siti.fatimah@motac.gov.my',
        ]);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id, // Critical: Linked FK for authenticated submissions
            'guest_name' => null,
            'guest_email' => null,
        ]);

        // Verify user_id FK linking behavior (Requirement 2.4)
        $this->assertNotNull($ticket->user_id, 'Authenticated submissions must have user_id linked for v3.6.0 hybrid architecture');
        $this->assertEquals($user->id, $ticket->user_id);
        $this->assertFalse($ticket->isGuestSubmission());

        // Verify database constraint enforces FK relationship
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'guest_name' => null,
            'guest_email' => null,
        ]);
    }

    /**
     * Test isAuthenticatedSubmission() returns true for authenticated tickets
     */
    #[Test]
    public function is_authenticated_submission_returns_true_for_authenticated_tickets(): void
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
    #[Test]
    public function is_authenticated_submission_returns_false_for_guest_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_name' => 'Jane Doe',
            'guest_email' => 'jane@example.com',
        ]);

        $this->assertFalse($ticket->isAuthenticatedSubmission());
    }

    /**
     * Test submitter_* field capture for guest submissions (v3.6.0 Requirement 2.5)
     */
    #[Test]
    public function submitter_field_capture_for_guest_submissions(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null, // NULL for guest submission
            'guest_name' => 'Aminah Binti Rashid',
            'guest_email' => 'aminah.rashid@motac.gov.my',
            'guest_phone' => '03-88888888',
            'guest_staff_id' => 'MOTAC123',
            'guest_grade' => 'N41',
            'guest_division' => 'Bahagian Kewangan',
        ]);

        // Verify submitter_* fields are captured for guests (Requirement 2.5)
        $this->assertEquals('Aminah Binti Rashid', $ticket->getSubmitterName());
        $this->assertEquals('aminah.rashid@motac.gov.my', $ticket->guest_email);
        $this->assertEquals('03-88888888', $ticket->guest_phone);
        $this->assertEquals('MOTAC123', $ticket->guest_staff_id);
        $this->assertEquals('N41', $ticket->guest_grade);
        $this->assertEquals('Bahagian Kewangan', $ticket->guest_division);

        // Verify all submitter fields are stored in database
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'user_id' => null,
            'guest_name' => 'Aminah Binti Rashid',
            'guest_email' => 'aminah.rashid@motac.gov.my',
            'guest_phone' => '03-88888888',
            'guest_staff_id' => 'MOTAC123',
            'guest_grade' => 'N41',
            'guest_division' => 'Bahagian Kewangan',
        ]);
    }

    /**
     * Test authenticated submissions use User model data (no submitter_* fields)
     */
    #[Test]
    public function authenticated_submissions_use_user_model_data_not_submitter_fields(): void
    {
        $user = User::factory()->create([
            'name' => 'Datuk Hakim Bin Omar',
            'email' => 'datuk.hakim@motac.gov.my',
        ]);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id, // Linked to User model
            'guest_name' => null,   // No submitter fields for authenticated
            'guest_email' => null,
            'guest_phone' => null,
        ]);

        // Verify authenticated submissions use User model data
        $this->assertEquals('Datuk Hakim Bin Omar', $ticket->getSubmitterName());
        $this->assertEquals('datuk.hakim@motac.gov.my', $ticket->getSubmitterEmail());

        // Verify submitter_* fields are NULL for authenticated submissions
        $this->assertNull($ticket->guest_name);
        $this->assertNull($ticket->guest_email);
        $this->assertNull($ticket->guest_phone);
    }

    /**
     * Test getSubmitterEmail() returns guest email for guest tickets
     */
    #[Test]
    public function get_submitter_email_returns_guest_email_for_guest_tickets(): void
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
    #[Test]
    public function get_submitter_email_returns_user_email_for_authenticated_tickets(): void
    {
        $user = User::factory()->create(['email' => 'david@example.com']);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals('david@example.com', $ticket->getSubmitterEmail());
    }

    /**
     * Test hybrid claiming: guest ticket (user_id=NULL) can be claimed by matching email
     */
    #[Test]
    public function hybrid_claiming_guest_ticket_can_be_claimed_by_matching_email(): void
    {
        $user = User::factory()->create(['email' => 'zainab.hassan@motac.gov.my']);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null, // Guest submission (NULL user_id)
            'guest_email' => 'zainab.hassan@motac.gov.my',
            'guest_name' => 'Zainab Hassan',
        ]);

        // Verify hybrid claiming behavior
        $this->assertTrue($ticket->canBeClaimedBy($user));
        $this->assertTrue($ticket->isGuestSubmission(), 'Ticket should be guest submission before claiming');
        $this->assertNull($ticket->user_id, 'Guest ticket should have user_id=NULL before claiming');
    }

    /**
     * Test hybrid claiming: guest ticket cannot be claimed by non-matching email
     */
    #[Test]
    public function hybrid_claiming_guest_ticket_cannot_be_claimed_by_non_matching_email(): void
    {
        $user = User::factory()->create(['email' => 'user@motac.gov.my']);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null, // Guest submission
            'guest_email' => 'different.user@motac.gov.my',
            'guest_name' => 'Different User',
        ]);

        // Verify hybrid claiming security: only matching email can claim
        $this->assertFalse($ticket->canBeClaimedBy($user));
        $this->assertTrue($ticket->isGuestSubmission());
        $this->assertNull($ticket->user_id);
    }

    /**
     * Test hybrid claiming: authenticated tickets (user_id linked) cannot be claimed
     */
    #[Test]
    public function hybrid_claiming_authenticated_tickets_cannot_be_claimed(): void
    {
        $user = User::factory()->create(['email' => 'owner@motac.gov.my']);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id, // Already authenticated (user_id linked)
        ]);

        // Verify authenticated tickets cannot be claimed (already linked)
        $this->assertFalse($ticket->canBeClaimedBy($user));
        $this->assertTrue($ticket->isAuthenticatedSubmission());
        $this->assertNotNull($ticket->user_id, 'Authenticated ticket should have user_id linked');
    }

    /**
     * Test getSubmitterIdentifier() returns correct format for guest
     */
    #[Test]
    public function get_submitter_identifier_returns_correct_format_for_guest(): void
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
    #[Test]
    public function get_submitter_identifier_returns_correct_format_for_authenticated_user(): void
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
    #[Test]
    public function get_submitter_grade_returns_guest_grade_for_guest_tickets(): void
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
    #[Test]
    public function get_submitter_division_returns_guest_division_for_guest_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_division' => 'IT Department',
        ]);

        $this->assertEquals('IT Department', $ticket->getSubmitterDivision());
    }
}
