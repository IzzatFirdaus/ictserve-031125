# MCP Configuration Guide - ICTServe

## Overview
This guide provides multiple MCP (Model Context Protocol) configuration options for the ICTServe project, addressing Node.js path resolution issues on Windows and providing fallback options.

## Available Configurations

### 1. Docker Configuration (Recommended)
**File**: `.kiro/settings/mcp.json` (current) / `mcp-docker-backup.json`
**Status**: ✅ **Active and Working**

**Features**:

- ✅ Sequential Thinking - Complex problem decomposition
- ✅ Memory - Persistent knowledge graph
- ✅ Chrome DevTools - Browser automation
- ✅ Playwright - Advanced browser testing
- ✅ Fetch - HTTP requests
- ✅ GitKraken - Git operations
- ✅ Bedrock - AWS AI services

**Requirements**:

- Docker Desktop running
- Docker containers: `docker compose up -d`

**Pros**:

- Isolated environment, no Node.js path issues
- Consistent across different systems
- Easy to maintain and debug

**Cons**:

- Requires Docker Desktop
- Slightly higher resource usage

### 2. Local NPX Configuration (Alternative)
**File**: `.kiro/settings/mcp-local-alternative.json`
**Status**: ⚠️ **May have Node.js issues**

**Features**:

- ⚠️ Sequential Thinking - May fail due to Node.js paths
- ⚠️ Memory - May fail due to Node.js paths  
- ⚠️ Chrome DevTools - May fail due to Node.js paths
- ⚠️ Playwright - May fail due to Node.js paths
- ✅ Fetch - Usually works
- ✅ GitKraken - Works (not Node.js based)
- ✅ Bedrock - Works (local Node.js file)

**Requirements**:

- Node.js 18+ installed
- Global npm packages installed
- Custom environment variables for path isolation

**Pros**:

- No Docker required
- Direct npm package execution
- Faster startup times

**Cons**:

- Node.js path resolution issues on Windows
- Requires manual npm package management
- Environment variable complexity

### 3. Minimal Configuration (Fallback)
**File**: `.kiro/settings/mcp-minimal.json`
**Status**: ✅ **Most Reliable**

**Features**:

- ✅ Fetch - HTTP requests (uvx-based)
- ✅ GitKraken - Git operations
- ✅ Bedrock - AWS AI services
- ❌ Sequential Thinking - Disabled
- ❌ Memory - Disabled
- ❌ Browser tools - Disabled

**Requirements**:

- Python with `uv` package manager (for fetch)
- GitKraken installed
- Node.js (for Bedrock server only)

**Pros**:

- Most reliable, minimal dependencies
- No Node.js path issues
- Quick setup and testing

**Cons**:

- Limited functionality
- No advanced planning tools
- No browser automation

## Configuration Switching

### Quick Switch Command

```powershell
# Switch to Docker mode (recommended)
.\switch-mcp-config.ps1 -Mode docker

# Switch to Local mode (alternative)
.\switch-mcp-config.ps1 -Mode local

# Switch to Minimal mode (fallback)
.\switch-mcp-config.ps1 -Mode minimal
```

### Manual Configuration

1. **Backup current config**:

   ```powershell
   Copy-Item ".kiro/settings/mcp.json" ".kiro/settings/mcp-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss').json"
   ```

2. **Copy desired config**:

   ```powershell
   # For Docker mode
   Copy-Item ".kiro/settings/mcp-docker-backup.json" ".kiro/settings/mcp.json"
   
   # For Local mode  
   Copy-Item ".kiro/settings/mcp-local-alternative.json" ".kiro/settings/mcp.json"
   
   # For Minimal mode
   Copy-Item ".kiro/settings/mcp-minimal.json" ".kiro/settings/mcp.json"
   ```

3. **Restart Kiro IDE**

## Setup Instructions

### Docker Mode Setup

1. **Ensure Docker containers are running**:

   ```bash
   docker compose up -d
   ```

2. **Verify containers**:

   ```bash
   docker ps --filter "name=ictserve-mcp"
   ```

3. **Switch configuration**:

   ```powershell
   .\switch-mcp-config.ps1 -Mode docker
   ```

### Local Mode Setup

1. **Install Node.js packages globally**:

   ```bash
   npm install -g @modelcontextprotocol/server-memory
   npm install -g @modelcontextprotocol/server-sequential-thinking  
   npm install -g chrome-devtools-mcp
   npm install -g @playwright/mcp
   ```

2. **Create NPM directories**:

   ```powershell
   mkdir -Force .npm-cache, .npm-global, .npm-user
   ```

3. **Switch configuration**:

   ```powershell
   .\switch-mcp-config.ps1 -Mode local
   ```

### Minimal Mode Setup

1. **Install uv (Python package manager)**:

   ```bash
   pip install uv
   ```

2. **Ensure GitKraken is installed**

3. **Switch configuration**:

   ```powershell
   .\switch-mcp-config.ps1 -Mode minimal
   ```

## Troubleshooting

### Docker Mode Issues
**Problem**: Containers not running

```bash
# Check container status
docker ps --filter "name=ictserve-mcp"

# Start containers
docker compose up -d

# View logs
docker logs ictserve-mcp-sequential-thinking
```

**Problem**: Container connection issues

```bash
# Test container accessibility
docker exec -i ictserve-mcp-memory echo "test"

# Restart specific container
docker restart ictserve-mcp-memory
```

### Local Mode Issues
**Problem**: Node.js path resolution errors

- Switch to Docker mode: `.\switch-mcp-config.ps1 -Mode docker`
- Or use Minimal mode: `.\switch-mcp-config.ps1 -Mode minimal`

**Problem**: NPM packages not found

```bash
# Install missing packages
npm install -g @modelcontextprotocol/server-memory
npm list -g --depth=0 | grep modelcontextprotocol
```

### Minimal Mode Issues
**Problem**: Fetch server not working

```bash
# Install/update uv
pip install --upgrade uv

# Test uvx
uvx --help
```

## Configuration Files Reference

### File Locations

- **Active Config**: `.kiro/settings/mcp.json`
- **Docker Backup**: `.kiro/settings/mcp-docker-backup.json`
- **Local Alternative**: `.kiro/settings/mcp-local-alternative.json`
- **Minimal Config**: `.kiro/settings/mcp-minimal.json`
- **Switcher Script**: `switch-mcp-config.ps1`

### Configuration Hierarchy

1. **Kiro Workspace**: `.kiro/settings/mcp.json` (highest priority)
2. **Project Root**: `.mcp.json` (fallback)
3. **User Global**: `~/.kiro/settings/mcp.json` (lowest priority)

## Recommendations

### For Development

- **Use Docker mode** for full functionality and reliability
- Keep containers running during development sessions
- Use `docker compose logs -f mcp-memory` to monitor

### For Testing/CI

- **Use Minimal mode** for fastest, most reliable setup
- Fewer dependencies, less likely to fail
- Sufficient for basic development tasks

### For Troubleshooting

1. Start with **Minimal mode** to verify basic functionality
2. Upgrade to **Docker mode** for full features
3. Only use **Local mode** if Docker is not available

## Performance Comparison

| Feature | Docker | Local | Minimal |
|---------|--------|-------|---------|
| Startup Time | Medium | Fast | Fastest |
| Reliability | High | Low | Highest |
| Resource Usage | Medium | Low | Lowest |
| Functionality | Full | Full | Limited |
| Maintenance | Easy | Complex | Minimal |

## Support Matrix

| MCP Server | Docker | Local | Minimal |
|------------|--------|-------|---------|
| Sequential Thinking | ✅ | ⚠️ | ❌ |
| Memory | ✅ | ⚠️ | ❌ |
| Chrome DevTools | ✅ | ⚠️ | ❌ |
| Playwright | ✅ | ⚠️ | ❌ |
| Fetch | ✅ | ✅ | ✅ |
| GitKraken | ✅ | ✅ | ✅ |
| Bedrock | ✅ | ✅ | ✅ |

**Legend**:

- ✅ Fully supported and working
- ⚠️ May work but has known issues
- ❌ Not available in this configuration
