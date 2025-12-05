# Mimir Troubleshooting Guide

**Version**: 4.1.0  
**Last Updated**: 2025-12-05

---

## Quick Diagnostics

### Check All Services

```powershell
cd Mimir
docker compose ps
```

**Expected Output**: All services showing "healthy"

### Test Connectivity

```powershell
# Mimir health
curl http://localhost:9042/health

# Neo4j
curl http://localhost:7474

# Ollama
curl http://localhost:11434/api/tags
```

---

## Common Issues

### 1. Neo4j Container Unhealthy

**Symptom**: `neo4j_db` shows "unhealthy" status

**Cause**: Neo4j takes 45-60 seconds to fully start

**Solution**:

```powershell
# Wait for Neo4j to start
Start-Sleep -Seconds 60

# Check logs for "Started" message
docker logs neo4j_db | Select-String "Started"

# Test connection
docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ "RETURN 1"

# If still failing, restart
docker compose restart neo4j
```

### 2. Port Already in Use

**Symptom**: `Error: address already in use`

**Solution**:

```powershell
# Find process using port
netstat -ano | findstr :9042

# Kill process (replace PID)
taskkill /PID <PID> /F

# Or change port in docker-compose.yml
ports:
  - "9043:3000"  # Use different port
```

### 3. Mimir Server Keeps Restarting

**Symptom**: `mimir_server` container restarts repeatedly

**Causes**:

- Neo4j not ready
- Wrong Neo4j password
- Missing environment variables

**Solution**:

```powershell
# Check logs
docker logs mimir_server --tail 100

# Verify Neo4j is healthy
docker compose ps neo4j

# Check environment variables
docker exec mimir_server env | Select-String "NEO4J"

# Restart after Neo4j is ready
docker compose restart mimir-server
```

### 4. Ollama Model Not Found

**Symptom**: Embeddings failing with "model not found"

**Solution**:

```powershell
# Pull nomic-embed-text model
docker exec ollama_server ollama pull nomic-embed-text

# Verify model is available
docker exec ollama_server ollama list

# Should show: nomic-embed-text
```

### 5. Environment Variable Warnings

**Symptom**: Warnings about HOME, MIMIR_DEV_USER_*, etc.

**Impact**: None - these are optional OAuth/authentication variables

**Solution**: Already fixed in `Mimir/.env` (set to empty strings)

### 6. Can't Access Portal

**Symptom**: `http://localhost:9042/portal` not loading

**Solution**:

```powershell
# Check mimir-server is running
docker compose ps mimir-server

# Test health endpoint
curl http://localhost:9042/health

# Check logs
docker logs mimir_server --tail 50

# Restart service
docker restart mimir_server
```

### 7. MCP Endpoint Shows Error in Browser

**Symptom**: Browser shows "Not Acceptable: Client must accept text/event-stream"

**Impact**: None - this is EXPECTED behavior

**Explanation**: MCP endpoint uses Server-Sent Events (SSE) protocol. Browsers don't send the required `Accept: text/event-stream` header. Kiro IDE will connect properly.

**Test Manually**:

```powershell
curl http://localhost:9042/mcp `
  -Method POST `
  -Headers @{
    "Content-Type"="application/json"
    "Accept"="text/event-stream"
  } `
  -Body '{"jsonrpc":"2.0","method":"initialize","params":{},"id":1}'
```

### 8. Embeddings Not Working

**Symptom**: Vector search returns no results

**Solution**:

```powershell
# Check embeddings are enabled
docker exec mimir_server env | Select-String "EMBEDDINGS_ENABLED"

# Should show: MIMIR_EMBEDDINGS_ENABLED=true

# Check Ollama is running
docker ps --filter "name=ollama"

# Test embeddings
curl http://localhost:11434/api/embeddings -Method POST -Body '{"model":"nomic-embed-text","prompt":"test"}' -ContentType "application/json"

# If failing, pull model
docker exec ollama_server ollama pull nomic-embed-text
```

### 9. Lost Data After Restart

**Symptom**: Neo4j data disappears after restart

**Cause**: Using `docker compose down -v` (removes volumes)

**Solution**:

```powershell
# Use this to stop (preserves data)
docker compose down

# NOT this (deletes data)
# docker compose down -v

# Check volumes exist
docker volume ls | Select-String "neo4j"
```

### 10. Copilot API Not Authenticated

**Symptom**: LLM requests fail or use default model

**Impact**: Functional but not using GPT-4.1

**Solution**:

```powershell
# Add GitHub Copilot token
echo "ghp_YOUR_TOKEN" > Mimir/copilot-data/github_token

# Restart copilot-api
docker restart copilot_api_server

# Verify
curl http://localhost:4141/
```

---

## Performance Issues

### Slow Queries

**Symptom**: Neo4j queries take too long

**Solution**:

```cypher
// Create indexes in Neo4j Browser (http://localhost:7474)
CREATE INDEX FOR (n:Memory) ON (n.title);
CREATE INDEX FOR (n:File) ON (n.path);
CREATE INDEX FOR (n:Todo) ON (n.status);
```

### High Memory Usage

**Symptom**: Docker containers using too much RAM

**Solution**:

```yaml
# Add resource limits in docker-compose.yml
services:
  mimir-server:
    deploy:
      resources:
        limits:
          memory: 2G
  neo4j:
    deploy:
      resources:
        limits:
          memory: 2G
```

### Slow File Indexing

**Symptom**: `index_folder` takes too long

**Solution**:

```env
# In Mimir/.env
MIMIR_INDEXING_THREADS=4
MIMIR_EMBEDDINGS_DELAY_MS=100
MIMIR_MAX_EMBED_BATCH=128
```

---

## Build Issues

### npm Install Timeout

**Symptom**: `npm ci` fails with `ETIMEDOUT`

**Solution**: Already fixed in Dockerfile with timeout config

If still failing:

```powershell
# Build with no cache
docker compose build --no-cache
```

### Build Fails

**Symptom**: Docker build errors

**Solution**:

```powershell
# Clean build
docker compose down -v
docker compose build --no-cache
docker compose up -d

# Check disk space
docker system df

# Clean up unused resources
docker system prune -a
```

---

## Data Issues

### Corrupted Database

**Symptom**: Neo4j won't start, data errors

**Solution**:

```powershell
# Backup data
docker cp neo4j_db:/data ./neo4j-backup

# Remove volume
docker compose down -v

# Start fresh
docker compose up -d

# Restore if needed
docker cp ./neo4j-backup/. neo4j_db:/data
docker restart neo4j_db
```

### Clear All Data

**Symptom**: Need to reset Mimir completely

**Solution**:

```powershell
# Stop services
docker compose down

# Remove volumes (WARNING: deletes all data)
docker volume rm mimir_ollama_models
Remove-Item -Recurse Mimir/data/neo4j

# Start fresh
docker compose up -d
```

---

## Network Issues

### Can't Access from Host

**Symptom**: `localhost:9042` not accessible

**Solution**:

```powershell
# Check port mapping
docker compose ps

# Should show: 0.0.0.0:9042->3000/tcp

# Test from inside container
docker exec mimir_server curl http://localhost:3000/health

# Check Windows Firewall
# Allow Docker Desktop in Windows Firewall settings
```

### DNS Resolution Failed

**Symptom**: Mimir can't connect to Neo4j

**Solution**:

```powershell
# Check network
docker network inspect mimir_mcp_network

# Verify all containers are on same network
docker network inspect mimir_mcp_network | Select-String "Name"

# Restart network
docker compose down
docker compose up -d
```

---

## Debugging Tools

### View Logs

```powershell
# All services
docker compose logs -f

# Specific service
docker logs mimir_server -f
docker logs neo4j_db --tail 100

# Since timestamp
docker compose logs --since 2025-12-05T00:00:00

# Grep for errors
docker logs mimir_server | Select-String "ERROR"
```

### Shell Access

```powershell
# Mimir server
docker exec -it mimir_server sh

# Neo4j
docker exec -it neo4j_db bash

# Ollama
docker exec -it ollama_server sh
```

### Inspect Containers

```powershell
# Container details
docker inspect mimir_server

# Network details
docker network inspect mimir_mcp_network

# Volume details
docker volume inspect mimir_ollama_models
```

### Resource Monitoring

```powershell
# Real-time stats
docker stats

# Specific containers
docker stats mimir_server neo4j_db ollama_server

# Disk usage
docker system df
```

---

## Recovery Procedures

### Complete Reset

```powershell
# 1. Stop all services
docker compose down -v

# 2. Remove all data
Remove-Item -Recurse Mimir/data
Remove-Item -Recurse Mimir/logs

# 3. Rebuild images
docker compose build --no-cache

# 4. Start fresh
docker compose up -d

# 5. Wait for services
Start-Sleep -Seconds 60

# 6. Verify health
curl http://localhost:9042/health
```

### Backup Before Reset

```powershell
# 1. Export data
curl http://localhost:9042/api/memory/export > backup-$(Get-Date -Format "yyyyMMdd").json

# 2. Backup Neo4j
docker cp neo4j_db:/data ./neo4j-backup-$(Get-Date -Format "yyyyMMdd")

# 3. Backup Ollama models
docker cp ollama_server:/root/.ollama ./ollama-backup-$(Get-Date -Format "yyyyMMdd")

# 4. Now safe to reset
docker compose down -v
```

---

## Getting Help

### Check Documentation

1. [Setup Guide](01-SETUP.md) - Installation
2. [Docker Guide](02-DOCKER.md) - Docker management
3. [Quick Reference](03-QUICK-REFERENCE.md) - Common commands

### Collect Diagnostic Information

```powershell
# Service status
docker compose ps > diagnostic-info.txt

# Logs
docker compose logs >> diagnostic-info.txt

# Environment
docker exec mimir_server env >> diagnostic-info.txt

# Health checks
curl http://localhost:9042/health >> diagnostic-info.txt
```

### Report Issues

If issues persist:

1. Check [Mimir GitHub Issues](https://github.com/orneryd/Mimir/issues)
2. Review logs for error messages
3. Try complete reset procedure
4. Report with diagnostic information

---

## Prevention Tips

### Regular Maintenance

```powershell
# Weekly: Check disk space
docker system df

# Weekly: Clean unused resources
docker system prune

# Monthly: Backup Neo4j data
docker cp neo4j_db:/data ./neo4j-backup-$(Get-Date -Format "yyyyMMdd")

# Monthly: Update images
docker compose pull
docker compose up -d
```

### Best Practices

- Always use `docker compose down` (not `down -v`)
- Wait 60 seconds after starting Neo4j
- Monitor resource usage regularly
- Keep backups of important data
- Update Docker Desktop regularly

---

**Last Updated**: 2025-12-05  
**Mimir Version**: 4.1.0  
**Status**: All services operational ✅
