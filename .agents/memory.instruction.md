---
applyTo: '**'
---

# Coding Preferences
- Remove legacy Mimir references from scripts; prefer Neo4j + MCP stack only.
- When adjusting helper scripts, keep user-facing guidance concise and warn on deprecated services.

# Project Architecture
- PCTX stack now starts Neo4j and Copilot API only; Mimir services are decommissioned.
- Docker service names expected by helpers: neo4j_db, copilot_api_server; PCTX runs via `pctx dev`.

# Solutions Repository
- `start-pctx-stack.ps1` defaults updated to exclude Mimir and warn if requested.
- `execute-memory-import-and-cleanup.ps1` next steps trimmed to omit Mimir portal checks.
- `finalize-phase5.ps1` no longer tracks docs\mimir.md; reports none when no operational docs remain.
