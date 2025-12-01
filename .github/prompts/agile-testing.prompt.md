agent: test_agent

# Agile Test Execution

**Run minimal tests during development, full suite before commit.**

## Core Principles


## PHPUnit (Laravel)

### Development (Fast)
```bash
# Single file (RECOMMENDED)
php artisan test tests/Feature/AssetLoanTest.php

# Single method
php artisan test --filter=test_user_can_submit_loan

# Specific suite
php artisan test --testsuite=Unit
```

### Pre-Commit (Full)
```bash
php artisan test                    # All tests
php artisan test --coverage --min=80  # With coverage
```

## Playwright (E2E)

### Development (Fast)
```bash
# Single spec
npx playwright test tests/e2e/loan.spec.ts

# Single test
npx playwright test tests/e2e/loan.spec.ts -g "submit loan"

# Headed mode (debug)
npm run test:e2e:headed
```

### Pre-Commit (Full)
```bash
npm run test:e2e                    # All E2E tests
npm run test:accessibility          # WCAG 2.2 AA compliance
```

## TDD Workflow

### Bug Fix
```bash
# 1. Write failing test
php artisan test tests/Unit/Services/AssetServiceTest.php

# 2. Fix code, iterate until green
php artisan test tests/Unit/Services/AssetServiceTest.php

# 3. Validate feature flow
php artisan test tests/Feature/AssetManagementTest.php

# 4. Full suite before commit
php artisan test
```

### New Feature
```bash
# 1. Unit tests (service logic)
php artisan test tests/Unit/Services/NewFeatureServiceTest.php

# 2. Feature tests (integration)
php artisan test tests/Feature/NewFeatureTest.php

# 3. E2E tests (user flow)
npx playwright test tests/e2e/new-feature.spec.ts

# 4. Full validation
composer run test && npm run test:e2e
```

## Troubleshooting

**Database issues**:
```bash
php artisan migrate:fresh --seed
php artisan test tests/Feature/YourTest.php
```

**Flaky E2E tests**:
```bash
npm run test:e2e:headed  # Visual debugging
npx playwright test --debug tests/e2e/flaky.spec.ts
```

**Slow tests**:
```bash
php artisan test --filter=specific_test  # Isolate
php artisan test --parallel              # Parallelize
```

## Best Practices

✅ **DO**:

❌ **DON'T**:

## Quick Reference

| Task | Command |
|------|----------|
| Single test file | `php artisan test tests/Feature/File.php` |
| Single test method | `php artisan test --filter=method_name` |
| Unit tests only | `php artisan test --testsuite=Unit` |
| Feature tests only | `php artisan test --testsuite=Feature` |
| All PHP tests | `php artisan test` |
| Single E2E spec | `npx playwright test tests/e2e/spec.ts` |
| All E2E tests | `npm run test:e2e` |
| Accessibility | `npm run test:accessibility` |
| Full validation | `composer run test && npm run test:e2e` |

---

## Agent workflow — File-by-file (agile)

1) Confirm environment (PowerShell):
```powershell
php -v
composer --version
php artisan --version
npm -v
npx playwright --version
```

2) Pick a single test file to run.
3) Run the file only (example PowerShell commands):
```powershell
php artisan test tests/Feature/Portal/SubmissionHistoryTest.php
php artisan test tests/Feature/Portal/SubmissionHistoryTest.php --filter=test_recent_history_returns_expected_json
npx playwright test tests/e2e/loan.module.spec.ts
```
4) If the file fails — isolate, fix, re-run the failing method only; repeat until green. If blocked by infra, create an issue with logs and steps to reproduce.
5) When done with a batch, run the pre-commit full validation and push.
