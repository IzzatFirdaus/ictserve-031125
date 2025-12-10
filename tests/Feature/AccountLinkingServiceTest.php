<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\AccountLinkingServiceInterface;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Account Linking Service Tests for ICTServe v3.5.0
 *
 * Tests the optional account linking feature that allows newly registered
 * staff to link their historical guest submissions to their new account.
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D02 FR-050 Optional account linking
 * @see D03 SRS-DATA-001 Hybrid data association
 * @see Requirements 18.2, 18.4
 */
class AccountLinkingServiceTest extends TestCase
{
    use RefreshDatabase;

    private AccountLinkingServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AccountLinkingServiceInterface::class);
    }

    /**
     * Test that the service can be resolved from the container
     */
    #[Test]
    public function serviceCanBeResolved(): void
    {
        $this->assertInstanceOf(AccountLinkingServiceInterface::class, $this->service);
    }

    /**
     * Test finding unlinked helpdesk tickets by email
     *
     * @see Requirements 18.2
     */
    #[Test]
    public function findUnlinkedTicketsByEmail(): void
    {
        $email = 'test.user@motac.gov.my';

        // Create unlinked ticket (guest submission)
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => $email,
            'ticket_number' => 'HD-202501-0001',
            'subject' => 'Test Ticket',
            'status' => 'open',
        ]);

        $submissions = $this->service->findUnlinkedSubmissions($email);

        $this->assertCount(1, $submissions);
        $this->assertEquals('ticket', $submissions->first()['type']);
        $this->assertEquals($ticket->id, $submissions->first()['id']);
    }

    /**
     * Test finding unlinked loan applications by email
     *
     * @see Requirements 18.2
     */
    #[Test]
    public function findUnlinkedLoansByEmail(): void
    {
        $email = 'test.user@motac.gov.my';

        // Create unlinked loan (guest submission)
        $loan = LoanApplication::factory()->create([
            'user_id' => null,
            'applicant_email' => $email,
            'application_number' => 'LA-202501-0001',
            'purpose' => 'Test Loan',
        ]);

        $submissions = $this->service->findUnlinkedSubmissions($email);

        $this->assertCount(1, $submissions);
        $this->assertEquals('loan', $submissions->first()['type']);
        $this->assertEquals($loan->id, $submissions->first()['id']);
    }

    /**
     * Test that already linked submissions are not returned
     *
     * @see Requirements 18.2
     */
    #[Test]
    public function linkedSubmissionsNotReturned(): void
    {
        $user = User::factory()->create([
            'email' => 'test.user@motac.gov.my',
        ]);

        // Create linked ticket (authenticated submission)
        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'guest_email' => $user->email,
            'ticket_number' => 'HD-202501-0001',
        ]);

        $submissions = $this->service->findUnlinkedSubmissions($user->email);

        $this->assertCount(0, $submissions);
    }

    /**
     * Test email matching is case-insensitive
     *
     * @see Requirements 18.2
     */
    #[Test]
    public function emailMatchingIsCaseInsensitive(): void
    {
        $email = 'Test.User@MOTAC.gov.my';

        HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'test.user@motac.gov.my',
            'ticket_number' => 'HD-202501-0001',
        ]);

        $submissions = $this->service->findUnlinkedSubmissions($email);

        $this->assertCount(1, $submissions);
    }

    /**
     * Test linking submissions to user account
     *
     * @see Requirements 18.4
     */
    #[Test]
    public function linkSubmissionsToUser(): void
    {
        $user = User::factory()->create([
            'email' => 'test.user@motac.gov.my',
            'guest_submissions_linked' => 0,
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => $user->email,
            'ticket_number' => 'HD-202501-0001',
        ]);

        $loan = LoanApplication::factory()->create([
            'user_id' => null,
            'applicant_email' => $user->email,
            'application_number' => 'LA-202501-0001',
        ]);

        $linkedCount = $this->service->linkSubmissions($user, [
            ['type' => 'ticket', 'id' => $ticket->id],
            ['type' => 'loan', 'id' => $loan->id],
        ]);

        $this->assertEquals(2, $linkedCount);

        // Verify ticket is linked
        $ticket->refresh();
        $this->assertEquals($user->id, $ticket->user_id);

        // Verify loan is linked
        $loan->refresh();
        $this->assertEquals($user->id, $loan->user_id);

        // Verify user counter is updated
        $user->refresh();
        $this->assertEquals(2, $user->guest_submissions_linked);
    }

    /**
     * Test linking is atomic - all or nothing
     *
     * @see Requirements 18.4
     */
    #[Test]
    public function linkingIsAtomic(): void
    {
        $user = User::factory()->create([
            'email' => 'test.user@motac.gov.my',
            'guest_submissions_linked' => 0,
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => $user->email,
            'ticket_number' => 'HD-202501-0001',
        ]);

        // Try to link valid ticket and invalid loan
        $linkedCount = $this->service->linkSubmissions($user, [
            ['type' => 'ticket', 'id' => $ticket->id],
            ['type' => 'loan', 'id' => 99999], // Non-existent
        ]);

        // Only valid submission should be linked
        $this->assertEquals(1, $linkedCount);

        $ticket->refresh();
        $this->assertEquals($user->id, $ticket->user_id);
    }

    /**
     * Test cannot link submission belonging to different email
     *
     * @see Requirements 18.4
     */
    #[Test]
    public function cannotLinkSubmissionWithDifferentEmail(): void
    {
        $user = User::factory()->create([
            'email' => 'user1@motac.gov.my',
            'guest_submissions_linked' => 0,
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'user2@motac.gov.my', // Different email
            'ticket_number' => 'HD-202501-0001',
        ]);

        $linkedCount = $this->service->linkSubmissions($user, [
            ['type' => 'ticket', 'id' => $ticket->id],
        ]);

        $this->assertEquals(0, $linkedCount);

        $ticket->refresh();
        $this->assertNull($ticket->user_id);
    }

    /**
     * Test get linked submission count
     */
    #[Test]
    public function getLinkedSubmissionCount(): void
    {
        $user = User::factory()->create([
            'guest_submissions_linked' => 5,
        ]);

        $count = $this->service->getLinkedSubmissionCount($user);

        $this->assertEquals(5, $count);
    }

    /**
     * Test has unlinked submissions check
     */
    #[Test]
    public function hasUnlinkedSubmissions(): void
    {
        $user = User::factory()->create([
            'email' => 'test.user@motac.gov.my',
        ]);

        // Initially no unlinked submissions
        $this->assertFalse($this->service->hasUnlinkedSubmissions($user));

        // Create unlinked ticket
        HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => $user->email,
            'ticket_number' => 'HD-202501-0001',
        ]);

        $this->assertTrue($this->service->hasUnlinkedSubmissions($user));
    }

    /**
     * Test get linking statistics
     */
    #[Test]
    public function getLinkingStatistics(): void
    {
        $user = User::factory()->create([
            'email' => 'test.user@motac.gov.my',
        ]);

        // Create linked submissions
        HelpdeskTicket::factory()->count(2)->create([
            'user_id' => $user->id,
            'guest_email' => $user->email,
        ]);

        LoanApplication::factory()->create([
            'user_id' => $user->id,
            'applicant_email' => $user->email,
        ]);

        // Create unlinked submissions
        HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => $user->email,
            'ticket_number' => 'HD-202501-0001',
        ]);

        LoanApplication::factory()->count(2)->create([
            'user_id' => null,
            'applicant_email' => $user->email,
        ]);

        $stats = $this->service->getLinkingStatistics($user);

        $this->assertEquals(2, $stats['linked_tickets']);
        $this->assertEquals(1, $stats['linked_loans']);
        $this->assertEquals(1, $stats['unlinked_tickets']);
        $this->assertEquals(2, $stats['unlinked_loans']);
    }

    /**
     * Test empty email returns empty collection
     */
    #[Test]
    public function emptyEmailReturnsEmptyCollection(): void
    {
        $submissions = $this->service->findUnlinkedSubmissions('');

        $this->assertCount(0, $submissions);
    }

    /**
     * Test empty submission array returns zero
     */
    #[Test]
    public function emptySubmissionArrayReturnsZero(): void
    {
        $user = User::factory()->create();

        $linkedCount = $this->service->linkSubmissions($user, []);

        $this->assertEquals(0, $linkedCount);
    }
}
