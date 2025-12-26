# Percy Performance Optimization for ICTServe v3.6.1

This document describes the comprehensive performance optimization features implemented for Percy visual testing integration in ICTServe v3.6.1.

## Overview

The Percy Performance Optimizer provides advanced performance features to minimize the impact of visual testing on development workflows while maximizing efficiency and reliability.

## Features

### 1. Asynchronous Snapshot Upload Capabilities

**Purpose**: Enable non-blocking snapshot uploads to improve test execution speed.

**Implementation**:

- Concurrent upload processing with configurable limits
- Queue-based upload management
- Background processing with worker threads
- Priority-based snapshot processing

**Configuration**:

```bash
# Environment Variables
PERCY_ENABLE_ASYNC_UPLOADS=true          # Enable async uploads (default: true)
PERCY_MAX_CONCURRENT_UPLOADS=3           # Max concurrent uploads (default: 3)
```

**Benefits**:

- Reduced test execution time
- Better resource utilization
- Non-blocking test workflows
- Improved user experience

### 2. Network Usage Optimization

**Purpose**: Minimize network bandwidth usage and optimize data transfer.

**Implementation**:

- Intelligent compression algorithms
- Batch processing for multiple snapshots
- Request deduplication and caching
- Retry mechanisms with exponential backoff

**Configuration**:

```bash
# Environment Variables
PERCY_COMPRESSION_ENABLED=true           # Enable compression (default: true)
PERCY_BATCH_SIZE=5                       # Batch size for processing (default: 5)
```

**Benefits**:

- Reduced bandwidth usage
- Faster upload times
- Lower network costs
- Improved reliability

### 3. Caching Mechanisms

**Purpose**: Cache Percy CLI dependencies and snapshot data to improve performance.

**Implementation**:

- Intelligent caching with TTL (Time To Live)
- LRU (Least Recently Used) eviction policy
- Persistent cache storage
- Cache optimization and cleanup

**Configuration**:

```bash
# Environment Variables
PERCY_ENABLE_CACHING=true                # Enable caching (default: true)
PERCY_CACHE_MAX_AGE=86400000            # Cache max age in ms (default: 24 hours)
PERCY_CACHE_MAX_SIZE=104857600          # Cache max size in bytes (default: 100MB)
```

**Benefits**:

- Faster subsequent runs
- Reduced API calls
- Lower resource usage
- Improved offline capabilities

### 4. Performance Monitoring and Impact Measurement

**Purpose**: Monitor and measure the performance impact of Percy integration.

**Implementation**:

- Real-time performance metrics collection
- Memory usage monitoring
- Upload speed tracking
- Comprehensive reporting

**Configuration**:

```bash
# Environment Variables
PERCY_ENABLE_PERFORMANCE_OPTIMIZATION=true  # Enable performance optimization (default: true)
PERCY_PERFORMANCE_REPORT_INTERVAL=10000     # Report interval in ms (default: 10 seconds)
PERCY_MEMORY_THRESHOLD=524288000            # Memory threshold in bytes (default: 500MB)
```

**Metrics Tracked**:

- Upload success/failure rates
- Average upload times
- Memory usage patterns
- Cache hit/miss ratios
- Network bandwidth usage
- Queue processing efficiency

## Usage

### Basic Usage

```javascript
// Using the performance optimizer directly
const PercyPerformanceOptimizer = require('./scripts/percy/percy-performance-optimizer.cjs');

const optimizer = new PercyPerformanceOptimizer({
    maxConcurrentUploads: 3,
    batchSize: 5,
    enableCaching: true,
    enableAsyncUploads: true,
    compressionEnabled: true,
});

// Add snapshots to the optimized queue
await optimizer.addSnapshotToQueue({
    name: 'Homepage Test',
    path: '/path/to/snapshot',
    size: 1024000,
    type: 'image',
    priority: 1,
});

// Wait for all uploads to complete
await optimizer.waitForUploadsComplete();

// Get performance report
const report = optimizer.getPerformanceReport();
console.log(report);
```

### Integration with Percy CLI Wrapper

The performance optimizer is automatically integrated with the Percy CLI wrapper when enabled:

```bash
# Run tests with performance optimization
npm run test:e2e:percy

# Check performance metrics
npm run percy:performance-report

# Save detailed performance report
npm run percy:save-performance-report

# Wait for all uploads to complete
npm run percy:wait-uploads
```

### Testing Performance Optimization

Run the performance validation tests to verify optimization features:

```bash
# Run performance validation tests
npm run test:e2e:percy:performance

# Run basic setup validation
npm run test:e2e:percy:setup
```

## Performance Metrics

### Upload Metrics

- **Total Uploads**: Number of snapshots processed
- **Success Rate**: Percentage of successful uploads
- **Average Upload Time**: Mean time per upload
- **Concurrent Uploads**: Current active uploads
- **Queue Size**: Number of pending uploads

### Network Metrics

- **Total Requests**: Number of network requests made
- **Total Bytes**: Amount of data transferred
- **Compression Savings**: Bytes saved through compression
- **Retry Count**: Number of retry attempts
- **Timeout Count**: Number of timeout occurrences

### Cache Metrics

- **Cache Hits**: Number of cache hits
- **Cache Misses**: Number of cache misses
- **Hit Rate**: Percentage of cache hits
- **Cache Size**: Current cache size
- **Evictions**: Number of cache evictions

### Memory Metrics

- **Peak Memory**: Maximum memory usage
- **Current Memory**: Current memory usage
- **GC Count**: Number of garbage collections
- **Memory per Upload**: Average memory per upload

## Configuration Options

### Performance Optimization Settings

| Environment Variable | Default | Description |
|---------------------|---------|-------------|
| `PERCY_ENABLE_PERFORMANCE_OPTIMIZATION` | `true` | Enable performance optimization |
| `PERCY_MAX_CONCURRENT_UPLOADS` | `3` | Maximum concurrent uploads |
| `PERCY_BATCH_SIZE` | `5` | Batch size for processing |
| `PERCY_ENABLE_CACHING` | `true` | Enable caching mechanisms |
| `PERCY_ENABLE_ASYNC_UPLOADS` | `true` | Enable asynchronous uploads |
| `PERCY_COMPRESSION_ENABLED` | `true` | Enable compression |

### Cache Settings

| Environment Variable | Default | Description |
|---------------------|---------|-------------|
| `PERCY_CACHE_DIRECTORY` | `.percy-cache` | Cache directory path |
| `PERCY_CACHE_MAX_AGE` | `86400000` | Cache max age (24 hours) |
| `PERCY_CACHE_MAX_SIZE` | `104857600` | Cache max size (100MB) |
| `PERCY_ENABLE_QUEUE_PERSISTENCE` | `true` | Enable queue persistence |

### Monitoring Settings

| Environment Variable | Default | Description |
|---------------------|---------|-------------|
| `PERCY_PERFORMANCE_REPORT_INTERVAL` | `10000` | Report interval (10 seconds) |
| `PERCY_MEMORY_THRESHOLD` | `524288000` | Memory threshold (500MB) |
| `PERCY_UPLOAD_TIMEOUT` | `30000` | Upload timeout (30 seconds) |
| `PERCY_RETRY_ATTEMPTS` | `3` | Maximum retry attempts |

## CLI Commands

### Performance Optimizer Commands

```bash
# Test performance optimization
node scripts/percy/percy-performance-optimizer.cjs test-upload

# Get performance report
node scripts/percy/percy-performance-optimizer.cjs performance-report

# Save performance report
node scripts/percy/percy-performance-optimizer.cjs save-report

# Wait for uploads to complete
node scripts/percy/percy-performance-optimizer.cjs wait-complete

# Clear caches
node scripts/percy/percy-performance-optimizer.cjs clear-cache

# Shutdown optimizer
node scripts/percy/percy-performance-optimizer.cjs shutdown
```

### Percy CLI Wrapper Commands

```bash
# Get performance metrics
node scripts/percy/percy-cli-wrapper.cjs performance-metrics

# Save performance report
node scripts/percy/percy-cli-wrapper.cjs save-performance-report

# Wait for uploads
node scripts/percy/percy-cli-wrapper.cjs wait-uploads 30000

# Shutdown with cleanup
node scripts/percy/percy-cli-wrapper.cjs shutdown
```

## Best Practices

### 1. Optimal Configuration

- Set `maxConcurrentUploads` based on available bandwidth (2-4 for most cases)
- Use appropriate `batchSize` for your test suite size (5-10 for most cases)
- Enable caching for repeated test runs
- Monitor memory usage and adjust thresholds accordingly

### 2. Test Organization

- Group related snapshots for better batch processing
- Use priority levels for critical snapshots
- Implement proper cleanup in test teardown
- Monitor performance metrics regularly

### 3. Resource Management

- Monitor memory usage during large test runs
- Clear caches periodically to prevent disk space issues
- Use appropriate timeouts for network operations
- Implement proper error handling and retry logic

### 4. CI/CD Integration

- Configure appropriate timeouts for CI environments
- Use performance reports for build optimization
- Monitor trends in performance metrics
- Set up alerts for performance degradation

## Troubleshooting

### Common Issues

1. **High Memory Usage**
   - Reduce `maxConcurrentUploads`
   - Enable garbage collection
   - Clear caches more frequently
   - Monitor memory threshold settings

2. **Slow Upload Performance**
   - Check network connectivity
   - Verify compression settings
   - Review batch size configuration
   - Monitor retry attempts

3. **Cache Issues**
   - Clear cache directory manually
   - Check disk space availability
   - Verify cache permissions
   - Review cache size limits

4. **Queue Processing Problems**
   - Check queue persistence settings
   - Monitor active upload counts
   - Verify timeout configurations
   - Review error logs

### Performance Debugging

Enable debug logging for detailed performance information:

```bash
export PERCY_DEBUG=true
export PERCY_PERFORMANCE_DEBUG=true
```

Check performance logs:

```bash
# View performance logs
tail -f percy-logs/percy-info-*.log

# Check error logs
tail -f percy-logs/percy-error-*.log
```

## Integration with ICTServe v3.6.1

The performance optimization features are fully integrated with ICTServe's technology stack:

- **Laravel 12.43.1**: Configuration management through environment variables
- **Playwright 1.56.1**: Seamless integration with existing test framework
- **Livewire 3.7.3**: Optimized handling of dynamic content
- **Filament 4.3.1**: Enhanced admin panel visual testing
- **True Hybrid Architecture**: Support for both guest and authenticated workflows
- **Bahasa Melayu Interface**: Localized error messages and reporting

## Conclusion

The Percy Performance Optimizer provides comprehensive performance enhancements for visual testing in ICTServe v3.6.1. By implementing asynchronous uploads, network optimization, intelligent caching, and detailed monitoring, it significantly reduces the impact of visual testing on development workflows while maintaining reliability and accuracy.

For additional support or questions, refer to the Percy documentation or contact the ICTServe development team.
