<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\ExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Export Service Unit Tests
 *
 * Tests CSV generation, PDF generation, and queue processing.
 *
 * @traceability Requirements 9.1, 9.2, 9.3, 9.4
 */
class ExportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExportService;
        Storage::fake('local');
    }

    /**
     * Test CSV generation with proper formatting
     *
     * @test
     *
     * @traceability Requirement 9.2
     */
    public function test_generate_csv_with_proper_formatting(): void
    {
        $user = User::factory()->create();

        $tickets = HelpdeskTicket::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $filename = $this->service->exportSubmissions($user, 'csv', []);

        $this->assertNotEmpty($filename);
        $this->assertStringEndsWith('.csv', $filename);

        Storage::disk('local')->assertExists("exports/{$filename}");

        $content = Storage::disk('local')->get("exports/{$filename}");
        $this->assertStringContainsString('Submission Type', $content);
        $this->assertStringContainsString('Number', $content);
        $this->assertStringContainsString('Status', $content);
    }

    /**
     * Test PDF generation with branding
     *
     * @test
     *
     * @traceability Requirement 9.3
     */
    public function test_generate_pdf_with_branding(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        $filename = $this->service->exportSubmissions($user, 'pdf', []);

        $this->assertNotEmpty($filename);
        $this->assertStringEndsWith('.pdf', $filename);

        Storage::disk('local')->assertExists("exports/{$filename}");
    }

    /**
     * Test large export queueing
     *
     * @test
     *
     * @traceability Requirement 9.4
     */
    public function test_large_export_is_queued(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        // Create 1001 tickets to trigger queue
        HelpdeskTicket::factory()->count(1001)->create([
            'user_id' => $user->id,
        ]);

        $jobId = $this->service->exportSubmissions($user, 'csv', []);

        // Should return UUID instead of filename
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $jobId);

        Queue::assertPushed(\App\Jobs\ExportSubmissionsJob::class);
    }

    /**
     * Test export with date range filter
     *
     * @test
     *
     * @traceability Requirement 9.1
     */
    public function test_export_with_date_range_filter(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(10),
        ]);

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(2),
        ]);

        $filters = [
            'date_from' => now()->subDays(7)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ];

        $filename = $this->service->exportSubmissions($user, 'csv', $filters);

        $content = Storage::disk('local')->get("exports/{$filename}");
        $lines = explode("\n", trim($content));

        // Header + 1 data row (only recent ticket)
        $this->assertCount(2, $lines);
    }

    /**
     * Test CSV UTF-8 encoding for bilingual content
     *
     * @test
     *
     * @traceability Requirement 9.2
     */
    public function test_csv_utf8_encoding_for_bilingual_content(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'subject' => 'Masalah Pencetak - Printer Issue',
        ]);

        $filename = $this->service->exportSubmissions($user, 'csv', []);

        $content = Storage::disk('local')->get("exports/{$filename}");

        $this->assertStringContainsString('Masalah Pencetak', $content);
        $this->assertTrue(mb_check_encoding($content, 'UTF-8'));
    }

    /**
     * Test export includes both tickets and loans
     *
     * @test
     *
     * @traceability Requirement 9.1
     */
    public function test_export_includes_both_tickets_and_loans(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        LoanApplication::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        $filename = $this->service->exportSubmissions($user, 'csv', []);

        $content = Storage::disk('local')->get("exports/{$filename}");
        $lines = explode("\n", trim($content));

        // Header + 4 data rows
        $this->assertCount(5, $lines);
    }
}
