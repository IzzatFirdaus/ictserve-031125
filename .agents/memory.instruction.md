---
applyTo: '**'
---

# Coding Preferences

- Use the Memory MCP JSONL store at `storage/mcp/memory.jsonl` for persistent knowledge
- Keep helper scripts concise and focused on active services only

## Project Architecture

- Memory system uses MCP Memory Server with JSONL storage (no external database required)
- Docker service names: mcp-memory, copilot_api_server
- Memory file location: `storage/mcp/memory.jsonl`

## Solutions Repository

- All memory operations use MCP tools: create_entities, create_relations, add_observations
- Memory verification via read_graph and search_nodes tools
- No external database dependencies for memory storage
- `BilingualSupportService::getSupportedLocales()` returns an associative array keyed by locale code; Volt language switcher loops must destructure `code => meta` rather than treating entries as scalars to avoid htmlspecialchars array TypeErrors.
- When testing Google SSO, bind `SsoHealthCheckInterface` to a Mockery mock and stub `getServiceStatus()` per test to avoid network calls and cached statuses; avoid default stubs that mask per-test expectations.
