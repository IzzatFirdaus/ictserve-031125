<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\GuestLoanApplication;
use App\Models\Division;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Livewire;
use Tests\TestCase;

class GuestLoanApplicationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_divisions_are_ordered_by_locale_specific_column(): void
    {
        app()->setLocale('en');

        $bravo = Division::factory()->create([
            'name_en' => 'Bravo Division',
            'name_ms' => 'Bahagian Bravo',
        ]);

        $alpha = Division::factory()->create([
            'name_en' => 'Alpha Division',
            'name_ms' => 'Bahagian Alpha',
        ]);

        Livewire::test(GuestLoanApplication::class)
            ->assertViewHas('divisions', function ($divisions) use ($alpha, $bravo) {
                return $divisions->pluck('id')->all() === [$alpha->id, $bravo->id];
            });

        app()->setLocale('ms');

        $charlie = Division::factory()->create([
            'name_en' => 'Charlie Division',
            'name_ms' => 'Bahagian Charlie',
        ]);

        Livewire::test(GuestLoanApplication::class)
            ->assertViewHas('divisions', function ($divisions) use ($alpha, $bravo, $charlie) {
                return $divisions->pluck('id')->all() === [$alpha->id, $bravo->id, $charlie->id];
            });
    }
}
