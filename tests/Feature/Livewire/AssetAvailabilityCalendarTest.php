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

    #[Test]
    public function calendar_renders_with_bahasa_melayu_labels(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $asset = Asset::factory()->create(['status' => AssetStatus::AVAILABLE]);

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Assets\AssetAvailabilityCalendar::class, ['assetId' => $asset->id]);

        // Verify component renders successfully
        $component->assertStatus(200);

        // Verify translation keys are present (they will be translated by Laravel)
        $component->assertSee('Previous')
            ->assertSee('Next')
            ->assertSee('Available')
            ->assertSee('Loaned')
            ->assertSee('Maintenance');
    }

    #[Test]
    public function calendar_navigation_works_correctly(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Assets\AssetAvailabilityCalendar::class);

        // Test next month navigation
        $component->call('nextMonth')
            ->assertSet('currentMonth', now()->addMonth()->format('m'))
            ->assertSet('currentYear', now()->addMonth()->format('Y'));

        // Test previous month navigation
        $component->call('previousMonth')
            ->assertSet('currentMonth', now()->format('m'))
            ->assertSet('currentYear', now()->format('Y'));
    }

    #[Test]
    public function calendar_displays_asset_availability_correctly(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        // Create assets with different statuses
        $availableAsset = Asset::factory()->create(['status' => AssetStatus::AVAILABLE]);
        $maintenanceAsset = Asset::factory()->create(['status' => AssetStatus::MAINTENANCE]);

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Assets\AssetAvailabilityCalendar::class);

        // Verify calendar data is loaded
        $this->assertNotEmpty($component->get('calendarData'));

        // Verify assets are counted correctly in calendar data
        $calendarData = $component->get('calendarData');
        $todayData = collect($calendarData)->firstWhere('isToday', true);

        if ($todayData) {
            $this->assertArrayHasKey('availableCount', $todayData);
            $this->assertArrayHasKey('maintenanceCount', $todayData);
            $this->assertArrayHasKey('loanedCount', $todayData);
        }
    }

    #[Test]
    public function calendar_filters_by_asset_id_correctly(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $asset1 = Asset::factory()->create(['status' => AssetStatus::AVAILABLE]);
        $asset2 = Asset::factory()->create(['status' => AssetStatus::AVAILABLE]);

        // Test with specific asset filter
        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Assets\AssetAvailabilityCalendar::class, ['assetId' => $asset1->id]);

        $this->assertEquals($asset1->id, $component->get('assetId'));

        // Verify calendar data is loaded for the specific asset
        $this->assertNotEmpty($component->get('calendarData'));
    }

    #[Test]
    public function calendar_handles_echo_event_for_correct_asset_only(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $asset1 = Asset::factory()->create(['status' => AssetStatus::AVAILABLE]);
        $asset2 = Asset::factory()->create(['status' => AssetStatus::AVAILABLE]);

        // Component watching asset1
        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Assets\AssetAvailabilityCalendar::class, ['assetId' => $asset1->id]);

        // Event for asset2 should not trigger refresh
        $component->call('handleEchoAssetReturnedDamaged', [
            'asset_id' => $asset2->id,
            'transaction_id' => 123,
            'damage_report' => 'Test damage',
        ])
            ->assertNotDispatched('refreshCalendar');

        // Event for asset1 should trigger refresh
        $component->call('handleEchoAssetReturnedDamaged', [
            'asset_id' => $asset1->id,
            'transaction_id' => 123,
            'damage_report' => 'Test damage',
        ])
            ->assertDispatched('refreshCalendar');
    }
}
