# Mimir MCP Integration

AI agent integration guide for Mimir memory system.

## Overview

Mimir provides 13+ MCP tools for AI agents to manage knowledge graphs, semantic search, and task tracking. It runs as a **Docker-based service** with HTTP API.

## Current Status

**Kiro IDE Integration**: ⏸️ Disabled (requires full Docker stack)  
**Recommended**: Use built-in `memory` MCP server for basic knowledge graph needs  
**Alternative**: Run Mimir Docker stack and access via HTTP API

## Architecture

Mimir is designed to run as a Docker service stack:

```
┌─────────────────────────────────────────────────────────────┐
│                    Mimir Docker Stack                        │
│                                                              │
│  ┌──────────────────┐    ┌──────────────────┐              │
│  │  mimir-server    │───▶│     neo4j        │              │
│  │     :9042        │    │   :7474/:7687    │              │
│  └──────────────────┘    └──────────────────┘              │
│           │                                                  │
│           ▼                                                  │
│  ┌──────────────────┐                                       │
│  │   copilot-api    │  (optional - for LLM/embeddings)      │
│  │     :4141        │                                       │
│  └──────────────────┘                                       │
└─────────────────────────────────────────────────────────────┘
```

## Option 1: Use Built-in Memory Server (Recommended)

For most use cases, Kiro's built-in `memory` MCP server provides sufficient functionality:

```json
{
	"mcpServers": {
		"memory": {
			"command": "npx",
			"args": [
				"-y",
				"@modelcontextprotocol/server-memory",
				"storage/mcp/memory.jsonl"
			],
			"disabled": false,
			"autoApprove": [
				"create_entities",
				"create_relations",
				"add_observations",
				"delete_entities",
				"delete_observations",
				"delete_relations",
				"read_graph",
				"search_nodes",
				"open_nodes"
			]
		}
	}
}
```

**9 Auto-approved Tools**:

- `create_entities` - Add knowledge nodes
- `create_relations` - Link entities
- `add_observations` - Update facts
- `delete_entities` - Remove entities
- `delete_observations` - Remove facts
- `delete_relations` - Remove links
- `read_graph` - View all knowledge
- `search_nodes` - Find information
- `open_nodes` - Retrieve details

## Option 2: Run Mimir Docker Stack

For advanced features (semantic search, file indexing, task management):

### 1. Start Mimir Docker Stack

```bash
cd Mimir

# Copy environment template
cp env.example .env

# Edit .env to set NEO4J_PASSWORD
# NEO4J_PASSWORD=MxXhTKH3qntipYLa1e0QOluJ

# Start all services
npm run start
# Or: docker compose up -d
```

### 2. Verify Services Running

```bash
# Check health
curl http://localhost:9042/health

# Expected: {"status":"healthy","version":"4.1.0","tools":13}
```

### 3. Access Mimir

- **Web UI**: http://localhost:9042
- **MCP API**: http://localhost:9042/mcp
- **Neo4j Browser**: http://localhost:7474

## Mimir HTTP API Usage

When Mimir Docker stack is running, access tools via HTTP:

### Health Check

```bash
curl http://localhost:9042/health
```

### Create Memory Node

```bash
curl -X POST http://localhost:9042/mcp \
  -H "Content-Type: application/json" \
  -d '{
    "jsonrpc": "2.0",
    "method": "tools/call",
    "params": {
      "name": "memory_node",
      "arguments": {
        "action": "create",
        "data": {
          "type": "task",
          "content": "Implement authentication",
          "metadata": {"priority": "high"}
        }
      }
    },
    "id": 1
  }'
```

### Vector Search

```bash
curl -X POST http://localhost:9042/mcp \
  -H "Content-Type: application/json" \
  -d '{
    "jsonrpc": "2.0",
    "method": "tools/call",
    "params": {
      "name": "vector_search_nodes",
      "arguments": {
        "query": "authentication implementation",
        "limit": 10
      }
    },
    "id": 2
  }'
```

## Available Tools (13)

### Memory Operations (5 tools)

| Tool           | Description                        |
| -------------- | ---------------------------------- |
| `memory_node`  | Create, read, update, delete nodes |
| `memory_edge`  | Create relationships between nodes |
| `memory_batch` | Bulk operations for efficiency     |
| `memory_lock`  | Multi-agent coordination locks     |
| `memory_clear` | Clear all data (use carefully!)    |

### File Indexing (3 tools)

| Tool            | Description                 |
| --------------- | --------------------------- |
| `index_folder`  | Index code files into graph |
| `remove_folder` | Remove indexed folder       |
| `list_folders`  | Show watched folders        |

### Vector Search (2 tools)

| Tool                  | Description                     |
| --------------------- | ------------------------------- |
| `vector_search_nodes` | Semantic search with embeddings |
| `get_embedding_stats` | Embedding statistics            |

### Task Management (3 tools)

| Tool               | Description                        |
| ------------------ | ---------------------------------- |
| `todo`             | Create, update, complete tasks     |
| `todo_list`        | List tasks by filter               |
| `get_task_context` | Get filtered context by agent type |

## Comparison: Built-in Memory vs Mimir

| Feature         | Built-in Memory | Mimir                  |
| --------------- | --------------- | ---------------------- |
| Storage         | JSONL file      | Neo4j graph            |
| Search          | Text match      | Semantic search        |
| Relationships   | Basic           | Advanced graph         |
| Visualization   | None            | Neo4j Browser + Web UI |
| File Indexing   | No              | Yes                    |
| Task Management | No              | Yes                    |
| Multi-agent     | No              | Yes (locks)            |
| Setup           | None            | Docker required        |
| Persistence     | File-based      | Database               |

## Why Mimir is Disabled by Default

1. **Docker Dependency**: Requires full Docker stack (Neo4j, Mimir Server)
2. **Resource Usage**: Neo4j requires significant memory (~1GB+)
3. **Complexity**: More setup than built-in memory server
4. **Kiro Limitation**: Kiro MCP client expects stdio transport, not HTTP

## Enabling Mimir (Advanced Users)

To use Mimir with Kiro IDE:

1. **Run Mimir Docker Stack** (see above)
2. **Access via HTTP API** directly in your code/scripts
3. **Or** create an HTTP-to-stdio bridge script (advanced)

For most ICTServe development, the built-in `memory` server is sufficient.

## References

- [Mimir Official Documentation](README-official.md)
- [Mimir GitHub Repository](https://github.com/orneryd/Mimir)
- [Neo4j Knowledge Graph Guide](NEO4J_KNOWLEDGE_GRAPH_GUIDE.md)

---

**Last Updated**: 2025-11-30  
**Status**: Mimir disabled in Kiro (use built-in memory or Docker stack)
