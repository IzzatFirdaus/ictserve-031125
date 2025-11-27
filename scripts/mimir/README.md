# Mimir Helper Scripts

PowerShell scripts for managing Mimir services in ICTServe.

## Quick Start

```powershell
# Start Mimir services
.\scripts\mimir\start.ps1

# Check status
.\scripts\mimir\status.ps1

# Stop services
.\scripts\mimir\stop.ps1

# Stop and remove data
.\scripts\mimir\stop.ps1 -RemoveVolumes
```

## Scripts

### start.ps1
Starts Mimir services (neo4j, copilot-api, mimir-server).

```powershell
.\scripts\mimir\start.ps1
```

### stop.ps1
Stops Mimir services. Use `-RemoveVolumes` to delete data.

```powershell
# Stop (preserve data)
.\scripts\mimir\stop.ps1

# Stop and remove data
.\scripts\mimir\stop.ps1 -RemoveVolumes
```

### status.ps1
Checks health of all Mimir services.

```powershell
.\scripts\mimir\status.ps1
```

## Access Points

- **Mimir Portal**: http://localhost:9042/portal
- **Neo4j Browser**: http://localhost:7474 (user: `neo4j`, pass: `MxXhTKH3qntipYLa1e0QOluJ`)
- **Health Check**: http://localhost:9042/health

## Troubleshooting

**Services won't start:**
```powershell
# Check Docker is running
docker info

# View logs
docker compose logs mimir-server
docker compose logs neo4j
```

**Can't connect to Neo4j:**
```powershell
# Wait 30-60 seconds for Neo4j to start
docker compose logs neo4j

# Check it's responding
curl http://localhost:7474
```

**Port conflicts:**
Edit `compose.yaml` to change ports if needed.
