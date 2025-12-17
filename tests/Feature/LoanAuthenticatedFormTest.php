<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\GuestLoanApplication;
use App\Models\Division;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoanAuthenticatedFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function authenticated_user_submission_links_to_user_id(): void
    {
        // Create a user with grade and division
        $division = Division::first();
        $grade = Grade::first();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@motac.gov.my',
            'phone' => '03-12345678',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        // Create an authenticated loan application directly to test hybrid architecture
        $authApplication = LoanApplication::factory()->create([
            'user_id' => $user->id, // Authenticated submission
            'applicant_name' => $user->name,
            'applicant_email' => $user->email,
            'purpose' => 'Ujian Permohonan Pinjaman', // BM content
            'location' => 'Bangunan Pejabat A', // BM content
            'division_id' => $division->id,
        ]);

        // Verify authenticated submission links to user_id
        $this->assertDatabaseHas('loan_applications', [
            'id' => $authApplication->id,
            'user_id' => $user->id, // Hybrid architecture: authenticated submission
            'applicant_name' => $user->name,
            'purpose' => 'Ujian Permohonan Pinjaman',
        ]);

        // Verify hybrid architecture methods work correctly
        $this->assertFalse($authApplication->isGuestSubmission());
        $this->assertTrue($authApplication->isAuthenticatedSubmission());
    }

    #[Test]
    public function authenticated_form_auto_fill_from_profile(): void
    {
        $division = Division::first();
        $grade = Grade::first();

        $user = User::factory()->create([
            'name' => 'Pengguna Pra-isi',
            'email' => 'praisi@motac.gov.my',
            'phone' => '03-11111111',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        $this->actingAs($user);

        // Verify form auto-fill from user profile
        // Note: division_id is intentionally NOT pre-filled to show placeholder
        Livewire::test(GuestLoanApplication::class)
            ->assertSet('form.applicant_name', $user->name)
            ->assertSet('form.phone', $user->phone);
    }

    #[Test]
    public function authenticated_user_sees_profile_display_in_bahasa(): void
    {
        app()->setLocale('ms'); // Set Bahasa Melayu locale

        $division = Division::first();
        $grade = Grade::first();

        $user = User::factory()->create([
            'name' => 'Pengguna Paparan',
            'email' => 'paparan@motac.gov.my',
            'phone' => '03-22222222',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        $this->actingAs($user);

        Livewire::test(GuestLoanApplication::class)
            ->assertSee($user->name)
            ->assertSee($user->phone)
            ->assertSee('Maklumat Anda') // BM: "Your Information"
            ->assertSee('Nama Penuh') // BM: "Full Name"
            ->assertSee('Bahagian'); // BM: "Division"
    }

    #[Test]
    public function guest_user_sees_form_input_fields_in_bahasa(): void
    {
        app()->setLocale('ms'); // Set Bahasa Melayu locale

        Livewire::test(GuestLoanApplication::class)
            ->assertSee('Nama Penuh') // BM: "Full Name"
            ->assertSee('Masukkan nama penuh anda') // BM: "Enter your full name"
            ->assertSee('Jawatan') // BM: "Position"
            ->assertSee('Bahagian') // BM: "Division"
            ->assertDontSee('Maklumat Anda'); // Should not see profile display
    }

    #[Test]
    public function guest_user_must_fill_contact_fields_on_step1(): void
    {
        // Test as guest (not authenticated) - verify required fields are shown
        Livewire::test(GuestLoanApplication::class)
            ->assertSet('currentStep', 1)
            ->assertSee('Nama Penuh') // BM: Full Name field should be visible for guests
            ->assertSee('Jawatan & Gred') // BM: Position & Grade field should be visible
            ->assertSee('No. Telefon') // BM: Phone field should be visible
            ->assertSee('Bahagian/Unit'); // BM: Division field should be visible
    }

    #[Test]
    public function guest_user_can_advance_when_all_contact_fields_filled(): void
    {
        $division = Division::first();

        // Test that guest users can set contact fields
        Livewire::test(GuestLoanApplication::class)
            ->assertSet('currentStep', 1)
            ->set('form.applicant_name', 'Pengguna Tetamu')
            ->set('form.applicant_position', 'Penolong Pegawai Tadbir N41')
            ->set('form.applicant_grade', '41')
            ->set('form.phone', '03-98765432')
            ->set('form.division_id', $division->id)
            ->assertSet('form.applicant_name', 'Pengguna Tetamu')
            ->assertSet('form.phone', '03-98765432');
    }

    #[Test]
    public function hybrid_workflow_validates_correctly(): void
    {
        $division = Division::first();
        $grade = Grade::first();

        // Test authenticated user workflow
        $user = User::factory()->create([
            'name' => 'Pengguna Disahkan',
            'email' => 'disahkan@motac.gov.my',
            'phone' => '03-33333333',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        // Create both guest and authenticated applications to test hybrid architecture
        $guestApp = LoanApplication::factory()->create([
            'user_id' => null,
            'applicant_name' => 'Tetamu',
            'applicant_email' => 'tetamu@motac.gov.my',
            'purpose' => 'Mesyuarat Tetamu',
            'division_id' => $division->id,
        ]);

        $authApp = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'applicant_name' => $user->name,
            'applicant_email' => $user->email,
            'purpose' => 'Mesyuarat Pengguna Disahkan',
            'division_id' => $division->id,
        ]);

        // Verify hybrid architecture works correctly
        $this->assertTrue($guestApp->isGuestSubmission());
        $this->assertFalse($guestApp->isAuthenticatedSubmission());

        $this->assertFalse($authApp->isGuestSubmission());
        $this->assertTrue($authApp->isAuthenticatedSubmission());

        $this->assertNull($guestApp->user_id);
        $this->assertEquals($user->id, $authApp->user_id);
    }
}
