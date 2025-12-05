# Mimir Quick Reference

**Version**: 4.1.0  
**Last Updated**: 2025-12-05

---

## Access URLs

### Mimir Interfaces

| Interface | URL | Purpose |
|-----------|-----|---------|
| **Portal UI** | <http://localhost:9042/portal> | Web interface for memory management |
| **Orchestration Studio** | <http://localhost:9042/studio> | Workflow orchestration interface |
| **MCP Endpoint** | <http://localhost:9042/mcp> | MCP protocol endpoint (for Kiro IDE) |
| **Health Check** | <http://localhost:9042/health> | Service health status |

### Neo4j Database

| Interface | URL | Credentials |
|-----------|-----|-------------|
| **Neo4j Browser** | <http://localhost:7474> | neo4j / MxXhTKH3qntipYLa1e0QOluJ |
| **Bolt Protocol** | bolt://localhost:7687 | neo4j / MxXhTKH3qntipYLa1e0QOluJ |

### Other Services

| Service | URL | Purpose |
|---------|-----|---------|
| **Copilot API** | <http://localhost:4141> | GitHub Copilot API bridge |
| **Ollama API** | <http://localhost:11434> | Local LLM and embeddings |

---

## Docker Commands

### Service Management

```powershell
# Start all services
cd Mimir
docker compose up -d

# Stop all services
docker compose down

# Restart all services
docker compose restart

# Restart specific service
docker restart mimir_server
docker restart neo4j_db
docker restart ollama_server
```

### Status & Monitoring

```powershell
# Check service status
docker compose ps

# View logs (all services)
docker compose logs -f

# View logs (specific service)
docker logs mimir_server -f
docker logs neo4j_db --tail 100
docker logs ollama_server --tail 50

# Check resource usage
docker stats --no-stream
```

### Health Checks

```powershell
# Mimir health
curl http://localhost:9042/health

# Neo4j health
docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ "RETURN 1"

# Ollama health
docker exec ollama_server ollama list
```

---

## MCP Tools (17 Available)

### Memory Operations

```javascript
// Create memory node
memory_node({
  operation: "add",
  type: "memory",
  properties: {
    title: "ICTServe Project",
    content: "Laravel 12 application with Filament 4"
  }
})

// Search nodes
memory_node({
  operation: "search",
  query: "Laravel Filament"
})

// Create relationship
memory_edge({
  operation: "add",
  source: "memory-123",
  target: "memory-456",
  type: "relates_to"
})

// Bulk operations
memory_batch({
  operation: "add_nodes",
  nodes: [
    {type: "memory", properties: {title: "Node 1"}},
    {type: "memory", properties: {title: "Node 2"}}
  ]
})
```

### File Indexing

```javascript
// Index folder
index_folder({
  path: "C:\\laragon\\www\\ictserve-031125",
  recursive: true,
  generate_embeddings: true,
  file_patterns: ["*.php", "*.blade.php", "*.js"]
})

// List watched folders
list_folders()

// Remove folder
remove_folder({
  path: "C:\\laragon\\www\\ictserve-031125"
})
```

### Vector Search

```javascript
// Semantic search
vector_search_nodes({
  query: "authentication middleware",
  limit: 10,
  min_similarity: 0.75,
  types: ["file", "memory"]
})

// Get embedding stats
get_embedding_stats()
```

### Task Management

```javascript
// Create todo
todo({
  operation: "create",
  title: "Fix login bug",
  priority: "high",
  status: "pending"
})

// Create todo list
todo_list({
  operation: "create",
  title: "Sprint 1 Tasks",
  priority: "high"
})

// Get task context
get_task_context({
  taskId: "todo-123",
  agentType: "worker"
})
```

---

## Environment Variables

### Key Settings

```env
# LLM Provider
MIMIR_DEFAULT_PROVIDER=openai
MIMIR_DEFAULT_MODEL=gpt-4.1
MIMIR_LLM_API=http://copilot-api:4141

# Embeddings
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

# Features
MIMIR_AUTO_INDEX_DOCS=true
MIMIR_INDEXING_THREADS=2
MIMIR_FEATURE_VECTOR_EMBEDDINGS=true
```

---

## Common Tasks

### Start Mimir

```powershell
cd C:\laragon\www\ictserve-031125\Mimir
docker compose up -d
Start-Sleep -Seconds 60
curl http://localhost:9042/health
```

### Stop Mimir

```powershell
cd C:\laragon\www\ictserve-031125\Mimir
docker compose down
```

### View Logs

```powershell
# All services
docker compose logs -f

# Specific service
docker logs mimir_server -f

# Last 100 lines
docker logs mimir_server --tail 100
```

### Restart After Config Change

```powershell
cd Mimir
docker compose restart mimir-server
Start-Sleep -Seconds 30
curl http://localhost:9042/health
```

### Clear Neo4j Data

```powershell
# WARNING: Deletes all data
docker compose down
rm -r Mimir/data/neo4j
docker compose up -d
```

### Pull Ollama Model

```powershell
docker exec ollama_server ollama pull nomic-embed-text
docker exec ollama_server ollama list
```

---

## Troubleshooting Quick Fixes

### Service Won't Start

```powershell
# Check logs
docker logs mimir_server --tail 100

# Restart service
docker restart mimir_server

# Full restart
docker compose down
docker compose up -d
```

### Port Conflict

```powershell
# Check what's using port
netstat -ano | findstr :9042

# Change port in docker-compose.yml
ports:
  - "9043:3000"  # Use different port
```

### Neo4j Connection Failed

```powershell
# Verify Neo4j is running
docker ps --filter "name=neo4j"

# Test connection
docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ "RETURN 1"

# Check password in .env matches docker-compose.yml
```

### Embeddings Not Working

```powershell
# Check Ollama is running
docker ps --filter "name=ollama"

# Verify model is pulled
docker exec ollama_server ollama list

# Pull model if missing
docker exec ollama_server ollama pull nomic-embed-text

# Test embeddings
curl http://localhost:11434/api/embeddings -Method POST -Body '{"model":"nomic-embed-text","prompt":"test"}' -ContentType "application/json"
```

---

## Kiro IDE Integration

### MCP Configuration

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
        "memory_batch",
        "vector_search_nodes",
        "todo",
        "todo_list",
        "index_folder",
        "list_folders",
        "get_embedding_stats"
      ]
    }
  }
}
```

### Test Connection

In Kiro IDE chat:

```
User: "Create a memory node for testing"
```

Kiro should use `memory_node` tool to create the entity.

---

## Performance Tips

### Indexing Large Codebases

```env
# Increase threads for faster indexing
MIMIR_INDEXING_THREADS=4

# Reduce delay for faster embeddings
MIMIR_EMBEDDINGS_DELAY_MS=100

# Increase batch size
MIMIR_MAX_EMBED_BATCH=128
```

### Neo4j Memory Tuning

```yaml
# In docker-compose.yml
environment:
  NEO4J_dbms_memory_pagecache_size: 1G
  NEO4J_dbms_memory_heap_max__size: 4G
```

### Ollama Performance

```yaml
# Enable GPU support (NVIDIA)
deploy:
  resources:
    reservations:
      devices:
        - driver: nvidia
          count: 1
          capabilities: [gpu]
```

---

## Useful Queries

### Neo4j Cypher Queries

```cypher
// Count all nodes
MATCH (n) RETURN count(n)

// Find memory nodes
MATCH (n:Memory) RETURN n LIMIT 10

// Find relationships
MATCH (a)-[r]->(b) RETURN a, r, b LIMIT 10

// Search by property
MATCH (n:Memory) WHERE n.title CONTAINS 'Laravel' RETURN n

// Delete all data (WARNING)
MATCH (n) DETACH DELETE n
```

### HTTP API Calls

```powershell
# Health check
curl http://localhost:9042/health

# List tools
curl http://localhost:9042/mcp -Method POST -Headers @{"Content-Type"="application/json"} -Body '{
  "jsonrpc":"2.0",
  "method":"tools/list",
  "params":{},
  "id":1
}'

# Call tool
curl http://localhost:9042/mcp -Method POST -Headers @{"Content-Type"="application/json"} -Body '{
  "jsonrpc":"2.0",
  "method":"tools/call",
  "params":{
    "name":"memory_node",
    "arguments":{"operation":"query","type":"memory"}
  },
  "id":1
}'
```

---

## Resource Usage

### Typical Usage

- **Mimir Server**: ~200MB RAM, <5% CPU
- **Neo4j**: ~512MB RAM, <10% CPU
- **Ollama**: ~500MB RAM, <5% CPU (idle), 50-80% CPU (embedding generation)
- **Copilot API**: ~100MB RAM, <5% CPU

### Disk Usage

- **Docker Images**: ~2GB
- **Neo4j Data**: ~100MB (empty), grows with usage
- **Ollama Models**: ~500MB per model
- **Logs**: ~10MB per day

---

## Next Steps

- **[Docker Management](02-DOCKER.md)** - Detailed Docker guide
- **[MCP Integration](04-MCP-INTEGRATION.md)** - Kiro IDE integration
- **[Troubleshooting](10-TROUBLESHOOTING.md)** - Common issues

---

**Quick Reference Card**  
**Start**: `cd Mimir && docker compose up -d`  
**Stop**: `docker compose down`  
**Logs**: `docker logs mimir_server -f`  
**Health**: `curl http://localhost:9042/health`  
**Portal**: <http://localhost:9042/portal>  
**Neo4j**: <http://localhost:7474>
