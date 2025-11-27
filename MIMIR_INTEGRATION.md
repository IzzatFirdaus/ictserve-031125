# Mimir Integration for ICTServe

## Overview

Mimir provides AI-powered memory and knowledge graph capabilities for ICTServe development. It enables persistent memory across AI agent sessions, semantic code search, and multi-agent orchestration.

**Status**: ✅ Fully integrated with Docker Compose

## Quick Start

### 1. Start All Services (Including Mimir)

```powershell
# Start all services (ICTServe + Mimir)
docker compose up -d

# Check status
docker compose ps

# View logs
docker compose logs -f mimir-server
```

### 2. Access Mimir

| Service | URL | Credentials |
|---------|-----|-------------|
| **Mimir Portal** | <http://localhost:9042/portal> | None (dev mode) |
| **Neo4j Browser** | <http://localhost:7474> | user: `neo4j`, pass: `MxXhTKH3qntipYLa1e0QOluJ` |
| **Health Check** | <http://localhost:9042/health> | None |

### 3. Index ICTServe Project

```powershell
# Using helper script
.\scripts\mimir\index-project.ps1

# Or manually via API
curl -X POST http://localhost:9042/api/index/folder `
  -H "Content-Type: application/json" `
  -d '{"path": "/workspace", "embeddings": true}'
```

## Architecture

```
┌─────────────────────────────────────────┐
│          ICTServe Application           │
│  ┌─────────────────────────────────┐   │
│  │   Laravel 12 + Livewire 3       │   │
│  │   Filament 4 Admin Panel        │   │
│  └─────────────────────────────────┘   │
└───────────────┬─────────────────────────┘
                │
                ▼
┌───────────────────────────────────────┐
│       Mimir Server (Port 9042)        │
│  ┌─────────────────────────────────┐  │
│  │     MCP API + Chat API          │  │
│  │     File Indexing + RAG         │  │
│  └─────────────────────────────────┘  │
└───────────────┬─────────────────────────┘
                │
                ▼
┌───────────────────────────────────────┐
│       Neo4j DB (Ports 7474, 7687)     │
│  - Tasks, files, relationships        │
│  - Vector embeddings (semantic)       │
└───────────────────────────────────────┘
```

## Configuration

### Environment Variables (.env.docker)

```bash
# Mimir Configuration
NEO4J_PASSWORD=MxXhTKH3qntipYLa1e0QOluJ
MIMIR_SERVER_URL=http://localhost:9042
MIMIR_PORT=9042
MIMIR_DEFAULT_PROVIDER=copilot
MIMIR_DEFAULT_MODEL=gpt-4.1
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_PROVIDER=copilot
MIMIR_EMBEDDINGS_MODEL=text-embedding-3-small
MIMIR_EMBEDDINGS_DIMENSIONS=1536
MIMIR_AUTO_INDEX_DOCS=true
```

### Docker Compose Services

The following Mimir services are integrated into `compose.yaml`:

1. **neo4j** - Graph database (ports 7474, 7687)
2. **copilot-api** - AI model access (port 4141)
3. **mimir-server** - Main Mimir server (port 9042)

## Features

### 1. Persistent Memory

Store AI agent conversations and context in Neo4j:

- **Tasks**: TODOs, feature requests, bug reports
- **Files**: Indexed codebase with semantic search
- **Relationships**: Links between tasks, files, and concepts
- **Context**: Conversation history and agent decisions

### 2. Semantic Code Search

Search your codebase by meaning:

```bash
# Via Mimir Portal
http://localhost:9042/portal

# Via API
curl -X POST http://localhost:9042/mcp \
  -H "Content-Type: application/json" \
  -d '{
    "jsonrpc": "2.0",
    "method": "tools/call",
    "params": {
      "name": "vector_search_nodes",
      "arguments": {
        "query": "authentication middleware",
        "types": ["file"],
        "limit": 10
      }
    },
    "id": 1
  }'
```

### 3. File Indexing

Automatically watches and indexes your codebase:

**Supported file types**:

- Languages: PHP, JavaScript, TypeScript, Python, Java, Go, Rust, C/C++, C#, Ruby
- Markup: Markdown, HTML, XML
- Data: JSON, YAML, SQL
- Styles: CSS, SCSS

**Automatically skipped**:

- Images, videos, audio files
- Archives, binaries, compiled files
- `node_modules/`, `vendor/`, `dist/`, `build/`
- Files in `.gitignore`

## Helper Scripts

### Start Mimir

```powershell
# Start Mimir services only
.\scripts\mimir\start.ps1

# Or start all services
docker compose up -d
```

### Stop Mimir

```powershell
# Stop Mimir services only
.\scripts\mimir\stop.ps1

# Or stop all services
docker compose down
```

### Check Status

```powershell
# Check Mimir status
.\scripts\mimir\status.ps1

# Or check all services
docker compose ps
```

### Index Project

```powershell
# Index ICTServe project
.\scripts\mimir\index-project.ps1

# With embeddings (slower but enables semantic search)
.\scripts\mimir\index-project.ps1 -WithEmbeddings
```

## Use Cases for ICTServe

### 1. Code Documentation

Ask AI about ICTServe codebase:

- "How does the dual approval workflow work?"
- "Show me all Livewire components"
- "Explain the asset loan process"

### 2. Feature Development

Track feature implementation:

- Create TODOs for new features
- Link tasks to related files
- Track dependencies between tasks

### 3. Bug Tracking

Persistent bug investigation:

- Store bug reports with context
- Link bugs to affected files
- Track resolution steps

### 4. Knowledge Management

Build project knowledge graph:

- Document architectural decisions
- Link related components
- Track technical debt

## Troubleshooting

### Services won't start

```powershell
# Check Docker is running
docker info

# Check for port conflicts
docker compose ps

# View logs
docker compose logs mimir-server
docker compose logs neo4j
docker compose logs copilot-api
```

### Can't connect to Neo4j

```powershell
# Wait for Neo4j to fully start (30-60 seconds)
docker compose logs neo4j

# Check it's responding
curl http://localhost:7474
```

### Indexing fails

```powershell
# Check workspace path
docker compose exec mimir-server ls -la /workspace

# Check Mimir logs
docker compose logs mimir-server
```

### Embeddings not working

```powershell
# Check embeddings configuration
docker compose exec mimir-server env | grep EMBEDDINGS

# Check Copilot API
curl http://localhost:4141/v1/models
```

## Data Persistence

Mimir data is stored in Docker volumes:

```
neo4j-data/     # Database files (tasks, relationships, etc.)
neo4j-logs/     # Neo4j logs
Mimir/data/     # Mimir application data
Mimir/logs/     # Mimir logs
```

**✓ Stopping containers doesn't delete data!** Your tasks and knowledge graph persist.

To reset Mimir data:

```powershell
docker compose down
docker volume rm ictserve-031125_neo4j-data
docker compose up -d
```

## References

- **Official Documentation**: `Mimir/docs/README.md`
- **Quick Start Guide**: `docs/mimir/INTEGRATION.md`
- **ICTServe Documentation**: `docs/`

## Support

- **GitHub Issues**: <https://github.com/orneryd/Mimir/issues>
- **ICTServe Documentation**: `docs/`
- **Mimir Documentation**: `Mimir/docs/`
