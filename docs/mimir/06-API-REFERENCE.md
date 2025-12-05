# Mimir API Reference

**Version**: 4.1.0  
**Last Updated**: 2025-12-05  
**Status**: ✅ Complete

---

## Overview

Mimir provides 17 MCP (Model Context Protocol) tools for knowledge graph management, file indexing, and workflow orchestration. This reference documents all available tools.

---

## MCP Endpoint

```
http://localhost:9042/mcp
```

### Connection Test

```powershell
# Test MCP endpoint
curl http://localhost:9042/health

# Expected response:
# {"status":"healthy","version":"4.1.0"}
```

---

## Memory Management Tools

### 1. memory_node

Manage knowledge graph nodes (entities).

**Operations**: `add`, `get`, `update`, `delete`, `query`, `search`

#### Add Node

```javascript
memory_node({
  operation: "add",
  type: "memory",
  properties: {
    name: "Laravel_Authentication_Pattern",
    title: "Laravel Authentication Implementation",
    content: "JWT-based authentication with refresh tokens...",
    tags: ["laravel", "authentication", "security"]
  }
})
```

#### Get Node

```javascript
memory_node({
  operation: "get",
  id: "memory-123"
})
```

#### Update Node

```javascript
memory_node({
  operation: "update",
  id: "memory-123",
  properties: {
    status: "completed",
    updated_at: "2025-12-05T10:00:00Z"
  }
})
```

#### Query Nodes

```javascript
memory_node({
  operation: "query",
  type: "todo",
  filters: {
    status: "pending",
    priority: "high"
  }
})
```

#### Search Nodes

```javascript
memory_node({
  operation: "search",
  query: "authentication security",
  options: {
    limit: 10,
    types: ["memory", "concept"]
  }
})
```

#### Delete Node

```javascript
// Request deletion (returns confirmationId)
memory_node({
  operation: "delete",
  id: "memory-123"
})

// Confirm deletion
memory_node({
  operation: "delete",
  id: "memory-123",
  confirm: true,
  confirmationId: "conf-xyz"
})
```

---

### 2. memory_edge

Manage relationships between nodes.

**Operations**: `add`, `delete`, `get`, `neighbors`, `subgraph`

#### Add Relationship

```javascript
memory_edge({
  operation: "add",
  source: "memory-123",
  target: "concept-456",
  type: "relates_to",
  properties: {
    strength: "strong",
    context: "implementation pattern"
  }
})
```

#### Get Relationships

```javascript
memory_edge({
  operation: "get",
  node_id: "memory-123",
  direction: "both"  // "in", "out", or "both"
})
```

#### Find Neighbors

```javascript
memory_edge({
  operation: "neighbors",
  node_id: "memory-123",
  edge_type: "relates_to",
  depth: 2
})
```

#### Get Subgraph

```javascript
memory_edge({
  operation: "subgraph",
  node_id: "project-1",
  depth: 2
})
```

#### Delete Relationship

```javascript
memory_edge({
  operation: "delete",
  edge_id: "edge-789"
})
```

---

### 3. memory_batch

Perform bulk operations efficiently.

**Operations**: `add_nodes`, `update_nodes`, `delete_nodes`, `add_edges`, `delete_edges`

#### Batch Add Nodes

```javascript
memory_batch({
  operation: "add_nodes",
  nodes: [
    {
      type: "todo",
      properties: {
        title: "Implement feature A",
        priority: "high"
      }
    },
    {
      type: "todo",
      properties: {
        title: "Implement feature B",
        priority: "medium"
      }
    }
  ]
})
```

#### Batch Update Nodes

```javascript
memory_batch({
  operation: "update_nodes",
  updates: [
    {
      id: "todo-1",
      properties: { status: "completed" }
    },
    {
      id: "todo-2",
      properties: { status: "in_progress" }
    }
  ]
})
```

#### Batch Delete Nodes

```javascript
// Request deletion
memory_batch({
  operation: "delete_nodes",
  ids: ["todo-1", "todo-2", "todo-3"]
})

// Confirm deletion
memory_batch({
  operation: "delete_nodes",
  ids: ["todo-1", "todo-2", "todo-3"],
  confirm: true,
  confirmationId: "conf-xyz"
})
```

---

### 4. memory_lock

Manage locks for multi-agent coordination.

**Operations**: `acquire`, `release`, `query_available`, `cleanup`

#### Acquire Lock

```javascript
memory_lock({
  operation: "acquire",
  node_id: "todo-1",
  agent_id: "worker-1",
  timeout_ms: 300000  // 5 minutes
})
```

#### Release Lock

```javascript
memory_lock({
  operation: "release",
  node_id: "todo-1",
  agent_id: "worker-1"
})
```

#### Query Available Nodes

```javascript
memory_lock({
  operation: "query_available",
  type: "todo",
  filters: { status: "pending" }
})
```

#### Cleanup Expired Locks

```javascript
memory_lock({
  operation: "cleanup"
})
```

---

### 5. memory_clear

Clear data from knowledge graph.

**Operations**: Clear by type or all data

#### Clear Specific Type

```javascript
// Request clear (returns confirmationId)
memory_clear({
  type: "todo"
})

// Confirm clear
memory_clear({
  type: "todo",
  confirm: true,
  confirmationId: "conf-xyz"
})
```

#### Clear All Data

```javascript
// ⚠️ DANGEROUS: Clears entire graph
memory_clear({
  type: "ALL",
  confirm: true,
  confirmationId: "conf-xyz"
})
```

---

## File Indexing Tools

### 6. index_folder

Index files and watch for changes.

```javascript
index_folder({
  path: "/workspace/app/Models",
  file_patterns: ["*.php"],
  ignore_patterns: ["*.test.php"],
  recursive: true,
  generate_embeddings: true,
  debounce_ms: 500
})
```

**Parameters**:

- `path` (required): Absolute path to folder
- `file_patterns`: File patterns to watch (default: all files)
- `ignore_patterns`: Additional ignore patterns
- `recursive`: Watch subdirectories (default: true)
- `generate_embeddings`: Generate vector embeddings (default: auto-detected)
- `debounce_ms`: Debounce delay for file events (default: 500)

---

### 7. remove_folder

Stop watching folder and remove indexed files.

```javascript
remove_folder({
  path: "/workspace/app/Models"
})
```

---

### 8. list_folders

List all watched folders.

```javascript
list_folders()
```

**Returns**:

```json
{
  "folders": [
    {
      "path": "/workspace/app/Models",
      "file_patterns": ["*.php"],
      "recursive": true,
      "file_count": 25
    }
  ]
}
```

---

## Search Tools

### 9. vector_search_nodes

Semantic search using vector embeddings.

```javascript
vector_search_nodes({
  query: "authentication and security implementation",
  types: ["memory", "file_chunk"],
  min_similarity: 0.75,
  limit: 10,
  depth: 1
})
```

**Parameters**:

- `query` (required): Natural language search query
- `types`: Filter by node types (optional)
- `min_similarity`: Minimum cosine similarity (0-1, default: 0.75)
- `limit`: Maximum results (default: 10)
- `depth`: Graph traversal depth (1-3, default: 1)

**Returns**: Nodes ranked by semantic similarity

---

### 10. get_embedding_stats

Get statistics about embeddings.

```javascript
get_embedding_stats()
```

**Returns**:

```json
{
  "total_nodes_with_embeddings": 150,
  "by_type": {
    "file_chunk": 120,
    "memory": 20,
    "todo": 10
  },
  "dimensions": 768,
  "provider": "ollama",
  "model": "nomic-embed-text"
}
```

---

## Task Management Tools

### 11. todo

Manage individual tasks.

**Operations**: `create`, `get`, `update`, `complete`, `delete`, `list`

#### Create Todo

```javascript
todo({
  operation: "create",
  title: "Implement email notifications",
  description: "Add email notification system using Laravel Mail",
  priority: "high",
  status: "pending",
  list_id: "todoList-123"
})
```

#### Complete Todo

```javascript
todo({
  operation: "complete",
  todo_id: "todo-456"
})
```

#### List Todos

```javascript
todo({
  operation: "list",
  filters: {
    status: "pending",
    priority: "high"
  }
})
```

---

### 12. todo_list

Manage task collections.

**Operations**: `create`, `get`, `update`, `archive`, `delete`, `list`, `add_todo`, `remove_todo`, `get_stats`

#### Create Todo List

```javascript
todo_list({
  operation: "create",
  title: "Sprint 1 Tasks",
  description: "Tasks for first sprint",
  priority: "high"
})
```

#### Add Todo to List

```javascript
todo_list({
  operation: "add_todo",
  list_id: "todoList-123",
  todo_id: "todo-456"
})
```

#### Get List Stats

```javascript
todo_list({
  operation: "get_stats",
  list_id: "todoList-123"
})
```

**Returns**:

```json
{
  "total": 10,
  "pending": 5,
  "in_progress": 3,
  "completed": 2
}
```

---

## Workflow Tools

### 13. execute_workflow

Execute multi-agent workflow.

```javascript
execute_workflow({
  tasks: [
    {
      id: "task-1",
      title: "Generate code",
      prompt: "Generate User model with authentication",
      agentRoleDescription: "Laravel code generator",
      recommendedModel: "gpt-4o",
      parallelGroup: 1,
      dependencies: [],
      maxRetries: 2
    },
    {
      id: "task-2",
      title: "Generate tests",
      prompt: "Generate tests for User model",
      agentRoleDescription: "Test generator",
      recommendedModel: "gpt-4o",
      parallelGroup: 2,
      dependencies: ["task-1"],
      maxRetries: 2
    }
  ]
})
```

**Returns**: `execution_id` for tracking

---

### 14. get_execution_status

Get workflow execution status.

```javascript
get_execution_status({
  execution_id: "exec-123"
})
```

**Returns**:

```json
{
  "status": "running",
  "progress": {
    "completed": 1,
    "total": 2,
    "current": "task-2"
  }
}
```

---

### 15. get_execution_results

Get workflow execution results.

```javascript
get_execution_results({
  execution_id: "exec-123"
})
```

**Returns**: All task outputs and deliverables

---

### 16. cancel_execution

Cancel running workflow.

```javascript
cancel_execution({
  execution_id: "exec-123"
})
```

---

## Context Tools

### 17. get_task_context

Get filtered task context for agents.

```javascript
get_task_context({
  taskId: "task-123",
  agentType: "worker"  // "pm", "worker", or "qc"
})
```

**Agent Types**:

- `pm`: Full context (100%)
- `worker`: Minimal context (<10% - files, dependencies, requirements only)
- `qc`: Requirements + worker output for verification

---

## Error Handling

### Common Error Responses

```json
{
  "error": "Entity not found",
  "code": "NOT_FOUND",
  "details": {
    "entity_id": "memory-123"
  }
}
```

### Error Codes

| Code | Description | Action |
|------|-------------|--------|
| `NOT_FOUND` | Entity not found | Verify entity ID |
| `INVALID_OPERATION` | Invalid operation | Check operation name |
| `MISSING_PARAMETER` | Required parameter missing | Add required parameter |
| `CONFIRMATION_REQUIRED` | Destructive operation needs confirmation | Use confirmationId |
| `LOCK_FAILED` | Failed to acquire lock | Retry or check lock status |

---

## Rate Limiting

Mimir implements rate limiting for API requests:

- **Default**: 100 requests per minute
- **Burst**: 200 requests per minute
- **Headers**: `X-RateLimit-Limit`, `X-RateLimit-Remaining`

---

## Related Documentation

- **[04-MCP-INTEGRATION.md](04-MCP-INTEGRATION.md)** - Kiro IDE integration
- **[07-NEO4J-GUIDE.md](07-NEO4J-GUIDE.md)** - Knowledge graph queries
- **[08-EMBEDDINGS.md](08-EMBEDDINGS.md)** - Vector embeddings
- **[09-WORKFLOWS.md](09-WORKFLOWS.md)** - Workflow orchestration

---

**Last Updated**: 2025-12-05  
**Mimir Version**: 4.1.0  
**Total Tools**: 17
