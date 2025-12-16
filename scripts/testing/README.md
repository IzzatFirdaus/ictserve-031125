# Testing Scripts

This directory contains scripts and utilities for running tests in the ICTServe project.

## Quick Reference

| Script | Purpose | Platform |
|--------|---------|----------|
| `run-tests-sequential.sh` | Run tests one by one with fail-fast | Linux/Mac/WSL |
| `run-tests-sequential.ps1` | Run tests one by one with fail-fast | Windows/PowerShell |
| `run-individual-tests.js` | Run Playwright E2E tests individually | Node.js |
| `run-test.ps1` | Helper wrapper for running specific tests | PowerShell |
| `test-changed.ps1` | Run only tests for changed files | PowerShell |

## Sequential Test Runner (NEW)

**🚀 Quick Start:**
```bash
# Run all tests sequentially (stops on first failure)
./scripts/testing/run-tests-sequential.sh

# Run specific directory
./scripts/testing/run-tests-sequential.sh tests/Feature

# Using Make
make test-sequential
```

**📚 Documentation:**
- [README-SEQUENTIAL.md](./README-SEQUENTIAL.md) - Overview and features
- [USAGE-EXAMPLES.md](./USAGE-EXAMPLES.md) - Real-world examples and output
- [CI-CD-INTEGRATION.md](./CI-CD-INTEGRATION.md) - Integration with CI/CD pipelines

**✨ Key Features:**
- ✅ Runs tests one file at a time
- ✅ Stops immediately on first failure (fail-fast)
- ✅ Clear progress indication with colors
- ✅ Detailed summary report
- ✅ Cross-platform support (Bash & PowerShell)

**When to Use:**
- 🐛 Debugging test failures
- 🔍 Finding which test file fails first
- 💻 Local development workflow
- 🚦 CI/CD fail-fast pipelines
- 📊 Detailed per-file reporting

## Standard Test Commands

### PHPUnit Tests (Parallel)
```bash
# Run all tests in parallel (default Laravel behavior)
php artisan test

# Run specific directory
php artisan test tests/Unit

# Run specific file
php artisan test tests/Feature/ExampleTest.php

# Run with filter
php artisan test --filter=test_example
```

### Playwright E2E Tests
```bash
# Run all E2E tests
npm run test:e2e

# Run specific test file
npx playwright test tests/e2e/login.spec.ts

# Run with UI mode
npx playwright test --ui
```

## Comparison: Sequential vs Parallel

| Feature | Sequential Runner | Parallel Runner (`php artisan test`) |
|---------|------------------|-------------------------------------|
| **Execution** | One file at a time | Multiple files simultaneously |
| **Speed** | Slower (100-150s) | Faster (20-30s) |
| **Stops on Failure** | ✅ Yes (immediately) | ❌ No (runs all) |
| **Output Clarity** | ✅ High (per-file) | ⚠️ Mixed output |
| **Best For** | Debugging, fail-fast | Full suite runs |
| **Memory Usage** | Lower (256MB) | Higher (2GB+) |
| **CI Integration** | Great for PR checks | Great for main branch |

## Workflow Recommendations

### Local Development
```bash
# Option 1: Sequential (find issues fast)
./scripts/testing/run-tests-sequential.sh tests/Unit

# Option 2: Parallel (quick feedback)
php artisan test --parallel
```

### Pre-Commit Hook
```bash
# Run only changed tests
./scripts/testing/test-changed.ps1

# Or run unit tests sequentially
./scripts/testing/run-tests-sequential.sh tests/Unit
```

### CI/CD Pipeline
```yaml
# Fast feedback for PRs
- run: ./scripts/testing/run-tests-sequential.sh tests/Unit
- run: ./scripts/testing/run-tests-sequential.sh tests/Feature

# Comprehensive testing for main branch
- run: php artisan test --parallel
```

## Additional Tools

### Larastan (Static Analysis)
```bash
# Check if Larastan is ready
./scripts/testing/check-larastan-ready.sh

# Save Larastan output
./scripts/testing/save-larastan-outputs.sh
```

### Test Attribute Updaters
```bash
# Update test attributes to PHPUnit 11 format
php scripts/testing/update-test-attributes.php
php scripts/testing/update-test-attributes-v2.php
```

## Environment Setup

### Prerequisites
- PHP 8.2+
- Composer dependencies installed
- Database configured (for feature tests)
- Node.js (for E2E tests)

### Configuration
Tests use settings from `phpunit.xml` and `.env.testing`:

```ini
# .env.testing
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_STORE=array
QUEUE_CONNECTION=sync
```

## Troubleshooting

### Tests Not Running
1. Check dependencies are installed: `composer install`
2. Check database is configured: `php artisan migrate --env=testing`
3. Check script permissions: `chmod +x scripts/testing/*.sh`

### Script Execution Issues
```bash
# Linux/Mac: Permission denied
chmod +x scripts/testing/run-tests-sequential.sh

# Windows: PowerShell execution policy
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Tests Pass Individually But Fail in Suite
- Check for shared state between tests
- Ensure proper use of `RefreshDatabase` trait
- Check for database seeds or factories affecting state

## Contributing

When adding new test scripts:
1. Follow existing naming conventions
2. Add documentation to this README
3. Ensure cross-platform compatibility
4. Add usage examples
5. Test thoroughly before committing

## Related Documentation

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Laravel Testing Docs](https://laravel.com/docs/testing)
- [Playwright Documentation](https://playwright.dev/)
- [GitHub Actions with Laravel](https://docs.github.com/en/actions)

---

**Last Updated**: December 2024  
**Maintainer**: ICTServe Development Team
