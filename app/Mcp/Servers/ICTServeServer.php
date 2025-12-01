<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\CheckAssetStatusTool;
use App\Mcp\Tools\QueryHelpdeskTicketsTool;
use Laravel\Mcp\Server;

class ICTServeServer extends Server
{
    protected string $name = 'ICTServe Server';
    protected string $version = '1.0.0';
    protected string $instructions = 'Provides access to ICTServe helpdesk tickets and asset management data';

    protected array $tools = [
        QueryHelpdeskTicketsTool::class,
        CheckAssetStatusTool::class,
    ];

    protected array $resources = [];
    protected array $prompts = [];
}
