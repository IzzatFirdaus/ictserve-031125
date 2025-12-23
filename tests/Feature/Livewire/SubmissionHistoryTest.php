<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Staff\SubmissionHistory;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubmissionHistoryTest extends TestCase
{
    use RefreshDatabase;

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

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->assertSee(__('portal.history_title')) // 'Sejarah permohonan'
            ->assertSee(__('portal.history_subtitle')) // 'Lihat dan urus semua permohonan helpdesk dan pinjaman aset anda.'
            ->assertSee(__('portal.history_helpdesk_tab')) // 'Tiket helpdesk'
            ->assertSee(__('portal.history_loans_tab')); // 'Pinjaman aset'
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

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->set('search', 'Unique Ticket Subject')
            ->assertSee('Unique Ticket Subject')
            ->assertSee('T-12345');
    }

    #[Test]
    public function displays_bahasa_melayu_search_placeholder(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->assertSeeHtml('placeholder="'.__('portal.search_placeholder_helpdesk').'"'); // 'Cari nombor tiket, subjek atau penerangan...'
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
            ->call('switchTab', 'loans')
            ->assertSet('activeTab', 'loans');
    }

    #[Test]
    public function displays_empty_state_with_bahasa_melayu_content(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->assertSee(__('portal.no_submissions_found')) // 'Tiada permohonan dijumpai'
            ->assertSee(__('portal.no_submissions_yet')); // 'Anda belum membuat sebarang permohonan.'
    }

    #[Test]
    public function can_filter_loans_with_bahasa_melayu_placeholders(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->call('switchTab', 'loans')
            ->assertSeeHtml('placeholder="'.__('portal.search_placeholder_loans').'"'); // 'Cari nombor permohonan atau tujuan...'
    }

    #[Test]
    public function displays_bahasa_melayu_loan_status_options(): void
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->call('switchTab', 'loans');

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
