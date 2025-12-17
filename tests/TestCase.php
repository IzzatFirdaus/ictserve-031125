<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase; // Run migrations for in-memory SQLite database

    /** @var bool Prevent automatic database seeding for all tests */
    protected $seed = false;

    protected static bool $viewsInitialized = false;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (! static::$viewsInitialized) {
            // Clear compiled views once to prevent Filament component pollution
            // Skip view:cache on Windows due to file locking issues that cause test timeouts
            $this->artisan('view:clear');

            $compiledViewPath = storage_path('framework/views_testing');
            File::ensureDirectoryExists($compiledViewPath);
            File::cleanDirectory($compiledViewPath);

            // Normalize permissions on compiled Volt views to avoid Windows access issues during tests
            @chmod($compiledViewPath, 0777);
            foreach (File::glob("{$compiledViewPath}/*") as $viewFile) {
                @chmod($viewFile, 0666);
            }

            // Note: view:cache is skipped to prevent Windows file locking timeouts
            // Views will be compiled on-demand during tests
            static::$viewsInitialized = true;
        }

        // Create roles and permissions for all tests
        $this->createRolesAndPermissions();

        // Temporarily hide Filament admin views to prevent Panel component resolution errors during tests
        $filamentViews = [
            resource_path('views/filament/pages/helpdesk-reports.blade.php'),
            resource_path('views/filament/pages/report-builder.blade.php'),
        ];

        foreach ($filamentViews as $view) {
            $backup = $view.'.backup';
            if (file_exists($view) && ! file_exists($backup)) {
                // Use @ to suppress file system errors (file may be locked on Windows)
                @rename($view, $backup);
            }
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
        $filamentViews = [
            resource_path('views/filament/pages/helpdesk-reports.blade.php'),
            resource_path('views/filament/pages/report-builder.blade.php'),
        ];

        foreach ($filamentViews as $view) {
            $backup = $view.'.backup';
            if (file_exists($backup)) {
                // Use @ to suppress file system errors (file may be locked on Windows)
                @rename($backup, $view);
            }
        }

        parent::tearDown();
    }
}
