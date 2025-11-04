<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Helpdesk;

use App\Livewire\Helpdesk\SubmitTicket;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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
    use DatabaseMigrations;

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
            ->assertSee(__('helpdesk.quick_submission'));
    }

    #[Test]
    public function it_loads_divisions_and_categories(): void
    {
        // Test that divisions and categories computed properties work
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

        // Verify computed properties return collections (implementation test)
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $component->divisions);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $component->categories);

        // Verify data was loaded
        $this->assertGreaterThan(0, $component->divisions->count());
        $this->assertGreaterThan(0, $component->categories->count());
    }

    #[Test]
    public function it_displays_localized_category_names_in_english(): void
    {
        TicketCategory::factory()->create([
            'code' => 'CAT-ALPHA',
            'name_en' => 'Alpha Support',
            'name_ms' => 'Sokongan Alfa',
            'is_active' => true,
        ]);
        TicketCategory::factory()->create([
            'code' => 'CAT-BETA',
            'name_en' => 'Beta Support',
            'name_ms' => 'Sokongan Beta',
            'is_active' => true,
        ]);

        app()->setLocale('en');

        Livewire::test(SubmitTicket::class)
            ->assertSeeInOrder([
                'Alpha Support',
                'Beta Support',
            ]);
    }

    #[Test]
    public function it_displays_localized_category_names_in_malay(): void
    {
        TicketCategory::factory()->create([
            'code' => 'CAT-ALPHA',
            'name_en' => 'Alpha Support',
            'name_ms' => 'Sokongan Alfa',
            'is_active' => true,
        ]);
        TicketCategory::factory()->create([
            'code' => 'CAT-BETA',
            'name_en' => 'Beta Support',
            'name_ms' => 'Sokongan Beta',
            'is_active' => true,
        ]);

        app()->setLocale('ms');

        Livewire::test(SubmitTicket::class)
            ->assertSeeInOrder([
                'Sokongan Alfa',
                'Sokongan Beta',
            ]);
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
        // Create test data
        Division::factory()->create(['id' => 1, 'name_en' => 'IT Division']);
        TicketCategory::factory()->hardware()->create(['id' => 1, 'name_en' => 'Hardware Issue']);

        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'John Doe')
            ->set('guest_email', 'john@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->set('staff_id', 'MOTAC001')
            ->set('division_id', 1)
            ->set('category_id', 1)
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
        // Create test data
        Division::factory()->create(['id' => 1, 'name_en' => 'IT Division']);
        TicketCategory::factory()->hardware()->create(['id' => 1, 'name_en' => 'Hardware Issue']);

        // Verify no tickets exist initially
        $this->assertEquals(0, HelpdeskTicket::count());

        // Create first ticket
        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'John Doe')
            ->set('guest_email', 'john@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->set('division_id', 1)
            ->set('category_id', 1)
            ->set('subject', 'First Issue')
            ->set('description', 'First test description')
            ->call('submit');

        $this->assertEquals(1, HelpdeskTicket::count());
        $firstTicket = HelpdeskTicket::orderBy('id')->first();
        $this->assertNotNull($firstTicket, 'First ticket should exist');

        // Create second ticket
        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'Jane Doe')
            ->set('guest_email', 'jane@motac.gov.my')
            ->set('guest_phone', '+60123456788')
            ->set('division_id', 1)
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
            ->set('category_id', 1)
            ->set('subject', 'Test Issue')
            ->set('description', 'Test description with sufficient length')
            ->set('priority', 'normal')
            ->call('submit')
            ->assertSee('Your Ticket Has Been Submitted')
            ->assertSee('Ticket Number');
    }

    #[Test]
    public function it_uses_computed_properties_for_performance(): void
    {
        $component = Livewire::test(SubmitTicket::class);

        // Verify computed properties are used
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $component->divisions);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $component->categories);
    }

    #[Test]
    public function it_supports_bilingual_validation_messages_english(): void
    {
        app()->setLocale('en');

        Livewire::test(SubmitTicket::class)
            ->set('currentStep', 1)
            ->call('submit')
            ->assertSee('Full name is required')
            ->assertSee('Email address is required');
    }

    #[Test]
    public function it_supports_bilingual_validation_messages_malay(): void
    {
        app()->setLocale('ms');

        Livewire::test(SubmitTicket::class)
            ->set('currentStep', 1)
            ->call('submit')
            ->assertSee('Nama penuh diperlukan')
            ->assertSee('Alamat e-mel diperlukan');
    }

    #[Test]
    public function it_submits_ticket_as_authenticated_user(): void
    {
        // Create test data
        Division::factory()->create(['id' => 1, 'name_en' => 'IT Division']);
        TicketCategory::factory()->hardware()->create(['id' => 1, 'name_en' => 'Hardware Issue']);

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
            ->set('division_id', 1)
            ->set('category_id', 1)
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
        $this->assertNull($ticket->guest_name);
        $this->assertNull($ticket->guest_email);
        $this->assertNull($ticket->guest_phone);
    }

    #[Test]
    public function it_uses_hybrid_service_for_guest_submission(): void
    {
        Division::factory()->create(['id' => 1]);
        TicketCategory::factory()->hardware()->create(['id' => 1]);

        Livewire::test(SubmitTicket::class)
            ->set('guest_name', 'Guest User')
            ->set('guest_email', 'guest@motac.gov.my')
            ->set('guest_phone', '+60123456789')
            ->set('division_id', 1)
            ->set('category_id', 1)
            ->set('subject', 'Guest Issue')
            ->set('description', 'Guest submission test')
            ->call('submit');

        // Verify guest ticket was created
        $ticket = HelpdeskTicket::where('guest_email', 'guest@motac.gov.my')->first();
        $this->assertNotNull($ticket);
        $this->assertNull($ticket->user_id);
        $this->assertEquals('Guest User', $ticket->guest_name);
    }
}
