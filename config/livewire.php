<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Livewire Asset URL
    |--------------------------------------------------------------------------
    |
    | Ensure the Livewire JavaScript asset resolves correctly when the app is
    | served from a subdirectory or behind a reverse proxy. Override via
    | LIVEWIRE_ASSET_URL when using a CDN or custom asset host.
    |
    */
    'asset_url' => env('LIVEWIRE_ASSET_URL', env('APP_URL') ? rtrim(env('APP_URL'), '/').'/livewire/livewire.js' : null),
];
