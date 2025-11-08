<?php

namespace Tests\Feature\Livewire\Assets;

use App\Livewire\Assets\AssetAvailabilityCalendar;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssetAvailabilityCalendarTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function renders_successfully(): void
    {
        Livewire::test(AssetAvailabilityCalendar::class)
            ->assertStatus(200);
    }
}
