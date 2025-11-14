---
applyTo: '**'
---

# Livewire & Volt Instructions

Purpose  
Defines conventions, standards, traceability, testing, accessibility and operational rules for Livewire 3 and Volt components in ICTServe. This document is normative for developers, maintainers, testers and AI agents when creating or modifying interactive UI components. Reference the official Livewire documentation at http://livewire.laravel.com/docs/ for Livewire-specific requirements, design, testing and conventions.

Scope  
Applies to all Livewire (incl. Volt) components and their Blade/Volt views, tests, and related assets. Covers scaffolding, placement, naming, metadata, accessibility, tests, and documentation. Relevant paths: `app/Livewire/`, `resources/views/livewire/`, `tests/Feature/`, `resources/js/*` (if component uses JS), and `docs/components/`.

Standards & References (Mandatory)
- Official Livewire Documentation (http://livewire.laravel.com/docs/)
- PSR-12, Laravel 12 best practices
- Livewire 3 + Volt conventions
- WCAG 2.2 Level AA (accessibility)
- ISO/IEC/IEEE 12207, ISO 9001, BPM/MOTAC policies

Traceability (Mandatory)
- Every feature implemented by Livewire/Volt MUST be traceable to:
  - Requirement IDs from D03 (SRS)
  - Design refs from D04/D11
  - Test/integration refs from D07/D08 where applicable
- Add a `trace` metadata block or inline comment at the top of component files and key view templates listing: SRS IDs, design refs, author, and last-updated date.
  - Example top comment:
    ```php
    // name: UserTable (Livewire Component)
    // description: Livewire table to list and filter users for admin
    // author: dev-team@motac.gov.my
    // trace: SRS-FR-012; D04 §4.2; D11 §6
    // last-updated: 2025-10-21
    ```

Mandatory Rules & Conventions
- Placement & Naming
  - Components: `app/Livewire/`, class names PascalCase, file names match class (UserTable.php).
  - Views: `resources/views/livewire/component-name.blade.php` (kebab-case filenames).
  - Volt components follow same placement and naming conventions when used.
- Scaffolding
  - Use artisan when possible: php artisan make:livewire UserTable (or the Volt generator). See: https://livewire.laravel.com/docs/quickstart
  - Keep components small and single-responsibility — split large components into child components. See: https://livewire.laravel.com/docs/nesting
- Component structure
  - Use typed properties and return types. See: https://livewire.laravel.com/docs/properties
  - Public reactive properties only when necessary; prefer computed getters and action methods. See: https://livewire.laravel.com/docs/actions, https://livewire.laravel.com/docs/computed-properties
  - Lifecycles: use mount(), hydrate(), updated<Property>(), and render() appropriately. See: https://livewire.laravel.com/docs/lifecycle-hooks
- Model & Data
  - Eager-load relations in components to avoid N+1 queries: Model::with('relation')->...
  - Respect authorization: apply policies/gates in controller or mount and use @can in views.
- Accessibility & UI
  - All interactive elements must have semantic HTML (buttons, forms).
  - Add ARIA attributes where necessary and ensure focus management (modals, form errors).
  - Provide skip links/landmarks in pages that host components.
- Localization
  - Use trans() / __() helpers for strings; primary language Bahasa Melayu, English secondary where helpful (see D15).
- No new top-level dirs
  - Do not create new top-level folders. Use existing structure (`app/Livewire`, `resources/views/livewire`, `docs/`).
- Metadata & Documentation
  - Each non-trivial component MUST include a top metadata comment and a short docs file in `docs/components/component-name.md` describing purpose, props, events, required permissions, trace refs, and accessibility notes.

Step-by-step workflow for adding or modifying a component
1. Check requirements/design: identify SRS (D03) and design refs (D04/D11); update RTM if implementing a new SRS.
2. Scaffold component: php artisan make:livewire ComponentName (or Volt equivalent).
3. Implement logic: typed properties, methods, calls to services/Repositories; keep business logic out of views.
4. Add metadata comment at file top (name, description, author, trace, last-updated).
5. Create/modify Blade view under `resources/views/livewire/`.
6. Write tests: unit for pure logic; Livewire tests under `tests/Feature/ComponentNameTest.php`.
7. Run local checks: vendor/bin/phpstan, vendor/bin/pint --dirty, php artisan test.
8. Add docs: `docs/components/component-name.md` with usage, trace refs, keys, accessibility checklist, and rollback steps if relevant.
9. Open PR: include traceability IDs, tests, screenshots if UI changed, accessibility checklist, and required reviewers (Dev, Security, Accessibility).
10. After merge: update RTM / D03 and notify operations if component affects integration or production flows.

Testing & Validation (Mandatory)
- Livewire tests:
  - Use Livewire::test(Component::class) to assert properties, methods, emitted events, redirects, validation messages. See: https://livewire.laravel.com/docs/testing
  - Authenticate in tests when component requires user: actingAs($user).
- Volt tests:
  - Use Volt::test(...) where Volt-specific helpers are required.
- Test coverage:
  - Feature tests for component workflows (form submission, validation, error states).
  - Unit tests for helper classes and service layer.
- CI:
  - CI must run php artisan test, phpstan, pint, and accessibility checks for PRs touching components.
- Accessibility tests:
  - Automated: axe/lighthouse on hosted preview or component storybook where feasible.
  - Manual: keyboard navigation, screen-reader smoke tests for complex components.

Examples & Patterns

- Minimal Livewire component (app/Livewire/UserTable.php)
```php
<?php
// name: UserTable
// description: Livewire component to display & filter users for admin
// author: dev-team@motac.gov.my
// trace: SRS-FR-012; D04 §4.2; D11 §6
// last-updated: 2025-10-21

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Contracts\View\View;

class UserTable extends Component

    public string $search = '';

    public function render(): View
    
        $users = User::with('division')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%$this->search%"))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.user-table', compact('users'));


```

- View (resources/views/livewire/user-table.blade.php)
```blade
<div>
    <label for="search" class="form-label">Cari</label>
    <input id="search" wire:model.debounce.300ms="search" class="form-control" type="text" />
    <div class="table-responsive mt-3">
        <table class="table" aria-label="Senarai pengguna">
            <thead><tr><th>Nama</th><th>Emel</th><th>Bahagian</th></tr></thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td> $user->name </td>
                        <td> $user->email </td>
                        <td> $user->division->name ?? '-' </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
         $users->links() 
    </div>
</div>
```

Component metadata block (copy into file top)
```
# name: <ComponentName>
# description: <one-line purpose>
# author: <team/email>
# trace: <SRS-ID(s); D04 §; D11 §>
# last-updated: YYYY-MM-DD
```

Common pitfalls & guidance
- Avoid heavy data processing in render(); fetch/precompute in mount() or via services to keep re-renders cheap.
- Use pagination and caching for large datasets; avoid returning all records. See: https://livewire.laravel.com/docs/pagination
- Use emit/dispatchBrowserEvent for JS interactions; keep progressive enhancement and ARIA support for accessibility.
- When using file uploads or large inputs, stream/process in background jobs and provide progress UI. See: https://livewire.laravel.com/docs/file-uploads
- Prefer policies over inline role checks; check authorization in mount() and in routes if applicable.

File & Documentation Requirements
- Add a short docs file per component under `docs/components/` with:
  - Purpose, props, events, required permissions, sample usage, trace refs, accessibility considerations, tests, and rollback steps.
- Update top-level docs index `docs/components/README.md` listing all components and owners.
- Add or update PR templates to include component-specific checklist items.

PR Checklist (components)
- [ ] Traceability IDs included (SRS/Design refs)
- [ ] Component metadata header present
- [ ] Tests added/updated (Livewire/Volt + integration)
- [ ] Accessibility checklist completed (axe/Lighthouse/keyboard/screen reader)
- [ ] phpstan & pint passes locally
- [ ] Docs updated under docs/components/
- [ ] Reviewer: include accessibility owner when UI changes

Cross-references
- Official Livewire Documentation References:
  - **Getting Started**
  - Quickstart: https://livewire.laravel.com/docs/quickstart
  - Installation: https://livewire.laravel.com/docs/installation
  - Upgrade Guide: https://livewire.laravel.com/docs/upgrade-guide
  - **Essentials**
  - Components: https://livewire.laravel.com/docs/components
  - Properties: https://livewire.laravel.com/docs/properties
  - Actions: https://livewire.laravel.com/docs/actions
  - Forms: https://livewire.laravel.com/docs/forms
  - Events: https://livewire.laravel.com/docs/events
  - Lifecycle Hooks: https://livewire.laravel.com/docs/lifecycle-hooks
  - Nesting Components: https://livewire.laravel.com/docs/nesting
  - Testing: https://livewire.laravel.com/docs/testing
  - **Features**
  - Alpine: https://livewire.laravel.com/docs/alpine
  - Navigate: https://livewire.laravel.com/docs/navigate
  - Lazy Loading: https://livewire.laravel.com/docs/lazy
  - Validation: https://livewire.laravel.com/docs/validation
  - File Uploads: https://livewire.laravel.com/docs/file-uploads
  - Pagination: https://livewire.laravel.com/docs/pagination
  - URL Query Parameters: https://livewire.laravel.com/docs/url-query-parameters
  - Computed Properties: https://livewire.laravel.com/docs/computed-properties
  - Session Properties: https://livewire.laravel.com/docs/session-properties
  - Redirecting: https://livewire.laravel.com/docs/redirecting
  - File Downloads: https://livewire.laravel.com/docs/file-downloads
  - Locked Properties: https://livewire.laravel.com/docs/locked
  - Request Bundling: https://livewire.laravel.com/docs/request-bundling
  - Offline States: https://livewire.laravel.com/docs/offline-states
  - Teleport: https://livewire.laravel.com/docs/teleport
  - **HTML Directives**
  - wire:click: https://livewire.laravel.com/docs/wire-click
  - wire:submit: https://livewire.laravel.com/docs/wire-submit
  - wire:model: https://livewire.laravel.com/docs/wire-model
  - wire:loading: https://livewire.laravel.com/docs/wire-loading
  - wire:navigate: https://livewire.laravel.com/docs/wire-navigate
  - wire:current: https://livewire.laravel.com/docs/wire-current
  - wire:cloak: https://livewire.laravel.com/docs/wire-cloak
  - wire:dirty: https://livewire.laravel.com/docs/wire-dirty
  - wire:confirm: https://livewire.laravel.com/docs/wire-confirm
  - wire:transition: https://livewire.laravel.com/docs/wire-transition
  - wire:init: https://livewire.laravel.com/docs/wire-init
  - wire:poll: https://livewire.laravel.com/docs/wire-poll
  - wire:offline: https://livewire.laravel.com/docs/wire-offline
  - wire:ignore: https://livewire.laravel.com/docs/wire-ignore
  - wire:replace: https://livewire.laravel.com/docs/wire-replace
  - wire:show: https://livewire.laravel.com/docs/wire-show
  - wire:stream: https://livewire.laravel.com/docs/wire-stream
  - wire:text: https://livewire.laravel.com/docs/wire-text
  - **Concepts**
  - Morphing: https://livewire.laravel.com/docs/morphing
  - Hydration: https://livewire.laravel.com/docs/hydration
  - Nesting: https://livewire.laravel.com/docs/nesting
  - **Advanced**
  - Troubleshooting: https://livewire.laravel.com/docs/troubleshooting
  - Security: https://livewire.laravel.com/docs/security
  - JavaScript: https://livewire.laravel.com/docs/javascript
  - Synthesizers: https://livewire.laravel.com/docs/synthesizers
  - Contribution Guide: https://livewire.laravel.com/docs/contribution-guide

Contacts & Owners
- Frontend / Livewire owner: frontend@motac.gov.my
- Accessibility: accessibility@motac.gov.my
- Documentation & Traceability: docs@motac.gov.my
- DevOps / CI: devops@motac.gov.my

Notes & Governance
- This file is normative for Livewire & Volt development in this repository. Any deviation that impacts accessibility, security, or traceability requires formal change request and RTM update (see D01 §9.3).
- Review and update this document after major Livewire/Volt upgrades or when project conventions change (at least annually).

Appendix — Quick commands
- Scaffold: php artisan make:livewire ComponentName
- Run tests: php artisan test
- Static checks: vendor/bin/phpstan analyse && vendor/bin/pint --test
- Dev server: composer run dev (project orchestration) and npm run dev (Vite)
- Build assets: npm run build
