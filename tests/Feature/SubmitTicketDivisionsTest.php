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

        app()->setLocale('en');

        $component = Livewire::test(SubmitTicket::class);

        $this->assertSame(
            ['Human Resources', 'Information Technology'],
            $component->divisions->pluck('name')->toArray()
        );

        app()->setLocale('ms');

        $componentMs = Livewire::test(SubmitTicket::class);

        $this->assertSame(
            ['Bahagian Sumber Manusia', 'Bahagian Teknologi Maklumat'],
            $componentMs->divisions->pluck('name')->toArray()
        );
    }
}
