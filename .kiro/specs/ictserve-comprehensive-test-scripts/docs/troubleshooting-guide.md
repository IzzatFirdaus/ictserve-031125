# Troubleshooting Guide - ICTServe Automation Scripts

## Common Issues and Solutions

### Browser and WebDriver Issues

#### Issue: Browser driver not found or incompatible version

**Symptoms:**

- Error: "WebDriver executable not found"
- Error: "This version of ChromeDriver only supports Chrome version X"
- Browser fails to launch

**Solutions:**

```powershell
# Update browser drivers automatically
.\utilities\browser-automation.ps1 -UpdateDrivers

# Check driver compatibility
.\utilities\browser-automation.ps1 -CheckCompatibility

# Download specific driver version
.\utilities\browser-automation.ps1 -DownloadDriver chrome -Version "120.0.6099.109"

# Reset driver configuration
.\utilities\browser-automation.ps1 -ResetDrivers
```

#### Issue: Browser crashes or becomes unresponsive

**Symptoms:**

- Browser window freezes during automation
- "Browser process has crashed" error
- Timeout errors during page loading

**Solutions:**

```powershell
# Run with increased timeouts
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Timeout 60000

# Use headless mode to reduce resource usage
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Mode Headless

# Clear browser cache and data
.\utilities\browser-automation.ps1 -ClearBrowserData

# Restart browser service
.\utilities\browser-automation.ps1 -RestartBrowser
```

#### Issue: Visual demonstration mode not working

**Symptoms:**

- Browser window not visible during demo mode
- Element highlighting not appearing
- Annotations not displaying

**Solutions:**

```powershell
# Verify demo configuration
.\utilities\config-loader.ps1 -ValidateConfig demo-settings

# Reset demo settings to defaults
.\utilities\config-loader.ps1 -ResetDemoSettings

# Test visual features individually
.\utilities\visual-demo-helpers.ps1 -TestHighlighting
.\utilities\visual-demo-helpers.ps1 -TestAnnotations
.\utilities\visual-demo-helpers.ps1 -TestMouseCursor

# Run in debug mode
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Mode Demo -Debug
```

### Network and Connectivity Issues

#### Issue: Cannot connect to ICTServe system

**Symptoms:**

- "Connection refused" errors
- "Host not found" errors
- Timeout errors during page loading

**Solutions:**

```powershell
# Test connectivity to target environment
.\utilities\common-functions.ps1 -TestConnectivity -Environment testing

# Check DNS resolution
.\utilities\common-functions.ps1 -TestDNS -Hostname "test.ictserve.motac.gov.my"

# Verify network configuration
.\utilities\common-functions.ps1 -CheckNetworkConfig

# Test with different environment
.\Main-Menu.ps1 -Environment development
```

#### Issue: API endpoints returning errors

**Symptoms:**

- HTTP 404, 500, or 503 errors
- API authentication failures
- Timeout errors on API calls

**Solutions:**

```powershell
# Test API endpoints
.\utilities\api-helpers.ps1 -TestEndpoints -Environment testing

# Validate API credentials
.\utilities\api-helpers.ps1 -ValidateCredentials -Environment testing

# Check API service status
.\utilities\api-helpers.ps1 -CheckServiceStatus -Environment testing

# Test with different API version
.\utilities\api-helpers.ps1 -TestEndpoints -Environment testing -ApiVersion v2
```

#### Issue: WebSocket connections failing

**Symptoms:**

- Real-time features not working
- "WebSocket connection failed" errors
- Notifications not appearing

**Solutions:**

```powershell
# Test WebSocket connectivity
.\utilities\api-helpers.ps1 -TestWebSocket -Environment testing

# Check Laravel Reverb service
.\utilities\api-helpers.ps1 -CheckReverbStatus -Environment testing

# Test with fallback polling
.\scripts\authenticated-workflows\dashboard\test-real-time-updates.ps1 -FallbackMode polling
```

### Authentication and Authorization Issues

#### Issue: Login failures with valid credentials

**Symptoms:**

- "Invalid credentials" errors with correct username/password
- Authentication timeouts
- Session creation failures

**Solutions:**

```powershell
# Validate stored credentials
.\utilities\config-loader.ps1 -ValidateCredentials -Environment testing

# Test manual login
.\utilities\config-loader.ps1 -TestLogin -Environment testing -Username "demo.user@motac.gov.my"

# Clear browser cookies and session data
.\utilities\browser-automation.ps1 -ClearSession

# Reset test user password
.\utilities\data-generators.ps1 -ResetUserPassword -Environment testing -User "demo.user"
```

#### Issue: Google Workspace SSO not working

**Symptoms:**

- OAuth redirect failures
- "Domain not authorized" errors
- SSO timeout errors

**Solutions:**

```powershell
# Test OAuth configuration
.\utilities\api-helpers.ps1 -TestOAuth -Environment testing

# Validate domain configuration
.\utilities\api-helpers.ps1 -ValidateDomain -Domain "motac.gov.my"

# Test with manual OAuth flow
.\scripts\authenticated-workflows\authentication\test-google-sso.ps1 -ManualMode

# Check Google Workspace settings
.\utilities\api-helpers.ps1 -CheckGoogleWorkspace -Domain "motac.gov.my"
```

#### Issue: Admin panel access denied

**Symptoms:**

- "Access denied" errors for admin users
- Role-based restrictions not working correctly
- Permission errors in Filament panel

**Solutions:**

```powershell
# Validate admin credentials
.\utilities\config-loader.ps1 -ValidateCredentials -Environment testing -Role admin

# Test role assignments
.\utilities\api-helpers.ps1 -TestRoleAssignment -Environment testing -User "admin@motac.gov.my"

# Check Filament configuration
.\utilities\api-helpers.ps1 -CheckFilamentConfig -Environment testing

# Reset admin permissions
.\utilities\data-generators.ps1 -ResetAdminPermissions -Environment testing
```

### AI Integration Issues

#### Issue: Ollama local AI server not responding

**Symptoms:**

- "Connection refused" to Ollama server
- AI model loading failures
- Timeout errors on AI requests

**Solutions:**

```powershell
# Check Ollama service status
.\utilities\api-helpers.ps1 -CheckOllamaStatus

# Start Ollama service
.\utilities\api-helpers.ps1 -StartOllama

# Test Ollama connectivity
.\utilities\api-helpers.ps1 -TestOllama -BaseUrl "http://localhost:11434"

# Download required models
.\utilities\api-helpers.ps1 -DownloadOllamaModel -Model "llama2"
```

#### Issue: AWS Bedrock authentication failures

**Symptoms:**

- "Access denied" errors from AWS Bedrock
- Invalid credentials errors
- Region not supported errors

**Solutions:**

```powershell
# Validate AWS credentials
.\utilities\config-loader.ps1 -ValidateAWSCredentials

# Test Bedrock connectivity
.\utilities\api-helpers.ps1 -TestBedrock -Region "ap-southeast-1"

# Check Bedrock model availability
.\utilities\api-helpers.ps1 -ListBedrockModels -Region "ap-southeast-1"

# Update AWS credentials
.\utilities\config-loader.ps1 -UpdateAWSCredentials
```

#### Issue: AI model routing not working correctly

**Symptoms:**

- Sensitive data being sent to cloud models
- Wrong model selection for query complexity
- Model routing rules not applied

**Solutions:**

```powershell
# Test model routing logic
.\utilities\api-helpers.ps1 -TestModelRouting

# Validate routing rules
.\utilities\config-loader.ps1 -ValidateRoutingRules

# Test data classification
.\utilities\api-helpers.ps1 -TestDataClassification -Text "This is sensitive personal data"

# Reset routing configuration
.\utilities\config-loader.ps1 -ResetRoutingConfig
```

### Performance and Resource Issues

#### Issue: Scripts running slowly or timing out

**Symptoms:**

- Long execution times
- Timeout errors
- High CPU or memory usage

**Solutions:**

```powershell
# Check system resources
.\utilities\common-functions.ps1 -CheckSystemResources

# Run with performance monitoring
.\Main-Menu.ps1 -MonitorPerformance

# Optimize execution settings
.\utilities\common-functions.ps1 -OptimizeSettings -Mode Performance

# Run in headless mode for better performance
.\Main-Menu.ps1 -Mode Headless

# Increase timeout values
.\utilities\config-loader.ps1 -UpdateSetting execution.defaultTimeout 60000
```

#### Issue: Memory leaks during automated operations

**Symptoms:**

- Increasing memory usage over time
- System becoming unresponsive
- Out of memory errors

**Solutions:**

```powershell
# Enable garbage collection
.\Run-All.ps1 -EnableGC

# Run scripts in smaller groups
.\Main-Menu.ps1 -GroupSize 10

# Monitor memory usage
.\utilities\common-functions.ps1 -MonitorMemory

# Restart browser between scripts
.\utilities\browser-automation.ps1 -RestartBetweenScripts
```

#### Issue: Database connection issues

**Symptoms:**

- "Connection timeout" errors
- "Too many connections" errors
- Database query failures

**Solutions:**

```powershell
# Test database connectivity
.\utilities\api-helpers.ps1 -TestDatabase -Environment testing

# Check connection pool settings
.\utilities\config-loader.ps1 -CheckDatabaseConfig -Environment testing

# Reset database connections
.\utilities\api-helpers.ps1 -ResetDatabaseConnections

# Test with different database server
.\utilities\api-helpers.ps1 -TestDatabase -Environment testing -Server backup
```

### Configuration and Setup Issues

#### Issue: Configuration files not loading

**Symptoms:**

- "Configuration file not found" errors
- Invalid JSON format errors
- Missing required settings errors

**Solutions:**

```powershell
# Validate configuration files
.\utilities\config-loader.ps1 -ValidateAll

# Check JSON syntax
.\utilities\config-loader.ps1 -ValidateJSON -FilePath "config/environments.json"

# Restore from backup
.\utilities\config-loader.ps1 -RestoreConfig -BackupPath ".\backups\config-backup-latest"

# Reset to default configuration
.\utilities\config-loader.ps1 -ResetToDefaults
```

#### Issue: Credential decryption failures

**Symptoms:**

- "Failed to decrypt credentials" errors
- Invalid encryption key errors
- Corrupted credential file errors

**Solutions:**

```powershell
# Test credential decryption
.\utilities\config-loader.ps1 -TestCredentials -Environment testing

# Regenerate encryption keys
.\utilities\config-loader.ps1 -RegenerateKeys

# Re-encrypt credentials
.\utilities\config-loader.ps1 -ReencryptCredentials

# Restore credentials from backup
.\utilities\config-loader.ps1 -RestoreCredentials -BackupPath ".\backups\credentials-backup-latest"
```

#### Issue: Environment-specific settings not applied

**Symptoms:**

- Wrong base URL being used
- Incorrect database connections
- Environment variables not set

**Solutions:**

```powershell
# Check current environment
.\utilities\config-loader.ps1 -ShowCurrentEnvironment

# Set environment explicitly
$env:ICTSERVE_ENVIRONMENT = "testing"

# Validate environment configuration
.\utilities\config-loader.ps1 -ValidateEnvironment testing

# Reload environment settings
.\utilities\config-loader.ps1 -ReloadEnvironment testing
```

### Test Data and State Issues

#### Issue: Test data corruption or inconsistency

**Symptoms:**

- Unexpected test failures
- Data validation errors
- Inconsistent test results

**Solutions:**

```powershell
# Reset test data
.\utilities\data-generators.ps1 -ResetTestData -Environment testing

# Generate fresh test data
.\utilities\data-generators.ps1 -GenerateTestData -Environment testing

# Validate test data integrity
.\utilities\data-generators.ps1 -ValidateTestData -Environment testing

# Clean up orphaned data
.\utilities\data-generators.ps1 -CleanupOrphanedData -Environment testing
```

#### Issue: Database state conflicts

**Symptoms:**

- Foreign key constraint errors
- Duplicate key errors
- Transaction rollback errors

**Solutions:**

```powershell
# Check database state
.\utilities\api-helpers.ps1 -CheckDatabaseState -Environment testing

# Reset database to clean state
.\utilities\data-generators.ps1 -ResetDatabase -Environment testing

# Run database migrations
.\utilities\api-helpers.ps1 -RunMigrations -Environment testing

# Seed database with test data
.\utilities\data-generators.ps1 -SeedDatabase -Environment testing
```

### Reporting and Logging Issues

#### Issue: Reports not generating correctly

**Symptoms:**

- Empty or incomplete reports
- Report generation errors
- Missing screenshots or logs

**Solutions:**

```powershell
# Test report generation
.\utilities\reporting-utilities.ps1 -TestReportGeneration

# Check report permissions
.\utilities\reporting-utilities.ps1 -CheckPermissions

# Generate report manually
.\utilities\reporting-utilities.ps1 -GenerateReport -Type HTML -OutputPath ".\reports\manual-report.html"

# Clear report cache
.\utilities\reporting-utilities.ps1 -ClearCache
```

#### Issue: Log files not being created

**Symptoms:**

- Missing log files
- Empty log files
- Log permission errors

**Solutions:**

```powershell
# Check logging configuration
.\utilities\config-loader.ps1 -CheckLoggingConfig

# Test log file creation
.\utilities\reporting-utilities.ps1 -TestLogging

# Fix log file permissions
.\utilities\reporting-utilities.ps1 -FixLogPermissions

# Reset logging configuration
.\utilities\config-loader.ps1 -ResetLoggingConfig
```

## Diagnostic Commands

### System Health Check

```powershell
# Comprehensive system health check
.\utilities\common-functions.ps1 -HealthCheck -Comprehensive

# Quick health check
.\utilities\common-functions.ps1 -HealthCheck -Quick

# Environment-specific health check
.\utilities\common-functions.ps1 -HealthCheck -Environment testing
```

### Connectivity Testing

```powershell
# Test all connections
.\utilities\api-helpers.ps1 -TestAllConnections -Environment testing

# Test specific service
.\utilities\api-helpers.ps1 -TestService -Service "database" -Environment testing

# Network diagnostics
.\utilities\common-functions.ps1 -NetworkDiagnostics -Target "test.ictserve.motac.gov.my"
```

### Configuration Validation

```powershell
# Validate all configurations
.\utilities\config-loader.ps1 -ValidateAll -Environment testing

# Check specific configuration
.\utilities\config-loader.ps1 -ValidateConfig browser-settings

# Generate configuration report
.\utilities\config-loader.ps1 -GenerateConfigReport -OutputPath ".\reports\config-report.html"
```

### Performance Analysis

```powershell
# Analyze script performance
.\utilities\reporting-utilities.ps1 -AnalyzePerformance -Period "7days"

# Check resource usage
.\utilities\common-functions.ps1 -CheckResourceUsage

# Performance benchmarking
.\utilities\common-functions.ps1 -BenchmarkPerformance -Suite "guest-workflows"
```

## Debug Mode and Verbose Logging

### Enable Debug Mode

```powershell
# Run script with debug output
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Debug

# Enable verbose logging
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Verbose

# Trace mode for detailed execution
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Trace
```

### Log Analysis

```powershell
# View recent logs
.\utilities\reporting-utilities.ps1 -ViewLogs -Recent 1hour

# Search logs for errors
.\utilities\reporting-utilities.ps1 -SearchLogs -Pattern "error|exception|failed"

# Analyze log patterns
.\utilities\reporting-utilities.ps1 -AnalyzeLogs -Pattern "authentication"

# Export logs for analysis
.\utilities\reporting-utilities.ps1 -ExportLogs -Format CSV -DateRange "2024-01-01,2024-01-31"
```

## Recovery Procedures

### Configuration Recovery

```powershell
# Restore configuration from backup
.\utilities\config-loader.ps1 -RestoreConfig -BackupPath ".\backups\config-backup-latest"

# Reset to factory defaults
.\utilities\config-loader.ps1 -FactoryReset -Confirm

# Rebuild configuration from template
.\utilities\config-loader.ps1 -RebuildConfig -Template "testing"
```

### Data Recovery

```powershell
# Restore test data from backup
.\utilities\data-generators.ps1 -RestoreTestData -BackupPath ".\backups\testdata-backup-latest"

# Regenerate test data
.\utilities\data-generators.ps1 -RegenerateTestData -Environment testing

# Import test data from file
.\utilities\data-generators.ps1 -ImportTestData -FilePath ".\data\test-data-export.json"
```

### Service Recovery

```powershell
# Restart all services
.\utilities\common-functions.ps1 -RestartServices

# Reset browser automation
.\utilities\browser-automation.ps1 -Reset

# Reinitialize AI services
.\utilities\api-helpers.ps1 -ReinitializeAI
```

## Prevention and Best Practices

### Regular Maintenance

1. **Daily Tasks:**
   - Check system health
   - Review error logs
   - Validate critical configurations

2. **Weekly Tasks:**
   - Update browser drivers
   - Backup configurations
   - Clean up old logs and reports

3. **Monthly Tasks:**
   - Rotate encryption keys
   - Update test data
   - Performance analysis and optimization

### Monitoring and Alerting

```powershell
# Set up monitoring
.\utilities\common-functions.ps1 -SetupMonitoring

# Configure alerts
.\utilities\common-functions.ps1 -ConfigureAlerts -Email "admin@motac.gov.my"

# Test alert system
.\utilities\common-functions.ps1 -TestAlerts
```

### Documentation and Knowledge Sharing

1. **Document Issues:** Keep a record of issues and their solutions
2. **Share Knowledge:** Share troubleshooting experiences with the team
3. **Update Procedures:** Update troubleshooting procedures based on new issues
4. **Training:** Provide troubleshooting training to new team members

## Getting Help

### Internal Support

1. **Development Team:** Contact the ICTServe development team for system-specific issues
2. **IT Support:** Contact IT support for infrastructure and network issues
3. **Documentation:** Refer to the comprehensive documentation in the docs/ directory

### External Resources

1. **Selenium Documentation:** For WebDriver-related issues
2. **PowerShell Documentation:** For PowerShell scripting issues
3. **Browser Documentation:** For browser-specific issues
4. **AI Service Documentation:** For Ollama and AWS Bedrock issues

### Emergency Contacts

- **System Administrator:** <admin@motac.gov.my>
- **Development Team Lead:** <dev-lead@motac.gov.my>
- **IT Support:** <it-support@motac.gov.my>
- **Emergency Hotline:** +60-3-XXXX-XXXX (24/7)

---

*This troubleshooting guide covers the most common issues encountered with the ICTServe Comprehensive Automation Suite. For issues not covered in this guide, please contact the development team or refer to the detailed documentation.*
