<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Services\AssetTransactionService;
use App\Services\LoanReminderService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoanReminderServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function send_return_reminders_delegates_to_asset_transaction_service(): void
    {
        $assetTransactionService = Mockery::mock(AssetTransactionService::class);
        $assetTransactionService->shouldReceive('sendReturnReminders')->once();

        $service = new LoanReminderService($assetTransactionService);
        $service->sendReturnReminders();

        // Ensure test has an assertion - Mockery verifies the expectation in tearDown
        $this->assertTrue(true);
    }

    #[Test]
    public function track_overdue_assets_delegates_to_asset_transaction_service(): void
    {
        $assetTransactionService = Mockery::mock(AssetTransactionService::class);
        $assetTransactionService->shouldReceive('trackOverdueAssets')->once();

        $service = new LoanReminderService($assetTransactionService);
        $service->trackOverdueAssets();

        $this->assertTrue(true);
    }
}
