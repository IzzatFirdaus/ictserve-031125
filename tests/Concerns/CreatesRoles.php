<?php

declare(strict_types=1);

namespace Tests\Concerns;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Creates Roles Trait
 *
 * Provides helper method to create required roles and permissions for tests.
 * Use this trait in tests that need role-based functionality.
 */
trait CreatesRoles
{
    /**
     * Create all application roles and basic permissions
     */
    protected function createRoles(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create basic permissions
        $permissions = [
            'helpdesk.view',
            'helpdesk.create',
            'helpdesk.admin',
            'loan.view',
            'loan.create',
            'loan.approve',
            'loan.admin',
            'asset.view',
            'asset.admin',
            'user.view',
            'user.admin',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        Role::firstOrCreate(['name' => 'staff']);
        Role::firstOrCreate(['name' => 'approver']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'superuser']);
    }
}
