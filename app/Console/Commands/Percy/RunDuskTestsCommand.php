<?php

declare(strict_types=1);

namespace App\Console\Commands\Percy;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;

/**
 * Run Dusk Tests with Percy Command
 *
 * Executes Laravel Dusk tests with Percy visual testing integration
 * for ICTServe v3.6.1 application.
 *
 * @version 3.6.1
 */
class RunDuskTestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'percy:run-dusk 
                            {--without-percy : Run Dusk tests without Percy visual testing}
                            {--group= : Run specific test group}
                            {--filter= : Filter tests by name pattern}
                            {--parallel= : Number of parallel processes}
                            {--env= : Environment to use for testing}
                            {--debug : Enable debug mode}
                            {--build-name= : Custom Percy build name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Laravel Dusk tests with Percy visual testing for ICTServe v3.6.1';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Menjalankan ujian Dusk dengan Percy untuk ICTServe v3.6.1...');
        $this->newLine();

        // Check if Percy should be disabled
        $withoutPercy = $this->option('without-percy');

        if (! $withoutPercy) {
            // Validate Percy configuration first
            if (! $this->validatePercyConfiguration()) {
                return 1;
            }
        }

        // Prepare environment
        $this->prepareTestEnvironment();

        // Build Dusk command
        $command = $this->buildDuskCommand($withoutPercy);

        // Execute tests
        return $this->executeDuskTests($command, $withoutPercy);
    }

    /**
     * Validate Percy configuration before running tests
     */
    private function validatePercyConfiguration(): bool
    {
        $this->info('🔍 Memeriksa konfigurasi Percy...');

        // Check if Percy is enabled
        if (! Config::get('percy.enabled', false)) {
            $this->warn('Percy dimatikan dalam konfigurasi. Gunakan --without-percy untuk menjalankan ujian tanpa Percy.');

            return false;
        }

        // Check Percy token
        $token = Config::get('percy.token');
        if (! $token) {
            $this->error('Token Percy tidak ditemui. Sila tetapkan PERCY_TOKEN dalam pembolehubah persekitaran.');
            $this->info('Gunakan: php artisan percy:validate-config untuk maklumat lanjut.');

            return false;
        }

        // Check if Percy CLI is available
        try {
            $result = Process::run('npx percy --version');
            if (! $result->successful()) {
                $this->error('Percy CLI tidak ditemui. Sila pasang dengan: npm install --save-dev @percy/cli');

                return false;
            }

            $version = trim($result->output());
            $this->info("✅ Percy CLI ditemui: {$version}");
        } catch (Exception $e) {
            $this->error('Gagal memeriksa Percy CLI: '.$e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Prepare test environment
     */
    private function prepareTestEnvironment(): void
    {
        $this->info('⚙️ Menyediakan persekitaran ujian...');

        // Set environment variables
        $env = $this->option('env', 'testing');
        putenv("APP_ENV={$env}");

        // Set Percy environment variables
        if (! $this->option('without-percy')) {
            $buildName = $this->option('build-name') ?? $this->generateBuildName();
            putenv("PERCY_BUILD_NAME={$buildName}");

            if ($this->option('parallel')) {
                putenv('PERCY_PARALLEL_TOTAL='.$this->option('parallel'));
                putenv('PERCY_PARALLEL_NONCE='.uniqid('dusk_'));
            }

            if ($this->option('debug')) {
                putenv('PERCY_DEBUG=1');
            }
        }

        // Ensure database is ready
        $this->info('📊 Memeriksa pangkalan data ujian...');
        try {
            $result = Process::run('php artisan migrate:status --env=testing');
            if (! $result->successful()) {
                $this->warn('Menjalankan migrasi pangkalan data ujian...');
                Process::run('php artisan migrate --env=testing --force');
            }
        } catch (Exception $e) {
            $this->warn('Tidak dapat memeriksa status pangkalan data: '.$e->getMessage());
        }
    }

    /**
     * Build Dusk command with options
     */
    private function buildDuskCommand(bool $withoutPercy): string
    {
        $command = 'php artisan dusk';

        // Add group filter
        if ($group = $this->option('group')) {
            $command .= " --group={$group}";
        }

        // Add name filter
        if ($filter = $this->option('filter')) {
            $command .= " --filter={$filter}";
        }

        // Add environment
        if ($env = $this->option('env')) {
            $command .= " --env={$env}";
        }

        // Add debug mode
        if ($this->option('debug')) {
            $command .= ' --debug';
        }

        // Wrap with Percy if enabled
        if (! $withoutPercy) {
            $command = "npx percy exec -- {$command}";
        }

        return $command;
    }

    /**
     * Execute Dusk tests
     */
    private function executeDuskTests(string $command, bool $withoutPercy): int
    {
        $this->info('🧪 Menjalankan ujian Dusk...');
        $this->newLine();

        if (! $withoutPercy) {
            $this->info('📸 Percy visual testing diaktifkan');
            $this->info('🔗 Tangkapan visual akan dimuat naik ke Percy dashboard');
        } else {
            $this->info('⚠️ Percy visual testing dimatikan');
        }

        $this->newLine();
        $this->info("Menjalankan: {$command}");
        $this->newLine();

        // Execute the command
        try {
            $startTime = microtime(true);

            $result = Process::run($command, function (string $type, string $buffer) {
                echo $buffer;
            });

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            $this->newLine();

            if ($result->successful()) {
                $this->info("✅ Ujian Dusk selesai dalam {$duration} saat");

                if (! $withoutPercy) {
                    $this->info('📊 Semak Percy dashboard untuk keputusan visual:');
                    $this->info('   https://percy.io/'.Config::get('percy.project'));
                }

                return 0;
            } else {
                $this->error("❌ Ujian Dusk gagal selepas {$duration} saat");
                $this->error('Kod keluar: '.$result->exitCode());

                if (! $withoutPercy) {
                    $this->info('📊 Semak Percy dashboard untuk maklumat lanjut:');
                    $this->info('   https://percy.io/'.Config::get('percy.project'));
                }

                return $result->exitCode();
            }
        } catch (Exception $e) {
            $this->error('Ralat menjalankan ujian: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Generate build name for Percy
     */
    private function generateBuildName(): string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $env = $this->option('env', 'testing');
        $group = $this->option('group', 'all');

        return "ictserve-v3.6.1-dusk-{$env}-{$group}-{$timestamp}";
    }
}
