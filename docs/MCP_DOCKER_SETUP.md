# MCP Docker Setup - ICTServe

## Overview
ICTServe uses Docker containers for MCP (Model Context Protocol) servers to avoid Node.js path resolution issues on Windows. This provides a reliable, isolated environment for all MCP services.

## Architecture
```
Kiro IDE → Docker Exec → MCP Container → MCP Server → Response
```

## Available MCP Servers

### 1. Sequential Thinking (`ictserve-mcp-sequential-thinking`)
- **Purpose**: Complex problem decomposition and multi-step reasoning
- **Container**: Node.js 20 Alpine with pre-installed MCP server
- **Usage**: Planning complex features, debugging workflows
- **Status**: ✅ Active

### 2. Memory (`ictserve-mcp-memory`)
- **Purpose**: Persistent knowledge graph across sessions
- **Container**: Node.js 20 Alpine with volume-mounted storage
- **Data**: `/data/memory.jsonl` (mapped to `./storage/mcp/memory.jsonl`)
- **Usage**: Storing patterns, decisions, implementation status
- **Status**: ✅ Active

### 3. Chrome DevTools (`ictserve-mcp-chrome-devtools`)
- **Purpose**: Browser automation and debugging
- **Container**: Node.js 20 Alpine with Chrome DevTools MCP
- **Usage**: Page inspection, element interaction, debugging
- **Status**: ✅ Active

### 4. Playwright (`ictserve-mcp-playwright`)
- **Purpose**: Advanced browser automation and E2E testing
- **Container**: Node.js 20 Alpine with 2GB shared memory
- **Usage**: Cross-browser testing, complex automation workflows
- **Status**: ✅ Active

## Configuration

### Docker Compose Services
```yaml
services:
  mcp-memory:
    build:
      context: ./docker
      dockerfile: Dockerfile.mcp-memory
    volumes:
      - ./storage/mcp:/data
    stdin_open: true
    tty: true

  mcp-sequential-thinking:
    build:
      context: ./docker
      dockerfile: Dockerfile.mcp-sequential-thinking
    environment:
      - DISABLE_THOUGHT_LOGGING=false
    stdin_open: true
    tty: true
```

### Kiro MCP Configuration (`.mcp.json`)
```json
{
  "sequentialthinking": {
    "command": "docker",
    "args": ["exec", "-i", "ictserve-mcp-sequential-thinking", "npx", "@modelcontextprotocol/server-sequential-thinking"],
    "disabled": false,
    "autoApprove": ["sequentialthinking"]
  },
  "memory": {
    "command": "docker", 
    "args": ["exec", "-i", "ictserve-mcp-memory", "npx", "@modelcontextprotocol/server-memory", "/data/memory.jsonl"],
    "disabled": false,
    "autoApprove": ["create_entities", "search_nodes", "open_nodes", ...]
  }
}
```

## Management Commands

### Start All Services
```bash
docker compose up -d
```

### Check MCP Container Status
```bash
docker ps --filter "name=ictserve-mcp"
```

### View Container Logs
```bash
docker logs ictserve-mcp-sequential-thinking
docker logs ictserve-mcp-memory
docker logs ictserve-mcp-chrome-devtools
docker logs ictserve-mcp-playwright
```

### Restart Specific MCP Server
```bash
docker restart ictserve-mcp-memory
```

### Access Container Shell (for debugging)
```bash
docker exec -it ictserve-mcp-sequential-thinking sh
```

### Test MCP Server Connectivity
```bash
docker exec -i ictserve-mcp-sequential-thinking echo "test"
```

## Data Persistence

### Memory Server Data
- **Host Path**: `./storage/mcp/memory.jsonl`
- **Container Path**: `/dat
