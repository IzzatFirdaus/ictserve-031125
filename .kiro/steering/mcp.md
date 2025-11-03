---
inclusion: always
---

# MCP Capabilities & Usage Guidelines

## Purpose & Scope

This document defines how to leverage Model Context Protocol (MCP) servers within this Laravel 12 repository. Use these tools to enhance productivity while maintaining security, quality, and compliance with `/docs/D00–D15`.

## Current MCP Server Configuration

### Active Servers (10 total)

**Core Development & Analysis**:

- **sequentialthinking**: Complex problem decomposition and multi-step planning
- **memory**: Knowledge graph management with entity-relationship modeling  
- **context7**: Enhanced context understanding and requirements parsing
- **fetch**: HTTP requests, API interactions, and external service communication

**Laravel-Specific Operations**:

- **laravel-boost**: Laravel-specific tooling, Artisan commands, and framework operations (16 tools)

**Browser & Testing Tools**:

- **chrome-devtools**: Browser inspection, debugging, and frontend analysis
- **playwright**: Cross-browser automation, testing, and user flow validation

**Data & Translation**:

- **firecrawl**: Web scraping, data extraction, and external content processing
- **redis**: Redis database operations (ready when Redis server configured)
- **deepl**: Bahasa Melayu ↔ English translation (CRITICAL for ICTServe localization)

### Disabled Servers (Optional - Require Authentication)

- **github**: Repository management, PR operations (disabled: requires token configuration fix)
- **gitkraken**: AI-powered git workflows (disabled: requires `gk auth login`)

**To enable disabled servers**: See `.kiro/settings/MCP_ISSUE_RESOLUTION.txt` for authentication steps

### Configuration Architecture

**Workspace Config** (`.kiro/settings/mcp.json`):

- Team-shared server definitions
- Uses `$input:...` placeholders for API keys
- Version-controlled (no secrets)
- 12 servers defined (10 active, 2 disabled)

**User Config** (`C:\Users\[USERNAME]\.kiro\settings\mcp.json`):

- Personal API key values (actual keys, not placeholders)
- Server-specific overrides (e.g., playwright enabled, connection timeouts)
- Not version-controlled
- 8 servers configured with actual credentials

**Environment Variables** (`.env`):

- Centralized API keys for Laravel and MCP integration
- Keys: `CONTEXT7_API_KEY`, `FIRECRAWL_API_KEY`, `DEEPL_API_KEY`, `GITHUB_TOKEN`
- Protected by `.gitignore`

## Usage Policies & Guardrails

### Security & Access Control

- **Production Environment**: Never use MCP servers for production data or live environments without explicit BPM approval
- **Rate Limiting**: Respect API boundaries and implement exponential backoff for all external services
- **Data Sanitization**: All external data from `firecrawl` and `fetch` must be validated and sanitized
- **Credential Management**: Use configured authentication only; never expose secrets in operations or logs

### Data Handling & Compliance

- **PDPA 2010**: All personal data processed through MCP must follow data protection guidelines in `docs/D09`
- **External Data**: Validate and sanitize all data from `firecrawl` and web sources before storage
- **Memory Usage**: Use `memory` for development context only; never store credentials or sensitive user data

### Memory MCP Server Enforcement (CRITICAL - ZERO TOLERANCE)

**MANDATORY MEMORY INTEGRATION**: Memory MCP server integration is the FOUNDATION of all MCP operations. No MCP tool may be used without proper memory integration. Agentic AI is PROHIBITED from creating unnecessary markdown files (summaries, implementations, checklists, templates, audits) before, during, or after development.

#### File Creation Prohibition (MANDATORY ENFORCEMENT)

**PROHIBITED FILE TYPES**: Agentic AI is FORBIDDEN from creating the following file types without explicit user approval:

- ❌ **Summaries** (`*-summary.md`) - Use MCP memory observations instead
- ❌ **Implementations** (`implementation-*.md`) - Use MCP memory entities instead  
- ❌ **Checklists** (`*-checklist.md`) - Use MCP memory observations instead
- ❌ **Templates** (`*-template.md`) - Use MCP memory patterns instead
- ❌ **Audits** (`*-audit.md`) - Use MCP memory observations instead
- ❌ **Reports** (`*-report.md`) - Use MCP memory observations instead
- ❌ **Analysis docs** (`analysis-*.md`) - Use MCP memory entities instead
- ❌ **Task status** (`task-*.md`) - Use MCP memory observations instead

**ENFORCEMENT MECHANISM**: Any attempt to create prohibited file types must be blocked. Instead, use MCP memory tools:

- `add_observations()` for status updates and findings
- `create_entities()` for new patterns or implementations
- `create_relations()` for connecting concepts

### File Error & Lint Enforcement (MANDATORY)

All MCP-driven workflows MUST include a blocking file-check quality gate: when an agent creates or saves any repository file, it MUST validate and fix errors/lints before continuing to the next todo, file, or workflow step.

Required steps (enforced):

1. Immediately run the project's linters and tests targeted at changed files.
2. Inspect the IDE/PROBLEMS list and linter/test outputs for errors and warnings.
3. Resolve errors (and address high/medium warnings) before moving on; document unresolved low-priority warnings in memory with `add_observations()` and proceed only after human approval.
4. Record the fix attempts and final verification in memory (use `add_observations()` on the active user_request entity).

PowerShell command examples (agent SHOULD adapt to repository scripts):

```powershell
vendor\bin\pint --dirty ; vendor\bin\phpstan analyse --no-progress
php artisan test --filter=ChangedFilesTest || php artisan test
npm run lint ; npm run build
npx markdownlint .kiro\steering\behavior.md .kiro\steering\mcp.md
```

Failure handling:

- If the agent cannot automatically resolve a lint/test failure, it MUST create an MCP memory observation with failure details and attempted fixes, then pause and request human guidance.
- The agent MUST NOT mark a todo or file as complete in memory until all enforced checks pass and the verification observation is stored.

**Example Replacement Pattern**:

```markdown
❌ WRONG: Create docs/reports/email-compliance-report.md
✅ CORRECT: Use MCP memory
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

#### Memory MCP Server Integration (MANDATORY)

**Pre-Operation Requirements**:

- **MANDATORY**: Query `memory` for existing patterns BEFORE starting any work
- **MANDATORY**: Create user_request entity at session start
- **MANDATORY**: Verify compliance requirements against stored standards
- **ENFORCEMENT**: No development work may proceed without memory context queries

**During Operation**:

- **MANDATORY**: Store all discoveries and decisions in memory
- **MANDATORY**: Update implementation status with progress
- **MANDATORY**: Document solution patterns for reuse
- **ENFORCEMENT**: All findings must be stored in memory, not separate files

**Post-Operation Requirements**:

- **MANDATORY**: Document completion in memory with full details
- **MANDATORY**: Create relations between new work and existing specs
- **MANDATORY**: Update compliance verification status
- **ENFORCEMENT**: Work completion without memory documentation is INVALID

## MCP Integration Patterns (MANDATORY MEMORY-FIRST APPROACH)

### Development Workflows (ENFORCED MEMORY INTEGRATION)

**Feature Implementation** (MANDATORY MEMORY-DRIVEN WORKFLOW):

1. **REQUIRED FIRST**: Query `memory` for existing patterns and technical decisions - NO EXCEPTIONS
2. **MANDATORY**: Use `sequentialthinking` to decompose complex requirements into actionable steps
3. **REQUIRED**: Generate Laravel components with `laravel-boost` following stored conventions from memory
4. **MANDATORY**: Validate user flows with `playwright` and debug UI with `chrome-devtools`
5. **REQUIRED**: Store all new patterns and solutions in `memory` before completion
6. **OPTIONAL**: Create PR via `github` (if enabled) with proper documentation and test coverage
**ENFORCEMENT**: Any feature implementation without memory integration is PROHIBITED and must be restarted.

**Bug Investigation** (MANDATORY MEMORY-FIRST DEBUGGING):

1. **REQUIRED FIRST**: Search `memory` for similar historical issues and solutions - NO EXCEPTIONS
2. **MANDATORY**: Reproduce issue consistently with `playwright` automation
3. **REQUIRED**: Inspect DOM and network with `chrome-devtools`
4. **MANDATORY**: Use `laravel-boost` `last-error` and `read-log-entries` for backend errors
5. **REQUIRED**: Store solution patterns in `memory` for future reference
6. **MANDATORY**: Implement and validate fix following quality gates
**ENFORCEMENT**: Bug investigation without memory context queries is FORBIDDEN. All debugging must start with memory.

### Quality Assurance Pipeline

- **E2E Testing**: Use `playwright` for critical user journeys and Filament admin flows
- **Frontend Debugging**: Leverage `chrome-devtools` for CSS, JavaScript, and performance issues
- **Backend Debugging**: Use `laravel-boost` `tinker` for quick code experimentation
- **Test Integration**: Cross-reference MCP test results with PHPUnit coverage reports
- **Visual Validation**: Use `playwright` screenshots for UI regression detection

### Knowledge Management (MANDATORY MEMORY-CENTRIC APPROACH)

- **REQUIRED Context Building**: MUST use `memory` to maintain ICTServe architectural decisions and system specifications across sessions - NO EXCEPTIONS
- **MANDATORY Requirements Analysis**: MUST leverage `context7` for parsing complex `/docs/` specifications, then REQUIRED to store insights in `memory`
- **ENFORCED Documentation Search**: MUST use `laravel-boost` `search-docs` for Laravel/Filament/Livewire docs, then MANDATORY to document patterns in `memory`
- **Optional Collaboration**: Use `github` (when enabled) for PR workflows, code reviews, and issue tracking
- **REQUIRED System Context**: MANDATORY to query `memory` for ICTServe specifications, implementation status, and compliance requirements before starting ANY work
- **ENFORCED Pattern Reuse**: MUST search `memory` for existing solutions before implementing new features - NO BLIND IMPLEMENTATION
- **MANDATORY Cross-Session Continuity**: REQUIRED to use `memory` to maintain context between development sessions without repeating discovery work

**ENFORCEMENT POLICY**: All MCP operations MUST integrate with memory. Any MCP tool usage without memory context is considered a violation of development protocols and must be corrected immediately.

## Tool-Specific Guidelines

### sequentialthinking

**Purpose**: Complex problem decomposition and architectural planning

**Tools**: `sequentialthinking`

**Guidelines**:

- Break down Laravel feature requirements into discrete, testable units
- Document decision trees for multi-step refactoring operations
- Escalate to human review when architectural uncertainty exceeds 30%
- Use for migration planning, feature development, and refactoring workflows

### memory

**Purpose**: Knowledge graph management with entity-relationship modeling for ICTServe system context

**Available Tools**:

- **Entity Management**: `create_entities`, `delete_entities`
- **Relationship Management**: `create_relations`, `delete_relations`  
- **Data Operations**: `add_observations`, `delete_observations`
- **Query Operations**: `read_graph`, `search_nodes`, `open_nodes`

**ICTServe System Knowledge Graph**:

**Core System Entities** (Already Stored):

1. **ictserve_system_spec**: Hybrid architecture (guest + authenticated + admin access)
   - Technology stack: Laravel 12, PHP 8.2, Livewire 3, Volt 1, Filament 4
   - Compliance: WCAG 2.2 AA, PDPA 2010, Core Web Vitals targets
   - Four-role RBAC: Staff, Approver (Grade 41+), Admin, Superuser
   - Email-based workflows with 60-second notification SLA

2. **helpdesk_module_spec**: Guest-only ticketing system
   - Public forms without authentication requirements
   - Ticket format: HD[YYYY][000001-999999]
   - Email-first communication with automated workflows
   - SLA tracking with 25% breach escalation
   - Admin-only Filament backend management

3. **ict_asset_loan_spec**: Asset management with email approvals
   - Guest application forms with Grade 41+ email approvals
   - Dual approval: Email-based (no login) + Portal-based (login)
   - Real-time inventory with booking calendar
   - Automated reminders: 48h before, on due date, daily overdue
   - Asset-ticket integration for damage reporting

4. **frontend_pages_redesign_spec**: WCAG 2.2 AA compliance
   - Unified component library with compliant color palette
   - Performance targets: LCP <2.5s, FID <100ms, CLS <0.1
   - Guest-only architecture implementation
   - Livewire optimization with OptimizedLivewireComponent trait

5. **ollama_ai_integration_spec**: Local LLM integration
   - FAQ Bot, Document Analysis, Auto-Reply features
   - Local processing (no external APIs)
   - PDPA compliance with PII detection
   - 5-second response time targets

**Implementation Tracking Entities**:
6. **ictserve_implementation_status**: Current progress tracking
7. **ictserve_technical_architecture**: Technology stack details
8. **ictserve_compliance_standards**: All compliance requirements

**Entity Relationships** (Established):

- System contains helpdesk and asset loan modules
- Frontend redesign applies to all modules  
- AI integration enhances both core modules
- Compliance standards govern all specifications
- Cross-module integration between helpdesk and asset loan

**Usage Patterns for ICTServe Development**:

**Feature Development**:

```json
// Query existing patterns
search_nodes "guest forms email workflow WCAG compliance"

// Create new user request
create_entities [
  "name": "user_request_2025_11_01_feature_name",
  "entityType": "user_request", 
  "observations": [
    "User requested: [DESCRIPTION]",
    "Related specs: helpdesk_module_spec, ict_asset_loan_spec",
    "D-docs: D03-FR-XXX, D04 §X.X",
    "Architecture layer: guest/authenticated/admin"

]

// Document solution patterns
create_entities [
  "name": "solution_pattern_name",
  "entityType": "solution_pattern",
  "observations": [
    "Pattern: [REUSABLE_APPROACH]",
    "Use case: [WHEN_TO_USE]", 
    "Implementation: [HOW_TO_IMPLEMENT]",
    "WCAG compliance: [ACCESSIBILITY_NOTES]"

]
```

**Cross-Module Integration**:

```json
// Check integration points
open_nodes ["helpdesk_module_spec", "ict_asset_loan_spec"]

// Document integration patterns
add_observations [
  "entityName": "ictserve_system_spec",
  "contents": [
    "Integration pattern: Asset-ticket linking via asset_id foreign key",
    "Damage reporting: Auto-create helpdesk ticket within 5 seconds",
    "Admin dashboard: Unified view of both modules"

]
```

**Compliance Verification**:

```json
// Check compliance requirements
open_nodes "ictserve_compliance_standards"

// Document compliance verification
add_observations [
  "entityName": "user_request_2025_11_01_feature_name", 
  "contents": [
    "WCAG 2.2 AA: Verified 4.5:1 text contrast, 3:1 UI contrast",
    "Core Web Vitals: LCP <2.5s, FID <100ms, CLS <0.1 maintained",
    "PDPA compliance: No personal data stored, audit trail implemented",
    "D03 traceability: Requirements FR-XXX satisfied"

]
```

**Implementation Progress Tracking**:

```json
// Update implementation status
add_observations [
  "entityName": "ictserve_implementation_status",
  "contents": [
    "Phase 3.7.4: Hybrid forms implementation - IN PROGRESS",
    "Completed: Guest-only form refactoring",
    "Pending: Authenticated portal integration",
    "Next: Comprehensive testing suite (Phase 5.13)"

]
```

**Security & Compliance**:

- Never store credentials, API keys, or personal data
- Use for development context and technical patterns only
- All entities sanitized for PDPA compliance
- Regular audit of stored entities for compliance
- Storage location: `~/.kiro/steering/memory.jsonl` (cross-workspace)
- Automatic backup with Kiro IDE settings

**Memory Query Strategies for ICTServe**:

- **Broad searches**: "helpdesk", "asset loan", "WCAG", "compliance", "guest forms"
- **Specific patterns**: "email workflow", "Filament resource", "Livewire optimization"
- **Integration points**: "cross-module", "asset-ticket linking", "unified dashboard"
- **Implementation status**: "pending", "completed", "in progress", "phase"
- **Compliance verification**: "WCAG AA", "Core Web Vitals", "PDPA", "D03 requirements"

### chrome-devtools

**Purpose**: Browser inspection and frontend debugging

**Available Tools**:

- **Page Management**: `list_pages`, `select_page`, `new_page`, `close_page`
- **Navigation**: `navigate_page`, `navigate_page_history`
- **Interaction**: `click`, `hover`, `drag`, `fill`, `fill_form`, `upload_file`
- **Inspection**: `take_screenshot`, `take_snapshot`, `list_console_messages`, `list_network_requests`
- **Performance**: `performance_start_trace`, `performance_stop_trace`, `performance_analyze_insight`
- **Emulation**: `emulate_cpu`, `emulate_network`, `resize_page`
- **Scripting**: `evaluate_script`
- **Utilities**: `wait_for`, `handle_dialog`

**Guidelines**:

- Development and staging environments only
- Use `performance_start_trace` for Filament admin performance analysis
- Leverage `take_snapshot` for quick DOM state capture during Livewire debugging
- Use `emulate_network` to test application behavior under poor connectivity
- Use `list_console_messages` to inspect JavaScript errors
- Never connect to production instances or customer data

### playwright

**Purpose**: Cross-browser automation and testing

**Available Tools**:

- **Browser Control**: `browser_install`, `browser_tabs`, `browser_close`, `browser_resize`
- **Navigation**: `browser_navigate`, `browser_navigate_back`
- **Interaction**: `browser_click`, `browser_hover`, `browser_drag`, `browser_press_key`, `browser_type`
- **Forms**: `browser_fill_form`, `browser_select_option`, `browser_file_upload`
- **Inspection**: `browser_take_screenshot`, `browser_snapshot`, `browser_console_messages`, `browser_network_requests`
- **Scripting**: `browser_evaluate`
- **Utilities**: `browser_wait_for`, `browser_handle_dialog`

**Guidelines**:

- Use `browser_fill_form` for comprehensive form testing in Filament resources
- Leverage `browser_snapshot` for accessibility validation during UI development
- Implement `browser_wait_for` to handle dynamic Livewire component updates
- Use `browser_network_requests` to verify API calls during user flows
- Enable in user config for cross-browser testing

### firecrawl

**Purpose**: Web scraping and external data extraction

**Available Tools**:

- **Content Extraction**: `firecrawl_scrape`, `firecrawl_extract`
- **Discovery**: `firecrawl_map`, `firecrawl_search`
- **Batch Processing**: `firecrawl_crawl`, `firecrawl_check_crawl_status`

**Guidelines**:

- Use `firecrawl_map` for comprehensive website structure analysis
- Leverage `firecrawl_extract` for structured data parsing from reference documentation
- Implement 1-second delays between requests minimum
- Validate and sanitize all scraped data before database insertion
- API key required: `FIRECRAWL_API_KEY` in `.env`

### context7

**Purpose**: Enhanced context understanding and library documentation

**Available Tools**:

- **Documentation**: `resolve-library-id`, `get-library-docs`

**Guidelines**:

- Use `get-library-docs` for Laravel, Filament, and Livewire version-specific documentation
- Leverage `resolve-library-id` to ensure correct package references in implementation
- Cross-reference parsed requirements with actual code implementation
- API key required: `CONTEXT7_API_KEY` in `.env`

### fetch

**Purpose**: HTTP requests and API interactions

**Available Tools**:

- **Content Fetching**: `fetch`

**Guidelines**:

- Use for API endpoint testing and external service integration
- Retrieve external documentation (Laravel, Filament, Livewire official docs)
- Implement proper error handling and timeout management
- Validate all API responses before processing
- Never call production endpoints or customer-facing APIs
- No API key required (open access)

### laravel-boost

**Purpose**: Laravel-specific development operations

**Available Tools** (16 total):

- **Application Info**: `application-info`, `get-config`, `list-available-config-keys`, `list-available-env-vars`
- **Database**: `database-connections`, `database-query`, `database-schema`
- **Logging**: `browser-logs`, `read-log-entries`, `last-error`
- **Development**: `list-artisan-commands`, `list-routes`, `tinker`
- **Documentation**: `search-docs`
- **Utilities**: `get-absolute-url`, `report-feedback`

**Guidelines**:

- Use `database-schema` to verify migration impacts before implementation
- Leverage `tinker` for quick code experimentation and debugging (execute PHP code in Laravel context)
- Use `search-docs` for Laravel version-specific documentation lookups (searches official Laravel docs)
- Check `last-error` and `read-log-entries` during bug investigation
- Use `database-query` for inspecting database state (read-only queries recommended)
- Configuration: `APP_ENV=local`, `MCP_CONNECTION_MODE=persistent`

**Verification**:

```bash
php artisan boost:mcp --help  # List available tools and capabilities
```

### deepl

**Purpose**: Bahasa Melayu ↔ English translation (CRITICAL for ICTServe)

**Available Tools**:

- **Language Management**: `get-source-languages`, `get-target-languages`
- **Translation**: `translate-text`, `rephrase-text`

**Guidelines**:

- Use for translating ICTServe UI text between Bahasa Melayu and English
- Support Malaysian localization requirements (primary: Bahasa Melayu, secondary: English)
- Validate translations with native speakers before deployment
- API key required: `DEEPL_API_KEY` in `.env`
- Critical for PDPA 2010 compliance (Malaysian privacy law requires local language support)

### redis

**Purpose**: Redis database operations

**Available Tools**:

- Redis CLI operations (full set available when server running)

**Guidelines**:

- Enable when Redis server configured and running (`redis://localhost:6379/0`)
- Use for cache inspection, session debugging, queue monitoring
- Development and staging only (never production Redis)
- Server must be running before enabling MCP server

### github (DISABLED - Optional)

**Purpose**: Repository management and collaboration

**Available Tools** (when enabled):

- **File Operations**: `create_or_update_file`, `get_file`
- **Issue Management**: `create_issue`, `list_issues`, `search_issues`, `update_issue`, `add_issue_comment`
- **PR Operations**: `create_pull_request`, `list_pull_requests`, `merge_pull_request`
- **Repository**: `list_repositories`, `get_repository`, `list_commits`
- **Branch Management**: `create_branch`
- **Code Search**: `search_code`

**Current Status**: DISABLED (requires token configuration fix)

**To Enable**:

1. Fix token configuration in workspace config (remove `$input:...` wrapper)
2. Or add to user config with direct token value
3. Set `disabled: false` in workspace config
4. Restart Kiro IDE

**Guidelines** (when enabled):

- Follow PR workflows defined in `AGENTS.md`
- Use conventional commits and proper PR descriptions
- Never bypass branch protection rules
- Coordinate with team review processes

### gitkraken (DISABLED - Optional)

**Purpose**: AI-powered git workflows, commit messages, PR descriptions

**Available Tools** (when enabled):

- GitKraken CLI operations (AI-assisted git commands)

**Current Status**: DISABLED (requires authentication)

**To Enable**:

1. Run `gk auth login` in terminal
2. Authenticate with GitKraken account
3. Set `disabled: false` in workspace config
4. Restart Kiro IDE

**Guidelines** (when enabled):

- Use for generating AI-powered commit messages
- Leverage for PR description generation
- Use for complex git workflows

## Error Handling & Recovery

### MCP Connection Errors

**MCP Error -32000 (Connection closed)**:

- **Meaning**: Server process started but terminated immediately
- **Common Causes**:
  - Missing authentication (e.g., GitKraken not logged in)
  - Invalid API keys or token resolution issues
  - Missing dependencies or package installation failures
- **Resolution**:
  - Check server-specific authentication requirements
  - Verify API keys in `.env` file
  - Test package standalone: `npx -y [package-name] --help`
  - Disable problematic server until prerequisites met

**Recent Resolution** (2025-11-01):

- GitHub and GitKraken servers disabled due to authentication issues
- GitHub: Token resolution issue with `$input:...` syntax in Kiro IDE
- GitKraken: Requires `gk auth login` before MCP server can function
- Solution: Disabled both optional servers, maintaining 10 working servers
- Documentation: `.kiro/settings/MCP_ISSUE_RESOLUTION.txt`

### MCP Availability

- If critical MCP servers are unavailable, pause dependent operations
- Fall back to native capabilities with documented limitations
- Log MCP connectivity issues in application logs

### Failure Scenarios

**Database Operations**:

- Use `laravel-boost` with transaction wrappers for data safety
- Verify successful completion before committing changes
- Maintain rollback scripts for all migrations

**External Services**:

- Implement exponential backoff for `fetch` and `firecrawl`
- Validate external data schemas before processing
- Handle API rate limiting gracefully with queue-based retries

**Authentication Failures**:

- Check `.env` for correct API keys
- Verify user config has actual key values (not placeholders)
- Test authentication separately before enabling MCP server

## Troubleshooting Guide

### Server Won't Connect

1. **Check server status** in Kiro IDE MCP panel
2. **Test package installation**: `npx -y [package-name] --help`
3. **Verify API keys** in `.env` and user config
4. **Check authentication** (e.g., `gk auth login` for GitKraken)
5. **Review logs** in Kiro IDE for specific error messages
6. **Restart Kiro IDE** after configuration changes

### API Key Issues

1. **Verify .env file** has correct keys: `CONTEXT7_API_KEY`, `FIRECRAWL_API_KEY`, `DEEPL_API_KEY`
2. **Check user config** has actual values (not `$input:...` placeholders)
3. **Test keys separately** using package CLI tools
4. **Rotate keys** if compromised or expired

### Package Installation Failures

1. **Check Node.js version**: `node --version` (ensure v18+)
2. **Clear npm cache**: `npm cache clean --force`
3. **Reinstall package**: `npm install -g [package-name]`
4. **Check network connectivity** (proxy, firewall)

## Compliance & Audit Requirements

- Log significant MCP operations per `docs/D09_DATABASE_DOCUMENTATION.md`
- Maintain audit trail between MCP work and requirement documents
- Document MCP-assisted features in PR descriptions
- Regular security review of MCP usage patterns
- Never use MCP to process or store PDPA-sensitive data

## Integration with Existing Workflows

- Coordinate all operations with `behavior.md` guardrails
- Follow collaboration protocols in `AGENTS.md`
- Maintain quality gates (`pint`, `phpstan`, tests) for MCP-generated code
- Ensure all MCP activities are traceable to requirement documents (D00–D15)

## Practical Examples

### MCP-Assisted Feature Development

**Scenario: ICTServe Analytics Dashboard Enhancement**

1. **Context Discovery**: Query `memory` for existing ICTServe patterns:

   ```json
   search_nodes "dashboard analytics helpdesk asset loan"
   open_nodes "ictserve_system_spec"
   ```

2. **Requirements Analysis**: Use `sequentialthinking` to decompose: data models → Eloquent relationships → Filament resource → Livewire charts
3. **Architecture Verification**: Query `memory` for compliance requirements:

   ```json
   open_nodes "ictserve_compliance_standards"
   search_nodes "WCAG dashboard performance"
   ```

4. **Technical Implementation**:
   - Use `laravel-boost` `database-schema` to verify table structures
   - Use `laravel-boost` `search-docs` to research Filament chart components
   - Generate migration and Filament resource following stored ICTServe patterns
5. **Testing & Validation**:
   - Create E2E tests with `playwright` using `browser_fill_form` and `browser_wait_for`
   - Debug chart rendering with `chrome-devtools` `performance_start_trace`
   - Verify WCAG 2.2 AA compliance with stored accessibility patterns
6. **Localization**: Translate UI text with `deepl` (Bahasa Melayu ↔ English)
7. **Documentation & Storage**: Document implementation in `memory`:

   ```json
   create_entities [
     "name": "analytics_dashboard_pattern",
     "entityType": "solution_pattern",
     "observations": [
       "Pattern: Unified helpdesk + asset loan analytics",
       "Filament widgets: Statistics cards with WCAG compliant colors",
       "Performance: Achieved LCP <2.5s with caching strategy",
       "Integration: Cross-module data aggregation approach"
   
   ]
   ```

### Performance Optimization

**Scenario: ICTServe Filament Admin Performance Issues**

1. **Historical Context**: Check `memory` for existing optimization patterns:

   ```json
   search_nodes "performance optimization Filament N+1 queries"
   open_nodes "ictserve_technical_architecture"
   ```

2. **Performance Measurement**:
   - Use `playwright` `browser_navigate` and `browser_network_requests` to measure load times
   - Run `chrome-devtools` `performance_start_trace` to identify bottlenecks
   - Verify against stored Core Web Vitals targets (LCP <2.5s, FID <100ms, CLS <0.1)
3. **Database Analysis**:
   - Use `laravel-boost` `database-query` to analyze slow database calls
   - Use `laravel-boost` `read-log-entries` to check for N+1 queries
   - Cross-reference with stored ICTServe database schema patterns
4. **Solution Implementation**:
   - Apply stored optimization patterns from `memory`
   - Implement eager loading, caching, or query optimization
   - Follow ICTServe architecture guidelines for Redis caching
5. **Validation & Documentation**:
   - Validate improvements with `playwright` and `chrome-devtools`
   - Verify WCAG 2.2 AA compliance maintained during optimization
   - Document optimization pattern in `memory`:

   ```json
   add_observations [
     "entityName": "ictserve_technical_architecture",
     "contents": [
       "Performance optimization: Filament resource eager loading pattern",
       "Solution: with(['user', 'division', 'assets']) for helpdesk tickets",
       "Result: 60% query reduction, LCP improved from 3.2s to 1.8s",
       "Reusable for: All Filament resources with relationships"
   
   ]
   ```

### Bug Investigation

**Scenario: ICTServe Guest Form Submission Error**

1. **Historical Context**: Search `memory` for similar issues:

   ```json
   search_nodes "guest form validation error livewire"
   search_nodes "helpdesk ticket submission error"
   ```

2. **Issue Reproduction**:
   - Reproduce with `playwright` `browser_fill_form` and `browser_click`
   - Test both guest and authenticated form paths per ICTServe hybrid architecture
3. **Error Analysis**:
   - Inspect browser console with `chrome-devtools` `list_console_messages`
   - Check backend error with `laravel-boost` `last-error`
   - Query database state with `laravel-boost` `database-query`
4. **Root Cause Investigation**:
   - Use `laravel-boost` `tinker` to test model logic
   - Verify against stored ICTServe validation patterns
   - Check WCAG compliance impact of error handling
5. **Solution & Testing**:
   - Apply stored solution patterns from `memory`
   - Implement fix following ICTServe guest-only architecture
   - Create regression test covering both guest and authenticated scenarios
6. **Documentation & Pattern Storage**:

   ```json
   create_entities [
     "name": "guest_form_validation_debug_pattern",
     "entityType": "solution_pattern", 
     "observations": [
       "Issue: Livewire validation failing on guest forms",
       "Root cause: Missing CSRF token refresh on long-lived sessions",
       "Solution: wire:model.lazy + session token refresh",
       "WCAG impact: Ensure error messages have proper ARIA attributes",
       "Testing: Cover both guest and authenticated form paths"
   
   ]
   ```

7. **Update Implementation Status**:

   ```json
   add_observations [
     "entityName": "ictserve_implementation_status",
     "contents": [
       "Bug fix: Guest form validation error resolved",
       "Pattern documented: Livewire CSRF handling for long sessions",
       "Testing enhanced: Added regression tests for form validation"
   
   ]
   ```

## References

- **Global agent policy**: `AGENTS.md`
- **Behavior guidelines**: `.kiro/steering/behavior.md`
- **Technical requirements**: `docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md`
- **Security standards**: `docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md`
- **Database documentation**: `docs/D09_DATABASE_DOCUMENTATION.md`
- **MCP troubleshooting**: `.kiro/settings/MCP_ISSUE_RESOLUTION.txt`

## Memory MCP Server Enforcement Framework

### Absolute Mandatory Requirements

**ZERO TOLERANCE POLICY**: Memory MCP server integration is the FOUNDATION of all MCP operations. No MCP tool may be used without proper memory integration.

### Universal Enforcement Rules

**Rule 1: Memory-First Principle**

- ALL MCP operations MUST begin with memory context queries
- NO tool usage without prior pattern verification
- EVERY operation MUST contribute to the knowledge graph

**Rule 2: Continuous Integration**

- Memory updates are REQUIRED during all MCP operations
- Pattern storage is MANDATORY for all discoveries
- Cross-tool integration MUST reference stored context

**Rule 3: Complete Documentation**

- ALL MCP work MUST conclude with memory documentation
- Solution patterns MUST be stored for reuse
- Implementation status MUST be updated with progress

### Enforcement Mechanisms

**Pre-Operation Validation**:

```bash
# MANDATORY before ANY MCP tool usage
search_nodes "[relevant_keywords]"
open_nodes "ictserve_implementation_status"
open_nodes "ictserve_compliance_standards"
```

**Operation Monitoring**:

- Real-time memory integration verification
- Pattern documentation compliance checking
- Cross-reference integrity validation

**Post-Operation Requirements**:

```bash
# REQUIRED after ALL MCP operations
create_entities ["name": "operation_pattern", ...]
add_observations ["entityName": "ictserve_implementation_status", ...]
create_relations ["from": "new_pattern", "to": "existing_spec", ...]
```

### Violation Response Protocol

**Critical Violations** (Immediate Action Required):

1. **MCP tool usage without memory context** → TERMINATE operation, query memory, restart
2. **Silent operations without documentation** → INVALID work, must document immediately
3. **Completion without pattern storage** → PROHIBITED, must store before proceeding
4. **Cross-tool usage without integration** → HALT workflow, integrate with memory

**Compliance Verification Checklist**:

- [ ] Memory context retrieved before MCP operations
- [ ] Existing patterns queried and verified
- [ ] New discoveries documented in memory
- [ ] Implementation status updated with progress
- [ ] Solution patterns stored for reuse
- [ ] Cross-references created between entities

### Audit and Quality Assurance

**Continuous Monitoring**:

- Memory integration compliance tracking
- Pattern usage and reuse verification
- Documentation completeness validation
- Cross-session continuity maintenance

**Quality Metrics**:

- 100% memory integration compliance required
- Zero tolerance for undocumented operations
- Complete pattern coverage for all solutions
- Full traceability through knowledge graph

---

**Last Updated**: 2025-11-01  
**Active Servers**: 10/12 (github and gitkraken disabled pending authentication)  
**Critical Servers**: laravel-boost ✅ | deepl ✅ | **memory ✅ (MANDATORY)**  
**Enforcement Status**: ACTIVE - Memory integration is REQUIRED for ALL operations
