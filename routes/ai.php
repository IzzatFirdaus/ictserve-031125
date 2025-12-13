<?php

declare(strict_types=1);

use App\Mcp\Servers\ICTServeServer;
use App\Mcp\Servers\LaravelBoostCompatServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('ictserve', ICTServeServer::class);

app()->booted(function (): void {
    if (class_exists(\Laravel\Boost\Mcp\Boost::class)) {
        Mcp::local('laravel-boost', LaravelBoostCompatServer::class);
    }
});
