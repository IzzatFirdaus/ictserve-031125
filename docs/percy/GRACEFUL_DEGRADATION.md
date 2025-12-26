# Percy Graceful Degradation Features

This document describes the graceful degradation features implemented for Percy visual testing integration in ICTServe v3.6.1.

## Overview

Percy graceful degradation ensures that tests continue to run successfully even when Percy visual testing is unavailable, misconfigured, or disabled. This provides resilience and flexibility for different development environments and scenarios.

## Features

### 1. Automatic Degradation Detection

The system automatically detects when Percy should be disabled:

- **Missing Token**: When `PERCY_TOKEN` environment variable is not set
- **Service Unavailable**: When Percy API is unreachable
- **Configuration Errors**: When Percy configuration is invalid
- **Network Issues**: When network connectivity prevents Percy operations

### 2. Manual Degradation Control

You can manually control Percy degradation through environment variables or CLI commands:

#### Environment Variables

```bash
# Disable Percy completely
PERCY_ENABLED=false

# Enable graceful degradation (default: true)
PERCY_GRACEFUL_DEGRADATION=true

# Skip uploading snapshots (local testing)
PERCY_SKIP_UPLOADS=true

# Run in local-only mode
PERCY_LOCAL_ONLY=true

# Enable fallback mode
PERCY_FALLBACK_MODE=true
```

#### CLI Commands

```bash
# Check degradation status
node scripts/percy/percy-degradation-manager.cjs status

# Enable degradation with reason
node scripts/percy/percy-cli-wrapper.cjs enable-degradation "maintenance"

# Disable degradation
node scripts/percy/percy-cli-wrapper.cjs disable-degradation

# Check current degradation state
node scripts/percy/percy-cli-wrapper.cjs degradation-state
```

### 3. Test Execution Modes

#### Run Tests Without Percy

```bash
# Skip Percy completely
npm run test:e2e:no-percy

# Or use the degradation manager directly
node scripts/percy/percy-degradation-manager.cjs run-skip-percy "playwright test"
```

#### Run Tests in Local-Only Mode

```bash
# Local-only mode (no uploads)
npm run test:e2e:local-percy

# Or use the degradation manager directly
node scripts/percy/percy-degradation-manager.cjs run-local-only "playwright test"
```

#### Run Tests with Fallback Support

```bash
# Automatic fallback on errors
npm run test:e2e:fallback

# Or use the degradation manager directly
node scripts/percy/percy-degradation-manager.cjs run "playwright test"
```

### 4. Configuration-Based Degradation

Create a `percy-degradation.config.json` file to configure environment-specific degradation settings:

```json
{
  "degradation": {
    "enabled": true,
    "skipUploads": false,
    "localOnly": false,
    "fallbackMode": false
  },
  "environments": {
    "development": {
      "enabled": true,
      "localOnly": true,
      "skipUploads": false,
      "gracefulDegradation": true
    },
    "testing": {
      "enabled": true,
      "localOnly": false,
      "skipUploads": false,
      "gracefulDegradation": true
    },
    "ci": {
      "enabled": true,
      "localOnly": false,
      "skipUploads": false,
      "gracefulDegradation": true
    },
    "production": {
      "enabled": false,
      "localOnly": false,
      "skipUploads": true,
      "gracefulDegradation": true
    }
  }
}
```

Apply environment-specific configuration:

```bash
# Apply development environment settings
node scripts/percy/percy-degradation-manager.cjs apply-env development

# Apply CI environment settings
node scripts/percy/percy-degradation-manager.cjs apply-env ci
```

## Degradation Scenarios

### 1. Missing Percy Token

**Scenario**: `PERCY_TOKEN` environment variable is not set.

**Behavior**:

- Percy is automatically disabled
- Tests run without visual snapshots
- Warning messages are logged
- Tests complete successfully

**Resolution**: Set the `PERCY_TOKEN` environment variable.

### 2. Percy Service Unavailable

**Scenario**: Percy API is unreachable or returning errors.

**Behavior**:

- Automatic retry with exponential backoff
- After max retries, graceful degradation is enabled
- Tests continue without visual snapshots
- Error details are logged for debugging

**Resolution**: Check Percy service status and network connectivity.

### 3. Network Connectivity Issues

**Scenario**: Network issues prevent Percy operations.

**Behavior**:

- Automatic retry mechanisms
- Graceful degradation after retry limit
- Tests continue execution
- Network errors are logged

**Resolution**: Check network connectivity and firewall settings.

### 4. Configuration Errors

**Scenario**: Percy configuration is invalid or incomplete.

**Behavior**:

- Configuration validation fails
- Helpful error messages with resolution steps
- Graceful degradation (if enabled)
- Tests continue without Percy

**Resolution**: Fix configuration based on error messages.

## Monitoring and Reporting

### Degradation Status

Check the current degradation status:

```bash
node scripts/percy/percy-degradation-manager.cjs status
```

Example output:

```json
{
  "percyEnabled": false,
  "gracefulDegradation": true,
  "degradationReasons": ["missing-token"],
  "modes": {
    "skipUploads": false,
    "localOnly": false,
    "fallbackMode": false
  },
  "environment": {
    "PERCY_TOKEN": "[MISSING]"
  }
}
```

### Degradation Report

Generate a comprehensive degradation report:

```bash
node scripts/percy/percy-degradation-manager.cjs report
```

The report includes:

- Current degradation status
- Configuration settings
- Recommendations for improvement
- Environment information

### Error Statistics

View error handling statistics:

```bash
node scripts/percy/percy-cli-wrapper.cjs error-stats
```

## Best Practices

### 1. Development Environment

- Use local-only mode to avoid unnecessary uploads
- Enable graceful degradation for resilience
- Set up environment-specific configuration

```bash
# Development setup
export PERCY_LOCAL_ONLY=true
export PERCY_GRACEFUL_DEGRADATION=true
```

### 2. CI/CD Environment

- Always enable graceful degradation in CI
- Use proper Percy tokens and configuration
- Monitor degradation reports for issues

```bash
# CI setup
export PERCY_ENABLED=true
export PERCY_GRACEFUL_DEGRADATION=true
export PERCY_TOKEN=$PERCY_TOKEN_SECRET
```

### 3. Testing Strategy

- Test both with and without Percy enabled
- Validate degradation scenarios
- Monitor test execution times

```bash
# Test with Percy
npm run test:e2e:percy

# Test without Percy
npm run test:e2e:no-percy

# Test degradation scenarios
npm run test:e2e:percy:degradation
```

### 4. Error Handling

- Monitor error logs and statistics
- Set up alerts for critical errors
- Regularly review degradation reports

```bash
# Generate error report
node scripts/percy/percy-cli-wrapper.cjs save-error-report

# Check error statistics
node scripts/percy/percy-cli-wrapper.cjs error-stats
```

## Troubleshooting

### Common Issues

1. **Tests fail when Percy is disabled**
   - Ensure tests don't depend on Percy-specific functionality
   - Use graceful degradation validation tests

2. **Percy snapshots not being skipped**
   - Check environment variables
   - Verify degradation configuration
   - Review error logs

3. **Degradation not working in CI**
   - Ensure proper environment variable setup
   - Check CI configuration
   - Review CI logs for errors

### Debug Commands

```bash
# Check Percy configuration
node scripts/percy/percy-cli-wrapper.cjs validate

# Check degradation state
node scripts/percy/percy-cli-wrapper.cjs degradation-state

# Generate comprehensive report
node scripts/percy/percy-cli-wrapper.cjs error-report

# Test degradation scenarios
npm run test:e2e:percy:degradation
```

## Integration with ICTServe v3.6.1

The graceful degradation features are fully integrated with ICTServe's architecture:

- **True Hybrid Architecture**: Supports both guest and authenticated user workflows
- **Bahasa Melayu Interface**: Handles Bahasa Melayu UI elements gracefully
- **Laravel 12.43.1**: Compatible with Laravel's environment configuration
- **Playwright 1.56.1**: Integrated with existing Playwright test framework
- **Comprehensive E2E Suite**: Works with all 16+ existing test files

## Conclusion

Percy graceful degradation provides robust fallback mechanisms that ensure test reliability and developer productivity even when visual testing is unavailable. The features are designed to be transparent, configurable, and provide helpful feedback for troubleshooting and optimization.
