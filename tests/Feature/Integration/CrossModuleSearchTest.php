<?php

declare(strict_types=1);

namespace Tests\Feature\Integration;

use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Cross-Module Search Integration Test
 *
 * Tests unified search across helpdesk tickets and loan applications.
 *
 * Requirements: R11 (Cross-Module Integration), 5.2.1-5.2.5
 */
class CrossModuleSearchTest extends TestCase
{
	use RefreshDatabase;

	private User $user;
	private User $adminUser;
	private Division $division;
	private TicketCategory $category;

	protected function setUp(): void
	{
		parent::setUp();

		$this->user = User::factory()->create([
			'name' => 'Test User',
			'email' => 'testuser@motac.gov.my',
		]);

		$this->adminUser = User::factory()->create([
			'name' => 'Admin User',
			'email' => 'admin@motac.gov.my',
		]);

		$this->division = Division::factory()->create([
			'name' => 'ICT Division',
		]);

		$this->category = TicketCategory::factory()->create([
			'name' => 'Technical Support',
		]);
	}

	#[Test]
	public function can_search_tickets_by_subject(): void
	{
		HelpdeskTicket::factory()->create([
			'subject' => 'Laptop screen broken',
			'user_id' => $this->user->id,
			'category_id' => $this->category->id,
		]);

		HelpdeskTicket::factory()->create([
			'subject' => 'Network connectivity issue',
			'user_id' => $this->user->id,
			'category_id' => $this->category->id,
		]);

		$results = HelpdeskTicket::where('subject', 'like', '%laptop%')->get();

		$this->assertCount(1, $results);
		$this->assertStringContainsString('Laptop', $results->first()->subject);
	}

	#[Test]
	public function can_search_loan_applications_by_purpose(): void
	{
		LoanApplication::factory()->create([
			'user_id' => $this->user->id,
			'division_id' => $this->division->id,
			'purpose' => 'Conference presentation equipment',
		]);

		LoanApplication::factory()->create([
			'user_id' => $this->user->id,
			'division_id' => $this->division->id,
			'purpose' => 'Training session materials',
		]);

		$results = LoanApplication::where('purpose', 'like', '%conference%')->get();

		$this->assertCount(1, $results);
		$this->assertStringContainsString('Conference', $results->first()->purpose);
	}

	#[Test]
	public function can_filter_tickets_by_status(): void
	{
		HelpdeskTicket::factory()->create([
			'status' => 'open',
			'user_id' => $this->user->id,
			'category_id' => $this->category->id,
		]);

		HelpdeskTicket::factory()->create([
			'status' => 'closed',
			'user_id' => $this->user->id,
			'category_id' => $this->category->id,
		]);

		HelpdeskTicket::factory()->create([
			'status' => 'open',
			'user_id' => $this->user->id,
			'category_id' => $this->category->id,
		]);

		$openTickets = HelpdeskTicket::where('status', 'open')->get();
		$closedTickets = HelpdeskTicket::where('status', 'closed')->get();

		$this->assertCount(2, $openTickets);
		$this->assertCount(1, $closedTickets);
	}

	#[Test]
	public function can_filter_loan_applications_by_date_range(): void
	{
		LoanApplication::factory()->create([
			'user_id' => $this->user->id,
			'division_id' => $this->division->id,
			'created_at' => now()->subDays(5),
		]);

		LoanApplication::factory()->create([
			'user_id' => $this->user->id,
			'division_id' => $this->division->id,
			'created_at' => now()->subDays(15),
		]);

		$recentLoans = LoanApplication::where('created_at', '>=', now()->subDays(7))->get();

		$this->assertCount(1, $recentLoans);
	}

	#[Test]
	public function can_filter_by_user(): void
	{
		$otherUser = User::factory()->create();

		HelpdeskTicket::factory()->count(3)->create([
			'user_id' => $this->user->id,
			'category_id' => $this->category->id,
		]);

		HelpdeskTicket::factory()->count(2)->create([
			'user_id' => $otherUser->id,
			'category_id' => $this->category->id,
		]);

		$userTickets = HelpdeskTicket::where('user_id', $this->user->id)->get();

		$this->assertCount(3, $userTickets);
	}

	#[Test]
	public function search_results_include_related_data(): void
	{
		$ticket = HelpdeskTicket::factory()->create([
			'user_id' => $this->user->id,
			'category_id' => $this->category->id,
		]);

		$ticketWithRelations = HelpdeskTicket::with(['user', 'category'])
			->find($ticket->id);

		$this->assertNotNull($ticketWithRelations->user);
		$this->assertNotNull($ticketWithRelations->category);
		$this->assertEquals($this->user->id, $ticketWithRelations->user->id);
	}

	#[Test]
	public function can_combine_multiple_filters(): void
	{
		HelpdeskTicket::factory()->create([
			'subject' => 'Laptop issue',
			'status' => 'open',
			'priority' => 'high',
			'user_id' => $this->user->id,
			'category_id' => $this->category->id,
		]);

		HelpdeskTicket::factory()->create([
			'subject' => 'Laptop problem',
			'status' => 'closed',
			'priority' => 'high',
			'user_id' => $this->user->id,
			'category_id' => $this->category->id,
		]);

		HelpdeskTicket::factory()->create([
			'subject' => 'Network issue',
			'status' => 'open',
			'priority' => 'low',
			'user_id' => $this->user->id,
			'category_id' => $this->category->id,
		]);

		$results = HelpdeskTicket::query()
			->where('subject', 'like', '%laptop%')
			->where('status', 'open')
			->where('priority', 'high')
			->get();

		$this->assertCount(1, $results);
	}

	#[Test]
	public function search_is_case_insensitive(): void
	{
		HelpdeskTicket::factory()->create([
			'subject' => 'LAPTOP SCREEN BROKEN',
			'user_id' => $this->user->id,
			'category_id' => $this->category->id,
		]);

		$results = HelpdeskTicket::whereRaw('LOWER(subject) LIKE ?', ['%laptop%'])->get();

		$this->assertCount(1, $results);
	}
}
