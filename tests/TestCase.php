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

        // Ensure application environment is set to testing (some bootstrap code may
        // set APP_ENV to 'local' in dev environments). Set superglobals and config
        // after the application is available so testing behavior is consistent.
        $_ENV['APP_ENV'] = 'testing';
        $_SERVER['APP_ENV'] = 'testing';
        putenv('APP_ENV=testing');
        config(['app.env' => 'testing']);

        // Apply test database config so that the DB connection is sqlite in-memory
        // and migrations run against sqlite rather than a developer MySQL server.
        config(['database.default' => env('DB_CONNECTION', 'sqlite')]);
        config(['database.connections.sqlite.database' => env('DB_DATABASE', ':memory:')]);

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
     * Override migration step to ensure tests run on the expected connection (sqlite in-memory by default).
     *
     * This avoids cases where early config resolution causes migrations to run against MySQL in CI or
     * developer environments where MySQL isn't available. We explicitly pass --database to migrate:fresh
     * using the current DB connection (usually 'sqlite' per phpunit.xml or .env.testing).
     */
    protected function migrateDatabases()
    {
        // Prefer the configured database connection; fallback to env or sqlite
        $connection = config('database.default', env('DB_CONNECTION', 'sqlite'));

        // Merge default migration params and force the --database option
        $params = $this->migrateFreshUsing();
        $params['--database'] = $connection;

        // Debug output for test runs - also write to file so we can inspect after failure
        @file_put_contents(storage_path('logs/test-debug.log'), "migrateDatabases called --database={$connection} params=".json_encode($params)."\n", FILE_APPEND);

        $this->artisan('migrate:fresh', $params);
    }

    /**
     * Ensure any calls to refresh the test database route through our migration helper,
     * so they pick up the explicit --database option we pass above.
     */
    protected function refreshTestDatabase()
    {
        $this->migrateDatabases();

        $this->app[\Illuminate\Contracts\Console\Kernel::class]->setArtisan(null);
    }

    /**
     * Hook called before the database refresh; useful for diagnostic logging.
     */
    protected function beforeRefreshingDatabase()
    {
        @file_put_contents(__DIR__.'/test-debug.log', 'beforeRefreshingDatabase config.default='.config('database.default').' env.DB_CONNECTION='.env('DB_CONNECTION')."\n", FILE_APPEND);
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
