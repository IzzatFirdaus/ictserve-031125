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
    | Set to null to use Livewire's default asset injection.
    |
    */
    'asset_url' => env('LIVEWIRE_ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Inject Assets
    |--------------------------------------------------------------------------
    |
    | By default, Livewire will inject its JavaScript and CSS assets into
    | the page automatically. You can disable this behavior by setting
    | this option to false.
    |
    */
    'inject_assets' => true,
];
