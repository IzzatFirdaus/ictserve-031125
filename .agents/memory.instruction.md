---
applyTo: '**'
---

# Coding Preferences

- Use the Memory MCP JSONL store at `storage/mcp/memory.jsonl` for persistent knowledge
- Keep helper scripts concise and focused on active services only

## GitHub Codespaces Setup (2025-12-15)

**Composer/Vendor Issues RESOLVED** - Configured automatic Composer auth for Codespaces:
- `.devcontainer/devcontainer.json` - Runs setup-composer.sh on Codespaces creation
- `.devcontainer/setup-composer.sh` - Handles GitHub token auth, HTTPS git config, vendor installation
- `.github/workflows/composer-validate.yml` - CI/CD validation for Composer
- Fixes: GitHub authentication (token-based), vendor conflicts (squizlabs), SSH→HTTPS redirection
- Codespaces auto-injects `$GITHUB_TOKEN` env var - no manual setup needed

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

## Authenticated Frontend v3.6.0 Refactoring (2025-12-15)

**MyDS Design Tokens Applied:**
- Cards/Containers: `rounded-l` (12px) + `shadow-card` + `theme-transition`
- Buttons: `rounded-m` (8px) + `shadow-button` + `min-h-11 min-w-11`
- Inputs/Selects: `rounded-m` + `min-h-11` + proper dark mode variants
- Focus Indicators: `focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2`

**WCAG 2.2 AA Compliance Fixes:**
- All touch targets now meet 44×44px minimum (WCAG 2.5.8)
- Focus indicators upgraded to 3px visible ring with outline-offset-2
- Form inputs have proper `aria-required`, `aria-describedby`, `aria-invalid`
- Error messages use `role="alert"` for screen reader announcements
- Table headers already had `scope="col"` (submission-history)
- Notification center now has ARIA live region (`role="log"` + `aria-live="polite"`)
- Screen reader announcements added for new notifications (`aria-live="assertive"`)

**Files Updated:**
1. `resources/views/livewire/authenticated-dashboard.blade.php` - Statistics cards, buttons, claim banner
2. `resources/views/livewire/portal/user-profile.blade.php` - All form sections (profile, password, language, notifications)
3. `resources/views/livewire/staff/submission-history.blade.php` - Tabs, filters, action buttons
4. `resources/views/livewire/portal/notification-center.blade.php` - ARIA live regions, filter buttons, action buttons
