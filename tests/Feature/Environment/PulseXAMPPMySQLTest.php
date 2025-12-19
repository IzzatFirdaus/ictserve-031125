<?php

declare(strict_types=1);

namespace Tests\Feature\Environment;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Test Laravel Pulse integration with XAMPP MySQL
 * 
 * Requirements: 6.1, 6.5
 */
class PulseXAMPPMySQLTest extends TestCase
{
    #[Test]
    public function it_has_correct_pulse_configuration(): void
    {
        $config = config('pulse');

        // Verify basic Pulse configuration
        $this->assertEquals('pulse', $config['path']);
        $this->assertArrayHasKey('middleware', $config);
        $this->assertContains('web', $config['middleware']);

        // Verify storage configuration for XAMPP MySQL
        $this->assertEquals('database', $config['storage']['driver']);
        $this->assertEquals(1000, $config['storage']['database']['chunk']);
        $this->assertEquals('7 days', $config['storage']['trim']['keep']);
    }

    #[Test]
    public function it_has_correct_ingest_configuration(): void
    {
        $config = config('pulse.ingest');

        // Verify ingest configuration for XAMPP environment
        $this->assertEquals('storage', $config['driver']);
        $this->assertEquals(5000, $config['buffer']);

        // Verify trim configuration
        $this->assertArrayHasKey('trim', $config);
        $this->assertArrayHasKey('keep', $config['trim']);
        $this->assertEquals('7 days', $config['trim']['keep']);
    }

    #[Test]
    public function it_has_enabled_recorders(): void
    {
        $config = config('pulse.recorders');

        // Verify core recorders are configured
        $this->assertArrayHasKey('Laravel\\Pulse\\Recorders\\UserRequests', $config);
        $this->assertArrayHasKey('Laravel\\Pulse\\Recorders\\SlowQueries', $config);
        $this->assertArrayHasKey('Laravel\\Pulse\\Recorders\\Exceptions', $config);
        $this->assertArrayHasKey('Laravel\\Pulse\\Recorders\\UserJobs', $config);
        $this->assertArrayHasKey('Laravel\\Pulse\\Recorders\\CacheInteractions', $config);

        // Verify UserRequests recorder configuration
        $userRequestsConfig = $config['Laravel\\Pulse\\Recorders\\UserRequests'];
        $this->assertTrue($userRequestsConfig['enabled']);
        $this->assertEquals(1, $userRequestsConfig['sample_rate']);

        // Verify SlowQueries recorder configuration
        $slowQueriesConfig = $config['Laravel\\Pulse\\Recorders\\SlowQueries'];
        $this->assertTrue($slowQueriesConfig['enabled']);
        $this->assertEquals(500, $slowQueriesConfig['threshold']); // Per Requirement 36.2: 500ms threshold
    }

    #[Test]
    public function it_can_access_pulse_dashboard_configuration(): void
    {
        $config = config('pulse');

        // Verify dashboard access configuration
        $this->assertEquals('pulse', $config['path']);
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
}
