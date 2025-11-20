<?php

declare(strict_types=1);

namespace Tests\Feature\Database\Seeders;

use App\Models\Division;
use Database\Seeders\FullDivisionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullDivisionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_divisions_are_seeded_from_csv(): void
    {
        $this->seed(FullDivisionSeeder::class);

        $this->assertDatabaseHas('divisions', [
            'code' => 'ICT',
        ]);

        // Ensure a reasonable count (the CSV contains 12 sample rows)
        $this->assertTrue(Division::count() >= 10);
    }
}
