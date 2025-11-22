# Deprecated: OpenMemory Guide

This file is retained solely for backward compatibility. The ICTServe project migrated to the **Mimir Memory System (Neo4j graph + embeddings)** on Nov 22, 2025.

## Current Source of Truth

- **Active memory / knowledge graph operations**: see `mimir.md`
- **Neo4j + Mimir services**: defined in `docker-compose.yml`
- **Legacy references**: Update `openmemory.md` references to `mimir.md` in new code.

## Migration Summary

| Item | Status |
| ---- | ------ |
| Graph backend | Neo4j (docker service `neo4j_db`) |
| MCP server | Mimir (`mimir_server` on port 9042) |
| LLM provider | Copilot API (docker service `copilot_api_server`) |
| Legacy OpenMemory | **Replaced** by Mimir node/edge tools |

## Action Required

1. Use Mimir tools (`memory_node`, `memory_edge`, `vector_search_nodes`).
2. Do not add new content here.
3. See `mimir.md` for all documentation.

---
Last updated: 2025-11-22 (migration stub)
