---
applyTo: "**"
description: "Contribution workflow, PR standards, branching strategy, code review requirements, and compliance checklist for ICTServe development"
---

# Contributing Instructions

**Purpose**
Defines mandatory contribution standards, developer workflow, and compliance requirements for ICTServe. This document ensures traceability to requirements (D03), design (D04/D11), testing (D08/D10), and UI/UX (D12–D14). It is normative for all contributors.

**Scope**
Applies to all contributions affecting code, scripts, workflows, documentation, and configuration.

## 1. Mandatory Rules (Summary)

- **Branching**:
  - Source: `develop`
  - Format: `<type>/<short-description>` (e.g., `feature/ticket-triage`, `fix/login-csrf`).
- **Commits**:
  - Style: Conventional Commits (`feat: add login`, `fix: csrf token`).
  - Context: Provide meaningful bodies for non-trivial commits.
- **Traceability**:
  - Every PR MUST map to a Requirement ID (D03) or Design Reference (D04).
  - Add metadata headers to new major files.
- **Quality**:
  - **PHP**: Strict typing (`declare(strict_types=1)`), PHP 8.4 features allowed.
  - **Tests**: CI must pass (`php artisan test`).
  - **Static Analysis**: Zero errors on Level 9 (`phpstan`).

## 2. Step-by-Step Workflow

1.  **Sync & Branch**:
    ```bash
    git checkout develop
    git pull origin develop
    git checkout -b feature/my-feature-name
    ```

2.  **Implement**:
    - Write code following **PSR-12** and project **Architecture** (Services, Volt, Policies).
    - Add/Update tests in `tests/Feature` or `tests/Unit`.

3.  **Local Validation** (Must pass before push):
    ```bash
    # 1. Format Code
    vendor/bin/pint

    # 2. Static Analysis
    vendor/bin/phpstan analyse

    # 3. Run Tests
    php artisan test

    # 4. Build Frontend (if UI changed)
    npm run build
    ```

4.  **Commit & Push**:
    ```bash
    git commit -m "feat(tickets): implement priority sorting (Ref: SRS-012)"
    git push origin feature/my-feature-name
    ```

5.  **Open Pull Request**:
    - Fill out the PR Template (see below).
    - Assign reviewers (Dev + Domain Owner).

## 3. Pull Request Template

**Title**: `feat/fix/chore: Short Description`

**Description**:
- **Summary**: What does this change do?
- **Traceability**:
  - Requirement: `[SRS-FR-XXX]` (D03)
  - Design: `[D04-SEC-XX]` (D04)
- **Type**: `Feature` / `Bugfix` / `Refactor` / `Docs`

**Checklist**:
- [ ] Tests added/updated (`php artisan test` passed)
- [ ] Static Analysis passed (`phpstan` Level 9)
- [ ] Accessibility verified (if UI change) - WCAG 2.2 AA
- [ ] Documentation updated (if applicable)
- [ ] Migrations tested (up & down)

## 4. Coding Standards

### PHP / Laravel
- **Strict Types**: All PHP files must start with `declare(strict_types=1);`.
- **Logic Location**: Business logic goes in **Services**, not Controllers.
- **UI Components**: Use **Livewire Volt** for new UI components.
- **Authorization**: Use `$this->authorize()` or Policies in all controller actions.

### Database
- **Migrations**: Always include a destructive `down()` method.
- **Zero-Downtime**: Never rename columns directly in production-bound migrations.

### Security
- **Secrets**: NEVER commit API keys or credentials. Use `.env`.
- **Validation**: Use Form Requests for all user input.
- **Output**: Escape all data in Blade (`{{ $var }}`).

## 5. Branching Strategy

| Branch | Purpose | Protection Rule |
| :--- | :--- | :--- |
| `main` | Production code. Releases are tagged here. | **Locked**. Require PR + CI + Approval. |
| `develop` | Integration branch. Next release. | **Protected**. Require PR + CI. |
| `feature/*` | New features. | Delete after merge. |
| `fix/*` | Bug fixes. | Delete after merge. |
| `hotfix/*` | Critical prod fixes (branch from main). | Merge to main & develop. |

## 6. Definition of Done

A PR is considered "Done" when:
1.  CI pipeline is green.
2.  Code review approved by at least 1 peer.
3.  No new PHPStan errors introduced.
4.  Test coverage is maintained or improved.
5.  Documentation (D10/D11/Docs folder) reflects the changes.
