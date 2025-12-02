# Mimir - AI Memory System

Mimir is an advanced AI memory and knowledge management system integrated as a Git submodule in ICTServe.

## Overview

Mimir provides:

- **Knowledge Graph Storage** (Neo4j)
- **Vector Search** (embeddings)
- **Task Management** (todo system)
- **Codebase Indexing** (file analysis)
- **MCP Integration** (13 tools for AI agents)

## Quick Start

```bash
# Navigate to Mimir directory
cd Mimir

# Start services
make up

# Check health
curl http://localhost:9042/health

# Access portal
start http://localhost:9042/portal
```

## Documentation

- [Setup Guide](SETUP.md) - Installation and configuration
- [Docker Deployment](DOCKER.md) - Docker setup for Mimir
- [MCP Integration](MCP_INTEGRATION.md) - AI agent integration
- [Submodule Management](SUBMODULE.md) - Git submodule operations
- [Troubleshooting](TROUBLESHOOTING.md) - Common issues

## Services

| Service | Port | Description |
|---------|------|-------------|
| mimir-server | 9042 | Main Mimir API and portal |
| neo4j | 7474 | Neo4j browser interface |
| copilot-api | 4141 | GitHub Copilot API bridge |

## Architecture

```
┌─────────────────────────────────────────┐
│         Mimir Docker Stack              │
│                                         │
│  ┌──────────────┐    ┌──────────────┐ │
│  │ mimir-server │───▶│    neo4j     │ │
│  │    :9042     │    │    :7474     │ │
│  └──────────────┘    └──────────────┘ │
│         │                               │
│         ▼                               │
│  ┌──────────────┐                      │
│  │ copilot-api  │                      │
│  │    :4141     │                      │
│  └──────────────┘                      │
└─────────────────────────────────────────┘
```

## MCP Tools (13 Available)

**Memory Operations**:

- memory_node, memory_edge, memory_batch
- vector_search_nodes, vector_search_edges

**File Indexing**:

- index_folder, list_folders, get_folder_stats

**Task Management**:

- todo, todo_list, get_task_context

**System**:

- get_embedding_stats, health_check

## Configuration

Mimir uses `.env` for configuration:

```ini
# LLM Provider
MIMIR_LLM_PROVIDER=copilot  # or ollama
MIMIR_LLM_MODEL=gpt-4.1

# Embeddings
MIMIR_EMBEDDINGS_PROVIDER=copilot
MIMIR_EMBEDDINGS_MODEL=text-embedding-3-small

# Neo4j
NEO4J_URI=bolt://neo4j:7687
NEO4J_USER=neo4j
NEO4J_PASSWORD=<password>

# Workspace
MIMIR_WORKSPACE_ROOT=C:\XAMPP\htdocs\ictserve-031125
```

## Integration with ICTServe

Mimir is integrated as a Git submodule:

```bash
# Update submodule
git submodule update --init --recursive

# Pull latest changes
cd Mimir
git pull origin main
cd ..
git add Mimir
git commit -m "Update Mimir submodule"
```

## Status

✅ **Deployed and Verified**

- Health check: PASSING (200 OK)
- Portal: Accessible at <http://localhost:9042/portal>
- Neo4j: Running at <http://localhost:7474>
- MCP: 13 tools available via HTTP API

## Next Steps

1. [Setup Mimir](SETUP.md) - Complete installation guide
2. [Configure MCP](MCP_INTEGRATION.md) - AI agent integration
3. [Index Codebase](SETUP.md#indexing) - Index ICTServe project
4. [Manage Submodule](SUBMODULE.md) - Git submodule operations
