# Mimir Setup Guide

Complete installation and configuration guide for Mimir AI memory system.

## Prerequisites

- Docker 24.0+
- Docker Compose 2.20+
- 4GB RAM minimum
- 5GB disk space
- Git (for submodule management)

## Quick Start

```bash
# Navigate to Mimir directory
cd Mimir

# Start all services
make up

# Check health
curl http://localhost:9042/health

# Access portal
start http://localhost:9042/portal
```

## Detailed Setup

### 1. Initialize Submodule

```bash
# From ICTServe root
git submodule update --init --recursive

# Verify Mimir directory exists
cd Mimir
ls
```

### 2. Configure Environment

```bash
# Copy environment template
cp .env.example .env

# Edit configuration
nano .env
```

**Key Settings**:

```ini
# LLM Provider
MIMIR_LLM_PROVIDER=copilot
MIMIR_LLM_MODEL=gpt-4.1

# Embeddings
MIMIR_EMBEDDINGS_PROVIDER=copilot
MIMIR_EMBEDDINGS_MODEL=text-embedding-3-small
MIMIR_EMBEDDINGS_ENABLED=true

# Neo4j
NEO4J_URI=bolt://neo4j:7687
NEO4J_USER=neo4j
NEO4J_PASSWORD=MxXhTKH3qntipYLa1e0QOluJ

# Workspace
MIMIR_WORKSPACE_ROOT=C:\XAMPP\htdocs\ictserve-031125
```

### 3. Start Services

```bash
# Build and start
make setup

# Or manually
docker compose up -d

# Verify services
docker compose ps
```

**Expected Output**:

```text
NAME                STATUS              PORTS
mimir-neo4j         Up (healthy)        7474, 7687
mimir-copilot-api   Up                  4141
mimir-server        Up                  9042
```

### 4. Verify Installation

```bash
# Health check
curl http://localhost:9042/health

# Expected: {"status":"healthy","version":"4.1.0","tools":13}

# Test Neo4j
curl http://localhost:7474

# Test portal
start http://localhost:9042/portal
```

## Access Points

| Service | URL | Credentials |
|---------|-----|-------------|
| Mimir Portal | <http://localhost:9042/portal> | None |
| Neo4j Browser | <http://localhost:7474> | neo4j / MxXhTKH3qntipYLa1e0QOluJ |
| MCP API | <http://localhost:9042/mcp> | None |
| Health Check | <http://localhost:9042/health> | None |

## Configuration Options

### LLM Providers

**GitHub Copilot** (default):

```ini
MIMIR_LLM_PROVIDER=copilot
MIMIR_LLM_MODEL=gpt-4.1
```

**Ollama** (local):

```ini
MIMIR_LLM_PROVIDER=ollama
MIMIR_LLM_MODEL=llama3.1:8b
OLLAMA_BASE_URL=http://host.docker.internal:11434
```

### Embeddings

**Enable embeddings** for semantic search:

```ini
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_PROVIDER=copilot
MIMIR_EMBEDDINGS_MODEL=text-embedding-3-small
```

**Disable embeddings** (faster, no semantic search):

```ini
MIMIR_EMBEDDINGS_ENABLED=false
```

### Workspace

Set workspace root for file indexing:

```ini
MIMIR_WORKSPACE_ROOT=C:\XAMPP\htdocs\ictserve-031125
```

## IDE Integration

### VS Code / Cursor / Windsurf

**Not recommended** - Kiro IDE can't connect to Docker HTTP endpoint.

Use built-in memory server instead:

```json
{
  "mcpServers": {
    "memory": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory", "storage/mcp/memory.jsonl"],
      "disabled": false
    }
  }
}
```

### Direct HTTP Access

Access Mimir via HTTP API:

```bash
# Create memory node
curl -X POST http://localhost:9042/mcp \
  -H "Content-Type: application/json" \
  -d '{"method":"memory_node","params":{"action":"create","data":{"type":"task","content":"Test task"}}}'
```

## File Indexing

Index codebase for semantic search:

```bash
# Index ICTServe project
cd Mimir
npm run index:add C:\XAMPP\htdocs\ictserve-031125

# With embeddings (enables semantic search)
npm run index:add C:\XAMPP\htdocs\ictserve-031125 --embeddings

# List indexed folders
npm run index:list

# Remove folder
npm run index:remove C:\XAMPP\htdocs\ictserve-031125
```

## MCP Tools (13 Available)

**Memory Operations**:

- memory_node - Create/read/update nodes
- memory_edge - Create relationships
- memory_batch - Bulk operations
- get_task_context - Get filtered context

**File Indexing**:

- index_folder - Index code files
- list_folders - Show watched folders
- get_folder_stats - Folder statistics

**Vector Search**:

- vector_search_nodes - Semantic search
- get_embedding_stats - Embedding statistics

**Task Management**:

- todo - Manage tasks
- todo_list - Manage task lists

**System**:

- health_check - Service health

## Makefile Commands

```bash
# Setup (build + start)
make setup

# Build images
make build

# Start services
make up

# Stop services
make stop

# Restart services
make restart

# View logs
make logs

# Shell into container
make shell

# Clean (remove volumes)
make clean
```

### Optional: Running the llama.cpp server (local embeddings)

If you want to run the local llama.cpp server for embeddings, place the model files in `Mimir/ollama_models` under the expected `models` -> `models/blobs/...` layout or set `LLAMA_ARG_MODEL` to a path that exists inside the container (e.g., `/models/models/blobs/sha256-...`).

To avoid crashes on machines without GPUs or without models, the Docker Compose configuration defaults to a CPU-friendly image and *does not* start the llama server by default.

To start llama server explicitly (requires a model present):

```powershell
cd Mimir
docker compose --profile llama up -d llama-server
```

If you have a CUDA-enabled host and want GPU support, change the image to the GPU variant (`server-cuda` in `docker-compose.yml`) and add your model. See `docker-compose.yml` for example environment variables.

If you get a model path error (`gguf_init_from_file: failed to open GGUF file`), ensure the model files are placed under `Mimir/ollama_models` and the `LLAMA_ARG_MODEL` env var points to the correct file inside the container.


## Troubleshooting

See [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for common issues.

### Quick Fixes

**Services won't start**:

```bash
docker compose logs
docker compose restart
```

**Neo4j connection failed**:

```bash
# Wait 30-60 seconds for Neo4j startup
docker compose logs neo4j
```

**Port conflicts**:

```bash
# Change ports in compose.yaml
ports:
  - "9043:3000"  # Use 9043 instead of 9042
```

## Next Steps

1. [Docker Deployment](DOCKER.md) - Docker configuration details
2. [MCP Integration](MCP_INTEGRATION.md) - AI agent integration
3. [Submodule Management](SUBMODULE.md) - Git submodule operations
4. [Troubleshooting](TROUBLESHOOTING.md) - Common issues
