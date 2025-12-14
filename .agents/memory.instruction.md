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
- PHPStan/Larastan at Level 9; phpstan.neon has comprehensive ignoreErrors for Laravel type covariance issues (View vs Contracts\View, User vs Authenticatable, etc.). Use phpstan-simple.neon for standalone analysis without vendor dependencies.
- Larastan may not infer Spatie Permission methods (e.g., `hasRole`) on `Auth::user()`; fix by assigning to a typed `/** @var \App\Models\User|null $user */ $user = Auth::user();` before calling role/permission methods.

## Frontend v3.6.0 Patterns

- **Theme Init Script**: All layouts must include `<x-theme-init-script />` in `<head>` for FOUT prevention. The script reads localStorage 'theme' key and applies 'dark' class to documentElement before page render.
- **Touch Targets**: Use `min-h-11 min-w-11` (44px) for WCAG 2.5.8 compliance, NOT `min-h-44` or `min-w-44` which are invalid Tailwind classes. 11 × 4px = 44px.
- **Dark Mode**: Layouts require `theme-transition` class on body/container elements AND appropriate dark mode variant classes (`dark:bg-gray-900`, `dark:text-gray-100`). Light mode is immutable default.
- **Theme Toggle**: Use `<livewire:components.theme-toggle />` component in navigation headers. For minimal layouts, place in fixed position top-right.
