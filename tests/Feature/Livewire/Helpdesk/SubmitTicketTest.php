<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Helpdesk;

use App\Livewire\Helpdesk\SubmitTicket;
use App\Models\Category;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Submit Ticket Livewire Component Tests
 *
 * Tests for guest helpdesk ticket submission with bilingual support
 *
 * @requirements 1.1, 1.2, 11.1-11.7, 15.1, 15.4
 * @version 1.0.0
 */
class SubmitTicketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        Division::factory()->create(['id' => 1, 'name' => 'IT Division']);
        Category::factory()->create(['id' => 1, 'name' => 'Hardware Issue', 'type' => 'helpdesk']);
    }

    /** @test */
    public function it_renders_successfully(): void
    {
        Livewire::test(SubmitTicket::class)
            ->assertStatus(200)
            ->assertSee(__('helpdesk.submit_ticket'))
            ->assertSee(__('helpdesk.quick_submission'));
    }

    /** @test */
    public function it_loads_divisions_and_categories(): void
    {
        Livewire::test(SubmitTicket::class)
            ->assertSee('IT Division')
            ->assertSee('Hardware Issue');
    }

    /** @test */
    public function it_validates_required_fields(): void
    {
        Livewire::test(SubmitTicket::class)
            ->call('submitTicket')
            ->assertHasErrors([
                'form.name' => 'required',
                'form.email' => 'required',
                'form.phone' => 'required',
                'form.division_id' => 'required',
                'form.category_id' => 'required',
                'form.subject' => 'required',
                'form.description' => 'required',
            ]);
    }

    /** @test */
    public function it_validates_email_format(): void
    {
        Livewire::test(SubmitTicket::class)
            ->set('form.email', 'invalid-email')
            ->call('submitTicket')
            ->assertHasErrors(['form.email' => 'email']);
    }

    /** @test */
    public function it_validates_description_minimum_length(): void
    {
        Livewire::test(SubmitTicket::class)
            ->set('form.description', 'Short')
            ->call('submitTicket')
            ->assertHasErrors(['form.description' => 'min']);
    }

    /** @test */
    public function it_submits_ticket_successfully_as_guest(): void
    {
        Livewire::test(SubmitTicket::class)
            ->set('form.name', 'John Doe')
            ->set('form.email', 'john@motac.gov.my')
            ->set('form.phone', '+60123456789')
            ->set('form.staff_id', 'MOTAC001')
            ->set('form.division_id', 1)
            ->set('form.category_id', 1)
            ->set('form.subject', 'Test Issue')
            ->set('form.description', 'This is a test description with more than 10 characters')
            ->call('submitTicket')
            ->assertHasNoErrors()
            ->assertSet('submitted', true)
            ->assertSet('ticketNumber', function ($value) {
                return str_starts_with($value, 'HD' . date('Y'));
            });

        // Verify ticket was created in database
        $this->assertDatabaseHas('helpdesk_tickets', [
            'guest_name' => 'John Doe',
            'guest_email' => 'john@motac.gov.my',
            'subject' => 'Test Issue',
            'status' => 'open',
            'user_id' => null, // Guest submission
        ]);
    }

    /** @test */
    public function it_generates_unique_ticket_numbers(): void
    {
        // Create first ticket
        Livewire::test(SubmitTicket::class)
            ->set('form.name', 'John Doe')
            ->set('form.email', 'john@motac.gov.my')
            ->set('form.phone', '+60123456789')
            ->set('form.division_id', 1)
            ->set('form.category_id', 1)
            ->set('form.subject', 'First Issue')
            ->set('form.description', 'First test description')
            ->call('submitTicket');

        $firstTicket = HelpdeskTicket::first();

        // Create second ticket
        Livewire::test(SubmitTicket::class)
            ->set('form.name', 'Jane Doe')
            ->set('form.email', 'jane@motac.gov.my')
            ->set('form.phone', '+60123456788')
            ->set('form.division_id', 1)
            ->set('form.category_id', 1)
            ->set('form.subject', 'Second Issue')
            ->set('form.description', 'Second test description')
            ->call('submitTicket');

        $secondTicket = HelpdeskTicket::latest()->first();

        // Verify ticket numbers are unique and sequential
        $this->assertNotEquals($firstTicket->ticket_number, $secondTicket->ticket_number);
        $this->assertEquals(
            (int) substr($firstTicket->ticket_number, -6) + 1,
            (int) substr($secondTicket->ticket_number, -6)
        );
    }

    /** @test */
    public function it_clears_form_successfully(): void
    {
        Livewire::test(SubmitTicket::class)
            ->set('form.name', 'John Doe')
            ->set('form.email', 'john@motac.gov.my')
            ->set('form.phone', '+60123456789')
            ->call('clearForm')
            ->assertSet('form.name', '')
            ->assertSet('form.email', '')
            ->assertSet('form.phone', '')
            ->assertSet('submitted', false)
            ->assertSet('ticketNumber', null);
    }

    /** @test */
    public function it_displays_success_message_after_submission(): void
    {
        Livewire::test(SubmitTicket::class)
            ->set('form.name', 'John Doe')
            ->set('form.email', 'john@motac.gov.my')
            ->set('form.phone', '+60123456789')
            ->set('form.division_id', 1)
            ->set('form.category_id', 1)
            ->set('form.subject', 'Test Issue')
            ->set('form.description', 'Test description with sufficient length')
            ->call('submitTicket')
            ->assertSee(__('helpdesk.ticket_submitted'))
            ->assertSee(__('helpdesk.ticket_number'))
            ->assertSee(__('helpdesk.confirmation_email'));
    }

    /** @test */
    public function it_uses_computed_properties_for_performance(): void
    {
        $component = Livewire::test(SubmitTicket::class);

        // Verify computed properties are used
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $component->divisions);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $component->categories);
    }

    /** @test */
    public function it_supports_bilingual_validation_messages_english(): void
    {
        app()->setLocale('en');

        Livewire::test(SubmitTicket::class)
            ->call('submitTicket')
            ->assertSee('Full name is required')
            ->assertSee('Email address is required');
    }

    /** @test */
    public function it_supports_bilingual_validation_messages_malay(): void
    {
        app()->setLocale('ms');

        Livewire::test(SubmitTicket::class)
            ->call('submitTicket')
            ->assertSee('Nama penuh diperlukan')
            ->assertSee('Alamat e-mel diperlukan');
    }
}
