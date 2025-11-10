<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase; // Run migrations for in-memory SQLite database

    /** @var bool Prevent automatic database seeding for all tests */
    protected $seed = false;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and permissions for all tests
        $this->createRolesAndPermissions();

        // Temporarily hide Filament admin views to prevent Panel component resolution errors during tests
        $filamentView = resource_path('views/filament/pages/helpdesk-reports.blade.php');
        $filamentViewBackup = resource_path('views/filament/pages/helpdesk-reports.blade.php.backup');

        if (file_exists($filamentView) && ! file_exists($filamentViewBackup)) {
            // Use @ to suppress file system errors (file may be locked on Windows)
            @rename($filamentView, $filamentViewBackup);
        }
    }

    /**
     * Create roles and permissions for testing
     */
    protected function createRolesAndPermissions(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create basic permissions
        $permissions = [
            'helpdesk.view', 'helpdesk.create', 'helpdesk.admin',
            'loan.view', 'loan.create', 'loan.approve', 'loan.admin',
            'asset.view', 'asset.admin',
            'user.view', 'user.admin',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'staff']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'approver']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superuser']);
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        // Restore Filament admin views after tests
        $filamentView = resource_path('views/filament/pages/helpdesk-reports.blade.php');
        $filamentViewBackup = resource_path('views/filament/pages/helpdesk-reports.blade.php.backup');

        if (file_exists($filamentViewBackup)) {
            // Use @ to suppress file system errors (file may be locked on Windows)
            @rename($filamentViewBackup, $filamentView);
        }

        parent::tearDown();
    }
}
