# Mimir Integration Guide for ICTServe

## Quick Start

### 1. Start Mimir

```powershell
# From ICTServe root
.\scripts\mimir\start.ps1

# Or from Mimir directory
cd Mimir
docker compose up -d
```

### 2. Verify Services

```powershell
.\scripts\mimir\status.ps1
```

**Expected Output**:

- mimir-server: Running on port 9042
- neo4j: Running on ports 7474, 7687
- copilot-api: Running on port 4141
- llama-server: Running on port 11434

### 3. Access Mimir

- **Portal**: <http://localhost:9042/portal>
- **Neo4j Browser**: <http://localhost:7474> (neo4j/password)
- **Health Check**: <http://localhost:9042/health>

## Configuration

### Environment Variables

Mimir is configured via `Mimir/.env`:

```ini
# Workspace (ICTServe root)
HOST_WORKSPACE_ROOT=D:\\xampp\\htdocs\\ictserve-031125

# Neo4j
NEO4J_PASSWORD=password

# LLM Provider (Copilot)
MIMIR_DEFAULT_PROVIDER=copilot
MIMIR_LLM_API=http://copilot-api:4141/v1
MIMIR_DEFAULT_MODEL=gpt-4.1

# Embeddings (llama.cpp)
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_API=http://llama-server:8080
MIMIR_EMBEDDINGS_MODEL=mxbai-embed-large
```

### Key Settings

| Variable | Value | Purpose |
|----------|-------|---------|
| `HOST_WORKSPACE_ROOT` | ICTServe root path | File indexing |
| `NEO4J_PASSWORD` | password | Database auth |
| `MIMIR_DEFAULT_PROVIDER` | copilot | LLM provider |
| `MIMIR_EMBEDDINGS_ENABLED` | true | Semantic search |

## File Indexing

### Index ICTServe Codebase

```powershell
cd Mimir
npm run index:add D:\xampp\htdocs\ictserve-031125 --embeddings
```

### List Indexed Folders

```powershell
npm run index:list
```

### Remove Folder

```powershell
npm run index:remove D:\xampp\htdocs\ictserve-031125
```

## MCP Tools (13 Available)

### Memory Operations

- `memory_node` - Create/read/update nodes
- `memory_edge` - Create relationships
- `memory_batch` - Bulk operations
- `get_task_context` - Get filtered context

### File Indexing

- `index_folder` - Index code files
- `list_folders` - Show watched folders
- `get_folder_stats` - Folder statistics

### Vector Search

- `vector_search_nodes` - Semantic search
- `get_embedding_stats` - Embedding statistics

### Task Management

- `todo` - Manage tasks
- `todo_list` - Manage task lists

### System

- `health_check` - Service health

## Docker Services

### Architecture

```
┌─────────────────────────────────────────┐
│         Mimir Docker Stack              │
│                                         │
│  ┌──────────────┐    ┌──────────────┐ │
│  │ mimir-server │───▶│    neo4j     │ │
│  │    :9042     │    │  :7474,:7687 │ │
│  └──────────────┘    └──────────────┘ │
│         │                               │
│         ▼                               │
│  ┌──────────────┐    ┌──────────────┐ │
│  │ copilot-api  │    │ llama-server │ │
│  │    :4141     │    │    :11434    │ │
│  └──────────────┘    └──────────────┘ │
└─────────────────────────────────────────┘
```

### Service Details

**mimir-server**:

- Port: 9042
- Purpose: Main API and web portal
- Endpoints: `/portal`, `/mcp`, `/health`, `/v1/chat/completions`

**neo4j**:

- Ports: 7474 (HTTP), 7687 (Bolt)
- Purpose: Graph database
- Credentials: neo4j/password

**copilot-api**:

- Port: 4141
- Purpose: GitHub Copilot API bridge
- Auth: GitHub token in `copilot-data/github_token`

**llama-server**:

- Port: 11434
- Purpose: Embeddings (mxbai-embed-large)
- Model: 1024 dimensions

## Management Commands

### PowerShell Scripts

```powershell
# Start services
.\scripts\mimir\start.ps1

# Stop services
.\scripts\mimir\stop.ps1

# Check status
.\scripts\mimir\status.ps1
```

### Docker Compose

```powershell
cd Mimir

# Start
docker compose up -d

# Stop
docker compose stop

# Restart
docker compose restart

# Logs
docker compose logs -f

# Remove (with volumes)
docker compose down -v
```

### Makefile

```bash
cd Mimir

# Setup (build + start)
make setup

# Build
make build

# Start
make up

# Stop
make stop

# Restart
make restart

# Logs
make logs

# Shell
make shell

# Clean (remove volumes)
make clean
```

## Troubleshooting

### Services Won't Start

```powershell
cd Mimir
docker compose logs
docker compose restart
```

### Neo4j Connection Failed

Wait 30-60 seconds for Neo4j startup:

```powershell
docker compose logs neo4j
```

### Port Conflicts

Edit `Mimir/docker-compose.yml`:

```yaml
ports:
  - "9043:3000"  # Change 9042 to 9043
```

### Embeddings Not Working

Check llama-server:

```powershell
docker compose logs llama-server
curl http://localhost:11434/health
```

## Integration with ICTServe

### Workspace Mount

Mimir mounts ICTServe root as `/workspace`:

```yaml
volumes:
  - D:\xampp\htdocs\ictserve-031125:/workspace
```

### Separate Docker Network

Mimir runs on its own Docker network (`mcp_network`), isolated from ICTServe main application.

### File Access

Access ICTServe files from Mimir container:

```powershell
docker compose exec mimir-server ls /workspace
```

## Next Steps

1. [Official README](README-official.md) - Complete Mimir documentation
2. [Setup Guide](SETUP.md) - Detailed installation
3. [Docker Guide](DOCKER.md) - Docker configuration
4. [Troubleshooting](TROUBLESHOOTING.md) - Common issues
