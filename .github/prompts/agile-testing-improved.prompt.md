---
agent: test_agent
---

# Agile Test Execution

**Run minimal tests during development, full suite before commit.**

## Decision Tree

| Change Type | Command |
|-------------|---------|
| Service/Model | `php artisan test tests/Unit/Services/File.php` |
| Controller/Route | `php artisan test tests/Feature/File.php` |
| UI/Flow | `npx playwright test tests/e2e/spec.ts` |
| Accessibility | `npm run test:accessibility` |
| Database | `php artisan migrate:fresh --seed && php artisan test` |

## PHPUnit (Laravel 12)

### Development
```bash
# Single file
php artisan test tests/Feature/AssetLoanTest.php

# Single method
php artisan test tests/Feature/AssetLoanTest.php --filter=test_user_can_submit_loan

# Suite
php artisan test --testsuite=Unit

# Parallel
php artisan test --parallel
```

### Pre-Commit
```bash
composer run test                    # All tests
php artisan test --coverage --min=80 # Coverage gate
```

## Playwright (E2E)

### Development
```bash
# Single spec
npx playwright test tests/e2e/loan.spec.ts

# Single test
npx playwright test tests/e2e/loan.spec.ts -g "submit loan"

# Debug
npm run test:e2e:headed
npm run test:e2e:ui
```

### Pre-Commit
```bash
npm run test:e2e                     # All E2E
npm run test:accessibility           # WCAG 2.2 AA
```

## Agent Workflow

### 1. Environment Check
```bash
php -v && composer --version && npm -v && npx playwright --version
```

### 2. Run Single File
```bash
# Feature test
php artisan test tests/Feature/Portal/SubmissionHistoryTest.php

# Failing method only
php artisan test tests/Feature/Portal/SubmissionHistoryTest.php --filter=test_method_name

# E2E spec
npx playwright test tests/e2e/loan.module.spec.ts
```

### 3. Fix & Iterate
- **Pass**: Move to next file
- **Fail**: Isolate with `--filter`, fix, re-run
- **Blocked**: Create issue with logs

### 4. Log Result
```json
{
  "file": "tests/Feature/Portal/SubmissionHistoryTest.php",
  "result": "passed|failed|blocked",
  "failingTests": ["test_method_name"],
  "actionTaken": "fixed: add null check for submission query",
  "commit": "abc1234",
  "timeSeconds": 18
}
```

## TDD Workflows

### Bug Fix
```bash
# 1. Reproduce
php artisan test tests/Feature/AssetLoanTest.php --filter=test_fails

# 2. Fix + iterate
php artisan test tests/Feature/AssetLoanTest.php --filter=test_fails

# 3. Validate flow
php artisan test tests/Feature/AssetManagementTest.php

# 4. Full suite
composer run test
```

### New Feature
```bash
# 1. Unit tests
php artisan test tests/Unit/Services/NewFeatureServiceTest.php

# 2. Feature tests
php artisan test tests/Feature/NewFeatureTest.php

# 3. E2E tests
npx playwright test tests/e2e/new-feature.spec.ts

# 4. Full validation
composer run test && npm run test:e2e
```

## Troubleshooting

**Database issues**:
```bash
php artisan migrate:fresh --seed --env=testing
php artisan test tests/Feature/YourTest.php
```

**Flaky E2E**:
```bash
npm run test:e2e:headed
npx playwright test --debug tests/e2e/flaky.spec.ts
```

**Slow tests**:
```bash
php artisan test --parallel
php artisan test --filter=specific_test
```

## Best Practices

✅ **DO**:
- Run single file during development
- Use `--filter` for rapid iteration
- Run full suite before commit
- Log test results in `test-results/`

❌ **DON'T**:
- Run full suite during active development
- Increase timeouts to patch flaky tests
- Change production env to make tests pass
- Skip tests before committing

## Quick Reference

| Task | Command |
|------|---------|
| Single file | `php artisan test tests/Feature/File.php` |
| Single method | `php artisan test tests/Feature/File.php --filter=method` |
| Unit suite | `php artisan test --testsuite=Unit` |
| Feature suite | `php artisan test --testsuite=Feature` |
| All PHP tests | `composer run test` |
| Single E2E spec | `npx playwright test tests/e2e/spec.ts` |
| All E2E tests | `npm run test:e2e` |
| Accessibility | `npm run test:accessibility` |
| E2E report | `npm run test:e2e:report` |
| Full validation | `composer run test && npm run test:e2e` |
