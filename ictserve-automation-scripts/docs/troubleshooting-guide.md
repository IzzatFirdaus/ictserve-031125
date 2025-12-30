# ICTServe Automation Suite - Troubleshooting Guide

## Common Issues and Solutions

### 1. PowerShell Execution Policy Issues

**Problem**: Scripts fail to run with "execution policy" errors.

**Solution**:

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### 2. Missing PowerShell Version

**Problem**: Scripts require PowerShell 7.x but older version is installed.

**Solution**:

- Download and install PowerShell 7.x from: <https://github.com/PowerShell/PowerShell/releases>
- Or use winget: `winget install Microsoft.PowerShell`

### 3. Browser Driver Issues

**Problem**: Browser automation fails with WebDriver errors.

**Solution**:

- Ensure Chrome or Edge is installed
- For real browser automation, install Selenium WebDriver
- Current implementation uses mock drivers for testing

### 4. Configuration File Errors

**Problem**: Scripts fail to load configuration files.

**Solution**:

1. Verify config files exist in `config/` directory
2. Check JSON syntax in configuration files
3. Update credentials in `config/credentials.json`

### 5. Permission Errors

**Problem**: Scripts fail to create directories or write files.

**Solution**:

- Run PowerShell as Administrator
- Check folder permissions
- Ensure antivirus isn't blocking script execution

### 6. Network Connectivity Issues

**Problem**: API tests fail with network errors.

**Solution**:

- Check internet connectivity
- Verify firewall settings
- Update environment URLs in `config/environments.json`

### 7. Missing Script Files

**Problem**: Menu options fail because scripts don't exist.

**Solution**:

- The system automatically creates placeholder scripts
- Check the `scripts/` directory structure
- Run individual tests to generate missing files

### 8. Log File Issues

**Problem**: Cannot write to log files or reports directory.

**Solution**:

- Check if `reports/` directory exists
- Verify write permissions
- Clear old log files if disk space is low

### 9. Screenshot Capture Failures

**Problem**: Screenshot functionality doesn't work.

**Solution**:

- Ensure `reports/screenshots/` directory exists
- Check available disk space
- For real screenshots, ensure browser is visible (not headless)

### 10. Test Data Issues

**Problem**: Tests fail due to invalid test data.

**Solution**:

- Update test data in individual script files
- Check email formats and phone numbers
- Verify department and category values match your system

## Performance Optimization

### Slow Test Execution

- Use Headless mode for faster execution
- Reduce screenshot frequency
- Optimize wait times in scripts

### Memory Usage

- Close browser sessions properly
- Clear execution history periodically
- Monitor PowerShell memory usage

## Environment-Specific Issues

### Development Environment

- Use localhost URLs
- Ensure Laravel application is running
- Check database connectivity

### Testing Environment

- Verify test server accessibility
- Update credentials for test environment
- Check SSL certificate validity

### Production Environment

- Use read-only operations only
- Verify production URLs and credentials
- Enable headless mode for CI/CD

## Getting Help

### Log Analysis

1. Check execution logs in `reports/execution-logs/`
2. Review test results in `reports/test-results/`
3. Examine screenshots in `reports/screenshots/`

### Debug Mode
Run scripts with verbose output:

```powershell
$VerbosePreference = "Continue"
.\script-name.ps1 -Mode Visual -Environment testing
```

### Contact Information

- Check project documentation
- Review GitHub issues
- Contact system administrator

## Frequently Asked Questions

### Q: Can I run tests in parallel?
A: Currently, tests run sequentially. Parallel execution will be added in future versions.

### Q: How do I add custom test scenarios?
A: Create new PowerShell scripts following the existing template structure.

### Q: Can I integrate with CI/CD pipelines?
A: Yes, use Headless mode and JSON reporting for automated integration.

### Q: How do I update test credentials?
A: Edit `config/credentials.json` with your environment-specific credentials.

### Q: What browsers are supported?
A: Chrome and Edge are supported. Firefox support will be added in future versions.

## Version History

- v1.0.0: Initial release with mock implementations
- Future: Real browser integration, parallel execution, enhanced reporting

## Support

For additional support:

1. Check the Quick Start Guide (Menu Option 28)
2. Review execution logs and error messages
3. Test individual components using the test suite
4. Contact your system administrator for environment-specific issues
