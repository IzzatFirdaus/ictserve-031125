<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Role and Permission Seeder for ICTServe RBAC System
 *
 * Implements four-role RBAC system as per D03 requirements:
 * - Staff: Basic authenticated portal access
 * - Approver: Grade 41+ approval rights
 * - Admin: Operational asset and loan management
 * - Superuser: Full system governance and configuration
 *
 * @see D03-FR-010.1 Role-based access control
 * @see D04 §4.4 RBAC implementation
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for Helpdesk module
        $helpdeskPermissions = [
            'helpdesk.view',
            'helpdesk.create',
            'helpdesk.update',
            'helpdesk.delete',
            'helpdesk.assign',
            'helpdesk.resolve',
            'helpdesk.admin',
        ];

        // Create permissions for Asset Loan module
        $loanPermissions = [
            'loan.view',
            'loan.create',
            'loan.update',
            'loan.delete',
            'loan.approve',
            'loan.issue',
            'loan.return',
            'loan.admin',
        ];

        // Create permissions for Asset Management
        $assetPermissions = [
            'asset.view',
            'asset.create',
            'asset.update',
            'asset.delete',
            'asset.manage',
            'asset.admin',
        ];

        // Create permissions for User Management
        $userPermissions = [
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'user.admin',
        ];

        // Create permissions for System Administration
        $systemPermissions = [
            'system.config',
            'system.audit',
            'system.backup',
            'system.admin',
        ];

        // Create all permissions
        $allPermissions = array_merge(
            $helpdeskPermissions,
            $loanPermissions,
            $assetPermissions,
            $userPermissions,
            $systemPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // 1. Staff Role - Basic authenticated portal access
        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'helpdesk.view',
            'helpdesk.create',
            'loan.view',
            'loan.create',
        ]);

        // 2. Approver Role - Grade 41+ approval rights
        $approverRole = Role::create(['name' => 'approver']);
        $approverRole->givePermissionTo([
            'helpdesk.view',
            'helpdesk.create',
            'helpdesk.assign',
            'loan.view',
            'loan.create',
            'loan.approve',
            'asset.view',
        ]);

        // 3. Admin Role - Operational asset and loan management
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'helpdesk.view',
            'helpdesk.create',
            'helpdesk.update',
            'helpdesk.assign',
            'helpdesk.resolve',
            'helpdesk.admin',
            'loan.view',
            'loan.create',
            'loan.update',
            'loan.approve',
            'loan.issue',
            'loan.return',
            'loan.admin',
            'asset.view',
            'asset.create',
            'asset.update',
            'asset.manage',
            'asset.admin',
            'user.view',
        ]);

        // 4. Superuser Role - Full system governance and configuration
        $superuserRole = Role::create(['name' => 'superuser']);
        $superuserRole->givePermissionTo(Permission::all());

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Created roles: staff, approver, admin, superuser');
        $this->command->info('Total permissions: '.count($allPermissions));
    }
}
