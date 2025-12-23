<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Filament\Resources\Helpdesk\Pages\CreateHelpdeskTicket;
use App\Filament\Resources\Helpdesk\Pages\EditHelpdeskTicket;
use App\Filament\Resources\Helpdesk\Pages\ListHelpdeskTickets;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Helpdesk Ticket Resource Test v3.6.0
 *
 * Tests comprehensive helpdesk ticket management functionality including
 * CRUD operations, status transitions, SLA tracking, and Bahasa Melayu content.
 *
 * @see D03 Requirements 2.2, 2.5, 3.3, 4.2, 4.3, 13.1-13.5, 22.3
 * @see D04 Software Design - Filament Resources
 * @see D12 UI/UX Design Guide - WCAG 2.2 AA Compliance
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

        // Set application locale to Bahasa Melayu
        app()->setLocale('ms');

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
    public function can_create_helpdesk_ticket_with_livewire(): void
    {
        $category = TicketCategory::factory()->create();
        $division = Division::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(CreateHelpdeskTicket::class)
            ->fillForm([
                'user_id' => null,
                'subject' => 'Masalah Sistem Email',
                'category_id' => $category->id,
                'priority' => 'normal',
                'status' => 'open',
                'guest_name' => 'Ahmad bin Ali',
                'guest_email' => 'ahmad@motac.gov.my',
                'guest_phone' => '+60123456789',
                'guest_staff_id' => 'STAFF001',
                'division_id' => $division->id,
                'job_grade' => '41',
                'declaration_accepted' => true,
                'description' => 'Sistem email tidak dapat diakses sejak pagi ini. Perlu bantuan segera.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('helpdesk_tickets', [
            'subject' => 'Masalah Sistem Email',
            'priority' => 'normal',
            'guest_name' => 'Ahmad bin Ali',
        ]);
    }

    #[Test]
    public function can_validate_helpdesk_ticket_form(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreateHelpdeskTicket::class)
            ->fillForm([
                'subject' => '',
                'guest_email' => 'invalid-email',
                'description' => 'Too short',
            ])
            ->call('create')
            ->assertHasFormErrors([
                'subject' => 'required',
                'guest_email' => 'email',
                'description' => 'min',
            ]);
    }

    #[Test]
    public function can_edit_helpdesk_ticket_with_livewire(): void
    {
        $division = Division::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Original Title',
            'priority' => 'low',
            'division_id' => $division->id,
            'job_grade' => '41',
            'declaration_accepted' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditHelpdeskTicket::class, ['record' => $ticket->getRouteKey()])
            ->fillForm([
                'subject' => 'Tajuk Dikemaskini',
                'priority' => 'high',
                'division_id' => $division->id,
                'job_grade' => '42',
                'declaration_accepted' => true,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'subject' => 'Tajuk Dikemaskini',
            'priority' => 'high',
        ]);
    }

    #[Test]
    public function can_filter_tickets_by_status(): void
    {
        $openTickets = HelpdeskTicket::factory()->count(3)->create(['status' => 'open']);
        $closedTickets = HelpdeskTicket::factory()->count(2)->create(['status' => 'closed']);

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->filterTable('status', 'open')
            ->assertCanSeeTableRecords($openTickets)
            ->assertCanNotSeeTableRecords($closedTickets);
    }

    #[Test]
    public function can_filter_tickets_by_priority(): void
    {
        $highPriorityTickets = HelpdeskTicket::factory()->count(2)->create(['priority' => 'high']);
        $lowPriorityTickets = HelpdeskTicket::factory()->count(3)->create(['priority' => 'low']);

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->filterTable('priority', 'high')
            ->assertCanSeeTableRecords($highPriorityTickets)
            ->assertCanNotSeeTableRecords($lowPriorityTickets);
    }

    #[Test]
    public function can_search_tickets_by_subject(): void
    {
        $searchableTicket = HelpdeskTicket::factory()->create([
            'subject' => 'Masalah Sistem Unik',
        ]);
        $otherTickets = HelpdeskTicket::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->searchTable('Masalah Sistem Unik')
            ->assertCanSeeTableRecords([$searchableTicket])
            ->assertCanNotSeeTableRecords($otherTickets);
    }

    #[Test]
    public function can_update_ticket_status(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['status' => 'open']);

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->callTableAction('updateStatus', $ticket, data: [
                'status' => 'in_progress',
                'comment' => 'Sedang diproses oleh pasukan teknikal',
            ])
            ->assertNotified();

        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'status' => 'in_progress',
        ]);
    }

    #[Test]
    public function can_mark_ticket_as_resolved(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['status' => 'in_progress']);

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->callTableAction('markResolved', $ticket, data: [
                'resolution_notes' => 'Masalah telah diselesaikan dengan mengemaskini konfigurasi sistem',
            ])
            ->assertNotified();

        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'status' => 'resolved',
        ]);
    }

    #[Test]
    public function can_assign_ticket_to_user(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['assigned_to_user' => null]);
        $assignee = User::factory()->admin()->create();

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->callTableAction('assign', $ticket, data: [
                'assigned_to_user' => $assignee->id,
                'assignment_notes' => 'Diberikan kepada pakar sistem',
            ])
            ->assertNotified();

        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'assigned_to_user' => $assignee->id,
        ]);
    }

    #[Test]
    public function can_bulk_assign_tickets(): void
    {
        $tickets = HelpdeskTicket::factory()->count(3)->create(['assigned_to_user' => null]);
        $assignee = User::factory()->admin()->create();

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->callTableBulkAction('assign', $tickets, [
                'assigned_to_user' => $assignee->id,
                'assignment_notes' => 'Tugasan berkumpulan',
            ])
            ->assertNotified();

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHas('helpdesk_tickets', [
                'id' => $ticket->id,
                'assigned_to_user' => $assignee->id,
            ]);
        }
    }

    #[Test]
    public function can_bulk_update_ticket_status(): void
    {
        $tickets = HelpdeskTicket::factory()->count(3)->create(['status' => 'open']);

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->callTableBulkAction('update_status', $tickets, [
                'status' => 'in_progress',
                'comment' => 'Semua tiket sedang diproses',
            ])
            ->assertNotified();

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHas('helpdesk_tickets', [
                'id' => $ticket->id,
                'status' => 'in_progress',
            ]);
        }
    }

    #[Test]
    public function can_bulk_close_tickets(): void
    {
        $tickets = HelpdeskTicket::factory()->count(2)->create(['status' => 'resolved']);

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->callTableBulkAction('close', $tickets, [
                'resolution_notes' => 'Semua tiket telah diselesaikan',
            ])
            ->assertNotified();

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHas('helpdesk_tickets', [
                'id' => $ticket->id,
                'status' => 'closed',
            ]);
        }
    }

    #[Test]
    public function can_export_tickets(): void
    {
        $tickets = HelpdeskTicket::factory()->count(5)->create();

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->callTableBulkAction('export', $tickets, [
                'format' => 'csv',
            ])
            ->assertNotified();
    }

    #[Test]
    public function displays_bahasa_melayu_table_headers(): void
    {
        HelpdeskTicket::factory()->create();

        $response = Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class);

        $response->assertSee('No. Tiket'); // Ticket Number
        $response->assertSee('Subjek'); // Subject
        $response->assertSee('Status'); // Status
        $response->assertSee('Keutamaan'); // Priority
        $response->assertSee('Kategori'); // Category
    }

    #[Test]
    public function displays_ticket_status_options_in_bahasa_melayu(): void
    {
        HelpdeskTicket::factory()->create(['status' => 'open']);

        $response = Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class);

        $response->assertSee('Terbuka'); // Open status in BM
    }

    #[Test]
    public function displays_ticket_management_actions_in_bahasa_melayu(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['status' => 'open']);

        $response = Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class);

        $response->assertSee('Kemaskini Status'); // Update Status action
        $response->assertSee('Tandai Selesai'); // Mark Resolved action
        $response->assertSee('Tugaskan'); // Assign action
    }

    #[Test]
    public function displays_submission_type_indicators(): void
    {
        $guestTicket = HelpdeskTicket::factory()->create(['user_id' => null]);
        $authenticatedTicket = HelpdeskTicket::factory()->create(['user_id' => $this->admin->id]);

        $response = Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class);

        $response->assertSee('Permohonan Tetamu'); // Guest submission
        $response->assertSee('Permohonan Pengguna'); // Authenticated submission
    }

    #[Test]
    public function displays_sla_status_indicators(): void
    {
        // Create overdue ticket
        $overdueTicket = HelpdeskTicket::factory()->create([
            'priority' => 'high',
            'sla_resolution_due_at' => now()->subDays(2),
        ]);

        $response = Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class);

        $response->assertSee('Lewat Tempoh'); // Overdue SLA indicator
    }

    #[Test]
    public function ticket_number_is_auto_generated(): void
    {
        $category = TicketCategory::factory()->create();
        $division = Division::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(CreateHelpdeskTicket::class)
            ->fillForm([
                'user_id' => null,
                'subject' => 'Test Ticket',
                'category_id' => $category->id,
                'priority' => 'normal',
                'status' => 'open',
                'guest_name' => 'Test Guest',
                'guest_email' => 'guest@motac.gov.my',
                'guest_phone' => '+60123456789',
                'guest_staff_id' => 'STAFF001',
                'division_id' => $division->id,
                'job_grade' => '41',
                'declaration_accepted' => true,
                'description' => 'Test description with enough content',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $ticket = HelpdeskTicket::latest()->first();
        $this->assertNotNull($ticket->ticket_number);
        $this->assertStringStartsWith('HD', $ticket->ticket_number);
    }

    #[Test]
    public function can_sort_tickets_by_created_date(): void
    {
        $oldTicket = HelpdeskTicket::factory()->create(['created_at' => now()->subDays(2)]);
        $newTicket = HelpdeskTicket::factory()->create(['created_at' => now()]);

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->sortTable('created_at', 'desc')
            ->assertCanSeeTableRecords([$newTicket, $oldTicket], inOrder: true);
    }

    #[Test]
    public function can_use_pagination(): void
    {
        HelpdeskTicket::factory()->count(30)->create();

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->assertCanRenderTableColumn('ticket_number')
            ->assertCountTableRecords(25); // Default pagination
    }

    #[Test]
    public function superuser_can_delete_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        Livewire::actingAs($this->superuser)
            ->test(ListHelpdeskTickets::class)
            ->callTableAction('delete', $ticket)
            ->assertNotified();

        $this->assertSoftDeleted('helpdesk_tickets', ['id' => $ticket->id]);
    }

    #[Test]
    public function admin_cannot_delete_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class)
            ->assertTableActionHidden('delete', $ticket);
    }

    #[Test]
    public function displays_cross_module_asset_linkage(): void
    {
        // This would test asset linkage if the relationship exists
        $ticket = HelpdeskTicket::factory()->create();

        $response = Livewire::actingAs($this->admin)
            ->test(ListHelpdeskTickets::class);

        $response->assertCanRenderTableColumn('relatedAsset.name');
    }
}
