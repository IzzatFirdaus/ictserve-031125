# MCP Memory Server Setup (Docker)

## Quick Start

```powershell
# Build and start MCP Memory Server
docker compose up -d memory

# Import existing memory data
./scripts/docker/import-memory.ps1

# Check status
./scripts/docker/memory-mcp.ps1 status

# View logs
./scripts/docker/memory-mcp.ps1 logs
```

## Architecture

- **Image**: `mcp/memory:latest`
- **Container**: `ictserve-memory`
- **Storage**: `memory-data` Docker volume (persistent)
- **Format**: JSONL (JSON Lines)

## Core Concepts

### Entities
Nodes in the knowledge graph with:

- `name` (unique identifier)
- `entityType` (e.g., "person", "organization", "technical_implementation")
- `observations[]` (array of facts)

### Relations
Directed connections between entities:

- `from` (source entity)
- `to` (target entity)
- `relationType` (e.g., "works_at", "implements", "documents")

### Observations
Atomic facts about entities:

- Stored as strings
- One fact per observation
- Can be added/removed independently

## API Tools

### create_entities

```json
{
  "entities": [
    {
      "name": "ICTServe_System",
      "entityType": "technical_implementation",
      "observations": [
        "Laravel 12 application",
        "Livewire 3 + Filament 4",
        "WCAG 2.2 AA compliant"
      ]
    }
  ]
}
```

### create_relations

```json
{
  "relations": [
    {
      "from": "Staff_Dashboard",
      "to": "Livewire_3",
      "relationType": "uses"
    }
  ]
}
```

### add_observations

```json
{
  "observations": [
    {
      "entityName": "ICTServe_System",
      "contents": [
        "Deployed on Docker",
        "MySQL 8.0 database"
      ]
    }
  ]
}
```

### search_nodes

```json
{
  "query": "Livewire"
}
```

### open_nodes

```json
{
  "names": ["ICTServe_System", "Staff_Dashboard"]
}
```

### read_graph
Returns entire knowledge graph (no input required).

## Usage with Claude Desktop

Add to `claude_desktop_config.json`:

```json
{
  "mcpServers": {
    "memory": {
      "command": "docker",
      "args": ["run", "-i", "-v", "ictserve-memory:/app/dist", "--rm", "mcp/memory"]
    }
  }
}
```

## Usage with VS Code

Add to `.vscode/mcp.json`:

```json
{
  "servers": {
    "memory": {
      "command": "docker",
      "args": [
        "run",
        "-i",
        "-v",
        "ictserve-memory:/app/dist",
        "--rm",
        "mcp/memory"
      ]
    }
  }
}
```

## Helper Scripts

```powershell
# Start memory server
./scripts/docker/memory-mcp.ps1 start

# Stop memory server
./scripts/docker/memory-mcp.ps1 stop

# Check status
./scripts/docker/memory-mcp.ps1 status

# View logs
./scripts/docker/memory-mcp.ps1 logs

# Open shell
./scripts/docker/memory-mcp.ps1 shell
```

## Existing Memory Data

ICTServe has existing knowledge stored in `storage/mcp/memory.jsonl`:

- **50+ entities** covering system documentation, implementations, patterns
- **Complete D00-D15 documentation** mapped to memory entities
- **Coding patterns** (Filament 4, Livewire 3, testing, database)
- **Solved issues** (500 errors, DB connections, seeding)
- **Implementation history** (email system, helpdesk module, E2E tests)

### Import Existing Data

```powershell
# Import all existing knowledge
./scripts/docker/import-memory.ps1
```

This imports:

- System documentation entities (D00-D15)
- Technical implementations
- Coding patterns and best practices
- Solved issues and debugging solutions
- Work session history

## Data Persistence

Memory data is stored in Docker volume `memory-data`:

```powershell
# Inspect volume
docker volume inspect ictserve-031125_memory-data

# Backup memory
docker run --rm -v ictserve-031125_memory-data:/data -v ${PWD}:/backup alpine tar czf /backup/memory-backup.tar.gz -C /data .

# Restore memory
docker run --rm -v ictserve-031125_memory-data:/data -v ${PWD}:/backup alpine tar xzf /backup/memory-backup.tar.gz -C /data
```

## Integration with ICTServe

### Example: Store Project Status

```json
{
  "entities": [
    {
      "name": "ICTServe_Project_Status",
      "entityType": "project_status",
      "observations": [
        "Phase: Production Ready",
        "Version: 3.0.0",
        "Last Updated: 2025-01-06",
        "Docker Setup: Complete",
        "MCP Memory: Configured"
      ]
    }
  ]
}
```

### Example: Link Documentation

```json
{
  "relations": [
    {
      "from": "Staff_Dashboard_Implementation",
      "to": "D04_Software_Design",
      "relationType": "documented_by"
    },
    {
      "from": "Staff_Dashboard_Implementation",
      "to": "Livewire_3_Component_Patterns",
      "relationType": "uses"
    }
  ]
}
```

## Troubleshooting

### Container won't start

```powershell
# Check logs
docker compose logs memory

# Rebuild image
docker compose build memory
docker compose up -d memory
```

### Memory data lost

```powershell
# Verify volume exists
docker volume ls | Select-String "memory-data"

# Check volume mount
docker compose exec memory ls -la /app/dist
```

### Permission issues

```powershell
# Fix volume permissions
docker compose exec memory chmod -R 755 /app/dist
```

## References

- Official Docs: <https://github.com/modelcontextprotocol/servers/tree/main/src/memory>
- MCP Protocol: <https://modelcontextprotocol.io>
- ICTServe Memory Guide: `.amazonq/rules/Memory.md`
