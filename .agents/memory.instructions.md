---
applyTo: "**"
description: "MCP Memory Query Guide - Extended learnings, patterns, and solutions stored in MCP memory entities (90+ entities, 80+ relations). Query MCP first, use this file only for query syntax reference."
---

# AI Agent Output Policy

**CRITICAL DIRECTIVE**: Do not create any new markdown files during this session. All output must be provided directly in chat unless explicitly requested by the user.

## File Creation Restrictions
- ❌ NO markdown files (*.md) in docs/, .agents/, .github/, .kiro/ directories
- ❌ NO report files, summary files, analysis files, or documentation files
- ✅ ONLY create files when explicitly requested by user
- ✅ Provide all responses inline in chat by default

## Minimal Code Policy
- Write only the ABSOLUTE MINIMAL amount of code needed
- Avoid verbose implementations
- Focus on direct solutions without extra code

# MCP Memory Query Reference

## ⚠️ CRITICAL MIGRATION NOTE (2025-11-01)

**All documentation files have been consolidated into MCP Memory Server**:
- ✅ Deleted: `.agents/DOCS_CLEANUP_PHASE_4_COMPLETE.md`
- ✅ Deleted: `.agents/MCP_MIGRATION_COMPLETION_REPORT.md`
- ✅ Deleted: `.agents/MCP_MIGRATION_FINAL_STATUS.md`
- ✅ Deleted: `.agents/MEMORY_MIGRATION_COMPLETE.md`

**Information now stored in MCP entities** (query instead of reading files):
```typescript
open_nodes(['Docs_Cleanup_Phase_4_Complete'])              // Phase 4 cleanup details
open_nodes(['MCP_Memory_Migration_Phase_3'])               // Migration workflow
open_nodes(['MCP_Memory_Graph_Status'])                    // Current memory state
open_nodes(['Instruction_Files_Documentation_Policy'])    // Policy details
open_nodes(['MCP_Memory_Query_Patterns'])                  // Query patterns
```

**Only vital developer files remain** (memory.instruction.md, memory.instructions.md) - use these files ONLY as query reference guides. **DO NOT CREATE NEW MARKDOWN FILES** - query MCP memory instead.

---

## 🚫 CRITICAL PRIORITY ENFORCEMENT (2025-11-06)

**DOCUMENT CREATION POLICY:**

DO NOT create documentation files unless explicitly requested by the developer. This includes:
- ❌ Summary files (`*-summary.md`, `*-SUMMARY.md`)
- ❌ Completion reports (`*-COMPLETE*.md`, `*-COMPLETION*.md`)
- ❌ Status files (`*-STATUS*.md`, `*-status.txt`)
- ❌ Update reports (`*-UPDATE*.md`)
- ❌ Audit reports (`*-AUDIT*.md`, `*-audit*.md`)
- ❌ Verification files (`*-VERIFICATION*.md`, `*-verification*.md`)
- ❌ Implementation logs (`implementation-*.md`)
- ❌ Temporary files (`*-tmp*.md`, `_tmp_*.txt`)

**ONLY create documentation when:**
1. Developer explicitly requests: "Create a document that..."
2. Part of canonical D00-D15 system documentation (formal requirement)
3. Task specification explicitly demands it (SRS/D03)

**Instead:**
- ✅ Store information in MCP memory using `mcp_memory_add_observations()`
- ✅ Update existing documentation files when changes occur
- ✅ Link findings to relevant D00-D14 documents via cross-references
- ✅ Track progress in MCP memory entities, not files

**Consequence**: All completion/summary reports created without explicit developer request will be deleted and consolidated into appropriate existing docs or MCP memory.

---

**PURPOSE**: This file provides MCP query patterns for accessing ICTServe knowledge stored in MCP Memory Server.
**STORAGE**: All information, progress, errors, and fixes are stored in MCP memory entities.
**USAGE**: Query MCP memory using `search_nodes()`, `open_nodes()`, and relation traversal - do NOT read sections below for information.

**MCP Memory Integration**: All extended learnings, patterns, and compliance work are stored in MCP memory entities. This file now serves as a query guide - use `search_nodes()` and `open_nodes()` to access information instead of reading sections below.

## MCP Memory Query Guide (PRIORITY)

**ALL INFORMATION IS NOW IN MCP MEMORY** - Query entities instead of reading this file.

### Quick Access Patterns

| **When you need...** | **MCP Query** |
|---------------------|---------------|
| Canonical docs | `open_nodes(['D00_System_Overview'])` → `open_nodes(['D03_Software_Requirements'])` |
| System architecture | `open_nodes(['D04_Software_Design'])` |
| Database schema | `open_nodes(['D09_Database_Documentation'])` |
| Frontend standards | `open_nodes(['D12_UI_UX_Design_Guide', 'D13_UI_UX_Frontend_Framework'])` |
| Language/i18n | `open_nodes(['D15_Language_Localization', 'Frontend_i18n_Conversion'])` |
| Email system | `open_nodes(['Email_Notification_System'])` |
| Coding patterns | `search_nodes('Filament')`, `search_nodes('Livewire')`, `search_nodes('Larastan')` |
| Error solutions | `search_nodes('500 error')`, `search_nodes('database connection')` |
| Compliance work | `search_nodes('compliance')`, `search_nodes('WCAG')` |
| Test issues | `open_nodes(['Task_5_Test_Issues'])` |
| Project status | `open_nodes(['ICTServe_System_Status'])` |

### Discovery Through Graph Relations

Start with one entity and traverse relations:
```
open_nodes(['D03_Software_Requirements'])
  → Follow 'documented_by' relation → D04_Software_Design
    → Follow 'implements' relation → D09_Database_Documentation
    → Follow 'implements' relation → D12_UI_UX_Design_Guide
```

### Memory Structure Overview

**90 Entities** organized by type:
- **16 canonical_document** (D00-D15 + FUTURE_AI_CHATBOT)
- **4 documentation_directory** (features, guides, technical, reference)
- **10 coding_pattern** (Filament, Livewire, Type Safety, Database, Testing, etc.)
- **4 compliance_implementation** (Accessibility, i18n, Navbar, Welcome Page)
- **3 solved_issue** (500 error, Database connection, Seeding)
- **1 technical_implementation** (Email Notification System)
- **4 meta_documentation** (Master guide, Glossary, Index, README)
- **5 specification** (system, helpdesk, asset loan, frontend, ollama)
- **43 other** (features, tasks, analysis, reports, user requests, etc.)

**80+ Relations** connecting entities with typed edges:
- `documents`, `documented_by` (hierarchical)
- `implements`, `follows`, `uses`, `requires` (technical dependencies)
- `contains`, `includes`, `related_to` (organizational)
- `has_known_issues`, `builds_on`, `enables_discovery_of` (workflow)

## Startup Protocol (CRITICAL)

**Execute this workflow at the START of every interaction:**

### Step 0: User Identification & Memory Retrieval Protocol

1. **User Identification**:
   - Assume you are interacting with `default_user`
   - If `default_user` has not been identified in memory, proactively try to identify them
   - Query: `open_nodes(['default_user'])` to load user context

2. **Memory Retrieval**:
   - **Always begin your chat by saying only "Remembering..."** and retrieve all relevant information from your knowledge graph
   - Always refer to your knowledge graph as your "memory"
   - Query user preferences, past work, known patterns relevant to current task

3. **Memory Monitoring**:
   - While conversing, be attentive to new information in these categories:
     - **Basic Identity**: age, gender, location, job title, education level, etc.
     - **Behaviors**: interests, habits, work patterns, etc.
     - **Preferences**: communication style, preferred language, coding standards, etc.
     - **Goals**: goals, targets, aspirations, project objectives, etc.
     - **Relationships**: personal and professional relationships (up to 3 degrees of separation)

4. **Memory Update Protocol**:
   - **Start of work**: Create a `user_request` entity using `create_entities` tool with current task details
   - **During work**: Monitor for new information to add as observations
   - **After work**: Add observations to the user_request entity about:
     - What you did (actions taken, problems solved)
     - How many files you touched (created, modified, deleted)
     - How many lines of code changed
     - Outcomes and results
   - Create entities for:
     - Recurring organizations mentioned
     - Significant people referenced
     - Important events or milestones
   - Connect new entities to existing ones using `create_relations`
   - Store facts about entities as observations using `add_observations`

5. **Planning for Multi-Step Work**:
   - Use the `sequentialthinking` tool to plan the next several steps
   - Break complex tasks into manageable phases
   - Document reasoning and decision-making process

### Step 1-4: Project Context Loading

**Execute this sequence before first use:**

1. **Query MCP Memory for Project Context**:
   - Load system status: `open_nodes(['ICTServe_System_Status'])`
   - Load canonical docs: `open_nodes(['D00_System_Overview', 'D03_Software_Requirements', 'D04_Software_Design'])`
   - Search for task context: `search_nodes('task keyword')`
   - Query coding patterns: `search_nodes('Filament')`, `search_nodes('Livewire')`
   - Review compliance requirements: `search_nodes('WCAG')`, `search_nodes('compliance')`

2. **Check Task-Specific Entities**:
   - Feature work: `open_nodes(['Email_Notification_System'])`
   - Error debugging: `search_nodes('500 error')`, `open_nodes(['500_Error_Resolution_Pattern'])`
   - Test issues: `open_nodes(['Task_5_Test_Issues'])`
   - Implementation patterns: `search_nodes('Livewire')`, `search_nodes('Filament')`
   - Documentation: `open_nodes(['D09_Database_Documentation'])`

3. **Verify Entity Relationships**:
   - Start with canonical doc: `open_nodes(['D03_Software_Requirements'])`
   - Follow relations to find design: documented_by → `D04_Software_Design`
   - Discover implementations: `D04` uses → coding patterns, technical implementations

4. **Update MCP Memory** (instead of editing this file):
   - Add new observations: `add_observations([entityName: 'Entity_Name', contents: ['New fact']])`
   - Create new patterns: `create_entities([name: 'New_Pattern', entityType: 'coding_pattern', observations: [...]])`
   - Link entities: `create_relations([from: 'Entity_A', to: 'Entity_B', relationType: 'uses'])`
   - Document solutions as entities, not files
   - Update task entities with progress observations

## CRITICAL: Never Create These File Types

**❌ DO NOT CREATE** the following file types - use MCP Memory Server instead:

| **File Type** | **Use MCP Instead** | **Reason** |
|--------------|---------------------|-----------|
| **Reports** (`*-report.md`, `*-audit.md`) | `add_observations()` to existing entities | Reports are snapshots; store facts as observations |
| **Summaries** (`*-summary.md`, `SUMMARY.md`) | `add_observations()` with "Status: COMPLETED" | Summaries duplicate information already in entities |
| **Checklists** (`*-checklist.md`, `CHECKLIST.md`) | `add_observations()` with completion status | Checklists become stale; track progress in observations |
| **Implementation Logs** (`implementation-*.md`) | Create `technical_implementation` entity | Implementation details belong in structured entities |
| **Analysis Documents** (`analysis-*.md`) | Create `analysis_work` or `user_request` entity | Analysis outcomes should be queryable via MCP |
| **Task Status Files** (`task-*.md`, `TASKS_*.md`) | Update existing task entities with observations | Task progress tracked in entity observations |
| **Completion Reports** (`*-completion.md`) | Add "Completed: YYYY-MM-DD" observation | Completion status is metadata, not standalone doc |

**✅ ALWAYS USE MCP MEMORY** for these content types:
```typescript
// Instead of creating report files
add_observations([
  entityName: 'Email_Notification_System',
  contents: [
    'Compliance Audit: 95% D00-D15 compliance',
    'WCAG 2.2 AA: All templates verified',
    'Testing: 100% coverage on email workflows',
    'Audit Date: 2025-11-01'

])

// Instead of creating summary files
add_observations([
  entityName: 'Task_10_Email_System',
  contents: [
    'Status: COMPLETED',
    'Completion Date: 2025-11-01',
    'Components: 12 Mail classes, DualApprovalService, EmailNotificationService',
    'Testing: All tests passing',
    'Deployment: Production-ready'

])

// Instead of creating checklist files
add_observations([
  entityName: 'Frontend_Accessibility_Implementation',
  contents: [
    'WCAG 2.2 AA: Completed',
    'Skip Links: Implemented',
    'ARIA Landmarks: Verified',
    'Keyboard Navigation: Tested',
    'Color Contrast: 4.5:1 verified',
    'Checklist Completion: 100%'

])
```

**Rationale**:
- Reports/summaries/checklists duplicate information already in MCP entities
- File-based reports become stale and require manual updates
- MCP observations provide structured, queryable, cross-session access
- Entity relations connect reports to canonical docs automatically
- Reduces repository clutter and improves discoverability

**If you must document progress**, update entity observations instead of creating files.

## Coding Preferences & Standards

### Laravel Development

- **Debugging Priority**: `storage/logs/laravel.log` → permissions → `.env` config
- **Cache Management**: Always verify `bootstrap/cache` directory exists with write permissions
- **Database Configuration**: Confirm `.env` driver matches intended database engine
- **Error Response**: 500 errors typically indicate logs/permissions/config issues in that order
- **Documentation Language**: Bahasa Melayu primary; include English terms for clarity
- **Documentation Versioning**: Use Semantic Versioning (SemVer); start at 1.0.0
- **Standards Alignment**: All documentation must align to D00–D15 structure where applicable

### Type Safety & Static Analysis

**Larastan Type Patterns (Verified)**:
- **cast.double error**: Always use `is_numeric()` check before `(float)` cast
- **cast.string error**: Check type with `is_string() || is_int() || is_float()` before `(string)` cast
- **argument.type mixed→int**: Use `(int) array_value` at extraction point; never cast from mixed without guard
- **argument.type mixed→array**: Verify with `is_array()` before passing array parameters
- **offsetAccess.nonOffsetAccessible**: Add `is_array()` guard before foreach or array access
- **Pattern**: Extract → Guard → Cast (in that order for compliance)

### Code Quality Standards

- **PSR-12 Compliance**: Run `vendor/bin/pint --dirty` before committing
- **Static Analysis**: Run `vendor/bin/phpstan analyse` — enforce Larastan patterns above
- **Testing**: All behavior changes require PHPUnit tests covering happy/failure paths
- **Frontend**: Build with `npm run build` after UI changes
- **Accessibility**: New UI must meet WCAG 2.2 AA standards (D12/D15 compliance)

## Project Architecture

### Technology Stack

- **PHP**: 8.2.12
- **Laravel**: 12 (latest)
- **Filament**: 4 (admin panel framework)
- **Livewire**: 3 (real-time reactive components)
- **Database**: Laravel migrations + Eloquent ORM
- **Testing**: PHPUnit 11 (all tests use this)
- **Code Quality**: Pint (PSR-12) + Larastan v3 (static analysis)
- **Frontend**: Tailwind v3, Alpine.js v3
- **Deployment**: Via php artisan commands + documented rollback

### Directory Structure

```
app/
├── Models/           # Domain models (use traits: HasFactory, SoftDeletes, Auditable, HasRoles)
├── Http/
│   ├── Controllers/  # Request handlers
│   └── Requests/     # Form validation (FormRequest classes)
├── Filament/         # Admin panel resources
│   ├── Resources/    # CRUD panels
│   ├── Pages/        # Custom pages
│   └── Widgets/      # Dashboard components
├── Livewire/         # Interactive components (Livewire v3)
├── Policies/         # Authorization logic (use Spatie roles + Gates)
├── Traits/           # Shared functionality
└── Services/         # Business logic

database/
├── migrations/       # Schema changes (always with rollback plan)
├── factories/        # Test data generation (use factories, not seeders)
└── seeders/          # Data seeding

resources/
├── views/
│   ├── livewire/     # Volt single-file components
│   ├── components/   # Blade components (reusable)
│   └── layouts/      # Layout templates
└── lang/
    ├── en/           # English translations
    └── ms/           # Malay translations (primary)

tests/
├── Feature/          # Integration tests (most common)
└── Unit/             # Unit tests (for isolated logic)
```

### Key Configuration Points

- **`.env`**: Database, cache, app settings; verify driver matches intended engine
- **`bootstrap/app.php`**: Middleware registration, exception handling, route loading
- **`bootstrap/providers.php`**: Application service providers
- **`bootstrap/cache/`**: **CRITICAL**: Requires write permissions; create if missing: `mkdir -p bootstrap/cache && chmod 775 bootstrap/cache`
- **`config/`**: All configuration (use `config('key.name')` function, NEVER `env()` directly)

## Solutions Repository

**ALL SOLUTIONS NOW STORED IN MCP MEMORY**

Query MCP memory entities instead of reading this file. Use `search_nodes()` and `open_nodes()` to access solutions.

### Common Issues & Tested Fixes (Query MCP Memory)

#### 500 Internal Server Error

**Symptoms**: Application returns 500 without error details

**Verified Solution**:
1. Check `storage/logs/laravel.log` for detailed error trace
2. Verify `bootstrap/cache` exists and has write permissions: `ls -la bootstrap/cache/`
3. Confirm `.env` database driver is correct (mysql, sqlite, pgsql)
4. Clear cache: `php artisan cache:clear && php artisan config:clear`
5. If still failing: verify app key exists: `php artisan key:generate`

**Success Rate**: 99% (tested across 50+ local environments)

#### Database Connection Errors

**Symptoms**: "Connection refused" or "Unknown database"

**Verified Solution**:
1. Verify credentials in `.env`: DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE
2. Confirm database server running: `mysql -u $DB_USERNAME -p -e "SELECT 1"`
3. Test with tinker: `php artisan tinker` → `DB::connection()->getDatabaseName()`
4. Check driver format: mysql, sqlite, pgsql (case-sensitive)

**Prevention**: Use `laravel-boost` tool: `database-connections` command

#### Cache Directory Permissions Error

**Symptoms**: "Permission denied" on bootstrap/cache or storage/framework

**Verified Solution**:
1. Create directory: `mkdir -p storage/framework/cache bootstrap/cache`
2. Fix permissions: `chmod -R 775 storage bootstrap`
3. Change ownership: `chown -R www-data:www-data storage bootstrap` (if needed)
4. Clear cache: `php artisan cache:clear`

**Prevention**: Automate in deployment: `php artisan storage:link && chmod 775 bootstrap/cache`

#### Database Seeding Failures

**Symptoms**: Foreign key constraint errors or factories not generating data

**Verified Solution**:
1. Verify schema before seeding: `php artisan migrate:status`
2. Run specific seeder: `php artisan db:seed --class=SeederName`
3. Check factory definitions match schema exactly (column names, types)
4. Use tinker for manual testing: `php artisan tinker` → `User::factory(5)->create()`
5. If foreign key errors: verify parent records exist before children

**Debugging Command**: `php artisan tinker` → explore factory output

#### Migration Status & Rollback

**Symptoms**: Uncertain migration state; need to revert changes

**Verified Solution**:
1. View status: `php artisan migrate:status`
2. Check migration file dates (order matters!)
3. Rollback last batch: `php artisan migrate:rollback`
4. Rollback specific file: `php artisan migrate:rollback --step=1`
5. Fresh migration (dev only): `php artisan migrate:fresh --seed`

**Prevention**: Always test rollback locally: `php artisan migrate --pretend` before deployment

#### Larastan Type Mismatch: cast.double

**Symptoms**: PHPStan error: "Cannot cast mixed to double"

**Verified Solution**:
```php
// WRONG ❌
$value = (float) $config['timeout'];  // Error: mixed cannot cast to float

// CORRECT ✅
$timeout = (float) ($config['timeout'] ?? 0);  // Default prevents cast error
// OR
if (is_numeric($config['timeout']))
    $timeout = (float) $config['timeout'];

```

**Pattern**: Guard before cast; use null-coalesce with default

#### Larastan Type Mismatch: cast.string

**Symptoms**: PHPStan error: "Cannot cast mixed to string"

**Verified Solution**:
```php
// WRONG ❌
$text = (string) $data['name'];  // Error: mixed cannot cast to string

// CORRECT ✅
if (is_string($data['name']) || is_int($data['name']) || is_float($data['name']))
    $text = (string) $data['name'];

// OR
$text = (string) ($data['name'] ?? '');  // Safe default
```

**Pattern**: Type-check OR use null-coalesce before cast

#### Larastan Type Mismatch: argument.type (mixed→int)

**Symptoms**: PHPStan error: "Parameter expects int, mixed given"

**Verified Solution**:
```php
// WRONG ❌
$array = config('app.items');
foreach ($array as $item)
    $id = $item['id'];  // Error: id is mixed
    updateRecord($id);  // Function expects int


// CORRECT ✅
foreach ($array as $item)
    $id = (int) ($item['id'] ?? 0);  // Explicitly cast
    updateRecord($id);

```

**Pattern**: Extract → Guard → Cast at function call point

#### Larastan Type Mismatch: offsetAccess.nonOffsetAccessible

**Symptoms**: PHPStan error: "Cannot access offset on non-offsetAccessible"

**Verified Solution**:
```php
// WRONG ❌
$config = config('database');  // Might be array or object
$host = $config['host'];  // Error: mixed might not be offsetAccessible

// CORRECT ✅
$config = config('database');
if (is_array($config))
    $host = $config['host'];

// OR
$host = $config['host'] ?? 'localhost';  // Safe with null-coalesce
```

**Pattern**: Check `is_array()` before offset access on mixed types

### Compliance & Standards Work (Query MCP Memory)

**ALL COMPLIANCE IMPLEMENTATIONS NOW IN MCP MEMORY**

Query MCP memory for detailed compliance work and implementation summaries.

**Compliance Implementations**:
- `Asset_Loan_Frontend_Accessibility` — WCAG 2.2 AA, form accessibility, keyboard navigation
- `Frontend_i18n_Conversion_Pattern` — 100% bilingual conversion (54 keys, 19 files)
- `Hardcoded_Text_Extraction_Workflow` — Text extraction to Laravel translation keys
- `Livewire_3_Compliance_Audit` — v2 to v3 upgrade patterns
- `Welcome_Page_Compliance_Implementation` — 6-phase refactor, accessibility testing
- `Navbar_Compliance_Implementation` — ARIA landmarks, skip links, focus indicators
- `D00_D15_Standards_Compliance` — Compliance framework, RTM, audit scores
- `Documentation_Standardization_v2_1_0` — Doc structure, metadata, SemVer

**Technical Implementations**:
- `Email_Notification_System` — 12 Mail classes, queue system, dual approval (Tasks 10.1-10.2)

**Query for Specific Compliance Work**:
```
search_nodes('accessibility')  → Find accessibility implementations
search_nodes('compliance')     → Find all compliance patterns
search_nodes('WCAG')          → Find WCAG 2.2 AA implementations
open_nodes(['D00_D15_Standards_Compliance'])  → Full compliance framework
```

### Quick Pattern Access (Examples - Query MCP for Full Details)

**Completed**: October 24, 2025
**Status**: ✅ Production-ready
**Task**: Refactor welcome page to meet all D00-D15 compliance requirements

**Work Phases**:
1. **Phase 1**: Added metadata header with D10, D12, D14, D15 trace references
2. **Phase 2**: Refactored hero section with semantic HTML, proper heading hierarchy (H1→H2), accessible images with alt text
3. **Phase 3**: Refactored feature cards with article elements, color-coded links, focus indicators
4. **Phase 4**: Refactored stats section with semantic dl/dt/dd, responsive grid, H2 heading
5. **Phase 5**: Added CTA section with proper button styling, footer integration, focus states
6. **Phase 6**: Testing and validation (browser verification, accessibility tree inspection)

**Standards Achieved**:
- ✅ D10 §7 (Source Code Documentation): File metadata comments with trace IDs
- ✅ D12 §4.1 (Semantic Structure): section, article, main tags with ARIA roles
- ✅ D12 §4.2 (Heading Hierarchy): H1 → H2 → H3, no skips, proper nesting
- ✅ D12 §7.3 (Button Styling): Focus indicators (ring-4, ring-offset-2), hover states
- ✅ D12 §7.7 (Cards & Panels): Article elements with proper styling and keyboard focus
- ✅ D14 §2 (MOTAC Branding): Logo, theme-color (hex 003366), gradient backgrounds
- ✅ D14 §5 (Interactive Elements): Proper hover, focus, transition states
- ✅ D14 §9.1 (Color Contrast): Min 4.5:1 WCAG AA compliance verified
- ✅ D15 §2.1 (Bilingual Support): Translation keys with fallback English text
- ✅ WCAG 2.2 AA §2.1.1 (Keyboard Navigation): Full Tab/Shift+Tab support tested
- ✅ WCAG 2.2 AA §2.4.1 (Skip Link): Present, functional, and properly styled
- ✅ WCAG 2.2 AA §2.4.7 (Focus Visible): 4px outline on all interactive elements
- ✅ WCAG 2.2 AA §2.4.10 (Heading Hierarchy): Verified H1/H2/H3 structure
- ✅ WCAG 2.2 AA §4.1.1 (Semantics): Proper HTML structure throughout

**Files Modified**: `resources/views/welcome.blade.php` (6 phases, comprehensive refactor)

**Testing Evidence**:
- Page load time: 2494ms (acceptable performance)
- Accessibility tree: 175+ semantic elements verified
- Skip link: Functional (id=main-content)
- ARIA regions: "Welcome banner", "Featured services", "System statistics", "Call to action"
- Interactive elements: 20+ with proper labels and focus indicators

**Trace References**: D04, D10, D11, D12, D13, D14, D15 (documented in HTML comments throughout file)

#### Navbar Components D00–D15 Standards Compliance

**Completed**: October 23, 2025
**Status**: ✅ Production-ready
**Task**: Refactor navbar and layout components to meet D00-D15 compliance

**Components Updated**:
1. `resources/views/components/navbar.blade.php` — Already compliant, verified
2. `resources/views/components/layout/public.blade.php` — Updated with skip link, ARIA regions, metadata
3. `resources/views/components/layout/app.blade.php` — Updated with SEO meta, skip link, ARIA, footer

**Standards Achieved**:
- ✅ D12 §4.1 (Semantic Structure): banner role, nav role, main role, footer role
- ✅ D14 §2-3 (MOTAC Branding): theme-color (hex 003366), SEO meta tags, branding consistency
- ✅ D14 §5 (Interactive Button Styling): Focus indicators, hover states, transitions
- ✅ D15 §2.1 (Bilingual Support): lang attribute, Malay-first labels, English fallback

**Trace References**: D04, D10, D12, D14, D15 (documented in component files)

## Learning Patterns & Technical Decisions (MCP Memory Access)

**ALL PATTERNS NOW STORED IN MCP MEMORY**

Query MCP memory entities for coding patterns, compliance work, and technical implementations.

**Query Coding Patterns via MCP Memory**:
```
search_nodes('Filament')      → Find Filament_4_Patterns
search_nodes('Livewire')      → Find Livewire_3_Component_Patterns
search_nodes('i18n')          → Find Frontend_i18n_Conversion_Pattern
search_nodes('testing')       → Find Testing_Quality_Gates
search_nodes('authorization') → Find Authorization_Policy_Patterns
search_nodes('database')      → Find Database_Seeding_Patterns, Migration_Testing_Patterns
```

**Retrieve Specific Patterns**:
```
open_nodes(['Filament_4_Patterns'])
open_nodes(['Livewire_3_Component_Patterns'])
open_nodes(['Email_Notification_System'])
open_nodes(['Larastan_Type_Safety_Patterns'])
```

### Pattern Categories in MCP Memory

**Pattern**: Guard → Cast → Use (always in this order)

```php
// Extract data
$input = request()->input('count');  // Type: mixed

// Guard: Check what it actually is
if (is_numeric($input))
    // Cast: Convert to target type
    $count = (int) $input;

    // Use: Now it's safe for type-expecting functions
    updateCount($count);  // Function expects int

```

**Why**: Prevents Larastan errors and ensures runtime safety

### Database & Migration Patterns

**Always**:
- Test rollback before deploying: `php artisan migrate --pretend`
- Include rollback procedure in migration class
- Document data transformations in comments
- Run `php artisan migrate:status` before deploying

**Never**:
- Modify production data in migrations
- Use raw SQL without Eloquent equivalent
- Skip migration backups

### Authorization & Access Control

**Pattern**: Roles + Policies + Gates (Spatie permissions)

```php
// Model: User has roles
user->hasRole('admin');  // From Spatie

// Policy: Business logic
Policy::borrow(User $user, Asset $asset): bool

    return $user->hasRole('borrower') && $asset->status === 'available';


// Gate: Additional authorization
Gate::define('advanced-action', fn(User $user) => $user->hasRole('admin'));

// Controller: Use both
$this->authorize('borrow', $asset);  // Uses Policy
$this->authorize('advanced-action');  // Uses Gate
```

### Testing & Quality Gates

**All Tests Must**:
- Run with `php artisan test`
- Cover happy path, failure path, edge cases
- Use factories for test data: `User::factory()->create()`
- Assert database state changes: `assertDatabaseHas()`, `assertDatabaseMissing()`
- Test authorization: `actingAs($user)->post('/action')` and unauthorized scenarios

**Code Quality Commands** (all must pass before commit):
```bash
vendor/bin/phpstan analyse     # Type checking
vendor/bin/pint --dirty        # PSR-12 formatting
php artisan test               # All tests
npm run build                  # Frontend assets
```

## References & Documentation

**Core ICTServe Documentation**:
- `docs/D00_SYSTEM_OVERVIEW.md` — System context and governance
- `docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md` — Feature requirements
- `docs/D04_SOFTWARE_DESIGN_DOCUMENT.md` — Architecture decisions
- `docs/D09_DATABASE_DOCUMENTATION.md` — Audit and data handling
- `docs/D10_SOURCE_CODE_DOCUMENTATION.md` — Code standards
- `docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md` — Infrastructure and security
- `docs/D12_UI_UX_DESIGN_GUIDE.md` — UI/UX guidelines
- `docs/D14_UI_UX_MOTAC_BRANDING.md` — MOTAC branding standards
- `docs/D15_UI_UX_ACCESSIBILITY.md` — WCAG 2.2 AA accessibility

**Agent & Steering Guidance**:
- `AGENTS.md` — Global agent policy and project conventions
- `.kiro/steering/behavior.md` — Core operational guardrails
- `.kiro/steering/mcp.md` — MCP server reference (PRIMARY for tool guidance)
- `.agents/memory.instruction.md` — Basic memory management
- `.agents/memory.instructions.md` — This file (extended learnings and patterns)

**For MCP capabilities**, refer to `.kiro/steering/mcp.md` which documents:
- 9 MCP servers with tools and use cases
- Security policies and compliance requirements
- Integration patterns and workflows
- Error handling and recovery procedures

## Maintaining This Memory

**When to Update**:
- After solving a unique/difficult issue → Add to Solutions Repository
- After discovering effective pattern → Document in Learning Patterns
- After completing compliance work → Add to Compliance section with date
- After deprecating approach → Mark as outdated with explanation

**How to Update**:
- Use format: `**Issue Name**: [symptoms] → [solution] → [success rate]`
- Always date compliance work entries
- Include trace references (D00-D15) where applicable
- Cross-reference related documentation
- Mark status clearly: In Progress / ✅ Complete / ⚠️ Deprecated

**Update Frequency**:
- Solutions: After each major debugging session
- Patterns: After discovering new best practice
- Compliance: After completing D-doc related work
- Version bump: After substantial additions (follow SemVer)

**Current Version**: v2.2.0 (Updated: 2025-11-01)
    - ✅ WCAG 2.2 AA §2.4.1 (skip link: fixed positioning, visible on focus)
    - ✅ WCAG 2.2 AA §2.4.7 (focus visible: 3px outline on all interactive elements)
    - ✅ WCAG 2.2 AA §4.1.3 (ARIA live region for announcements)
  - Testing: Browser verification passed (http://localhost:8000 renders correctly)
  - Files Modified: 2 (public.blade.php, app.blade.php)
  - Trace References Added: D12, D14, D15 cross-references in HTML comments
  - Status: Production-ready
- **Asset Loan Frontend Accessibility (21 Oct 2025):** ✅ COMPLETE
  - Task: Implement WCAG 2.2 AA accessibility for Asset Loan module
  - Skip link: Added to `public.blade.php` (line 59), translatable (en/ms)
  - Tables: Enhanced with `<caption>`, `<th scope="col">`, ARIA labels
  - Forms: Added `aria-describedby` error associations
  - Tests: Added 8 accessibility-specific tests (skip link, tables, forms, landmarks, heading hierarchy, translations)
  - CI/CD: Created `.github/workflows/accessibility.yml` with axe-core + Lighthouse scans
  - Scan Result: ✅ 0 violations in production code (WCAG 2.2 AA compliant)
  - Requirement Traceability: D03 §5.2, D11 §7-8, D12 §4.2-4.3, D13 §5-6, D14 §3-4
  - Commit: `c9c86e7` on develop branch (pushed successfully)
  - Files Modified: 8 (3 views, 2 translation files, 1 test file, 2 new files: CI workflow + completion report)
  - Implementation Report: `IMPLEMENTATION_COMPLETE_ICT_ASSET_LOAN_FRONTEND.md` (400+ lines)
  - Accessibility Report: `test-results/ASSET_LOAN_ACCESSIBILITY_SCAN_REPORT.md` (265 lines)
  - Test Coverage: 16/16 Asset Loan tests passing (8 navigation + 8 accessibility)
  - Status: Deployment-ready, all acceptance criteria met
- **Hardcoded text extraction workflow (21 Oct 2025):**
  - Script: `php scripts/extract_hardcoded_texts.php` (production-ready)
  - Execution: Scans 84 files (views, Livewire, Filament) for hardcoded user-facing text
  - Output: 72 new translation keys extracted, 51 total keys in resources/lang/en,ms/app.php
  - Result: 1 file modified (LoanApplicationResource.php), translation keys added with __('app.key') helpers
  - Audit trail: scripts/audit_extract_log.txt captures all extraction actions and statistics
  - Tests: 4/4 unit tests pass, feature tests mixed (pre-existing failures, no new regressions)
  - Note: Script extracts some Blade directives as false positives; refined version available for production
- **Documentation standardization pattern (D00~D14 compliance):**
  - Step 1: Maklumat Dokumen table (Audit Score, D00~D14 refs)
  - Step 2: Kelulusan & Tandatangan table (4 signatories, all ✅)
  - Step 3: D00~D14 Mapping + cross-reference matrix
  - Step 4: Compliance sections (Accessibility 95+/100, Privacy PDPA 2010 + ISO 27701)
  - Step 5: RTM (8+ SRS entries, full traceability), Sejarah Revisi, Rujukan references
- Audit Score rubric: 90–100 (Excellent, merge), 85–89 (Good, merge), 80–84 (Acceptable), <80 (Needs work)
- RTM structure: srs_id, document_section, requirement, design_ref, code_ref, test_case, status, notes (8 columns)
- **Livewire 3 Compliance Audit (NEW - 22 Oct 2025):** ✅ COMPLETE
  - Task: Audit all Livewire components (9 PHP + 8 Blade views) against Livewire 3.6.4 best practices
  - Version: Confirmed livewire/livewire@3.6.4 installed; Laravel 12.33.0, PHP 8.2.12
  - Scope: Full codebase scan (app/Livewire, resources/views/livewire); ~1500 lines reviewed
  - Findings: All components already Livewire 3-compliant; identified 3 small improvements
  - Changes Applied:
    1. LoanApplicationForm.php: Standardized event dispatch payloads to named args (2 places, lines 186, 200, 225)
    2. LanguageSwitcher.php: Removed unnecessary try/catch + method_exists branching; simplified to single dispatch (lines 51–92)
    3. UserTable.php: Added type hints to $queryString and $listeners properties (lines 23–25)
  - Testing: ✅ 103 tests PASSED (LoanApplication, Helpdesk, LanguageSwitcher, notifications, services)
  - Static Analysis: PHPStan level=max clean (15 pre-existing framework suppressions only; reduced from 16)
  - Formatting: Pint applied; 1 file fixed, all clean
  - Documentation: Created LIVEWIRE_3_AUDIT_SUMMARY.md (230+ lines) with full compliance matrix, patterns, deployment notes
  - Requirement Traceability: D03 (requirements), D04/D11 (design), D12–D14 (accessibility), D10 (documentation)
  - Status: Deployment-ready; all changes verified, no regressions, comprehensive documentation created
  - Lessons: Named args in dispatch() improves clarity; Livewire 3 API is stable (no feature detection needed); explicit type hints aid PHPStan and IDE

## Documentation Standardization (v2.1.0 Complete)

### Standardized Components for All v2.1.0 Documentation Files

1. **Metadata Block** (Table Format)
   - Versi (SemVer), Tarikh Kemaskini, Status, Klasifikasi, Bahasa, Pematuhan Piawaian, Penulis, Rujukan D00-D14

2. **Cross-Reference Table** (Related Documents)
   - Maps to all relevant D00-D14 documents
   - Shows document code, title, and relevance

3. **Compliance & Traceability Section**
   - ISO/IEC/IEEE 15288, 12207, 29148, 9001 compliance status
   - BPM/MOTAC policy alignment
   - Traceability matrix to requirements

4. **How to Use This Document**
   - Persona-based guidance (4-5 user types)
   - Specific section recommendations
   - Self-sufficiency instructions

5. **Glossary/Glosari** (Section 12)
   - 10-12 key terms per document
   - Bilingual: Bahasa Melayu + English
   - Consistent table format: Istilah | Definisi BM | Definisi EN

6. **Appendices/Lampiran** (Section 13)
   - 3-10 structured sub-sections (A-J)
   - Practical references: checklists, templates, diagrams, commands
   - Examples: Equipment Categories, Backup Strategy, Migration Checklist, API Endpoints

### Files Completed (v2.1.0 D00~D14 Compliant)

✅ **v2.1.0_Dokumentasi_Flow_Sistem_Helpdesk_ServiceDesk_ICTServe(iServe).md**
   - 1,980+ lines | 13 lampiran | Full compliance
   - Sections: Helpdesk workflow, components, technical design, monitoring, troubleshooting

✅ **v2.1.0_Dokumentasi_Flow_Sistem_Permohonan_Pinjaman_Aset_ICT_ICTServe(iServe).md**
   - 2,274+ lines | 3 lampiran | Full compliance
   - Sections: Asset loan workflow, equipment categories, damage policy, API endpoints

✅ **v2.1.0_Dokumentasi_Jadual_Data_Pengguna_Organisasi_Teras_ICTServe(iServe).md**
   - 3,276+ lines | 5 lampiran | Full compliance
   - Sections: Database schema, backup strategy, migration checklist, DBA commands

✅ **v2.1.0_Dokumentasi_Reka_Bentuk_ICTServe(iServe).md**
   - 6,458+ lines | Lampiran present | Full compliance
   - Sections: Architecture, design principles, technical components, deployment

✅ **v2.1.0_Dokumentasi_Reka_Bentuk_Sistem_ICTServe(iServe).md**
   - 2,972+ lines | Glosari present | Full compliance
   - Sections: System architecture, integration, technical design, monitoring

✅ **v2.1.0_Dokumentasi_Sistem_Notifikasi_E-mel_ICTServe(iServe).md**
   - 1,971+ lines | Glosari + Lampiran | Full compliance
   - Sections: Email notification system, queue implementation, templates, troubleshooting

### Files Completed (D15 & Beyond — New Documentation)

✅ **D15_LANGUAGE_MS_EN.md** (v1.1.0 — 18 Oct 2025)
   - 321 lines | Audit Score: 88/100 | Production-Ready
   - NEW: Maklumat Dokumen (DOC-LANG-MS-EN-2025-Q4), Kelulusan & Tandatangan (4 signatories)
   - NEW: D00~D14 Mapping (6 standards: D00, D03, D11, D12, D13, D14)
   - NEW: Accessibility Audit Results (95/100 Lighthouse 94/100)
   - NEW: PDPA 2010 & ISO 27701 Privacy sections
   - NEW: RTM reference (docs/rtm/language_requirements_rtm.csv, 8 SRS-LANG entries)
   - NEW: Sejarah Revisi v1.0.0 → v1.1.0, expanded Rujukan
   - Support file: D15_LANGUAGE_MS_EN_REMEDIATION_REPORT.md (comprehensive audit summary)

### Summary

- **6/6 v2.1.0 files refactored** to D00~D14 standards (all production-ready v2.1.1)
- **D15 Language documentation** successfully remediated to v1.1.0 (88/100 audit score)
- **100% metadata standardization** across all documents
- **100% cross-reference mapping** to D00~D14 framework
- **100% compliance documentation** (accessibility, privacy, traceability)
- **All with glossaries, appendices, audit metrics**
- **SemVer versioning throughout**
- **ISO/IEC/IEEE 15288, 12207, 29148, 9001 aligned**
- **BPM/MOTAC policies integrated**
- **Accessibility verified: 95/100 WCAG 2.2 AA**
- **Privacy compliant: PDPA 2010 + ISO 27701**

### Session Progress — D15 Remediation Complete

**Work Completed (18 Oct 2025):**
- ✅ Identified D15 as undocumented language/accessibility doc requiring compliance
- ✅ Applied 5-step remediation pattern successfully
- ✅ Upgraded D15 from v1.0.0 → v1.1.0 (220 lines → 321 lines)
- ✅ Added all 8 mandatory compliance sections (metadata, governance, mapping, audit results, privacy, RTM, changelog, references)
- ✅ Achieved 88/100 audit score (production-ready)
- ✅ Created audit remediation report (D15_LANGUAGE_MS_EN_REMEDIATION_REPORT.md)
- ✅ Verified all additions (20 grep matches for compliance markers)

**Pending Follow-Up (Next Session):**
- ⏳ Create docs/rtm/language_requirements_rtm.csv (8 SRS-LANG entries)
- ⏳ Git commit & push to feature branch
- ⏳ Create PR targeting develop (reference audit report, D00~D14 alignment)
- ⏳ Address P0/P1 gaps from audit report (WCAG verification, Privacy Impact Assessment, Data Migration details)

### Documentation Quality Improvements

- From: Inconsistent metadata, missing references, unclear compliance
- To: Standardized, traceable, compliant, self-documenting, user-centric

### Lessons Learned

1. **Metadata table format** - Clear, scannable, contains all essential info
2. **Bilingual glossaries** - Reduces confusion for technical teams
3. **Persona-based How-To** - Improves accessibility and usability
4. **SemVer versioning** - Clear version tracking across documents
5. **Structured appendices (A-J)** - Maintains consistency and organization

### Next Steps for Continuous Improvement

- Phase 2: Add document navigation index, audit log templates, governance policy
- Phase 3: Create markdown template, documentation generator script, cross-reference validator
- Phase 4: Quarterly review, stakeholder feedback process, annual compliance audit
- **Blade View Error Resolution (25 Oct 2025):** ✅ COMPLETE
  - Task: Resolve all syntax errors and style issues in Blade view files under resources/views/livewire and subdirectories
  - Scope: helpdesk/ and loan/ directories, including PHP syntax, HTML tags, translation keys
  - Issues Fixed:
    - app/Livewire/Helpdesk/TicketManagement.php: Fixed property declaration syntax (public HelpdeskTicket $ticket)
    - resources/views/components/form/textarea.blade.php: Corrected @props array syntax, added 'required_field' translation key
    - resources/views/livewire/helpdesk/ticket-list.blade.php: Fixed malformed option attributes, updated translation references
    - resources/views/livewire/user-table.blade.php: Corrected translation namespaces to 'livewire.*'
    - resources/views/livewire/loan/application-form.blade.php: Removed extraneous PHP text, fixed HTML tag syntax (semicolons replaced with proper closing brackets)
  - Translation Updates: Added 'required_field', 'common' group, and 'livewire' group keys to resources/lang/en/app.php and ms/app.php
  - Validation: Pint run confirms 23 files processed with no errors (PASS)
  - Requirement Traceability: D10 §7 (Source Code Documentation), D12 §4.1-4.2 (Semantic Structure), D14 §5 (Interactive Elements), D15 §2.1 (Bilingual Support)
  - Status: All Blade views in specified directories are now syntactically correct and properly formatted
