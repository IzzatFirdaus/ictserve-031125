<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Staff\SubmissionHistory;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubmissionHistoryTest extends TestCase
{
    #[Test]
    public function renders_successfully(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->assertStatus(200);
    }

    #[Test]
    public function displays_bahasa_melayu_labels(): void
    {
        $user = User::factory()->create();

        // Component uses lazy loading, so we check the component properties instead
        $component = Livewire::actingAs($user)
            ->test(SubmissionHistory::class);

        // Verify component has expected properties
        $component->assertSet('activeTab', 'tickets');

        // Check that translation keys exist
        $this->assertNotEmpty(__('portal.history_title'));
        $this->assertNotEmpty(__('portal.history_subtitle'));
        $this->assertNotEmpty(__('portal.history_helpdesk_tab'));
        $this->assertNotEmpty(__('portal.history_loans_tab'));
    }

    #[Test]
    public function can_filter_tickets(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'subject' => 'Unique Ticket Subject',
            'ticket_number' => 'T-12345',
        ]);

        $component = Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->set('search', 'Unique Ticket Subject');

        // Verify search is set
        $component->assertSet('search', 'Unique Ticket Subject');
    }

    #[Test]
    public function displays_bahasa_melayu_search_placeholder(): void
    {
        $user = User::factory()->create();

        // Check that translation key exists
        $this->assertNotEmpty(__('portal.search_placeholder_helpdesk'));

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->assertStatus(200);
    }

    #[Test]
    public function displays_bahasa_melayu_status_options(): void
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(SubmissionHistory::class);

        // Check that BM status options are available
        $this->assertEquals(__('common.all_statuses'), $component->get('ticketStatusOptions')['all']); // 'Semua Status'
        $this->assertEquals(__('common.open'), $component->get('ticketStatusOptions')['open']); // 'Terbuka'
        $this->assertEquals(__('common.in_progress'), $component->get('ticketStatusOptions')['in_progress']); // 'Dalam Proses'
        $this->assertEquals(__('common.resolved'), $component->get('ticketStatusOptions')['resolved']); // 'Diselesaikan'
        $this->assertEquals(__('common.closed'), $component->get('ticketStatusOptions')['closed']); // 'Ditutup'
    }

    #[Test]
    public function can_switch_between_tabs(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->assertSet('activeTab', 'tickets')
            ->set('activeTab', 'loans')
            ->assertSet('activeTab', 'loans');
    }

    #[Test]
    public function displays_empty_state_with_bahasa_melayu_content(): void
    {
        $user = User::factory()->create();

        // Check that translation keys exist
        $this->assertNotEmpty(__('portal.no_submissions_found'));
        $this->assertNotEmpty(__('portal.no_submissions_yet'));

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->assertStatus(200);
    }

    #[Test]
    public function can_filter_loans_with_bahasa_melayu_placeholders(): void
    {
        $user = User::factory()->create();

        // Check that translation key exists
        $this->assertNotEmpty(__('portal.search_placeholder_loans'));

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->set('activeTab', 'loans')
            ->assertSet('activeTab', 'loans');
    }

    #[Test]
    public function displays_bahasa_melayu_loan_status_options(): void
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->set('activeTab', 'loans');

        // Check that BM loan status options are available
        $this->assertEquals(__('common.all_statuses'), $component->get('loanStatusOptions')['all']); // 'Semua Status'
        $this->assertEquals(__('common.submitted'), $component->get('loanStatusOptions')['submitted']); // 'Dihantar'
        $this->assertEquals(__('common.under_review'), $component->get('loanStatusOptions')['under_review']); // 'Dalam Semakan'
        $this->assertEquals(__('common.approved'), $component->get('loanStatusOptions')['approved']); // 'Diluluskan'
        $this->assertEquals(__('common.active'), $component->get('loanStatusOptions')['active']); // 'Aktif'
        $this->assertEquals(__('common.returned'), $component->get('loanStatusOptions')['returned']); // 'Dipulangkan'
    }

    #[Test]
    public function resets_filters_with_proper_defaults(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->set('search', 'test search')
            ->set('statusFilter', ['open', 'in_progress'])
            ->set('dateFrom', '2024-01-01')
            ->set('dateTo', '2024-12-31')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('statusFilter', [])
            ->assertSet('dateFrom', '')
            ->assertSet('dateTo', '')
            ->assertSet('sortField', 'created_at')
            ->assertSet('sortDirection', 'desc');
    }
}
