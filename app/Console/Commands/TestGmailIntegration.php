<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\GmailService;
use Illuminate\Console\Command;

class TestGmailIntegration extends Command
{
    protected $signature = 'gmail:test {email} {--subject=Test Gmail Integration} {--body=This is a test email from ICTServe Gmail integration}';

    protected $description = 'Test Gmail API integration by sending a test email';

    public function handle(): int
    {
        $email = $this->argument('email');
        $subject = $this->option('subject');
        $body = $this->option('body');

        $this->info('Testing Gmail integration...');
        $this->info("Sending test email to: {$email}");

        try {
            // Test direct Gmail service
            $gmailService = app(GmailService::class);

            if (! $gmailService->validateEmailAddress($email)) {
                $this->error('Invalid email address provided');

                return self::FAILURE;
            }

            $messageId = $gmailService->sendEmail($email, $subject, $body);

            if ($messageId) {
                $this->info('✅ Email sent successfully via Gmail API');
                $this->info("Message ID: {$messageId}");

                // Test message status
                $status = $gmailService->getMessageStatus($messageId);
                if (! empty($status)) {
                    $this->info('Message status retrieved successfully');
                    $this->table(['Property', 'Value'], [
                        ['ID', $status['id'] ?? 'N/A'],
                        ['Thread ID', $status['thread_id'] ?? 'N/A'],
                        ['Size Estimate', $status['size_estimate'] ?? 'N/A'],
                    ]);
                }

                return self::SUCCESS;
            } else {
                $this->error('Failed to send email - no message ID returned');

                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Gmail integration test failed: '.$e->getMessage());
            $this->error('Stack trace: '.$e->getTraceAsString());

            return self::FAILURE;
        }
    }
}
