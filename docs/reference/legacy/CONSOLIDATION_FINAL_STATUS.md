# Documentation Consolidation - LEGACY (Deprecated)

**Date**: November 23, 2025  
**Status**: ❌ Deprecated  
**Progress**: Historical snapshot only (Neo4j retired; Memory MCP JSONL is authoritative)

> LEGACY WARNING: The Neo4j/Mimir stack is decommissioned. Use the Memory MCP JSONL store at `storage/mcp/memory.jsonl` with the Memory MCP server. Do **not** attempt to start or connect to Neo4j or Mimir. Credentials and commands have been removed.

---

## Summary (Historical Only)

This file records a past consolidation into Neo4j. That flow is obsolete. The canonical knowledge source is now the Memory MCP JSONL store (`storage/mcp/memory.jsonl`).

## Legacy Artifacts

- `README.md` - Project overview (keep)
- `AGENTS.md` - Agent guidelines (keep)
- `CLAUDE.md` - Claude-specific notes (keep)
- `GEMINI.md` - Gemini-specific notes (keep)

## Current Guidance

- Use the Memory MCP server; do not start Neo4j or Mimir containers.
- JSONL path: `storage/mcp/memory.jsonl`.
- Legacy credentials and commands were removed intentionally.

---

**Deprecated** - Memory MCP is the single source of truth. This document is retained only for audit history.
