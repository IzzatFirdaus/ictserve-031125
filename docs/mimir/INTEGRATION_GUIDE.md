# Mimir Integration Guide for ICTServe

Complete guide for integrating Mimir AI memory system with ICTServe.

## Overview

Mimir provides persistent AI memory and knowledge graph capabilities for ICTServe development. It enables:

- **Persistent Memory**: AI remembers context across sessions
- **Knowledge Graph**: Relationships between tasks, files, and concepts
- **Semantic Search**: Find code by meaning, not just keywords
- **File Indexing**: Automatic codebase tracking and RAG

## Architecture

```
ICTServe Application (Laravel 12)
         ↓
Mimir Server (Port 9042)
    ├── MCP API (/mcp)
    ├── Chat API (/v1/chat/completions)
    └── Portal UI (/portal)
         ↓
Neo4j Database (Ports 7474, 7687)
    ├── Tasks & TODOs
    ├── File nodes & chunks
    └── Vector embeddings
```

## Quick Start

### 1. Start Services

```powershell
# Start all services (ICTServe + Mimir)
docker compose up -d

# Or start Mimir only
.\scripts\mimir\start.ps1
```

### 2. Verify Installation

```powershell
# Check status
.\scripts\mimir\status.ps1

# Access Mimir Portal
Start-Process http://localhost:9042/portal

# Access Neo4j Browser
Start-Process http://localhost:7474
```

### 3. Index ICTServe Project

```powershell
# Via API
curl -X POST http://localhost:9042/api/index/folder `
  -H "Content-Type: application/json" `
  -d '{"path": "/workspace", "embeddings": true}'

# Or use Mimir Portal UI
# Navigate to http://localhost:9042/portal
# Click "File Indexing" → "Add Folder" → "/workspace"
```

## Configuration

### Environment Variables (.env.docker)

```bash
# Neo4j Database
NEO4J_PASSWORD=MxXhTKH3qntipYLa1e0QOluJ

# Mimir Server
MIMIR_SERVER_URL=http://localhost:9042
MIMIR_PORT=9042

# LLM Provider (Copilot API)
MIMIR_DEFAULT_PROVIDER=copilot
MIMIR_DEFAULT_MODEL=gpt-4.1
MIMIR_LLM_API=http://copilot-api:4141

# Embeddings (Semantic Search)
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_PROVIDER=copilot
MIMIR_EMBEDDINGS_MODEL=text-embedding-3-small
MIMIR_EMBEDDINGS_DIMENSIONS=1536

# Auto-index Mimir docs on startup
MIMIR_AUTO_INDEX_DOCS=true
```

### Docker Compose Services

The following services are integrated:

1. **neo4j** - Graph database (ports 7474, 7687)
2. **copilot-api** - AI model access (port 4141)
3. **mimir-server** - Main Mimir server (port 9042)

## Features

### 1. Persistent Memory

Store AI conversations and context:

```javascript
// Create a task
{
  "operation": "create",
  "title": "Implement user authentication",
  "priority": "high",
  "status": "pending"
}

// Add context
{
  "operation": "update",
  "id": "todo-123",
  "context": {
    "files": ["app/Http/Controllers/AuthController.php"],
    "notes": "Use Laravel Sanctum"
  }
}
```

### 2. Semantic Code Search

Search by meaning:

```bash
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

Automatically watches and indexes:

**Supported**: PHP, JavaScript, TypeScript, Blade, Markdown, JSON, YAML, CSS
**Skipped**: Images, videos, archives, `node_modules/`, `vendor/`, `.git/`

## Use Cases

### 1. Code Documentation

Ask AI about ICTServe:
- "How does dual approval workflow work?"
- "Show me all Livewire components"
- "Explain asset loan process"

### 2. Feature Development

Track implementation:
- Create TODOs for features
- Link tasks to files
- Track dependencies

### 3. Bug Tracking

Persistent investigation:
- Store bug reports with context
- Link bugs to affected files
- Track resolution steps

## Helper Scripts

### Start Mimir

```powershell
.\scripts\mimir\start.ps1
```

### Stop Mimir

```powershell
# Stop (preserve data)
.\scripts\mimir\stop.ps1

# Stop and remove data
.\scripts\mimir\stop.ps1 -RemoveVolumes
```

### Check Status

```powershell
.\scripts\mimir\status.ps1
```

## Troubleshooting

### Services won't start

```powershell
# Check Docker
docker info

# View logs
docker compose logs mimir-server
docker compose logs neo4j
docker compose logs copilot-api
```

### Can't connect to Neo4j

```powershell
# Wait 30-60 seconds for startup
docker compose logs neo4j

# Check response
curl http://localhost:7474
```

### Indexing fails

```powershell
# Check workspace mount
docker compose exec mimir-server ls -la /workspace

# View logs
docker compose logs mimir-server
```

### Embeddings not working

```powershell
# Check configuration
docker compose exec mimir-server env | grep EMBEDDINGS

# Check Copilot API
curl http://localhost:4141/v1/models
```

## Data Persistence

Data stored in Docker volumes:

```
neo4j-data/     # Database files
neo4j-logs/     # Neo4j logs
Mimir/data/     # Mimir application data
Mimir/logs/     # Mimir logs
```

**Stopping containers preserves data!**

To reset:

```powershell
docker compose down
docker volume rm ictserve-031125_neo4j-data
docker compose up -d
```

## References

- **Official Docs**: `docs/mimir/README-official.md`
- **Mimir GitHub**: https://github.com/orneryd/Mimir
- **ICTServe Docs**: `docs/`

## Support

- **GitHub Issues**: https://github.com/orneryd/Mimir/issues
- **ICTServe Docs**: `docs/`
