# Agent Assignments for ICTServe Prompts

This document maps each prompt to its most suitable specialized agent based on the task requirements.

## Current Agent Assignments

| Prompt | Agent | Rationale |
|--------|-------|-----------|
| `agile-testing.prompt.md` | `test-agent` | Specialized QA engineer for PHPUnit and Playwright testing |
| `agile-testing-improved.prompt.md` | `test-agent` | Enhanced testing workflows require test expertise |
| `code-review.prompt.md` | `lint-agent` | Code style and quality checks are core lint-agent functions |
| `documentation-generator.prompt.md` | `docs-agent` | Expert technical writer for PHPDoc and README generation |
| `laravel-feature-implementation.prompt.md` | `dev-deploy-agent` | Build and deployment specialist for Laravel features |
| `accessibility-audit.prompt.md` | `lint-agent` | Code quality checks include accessibility compliance |
| `security-review.prompt.md` | `lint-agent` | Security analysis is part of code quality review |
| `test-generation.prompt.md` | `test-agent` | Comprehensive test generation requires testing expertise |
| `commit-git.prompt.md` | `dev-deploy-agent` | Git operations and deployment workflows |

## Available Specialized Agents

### Core Agents
- **`api-agent`** - REST API specialist for Laravel endpoints
- **`test-agent`** - QA engineer for comprehensive testing
- **`lint-agent`** - Code style and quality specialist
- **`docs-agent`** - Technical documentation expert
- **`dev-deploy-agent`** - Build and deployment specialist

### Claudette Agents (General Purpose)
- **`Claudette-Coder`** - General coding tasks with autonomous execution
- **`Claudette-Debug`** - Root cause analysis and debugging specialist
- **`Claudette-Compact`** - Concise coding solutions
- **`Claudette-Condensed`** - Minimal response coding
- **`Claudette-Ecko`** - Echo-based development
- **`Claudette-Mimir`** - Memory-enhanced coding
- **`Claudette-Mini-NT`** - Minimal non-terminal coding
- **`Claudette-Mini-T`** - Minimal terminal coding
- **`Claudette-Researcher`** - Research-focused development

## Agent Selection Criteria

1. **Task Specialization** - Match agent expertise to prompt requirements
2. **Tool Access** - Ensure agent has necessary tools for the task
3. **Context Awareness** - Agent understands ICTServe project specifics
4. **Workflow Alignment** - Agent methodology matches prompt workflow

## Usage Guidelines

- Use specialized agents for their domain expertise
- Fall back to Claudette agents for complex multi-domain tasks
- Consider `Claudette-Debug` for troubleshooting prompts
- Use `Claudette-Coder` for general Laravel development tasks

## Future Considerations

- Monitor agent performance and adjust assignments as needed
- Create new specialized agents for emerging workflow patterns
- Consider hybrid approaches for complex multi-step prompts