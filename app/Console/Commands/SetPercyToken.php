<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Artisan Command: Set Percy Token
 * 
 * This command helps set the Percy token in the environment configuration
 * for ICTServe v3.6.1 integration.
 */
class SetPercyToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'percy:set-token 
                            {token : The Percy token to set}
                            {--env-file=.env : The environment file to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Percy token in environment configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $token = $this->argument('token');
        $envFile = $this->option('env-file');

        if (empty($token)) {
            $this->error('Percy token cannot be empty');
            return Command::FAILURE;
        }

        if (!File::exists($envFile)) {
            $this->error("Environment file {$envFile} does not exist");
            return Command::FAILURE;
        }

        try {
            $envContent = File::get($envFile);

            // Check if PERCY_TOKEN already exists
            if (preg_match('/^PERCY_TOKEN=/m', $envContent)) {
                // Update existing token
                $envContent = preg_replace('/^PERCY_TOKEN=.*$/m', "PERCY_TOKEN={$token}", $envContent);
                $this->info('Updated existing PERCY_TOKEN');
            } else {
                // Add new token
                $envContent .= "\nPERCY_TOKEN={$token}\n";
                $this->info('Added new PERCY_TOKEN');
            }

            File::put($envFile, $envContent);

            $this->info("✅ Percy token has been set in {$envFile}");
            $this->info('💡 Run the following commands to apply changes:');
            $this->info('  php artisan config:clear');
            $this->info('  php artisan percy:validate-config');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to update environment file: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
