---
applyTo: "**"
description: "Contribution workflow, PR standards, branching strategy, code review requirements, and compliance checklist for ICTServe development"
---

# Contributing Instructions

**Purpose**
Defines mandatory contribution standards, developer workflow, and compliance requirements for ICTServe. This document ensures code quality, traceability to requirements (D03), design patterns (D04/D11), testing standards (D08/D10), and accessibility compliance (WCAG 2.2 AA). It is normative for all contributors.

**Scope**
Applies to all contributions affecting code, scripts, workflows, documentation, and configuration.

---

## 1. Mandatory Rules (Summary)

- **Branching**:
  - Source: `develop` branch
  - Format: `<type>/<short-description>` (e.g., `feature/ticket-triage`, `fix/login-csrf`)
  - Delete branch after merge
- **Commits**:
  - Style: Conventional Commits (`feat: add login`, `fix: csrf token`, `chore: update deps`)
  - Provide meaningful commit bodies for non-trivial changes
- **Traceability**:
  - All major changes should reference relevant D0X documentation
  - Include `// trace: D03-FR-001, D10-§4` comments for significant implementations
- **Quality**:
  - **PHP**: Strict typing (`declare(strict_types=1)`), PSR-12 compliance
  - **Tests**: All tests passing (`php artisan test`)
  - **Static Analysis**: Zero errors on PHPStan Level 9 (`vendor/bin/phpstan analyse`)
  - **Code Format**: Pass Laravel Pint (`vendor/bin/pint`)

---

## 2. Step-by-Step Workflow

1. **Sync & Branch**:
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/my-feature-name
   ```

2. **Implement**:
   - Follow project architecture standards (Services, Controllers, Livewire/Volt, Policies)
   - Add/Update tests in `tests/Feature/` or `tests/Unit/`
   - Follow PSR-12 coding standards (enforced by Laravel Pint)
   - Reference D10 for code structure guidelines

3. **Local Validation** (Must pass before push):
   ```bash
   # 1. Format code with Laravel Pint
   vendor/bin/pint --dirty

   # 2. Static Analysis (Level 9)
   vendor/bin/phpstan analyse

   # 3. Run tests
   php artisan test

   # 4. Build frontend (if UI changed)
   npm run build
   ```

4. **Commit & Push**:
   ```bash
   git commit -m "feat: implement ticket priority sorting"
   git push origin feature/my-feature-name
   ```

5. **Open Pull Request**:
   - Use PR template (see below)
   - Assign reviewers for code review
   - Ensure CI pipeline passes

---

## 3. Pull Request Template

**Title**: `feat/fix/chore: Short Description`

**Description**:
```markdown
## Summary
Brief description of changes

## Type
- [ ] Feature
- [ ] Bugfix
- [ ] Refactor
- [ ] Documentation

## Testing
- [ ] Tests added/updated
- [ ] All tests passing (`php artisan test`)
- [ ] Manual testing completed

## Code Quality
- [ ] Static Analysis passed (`phpstan` Level 9)
- [ ] Code formatted (`pint --dirty`)
- [ ] No warnings or errors

## Accessibility & Compliance
- [ ] WCAG 2.2 AA compliance verified (if UI change)
- [ ] Localization keys checked (if text changed)
- [ ] Documentation updated (if applicable)

## Database
- [ ] Migrations reversible (up & down methods)
- [ ] Zero-downtime approach used (if applicable)

## Traceability
- [ ] D-documentation references added where applicable
```

---

## 4. Coding Standards

### PHP / Laravel
- **Strict Types**: All PHP files start with `declare(strict_types=1);`
- **Type Hints**: Explicit return types on all functions/methods
- **Constructor Promotion**: Use PHP 8 constructor property promotion
- **Logic Location**: Business logic in Services (`app/Services/`), not Controllers
- **UI Components**: Use Livewire Volt for interactive components (see D10 §4.3)
- **Authorization**: Policies in `app/Policies/` for model actions; Gates in `bootstrap/app.php` for features
- **Eloquent**: Use `protected function casts(): array` method (not `$casts` property); prefer relationships over raw SQL; eager load to prevent N+1 queries

### Database
- **Migrations**: Always include destructive `down()` method for rollback
- **Column Changes**: Use `->change()` with full attribute specification (all previous attributes must be included)
- **Zero-Downtime**: Plan for backward compatibility during deploys
- **Indexes**: Add indexes on foreign keys and frequently queried columns

### Security
- **Secrets**: NEVER commit `.env` values, API keys, or credentials
- **Validation**: Use Form Request classes for all user input
- **Output Escaping**: Use Blade syntax (`{{ }}`) for automatic HTML escaping
- **Authorization**: Always authorize user actions before database changes

### Testing
- **Framework**: PHPUnit (use `php artisan make:test --phpunit {name}`)
- **Structure**: Feature tests in `tests/Feature/`, Unit tests in `tests/Unit/`
- **Factories**: Use model factories for test data
- **Coverage**: Maintain or improve test coverage with each PR

---

## 5. Branching Strategy

| Branch       | Purpose                          | Protection Rule                        |
| :----------- | :------------------------------- | :------------------------------------- |
| `main`       | Production release tag point     | Locked - PR required + CI pass         |
| `develop`    | Integration / next release       | Protected - PR required + CI pass      |
| `feature/*`  | New features                     | Delete after merge                     |
| `fix/*`      | Bug fixes                        | Delete after merge                     |
| `hotfix/*`   | Critical production fixes        | Merge to main & develop                |

---

## 6. Definition of Done

A PR is considered "Done" when:

1. CI pipeline is green (tests, static analysis, linting)
2. Code review approved by at least 1 maintainer
3. No new warnings or errors introduced
4. Test coverage maintained or improved
5. Documentation reflects the changes (code comments and `docs/` folder)
6. Traceability comments included for significant implementations

---

## 7. Documentation References

- **D03**: Software Requirements Specification — functional requirements (FR), non-functional requirements (NFR)
- **D04**: Architecture Design — system design, component interaction, API contracts
- **D10**: Source Code Documentation — code standards, patterns, directory structure
- **D11**: Technical Design Documentation — infrastructure, deployment, monitoring
- **D12**: UI/UX Design Guide — component specifications, localization, accessibility rules

---

## 8. CI/CD Integration

**GitHub Actions** (`.github/workflows/`):
- Runs `phpstan analyse` (Level 9 strictness)
- Runs `vendor/bin/pint --test` (PSR-12 format check)
- Runs `php artisan test` (all tests)
- Security scanning for secrets, dependencies

**Before Pushing**: Run all checks locally to avoid CI failures.

---

**Status**: ✅ Production-ready
