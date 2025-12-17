---
inclusion: always
---

# ICTServe Agent Behavior

## Project Context

**ICTServe v3.6.0** - Laravel 12 Enterprise Application  
**Stack**: PHP 8.2.12, Laravel 12.40.1, Filament 4.1.10, Livewire 3.7.0, Tailwind 4.1.17  
**Compliance**: PDPA 2010, WCAG 2.2 AA, PSR-12, MyGOV Digital Service Standards v2.1.0  
**Documentation**: D00–D18 system specifications  
**Language**: Bahasa Melayu only (language switcher disabled)

## Core Responsibilities

You are an AI assistant working exclusively within the ICTServe Laravel 12 repository. Deliver incremental, well-tested changes that align with D00–D18 documentation. When requirements conflict, request human guidance.

**Key Duties**:

- Implement features using Laravel 12/Filament v4/Livewire v3 patterns per D03
- Maintain PSR-12 compliance, strict typing, and comprehensive testing
- Reference D00–D18 documentation in all significant changes
- Preserve PDPA 2010 compliance and audit logging per D09/D11
- Follow established architectural conventions

## Mandatory Session Protocol

**REQUIRED at START of every session**:

1. **Create MCP Memory Entity**:

   ```json
   {
     "entities": [{
       "name": "user_request_YYYY_MM_DD_TASK_NAME",
       "entityType": "user_request", 
       "observations": [
         "User requested: [DESCRIPTION]",
         "Start time: [TIMESTAMP]",
         "Scope: [FILES/DOMAINS]"
       ]
     }]
   }
   ```

2. **Query System Context**:
   - `search_nodes` for relevant ICTServe patterns
   - `open_nodes` "ictserve_implementation_status"
   - `open_nodes` "ictserve_compliance_standards"

3. **Plan Complex Work** (use `sequentialthinking` for multi-phase tasks):
   - Analysis → Design → Implementation → Testing → Validation
   - Document decisions in memory

4. **Reference Documentation**:
   - Trace requirements to D03 (SRS) or D04 (Design)
   - Check D09/D11 for security patterns
   - Store insights in memory

## Memory Integration Requirements

**CRITICAL**: Memory MCP server integration is mandatory for all development work.

### Prohibited File Creation

**NEVER create these file types** without explicit user approval:

- `*-summary.md`, `implementation-*.md`, `*-checklist.md`
- `*-template.md`, `*-audit.md`, `*-report.md`
- `analysis-*.md`, `task-*.md`

**Use MCP memory instead**:

- `add_observations()` for status updates
- `create_entities()` for new patterns  
- `create_relations()` for connections

### Memory Workflow

**Before Work**: Query existing patterns, check implementation status
**During Work**: Store discoveries and decisions in memory
**After Work**: Document completion with full details and relations

## Development Guidelines

### Decision Framework

When facing unclear requirements:

1. **Consult D00–D18 documentation** (source of truth)
2. **Examine existing code patterns** (override external guidance)
3. **Present implementation options** with trade-offs
4. **Escalate policy questions** to BPM/admin roles

**Priority**: Existing conventions > established patterns > new approaches

### Allowed Actions

✅ **Permitted**:

- Implement Laravel 12/Filament v4/Livewire v3 features per D03
- Create/update PHPUnit 12 tests with PHP 8 attributes
- Add documentation with D00–D18 traceability
- Refactor code maintaining backward compatibility
- Update migrations with rollback plans

❌ **Forbidden**:

- Production data writes without approval
- Schema changes without migration + rollback workflow
- Modify system config, secrets, or deployment credentials
- Commit secrets, credentials, or PDPA-sensitive data
- Change `/docs/D00–D18` without proper change-log

### Quality Standards

**Pre-submission checks**:

```bash
vendor/bin/pint                # PSR-12 compliance
vendor/bin/phpstan analyse     # Static analysis
php artisan test              # PHPUnit 12 tests
npm run build                 # Frontend compilation
```

### Testing Requirements (PHPUnit 12)

**MANDATORY**: Use PHP 8 attributes, not PHPDoc annotations

```php
<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class ExampleTest extends TestCase
{
    #[Test]
    public function it_performs_operation(): void
    {
        $result = $this->performOperation();
        $this->assertInstanceOf(ExpectedClass::class, $result);
    }

    #[Test]
    #[DataProvider('validDataProvider')]
    public function it_validates_data(string $input, bool $expected): void
    {
        $this->assertSame($expected, $this->validateInput($input));
    }

    public static function validDataProvider(): array
    {
        return [
            'valid email' => ['test@example.com', true],
            'invalid email' => ['invalid-email', false],
        ];
    }
}
```

### Error & Lint Enforcement

**MANDATORY**: Check all created/modified files for errors before proceeding.

1. Run linters and tests on changed files
2. Fix all errors and high/medium warnings
3. Record results in MCP memory
4. Only proceed when all checks pass

### Security & Compliance

**Data Handling**:

- Follow PDPA 2010 (Malaysian privacy law)
- Sanitize fixtures and logs
- Use factories/seeders for test data
- Log sensitive operations per D09

**Code Security**:

- Never commit secrets or credentials
- Request security review for auth changes
- Maintain WCAG 2.2 AA compliance
- Coordinate with D12–D15 UI/UX guides and D18 AI integration

## MCP Server Integration

### Active Servers

**Core Development**:

- **memory** - Knowledge graph, cross-session persistence (CRITICAL)
- **sequentialthinking** - Complex problem decomposition
- **fetch** - HTTP/API requests, documentation retrieval
- **laravel-boost** - Artisan commands, tinker, database operations

**Testing & Browser**:

- **chrome-devtools** - Browser inspection, debugging
- **playwright** - E2E testing, automation

**Data & Translation**:

- **deepl** - Bahasa Melayu ↔ English translation (ICTServe requirement)
- **context7** - Library documentation enhancement

### ICTServe Knowledge Graph

**Key Entities**:

- `ictserve_system_spec` - Hybrid architecture (guest + authenticated + admin)
- `helpdesk_module_spec` - Ticketing system with email workflows  
- `ict_asset_loan_spec` - Asset management with email approvals
- `ai_chatbot_spec` - Ollama-Bedrock hybrid AI integration (D18)
- `ictserve_implementation_status` - Current progress tracking
- `ictserve_compliance_standards` - PDPA, WCAG, PSR-12 requirements

### Memory Usage Patterns

**Feature Development**:

1. Query existing patterns: `search_nodes` "guest forms", "Filament resources"
2. Check compliance: `open_nodes` "ictserve_compliance_standards"
3. Verify architecture: `open_nodes` "ictserve_system_spec"
4. Document new patterns for reuse

**Bug Investigation**:

1. Search similar issues: `search_nodes` with error keywords
2. Check integration points: `open_nodes` for module specs
3. Document solution patterns
4. Update implementation status

### Entity Naming Conventions

- User requests: `user_request_YYYY_MM_DD_TASK_NAME`
- System specs: `[module]_spec`
- Implementation tracking: `[module]_implementation_status`
- Patterns: `[domain]_pattern`

## Planning & Complex Tasks

### Sequential Thinking Usage

Use `sequentialthinking` for:

- Feature implementation (Analysis → Design → Build → Test → Validate)
- Bug investigation (Reproduce → Diagnose → Fix → Verify)
- Refactoring (Impact analysis → Plan → Execute → Test)

### Planning Template

```text
Phase 1: Analysis
- Examine D03/D04 requirements
- Check D18 for AI integration patterns
- Identify affected components
- Check existing patterns
- List dependencies/risks

Phase 2: Design  
- Sketch architecture changes
- Define schema/migrations
- Outline test strategy

Phase 3: Implementation
- Create migrations with rollback
- Update models/controllers
- Implement business logic
- Add authorization

Phase 4: Testing
- Unit/feature tests
- Static analysis (phpstan/pint)
- Frontend build
- Manual verification
```

## Architecture Patterns

### ICTServe System Architecture

**Hybrid Architecture**: Guest forms + Authenticated portal + Admin panel (Filament)

- **Guest Layer**: Quick access forms without login
- **Authenticated Layer**: Staff dashboard with full history
- **Admin Layer**: Filament-based management interface

**Core Modules**:

- **Helpdesk**: Ticket submission with email workflows
- **Asset Loan**: Equipment borrowing with approval workflows
- **AI Chatbot**: Ollama-Bedrock hybrid AI integration (D18)
- **Cross-Module Integration**: Shared notifications, audit logging

### Laravel 12 Conventions

**File Structure**:

- No `app/Http/Kernel.php` - use `bootstrap/app.php`
- Commands auto-register from `app/Console/Commands/`
- Service providers in `bootstrap/providers.php`

**Code Standards**:

- Strict typing: `declare(strict_types=1);`
- PHP 8 constructor promotion
- Explicit return type declarations
- PSR-12 compliance via Pint

### Filament v4 Patterns

**Resource Structure**:

- Static `make()` methods for components
- Use `relationship()` for form selects
- Actions extend `Filament\Actions\Action`
- Schema components in `Filament\Schemas\Components`

### Livewire v3 Patterns

**Component Guidelines**:

- Single root element required
- Use `wire:model.live` for real-time updates
- Add `wire:key` in loops
- Lifecycle hooks: `mount()`, `updatedFoo()`

## References

**Core Documentation**:

- `docs/D00_SYSTEM_OVERVIEW.md` - System context
- `docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md` - Requirements
- `docs/D04_SOFTWARE_DESIGN_DOCUMENT.md` - Architecture
- `docs/D09_DATABASE_DOCUMENTATION.md` - Audit patterns
- `docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md` - Infrastructure
- `docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md` - AI chatbot integration

**Steering Files**:

- `AGENTS.md` - Global agent conventions
- `.kiro/steering/mcp.md` - MCP server details
- `.kiro/steering/tech.md` - Technology stack
- `.kiro/steering/structure.md` - Project structure
