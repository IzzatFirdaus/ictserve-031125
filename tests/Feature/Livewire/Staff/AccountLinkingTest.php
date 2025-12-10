<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Staff;

use App\Livewire\Staff\AccountLinking;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Account Linking Livewire Component Tests
 *
 * Tests the AccountLinking Livewire component functionality including:
 * - Email search for unlinked submissions
 * - Display of matching submissions
 * - Linking submissions to user account
 * - Success/error feedback
 *
 * @see Requirements 18.1, 18.2, 18.3, 18.4, 18.5
 * @see D02 FR-050 Optional account linking
 */
class AccountLinkingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'test.user@motac.gov.my',
        ]);
    }

    /**
     * Test that the component renders correctly for authenticated users.
     *
     * @see Requirement 18.1
     */
    #[Test]
    public function component_renders_for_authenticated_user(): void
    {
        Livewire::actingAs($this->user)
            ->test(AccountLinking::class)
            ->assertStatus(200)
            ->assertSee(__('account_linking.title'))
            ->assertSee(__('account_linking.search_title'));
    }

    /**
     * Test that the component pre-fills user's email.
     *
     * @see Requirement 18.1
     */
    #[Test]
    public function component_prefills_user_email(): void
    {
        Livewire::actingAs($this->user)
            ->test(AccountLinking::class)
            ->assertSet('searchEmail', $this->user->email);
    }

    /**
     * Test searching for unlinked submissions by email.
     *
     * @see Requirement 18.2
     */
    #[Test]
    public function search_finds_unlinked_tickets(): void
    {
        // Create an unlinked ticket
        $ticket = HelpdeskTicket::factory()->create([
            'guest_email' => $this->user->email,
            'user_id' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(AccountLinking::class)
            ->set('searchEmail', $this->user->email)
            ->call('searchSubmissions')
            ->assertSet('hasSearched', true)
            ->assertSee($ticket->ticket_number ?? "HD-{$ticket->id}");
    }

    /**
     * Test searching for unlinked loan applications.
     *
     * @see Requirement 18.2
     */
    #[Test]
    public function search_finds_unlinked_loans(): void
    {
        // Create an unlinked loan
        $loan = LoanApplication::factory()->create([
            'applicant_email' => $this->user->email,
            'user_id' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(AccountLinking::class)
            ->set('searchEmail', $this->user->email)
            ->call('searchSubmissions')
            ->assertSet('hasSearched', true)
            ->assertSee($loan->application_number ?? "LA-{$loan->id}");
    }

    /**
     * Test that no results message is shown when no submissions found.
     *
     * @see Requirement 18.2
     */
    #[Test]
    public function shows_no_results_message_when_empty(): void
    {
        Livewire::actingAs($this->user)
            ->test(AccountLinking::class)
            ->set('searchEmail', 'nonexistent@motac.gov.my')
            ->call('searchSubmissions')
            ->assertSet('hasSearched', true)
            ->assertSee(__('account_linking.no_submissions_found'));
    }

    /**
     * Test email validation on search.
     *
     * @see Requirement 18.1
     */
    #[Test]
    public function validates_email_format(): void
    {
        Livewire::actingAs($this->user)
            ->test(AccountLinking::class)
            ->set('searchEmail', 'invalid-email')
            ->call('searchSubmissions')
            ->assertHasErrors(['searchEmail' => 'email']);
    }

    /**
     * Test selecting and deselecting submissions.
     *
     * @see Requirement 18.3
     */
    #[Test]
    public function can_toggle_submission_selection(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'guest_email' => $this->user->email,
            'user_id' => null,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AccountLinking::class)
            ->set('searchEmail', $this->user->email)
            ->call('searchSubmissions');

        // Select the submission
        $component->call('toggleSelection', 'ticket', $ticket->id)
            ->assertCount('selectedSubmissions', 1);

        // Deselect the submission
        $component->call('toggleSelection', 'ticket', $ticket->id)
            ->assertCount('selectedSubmissions', 0);
    }

    /**
     * Test select all functionality.
     *
     * @see Requirement 18.3
     */
    #[Test]
    public function can_select_all_submissions(): void
    {
        HelpdeskTicket::factory()->count(3)->create([
            'guest_email' => $this->user->email,
            'user_id' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(AccountLinking::class)
            ->set('searchEmail', $this->user->email)
            ->call('searchSubmissions')
            ->call('selectAll')
            ->assertCount('selectedSubmissions', 3);
    }

    /**
     * Test linking selected submissions to user account.
     *
     * @see Requirement 18.4
     */
    #[Test]
    public function can_link_submissions_to_account(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'guest_email' => $this->user->email,
            'user_id' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(AccountLinking::class)
            ->set('searchEmail', $this->user->email)
            ->call('searchSubmissions')
            ->call('toggleSelection', 'ticket', $ticket->id)
            ->call('linkSubmissions')
            ->assertSet('successMessage', trans_choice('account_linking.submissions_linked_success', 1, ['count' => 1]));

        // Verify the ticket is now linked
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test error message when no submissions selected.
     *
     * @see Requirement 18.4
     */
    #[Test]
    public function shows_error_when_no_submissions_selected(): void
    {
        Livewire::actingAs($this->user)
            ->test(AccountLinking::class)
            ->call('linkSubmissions')
            ->assertSet('errorMessage', __('account_linking.no_submissions_selected'));
    }

    /**
     * Test reset search functionality.
     *
     * @see Requirement 18.1
     */
    #[Test]
    public function can_reset_search(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'guest_email' => $this->user->email,
            'user_id' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(AccountLinking::class)
            ->set('searchEmail', 'other@motac.gov.my')
            ->call('searchSubmissions')
            ->call('toggleSelection', 'ticket', $ticket->id)
            ->call('resetSearch')
            ->assertSet('searchEmail', $this->user->email)
            ->assertSet('hasSearched', false)
            ->assertCount('selectedSubmissions', 0);
    }

    /**
     * Test linking statistics are displayed.
     *
     * @see Requirement 18.1
     */
    #[Test]
    public function displays_linking_statistics(): void
    {
        // Create linked and unlinked submissions
        HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
        ]);

        HelpdeskTicket::factory()->create([
            'guest_email' => $this->user->email,
            'user_id' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(AccountLinking::class)
            ->assertSee(__('account_linking.linked_tickets'))
            ->assertSee(__('account_linking.unlinked_tickets'));
    }

    /**
     * Test route is accessible at /dashboard/link-submissions.
     *
     * @see Requirement 18.1
     */
    #[Test]
    public function route_is_accessible(): void
    {
        $this->actingAs($this->user)
            ->get('/dashboard/link-submissions')
            ->assertStatus(200)
            ->assertSeeLivewire(AccountLinking::class);
    }

    /**
     * Test route requires authentication.
     *
     * @see Requirement 18.1
     */
    #[Test]
    public function route_requires_authentication(): void
    {
        $this->get('/dashboard/link-submissions')
            ->assertRedirect('/login');
    }
}
