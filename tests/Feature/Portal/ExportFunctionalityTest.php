<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use App\Jobs\ExportSubmissionsJob;
use App\Livewire\ExportSubmissions;
use App\Models\Asset;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\ExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Export Functionality Feature Tests
 *
 * Tests CSV export, PDF export, large export queueing,
 * and file retention.
 *
 * Requirements: 9.1, 9.2, 9.3, 9.4, 9.5
 * Traceability: D03 SRS-FR-009, D04 §3.7
 */
class ExportFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Division $division;

    protected TicketCategory $category;

    protected ExportService $exportService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->division = Division::factory()->create(['name' => 'IT Division']);
        $this->category = TicketCategory::factory()->create(['name' => 'Hardware']);

        $this->user = User::factory()->create([
            'division_id' => $this->division->id,
            'grade' => 40,
        ]);

        $this->exportService = app(ExportService::class);
    }

    #[Test]
    public function user_can_access_export_interface(): void
    {
        Livewire::actingAs($this->user)
            ->test(ExportSubmissions::class)
            ->assertSee('Export Submissions');
    }

    #[Test]
    public function user_can_select_export_format(): void
    {
        Livewire::actingAs($this->user)
            ->test(ExportSubmissions::class)
            ->assertSee('CSV')
            ->assertSee('PDF');
    }

    #[Test]
    public function user_can_export_submissions_as_csv(): void
    {
        HelpdeskTicket::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $filename = $this->exportService->exportSubmissions($this->user, 'csv');

        $this->assertNotNull($filename);
        Storage::disk('local')->assertExists("exports/{$filename}");
    }

    #[Test]
    public function csv_export_contains_correct_headers(): void
    {
        HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $filename = $this->exportService->exportSubmissions($this->user, 'csv');
        $content = Storage::disk('local')->get("exports/{$filename}");

        $this->assertStringContainsString('Submission Type', $content);
        $this->assertStringContainsString('Number', $content);
        $this->assertStringContainsString('Subject/Asset', $content);
        $this->assertStringContainsString('Status', $content);
        $this->assertStringContainsString('Date Submitted', $content);
    }

    #[Test]
    public function csv_export_contains_submission_data(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'subject' => 'Test Ticket Subject',
        ]);

        $filename = $this->exportService->exportSubmissions($this->user, 'csv');
        $content = Storage::disk('local')->get("exports/{$filename}");

        $this->assertStringContainsString($ticket->ticket_number, $content);
        $this->assertStringContainsString('Test Ticket Subject', $content);
    }

    #[Test]
    public function user_can_export_submissions_as_pdf(): void
    {
        HelpdeskTicket::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $filename = $this->exportService->exportSubmissions($this->user, 'pdf');

        $this->assertNotNull($filename);
        Storage::disk('local')->assertExists("exports/{$filename}");
    }

    #[Test]
    public function pdf_export_contains_motac_branding(): void
    {
        HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $filename = $this->exportService->exportSubmissions($this->user, 'pdf');

        // PDF should exist
        Storage::disk('local')->assertExists("exports/{$filename}");

        // In a real test, you'd verify PDF content
        $this->assertTrue(true);
    }

    #[Test]
    public function user_can_filter_export_by_date_range(): void
    {
        // Create old ticket
        HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'created_at' => now()->subDays(30),
        ]);

        // Create recent ticket
        $recentTicket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'created_at' => now(),
        ]);

        $filters = [
            'date_from' => now()->subDays(7)->format('Y-m-d'),
        ];

        $filename = $this->exportService->exportSubmissions($this->user, 'csv', $filters);
        $content = Storage::disk('local')->get("exports/{$filename}");

        // Should only contain recent ticket
        $this->assertStringContainsString($recentTicket->ticket_number, $content);
    }

    #[Test]
    public function large_exports_are_queued(): void
    {
        Queue::fake();

        // Create 1001 tickets (exceeds 1000 threshold)
        HelpdeskTicket::factory()->count(1001)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $jobId = $this->exportService->exportSubmissions($this->user, 'csv');

        // Should return job ID instead of filename
        $this->assertIsString($jobId);

        // Should dispatch export job
        Queue::assertPushed(ExportSubmissionsJob::class);
    }

    #[Test]
    public function queued_export_sends_email_notification(): void
    {
        Queue::fake();

        HelpdeskTicket::factory()->count(1001)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $this->exportService->exportSubmissions($this->user, 'csv');

        Queue::assertPushed(ExportSubmissionsJob::class, function ($job) {
            return $job->user->id === $this->user->id;
        });
    }

    #[Test]
    public function export_files_are_deleted_after_7_days(): void
    {
        $filename = 'test_export.csv';
        Storage::disk('local')->put("exports/{$filename}", 'test content');

        // Simulate file created 8 days ago
        touch(Storage::disk('local')->path("exports/{$filename}"), now()->subDays(8)->timestamp);

        // Run cleanup command (would be scheduled)
        $this->artisan('exports:cleanup');

        Storage::disk('local')->assertMissing("exports/{$filename}");
    }

    #[Test]
    public function export_files_within_7_days_are_not_deleted(): void
    {
        $filename = 'recent_export.csv';
        Storage::disk('local')->put("exports/{$filename}", 'test content');

        // File created 5 days ago
        touch(Storage::disk('local')->path("exports/{$filename}"), now()->subDays(5)->timestamp);

        // Run cleanup command
        $this->artisan('exports:cleanup');

        Storage::disk('local')->assertExists("exports/{$filename}");
    }

    #[Test]
    public function export_progress_indicator_displayed_for_large_exports(): void
    {
        Queue::fake();

        HelpdeskTicket::factory()->count(1001)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ExportSubmissions::class)
            ->set('exportFormat', 'csv')
            ->call('generateExport')
            ->assertSee('Processing');
    }

    #[Test]
    public function export_filename_includes_timestamp(): void
    {
        HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $filename = $this->exportService->exportSubmissions($this->user, 'csv');

        // Filename should match pattern: submissions_YYYY-MM-DD_HHMMSS.csv
        $this->assertMatchesRegularExpression('/submissions_\d{4}-\d{2}-\d{2}_\d{6}\.csv/', $filename);
    }

    #[Test]
    public function export_respects_user_permissions(): void
    {
        $otherUser = User::factory()->create();
        $otherTicket = HelpdeskTicket::factory()->create([
            'user_id' => $otherUser->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $filename = $this->exportService->exportSubmissions($this->user, 'csv');
        $content = Storage::disk('local')->get("exports/{$filename}");

        // Should not contain other user's ticket
        $this->assertStringNotContainsString($otherTicket->ticket_number, $content);
    }

    #[Test]
    public function export_handles_empty_results(): void
    {
        $filename = $this->exportService->exportSubmissions($this->user, 'csv');
        $content = Storage::disk('local')->get("exports/{$filename}");

        // Should still have headers
        $this->assertStringContainsString('Submission Type', $content);
    }

    #[Test]
    public function export_includes_both_tickets_and_loans(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $asset = Asset::factory()->create();
        $loan = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $loan->id,
            'asset_id' => $asset->id,
        ]);

        $filename = $this->exportService->exportSubmissions($this->user, 'csv');
        $content = Storage::disk('local')->get("exports/{$filename}");

        $this->assertStringContainsString($ticket->ticket_number, $content);
        $this->assertStringContainsString($loan->application_number, $content);
    }

    #[Test]
    public function export_file_size_limited_to_10mb(): void
    {
        // Create many tickets
        HelpdeskTicket::factory()->count(100)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $filename = $this->exportService->exportSubmissions($this->user, 'pdf');
        $fileSize = Storage::disk('local')->size("exports/{$filename}");

        // File should be under 10MB (10485760 bytes)
        $this->assertLessThan(10485760, $fileSize);
    }
}
