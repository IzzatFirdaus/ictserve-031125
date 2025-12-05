# Mimir Setup Guide

**Version**: 4.1.0  
**Last Updated**: 2025-12-05  
**Configuration**: GitHub Copilot GPT-4.1 + Ollama nomic-embed-text embeddings

---

## Prerequisites

### Required Software

- **Docker Desktop** (Windows/Mac) or Docker Engine (Linux)
- **Git** (for submodule management)
- **PowerShell** (Windows) or Bash (Linux/Mac)

### System Requirements

- **RAM**: Minimum 4GB, Recommended 8GB
- **Disk**: 2GB free space for Docker images and data
- **Ports**: 9042, 7474, 7687, 4141, 11434 must be available

---

## Installation Steps

### 1. Initialize Mimir Submodule

```powershell
# From ICTServe root directory
git submodule update --init --recursive

# Navigate to Mimir
cd Mimir
```

### 2. Configure Environment

Copy the example environment file:

```powershell
cp .env.example .env
```

Edit `Mimir/.env` with your settings:

```env
# LLM Configuration (GitHub Copilot)
MIMIR_DEFAULT_PROVIDER=openai
MIMIR_DEFAULT_MODEL=gpt-4.1
MIMIR_LLM_API=http://copilot-api:4141
MIMIR_LLM_API_PATH=/v1/chat/completions
MIMIR_LLM_API_KEY=dummy-key-for-proxy

# Embeddings Configuration (Ollama)
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_PROVIDER=ollama
MIMIR_EMBEDDINGS_MODEL=nomic-embed-text
MIMIR_EMBEDDINGS_API=http://ollama:11434
MIMIR_EMBEDDINGS_API_PATH=/v1/embeddings
MIMIR_EMBEDDINGS_DIMENSIONS=768
MIMIR_EMBEDDINGS_CHUNK_SIZE=768

# Database Configuration
NEO4J_URI=bolt://neo4j:7687
NEO4J_USER=neo4j
NEO4J_PASSWORD=MxXhTKH3qntipYLa1e0QOluJ

# Workspace Configuration
HOST_WORKSPACE_ROOT=C:\laragon\www\ictserve-031125
WORKSPACE_ROOT=/workspace

# Feature Flags
MIMIR_AUTO_INDEX_DOCS=true
MIMIR_INDEXING_THREADS=2
```

### 3. Start Docker Services

```powershell
# Start all services
docker compose up -d

# Wait for services to be healthy (45-60 seconds)
Start-Sleep -Seconds 60

# Check status
docker compose ps
```

**Expected Output**:

```
NAME                 STATUS
copilot_api_server   Up (healthy)
mimir_server         Up (healthy)
neo4j_db             Up (healthy)
ollama_server        Up (healthy)
```

### 4. Pull Ollama Embedding Model

```powershell
# Pull nomic-embed-text model
docker exec ollama_server ollama pull nomic-embed-text

# Verify model is available
docker exec ollama_server ollama list
```

### 5. Verify Installation

```powershell
# Test Mimir health
curl http://localhost:9042/health

# Test Neo4j connection
docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ "RETURN 1"

# Test Ollama embeddings
curl http://localhost:11434/api/embeddings -Method POST -Body '{"model":"nomic-embed-text","prompt":"test"}' -ContentType "application/json"
```

---

## Configuration Options

### Option 1: Copilot Chat + Ollama Embeddings (Recommended)

**Best for**: Production use with reliable embeddings

```env
MIMIR_DEFAULT_PROVIDER=openai
MIMIR_DEFAULT_MODEL=gpt-4.1
MIMIR_LLM_API=http://copilot-api:4141

MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_PROVIDER=ollama
MIMIR_EMBEDDINGS_MODEL=nomic-embed-text
MIMIR_EMBEDDINGS_API=http://ollama:11434
MIMIR_EMBEDDINGS_DIMENSIONS=768
```

**Pros**:

- Fast, reliable embeddings (nomic-embed-text)
- No API rate limits for embeddings
- 768 dimensions (optimal for most use cases)

**Cons**:

- Requires Ollama container running
- ~500MB disk space for model

### Option 2: Copilot Chat + Copilot Embeddings

**Best for**: Simplicity, no local models

```env
MIMIR_DEFAULT_PROVIDER=openai
MIMIR_DEFAULT_MODEL=gpt-4.1
MIMIR_LLM_API=http://copilot-api:4141

MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_PROVIDER=copilot
MIMIR_EMBEDDINGS_MODEL=text-embedding-3-small
MIMIR_EMBEDDINGS_API=http://copilot-api:4141
MIMIR_EMBEDDINGS_API_PATH=/v1/embeddings
MIMIR_EMBEDDINGS_DIMENSIONS=1536
```

**Pros**:

- No local models required
- Higher dimensions (1536)

**Cons**:

- Requires GitHub Copilot authentication
- Subject to API rate limits
- Slower for large indexing operations

### Option 3: Embeddings Disabled

**Best for**: Testing, minimal setup

```env
MIMIR_EMBEDDINGS_ENABLED=false
```

**Pros**:

- Fastest startup
- Minimal resource usage

**Cons**:

- No semantic search
- No vector_search_nodes tool

---

## Post-Installation

### 1. Configure Kiro IDE

Edit `.kiro/settings/mcp.json`:

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
        "list_folders"
      ]
    }
  }
}
```

**Restart Kiro IDE** to apply changes.

### 2. Index ICTServe Codebase (Optional)

```powershell
# Via Mimir Portal
start http://localhost:9042/portal

# Or via API
curl http://localhost:9042/mcp -Method POST -Headers @{"Content-Type"="application/json"} -Body '{
  "jsonrpc":"2.0",
  "method":"tools/call",
  "params":{
    "name":"index_folder",
    "arguments":{
      "path":"C:\\laragon\\www\\ictserve-031125",
      "recursive":true,
      "generate_embeddings":true
    }
  },
  "id":1
}'
```

### 3. Verify MCP Integration

In Kiro IDE, test Mimir tools:

```
User: "Create a memory node for ICTServe project"
```

Kiro should use `memory_node` tool to create the entity.

---

## Troubleshooting

### Neo4j Not Starting

**Symptom**: `neo4j_db` container unhealthy

**Solution**:

```powershell
# Stop and remove container
docker compose stop neo4j
docker rm neo4j_db

# Clear data (WARNING: deletes all data)
rm -r Mimir/data/neo4j

# Restart
docker compose up -d neo4j

# Wait 60 seconds
Start-Sleep -Seconds 60

# Verify
docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ "RETURN 1"
```

### Ollama Model Not Found

**Symptom**: Embeddings failing with "model not found"

**Solution**:

```powershell
# Pull model manually
docker exec ollama_server ollama pull nomic-embed-text

# Verify
docker exec ollama_server ollama list
```

### Port Conflicts

**Symptom**: "Port already in use" error

**Solution**:

```powershell
# Check what's using the port
netstat -ano | findstr :9042

# Kill the process or change Mimir port in docker-compose.yml
```

### Mimir Server Restarting

**Symptom**: `mimir_server` container keeps restarting

**Solution**:

```powershell
# Check logs
docker logs mimir_server --tail 100

# Common causes:
# 1. Neo4j not ready - wait longer
# 2. Wrong Neo4j password - check .env
# 3. Missing environment variables - check .env
```

---

## Verification Checklist

- [ ] Docker services running (`docker compose ps`)
- [ ] All services healthy (no "unhealthy" status)
- [ ] Mimir health check passing (`curl http://localhost:9042/health`)
- [ ] Neo4j accessible (`http://localhost:7474`)
- [ ] Ollama model pulled (`docker exec ollama_server ollama list`)
- [ ] Kiro IDE MCP configured (`.kiro/settings/mcp.json`)
- [ ] Kiro IDE restarted
- [ ] Mimir tools available in Kiro IDE

---

## Next Steps

1. **[Docker Management](02-DOCKER.md)** - Learn Docker commands
2. **[MCP Integration](04-MCP-INTEGRATION.md)** - Deep dive into Kiro IDE integration
3. **[API Reference](06-API-REFERENCE.md)** - Explore available tools

---

## Support

### Common Issues

- See [10-TROUBLESHOOTING.md](10-TROUBLESHOOTING.md)

### Logs

```powershell
docker logs mimir_server --tail 100
docker logs neo4j_db --tail 50
docker logs ollama_server --tail 50
```

### Health Checks

- Mimir: <http://localhost:9042/health>
- Neo4j: <http://localhost:7474>
- Ollama: <http://localhost:11434>

---

**Setup Complete!** Mimir is now ready for use with ICTServe.
