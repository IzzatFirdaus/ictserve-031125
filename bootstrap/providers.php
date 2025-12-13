<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\VoltServiceProvider::class,
    App\Providers\PasswordValidationServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    App\Providers\PulseServiceProvider::class,
    App\Providers\PerformanceServiceProvider::class,
    App\Providers\ApiRateLimitingServiceProvider::class,
    App\Providers\RecaptchaServiceProvider::class,
    App\Providers\ClamavServiceProvider::class,
];
