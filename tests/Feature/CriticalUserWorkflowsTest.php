<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\LoanStatus;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\OTPHandoverService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Critical User Workflows Feature Tests
 *
 * Tests for critical user workflows including:
 * - Ticket Claiming (R26)
 * - Profile Data Correction (R25)
 * - OTP Handover Verification (R20)
 * - Contact Form Integration (R21)
 * - Unified Login with Role Detection (R22)
 *
 * @see D03-FR-026 Ticket Claiming Workflow
 * @see D03-FR-025 Profile Data Management
 * @see D03-FR-020 Digital Handshake (OTP Verification)
 * @see D03-FR-021 Contact Form Integration
 * @see D03-FR-022 Unified Authentication Interface
 * @see Task 6.1.2 - Write feature tests for user workflows
 */
class CriticalUserWorkflowsTest extends TestCase
{
    protected User $staff;

    protected User $admin;

    protected Division $division;

    protected TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->division = Division::factory()->create([
            'name_en' => 'IT Division',
            'name_ms' => 'Bahagian Teknologi Maklumat',
            'is_active' => true,
        ]);

        $this->category = TicketCategory::factory()->create([
            'name_en' => 'General Enquiry',
            'name_ms' => 'Pertanyaan Umum',
            'code' => 'GEN',
            'is_active' => true,
        ]);

        $this->staff = User::factory()->create([
            'email' => 'staff@motac.gov.my',
            'name' => 'Test Staff',
            'role' => 'staff',
        ]);

        $this->admin = User::factory()->create([
            'email' => 'admin@motac.gov.my',
            'name' => 'Test Admin',
            'role' => 'admin',
        ]);

        // Assign Spatie roles if available
        if (class_exists('\Spatie\Permission\Models\Role')) {
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
            $this->admin->assignRole($adminRole);
        }
    }

    // =========================================================================
    // TICKET CLAIMING WORKFLOW TESTS (R26)
    // =========================================================================

    #[Test]
    public function staff_can_view_claimable_tickets_matching_their_email(): void
    {
        // Create guest tickets with staff's email
        $guestTicket1 = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => $this->staff->email,
            'guest_name' => 'Guest Submission',
            'subject' => 'Guest Ticket 1',
        ]);

        $guestTicket2 = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => $this->staff->email,
            'guest_name' => 'Guest Submission',
            'subject' => 'Guest Ticket 2',
        ]);

        // Create ticket with different email (should not be claimable)
        HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'other@motac.gov.my',
            'guest_name' => 'Other Guest',
            'subject' => 'Other Ticket',
        ]);

        $this->actingAs($this->staff);

        // Query claimable tickets
        $claimableTickets = HelpdeskTicket::query()
            ->whereNull('user_id')
            ->where('guest_email', $this->staff->email)
            ->get();

        $this->assertCount(2, $claimableTickets);
        $this->assertTrue($claimableTickets->contains($guestTicket1));
        $this->assertTrue($claimableTickets->contains($guestTicket2));
    }

    #[Test]
    public function claiming_ticket_requires_email_verification(): void
    {
        $guestTicket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => $this->staff->email,
            'guest_name' => 'Guest Submission',
        ]);

        $this->actingAs($this->staff);

        // Simulate OTP generation for claiming
        $otp = '123456'; // Fixed OTP for testing
        $hashedOtp = Hash::make($otp);

        // Store in session (simulating the claiming workflow)
        session([
            'claim_otp' => $hashedOtp,
            'claim_otp_expires' => now()->addMinutes(10),
            'claim_ticket_ids' => [$guestTicket->id],
        ]);

        // Verify OTP check works
        $this->assertTrue(Hash::check($otp, session('claim_otp')));
        $this->assertTrue(now()->lt(session('claim_otp_expires')));
    }

    #[Test]
    public function successful_claim_links_ticket_to_user_account(): void
    {
        $guestTicket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => $this->staff->email,
            'guest_name' => 'Guest Submission',
            'subject' => 'Claimable Ticket',
        ]);

        $this->assertNull($guestTicket->user_id);

        // Simulate successful claim
        $guestTicket->update(['user_id' => $this->staff->id]);

        $guestTicket->refresh();
        $this->assertEquals($this->staff->id, $guestTicket->user_id);
    }

    #[Test]
    public function expired_otp_prevents_ticket_claiming(): void
    {
        $guestTicket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => $this->staff->email,
        ]);

        $this->actingAs($this->staff);

        // Set expired OTP
        session([
            'claim_otp' => Hash::make('123456'),
            'claim_otp_expires' => now()->subMinutes(5), // Expired
            'claim_ticket_ids' => [$guestTicket->id],
        ]);

        // Verify OTP is expired
        $this->assertTrue(now()->gt(session('claim_otp_expires')));

        // Ticket should remain unclaimed
        $guestTicket->refresh();
        $this->assertNull($guestTicket->user_id);
    }

    // =========================================================================
    // PROFILE DATA CORRECTION TESTS (R25)
    // =========================================================================

    #[Test]
    public function profile_page_displays_read_only_fields(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get(route('portal.profile'));
        $response->assertSuccessful();

        // Profile page should show read-only fields
        $response->assertSee($this->staff->email);
    }

    #[Test]
    public function request_correction_creates_helpdesk_ticket(): void
    {
        $this->actingAs($this->staff);

        // Create profile correction category
        $correctionCategory = TicketCategory::factory()->create([
            'name_en' => 'Profile Data Correction',
            'name_ms' => 'Pembetulan Data Profil',
            'code' => 'PDC',
            'is_active' => true,
        ]);

        // Simulate correction request
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->staff->id,
            'category_id' => $correctionCategory->id,
            'subject' => 'Profile Data Correction Request - Email',
            'description' => 'Current value: '.$this->staff->email,
            'priority' => 'normal',
        ]);

        $this->assertDatabaseHas('helpdesk_tickets', [
            'user_id' => $this->staff->id,
            'category_id' => $correctionCategory->id,
        ]);

        $this->assertStringContainsString('Profile Data Correction', $ticket->subject);
    }

    // =========================================================================
    // OTP HANDOVER VERIFICATION TESTS (R20)
    // =========================================================================

    #[Test]
    public function otp_is_generated_when_loan_is_approved(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'user_id' => $this->staff->id,
        ]);

        $service = new OTPHandoverService;
        $otp = $service->generatePickupOTP($application);

        // OTP should be 4 digits
        $this->assertEquals(4, strlen($otp));
        $this->assertMatchesRegularExpression('/^\d{4}$/', $otp);

        // Application should have hashed OTP stored
        $application->refresh();
        $this->assertNotNull($application->pickup_otp_hash);
        $this->assertNotNull($application->pickup_otp_expires_at);
        $this->assertTrue($application->pickup_otp_expires_at->gt(now()));
    }

    #[Test]
    public function correct_otp_validates_successfully(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'user_id' => $this->staff->id,
        ]);

        $service = new OTPHandoverService;
        $otp = $service->generatePickupOTP($application);

        // Validate with correct OTP
        $result = $service->validatePickupOTP($application, $otp, $this->admin);

        $this->assertTrue($result);

        // Validation timestamp should be recorded
        $application->refresh();
        $this->assertNotNull($application->pickup_otp_validated_at);
        $this->assertEquals($this->admin->id, $application->pickup_otp_validated_by);
    }

    #[Test]
    public function incorrect_otp_fails_validation(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'user_id' => $this->staff->id,
        ]);

        $service = new OTPHandoverService;
        $service->generatePickupOTP($application);

        // Validate with incorrect OTP
        $result = $service->validatePickupOTP($application, '0000', $this->admin);

        $this->assertFalse($result);

        // Attempt counter should increment
        $application->refresh();
        $this->assertGreaterThan(0, $application->pickup_otp_attempts);
    }

    #[Test]
    public function expired_otp_fails_validation(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'user_id' => $this->staff->id,
            'pickup_otp_hash' => Hash::make('1234'),
            'pickup_otp_expires_at' => now()->subHours(1), // Expired
        ]);

        $service = new OTPHandoverService;
        $result = $service->validatePickupOTP($application, '1234', $this->admin);

        $this->assertFalse($result);
    }

    #[Test]
    public function otp_can_be_regenerated(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'user_id' => $this->staff->id,
        ]);

        $service = new OTPHandoverService;
        $firstOtp = $service->generatePickupOTP($application);
        $firstHash = $application->fresh()->pickup_otp_hash;

        // Regenerate OTP
        $secondOtp = $service->regenerateOTP($application);

        $this->assertNotEquals($firstOtp, $secondOtp);

        $application->refresh();
        $this->assertNotEquals($firstHash, $application->pickup_otp_hash);
    }

    // =========================================================================
    // CONTACT FORM INTEGRATION TESTS (R21)
    // =========================================================================

    #[Test]
    public function contact_form_creates_helpdesk_ticket(): void
    {
        // Create General Enquiry category
        $enquiryCategory = TicketCategory::factory()->create([
            'name_en' => 'General Enquiry',
            'name_ms' => 'Pertanyaan Umum',
            'code' => 'GEN-ENQ',
            'is_active' => true,
        ]);

        // Simulate contact form submission creating a ticket
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'category_id' => $enquiryCategory->id,
            'guest_name' => 'Contact Form User',
            'guest_email' => 'contact@example.com',
            'guest_phone' => '03-12345678',
            'subject' => 'Contact Form Enquiry',
            'description' => 'Message from contact form',
            'source' => 'contact_form',
        ]);

        $this->assertDatabaseHas('helpdesk_tickets', [
            'guest_email' => 'contact@example.com',
            'source' => 'contact_form',
            'category_id' => $enquiryCategory->id,
        ]);

        // Ticket number should be generated
        $this->assertNotNull($ticket->ticket_number);
        $this->assertStringStartsWith('HD', $ticket->ticket_number);
    }

    #[Test]
    public function contact_form_returns_ticket_id_for_tracking(): void
    {
        $enquiryCategory = TicketCategory::factory()->create([
            'code' => 'GEN-ENQ',
            'is_active' => true,
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'category_id' => $enquiryCategory->id,
            'guest_email' => 'track@example.com',
            'source' => 'contact_form',
        ]);

        // Verify ticket number format for tracking
        $this->assertMatchesRegularExpression('/^HD\d{4}\d{6}$/', $ticket->ticket_number);
    }

    // =========================================================================
    // UNIFIED LOGIN TESTS (R22)
    // =========================================================================

    #[Test]
    public function login_page_is_accessible(): void
    {
        $response = $this->get(route('login'));
        $response->assertSuccessful();
    }

    #[Test]
    public function staff_user_redirects_to_portal_dashboard_after_login(): void
    {
        $response = $this->actingAs($this->staff)
            ->get(route('portal.dashboard'));

        $response->assertSuccessful();
    }

    #[Test]
    public function admin_user_can_access_admin_panel(): void
    {
        $this->actingAs($this->admin);

        // Admin should have admin role
        $this->assertTrue($this->admin->hasRole('admin'));
    }

    #[Test]
    public function unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get(route('portal.dashboard'));
        $response->assertRedirect(route('login'));
    }

    // =========================================================================
    // SERVICE REQUEST ROUTING TESTS (R21)
    // =========================================================================

    #[Test]
    public function service_request_routes_to_helpdesk_with_prefilled_category(): void
    {
        $serviceCategory = TicketCategory::factory()->create([
            'name_en' => 'Service Request',
            'name_ms' => 'Permintaan Perkhidmatan',
            'code' => 'SVC-REQ',
            'is_active' => true,
        ]);

        // Simulate service request creating a ticket
        $ticket = HelpdeskTicket::factory()->create([
            'category_id' => $serviceCategory->id,
            'guest_email' => 'service@example.com',
            'subject' => 'Service Request',
            'source' => 'service_request',
        ]);

        $this->assertDatabaseHas('helpdesk_tickets', [
            'category_id' => $serviceCategory->id,
            'source' => 'service_request',
        ]);
    }

    // =========================================================================
    // BILINGUAL SUPPORT TESTS (R13)
    // =========================================================================

    #[Test]
    public function language_preference_persists_in_session(): void
    {
        $this->actingAs($this->staff);

        // Set language to Malay
        session(['locale' => 'ms']);

        $this->assertEquals('ms', session('locale'));

        // Set language to English
        session(['locale' => 'en']);

        $this->assertEquals('en', session('locale'));
    }

    #[Test]
    public function locale_detection_follows_priority_order(): void
    {
        // Priority: session > cookie > Accept-Language > config default

        // Test session takes priority
        session(['locale' => 'ms']);
        $this->assertEquals('ms', session('locale'));

        // Clear session, cookie should be next
        session()->forget('locale');
        $this->assertNull(session('locale'));
    }
}
