<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    #[Test]
    public function homepage_renders_correctly_with_ictserve_branding(): void
    {
        $this->get(route('welcome'))
            ->assertOk()
            ->assertSee('ICTServe')
            ->assertSee('Buat Aduan')
            ->assertSee('Mohon Sekarang')
            ->assertSee('Semak Status')
            ->assertDontSee('welcome.hero_title');
    }

    #[Test]
    public function homepage_displays_main_services(): void
    {
        $response = $this->get(route('welcome'));

        $response->assertOk()
            ->assertSee('Aduan ICT')
            ->assertSee('Pinjaman Aset')
            ->assertSee('Semak Status')
            ->assertSee('Soalan Lazim');
    }
}
