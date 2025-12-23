<?php

declare(strict_types=1);

namespace Tests\Feature\Database\Seeders;

use Database\Seeders\TicketCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TicketCategorySeederTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ticket_categories_are_seeded(): void
    {
        $this->seed(TicketCategorySeeder::class);

        $this->assertDatabaseCount('ticket_categories', 11);

        $this->assertDatabaseHas('ticket_categories', [
            'code' => 'HARDWARE_IPAD',
            'name_ms' => 'Perkakasan: iPad',
        ]);

        $this->assertDatabaseHas('ticket_categories', [
            'code' => 'LOAN_REQUEST',
            'name_en' => 'Loan Request',
        ]);
    }
}
