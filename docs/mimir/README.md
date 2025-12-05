# Mimir Memory System - Documentation

**Version**: 4.1.0  
**Status**: ✅ Operational  
**Last Updated**: 2025-12-05

---

## Overview

Mimir is an advanced AI memory and knowledge management system integrated as a Git submodule in ICTServe. It provides persistent, cross-session knowledge storage using Neo4j graph database with vector embeddings for semantic search.

### Key Features

- **Knowledge Graph Storage** - Neo4j-powered graph database
- **Vector Search** - Semantic search with embeddings
- **Task Management** - Todo system with context filtering
- **Codebase Indexing** - Automatic file analysis and monitoring
- **MCP Integration** - 17 tools for AI agent workflows
- **Workflow Orchestration** - Parallel LLM agent execution

---

## Quick Start

### Start Services

```powershell
cd Mimir
docker compose up -d
```

### Verify Health

```powershell
curl http://localhost:9042/health
```

**Expected Response**:

```json
{"status":"healthy","version":"4.1.0","mode":"shared-session","tools":17}
```

### Access Interfaces

- **Mimir Portal**: <http://localhost:9042/portal>
- **Neo4j Browser**: <http://localhost:7474> (neo4j / MxXhTKH3qntipYLa1e0QOluJ)
- **MCP Endpoint**: <http://localhost:9042/mcp> (for Kiro IDE)

---

## Documentation Index

### Getting Started

1. **[Setup Guide](01-SETUP.md)** - Installation and configuration
2. **[Docker Deployment](02-DOCKER.md)** - Docker setup and management
3. **[Quick Reference](03-QUICK-REFERENCE.md)** - Common commands and URLs

### Integration

4. **[MCP Integration](04-MCP-INTEGRATION.md)** - Kiro IDE integration
5. **[Submodule Management](05-SUBMODULE.md)** - Git submodule operations
6. **[API Reference](06-API-REFERENCE.md)** - HTTP API and tools

### Advanced

7. **[Neo4j Knowledge Graph](07-NEO4J-GUIDE.md)** - Graph database usage
8. **[Embeddings Configuration](08-EMBEDDINGS.md)** - Vector search setup
9. **[Workflow Orchestration](09-WORKFLOWS.md)** - Multi-agent workflows

### Maintenance

10. **[Troubleshooting](10-TROUBLESHOOTING.md)** - Common issues and solutions
11. **[Changelog](11-CHANGELOG.md)** - Version history and updates

---

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│              Mimir Docker Stack                         │
│                                                         │
│  ┌──────────────┐    ┌──────────────┐   ┌──────────┐ │
│  │ mimir-server │───▶│    neo4j     │   │  ollama  │ │
│  │    :9042     │    │  :7474,:7687 │   │  :11434  │ │
│  └──────┬───────┘    └──────────────┘   └──────────┘ │
│         │                                              │
│         ▼                                              │
│  ┌──────────────┐                                     │
│  │ copilot-api  │                                     │
│  │    :4141     │                                     │
│  └──────────────┘                                     │
└─────────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────┐
│              ICTServe Workspace                         │
│         C:\laragon\www\ictserve-031125                 │
└─────────────────────────────────────────────────────────┘
```

---

## Services

| Service | Container | Port | Status | Purpose |
|---------|-----------|------|--------|---------|
| **Mimir Server** | mimir_server | 9042 | ✅ Healthy | Main API and portal |
| **Neo4j** | neo4j_db | 7474, 7687 | ✅ Healthy | Graph database |
| **Copilot API** | copilot_api_server | 4141 | ✅ Healthy | LLM provider |
| **Ollama** | ollama_server | 11434 | ✅ Healthy | Embeddings provider |

---

## MCP Tools (17 Available)

### Memory Operations

- `memory_node` - Manage memory nodes (CRUD + search)
- `memory_edge` - Manage relationships
- `memory_batch` - Bulk operations
- `memory_lock` - Multi-agent coordination
- `memory_clear` - Clear graph data

### File Indexing

- `index_folder` - Index and watch folders
- `remove_folder` - Stop watching
- `list_folders` - List watched folders

### Search & Analytics

- `vector_search_nodes` - Semantic search
- `get_embedding_stats` - Embedding statistics

### Task Management

- `todo` - Manage individual todos
- `todo_list` - Manage todo lists
- `get_task_context` - Get filtered context

### Workflow Orchestration

- `execute_workflow` - Execute parallel workflows
- `get_execution_status` - Check workflow status
- `get_execution_results` - Get results
- `cancel_execution` - Cancel workflow

---

## Configuration

### Environment Variables

Key settings in `Mimir/.env`:

```env
# LLM Provider (GitHub Copilot)
MIMIR_DEFAULT_PROVIDER=openai
MIMIR_DEFAULT_MODEL=gpt-4.1
MIMIR_LLM_API=http://copilot-api:4141

# Embeddings (Ollama nomic-embed-text)
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_PROVIDER=ollama
MIMIR_EMBEDDINGS_MODEL=nomic-embed-text
MIMIR_EMBEDDINGS_API=http://ollama:11434
MIMIR_EMBEDDINGS_DIMENSIONS=768

# Database
NEO4J_URI=bolt://neo4j:7687
NEO4J_USER=neo4j
NEO4J_PASSWORD=MxXhTKH3qntipYLa1e0QOluJ

# Workspace
HOST_WORKSPACE_ROOT=C:\laragon\www\ictserve-031125
WORKSPACE_ROOT=/workspace
```

---

## Integration with ICTServe

Mimir is integrated as a Git submodule:

```bash
# Initialize submodule
git submodule update --init --recursive

# Update to latest
cd Mimir
git pull origin main
cd ..
git add Mimir
git commit -m "Update Mimir submodule"
```

### Kiro IDE Configuration

**File**: `.kiro/settings/mcp.json`

```json
{
  "mcpServers": {
    "mimir": {
      "url": "http://localhost:9042/mcp",
      "disabled": false,
      "autoApprove": [
        "memory_node",
        "memory_edge",
        "vector_search_nodes",
        "todo",
        "todo_list"
      ]
    }
  }
}
```

---

## Status

✅ **Fully Operational**

- All services healthy
- MCP endpoint responding
- Neo4j database accessible
- Embeddings enabled (Ollama nomic-embed-text)
- 17 tools available
- Kiro IDE integration configured

---

## Quick Commands

```powershell
# Start services
cd Mimir
docker compose up -d

# Check status
docker compose ps

# View logs
docker compose logs -f mimir-server

# Restart service
docker restart mimir_server

# Stop services
docker compose down

# Health check
curl http://localhost:9042/health
```

---

## Support

### Documentation

- See individual guides in this directory
- Check `10-TROUBLESHOOTING.md` for common issues

### Access Points

- **Portal**: <http://localhost:9042/portal>
- **Neo4j**: <http://localhost:7474>
- **Health**: <http://localhost:9042/health>

### Logs

```powershell
docker logs mimir_server --tail 100
docker logs neo4j_db --tail 50
```

---

**Next Steps**: See [01-SETUP.md](01-SETUP.md) for detailed installation guide.
