<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Centralized runtime flags for feature toggles. These typically read
    | from environment variables for easy enabling/disabling per environment.
    |
    */

    'raptor_mini_preview' => (bool) env('RAPTOR_MINI_PREVIEW', true),
];
