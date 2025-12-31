<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestGmailConfig extends Command
{
    protected $signature = 'gmail:config';

    protected $description = 'Test Gmail configuration values';

    public function handle(): int
    {
        $this->info('Testing Gmail Configuration...');

        // Test direct environment access
        $enabled_direct = $_ENV['GOOGLE_GMAIL_ENABLED'] ?? null;
        $path_direct = $_ENV['GOOGLE_SERVICE_ACCOUNT_PATH'] ?? null;
        $email_direct = $_ENV['GOOGLE_GMAIL_USER_EMAIL'] ?? null;

        $enabled = config('services.google.gmail_enabled');
        $path = config('services.google.service_account_path');
        $email = config('services.google.gmail_user_email');

        $this->table(['Setting', 'Config Value', 'Direct $_ENV', 'Type'], [
            ['Gmail Enabled', var_export($enabled, true), var_export($enabled_direct, true), gettype($enabled)],
            ['Service Account Path', $path ?: 'NOT SET', $path_direct ?: 'NOT SET', gettype($path)],
            ['Gmail User Email', $email ?: 'NOT SET', $email_direct ?: 'NOT SET', gettype($email)],
        ]);

        // Check if file exists
        if ($path_direct) {
            $fullPath = base_path($path_direct);
            $this->info("Checking file: {$fullPath}");
            if (file_exists($fullPath)) {
                $this->info('✅ Service account file exists');
                $this->info('File size: '.filesize($fullPath).' bytes');
            } else {
                $this->error('❌ Service account file NOT found');
            }
        }

        // Test environment variables directly
        $this->info("\nDirect Environment Variables:");
        $this->table(['Variable', 'env()', '$_ENV'], [
            ['GOOGLE_GMAIL_ENABLED', env('GOOGLE_GMAIL_ENABLED', 'NOT SET'), $_ENV['GOOGLE_GMAIL_ENABLED'] ?? 'NOT SET'],
            ['GOOGLE_SERVICE_ACCOUNT_PATH', env('GOOGLE_SERVICE_ACCOUNT_PATH', 'NOT SET'), $_ENV['GOOGLE_SERVICE_ACCOUNT_PATH'] ?? 'NOT SET'],
            ['GOOGLE_GMAIL_USER_EMAIL', env('GOOGLE_GMAIL_USER_EMAIL', 'NOT SET'), $_ENV['GOOGLE_GMAIL_USER_EMAIL'] ?? 'NOT SET'],
        ]);

        return self::SUCCESS;
    }
}
