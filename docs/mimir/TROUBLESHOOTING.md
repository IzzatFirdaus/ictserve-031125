# Mimir Troubleshooting

Common issues and solutions for Mimir AI memory system.

## Service Issues

### Services Won't Start

**Symptom**: `docker compose up -d` fails

**Solution**:

```bash
# Check Docker is running
docker ps

# View error logs
cd Mimir
docker compose logs

# Restart Docker Desktop (Windows)
# Or restart Docker service (Linux)

# Try again
docker compose up -d
```

### Neo4j Not Starting

**Symptom**: Neo4j container exits immediately

**Solution**:

```bash
# Check Neo4j logs
docker compose logs neo4j

# Common issue: Port conflict
netstat -ano | findstr :7474

# Change port in compose.yaml
ports:
  - "7475:7474"  # Use 7475 instead

# Or remove conflicting service
```

### Mimir Server Crashes

**Symptom**: mimir-server container restarts repeatedly

**Solution**:

```bash
# View crash logs
docker compose logs mimir-server

# Check Neo4j is healthy
docker compose ps neo4j
# Should show "healthy"

# Wait for Neo4j startup (60 seconds)
sleep 60
docker compose restart mimir-server
```

## Connection Issues

### Can't Connect to Neo4j

**Symptom**: `Connection refused` to Neo4j

**Solution**:

```bash
# Wait for Neo4j startup (45-60 seconds)
docker compose logs neo4j | grep "Started"

# Test connection
curl http://localhost:7474

# Check health
docker compose ps neo4j
# Should show "healthy"

# If still failing, restart
docker compose restart neo4j
```

### Can't Access Portal

**Symptom**: `http://localhost:9042/portal` not loading

**Solution**:

```bash
# Check mimir-server is running
docker compose ps mimir-server

# Check logs
docker compose logs mimir-server

# Test health endpoint
curl http://localhost:9042/health

# Restart service
docker compose restart mimir-server
```

### MCP API Not Responding

**Symptom**: MCP requests timeout

**Solution**:

```bash
# Check service status
docker compose ps

# Test health
curl http://localhost:9042/health

# View logs
docker compose logs -f mimir-server

# Restart
docker compose restart
```

## Build Issues

### npm Install Timeout

**Symptom**: `npm ci` fails with `ETIMEDOUT`

**Solution**: Already fixed in Dockerfile with timeout config

```dockerfile
RUN npm config set fetch-timeout 300000 && \
    npm config set fetch-retries 5 && \
    npm ci --legacy-peer-deps
```

If still failing:

```bash
# Build with no cache
docker compose build --no-cache

# Or increase timeout further
# Edit Dockerfile: fetch-timeout 600000
```

### Build Fails

**Symptom**: Docker build errors

**Solution**:

```bash
# Clean build
docker compose down -v
docker compose build --no-cache
docker compose up -d

# Check disk space
df -h

# Check Docker resources
docker system df
```

## Authentication Issues

### Copilot API Not Working

**Symptom**: LLM requests fail

**Solution**:

```bash
# Check GitHub token exists
cat copilot-data/github_token

# If missing, add token
echo "ghp_YOUR_TOKEN" > copilot-data/github_token

# Restart copilot-api
docker compose restart copilot-api

# Test
curl http://localhost:4141/health
```

### Neo4j Authentication Failed

**Symptom**: `Authentication failed` error

**Solution**:

```bash
# Check password in .env
cat .env | grep NEO4J_PASSWORD

# Should match compose.yaml
# Default: MxXhTKH3qntipYLa1e0QOluJ

# Reset Neo4j
docker compose down -v
docker compose up -d
```

## Performance Issues

### Slow Queries

**Symptom**: Neo4j queries take too long

**Solution**:

```cypher
// Create indexes in Neo4j Browser
CREATE INDEX FOR (n:Task) ON (n.status);
CREATE INDEX FOR (n:File) ON (n.path);
CREATE INDEX FOR (n:Node) ON (n.type);
```

### High Memory Usage

**Symptom**: Docker containers using too much RAM

**Solution**:

```yaml
# Add resource limits in compose.yaml
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

```bash
# Disable embeddings for faster indexing
# Edit .env
MIMIR_EMBEDDINGS_ENABLED=false

# Or index without embeddings
npm run index:add /path/to/folder --no-embeddings
```

## Data Issues

### Lost Data After Restart

**Symptom**: Neo4j data disappears

**Solution**:

```bash
# Check volume exists
docker volume ls | grep neo4j

# Verify volume mount
docker compose config | grep neo4j-data

# Don't use `docker compose down -v`
# Use `docker compose down` instead
```

### Corrupted Database

**Symptom**: Neo4j won't start, data errors

**Solution**:

```bash
# Backup data
docker cp mimir-neo4j:/data ./neo4j-backup

# Remove volume
docker compose down -v

# Start fresh
docker compose up -d

# Restore if needed
docker cp ./neo4j-backup/. mimir-neo4j:/data
```

### Clear All Data

**Symptom**: Need to reset Mimir

**Solution**:

```bash
# Stop services
docker compose down

# Remove volumes
docker volume rm mimir_neo4j-data

# Start fresh
docker compose up -d
```

## Network Issues

### Port Conflicts

**Symptom**: `address already in use`

**Solution**:

```bash
# Find conflicting process
netstat -ano | findstr :9042

# Kill process (Windows)
taskkill /PID <PID> /F

# Or change port in compose.yaml
ports:
  - "9043:3000"  # Use 9043 instead
```

### Can't Access from Host

**Symptom**: `localhost:9042` not accessible

**Solution**:

```bash
# Check port mapping
docker compose ps

# Should show: 0.0.0.0:9042->3000/tcp

# Test from inside container
docker compose exec mimir-server curl http://localhost:3000/health

# Check firewall (Windows)
# Allow Docker Desktop in Windows Firewall
```

## IDE Integration Issues

### Kiro Can't Connect

**Symptom**: MCP connection timeout

**Solution**: Mimir MCP disabled in Kiro (by design)

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

### HTTP Bridge Missing

**Symptom**: `Cannot find module 'mcp-http-client.js'`

**Solution**: Create bridge script (see [MCP_INTEGRATION.md](MCP_INTEGRATION.md))

Or use HTTP API directly:

```bash
curl -X POST http://localhost:9042/mcp \
  -H "Content-Type: application/json" \
  -d '{"method":"health_check","params":{}}'
```

## Debugging

### View Logs

```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f mimir-server

# Last 100 lines
docker compose logs --tail=100 neo4j

# Since timestamp
docker compose logs --since 2024-01-01T00:00:00
```

### Shell Access

```bash
# Mimir server
docker compose exec mimir-server sh

# Neo4j
docker compose exec neo4j bash

# Copilot API
docker compose exec copilot-api sh
```

### Inspect Containers

```bash
# Container details
docker inspect mimir-server

# Network details
docker network inspect mimir_default

# Volume details
docker volume inspect mimir_neo4j-data
```

### Health Checks

```bash
# Mimir health
curl http://localhost:9042/health

# Neo4j health
curl http://localhost:7474

# Copilot API health
curl http://localhost:4141/health

# All services
docker compose ps
```

## Getting Help

If issues persist:

1. Check [GitHub Issues](https://github.com/orneryd/Mimir/issues)
2. Review [Mimir Documentation](https://github.com/orneryd/Mimir)
3. Check [Docker logs](#view-logs)
4. Try [reset everything](#clear-all-data)

## Next Steps

- [Setup Guide](SETUP.md) - Installation instructions
- [Docker Deployment](DOCKER.md) - Docker configuration
- [MCP Integration](MCP_INTEGRATION.md) - AI agent integration
