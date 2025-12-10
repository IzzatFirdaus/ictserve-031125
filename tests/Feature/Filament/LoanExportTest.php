<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoanExportTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    #[Test]
    public function example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
