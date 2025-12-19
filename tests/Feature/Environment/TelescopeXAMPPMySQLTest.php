<?php

declare(strict_types=1);

namespace Tests\Feature\Environment;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Test Laravel Telescope integration with XAMPP MySQL
 * 
 * Requirements: 6.3, 6.5
 */
class TelescopeXAMPPMySQLTest extends TestCase
{
    #[Test]
    public function it_has_correct_telescope_configuration(): void
    {
        $config = config('telescope');

        // Verify basic Telescope configuration
        $this->assertEquals('telescope', $config['path']);
        $this->assertArrayHasKey('middleware', $config);
        $this->assertContains('web', $config['middleware']);

        // Verify storage configuration for XAMPP MySQL
        $this->assertEquals('database', $config['driver']);
        $this->assertEquals('mysql', $config['storage']['database']['connection']);
        $this->assertEquals(1000, $config['storage']['database']['chunk']);
    }

    #[Test]
    public function it_has_correct_queue_configuration(): void
    {
        $config = config('telescope.queue');

        // Verify queue configuration for XAMPP environment
        $this->assertArrayHasKey('connection', $config);
        $this->assertArrayHasKey('queue', $config);
        $this->assertArrayHasKey('delay', $config);
        $this->assertEquals(10, $config['delay']);
    }

    #[Test]
    public function it_has_enabled_watchers(): void
    {
        $config = config('telescope.watchers');

        // Verify core watchers are configured
        $this->assertArrayHasKey('Laravel\\Telescope\\Watchers\\RequestWatcher', $config);
        $this->assertArrayHasKey('Laravel\\Telescope\\Watchers\\QueryWatcher', $config);
        $this->assertArrayHasKey('Laravel\\Telescope\\Watchers\\ExceptionWatcher', $config);
        $this->assertArrayHasKey('Laravel\\Telescope\\Watchers\\JobWatcher', $config);
        $this->assertArrayHasKey('Laravel\\Telescope\\Watchers\\CacheWatcher', $config);

        // Verify RequestWatcher configuration
        $requestWatcherConfig = $config['Laravel\\Telescope\\Watchers\\RequestWatcher'];
        $this->assertTrue($requestWatcherConfig['enabled']);

        // Verify QueryWatcher configuration
        $queryWatcherConfig = $config['Laravel\\Telescope\\Watchers\\QueryWatcher'];
        $this->assertTrue($queryWatcherConfig['enabled']);
        $this->assertArrayHasKey('slow', $queryWatcherConfig);
    }

    #[Test]
    public function it_can_access_telescope_dashboard_configuration(): void
    {
        $config = config('telescope');

        // Verify dashboard access configuration
        $this->assertEquals('telescope', $config['path']);
        $this->assertArrayHasKey('middleware', $config);

        // Verify domain configuration (should be null for local development)
        $this->assertNull($config['domain']);
    }

    #[Test]
    public function it_has_mysql_database_connection(): void
    {
        // Verify that the default database connection is MySQL for XAMPP
        $defaultConnection = config('database.default');
        $this->assertEquals('mysql', $defaultConnection);

        // Verify MySQL connection configuration
        $mysqlConfig = config('database.connections.mysql');
        $this->assertEquals('127.0.0.1', $mysqlConfig['host']);
        $this->assertEquals('3306', $mysqlConfig['port']);
        $this->assertEquals('root', $mysqlConfig['username']);
        $this->assertEquals('', $mysqlConfig['password']);
    }

    #[Test]
    public function it_has_correct_data_pruning_configuration(): void
    {
        $config = config('telescope');

        // Verify data pruning configuration
        $this->assertArrayHasKey('prune', $config);
        $this->assertArrayHasKey('hours', $config['prune']);

        // Verify reasonable retention period for XAMPP environment (7 days = 168 hours)
        $this->assertEquals(168, $config['prune']['hours']);
    }
}
