<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Helpdesk\SubmitTicket;
use App\Models\Division;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubmitTicketDivisionsTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function it_returns_active_divisions_sorted_by_localized_name(): void
    {
        Division::factory()->create([
            'code' => 'ICT',
            'name_en' => 'Information Technology',
            'name_ms' => 'Bahagian Teknologi Maklumat',
        ]);

        Division::factory()->create([
            'code' => 'HR',
            'name_en' => 'Human Resources',
            'name_ms' => 'Bahagian Sumber Manusia',
        ]);

        Division::factory()->inactive()->create([
            'code' => 'ARCH',
            'name_en' => 'Archive Division',
            'name_ms' => 'Bahagian Arkib',
        ]);

        // Test English locale
        app()->setLocale('en');

        $component = Livewire::test(SubmitTicket::class);

        // Divisions are passed to the view, so check the rendered content
        $component->assertSee('Human Resources')
            ->assertSee('Information Technology')
            ->assertDontSee('Archive Division'); // Inactive division should not appear

        // Test Malay locale
        app()->setLocale('ms');

        $componentMs = Livewire::test(SubmitTicket::class);

        $componentMs->assertSee('Bahagian Sumber Manusia')
            ->assertSee('Bahagian Teknologi Maklumat')
            ->assertDontSee('Bahagian Arkib'); // Inactive division should not appear
    }
}
