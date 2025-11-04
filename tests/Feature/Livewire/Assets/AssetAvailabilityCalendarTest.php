<?php

namespace Tests\Feature\Livewire\Assets;

use App\Livewire\Assets\AssetAvailabilityCalendar;
use Livewire\Livewire;
use Tests\TestCase;

class AssetAvailabilityCalendarTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(AssetAvailabilityCalendar::class)
            ->assertStatus(200);
    }
}
