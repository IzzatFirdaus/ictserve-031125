# Percy Error Handling and Resilience Implementation

## Overview

This document describes the comprehensive error handling and resilience features implemented for Percy visual testing integration in ICTServe v3.6.1. The implementation provides robust error recovery, graceful degradation, and detailed logging capabilities to ensure reliable visual testing operations.

## Architecture

### Core Components

1. **PercyErrorHandler** (`scripts/percy/percy-error-handler.cjs`)
   - Comprehensive error classification and handling
   - Automatic retry mechanisms with exponential backoff
   - Graceful degradation when Percy services are unavailable
   - Error statistics tracking and reporting

2. **Enhanced PercyCliWrapper** (`scripts/percy/percy-cli-wrapper.cjs`)
   - Integration with PercyErrorHandler for resilient operations
   - Enhanced configuration validation with detailed error messages
   - Timeout handling for network operations
   - Comprehensive error reporting

3. **PercyLogger** (`scripts/percy/percy-logger.cjs`)
   - Structured logging with multiple levels (error, warn, info, debug)
   - File-based logging with automatic rotation
   - Performance metrics tracking
   - Bahasa Melayu support for v3.6.1 compatibility

## Error Types and Handling

### Configuration Errors

**Type**: `ConfigurationError`
**Scenarios**:

- Missing Percy token (`PERCY_TOKEN` not set)
- Invalid Percy project configuration
- Percy CLI not available or not installed
- Invalid configuration parameters

**Handling**:

- Provides specific resolution steps for each error type
- Supports graceful degradation for non-critical errors
- Detailed error messages with actionable guidance

**Example**:

```javascript
const error = PercyErrorHandler.createConfigurationError(
    'Percy token is missing',
    'token'
);
// Provides resolution steps:
// 1. Obtain Percy token from BrowserStack Percy dashboard
// 2. Set PERCY_TOKEN environment variable
// 3. Ensure token has proper permissions
// 4. Verify token is not expired
```

### Network Errors

**Type**: `NetworkError`
**Scenarios**:

- Connection timeouts during snapshot upload
- Network connectivity issues
- Percy API unavailability
- Rate limiting responses

**Handling**:

- Automatic retry with exponential backoff (1s, 2s, 4s, 8s, etc.)
- Maximum retry limit (default: 3 attempts)
- Graceful degradation after retry exhaustion
- Network operation timeout handling

**Example**:

```javascript
const error = PercyErrorHandler.createNetworkError(
    'Connection timeout',
    'snapshot-upload',
    0, // current retry count
    3  // max retries
);
// Automatically retries with increasing delays
```

### Service Errors

**Type**: `ServiceError`
**Scenarios**:

- Percy service temporarily unavailable (503, 502, 500)
- Authentication failures (401, 403)
- Rate limiting (429)
- Invalid API responses

**Handling**:

- Differentiation between temporary and permanent failures
- Appropriate response based on HTTP status codes
- Graceful degradation for temporary issues
- Detailed error reporting with service status

**Example**:

```javascript
const error = PercyErrorHandler.createServiceError(
    'Percy API unavailable',
    'percy',
    503 // HTTP status code
);
// Enables graceful degradation for temporary service issues
```

### Critical Errors

**Type**: `PercyCriticalError`
**Scenarios**:

- Unrecoverable configuration issues
- System-level failures
- Security-related errors

**Handling**:

- Immediate failure without retry
- Detailed error reporting
- Optional graceful degradation based on configuration

## Graceful Degradation

### Mechanism

When Percy services are unavailable or configuration is invalid, the system can continue test execution without visual captures:

1. **Automatic Detection**: Error handler detects Percy unavailability
2. **Environment Variables**: Sets `PERCY_ENABLED=false` and `PERCY_SKIP_UPLOADS=true`
3. **Test Continuation**: Tests continue normal execution without Percy operations
4. **Logging**: Detailed logging of degradation events

### Configuration

```javascript
const errorHandler = new PercyErrorHandler({
    enableGracefulDegradation: true, // Enable/disable graceful degradation
    maxRetries: 3,                   // Maximum retry attempts
    retryDelay: 1000,               // Initial retry delay (ms)
    failOnError: false              // Fail immediately on errors
});
```

### Environment Variables

- `PERCY_ENABLED`: Enable/disable Percy integration
- `PERCY_FAIL_ON_ERROR`: Fail tests on Percy errors (default: false)
- `PERCY_MAX_RETRIES`: Maximum retry attempts (default: 3)
- `PERCY_RETRY_DELAY`: Initial retry delay in milliseconds (default: 1000)
- `PERCY_GRACEFUL_DEGRADATION`: Enable graceful degradation (default: true)

## Logging and Monitoring

### Log Levels

1. **ERROR**: Critical errors and failures
2. **WARN**: Warnings and recoverable issues
3. **INFO**: General information and successful operations
4. **DEBUG**: Detailed debugging information

### Log Outputs

1. **Console Output**: Real-time logging with color coding
2. **File Output**: Persistent logging with automatic rotation
3. **Structured Logging**: JSON metadata for detailed analysis

### Performance Metrics

- Total operation duration
- Average operation time
- Error rates and statistics
- Snapshot capture metrics
- Retry operation tracking

### Bahasa Melayu Support

For ICTServe v3.6.1 compatibility, the logger supports Bahasa Melayu messages:

```javascript
const logger = new PercyLogger({
    bahasaMelayu: true
});

// Logs in Bahasa Melayu:
// "Binaan Percy berjaya dicipta" (Percy build created successfully)
// "Tangkapan skrin Percy diambil" (Percy snapshot captured)
// "Ralat perkhidmatan Percy" (Percy service error)
```

## Error Reporting

### Comprehensive Reports

The system generates detailed error reports including:

- Error statistics and trends
- Performance metrics
- Configuration details
- Environment information
- Recent operations and failures

### Report Generation

```bash
# Generate error report
node scripts/percy/percy-cli-wrapper.cjs error-report

# Save error report to file
node scripts/percy/percy-cli-wrapper.cjs save-error-report

# Get error statistics
node scripts/percy/percy-cli-wrapper.cjs error-stats
```

### Report Structure

```json
{
  "percy": {
    "buildInfo": { ... },
    "configuration": { ... },
    "environment": { ... }
  },
  "errorHandling": {
    "summary": {
      "totalErrors": 0,
      "criticalErrors": 0,
      "recoveredErrors": 0,
      "percyEnabled": true,
      "gracefulDegradationEnabled": true
    },
    "errorsByType": { ... },
    "retryOperations": [ ... ],
    "configuration": { ... },
    "environment": { ... }
  },
  "timestamp": "2025-12-25T23:56:44.565Z"
}
```

## Usage Examples

### Basic Error Handling

```javascript
const { PercyErrorHandler } = require('./scripts/percy/percy-error-handler.cjs');

const errorHandler = new PercyErrorHandler({
    enableGracefulDegradation: true,
    maxRetries: 3,
    logLevel: 'info'
});

// Handle configuration error
try {
    // Percy operation
} catch (error) {
    const result = errorHandler.handleError(error);
    if (result.gracefulDegradation) {
        console.log('Continuing without Percy');
    }
}
```

### Enhanced CLI Wrapper Usage

```javascript
const PercyCliWrapper = require('./scripts/percy/percy-cli-wrapper.cjs');

const wrapper = new PercyCliWrapper({
    enableGracefulDegradation: true,
    maxRetries: 3,
    failOnError: false
});

// Validate configuration with error handling
const validation = await wrapper.validateConfiguration();
if (!validation.valid && validation.gracefulDegradation) {
    console.log('Percy unavailable, continuing tests');
}
```

### Logging with Performance Tracking

```javascript
const PercyLogger = require('./scripts/percy/percy-logger.cjs');

const logger = new PercyLogger({
    logLevel: 'debug',
    bahasaMelayu: true // For ICTServe v3.6.1
});

// Track operation performance
const startTime = logger.startTiming('snapshot-capture');
// ... perform operation ...
logger.endTiming('snapshot-capture', startTime, { result: 'success' });

// Log Percy-specific operations
logger.logPercyOperation('build-create', 'success', { buildId: 'test-123' });
```

## Testing and Validation

### Error Handler Testing

```bash
# Test configuration error handling
node scripts/percy/percy-error-handler.cjs test-config-error

# Test network error handling with retry
node scripts/percy/percy-error-handler.cjs test-network-error

# Test service error handling
node scripts/percy/percy-error-handler.cjs test-service-error

# Generate error statistics
node scripts/percy/percy-error-handler.cjs stats
```

### CLI Wrapper Testing

```bash
# Validate Percy configuration
node scripts/percy/percy-cli-wrapper.cjs validate

# Check Percy enabled status
node scripts/percy/percy-cli-wrapper.cjs percy-enabled

# Reset error handler state
node scripts/percy/percy-cli-wrapper.cjs reset-errors
```

### Logger Testing

```bash
# Test all log levels
node scripts/percy/percy-logger.cjs test-logging

# Test Percy operations logging
node scripts/percy/percy-logger.cjs test-percy-operations

# Test Bahasa Melayu logging
node scripts/percy/percy-logger.cjs test-percy-operations --bahasa-melayu

# Generate performance metrics
node scripts/percy/percy-logger.cjs performance-metrics
```

## Integration with Playwright Tests

### Graceful Degradation in Tests

```typescript
import { test, expect } from '@playwright/test';

test('should continue when Percy is unavailable', async ({ page }) => {
    // Percy may be unavailable, but test continues
    await page.goto('/');
    
    // Normal test assertions continue to work
    await expect(page.locator('body')).toBeVisible();
    
    // Test functionality is preserved
    const title = await page.title();
    expect(title).toBeTruthy();
});
```

### Error Handling in Test Setup

```typescript
test.beforeEach(async () => {
    // Reset Percy error state for clean tests
    process.env.PERCY_ENABLED = 'true';
    
    // Configure error handling
    process.env.PERCY_GRACEFUL_DEGRADATION = 'true';
    process.env.PERCY_FAIL_ON_ERROR = 'false';
});
```

## Best Practices

### Configuration

1. **Always enable graceful degradation** for non-critical environments
2. **Set appropriate retry limits** based on network conditions
3. **Use debug logging** during development and troubleshooting
4. **Configure timeouts** based on expected operation duration

### Error Handling

1. **Check error types** before applying specific handling logic
2. **Log all errors** with sufficient context for debugging
3. **Provide actionable resolution steps** for configuration errors
4. **Monitor error rates** and trends over time

### Performance

1. **Use asynchronous operations** where possible
2. **Implement timeout handling** for all network operations
3. **Track performance metrics** to identify bottlenecks
4. **Optimize retry strategies** based on error patterns

### Monitoring

1. **Generate regular error reports** for trend analysis
2. **Monitor graceful degradation events** to identify service issues
3. **Track retry operation success rates** to optimize retry logic
4. **Use structured logging** for automated analysis

## Troubleshooting

### Common Issues

1. **Percy token not configured**
   - Solution: Set `PERCY_TOKEN` environment variable
   - Check: Token permissions and expiration

2. **Percy CLI not available**
   - Solution: Install Percy CLI (`npm install --save-dev @percy/cli`)
   - Check: PATH configuration and Node.js version

3. **Network connectivity issues**
   - Solution: Check firewall and proxy settings
   - Check: Percy service status at <https://status.percy.io/>

4. **High error rates**
   - Solution: Review retry configuration and network conditions
   - Check: Error reports for patterns and trends

### Debugging Steps

1. **Enable debug logging**: Set `PERCY_DEBUG=true`
2. **Check configuration**: Run `validate` command
3. **Review error reports**: Generate comprehensive error report
4. **Monitor performance**: Check performance metrics
5. **Test error handling**: Use test commands to validate behavior

## Conclusion

The comprehensive error handling and resilience implementation provides robust Percy integration for ICTServe v3.6.1, ensuring reliable visual testing operations even when Percy services are unavailable. The system supports graceful degradation, detailed logging, and comprehensive error reporting to maintain test reliability and provide actionable debugging information.

The implementation follows best practices for error handling, retry logic, and monitoring, making it suitable for production use in CI/CD environments while providing excellent developer experience during local development.
