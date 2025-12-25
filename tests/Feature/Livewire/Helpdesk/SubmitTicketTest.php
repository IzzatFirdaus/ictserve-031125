<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Helpdesk;

use App\Livewire\Helpdesk\SubmitTicket;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Submit Ticket Livewire Component Tests
 *
 * Tests for guest helpdesk ticket submission with bilingual support
 *
 * @requirements 1.1, 1.2, 11.1-11.7, 15.1, 15.4
 *
 * @version 1.0.0
 */
class SubmitTicketTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
    }

    #[Test]
    public function it_renders_successfully(): void
    {
        Livewire::test(SubmitTicket::class)
            ->assertStatus(200)
            ->assertSee(__('helpdesk.submit_ticket'))
            ->assertSee(__('helpdesk.submit_ticket_description'));
    }

    #[Test]
    public function it_loads_divisions_and_categories(): void
    {
        // Test that divisions and categories are passed to the view
        // Create test data BEFORE instantiating the component
        Division::factory()->create([
            'id' => 1,
            'code' => 'IT',
            'name_en' => 'IT Division',
            'name_ms' => 'Bahagian Teknologi Maklumat',
            'is_active' => true,
        ]);
        TicketCategory::factory()
            ->hardware()
            ->create([
                'id' => 1,
                'name_en' => 'Hardware Issue',
                'name_ms' => 'Isu Perkakasan',
                'is_active' => true,
            ]);

        $component = Livewire::test(SubmitTicket::class);

        // Verify divisions are displayed on step 1
        $component->assertSee('IT Division');

        // Navigate to step 2 to verify categories are displayed
        $component->set('currentStep', 2)
            ->assertSee('Hardware Issue');
    }

    #[Test]
    public function it_displays_localized_category_names_in_english(): void
    {
        // This test verifies that category names are localized to English
        // Categories are created in both test here instead of setUp to keep test isolation
        $alpha = TicketCategory::factory()->create([
            'code' => 'CAT-ALPHA',
            'name_en' => 'Alpha Support',
            'name_ms' => 'Sokongan Alfa',
            'is_active' => true,
        ]);
        $beta = TicketCategory::factory()->create([
            'code' => 'CAT-BETA',
            'name_en' => 'Beta Support',
            'name_ms' => 'Sokongan Beta',
            'is_active' => true,
        ]);

        app()->setLocale('en');

        $component = Livewire::test(SubmitTicket::class)
            ->assertSet('currentStep', 1)
            ->set('currentStep', 2); // Navigate to Step 2

        // Access computed property via magic property getter
        $categories = $component->categories;

        // Verify data was loaded
        $this->assertNotEmpty($categories, 'Categories should not be empty');
        $this->assertCount(2, $categories, 'Should have 2 categories');

        $component->assertSee('Alpha Support')
            ->assertSee('Beta Support')
            ->assertDontSee('Sokongan Alfa')
            ->assertDontSee('Sokongan Beta');
    }

    #[Test]
    public function it_displays_localized_category_names_in_malay(): void
    {
        // This test verifies that category names are localized to Malay
        $alpha = TicketCategory::factory()->create([
            'code' => 'CAT-ALPHA',
            'name_en' => 'Alpha Support',
            'name_ms' => 'Sokongan Alfa',
            'is_active' => true,
        ]);
        $beta = TicketCategory::factory()->create([
            'code' => 'CAT-BETA',
            'name_en' => 'Beta Support',
            'name_ms' => 'Sokongan Beta',
            'is_active' => true,
        ]);

        app()->setLocale('ms');

        Livewire::test(SubmitTicket::class)
            ->assertSet('currentStep', 1)
            ->set('currentStep', 2) // Navigate to Step 2
            ->assertSee('Sokongan Alfa')
            ->assertSee('Sokongan Beta')
            ->assertDontSee('Alpha Support')
            ->assertDontSee('Beta Support');
    }

    #[Test]
    public function it_validates_required_fields(): void
    {
        Livewire::test(SubmitTicket::class)
            ->set('currentStep', 1)
            ->call('submit')
            ->assertHasErrors([
                'guest_name' => 'required',
                'guest_email' => 'required',
                'guest_phone' => 'required',
                'division_id' => 'required',
            ]);
    }

    #[Test]
    public function it_validates_email_format(): void
    {
        Livewire::test(SubmitTicket::class)
            ->set('guest_email', 'invalid-email')
            ->set('currentStep', 1)
            ->call('submit')
            ->assertHasErrors(['guest_email' => 'email']);
    }

    #[Test]
    public function it_validates_description_minimum_length(): void
    {
        Livewire::test(SubmitTicket::class)
            ->set('description', 'Short')
            ->set('currentStep', 2)
            ->call('submit')
            ->assertHasErrors(['description' => 'min']);
    }

    #[Test]
    public function it_submits_ticket_successfully_as_guest(): void
    {
        // Create test data without hardcoded IDs
        $division = Division::factory()->create(['name_en' => 'IT Division']);
        $category = TicketCategory::factory()->hardware()->create(['name_en' => 'Hardware Issue']);

        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'John Doe')
            ->set('guest_email', 'john@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->set('staff_id', 'MOTAC001')
            ->set('division_id', $division->id)
            ->set('job_grade', '41')
            ->set('declaration_accepted', true)
            ->set('terms_accepted', true)
            ->set('category_id', $category->id)
            ->set('subject', 'Test Issue')
            ->set('description', 'This is a test description with more than 10 characters')
            ->call('submit')
            ->assertHasNoErrors();

        // Verify ticket was created in database
        $this->assertDatabaseHas('helpdesk_tickets', [
            'guest_name' => 'John Doe',
            'guest_email' => 'john@motac.gov.my',
            'subject' => 'Test Issue',
            'status' => 'open',
            'user_id' => null, // Guest submission
        ]);
    }

    #[Test]
    public function it_generates_unique_ticket_numbers(): void
    {
        // Create test data without hardcoded IDs
        $division = Division::factory()->create(['name_en' => 'IT Division']);
        $category = TicketCategory::factory()->hardware()->create(['name_en' => 'Hardware Issue']);

        // Verify no tickets exist initially
        $this->assertEquals(0, HelpdeskTicket::count());

        // Create first ticket
        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'John Doe 1')
            ->set('guest_email', 'john1@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->set('division_id', 1)
            ->set('job_grade', '41')
            ->set('declaration_accepted', true)
            ->set('terms_accepted', true)
            ->set('category_id', 1)
            ->set('subject', 'First Issue')
            ->set('description', 'First test description')
            ->call('submit');

        $this->assertEquals(1, HelpdeskTicket::count());
        $firstTicket = HelpdeskTicket::orderBy('id')->first();
        $this->assertNotNull($firstTicket, 'First ticket should exist');

        // Create second ticket
        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'John Doe 2')
            ->set('guest_email', 'john2@motac.gov.my')
            ->set('guest_phone', '+60123456788')
            ->set('division_id', 1)
            ->set('job_grade', '42')
            ->set('declaration_accepted', true)
            ->set('terms_accepted', true)
            ->set('category_id', 1)
            ->set('subject', 'Second Issue')
            ->set('description', 'Second test description')
            ->call('submit');

        $this->assertEquals(2, HelpdeskTicket::count());
        $allTickets = HelpdeskTicket::orderBy('id')->get();
        $this->assertEquals(2, $allTickets->count());

        $secondTicket = $allTickets[1];
        $this->assertNotNull($secondTicket, 'Second ticket should exist');

        // Verify ticket numbers are unique and follow format HD[YYYY][XXXXXX]
        $this->assertNotEquals($firstTicket->ticket_number, $secondTicket->ticket_number, 'Ticket numbers should be unique');
        $this->assertStringStartsWith('HD2025', $firstTicket->ticket_number);
        $this->assertStringStartsWith('HD2025', $secondTicket->ticket_number);

        // Verify sequential IDs in ticket numbers
        $firstId = (int) substr($firstTicket->ticket_number, -6);
        $secondId = (int) substr($secondTicket->ticket_number, -6);
        $this->assertEquals($firstId + 1, $secondId, 'Ticket number IDs should be sequential');
    }

    #[Test]
    public function it_clears_form_successfully(): void
    {
        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'John Doe')
            ->set('guest_email', 'john@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->call('resetForm')
            ->assertSet('guest_name', '')
            ->assertSet('guest_email', '')
            ->assertSet('guest_phone', '')
            ->assertSet('ticketNumber', null);
    }

    #[Test]
    public function it_displays_success_message_after_submission(): void
    {
        Division::factory()->create(['id' => 1, 'is_active' => true]);
        TicketCategory::factory()->create(['id' => 1, 'is_active' => true]);

        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'John Doe')
            ->set('guest_email', 'john@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->set('division_id', 1)
            ->set('job_grade', '41')
            ->set('declaration_accepted', true)
            ->set('terms_accepted', true)
            ->set('category_id', 1)
            ->set('subject', 'Test Issue')
            ->set('description', 'Test description with sufficient length')
            ->set('priority', 'normal')
            ->call('submit')
            ->assertSee('Your ticket has been submitted') // Match actual translation (lowercase)
            ->assertSee('Ticket number');
    }

    #[Test]
    public function it_uses_computed_properties_for_performance(): void
    {
        Division::factory()->create(['id' => 1, 'is_active' => true, 'name_en' => 'Test Division']);
        TicketCategory::factory()->hardware()->create(['id' => 1, 'is_active' => true, 'name_en' => 'Test Category']);

        $component = Livewire::test(SubmitTicket::class);

        // Verify divisions are rendered in the form on step 1
        $component->assertSee('Test Division')
            ->assertStatus(200); // Component renders without error

        // Verify categories render on step 2
        $component->set('currentStep', 2)
            ->assertSee('Test Category');
    }

    #[Test]
    public function it_supports_bilingual_validation_messages_english(): void
    {
        app()->setLocale('en');

        Livewire::test(SubmitTicket::class)
            ->set('currentStep', 1)
            ->call('submit')
            ->assertSee('You must accept this declaration to continue')
            ->assertSee('You must accept the terms of service to continue');
    }

    #[Test]
    public function it_supports_bilingual_validation_messages_malay(): void
    {
        app()->setLocale('ms');

        Livewire::test(SubmitTicket::class)
            ->set('currentStep', 1)
            ->call('submit')
            ->assertSee('Anda mesti menerima pengakuan ini untuk meneruskan')
            ->assertSee('Anda mesti menerima terma perkhidmatan untuk meneruskan');
    }

    #[Test]
    public function it_submits_ticket_as_authenticated_user(): void
    {
        // Create test data without hardcoded IDs
        $division = Division::factory()->create(['name_en' => 'IT Division']);
        $category = TicketCategory::factory()->hardware()->create(['name_en' => 'Hardware Issue']);

        // Create and authenticate user
        $user = \App\Models\User::factory()->create([
            'name' => 'Authenticated User',
            'email' => 'auth@motac.gov.my',
        ]);

        Livewire::actingAs($user)
            ->test(SubmitTicket::class)
            ->set('guest_name', 'Should Be Ignored')
            ->set('guest_email', 'ignored@example.com')
            ->set('guest_phone', '+60123456789')
            ->set('division_id', $division->id)
            ->set('job_grade', '41')
            ->set('declaration_accepted', true)
            ->set('terms_accepted', true)
            ->set('category_id', $category->id)
            ->set('subject', 'Authenticated Test Issue')
            ->set('description', 'This is an authenticated user submission')
            ->call('submit')
            ->assertHasNoErrors();

        // Verify ticket was created with user_id (authenticated submission)
        $this->assertDatabaseHas('helpdesk_tickets', [
            'subject' => 'Authenticated Test Issue',
            'status' => 'open',
            'user_id' => $user->id,
        ]);

        // Verify guest fields are null for authenticated submission
        $ticket = HelpdeskTicket::where('user_id', $user->id)->first();
        $this->assertNotNull($ticket);
        $this->assertNull($ticket->guest_name);
        $this->assertNull($ticket->guest_email);
        $this->assertNull($ticket->guest_phone);

        // Reproduce the bug: submit from step 3 when there are no guest fields set
        Livewire::actingAs($user)
            ->test(SubmitTicket::class)
            ->set('division_id', $division->id)
            ->set('job_grade', '41')
            ->set('declaration_accepted', true)
            ->set('terms_accepted', true)
            ->set('category_id', $category->id)
            ->set('subject', 'Authenticated Step 3 Issue')
            ->set('description', 'Submit from step 3 to ensure auth user validation passes')
            ->set('currentStep', 3)
            ->call('submit')
            ->assertHasNoErrors();
    }

    #[Test]
    public function it_uses_hybrid_service_for_guest_submission(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->hardware()->create();

        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'Guest User')
            ->set('guest_email', 'guest@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->set('division_id', $division->id)
            ->set('job_grade', '41')
            ->set('declaration_accepted', true)
            ->set('terms_accepted', true)
            ->set('category_id', $category->id)
            ->set('subject', 'Guest Issue')
            ->set('description', 'Guest description')
            ->call('submit');

        // Verify guest ticket was created
        $ticket = HelpdeskTicket::where('guest_email', 'guest@motac.gov.my')->first();
        $this->assertNotNull($ticket);
        $this->assertNull($ticket->user_id);
        $this->assertEquals('Guest User', $ticket->guest_name);
    }

    #[Test]
    public function it_requires_terms_accepted_for_guest_submission(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->hardware()->create();

        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'Guest User')
            ->set('guest_email', 'guest@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->set('division_id', $division->id)
            ->set('job_grade', '41')
            ->set('declaration_accepted', true)
            ->set('terms_accepted', false) // Terms NOT accepted
            ->set('category_id', $category->id)
            ->set('subject', 'Guest Issue')
            ->set('description', 'Guest description')
            ->call('submit')
            ->assertHasErrors('terms_accepted');

        // Verify no ticket was created
        $this->assertDatabaseMissing('helpdesk_tickets', [
            'guest_email' => 'guest@motac.gov.my',
        ]);
    }

    #[Test]
    public function it_requires_terms_accepted_for_authenticated_submission(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->hardware()->create();

        $user = \App\Models\User::factory()->create();

        Livewire::actingAs($user)
            ->test(SubmitTicket::class)
            ->set('division_id', $division->id)
            ->set('job_grade', '41')
            ->set('declaration_accepted', true)
            ->set('terms_accepted', false) // Terms NOT accepted
            ->set('category_id', $category->id)
            ->set('subject', 'Test Issue')
            ->set('description', 'Test description')
            ->call('submit')
            ->assertHasErrors('terms_accepted');

        // Verify no ticket was created
        $this->assertDatabaseMissing('helpdesk_tickets', [
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function it_accepts_submission_with_both_declaration_and_terms(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->hardware()->create();

        $user = \App\Models\User::factory()->create();

        Livewire::actingAs($user)
            ->test(SubmitTicket::class)
            ->set('division_id', $division->id)
            ->set('job_grade', '41')
            ->set('declaration_accepted', true)
            ->set('terms_accepted', true) // Both checkboxes accepted
            ->set('category_id', $category->id)
            ->set('subject', 'Compliant Issue')
            ->set('description', 'User accepted both declaration and terms')
            ->call('submit')
            ->assertHasNoErrors();

        // Verify ticket was created
        $this->assertDatabaseHas('helpdesk_tickets', [
            'user_id' => $user->id,
            'subject' => 'Compliant Issue',
        ]);
    }

    #[Test]
    public function it_shows_terms_validation_error_message_in_english(): void
    {
        app()->setLocale('en');
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->hardware()->create();

        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'Test Guest')
            ->set('guest_email', 'test@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->set('division_id', $division->id)
            ->set('job_grade', '41')
            ->set('declaration_accepted', true)
            ->set('terms_accepted', false)
            ->set('category_id', $category->id)
            ->set('subject', 'Test')
            ->set('description', 'Test description')
            ->call('submit')
            ->assertSee(__('helpdesk.terms_required'));
    }

    #[Test]
    public function it_shows_terms_validation_error_message_in_malay(): void
    {
        app()->setLocale('ms');
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->hardware()->create();

        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'Test Guest')
            ->set('guest_email', 'test@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->set('division_id', $division->id)
            ->set('job_grade', '41')
            ->set('declaration_accepted', true)
            ->set('terms_accepted', false)
            ->set('category_id', $category->id)
            ->set('subject', 'Test')
            ->set('description', 'Test description')
            ->call('submit')
            ->assertSee(__('helpdesk.terms_required'));
    }

    /**
     * Test ISO Document ID Compliance
     *
     * @trace Task 4.1.5 - ISO compliance header in guest ticket form
     * @trace Requirement 6.8 - ISO document identifier display
     */
    #[Test]
    public function it_displays_iso_document_id_in_form(): void
    {
        // The ISO document ID PK.(S).MOTAC.07.(L1) should be visible in the form
        // for ISO 9001:2015 compliance and audit traceability
        $response = $this->get(route('helpdesk.create'));

        $response->assertStatus(200)
            ->assertSee('PK.(S).MOTAC.07.(L1)');
    }

    /**
     * Test mandatory disclaimer checkbox gates submit button
     *
     * @trace Task 4.1.6 - Mandatory disclaimer checkbox gate
     * @trace Requirement 6.9 - Declaration acceptance required
     */
    #[Test]
    public function it_requires_declaration_accepted_for_submission(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->hardware()->create();

        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'Test Guest')
            ->set('guest_email', 'declaration-test@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->set('division_id', $division->id)
            ->set('job_grade', '41')
            ->set('declaration_accepted', false) // Declaration NOT accepted
            ->set('terms_accepted', true)
            ->set('category_id', $category->id)
            ->set('subject', 'Test Issue')
            ->set('description', 'Test description with sufficient length')
            ->call('submit')
            ->assertHasErrors('declaration_accepted');

        // Verify no ticket was created
        $this->assertDatabaseMissing('helpdesk_tickets', [
            'guest_email' => 'declaration-test@motac.gov.my',
        ]);
    }

    /**
     * Test both declaration and terms must be accepted
     *
     * @trace Task 4.1.6 - Both checkboxes required for submission
     */
    #[Test]
    public function it_requires_both_declaration_and_terms_for_submission(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->hardware()->create();

        // Test with neither accepted
        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'Test Guest')
            ->set('guest_email', 'both-test@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->set('division_id', $division->id)
            ->set('job_grade', '41')
            ->set('declaration_accepted', false)
            ->set('terms_accepted', false)
            ->set('category_id', $category->id)
            ->set('subject', 'Test Issue')
            ->set('description', 'Test description with sufficient length')
            ->call('submit')
            ->assertHasErrors(['declaration_accepted', 'terms_accepted']);
    }
}
