---
inclusion:
  fileMatchPattern:
    - 'routes/ai.php'
    - 'app/Mcp/**/*.php'
    - 'config/mcp.php'
  applyWhen:
    - Model Context Protocol server implementation
    - AI tool integration
    - MCP resource and prompt creation
---

# Laravel MCP Integration Guidelines

Laravel MCP provides native Model Context Protocol server capabilities for Laravel applications.

**Version**: 0.3.5 (ICTServe v3.6.1)

## Installation

```bash
composer require laravel/mcp
php artisan vendor:publish --tag=ai-routes
```

## Creating MCP Servers

```bash
# Create MCP server
php artisan make:mcp-server ICTServeServer

# Create tools, resources, prompts
php artisan make:mcp-tool HelpdeskTicketTool
php artisan make:mcp-resource SystemDocumentationResource
php artisan make:mcp-prompt HelpdeskAssistantPrompt
```

## Server Registration

```php
// routes/ai.php
use App\Mcp\Servers\ICTServeServer;
use Laravel\Mcp\Facades\Mcp;

// Web server (HTTP-based)
Mcp::web('/mcp/ictserve', ICTServeServer::class)
    ->middleware(['auth:sanctum']);

// Local server (stdio-based)
Mcp::local('ictserve', ICTServeServer::class);
```

## ICTServe MCP Server

```php
namespace App\Mcp\Servers;

use Laravel\Mcp\Server;

class ICTServeServer extends Server
{
    protected string $name = 'ICTServe Management Server';
    protected string $version = '3.6.1';
    protected string $instructions = 'Provides access to ICTServe helpdesk, asset loan, and system management capabilities.';

    protected array $tools = [
        HelpdeskTicketTool::class,
    ];

    protected array $resources = [
        SystemDocumentationResource::class,
    ];

    protected array $prompts = [
        HelpdeskAssistantPrompt::class,
    ];
}
```

## Tool Implementation

```php
namespace App\Mcp\Tools;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Tool;

class HelpdeskTicketTool extends Tool
{
    protected string $name = 'create_helpdesk_ticket';
    protected string $description = 'Create a new helpdesk ticket';

    public function handle(Request $request): Response
    {
        if (!$request->user()->can('manage-helpdesk')) {
            return Response::error('Permission denied.');
        }

        $ticket = HelpdeskTicket::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'category' => $request->input('category'),
        ]);

        return Response::content("Ticket #{$ticket->ticket_number} created successfully.");
    }
}
```

## Testing MCP Servers

```bash
# Test web server
php artisan mcp:inspector mcp/ictserve

# Test local server
php artisan mcp:inspector ictserve
```

## Authentication

```php
// Sanctum authentication
Mcp::web('/mcp/ictserve', ICTServeServer::class)
    ->middleware('auth:sanctum');

// OAuth 2.1 authentication
Mcp::oauthRoutes();
```

## Best Practices

1. Use `php artisan make:mcp-*` commands for generation
2. Implement authorization in tool handlers
3. Test servers with MCP Inspector
4. Use descriptive tool names and descriptions
5. Document tool parameters clearly

## ICTServe Usage

ICTServe uses Laravel Boost MCP server (not custom MCP server) for AI-assisted development:

```bash
# Start Laravel Boost MCP
php artisan boost:mcp
```

Do not create custom MCP servers unless explicitly requested for specific AI tool integration needs.
