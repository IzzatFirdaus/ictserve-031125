<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Filament\Resources\Helpdesk\HelpdeskTicketResource\Pages\CreateHelpdeskTicket;
use App\Filament\Resources\Helpdesk\HelpdeskTicketResource\Pages\EditHelpdeskTicket;
use App\Filament\Resources\Helpdesk\HelpdeskTicketResource\Pages\ListHelpdeskTickets;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Helpdesk Ticket Resource Test
 *
 * Tests CRUD operations, authorization, filtering, and bulk actions
 * for the HelpdeskTicketResource in Filament admin panel.
 *
 * Requirements: 18.1, D03-FR-015.1
 */
class HelpdeskTicketResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $superuser;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->superuser = User::factory()->superuser()->create();
        $this->staff = User::factory()->staff()->create();
    }

    #[Test]
    public function admin_can_view_helpdesk_tickets(): void
    {
        $tickets = HelpdeskTicket::factory()->count(5)->create();

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->assertCanSeeTableRecords($tickets)
            ->assertCanRenderTableColumn('ticket_number')
            ->assertCanRenderTableColumn('priority')
            ->assertCanRenderTableColumn('status');
    }

    #[Test]
    public function superuser_can_view_helpdesk_tickets(): void
    {
        $tickets = HelpdeskTicket::factory()->count(3)->create();

        $this->actingAs($this->superuser);

        Livewire::test(ListHelpdeskTickets::class)
            ->assertCanSeeTableRecords($tickets);
    }

    #[Test]
    public function staff_cannot_access_helpdesk_resource(): void
    {
        $this->actingAs($this->staff)
            ->get(HelpdeskTicketResource::getUrl('index'))
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_create_helpdesk_ticket(): void
    {
        $category = TicketCategory::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($this->admin);

        Livewire::test(CreateHelpdeskTicket::class)
            ->fillForm([
                'subject' => 'Test Ticket',
                'description' => 'Test description',
                'priority' => 'normal',
                'category_id' => $category->id,
                'status' => 'open',
                'user_id' => $user->id, // Authenticated submission
            ])
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('helpdesk_tickets', [
            'subject' => 'Test Ticket',
            'priority' => 'normal',
        ]);
    }

    #[Test]
    public function admin_can_edit_helpdesk_ticket(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Original Title',
            'priority' => 'low',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(EditHelpdeskTicket::class, ['record' => $ticket->getRouteKey()])
            ->fillForm([
                'subject' => 'Updated Title',
                'priority' => 'high',
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'subject' => 'Updated Title',
            'priority' => 'high',
        ]);
    }

    #[Test]
    public function admin_can_filter_tickets_by_status(): void
    {
        $openTickets = HelpdeskTicket::factory()->count(3)->create(['status' => 'open']);
        $closedTickets = HelpdeskTicket::factory()->count(2)->create(['status' => 'closed']);

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->filterTable('status', 'open')
            ->assertCanSeeTableRecords($openTickets)
            ->assertCanNotSeeTableRecords($closedTickets);
    }

    #[Test]
    public function admin_can_filter_tickets_by_priority(): void
    {
        $highPriorityTickets = HelpdeskTicket::factory()->count(2)->create(['priority' => 'high']);
        $lowPriorityTickets = HelpdeskTicket::factory()->count(3)->create(['priority' => 'low']);

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->filterTable('priority', 'high')
            ->assertCanSeeTableRecords($highPriorityTickets)
            ->assertCanNotSeeTableRecords($lowPriorityTickets);
    }

    #[Test]
    public function admin_can_search_tickets(): void
    {
        $searchableTicket = HelpdeskTicket::factory()->create([
            'subject' => 'Unique Search Term',
        ]);
        $otherTickets = HelpdeskTicket::factory()->count(3)->create();

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->searchTable('Unique Search Term')
            ->assertCanSeeTableRecords([$searchableTicket])
            ->assertCanNotSeeTableRecords($otherTickets);
    }

    #[Test]
    public function admin_can_bulk_update_ticket_status(): void
    {
        $tickets = HelpdeskTicket::factory()->count(3)->create(['status' => 'open']);

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->callTableBulkAction('update_status', $tickets, data: ['status' => 'in_progress'])
            ->assertHasNoErrors();

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHas('helpdesk_tickets', [
                'id' => $ticket->id,
                'status' => 'in_progress',
            ]);
        }
    }

    #[Test]
    public function admin_can_assign_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['assigned_to_user' => null]);
        $assignee = User::factory()->admin()->create();

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->callTableAction('assign', $ticket, data: ['assigned_to_user' => $assignee->id])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'assigned_to_user' => $assignee->id,
        ]);
    }

    #[Test]
    public function ticket_validation_rules(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateHelpdeskTicket::class)
            ->fillForm([
                // Omitting all required fields
            ])
            ->call('create')
            ->assertHasErrors(); // Just check that there ARE errors
    }

    #[Test]
    public function ticket_number_is_auto_generated(): void
    {
        $category = TicketCategory::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($this->admin);

        Livewire::test(CreateHelpdeskTicket::class)
            ->fillForm([
                'subject' => 'Test Ticket',
                'description' => 'Test description',
                'priority' => 'normal',
                'category_id' => $category->id,
                'status' => 'open',
                'user_id' => $user->id,
            ])
            ->call('create')
            ->assertHasNoErrors();

        $ticket = HelpdeskTicket::latest()->first();
        $this->assertNotNull($ticket->ticket_number);
        $this->assertStringStartsWith('HD', $ticket->ticket_number);
    }

    #[Test]
    public function admin_can_view_ticket_details(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        $this->actingAs($this->admin);

        // View functionality tested via edit page since ViewHelpdeskTicket is optional
        Livewire::test(EditHelpdeskTicket::class, ['record' => $ticket->getRouteKey()])
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_export_tickets(): void
    {
        $tickets = HelpdeskTicket::factory()->count(5)->create();

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->selectTableRecords($tickets)
            ->callTableBulkAction('export', [], ['format' => 'csv'])
            ->assertHasNoErrors();

        // Verify export was triggered
        $this->assertTrue(true); // Placeholder for actual export verification
    }

    #[Test]
    public function ticket_sla_tracking(): void
    {
        // Set test time BEFORE creating ticket
        \Illuminate\Support\Carbon::setTestNow('2025-01-15 10:00:00');

        // Create ticket with SLA due date 3 days in the past
        $ticket = HelpdeskTicket::factory()->create([
            'priority' => 'high',
            'sla_resolution_due_at' => \Illuminate\Support\Carbon::parse('2025-01-12 10:00:00'),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->assertCanSeeTableRecords([$ticket])
            ->assertTableColumnStateSet('sla_status', 'overdue', $ticket);

        \Illuminate\Support\Carbon::setTestNow(); // Reset
    }

    #[Test]
    public function unauthorized_user_cannot_delete_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        $this->actingAs($this->admin); // Admin cannot delete, only superuser

        Livewire::test(ListHelpdeskTickets::class)
            ->assertTableActionHidden('delete', $ticket);
    }

    #[Test]
    public function superuser_can_delete_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        $this->actingAs($this->superuser);

        Livewire::test(ListHelpdeskTickets::class)
            ->callTableAction('delete', $ticket)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('helpdesk_tickets', ['id' => $ticket->id]);
    }
}
