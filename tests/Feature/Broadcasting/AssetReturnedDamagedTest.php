<?php

declare(strict_types=1);

namespace Tests\Feature\Broadcasting;

use App\Events\AssetReturnedDamaged;
use App\Enums\AssetCondition;
use App\Enums\TransactionType;
use App\Models\Asset;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\AssetTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssetReturnedDamagedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function asset_returned_with_damage_dispatches_broadcast_event(): void
    {
        Event::fake();

        // Create required models
        $user = User::factory()->create();
        $application = LoanApplication::factory()->create(['user_id' => $user->id]);
        $asset = Asset::factory()->create();

        // Create an issue transaction (previous state)
        $issue = LoanTransaction::factory()->issue()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
            'processed_by' => $user->id,
        ]);

        // Mock NotificationService to avoid external notifications
        $notificationMock = $this->createMock(NotificationService::class);
        $this->app->instance(NotificationService::class, $notificationMock);

        // Call checkInAsset with damaged condition
        $service = app(AssetTransactionService::class);

        $transaction = $service->checkInAsset($application, $asset, $user, [
            'condition_after' => AssetCondition::DAMAGED,
            'damage_report' => 'Broken hinge',
        ]);

        // Ensure the event was dispatched
        Event::assertDispatched(AssetReturnedDamaged::class, function ($event) use ($asset, $transaction) {
            // Confirm event contains expected models and values
            TestCase::assertInstanceOf(ShouldBroadcast::class, $event);

            return $event->asset->id === $asset->id && $event->transaction->id === $transaction->id;
        });
    }
}
