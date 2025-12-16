# Sequential Test Runner - Usage Examples

## Quick Start

### Basic Usage
```bash
# Run all tests sequentially
./scripts/testing/run-tests-sequential.sh

# Run specific directory
./scripts/testing/run-tests-sequential.sh tests/Feature

# Using PowerShell (Windows)
.\scripts\testing\run-tests-sequential.ps1 -TestDir tests/Unit

# Using Docker/Make
make test-sequential
```

## Output Examples

### Example 1: All Tests Pass

```
========================================
Sequential Test Runner (Fail-Fast)
========================================
Test Directory: tests/Unit
Found 50 test files

----------------------------------------
[1/50] Running: tests/Unit/ExampleTest.php
----------------------------------------

   PASS  Tests\Unit\ExampleTest
  ✓ it can do basic math                                    0.01s

Tests:    1 passed (1 tests, 1 assertions)
Duration: 0.02s

✓ PASSED

----------------------------------------
[2/50] Running: tests/Unit/Services/BedrockServiceTest.php
----------------------------------------

   PASS  Tests\Unit\Services\BedrockServiceTest
  ✓ it can initialize service                              0.02s
  ✓ it validates configuration                             0.01s

Tests:    2 passed (2 tests, 5 assertions)
Duration: 0.04s

✓ PASSED

... (continues for all files) ...

========================================
TEST SUMMARY
========================================
Total Files:   50
Tests Run:     50
Passed:        50
Failed:        0

Passed Files:
  ✓ tests/Unit/ExampleTest.php
  ✓ tests/Unit/Services/BedrockServiceTest.php
  ✓ tests/Unit/Services/AutoReplyServiceTest.php
  ... (all 50 files listed)

All tests passed!
```

### Example 2: Test Failure (Fail-Fast Behavior)

```
========================================
Sequential Test Runner (Fail-Fast)
========================================
Test Directory: tests/Feature
Found 100 test files

----------------------------------------
[1/100] Running: tests/Feature/Auth/LoginTest.php
----------------------------------------

   PASS  Tests\Feature\Auth\LoginTest
  ✓ user can login with valid credentials                  0.15s
  ✓ user cannot login with invalid credentials             0.12s

Tests:    2 passed (2 tests, 8 assertions)
Duration: 0.28s

✓ PASSED

----------------------------------------
[2/100] Running: tests/Feature/Auth/RegisterTest.php
----------------------------------------

   PASS  Tests\Feature\Auth\RegisterTest
  ✓ user can register with valid data                      0.20s

Tests:    1 passed (1 tests, 4 assertions)
Duration: 0.21s

✓ PASSED

----------------------------------------
[3/100] Running: tests/Feature/Helpdesk/TicketTest.php
----------------------------------------

   FAIL  Tests\Feature\Helpdesk\TicketTest
  ⨯ user can create ticket                                 0.10s
  ✓ user can view their tickets                            0.08s

  ───────────────────────────────────────────────────────────
   FAILED  Tests\Feature\Helpdesk\TicketTest > user can create ticket

  Failed asserting that false is true.

  at tests/Feature/Helpdesk/TicketTest.php:25
     21│     {
     22│         $user = User::factory()->create();
     23│         $response = $this->actingAs($user)->post('/tickets');
     24│
  ❯  25│         $response->assertOk();
     26│
     27│         $this->assertDatabaseHas('tickets', [
     28│             'user_id' => $user->id,
  ───────────────────────────────────────────────────────────

Tests:    1 failed, 1 passed (2 tests, 5 assertions)
Duration: 0.19s

✗ FAILED

========================================
Test execution stopped due to failure
========================================

TEST SUMMARY
========================================
Total Files:   100
Tests Run:     3
Passed:        2
Failed:        1

Passed Files:
  ✓ tests/Feature/Auth/LoginTest.php
  ✓ tests/Feature/Auth/RegisterTest.php

Failed Files:
  ✗ tests/Feature/Helpdesk/TicketTest.php
```

**Note**: The runner stopped at file 3 out of 100 because of the failure. Files 4-100 were not executed.

## Comparison with Standard Test Runner

### Standard Parallel Runner (`php artisan test --parallel`)

```bash
$ php artisan test --parallel

   PASS  Tests\Unit\ExampleTest
   PASS  Tests\Feature\Auth\LoginTest
   FAIL  Tests\Feature\Helpdesk\TicketTest
   PASS  Tests\Feature\Dashboard\IndexTest
   ... (continues running all tests) ...

Tests:    95 passed, 1 failed (100 tests, 500 assertions)
Duration: 5.2s
```

**Issues:**
- ❌ All tests run even after first failure
- ❌ Hard to identify which file failed first
- ❌ Output can be overwhelming with multiple failures

### Sequential Fail-Fast Runner

```bash
$ ./scripts/testing/run-tests-sequential.sh

[1/100] Running: tests/Unit/ExampleTest.php
✓ PASSED

[2/100] Running: tests/Feature/Auth/LoginTest.php
✓ PASSED

[3/100] Running: tests/Feature/Helpdesk/TicketTest.php
✗ FAILED

========================================
Test execution stopped due to failure
========================================
```

**Benefits:**
- ✅ Stops immediately on first failure
- ✅ Clear indication of which file failed
- ✅ Saves time during debugging
- ✅ Focused output for fixing issues

## Real-World Scenarios

### Scenario 1: Pre-Commit Hook

You want to ensure tests pass before committing:

```bash
#!/bin/bash
# .git/hooks/pre-commit

echo "Running unit tests before commit..."
./scripts/testing/run-tests-sequential.sh tests/Unit

if [ $? -ne 0 ]; then
    echo ""
    echo "❌ Unit tests failed! Please fix before committing."
    echo "Commit aborted."
    exit 1
fi

echo "✅ All unit tests passed!"
exit 0
```

### Scenario 2: Debugging Test Suite

You have 200 test files and one is failing intermittently:

```bash
# Run sequentially to identify the problematic file
./scripts/testing/run-tests-sequential.sh > test-log.txt 2>&1

# Check the log to see exactly where it failed
tail -50 test-log.txt
```

### Scenario 3: CI/CD Pipeline

You want fast feedback in your CI pipeline:

```yaml
# .github/workflows/pr-checks.yml
name: PR Checks

on: pull_request

jobs:
  quick-test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      
      # Run unit tests sequentially for quick failure detection
      - name: Unit Tests (Fail-Fast)
        run: ./scripts/testing/run-tests-sequential.sh tests/Unit
      
      # Only run feature tests if unit tests pass
      - name: Feature Tests (Fail-Fast)
        run: ./scripts/testing/run-tests-sequential.sh tests/Feature
```

### Scenario 4: Local Development

You're working on a feature and want to test as you go:

```bash
# Terminal 1: Watch for file changes and run tests
watch -n 5 ./scripts/testing/run-tests-sequential.sh tests/Feature/YourFeature

# Terminal 2: Edit your code

# Each time you save, tests run automatically
```

## Tips and Best Practices

### 1. Test Organization
Organize tests by feature/module for better sequential execution:

```
tests/
├── Unit/
│   ├── Services/
│   ├── Models/
│   └── Helpers/
├── Feature/
│   ├── Auth/
│   ├── Helpdesk/
│   └── Dashboard/
```

Then run specific areas:
```bash
./scripts/testing/run-tests-sequential.sh tests/Feature/Helpdesk
```

### 2. Fast Tests First
Organize tests so faster tests run first:
- Unit tests (fast) before Feature tests (slower)
- Simple tests before complex integration tests

### 3. Naming Convention
Use clear test file names for better output:
```
✅ LoginTest.php
✅ UserRegistrationTest.php
❌ Test1.php
❌ TestStuff.php
```

### 4. Exit Codes
The script returns proper exit codes for automation:
- `0` = All tests passed
- `1` = Tests failed or error occurred

```bash
./scripts/testing/run-tests-sequential.sh
EXIT_CODE=$?

if [ $EXIT_CODE -eq 0 ]; then
    echo "Success! All tests passed."
else
    echo "Failure! Check test output above."
    exit 1
fi
```

## Performance Data

Based on a test suite with 244 test files:

| Method | Time to First Failure | Total Time (all pass) | Memory Usage |
|--------|----------------------|----------------------|--------------|
| Sequential | 0.5s | 120s | 256MB |
| Parallel (8 processes) | 5s | 25s | 2GB |

**Conclusion**: Sequential is better for:
- Finding failures quickly
- Debugging
- Resource-constrained environments

Parallel is better for:
- Fast full suite runs
- CI main branch
- When all tests are likely to pass
