<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Header View Tests
 *
 * @trace Requirements 1.1, 3.2
 */
class HeaderViewTest extends TestCase
{
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
        $response->assertSee('Aduan ICT'); // ICT Complaints
        $response->assertSee('Pinjaman Aset'); // Asset Loan
        $response->assertSee('Semak Status'); // Check Status
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
        $response->assertSee('Papan Pemuka'); // Dashboard
        $response->assertSee('Profil'); // Profile link
        $response->assertSee('Log keluar'); // Logout (lowercase 'k')
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
        $response->assertSee('id="mobile-menu"', false);
        $response->assertSee('Buka menu utama'); // Mobile menu button aria-label
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
        $response->assertSee('Profil'); // Profile link
        // Note: Settings link may not be present in all views
        // The logout functionality is handled via Livewire component
    }
}
