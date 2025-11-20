# ICTServe OpenMemory Guide
# Example bridge invocation

```bash
ICTServe is a Laravel 12 application for MOTAC (Ministry of Tourism, Arts and Culture Malaysia) implementing a Business Process Management system. The system includes:

- **Guest Portal**: Public submission workflows for tourism/arts organizations
- **Staff Portal**: Internal processing, approval workflows, SLA tracking
- **Filament Admin**: Full CRUD, user management, department management, audit logs
- **Email System**: 12 Mail classes with queue-based dual approval notifications
- **Memory Graph API**: Native Laravel implementation of agentic AI memory (Nov 2025)

## Architecture

**Stack**:

- Laravel 12 (PHP 8.2.12)
- Livewire 3 + Volt (interactive components)
- Filament 4 (admin panel with SDUI patterns)
- Tailwind CSS 3 + Alpine.js
- MySQL/MariaDB (production) / SQLite (testing)
- PHPUnit 11 + Larastan (static analysis)

**Key Patterns**:

- MVC + Server-Driven UI (SDUI) via Filament Resources
- Guest-only architecture (no authentication for public portal)
- Dual approval workflows (email + portal interfaces)
- Bilingual support (Bahasa Melayu primary, English secondary)
- WCAG 2.2 Level AA accessibility compliance
- PSR-12 code standards enforced via Pint

## User Defined Namespaces

- `authentication` - Laravel Sanctum, policies, role-based access
- `frontend` - Livewire/Volt components, Blade views, Tailwind styling
- `backend` - Controllers, services, jobs, mail classes
- `database` - Migrations, models, factories, seeders
- `testing` - PHPUnit feature/unit tests, E2E Playwright tests
- `filament-admin` - Filament 4 resources, pages, widgets, actions
- `email` - Queue-based notification system, dual approval emails
- `accessibility` - WCAG 2.2 AA compliance, ARIA patterns, keyboard navigation
- `localization` - Bilingual MS/EN translation system
- `memory-graph` - Agentic AI memory implementation (native Laravel)
- `documentation` - D00-D15 canonical docs, RTM traceability

## Components

### Memory Graph System (Nov 2025)
**Location**: `app/Models/Memory*`, `app/Services/MemoryGraphService.php`, `database/migrations/2025_11_15_120000_create_memory_graph_tables.php`
**Purpose**: Native Laravel implementation of semantic knowledge graph for AI agent memory
**Key Models**:

- `MemoryEntity` - Core knowledge nodes with UUID PKs, soft deletes, metadata
- `MemoryObservation` - Facts/details linked to entities (content_hash, confidence, recorded_at)
- `MemoryRelation` - Edges connecting entities (relation_type, discovered_at, unique constraints)
- `MemoryAdapter` - External integration points (MCP servers, APIs)
- `MemoryAdapterSync` - Sync history with payload/error tracking

**Schema Features**:

- UUID primary keys for distributed systems
- Soft deletes on all tables
- Confidence scoring (0-1 decimal) for probabilistic reasoning
- Content hashing for observation deduplication
- Unique constraints on (entity_id, relation_type, related_entity_id) for relations
- Unique constraints on (entity_id, content_hash) for observations
- Adapter pivot table for many-to-many entity-adapter associations

**Service Layer**: `MemoryGraphService` provides:

- `createEntity()` / `updateEntity()` / `deleteEntity()`
- `addObservation()` / `removeObservation()`
- `createRelation()` / `removeRelation()`
- `createAdapter()` / `syncAdapter()` / `attachEntityToAdapter()`

**Validation**: FormRequests in `app/Http/Requests/Memory/`

- `StoreMemoryEntityRequest` - Validates entity creation (name uniqueness, typed metadata)
- `UpdateMemoryEntityRequest` - Validates entity updates
- `StoreMemoryObservationRequest` - Validates observation recording (content, confidence bounds)

**Status**: Migration + models + service layer complete (Nov 15, 2025). Pending: API controllers, routes, feature tests, documentation integration.

### Automated Memory Sync & Agent Hooks

To ensure markdown documentation and agentic notes are recorded in MCP memory, the project offers these integration points:

- Schedule: `php artisan memory:sync-markdown` scans `docs/`, `.agents/`, `.github/`, and root for markdown files then imports them into the memory graph. To run automatically, `routes/console.php` already schedules it daily at 03:00 — you can adjust or add your own schedule. Configure the command to run more often via OS scheduler when necessary.

- Windows: register an OS scheduled task using `scripts/register-memory-sync.ps1`.

 
- Linux/macOS: use cron or systemd timer to run `php artisan memory:sync-markdown` on your preferred interval. Example (crontab):
#
```bash
# Import docs every hour
0 * * * * cd /path/to/ictserve && php artisan memory:sync-markdown >> storage/logs/memory-sync.log 2>&1
```

- Agent Endpoint: `POST /api/v1/memory/import` — allows agentic LLM tools to push content directly into memory during sessions. The endpoint accepts `content`, `title`, `entity_type`, `summary`, and optional `path` so agents can pass either source text or a repo path.

Security: The endpoint is protected by a bearer token `MEMORY_API_TOKEN` (recommended for agentic services) or by authenticated users with the MemoryEntity 'create' permission. By default the sync uses a `local` MemoryAdapter to keep imports auditable and reversible.

Example agent request:

```bash
curl -X POST https://your.app/api/v1/memory/import \
  -H "Authorization: Bearer $MEMORY_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"title":"Session Note","content":"We updated the email template.","entity_type":"analysis_work"}'
```

### Agentic integration guidance

To make memory usage automatic from any agentic LLM session, follow these steps inside your agent/tool:

1. At session start, call `GET /api/v1/memory/search?q=topic` to load memory context relevant to the session.
2. If the agent generates new facts, call `POST /api/v1/memory/import` with the `content` and `title`.
3. For bulk imports or attachments produced during the session, call the scheduled import endpoint or ensure your agent pushes content with a `source` and `path` so the `memory:sync-markdown` can track provenance.

Important: Always use `MEMORY_API_TOKEN` for agent credentials. Store it securely in the agent configuration and never embed it in logs. Also create `work_session` entities for major session imports (recommendation: agents should call the import endpoint with a session ID to link notes to session). This repository auto-creates `work_session` for scheduled imports; agents can use `Session_` naming for consistency.

Example plugin (JavaScript)

```javascript
// Lightweight agent plugin outline: call memory search & import in session
async function startSession(agentToken, query) {
  const search = await fetch(`/api/v1/memory/search?q=${encodeURIComponent(query)}`, {
    headers: { 'Authorization': `Bearer ${agentToken}` }
  });
  const data = await search.json();
  // Load memory into agent state
  agentState.memory = data;
}

async function commitObservation(agentToken, title, content) {
  await fetch('/api/v1/memory/import', {
     method: 'POST',
     headers: { 'Authorization': `Bearer ${agentToken}`, 'Content-Type': 'application/json' },
     body: JSON.stringify({ title, content })
  });
}

### Memory bridge (example)

If your agentic platform cannot make direct HTTP calls to your app, run a local memory bridge which acts as a proxy to the Memory API. Use `scripts/memory-bridge.js` (Node). Example:

```bash
# Run memory bridge to import a single note
MEMORY_API_TOKEN=mytoken node scripts/memory-bridge.js "Auto note from agent"
```

```

### Policy: Reference Research & Planning for Adjustments

Any adjustment to the memory system (schema, service, adapter, or semantics) MUST reference the research and planning artifacts identified during the MCP investigation:

- `MCP_MemoryServer_Complete_Export_2025-11-08` (complete export)
- `Memory_Graph_Implementation_2025-11-15` (implementation details)
- `Memory_Graph_Schema_Design` (schema reasoning)
- `Memory_Graph_Service_Patterns` (service-level patterns)

Adjustments should also create a `work_session` entity that documents what changed, why it changed, and who performed the change — then link that `work_session` to the research entities above. This policy ensures traceability and allows future agents / reviewers to discover rationale and research supporting changes.

Adjustments should also create a `work_session` entity that documents what changed, why it changed, and who performed the change — then link that `work_session` to the research entities above. This policy ensures traceability and allows future agents / reviewers to discover rationale and research supporting changes.

### Email Notification System
**Location**: `app/Mail/`, `app/Jobs/`, `app/Services/EmailNotificationService.php`
**Purpose**: Queue-based dual approval workflows (Tasks 10.1-10.2)
**Components**:

- 12 Mail classes with ShouldQueue interface
- DualApprovalService for email + portal approval tracking
- 5 email approval routes + 3 portal approval routes
- 11 WCAG 2.2 AA compliant templates (bilingual)
- Queue jobs with exponential backoff

### Filament Admin Panel
**Location**: `app/Filament/`, `app/Filament/Resources/`
**Purpose**: Full CRUD interface for users, departments, submissions, audit logs
**Patterns**:

- Unified action namespace: `Filament\Actions\Action`
- BulkAction namespace: `Filament\Actions\BulkAction`
- Custom Pages: `getHeaderActions()` with `->form()` modals
- Resources: `Filament\Schemas\Schema` for form signatures
- SoftDeletes trait for logical deletion

**Status**: 18 phases complete (Nov 15, 2025), WCAG 2.2 AA, bilingual support

### Livewire/Volt Components
**Location**: `app/Livewire/`, `resources/views/livewire/`
**Purpose**: Interactive UI components for staff/guest portals
**Patterns**:

- `#[Reactive]` for state variables
- `#[Computed]` for derived values
- `wire:model.live` for real-time updates
- Named event dispatching
- Performance: `#[Lazy]`, `wire:model.debounce`, pagination

**Compliance**: 98% Livewire 3 + Volt compliance (audited Jan 6, 2025)

## Patterns

### Database Migration Pattern

- Always use `declare(strict_types=1);`
- UUID PKs via `$table->uuid('id')->primary()`
- Soft deletes via `$table->softDeletes()`
- Foreign keys with `constrained()->cascadeOnDelete()`
- Unique constraints via `unique()` method chaining
- Indices for frequently queried columns
- Down method with proper rollback (drop tables, indices)

### Eloquent Model Pattern

- Use `HasUuids` trait for UUID PKs
- Use `SoftDeletes` for logical deletion
- Define `casts()` method (NOT `$casts` property)
- Explicit relationship return type hints (`HasMany`, `BelongsTo`, etc.)
- `$fillable` array for mass assignment protection
- PHPDoc blocks for array shape type definitions

### Service Layer Pattern

- Constructor dependency injection
- Type-hinted parameters and return types
- Database transactions for multi-model operations
- Error handling with descriptive exceptions
- Private helper methods for complex logic

### Validation Pattern

- FormRequest classes (NOT inline controller validation)
- Authorization via `authorize()` method
- Custom error messages via `messages()` method
- Array/object validation with shape definitions
- Unique/exists rules with table/column specification

### Testing Pattern

- Feature tests for workflows (guest submission, approval, email)
- Unit tests for services/helpers
- Factory usage for test data (avoid manual model creation)
- Database transactions for test isolation
- Assertion helpers: `assertDatabaseHas`, `assertSeeLivewire`, `assertNotified`
- Prefer PHPUnit attributes like `#[Test]` instead of `@test` docblocks to avoid deprecated metadata in future PHPUnit versions

### Accessibility Pattern (WCAG 2.2 AA)

- ARIA landmarks on all major sections
- Skip links for keyboard navigation
- Focus indicators on interactive elements
- Form labels with `for` attribute
- Alt text on images
- Color contrast ratio ≥ 4.5:1

### Localization Pattern

- Translation keys in `lang/en/` and `lang/ms/`
- `__('key')` function in Blade templates
- `trans()` for complex pluralization
- Fallback to English when MS translation missing
- 27.6% localized as of Nov 15, 2025 (target: 100%)

## Known Issues & Solutions

### 500 Error Resolution Pattern
**Entity**: `500_Error_Resolution_Pattern`
**Steps**:

1. Check `storage/logs/laravel.log` for stack trace
2. Verify `bootstrap/cache` permissions (775)
3. Check `.env` database credentials
4. Run `php artisan cache:clear && php artisan config:clear`

### Database Connection Error
**Entity**: `Database_Connection_Error_Resolution`
**Steps**:

1. Verify DB driver in `.env` (mysql, sqlite, pgsql)
2. Test connection: `php artisan tinker` → `DB::connection()->getDatabaseName()`
3. Check database server status
4. Validate credentials (DB_HOST, DB_USERNAME, DB_PASSWORD)

### Seeding Failures
**Entity**: `Seeding_Failures_Resolution`
**Steps**:

1. Check foreign key constraints order
2. Verify factory definitions match schema
3. Use `php artisan db:seed --class=SeederName` for specific seeder
4. Debug with `php artisan tinker` factory generation

### SelectFilter TypeError
**Entity**: `SelectFilter_TypeError_Fix_2025-01-06`
**Solution**: Specify localized display column for relationship depending on locale
**Files**: `app/Filament/Resources/Users/Tables/UsersTable.php`

### ApprovalInterface Test Failure
**Entity**: `Copilot_ApprovalInterface_Fix_Task_2025-11-10`
**Solution**: Update middleware to check legacy role + Spatie permissions, add `pendingApprovals` property in Livewire component
**Files**: `tests/Feature/Portal/ApprovalInterfaceTest.php`

### E2E Test Route Discrepancies
**Entity**: `E2E_Test_Route_Discrepancies_Fix_2025-01-06`
**Solution**: Tests referenced guest routes for authenticated workflows; updated to use staff/portal routes
**Verification**: Ensure staff portal coverage in CI

## Recent Work Sessions

### Session_2025-11-15_MemoryGraphImplementation
**Task**: Implement native Laravel memory graph for agentic AI
**Completed**:

- Migration: `2025_11_15_120000_create_memory_graph_tables.php` (6 tables, UUID PKs, soft deletes, constraints)
- Models: `MemoryEntity`, `MemoryObservation`, `MemoryRelation`, `MemoryAdapter`, `MemoryAdapterSync`
- Service: `MemoryGraphService` with CRUD operations, relation management, adapter sync
- Validation: `StoreMemoryEntityRequest`, `UpdateMemoryEntityRequest`, `StoreMemoryObservationRequest`
**Pending**:
- API controllers for REST endpoints
- Routes registration under `/api/v1/memory`
- Feature tests (entity CRUD, observation recording, relation creation)
- Documentation integration (update D10, D11)
**Blockers**: None
**Lines**: 847 (migration: 247, models: 315, service: 187, requests: 98)

### Session_2025-11-15_ImportAttachmentsAndDeleteFiles
**Task**: Migrate markdown documentation to memory.jsonl and delete source files
**Completed**:

- Imported 8 markdown files to memory entities
- Deleted files: localization report, Livewire audit, E2E fix, completion doc, copilot task, SelectFilter fix, superuser/admin guides
- Created work_session record for traceability
**Files Deleted**: 8 markdown files
**Relations**: Documents 8 imported entities

### Session_2025-11-15_Playwright_Failure_Resolution_Update
**Task**: Resolve listed Playwright test failures in markdown logs and update progress status.
**Changes**:

- Updated `failed-playwright-tests-2.md` with a consolidated progress header, category status, and next actions.
- Attempted header insertion into `failed-playwright-tests.md` (tool limitation prevented inline write); left content intact and tracked status in the second log.
**Findings**:

- Accessibility suites largely passing (guest/auth/admin/approver) in refactored files; legacy specs remain flaky/timeouts.
- Staff login stable using seeded users; admin panel tests may skip depending on routing/policies.
- Performance thresholds (TTFB, Lighthouse Performance) fail in local dev; advise production build and caching for CI.
**Next steps**:

- Prefer refactored specs in CI gates; progressively retire legacy specs.
- Run with `npm run build` + caching for performance audits.
- Set `BROADCAST_DRIVER=log` locally to suppress Pusher warnings.
**Trace**: D03 § testing; D12/D14 accessibility; files: tests/e2e/*, failed-playwright-tests*.md

### Session_2025-11-20_E2E_Form_Wizards
**Task**: Align Playwright refactored specs with the new multi-step helpdesk and asset loan forms for guest and authenticated users.
**Changes**:

- Updated `tests/e2e/helpdesk.refactored.spec.ts` to follow the 4-step wizard (contact info, issue details, attachments, confirmation) and cover guest declaration/job grade fields plus authenticated step navigation.
- Updated `tests/e2e/loan.refactored.spec.ts` to navigate the 7-step asset loan wizard (prefilled auth step, responsible officer, equipment table) and refreshed the guest wizard flow with future-dated requests and equipment selection.
**Notes**: Tests were not executed in this session per request; fixtures remain unchanged.

### Session_2025-11-20_HelpdeskIntelephenseCompliance
**Task**: Resolve Intelephense return-type warnings across helpdesk Livewire components.
**Changes**:

- Added explicit `View` return types and imports to helpdesk components (`Dashboard`, `MyTickets`, `NotificationCenter`, `TicketDetails`, `TrackTicket`, `SubmitTicket`).
- Added `Collection` return types to computed data providers in `SubmitTicket` (categories, assets).
- Ran `vendor/bin/pint --dirty` to keep styling consistent.
**Notes**: No tests executed; behavioural logic unchanged.

### Session_2025-11-20_LoanIntelephenseCompliance
**Task**: Address Intelephense warnings in loan-related Livewire components and guest loan flows.
**Changes**:

- Added explicit `View` return types/imports across loan Livewire components (`ApprovalQueue`, `AuthenticatedDashboard`, `AuthenticatedLoanDashboard`, `LoanDashboard` incl. `placeholder`, `LoanDetails`, `LoanExtension`, `LoanHistory`, `SubmitApplication`, `GuestLoanApplication`, `GuestLoanTracking`).
- Ensured guest loan submission methods use void return types where appropriate.
- Ran `vendor/bin/pint --dirty` to align formatting.
**Notes**: No behavioural changes; tests not executed per request.

## Project Milestones

### Session_2025-11-19_Larastan_Fixes_Seeders_and_Factories
**Task**: Reduce Larastan noise across seeders/factories by adding type safety and guard rails.
**Changes**:

- Hardened `FullDivisionSeeder` CSV parsing with header validation and string key normalization.
- Added null/data guards for `HelpdeskTicketSeeder` (requires admin/users, safer division defaults) and `LoanModuleSeeder` (requires all asset categories) to avoid nullable access.
- Corrected cross-module seeder to use `asset_tag` field and seeded helpdesk tickets with guaranteed admin assignment.
- Tightened factories (Asset/AssetCategory/Division/InternalComment/CrossModuleIntegration/Grade) with typed option arrays, list handling, and proper PHPDoc generics so Larastan understands offsets and return types.
**Result**: Targeted `phpstan analyse` on updated seeders/factories now passes; full run down to ~1,995 errors concentrated in loans/actions and Livewire traits.
**Trace**: Larastan logs 2025-11-19; files: database/seeders/*, database/factories/*

### Session_2025-11-19_Larastan_Loans_Livewire
**Task**: Address Larastan errors in loans Filament actions/resources/tables and shared optimization traits.
**Changes**:

- ExportLoansAction, issuance/return actions now guard nulls, type-enforce enums, and normalize stream handling for CSV export and loan item updates.
- Loan resource schema/table/infolist cleaned to use typed enums, Builder generics, safer date/approval formatting, and bulk action callbacks that validate model instances.
- OptimizedQueries trait now documents Builder generics and safely returns cached counts.
**Result**: Targeted Larastan against loans actions/resources/tables and optimization traits now passes; full run reduced to ~1,942 errors (remaining in audit/users/widgets/services).
**Trace**: phpstan analyse scope loans/tables/actions/traits 2025-11-19.

### Session_2025-11-20_Larastan_Filament_Widgets
**Task**: Clear Filament audit/users/widgets Larastan errors.
**Changes**:

- Audit resources/pages/tables: guarded tooltip state types, added export/download guards, clarified model generics, and aligned navigation badge computation.
- Dashboard widgets (asset availability/calendar, loan/helpdesk stats, unified analytics, resolution time, activity widgets) now use typed counts, numeric guards, and explicit return array shapes to satisfy Larastan.
- User create flow now verifies model instance/temporary password types before sending welcome mail.
**Result**: `phpstan analyse app/Filament/Resources/System app/Filament/Resources/Users app/Filament/Widgets` passes with zero errors.
**Trace**: Larastan Filament widgets/users/audit run 2025-11-20.

### Session_2025-11-21_Larastan_Middleware_Type_Safety
**Task**: Address Larastan middleware/request issues raised in phpstan analyse run (1713 errors baseline).
**Changes**:

- Hardened re-authentication/session timeout middleware with integer guards, safe remaining time math, and swapped the removed `activity()` helper for structured Log entries.
- Validated locale detection inputs (session/cookies/config) and supported locale config arrays to prevent mixed casts.
- Normalized portal activity subject ids/titles and admin rate limit throttle keys for scalar safety; sanitized memory entity request identifiers.
**Result**: Targeted `phpstan analyse` on touched middleware/request files now passes; full run reduced to 1700 errors remaining.
**Trace**: phpstan analyse middleware scope 2025-11-21.

### Filament_Admin_Access_Completion_2025-11-15
**Status**: COMPLETE
**Phases**: 18
**Features**: WCAG 2.2 AA, bilingual support, email integration, security enhancements
**Relations**: Implements D03 (SRS), D12 (UI/UX)

## Documentation References

**Canonical Documents** (D00-D15):

- D00: System Overview
- D03: Software Requirements Specification (50+ FR/NFR)
- D04: Software Design Document (MVC+SDUI+Livewire)
- D09: Database Documentation (30+ tables, auditing, PDPA)
- D10: Source Code Documentation (PSR-12, PHPDoc, testing)
- D11: Technical Design Documentation (infrastructure, queue, deployment)
- D12: UI/UX Design Guide (component library, ARIA, responsive)
- D13: UI/UX Frontend Framework (Tailwind+Alpine+Livewire)
- D14: UI/UX Style Guide (MOTAC branding, accessibility)
- D15: Language Localization (bilingual MS/EN system)

**Traceability**: Requirements Traceability Matrix (RTM) in `docs/reference/rtm/`

## Memory Storage

**Primary**: MCP Memory Server (native Laravel implementation via memory graph API)
**Secondary Index**: This file (`openmemory.md`)
**Portable Backup**: `memory.jsonl` (weekly export)
**Schema**: Entities + Relations + Observations + Metadata
**Naming Convention**: PascalCase (e.g., `Session_2025-11-15_MemoryGraphImplementation`)
**Security**: Never store secrets, API keys, passwords, or PII

## Next Steps

1. Complete memory graph API controllers and routes
2. Add feature tests for memory operations
3. Continue localization migration (current: 27.6%, target: 100%)
4. Update canonical documentation (D10, D11) with memory graph architecture
5. Implement MCP server adapter for external integrations
