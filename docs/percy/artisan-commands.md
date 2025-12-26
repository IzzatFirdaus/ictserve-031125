# Percy Artisan Commands for ICTServe v3.6.1

This document describes the Laravel Artisan commands available for managing Percy visual testing integration in ICTServe v3.6.1.

## Available Commands

### 1. percy:validate-config
Validates Percy configuration and provides setup guidance.

```bash
# Basic validation
php artisan percy:validate-config

# Show current configuration
php artisan percy:validate-config --show-config

# Validate for specific environment
php artisan percy:validate-config --environment=staging
```

**Features:**

- Validates Percy token and project configuration
- Checks ICTServe v3.6.1 specific settings
- Shows technology stack compatibility
- Provides setup instructions in Bahasa Melayu

### 2. percy:check-status
Provides comprehensive status information for Percy integration.

```bash
# Basic status check
php artisan percy:check-status

# Detailed status with system information
php artisan percy:check-status --detailed

# Show recent builds
php artisan percy:check-status --builds

# Configuration status only
php artisan percy:check-status --config

# Services status only
php artisan percy:check-status --services

# JSON output for automation
php artisan percy:check-status --json
```

**Features:**

- Configuration validation status
- Percy API connectivity check
- Percy CLI availability
- Node.js dependencies status
- Recent builds information
- ICTServe v3.6.1 specific configuration
- Technology stack information
- Performance settings overview

### 3. percy:run-dusk
Executes Laravel Dusk tests with Percy visual testing integration.

```bash
# Run Dusk tests with Percy
php artisan percy:run-dusk

# Run without Percy (fallback mode)
php artisan percy:run-dusk --without-percy

# Run specific test group
php artisan percy:run-dusk --group=authentication

# Filter tests by name
php artisan percy:run-dusk --filter=LoginTest

# Run with parallel execution
php artisan percy:run-dusk --parallel=4

# Custom build name
php artisan percy:run-dusk --build-name="feature-login-v2"

# Debug mode
php artisan percy:run-dusk --debug

# Specific environment
php artisan percy:run-dusk --env=staging
```

**Features:**

- Automatic Percy configuration validation
- Database migration check
- Percy CLI integration
- Parallel test execution support
- Custom build naming
- Graceful fallback when Percy is unavailable
- Environment-specific configuration
- Comprehensive error handling

### 4. percy:manage-build
Manages Percy builds through the API.

```bash
# Create a new build
php artisan percy:manage-build create --name="manual-build" --branch=feature/ui-updates

# Check build status
php artisan percy:manage-build status --build-id=12345

# List recent builds
php artisan percy:manage-build list --limit=10

# Finalize a build
php artisan percy:manage-build finalize --build-id=12345

# Delete a build (with confirmation)
php artisan percy:manage-build delete --build-id=12345

# Force delete without confirmation
php artisan percy:manage-build delete --build-id=12345 --force
```

**Features:**

- Build creation with custom parameters
- Build status monitoring
- Build finalization
- Build deletion with safety checks
- Comprehensive build information display
- Integration with Percy API
- Error handling and validation

### 5. percy:set-token
Sets Percy authentication token in environment configuration.

```bash
# Set Percy token interactively
php artisan percy:set-token

# Set token directly
php artisan percy:set-token your_percy_token_here
```

**Features:**

- Secure token storage in .env file
- Token validation
- Configuration backup
- Setup guidance

## Usage Examples

### Initial Setup

```bash
# 1. Set Percy token
php artisan percy:set-token

# 2. Validate configuration
php artisan percy:validate-config --show-config

# 3. Check overall status
php artisan percy:check-status --detailed
```

### Running Tests

```bash
# Development testing
php artisan percy:run-dusk --without-percy --group=smoke

# Staging testing with Percy
php artisan percy:run-dusk --env=staging --build-name="staging-regression"

# Production-like testing
php artisan percy:run-dusk --parallel=4 --build-name="release-candidate"
```

### Build Management

```bash
# Create and monitor a build
php artisan percy:manage-build create --name="manual-qa-build"
php artisan percy:manage-build status --build-id=12345

# List and review builds
php artisan percy:manage-build list
```

### Monitoring and Debugging

```bash
# Quick health check
php artisan percy:check-status

# Detailed system information
php artisan percy:check-status --detailed

# JSON output for monitoring systems
php artisan percy:check-status --json > percy-status.json
```

## Integration with ICTServe v3.6.1

These commands are specifically designed for ICTServe v3.6.1 and include:

- **True Hybrid Architecture Support**: Commands handle both guest and authenticated user workflows
- **Bahasa Melayu Interface**: All output and error messages are in Bahasa Melayu
- **Technology Stack Validation**: Checks compatibility with Laravel 12.43.1, Livewire 3.7.3, Filament 4.3.1, and Playwright 1.56.1
- **WCAG 2.2 AA Compliance**: Configuration validation includes accessibility settings
- **Environment-Specific Configuration**: Support for different deployment environments

## Error Handling

All commands include comprehensive error handling:

- **Graceful Degradation**: Tests can run without Percy when services are unavailable
- **Configuration Validation**: Clear error messages with resolution steps
- **Network Resilience**: Automatic retry mechanisms for API calls
- **User-Friendly Messages**: All errors and guidance in Bahasa Melayu

## Automation and CI/CD

Commands support automation through:

- **JSON Output**: Machine-readable status information
- **Exit Codes**: Proper exit codes for CI/CD integration
- **Environment Variables**: Configuration through environment variables
- **Non-Interactive Mode**: All commands work in automated environments

## Security Considerations

- **Token Security**: Percy tokens are stored securely in .env files
- **API Rate Limiting**: Commands respect Percy API rate limits
- **Error Logging**: Sensitive information is not logged
- **Configuration Validation**: Prevents misconfiguration that could expose data

## Troubleshooting

Common issues and solutions:

1. **Token Not Found**: Run `php artisan percy:set-token`
2. **Percy CLI Missing**: Run `npm install --save-dev @percy/cli @percy/playwright`
3. **API Connection Failed**: Check network connectivity and token validity
4. **Build Creation Failed**: Verify project configuration and permissions
5. **Tests Failing**: Use `--without-percy` flag to isolate Percy-related issues

For detailed troubleshooting, use:

```bash
php artisan percy:check-status --detailed
php artisan percy:validate-config --show-config
```
