<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\GuestSubmissionClaimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Guest Submission Claim Service Unit Tests
 *
 * Tests email matching, ownership verification, and claim process.
 *
 * @traceability Requirement 2.5
 */
class GuestSubmissionClaimServiceTest extends TestCase
{
    use RefreshDatabase;

    private GuestSubmissionClaimService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GuestSubmissionClaimService;
    }

    /**
     * Test finding claimable submissions by email
     *
     *
     * @traceability Requirement 2.5
     */
    #[Test]
    public function test_find_claimable_submissions_by_email(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@motac.gov.my',
        ]);

        // Create guest submissions with matching email
        HelpdeskTicket::factory()->count(2)->create([
            'email' => 'staff@motac.gov.my',
            'user_id' => null,
        ]);

        LoanApplication::factory()->count(1)->create([
            'email' => 'staff@motac.gov.my',
            'user_id' => null,
        ]);

        // Create submissions with different email
        HelpdeskTicket::factory()->create([
            'email' => 'other@motac.gov.my',
            'user_id' => null,
        ]);

        $claimable = $this->service->findClaimableSubmissions($user);

        $this->assertCount(3, $claimable);
    }

    /**
     * Test ownership verification
     *
     *
     * @traceability Requirement 2.5
     */
    #[Test]
    public function test_verify_ownership_with_matching_email(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@motac.gov.my',
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'email' => 'staff@motac.gov.my',
            'user_id' => null,
        ]);

        $result = $this->service->verifyOwnership($user, $ticket);

        $this->assertTrue($result);
    }

    /**
     * Test ownership verification fails with mismatched email
     *
     *
     * @traceability Requirement 2.5
     */
    #[Test]
    public function test_verify_ownership_fails_with_mismatched_email(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@motac.gov.my',
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'email' => 'other@motac.gov.my',
            'user_id' => null,
        ]);

        $result = $this->service->verifyOwnership($user, $ticket);

        $this->assertFalse($result);
    }

    /**
     * Test claim submission process
     *
     *
     * @traceability Requirement 2.5
     */
    #[Test]
    public function test_claim_submission_links_to_user_account(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'staff@motac.gov.my',
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'email' => 'staff@motac.gov.my',
            'user_id' => null,
        ]);

        $result = $this->service->claimSubmission($user, $ticket);

        $this->assertTrue($result);
        $this->assertEquals($user->id, $ticket->fresh()->user_id);

        Mail::assertSent(\App\Mail\SubmissionClaimedMail::class);
    }

    /**
     * Test claim submission creates portal activity
     *
     *
     * @traceability Requirement 2.5
     */
    #[Test]
    public function test_claim_submission_creates_portal_activity(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@motac.gov.my',
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'email' => 'staff@motac.gov.my',
            'user_id' => null,
        ]);

        $this->service->claimSubmission($user, $ticket);

        $this->assertDatabaseHas('portal_activities', [
            'user_id' => $user->id,
            'activity_type' => 'submission_claimed',
            'subject_type' => HelpdeskTicket::class,
            'subject_id' => $ticket->id,
        ]);
    }

    /**
     * Test claim submission throws exception for mismatched email
     *
     *
     * @traceability Requirement 2.5
     */
    #[Test]
    public function test_claim_submission_throws_exception_for_mismatched_email(): void
    {
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);

        $user = User::factory()->create([
            'email' => 'staff@motac.gov.my',
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'email' => 'other@motac.gov.my',
            'user_id' => null,
        ]);

        $this->service->claimSubmission($user, $ticket);
    }

    /**
     * Test finding claimable submissions excludes already claimed
     *
     *
     * @traceability Requirement 2.5
     */
    #[Test]
    public function test_find_claimable_submissions_excludes_already_claimed(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@motac.gov.my',
        ]);

        // Create guest submission
        HelpdeskTicket::factory()->create([
            'email' => 'staff@motac.gov.my',
            'user_id' => null,
        ]);

        // Create already claimed submission
        HelpdeskTicket::factory()->create([
            'email' => 'staff@motac.gov.my',
            'user_id' => $user->id,
        ]);

        $claimable = $this->service->findClaimableSubmissions($user);

        $this->assertCount(1, $claimable);
    }
}
