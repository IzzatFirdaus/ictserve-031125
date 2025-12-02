<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\AccessoryTrackingServiceInterface;
use App\Contracts\AccountLinkingServiceInterface;
use App\Contracts\ApprovalServiceInterface;
use App\Contracts\HelpdeskServiceInterface;
use App\Contracts\NotificationPreferenceServiceInterface;
use App\Contracts\RegistrationServiceInterface;
use App\Contracts\ResponsibleOfficerServiceInterface;
use App\Contracts\TokenServiceInterface;
use App\Events\AssetReturnedDamaged;
use App\Events\LoanStatusChanged;
use App\Events\TicketStatusChanged;
use App\Listeners\CreateMaintenanceTicketForDamagedAsset;
use App\Listeners\LogFailedLoginAttempt;
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
use App\Policies\UserPolicy;
use App\Services\AccessoryTrackingService;
use App\Services\AccountLinkingService;
use App\Services\ApprovalService;
use App\Services\BedrockService;
use App\Services\HelpdeskService;
use App\Services\NotificationPreferenceService;
use App\Services\RegistrationService;
use App\Services\ResponsibleOfficerService;
use App\Services\TokenService;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use Illuminate\Auth\Events\Failed;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BedrockRuntimeClient::class, function () {
            return new BedrockRuntimeClient([
                'region' => config('bedrock.region'),
                'version' => config('bedrock.version'),
                'credentials' => config('bedrock.credentials'),
            ]);
        });

        $this->app->singleton(BedrockService::class);

        // Register TokenService for v3.5.0 True Hybrid Architecture
        $this->app->singleton(TokenServiceInterface::class, TokenService::class);

        // Register HelpdeskService for v3.5.0 True Hybrid Architecture
        $this->app->singleton(HelpdeskServiceInterface::class, HelpdeskService::class);

        // Register ApprovalService for v3.5.0 Email-Based Approval Workflow
        $this->app->singleton(ApprovalServiceInterface::class, ApprovalService::class);

        // Register RegistrationService for v3.5.0 Self-Registration
        $this->app->singleton(RegistrationServiceInterface::class, RegistrationService::class);

        // Register AccountLinkingService for v3.5.0 Optional Account Linking
        $this->app->singleton(AccountLinkingServiceInterface::class, AccountLinkingService::class);

        // Register NotificationPreferenceService for v3.5.0 Notification Preferences
        $this->app->singleton(NotificationPreferenceServiceInterface::class, NotificationPreferenceService::class);

        // Register ResponsibleOfficerService for v3.5.0 Responsible Officer Management
        $this->app->singleton(ResponsibleOfficerServiceInterface::class, ResponsibleOfficerService::class);

        // Register AccessoryTrackingService for v3.5.0 Accessory Tracking
        $this->app->singleton(AccessoryTrackingServiceInterface::class, AccessoryTrackingService::class);
    }

    public function boot(): void
    {
        app()->useLangPath(base_path('lang'));

        // Register model observers
        HelpdeskTicket::observe(HelpdeskTicketObserver::class);
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

        // Register email notification listeners
        Event::listen(LoanStatusChanged::class, SendLoanStatusEmail::class);
        Event::listen(TicketStatusChanged::class, SendTicketStatusEmail::class);

        // Register policies explicitly for Filament resources
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(HelpdeskTicket::class, HelpdeskTicketPolicy::class);
        Gate::policy(LoanApplication::class, LoanApplicationPolicy::class);
        Gate::policy(Asset::class, AssetPolicy::class);
    }
}
