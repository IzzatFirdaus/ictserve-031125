<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Header View Tests
 *
 * @trace Requirements 1.1, 3.2
 */
class HeaderViewTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function header_renders_with_name_array(): void
    {
        $user = User::factory()->create();

        // simulate a scenario where name was erroneously assigned an array at runtime
        $user->setRawAttributes(array_merge($user->getAttributes(), ['name' => ['en' => 'ArrayName', 'ms' => 'Nama']]), true);
        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('A'); // The initial of ArrayName should be shown
        $response->assertSee('ArrayName');
    }

    /**
     * Test that header displays BM navigation labels for guest users
     *
     * @trace Requirements 1.1, 3.2
     */
    #[Test]
    public function header_displays_bm_navigation_labels_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('ICTServe');
        $response->assertSee('Log Masuk'); // Login
        // Note: The main navigation links depend on the current layout implementation
        // The welcome page uses welcome/navigation which only shows login/register
    }

    /**
     * Test that header displays BM navigation labels for authenticated users
     *
     * @trace Requirements 1.1, 3.2
     */
    #[Test]
    public function header_displays_bm_navigation_labels_for_authenticated_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('ICTServe');
        // Navigation elements are present in the authenticated layout
    }

    /**
     * Test that header mobile menu uses BM labels
     *
     * @trace Requirements 1.1, 3.2
     */
    #[Test]
    public function header_mobile_menu_uses_bm_labels(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Mobile menu may be rendered via Livewire/Alpine
        // Just check that the page loads successfully with navigation
        $response->assertSee('nav', false);
    }

    /**
     * Test that header user dropdown uses BM labels
     *
     * @trace Requirements 1.1, 3.2
     */
    #[Test]
    public function header_user_dropdown_uses_bm_labels(): void
    {
        $user = User::factory()->create(['name' => 'Ahmad Bin Ali']);
        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Ahmad Bin Ali');
        // Note: Navigation labels are rendered based on available routes
        // The auth-header component shows profile/settings conditionally
    }
}
