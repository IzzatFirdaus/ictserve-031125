<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\GuestLoanApplication;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuestLoanApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_submit_loan_application_with_null_user_id(): void
    {
        $division = Division::factory()->create();

        // Create a guest loan application directly to test hybrid architecture
        $guestApplication = LoanApplication::factory()->create([
            'user_id' => null, // Guest submission
            'applicant_name' => 'John Doe',
            'applicant_email' => 'john.doe@motac.gov.my',
            'applicant_phone' => '0123456789',
            'purpose' => 'Mesyuarat Rasmi', // BM content
            'location' => 'Bilik Mesyuarat 1', // BM content
            'division_id' => $division->id,
        ]);

        // Verify guest submission creates record with user_id=NULL
        $this->assertDatabaseHas('loan_applications', [
            'id' => $guestApplication->id,
            'applicant_name' => 'John Doe',
            'user_id' => null, // Hybrid architecture: guest submission
            'purpose' => 'Mesyuarat Rasmi',
        ]);

        // Verify hybrid architecture methods work correctly
        $this->assertTrue($guestApplication->isGuestSubmission());
        $this->assertFalse($guestApplication->isAuthenticatedSubmission());
    }

    #[Test]
    public function guest_submission_captures_submitter_fields(): void
    {
        $division = Division::factory()->create();

        // Create a guest loan application to test submitter field capture
        $guestApplication = LoanApplication::factory()->create([
            'user_id' => null, // Guest submission
            'applicant_name' => 'Jane Smith',
            'applicant_email' => 'jane.smith@motac.gov.my',
            'applicant_phone' => '0198765432',
            'purpose' => 'Lawatan Kerja',
            'location' => 'Pejabat Negeri',
            'division_id' => $division->id,
        ]);

        // Verify submitter fields are captured for guest
        $this->assertDatabaseHas('loan_applications', [
            'id' => $guestApplication->id,
            'user_id' => null,
            'applicant_name' => 'Jane Smith',
            'applicant_email' => 'jane.smith@motac.gov.my',
            'applicant_phone' => '0198765432',
        ]);

        // Verify it's recognized as a guest submission
        $this->assertTrue($guestApplication->isGuestSubmission());
    }

    #[Test]
    public function guest_loan_application_displays_bahasa(): void
    {
        // Test BM content in loan application form
        Livewire::test(GuestLoanApplication::class)
            ->assertSee('MAKLUMAT PEMOHON') // BM section header
            ->assertSee('Nama Penuh') // BM field label
            ->assertSee('Bahagian/Unit') // BM division label
            ->assertSee('Tujuan Permohonan') // BM purpose label
            ->assertSee('Lokasi') // BM location label
            ->assertSee('Tarikh Pinjaman'); // BM loan date label
    }

    #[Test]
    public function hybrid_data_association_works_correctly(): void
    {
        $division = Division::factory()->create();

        // Create a guest application
        $guestApp = LoanApplication::factory()->create([
            'user_id' => null, // Guest submission
            'applicant_name' => 'Guest User',
            'applicant_email' => 'guest@motac.gov.my',
            'division_id' => $division->id,
        ]);

        // Create an authenticated user application
        $user = User::factory()->create();
        $authApp = LoanApplication::factory()->create([
            'user_id' => $user->id, // Authenticated submission
            'applicant_name' => $user->name,
            'applicant_email' => $user->email,
            'division_id' => $division->id,
        ]);

        // Verify hybrid data association
        $this->assertTrue($guestApp->isGuestSubmission());
        $this->assertFalse($guestApp->isAuthenticatedSubmission());

        $this->assertFalse($authApp->isGuestSubmission());
        $this->assertTrue($authApp->isAuthenticatedSubmission());

        // Verify nullable user_id FK behavior
        $this->assertNull($guestApp->user_id);
        $this->assertNotNull($authApp->user_id);
        $this->assertEquals($user->id, $authApp->user_id);
    }
}
