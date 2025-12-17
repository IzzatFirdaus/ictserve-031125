# Sequential Test Runner - CI/CD Integration Examples

## GitHub Actions

### Example 1: Use sequential runner in CI
Add this to your `.github/workflows/ci.yml`:

```yaml
- name: Run Tests Sequentially (Fail-Fast)
  run: ./scripts/testing/run-tests-sequential.sh
  env:
    DB_CONNECTION: mysql
    DB_HOST: 127.0.0.1
    DB_PORT: 3306
    DB_DATABASE: testing
    DB_USERNAME: root
    DB_PASSWORD: root
```

### Example 2: Run specific test directory in CI
```yaml
- name: Run Feature Tests Sequentially
  run: ./scripts/testing/run-tests-sequential.sh tests/Feature
```

### Example 3: Conditional test execution
```yaml
jobs:
  test-parallel:
    name: Quick Test (Parallel)
    runs-on: ubuntu-latest
    steps:
      - name: Run Tests in Parallel
        run: php artisan test --parallel

  test-sequential:
    name: Detailed Test (Sequential)
    runs-on: ubuntu-latest
    if: github.event_name == 'pull_request'
    steps:
      - name: Run Tests Sequentially
        run: ./scripts/testing/run-tests-sequential.sh
```

## GitLab CI

```yaml
test:sequential:
  stage: test
  script:
    - ./scripts/testing/run-tests-sequential.sh
  only:
    - merge_requests
```

## Jenkins Pipeline

```groovy
stage('Test') {
    steps {
        sh './scripts/testing/run-tests-sequential.sh'
    }
}
```

## CircleCI

```yaml
jobs:
  test:
    steps:
      - run:
          name: Run Tests Sequentially
          command: ./scripts/testing/run-tests-sequential.sh
```

## Docker Compose

Add to your `docker-compose.yml`:

```yaml
services:
  app:
    # ... other config
    
test:
  extends: app
  command: ./scripts/testing/run-tests-sequential.sh
  profiles:
    - test
```

Run with:
```bash
docker-compose run --rm test
```

## Local Development Workflow

### Before Committing
```bash
# Run all tests sequentially to catch issues early
./scripts/testing/run-tests-sequential.sh

# Or just run tests for the area you changed
./scripts/testing/run-tests-sequential.sh tests/Feature/Auth
```

### Debugging Test Failures
```bash
# Find which test file is failing first
./scripts/testing/run-tests-sequential.sh

# Fix the failing test, then run again
./scripts/testing/run-tests-sequential.sh

# Continue until all tests pass
```

### Pre-push Hook
Create `.git/hooks/pre-push`:

```bash
#!/bin/bash

echo "Running tests before push..."
./scripts/testing/run-tests-sequential.sh tests/Unit

if [ $? -ne 0 ]; then
    echo "Tests failed! Push aborted."
    exit 1
fi

echo "All tests passed! Continuing with push..."
exit 0
```

Make it executable:
```bash
chmod +x .git/hooks/pre-push
```

## Performance Considerations

### When to Use Sequential Runner
- ✅ Debugging test failures
- ✅ CI/CD for small to medium projects
- ✅ Pre-commit/pre-push hooks
- ✅ When you want detailed per-file feedback
- ✅ When running specific test directories

### When to Use Parallel Runner
- ✅ Large test suites (100+ files)
- ✅ Fast feedback in development
- ✅ CI/CD main branch builds
- ✅ When test isolation is guaranteed

### Comparison

| Metric | Sequential | Parallel |
|--------|-----------|----------|
| Execution Time | Slower | Faster |
| Failure Feedback | Immediate (stops) | After all complete |
| Resource Usage | Lower | Higher |
| Output Clarity | High | Moderate |
| Debugging | Easier | Harder |

## Output Capturing

### Save output to file
```bash
./scripts/testing/run-tests-sequential.sh 2>&1 | tee test-results.log
```

### Save only summary
```bash
./scripts/testing/run-tests-sequential.sh | grep -A 20 "TEST SUMMARY"
```

### Parse for CI reporting
```bash
#!/bin/bash
./scripts/testing/run-tests-sequential.sh > test-output.txt 2>&1
EXIT_CODE=$?

# Extract summary
grep -A 10 "TEST SUMMARY" test-output.txt > test-summary.txt

exit $EXIT_CODE
```

## Advanced Usage

### Run with custom PHP binary
```bash
# Modify the script or use environment variable
PHP_BINARY=/usr/bin/php8.2 ./scripts/testing/run-tests-sequential.sh
```

### Run with coverage
You'll need to modify the script to add `--coverage` flags to the php artisan test command.

### Run with specific PHPUnit options
Modify the script to pass additional arguments:

```bash
# In the script, change:
php artisan test "$test_file" --colors=always

# To:
php artisan test "$test_file" --colors=always --stop-on-failure
```

## Troubleshooting

### Tests pass individually but fail in runner
- Check for shared state between tests
- Ensure proper database cleanup between tests
- Use `RefreshDatabase` trait in test classes

### Runner hangs or doesn't start
- Verify PHP is in PATH
- Check file permissions (chmod +x)
- Ensure you're in project root directory

### Colors not showing in CI
Some CI systems don't support ANSI colors. You can:
- Disable colors in the script
- Use CI-specific output format
- Check CI documentation for color support
