<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.login');
    }

    #[Test]
    public function users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    #[Test]
    public function admin_users_are_redirected_to_filament_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $admin->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('filament.admin.pages.admin-dashboard', absolute: false));
    }

    #[Test]
    public function users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password');

        $component->call('login');

        $component
            ->assertHasErrors()
            ->assertNoRedirect();

        $this->assertGuest();
    }

    #[Test]
    public function navigation_menu_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response
            ->assertOk()
            ->assertSeeVolt('navigation.portal-navigation');
    }

    #[Test]
    public function users_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Visit dashboard to ensure navigation renders
        $response = $this->get('/dashboard');

        $response
            ->assertOk()
            ->assertSeeVolt('navigation.portal-navigation');

        // Logout via the form post (the actual logout mechanism)
        $logoutResponse = $this->post(route('logout'));

        $logoutResponse->assertRedirect('/');

        $this->assertGuest();
    }

    #[Test]
    public function users_can_logout_via_route(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('welcome'));

        $this->assertGuest();
    }
}
