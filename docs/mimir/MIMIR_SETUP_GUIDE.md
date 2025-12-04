# Mimir MCP Server Setup Guide

## Current Status

✅ **Mimir Submodule**: Initialized and built  
✅ **Build Output**: `Mimir/build/index.js` exists  
⏸️ **MCP Server**: Disabled (requires Docker services)

## Why Mimir is Disabled

Mimir requires a full Docker stack to run:

- **Neo4j** database (port 7687/7474)
- **Mimir Server** (port 9042)
- **Optional**: Copilot API or Ollama for embeddings

The built-in `memory` MCP server provides sufficient functionality for most use cases without Docker dependencies.

## Quick Start: Use Built-in Memory Server

The `memory` MCP server is already enabled and provides:

- Knowledge graph storage (JSONL file)
- Entity and relationship management
- Cross-session persistence
- No Docker required

**Location**: `.kiro/settings/mcp.json` → `memory` server (already enabled)

## Advanced: Enable Mimir with Docker

### Step 1: Start Mimir Docker Stack

```powershell
# Navigate to Mimir directory
cd Mimir

# Copy environment template
Copy-Item env.example .env

# Edit .env and set NEO4J_PASSWORD
# Example: NEO4J_PASSWORD=MxXhTKH3qntipYLa1e0QOluJ

# Start Docker services
npm run start
# Or: docker compose up -d
```

### Step 2: Verify Services

```powershell
# Check health
curl http://localhost:9042/health

# Expected response:
# {"status":"healthy","version":"1.0.0","tools":13}

# Check Neo4j
# Open browser: http://localhost:7474
# Login: neo4j / [your NEO4J_PASSWORD]
```

### Step 3: Enable Mimir in Kiro

Edit `.kiro/settings/mcp.json`:

```json
"mimir": {
  "disabled": false,  // Change from true to false
  ...
}
```

### Step 4: Restart Kiro IDE

Reload the MCP servers or restart Kiro IDE to connect to Mimir.

## Mimir vs Built-in Memory

| Feature | Built-in Memory | Mimir |
|---------|----------------|-------|
| Storage | JSONL file | Neo4j graph database |
| Search | Text matching | Semantic search with embeddings |
| Visualization | None | Neo4j Browser + Web UI |
| File Indexing | No | Yes (automatic code indexing) |
| Task Management | No | Yes (TODO tracking) |
| Setup Complexity | None | Docker stack required |
| Resource Usage | Minimal | ~1GB+ (Neo4j) |

## Troubleshooting

### Error: "Cannot find module 'build/index.js'"

**Solution**: Build Mimir first

```powershell
cd Mimir
npm install
npm run build
```

### Error: "Connection refused" or "Neo4j not available"

**Solution**: Start Docker services

```powershell
cd Mimir
npm run start
```

### Error: "Authentication failed"

**Solution**: Check Neo4j password in `.env`

```powershell
# Mimir/.env
NEO4J_PASSWORD=your_password_here
```

## References

- **Full Documentation**: `docs/mimir/MCP_INTEGRATION.md`
- **Mimir README**: `Mimir/README.md`
- **Neo4j Guide**: `docs/mimir/NEO4J_KNOWLEDGE_GRAPH_GUIDE.md`

---

**Last Updated**: 2025-12-04  
**Mimir Version**: 1.0.0  
**Status**: Built and ready (Docker services required to enable)
