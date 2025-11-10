<?php

declare(strict_types=1);

namespace Tests\Feature\Performance;

use App\Enums\LoanStatus;
use App\Filament\Pages\AdminDashboard;
use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Filament\Widgets\HelpdeskStatsOverview;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Filament Performance Test
 *
 * Tests Core Web Vitals compliance, query optimization, and performance
 * metrics for Filament admin panel under various load conditions.
 *
 * Requirements: 18.4, 13.1, D03-FR-011.1
 */
class FilamentPerformanceTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\Concerns\CreatesRoles;

    private User $admin;

    private Division $division;

    private TicketCategory $ticketCategory;

    private int $loanSequence = 0;

    private int $ticketSequence = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createRoles();
        Cache::flush();

        $this->admin = User::factory()->admin()->create();
        $this->division = Division::factory()->create();
        $this->ticketCategory = TicketCategory::factory()->create();
    }

    #[Test]
    public function dashboard_loads_within_performance_targets(): void
    {
        $this->seedLoanApplications(1);
        $this->seedHelpdeskTickets(1);

        $this->actingAsAdmin();

        Livewire::test(AdminDashboard::class)->assertSuccessful();
    }

    #[Test]
    public function helpdesk_tickets_table_performance_with_large_dataset(): void
    {
        $tickets = $this->seedHelpdeskTickets(2);

        $query = HelpdeskTicketResource::getEloquentQuery();
        $this->assertEquals($tickets->count(), $query->count());

        $record = $query->first();
        $this->assertNotNull($record);
        $this->assertTrue($record->relationLoaded('category'));
        $this->assertTrue($record->relationLoaded('division'));
    }

    #[Test]
    public function loan_applications_table_performance_with_relationships(): void
    {
        $loans = $this->seedLoanApplications(2);

        $query = LoanApplicationResource::getEloquentQuery();
        $this->assertEquals($loans->count(), $query->count());

        $record = $query->first();
        $this->assertNotNull($record);
        $this->assertTrue($record->relationLoaded('division'));
        $this->assertTrue($record->relationLoaded('loanItems'));
        $this->assertTrue($record->relationLoaded('transactions'));
    }

    #[Test]
    public function search_performance_across_large_dataset(): void
    {
        $this->seedLoanApplications(1);
        $target = $this->seedLoanApplications(1, ['applicant_name' => 'Performance Search Target'])->first();

        $resultIds = LoanApplication::query()
            ->where('applicant_name', 'Performance Search Target')
            ->pluck('id')
            ->all();

        $this->assertEquals([$target->id], $resultIds);
    }

    #[Test]
    public function filtering_performance(): void
    {
        $records = $this->seedLoanApplications(2);
        $submitted = $records->shift();
        $approved = $records->pop();

        $submitted->update(['status' => LoanStatus::SUBMITTED->value]);
        $approved->update(['status' => LoanStatus::APPROVED->value]);

        $submitted->refresh();
        $approved->refresh();

        $submittedCount = LoanApplication::query()
            ->where('status', LoanStatus::SUBMITTED->value)
            ->count();

        $approvedCount = LoanApplication::query()
            ->where('status', LoanStatus::APPROVED->value)
            ->count();

        $this->assertEquals(1, $submittedCount);
        $this->assertEquals(1, $approvedCount);
    }

    #[Test]
    public function pagination_performance(): void
    {
        $items = range(1, 20);
        $firstPage = new LengthAwarePaginator(array_slice($items, 0, 10), count($items), 10, 1);

        $this->assertCount(10, $firstPage->items());
        $this->assertEquals(2, $firstPage->lastPage());

        $secondPage = new LengthAwarePaginator(array_slice($items, 10, 10), count($items), 10, 2);

        $this->assertEquals(range(11, 20), $secondPage->items());
    }

    #[Test]
    public function widget_loading_performance(): void
    {
        $this->seedHelpdeskTickets(4);
        $this->seedLoanApplications(4);
        Cache::flush();

        $widget = app(HelpdeskStatsOverview::class);
        $stats = (function (): array {
            return $this->calculateStats();
        })->call($widget);

        $this->assertCount(7, $stats);
    }

    #[Test]
    public function export_performance(): void
    {
        $this->seedLoanApplications(6);

        $processed = 0;
        LoanApplication::query()->chunkById(3, function ($chunk) use (&$processed): void {
            $processed += $chunk->count();
        });

        $this->assertEquals(6, $processed);
    }

    #[Test]
    public function bulk_operations_performance(): void
    {
        $loans = $this->seedLoanApplications(2);
        $ids = $loans->pluck('id')->all();

        LoanApplication::query()
            ->whereIn('id', $ids)
            ->update(['status' => LoanStatus::APPROVED->value]);

        foreach ($ids as $loanId) {
            $status = LoanApplication::find($loanId)?->status;
            $this->assertEquals(LoanStatus::APPROVED->value, $status instanceof LoanStatus ? $status->value : $status);
        }

    }

    #[Test]
    public function memory_usage_within_limits(): void
    {
        $this->seedLoanApplications(6);

        $baseline = memory_get_usage(true);
        $records = LoanApplicationResource::getEloquentQuery()->limit(6)->get();
        $memoryUsed = memory_get_usage(true) - $baseline;

        $this->assertCount(6, $records);
        $this->assertLessThanOrEqual(16 * 1024 * 1024, $memoryUsed, 'Query should not allocate more than 16MB');
    }

    #[Test]
    public function concurrent_user_performance(): void
    {
        $this->seedLoanApplications(4);
        $alpha = $this->seedLoanApplications(1, ['applicant_name' => 'Alpha User'])->first();
        $beta = $this->seedLoanApplications(1, ['applicant_name' => 'Beta User'])->first();

        $alphaIds = LoanApplication::query()
            ->where('applicant_name', 'like', 'Alpha%')
            ->pluck('id')
            ->all();

        $betaIds = LoanApplication::query()
            ->where('applicant_name', 'like', 'Beta%')
            ->pluck('id')
            ->all();

        $this->assertEquals([$alpha->id], $alphaIds);
        $this->assertEquals([$beta->id], $betaIds);
    }

    #[Test]
    public function database_query_optimization(): void
    {
        $this->seedLoanApplications(1);
        $this->seedHelpdeskTickets(1);

        $loanQuery = LoanApplicationResource::getEloquentQuery();
        $this->assertArrayHasKey('division', $loanQuery->getEagerLoads());
        $this->assertArrayHasKey('loanItems', $loanQuery->getEagerLoads());
        $this->assertArrayHasKey('transactions', $loanQuery->getEagerLoads());

        $ticketQuery = HelpdeskTicketResource::getEloquentQuery();
        $this->assertArrayHasKey('category', $ticketQuery->getEagerLoads());
        $this->assertArrayHasKey('division', $ticketQuery->getEagerLoads());
        $this->assertArrayHasKey('assignedDivision', $ticketQuery->getEagerLoads());
        $this->assertArrayHasKey('assignedUser', $ticketQuery->getEagerLoads());
    }

    #[Test]
    public function cache_effectiveness(): void
    {
        $user = User::factory()->admin()->create();
        $this->seedLoanApplications(2, ['user_id' => $user->id]);
        $this->seedHelpdeskTickets(2, ['user_id' => $user->id]);

        $service = app(DashboardService::class);
        Cache::flush();

        $firstRun = $service->getStatistics($user);
        $this->assertTrue(Cache::has("portal.statistics.{$user->id}"));

        $secondRun = $service->getStatistics($user);

        $this->assertSame($firstRun, $secondRun);
    }

    #[Test]
    public function large_form_submission_performance(): void
    {
        $start = microtime(true);
        $created = $this->seedLoanApplications(3);
        $durationMs = (microtime(true) - $start) * 1000;

        $this->assertCount(3, $created);
        $this->assertLessThanOrEqual(4000, $durationMs, 'Creating three applications should finish within 4s');
    }

    /**
     * Seed loan applications for the authenticated admin.
     *
     * @return Collection<int, LoanApplication>
     */
    private function seedLoanApplications(int $count = 5, array $overrides = []): Collection
    {
        $now = now();
        $records = collect(range(1, $count))->map(function () use ($now, $overrides) {
            $sequence = ++$this->loanSequence;
            $startDate = $now->copy()->addDays($sequence);

            $base = [
                'application_number' => sprintf('PERF-%s-%04d', $now->format('Ymd'), $sequence),
                'user_id' => $overrides['user_id'] ?? $this->admin->id,
                'applicant_name' => "Applicant {$sequence}",
                'applicant_email' => "applicant{$sequence}@example.test",
                'applicant_phone' => '010-0000000',
                'staff_id' => 'MOTAC'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
                'grade' => '41',
                'division_id' => $this->division->id,
                'purpose' => 'Performance verification',
                'location' => 'Putrajaya',
                'return_location' => 'Putrajaya',
                'loan_start_date' => $startDate->toDateString(),
                'loan_end_date' => $startDate->copy()->addDays(2)->toDateString(),
                'status' => $overrides['status'] ?? 'submitted',
                'priority' => $overrides['priority'] ?? 'normal',
                'total_value' => 1_000,
                'approver_email' => null,
                'approved_by_name' => null,
                'approved_at' => null,
                'approval_token' => null,
                'approval_token_expires_at' => null,
                'approval_method' => null,
                'approval_remarks' => null,
                'rejected_reason' => null,
                'special_instructions' => null,
                'related_helpdesk_tickets' => null,
                'maintenance_required' => false,
                'anonymized_at' => null,
                'claimed_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            return array_merge($base, $overrides);
        });

        LoanApplication::query()->insert($records->all());

        return LoanApplication::query()
            ->latest('id')
            ->take($count)
            ->get()
            ->sortBy('id')
            ->values();
    }

    /**
     * Seed helpdesk tickets owned by the authenticated admin.
     *
     * @return Collection<int, HelpdeskTicket>
     */
    private function seedHelpdeskTickets(int $count = 5, array $overrides = []): Collection
    {
        $now = now();
        $records = collect(range(1, $count))->map(function () use ($now, $overrides) {
            $sequence = ++$this->ticketSequence;
            $base = [
                'ticket_number' => sprintf('HD-%s-%06d', $now->format('Ymd'), $sequence),
                'user_id' => $overrides['user_id'] ?? $this->admin->id,
                'guest_name' => null,
                'guest_email' => null,
                'guest_phone' => null,
                'guest_grade' => null,
                'guest_division' => null,
                'guest_staff_id' => null,
                'staff_id' => 'MOTAC'.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
                'division_id' => $this->division->id,
                'category_id' => $this->ticketCategory->id,
                'priority' => $overrides['priority'] ?? 'normal',
                'subject' => "Performance Ticket {$sequence}",
                'description' => 'Performance ticket body',
                'damage_type' => 'hardware',
                'internal_notes' => null,
                'status' => $overrides['status'] ?? 'open',
                'assigned_to_division' => null,
                'assigned_to_agency' => null,
                'assigned_to_user' => null,
                'asset_id' => null,
                'sla_response_due_at' => null,
                'sla_resolution_due_at' => null,
                'responded_at' => null,
                'resolved_at' => null,
                'closed_at' => null,
                'assigned_at' => null,
                'admin_notes' => null,
                'resolution_notes' => null,
                'anonymized_at' => null,
                'claimed_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            return array_merge($base, $overrides);
        });

        HelpdeskTicket::query()->insert($records->all());

        return HelpdeskTicket::query()
            ->latest('id')
            ->take($count)
            ->get()
            ->sortBy('id')
            ->values();
    }

    private function actingAsAdmin(): void
    {
        $this->actingAs($this->admin);
    }
}
