# Mimir MCP Integration Status

## Current Status: HTTP-Only Access

**Mimir MCP Server**: Running in Docker at <http://localhost:9042/mcp>  
**Kiro IDE Integration**: Disabled (HTTP bridge not available)

## Why Disabled

Kiro IDE expects a local Node.js script (`mcp-http-client.js`) to bridge HTTP connections, but:

- Mimir runs in Docker container
- HTTP bridge script doesn't exist in local workspace
- Direct HTTP MCP connections not supported by Kiro

## Available Access Methods

### 1. Direct HTTP API (Working ✅)

```bash
# Health check
curl http://localhost:9042/health

# Portal UI
http://localhost:9042/portal
```

### 2. Docker Container Access (Working ✅)

```bash
# Shell into container
docker exec -it mimir-server sh

# View logs
docker logs mimir-server -f
```

### 3. Neo4j Direct Access (Working ✅)

```bash
# Neo4j Browser
http://localhost:7474

# Credentials in Mimir/.env
```

## Mimir Tools (13 available via HTTP)

**Memory Operations**:

- memory_node, memory_edge, memory_batch
- vector_search_nodes, vector_search_edges

**File Indexing**:

- index_folder, list_folders, get_folder_stats

**Task Management**:

- todo, todo_list, get_task_context

**System**:

- get_embedding_stats, health_check

## Alternative: Use Built-in Memory Server

Kiro's built-in `memory` MCP server is **enabled and working**:

```json
"memory": {
  "command": "npx",
  "args": ["-y", "@modelcontextprotocol/server-memory", "storage/mcp/memory.jsonl"],
  "disabled": false
}
```

**9 auto-approved tools**:

- create_entities, create_relations, add_observations
- delete_entities, delete_observations, delete_relations
- read_graph, search_nodes, open_nodes

## Recommendation

**Use built-in memory server** for AI agent memory operations. Mimir remains available for:

- Advanced vector search via HTTP API
- Neo4j graph visualization
- Codebase indexing via portal
- Task context management

## Future Integration

To enable Kiro → Mimir MCP:

1. Create `Mimir/scripts/mcp-http-client.js` bridge script
2. Update `.kiro/settings/mcp.json` with `disabled: false`
3. Restart IDE

---

**Current config**: Mimir disabled in Kiro, built-in memory server active
