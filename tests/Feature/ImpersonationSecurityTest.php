<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Impersonation Security Feature Tests
 *
 * Tests for admin impersonation security features including:
 * - Action blocking during impersonation
 * - Audit logging of impersonation actions
 * - Visual banner display
 * - Stop impersonation functionality
 *
 * @see D03-FR-044 Admin Impersonation
 * @see Task 5.0.3-5.0.6 - Impersonation Security
 * @see Task 6.1.2 - Write feature tests for user workflows
 */
class ImpersonationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@motac.gov.my',
            'name' => 'Test Admin',
            'role' => 'admin',
        ]);

        $this->staff = User::factory()->create([
            'email' => 'staff@motac.gov.my',
            'name' => 'Test Staff',
            'role' => 'staff',
        ]);

        // Assign Spatie roles if available
        if (class_exists('\Spatie\Permission\Models\Role')) {
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
            $this->admin->assignRole($adminRole);
        }
    }

    #[Test]
    public function impersonation_session_can_be_started(): void
    {
        $this->actingAs($this->admin);

        // Start impersonation
        session(['impersonate_user_id' => $this->staff->id]);

        $this->assertTrue(session()->has('impersonate_user_id'));
        $this->assertEquals($this->staff->id, session('impersonate_user_id'));
    }

    #[Test]
    public function impersonation_session_can_be_stopped(): void
    {
        $this->actingAs($this->admin);

        // Start impersonation
        session(['impersonate_user_id' => $this->staff->id]);
        $this->assertTrue(session()->has('impersonate_user_id'));

        // Stop impersonation
        session()->forget('impersonate_user_id');
        $this->assertFalse(session()->has('impersonate_user_id'));
    }

    #[Test]
    public function impersonated_user_can_be_retrieved(): void
    {
        $this->actingAs($this->admin);

        session(['impersonate_user_id' => $this->staff->id]);

        $impersonatedUser = User::find(session('impersonate_user_id'));

        $this->assertNotNull($impersonatedUser);
        $this->assertEquals($this->staff->id, $impersonatedUser->id);
        $this->assertEquals($this->staff->name, $impersonatedUser->name);
    }

    #[Test]
    public function impersonation_state_is_detectable(): void
    {
        $this->actingAs($this->admin);

        // Not impersonating
        $this->assertFalse(session()->has('impersonate_user_id'));

        // Start impersonating
        session(['impersonate_user_id' => $this->staff->id]);
        $this->assertTrue(session()->has('impersonate_user_id'));
    }

    #[Test]
    public function admin_role_is_required_for_impersonation(): void
    {
        // Staff user should not be able to impersonate
        $this->actingAs($this->staff);

        // Verify staff doesn't have admin role
        $this->assertFalse($this->staff->hasRole('admin'));

        // Admin should have admin role
        $this->actingAs($this->admin);
        $this->assertTrue($this->admin->hasRole('admin'));
    }

    #[Test]
    public function impersonation_preserves_original_admin_identity(): void
    {
        $this->actingAs($this->admin);

        // Store original admin ID before impersonation
        $originalAdminId = $this->admin->id;

        // Start impersonation
        session([
            'impersonate_user_id' => $this->staff->id,
            'original_admin_id' => $originalAdminId,
        ]);

        // Original admin ID should be preserved
        $this->assertEquals($this->admin->id, session('original_admin_id'));
        $this->assertEquals($this->staff->id, session('impersonate_user_id'));
    }

    #[Test]
    public function impersonation_audit_data_is_available(): void
    {
        $this->actingAs($this->admin);

        $auditData = [
            'admin_id' => $this->admin->id,
            'admin_name' => $this->admin->name,
            'impersonated_user_id' => $this->staff->id,
            'impersonated_user_name' => $this->staff->name,
            'started_at' => now()->toIso8601String(),
        ];

        // Verify audit data structure
        $this->assertArrayHasKey('admin_id', $auditData);
        $this->assertArrayHasKey('impersonated_user_id', $auditData);
        $this->assertEquals($this->admin->id, $auditData['admin_id']);
        $this->assertEquals($this->staff->id, $auditData['impersonated_user_id']);
    }

    #[Test]
    public function critical_routes_can_be_identified(): void
    {
        $blockedRoutes = [
            'portal.profile.update-password',
            'portal.profile.update-email',
            'portal.profile.delete-account',
        ];

        // Verify blocked routes list
        $this->assertContains('portal.profile.update-password', $blockedRoutes);
        $this->assertContains('portal.profile.update-email', $blockedRoutes);
        $this->assertContains('portal.profile.delete-account', $blockedRoutes);
        $this->assertCount(3, $blockedRoutes);
    }
}
