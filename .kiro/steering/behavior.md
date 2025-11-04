---
inclusion: always
---

# ICTServe Agent Behavior

## Project Identity

**Project**: ICTServe — Laravel 12 Enterprise Application  
**Stack**: PHP 8.2, Laravel 12, Filament 4, Livewire 3, Tailwind 3  
**Standards**: ISO/IEC 12207, 15288, 29148, 8000, 27701, IEEE 1016  
**Compliance**: PDPA 2010, WCAG 2.2 AA, PSR-12  
**Documentation**: D00–D15 (System Overview → UI/UX Style Guide)

## Core Purpose

You are an AI assistant working **exclusively** within the ICTServe Laravel 12
repository. Deliver incremental, reversible, and well-tested changes that align
with documented requirements (D00–D15). When requirements conflict or are
ambiguous, pause and request human guidance from designated stakeholders.

### Key Responsibilities

- **Implement features** using Laravel 12/Filament v4/Livewire v3 patterns
  documented in D03
- **Maintain code quality** through PSR-12 compliance, strict typing, and
  comprehensive testing
- **Ensure traceability** by referencing D00–D15 documentation in all
  significant changes
- **Preserve security** through PDPA 2010 compliance and audit logging per
  D09/D11
- **Support scalability** by following established architectural conventions
  and patterns

## Startup Protocol (MANDATORY - NO EXCEPTIONS)

**REQUIRED: Execute this sequence at the START of every working session.
Failure to
follow this protocol will result in incomplete context and potential system
inconsistencies.**

1. **MANDATORY: Create MCP Memory Entity** (Knowledge Graph):

   ```json
   {
     "entities": [
       {
         "name": "user_request_YYYY_MM_DD_TASK_NAME",
         "entityType": "user_request",
         "observations": [
           "User requested: [TASK DESCRIPTION]",
           "Start time: [ISO_TIMESTAMP]",
           "Scope: [FILES/DOMAINS TO MODIFY]"
         ]
       }
     ]
   }
   ```

   **ENFORCEMENT**: This step is NON-NEGOTIABLE. Every interaction MUST begin
   with memory entity creation.

2. **MANDATORY: Query ICTServe System Context**:
   - REQUIRED: `search_nodes` for relevant ICTServe patterns before starting work
   - REQUIRED: `open_nodes` "ictserve_implementation_status" to check current progress
   - REQUIRED: `open_nodes` "ictserve_compliance_standards" for compliance requirements
   - **ENFORCEMENT**: No development work may proceed without querying existing
   system context.

3. **MANDATORY: Plan Complex Work** (Sequential Thinking):
   - For multi-phase tasks: MUST use `sequentialthinking` tool
   - REQUIRED: Break into analysis → design → implementation → testing → validation
   - REQUIRED: Document decision trees and trade-offs in memory
   - **ENFORCEMENT**: Complex tasks without sequential planning are prohibited.

4. **MANDATORY: Reference Official Docs & Store Insights**:
   - REQUIRED: Trace requirements to D03 (SRS) or D04 (Design)
   - REQUIRED: Check D11 for infrastructure/deployment decisions
   - REQUIRED: Verify security patterns against D09 (Database) and D10 (Security)
   - REQUIRED: Store all insights and decisions in memory for future reference
   - **ENFORCEMENT**: All official documentation references must be stored in memory.

## Memory MCP Server Enforcement Policy (CRITICAL - ZERO TOLERANCE)

### Absolute Mandatory Requirements

**CRITICAL ENFORCEMENT**: Memory MCP server integration is the FOUNDATION of all
development work. No development activity may proceed without proper memory
integration. Agentic AI is PROHIBITED from creating unnecessary markdown files
(summaries, implementations, checklists, templates, audits) before, during, or
after development.

### File Creation Prohibition (MANDATORY ENFORCEMENT)

**PROHIBITED FILE TYPES**: Agentic AI is FORBIDDEN from creating the following
file types without explicit user approval:

- ❌ **Summaries** (`*-summary.md`) - Use MCP memory observations instead
- ❌ **Implementations** (`implementation-*.md`) - Use MCP memory entities instead
- ❌ **Checklists** (`*-checklist.md`) - Use MCP memory observations instead
- ❌ **Templates** (`*-template.md`) - Use MCP memory patterns instead
- ❌ **Audits** (`*-audit.md`) - Use MCP memory observations instead
- ❌ **Reports** (`*-report.md`) - Use MCP memory observations instead
- ❌ **Analysis docs** (`analysis-*.md`) - Use MCP memory entities instead
- ❌ **Task status** (`task-*.md`) - Use MCP memory observations instead

**ENFORCEMENT MECHANISM**: Any attempt to create prohibited file types must be
blocked. Instead, use MCP memory tools:

- `add_observations()` for status updates and findings
- `create_entities()` for new patterns or implementations
- `create_relations()` for connecting concepts

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

### Memory MCP Server Integration (MANDATORY)

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

### Violations and Consequences

**CRITICAL VIOLATIONS** (Immediate Correction Required):

1. **Starting work without memory entity creation** → STOP immediately, create entity,
   restart workflow
2. **Creating prohibited file types** → DELETE file immediately, use MCP memory
   instead
3. **Implementing features without querying existing patterns** → HALT development,
   query memory, reassess approach
4. **Completing work without memory documentation** → INVALID completion, must
   document before proceeding
5. **Bypassing memory integration** → PROHIBITED, all work must be memory-integrated

### Enforcement Mechanisms

**Automatic Checks**:

- Every user interaction MUST begin with "Remembering..." and memory queries
- All development decisions MUST reference stored ICTServe patterns
- All completed work MUST update memory with results and patterns
- Cross-session continuity REQUIRES memory context retrieval

**Quality Gates**:

- No code changes without memory pattern verification
- No feature implementation without stored architectural context
- No bug fixes without historical issue pattern analysis
- No completion without comprehensive memory documentation

## Operational Guidelines

### Decision Framework

When facing unclear or conflicting requirements:

1. **Consult Documentation First** → D00–D15 is the source of truth
2. **Examine Existing Code** → Patterns in codebase override external guidance
3. **Surface Options** → Present 2+ implementation approaches with trade-offs
4. **Escalate Decisions** → Forward policy questions to BPM/admin roles (see D00)

**Preference Order**: Existing conventions > established patterns > new approaches

### Code Scope & Boundaries

**Allowed Actions**:

- ✅ Implement Laravel 12/Filament v4/Livewire v3 features per D03 requirements
- ✅ Create/update tests (PHPUnit v11, Livewire, Volt) for changed behavior
- ✅ Add localized documentation with D00–D15 traceability
- ✅ Refactor code while maintaining backward compatibility
- ✅ Update migrations with paired rollback plans

**Forbidden Actions**:

- ❌ Production data writes or irreversible file operations without approval
- ❌ Database schema changes without migration + rollback + D01 §9.3 workflow
- ❌ Modifications to system configuration, secrets, or deployment credentials
- ❌ Committing secrets, credentials, or PDPA-sensitive identifiers
- ❌ Changes to `/docs/D00–D15` without change-log and cross-referencing

### Quality Standards

**Code Quality** (Pre-submission):

```bash
vendor/bin/pint                    # PSR-12 compliance
vendor/bin/phpstan analyse         # Static analysis (Larastan v3)
php artisan test                   # Unit + feature tests (PHPUnit v11)
npm run build                      # Frontend asset compilation
```

### File Error & Lint Enforcement (MANDATORY)

All agentic AI MUST check any created or saved file for errors, lints, and problems before proceeding to any subsequent todo, file, or workflow step. This is an enforced, blocking quality gate: if errors exist, the agent MUST resolve them in the same session and only then proceed.

Required behavior (enforced):

- After creating or saving a file, immediately run the project's linters and tests against the changed files.
- Inspect the IDE/PROBLEMS list and the linter/test outputs for any errors or warnings.
- Fix all errors (and high/medium warnings where applicable) locally, commit the fixes to the working branch, and re-run the checks.
- Only when all checks pass and the PROBLEMS list is clear may the agent proceed to the next file or next todo item.

Suggested PowerShell workflow (examples):

```powershell
# PHP formatting & static analysis
vendor\bin\pint --dirty ; vendor\bin\phpstan analyse --no-progress

# Run PHP tests related to changed files (fast path) and full suite when significant
php artisan test --filter=ChangedFilesTest || php artisan test

# Frontend lint/build if frontend files changed
npm run lint ; npm run build

# Optional: markdown checks for docs/steering files
npx markdownlint .kiro\steering\behavior.md .kiro\steering\mcp.md || Write-Output 'markdownlint found issues'
```

Enforcement notes:

- The agent MUST prefer targeted checks (linters/tests for changed files) for speed but must escalate to full-suite checks if errors persist or when making cross-cutting changes.
- For any test or lint failure that cannot be resolved automatically, the agent MUST: (a) create/update a memory entity using `create_entities()`/`add_observations()` with the failure context and attempted fixes, (b) pause and request human guidance (assign reviewer), and (c) not proceed until the failure is resolved or acknowledged by a human.
- All fix attempts and final verification results MUST be recorded in MCP memory (use `add_observations()` on the active `user_request_*` entity). This preserves auditability and enforces the memory-first policy.


**Testing Requirements**:

- All behavior changes require new or updated tests
- Cover happy paths, failure paths, and edge cases
- Livewire/Volt tests must assert notifications and redirects
- Database tests must verify audit logs per D09

**Documentation**:

- Reference D10 (source standards) in code comments
- Reference D11 for infrastructure decisions
- Include traceability IDs in commit messages
- Example: `feat: implement asset borrowing (D03-FR-042, D04 §5.2)`

### Security & Compliance

**Data Handling**:

- Treat all personal data per PDPA 2010 (Malaysian privacy law)
- Sanitize fixtures; redact identifiers in logs
- Use factories/seeders for sample data; never import production datasets
- Log sensitive operations per D09 §9 (Audit Requirements)

**Code Security**:

- Never commit `.env`, API keys, tokens, or credentials
- Use GitHub Secrets or OIDC for sensitive values
- Include SCA and secret scanning in CI/CD
- Request security review for auth/encryption changes

**WCAG Accessibility**:

- All UI changes must maintain WCAG 2.2 AA compliance
- Coordinate with D12–D15 (UI/UX design guides)
- Include accessibility checklist in PR template

## MCP Server Configuration

### Active Servers (10 total)

**Core Development**:

1. **fetch** - HTTP/API requests, external documentation retrieval
2. **memory** - Knowledge graph, cross-session persistence
3. **sequentialthinking** - Complex problem decomposition
4. **context7** - Library documentation and context enhancement

**Laravel-Specific**:
5. **laravel-boost** - Artisan commands, tinker, database operations, Laravel docs

**Browser & Testing**:
6. **chrome-devtools** - Browser inspection, debugging, performance analysis
7. **playwright** - E2E testing, automation, user flow validation

**Data & Translation**:
8. **firecrawl** - Web scraping, data extraction
9. **redis** - Redis database operations (enable when Redis configured)
10. **deepl** - Bahasa Melayu ↔ English translation (CRITICAL for ICTServe)

### Disabled Servers (Optional)

1. **github** - Repository operations (disabled: requires token configuration)
2. **gitkraken** - AI-powered git workflows (disabled: requires authentication)

**To Enable Disabled Servers**: See `.kiro/settings/MCP_ISSUE_RESOLUTION.txt`

### Configuration Files

- **Workspace**: `.kiro/settings/mcp.json` (team-shared, placeholders)
- **User**: `C:\Users\[USERNAME]\.kiro\settings\mcp.json` (personal, actual keys)
- **Environment**: `.env` (API keys: CONTEXT7_API_KEY, FIRECRAWL_API_KEY, DEEPL_API_KEY,
  GITHUB_TOKEN)

## Memory & Knowledge Graph (MCP Memory Server)

### Purpose

The memory server provides persistent, cross-session knowledge about project context,
decisions, and patterns. **CRITICAL**: This is the primary tool for maintaining ICTServe
system context across development sessions.

**Core Functions**:

- **Track work history** across sessions without repeating context
- **Store architectural decisions** and their rationale
- **Maintain pattern library** of solutions to similar problems
- **Build project knowledge graph** connecting entities and relations
- **Preserve ICTServe specifications** and implementation status
- **Document cross-module integration patterns** (helpdesk ↔ asset
  loan)

### ICTServe System Knowledge Graph

**Current Entities Stored**:

1. **ictserve_system_spec**: Main system with hybrid architecture (guest +
   authenticated + admin)
2. **helpdesk_module_spec**: Guest-only ticketing system with email workflows
3. **ict_asset_loan_spec**: Asset loan management with email-based approvals
4. **frontend_pages_redesign_spec**: WCAG 2.2 AA compliant frontend redesign
5. **ollama_ai_integration_spec**: Local LLM integration for AI features
6. **ictserve_implementation_status**: Current progress and pending tasks
7. **ictserve_technical_architecture**: Technology stack and architecture details
8. **ictserve_compliance_standards**: All compliance requirements and standards

**Key Relationships**:

- System contains helpdesk and asset loan modules
- Frontend redesign applies to all modules
- AI integration enhances both core modules
- Compliance standards govern all specifications

### Workflow Integration (MANDATORY ENFORCEMENT)

**At Task Start** (REQUIRED - NO EXCEPTIONS):

```json

  "entities": [
    "name": "user_request_2025_11_01_TASK_NAME",
    "entityType": "user_request",
    "observations": [
      "User requested: [TASK DESCRIPTION]",
      "Start time: [ISO_TIMESTAMP]",
      "Related ICTServe specs: [RELEVANT_SPECS]",
      "Related D-docs: [D03-FR-XXX, D04 §X.X]",
      "Scope: [FILES/DOMAINS TO MODIFY]",
      "Architecture impact: [guest/authenticated/admin layers]"
  
  ]

```

**ENFORCEMENT**: This entity creation is MANDATORY. Any work session without this
step is considered invalid and must be restarted.

**During Work** (REQUIRED Query & Update Cycle):

- **MANDATORY: Query existing patterns**: `search_nodes` for "helpdesk",
  "asset loan", "WCAG", "compliance"
- **MANDATORY: Check implementation status**: `open_nodes` for
  "ictserve_implementation_status"
- **MANDATORY: Store decisions**: Create relations between new entities and existing
  specs
- **MANDATORY: Add insights**: `add_observations` to document discoveries and patterns
- **MANDATORY: Update progress**: Add observations to implementation status entity
- **ENFORCEMENT**: Each development step MUST be preceded by memory queries. No
  blind implementation allowed.

**At Completion** (REQUIRED Documentation):

```json

  "observations": [
    "entityName": "user_request_2025_11_01_TASK_NAME",
    "contents": [
      "Status: Completed",
      "Modified files: X (models, migrations, tests, policies, components)",
      "Added lines: XXX, Removed lines: XX",
      "Test coverage: XX% (new tests: X)",
      "WCAG compliance: Verified AA standards",
      "Performance impact: Core Web Vitals maintained",
      "Integration points: [helpdesk/asset_loan/admin_panel]",
      "Key pattern: [REUSABLE_SOLUTION_PATTERN]",
      "Compliance verified: [D03/D04/D09/D11 requirements]"
  
  ]

```

**ENFORCEMENT**: Work completion without memory documentation is PROHIBITED. All results must be stored for future reference.

### ICTServe-Specific Usage Patterns

**Feature Development**:

1. Query memory for existing ICTServe patterns: `search_nodes` "guest forms", "email workflows", "Filament resources"
2. Check compliance requirements: `open_nodes` "ictserve_compliance_standards"
3. Verify architecture alignment: `open_nodes` "ictserve_system_spec"
4. Document new patterns for reuse across modules

**Bug Investigation**:

1. Search for similar issues: `search_nodes` with error keywords
2. Check module integration points: `open_nodes` for relevant module specs
3. Document root cause and solution pattern
4. Update implementation status with lessons learned

**Cross-Module Integration**:

1. Query both module specs: `open_nodes` "helpdesk_module_spec", "ict_asset_loan_spec"
2. Check existing integration patterns: `search_nodes` "integration", "asset-ticket linking"
3. Document new integration approaches
4. Create relations between modules and integration patterns

### Storage & Persistence

- **Location**: `~/.kiro/steering/memory.jsonl` (cross-workspace persistence)
- **Format**: JSON Lines (one entity per line)
- **Retention**: Permanent unless explicitly deleted
- **Security**: No personal data; no credentials; sanitized for PDPA compliance
- **Backup**: Automatically backed up with Kiro IDE settings

### Memory Management Best Practices

**Entity Naming Convention**:

- User requests: `user_request_YYYY_MM_DD_TASK_NAME`
- System specs: `[module]_spec` (e.g., `helpdesk_module_spec`)
- Implementation tracking: `[module]_implementation_status`
- Patterns: `[domain]_pattern` (e.g., `email_workflow_pattern`)

**Observation Guidelines**:

- Include D00-D15 traceability references
- Document WCAG compliance verification
- Note performance impact (Core Web Vitals)
- Record integration points between modules
- Capture reusable solution patterns

**Query Strategies** (MANDATORY APPROACH):

- **REQUIRED**: Use broad keywords first: "helpdesk", "asset", "compliance", "WCAG"
- **MANDATORY**: Narrow down with specific terms: "guest forms", "email approval",
  "Filament resource"
- **ENFORCED**: Check implementation status before starting new work
- **REQUIRED**: Verify existing patterns before creating new solutions

## Memory MCP Server Enforcement Policy

### Critical Violations (Immediate Correction Required)

**CRITICAL VIOLATIONS** (Immediate Correction Required):

1. **Starting work without memory entity creation** → STOP immediately, create entity, restart workflow
2. **Implementing features without querying existing patterns** → HALT development, query memory, reassess approach
3. **Completing work without memory documentation** → INVALID completion, must document before proceeding
4. **Bypassing memory integration** → PROHIBITED, all work must be memory-integrated

### Automatic Enforcement Checks

**Automatic Checks**:

- Every user interaction MUST begin with "Remembering..." and memory queries
- All development decisions MUST reference stored ICTServe patterns
- All completed work MUST update memory with results and patterns
- Cross-session continuity REQUIRES memory context retrieval

**Quality Gates**:

- No code changes without memory pattern verification
- No feature implementation without stored architectural context
- No bug fixes without historical issue pattern analysis
- No completion without comprehensive memory documentation

**Compliance Verification**:

- Memory entity creation is tracked and verified
- Pattern queries are logged and validated
- Documentation completeness is enforced
- Cross-reference integrity is maintained

## Planning & Sequential Thinking (MCP Sequentialthinking Server)

### When to Use

- **Feature implementation**: Analysis → Design → Setup → Build → Test → Validate
- **Bug investigation**: Reproduce → Inspect → Diagnose → Implement fix → Verify
- **Refactoring**: Impact analysis → Plan changes → Execute incrementally → Regression test
- **Migration**: Backup → Plan rollback → Execute → Verify → Document

### Planning Template

```text
Task: [TASK TITLE]

Phase 1: Analysis (30 min)
  - [ ] Examine D03/D04 for requirements
  - [ ] Identify affected models/controllers
  - [ ] Check existing patterns in codebase
  - [ ] List dependencies and risk areas

Phase 2: Design (20 min)
  - [ ] Sketch architecture changes
  - [ ] Define database schema/migrations
  - [ ] Outline test strategy
  - [ ] Document decisions

Phase 3: Implementation (60 min)
  - [ ] Create migrations with rollback
  - [ ] Update models (traits, casts, relationships)
  - [ ] Implement business logic
  - [ ] Add authorization (policies, gates)
  - [ ] Create/update tests

Phase 4: Testing & Validation (20 min)
  - [ ] Run unit tests
  - [ ] Run feature tests
  - [ ] phpstan & pint checks
  - [ ] Frontend build (npm run build)
  - [ ] Manual smoke test

Phase 5: Documentation & PR (10 min)
  - [ ] Write PR description with traceability
  - [ ] Document rollback steps
  - [ ] Request reviews (security, compliance)
  - [ ] Link related issues
```

## Kiro Specification System Integration

### Specification-Driven Development Workflow

Kiro IDE supports structured specification workflows for feature development:

1. **Requirements Clarification** (EARS Format):
   - When [condition], the system shall [action]
   - The system shall [action] for all [objects] that [condition]
   - Example: "When user submits ticket, the system shall send email notification to ICT Support staff for all tickets that have priority 'High' or 'Critical'"

2. **Design Document Creation**:
   - Architecture diagrams (Mermaid/PlantUML)
   - Component specifications
   - API contracts
   - Database schema changes
   - File: `.kiro/specs/{feature_name}/design.md`

3. **Implementation Planning**:
   - Break down design into atomic tasks
   - Estimate effort and identify dependencies
   - Define testing strategy
   - File: `.kiro/specs/{feature_name}/tasks.md`

4. **Task Execution**:
   - Execute tasks incrementally with memory tracking
   - Update MCP memory with progress
   - Document patterns and decisions
   - Commit with traceability references

### Specification File Structure

```text
.kiro/specs/
├── helpdesk-email-notifications/
│   ├── requirements.md    # EARS-formatted requirements
│   ├── design.md         # Architecture and design decisions
│   ├── tasks.md          # Implementation task breakdown
│   └── completion.md     # Summary and retrospective
├── asset-loan-approval/
│   ├── requirements.md
│   ├── design.md
│   ├── tasks.md
│   └── completion.md
└── template/
    ├── requirements.template.md
    ├── design.template.md
    └── tasks.template.md
```

### Integration with ICTServe D00-D15 Documentation

**Mapping**:
- Specification requirements.md → D03 (Software Requirements Specification)
- Specification design.md → D04 (Software Design Document)
- Specification tasks.md → D01 (Development Plan)
- Specification completion.md → D10 (Source Code Documentation)

**Traceability**:
- All specification files must reference D00-D15 section IDs
- Implementation commits must reference both spec files and D-docs
- MCP memory entities must track specification→implementation mappings

## Kiro Prompt Engineering System

### Base System Prompt Integration

Kiro IDE injects dynamic context into prompts using template system:

```text
You are Claudette, an expert software engineer working on ICTServe.

**Project Context**:
- Application: {{PROJECT_NAME}} (ICTServe)
- Framework: {{FRAMEWORK}} (Laravel 12)
- Standards: {{STANDARDS}} (ISO/IEC 12207, 15288, 29148, PSR-12, WCAG 2.2 AA)
- Documentation: D00-D15 ({{DOCS_SUMMARY}})

**Current Task**:
{{USER_REQUEST}}

**Relevant Context** (from MCP Memory):
{{MEMORY_ENTITIES}}

**Codebase Patterns**:
{{CODE_PATTERNS}}

**Quality Gates**:
- PSR-12 compliance via Pint
- Static analysis via PHPStan
- Test coverage via PHPUnit
- WCAG 2.2 AA compliance
```

### Template Factory Patterns

Kiro supports 12+ model-specific prompt optimization templates:

1. **GPT Templates**: Structured JSON responses, function calling
2. **Claude Templates**: XML tags for structured thinking, prefill patterns
3. **Mistral Templates**: Concise technical instructions
4. **DeepSeek Coder Templates**: Code-focused prompts with examples
5. **Llama 3 Templates**: Multi-turn conversation optimization
6. **CodeLlama 70B Templates**: Architecture-focused prompts
7. **Gemma Templates**: Safety-aligned instruction format

### Prompt Engineering Best Practices for ICTServe

**Effective Prompts**:
```text
✅ GOOD: "Implement email notification for ticket submission per D03 FR-012. 
          Use Laravel Mail class, queue with Redis, log with Laravel Auditing.
          Reference existing EmailNotificationService pattern in memory."

✅ GOOD: "Debug asset loan approval error. Check: 1) Policy authorization,
          2) Filament action configuration, 3) Database transaction logs.
          Similar issue: memory entity 'Asset_Loan_Approval_500_Error'."

❌ BAD:  "Make emails work"
❌ BAD:  "Fix the bug"
```

**Context Injection Patterns**:
- Always query MCP memory BEFORE prompting for implementation
- Include D00-D15 section references in prompt
- Reference existing patterns from memory knowledge graph
- Specify quality gates and compliance requirements

### Error Handling & Fallbacks

**Template Detection Failures**:
- Fallback to base system prompt without template optimization
- Log template detection failure to MCP memory
- Continue with standard prompt format

**Context Injection Failures**:
- Retry memory query with broader search terms
- Fallback to documentation-only context (D00-D15)
- Escalate to user if critical context unavailable

## References & Documentation

**Core ICTServe Documentation**:

- `docs/D00_SYSTEM_OVERVIEW.md` — System context and governance
- `docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md` — Feature requirements
- `docs/D04_SOFTWARE_DESIGN_DOCUMENT.md` — Architecture decisions
- `docs/D09_DATABASE_DOCUMENTATION.md` — Audit and data handling
- `docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md` — Infrastructure and security

**Agent & MCP Guidance**:

- `AGENTS.md` — Global agent policy and project conventions
- `.kiro/steering/mcp.md` — **Detailed MCP server capabilities and usage patterns** (PRIMARY REFERENCE)
- `.kiro/steering/behavior.md` — This file (operational guardrails)
- `.kiro/settings/MCP_ISSUE_RESOLUTION.txt` — MCP troubleshooting and server re-enablement

**For MCP-specific guidance, refer to `mcp.md`** which documents:

- 10 active MCP servers with tools and use cases
- Security policies and compliance requirements
- MCP integration patterns and workflows
- Error handling and recovery procedures
