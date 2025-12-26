<?php

declare(strict_types=1);

namespace App\Console\Commands\Percy;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;

/**
 * Percy Status Command
 *
 * Provides comprehensive status information for Percy visual testing
 * integration in ICTServe v3.6.1 application.
 *
 * @version 3.6.1
 */
class StatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'percy:check-status 
                            {--detailed : Show detailed status information}
                            {--builds : Show recent builds status}
                            {--config : Show configuration status}
                            {--services : Check Percy services status}
                            {--json : Output in JSON format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Percy visual testing status for ICTServe v3.6.1';

    /**
     * Percy API base URL
     */
    private const PERCY_API_BASE = 'https://percy.io/api/v1';

    /**
     * Status information
     */
    private array $statusInfo = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('json')) {
            return $this->handleJsonOutput();
        }

        $this->info('📊 Status Percy untuk ICTServe v3.6.1');
        $this->newLine();

        // Check configuration status
        if ($this->option('config') || ! $this->hasSpecificOptions()) {
            $this->checkConfigurationStatus();
        }

        // Check services status
        if ($this->option('services') || ! $this->hasSpecificOptions()) {
            $this->checkServicesStatus();
        }

        // Check builds status
        if ($this->option('builds') || ! $this->hasSpecificOptions()) {
            $this->checkBuildsStatus();
        }

        // Show detailed information
        if ($this->option('detailed')) {
            $this->showDetailedStatus();
        }

        // Display summary
        $this->displayStatusSummary();

        return 0;
    }

    /**
     * Handle JSON output
     */
    private function handleJsonOutput(): int
    {
        $this->gatherAllStatusInfo();
        $this->line(json_encode($this->statusInfo, JSON_PRETTY_PRINT));

        return 0;
    }

    /**
     * Check if specific options are provided
     */
    private function hasSpecificOptions(): bool
    {
        return $this->option('config') || $this->option('services') || $this->option('builds');
    }

    /**
     * Check configuration status
     */
    private function checkConfigurationStatus(): void
    {
        $this->info('⚙️ Status Konfigurasi:');

        $configStatus = [
            'valid' => true,
            'issues' => [],
            'details' => [],
        ];

        // Check Percy token
        $token = Config::get('percy.token');
        if (! $token) {
            $configStatus['valid'] = false;
            $configStatus['issues'][] = 'Token Percy tidak ditemui';
            $this->error('   ❌ Token Percy: Tidak ditemui');
        } else {
            $configStatus['details']['token'] = 'Ditetapkan';
            $this->info('   ✅ Token Percy: Ditetapkan');
        }

        // Check project configuration
        $project = Config::get('percy.project');
        if (! $project) {
            $configStatus['valid'] = false;
            $configStatus['issues'][] = 'Nama projek tidak ditemui';
            $this->error('   ❌ Projek: Tidak ditemui');
        } else {
            $configStatus['details']['project'] = $project;
            $this->info("   ✅ Projek: {$project}");
        }

        // Check if Percy is enabled
        $enabled = Config::get('percy.enabled', false);
        $configStatus['details']['enabled'] = $enabled;
        if ($enabled) {
            $this->info('   ✅ Status: Diaktifkan');
        } else {
            $this->warn('   ⚠️ Status: Dimatikan');
        }

        // Check environment configuration
        $environment = app()->environment();
        $envConfig = Config::get("percy.environments.{$environment}");
        if ($envConfig) {
            $configStatus['details']['environment'] = $environment;
            $this->info("   ✅ Persekitaran: {$environment}");
        } else {
            $configStatus['issues'][] = "Konfigurasi persekitaran '{$environment}' tidak ditemui";
            $this->warn("   ⚠️ Persekitaran: {$environment} (tiada konfigurasi khusus)");
        }

        // Check ICTServe-specific configuration
        $ictserveConfig = Config::get('percy.ictserve');
        if ($ictserveConfig) {
            $configStatus['details']['ictserve_config'] = true;
            $this->info('   ✅ Konfigurasi ICTServe: Ditemui');

            // Check hybrid architecture
            if (isset($ictserveConfig['hybrid_architecture'])) {
                $this->info('   ✅ True Hybrid Architecture: Dikonfigurasi');
            }

            // Check Bahasa Melayu support
            if (isset($ictserveConfig['bahasa_melayu'])) {
                $this->info('   ✅ Antara Muka Bahasa Melayu: Dikonfigurasi');
            }
        } else {
            $configStatus['issues'][] = 'Konfigurasi khusus ICTServe tidak ditemui';
            $this->warn('   ⚠️ Konfigurasi ICTServe: Tidak ditemui');
        }

        $this->statusInfo['configuration'] = $configStatus;
        $this->newLine();
    }

    /**
     * Check services status
     */
    private function checkServicesStatus(): void
    {
        $this->info('🌐 Status Perkhidmatan:');

        $servicesStatus = [
            'percy_api' => $this->checkPercyApiStatus(),
            'percy_cli' => $this->checkPercyCliStatus(),
            'node_modules' => $this->checkNodeModulesStatus(),
        ];

        $this->statusInfo['services'] = $servicesStatus;
        $this->newLine();
    }

    /**
     * Check Percy API status
     */
    private function checkPercyApiStatus(): array
    {
        try {
            $token = Config::get('percy.token');
            if (! $token) {
                $this->error('   ❌ Percy API: Token tidak ditemui');

                return ['status' => 'error', 'message' => 'Token tidak ditemui'];
            }

            $response = Http::withHeaders([
                'Authorization' => "Token {$token}",
                'User-Agent' => 'ICTServe-v3.6.1-Percy-Integration',
            ])->timeout(10)->get(self::PERCY_API_BASE.'/user');

            if ($response->successful()) {
                $user = $response->json();
                $userName = $user['data']['attributes']['name'] ?? 'Unknown';
                $this->info("   ✅ Percy API: Berhubung ({$userName})");

                return ['status' => 'connected', 'user' => $userName];
            } else {
                $this->error('   ❌ Percy API: Gagal pengesahan');

                return ['status' => 'auth_failed', 'code' => $response->status()];
            }
        } catch (Exception $e) {
            $this->error('   ❌ Percy API: Tidak dapat dihubungi');

            return ['status' => 'unreachable', 'error' => $e->getMessage()];
        }
    }

    /**
     * Check Percy CLI status
     */
    private function checkPercyCliStatus(): array
    {
        try {
            $result = Process::run('npx percy --version');
            if ($result->successful()) {
                $version = trim($result->output());
                $this->info("   ✅ Percy CLI: {$version}");

                return ['status' => 'available', 'version' => $version];
            } else {
                $this->error('   ❌ Percy CLI: Tidak ditemui');

                return ['status' => 'not_found'];
            }
        } catch (Exception $e) {
            $this->error('   ❌ Percy CLI: Ralat memeriksa');

            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    /**
     * Check Node modules status
     */
    private function checkNodeModulesStatus(): array
    {
        $requiredPackages = [
            '@percy/cli',
            '@percy/playwright',
        ];

        $packageStatus = [];
        $allAvailable = true;

        foreach ($requiredPackages as $package) {
            if (file_exists(base_path("node_modules/{$package}/package.json"))) {
                $packageJson = json_decode(file_get_contents(base_path("node_modules/{$package}/package.json")), true);
                $version = $packageJson['version'] ?? 'unknown';
                $packageStatus[$package] = ['status' => 'installed', 'version' => $version];
                $this->info("   ✅ {$package}: {$version}");
            } else {
                $packageStatus[$package] = ['status' => 'missing'];
                $this->error("   ❌ {$package}: Tidak dipasang");
                $allAvailable = false;
            }
        }

        return [
            'status' => $allAvailable ? 'complete' : 'incomplete',
            'packages' => $packageStatus,
        ];
    }

    /**
     * Check builds status
     */
    private function checkBuildsStatus(): void
    {
        $this->info('🏗️ Status Build:');

        $token = Config::get('percy.token');
        if (! $token) {
            $this->error('   ❌ Tidak dapat memeriksa build: Token tidak ditemui');
            $this->statusInfo['builds'] = ['status' => 'unavailable', 'reason' => 'no_token'];

            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Token {$token}",
            ])->get(self::PERCY_API_BASE.'/builds', [
                'project' => Config::get('percy.project'),
                'limit' => 5,
            ]);

            if ($response->successful()) {
                $builds = $response->json()['data'];

                if (empty($builds)) {
                    $this->info('   ℹ️ Tiada build ditemui');
                    $this->statusInfo['builds'] = ['status' => 'empty', 'count' => 0];
                } else {
                    $this->info("   📊 {count($builds)} build terkini:");

                    $buildsSummary = [];
                    foreach (array_slice($builds, 0, 3) as $build) {
                        $attributes = $build['attributes'];
                        $state = $this->formatBuildState($attributes['state']);
                        $buildNumber = $attributes['build-number'];
                        $branch = $attributes['branch'];

                        $this->info("      • Build #{$buildNumber} ({$branch}): {$state}");
                        $buildsSummary[] = [
                            'number' => $buildNumber,
                            'branch' => $branch,
                            'state' => $attributes['state'],
                            'snapshots' => $attributes['total-snapshots'] ?? 0,
                        ];
                    }

                    $this->statusInfo['builds'] = [
                        'status' => 'available',
                        'count' => count($builds),
                        'recent' => $buildsSummary,
                    ];
                }
            } else {
                $this->error('   ❌ Gagal mendapatkan status build');
                $this->statusInfo['builds'] = ['status' => 'error', 'code' => $response->status()];
            }
        } catch (Exception $e) {
            $this->error('   ❌ Ralat memeriksa build: '.$e->getMessage());
            $this->statusInfo['builds'] = ['status' => 'error', 'error' => $e->getMessage()];
        }

        $this->newLine();
    }

    /**
     * Show detailed status information
     */
    private function showDetailedStatus(): void
    {
        $this->info('📋 Maklumat Terperinci:');
        $this->newLine();

        // System information
        $this->info('🖥️ Maklumat Sistem:');
        $this->info('   • Versi Laravel: '.app()->version());
        $this->info('   • Versi PHP: '.PHP_VERSION);
        $this->info('   • Persekitaran: '.app()->environment());
        $this->info('   • Versi ICTServe: 3.6.1');
        $this->newLine();

        // Technology stack
        $techStack = Config::get('percy.ictserve.technology_stack', []);
        if (! empty($techStack)) {
            $this->info('⚙️ Teknologi Stack:');
            foreach ($techStack as $tech => $version) {
                $this->info("   • {$tech}: {$version}");
            }
            $this->newLine();
        }

        // Percy configuration details
        $this->info('🔧 Konfigurasi Percy:');
        $this->info('   • Lebar Skrin: '.implode(', ', Config::get('percy.snapshot.widths', [])));
        $this->info('   • Ketinggian Minimum: '.Config::get('percy.snapshot.min_height', 'N/A'));
        $this->info('   • JavaScript: '.(Config::get('percy.snapshot.enable_javascript', false) ? 'Diaktifkan' : 'Dimatikan'));
        $this->info('   • Timeout Rangkaian: '.Config::get('percy.snapshot.network_idle_timeout', 'N/A').'ms');
        $this->newLine();

        // Performance settings
        $performance = Config::get('percy.performance', []);
        if (! empty($performance)) {
            $this->info('🚀 Tetapan Prestasi:');
            $this->info('   • Upload Async: '.($performance['async_upload'] ?? false ? 'Ya' : 'Tidak'));
            $this->info('   • Cache: '.($performance['cache_enabled'] ?? false ? 'Diaktifkan' : 'Dimatikan'));
            $this->info('   • Upload Serentak: '.($performance['max_concurrent_uploads'] ?? 'N/A'));
            $this->newLine();
        }
    }

    /**
     * Display status summary
     */
    private function displayStatusSummary(): void
    {
        $this->info('📊 Ringkasan Status:');
        $this->newLine();

        $configValid = $this->statusInfo['configuration']['valid'] ?? false;
        $servicesOk = $this->areServicesHealthy();
        $buildsAvailable = isset($this->statusInfo['builds']) && $this->statusInfo['builds']['status'] !== 'error';

        if ($configValid && $servicesOk && $buildsAvailable) {
            $this->info('🎉 Percy visual testing untuk ICTServe v3.6.1 berfungsi dengan baik!');
            $this->info('Anda boleh menjalankan ujian visual dengan:');
            $this->info('   • php artisan percy:dusk');
            $this->info('   • npx percy exec -- npm run test:e2e');
        } elseif ($configValid && $servicesOk) {
            $this->info('✅ Percy dikonfigurasi dengan betul dan perkhidmatan tersedia.');
            $this->info('Anda boleh mula menjalankan ujian visual.');
        } else {
            $this->warn('⚠️ Percy memerlukan konfigurasi tambahan:');

            if (! $configValid) {
                $this->warn('   • Jalankan: php artisan percy:validate-config --fix');
            }

            if (! $servicesOk) {
                $this->warn('   • Pasang dependencies: npm install --save-dev @percy/cli @percy/playwright');
            }
        }
    }

    /**
     * Check if services are healthy
     */
    private function areServicesHealthy(): bool
    {
        $services = $this->statusInfo['services'] ?? [];

        return ($services['percy_api']['status'] ?? '') === 'connected' &&
            ($services['percy_cli']['status'] ?? '') === 'available' &&
            ($services['node_modules']['status'] ?? '') === 'complete';
    }

    /**
     * Gather all status information for JSON output
     */
    private function gatherAllStatusInfo(): void
    {
        $this->checkConfigurationStatus();
        $this->checkServicesStatus();
        $this->checkBuildsStatus();

        $this->statusInfo['summary'] = [
            'configuration_valid' => $this->statusInfo['configuration']['valid'] ?? false,
            'services_healthy' => $this->areServicesHealthy(),
            'builds_available' => isset($this->statusInfo['builds']) && $this->statusInfo['builds']['status'] !== 'error',
            'overall_status' => $this->getOverallStatus(),
            'timestamp' => now()->toISOString(),
            'ictserve_version' => '3.6.1',
        ];
    }

    /**
     * Get overall status
     */
    private function getOverallStatus(): string
    {
        $configValid = $this->statusInfo['configuration']['valid'] ?? false;
        $servicesOk = $this->areServicesHealthy();

        if ($configValid && $servicesOk) {
            return 'healthy';
        } elseif ($configValid || $servicesOk) {
            return 'partial';
        } else {
            return 'unhealthy';
        }
    }

    /**
     * Format build state for display
     */
    private function formatBuildState(string $state): string
    {
        return match ($state) {
            'pending' => '⏳ Menunggu',
            'processing' => '🔄 Memproses',
            'finished' => '✅ Selesai',
            'failed' => '❌ Gagal',
            'expired' => '⏰ Tamat Tempoh',
            default => $state,
        };
    }
}
