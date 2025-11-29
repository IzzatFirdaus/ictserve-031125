<?php

declare(strict_types=1);

use App\Mcp\Servers\ICTServeServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('ictserve', ICTServeServer::class);
