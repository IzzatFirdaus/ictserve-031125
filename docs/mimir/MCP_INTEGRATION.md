# Mimir MCP Integration

AI agent integration guide for Mimir memory system.

## Overview

Mimir provides 13 MCP tools for AI agents to manage knowledge, search code, and track tasks.

## Current Status

**Kiro IDE Integration**: ❌ Disabled  
**Reason**: HTTP bridge script not available  
**Alternative**: Use built-in memory server

## Built-in Memory Server (Recommended)

Kiro's built-in memory server is enabled and working:

```json
{
  "mcpServers": {
    "memory": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory", "storage/mcp/memory.jsonl"],
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
- create_entities - Add knowledge
- create_relations - Link entities
- add_observations - Update facts
- delete_entities - Remove entities
- delete_observations - Remove facts
- delete_relations - Remove links
- read_graph - View all knowledge
- search_nodes - Find information
- open_nodes - Retrieve details

## Mimir HTTP API

Access Mimir directly via HTTP:

### Health Check

```bash
curl http://localhost:9042/health

# Response: {"status":"healthy","version":"4.1.0","tools":13}
```

### Create Memory Node

```bash
curl -X POST http://localhost:9042/mcp \
  -H "Content-Type: application/json" \
  -d '{
    "method": "memory_node",
    "params": {
      "action": "create",
      "data": {
        "type": "task",
        "content": "Implement authentication",
        "metadata": {"priority": "high"}
      }
    }
  }'
```

### Vector Search

```bash
curl -X POST http://localhost:9042/mcp \
  -H "Content-Type: application/json" \
  -d '{
    "method": "vector_search_nodes",
    "params": {
      "query": "authentication implementation",
      "limit": 10
    }
  }'
```

### List Tasks

```bash
curl -X POST http://localhost:9042/mcp \
  -H "Content-Type: application/json" \
  -d '{
    "method": "todo_list",
    "params": {
      "filter": "pending"
    }
  }'
```

## Available Tools (13)

### Memory Operations (6)

**memory_node**
- Create, read, update, delete nodes
- Node types: task, file, concept, entity
- Supports metadata and relationships

**memory_edge**
- Create relationships between nodes
- Edge types: depends_on, related_to, implements, uses

**memory_batch**
- Bulk operations for efficiency
- Create multiple nodes/edges at once

**memory_lock**
- Multi-agent coordination
- Prevent concurrent modifications

**memory_clear**
- Clear all data (use carefully!)
- Requires confirmation

**get_task_context**
- Get filtered context by agent type
- Returns relevant nodes and edges

### File Indexing (3)

**index_folder**
- Index code files into graph
- Extracts functions, classes, imports
- Creates file nodes and relationships

**list_folders**
- Show watched folders
- Display indexing status

**get_folder_stats**
- Folder statistics
- File count, size, last indexed

### Vector Search (2)

**vector_search_nodes**
- Semantic search with embeddings
- Find similar nodes by meaning
- Requires embeddings enabled

**get_embedding_stats**
- Embedding statistics
- Model info, dimensions, count

### Task Management (2)

**todo**
- Create, update, complete tasks
- Set priority, due date, assignee

**todo_list**
- List tasks by filter
- Filters: pending, completed, overdue

## Portal UI

Access Mimir web portal:

```bash
start http://localhost:9042/portal
```

**Features**:
- Visual knowledge graph
- Task management
- File indexing
- Search interface
- Settings configuration

## Neo4j Browser

Direct database access:

```bash
start http://localhost:7474
```

**Credentials**:
- User: neo4j
- Password: MxXhTKH3qntipYLa1e0QOluJ

**Cypher Queries**:

```cypher
// List all nodes
MATCH (n) RETURN n LIMIT 25

// Find tasks
MATCH (n:Task) RETURN n

// Find relationships
MATCH (a)-[r]->(b) RETURN a, r, b LIMIT 25

// Search by content
MATCH (n) WHERE n.content CONTAINS 'authentication' RETURN n
```

## Future Integration

To enable Kiro → Mimir MCP:

### 1. Create HTTP Bridge Script

Create `Mimir/scripts/mcp-http-client.js`:

```javascript
#!/usr/bin/env node
const http = require('http');

const MIMIR_URL = process.argv[2] || 'http://localhost:9042/mcp';

// MCP stdio protocol bridge
process.stdin.on('data', async (data) => {
  const request = JSON.parse(data.toString());
  
  const response = await fetch(MIMIR_URL, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(request)
  });
  
  const result = await response.json();
  process.stdout.write(JSON.stringify(result) + '\n');
});
```

### 2. Update Kiro Config

Enable in `.kiro/settings/mcp.json`:

```json
{
  "mcpServers": {
    "mimir": {
      "command": "node",
      "args": ["Mimir/scripts/mcp-http-client.js", "http://localhost:9042/mcp"],
      "disabled": false,
      "autoApprove": [
        "memory_node",
        "memory_edge",
        "vector_search_nodes",
        "todo",
        "todo_list"
      ]
    }
  }
}
```

### 3. Restart IDE

Restart Kiro to load MCP server.

## Comparison: Built-in vs Mimir

| Feature | Built-in Memory | Mimir |
|---------|----------------|-------|
| Storage | JSONL file | Neo4j graph |
| Search | Text match | Semantic search |
| Relationships | Basic | Advanced graph |
| Visualization | None | Neo4j Browser |
| File Indexing | No | Yes |
| Task Management | No | Yes |
| Multi-agent | No | Yes (locks) |
| Setup | None | Docker required |

## Recommendation

**Use built-in memory server** for:
- Simple knowledge storage
- Quick setup
- No Docker required
- Basic entity/relation management

**Use Mimir** for:
- Advanced graph queries
- Semantic search
- File indexing
- Task management
- Multi-agent coordination
- Visual knowledge graph

## Next Steps

1. [Setup Guide](SETUP.md) - Install Mimir
2. [Docker Deployment](DOCKER.md) - Docker configuration
3. [Troubleshooting](TROUBLESHOOTING.md) - Common issues
