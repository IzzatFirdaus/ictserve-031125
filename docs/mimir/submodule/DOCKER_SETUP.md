# Mimir Docker Setup

## Quick Start

```bash
cd Mimir

# Start Mimir services
make setup

# View logs
make logs
```

## Services

- **Neo4j** - Graph database (ports 7474, 7687)
- **Copilot API** - GitHub Copilot LLM (port 4141)
- **Mimir Server** - Memory system (port 9042)

## Access

- Mimir Portal: <http://localhost:9042/portal>
- Neo4j Browser: <http://localhost:7474>
- Health Check: <http://localhost:9042/health>

## Commands

```bash
# Build images
make build

# Start services
make up

# Stop services
make stop

# Restart services
make restart

# View logs
make logs

# Open shell
make shell

# Clean (remove volumes)
make clean
```

## Configuration

Edit `.env.docker` for custom settings:

- `NEO4J_PASSWORD` - Database password
- `MIMIR_DEFAULT_MODEL` - LLM model (gpt-4.1)
- `MIMIR_EMBEDDINGS_ENABLED` - Enable/disable embeddings

## Troubleshooting

**Copilot API authentication:**

```bash
# Add GitHub token to copilot-data/github_token
echo "ghp_YOUR_TOKEN" > copilot-data/github_token
```

**Neo4j connection failed:**

```bash
# Check Neo4j health
docker compose -f compose.yaml logs neo4j
```

**Port conflicts:**

```bash
# Change ports in compose.yaml
ports:
  - "9043:3000"  # Change 9042 to 9043
```

## Integration with ICTServe

Mimir runs independently but can be accessed from ICTServe:

```bash
# From ICTServe root
cd Mimir && make up

# Access from ICTServe MCP config
# URL: http://localhost:9042/mcp
```
