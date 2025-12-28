<?php

declare(strict_types=1);

namespace Tests\Feature\Percy;

use App\Services\PercyService;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Percy Configuration Test
 *
 * Tests Percy configuration management system for ICTServe v3.6.1
 */
class PercyConfigurationTest extends TestCase
{
    private PercyService $percyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->percyService = app(PercyService::class);
    }

    #[Test]
    public function percy_configuration_loads_correctly(): void
    {
        $config = Config::get('percy');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('project', $config);
        $this->assertArrayHasKey('enabled', $config);
        $this->assertArrayHasKey('snapshot', $config);
        $this->assertArrayHasKey('ictserve', $config);
        $this->assertArrayHasKey('environments', $config);
        $this->assertArrayHasKey('messages', $config);
    }

    #[Test]
    public function percy_service_validates_configuration(): void
    {
        // Test with missing token (should have errors)
        Config::set('percy.token', null);
        $errors = $this->percyService->validateConfiguration();

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Token Percy tidak ditemui', $errors[0]);
    }

    #[Test]
    public function percy_service_validates_configuration_with_token(): void
    {
        // Test with valid token
        Config::set('percy.token', 'test_token_123');
        Config::set('percy.project', 'test_project');

        $errors = $this->percyService->validateConfiguration();

        // Should have no errors with valid token and project
        $this->assertEmpty($errors);
    }

    #[Test]
    public function percy_service_gets_environment_configuration(): void
    {
        $config = $this->percyService->getConfiguration();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('enabled', $config);
        $this->assertArrayHasKey('project', $config);
    }

    #[Test]
    public function percy_service_gets_snapshot_configuration(): void
    {
        $snapshotConfig = $this->percyService->getSnapshotConfig();

        $this->assertIsArray($snapshotConfig);
        $this->assertArrayHasKey('widths', $snapshotConfig);
        $this->assertArrayHasKey('minHeight', $snapshotConfig);
        $this->assertArrayHasKey('enableJavaScript', $snapshotConfig);
        $this->assertArrayHasKey('waitForTimeout', $snapshotConfig);

        // Test with overrides
        $overrides = ['widths' => [768, 1024]];
        $configWithOverrides = $this->percyService->getSnapshotConfig($overrides);

        $this->assertEquals([768, 1024], $configWithOverrides['widths']);
    }

    #[Test]
    public function percy_service_gets_hybrid_architecture_selectors(): void
    {
        $selectors = $this->percyService->getHybridArchitectureSelectors();

        $this->assertIsArray($selectors);
        $this->assertArrayHasKey('guest_selectors', $selectors);
        $this->assertArrayHasKey('authenticated_selectors', $selectors);
        $this->assertArrayHasKey('admin_selectors', $selectors);

        $this->assertContains('.guest-form', $selectors['guest_selectors']);
        $this->assertContains('.dashboard', $selectors['authenticated_selectors']);
        $this->assertContains('.filament-admin', $selectors['admin_selectors']);
    }

    #[Test]
    public function percy_service_gets_bahasa_melayu_config(): void
    {
        $config = $this->percyService->getBahasaMelayuConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('validate_language', $config);
        $this->assertArrayHasKey('exclude_language_switcher', $config);
        $this->assertArrayHasKey('interface_version', $config);

        $this->assertTrue($config['validate_language']);
        $this->assertTrue($config['exclude_language_switcher']);
        $this->assertEquals('3.6.0+', $config['interface_version']);
    }

    #[Test]
    public function percy_service_gets_accessibility_config(): void
    {
        $config = $this->percyService->getAccessibilityConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('wcag_level', $config);
        $this->assertArrayHasKey('wcag_version', $config);
        $this->assertArrayHasKey('validate_contrast', $config);
        $this->assertArrayHasKey('validate_focus_indicators', $config);

        $this->assertEquals('AA', $config['wcag_level']);
        $this->assertEquals('2.2', $config['wcag_version']);
    }

    #[Test]
    public function percy_service_gets_technology_stack_info(): void
    {
        $techStack = $this->percyService->getTechnologyStackInfo();

        $this->assertIsArray($techStack);
        $this->assertArrayHasKey('laravel', $techStack);
        $this->assertArrayHasKey('livewire', $techStack);
        $this->assertArrayHasKey('filament', $techStack);
        $this->assertArrayHasKey('playwright', $techStack);
        $this->assertArrayHasKey('tailwind', $techStack);

        $this->assertEquals('12.43.1', $techStack['laravel']);
        $this->assertEquals('3.7.3', $techStack['livewire']);
        $this->assertEquals('4.3.1', $techStack['filament']);
        $this->assertEquals('1.56.1', $techStack['playwright']);
        $this->assertEquals('4.1.18', $techStack['tailwind']);
    }

    #[Test]
    public function percy_service_generates_build_name(): void
    {
        Config::set('percy.project', 'test-project');

        $buildName = $this->percyService->generateBuildName();

        $this->assertStringContainsString('test-project', $buildName);
        $this->assertStringContainsString('testing', $buildName); // Current environment

        // Test with suffix
        $buildNameWithSuffix = $this->percyService->generateBuildName('feature-branch');
        $this->assertStringContainsString('feature-branch', $buildNameWithSuffix);
    }

    #[Test]
    public function percy_service_handles_errors_gracefully(): void
    {
        $exception = new \RuntimeException('Test error');

        // Should not throw exception with graceful degradation enabled
        Config::set('percy.error_handling.graceful_degradation', true);

        $this->expectNotToPerformAssertions();
        $this->percyService->handleError('test_operation', $exception);
    }

    #[Test]
    public function percy_service_gets_available_environment_configs(): void
    {
        $envConfigs = $this->percyService->getAvailableEnvironmentConfigs();

        $this->assertIsArray($envConfigs);
        $this->assertArrayHasKey('local', $envConfigs);
        $this->assertArrayHasKey('testing', $envConfigs);
        $this->assertArrayHasKey('staging', $envConfigs);
        $this->assertArrayHasKey('production', $envConfigs);

        // Check that local config exists (we created it)
        $this->assertTrue($envConfigs['local']['exists']);
        $this->assertTrue($envConfigs['local']['readable']);
        $this->assertGreaterThan(0, $envConfigs['local']['size']);
    }

    #[Test]
    public function environment_specific_configuration_overrides(): void
    {
        // Create a temporary environment config for testing
        $testEnvConfig = [
            'enabled' => false,
            'debug' => true,
            'snapshot' => [
                'widths' => [768, 1024],
                'min_height' => 600,
            ],
        ];

        $tempConfigFile = config_path('percy.testing.php');
        $originalContent = file_exists($tempConfigFile) ? file_get_contents($tempConfigFile) : null;

        try {
            // Write test config
            file_put_contents($tempConfigFile, '<?php return '.var_export($testEnvConfig, true).';');

            // Get configuration (should include overrides)
            $config = $this->percyService->getConfiguration();

            $this->assertFalse($config['enabled']);
            $this->assertTrue($config['debug']);
            $this->assertEquals([768, 1024], $config['snapshot']['widths']);
            $this->assertEquals(600, $config['snapshot']['min_height']);
        } finally {
            // Restore original config
            if ($originalContent !== null) {
                file_put_contents($tempConfigFile, $originalContent);
            }
        }
    }

    #[Test]
    public function bahasa_melayu_error_messages(): void
    {
        $messages = Config::get('percy.messages');

        $this->assertIsArray($messages);
        $this->assertArrayHasKey('token_missing', $messages);
        $this->assertArrayHasKey('token_invalid', $messages);
        $this->assertArrayHasKey('project_missing', $messages);
        $this->assertArrayHasKey('config_invalid', $messages);
        $this->assertArrayHasKey('service_unavailable', $messages);
        $this->assertArrayHasKey('upload_failed', $messages);
        $this->assertArrayHasKey('build_failed', $messages);

        // Verify messages are in Bahasa Melayu
        $this->assertStringContainsString('Token Percy tidak ditemui', $messages['token_missing']);
        $this->assertStringContainsString('Sila tetapkan PERCY_TOKEN', $messages['token_missing']);
        $this->assertStringContainsString('tidak sah', $messages['token_invalid']);
        $this->assertStringContainsString('Nama projek Percy', $messages['project_missing']);
    }
}
