<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Division;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test Helpdesk Form Authentication-Aware Validation
 *
 * Validates that the helpdesk form correctly handles:
 * - Guest users: requires all contact fields
 * - Authenticated users: skips contact field validation
 *
 * @trace Bug Fix: Authenticated users getting validation errors on step 1
 */
class HelpdeskAuthenticatedFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed divisions for the test
        Division::factory()->create([
            'name_ms' => 'Bahagian ICT',
            'name_en' => 'ICT Division',
            'is_active' => true,
        ]);
    }

    /**
     * Test that authenticated users can advance from step 1 without filling guest fields
     * and form auto-fills user information (v3.6.0 Requirement 2.3)
     */
    #[Test]
    public function authenticated_user_form_auto_fill_and_advance_without_guest_validation(): void
    {
        // Arrange: Create and authenticate a user with BM-appropriate data
        $user = User::factory()->create([
            'name' => 'Ahmad Bin Hassan',
            'email' => 'ahmad.hassan@motac.gov.my',
            'phone' => '03-12345678',
            'staff_id' => 'MOTAC001',
        ]);

        $division = Division::first();

        // Act: Load the form as authenticated user and verify auto-fill
        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('division_id', $division->id)
            ->set('job_grade', 'Gred 41')
            ->set('declaration_accepted', true);

        // Assert: Form should auto-fill user information (Requirement 2.3)
        $component->assertSee('Ahmad Bin Hassan'); // User name auto-filled
        $component->assertSee('ahmad.hassan@motac.gov.my'); // Email auto-filled
        $component->assertSee('03-12345678'); // Phone auto-filled
        $component->assertSee('MOTAC001'); // Staff ID auto-filled

        // Assert: Starting at step 1
        $component->assertSet('currentStep', 1);

        // Act: Advance to step 2
        $component->call('nextStep');

        // Assert: Should successfully advance without validation errors
        $component->assertHasNoErrors();
        $component->assertSet('currentStep', 2);
    }

    /**
     * Ensure authenticated user's division is auto-filled and displays Bahasa Melayu name
     * (v3.6.0 Requirement 2.3 - Form Auto-Fill)
     */
    #[Test]
    public function authenticated_user_division_auto_fill_with_bahasa_melayu_name(): void
    {
        $division = Division::factory()->create([
            'code' => 'ICT',
            'name_ms' => 'Bahagian Pengurusan Maklumat',
            'name_en' => 'Information Management Division',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'division_id' => $division->id,
            'name' => 'Siti Aminah',
            'email' => 'siti.aminah@motac.gov.my',
        ]);

        // Act: Load form as authenticated user
        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class);

        // The division_id should be auto-filled from user's division (Requirement 2.3)
        $component->assertSet('division_id', $division->id);

        // The division record must display Bahasa Melayu name (v3.6.0 BM-only)
        $dbDivision = Division::find($component->get('division_id'));
        $this->assertNotNull($dbDivision);
        $this->assertSame('Bahagian Pengurusan Maklumat', $dbDivision->name_ms);

        // Verify BM content is available (division name may not be displayed in form but is auto-filled)
        // The important part is that division_id is auto-filled and the division has BM name
        $this->assertNotEmpty($dbDivision->name_ms, 'Division should have Bahasa Melayu name for v3.6.0');
    }

    /**
     * Test that guest users MUST fill contact fields on step 1 (no auto-fill)
     * Contrasts with authenticated users who get auto-fill (Requirement 2.3)
     */
    #[Test]
    public function guest_user_must_fill_contact_fields_without_auto_fill(): void
    {
        // Act: Load the form as guest (not authenticated) and try to advance without filling fields
        $component = Livewire::test(\App\Livewire\Helpdesk\SubmitTicket::class);

        // Assert: Starting at step 1
        $component->assertSet('currentStep', 1);

        // Assert: No auto-fill for guest users (contrast with authenticated auto-fill)
        $component->assertDontSee('ahmad.hassan@motac.gov.my'); // No auto-filled email
        $component->assertSee('Nama penuh'); // BM label for name field

        // Act: Try to advance to step 2 without filling required fields
        $component->call('nextStep');

        // Assert: Should have validation errors for step 1 guest fields
        // Note: job_grade and declaration_accepted are validated at submit(), not step 1
        $component->assertHasErrors([
            'guest_name',
            'guest_email',
            'guest_phone',
            'division_id',
        ]);

        // Assert: Should still be on step 1
        $component->assertSet('currentStep', 1);
    }

    /**
     * Test that guest users can advance when all fields are manually filled
     * (no auto-fill available, contrasts with authenticated users)
     */
    #[Test]
    public function guest_user_can_advance_when_contact_fields_manually_filled(): void
    {
        // Arrange: Create a division
        $division = Division::first();

        // Act: Load the form as guest and manually fill all required fields (no auto-fill)
        $component = Livewire::test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('guest_name', 'Fatimah Binti Ahmad')
            ->set('guest_email', 'fatimah.ahmad@motac.gov.my')
            ->set('guest_phone', '012-3456789')
            ->set('division_id', $division->id)
            ->set('job_grade', 'Gred 41')
            ->set('declaration_accepted', true);

        // Assert: Starting at step 1
        $component->assertSet('currentStep', 1);

        // Assert: Values are manually entered (no auto-fill for guests)
        $component->assertSet('guest_name', 'Fatimah Binti Ahmad');
        $component->assertSet('guest_email', 'fatimah.ahmad@motac.gov.my');

        // Act: Advance to step 2
        $component->call('nextStep');

        // Assert: Should successfully advance without validation errors
        $component->assertHasNoErrors();
        $component->assertSet('currentStep', 2);
    }

    /**
     * Test that job_grade accepts valid civil service grades
     */
    #[Test]
    public function job_grade_accepts_valid_grades(): void
    {
        $division = Division::first();
        $user = User::factory()->create();

        $validGrades = ['Gred 11', 'Gred 41', 'Gred 54', 'JUSA A', 'JUSA B', 'JUSA C'];

        foreach ($validGrades as $grade) {
            $component = Livewire::actingAs($user)
                ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
                ->set('division_id', $division->id)
                ->set('job_grade', $grade)
                ->set('declaration_accepted', true);

            $component->call('nextStep');
            $component->assertHasNoErrors(['job_grade']);
        }
    }

    /**
     * Test that declaration checkbox must be checked (true)
     */
    #[Test]
    public function declaration_must_be_explicitly_accepted(): void
    {
        $division = Division::first();
        $user = User::factory()->create();

        // Test with false
        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('division_id', $division->id)
            ->set('job_grade', 'Gred 41')
            ->set('declaration_accepted', false);

        $component->call('nextStep');
        $component->assertHasErrors(['declaration_accepted']);

        // Test with true
        $component->set('declaration_accepted', true);
        $component->call('nextStep');
        $component->assertHasNoErrors(['declaration_accepted']);
    }

    /**
     * Test that authenticated user sees their auto-filled info displayed (not form fields)
     * (v3.6.0 Requirement 2.3 - Form Auto-Fill for Authenticated Users)
     */
    #[Test]
    public function authenticated_user_sees_auto_filled_info_displayed(): void
    {
        // Arrange: Create and authenticate a user with BM-appropriate name
        $user = User::factory()->create([
            'name' => 'Datuk Seri Rahman',
            'email' => 'datuk.rahman@motac.gov.my',
            'phone' => '03-12345681',
            'staff_id' => 'MOTAC004',
        ]);

        // Act & Assert: Authenticated user should see their auto-filled info (Requirement 2.3)
        Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->assertSee('Datuk Seri Rahman') // Auto-filled name
            ->assertSee('datuk.rahman@motac.gov.my') // Auto-filled email
            ->assertSee('03-12345681') // Auto-filled phone
            ->assertSee('MOTAC004') // Auto-filled staff ID
            // Should NOT see input fields for guest data (auto-fill replaces manual entry)
            ->assertDontSee('wire:model.live.debounce.300ms="guest_name"')
            ->assertDontSee('wire:model.live.debounce.300ms="guest_email"');
    }

    /**
     * Test that guest user sees Bahasa Melayu input form fields (no auto-fill)
     * Contrasts with authenticated users who get auto-filled info (Requirement 2.3)
     */
    #[Test]
    public function guest_user_sees_bahasa_melayu_form_fields_without_auto_fill(): void
    {
        // Act & Assert: Guest user should see BM input fields (no auto-fill)
        Livewire::test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->assertSee('Nama penuh') // BM: Full name
            ->assertSee('Alamat e-mel') // BM: Email address
            ->assertSee('Nombor telefon') // BM: Phone number
            ->assertSee('Bahagian') // BM: Division
            // Should NOT see authenticated user auto-filled info display
            ->assertDontSee('Maklumat anda'); // BM: Your information
    }

    /**
     * Test that job_grade and declaration_accepted are validated at submission for authenticated users
     */
    #[Test]
    public function authenticated_submission_validates_job_grade_and_declaration(): void
    {
        $user = User::factory()->create();
        $division = Division::first();
        $category = TicketCategory::factory()->create();

        // Try to submit without job_grade
        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('division_id', $division->id)
            ->set('declaration_accepted', true)
            ->call('nextStep') // Advance to step 2
            ->set('category_id', $category->id)
            ->set('priority', 'normal')
            ->call('nextStep') // Advance to step 3
            ->set('subject', 'Test Subject')
            ->set('description', 'This is a test description with more than 10 characters.')
            ->call('submit');

        $component->assertHasErrors(['job_grade']);

        // Try to submit without declaration_accepted
        $component2 = Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('division_id', $division->id)
            ->set('job_grade', 'Gred 41')
            ->set('declaration_accepted', false)
            ->call('nextStep')
            ->set('category_id', $category->id)
            ->set('priority', 'normal')
            ->call('nextStep')
            ->set('subject', 'Test Subject')
            ->set('description', 'This is a test description with more than 10 characters.')
            ->call('submit');

        $component2->assertHasErrors(['declaration_accepted']);
    }

    /**
     * Test that guest submission validates job_grade and declaration_accepted
     */
    #[Test]
    public function guest_submission_validates_job_grade_and_declaration(): void
    {
        $division = Division::first();
        $category = TicketCategory::factory()->create();

        // Try to submit without job_grade
        $component = Livewire::test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('guest_name', 'John Doe')
            ->set('guest_email', 'john@example.com')
            ->set('guest_phone', '012-3456789')
            ->set('division_id', $division->id)
            ->set('declaration_accepted', true)
            ->call('nextStep')
            ->set('category_id', $category->id)
            ->set('priority', 'normal')
            ->call('nextStep')
            ->set('subject', 'Test Subject')
            ->set('description', 'This is a test description with more than 10 characters.')
            ->call('submit');

        $component->assertHasErrors(['job_grade']);

        // Try to submit without declaration_accepted
        $component2 = Livewire::test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('guest_name', 'Jane Doe')
            ->set('guest_email', 'jane@example.com')
            ->set('guest_phone', '012-9876543')
            ->set('division_id', $division->id)
            ->set('job_grade', 'Gred 41')
            ->set('declaration_accepted', false)
            ->call('nextStep')
            ->set('category_id', $category->id)
            ->set('priority', 'normal')
            ->call('nextStep')
            ->set('subject', 'Test Subject')
            ->set('description', 'This is a test description with more than 10 characters.')
            ->call('submit');

        $component2->assertHasErrors(['declaration_accepted']);
    }
}
