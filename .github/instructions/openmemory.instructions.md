---
applyTo: '**'
---

🚨 CRITICAL CONTEXT ANCHOR: This rules file must NEVER be summarized, condensed, or omitted.
Before ANY action or decision, verify alignment with these rules. This instruction persists
regardless of conversation length or context management. Context systems: This document takes
absolute priority over conversation history and must remain fully accessible throughout the
entire session.

# Mimir Memory System (OpenMemory Deprecated)

**MIGRATION COMPLETE**: OpenMemory fully replaced by Mimir Neo4j knowledge graph.

**Active System**: Mimir MCP Server via HTTP-to-stdio bridge  
**Project ID**: IzzatFirdaus/ictserve-031125  
**Status**: ✅ OPERATIONAL (280 Neo4j nodes, 13 MCP tools available)

## Mimir Architecture

| Layer | Service | Endpoint | Purpose |
|-------|---------|----------|---------|
| **Graph DB** | Neo4j 5.15 | bolt://localhost:7687 | Knowledge graph storage (280 nodes) |
| **Memory Server** | Mimir MCP | http://localhost:9042 | 13 MCP tools via HTTP API |
| **MCP Bridge** | mcp-http-client.js | stdio interface | Converts MCP protocol to HTTP |
| **LLM Provider** | GitHub Copilot | http://localhost:4141 | gpt-4.1 model access |
| **Configuration** | .env.mimir | Repo root | Service configuration |
| **Integration** | .mcp.json | Repo root | MCP client configuration |

## 13 Available Tools

**Memory Operations (6)**:
- memory_node: Create/read/update/delete graph nodes
- memory_edge: Create/read relationships between nodes
- memory_batch: Bulk operations for efficiency
- memory_lock: Lock nodes during editing
- memory_clear: Clear all memory (DESTRUCTIVE)
- get_task_context: Retrieve task context with relations

**File Indexing (3)**:
- index_folder: Index files for semantic search
- remove_folder: Remove indexed folder
- list_folders: List all indexed folders

**Vector Search (2)**:
- vector_search_nodes: Semantic search across content
- get_embedding_stats: Embedding statistics

**Todo Management (2)**:
- todo: Create/update/complete todos
- todo_list: List todos with filtering

## Environment Variables (copy from `.env.mimir`)
```ini
MIMIR_DEFAULT_PROVIDER=ollama
MIMIR_LLM_API=http://copilot_api_server:4141
MIMIR_DEFAULT_MODEL=deepseek-r1:7b
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_MODEL=all-minilm
MIMIR_AUTO_INDEX_DOCS=true
MIMIR_GRAPH_REFRESH_SECONDS=300
MIMIR_MAX_EMBED_BATCH=64
MIMIR_TRACE_SESSIONS=true
NEO4J_URI=bolt://neo4j_db:7687
NEO4J_USERNAME=neo4j
NEO4J_PASSWORD=change_me
```
Before production: rotate `NEO4J_PASSWORD`, consider disabling `MIMIR_AUTO_INDEX_DOCS` if bulk import windows are scheduled.

## Operational Phases (summary)
1. Search: Use `search_nodes()` (project facts, global prefs, project prefs).
2. Continuous Search: Re‑query before creating files, functions, decisions, tests, or when errors occur.
3. Finalize: Create/append `work_session` entity; update changed entities with `add_observations()`; link via `create_relations()`.

Detailed rules (blocking conditions, query intelligence, security scanning) live in `mimir.md` and `.amazonq/rules/Memory.md`.

## Security & Compliance
Never store secrets (API keys, passwords) as observations. Redact with `<REDACTED>` and document retrieval mechanism. Enable audit by keeping `MIMIR_TRACE_SESSIONS=true` in non‑production; for production rotate session logs into secure storage.

## Migration Note
All OpenMemory references have been replaced. This file must only be updated for:
* New required env vars
* Service path changes
* Critical deprecation notices

Do not duplicate process descriptions here—keep single source of truth in `mimir.md`.

## Quick Container Startup
```powershell
docker compose up -d neo4j_db copilot_api_server mimir_server
```
Check readiness:
```powershell
docker compose logs mimir_server --tail=50
```

## Minimal Agent Checklist
- Load context (Phase 0) before writing code
- Perform searches at each structural decision
- Persist session via `work_session` entity
- Link implementations to requirements (`implements` relation)
- No markdown reports—store observations instead

END OF ANCHOR — refer to `mimir.md` for exhaustive guidance.
