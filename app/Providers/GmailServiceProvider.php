<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\GoogleOAuthVerificationServiceInterface;
use App\Mail\Transport\GmailTransport;
use App\Services\GmailService;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;

class GmailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GmailService::class, function ($app) {
            return new GmailService(
                $app->make(GoogleOAuthVerificationServiceInterface::class)
            );
        });
    }

    public function boot(): void
    {
        $this->app->afterResolving(MailManager::class, function (MailManager $mailManager) {
            $mailManager->extend('gmail', function (array $config) {
                return new GmailTransport(
                    $this->app->make(GmailService::class)
                );
            });
        });
    }
}
