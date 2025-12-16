---
inclusion: always
---

# Model Context Protocol (MCP) Guidelines

## Overview

The Model Context Protocol (MCP) is an open standard that enables AI assistants to securely connect to data sources and tools. This steering file provides comprehensive guidelines for configuring, using, and troubleshooting MCP servers across all projects.

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

## MCP Server Configuration

### Configuration File Structure

MCP servers are configured in `.kiro/settings/mcp.json`:

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

   ```json
   {
     "memory": {
       "command": "npx",
       "args": ["-y", "@modelcontextprotocol/server-memory"],
       "env": {
         "MEMORY_FILE_PATH": "/path/to/memory.jsonl"
       },
       "autoApprove": [
         "create_entities", "create_relations", "add_observations",
         "delete_entities", "delete_observations", "delete_relations",
         "read_graph", "search_nodes", "open_nodes"
       ]
     }
   }
   ```

2. **Sequential Thinking**: Complex problem decomposition

   ```json
   {
     "sequentialthinking": {
       "command": "npx",
       "args": ["-y", "@modelcontextprotocol/server-sequential-thinking"],
       "autoApprove": ["sequentialthinking"]
     }
   }
   ```

3. **Fetch Server**: HTTP requests and web content retrieval

   ```json
   {
     "fetch": {
       "command": "uvx",
       "args": ["--native-tls", "mcp-server-fetch"],
       "autoApprove": ["fetch"]
     }
   }
   ```

### Browser & Testing Servers

4. **Chrome DevTools**: Browser automation and debugging

   ```json
   {
     "chrome-devtools": {
       "command": "npx",
       "args": ["-y", "chrome-devtools-mcp@latest"],
       "autoApprove": [
         "navigate_page", "take_snapshot", "click", "fill",
         "evaluate_script", "take_screenshot", "list_pages"
       ]
     }
   }
   ```

5. **Playwright**: E2E testing and browser automation

   ```json
   {
     "playwright": {
       "command": "npx",
       "args": ["-y", "@playwright/mcp@latest"],
       "autoApprove": [
         "browser_navigate", "browser_click", "browser_snapshot",
         "browser_fill", "browser_evaluate", "browser_take_screenshot"
       ]
     }
   }
   ```

### Optional Enhancement Servers

6. **GitHub Integration**: Repository operations

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

7. **Context Enhancement**: Library documentation

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

**Local Servers**:

- Must be implemented within your application
- Follow framework-specific MCP implementation patterns

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

## Framework-Specific Integration

### Laravel MCP Integration

For Laravel applications, implement custom MCP servers:

1. **Install Laravel MCP**:

   ```bash
   composer require laravel/mcp
   php artisan vendor:publish --tag=ai-routes
   ```

2. **Create MCP Server**:

   ```bash
   php artisan make:mcp-server MyServer
   ```

3. **Register Server**:

   ```php
   // routes/ai.php
   use Laravel\Mcp\Facades\Mcp;
   
   Mcp::local('my-server', MyServer::class);
   ```

4. **Configuration**:

   ```json
   {
     "my-server": {
       "command": "php",
       "args": ["artisan", "mcp:start", "my-server"],
       "cwd": "/path/to/laravel/project"
     }
   }
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

## Best Practices Summary

### Configuration Management

- Use version control for MCP configurations
- Document server purposes and dependencies
- Implement environment-specific configs
- Regular security audits of API keys

### Development Workflow

- Test servers locally before deployment
- Use auto-approval judiciously
- Monitor server performance and logs
- Implement graceful error handling

### Security Considerations

- Principle of least privilege for API keys
- Regular rotation of authentication tokens
- Network security for remote servers
- Audit logs for sensitive operations

### Performance Optimization

- Prefer local servers for frequent operations
- Implement caching for read-heavy workloads
- Use connection pooling for database servers
- Monitor and optimize memory usage

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
