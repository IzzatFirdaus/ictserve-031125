<?php

declare(strict_types=1);

namespace App\Providers;

use App\Broadcasting\BroadcastManager as AppBroadcastManager;
use App\Broadcasting\ChannelRegistrar;
use App\Contracts\AccessoryTrackingServiceInterface;
use App\Contracts\AccountLinkingServiceInterface;
use App\Contracts\ApiTokenServiceInterface;
use App\Contracts\ApprovalServiceInterface;
use App\Contracts\GoogleOAuthVerificationServiceInterface;
use App\Contracts\GoogleServicesCacheServiceInterface;
use App\Contracts\GoogleSsoServiceInterface;
use App\Contracts\HelpdeskServiceInterface;
use App\Contracts\NotificationPreferenceServiceInterface;
use App\Contracts\OllamaClientContract;
use App\Contracts\PerformanceMonitoringServiceInterface;
use App\Contracts\RegistrationServiceInterface;
use App\Contracts\ResponsibleOfficerServiceInterface;
use App\Contracts\SsoHealthCheckInterface;
use App\Contracts\TokenServiceInterface;
use App\Events\AssetReturnedDamaged;
use App\Events\LoanStatusChanged;
use App\Events\TicketStatusChanged;
use App\Listeners\BroadcastEventAuditListener;
use App\Listeners\CreateMaintenanceTicketForDamagedAsset;
use App\Listeners\LogFailedLoginAttempt;
use App\Listeners\NotificationCreatedListener;
use App\Listeners\SendLoanStatusEmail;
use App\Listeners\SendTicketStatusEmail;
use App\Listeners\UpdateEmailLogOnFailure;
use App\Listeners\UpdateEmailLogOnSend;
use App\Models\Asset;
use App\Models\HelpdeskComment;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Observers\HelpdeskCommentObserver;
use App\Observers\HelpdeskTicketCacheObserver;
use App\Observers\HelpdeskTicketObserver;
use App\Observers\LoanApplicationCacheObserver;
use App\Observers\UserObserver;
use App\Policies\AssetPolicy;
use App\Policies\HelpdeskTicketPolicy;
use App\Policies\LoanApplicationPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\UserPolicy;
use App\Services\AccessoryTrackingService;
use App\Services\AccountLinkingService;
use App\Services\ApiTokenService;
use App\Services\ApprovalService;
use App\Services\BedrockService;
use App\Services\GoogleOAuthVerificationService;
use App\Services\GoogleServicesCacheService;
use App\Services\GoogleServicesPerformanceMonitor;
use App\Services\GoogleSsoService;
use App\Services\HelpdeskService;
use App\Services\ModelRouter;
use App\Services\NotificationPreferenceService;
use App\Services\OllamaClient;
use App\Services\PerformanceMonitoringService;
use App\Services\RegistrationService;
use App\Services\ResponsibleOfficerService;
use App\Services\SsoHealthCheck;
use App\Services\TokenService;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use Illuminate\Auth\Events\Failed;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Broadcasting\BroadcastManager as FrameworkBroadcastManager;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Conditionally register development-only service providers
        // This prevents "ServiceProvider not found" errors when packages are not available

        // Register IDE Helper service provider only in local/development environments
        if ($this->app->environment('local', 'development') && class_exists(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class)) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        // Register Laravel Boost service provider if available (dev dependency)
        // Only register if the class exists to prevent errors in production/Docker
        if (class_exists(\Laravel\Boost\BoostServiceProvider::class)) {
            $this->app->register(\Laravel\Boost\BoostServiceProvider::class);
        }

        // Register Laravel Pail service provider if available (dev dependency)
        if (class_exists(\Laravel\Pail\PailServiceProvider::class)) {
            $this->app->register(\Laravel\Pail\PailServiceProvider::class);
        }

        $this->app->singleton(ChannelRegistrar::class);

        $this->app->extend(FrameworkBroadcastManager::class, function ($manager, $app) {
            return new AppBroadcastManager(
                $app,
                $app->make(ChannelRegistrar::class),
            );
        });

        $this->app->singleton(BedrockRuntimeClient::class, function () {
            $credentials = config('bedrock.credentials');

            $config = [
                'region' => config('bedrock.region'),
                'version' => config('bedrock.version'),
            ];

            if (
                is_array($credentials)
                && is_string($credentials['key'] ?? null)
                && is_string($credentials['secret'] ?? null)
                && $credentials['key'] !== ''
                && $credentials['secret'] !== ''
            ) {
                $config['credentials'] = [
                    'key' => $credentials['key'],
                    'secret' => $credentials['secret'],
                    'token' => $credentials['token'] ?? null,
                ];
            }

            return new BedrockRuntimeClient($config);
        });

        $this->app->singleton(BedrockService::class);

        $this->app->singleton(ModelRouter::class);

        // Register TokenService for v3.5.0 True Hybrid Architecture
        $this->app->singleton(TokenServiceInterface::class, TokenService::class);

        // Register HelpdeskService for v3.5.0 True Hybrid Architecture
        $this->app->singleton(HelpdeskServiceInterface::class, HelpdeskService::class);

        // Register ApprovalService for v3.5.0 Email-Based Approval Workflow
        $this->app->singleton(ApprovalServiceInterface::class, ApprovalService::class);

        // Register RegistrationService for v3.5.0 Self-Registration
        $this->app->singleton(RegistrationServiceInterface::class, RegistrationService::class);

        // Register GoogleSsoService for v3.6.0 Google SSO Enhancement
        // Per Requirements 1.1, 1.2, 2.1, 4.1: Google SSO with domain validation and audit logging
        $this->app->singleton(GoogleSsoServiceInterface::class, GoogleSsoService::class);

        // Register SsoHealthCheck for v3.6.0 Google SSO Enhancement
        // Per Requirements 8.1, 8.2: SSO health monitoring and configuration validation
        $this->app->singleton(SsoHealthCheckInterface::class, SsoHealthCheck::class);

        // Register GoogleOAuthVerificationService for v3.6.1 OAuth Verification Management
        // Per Requirements 1.1, 1.2, 2.5, 4.1: OAuth verification status and test user management
        $this->app->singleton(GoogleOAuthVerificationServiceInterface::class, GoogleOAuthVerificationService::class);

        // Register GoogleServicesErrorHandler for v3.6.1 Enhanced Error Handling
        // Per Requirements 7.1, 7.2, 7.4, 7.5: Centralized error handling for all Google services
        $this->app->singleton(
            \App\Contracts\GoogleServicesErrorHandlerInterface::class,
            \App\Services\GoogleServicesErrorHandler::class
        );

        // Register GoogleServicesCacheService for v3.6.1 Performance Optimization
        // Per Requirements 13.2, 13.3: Redis caching for Google user profiles and OAuth tokens
        $this->app->singleton(GoogleServicesCacheServiceInterface::class, GoogleServicesCacheService::class);

        // Register GoogleServicesPerformanceMonitor for v3.6.1 Performance Monitoring
        // Per Requirements 13.5, 17.2: Performance monitoring for all Google services
        $this->app->singleton(GoogleServicesPerformanceMonitor::class);

        // Register AccountLinkingService for v3.5.0 Optional Account Linking
        $this->app->singleton(AccountLinkingServiceInterface::class, AccountLinkingService::class);

        // Register NotificationPreferenceService for v3.5.0 Notification Preferences
        $this->app->singleton(NotificationPreferenceServiceInterface::class, NotificationPreferenceService::class);

        // Register NotificationSchedulingService for v3.6.1 Notification Scheduling
        // Per Requirements 2.7: Notification scheduling for future delivery
        $this->app->singleton(
            \App\Contracts\NotificationSchedulingServiceInterface::class,
            \App\Services\NotificationSchedulingService::class
        );

        // Register ResponsibleOfficerService for v3.5.0 Responsible Officer Management
        $this->app->singleton(ResponsibleOfficerServiceInterface::class, ResponsibleOfficerService::class);

        // Register AccessoryTrackingService for v3.5.0 Accessory Tracking
        $this->app->singleton(AccessoryTrackingServiceInterface::class, AccessoryTrackingService::class);

        // Register PerformanceMonitoringService for v3.5.0 Laravel Pulse Integration
        // Per Requirement 36: Application Performance Monitoring
        $this->app->singleton(PerformanceMonitoringServiceInterface::class, PerformanceMonitoringService::class);

        // Register ApiTokenService for v3.5.0 API Authentication
        // Per Requirement 37: API Authentication (Laravel Sanctum)
        $this->app->singleton(ApiTokenServiceInterface::class, ApiTokenService::class);

        // Register OllamaClient for v3.6.0 AI Integration
        // Per Requirements 6.1: Local LLM Processing
        // Selaras dengan Laravel 12.40.1 service container patterns
        $this->app->singleton(OllamaClientContract::class, OllamaClient::class);

        // Register AIBroadcastingService for v3.6.0 Real-time AI Notifications
        // Per Requirements 11.1, 11.2, 11.3: Real-time AI broadcasting
        // Selaras dengan D16 Broadcasting Setup v3.6.0
        $this->app->singleton(\App\Services\AIBroadcastingService::class);

        // Register JobMonitoringService for v3.6.0 Queue Monitoring
        // Per Requirements 8.1, 8.3, 8.5: Job status tracking, performance monitoring, error handling
        // Selaras dengan D17 Queue Management v3.6.0
        $this->app->singleton(\App\Services\Monitoring\JobMonitoringService::class);
    }

    public function boot(): void
    {
        app()->useLangPath(base_path('lang'));

        if (app()->runningUnitTests()) {
            config([
                'cache.default' => 'array',
                'session.driver' => 'array',
            ]);
        }

        // Register Filament custom anonymous components for MyDS v2025.2 compliance
        // Maps <x-filament.components.widget-card> and <x-filament.components.stats-card>
        // to the MyDS-compliant views in resources/views/filament/components/
        Blade::anonymousComponentPath(resource_path('views/filament/components'), 'filament.components');

        // Register model observers
        // Note: HelpdeskTicketObserver is registered via #[ObservedBy] attribute on the model
        HelpdeskTicket::observe(HelpdeskTicketCacheObserver::class);
        HelpdeskComment::observe(HelpdeskCommentObserver::class);
        LoanApplication::observe(LoanApplicationCacheObserver::class);
        User::observe(UserObserver::class);

        // Register event listeners
        Event::listen(MessageSent::class, UpdateEmailLogOnSend::class);
        Event::listen(JobFailed::class, UpdateEmailLogOnFailure::class);
        Event::listen(AssetReturnedDamaged::class, CreateMaintenanceTicketForDamagedAsset::class);
        // Log failed login attempts to help debug authentication issues
        Event::listen(Failed::class, LogFailedLoginAttempt::class);
        // Listen for database notifications to broadcast them via WebSocket
        Event::listen(NotificationSent::class, NotificationCreatedListener::class);
        // Listen for broadcast events to log them for audit purposes (Requirements 7.5)
        Event::listen(BroadcastEvent::class, BroadcastEventAuditListener::class);

        // Register email notification listeners
        Event::listen(LoanStatusChanged::class, SendLoanStatusEmail::class);
        Event::listen(TicketStatusChanged::class, SendTicketStatusEmail::class);

        // Register policies explicitly for Filament resources
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(HelpdeskTicket::class, HelpdeskTicketPolicy::class);
        Gate::policy(LoanApplication::class, LoanApplicationPolicy::class);
        Gate::policy(Asset::class, AssetPolicy::class);
        Gate::policy(DatabaseNotification::class, NotificationPolicy::class);
    }
}
