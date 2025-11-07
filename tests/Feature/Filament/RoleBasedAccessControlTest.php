<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Role-Based Access Control Test Suite
 *
 * Tests Spatie Permission integration with ICTServe four-role RBAC system.
 *
 * Requirements: 17.1, 4.1, 4.2
 * Traceability: D03-FR-017.1, D04 §4.4
 */
class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    /**
     * Test that all four roles are created correctly
     */
    #[Test]
    public function it_creates_all_four_roles(): void
    {
        $roles = Role::all()->pluck('name')->toArray();

        $this->assertCount(4, $roles);
        $this->assertContains('staff', $roles);
        $this->assertContains('approver', $roles);
        $this->assertContains('admin', $roles);
        $this->assertContains('superuser', $roles);
    }

    /**
     * Test that all required permissions are created
     */
    #[Test]
    public function it_creates_all_required_permissions(): void
    {
        $permissions = Permission::all()->pluck('name')->toArray();

        // Helpdesk permissions
        $this->assertContains('helpdesk.view', $permissions);
        $this->assertContains('helpdesk.create', $permissions);
        $this->assertContains('helpdesk.admin', $permissions);

        // Loan permissions
        $this->assertContains('loan.view', $permissions);
        $this->assertContains('loan.approve', $permissions);
        $this->assertContains('loan.admin', $permissions);

        // Asset permissions
        $this->assertContains('asset.view', $permissions);
        $this->assertContains('asset.admin', $permissions);

        // User permissions
        $this->assertContains('user.view', $permissions);
        $this->assertContains('user.admin', $permissions);

        // System permissions
        $this->assertContains('system.config', $permissions);
        $this->assertContains('system.admin', $permissions);
    }

    /**
     * Test staff role has correct permissions
     */
    #[Test]
    public function staff_role_has_correct_permissions(): void
    {
        $staffRole = Role::findByName('staff');

        /** @phpstan-ignore-next-line */
        $this->assertTrue($staffRole->hasPermissionTo('helpdesk.view'));
        /** @phpstan-ignore-next-line */
        $this->assertTrue($staffRole->hasPermissionTo('helpdesk.create'));
        /** @phpstan-ignore-next-line */
        $this->assertTrue($staffRole->hasPermissionTo('loan.view'));
        /** @phpstan-ignore-next-line */
        $this->assertTrue($staffRole->hasPermissionTo('loan.create'));

        // Staff should NOT have admin permissions
        /** @phpstan-ignore-next-line */
        $this->assertFalse($staffRole->hasPermissionTo('helpdesk.admin'));
        /** @phpstan-ignore-next-line */
        $this->assertFalse($staffRole->hasPermissionTo('loan.admin'));
        /** @phpstan-ignore-next-line */
        $this->assertFalse($staffRole->hasPermissionTo('user.admin'));
    }

    /**
     * Test approver role has correct permissions
     */
    #[Test]
    public function approver_role_has_correct_permissions(): void
    {
        $approverRole = Role::findByName('approver');

        $this->assertTrue($approverRole->hasPermissionTo('helpdesk.view'));
        $this->assertTrue($approverRole->hasPermissionTo('loan.approve'));
        $this->assertTrue($approverRole->hasPermissionTo('asset.view'));

        // Approver should NOT have admin permissions
        $this->assertFalse($approverRole->hasPermissionTo('helpdesk.admin'));
        $this->assertFalse($approverRole->hasPermissionTo('user.admin'));
    }

    /**
     * Test admin role has correct permissions
     */
    #[Test]
    public function admin_role_has_correct_permissions(): void
    {
        $adminRole = Role::findByName('admin');

        $this->assertTrue($adminRole->hasPermissionTo('helpdesk.admin'));
        $this->assertTrue($adminRole->hasPermissionTo('loan.admin'));
        $this->assertTrue($adminRole->hasPermissionTo('asset.admin'));
        $this->assertTrue($adminRole->hasPermissionTo('user.view'));

        // Admin should NOT have full system config access
        $this->assertFalse($adminRole->hasPermissionTo('system.config'));
        $this->assertFalse($adminRole->hasPermissionTo('user.admin'));
    }

    /**
     * Test superuser role has all permissions
     */
    #[Test]
    public function superuser_role_has_all_permissions(): void
    {
        $superuserRole = Role::findByName('superuser');
        $allPermissions = Permission::all();

        foreach ($allPermissions as $permission) {
            $this->assertTrue(
                $superuserRole->hasPermissionTo($permission),
                "Superuser should have permission: {$permission->name}"
            );
        }
    }

    /**
     * Test user can be assigned a role
     */
    #[Test]
    public function user_can_be_assigned_role(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $user->assignRole('staff');

        $this->assertTrue($user->hasRole('staff'));
        $this->assertTrue($user->can('helpdesk.view'));
        $this->assertTrue($user->can('loan.create'));
    }

    /**
     * Test user with admin role has admin access
     */
    #[Test]
    public function user_with_admin_role_has_admin_access(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole('admin');

        $this->assertTrue($user->hasAdminAccess());
        $this->assertTrue($user->can('helpdesk.admin'));
        $this->assertTrue($user->can('loan.admin'));
        $this->assertTrue($user->can('asset.admin'));
    }

    /**
     * Test user with superuser role has full access
     */
    #[Test]
    public function user_with_superuser_role_has_full_access(): void
    {
        $user = User::factory()->create(['role' => 'superuser']);
        $user->assignRole('superuser');

        $this->assertTrue($user->isSuperuser());
        $this->assertTrue($user->hasAdminAccess());
        $this->assertTrue($user->can('system.config'));
        $this->assertTrue($user->can('user.admin'));
        $this->assertTrue($user->can('system.admin'));
    }

    /**
     * Test user without admin role does not have admin access
     */
    #[Test]
    public function user_without_admin_role_does_not_have_admin_access(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $user->assignRole('staff');

        $this->assertFalse($user->hasAdminAccess());
        $this->assertFalse($user->can('helpdesk.admin'));
        $this->assertFalse($user->can('user.admin'));
    }

    /**
     * Test role permissions are cached correctly
     */
    #[Test]
    public function role_permissions_are_cached(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole('admin');

        // First check - should cache
        $this->assertTrue($user->can('helpdesk.admin'));

        // Second check - should use cache
        $this->assertTrue($user->can('helpdesk.admin'));

        // Verify cache key exists
        $this->assertTrue(cache()->has('spatie.permission.cache'));
    }

    /**
     * Test user can have multiple roles (edge case)
     */
    #[Test]
    public function user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole(['admin', 'approver']);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('approver'));
        $this->assertTrue($user->can('helpdesk.admin'));
        $this->assertTrue($user->can('loan.approve'));
    }

    /**
     * Test role can be removed from user
     */
    #[Test]
    public function role_can_be_removed_from_user(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole('admin');

        $this->assertTrue($user->hasRole('admin'));

        $user->removeRole('admin');

        $this->assertFalse($user->hasRole('admin'));
        $this->assertFalse($user->can('helpdesk.admin'));
    }

    /**
     * Test permission check with non-existent permission
     */
    #[Test]
    public function permission_check_with_non_existent_permission_returns_false(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole('admin');

        $this->assertFalse($user->can('non.existent.permission'));
    }
}
