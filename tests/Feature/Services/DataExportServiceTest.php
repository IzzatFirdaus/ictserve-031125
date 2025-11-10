<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Asset;
use App\Models\LoanApplication;
use App\Services\DataExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Data Export Service Tests
 *
 * @trace D03-FR-013.5 (Data Export Functionality)
 * @trace D03-FR-004.5 (Export Formats)
 */
class DataExportServiceTest extends TestCase
{
    use RefreshDatabase;

    private DataExportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DataExportService::class);
        Storage::fake('local');
    }

    public function test_exports_loan_applications_to_csv(): void
    {
        LoanApplication::factory()->count(5)->create();

        $path = $this->service->exportLoanApplications();

        $this->assertNotNull($path);
        $this->assertStringContainsString('loan_applications_', $path);
        $this->assertStringEndsWith('.csv', $path);
        Storage::disk('local')->assertExists($path);
    }

    public function test_exports_assets_to_csv(): void
    {
        Asset::factory()->count(10)->create();

        $path = $this->service->exportAssets();

        $this->assertNotNull($path);
        $this->assertStringContainsString('assets_', $path);
        Storage::disk('local')->assertExists($path);
    }

    public function test_filters_loan_applications_by_status(): void
    {
        LoanApplication::factory()->count(3)->create(['status' => 'approved']);
        LoanApplication::factory()->count(2)->create(['status' => 'submitted']);

        $path = $this->service->exportLoanApplications(['status' => 'approved']);

        Storage::disk('local')->assertExists($path);
        $content = Storage::disk('local')->get($path);
        $this->assertStringContainsString('approved', $content);
    }

    public function test_filters_by_date_range(): void
    {
        LoanApplication::factory()->create(['created_at' => now()->subDays(10)]);
        LoanApplication::factory()->create(['created_at' => now()->subDays(2)]);

        $path = $this->service->exportLoanApplications([
            'date_from' => now()->subDays(5)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]);

        Storage::disk('local')->assertExists($path);
    }

    public function test_csv_has_proper_headers(): void
    {
        LoanApplication::factory()->create();

        $path = $this->service->exportLoanApplications();
        $content = Storage::disk('local')->get($path);
        $lines = explode("\n", $content);

        $this->assertStringContainsString('Application Number', $lines[0]);
        $this->assertStringContainsString('Applicant Name', $lines[0]);
        $this->assertStringContainsString('Status', $lines[0]);
    }
}
