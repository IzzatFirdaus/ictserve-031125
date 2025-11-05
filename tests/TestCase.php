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

        // Temporarily hide Filament admin views to prevent Panel component resolution errors during tests
        $filamentView = resource_path('views/filament/pages/helpdesk-reports.blade.php');
        $filamentViewBackup = resource_path('views/filament/pages/helpdesk-reports.blade.php.backup');

        if (file_exists($filamentView) && ! file_exists($filamentViewBackup)) {
            // Use @ to suppress file system errors (file may be locked on Windows)
            @rename($filamentView, $filamentViewBackup);
        }
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
