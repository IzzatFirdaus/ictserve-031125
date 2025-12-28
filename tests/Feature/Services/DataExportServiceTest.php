<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Asset;
use App\Models\LoanApplication;
use App\Services\DataExportService;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Data Export Service Tests
 *
 * @trace D03-FR-013.5 (Data Export Functionality)
 * @trace D03-FR-004.5 (Export Formats)
 */
class DataExportServiceTest extends TestCase
{
    private DataExportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DataExportService::class);
        Storage::fake('local');
    }

    #[Test]
    public function exports_loan_applications_to_csv(): void
    {
        LoanApplication::factory()->count(5)->create();

        $path = $this->service->exportLoanApplications();

        $this->assertNotNull($path);
        $this->assertStringContainsString('loan_applications_', $path);
        $this->assertStringEndsWith('.csv', $path);
        Storage::assertExists($path);
    }

    #[Test]
    public function exports_assets_to_csv(): void
    {
        Asset::factory()->count(10)->create();

        $path = $this->service->exportAssets();

        $this->assertNotNull($path);
        $this->assertStringContainsString('assets_', $path);
        Storage::assertExists($path);
    }

    #[Test]
    public function filters_loan_applications_by_status(): void
    {
        LoanApplication::factory()->count(3)->create(['status' => 'approved']);
        LoanApplication::factory()->count(2)->create(['status' => 'submitted']);

        $path = $this->service->exportLoanApplications(['status' => 'approved']);

        Storage::assertExists($path);
        $content = Storage::disk('local')->get($path);
        $this->assertStringContainsString('approved', $content);
    }

    #[Test]
    public function filters_by_date_range(): void
    {
        LoanApplication::factory()->create(['created_at' => now()->subDays(10)]);
        LoanApplication::factory()->create(['created_at' => now()->subDays(2)]);

        $path = $this->service->exportLoanApplications([
            'start_date' => now()->subDays(5)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]);

        Storage::assertExists($path);
    }

    #[Test]
    public function csv_has_proper_headers(): void
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
