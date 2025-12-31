<?php

return [
    App\Providers\ApiRateLimitingServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\ClamavServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\GmailServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\PasswordValidationServiceProvider::class,
    App\Providers\PerformanceServiceProvider::class,
    App\Providers\PulseServiceProvider::class,
    App\Providers\RecaptchaServiceProvider::class,
    App\Providers\VoltServiceProvider::class,
    App\Providers\WidgetServiceProvider::class,
    Laravel\Sanctum\SanctumServiceProvider::class,
    Spatie\Activitylog\ActivitylogServiceProvider::class,
];
