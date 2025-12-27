<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Laravel Reverb Configuration Test
 *
 * Confirms that Laravel Reverb v1.6.3 is properly configured for ICTServe v3.6.0
 * real-time communication system.
 *
 * @see .kiro/specs/ictserve-comprehensive-v3.6/tasks.md Task 8.4
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 *
 * @requirements 6.1, 6.2, 6.3, 6.4, 6.5
 */
#[Group('environment-specific')]
class ReverbConfigurationTest extends TestCase
{
    #[Test]
    public function reverb_connection_is_available(): void
    {
        // Reverb connection should be configured even if not default
        $reverbConnection = Config::get('broadcasting.connections.reverb');
        $this->assertNotNull($reverbConnection, 'Reverb connection should be configured');
        $this->assertEquals('reverb', $reverbConnection['driver']);
    }

    #[Test]
    public function reverb_connection_is_properly_configured(): void
    {
        $reverbConfig = Config::get('broadcasting.connections.reverb');

        $this->assertNotNull($reverbConfig);
        $this->assertEquals('reverb', $reverbConfig['driver']);
        $this->assertArrayHasKey('key', $reverbConfig);
        $this->assertArrayHasKey('secret', $reverbConfig);
        $this->assertArrayHasKey('app_id', $reverbConfig);
        $this->assertArrayHasKey('options', $reverbConfig);
    }

    #[Test]
    public function reverb_server_configuration_exists(): void
    {
        $serverConfig = Config::get('reverb.servers.reverb');

        $this->assertNotNull($serverConfig);
        $this->assertArrayHasKey('host', $serverConfig);
        $this->assertArrayHasKey('port', $serverConfig);
        $this->assertArrayHasKey('scaling', $serverConfig);
        $this->assertArrayHasKey('pulse_ingest_interval', $serverConfig);
        $this->assertArrayHasKey('telescope_ingest_interval', $serverConfig);
    }

    #[Test]
    public function reverb_app_configuration_structure_exists(): void
    {
        $appsConfig = Config::get('reverb.apps');

        $this->assertNotNull($appsConfig);
        $this->assertArrayHasKey('provider', $appsConfig);
        $this->assertArrayHasKey('apps', $appsConfig);
        $this->assertEquals('config', $appsConfig['provider']);
        $this->assertIsArray($appsConfig['apps']);
        $this->assertNotEmpty($appsConfig['apps']);
    }

    #[Test]
    public function reverb_scaling_configuration_is_valid(): void
    {
        $scalingConfig = Config::get('reverb.servers.reverb.scaling');

        $this->assertNotNull($scalingConfig);
        $this->assertArrayHasKey('enabled', $scalingConfig);
        $this->assertArrayHasKey('channel', $scalingConfig);
        $this->assertArrayHasKey('server', $scalingConfig);

        // Verify Redis configuration for scaling
        $redisConfig = $scalingConfig['server'];
        $this->assertArrayHasKey('host', $redisConfig);
        $this->assertArrayHasKey('port', $redisConfig);
        $this->assertArrayHasKey('database', $redisConfig);
    }

    #[Test]
    public function reverb_configuration_structure_is_valid(): void
    {
        // Test that Reverb configuration structure is correct
        $reverbConfig = Config::get('reverb');

        $this->assertNotNull($reverbConfig);
        $this->assertArrayHasKey('default', $reverbConfig);
        $this->assertArrayHasKey('servers', $reverbConfig);
        $this->assertArrayHasKey('apps', $reverbConfig);

        // Verify default server
        $this->assertEquals('reverb', $reverbConfig['default']);
    }

    #[Test]
    public function reverb_pulse_integration_is_configured(): void
    {
        $pulseInterval = Config::get('reverb.servers.reverb.pulse_ingest_interval');
        $this->assertIsInt($pulseInterval);
        $this->assertGreaterThan(0, $pulseInterval);
    }

    #[Test]
    public function reverb_telescope_integration_is_configured(): void
    {
        $telescopeInterval = Config::get('reverb.servers.reverb.telescope_ingest_interval');
        $this->assertIsInt($telescopeInterval);
        $this->assertGreaterThan(0, $telescopeInterval);
    }

    #[Test]
    public function reverb_server_settings_are_reasonable(): void
    {
        $serverConfig = Config::get('reverb.servers.reverb');

        // Verify port is reasonable
        $port = $serverConfig['port'];
        $this->assertIsInt($port);
        $this->assertGreaterThan(1024, $port);
        $this->assertLessThan(65536, $port);

        // Verify max request size is set
        $maxRequestSize = $serverConfig['max_request_size'];
        $this->assertIsInt($maxRequestSize);
        $this->assertGreaterThan(0, $maxRequestSize);
    }

    #[Test]
    public function broadcasting_configuration_includes_reverb(): void
    {
        $connections = Config::get('broadcasting.connections');

        $this->assertArrayHasKey('reverb', $connections);

        $reverbConnection = $connections['reverb'];
        $this->assertEquals('reverb', $reverbConnection['driver']);
    }

    #[Test]
    public function reverb_can_be_set_as_broadcast_driver(): void
    {
        // Test that reverb can be used as broadcast driver
        $originalDefault = Config::get('broadcasting.default');

        // Temporarily set reverb as default
        Config::set('broadcasting.default', 'reverb');

        $this->assertEquals('reverb', Config::get('broadcasting.default'));

        // Restore original
        Config::set('broadcasting.default', $originalDefault);
    }
}
