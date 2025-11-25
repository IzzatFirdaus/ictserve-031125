# Mimir Setup Guide for ICTServe

## Quick Setup (3 Steps)

### 1. Start Mimir Services

```powershell
cd Mimir
docker compose -f compose.yaml up -d
```

**Services Started**:

- ✅ Neo4j (database) - <http://localhost:7474>
- ✅ Copilot API (LLM) - <http://localhost:4141>  
- ✅ Mimir Server (MCP + Web UI) - <http://localhost:9042>

### 2. Verify Services Running

```powershell
# Check all services are up
docker compose -f compose.yaml ps

# Check Mimir health
curl http://localhost:9042/health

# View logs
docker compose -f compose.yaml logs -f mimir-server
```

### 3. Configure IDE Integration

#### VS Code / Cursor / Windsurf

Add to your MCP settings (`.kiro/settings/mcp.json` or equivalent):

```json
{
  "mcpServers": {
    "mimir": {
      "command": "node",
      "args": ["Mimir/scripts/mcp-http-client.js", "http://localhost:9042/mcp"],
      "env": {
        "MIMIR_API_URL": "http://localhost:9042"
      },
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

**Restart your IDE** to load the MCP server.

## Access Points

| Service | URL | Credentials |
|---------|-----|-------------|
| **Mimir Portal** | <http://localhost:9042/portal> | None |
| **Neo4j Browser** | <http://localhost:7474> | neo4j / MxXhTKH3qntipYLa1e0QOluJ |
| **MCP API** | <http://localhost:9042/mcp> | None |
| **Chat API** | <http://localhost:9042/v1/chat/completions> | None |

## Using Mimir with AI Agents

### In VS Code Chat

```
@mimir create a TODO for implementing authentication
@mimir list all pending tasks
@mimir search for authentication-related files
```

### In Claude Desktop / ChatGPT

```
Use the memory_node tool to create a task for implementing authentication
Use the vector_search_nodes tool to find related files
Use the todo tool to list all pending tasks
```

## File Indexing (Optional)

Index your codebase for semantic search:

```powershell
# Index ICTServe project
cd Mimir
npm run index:add C:\XAMPP\htdocs\ictserve-031125

# With embeddings (slower but enables semantic search)
npm run index:add C:\XAMPP\htdocs\ictserve-031125 --embeddings

# List indexed folders
npm run index:list

# Remove folder
npm run index:remove C:\XAMPP\htdocs\ictserve-031125
```

## Configuration

Current setup (`.env`):

- ✅ **LLM Provider**: GitHub Copilot (GPT-4.1)
- ✅ **Embeddings**: Copilot text-embedding-3-small (1536 dims)
- ✅ **Workspace**: `C:\XAMPP\htdocs\ictserve-031125`
- ✅ **Auto-index docs**: Enabled

## Troubleshooting

### Services won't start

```powershell
# Check Docker is running
docker ps

# View logs
cd Mimir
docker compose -f compose.yaml logs

# Restart services
docker compose -f compose.yaml restart
```

### Can't connect to Neo4j

```powershell
# Wait 30-60 seconds for Neo4j to fully start
docker compose -f compose.yaml logs neo4j

# Test connection
curl http://localhost:7474
```

### MCP not working in IDE

1. Check Mimir is running: `curl http://localhost:9042/health`
2. Verify MCP config in `.kiro/settings/mcp.json`
3. Restart IDE completely
4. Check IDE logs for MCP errors

### Embeddings not working

```powershell
# Check Copilot API is running
docker compose -f compose.yaml logs copilot-api

# Test embeddings endpoint
curl -X POST http://localhost:4141/v1/embeddings \
  -H "Content-Type: application/json" \
  -d '{"input": "test", "model": "text-embedding-3-small"}'
```

## Available MCP Tools

Mimir provides 13 tools for AI agents:

**Memory Operations**:

- `memory_node` - Create/read/update nodes (tasks, files, concepts)
- `memory_edge` - Create relationships between nodes
- `memory_batch` - Bulk operations
- `memory_lock` - Multi-agent coordination
- `memory_clear` - Clear data (use carefully!)
- `get_task_context` - Get filtered context by agent type

**File Indexing**:

- `index_folder` - Index code files into graph
- `remove_folder` - Stop watching folder
- `list_folders` - Show watched folders

**Vector Search**:

- `vector_search_nodes` - Semantic search with AI embeddings
- `get_embedding_stats` - Embedding statistics

**Todo Management**:

- `todo` - Manage individual tasks
- `todo_list` - Manage task lists

## Next Steps

1. ✅ **Test in IDE**: Try `@mimir what is Neo4j?` in VS Code Chat
2. ✅ **Index codebase**: Run `npm run index:add` to enable semantic search
3. ✅ **Explore Portal**: Visit <http://localhost:9042/portal>
4. ✅ **View graph**: Open Neo4j Browser at <http://localhost:7474>

## Documentation

- **Mimir GitHub**: <https://github.com/orneryd/Mimir>
- **Quick Start**: <https://github.com/orneryd/Mimir#quick-start>
- **IDE Integration**: <https://github.com/orneryd/Mimir/blob/main/docs/IDE_INTEGRATION.md>
- **MCP Tools**: <https://github.com/orneryd/Mimir#available-tools>
