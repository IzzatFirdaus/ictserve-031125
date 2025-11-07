---
applyTo: "**"
description: "MCP Memory Query Guide - AI agent memory management using MCP Memory Server. Query patterns, entity architecture, and lifecycle protocols for ICTServe."
---

# Agent Memory & Context Management (MCP) — Best Practices

## Purpose & Scope

**CRITICAL MANDATE**: All project knowledge is stored in MCP Memory Server entities. This file is a **query reference only** - do not read for information, use as patterns and syntax guide.

**Authority**: Based on research of MCP Protocol v2025-06-18 specifications, knowledge graph architecture (Google, academic standards), and AI agent memory best practices.

## Research Findings

**Per MCP Specification v2025-06-18** (Anthropic, 2025):
- Resources expose data through URI-based interfaces with standardized capabilities
- Tools accept structured input (JSON Schema) and return typed outputs
- Protocol version negotiation ensures backwards compatibility

**Per Knowledge Graph Architecture** (Wikipedia, Academic standards):
- Entities = nodes representing objects/concepts with typed properties
- Relations = edges connecting entities with semantic meaning
- Observations = flexible facts about entities (support evolution)
- Reasoning = traverse relations for implicit knowledge discovery

**Per AI Agent Memory Best Practices** (Anthropic Research, Knowledge Management):
- Memory should be **queryable** (search + structured retrieval)
- Memory should be **persistent** (survives session boundaries)
- Memory should be **evolving** (update with new information)
- Memory should be **minimalist** (avoid duplication with files)

## Knowledge Graph Architecture

**MCP Memory Server** implements a semantic knowledge graph:

```
Entities (nodes) ←→ Relations (edges) ←→ Observations (facts)

Entity Structure:
├── name (unique identifier)
├── entityType (canonical_document, technical_implementation, etc.)
└── observations[] (array of facts/details)

Relation Structure:
├── from (source entity)
├── relationType (semantic meaning: documents, implements, uses, related_to)
└── to (target entity)
```

**This provides:**
- ✅ **Discoverability**: Search across all facts and relations
- ✅ **Traceability**: Follow entity links to related knowledge
- ✅ **Flexibility**: Add observations without schema migration
- ✅ **Reasoning**: Implicit knowledge through relation traversal

## Startup Protocol (CRITICAL)

**Execute this workflow at the START of every interaction:**

### Phase 0: Query Existing Memory First

**BEFORE** creating any new entities or information:

```markdown
Step 1: Search for existing knowledge
  search_nodes('your-topic-or-keyword')
  → Returns: Matching entity names + basic metadata

Step 2: Open relevant entities
  open_nodes(['Entity_Name_1', 'Entity_Name_2'])
  → Returns: Full entity details with observations + relations

Step 3: Traverse relations if needed
  From opened entity: Follow 'documented_by', 'implements', 'uses' relations
  → Discover connected knowledge without manual queries
```

**Result**: You now have context-specific knowledge without reading documentation files.

### Phase 1: Memory Initialization (Session Start)

1. **Query user context**:
   ```
   search_nodes('default_user')  OR  open_nodes(['default_user'])
   ```
   Retrieve: preferences, past work, known patterns

2. **Load project status**:
   ```
   open_nodes(['ICTServe_System_Status', 'Staff_Dashboard_Implementation_Progress'])
   ```
   Retrieve: Current project state, in-progress features, blockers

3. **Load task-specific context**:
   ```
   search_nodes('your-current-task-keywords')
   ```
   Retrieve: Relevant entities (patterns, issues, implementations)

### Phase 2: Memory Update Protocol (During Work)

**Monitor for new information:**

| Category | When Found | Action |
|----------|-----------|--------|
| Facts | New understanding discovered | `add_observations()` to relevant entity |
| Solutions | Bug fixed / pattern identified | Create new `solved_issue` entity |
| Features | Implementation completed | Update `technical_implementation` entity |
| Context | User preferences identified | Add to user context entity |

**Update Pattern**:
```typescript
// 1. Search first to avoid duplicates
search_nodes('keyword')

// 2. If exists: add observation
mcp_memory_add_observations([{
  entityName: 'Existing_Entity',
  contents: ['New fact or observation']
}])

// 3. If new: create entity
mcp_memory_create_entities([{
  name: 'Unique_Entity_Name',
  entityType: 'solved_issue|coding_pattern|technical_implementation',
  observations: ['Key observations about this entity']
}])

// 4. If related: create relation
mcp_memory_create_relations([{
  from: 'New_Entity',
  relationType: 'related_to|documents|implements|uses',
  to: 'Existing_Entity'
}])
```

### Phase 3: Memory Finalization (Session End)

**Before session ends:**

1. Create user_request entity with:
   - Task description
   - Files touched (created, modified, deleted)
   - Lines of code changed
   - Issues solved
   - Recommendations for next session

2. Update affected entities with observations:
   - Technical_implementation: Add progress notes
   - Solved_issue: Add success metrics
   - User: Add interaction patterns observed

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

## Integration Patterns for Agents (Operationalizing Memory Tools)

### Pattern 1: Memory-Driven Workflow Initialization

**Scenario**: Agent starts work on ICTServe task (e.g., "Build staff dashboard export feature")

**Workflow**:
```
1. INITIALIZE
   search_nodes('export feature')
   → Find: Requirements, design docs, existing implementations
   
2. CONTEXT LOAD
   open_nodes(['D03_Software_Requirements', 'D04_Software_Design', 'Staff_Dashboard_Implementation_Progress'])
   → Get: Feature requirements (traceability SRS-IDs), design decisions, component status
   
3. PATTERN REUSE
   search_nodes('export')
   → Find: Export_Service_Implementation entity with tested patterns
   open_nodes(['Export_Service_Implementation'])
   → Get: Observation: "Laravel Maatwebsite Excel library, queue-based async exports, 3-week retention policy"
   
4. BEGIN WORK
   Create user_request entity documenting:
   - Task: "Build staff dashboard export feature"
   - Linked entities: Staff_Dashboard_Implementation_Progress, Export_Service_Implementation
   - Scope: Export submission history as CSV/Excel
   
5. DURING WORK
   add_observations(['user_request'], [
     "Component: ExportSubmissionHistory created (Livewire 3)",
     "Service: ExportService extended with ->submissions() method",
     "Queue: ExportJob::dispatch() added to queue (1-hour timeout)",
     "Files created: 2 (component, job); Lines: 147"
   ])
```

**Result**: Memory contains complete work history, linked to patterns and requirements. Next agent session can query `open_nodes(['user_request'])` to see work context and dependencies.

---

### Pattern 2: Component Dependency Resolution

**Scenario**: Agent needs to build Livewire component but uncertain of dependencies

**Workflow**:
```
1. SEARCH FOR COMPONENT PATTERNS
   search_nodes('Livewire component')
   → Find: Livewire_3_Component_Patterns entity
   
2. LOAD PATTERNS
   open_nodes(['Livewire_3_Component_Patterns'])
   Observations include:
   - "#[Reactive] for state variables"
   - "#[Computed] for derived values"
   - "wire:model.live for real-time updates"
   - "Testing: Volt::test() with assertions"
   - "Example: QuickActions component in Staff_Dashboard_Implementation_Progress"
   
3. TRAVERSE TO REAL EXAMPLE
   Follow relation: Livewire_3_Component_Patterns --relates_to--> Staff_Dashboard_Implementation_Progress
   open_nodes(['Staff_Dashboard_Implementation_Progress'])
   Observations include component list with QuickActions, RecentActivity, etc.
   
4. SEARCH RELATED SERVICE PATTERNS
   search_nodes('ExportService')
   → Find: Export_Service_Implementation entity
   → Get: "Uses Livewire validation, dispatches queue jobs, integrates with DashboardService"
   
5. BEGIN IMPLEMENTATION
   Use discovered patterns as templates:
   - Service structure from Export_Service_Implementation
   - Component wiring from QuickActions pattern
   - Testing approach from Livewire_3_Component_Patterns
```

**Result**: Component implementation follows proven patterns from project. Reduces decision fatigue, improves consistency.

---

### Pattern 3: Requirements Traceability

**Scenario**: Agent needs to verify feature implementation satisfies requirements

**Workflow**:
```
1. SEARCH REQUIREMENTS
   search_nodes('SRS-1.1')
   → Find: Requirement_SRS_1_1 entity (if requirement-mapped)
   
2. LOAD REQUIREMENT DETAIL
   open_nodes(['Requirement_SRS_1_1'])
   Observations: "Display 10 most recent submissions, sortable by date/status/priority"
   
3. CHECK IMPLEMENTATION STATUS
   Follow relation: Requirement_SRS_1_1 --implements--> Staff_Dashboard_Implementation_Progress
   open_nodes(['Staff_Dashboard_Implementation_Progress'])
   Search observation: "Phase 2: SubmissionHistoryComponent (5 of 10 features complete)"
   
4. IDENTIFY GAPS
   Requirement needs: sorting by priority
   Implementation status: sorting by date/status complete, priority sorting NOT YET DONE
   
5. DOCUMENT WORK
   add_observations(['Requirement_SRS_1_1'], [
     "Implementation Status: 5/10 features complete",
     "Completed: Date sorting, status sorting, pagination",
     "Pending: Priority sorting (awaiting field addition to Submission model)",
     "Blocker: D09 database schema revision needed for priority field"
   ])
   
6. CREATE DEPENDENCY ENTITY
   create_entities([{
     name: 'Priority_Field_Blocker_SRS_1_1',
     entityType: 'blocker',
     observations: [
       "Requirement: SRS-1.1 (priority sorting)",
       "Implementation: SubmissionHistoryComponent",
       "Blocker: priority field missing from Submission model",
       "Resolution: Add migration, update factory, update seeder",
       "Estimated effort: 2 hours"
     ]
   }])
```

**Result**: Complete traceability chain: SRS requirement → component implementation → blockers → effort estimates. Future agent queries can immediately assess completion status.

---

### Pattern 4: Error Resolution with Context

**Scenario**: Agent encounters "500 Internal Server Error" during component development

**Workflow**:
```
1. CHECK LOGS
   Read storage/logs/laravel.log last 50 entries
   
2. SEARCH FOR PATTERN
   search_nodes('500 error')
   → Find: 500_Error_Resolution_Pattern entity
   
3. LOAD PATTERN
   open_nodes(['500_Error_Resolution_Pattern'])
   Observations include:
   - "Step 1: Check storage/logs/laravel.log"
   - "Step 2: Verify bootstrap/cache permissions"
   - "Step 3: Check database connection settings"
   - "Prevention: Always run php artisan cache:clear after major changes"
   
4. DIAGNOSE
   Log shows: "PDOException: SQLSTATE[HY000] [1045] Access denied for user"
   → Matches "database connection" scenario from pattern
   
5. FOLLOW PATTERN INSTRUCTIONS
   Check database credentials in .env
   Find: DB_HOST=wrong.host.com (typo)
   Fix: DB_HOST=localhost
   
6. DOCUMENT SOLUTION
   add_observations(['500_Error_Resolution_Pattern'], [
     "Variant: Database connection error caused by typo in DB_HOST",
     "Resolution time: 3 minutes",
     "Prevention: Add database connection validation to CI pipeline"
   ])
   
7. CREATE INCIDENT ENTITY
   create_entities([{
     name: 'DB_Connection_Typo_Incident_2025_11_06',
     entityType: 'solved_issue',
     observations: [
       "Error: 500 Internal Server Error",
       "Root cause: DB_HOST=wrong.host.com",
       "Resolution: Corrected to DB_HOST=localhost",
       "Time to resolution: 3 minutes",
       "Related pattern: 500_Error_Resolution_Pattern"
     ]
   }])
```

**Result**: Error resolved quickly using pattern knowledge. Solution stored for future reference. Patterns updated with new variant information.

---

### Pattern 5: Multi-Session Context Preservation

**Scenario**: Work on staff dashboard spans 3 sessions (Day 1, Day 2, Day 3)

**Session 1 - Day 1 (Agent A)**:
```
START
search_nodes('Staff Dashboard')
open_nodes(['Staff_Dashboard_Implementation_Progress'])
→ Get current status: "Phase 2 complete, Phase 3 (9/17 components) in progress"

WORK
Build components: AuthenticatedDashboard, QuickActions, RecentActivity
add_observations(['Staff_Dashboard_Implementation_Progress'], [
  "Day 1 work: Built 3 core components (AuthenticatedDashboard, QuickActions, RecentActivity)",
  "Files created: 3 Livewire components",
  "Lines added: 315",
  "Testing: All components pass basic rendering tests",
  "Blockers: None",
  "Next priority: SubmissionHistory component"
])

END
create_entities([{
  name: 'Session_Day1_Dashboard_Work',
  entityType: 'work_session',
  observations: [...work details...]
}])
```

**Session 2 - Day 2 (Agent B)**:
```
START
search_nodes('Staff Dashboard')
open_nodes(['Staff_Dashboard_Implementation_Progress'])
→ Get status: Phase 3 (9/17), see Day 1 work added as observation

search_nodes('SubmissionHistory')
→ Find: SubmissionHistoryComponent pattern from Day 1 work

WORK
Day 2 build: 4 more components (SubmissionHistory, SubmissionDetail, SubmissionFilters, UserProfile)
add_observations(['Staff_Dashboard_Implementation_Progress'], [
  "Day 2 work: Built 4 components (SubmissionHistory, SubmissionDetail, SubmissionFilters, UserProfile)",
  "Dependency: Extended DashboardService with new query scopes",
  "Files created: 4 components, 1 service extension",
  "Lines added: 487",
  "Testing: 12 new test cases, all passing",
  "Blockers: Form validation patterns inconsistent; created form_validation_standardization entity",
  "Next priority: Notification preferences, Settings components"
])

END
create_entities([{
  name: 'Session_Day2_Dashboard_Work',
  entityType: 'work_session',
  observations: [...work details...]
}])
```

**Session 3 - Day 3 (Agent C)**:
```
START
open_nodes(['Staff_Dashboard_Implementation_Progress'])
→ See Day 1 + Day 2 work + current status: "13/17 components complete"

search_nodes('form validation')
→ Find: form_validation_standardization entity created by Agent B

WORK
Day 3 build: 4 final components + standardize form validation
add_observations(['Staff_Dashboard_Implementation_Progress'], [
  "Day 3 work: Built 4 final components + standardized form validation",
  "Components: NotificationPreferences, SecuritySettings, ExportData, AuditTrail",
  "Form validation: Applied Form_Validation_Standard to all 17 components",
  "Files modified: 17 components, 1 service",
  "Lines added: 156",
  "Testing: All 17 components pass comprehensive test suite",
  "Status: Phase 3 COMPLETE (17/17 components)",
  "Blockers: None",
  "Summary: Staff dashboard fully functional, WCAG 2.2 AA compliant, bilingual"
])

END
create_entities([{
  name: 'Session_Day3_Dashboard_Work',
  entityType: 'work_session',
  observations: [...work details...]
}])
```

**Result**: 3-agent collaboration captured seamlessly. Each agent could pick up work context immediately. Cross-agent blockers and dependencies documented and resolved. Full project history preserved in memory graph.

---

### Pattern 6: Query-Driven Decision Making

**Scenario**: Agent needs to decide: "Should I use Filament Resource or Livewire Component for new admin feature?"

**Workflow**:
```
1. SEARCH FOR SIMILAR FEATURES
   search_nodes('admin feature comparison')
   search_nodes('Filament vs Livewire')
   
2. LOAD ARCHITECTURAL GUIDANCE
   open_nodes(['D04_Software_Design', 'Filament_4_Patterns', 'Livewire_3_Component_Patterns'])
   
3. REVIEW EXISTING DECISIONS
   Follow relation: Staff_Dashboard_Implementation_Progress --uses--> Livewire_3_Component_Patterns
   Observations: "Dashboard uses Livewire for consistency with reactive components"
   
   Search: open_nodes(['Filament_Resource_Examples'])
   Observations: "Filament used for: CRUD operations (Users, Departments), bulk actions, data export"
   
4. ANALYZE REQUIREMENTS
   New feature: "Add admin panel to approve user submissions"
   - Read-heavy operation? No (single submit action per row)
   - Bulk actions needed? Yes (approve multiple, reject multiple, export)
   - Complex form? Yes (multi-field validation, conditional logic)
   
5. DECISION LOGIC
   Filament Resources = CRUD + bulk actions + forms ✅
   Livewire Components = reactive state + real-time updates ✅
   
   Requirement analysis:
   - "Bulk actions" → Favor Filament
   - "Complex form validation" → Favor Filament (forms built-in)
   - "Real-time updates" → Could use Livewire
   - "Consistency with dashboard" → Livewire preferred
   
   Decision: Use Filament Resource (bulk approval/rejection is critical)
   
6. DOCUMENT DECISION
   create_entities([{
     name: 'Admin_Submission_Approval_Feature_Decision',
     entityType: 'architectural_decision',
     observations: [
       "Feature: Admin submission approval panel",
       "Decision: Filament Resource (not Livewire component)",
       "Rationale: Bulk actions (approve/reject multiple) essential; Filament has native bulk actions",
       "Alternative considered: Livewire component (real-time updates, dashboard consistency)",
       "Why not alternative: Dashboard doesn't need real-time approval updates",
       "Implementation: Create SubmissionApprovalResource extending Filament\\Resources\\Resource",
       "Related entities: Filament_4_Patterns (for implementation details)",
       "Decision date: 2025-11-06"
     ]
   }])
```

**Result**: Decision documented with rationale. Future developers (or same agent in new session) understand why this feature uses Filament, preventing inconsistent rewrites.

---

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
