<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Status;

use App\Contracts\TokenServiceInterface;
use App\Livewire\Status\StatusChecker;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * StatusChecker Component Tests
 *
 * Tests for the token-based status lookup component supporting
 * both helpdesk tickets and loan applications.
 *
 * @see App\Livewire\Status\StatusChecker
 *
 * @requirements 2.1, 2.2
 */
class StatusCheckerTest extends TestCase
{
    use RefreshDatabase;

    protected TokenServiceInterface $tokenService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenService = app(TokenServiceInterface::class);
    }

    /**
     * Test that the status checker page loads correctly.
     */
    #[Test]
    public function status_checker_page_loads(): void
    {
        $response = $this->get(route('status.check'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(StatusChecker::class);
    }

    /**
     * Test that a valid ticket token returns the ticket details.
     *
     * @requirements 2.1
     */
    #[Test]
    public function valid_ticket_token_returns_ticket_details(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Test Ticket Subject',
            'status' => 'open',
        ]);

        // Generate status token
        $token = $this->tokenService->generateStatusToken($ticket);

        Livewire::test(StatusChecker::class)
            ->set('token', $token)
            ->set('type', 'ticket')
            ->call('checkStatus')
            ->assertSet('showResults', true)
            ->assertSet('notFound', false)
            ->assertSet('foundType', 'ticket');
    }

    /**
     * Test that a valid loan token returns the loan details.
     *
     * @requirements 2.1
     */
    #[Test]
    public function valid_loan_token_returns_loan_details(): void
    {
        $loan = LoanApplication::factory()->create([
            'applicant_name' => 'Test Applicant',
        ]);

        // Generate status token
        $token = $this->tokenService->generateStatusToken($loan);

        Livewire::test(StatusChecker::class)
            ->set('token', $token)
            ->set('type', 'loan')
            ->call('checkStatus')
            ->assertSet('showResults', true)
            ->assertSet('notFound', false)
            ->assertSet('foundType', 'loan');
    }

    /**
     * Test that an invalid token shows error message.
     *
     * @requirements 2.2
     */
    #[Test]
    public function invalid_token_shows_error_message(): void
    {
        $invalidToken = str_repeat('a', 64); // 64 character invalid token

        Livewire::test(StatusChecker::class)
            ->set('token', $invalidToken)
            ->call('checkStatus')
            ->assertSet('notFound', true)
            ->assertSet('showResults', false);
    }

    /**
     * Test that auto-detect finds ticket when type is auto.
     *
     * @requirements 2.1
     */
    #[Test]
    public function auto_detect_finds_ticket(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $token = $this->tokenService->generateStatusToken($ticket);

        Livewire::test(StatusChecker::class)
            ->set('token', $token)
            ->set('type', 'auto')
            ->call('checkStatus')
            ->assertSet('showResults', true)
            ->assertSet('foundType', 'ticket');
    }

    /**
     * Test that auto-detect finds loan when type is auto.
     *
     * @requirements 2.1
     */
    #[Test]
    public function auto_detect_finds_loan(): void
    {
        $loan = LoanApplication::factory()->create();
        $token = $this->tokenService->generateStatusToken($loan);

        Livewire::test(StatusChecker::class)
            ->set('token', $token)
            ->set('type', 'auto')
            ->call('checkStatus')
            ->assertSet('showResults', true)
            ->assertSet('foundType', 'loan');
    }

    /**
     * Test that clear search resets the form.
     */
    #[Test]
    public function clear_search_resets_form(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $token = $this->tokenService->generateStatusToken($ticket);

        Livewire::test(StatusChecker::class)
            ->set('token', $token)
            ->call('checkStatus')
            ->assertSet('showResults', true)
            ->call('clearSearch')
            ->assertSet('token', '')
            ->assertSet('showResults', false)
            ->assertSet('submission', null);
    }

    /**
     * Test that token validation requires minimum length.
     */
    #[Test]
    public function token_validation_requires_minimum_length(): void
    {
        Livewire::test(StatusChecker::class)
            ->set('token', 'short')
            ->call('checkStatus')
            ->assertHasErrors(['token']);
    }

    /**
     * Test that token is required.
     */
    #[Test]
    public function token_is_required(): void
    {
        Livewire::test(StatusChecker::class)
            ->set('token', '')
            ->call('checkStatus')
            ->assertHasErrors(['token' => 'required']);
    }

    /**
     * Test that timeline is built correctly for ticket.
     */
    #[Test]
    public function timeline_is_built_for_ticket(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'in_progress',
            'assigned_at' => now()->subHours(2),
            'responded_at' => now()->subHour(),
        ]);
        $token = $this->tokenService->generateStatusToken($ticket);

        $component = Livewire::test(StatusChecker::class)
            ->set('token', $token)
            ->call('checkStatus');

        $timeline = $component->get('timeline');
        $this->assertIsArray($timeline);
        $this->assertNotEmpty($timeline);
    }

    /**
     * Test that timeline is built correctly for loan.
     */
    #[Test]
    public function timeline_is_built_for_loan(): void
    {
        $loan = LoanApplication::factory()->create();
        $token = $this->tokenService->generateStatusToken($loan);

        $component = Livewire::test(StatusChecker::class)
            ->set('token', $token)
            ->call('checkStatus');

        $timeline = $component->get('timeline');
        $this->assertIsArray($timeline);
        $this->assertNotEmpty($timeline);
    }

    /**
     * Test that status checker can be accessed via URL with token.
     */
    #[Test]
    public function status_checker_with_token_in_url(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $token = $this->tokenService->generateStatusToken($ticket);

        $response = $this->get(route('status.check.token', ['token' => $token]));

        $response->assertStatus(200);
        $response->assertSeeLivewire(StatusChecker::class);
    }

    /**
     * Test bilingual error messages are available.
     *
     * @requirements 2.2
     */
    #[Test]
    public function bilingual_error_messages_available(): void
    {
        // Test English
        app()->setLocale('en');
        $this->assertNotEmpty(__('status.not_found_title'));
        $this->assertNotEmpty(__('status.not_found_message'));

        // Test Malay
        app()->setLocale('ms');
        $this->assertNotEmpty(__('status.not_found_title'));
        $this->assertNotEmpty(__('status.not_found_message'));
    }
}
