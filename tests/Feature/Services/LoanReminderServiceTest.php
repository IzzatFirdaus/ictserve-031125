<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Services\AssetTransactionService;
use App\Services\LoanReminderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class LoanReminderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_send_return_reminders_delegates_to_asset_transaction_service(): void
    {
        $assetTransactionService = Mockery::mock(AssetTransactionService::class);
        $assetTransactionService->shouldReceive('sendReturnReminders')->once();

        $service = new LoanReminderService($assetTransactionService);
        $service->sendReturnReminders();

        // Ensure test has an assertion - Mockery verifies the expectation in tearDown
        $this->assertTrue(true);
    }

    public function test_track_overdue_assets_delegates_to_asset_transaction_service(): void
    {
        $assetTransactionService = Mockery::mock(AssetTransactionService::class);
        $assetTransactionService->shouldReceive('trackOverdueAssets')->once();

        $service = new LoanReminderService($assetTransactionService);
        $service->trackOverdueAssets();

        $this->assertTrue(true);
    }
}
