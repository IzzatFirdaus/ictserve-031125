<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\PercyService;
use Illuminate\Console\Command;

/**
 * Artisan Command: Validate Percy Configuration
 * 
 * This command validates Percy visual testing configuration
 * for ICTServe v3.6.1 integration.
 */
class ValidatePercyConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'percy:validate-config 
                            {--show-config : Show current Percy configuration}
                            {--environment= : Validate for specific environment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate Percy visual testing configuration for ICTServe v3.6.1';

    /**
     * Execute the console command.
     */
    public function handle(PercyService $percyService): int
    {
        $this->info('🎭 Validating Percy Configuration for ICTServe v3.6.1');
        $this->newLine();

        // Show environment info
        $environment = $this->option('environment') ?? app()->environment();
        $this->info("Environment: {$environment}");
        $this->newLine();

        // Validate configuration
        $errors = $percyService->validateConfiguration();

        if (empty($errors)) {
            $this->info('✅ Percy configuration is valid!');

            // Show Percy status
            $enabled = $percyService->isEnabled();
            $status = $enabled ? '🟢 Enabled' : '🔴 Disabled';
            $this->info("Percy Status: {$status}");

            // Show configuration if requested
            if ($this->option('show-config')) {
                $this->showConfiguration($percyService);
            }

            return Command::SUCCESS;
        }

        // Show validation errors
        $this->error('❌ Percy configuration validation failed:');
        $this->newLine();

        foreach ($errors as $error) {
            $this->error("  • {$error}");
        }

        $this->newLine();
        $this->info('💡 Setup Instructions:');
        $this->info('  1. Set PERCY_TOKEN in your .env file');
        $this->info('  2. Set PERCY_PROJECT in your .env file');
        $this->info('  3. Set PERCY_ENABLED=true to enable Percy');
        $this->info('  4. Run: php artisan percy:validate-config --show-config');

        return Command::FAILURE;
    }

    /**
     * Show current Percy configuration
     */
    private function showConfiguration(PercyService $percyService): void
    {
        $this->newLine();
        $this->info('📋 Current Percy Configuration:');
        $this->newLine();

        $config = $percyService->getConfiguration();

        // Basic configuration
        $this->table(
            ['Setting', 'Value'],
            [
                ['Project', $config['project'] ?? 'Not set'],
                ['Enabled', $config['enabled'] ? 'Yes' : 'No'],
                ['Branch', $config['branch'] ?? 'Not set'],
                ['Target Branch', $config['target_branch'] ?? 'Not set'],
                ['Token', !empty($config['token']) ? 'Set (hidden)' : 'Not set'],
            ]
        );

        // Snapshot configuration
        $snapshotConfig = $percyService->getSnapshotConfig();
        $this->newLine();
        $this->info('📸 Snapshot Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Widths', implode(', ', $snapshotConfig['widths'])],
                ['Min Height', $snapshotConfig['minHeight']],
                ['JavaScript', $snapshotConfig['enableJavaScript'] ? 'Enabled' : 'Disabled'],
                ['Wait Timeout', $snapshotConfig['waitForTimeout'] . 'ms'],
            ]
        );

        // ICTServe specific configuration
        $hybridConfig = $percyService->getHybridArchitectureSelectors();
        $bahasaConfig = $percyService->getBahasaMelayuConfig();
        $accessibilityConfig = $percyService->getAccessibilityConfig();

        $this->newLine();
        $this->info('🏗️ ICTServe v3.6.1 Configuration:');
        $this->table(
            ['Feature', 'Status'],
            [
                ['Hybrid Architecture', 'Configured'],
                ['Bahasa Melayu Interface', $bahasaConfig['validate_language'] ? 'Enabled' : 'Disabled'],
                ['WCAG ' . $accessibilityConfig['wcag_version'] . ' ' . $accessibilityConfig['wcag_level'], 'Enabled'],
                ['Guest Selectors', count($hybridConfig['guest_selectors']) . ' configured'],
                ['Auth Selectors', count($hybridConfig['authenticated_selectors']) . ' configured'],
                ['Admin Selectors', count($hybridConfig['admin_selectors']) . ' configured'],
            ]
        );

        // Technology stack
        $techStack = $percyService->getTechnologyStackInfo();
        $this->newLine();
        $this->info('🛠️ Technology Stack:');
        $this->table(
            ['Technology', 'Version'],
            [
                ['Laravel', $techStack['laravel']],
                ['Livewire', $techStack['livewire']],
                ['Filament', $techStack['filament']],
                ['Playwright', $techStack['playwright']],
                ['Tailwind CSS', $techStack['tailwind']],
            ]
        );

        // Environment-specific configuration files
        $envConfigs = $percyService->getAvailableEnvironmentConfigs();
        $this->newLine();
        $this->info('📁 Environment-Specific Configuration Files:');

        $envConfigTable = [];
        foreach ($envConfigs as $env => $config) {
            $status = $config['exists'] ? '✅ Exists' : '❌ Missing';
            $size = $config['exists'] ? number_format($config['size']) . ' bytes' : 'N/A';
            $modified = $config['exists'] && $config['modified'] ? date('Y-m-d H:i:s', $config['modified']) : 'N/A';

            $envConfigTable[] = [
                ucfirst($env),
                $status,
                $size,
                $modified
            ];
        }

        $this->table(
            ['Environment', 'Status', 'Size', 'Last Modified'],
            $envConfigTable
        );

        $this->newLine();
        $this->info('💡 Environment Configuration Info:');
        $this->info('  • Environment-specific files override main configuration');
        $this->info('  • Files are located in config/percy.{environment}.php');
        $this->info('  • Current environment: ' . app()->environment());

        $currentEnvFile = config_path('percy.' . app()->environment() . '.php');
        if (file_exists($currentEnvFile)) {
            $this->info('  • ✅ Current environment config file exists');
        } else {
            $this->info('  • ❌ No environment-specific config for current environment');
        }
    }
}
