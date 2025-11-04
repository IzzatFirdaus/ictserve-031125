<?php

namespace Tests\Feature;

use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    public function test_homepage_renders_translated_content_in_english(): void
    {
        app()->setLocale('en');

        $this->get(route('welcome'))
            ->assertOk()
            ->assertSee('Welcome to ICTServe')
            ->assertSee('Staff Login')
            ->assertSee('Admin Login')
            ->assertSee('Submit Ticket')
            ->assertDontSee('welcome.hero_title');
    }

    public function test_homepage_renders_translated_content_in_malay(): void
    {
        $this->withHeader('Accept-Language', 'ms')
            ->get(route('welcome'))
            ->assertOk()
            ->assertSee('Selamat Datang ke ICTServe')
            ->assertSee('Log Masuk Kakitangan')
            ->assertSee('Log Masuk Admin')
            ->assertSee('Hantar Tiket')
            ->assertDontSee('welcome.hero_title');
    }
}
