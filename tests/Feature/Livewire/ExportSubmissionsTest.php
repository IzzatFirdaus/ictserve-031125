<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\ExportSubmissions;
use App\Models\User;
use App\Services\ExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Mockery;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class ExportSubmissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_range_requires_dates(): void
    {
        $user = User::factory()->create();

        $service = Mockery::mock(ExportService::class);
        $service->shouldNotReceive('exportToCsv', 'exportToExcel');
        app()->instance(ExportService::class, $service);

        $this->actingAs($user);

        Livewire::test(ExportSubmissions::class)
            ->set('dateRange', 'custom')
            ->set('customStartDate', null)
            ->set('customEndDate', null)
            ->call('generateExport')
            ->assertSet('isExporting', false);
    }

    public function test_generates_csv_export_via_service(): void
    {
        $user = User::factory()->create();

        $mockResponse = new StreamedResponse(function (): void {
            // noop for testing
        });

        $service = Mockery::mock(ExportService::class);
        $service->shouldReceive('exportToCsv')
            ->once()
            ->with($user, 'helpdesk', Mockery::on(function (array $filters): bool {
                return isset($filters['date_from'], $filters['date_to'])
                    && $filters['date_from'] instanceof Carbon
                    && $filters['date_to'] instanceof Carbon;
            }))
            ->andReturn($mockResponse);

        app()->instance(ExportService::class, $service);

        $this->actingAs($user);

        Livewire::test(ExportSubmissions::class)
            ->set('exportFormat', 'csv')
            ->call('generateExport')
            ->assertSet('isExporting', false);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
