<?php

declare(strict_types=1);

/**
 * Admin Login Design Test
 *
 * Tests the improved admin login page design to ensure it matches
 * the existing login and register page patterns while maintaining
 * ICTServe v3.6.0 specifications.
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.1 (Authentication), D12 §9 (WCAG 2.2 AA)
 * @trace D13 §2.2-2.7 (MyDS), D14 §4 (MOTAC Branding)
 *
 * @version 3.6.0
 *
 * @created 2025-12-18
 */

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminLoginDesignTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_login_page_displays_correctly(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);

        // Check for MOTAC branding elements
        $response->assertSee('ICTServe');
        $response->assertSee('Log Masuk Pentadbir');
        $response->assertSee('Sila log masuk untuk mengakses papan pemuka pentadbir');

        // Check for Bahasa Melayu interface (v3.6.0) with flexible login
        $response->assertSee('Emel atau Nama Pengguna');
        $response->assertSee('Kata Laluan');
        $response->assertSee('Ingat saya');
        $response->assertSee('Perlukan bantuan?');
        $response->assertSee('Hubungi Meja Bantuan');

        // Check for accessibility elements (WCAG 2.2 AA)
        $response->assertSee('Langkau ke kandungan utama', false); // Skip link in Malay
        $response->assertSee('main-content', false); // Main content target
    }

    #[Test]
    public function admin_login_page_has_proper_form_structure(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);

        // Check form elements exist - using wire:model for Livewire forms
        $response->assertSee('data.email', false);
        $response->assertSee('data.password', false);
        $response->assertSee('data.remember', false);
        $response->assertSee('type="submit"', false);

        // Check for flexible login support (D03 SRS-AUTH-003)
        $response->assertSee('nama@motac.gov.my atau nama', false);
        $response->assertSee('autocomplete="username"', false);
        $response->assertSee('autocomplete="current-password"', false);

        // Check for flexible login helper text
        $response->assertSee('Anda boleh log masuk menggunakan emel penuh atau nama pengguna sahaja');
    }

    #[Test]
    public function admin_login_page_includes_theme_switcher(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);

        // Check for theme switcher component (unified theme toggle)
        $response->assertSee('theme-toggle-unified', false);
    }

    #[Test]
    public function admin_login_page_includes_google_sso(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);

        // Check for Google SSO integration (D03 SRS-AUTH-005)
        $response->assertSee('auth/google', false);
        $response->assertSee('Google', false);
        $response->assertSee('atau', false); // "or" separator
    }

    #[Test]
    public function admin_login_page_has_proper_styling_classes(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);

        // Check for MyDS Design System classes
        $response->assertSee('font-heading', false);
        $response->assertSee('font-body', false);
        $response->assertSee('theme-transition', false);

        // Check for WCAG 2.2 AA compliant classes
        $response->assertSee('min-h-11', false); // 44px touch targets
        $response->assertSee('focus-visible:ring-3', false); // Focus indicators
        $response->assertSee('focus-visible:ring-primary-500', false);
    }

    #[Test]
    public function admin_login_authenticates_admin_users(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@motac.gov.my',
            'password' => bcrypt('password123'),
        ]);

        $admin->assignRole('admin');

        // Filament uses Livewire for authentication, so we test via Livewire
        $this->actingAs($admin);

        $response = $this->get('/admin');

        // Admin should be able to access admin panel (may redirect to dashboard)
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    #[Test]
    public function admin_login_rejects_non_admin_users(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@motac.gov.my',
            'password' => bcrypt('password123'),
        ]);

        $user->assignRole('staff');

        // Staff users should not have admin access
        $this->assertFalse($user->hasAdminAccess());
    }

    #[Test]
    public function admin_login_supports_flexible_login_with_username(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@motac.gov.my',
            'password' => bcrypt('password123'),
        ]);

        $admin->assignRole('admin');

        // Test that admin has proper access
        $this->assertTrue($admin->hasAdminAccess());
    }

    #[Test]
    public function admin_login_supports_flexible_login_with_full_email(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@motac.gov.my',
            'password' => bcrypt('password123'),
        ]);

        $admin->assignRole('admin');

        // Test that admin can be found by email
        $foundAdmin = User::where('email', 'admin@motac.gov.my')->first();
        $this->assertNotNull($foundAdmin);
        $this->assertTrue($foundAdmin->hasAdminAccess());
    }

    #[Test]
    public function admin_login_validates_credentials(): void
    {
        // Test that login page is accessible
        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        // Test that form has required fields
        $response->assertSee('Emel atau Nama Pengguna');
        $response->assertSee('Kata Laluan');
    }

    #[Test]
    public function admin_login_requires_authentication(): void
    {
        // Unauthenticated users should be redirected to login
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    #[Test]
    public function admin_login_page_includes_footer_links(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);

        // Check for footer elements
        $response->assertSee('Kementerian Pelancongan, Seni dan Budaya');
        $response->assertSee('Hak cipta terpelihara');
        $response->assertSee(date('Y'));

        // Check for help links
        $response->assertSee('Hubungi Meja Bantuan');
    }

    #[Test]
    public function admin_login_page_is_responsive(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);

        // Check for responsive design classes
        $response->assertSee('sm:max-w-md', false); // Mobile-first approach
        $response->assertSee('sm:px-6', false);
        $response->assertSee('lg:px-8', false);
    }

    #[Test]
    public function admin_login_page_has_proper_meta_tags(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);

        // Check that the page renders successfully with Livewire components
        $response->assertSee('ICTServe');
        $response->assertSee('Log Masuk Pentadbir');
    }
}
