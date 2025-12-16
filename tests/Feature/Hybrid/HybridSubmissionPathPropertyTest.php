<?php

declare(strict_types=1);

/**
 * Property-Based Tests for Hybrid Submission Paths
 *
 * Property-based tests to verify the True Hybrid Architecture
 * submission paths work correctly for both guest and authenticated users.
 *
 * @trace D00 §4.1 (True Hybrid Architecture)
 * @trace D03 SRS-DATA-001 (Hybrid Data Association)
 * @trace Requirements 4.1, 4.2, 4.4, 4.5 (Hybrid Submission Paths)
 *
 * @version 3.6.0
 *
 * @created 2025-12-16
 */

namespace Tests\Feature\Hybrid;

use App\Models\Division;
use App\Models\Grade;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HybridSubmissionPathPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 3: Hybrid Submission Path Validation
     *
     * For any submission (helpdesk ticket or loan application), the system
     * should correctly handle both guest (user_id=NULL) and authenticated
     * (user_id linked) submission paths, maintaining data integrity.
     *
     * **Feature: test-suite-comprehensive-v3.6, Property 3: Hybrid Submission Path Validation**
     * **Validates: Requirements 4.1, 4.2, 4.4, 4.5**
     */
    #[Test]
    #[DataProvider('guestSubmissionDataProvider')]
    public function property_guest_submission_creates_record_with_null_user_id(
        string $submissionType,
        array $submitterData
    ): void {
        $division = Division::first() ?? Division::factory()->create();

        if ($submissionType === 'helpdesk') {
            $submission = HelpdeskTicket::factory()->create([
                'user_id' => null, // Guest submission
                'guest_name' => $submitterData['name'],
                'guest_email' => $submitterData['email'],
                'guest_phone' => $submitterData['phone'],
                'subject' => 'Ujian Tiket Tetamu', // BM content
                'description' => 'Penerangan ujian untuk tiket tetamu',
            ]);

            // Verify guest submission properties
            $this->assertNull($submission->user_id);
            $this->assertEquals($submitterData['name'], $submission->guest_name);
            $this->assertEquals($submitterData['email'], $submission->guest_email);
            $this->assertTrue($submission->isGuestSubmission());
            $this->assertFalse($submission->isAuthenticatedSubmission());
        } else {
            $submission = LoanApplication::factory()->create([
                'user_id' => null, // Guest submission
                'applicant_name' => $submitterData['name'],
                'applicant_email' => $submitterData['email'],
                'applicant_phone' => $submitterData['phone'],
                'purpose' => 'Mesyuarat Rasmi', // BM content
                'location' => 'Bilik Mesyuarat 1',
                'division_id' => $division->id,
            ]);

            // Verify guest submission properties
            $this->assertNull($submission->user_id);
            $this->assertEquals($submitterData['name'], $submission->applicant_name);
            $this->assertEquals($submitterData['email'], $submission->applicant_email);
            $this->assertTrue($submission->isGuestSubmission());
            $this->assertFalse($submission->isAuthenticatedSubmission());
        }
    }

    #[Test]
    #[DataProvider('authenticatedSubmissionDataProvider')]
    public function property_authenticated_submission_links_to_user_id(
        string $submissionType,
        array $userData
    ): void {
        $division = Division::first() ?? Division::factory()->create();
        $grade = Grade::first() ?? Grade::factory()->create();

        $user = User::factory()->create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'phone' => $userData['phone'],
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        if ($submissionType === 'helpdesk') {
            $submission = HelpdeskTicket::factory()->create([
                'user_id' => $user->id, // Authenticated submission
                'guest_name' => null,
                'guest_email' => null,
                'subject' => 'Ujian Tiket Pengguna Disahkan', // BM content
                'description' => 'Penerangan ujian untuk tiket pengguna disahkan',
            ]);

            // Verify authenticated submission properties
            $this->assertEquals($user->id, $submission->user_id);
            $this->assertFalse($submission->isGuestSubmission());
            $this->assertTrue($submission->isAuthenticatedSubmission());
        } else {
            $submission = LoanApplication::factory()->create([
                'user_id' => $user->id, // Authenticated submission
                'applicant_name' => $user->name,
                'applicant_email' => $user->email,
                'purpose' => 'Lawatan Kerja Rasmi', // BM content
                'location' => 'Pejabat Negeri',
                'division_id' => $division->id,
            ]);

            // Verify authenticated submission properties
            $this->assertEquals($user->id, $submission->user_id);
            $this->assertFalse($submission->isGuestSubmission());
            $this->assertTrue($submission->isAuthenticatedSubmission());
        }
    }

    #[Test]
    public function property_hybrid_architecture_maintains_data_integrity(): void
    {
        $division = Division::first() ?? Division::factory()->create();
        $grade = Grade::first() ?? Grade::factory()->create();

        // Create authenticated user
        $user = User::factory()->create([
            'name' => 'Pengguna Disahkan',
            'email' => 'disahkan@motac.gov.my',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        // Create guest helpdesk ticket
        $guestTicket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_name' => 'Tetamu Helpdesk',
            'guest_email' => 'tetamu.helpdesk@motac.gov.my',
            'subject' => 'Tiket Tetamu',
        ]);

        // Create authenticated helpdesk ticket
        $authTicket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'subject' => 'Tiket Pengguna Disahkan',
        ]);

        // Create guest loan application
        $guestLoan = LoanApplication::factory()->create([
            'user_id' => null,
            'applicant_name' => 'Tetamu Pinjaman',
            'applicant_email' => 'tetamu.pinjaman@motac.gov.my',
            'purpose' => 'Mesyuarat Tetamu',
            'division_id' => $division->id,
        ]);

        // Create authenticated loan application
        $authLoan = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'applicant_name' => $user->name,
            'applicant_email' => $user->email,
            'purpose' => 'Mesyuarat Pengguna Disahkan',
            'division_id' => $division->id,
        ]);

        // Verify data integrity for all submissions
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $guestTicket->id,
            'user_id' => null,
            'guest_name' => 'Tetamu Helpdesk',
        ]);

        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $authTicket->id,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('loan_applications', [
            'id' => $guestLoan->id,
            'user_id' => null,
            'applicant_name' => 'Tetamu Pinjaman',
        ]);

        $this->assertDatabaseHas('loan_applications', [
            'id' => $authLoan->id,
            'user_id' => $user->id,
        ]);

        // Verify hybrid architecture methods
        $this->assertTrue($guestTicket->isGuestSubmission());
        $this->assertFalse($authTicket->isGuestSubmission());
        $this->assertTrue($guestLoan->isGuestSubmission());
        $this->assertFalse($authLoan->isGuestSubmission());
    }

    #[Test]
    public function property_nullable_user_id_fk_behavior(): void
    {
        $division = Division::first() ?? Division::factory()->create();

        // Test that user_id can be null (guest submission)
        $guestTicket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'tetamu@motac.gov.my',
        ]);

        $guestLoan = LoanApplication::factory()->create([
            'user_id' => null,
            'applicant_email' => 'tetamu@motac.gov.my',
            'division_id' => $division->id,
        ]);

        // Verify nullable FK works correctly
        $this->assertNull($guestTicket->user_id);
        $this->assertNull($guestTicket->user);
        $this->assertNull($guestLoan->user_id);
        $this->assertNull($guestLoan->user);

        // Test that user_id can be set (authenticated submission)
        $user = User::factory()->create();

        $authTicket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $authLoan = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'division_id' => $division->id,
        ]);

        // Verify FK relationship works correctly
        $this->assertEquals($user->id, $authTicket->user_id);
        $this->assertInstanceOf(User::class, $authTicket->user);
        $this->assertEquals($user->id, $authLoan->user_id);
        $this->assertInstanceOf(User::class, $authLoan->user);
    }

    #[Test]
    public function property_submitter_fields_captured_for_guests(): void
    {
        $division = Division::first() ?? Division::factory()->create();

        $guestData = [
            'name' => 'Ahmad Bin Ali',
            'email' => 'ahmad.ali@motac.gov.my',
            'phone' => '0123456789',
        ];

        // Test helpdesk ticket captures submitter fields
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_name' => $guestData['name'],
            'guest_email' => $guestData['email'],
            'guest_phone' => $guestData['phone'],
        ]);

        $this->assertEquals($guestData['name'], $ticket->guest_name);
        $this->assertEquals($guestData['email'], $ticket->guest_email);
        $this->assertEquals($guestData['phone'], $ticket->guest_phone);

        // Test loan application captures submitter fields
        $loan = LoanApplication::factory()->create([
            'user_id' => null,
            'applicant_name' => $guestData['name'],
            'applicant_email' => $guestData['email'],
            'applicant_phone' => $guestData['phone'],
            'division_id' => $division->id,
        ]);

        $this->assertEquals($guestData['name'], $loan->applicant_name);
        $this->assertEquals($guestData['email'], $loan->applicant_email);
        $this->assertEquals($guestData['phone'], $loan->applicant_phone);
    }

    #[Test]
    public function property_bahasa_melayu_content_in_submissions(): void
    {
        $division = Division::first() ?? Division::factory()->create();

        // Test BM content in helpdesk ticket
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'tetamu@motac.gov.my',
            'subject' => 'Masalah Komputer Tidak Berfungsi',
            'description' => 'Komputer saya tidak dapat dihidupkan sejak pagi tadi.',
        ]);

        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'subject' => 'Masalah Komputer Tidak Berfungsi',
        ]);

        // Test BM content in loan application
        $loan = LoanApplication::factory()->create([
            'user_id' => null,
            'applicant_email' => 'tetamu@motac.gov.my',
            'purpose' => 'Mesyuarat Rasmi Jabatan',
            'location' => 'Bilik Mesyuarat Utama',
            'division_id' => $division->id,
        ]);

        $this->assertDatabaseHas('loan_applications', [
            'id' => $loan->id,
            'purpose' => 'Mesyuarat Rasmi Jabatan',
            'location' => 'Bilik Mesyuarat Utama',
        ]);
    }

    /**
     * Data provider for guest submission test cases
     */
    public static function guestSubmissionDataProvider(): array
    {
        return [
            'helpdesk guest with simple name' => [
                'helpdesk',
                [
                    'name' => 'Ahmad Ali',
                    'email' => 'ahmad.ali@motac.gov.my',
                    'phone' => '0123456789',
                ],
            ],
            'helpdesk guest with full name' => [
                'helpdesk',
                [
                    'name' => 'Siti Nurhaliza Binti Abdullah',
                    'email' => 'siti.nurhaliza@motac.gov.my',
                    'phone' => '0198765432',
                ],
            ],
            'loan guest with simple name' => [
                'loan',
                [
                    'name' => 'Mohd Farid',
                    'email' => 'mohd.farid@motac.gov.my',
                    'phone' => '0111234567',
                ],
            ],
            'loan guest with full name' => [
                'loan',
                [
                    'name' => 'Nurul Izzah Binti Hassan',
                    'email' => 'nurul.izzah@motac.gov.my',
                    'phone' => '0129876543',
                ],
            ],
        ];
    }

    /**
     * Data provider for authenticated submission test cases
     */
    public static function authenticatedSubmissionDataProvider(): array
    {
        return [
            'helpdesk authenticated user' => [
                'helpdesk',
                [
                    'name' => 'Pengguna Disahkan Helpdesk',
                    'email' => 'pengguna.helpdesk@motac.gov.my',
                    'phone' => '0312345678',
                ],
            ],
            'loan authenticated user' => [
                'loan',
                [
                    'name' => 'Pengguna Disahkan Pinjaman',
                    'email' => 'pengguna.pinjaman@motac.gov.my',
                    'phone' => '0398765432',
                ],
            ],
        ];
    }
}
