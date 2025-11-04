<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\UpdateEmailLogOnFailure;
use App\Listeners\UpdateEmailLogOnSend;
use App\Models\HelpdeskComment;
use App\Models\HelpdeskTicket;
use App\Observers\HelpdeskCommentObserver;
use App\Observers\HelpdeskTicketObserver;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        HelpdeskTicket::observe(HelpdeskTicketObserver::class);
        HelpdeskComment::observe(HelpdeskCommentObserver::class);

        Event::listen(MessageSent::class, UpdateEmailLogOnSend::class);
        Event::listen(JobFailed::class, UpdateEmailLogOnFailure::class);
    }
}
