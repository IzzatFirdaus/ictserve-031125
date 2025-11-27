# Mimir Memory System Integration (ICTServe)

Status: ⚠️ **OPTIONAL STACK (root compose disabled)** — Copilot embeddings enabled in `Mimir/.env` (see MIMIR_SETUP.md)

Note: On 2025-11-22 the repository's Mimir-related markdown files were consolidated into `docs/mimir/` and their contents were stored as Memory nodes in the project's Mimir/Neo4j graph (ids: `mimir_quick_start`, `mimir_setup`, `mimir_npm_commands`, `mimir_neo4j_verification`, `mimir_neo4j_complete`, `mimir_integration_complete`, `mimir_setup_complete`, `mimir_docs_index`, `issue_resolution_2025_11_22`, `openmemory_deprecated`, `mimir_md_guide`). The legacy `openmemory.md` was removed as deprecated and archived in memory.

**IMPORTANT**: Mimir is currently **disabled** in the main docker-compose.yml to keep the Laravel stack lightweight. When you start the dedicated `Mimir/docker-compose.yml`, Copilot embeddings are enabled by default for semantic search.

**To enable Mimir**, see the comprehensive setup guide in `MIMIR_SETUP.md`.

This document provides a single, consolidated technical reference for Mimir integration. The legacy `openmemory.md` is retained only as a stub for backward compatibility.

---
## 1. Overview
Mimir provides a persistent Neo4j-backed knowledge graph + optional vector embeddings for:

- Memory nodes (tasks, concepts, documents, decisions)
- Edges (relationships: part_of, implements, uses, documents, resolves, etc.)
- Vector semantic search (embeddings) across indexed code & stored nodes
- Multi-agent coordination (locks + parallel task orchestration)

### Core URLs (local Docker defaults)

- Mimir Web UI / APIs: `http://localhost:9042`
- Neo4j Browser: `http://localhost:7474` (user: neo4j, pass: password)
- Copilot API (LLM): `http://localhost:4141`

---
## 2. Docker Services Added
Defined in `docker-compose.yml`:

- `neo4j_db` (Neo4j 5.15 community)
- `copilot_api_server` (OpenAI-compatible proxy for GitHub Copilot license)
- `mimir_server` (build from project Dockerfile, exposes port 9042 -> container 3000)

### Health Checks
Ensure all three services show **healthy** before attempting agent graph operations:

```powershell
docker compose ps
curl http://localhost:9042/health
curl http://localhost:7474
```

---
## 3. Environment Variables (Minimum Required)
Set (e.g. in a new `.env.mimir` then extend compose or export locally):

```
NEO4J_PASSWORD=password
MIMIR_DEFAULT_PROVIDER=openai
MIMIR_LLM_API=http://copilot-api:4141
MIMIR_DEFAULT_MODEL=gpt-4.1
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_MODEL=mxbai-embed-large
MIMIR_AUTO_INDEX_DOCS=true
```

Optional overrides already scaffolded in compose for embeddings / models.

---
## 4. Agent Migration Rules

1. Replace all references to **OpenMemory** with **Mimir** (done in `AGENTS.md`, legacy stub kept in `openmemory.md`).
2. Use Mimir tools instead of custom memory logic:
   - `memory_node` (create/read/update nodes)
   - `memory_edge` (create relationships)
   - `vector_search_nodes` (semantic search)
   - `todo` / `todo_list` (task management)
3. For bulk operations prefer `memory_batch`.
4. For coordination use `memory_lock` when multiple agents might mutate related nodes.
5. Do NOT append large narrative blocks to this file—persist durable knowledge as graph nodes + observations.

---
## 4b. Local MCP Client Config (Codex)

- `.mcp.json` now points to `Mimir/scripts/mcp-http-client.cjs` (CommonJS bridge). The prior `.js` path was removed; using `.cjs` prevents missing-command errors.
- Command used: `node Mimir/scripts/mcp-http-client.cjs http://localhost:9042/mcp` with `MIMIR_API_URL=http://localhost:9042`.
- If MCP tool calls return 406/Not Acceptable, verify the `.cjs` path and that the Mimir stack is running (`npm run status` in `Mimir/`).

---
## 5. Indexing ICTServe Codebase
Once Mimir server is running:

```powershell
# Index entire workspace (recommended from host path)
npm run index:add $PWD
# List indexed folders
npm run index:list
```

If path resolution issues inside Docker occur, fallback to internal container path `/workspace`.

Embedding model defaults to `mxbai-embed-large`; adjust via `MIMIR_EMBEDDINGS_MODEL`.

---
## 6. Recommended Node Types Mapping

| ICTServe Artifact | Mimir Node Type | Example Name |
| ----------------- | --------------- | ------------ |
| Requirement (D03) | document        | D03_FR_012 |
| Design Decision   | decision        | Cache_Strategy_Redis_vs_AppMemory_2025_11_06 |
| Implementation    | implementation  | Email_Notification_System |
| Pattern / Guideline| pattern        | Livewire_3_Component_Patterns |
| Bug Fix           | fix             | CORS_Error_Resolution_2025_11_06 |
| Work Session      | session         | Session_2025_11_22_MimirMigration |

(If stock Mimir types differ, map accordingly; adjust naming in first large batch import.)

---
## 7. Initial Migration Work Session Node
Create a session node capturing this migration (via Mimir tool call):

```
operation: create (memory_node)
properties:
  type: session
  title: "Mimir Migration Phase 1"
  summary: "Added docker services, updated docs, replaced OpenMemory references."
  date: 2025-11-22
```

Then link previous memory standard node (`KnowledgeBase_Specification_2025-11-15`).

---
## 8. Next Steps

1. Add `.env.mimir` and optionally wire it into compose with `env_file` for `mimir-server`.
2. Expose internal workspace path for indexing if needed (ensure read-only mount uses correct path for large scans).
3. Create initial nodes for high-value artifacts (requirements, design docs, key patterns) rather than relying on markdown duplication.
4. Configure VSCode extension settings (user/workspace settings):

```json
{
  "mimir.apiUrl": "http://localhost:9042",
  "mimir.model": "gpt-4.1",
  "mimir.defaultPreamble": "mimir-v2",
  "mimir.vectorSearch.depth": 1,
  "mimir.vectorSearch.limit": 10,
  "mimir.vectorSearch.minSimilarity": 0.5,
  "mimir.enableTools": true,
  "mimir.maxToolCalls": 3
}
```

5. Add automated indexing step (optional) in CI nightly build (not yet configured).
6. Keep the `config/app.php` entry for `min_approver_grade_level` in sync with the `MIN_APPROVER_GRADE_LEVEL` env var so Grade 41+ approval logic can be tuned per deployment.
7. Standardize Livewire/trailing service pagination helpers to `Illuminate\Pagination\LengthAwarePaginator` so PHPStan and IDE inspections recognise `links()` and other helper methods.

---
## 9. Decommission Tasks (OpenMemory)

| Task | Status |
| ---- | ------ |
| Rename references in instructions | COMPLETE |
| Stub legacy file `openmemory.md` | COMPLETE |
| Update AGENTS.md | COMPLETE |
| Update memory.jsonl index reference | COMPLETE |
| Add docker services | COMPLETE |
| Create integration doc (`mimir.md`) | COMPLETE |
| Create migration session node | PENDING (tool invocation) |

---
## 10. Validation Checklist

- [x] Docs reference Mimir not OpenMemory.
- [x] Docker services added (neo4j, copilot-api, mimir-server).
- [ ] Mimir server built & running locally.
- [ ] Neo4j reachable at 7474/7687.
- [ ] Initial indexing executed.
- [ ] Session node created documenting migration.

---
## 11. Larastan Remediation Notes (2025-11-24)

- Generated `phpstan-models.json` (models-only run) to triage Larastan Level 9 errors; full project JSON run still timing out locally.
- Relationship methods now normalize return generics using typed local variables (e.g., `/** @var BelongsTo<Related, self> $relation */`) to satisfy PHPStan's covariant model template expectations.
- User notification preferences are now normalized to `array<string, bool>` with boolean casting and guarded defaults via `getNotificationPreferences()` / `setNotificationPreferences()`.
- Scope methods adopt typed `Builder<self>` hints and docblocks to avoid missing generic type warnings.

---
## 12. OTP Handover Service Notes (2025-11-24)

- OTP generation/validation now centralizes logging context with application id/number and guards for missing/expired/locked OTPs before processing.
- Secure OTP generation now imports `random_int` explicitly and wraps generation/persistence errors, rethrowing as runtime exceptions after logging.
- Return receipt helper escapes dynamic fields (application number, applicant name, ISO id, date) to reduce XSS surface while keeping HTML output for PDF/export steps.

---
Note: This file consolidates the existing top-level `mimir.md` guidance into the canonical docs location: `docs/mimir/mimir.md`.
