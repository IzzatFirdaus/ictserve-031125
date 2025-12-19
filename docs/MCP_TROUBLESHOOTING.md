# MCP Server Troubleshooting Guide

## Windows Node.js Permission Issues

### Problem
Several MCP servers (sequentialthinking, memory, chrome-devtools, playwright) fail to start on Windows with the error:

```
Error: EPERM: operation not permitted, lstat 'C:\Users\admin.install'
```

### Root Cause
This is a Node.js installation issue on Windows where Node.js cannot resolve certain user profile paths. The `admin.install` path suggests a Windows installation or user profile corruption.

### Solutions (in order of preference)

#### Solution 1: Fix Node.js Installation
1. **Reinstall Node.js** with administrator privileges
2. **Clear npm cache**: `npm cache clean --force`
3. **Reset npm configuration**: `npm config delete prefix`

#### Solution 2: Use Alternative Node.js Installation
1. Install Node.js via **Chocolatey**: `choco install nodejs`
2. Or use **nvm-windows**: Install specific Node.js version
3. Or use **Volta**: Modern Node.js version manager

#### Solution 3: Use Docker-based MCP Servers
Create Docker containers for problematic MCP servers:

```dockerfile
# Dockerfile.mcp-sequential
FROM node:18-alpine
RUN npm install -g @modelcontextprotocol/server-sequential-thinking
CMD ["npx", "@modelcontextprotocol/server-sequential-thinking"]
```

Update `.mcp.json`:
```json
{
  "sequentialthinking": {
    "command": "docker",
    "args": ["run", "--rm", "-i", "mcp-sequential"],
    "disabled": false
  }
}
```

#### Solution 4: Use WSL2 (Windows Subsystem for Linux)
1. Install WSL2 with Ubuntu
2. Install Node.js in WSL2
3. Run MCP servers from WSL2 environment

### Temporary Workaround
The affected servers are currently disabled in `.mcp.json`. You can still use:
- ✅ **laravel-boost** - Works (PHP-based)
- ✅ **fetch** - Works (if Node.js path resolution succeeds)
- ✅ **herd** - Works (PHP-based)

### Alternative Tools
While fixing Node.js issues:

**Instead of sequentialthinking**:
- Use manual step-by-step planning in comments
- Break complex tasks into smaller, manageable pieces

**Instead of memory**:
- Use local JSON files for storing development notes
- Document patterns in `docs/` folder
- Use Git commit messages for tracking decisions

**Instead of chrome-devtools/playwright**:
- Use browser developer tools manually
- Use Laravel Dusk for browser testing
- Use Postman/Insomnia for API testing

### Verification Commands
Test if Node.js works properly:

```bash
# Test basic Node.js
node --version

# Test npm
npm --version

# Test npx with simple package
npx --version

# Test with problematic path resolution
npx -y cowsay "test"
```

### Prevention
1. **Regular Node.js updates** via official installer
2. **Avoid** installing Node.js in system directories
3. **Use** user-space installations when possible
4. **Keep** npm cache clean: `npm cache clean --force`

## Status
- **Current**: ✅ **RESOLVED** - Using Docker-based MCP servers
- **Solution**: All problematic MCP servers now run in Docker containers
- **Impact**: ✅ Full MCP functionality restored

## IMPORTANT: Kiro Configuration Files
Kiro uses **workspace-specific** MCP configuration files that override the root `.mcp.json`:

- **Primary Config**: `.kiro/settings/mcp.json` (used by Kiro IDE)
- **Secondary Config**: `.mcp.json` (fallback/reference)

**Both files have been updated** to use Docker containers.

## Docker Solution (IMPLEMENTED)
The ICTServe project now uses Docker containers for MCP servers to avoid Node.js path resolution issues:

### Container Status
```bash
docker ps --filter "name=ictserve-mcp"
```

### Available MCP Servers
- ✅ **sequentialthinking** - `ictserve-mcp-sequential-thinking`
- ✅ **memory** - `ictserve-mcp-memory` (with persistent storage)
- ✅ **chrome-devtools** - `ictserve-mcp-chrome-devtools`
- ✅ **playwright** - `ictserve-mcp-playwright` (with 2GB shared memory)

### Configuration
MCP servers are configured in `.mcp.json` to use `docker exec` commands:
```json
{
  "sequentialthinking": {
    "command": "docker",
    "args": ["exec", "-i", "ictserve-mcp-sequential-thinking", "npx", "@modelcontextprotocol/server-sequential-thinking"]
  }
}
```

### Maintenance Commands
```bash
# Start all services
docker compose up -d

# Check MCP container status
docker ps --filter "name=ictserve-mcp"

# View container logs
docker logs ictserve-mcp-sequential-thinking

# Restart specific MCP server
docker restart ictserve-mcp-memory
```
