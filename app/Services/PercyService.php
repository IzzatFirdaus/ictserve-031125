<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Percy Visual Testing Service for ICTServe v3.6.1
 * 
 * This service provides integration with Percy visual testing platform,
 * supporting the ICTServe True Hybrid Architecture and Bahasa Melayu interface.
 * 
 * @package App\Services
 * @version 3.6.1
 */
class PercyService
{
    /**
     * Check if Percy is enabled and properly configured
     */
    public function isEnabled(): bool
    {
        $config = $this->getConfiguration();
        $token = Config::get('percy.token');

        // Get the final enabled status from merged configuration
        $enabled = $config['enabled'] ?? false;

        return $enabled && !empty($token);
    }

    /**
     * Validate Percy configuration
     * 
     * @throws RuntimeException
     */
    public function validateConfiguration(): array
    {
        $errors = [];
        $config = $this->getConfiguration();

        // Check required configuration - get token directly from config
        $token = Config::get('percy.token');
        if (empty($token)) {
            $errors[] = Config::get('percy.messages.token_missing', 'Percy token is missing');
        }

        $project = Config::get('percy.project');
        if (empty($project)) {
            $errors[] = Config::get('percy.messages.project_missing', 'Percy project name is missing');
        }

        // Validate widths configuration
        $widths = $config['snapshot']['widths'] ?? Config::get('percy.snapshot.widths', []);
        if (empty($widths) || !\is_array($widths)) {
            $errors[] = 'Percy snapshot widths must be a non-empty array';
        }

        // Validate environment-specific configuration file if it exists
        $environment = app()->environment();
        $envConfigFile = config_path("percy.{$environment}.php");

        if (file_exists($envConfigFile)) {
            $envErrors = $this->validateEnvironmentConfigFile($envConfigFile);
            $errors = [...$errors, ...$envErrors];
        }

        // Log validation results
        if (!empty($errors)) {
            Log::warning('Percy configuration validation failed', [
                'errors' => $errors,
                'environment' => $environment,
                'config_file_exists' => file_exists($envConfigFile),
            ]);
        } else {
            Log::info('Percy configuration validation passed', [
                'project' => $config['project'] ?? Config::get('percy.project'),
                'environment' => $environment,
                'config_file_exists' => file_exists($envConfigFile),
            ]);
        }

        return $errors;
    }

    /**
     * Get Percy configuration for the current environment
     */
    public function getConfiguration(): array
    {
        $environment = app()->environment();
        $baseConfig = Config::get('percy', []);

        // Load environment-specific configuration file if it exists
        $envConfigFile = config_path("percy.{$environment}.php");
        $envFileConfig = [];

        if (file_exists($envConfigFile)) {
            $envFileConfig = require $envConfigFile;
            Log::info("Loaded Percy environment-specific configuration", [
                'environment' => $environment,
                'config_file' => $envConfigFile,
            ]);
        }

        // Get environment-specific settings from main config
        $envConfig = Config::get("percy.environments.{$environment}", []);

        // Merge configurations: base -> env settings -> env file (env file has highest priority)
        return [...$baseConfig, ...$envConfig, ...$envFileConfig];
    }

    /**
     * Get snapshot configuration for ICTServe v3.6.1
     */
    public function getSnapshotConfig(array $overrides = []): array
    {
        $config = $this->getConfiguration();

        $baseConfig = [
            'widths' => $config['snapshot']['widths'] ?? Config::get('percy.snapshot.widths', [375, 768, 1024, 1280, 1920]),
            'minHeight' => $config['snapshot']['min_height'] ?? Config::get('percy.snapshot.min_height', 1024),
            'percyCSS' => implode(' ', Config::get('percy.snapshot.percy_css', [])),
            'enableJavaScript' => $config['snapshot']['enable_javascript'] ?? Config::get('percy.snapshot.enable_javascript', true),
            'waitForTimeout' => $config['snapshot']['wait_for_timeout'] ?? Config::get('percy.snapshot.wait_for_timeout', 1000),
        ];

        return [...$baseConfig, ...$overrides];
    }

    /**
     * Get hybrid architecture specific selectors
     */
    public function getHybridArchitectureSelectors(): array
    {
        return Config::get('percy.ictserve.hybrid_architecture', [
            'guest_selectors' => ['.guest-form', '.guest-status', '.guest-workflow'],
            'authenticated_selectors' => ['.dashboard', '.profile', '.user-menu'],
            'admin_selectors' => ['.filament-admin', '.admin-panel', '.fi-sidebar'],
        ]);
    }

    /**
     * Get Bahasa Melayu interface configuration
     */
    public function getBahasaMelayuConfig(): array
    {
        return Config::get('percy.ictserve.bahasa_melayu', [
            'validate_language' => true,
            'exclude_language_switcher' => true,
            'interface_version' => '3.6.0+',
        ]);
    }

    /**
     * Get WCAG accessibility configuration
     */
    public function getAccessibilityConfig(): array
    {
        return Config::get('percy.ictserve.accessibility', [
            'wcag_level' => 'AA',
            'wcag_version' => '2.2',
            'validate_contrast' => true,
            'validate_focus_indicators' => true,
        ]);
    }

    /**
     * Handle Percy service errors gracefully
     */
    public function handleError(string $operation, \Throwable $exception): void
    {
        $gracefulDegradation = Config::get('percy.error_handling.graceful_degradation', true);
        $logErrors = Config::get('percy.error_handling.log_errors', true);

        if ($logErrors) {
            Log::warning("Percy {$operation} failed", [
                'error' => $exception->getMessage(),
                'operation' => $operation,
                'graceful_degradation' => $gracefulDegradation,
            ]);
        }

        if (!$gracefulDegradation) {
            throw new RuntimeException("Percy {$operation} failed: " . $exception->getMessage(), 0, $exception);
        }

        // Log graceful degradation
        Log::info("Percy {$operation} failed, continuing without visual testing", [
            'operation' => $operation,
        ]);
    }

    /**
     * Get technology stack information for Percy metadata
     */
    public function getTechnologyStackInfo(): array
    {
        return Config::get('percy.ictserve.technology_stack', [
            'laravel' => '12.43.1',
            'livewire' => '3.7.3',
            'filament' => '4.3.1',
            'playwright' => '1.56.1',
            'tailwind' => '4.1.18',
        ]);
    }

    /**
     * Generate Percy build name with ICTServe context
     */
    public function generateBuildName(string $suffix = ''): string
    {
        $project = Config::get('percy.project', 'ictserve-visual-testing');
        $environment = app()->environment();
        $timestamp = now()->format('Y-m-d-H-i-s');

        $buildName = "{$project}-{$environment}-{$timestamp}";

        if (!empty($suffix)) {
            $buildName .= "-{$suffix}";
        }

        return $buildName;
    }

    /**
     * Validate environment-specific configuration file
     */
    private function validateEnvironmentConfigFile(string $configFile): array
    {
        $errors = [];

        try {
            $config = require $configFile;

            if (!\is_array($config)) {
                $errors[] = "Environment configuration file must return an array: {$configFile}";
                return $errors;
            }

            // Validate snapshot configuration if present
            if (isset($config['snapshot'])) {
                if (isset($config['snapshot']['widths']) && !\is_array($config['snapshot']['widths'])) {
                    $errors[] = 'Environment config: snapshot.widths must be an array';
                }

                if (isset($config['snapshot']['min_height']) && !\is_int($config['snapshot']['min_height'])) {
                    $errors[] = 'Environment config: snapshot.min_height must be an integer';
                }
            }

            // Validate error handling configuration if present
            if (isset($config['error_handling'])) {
                $errorHandling = $config['error_handling'];

                if (isset($errorHandling['retry_attempts']) && (!\is_int($errorHandling['retry_attempts']) || $errorHandling['retry_attempts'] < 0)) {
                    $errors[] = 'Environment config: error_handling.retry_attempts must be a non-negative integer';
                }

                if (isset($errorHandling['timeout']) && (!\is_int($errorHandling['timeout']) || $errorHandling['timeout'] <= 0)) {
                    $errors[] = 'Environment config: error_handling.timeout must be a positive integer';
                }
            }

            // Validate performance configuration if present
            if (isset($config['performance'])) {
                $performance = $config['performance'];

                if (isset($performance['max_concurrent_uploads']) && (!\is_int($performance['max_concurrent_uploads']) || $performance['max_concurrent_uploads'] <= 0)) {
                    $errors[] = 'Environment config: performance.max_concurrent_uploads must be a positive integer';
                }

                if (isset($performance['cache_ttl']) && (!\is_int($performance['cache_ttl']) || $performance['cache_ttl'] < 0)) {
                    $errors[] = 'Environment config: performance.cache_ttl must be a non-negative integer';
                }
            }
        } catch (\Throwable $e) {
            $errors[] = "Failed to load environment configuration file {$configFile}: " . $e->getMessage();
        }

        return $errors;
    }

    /**
     * Get available environment-specific configuration files
     */
    public function getAvailableEnvironmentConfigs(): array
    {
        $configPath = config_path();
        $envConfigs = [];

        $environments = ['local', 'testing', 'staging', 'production'];

        foreach ($environments as $env) {
            $configFile = "{$configPath}/percy.{$env}.php";
            if (file_exists($configFile)) {
                $envConfigs[$env] = [
                    'file' => $configFile,
                    'exists' => true,
                    'readable' => is_readable($configFile),
                    'size' => filesize($configFile),
                    'modified' => filemtime($configFile),
                ];
            } else {
                $envConfigs[$env] = [
                    'file' => $configFile,
                    'exists' => false,
                    'readable' => false,
                    'size' => 0,
                    'modified' => null,
                ];
            }
        }

        return $envConfigs;
    }
}
