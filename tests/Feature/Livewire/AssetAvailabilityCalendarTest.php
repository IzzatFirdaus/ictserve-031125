<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssetAvailabilityCalendarTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function asset_calendar_reacts_to_asset_returned_damaged(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $asset = Asset::factory()->create(['status' => AssetStatus::AVAILABLE]);
        $application = LoanApplication::factory()->create(['user_id' => $user->id]);

        // Create damaged transaction
        $transaction = LoanTransaction::factory()->returnDamaged()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
            'processed_by' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Assets\AssetAvailabilityCalendar::class, ['assetId' => $asset->id])
            ->call('handleEchoAssetReturnedDamaged', [
                'asset_id' => $asset->id,
                'transaction_id' => $transaction->id,
                'damage_report' => $transaction->damage_report,
            ])
            ->assertDispatched('refreshCalendar');
    }
}
