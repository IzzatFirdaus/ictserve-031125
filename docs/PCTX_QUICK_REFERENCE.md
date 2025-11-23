# PCTX + Mimir Quick Reference

**Last Updated:** 2025-11-22  
**Status:** Ready for use  
**Token Savings:** 80-98% vs. sequential MCP calls

---

## One-Command Startup

```powershell
# Development (foreground with logs)
.\scripts\start-pctx-stack.ps1

# CI/CD (background, quick exit)
.\scripts\start-pctx-stack.ps1 -Detached

# Custom wait time
.\scripts\start-pctx-stack.ps1 -MaxWaitSeconds 300
```

---

## Service Endpoints

| Service | Endpoint | Purpose |
|---------|----------|---------|
| **Mimir Direct** | <http://localhost:9042/mcp> | Neo4j graph, RAG, vector search |
| **PCTX Proxy** | <http://127.0.0.1:8080/mcp> | **← Use this in VS Code** |
| **Neo4j** | bolt://localhost:7687 | Graph database |
| **Neo4j Browser** | <http://localhost:7474> | Visual graph explorer |
| **Copilot API** | <http://localhost:4141> | LLM backend |

---

## Health Checks

```powershell
# Mimir
curl http://localhost:9042/health
# Expected: {"status":"ok"}

# PCTX (after starting)
curl http://127.0.0.1:8080/health
# Expected: {"status":"ready","servers":{"mimir":"connected"}}

# All services via Docker
docker-compose ps
# Expected: 3-4 healthy containers
```

---

## VS Code Configuration

```json
{
  "mcpServers": {
    "pctx-mimir": {
      "command": "node",
      "args": [".\\Mimir\\scripts\\mcp-http-client.js", "http://127.0.0.1:8080/mcp"],
      "type": "stdio"
    }
  }
}
```

Then in VS Code chat: `@pctx-mimir`

---

## Code Mode Examples

### Example 1: Search + Update

```typescript
const tasks = await mimir.todo({
  operation: "list",
  filters: { status: "pending" }
});

for (const task of tasks) {
  await mimir.memory_node({
    operation: "update",
    id: task.id,
    properties: { status: "in_progress" }
  });
}

console.log(`Updated ${tasks.length} tasks`);
```

### Example 2: Graph Traversal

```typescript
const findings = await mimir.vector_search_nodes({
  query: "authentication",
  limit: 5
});

for (const item of findings) {
  const related = await mimir.memory_edge({
    operation: "neighbors",
    node_id: item.id,
    depth: 2
  });
  console.log(`${item.name} → ${related.length} neighbors`);
}
```

### Example 3: Batch Operations

```typescript
const results = await mimir.memory_batch({
  operations: [
    { type: "memory_node", operation: "add", properties: {...} },
    { type: "memory_node", operation: "add", properties: {...} },
    { type: "memory_edge", operation: "add", source: "...", target: "..." }
  ]
});

console.log(`Created ${results.length} items`);
```

---

## Token Savings Comparison

| Workflow | Sequential | Code Mode | Savings |
|----------|-----------|-----------|---------|
| Simple (3 ops) | 1,500 tokens | 300 tokens | **80%** |
| Medium (10 ops) | 4,000 tokens | 500 tokens | **87%** |
| Batch (100 ops) | 8,000+ tokens | 600 tokens | **93%** |
| Complex graph | 5,000+ tokens | 800 tokens | **85%** |

---

## Troubleshooting

### PCTX Won't Start

```powershell
# Check prerequisites
pctx --version
docker ps | Select-String mimir
Invoke-RestMethod http://localhost:9042/health

# View logs
cat logs/pctx-*.log

# Restart
.\scripts\start-pctx-stack.ps1 -Services "mimir,pctx"
```

### Health Check Fails

```powershell
# Mimir not ready
docker-compose up -d mimir_server neo4j_db
Start-Sleep -Seconds 15

# Check Neo4j
docker logs neo4j_db --tail 20

# Restart all
docker-compose down
docker-compose up -d
.\scripts\start-pctx-stack.ps1
```

### Type Errors in Code Mode

```typescript
// ❌ Wrong: mimir_search_nodes
// ✅ Right: mimir.vector_search_nodes

// ❌ Wrong: property_name
// ✅ Right: properties.property_name

// ❌ Wrong: await missing
// ✅ Right: const result = await mimir.foo()
```

### Port Already in Use

```powershell
# Find process on port 8080
netstat -ano | findstr :8080

# Kill it
Stop-Process -Id <PID> -Force

# Or change PCTX port in pctx.json: "port": 8081
```

---

## Available Mimir Functions (from PCTX)

```typescript
// Memory Graph
mimir.memory_node({operation, id, type, properties})
mimir.memory_edge({operation, source, target, type})
mimir.memory_batch({operations})
mimir.memory_clear()

// Search
mimir.vector_search_nodes({query, types, limit})
mimir.memory_node({operation: "search", query})

// File Indexing
mimir.index_folder({path, watch})
mimir.remove_folder({path})
mimir.list_folders()

// Tasks
mimir.todo({operation, filters, id, title, properties})

// Context
mimir.get_task_context({role, filters})
```

---

## Logs Location

- **PCTX Startup:** `logs/pctx-stack-*.log`
- **PCTX Stdout:** `logs/pctx-stdout.log` (detached mode)
- **PCTX Stderr:** `logs/pctx-stderr.log` (detached mode)
- **Docker:** `docker-compose logs [service]`
- **Neo4j:** `docker-compose logs neo4j_db`
- **Mimir:** `docker-compose logs mimir_server`

---

## Documentation

| Document | Purpose |
|----------|---------|
| `docs/PCTX_INTEGRATION_SETUP.md` | Full setup guide + configuration |
| `docs/PCTX_CODE_MODE_EXAMPLES.md` | Real-world workflow examples |
| `docs/GITHUB_MCP_SETUP.md` | GitHub MCP integration |
| `mimir.md` | Mimir memory system guide |
| `.vscode/mcp.json` | VS Code MCP configuration |

---

## Key Concepts

**PCTX:** Proxy server enabling "Code Mode" (write TypeScript instead of sequential tool calls)

**Code Mode:** AI agents write typed, sandboxed TypeScript code → Deno executes → returns JSON result

**Savings:** 80-98% token reduction for complex workflows (no LLM thinking between tool calls)

**Workflow:** AI agent writes code block once → Deno executes with type checking → agent receives structured result

---

## Quick Start (5 Minutes)

```powershell
# 1. Ensure Mimir running (wait 10-15s for Neo4j startup)
docker-compose up -d mimir_server neo4j_db copilot_api_server

# 2. Verify health
Invoke-RestMethod http://localhost:9042/health

# 3. Start PCTX
pctx init                                   # (if first time)
.\scripts\start-pctx-stack.ps1 -Detached   # Start in background

# 4. Check PCTX ready
Start-Sleep -Seconds 5
Invoke-RestMethod http://127.0.0.1:8080/health

# 5. Use in VS Code
# → Open VS Code chat
# → Select: @pctx-mimir
# → Write Code Mode script (example above)
```

---

## Common Patterns

**Get all pending tasks:**

```typescript
const tasks = await mimir.todo({
  operation: "list",
  filters: { status: "pending" }
});
```

**Search for pattern:**

```typescript
const results = await mimir.vector_search_nodes({
  query: "your search",
  types: ["coding_pattern", "implementation"],
  limit: 10
});
```

**Update multiple items:**

```typescript
for (const item of items) {
  await mimir.memory_node({
    operation: "update",
    id: item.id,
    properties: { status: "done", updated_at: Date.now() }
  });
}
```

**Create relationship:**

```typescript
await mimir.memory_edge({
  operation: "add",
  source: "node-1",
  target: "node-2",
  type: "related_to"
});
```

---

## Team Checklist

- [ ] Docker Desktop running
- [ ] PCTX installed (`pctx --version`)
- [ ] Mimir services healthy
- [ ] PCTX server running (`http://127.0.0.1:8080/health`)
- [ ] VS Code `.vscode/mcp.json` updated to use PCTX
- [ ] Test Code Mode execution in VS Code chat
- [ ] Document team usage patterns
- [ ] Monitor token savings vs. baseline

---

**Questions?** See full guides in `docs/` or check Mimir logs.
