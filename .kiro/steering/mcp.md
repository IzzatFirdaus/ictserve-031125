---
inclusion:
  always: true
  fileMatchPattern:
    - '.kiro/settings/mcp.json'
    - '**/*.php'
applyWhen:
  - MCP server configuration
  - Tool integration
  - Memory operations
  - Sequential thinking
---

# Model Context Protocol (MCP) Guidelines

## Overview

The Model Context Protocol (MCP) is an open standard that enables AI assistants to securely connect to data sources and tools. This steering file provides comprehensive guidelines for configuring, using, and troubleshooting MCP servers across all projects, with specific focus on Laravel MCP integration and essential MCP servers.

## Core MCP Concepts

### MCP Architecture

MCP follows a client-server architecture where:

- **MCP Client**: AI assistant (like Kiro) that connects to servers
- **MCP Server**: Provides tools, resources, and prompts to clients
- **Transport**: Communication layer (HTTP, stdio, SSE)

### MCP Primitives

1. **Tools**: Functions that AI can call to perform actions
2. **Resources**: Static or dynamic content that AI can read
3. **Prompts**: Reusable prompt templates for AI interactions

## Laravel MCP Integration

### Installation & Setup

Laravel MCP v0.3.5 provides native MCP server capabilities for Laravel applications:

```bash
# Install Laravel MCP
composer require laravel/mcp

# Publish AI routes
php artisan vendor:publish --tag=ai-routes
```

### Creating MCP Servers

Generate MCP servers using Artisan commands:

```bash
# Create a new MCP server
php artisan make:mcp-server WeatherServer

# Create tools, resources, and prompts
php artisan make:mcp-tool CurrentWeatherTool
php artisan make:mcp-resource WeatherGuidelinesResource
php artisan make:mcp-prompt DescribeWeatherPrompt
```

### Server Registration

Register servers in `routes/ai.php`:

```php
use App\Mcp\Servers\WeatherServer;
use Laravel\Mcp\Facades\Mcp;

// Web server (HTTP-based)
Mcp::web('/mcp/weather', WeatherServer::class)
    ->middleware(['auth:sanctum']);

// Local server (stdio-based)
Mcp::local('weather', WeatherServer::class);

// OAuth routes for authentication
Mcp::oauthRoutes();
```

### ICTServe MCP Server Example

```php
<?php
declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\HelpdeskTicketTool;
use App\Mcp\Resources\SystemDocumentationResource;
use App\Mcp\Prompts\HelpdeskAssistantPrompt;
use Laravel\Mcp\Server;

class ICTServeServer extends Server
{
    protected string $name = 'ICTServe Management Server';
    protected string $version = '3.6.1';
    protected string $instructions = 'Provides access to ICTServe helpdesk, asset loan, and system management capabilities.';

    protected array $tools = [
        HelpdeskTicketTool::class,
        // AssetLoanTool::class,
    ];

    protected array $resources = [
        SystemDocumentationResource::class,
    ];

    protected array $prompts = [
        HelpdeskAssistantPrompt::class,
    ];
}
```

### Authentication & Authorization

**Sanctum Authentication**:

```php
Mcp::web('/mcp/ictserve', ICTServeServer::class)
    ->middleware('auth:sanctum');
```

**OAuth 2.1 Authentication**:

```php
// Register OAuth routes
Mcp::oauthRoutes();

// Protected server
Mcp::web('/mcp/ictserve', ICTServeServer::class)
    ->middleware('auth:api');
```

**Authorization in Tools**:

```php
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

public function handle(Request $request): Response
{
    if (!$request->user()->can('manage-helpdesk')) {
        return Response::error('Permission denied.');
    }
    
    // Tool logic...
}
```

### Testing MCP Servers

**Unit Testing**:

```php
use PHPUnit\Framework\Attributes\Test;

class HelpdeskTicketToolTest extends TestCase
{
    #[Test]
    public function it_can_create_ticket(): void
    {
        $user = User::factory()->create();
        
        $response = ICTServeServer::actingAs($user)
            ->tool(HelpdeskTicketTool::class, [
                'title' => 'Test Ticket',
                'description' => 'Test Description',
                'category' => 'technical',
            ]);

        $response->assertOk()
            ->assertSee('Ticket created successfully');
    }
}
```

**MCP Inspector**:

```bash
# Test web server
php artisan mcp:inspector mcp/ictserve

# Test local server
php artisan mcp:inspector ictserve
```

## MCP Server Configuration

### Configuration File Structure

MCP servers are configured in `.amazonq/mcp.json`:

```json
{
  "mcpServers": {
    "server-name": {
      "command": "executable",
      "args": ["arg1", "arg2"],
      "env": {
        "ENV_VAR": "$env:ENV_VAR_NAME"
      },
      "disabled": false,
      "autoApprove": ["tool1", "tool2"]
    }
  }
}
```

### Configuration Properties

- **command**: Executable to run the server (npx, uvx, php, node, etc.)
- **args**: Command-line arguments for the server
- **env**: Environment variables (use `$env:VAR_NAME` for system variables)
- **disabled**: Boolean to enable/disable the server
- **autoApprove**: Array of tools to auto-approve without user confirmation
- **cwd**: Working directory for the server (optional)
- **url**: For HTTP-based servers (alternative to command/args)

### Environment Variable Management

**Best Practices**:

- Store sensitive keys in system environment variables
- Reference them using `$env:VARIABLE_NAME` syntax
- Never commit API keys directly in configuration files
- Use `.env` files for local development

**Example**:

```json
{
  "env": {
    "API_KEY": "$env:MY_API_KEY",
    "REGION": "$env:AWS_REGION"
  }
}
```

## Common MCP Server Types

### 1. NPX-Based Servers (Node.js)

Most common type, installed and run via npm:

```json
{
  "memory": {
    "command": "npx",
    "args": ["-y", "@modelcontextprotocol/server-memory"],
    "disabled": false
  }
}
```

### 2. UVX-Based Servers (Python)

Python servers using uv package manager:

```json
{
  "fetch": {
    "command": "uvx",
    "args": ["--native-tls", "mcp-server-fetch"],
    "disabled": false
  }
}
```

### 3. Local Application Servers

Servers that are part of your application:

```json
{
  "laravel-boost": {
    "command": "php",
    "args": ["artisan", "mcp:start", "server-name"],
    "cwd": "/path/to/project",
    "env": {
      "APP_ENV": "local"
    }
  }
}
```

### 4. HTTP-Based Servers

Servers accessible via HTTP:

```json
{
  "figma": {
    "url": "https://mcp.figma.com/mcp",
    "disabled": false
  }
}
```

## Essential MCP Servers

### Core Development Servers

1. **Memory Server**: Persistent knowledge graph across sessions

   **Purpose**: Maintains comprehensive knowledge about projects, patterns, and decisions across AI sessions.

   **Key Features**:
   - Entity-relationship knowledge graph
   - Cross-session persistence
   - Search and retrieval capabilities
   - Pattern storage and reuse

   ```json
   {
     "memory": {
       "command": "npx",
       "args": [
         "-y",
         "@modelcontextprotocol/server-memory",
         "C:\\XAMPP\\htdocs\\ictserve-031125\\storage\\mcp\\memory.jsonl"
       ],
       "disabled": false,
       "autoApprove": ["*"]
     }
   }
   ```

   **Usage Patterns**:

   ```bash
   # Store development patterns
   create_entities [{"name": "laravel_pattern", "entityType": "development_pattern", "observations": [...]}]
   
   # Search for existing solutions
   search_nodes "filament resource validation"
   
   # Retrieve specific knowledge
   open_nodes ["ictserve_system_spec", "laravel_12_patterns"]
   ```

2. **Sequential Thinking**: Dynamic problem decomposition

   **Purpose**: Enables complex, multi-step problem solving with adaptive thinking processes.

   **Key Features**:
   - Dynamic thought sequences
   - Hypothesis generation and verification
   - Branching and revision capabilities
   - Solution validation

   ```json
   {
     "sequentialthinking": {
       "command": "npx",
       "args": ["-y", "@modelcontextprotocol/server-sequential-thinking"],
       "disabled": false,
       "autoApprove": ["*"]
     }
   }
   ```

   **Usage Patterns**:

   ```bash
   # Complex feature planning
   sequentialthinking({
     "thought": "Analyzing ICTServe helpdesk integration requirements...",
     "thoughtNumber": 1,
     "totalThoughts": 5,
     "nextThoughtNeeded": true
   })
   ```

3. **Fetch Server**: Web content retrieval and processing

   **Purpose**: Fetches and processes web content for AI consumption, including documentation and APIs.

   **Key Features**:
   - HTTP/HTTPS content fetching
   - Markdown conversion
   - Content truncation and pagination
   - Raw HTML support

   ```json
   {
     "fetch": {
       "command": "uvx",
       "args": ["mcp-server-fetch"],
       "disabled": false,
       "autoApprove": ["*"]
     }
   }
   ```

   **Usage Patterns**:

   ```bash
   # Fetch documentation
   fetch({
     "url": "https://laravel.com/docs/12.x/mcp",
     "max_length": 5000
   })
   
   # Get raw HTML
   fetch({
     "url": "https://api.example.com/docs",
     "raw": true
   })
   ```

4. **Filesystem Server**: Secure file operations

   **Purpose**: Provides controlled access to filesystem operations with security boundaries.

   **Key Features**:
   - Configurable access controls
   - Directory traversal protection
   - File reading and writing
   - Directory listing and creation

   ```json
   {
     "filesystem": {
       "command": "npx",
       "args": ["-y", "@modelcontextprotocol/server-filesystem"],
       "env": {
         "ALLOWED_DIRECTORIES": "/workspace,/tmp"
       },
       "autoApprove": [
         "read_file", "write_file", "list_directory",
         "create_directory", "get_file_info"
       ]
     }
   }
   ```

   **Usage Patterns**:

   ```bash
   # Read configuration files
   read_file({"path": "/workspace/.env.example"})
   
   # List project structure
   list_directory({"path": "/workspace/app", "recursive": true})
   
   # Create new files
   write_file({
     "path": "/workspace/config/mcp.php",
     "content": "<?php\n\nreturn [...];"
   })
   ```

### Browser & Testing Servers

1. **Chrome DevTools**: Browser automation and debugging

   **Purpose**: Provides comprehensive browser automation and debugging capabilities.

   **Key Features**:
   - Page navigation and interaction
   - Element inspection and manipulation
   - JavaScript execution
   - Network monitoring
   - Performance analysis

   ```json
   {
     "chrome-devtools": {
       "command": "npx",
       "args": ["-y", "chrome-devtools-mcp@latest"],
       "autoApprove": [
         "navigate_page", "take_snapshot", "click", "fill",
         "evaluate_script", "take_screenshot", "list_pages",
         "list_network_requests", "get_console_message"
       ]
     }
   }
   ```

   **Usage Patterns**:

   ```bash
   # Navigate and test ICTServe pages
   navigate_page({"url": "http://127.0.0.1:8000/helpdesk"})
   take_snapshot()
   fill({"uid": "title-input", "value": "Test Ticket"})
   click({"uid": "submit-button"})
   ```

2. **Playwright**: E2E testing and browser automation

   **Purpose**: Advanced browser automation for comprehensive end-to-end testing.

   **Key Features**:
   - Multi-browser support
   - Mobile device emulation
   - Network interception
   - File uploads and downloads
   - Advanced selectors

   ```json
   {
     "playwright": {
       "command": "npx",
       "args": ["-y", "@playwright/mcp@latest"],
       "autoApprove": [
         "browser_navigate", "browser_click", "browser_snapshot",
         "browser_fill", "browser_evaluate", "browser_take_screenshot",
         "browser_wait_for", "browser_select_option"
       ]
     }
   }
   ```

   **Usage Patterns**:

   ```bash
   # Test ICTServe workflows
   browser_navigate({"url": "http://127.0.0.1:8000/admin"})
   browser_fill_form({
     "fields": [
       {"name": "email", "type": "textbox", "ref": "email-input", "value": "admin@motac.gov.my"},
       {"name": "password", "type": "textbox", "ref": "password-input", "value": "password"}
     ]
   })
   browser_click({"element": "Login Button", "ref": "login-btn"})
   ```

### Development & Integration Servers

1. **Laravel Boost**: Laravel-specific development tools

   **Purpose**: Provides Laravel-specific development capabilities and documentation access.

   **Key Features**:
   - Artisan command execution
   - Database operations and schema inspection
   - Laravel documentation search
   - Tinker integration
   - Application information

   ```json
   {
     "laravel-boost": {
       "command": "php",
       "args": ["artisan", "mcp:start", "boost"],
       "cwd": "/path/to/laravel/project",
       "autoApprove": [
         "application_info", "search_docs", "database_query",
         "database_schema", "tinker", "list_routes",
         "get_config", "read_log_entries"
       ]
     }
   }
   ```

   **Usage Patterns**:

   ```bash
   # Get application information
   application_info()
   
   # Search Laravel documentation
   search_docs({"queries": ["filament resources", "livewire forms"]})
   
   # Execute database queries
   database_query({"query": "SELECT * FROM helpdesk_tickets LIMIT 5"})
   
   # Run tinker commands
   tinker({"code": "User::factory()->create()", "timeout": 30})
   ```

2. **Git Operations**: Repository management

   **Purpose**: Provides Git repository operations and version control capabilities.

   **Key Features**:
   - Repository status and history
   - Branch management
   - Commit operations
   - File blame and diff
   - Stash management

   ```json
   {
     "git": {
       "command": "npx",
       "args": ["-y", "@modelcontextprotocol/server-git"],
       "autoApprove": [
         "git_status", "git_log_or_diff", "git_branch",
         "git_add_or_commit", "git_blame", "git_stash"
       ]
     }
   }
   ```

### Optional Enhancement Servers

1. **GitHub Integration**: Repository operations (Optional)

   ```json
   {
     "github": {
       "command": "npx",
       "args": ["-y", "@modelcontextprotocol/server-github"],
       "env": {
         "GITHUB_PERSONAL_ACCESS_TOKEN": "$env:GITHUB_TOKEN"
       },
       "disabled": true
     }
   }
   ```

2. **Context Enhancement**: Library documentation (Optional)

    ```json
    {
      "context7": {
        "command": "npx",
        "args": ["-y", "@upstash/context7-mcp"],
        "env": {
          "CONTEXT7_API_KEY": "$env:CONTEXT7_API_KEY"
        },
        "disabled": true
      }
    }
    ```

## MCP Server Management

### Installation Requirements

**Node.js Servers (NPX)**:

- Requires Node.js 18+ and npm
- Servers are downloaded automatically on first use
- Use `-y` flag to skip confirmation prompts

**Python Servers (UVX)**:

- Requires `uv` package manager installation
- Install via: `pip install uv` or platform-specific installer
- `uvx` downloads and runs packages automatically

**Laravel Servers**:

- Implemented within Laravel applications using Laravel MCP
- Registered in `routes/ai.php`
- Can be web-based (HTTP) or local (stdio)

**Local Servers**:

- Must be implemented within your application
- Follow framework-specific MCP implementation patterns

### ICTServe MCP Configuration

**Complete MCP Configuration for ICTServe**:

```json
{
  "mcpServers": {
    "memory": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory"],
      "env": {
        "MEMORY_FILE_PATH": "./memory.jsonl"
      },
      "autoApprove": [
        "create_entities", "create_relations", "add_observations",
        "delete_entities", "delete_observations", "delete_relations",
        "read_graph", "search_nodes", "open_nodes"
      ]
    },
    "sequentialthinking": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-sequential-thinking"],
      "autoApprove": ["sequentialthinking"]
    },
    "fetch": {
      "command": "uvx",
      "args": ["--native-tls", "mcp-server-fetch"],
      "autoApprove": ["fetch"]
    },
    "filesystem": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-filesystem"],
      "env": {
        "ALLOWED_DIRECTORIES": "./,/tmp"
      },
      "autoApprove": [
        "read_file", "write_file", "list_directory",
        "create_directory", "get_file_info"
      ]
    },
    "laravel-boost": {
      "command": "php",
      "args": ["artisan", "mcp:start", "boost"],
      "autoApprove": [
        "application_info", "search_docs", "database_query",
        "database_schema", "tinker", "list_routes",
        "get_config", "read_log_entries", "browser_logs"
      ]
    },
    "chrome-devtools": {
      "command": "npx",
      "args": ["-y", "chrome-devtools-mcp@latest"],
      "autoApprove": [
        "navigate_page", "take_snapshot", "click", "fill",
        "evaluate_script", "take_screenshot", "list_pages"
      ]
    },
    "playwright": {
      "command": "npx",
      "args": ["-y", "@playwright/mcp@latest"],
      "autoApprove": [
        "browser_navigate", "browser_click", "browser_snapshot",
        "browser_fill", "browser_evaluate", "browser_take_screenshot"
      ]
    },
    "git": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-git"],
      "autoApprove": [
        "git_status", "git_log_or_diff", "git_branch",
        "git_add_or_commit", "git_blame", "git_stash"
      ]
    }
  }
}
```

### Server Lifecycle

1. **Startup**: Servers start automatically when Kiro connects
2. **Reconnection**: Servers reconnect on configuration changes
3. **Shutdown**: Servers stop when Kiro closes or disconnects
4. **Error Recovery**: Failed servers retry with exponential backoff

### Debugging Server Issues

**Common Problems**:

- Missing dependencies (Node.js, Python, uv)
- Invalid API keys or environment variables
- Network connectivity issues
- Port conflicts for local servers

**Debugging Steps**:

1. Check server status in Kiro MCP panel
2. Verify environment variables are set
3. Test server manually via command line
4. Check logs for error messages
5. Validate configuration syntax

## Security & Best Practices

### API Key Management

**DO**:

- Store API keys in system environment variables
- Use descriptive environment variable names
- Rotate keys regularly
- Use least-privilege access tokens

**DON'T**:

- Commit API keys to version control
- Share keys in plain text
- Use overly broad permissions
- Hardcode keys in configuration files

### Auto-Approval Guidelines

**Safe to Auto-Approve**:

- Read-only operations (fetch, search, query)
- Memory operations (create_entities, search_nodes)
- Browser navigation and screenshots
- Documentation retrieval

**Require Manual Approval**:

- File system modifications
- Database writes
- External API calls with side effects
- System configuration changes

### Network Security

- Use HTTPS for remote servers when possible
- Validate SSL certificates (`--native-tls` flag)
- Implement rate limiting for public servers
- Use authentication for sensitive servers

## Advanced MCP Patterns

### Memory-Driven Development Workflow

**MANDATORY Integration**: All development work must integrate with the Memory MCP server.

**Pre-Development**:

```bash
# Query existing patterns
search_nodes "laravel filament resource patterns"
open_nodes ["ictserve_system_spec", "laravel_12_patterns"]
```

**During Development**:

```bash
# Store discoveries and decisions
create_entities [{
  "name": "filament_v4_resource_pattern",
  "entityType": "development_pattern",
  "observations": ["New pattern discovered...", "Implementation details..."]
}]
```

**Post-Development**:

```bash
# Document completion and create relations
add_observations [{
  "entityName": "ictserve_implementation_status",
  "contents": ["Feature X completed successfully", "Tests passing"]
}]

create_relations [{
  "from": "new_feature_pattern",
  "to": "ictserve_system_spec",
  "relationType": "implements"
}]
```

### Sequential Thinking for Complex Features

**Use Cases**:

- Multi-phase feature implementation
- Architecture decision analysis
- Bug investigation and resolution
- Performance optimization planning

**Example Workflow**:

```bash
sequentialthinking({
  "thought": "Analyzing ICTServe helpdesk integration requirements...",
  "thoughtNumber": 1,
  "totalThoughts": 8,
  "nextThoughtNeeded": true
})

# Continue with adaptive thinking process
sequentialthinking({
  "thought": "Considering hybrid architecture implications...",
  "thoughtNumber": 2,
  "totalThoughts": 8,
  "isRevision": false,
  "nextThoughtNeeded": true
})
```

### Laravel Boost Integration Patterns

**Documentation-First Development**:

```bash
# Always search docs before implementation
search_docs({
  "queries": ["filament v4 resources", "livewire forms", "laravel validation"],
  "packages": ["filament/filament", "livewire/livewire"]
})

# Verify current application state
application_info()
database_schema()
```

**Development Workflow**:

```bash
# Experiment with tinker
tinker({
  "code": "HelpdeskTicket::factory()->create(['status' => 'open'])",
  "timeout": 30
})

# Check database state
database_query({
  "query": "SELECT status, COUNT(*) FROM helpdesk_tickets GROUP BY status"
})

# Monitor application logs
read_log_entries({"entries": 20})
```

### Filesystem Operations for Code Generation

**Safe File Operations**:

```bash
# Read existing patterns
read_file({"path": "./app/Filament/Resources/HelpdeskTicketResource.php"})

# Create new files with proper structure
write_file({
  "path": "./app/Filament/Resources/AssetLoanResource.php",
  "content": "<?php\n\ndeclare(strict_types=1);\n\n..."
})

# List project structure
list_directory({"path": "./app/Filament", "recursive": true})
```

### Other Frameworks

- **Express.js**: Use `@modelcontextprotocol/sdk` npm package
- **FastAPI**: Use `mcp` Python package
- **Django**: Implement custom MCP protocol handlers
- **Ruby on Rails**: Use community MCP gems

## Troubleshooting Guide

### Server Won't Start

1. **Check Dependencies**:

   ```bash
   node --version  # For NPX servers
   uv --version    # For UVX servers
   ```

2. **Verify Configuration**:
   - JSON syntax is valid
   - Environment variables exist
   - Paths are correct

3. **Test Manually**:

   ```bash
   npx -y @modelcontextprotocol/server-memory
   uvx --native-tls mcp-server-fetch
   ```

### Authentication Failures

1. **Check API Keys**:

   ```bash
   echo $GITHUB_TOKEN
   echo $CONTEXT7_API_KEY
   ```

2. **Verify Permissions**:
   - API key has required scopes
   - Rate limits not exceeded
   - Service is operational

### Performance Issues

1. **Memory Usage**: Monitor server memory consumption
2. **Network Latency**: Use local servers when possible
3. **Rate Limiting**: Implement request throttling
4. **Caching**: Cache responses for read-only operations

### Connection Problems

1. **Port Conflicts**: Check if ports are available
2. **Firewall Rules**: Ensure network access
3. **SSL Issues**: Use `--native-tls` for HTTPS
4. **Proxy Settings**: Configure proxy if needed

## Advanced Configuration

### Custom Environment Setup

```json
{
  "custom-server": {
    "command": "node",
    "args": ["server.js"],
    "env": {
      "NODE_ENV": "production",
      "DEBUG": "mcp:*",
      "PORT": "3000"
    },
    "cwd": "/path/to/server"
  }
}
```

### Conditional Server Loading

Use environment-based configuration:

```json
{
  "development-only": {
    "command": "npx",
    "args": ["-y", "dev-server"],
    "disabled": "$env:NODE_ENV !== 'development'"
  }
}
```

### Server Chaining

Configure servers that depend on others:

```json
{
  "primary-server": {
    "command": "npx",
    "args": ["-y", "primary-mcp"],
    "disabled": false
  },
  "secondary-server": {
    "command": "npx",
    "args": ["-y", "secondary-mcp"],
    "env": {
      "PRIMARY_URL": "http://localhost:3000"
    },
    "disabled": false
  }
}
```

## ICTServe-Specific MCP Workflows

### Helpdesk Module Development

**Memory Integration**:

```bash
# Store helpdesk patterns
create_entities [{
  "name": "helpdesk_hybrid_pattern",
  "entityType": "architecture_pattern",
  "observations": [
    "Guest + authenticated dual access pattern",
    "Nullable user_id foreign key for hybrid support",
    "Email-based tracking for guest submissions"
  ]
}]
```

**Laravel Boost Verification**:

```bash
# Verify helpdesk table structure
database_schema({"filter": "helpdesk"})

# Test helpdesk model relationships
tinker({
  "code": "HelpdeskTicket::with(['user', 'category'])->first()",
  "timeout": 30
})
```

### Asset Loan Module Development

**Sequential Planning**:

```bash
sequentialthinking({
  "thought": "Planning asset loan approval workflow integration...",
  "thoughtNumber": 1,
  "totalThoughts": 6,
  "nextThoughtNeeded": true
})
```

**Implementation Verification**:

```bash
# Check asset loan relationships
database_query({
  "query": "SELECT la.*, li.name as item_name FROM loan_applications la JOIN loan_items li ON la.item_id = li.id LIMIT 5"
})
```

### AI Chatbot Integration (D18)

**Documentation Research**:

```bash
# Fetch latest AI integration patterns
fetch({
  "url": "https://docs.aws.amazon.com/bedrock/latest/userguide/agents.html",
  "max_length": 8000
})

# Store AI patterns in memory
create_entities [{
  "name": "ollama_bedrock_hybrid_pattern",
  "entityType": "ai_integration_pattern",
  "observations": ["Cloud hybrid AI architecture", "Local Ollama + AWS Bedrock"]
}]
```

## Best Practices Summary

### ICTServe Development Standards

**Memory-First Approach**:

- Always query memory before starting new work
- Store all patterns and decisions for reuse
- Create relations between related concepts
- Update implementation status continuously

**Laravel Boost Integration**:

- Use `search_docs` before any Laravel/Filament work
- Verify application state with `application_info`
- Test with `tinker` before implementing
- Monitor with `read_log_entries` and `browser_logs`

**Sequential Thinking for Complexity**:

- Use for multi-phase feature development
- Apply to architecture decisions
- Employ for debugging complex issues
- Utilize for performance optimization

### Configuration Management

- Use version control for MCP configurations
- Document server purposes and dependencies
- Implement environment-specific configs
- Regular security audits of API keys

### Development Workflow

- Test servers locally before deployment
- Use auto-approval judiciously for safe operations
- Monitor server performance and logs
- Implement graceful error handling

### Security Considerations

- Principle of least privilege for API keys
- Regular rotation of authentication tokens
- Network security for remote servers
- Audit logs for sensitive operations
- PDPA 2010 compliance for Malaysian data

### Performance Optimization

- Prefer local servers for frequent operations
- Implement caching for read-heavy workloads
- Use connection pooling for database servers
- Monitor and optimize memory usage
- Chunk large operations appropriately

## Resources

### Official Documentation

- [MCP Specification](https://spec.modelcontextprotocol.io/)
- [MCP SDK Documentation](https://modelcontextprotocol.io/docs)
- [Laravel MCP Documentation](https://laravel.com/docs/mcp)

### Community Resources

- [MCP Server Registry](https://github.com/modelcontextprotocol/servers)
- [Example Implementations](https://github.com/modelcontextprotocol/examples)
- [Community Discord](https://discord.gg/modelcontextprotocol)

### Tools & Utilities

- [MCP Inspector](https://github.com/modelcontextprotocol/inspector)
- [MCP Client Libraries](https://github.com/modelcontextprotocol/typescript-sdk)
- [Server Templates](https://github.com/modelcontextprotocol/create-mcp-server)
