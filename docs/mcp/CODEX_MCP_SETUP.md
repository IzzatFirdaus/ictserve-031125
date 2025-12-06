# Codex MCP Server Setup Guide

Complete guide for configuring Model Context Protocol (MCP) servers with Codex extension.

## Overview

MCP servers extend Codex capabilities with:
- **Memory**: Persistent context across sessions
- **Sequential Thinking**: Complex reasoning tasks
- **Chrome DevTools**: Browser automation and debugging
- **Playwright**: E2E testing (optional)
- **Laravel Boost**: Laravel-specific tools

## Configuration File

Location: `C:\Users\exatf\.codex\config.toml`

## Quick Start

### Option 1: Local Servers (Default)

Use npm-based servers running on your machine.

**Prerequisites:**
```powershell
# Install MCP servers globally
npm install -g @modelcontextprotocol/server-memory
npm install -g @modelcontextprotocol/server-sequential-thinking
npm install -g chrome-devtools-mcp
```

**Status:** Already configured and enabled in config.toml

### Option 2: Docker Servers

Use containerized MCP servers via Docker Compose.

**Start Docker servers:**
```powershell
# Start all MCP containers
docker compose up -d mcp-memory mcp-sequential-thinking mcp-chrome-devtools

# Verify containers are running
docker ps --filter "name=ictserve-mcp"
```

**Switch to Docker mode:**
```powershell
.\scripts\switch-mcp-mode.ps1 -Mode docker
```

## Switching Between Modes

### Automated Switch

```powershell
# Switch to Docker mode
.\scripts\switch-mcp-mode.ps1 -Mode docker

# Switch to local mode
.\scripts\switch-mcp-mode.ps1 -Mode local
```

### Manual Switch

Edit `C:\Users\exatf\.codex\config.toml`:

**For Local Mode:**
```toml
[mcp_servers.memory]
disabled = false  # Enable local

[mcp_servers.memory-docker]
disabled = true   # Disable Docker
```

**For Docker Mode:**
```toml
[mcp_servers.memory]
disabled = true   # Disable local

[mcp_servers.memory-docker]
disabled = false  # Enable Docker
```

## Server Details

### Memory Server

**Purpose:** Persistent context storage across sessions

**Local:**
- Command: Node.js
- Path: `@modelcontextprotocol/server-memory`
- Status: Enabled by default

**Docker:**
- Container: `ictserve-mcp-memory`
- Command: `docker exec -i ictserve-mcp-memory node /app/dist/index.js`

### Sequential Thinking Server

**Purpose:** Complex reasoning and multi-step problem solving

**Local:**
- Command: Node.js
- Path: `@modelcontextprotocol/server-sequential-thinking`
- Status: Enabled by default

**Docker:**
- Container: `ictserve-mcp-sequential-thinking`
- Command: `docker exec -i ictserve-mcp-sequential-thinking node /app/dist/index.js`

### Chrome DevTools Server

**Purpose:** Browser automation and debugging

**Local:**
- Command: Node.js
- Path: `chrome-devtools-mcp`
- Status: Enabled by default

**Docker:**
- Container: `ictserve-mcp-chrome-devtools`
- Command: `docker exec -i ictserve-mcp-chrome-devtools node /app/build/index.js`

### Laravel Boost Server

**Purpose:** Laravel-specific development tools

**Type:** Local PHP artisan command
- Command: `php artisan boost:mcp`
- Status: Enabled by default
- Requires: Laravel application

## Verification

### Check Codex Extension

1. Open VS Code
2. View → Output
3. Select "Codex" from dropdown
4. Look for MCP server initialization messages

### Test MCP Servers

**Local servers:**
```powershell
# Test memory server
node "C:\Users\exatf\AppData\Roaming\npm\node_modules\@modelcontextprotocol\server-memory\dist\index.js"

# Should show MCP protocol initialization
```

**Docker servers:**
```powershell
# Test memory container
docker exec -i ictserve-mcp-memory node /app/dist/index.js

# Should show MCP protocol initialization
```

### Check Docker Containers

```powershell
# List MCP containers
docker ps --filter "name=ictserve-mcp" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

# View logs
docker compose logs -f mcp-memory
docker compose logs -f mcp-sequential-thinking
docker compose logs -f mcp-chrome-devtools
```

## Troubleshooting

### Servers Not Starting

**Check Codex output:**
1. VS Code → View → Output
2. Select "Codex"
3. Look for error messages

**Common issues:**

1. **Node.js not found**
   - Verify: `node --version`
   - Install: https://nodejs.org/

2. **npm packages not installed**
   ```powershell
   npm install -g @modelcontextprotocol/server-memory
   npm install -g @modelcontextprotocol/server-sequential-thinking
   npm install -g chrome-devtools-mcp
   ```

3. **Docker containers not running**
   ```powershell
   docker compose up -d mcp-memory mcp-sequential-thinking mcp-chrome-devtools
   ```

4. **Wrong container names**
   - Verify: `docker ps --filter "name=ictserve-mcp"`
   - Should show: `ictserve-mcp-memory`, `ictserve-mcp-sequential-thinking`, etc.

### Timeout Errors

Increase timeout in config.toml:
```toml
[mcp_servers.memory]
startup_timeout_sec = 180  # Increase from 120
```

### Permission Errors (Docker)

```powershell
# Ensure Docker is running
docker ps

# Restart Docker Desktop if needed
```

## Advanced Configuration

### Custom Server Paths

Edit config.toml to use custom paths:

```toml
[mcp_servers.memory]
command = 'C:\\custom\\path\\node.exe'
args = ['C:\\custom\\path\\server-memory\\dist\\index.js']
```

### Environment Variables

Add environment variables to servers:

```toml
[mcp_servers.custom]
command = 'node'
args = ['server.js']
env = { 
    API_KEY = 'your-key',
    DEBUG = 'true'
}
```

### Multiple Configurations

Create separate config files:

```powershell
# Development config
C:\Users\exatf\.codex\config.dev.toml

# Production config
C:\Users\exatf\.codex\config.prod.toml

# Copy to active config
Copy-Item config.dev.toml config.toml
```

## Performance Tips

1. **Use Docker for consistency** - Same environment across machines
2. **Use local for speed** - Faster startup, no container overhead
3. **Disable unused servers** - Set `disabled = true` for servers you don't need
4. **Monitor resource usage** - Check Task Manager for memory/CPU usage

## Security Notes

1. **API Keys** - Use `$input:API_KEY` for sensitive values
2. **File Permissions** - Ensure config.toml is readable only by your user
3. **Docker Security** - Keep Docker Desktop updated
4. **Network Access** - MCP servers use stdio (no network exposure)

## References

- [MCP Specification](https://modelcontextprotocol.io/)
- [Codex Documentation](https://docs.codex.dev/)
- [Docker Compose Reference](https://docs.docker.com/compose/)
- [ICTServe MCP Setup](./MCP_SETUP.md)

## Support

**Issues:**
- Check Codex output panel for errors
- Review Docker logs: `docker compose logs mcp-memory`
- Verify container status: `docker ps`

**Scripts:**
- Switch mode: `.\scripts\switch-mcp-mode.ps1`
- Health check: `.\scripts\mcp-health-check.ps1`
- Test servers: `.\scripts\test-mcp-servers.ps1`

---

**Last Updated:** 2025-01-22  
**Version:** 1.0.0
