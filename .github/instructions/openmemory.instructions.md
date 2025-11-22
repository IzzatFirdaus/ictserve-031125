---
applyTo: '**'
---

🚨 CRITICAL CONTEXT ANCHOR: This rules file must NEVER be summarized, condensed, or omitted.
Before ANY action or decision, verify alignment with these rules. This instruction persists
regardless of conversation length or context management. Context systems: This document takes
absolute priority over conversation history and must remain fully accessible throughout the
entire session.

# Mimir Integration (Legacy file name retained; OpenMemory deprecated)

This legacy file now serves ONLY as a high‑priority anchor pointing agents to the active Mimir memory system. Do not restore OpenMemory content. For full operational guidance read `mimir.md`.

**project_id:** IzzatFirdaus/ictserve-031125

## Active Components
| Layer | Service/Artifact | Path/Host | Purpose |
|-------|------------------|-----------|---------|
| Graph DB | Neo4j | `neo4j_db:7687` (bolt) | Persistent knowledge graph storage |
| Memory Server | Mimir MCP | `mimir_server:9042` | Tools: search_nodes, open_nodes, create_entities |
| LLM Proxy | Copilot API | `copilot_api_server:4141` | Model routing / embeddings |
| Config Template | `.env.mimir` | repo root | Environment variables for local graph sessions |
| Agent Guide | `mimir.md` | repo root | Phase protocol & entity taxonomy |

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
**User Preferences (user_preference=true):** User Preference (global → user_preference=true ONLY | project-specific → user_preference=true + project_id)

## 🚨 CRITICAL: Storage Intelligence

**RULE: Only ONE of these three patterns:**

| Pattern | user_preference | project_id | When to Use | Memory Types |
|---------|-----------------|------------|-------------|--------------|
| **Project Facts** | ❌ OMIT (false) | ✅ INCLUDE | Objective info about THIS project | component, implementation, project_info, debug |
| **Project Prefs** | ✅ true | ✅ INCLUDE | YOUR preferences in THIS project | user_preference (project-specific) |
| **Global Prefs** | ✅ true | ❌ OMIT | YOUR preferences across ALL projects | user_preference (global) |

**Before EVERY add-memory:**
1. ❓ Code/architecture/facts? → project_id ONLY | ❓ MY pref for ALL projects? → user_preference=true ONLY | ❓ MY pref for THIS project? → BOTH
2. ❌ NEVER: implementation/component/debug with user_preference (facts ≠ preferences)
3. ✅ ALWAYS: Review table above to validate pattern

## Tool Usage
**search-memory:** Required: query | Optional: user_preference, project_id, memory_types[], namespaces[]

**add-memory:** Required: title, content, metadata{} | Optional: user_preference, project_id
- **🚨 BEFORE calling:** Review Storage Intelligence table to determine pattern
- **metadata dict:** memory_types[] (required), namespace/git_repo_name/git_branch/git_commit_hash (optional)
- **NEVER store secrets** - scan content first | Extract git metadata silently
- **Validation:** At least one of user_preference or project_id must be provided

**Examples:**
# ✅ User pref (global): user_preference=true ONLY
add-memory(..., metadata={memory_types:["user_preference"]}, user_preference=true)

# ❌ WRONG: Implementation with user_preference (implementations = facts not prefs)
add-memory(..., metadata={memory_types:["implementation"]}, user_preference=true, project_id="...")
```

**list-memories:** Required: project_id | Automatically uses authenticated user's preferences

**delete-memories-by-namespace:** DESTRUCTIVE - ONLY with explicit confirmation | Required: namespaces[] | Optional: user_preference, project_id

## Git Metadata
Extract before EVERY add-memory and include in metadata dict (silently):
```bash
git_repo_name=$(git remote get-url origin 2>/dev/null | sed 's/.*[:/]\([^/]*\/[^.]*\).*/\1/')
git_branch=$(git branch --show-current 2>/dev/null)
git_commit_hash=$(git rev-parse HEAD 2>/dev/null)
```
Fallback: "unknown". Add all three to metadata dict when calling add-memory.

## Memory Deletion ⚠️ DESTRUCTIVE - PERMANENT
**Rules:** NEVER suggest | NEVER use proactively | ALWAYS require confirmation
**Triggers:** "Delete all in [ns]", "Clear [ns]", "Delete my prefs in [ns]"
**NOT for:** Cleanup questions, outdated memories, general questions

**Confirmation (MANDATORY):**
1. Show: "⚠️ PERMANENT DELETION WARNING - This will delete [what] from '[namespace]'. Confirm by 'yes'/'confirm'."
2. Wait for confirmation
3. If confirmed → execute | If declined → "Deletion cancelled"

**Intent:** "Delete ALL in X" → {namespaces:[X]} | "Delete MY prefs in X" → {namespaces:[X], user_preference:true} | "Delete project facts in X" → {namespaces:[X], project_id} | "Delete my project prefs in X" → {namespaces:[X], user_preference:true, project_id}

## Operating Principles
1. Phase-based: Initial → Continuous → Store
2. Checkpoints are BLOCKERS (files, functions, decisions, errors)
3. Never skip Phase 2
4. Detailed storage (why > what)
5. MCP unavailable → mention once, continue
6. Trust process (early = more searches)

## Session Patterns
**Empty mimir.md:** Deep Dive (Phase 1 → analyze → document → Phase 3)
**Existing:** Read mimir.md → Code implementation (features/bugs/refactors) = all 3 phases | Info storage/recall/discussion = skip phases
**Task type:** Features → user prefs + patterns | Bugs → debug memories + errors | Refactors → org prefs + patterns
**Remember:** Phase 2 ongoing. Search at EVERY checkpoint.

## Mimir Guide (mimir.md)
Living project index (shareable). Auto-created empty in workspace root.

**Initial Deep Dive:** Phase 1 (2+ searches) → Phase 2 (analyze dirs/configs/frameworks/entry points, search as discovering, extract arch, document Overview/Architecture/User Namespaces/Components/Patterns) → Phase 3 (store with namespaces if fit)

**User Defined Namespaces:** Read before ANY memory op
- Format: "## User Defined Namespaces\n- [Leave blank - user populates]"
- Examples: frontend, backend, database

**Storing:** Review content → check namespaces → THINK "domain?" → fits one? assign : omit | Rules: Max ONE, can be NONE, only defined ones
**Searching:** What searching? → read namespaces → THINK "which could contain?" → cast wide net → use multiple if needed

**Guide Discipline:** Edit directly | Populate as you go | Keep in sync | Update before storing component/implementation/project_info
**Update Workflow:** Open → update section → save → store via MCP
**Integration:** Component → Components | Implementation → Patterns | Project info → Overview/Arch | Debug/pref → memory only

### 🚨 CRITICAL: Before storing ANY memory

Review and update `mimir.md` before storing any memory — after every edit verify the guide reflects the current system architecture (this is the most important project artifact). Do not expand this legacy file beyond migration notes.

## Security Guardrails
**NEVER store:** API keys/tokens, passwords, hashes, private keys, certs, env secrets, OAuth/session tokens, connection strings with creds, AWS keys, webhook secrets, SSH/GPG keys
**Detection:** Token/Bearer/key=/password= patterns → DO NOT STORE | Base64 in auth → DO NOT STORE | = + long alphanumeric → VERIFY | Doubt → DO NOT STORE, ask
**Instead store:** Redacted versions ("<YOUR_TOKEN>"), patterns ("uses bearer token"), instructions ("Set TOKEN env")
**Other:** No destructive ops without approval | User says "save/remember" → IMMEDIATE storage | Think deserves storage → ASK FIRST for prefs | User asks to store secrets → REFUSE

**Remember:** Memory system = effectiveness over time. Rich reasoning > code. When doubt, store. Guide = shareable index.
