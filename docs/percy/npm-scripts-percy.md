# NPM Scripts for Percy Visual Testing Integration

This document describes all available npm scripts for Percy visual testing in the ICTServe v3.6.1 application.

## Core Percy Scripts

### Basic Percy Operations

- `npm run percy:exec` - Execute Percy CLI commands directly
- `npm run percy:build` - Create a new Percy build
- `npm run percy:finalize` - Finalize the current Percy build
- `npm run percy:upload` - Upload snapshots to Percy
- `npm run percy:status` - Check Percy build status
- `npm run percy:report` - Generate Percy build report

### Percy Management

- `npm run percy:wrapper` - Use Percy CLI wrapper with enhanced error handling
- `npm run percy:build-manager` - Advanced build management with retry logic
- `npm run percy:simple` - Simple build manager for basic operations
- `npm run percy:validate` - Validate Percy configuration
- `npm run percy:health-check` - Check Percy service health
- `npm run percy:config-validate` - Validate Percy configuration file
- `npm run percy:token-validate` - Validate Percy authentication token
- `npm run percy:project-info` - Display Percy project information
- `npm run percy:build-info` - Display current build information
- `npm run percy:cleanup` - Clean up Percy temporary files

### Percy Performance & Optimization

- `npm run percy:performance` - Run performance optimization
- `npm run percy:performance-report` - Generate performance metrics report
- `npm run percy:save-performance-report` - Save performance report to file
- `npm run percy:wait-uploads` - Wait for all uploads to complete
- `npm run percy:shutdown` - Gracefully shutdown Percy processes

### Percy Degradation Management

- `npm run percy:degradation` - Run with degradation handling
- `npm run percy:degradation-status` - Check degradation status
- `npm run percy:degradation-report` - Generate degradation report
- `npm run percy:enable-degradation` - Enable graceful degradation mode
- `npm run percy:disable-degradation` - Disable graceful degradation mode

## Test Execution with Percy

### Basic E2E Testing with Percy

- `npm run test:e2e:percy` - Run all E2E tests with Percy snapshots
- `npm run test:e2e:percy:ui` - Run Percy tests with Playwright UI
- `npm run test:e2e:percy:debug` - Run Percy tests in debug mode
- `npm run test:e2e:percy:setup` - Run Percy setup validation tests
- `npm run test:e2e:percy:performance` - Run Percy performance validation tests

### Module-Specific Percy Testing

- `npm run test:e2e:percy:helpdesk` - Run helpdesk module tests with Percy
- `npm run test:e2e:percy:loan` - Run loan module tests with Percy

### Accessibility Testing with Percy

- `npm run test:accessibility:percy` - Run accessibility tests with Percy snapshots
- `npm run test:accessibility:no-percy` - Run accessibility tests without Percy

### Percy Degradation Testing

- `npm run test:e2e:percy:degradation` - Test Percy degradation handling
- `npm run test:e2e:no-percy` - Run all tests without Percy (fallback mode)
- `npm run test:e2e:local-percy` - Run tests with local-only Percy mode
- `npm run test:e2e:fallback` - Run tests with automatic Percy fallback

## CI/CD Pipeline Scripts

### CI-Specific Percy Scripts

- `npm run ci:percy` - Run Percy tests in CI environment
- `npm run ci:percy:parallel` - Run Percy tests with parallel execution
- `npm run ci:percy:branch` - Run Percy tests with branch-specific configuration
- `npm run ci:percy:pr` - Run Percy tests for pull requests
- `npm run ci:test:all` - Run all tests (E2E + accessibility) with Percy

### Environment-Specific Scripts

- `npm run production:percy` - Run Percy tests for production environment
- `npm run production:percy:baseline` - Run Percy tests with production baseline
- `npm run staging:percy` - Run Percy tests for staging environment

## Development Workflow Scripts

### Quick Development Testing

- `npm run dev:percy:quick` - Quick Percy validation for development
- `npm run dev:percy:dashboard` - Test dashboard components with Percy
- `npm run dev:percy:forms` - Test form components with Percy
- `npm run dev:percy:accessibility` - Test accessibility features with Percy

### Feature-Specific Development Testing

- `npm run dev:percy:responsive` - Test responsive design with Percy
- `npm run dev:percy:hybrid` - Test True Hybrid Architecture with Percy
- `npm run dev:percy:bahasa` - Test Bahasa Melayu interface with Percy

## Environment Variables

The following environment variables can be used to configure Percy behavior:

### Required

- `PERCY_TOKEN` - Percy authentication token

### Optional

- `PERCY_BRANCH` - Git branch name (auto-detected in CI)
- `PERCY_TARGET_BRANCH` - Target branch for comparisons (default: main)
- `PERCY_PARALLEL_NONCE` - Unique identifier for parallel builds
- `PERCY_PARALLEL_TOTAL` - Total number of parallel jobs
- `PERCY_PROJECT` - Percy project name (default: ictserve-v3.6.1-visual-testing)
- `PERCY_ENABLED` - Enable/disable Percy integration (default: true)

### CI-Specific

- `CI_BUILD_ID` - CI build identifier
- `CI_BRANCH` - CI branch name
- `CI_PULL_REQUEST` - Pull request number

## Usage Examples

### Local Development

```bash
# Quick Percy validation
npm run dev:percy:quick

# Test specific components
npm run dev:percy:dashboard
npm run dev:percy:forms

# Test with specific features
npm run dev:percy:responsive
npm run dev:percy:hybrid
```

### CI/CD Pipeline

```bash
# Standard CI execution
npm run ci:percy

# Parallel execution (4 workers)
PERCY_PARALLEL_TOTAL=4 npm run ci:percy:parallel

# Pull request testing
CI_PULL_REQUEST=123 npm run ci:percy:pr
```

### Production Deployment

```bash
# Production baseline testing
npm run production:percy:baseline

# Staging environment testing
npm run staging:percy
```

### Troubleshooting

```bash
# Validate configuration
npm run percy:config-validate

# Check service health
npm run percy:health-check

# Generate diagnostic report
npm run percy:report

# Clean up temporary files
npm run percy:cleanup
```

## Integration with ICTServe v3.6.1

These scripts are specifically designed for the ICTServe v3.6.1 technology stack:

- **Laravel 12.43.1** - Server-side framework
- **Livewire 3.7.3** - Dynamic component testing
- **Filament 4.3.1** - Admin panel visual validation
- **Playwright 1.56.1** - E2E testing framework
- **True Hybrid Architecture** - Guest and authenticated user workflows
- **Bahasa Melayu Interface** - Language-specific visual validation
- **WCAG 2.2 AA Compliance** - Accessibility visual testing

## Best Practices

1. **Development**: Use `dev:percy:quick` for rapid feedback
2. **Feature Testing**: Use specific feature scripts (`dev:percy:responsive`, etc.)
3. **CI/CD**: Use `ci:percy` with appropriate environment variables
4. **Troubleshooting**: Start with `percy:health-check` and `percy:config-validate`
5. **Performance**: Use `percy:performance-report` to monitor impact
6. **Fallback**: Always test `test:e2e:no-percy` to ensure graceful degradation

## Support

For issues with Percy integration:

1. Check `npm run percy:health-check`
2. Validate configuration with `npm run percy:config-validate`
3. Review logs with `npm run percy:report`
4. Use fallback mode with `npm run test:e2e:no-percy`
