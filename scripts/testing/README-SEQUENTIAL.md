# Sequential Test Runner

This directory contains scripts for running PHPUnit tests sequentially with fail-fast behavior.

## Overview

The sequential test runner executes test files one at a time and stops immediately when a test file fails. This is useful for:
- Debugging test failures
- CI/CD pipelines that need to fail fast
- Local development when you want to fix issues incrementally

## Scripts

### Bash Script (Linux/Mac/WSL)
```bash
./scripts/testing/run-tests-sequential.sh [test-directory]
```

**Examples:**
```bash
# Run all tests
./scripts/testing/run-tests-sequential.sh

# Run only Feature tests
./scripts/testing/run-tests-sequential.sh tests/Feature

# Run only Unit tests
./scripts/testing/run-tests-sequential.sh tests/Unit
```

### PowerShell Script (Windows/Cross-platform)
```powershell
.\scripts\testing\run-tests-sequential.ps1 [-TestDir <directory>]
```

**Examples:**
```powershell
# Run all tests
.\scripts\testing\run-tests-sequential.ps1

# Run only Feature tests
.\scripts\testing\run-tests-sequential.ps1 -TestDir tests/Feature

# Run only Unit tests
.\scripts\testing\run-tests-sequential.ps1 -TestDir tests/Unit
```

### Docker (via Makefile)
```bash
# Run tests sequentially inside Docker container
make test-sequential
```

## Features

- ✅ **Sequential Execution**: Tests run one file at a time
- ✅ **Fail-Fast**: Stops immediately on first failure
- ✅ **Clear Progress**: Shows which test is running and progress counter
- ✅ **Colored Output**: Uses color coding for better readability
- ✅ **Summary Report**: Shows passed/failed files at the end
- ✅ **Exit Codes**: Returns proper exit codes for CI/CD integration

## Output Format

```
========================================
Sequential Test Runner (Fail-Fast)
========================================
Test Directory: tests
Found 244 test files

----------------------------------------
[1/244] Running: tests/Unit/ExampleTest.php
----------------------------------------
✓ PASSED

----------------------------------------
[2/244] Running: tests/Feature/ExampleTest.php
----------------------------------------
✗ FAILED

========================================
Test execution stopped due to failure
========================================

TEST SUMMARY
========================================
Total Files:   244
Tests Run:     2
Passed:        1
Failed:        1

Failed Files:
  ✗ tests/Feature/ExampleTest.php
```

## Comparison with Standard Test Runner

| Feature | Standard (`php artisan test`) | Sequential Runner |
|---------|------------------------------|-------------------|
| Execution | Parallel (default) | Sequential |
| Stops on failure | No (runs all) | Yes (fail-fast) |
| Progress per file | No | Yes |
| File-level summary | Limited | Detailed |
| Best for | Full test suite | Debugging |

## Integration with CI/CD

You can use this in GitHub Actions or other CI systems:

```yaml
- name: Run Tests Sequentially
  run: ./scripts/testing/run-tests-sequential.sh
```

The script will:
1. Exit with code 0 if all tests pass
2. Exit with code 1 if any test fails
3. Output can be captured for CI logs

## Troubleshooting

### Script not executable (Linux/Mac)
```bash
chmod +x scripts/testing/run-tests-sequential.sh
```

### Cannot find PHP/Artisan
Make sure you're running from the project root directory.

### Tests run but fail immediately
This is the expected behavior! The runner stops on the first failing test file.
