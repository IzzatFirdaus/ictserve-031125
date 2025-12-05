# Mimir Docker Deployment

**Version**: 4.1.0  
**Last Updated**: 2025-12-05

---

## Overview

Mimir runs as a Docker Compose stack with four services: Mimir Server, Neo4j, Copilot API, and Ollama. This guide covers Docker deployment, management, and troubleshooting.

---

## Architecture

```
┌──────────────────────────────────────────────────┐
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

### 1. Mimir Server

**Purpose**: Main API and web portal

**Configuration**:

- **Port**: 9042 (external) → 3000 (internal)
- **Image**: timothyswt/mimir-server:latest
- **Volumes**:
  - `./data:/app/data` - Persistent data
  - `./logs:/app/logs` - Application logs
  - `C:\laragon\www\ictserve-031125:/workspace` - Workspace mount

**Endpoints**:

- `/portal` - Web UI
- `/studio` - Orchestration studio
- `/mcp` - MCP API
- `/health` - Health check

### 2. Neo4j

**Purpose**: Graph database for knowledge storage

**Configuration**:

- **Ports**: 7474 (HTTP), 7687 (Bolt)
- **Image**: neo4j:5.15-community
- **Volume**: `neo4j-data:/data` (persistent)
- **Memory**: 512M pagecache, 2G heap max
- **Plugins**: APOC

**Credentials**:

- User: `neo4j`
- Password: `MxXhTKH3qntipYLa1e0QOluJ`

**Health Check**:

```bash
cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ "RETURN 1"
```

### 3. Copilot API

**Purpose**: GitHub Copilot API bridge for LLM

**Configuration**:

- **Port**: 4141
- **Image**: timothyswt/copilot-api:latest
- **Volume**: `./copilot-data:/root/.local/share/copilot-api`

**Authentication**:

- Requires GitHub Copilot token
- Token stored in `copilot-data/github_token`

### 4. Ollama

**Purpose**: Local LLM and embeddings provider

**Configuration**:

- **Port**: 11434
- **Image**: ollama/ollama:latest
- **Volume**: `ollama_models:/root/.ollama`
- **Model**: nomic-embed-text (768 dimensions)

**Health Check**:

```bash
ollama list
```

---

## Docker Compose Configuration

### docker-compose.yml

```yaml
services:
  neo4j:
    image: neo4j:5.15-community
    platform: linux/amd64
    container_name: neo4j_db
    ports:
      - "7474:7474"
      - "7687:7687"
    volumes:
      - ./data/neo4j:/data
      - ./logs/neo4j:/logs
    environment:
      - NEO4J_AUTH=neo4j/MxXhTKH3qntipYLa1e0QOluJ
      - NEO4J_dbms_memory_pagecache_size=512M
      - NEO4J_dbms_memory_heap_max__size=2G
      - NEO4J_PLUGINS=["apoc"]
    healthcheck:
      test: ["CMD-SHELL", "cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ 'RETURN 1' || exit 1"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s
    networks:
      - mcp_network

  copilot-api:
    image: timothyswt/copilot-api:latest
    platform: linux/amd64
    container_name: copilot_api_server
    ports:
      - "4141:4141"
    volumes:
      - ./copilot-data:/root/.local/share/copilot-api
    environment:
      - NODE_ENV=production
    healthcheck:
      test: ["CMD-SHELL", "wget --spider -q http://localhost:4141/ || exit 1"]
      interval: 30s
      timeout: 10s
      retries: 5
      start_period: 15s
    networks:
      - mcp_network

  ollama:
    image: ollama/ollama:latest
    container_name: ollama_server
    platform: linux/amd64
    ports:
      - "11434:11434"
    volumes:
      - ollama_models:/root/.ollama
    environment:
      - OLLAMA_HOST=0.0.0.0:11434
      - OLLAMA_ORIGINS=*
    healthcheck:
      test: ["CMD", "ollama", "list"]
      interval: 10s
      timeout: 5s
      retries: 5
      start_period: 30s
    networks:
      - mcp_network

  mimir-server:
    build:
      context: .
      dockerfile: Dockerfile
    image: timothyswt/mimir-server:latest
    container_name: mimir_server
    ports:
      - "9042:3000"
    environment:
      - NEO4J_URI=bolt://neo4j:7687
      - NEO4J_USER=neo4j
      - NEO4J_PASSWORD=MxXhTKH3qntipYLa1e0QOluJ
      - MIMIR_LLM_API=http://copilot-api:4141
      - MIMIR_EMBEDDINGS_API=http://ollama:11434
      - HOST_WORKSPACE_ROOT=C:\laragon\www\ictserve-031125
      - WORKSPACE_ROOT=/workspace
    volumes:
      - ./data:/app/data
      - ./logs:/app/logs
      - C:\laragon\www\ictserve-031125:/workspace
    depends_on:
      neo4j:
        condition: service_healthy
      copilot-api:
        condition: service_healthy
      ollama:
        condition: service_healthy
    healthcheck:
      test: ["CMD", "node", "-e", "require('http').get('http://localhost:3000/health', (res) => process.exit(res.statusCode === 200 ? 0 : 1)).on('error', () => process.exit(1))"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 10s
    networks:
      - mcp_network

volumes:
  ollama_models:

networks:
  mcp_network:
    driver: bridge
```

---

## Management Commands

### Start Services

```powershell
# Start all services
cd Mimir
docker compose up -d

# Start specific service
docker compose up -d neo4j
docker compose up -d mimir-server

# Start with logs
docker compose up
```

### Stop Services

```powershell
# Stop all services
docker compose stop

# Stop specific service
docker compose stop mimir-server

# Stop and remove containers
docker compose down

# Stop and remove volumes (WARNING: deletes data)
docker compose down -v
```

### Restart Services

```powershell
# Restart all services
docker compose restart

# Restart specific service
docker restart mimir_server
docker restart neo4j_db
```

### View Status

```powershell
# Check service status
docker compose ps

# Check health
docker compose ps --format "table {{.Name}}\t{{.Status}}\t{{.Ports}}"

# Check resource usage
docker stats --no-stream
```

### View Logs

```powershell
# All services
docker compose logs -f

# Specific service
docker logs mimir_server -f
docker logs neo4j_db --tail 100

# Since timestamp
docker compose logs --since 2025-12-05T00:00:00
```

---

## Networking

### Internal Network

All containers communicate via Docker bridge network `mcp_network`:

**DNS Resolution**:

- `neo4j` → Neo4j container IP
- `copilot-api` → Copilot API container IP
- `ollama` → Ollama container IP
- `mimir-server` → Mimir server container IP

### Port Mapping

| Service | Internal Port | External Port | Protocol |
|---------|--------------|---------------|----------|
| mimir-server | 3000 | 9042 | HTTP |
| neo4j | 7474 | 7474 | HTTP |
| neo4j | 7687 | 7687 | Bolt |
| copilot-api | 4141 | 4141 | HTTP |
| ollama | 11434 | 11434 | HTTP |

### Firewall Configuration

**Windows**:

```powershell
# Allow Docker Desktop
New-NetFirewallRule -DisplayName "Docker Desktop" -Direction Inbound -Action Allow -Protocol TCP -LocalPort 9042,7474,7687,4141,11434
```

---

## Volumes

### Named Volumes

```yaml
volumes:
  ollama_models:  # Ollama model storage (~500MB per model)
```

### Bind Mounts

```yaml
volumes:
  - ./data:/app/data                              # Mimir data
  - ./logs:/app/logs                              # Mimir logs
  - ./data/neo4j:/data                            # Neo4j database
  - ./logs/neo4j:/logs                            # Neo4j logs
  - ./copilot-data:/root/.local/share/copilot-api # GitHub token
  - C:\laragon\www\ictserve-031125:/workspace     # ICTServe workspace
```

### Volume Management

```powershell
# List volumes
docker volume ls

# Inspect volume
docker volume inspect mimir_ollama_models

# Remove unused volumes
docker volume prune

# Backup volume
docker run --rm -v mimir_ollama_models:/data -v ${PWD}:/backup alpine tar czf /backup/ollama-backup.tar.gz /data
```

---

## Resource Limits

### Recommended Limits

```yaml
services:
  mimir-server:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
        reservations:
          cpus: '1'
          memory: 1G

  neo4j:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
        reservations:
          cpus: '1'
          memory: 1G

  ollama:
    deploy:
      resources:
        limits:
          cpus: '4'
          memory: 4G
        reservations:
          cpus: '2'
          memory: 2G
```

### GPU Support (Optional)

For Ollama GPU acceleration:

```yaml
ollama:
  deploy:
    resources:
      reservations:
        devices:
          - driver: nvidia
            count: 1
            capabilities: [gpu]
```

---

## Health Checks

### Service Health

```powershell
# Mimir health
curl http://localhost:9042/health

# Neo4j health
docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ "RETURN 1"

# Ollama health
docker exec ollama_server ollama list

# Copilot API health
curl http://localhost:4141/
```

### Startup Times

- **Neo4j**: 45-60 seconds
- **Copilot API**: 10-15 seconds
- **Ollama**: 20-30 seconds
- **Mimir Server**: 10-15 seconds (after dependencies)

---

## Troubleshooting

### Services Won't Start

```powershell
# Check Docker is running
docker ps

# View error logs
docker compose logs

# Restart Docker Desktop
# Then try again
docker compose up -d
```

### Port Conflicts

```powershell
# Check what's using port
netstat -ano | findstr :9042

# Kill process
taskkill /PID <PID> /F

# Or change port in docker-compose.yml
```

### Neo4j Not Ready

```powershell
# Wait for Neo4j startup
Start-Sleep -Seconds 60

# Check logs
docker logs neo4j_db --tail 50

# Look for "Started" message
docker logs neo4j_db | Select-String "Started"
```

### Mimir Server Crashes

```powershell
# Check logs
docker logs mimir_server --tail 100

# Verify Neo4j is healthy
docker compose ps neo4j

# Restart Mimir
docker restart mimir_server
```

---

## Backup & Recovery

### Backup Neo4j Data

```powershell
# Stop Neo4j
docker compose stop neo4j

# Backup data directory
Copy-Item -Recurse Mimir/data/neo4j Mimir/data/neo4j-backup-$(Get-Date -Format "yyyyMMdd")

# Restart Neo4j
docker compose start neo4j
```

### Restore Neo4j Data

```powershell
# Stop Neo4j
docker compose stop neo4j

# Restore data
Remove-Item -Recurse Mimir/data/neo4j
Copy-Item -Recurse Mimir/data/neo4j-backup-20251205 Mimir/data/neo4j

# Restart Neo4j
docker compose start neo4j
```

### Export/Import

```powershell
# Export all data
curl http://localhost:9042/api/memory/export > backup.json

# Import data
curl -X POST http://localhost:9042/api/memory/import -H "Content-Type: application/json" -d @backup.json
```

---

## Performance Tuning

### Neo4j Memory

```yaml
environment:
  - NEO4J_dbms_memory_pagecache_size=1G
  - NEO4J_dbms_memory_heap_max__size=4G
```

### Ollama Performance

```yaml
environment:
  - OLLAMA_NUM_PARALLEL=4
  - OLLAMA_MAX_LOADED_MODELS=2
```

### Mimir Indexing

```env
MIMIR_INDEXING_THREADS=4
MIMIR_EMBEDDINGS_DELAY_MS=100
MIMIR_MAX_EMBED_BATCH=128
```

---

## Next Steps

- **[Quick Reference](03-QUICK-REFERENCE.md)** - Common commands
- **[MCP Integration](04-MCP-INTEGRATION.md)** - Kiro IDE integration
- **[Troubleshooting](10-TROUBLESHOOTING.md)** - Common issues

---

**Docker Version**: 24.0+  
**Compose Version**: 2.0+  
**Platform**: Windows/Linux/Mac
