# 🚀 Quick Start: Mimir Memory for ICTServe Development

**TL;DR**: Neo4j is running, Mimir is connected, memory system is ready to use.

---

## 🎯 What Just Happened

✅ **Neo4j is working with Mimir for ICTServe repository**

- All 3 Docker services healthy (Neo4j, Mimir, Copilot API)
- 281 nodes in graph (280 indexed docs + 1 test memory)
- Memory create/read/update/query all verified and working
- Ready for AI agents to store and retrieve project knowledge

---

## 🏃 Quick Commands

### Check Everything is Running

```bash
# From root directory (use 'npm run' to avoid conflicts with built-in npm status)
npm run status

# Or manually
docker compose -f Mimir/docker-compose.yml ps
# Expected: all 3 containers "Up (healthy)"
```

### Access Mimir Portal (Web UI)

```
http://localhost:9042/portal
# Use this to view/manage memory visually
```

### Access Neo4j Browser (Database UI)

```
http://localhost:7474
Username: neo4j
Password: MxXhTKH3qntipYLa1e0QOluJ
```

### Check Mimir Health

```bash
curl http://localhost:9042/health
# Expected: {"status":"healthy","version":"4.1.0","tools":17}
```

### Query Memory (Neo4j)

```bash
# View all memory nodes
docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ \
  "MATCH (m:Memory) RETURN m.id, m.title, m.created_at LIMIT 10;"

# Search by tag
docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ \
  "MATCH (m:Memory) WHERE 'integration' IN m.tags RETURN m.title;"
```

---

## 📋 Next Step: Index Your Code

To let Mimir understand your ICTServe codebase:

```bash
# From root directory (easiest way)
npm run mimir:index:add

# Or from Mimir directory
cd Mimir && npm run index:add /workspace

# This takes 5-10 minutes and creates searchable nodes for all code files
```

Or use the Mimir Portal UI:

1. Go to <http://localhost:9042/portal>
2. Click "Index Folder"
3. Enter path: `/workspace`
4. Wait for completion (watch progress in logs)

---

## 🎮 All Available Commands

**From root directory:**

```bash
npm run mimir:start         # Start Mimir services
npm run mimir:stop          # Stop services
npm run mimir:status        # Show service status
npm run mimir:restart       # Restart services
npm run mimir:logs          # View logs
npm run mimir:rebuild       # Full rebuild (no cache)
npm run mimir:help          # Show all options

# Indexing commands
npm run mimir:index:add     # Index code folder
npm run mimir:index:list    # List indexed folders
npm run mimir:index:remove  # Remove indexed folder
```

---

## 💾 Store Memory About Your Project

Example: Add project knowledge to Neo4j (directly via Cypher):

```cypher
// Store project overview
CREATE (p:Node:Memory {
  id: 'ictserve-overview-001',
  title: 'ICTServe Project Overview',
  content: 'Laravel 12 BPM system for MOTAC. Guest submission flow, dual approval email system, dashboard reports...',
  tags: ['project', 'overview'],
  project_id: 'IzzatFirdaus/ictserve-031125',
  created_at: datetime()
});

// Store architecture decision
CREATE (a:Node:Memory {
  id: 'ictserve-arch-001',
  title: 'Architecture: MVC + SDUI + Livewire',
  content: 'Primary pattern: Model-View-Controller with Server-Driven UI components. Filament for admin, Livewire for reactive pages, Livewire Volt for single-file components...',
  tags: ['architecture', 'design'],
  project_id: 'IzzatFirdaus/ictserve-031125',
  created_at: datetime()
});
```

Then use the Mimir Portal or API to query this memory.

---

## 🔗 Connect Memory to Code

Link memories to indexed files:

```cypher
// Find a memory and a file
MATCH (m:Memory {id: 'ictserve-overview-001'}), (f:File)
WHERE f.path CONTAINS 'app/Models'
LIMIT 1

// Create relationship
CREATE (m)-[r:RELATES_TO {type: 'implements'}]->(f)
RETURN m.title, f.path;
```

Now AI agents can traverse from memory → code → related memories.

---

## 🤖 Using in AI Agent Workflows

Once indexed, AI agents can:

```javascript
// Search for related project knowledge
vector_search_nodes(
  query: "authentication system implementation",
  type: "Memory",
  project_id: "IzzatFirdaus/ictserve-031125",
  limit: 5
)
// Returns: Memory nodes related to auth implementation

// Search code context
vector_search_nodes(
  query: "User model relationships and casting",
  type: "File",
  limit: 10
)
// Returns: File nodes related to User model code

// Traverse knowledge graph
memory_edge(
  operation: "neighbors",
  node_id: "some-memory-node-id",
  depth: 2
)
// Returns: All connected knowledge (memory → code → architecture → etc)
```

---

## 📊 Status Dashboard

| Component | Status | URL | Details |
|-----------|--------|-----|---------|
| Neo4j Database | ✅ Running | bolt://localhost:7687 | 281 nodes, full read/write |
| Neo4j Browser | ✅ Running | <http://localhost:7474> | Web UI for queries |
| Mimir Server | ✅ Running | <http://localhost:9042> | HTTP API + Portal |
| Mimir Portal | ✅ Running | <http://localhost:9042/portal> | Memory management UI |
| Copilot API | ✅ Running | <http://localhost:4141> | LLM provider (GPT-4.1) |
| File Indexing | ✅ Ready | - | Can index any folder |
| Embeddings | ⏸️ Disabled | - | Optional: requires Ollama |

---

## 🐛 Troubleshooting

**Q: Services not showing healthy?**

```bash
docker compose -f Mimir/docker-compose.yml restart
docker logs mimir_server --tail 50
```

**Q: Can't connect to Neo4j Browser?**

```bash
# Check if port is open
curl http://localhost:7474
# Should return HTML response
```

**Q: Memory operations failing?**

```bash
# Test Neo4j directly
docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ \
  "RETURN 1 as test;"
```

**Q: Mimir not connecting to Neo4j?**

```bash
# Check logs for connection errors
docker logs mimir_server | grep -i "connection\|error"
```

---

## 📚 Full Documentation

- **Detailed Guide**: See `MIMIR_INTEGRATION_COMPLETE.md`
- **Verification Report**: See `MIMIR_NEO4J_VERIFICATION.md`
- **Project Memory**: See `mimir.md`
- **Agent Memory Rules**: See `.github/instructions/memory.instructions.md`

---

## 🎓 Key Concepts

**Nodes** = Facts/knowledge items (e.g., "ICTServe is a Laravel app")  
**Edges** = Relationships (e.g., "Memory relates to Code")  
**Memory** = Persistent knowledge graph, survives across sessions  
**Semantic Search** = Find knowledge by meaning, not just keywords  
**Project ID** = Tag memories with `IzzatFirdaus/ictserve-031125` for filtering

---

## ✨ What's Working

✅ Neo4j accepting connections from Mimir  
✅ Creating memory nodes in database  
✅ Retrieving memory nodes from database  
✅ Creating relationships between nodes  
✅ Querying relationships  
✅ File indexing system  
✅ HTTP API responding  
✅ Web Portal accessible  
✅ All Docker containers healthy  

---

## 🚀 You're Ready

Next: Run `npm run index:add /workspace` to index ICTServe code, then memory will be complete.

Questions? Check the detailed guides or Neo4j/Mimir logs.

**Happy coding! 🎉**
