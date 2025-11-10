<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    #[Test]
    public function new_users_can_register(): void
    {
        // Use a unique complex password that meets all requirements:
        // - Min 8 chars, mixed case, numbers, symbols, uncompromised
        $password = 'TestP@ssw0rd'.time(); // Unique timestamp ensures not compromised

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', $password)
            ->set('password_confirmation', $password);

        $component->call('register');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }
}
