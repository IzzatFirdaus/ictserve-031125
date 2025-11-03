---
applyTo: "**"
description: "MCP Memory Query Guide - AI agent memory management using MCP Memory Server (90+ entities, 80+ relations). Query MCP first for all project context."
---

# Agent Memory & Context Management (MCP)

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

**Only vital developer files remain** (memory.instruction.md, memory.instructions.md) - use these files ONLY as query reference guides.

---

## Purpose & Scope

**CRITICAL**: All project knowledge is stored in MCP Memory Server entities. This file provides query patterns for accessing that knowledge. **Query MCP first** - do not read sections below for information, use them only as query syntax examples.

This document defines how to leverage MCP Memory Server for AI agents working in ICTServe. The memory server contains 90+ entities with 80+ relations covering:
- Canonical documentation (D00-D15)
- Coding patterns (Filament, Livewire, Type Safety)
- Solved issues (500 errors, database, seeding)
- Compliance implementations (WCAG, i18n)
- Technical specifications
- Feature implementations

**Usage Model**: Query entities using `search_nodes()` and `open_nodes()` → Apply knowledge → Update with `add_observations()` when you discover new patterns.

## Core Purpose

**MCP Memory Server stores ALL project knowledge** - this file is a query reference only.

The memory system provides:
- **Context Preservation**: 90+ entities with canonical docs, patterns, solutions, specs
- **Cross-Session Continuity**: Knowledge graph persists between sessions
- **Solution Repository**: Solved issues with resolution patterns (query: `search_nodes('error')`)
- **Pattern Library**: Coding patterns for Filament, Livewire, Testing (query: `search_nodes('Filament')`)
- **Compliance Tracking**: WCAG, i18n implementations (query: `search_nodes('compliance')`)

**Query First, Update After**: Always query existing entities before creating new ones. Update observations when you discover new information.

**MCP Memory Integration**: All project knowledge is stored in MCP memory entities accessible via `search_nodes()`, `open_nodes()`, and relation traversal. This file references memory entities instead of duplicating documentation.

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

1. **Query MCP Memory for Context**:
   - Use `search_nodes('keyword')` to find relevant entities for current task
   - Load system status: `open_nodes(['ICTServe_System_Status'])`
   - Load canonical docs relevant to task: `open_nodes(['D03_Software_Requirements', 'D04_Software_Design'])`
   - Query related patterns: `search_nodes('Filament')`, `search_nodes('Livewire')`, `search_nodes('accessibility')`

2. **Review Known Solutions**:
   - Query `search_nodes('error')` or `search_nodes('500')` for debugging patterns
   - Use `open_nodes(['500_Error_Resolution_Pattern'])` for specific error solutions
   - Check task entities: `open_nodes(['Task_5_Test_Issues'])` for known problems
   - Avoid repeating previously failed approaches

3. **Load Project Architecture**:
   - Query `open_nodes(['D04_Software_Design'])` for architecture overview
   - Check database schema: `open_nodes(['D09_Database_Documentation'])`
   - Review tech stack and coding patterns from relevant entities
   - Follow relations to discover connected entities

4. **Initialize Context**:
   - Log task start time and scope in user_request entity
   - Create task entity if working on novel problem
   - Create new entities for novel solutions or patterns

## MCP Memory Query Examples (PRIORITY)

**Use these query patterns instead of reading file sections below:**

| **Task** | **MCP Query** | **Returns** |
|---------|---------------|-------------|
| System overview | `open_nodes(['ICTServe_System_Status'])` | Current project status |
| Architecture | `open_nodes(['D04_Software_Design'])` | System design doc entity |
| Database schema | `open_nodes(['D09_Database_Documentation'])` | Database documentation |
| Frontend patterns | `open_nodes(['D13_UI_UX_Frontend_Framework'])` | UI/UX framework guide |
| Error solutions | `search_nodes('500 error')` | 500_Error_Resolution_Pattern entity |
| Filament patterns | `search_nodes('Filament')` | Filament_4_Resource_Patterns entity |
| Livewire patterns | `search_nodes('Livewire')` | Livewire_3_Component_Patterns entity |
| Accessibility | `search_nodes('WCAG')` | Accessibility_WCAG_Implementation entity |
| i18n work | `search_nodes('i18n')` | Frontend_i18n_Conversion entity |
| Email system | `open_nodes(['Email_Notification_System'])` | Tasks 10.1-10.2 implementation |
| Test issues | `open_nodes(['Task_5_Test_Issues'])` | Known test problems + fixes |

**Graph Traversal**: Start with one entity and follow relations:
```typescript
open_nodes(['D03_Software_Requirements'])
  → Follow 'documented_by' → D04_Software_Design
  → Follow 'implements' → Frontend_i18n_Conversion
  → Follow 'uses' → Livewire_3_Component_Patterns
```

## CRITICAL: Never Create These File Types

**❌ DO NOT CREATE** the following file types - use MCP Memory Server instead:

| **File Type** | **Use MCP Instead** | **Why** |
|--------------|---------------------|---------|
| Reports (`*-report.md`) | `add_observations()` | Reports are snapshots; facts go in observations |
| Summaries (`*-summary.md`) | `add_observations()` with status | Summaries duplicate entity information |
| Checklists (`*-checklist.md`) | `add_observations()` with completion | Checklists become stale; track in observations |
| Implementation logs (`implementation-*.md`) | Create `technical_implementation` entity | Structured entity > unstructured file |
| Analysis docs (`analysis-*.md`) | Create `analysis_work` entity | Makes analysis queryable and discoverable |
| Task status (`task-*.md`) | Update task entity observations | Progress belongs in entity metadata |

**Example - Replace Report File with MCP Update**:
```typescript
// ❌ OLD WAY: Create docs/reports/email-compliance-report.md
// ✅ NEW WAY: Update MCP entity
add_observations([
  entityName: 'Email_Notification_System',
  contents: [
    'Compliance Audit: 95% D00-D15 adherence',
    'WCAG 2.2 AA: All templates verified',
    'Audit Date: 2025-11-01',
    'Issues: 3 minor branding inconsistencies (fixed)',
    'Recommendation: Quarterly compliance reviews'

])
```

**Rationale**: Files duplicate MCP entities, become stale, aren't queryable, and clutter the repository.

## Coding Preferences & Standards

**NOTE**: These sections remain for backward compatibility. **Query MCP entities for current standards:**
- Query `search_nodes('coding pattern')` for up-to-date patterns
- Query `open_nodes(['D04_Software_Design'])` for architecture rules
- Query `search_nodes('test')` for testing patterns

### Laravel Development

- **Debugging**: Check `storage/logs/laravel.log` for error details and stack traces
- **Permissions**: Always verify `bootstrap/cache` directory permissions for 500 errors
- **Configuration**: Confirm `.env` database settings match intended driver (MySQL, SQLite, etc.)
- **Error Resolution**: Check logs → permissions → config in that order

### Documentation Standards

- **Language**: Bahasa Melayu primary; include English terms for clarity where needed
- **Versioning**: Use Semantic Versioning (SemVer) across all documentation artifacts (start at 1.0.0)
- **Structure**: Align content to D00–D15 framework where applicable
- **Traceability**: Reference requirement IDs in all significant changes

### Code Quality

- **PSR-12 Compliance**: Run `vendor/bin/pint` before committing changes
- **Static Analysis**: Run `vendor/bin/phpstan analyse` to catch potential issues
- **Testing**: All behavior changes require new or updated PHPUnit tests
- **Build**: Ensure frontend compiles with `npm run build` for UI changes

## Project Architecture

### Directory Structure

```
app/
├── Models/                    # Domain models with traits
├── Http/Controllers/          # Request handlers
├── Filament/                  # Admin panel resources
├── Livewire/                  # Interactive components
├── Policies/                  # Authorization logic
└── Traits/                    # Shared functionality

database/
├── migrations/                # Schema changes
├── factories/                 # Test data generation
└── seeders/                   # Data seeding

resources/
├── views/livewire/           # Volt components
└── lang/                      # Translations (MS/EN)

tests/
├── Feature/                   # Integration tests
└── Unit/                      # Unit tests
```

### Key Configuration Files

- **`.env`**: Database, cache, and application settings
- **`bootstrap/app.php`**: Middleware and exception registration
- **`bootstrap/providers.php`**: Service provider bootstrap
- **`bootstrap/cache/`**: Application cache storage (requires write permissions)
- **`config/`**: All configuration files (use `config()` function, not `env()` directly)

### Technology Stack

- **PHP**: 8.2.12
- **Laravel**: 12 (latest version)
- **Filament**: 4 (admin panel)
- **Livewire**: 3 (real-time components)
- **Database**: Laravel migrations + Eloquent ORM
- **Testing**: PHPUnit 11
- **Code Quality**: Pint (PSR-12) + Larastan (static analysis)

## Solutions Repository

### Accessing Solutions via MCP Memory

**Query for Common Issues**:
```
search_nodes('500 error')           → Find 500_Error_Resolution_Pattern
search_nodes('database connection') → Find Database_Connection_Error_Resolution
search_nodes('seeding')             → Find Seeding_Failures_Resolution, Database_Seeding_Patterns
search_nodes('migration')           → Find Migration_Testing_Patterns
search_nodes('authorization')       → Find Authorization_Policy_Patterns
search_nodes('Blade')               → Find Blade_View_Error_Resolution
```

**Retrieve Specific Solutions**:
```
open_nodes(['500_Error_Resolution_Pattern'])
open_nodes(['Database_Connection_Error_Resolution'])
open_nodes(['Seeding_Failures_Resolution'])
```

**Solutions are stored as MCP memory entities with**:
- Issue description
- Solution steps (verified and tested)
- Success rate
- Prevention strategies
- Related commands
- Common causes
- Related documentation entities

**Note**: All solution entities link to relevant canonical documents (D09, D10, D11) via relations.

## Common Errors & Resolutions

**For detailed solutions, query MCP memory entities**:
- `500_Error_Resolution_Pattern` — Internal server errors
- `Database_Connection_Error_Resolution` — Database connection issues
- `Seeding_Failures_Resolution` — Foreign key and factory errors
- `Blade_View_Error_Resolution` — Blade syntax and component errors
- `Migration_Testing_Patterns` — Safe migration testing workflow
- `Database_Seeding_Patterns` — Seeding best practices

### Quick Reference (Query MCP for Full Details)

### IDE False Positives (Safe to Ignore)

**Intelephense P1013 "Undefined method 'user'":**
- When: Using `auth()->user()` in Laravel code
- Cause: Intelephense doesn't always recognize Laravel facades correctly
- Resolution: Safe to ignore - this is valid Laravel authentication code
- Workaround: Can add PHPDoc `@var \App\Models\User $user` if desired for IDE hints

**PHP "Unreachable code detected" in match expressions:**
- When: Using PHP 8.0+ match expressions with multiple cases before `default`
- Cause: PHP language server incorrectly flags cases after first case
- Resolution: Safe to ignore - match expression syntax is correct
- Example: `match($status)  'open' => 'value1', 'closed' => 'value2', default => 'default' ` is valid

**Laravel Extension translation warnings:**
- When: Translation keys exist but IDE hasn't indexed them
- Cause: VS Code/Intelephense cache not refreshed after new translation files
- Resolution: Reload VS Code window or restart Intelephense language server
- Workaround: Clear Laravel cache with `php artisan cache:clear`

#### 500 Error (Internal Server Error)

**Steps**:
1. Check `storage/logs/laravel.log` for error details
2. Verify `bootstrap/cache` directory exists and has write permissions: `ls -la bootstrap/cache/`
3. Confirm `.env` database settings (driver, host, credentials)
4. Clear cache: `php artisan cache:clear && php artisan config:clear`

**Related**: Laravel error handling in `bootstrap/app.php`

#### Database Connection Error

**Steps**:
1. Verify database driver in `.env` (mysql, sqlite, pgsql, etc.)
2. Check database credentials (DB_HOST, DB_USERNAME, DB_PASSWORD)
3. Ensure database server is running
4. Test connection: `php artisan tinker` → `DB::connection()->getDatabaseName()`

**Prevention**: Use `database-connections` tool from laravel-boost MCP server

#### Cache Directory Permissions Error

**Steps**:
1. Create missing cache directory: `mkdir -p storage/framework/cache`
2. Fix permissions: `chmod -R 775 storage/framework/cache`
3. Clear cache: `php artisan cache:clear`

**Note**: Re-run if `bootstrap/cache/` is removed

#### Seeding Issues

**Steps**:
1. Create missing directory: `mkdir -p storage/framework/cache`
2. Run specific seeder: `php artisan db:seed --class=SeederName`
3. Check for foreign key constraints preventing data insertion
4. Verify factory definitions match database schema

**Debugging**: Use `php artisan tinker` to manually test factory generation

#### Migration Status Check

**Steps**:
1. View all migrations: `php artisan migrate:status`
2. Rollback last batch: `php artisan migrate:rollback`
3. Migrate specific file: `php artisan migrate --path=database/migrations/2025_01_01_create_table.php`

**Prevention**: Test migrations locally before production deployment

## Documentation Organization (MCP Memory Navigation)

**Accessing Documentation via MCP Memory**:

**Canonical Documents (D00-D15)** — Query via `open_nodes()`:
```
D00_System_Overview              → System context, stakeholders, governance
D01_Development_Plan             → Agile methodology, quality gates, team roles
D02_Business_Requirements        → Business goals, success criteria
D03_Software_Requirements        → 50+ FR/NFR, guest-only architecture, email workflows
D04_Software_Design              → MVC+SDUI+Livewire architecture, patterns
D05_Data_Migration_Plan          → Migration strategy, ETL process
D06_Data_Migration_Specification → Migration scripts, data mapping
D07_System_Integration_Plan      → External system integration patterns
D08_System_Integration_Specification → API endpoints, webhooks
D09_Database_Documentation       → 30+ tables, relationships, auditing
D10_Source_Code_Documentation    → PSR-12, PHPDoc, testing standards
D11_Technical_Design             → Infrastructure, queue config, deployment
D12_UI_UX_Design_Guide           → Component library, ARIA, responsive design
D13_UI_UX_Frontend_Framework     → Tailwind+Alpine+Livewire stack
D14_UI_UX_Style_Guide            → MOTAC branding, accessibility, dark mode
D15_Language_Localization        → Bilingual MS/EN, translation system
```

**Documentation Directories** — Query via `open_nodes()`:
```
Docs_Features_Directory    → 5 feature implementation guides
Docs_Guides_Directory      → 3 system workflow diagrams
Docs_Technical_Directory   → Email system, PHPStan, task checklists
Docs_Reference_Directory   → 8 subdirectories (frontend, helpdesk, openapi, reports, rtm, summary, testing, versions)
```

**Meta Documentation** — Query via `open_nodes()`:
```
ICTServe_System_Documentation_Master → Master documentation guide
Project_Glossary                     → Terminology and acronyms
Documentation_Index                  → Comprehensive navigation
Project_README                       → Quick start guide
```

**Discovery Patterns**:
- Start with `D00_System_Overview` for context
- Use `D03_Software_Requirements` for feature requirements
- Follow relations: `open_nodes(['D03_Software_Requirements'])` → traverse `documented_by` relation → find `D04_Software_Design`
- Search by topic: `search_nodes('email')` → find `Email_Notification_System`
- Search by technology: `search_nodes('Filament')` → find `Filament_4_Patterns`

**Key Locations for Common Questions** (Query These Entities):
- Email notifications: `Email_Notification_System` (links to D07, D04)
- Dual approval: `Email_Notification_System` (Tasks 10.1-10.2 implementation)
- Frontend standards: `D12_UI_UX_Design_Guide`, `D13_UI_UX_Frontend_Framework`, `D14_UI_UX_Style_Guide`
- Database schema: `D09_Database_Documentation`
- System architecture: `D00_System_Overview`, `D04_Software_Design`
- Accessibility: `D12_UI_UX_Design_Guide`, `Asset_Loan_Frontend_Accessibility`, `Welcome_Page_Compliance_Implementation`
- Localization: `D15_Language_Localization`, `Frontend_i18n_Conversion_Pattern`, `Hardcoded_Text_Extraction_Workflow`

## Learning Patterns & Technical Decisions (MCP Memory Access)

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

**Coding Patterns**:
- `Filament_4_Patterns` — Unified action namespace, custom pages, widgets, SoftDeletes
- `Livewire_3_Component_Patterns` — #[Reactive], #[Computed], wire directives, testing
- `Blade_View_Error_Resolution` — Blade syntax debugging, component paths, cache clearing
- `Database_Seeding_Patterns` — Factory usage, seeding order, FK constraints
- `Migration_Testing_Patterns` — Safe migration testing, rollback verification
- `Authorization_Policy_Patterns` — Policy structure, gates, Spatie roles
- `Testing_Quality_Gates` — Coverage requirements, test types, CI/CD checks
- `Larastan_Type_Safety_Patterns` — Guard→Cast→Use pattern, PHPStan compliance

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

### Quick Pattern Access (Examples)

**Email Notification System (Tasks 10.1-10.2)** — Query: `open_nodes(['Email_Notification_System'])`
- 12 Mail classes with ShouldQueue interface
- EmailNotificationService + DualApprovalService
- 5 email approval routes + 3 portal approval routes
- 11 WCAG 2.2 AA compliant templates (bilingual)
- Queue jobs with exponential backoff

**Filament 4 Patterns** — Query: `open_nodes(['Filament_4_Patterns'])`
- Unified action namespace: `Filament\Actions\Action`
- BulkAction namespace: `Filament\Actions\BulkAction`
- Custom Pages: `getHeaderActions()` with `->form()` modals
- Resources: `Filament\Schemas\Schema` for form signatures
- SoftDeletes trait for logical deletion

**Livewire 3 Patterns** — Query: `open_nodes(['Livewire_3_Component_Patterns'])`
- #[Reactive] for state variables
- #[Computed] for derived values
- wire:model.live for real-time updates
- Named event dispatching
- Performance: #[Lazy], wire:model.debounce, pagination

## Troubleshooting Reference

### Before Running Migrations

```bash
# Always test rollback first
php artisan migrate --pretend

# Then migrate
php artisan migrate

# Verify changes
php artisan migrate:status
```

### When Tests Fail

```bash
# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run with specific test name
php artisan test --filter=testMethodName

# Check code quality first
vendor/bin/phpstan analyse
vendor/bin/pint --dirty
```

### When Asset Changes Don't Show

```bash
# Rebuild frontend assets
npm run build

# Or watch for changes during development
npm run dev

# Clear application cache
php artisan cache:clear
```

## References & Documentation

**MCP Memory Query Guide**:
- Query `Memory_Query_Guide` entity for complete discovery instructions
- Entry points: `D00_System_Overview` (system context) or `D03_Software_Requirements` (requirements)
- Search patterns: `search_nodes('keyword')` for broad search, `open_nodes(['Entity_Name'])` for specific retrieval
- Relation traversal: Follow 'documents', 'implements', 'uses', 'related_to' relations

**Core ICTServe Documentation** (Access via MCP Memory):
- `D00_System_Overview` — System context and governance
- `D03_Software_Requirements` — Feature requirements (50+ FR/NFR)
- `D04_Software_Design` — Architecture decisions (MVC+SDUI+Livewire)
- `D09_Database_Documentation` — 30+ tables, auditing, PDPA compliance
- `D10_Source_Code_Documentation` — PSR-12, PHPDoc, testing standards
- `D11_Technical_Design` — Infrastructure, queue config, deployment
- `D12_UI_UX_Design_Guide` — Component library, ARIA, responsive design
- `D13_UI_UX_Frontend_Framework` — Tailwind+Alpine+Livewire stack
- `D14_UI_UX_Style_Guide` — MOTAC branding, accessibility
- `D15_Language_Localization` — Bilingual MS/EN system

**Agent & Steering Guidance** (File-based, not in MCP memory):
- `AGENTS.md` — Global agent policy and project conventions
- `.kiro/steering/behavior.md` — Core operational guardrails
- `.kiro/steering/mcp.md` — MCP server reference (PRIMARY for tool guidance)
- `.agents/memory.instruction.md` — This file (memory management + MCP navigation)

**For MCP capabilities**, refer to `.kiro/steering/mcp.md` which documents:
- 9 MCP servers with tools and use cases
- Security policies and compliance requirements
- Integration patterns and workflows
- Error handling and recovery procedures

**Common MCP Memory Queries**:
```
# System Overview
open_nodes(['D00_System_Overview'])

# Requirements & Design
open_nodes(['D03_Software_Requirements', 'D04_Software_Design'])

# Database & Code Standards
open_nodes(['D09_Database_Documentation', 'D10_Source_Code_Documentation'])

# UI/UX & Accessibility
open_nodes(['D12_UI_UX_Design_Guide', 'D14_UI_UX_Style_Guide'])

# Search by Topic
search_nodes('email notification')
search_nodes('accessibility WCAG')
search_nodes('Livewire component patterns')
search_nodes('database migration')
search_nodes('Filament action namespace')
```

## Updating This Memory

**When to Update MCP Memory**:
- After solving a novel problem → Create new solved_issue entity
- After discovering a project pattern → Create new coding_pattern entity
- After completing compliance work → Create new compliance_implementation entity
- After deploying to production → Add observations to relevant entities

**How to Update MCP Memory**:
- Create new entities: `mcp_memory_create_entities` with entityType, name, observations
- Add observations: `mcp_memory_add_observations` to existing entities
- Create relations: `mcp_memory_create_relations` to connect entities
- Search before creating: Use `search_nodes()` to avoid duplicates

**Entity Types Available**:
- `canonical_document` — D00-D15 system documentation
- `documentation_directory` — docs subdirectories (features, guides, technical, reference)
- `technical_implementation` — Completed features (email system, etc.)
- `coding_pattern` — Proven code patterns (Filament, Livewire, testing, database, etc.)
- `compliance_implementation` — Completed compliance work (accessibility, i18n, audits)
- `solved_issue` — Debugging solutions (500 errors, DB connections, seeding)
- `meta_documentation` — Navigation docs (INDEX, README, GLOSSARY)
- `query_guide` — Memory discovery instructions
- `documentation_standard` — Documentation standards and conventions

**Always Include in New Entities**:
- Clear, descriptive observations (10-15 per entity)
- Related document references (e.g., "Related: D04 (design), D10 (code)")
- Status information (COMPLETED, In Progress, etc.)
- Date information when relevant (YYYY-MM-DD format)
- Links to related entities via relations

**Example: Adding New Solution**:
```
# 1. Search for existing solutions
search_nodes('your error keyword')

# 2. If not found, create entity
mcp_memory_create_entities([
  "entityType": "solved_issue",
  "name": "New_Error_Resolution",
  "observations": [
    "Issue: Description of error",
    "Solution Steps: 1) Step one, 2) Step two",
    "Success Rate: 100% (tested in X environments)",
    "Prevention: How to avoid",
    "Related: D11 (technical infrastructure)"

])

# 3. Create relations to relevant docs
mcp_memory_create_relations([
  "from": "New_Error_Resolution",
  "relationType": "related_to",
  "to": "D11_Technical_Design"
])
```

**Best Practices**:
- Use specific entity names (e.g., `Livewire_3_Component_Patterns` not `Livewire_Patterns`)
- Keep observations atomic (one fact per observation)
- Always link to canonical documents when applicable
- Date entries when time-sensitive (e.g., "Completed: 2025-11-01")
- Test queries after creating to verify discoverability
