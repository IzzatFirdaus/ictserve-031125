<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\GmailService;
use Illuminate\Console\Command;

class GmailAuthorize extends Command
{
    protected $signature = 'gmail:authorize {--code= : Authorization code from Google}';

    protected $description = 'Authorize Gmail API access using OAuth2';

    public function handle(): int
    {
        $code = $this->option('code');
        $gmailService = app(GmailService::class);

        if ($gmailService->isAuthenticated()) {
            $this->info('✅ Gmail is already authenticated!');
            $this->info('You can send emails using: php artisan gmail:test your@email.com');

            return self::SUCCESS;
        }

        if ($code) {
            $this->info('Processing authorization code...');

            if ($gmailService->authenticateWithCode($code)) {
                $this->info('✅ Gmail authorized successfully!');
                $this->info('You can now send emails using: php artisan gmail:test your@email.com');

                return self::SUCCESS;
            } else {
                $this->error('Failed to authorize. Please try again.');

                return self::FAILURE;
            }
        }

        // Show authorization URL
        $authUrl = $gmailService->getAuthUrl();

        $this->info('Gmail OAuth2 Authorization');
        $this->info('==========================');
        $this->newLine();
        $this->info('Step 1: Open this URL in your browser:');
        $this->newLine();
        $this->line($authUrl);
        $this->newLine();
        $this->info('Step 2: Sign in with your @motac.gov.my account');
        $this->info('Step 3: Allow the requested permissions');
        $this->info('Step 4: Copy the authorization code from the redirect URL');
        $this->newLine();
        $this->info('Step 5: Run this command with the code:');
        $this->line('php artisan gmail:authorize --code=YOUR_CODE_HERE');

        return self::SUCCESS;
    }
}
