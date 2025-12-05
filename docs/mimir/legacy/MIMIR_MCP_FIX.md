# Mimir MCP Connection Fix

**Date**: 2025-12-04  
**Issue**: MCP connection closing immediately after start  
**Status**: ✅ RESOLVED

---

## Problem

Kiro IDE was trying to run Mimir as a Node.js process:

```json
"mimir": {
  "command": "node",
  "args": ["C:\\laragon\\www\\ictserve-031125\\Mimir\\build\\index.js"],
  "env": { ... },
  "disabled": false
}
```

**Error Logs**:
```
[error] [mimir] Error connecting to MCP server: MCP error -32000: Connection closed
```

---

## Root Cause

Mimir is designed to run as a **Docker container** and be accessed via **HTTP endpoint**, not as a standalone Node.js process. The Docker container:

1. Manages Neo4j connection
2. Handles Copilot API integration
3. Provides HTTP server on port 9042
4. Serves MCP protocol at `/mcp` endpoint

---

## Solution

Changed MCP configuration to use HTTP endpoint:

```json
"mimir": {
  "url": "http://localhost:9042/mcp",
  "disabled": false,
  "autoApprove": [
    "memory_node",
    "memory_edge",
    "memory_batch",
    "memory_lock",
    "memory_clear",
    "index_folder",
    "remove_folder",
    "list_folders",
    "vector_search_nodes",
    "get_embedding_stats",
    "todo",
    "todo_list",
    "get_task_context"
  ]
}
```

---

## Verification

### 1. Docker Services Running

```powershell
PS> docker ps --filter "name=mimir"
NAMES                STATUS                      PORTS
mimir_server         Up (healthy)                0.0.0.0:9042->3000/tcp
neo4j_db             Up (healthy)                0.0.0.0:7474->7474/tcp, 0.0.0.0:7687->7687/tcp
```

### 2. HTTP Endpoint Responding

```powershell
PS> curl http://localhost:9042/health
StatusCode: 200
Content: {"status":"healthy","version":"4.1.0","mode":"shared-session","tools":17}
```

### 3. MCP Protocol Working

```powershell
PS> curl http://localhost:9042/mcp -Method POST -Headers @{"Content-Type"="application/json"} -Body '{"jsonrpc":"2.0","method":"initialize","params":{},"id":1}'
StatusCode: 200
Content: {"jsonrpc":"2.0","id":1,"result":{"protocolVersion":"2024-11-05",...}}
```

---

## Key Differences: Node Process vs HTTP Endpoint

| Aspect | Node Process | HTTP Endpoint |
|--------|-------------|---------------|
| **Execution** | Kiro spawns Node.js | Docker container runs independently |
| **Connection** | stdio (stdin/stdout) | HTTP (JSON-RPC over HTTP) |
| **Dependencies** | Requires Neo4j on host | Neo4j in Docker container |
| **Configuration** | Environment variables | Docker Compose + .env |
| **Session** | Per-client | Shared global session |
| **Lifecycle** | Managed by Kiro | Managed by Docker |

---

## MCP Protocol Details

**Endpoint**: `http://localhost:9042/mcp`  
**Protocol**: JSON-RPC 2.0  
**Version**: 2024-11-05  
**Session Mode**: `shared-global-session`  
**Server**: Mimir-RAG-TODO-MCP v4.0.0  
**Tools**: 17 available

### Available Tools

1. `memory_node` - Manage memory nodes (add, get, update, delete, query, search)
2. `memory_edge` - Manage relationships between nodes
3. `memory_batch` - Bulk operations on nodes/edges
4. `memory_lock` - Multi-agent coordination locks
5. `memory_clear` - Clear data from graph
6. `index_folder` - Index files and watch for changes
7. `remove_folder` - Stop watching folder
8. `list_folders` - List watched folders
9. `vector_search_nodes` - Semantic search with embeddings
10. `get_embedding_stats` - Embedding statistics
11. `todo` - Manage individual todos
12. `todo_list` - Manage todo lists
13. `get_task_context` - Get filtered task context
14. `execute_workflow` - Execute parallel LLM workflows
15. `get_execution_status` - Check workflow status
16. `get_execution_results` - Get workflow results
17. `cancel_execution` - Cancel running workflow

---

## Next Steps

1. **Restart Kiro IDE** to reconnect with new HTTP configuration
2. **Test Mimir Tools** in Kiro IDE
3. **Verify** tools are accessible and working

---

## Troubleshooting

### If Connection Still Fails

1. **Check Docker Services**:
   ```powershell
   docker ps --filter "name=mimir"
   ```

2. **Check Mimir Logs**:
   ```powershell
   docker logs mimir_server --tail 50
   ```

3. **Test HTTP Endpoint**:
   ```powershell
   curl http://localhost:9042/health
   ```

4. **Restart Mimir**:
   ```powershell
   docker restart mimir_server
   ```

### If Tools Not Working

1. **Check Neo4j Connection**:
   - Open Neo4j Browser: http://localhost:7474
   - Login: neo4j / MxXhTKH3qntipYLa1e0QOluJ

2. **Check Copilot API**:
   ```powershell
   docker logs copilot_api_server --tail 20
   ```

3. **Verify Environment**:
   ```powershell
   docker exec mimir_server env | grep MIMIR
   ```

---

## Related Documentation

- `MIMIR_DOCKER_STATUS.md` - Current operational status
- `MIMIR_SETUP_GUIDE.md` - Setup instructions
- `docs/mimir/MCP_INTEGRATION.md` - MCP integration guide
- `docs/mimir/DOCKER.md` - Docker configuration

---

**Resolution**: ✅ COMPLETE  
**Configuration**: HTTP endpoint at http://localhost:9042/mcp  
**Status**: Ready for use after Kiro IDE restart
