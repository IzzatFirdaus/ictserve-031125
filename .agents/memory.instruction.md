---
applyTo: '**'
---

# Coding Preferences
- Remove legacy Mimir/Neo4j references from scripts; use the Memory MCP JSONL store only.
- When adjusting helper scripts, keep user-facing guidance concise and warn on deprecated services.

# Project Architecture
- PCTX stack uses Memory MCP JSONL storage; Mimir and Neo4j services are decommissioned.
- Docker service names expected by helpers: mcp-memory, copilot_api_server; PCTX runs via `pctx dev`.

# Solutions Repository
- `start-pctx-stack.ps1` defaults exclude Mimir and warn if requested.
- `execute-memory-import-and-cleanup.ps1` next steps omit Mimir/Neo4j portal checks.
- `finalize-phase5.ps1` should not rely on Neo4j; prefer Memory MCP JSONL verification.
- `BilingualSupportService::getSupportedLocales()` returns an associative array keyed by locale code; Volt language switcher loops must destructure `code => meta` rather than treating entries as scalars to avoid htmlspecialchars array TypeErrors.
