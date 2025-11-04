<?php

namespace Tests\Feature\Livewire\Assets;

use App\Livewire\Assets\AssetAvailabilityCalendar;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Livewire;
use Tests\TestCase;

class AssetAvailabilityCalendarTest extends TestCase
{
    use DatabaseMigrations;

    public function test_renders_successfully()
    {
        Livewire::test(AssetAvailabilityCalendar::class)
            ->assertStatus(200);
    }
}
