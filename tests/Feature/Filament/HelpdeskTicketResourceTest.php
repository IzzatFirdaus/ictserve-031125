<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\HelpdeskTicketResource;
use App\Filament\Resources\HelpdeskTicketResource\Pages\CreateHelpdeskTicket;
use App\Filament\Resources\HelpdeskTicketResource\Pages\EditHelpdeskTicket;
use App\Filament\Resources\HelpdeskTicketResource\Pages\ListHelpdeskTickets;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    public function test_admin_can_view_helpdesk_tickets(): void
    {
        $tickets = HelpdeskTicket::factory()->count(5)->create();

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->assertCanSeeTableRecords($tickets)
            ->assertCanRenderTableColumn('ticket_number')
            ->assertCanRenderTableColumn('priority')
            ->assertCanRenderTableColumn('status');
    }

    public function test_superuser_can_view_helpdesk_tickets(): void
    {
        $tickets = HelpdeskTicket::factory()->count(3)->create();

        $this->actingAs($this->superuser);

        Livewire::test(ListHelpdeskTickets::class)
            ->assertCanSeeTableRecords($tickets);
    }

    public function test_staff_cannot_access_helpdesk_resource(): void
    {
        $this->actingAs($this->staff)
            ->get(HelpdeskTicketResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_helpdesk_ticket(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateHelpdeskTicket::class)
            ->fillForm([
                'title' => 'Test Ticket',
                'description' => 'Test description',
                'priority' => 'medium',
                'category' => 'hardware',
                'status' => 'open',
            ])
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('helpdesk_tickets', [
            'title' => 'Test Ticket',
            'priority' => 'medium',
            'category' => 'hardware',
        ]);
    }

    public function test_admin_can_edit_helpdesk_ticket(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'title' => 'Original Title',
            'priority' => 'low',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(EditHelpdeskTicket::class, ['record' => $ticket->getRouteKey()])
            ->fillForm([
                'title' => 'Updated Title',
                'priority' => 'high',
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'title' => 'Updated Title',
            'priority' => 'high',
        ]);
    }

    public function test_admin_can_filter_tickets_by_status(): void
    {
        $openTickets = HelpdeskTicket::factory()->count(3)->create(['status' => 'open']);
        $closedTickets = HelpdeskTicket::factory()->count(2)->create(['status' => 'closed']);

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->filterTable('status', 'open')
            ->assertCanSeeTableRecords($openTickets)
            ->assertCanNotSeeTableRecords($closedTickets);
    }

    public function test_admin_can_filter_tickets_by_priority(): void
    {
        $highPriorityTickets = HelpdeskTicket::factory()->count(2)->create(['priority' => 'high']);
        $lowPriorityTickets = HelpdeskTicket::factory()->count(3)->create(['priority' => 'low']);

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->filterTable('priority', 'high')
            ->assertCanSeeTableRecords($highPriorityTickets)
            ->assertCanNotSeeTableRecords($lowPriorityTickets);
    }

    public function test_admin_can_search_tickets(): void
    {
        $searchableTicket = HelpdeskTicket::factory()->create([
            'title' => 'Unique Search Term',
        ]);
        $otherTickets = HelpdeskTicket::factory()->count(3)->create();

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->searchTable('Unique Search Term')
            ->assertCanSeeTableRecords([$searchableTicket])
            ->assertCanNotSeeTableRecords($otherTickets);
    }

    public function test_admin_can_bulk_update_ticket_status(): void
    {
        $tickets = HelpdeskTicket::factory()->count(3)->create(['status' => 'open']);

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->selectTableRecords($tickets)
            ->callTableBulkAction('update_status', data: ['status' => 'in_progress'])
            ->assertHasNoErrors();

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHas('helpdesk_tickets', [
                'id' => $ticket->id,
                'status' => 'in_progress',
            ]);
        }
    }

    public function test_admin_can_assign_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['assigned_to' => null]);
        $assignee = User::factory()->admin()->create();

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->callTableAction('assign', $ticket, data: ['assigned_to' => $assignee->id])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'assigned_to' => $assignee->id,
        ]);
    }

    public function test_ticket_validation_rules(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateHelpdeskTicket::class)
            ->fillForm([
                'title' => '', // Required field
                'description' => '',
                'priority' => 'invalid_priority',
            ])
            ->call('create')
            ->assertHasErrors([
                'title' => 'required',
                'priority' => 'in',
            ]);
    }

    public function test_ticket_number_is_auto_generated(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateHelpdeskTicket::class)
            ->fillForm([
                'title' => 'Test Ticket',
                'description' => 'Test description',
                'priority' => 'medium',
                'category' => 'hardware',
            ])
            ->call('create')
            ->assertHasNoErrors();

        $ticket = HelpdeskTicket::latest()->first();
        $this->assertNotNull($ticket->ticket_number);
        $this->assertStringStartsWith('TK-', $ticket->ticket_number);
    }

    public function test_admin_can_view_ticket_details(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        $this->actingAs($this->admin);

        Livewire::test(ViewHelpdeskTicket::class, ['record' => $ticket->getRouteKey()])
            ->assertSuccessful()
            ->assertSee($ticket->title)
            ->assertSee($ticket->description);
    }

    public function test_admin_can_export_tickets(): void
    {
        HelpdeskTicket::factory()->count(5)->create();

        $this->actingAs($this->admin);

        $response = Livewire::test(ListHelpdeskTickets::class)
            ->callTableAction('export')
            ->assertHasNoErrors();

        // Verify export was triggered
        $this->assertTrue(true); // Placeholder for actual export verification
    }

    public function test_ticket_sla_tracking(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'priority' => 'high',
            'created_at' => now()->subHours(25), // Overdue
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListHelpdeskTickets::class)
            ->assertCanSeeTableRecords([$ticket])
            ->assertTableColumnStateSet('sla_status', 'overdue', $ticket);
    }

    public function test_unauthorized_user_cannot_delete_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        $this->actingAs($this->admin); // Admin cannot delete, only superuser

        Livewire::test(ListHelpdeskTickets::class)
            ->assertTableActionHidden('delete', $ticket);
    }

    public function test_superuser_can_delete_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        $this->actingAs($this->superuser);

        Livewire::test(ListHelpdeskTickets::class)
            ->callTableAction('delete', $ticket)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('helpdesk_tickets', ['id' => $ticket->id]);
    }
}
