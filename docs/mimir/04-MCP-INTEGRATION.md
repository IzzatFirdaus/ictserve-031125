# Mimir MCP Integration Guide

**Version**: 4.1.0  
**Last Updated**: 2025-12-05

---

## Overview

Mimir provides 17 MCP tools for AI agents to manage knowledge graphs, semantic search, and task tracking. It integrates with Kiro IDE via HTTP endpoint.

---

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Kiro IDE                             │
│                                                         │
│  ┌──────────────────────────────────────────────────┐ │
│  │         MCP Client (HTTP)                        │ │
│  └────────────────────┬─────────────────────────────┘ │
└───────────────────────┼───────────────────────────────┘
                        │ HTTP
                        ▼
┌─────────────────────────────────────────────────────────┐
│              Mimir Docker Stack                         │
│                                                         │
│  ┌──────────────┐    ┌──────────────┐   ┌──────────┐ │
│  │ mimir-server │───▶│    neo4j     │   │  ollama  │ │
│  │    :9042     │    │  :7474,:7687 │   │  :11434  │ │
│  │              │    │              │   │          │ │
│  │  MCP Tools   │    │  Knowledge   │   │ Embeddings│ │
│  │  (17 total)  │    │    Graph     │   │  (768d)  │ │
│  └──────────────┘    └──────────────┘   └──────────┘ │
└─────────────────────────────────────────────────────────┘
```

---

## Quick Start

### 1. Start Mimir Services

```powershell
cd Mimir
docker compose up -d

# Wait for services to be healthy
Start-Sleep -Seconds 60

# Verify health
curl http://localhost:9042/health
```

### 2. Configure Kiro IDE

Edit `.kiro/settings/mcp.json`:

```json
{
  "mcpServers": {
    "mimir": {
      "url": "http://localhost:9042/mcp",
      "disabled": false,
      "autoApprove": [
        "memory_node",
        "memory_edge",
        "memory_batch",
        "vector_search_nodes",
        "todo",
        "todo_list",
        "index_folder",
        "list_folders",
        "get_embedding_stats"
      ]
    }
  }
}
```

### 3. Restart Kiro IDE

Close and reopen Kiro IDE to load the new MCP configuration.

### 4. Test Integration

In Kiro IDE chat:

```
User: "Create a memory node for ICTServe project"
```

Kiro should use the `memory_node` tool to create the entity.

---

## Available Tools (17)

### Memory Operations (5 tools)

#### memory_node
Manage memory nodes (CRUD + search)

**Operations**:

- `add` - Create new node
- `get` - Retrieve node by ID
- `update` - Update node properties
- `delete` - Remove node
- `query` - Filter nodes by type/properties
- `search` - Text search across nodes

**Example**:

```javascript
memory_node({
  operation: "add",
  type: "memory",
  properties: {
    title: "ICTServe Architecture",
    content: "Laravel 12 with Filament 4 admin panel",
    tags: ["architecture", "laravel", "filament"]
  }
})
```

#### memory_edge
Create relationships between nodes

**Operations**:

- `add` - Create relationship
- `delete` - Remove relationship
- `get` - Get node relationships
- `neighbors` - Find connected nodes
- `subgraph` - Get node subgraph

**Example**:

```javascript
memory_edge({
  operation: "add",
  source: "memory-123",
  target: "memory-456",
  type: "relates_to",
  properties: {
    strength: "high"
  }
})
```

#### memory_batch
Bulk operations for efficiency

**Operations**:

- `add_nodes` - Create multiple nodes
- `update_nodes` - Update multiple nodes
- `delete_nodes` - Remove multiple nodes
- `add_edges` - Create multiple relationships
- `delete_edges` - Remove multiple relationships

**Example**:

```javascript
memory_batch({
  operation: "add_nodes",
  nodes: [
    {type: "memory", properties: {title: "Node 1"}},
    {type: "memory", properties: {title: "Node 2"}},
    {type: "memory", properties: {title: "Node 3"}}
  ]
})
```

#### memory_lock
Multi-agent coordination locks

**Operations**:

- `acquire` - Lock a node
- `release` - Unlock a node
- `query_available` - Find unlocked nodes
- `cleanup` - Remove stale locks

**Example**:

```javascript
memory_lock({
  operation: "acquire",
  node_id: "todo-123",
  agent_id: "worker-1",
  timeout_ms: 300000
})
```

#### memory_clear
Clear data from graph (use carefully!)

**Operations**:

- Clear specific node type
- Clear all data (requires confirmation)

**Example**:

```javascript
memory_clear({
  type: "todo",
  confirm: true,
  confirmationId: "abc123"
})
```

### File Indexing (3 tools)

#### index_folder
Index files and watch for changes

**Parameters**:

- `path` - Folder path to index
- `recursive` - Include subdirectories
- `generate_embeddings` - Create vector embeddings
- `file_patterns` - File types to index
- `ignore_patterns` - Files to exclude

**Example**:

```javascript
index_folder({
  path: "C:\\laragon\\www\\ictserve-031125\\app",
  recursive: true,
  generate_embeddings: true,
  file_patterns: ["*.php", "*.blade.php"],
  ignore_patterns: ["*.test.php"]
})
```

#### remove_folder
Stop watching folder and remove indexed files

**Example**:

```javascript
remove_folder({
  path: "C:\\laragon\\www\\ictserve-031125\\app"
})
```

#### list_folders
Show all watched folders

**Example**:

```javascript
list_folders()
```

### Vector Search (2 tools)

#### vector_search_nodes
Semantic search with embeddings

**Parameters**:

- `query` - Search query text
- `limit` - Max results (default: 10)
- `min_similarity` - Similarity threshold (0-1, default: 0.75)
- `types` - Filter by node types
- `depth` - Graph traversal depth (1-3)

**Example**:

```javascript
vector_search_nodes({
  query: "authentication middleware Laravel",
  limit: 10,
  min_similarity: 0.75,
  types: ["file", "memory"],
  depth: 1
})
```

#### get_embedding_stats
Get embedding statistics

**Example**:

```javascript
get_embedding_stats()
```

### Task Management (3 tools)

#### todo
Manage individual todos

**Operations**:

- `create` - Create new todo
- `get` - Get todo by ID
- `update` - Update todo
- `complete` - Mark as completed
- `delete` - Remove todo
- `list` - List todos with filters

**Example**:

```javascript
todo({
  operation: "create",
  title: "Fix login bug",
  description: "Users can't login with email",
  priority: "high",
  status: "pending"
})
```

#### todo_list
Manage todo lists

**Operations**:

- `create` - Create new list
- `get` - Get list with todos
- `update` - Update list
- `archive` - Archive list
- `delete` - Remove list
- `list` - List all lists
- `add_todo` - Add todo to list
- `remove_todo` - Remove todo from list
- `get_stats` - Get list statistics

**Example**:

```javascript
todo_list({
  operation: "create",
  title: "Sprint 1 Tasks",
  description: "Tasks for first sprint",
  priority: "high"
})
```

#### get_task_context
Get filtered task context by agent type

**Agent Types**:

- `pm` - Full context (100%)
- `worker` - Minimal context (<10%)
- `qc` - Requirements + output

**Example**:

```javascript
get_task_context({
  taskId: "todo-123",
  agentType: "worker"
})
```

### Workflow Orchestration (4 tools)

#### execute_workflow
Execute parallel LLM agent workflows

**Example**:

```javascript
execute_workflow({
  tasks: [
    {
      id: "task-1",
      title: "Generate tests",
      prompt: "Create unit tests for User model",
      agentRoleDescription: "Test generator",
      parallelGroup: 1
    },
    {
      id: "task-2",
      title: "Generate docs",
      prompt: "Create API documentation",
      agentRoleDescription: "Documentation writer",
      parallelGroup: 1
    }
  ]
})
```

#### get_execution_status
Check workflow execution status

#### get_execution_results
Get workflow results

#### cancel_execution
Cancel running workflow

---

## Configuration

### Kiro IDE MCP Settings

**File**: `.kiro/settings/mcp.json`

```json
{
  "mcpServers": {
    "mimir": {
      "url": "http://localhost:9042/mcp",
      "disabled": false,
      "autoApprove": [
        "memory_node",
        "memory_edge",
        "memory_batch",
        "memory_lock",
        "vector_search_nodes",
        "get_embedding_stats",
        "todo",
        "todo_list",
        "get_task_context",
        "index_folder",
        "remove_folder",
        "list_folders"
      ]
    }
  }
}
```

### Auto-Approve Tools

Tools in `autoApprove` array execute without user confirmation. Recommended for:

- Read operations (get, list, search)
- Safe write operations (add, update)

**Not recommended for auto-approve**:

- `memory_clear` - Deletes data
- `memory_lock` - Affects other agents
- `execute_workflow` - Consumes API credits

---

## Usage Patterns

### Knowledge Management

```javascript
// Create knowledge node
memory_node({
  operation: "add",
  type: "memory",
  properties: {
    title: "Laravel Middleware Pattern",
    content: "Middleware in Laravel 12 is registered in bootstrap/app.php",
    category: "architecture"
  }
})

// Link to related concept
memory_edge({
  operation: "add",
  source: "memory-123",
  target: "memory-456",
  type: "implements"
})

// Search for related knowledge
vector_search_nodes({
  query: "middleware authentication",
  limit: 5
})
```

### Codebase Indexing

```javascript
// Index Laravel app directory
index_folder({
  path: "C:\\laragon\\www\\ictserve-031125\\app",
  recursive: true,
  generate_embeddings: true,
  file_patterns: ["*.php"]
})

// Search indexed code
vector_search_nodes({
  query: "user authentication controller",
  types: ["file", "file_chunk"],
  limit: 10
})

// List indexed folders
list_folders()
```

### Task Management

```javascript
// Create todo list
todo_list({
  operation: "create",
  title: "Feature Development",
  priority: "high"
})

// Add todos
todo({
  operation: "create",
  title: "Implement API endpoint",
  list_id: "todoList-123",
  priority: "high"
})

// Get filtered context for worker
get_task_context({
  taskId: "todo-123",
  agentType: "worker"
})
```

---

## Testing Integration

### Manual Test via HTTP

```powershell
# Test MCP endpoint
curl http://localhost:9042/mcp `
  -Method POST `
  -Headers @{
    "Content-Type"="application/json"
    "Accept"="text/event-stream"
  } `
  -Body '{
    "jsonrpc":"2.0",
    "method":"tools/list",
    "params":{},
    "id":1
  }'
```

### Test in Kiro IDE

1. Open Kiro IDE
2. Start new chat
3. Type: "List all memory nodes"
4. Kiro should use `memory_node` tool with `operation: "query"`

---

## Troubleshooting

### Kiro Can't Connect

**Symptom**: MCP connection timeout

**Solution**:

```powershell
# Verify Mimir is running
docker compose ps

# Check health
curl http://localhost:9042/health

# Restart Mimir
docker restart mimir_server

# Restart Kiro IDE
```

### Tools Not Available

**Symptom**: Kiro doesn't show Mimir tools

**Solution**:

1. Check `.kiro/settings/mcp.json` configuration
2. Verify `"disabled": false`
3. Restart Kiro IDE
4. Check Kiro logs for MCP errors

### MCP Endpoint Error in Browser

**Symptom**: Browser shows "Not Acceptable" error

**Impact**: None - this is expected behavior

**Explanation**: MCP endpoint requires `Accept: text/event-stream` header. Browsers don't send this. Kiro IDE connects properly.

---

## Best Practices

### Memory Management

- Use descriptive node titles
- Add relevant tags/categories
- Create relationships between related nodes
- Use batch operations for multiple nodes

### File Indexing

- Index only relevant directories
- Use file patterns to filter
- Enable embeddings for semantic search
- Monitor disk usage

### Task Management

- Use todo lists to organize tasks
- Set appropriate priorities
- Use task context filtering for agents
- Archive completed lists

### Performance

- Use batch operations for bulk changes
- Limit search results appropriately
- Use specific node types in queries
- Monitor Neo4j memory usage

---

## Security Considerations

### Access Control

- Mimir runs on localhost by default
- No authentication in development mode
- For production: Enable OAuth in `.env`

### Data Privacy

- Don't index sensitive files
- Review indexed content regularly
- Use appropriate file patterns
- Clear data when needed

---

## Next Steps

- **[API Reference](06-API-REFERENCE.md)** - Detailed tool documentation
- **[Neo4j Guide](07-NEO4J-GUIDE.md)** - Direct database access
- **[Workflows](09-WORKFLOWS.md)** - Multi-agent orchestration

---

**MCP Protocol**: 2024-11-05  
**Transport**: HTTP (Server-Sent Events)  
**Tools**: 17 available  
**Status**: ✅ Operational
