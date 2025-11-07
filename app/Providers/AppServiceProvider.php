<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\AssetReturnedDamaged;
use App\Listeners\CreateMaintenanceTicketForDamagedAsset;
use App\Listeners\UpdateEmailLogOnFailure;
use App\Listeners\UpdateEmailLogOnSend;
use App\Models\Asset;
use App\Models\HelpdeskComment;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Observers\HelpdeskCommentObserver;
use App\Observers\HelpdeskTicketObserver;
use App\Policies\AssetPolicy;
use App\Policies\HelpdeskTicketPolicy;
use App\Policies\LoanApplicationPolicy;
use App\Policies\UserPolicy;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        app()->useLangPath(base_path('lang'));

        // Register model observers
        HelpdeskTicket::observe(HelpdeskTicketObserver::class);
        HelpdeskComment::observe(HelpdeskCommentObserver::class);

        // Register event listeners
        Event::listen(MessageSent::class, UpdateEmailLogOnSend::class);
        Event::listen(JobFailed::class, UpdateEmailLogOnFailure::class);
        Event::listen(AssetReturnedDamaged::class, CreateMaintenanceTicketForDamagedAsset::class);

        // Register policies explicitly for Filament resources
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(HelpdeskTicket::class, HelpdeskTicketPolicy::class);
        Gate::policy(LoanApplication::class, LoanApplicationPolicy::class);
        Gate::policy(Asset::class, AssetPolicy::class);
    }
}
