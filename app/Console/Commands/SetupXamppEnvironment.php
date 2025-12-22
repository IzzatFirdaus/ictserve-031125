<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\EnvironmentMigrationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Setup XAMPP Environment Command for ICTServe v3.6.1
 *
 * Provides guided setup and migration to XAMPP environment
 */
class SetupXamppEnvironment extends Command
{
    protected $signature = 'ict:setup-xampp
                           {--migrate : Execute full migration to XAMPP}
                           {--status : Show current environment status}
                           {--validate : Validate XAMPP prerequisites}
                           {--create-config : Create XAMPP configuration files}';

    protected $description = 'Setup and migrate ICTServe to XAMPP environment (MySQL + WSL Redis)';

    private EnvironmentMigrationService $migrationService;

    public function __construct(EnvironmentMigrationService $migrationService)
    {
        parent::__construct();
        $this->migrationService = $migrationService;
    }

    public function handle(): int
    {
        $this->info('ICTServe v3.6.1 - XAMPP Environment Setup');
        $this->info('=====================================');

        if ($this->option('status')) {
            return $this->showStatus();
        }

        if ($this->option('validate')) {
            return $this->validatePrerequisites();
        }

        if ($this->option('create-config')) {
            return $this->createConfiguration();
        }

        if ($this->option('migrate')) {
            return $this->executeMigration();
        }

        // Default: Show help and options
        return $this->showHelp();
    }

    /**
     * Show current environment status
     */
    private function showStatus(): int
    {
        $this->info('Current Environment Status:');
        $this->line('');

        $status = $this->migrationService->getMigrationStatus();

        $this->table(
            ['Component', 'Status', 'Details'],
            [
                ['XAMPP MySQL', $status['xampp_mysql_available'] ? '✅ Available' : '❌ Not Available', 'Host: 127.0.0.1:3306'],
                ['WSL Redis', $status['wsl_redis_available'] ? '✅ Available' : '❌ Not Available', 'Host: 127.0.0.1:6379'],
                ['Backup Directory', $status['backup_directory_exists'] ? '✅ Ready' : '❌ Missing', 'For migration backups'],
                ['Current Environment', '📋 '.$status['current_environment'], 'APP_ENV setting'],
                ['Database Connection', '🗄️ '.$status['database_connection'], 'Current DB driver'],
                ['Cache Driver', '💾 '.$status['cache_driver'], 'Current cache system'],
                ['Session Driver', '🔐 '.$status['session_driver'], 'Current session storage'],
                ['Queue Connection', '⚡ '.$status['queue_connection'], 'Current queue system'],
            ]
        );

        if ($status['xampp_mysql_available'] && $status['wsl_redis_available']) {
            $this->info('✅ XAMPP environment is ready for migration');
        } else {
            $this->warn('⚠️  XAMPP environment prerequisites not met');
            $this->info('Run: php artisan ict:setup-xampp --validate');
        }

        return 0;
    }

    /**
     * Validate XAMPP prerequisites
     */
    private function validatePrerequisites(): int
    {
        $this->info('Validating XAMPP Prerequisites:');
        $this->line('');

        $checks = [
            'XAMPP Installation' => $this->checkXamppInstallation(),
            'XAMPP MySQL' => $this->checkXamppMySQL(),
            'XAMPP Apache' => $this->checkXamppApache(),
            'WSL Availability' => $this->checkWSLAvailability(),
            'WSL Redis' => $this->checkWSLRedis(),
            'PowerShell Scripts' => $this->checkPowerShellScripts(),
            'Configuration Files' => $this->checkConfigurationFiles(),
        ];

        foreach ($checks as $check => $result) {
            $status = $result['status'] ? '✅' : '❌';
            $this->line("{$status} {$check}: {$result['message']}");
        }

        $allPassed = collect($checks)->every(fn ($result) => $result['status']);

        $this->line('');
        if ($allPassed) {
            $this->info('✅ All prerequisites validated successfully');
            $this->info('Ready to migrate: php artisan ict:setup-xampp --migrate');
        } else {
            $this->error('❌ Some prerequisites failed validation');
            $this->info('Please resolve the issues above before proceeding');
        }

        return $allPassed ? 0 : 1;
    }

    /**
     * Create XAMPP configuration files
     */
    private function createConfiguration(): int
    {
        $this->info('Creating XAMPP Configuration Files:');
        $this->line('');

        $files = [
            '.env.xampp' => $this->createEnvXamppFile(),
            'scripts/xampp/manage-xampp.ps1' => $this->checkPowerShellScript('xampp'),
            'scripts/wsl/manage-redis.ps1' => $this->checkPowerShellScript('wsl'),
            'scripts/environment/switch-environment.ps1' => $this->checkPowerShellScript('environment'),
        ];

        foreach ($files as $file => $exists) {
            if ($exists) {
                $this->line("✅ {$file} - Already exists");
            } else {
                $this->line("❌ {$file} - Missing (should be created by implementation)");
            }
        }

        $this->line('');
        $this->info('Configuration files status checked');
        $this->info('Use PowerShell scripts in scripts/ directory to manage services');

        return 0;
    }

    /**
     * Execute full migration to XAMPP
     */
    private function executeMigration(): int
    {
        $this->warn('⚠️  This will migrate ICTServe to XAMPP environment');
        $this->warn('   Current data will be backed up before migration');
        $this->line('');

        if (! $this->confirm('Do you want to proceed with the migration?')) {
            $this->info('Migration cancelled');

            return 0;
        }

        $this->info('Starting XAMPP environment migration...');
        $this->line('');

        // Show progress bar
        $bar = $this->output->createProgressBar(8);
        $bar->setFormat('verbose');

        $result = $this->migrationService->migrateToXampp();

        $bar->finish();
        $this->line('');
        $this->line('');

        if ($result['success']) {
            $this->info('✅ Migration completed successfully!');
            $this->line('');
            $this->info('Migration Results:');
            foreach ($result['results'] as $step => $status) {
                $icon = $status ? '✅' : '❌';
                $this->line("  {$icon} ".str_replace('_', ' ', ucfirst($step)));
            }

            $this->line('');
            $this->info('Backup Location: '.$result['backup_location']);
            $this->line('');
            $this->info('Next Steps:');
            $this->line('1. Start Laravel: php artisan serve');
            $this->line('2. Run tests: php artisan test --group=xampp');
            $this->line('3. Check status: php artisan ict:setup-xampp --status');

            return 0;
        } else {
            $this->error('❌ Migration failed: '.$result['message']);
            $this->line('');
            $this->info('Partial Results:');
            foreach ($result['results'] as $step => $status) {
                $icon = $status ? '✅' : '❌';
                $this->line("  {$icon} ".str_replace('_', ' ', ucfirst($step)));
            }

            $this->line('');
            $this->warn('Check logs and backup location: '.$result['backup_location']);

            return 1;
        }
    }

    /**
     * Show help information
     */
    private function showHelp(): int
    {
        $this->info('XAMPP Environment Setup Options:');
        $this->line('');
        $this->line('  --status         Show current environment status');
        $this->line('  --validate       Validate XAMPP prerequisites');
        $this->line('  --create-config  Create configuration files');
        $this->line('  --migrate        Execute full migration to XAMPP');
        $this->line('');
        $this->info('Examples:');
        $this->line('  php artisan ict:setup-xampp --status');
        $this->line('  php artisan ict:setup-xampp --validate');
        $this->line('  php artisan ict:setup-xampp --migrate');
        $this->line('');
        $this->info('PowerShell Scripts:');
        $this->line('  scripts/xampp/manage-xampp.ps1 -Action start');
        $this->line('  scripts/wsl/manage-redis.ps1 -Action start');
        $this->line('  scripts/environment/switch-environment.ps1 -Environment xampp');

        return 0;
    }

    /**
     * Check XAMPP installation
     *
     * @return array<string, mixed>
     */
    private function checkXamppInstallation(): array
    {
        $xamppPath = 'C:\xampp';

        if (PHP_OS_FAMILY === 'Windows') {
            $exists = is_dir($xamppPath);

            return [
                'status' => $exists,
                'message' => $exists ? "Found at {$xamppPath}" : "Not found at {$xamppPath}",
            ];
        }

        return [
            'status' => false,
            'message' => 'Windows required for XAMPP',
        ];
    }

    /**
     * Check XAMPP MySQL
     *
     * @return array<string, mixed>
     */
    private function checkXamppMySQL(): array
    {
        $mysqlPath = 'C:\xampp\mysql\bin\mysqld.exe';

        if (PHP_OS_FAMILY === 'Windows') {
            $exists = file_exists($mysqlPath);

            return [
                'status' => $exists,
                'message' => $exists ? 'MySQL executable found' : 'MySQL executable not found',
            ];
        }

        return [
            'status' => false,
            'message' => 'Windows required',
        ];
    }

    /**
     * Check XAMPP Apache
     *
     * @return array<string, mixed>
     */
    private function checkXamppApache(): array
    {
        $apachePath = 'C:\xampp\apache\bin\httpd.exe';

        if (PHP_OS_FAMILY === 'Windows') {
            $exists = file_exists($apachePath);

            return [
                'status' => $exists,
                'message' => $exists ? 'Apache executable found' : 'Apache executable not found',
            ];
        }

        return [
            'status' => false,
            'message' => 'Windows required',
        ];
    }

    /**
     * Check WSL availability
     *
     * @return array<string, mixed>
     */
    private function checkWSLAvailability(): array
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return [
                'status' => false,
                'message' => 'WSL only available on Windows',
            ];
        }

        // Try to execute WSL command
        exec('wsl --version 2>nul', $output, $returnCode);

        return [
            'status' => $returnCode === 0,
            'message' => $returnCode === 0 ? 'WSL is available' : 'WSL not installed or not available',
        ];
    }

    /**
     * Check WSL Redis
     *
     * @return array<string, mixed>
     */
    private function checkWSLRedis(): array
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return [
                'status' => false,
                'message' => 'WSL only available on Windows',
            ];
        }

        exec('wsl redis-cli --version 2>nul', $output, $returnCode);

        return [
            'status' => $returnCode === 0,
            'message' => $returnCode === 0 ? 'Redis available in WSL' : 'Redis not found in WSL',
        ];
    }

    /**
     * Check PowerShell scripts
     *
     * @return array<string, mixed>
     */
    private function checkPowerShellScripts(): array
    {
        $scripts = [
            'scripts/xampp/manage-xampp.ps1',
            'scripts/wsl/manage-redis.ps1',
            'scripts/environment/switch-environment.ps1',
        ];

        $existing = 0;
        foreach ($scripts as $script) {
            if (File::exists(base_path($script))) {
                $existing++;
            }
        }

        return [
            'status' => $existing === count($scripts),
            'message' => "{$existing}/".count($scripts).' scripts found',
        ];
    }

    /**
     * Check configuration files
     *
     * @return array<string, mixed>
     */
    private function checkConfigurationFiles(): array
    {
        $xamppEnvExists = File::exists(base_path('.env.xampp'));

        return [
            'status' => $xamppEnvExists,
            'message' => $xamppEnvExists ? '.env.xampp configuration ready' : '.env.xampp not found',
        ];
    }

    /**
     * Create .env.xampp file
     */
    private function createEnvXamppFile(): bool
    {
        return File::exists(base_path('.env.xampp'));
    }

    /**
     * Check PowerShell script
     */
    private function checkPowerShellScript(string $type): bool
    {
        $scripts = [
            'xampp' => 'scripts/xampp/manage-xampp.ps1',
            'wsl' => 'scripts/wsl/manage-redis.ps1',
            'environment' => 'scripts/environment/switch-environment.ps1',
        ];

        return isset($scripts[$type]) && File::exists(base_path($scripts[$type]));
    }
}
