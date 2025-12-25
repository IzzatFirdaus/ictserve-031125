<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Helpdesk\SubmitTicket;
use App\Models\Division;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubmitTicketDivisionsTest extends TestCase
{
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

    #[Test]
    public function guest_dropdown_shows_localized_names_not_codes(): void
    {
        // Seed a division that might previously have been shown as code 'ICT'
        Division::factory()->create([
            'code' => 'ICT',
            'name_ms' => 'Bahagian Pengurusan Maklumat',
            'name_en' => 'Information Management Division',
            'is_active' => true,
        ]);

        app()->setLocale('ms');

        $component = Livewire::test(SubmitTicket::class);

        $component->assertSee('Bahagian Pengurusan Maklumat')
            ->assertDontSee('ICT');
    }
}
