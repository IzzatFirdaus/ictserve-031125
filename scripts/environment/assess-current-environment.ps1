# ICTServe Current Environment Assessment Script
# Purpose: Document current environment configuration and issues
# Requirements: 8.1, 8.2, 8.4

param(
    [string]$OutputDir = "storage/backups/environment-assessment",
    [switch]$Verbose
)

$ErrorActionPreference = "Continue"
$timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
$assessmentFile = "$OutputDir/assessment_$timestamp.md"

# Create output directory
if (-not (Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir -Force | Out-Null
}

Write-Host "=== ICTServe Environment Assessment ===" -ForegroundColor Cyan
Write-Host "Timestamp: $timestamp" -ForegroundColor Gray
Write-Host ""

# Initialize assessment report
$report = @"
# ICTServe Environment Assessment Report
**Date**: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
**Purpose**: Pre-XAMPP migration environment documentation

## Executive Summary
This report documents the current ICTServe v3.6.1 environment configuration before migration to XAMPP.

---

## 1. System Information

"@

# Gather system information
Write-Host "Gathering system information..." -ForegroundColor Yellow
$report += @"
### Operating System
- **OS**: $([System.Environment]::OSVersion.VersionString)
- **Platform**: $([System.Environment]::OSVersion.Platform)
- **Architecture**: $([System.Environment]::Is64BitOperatingSystem)
- **Computer Name**: $env:COMPUTERNAME
- **User**: $env:USERNAME

"@

# Check PHP version
Write-Host "Checking PHP version..." -ForegroundColor Yellow
try {
    $phpVersion = php -v 2>&1 | Select-Object -First 1
    $report += @"
### PHP Configuration
- **Version**: $phpVersion
- **PHP Path**: $(Get-Command php -ErrorAction SilentlyContinue | Select-Object -ExpandProperty Source)

"@
} catch {
    $report += "### PHP Configuration`n- **Status**: PHP not found in PATH`n`n"
}

# Check Composer
Write-Host "Checking Composer..." -ForegroundColor Yellow
try {
    $composerVersion = composer --version 2>&1 | Select-Object -First 1
    $report += @"
### Composer
- **Version**: $composerVersion

"@
} catch {
    $report += "### Composer`n- **Status**: Composer not found`n`n"
}

# Check Node.js and npm
Write-Host "Checking Node.js and npm..." -ForegroundColor Yellow
try {
    $nodeVersion = node --version 2>&1
    $npmVersion = npm --version 2>&1
    $report += @"
### Node.js & npm
- **Node.js Version**: $nodeVersion
- **npm Version**: $npmVersion

"@
} catch {
    $report += "### Node.js & npm`n- **Status**: Not found`n`n"
}

$report += @"
---

## 2. Current Environment Configuration

"@

# Check .env file
Write-Host "Analyzing .env configuration..." -ForegroundColor Yellow
if (Test-Path ".env") {
    $envContent = Get-Content ".env" -Raw
    
    # Extract key configuration values (sanitized)
    $dbConnection = ($envContent | Select-String "DB_CONNECTION=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
    $dbHost = ($envContent | Select-String "DB_HOST=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
    $dbPort = ($envContent | Select-String "DB_PORT=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
    $dbDatabase = ($envContent | Select-String "DB_DATABASE=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
    
    $cacheDriver = ($envContent | Select-String "CACHE_STORE=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
    $sessionDriver = ($envContent | Select-String "SESSION_DRIVER=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
    $queueConnection = ($envContent | Select-String "QUEUE_CONNECTION=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
    
    $redisHost = ($envContent | Select-String "REDIS_HOST=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
    $redisPort = ($envContent | Select-String "REDIS_PORT=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
    
    $report += @"
### Database Configuration
- **Connection**: $dbConnection
- **Host**: $dbHost
- **Port**: $dbPort
- **Database**: $dbDatabase

### Cache & Session Configuration
- **Cache Driver**: $cacheDriver
- **Session Driver**: $sessionDriver
- **Queue Connection**: $queueConnection

### Redis Configuration
- **Host**: $redisHost
- **Port**: $redisPort

"@
} else {
    $report += "### Environment Configuration`n- **Status**: .env file not found`n`n"
}

$report += @"
---

## 3. Service Status Check

"@

# Check MySQL/MariaDB service
Write-Host "Checking database service..." -ForegroundColor Yellow
$mysqlService = Get-Service -Name "*mysql*","*mariadb*" -ErrorAction SilentlyContinue
if ($mysqlService) {
    $report += @"
### MySQL/MariaDB Service
- **Name**: $($mysqlService.Name)
- **Status**: $($mysqlService.Status)
- **Start Type**: $($mysqlService.StartType)

"@
} else {
    $report += "### MySQL/MariaDB Service`n- **Status**: No MySQL/MariaDB service found`n`n"
}

# Check Redis service
Write-Host "Checking Redis service..." -ForegroundColor Yellow
$redisService = Get-Service -Name "*redis*" -ErrorAction SilentlyContinue
if ($redisService) {
    $report += @"
### Redis Service
- **Name**: $($redisService.Name)
- **Status**: $($redisService.Status)
- **Start Type**: $($redisService.StartType)

"@
} else {
    $report += "### Redis Service`n- **Status**: No Redis service found (may be running in WSL or Docker)`n`n"
}

# Check Apache service
Write-Host "Checking Apache service..." -ForegroundColor Yellow
$apacheService = Get-Service -Name "*apache*","*httpd*" -ErrorAction SilentlyContinue
if ($apacheService) {
    $report += @"
### Apache Service
- **Name**: $($apacheService.Name)
- **Status**: $($apacheService.Status)
- **Start Type**: $($apacheService.StartType)

"@
} else {
    $report += "### Apache Service`n- **Status**: No Apache service found`n`n"
}

$report += @"
---

## 4. Docker Environment Check

"@

# Check Docker
Write-Host "Checking Docker environment..." -ForegroundColor Yellow
try {
    $dockerVersion = docker --version 2>&1
    $dockerComposeVersion = docker-compose --version 2>&1
    
    $report += @"
### Docker Installation
- **Docker Version**: $dockerVersion
- **Docker Compose Version**: $dockerComposeVersion

"@
    
    # Check running containers
    try {
        $containers = docker ps --format "{{.Names}}: {{.Status}}" 2>&1
        if ($containers) {
            $report += @"
### Running Containers
``````
$containers
``````

"@
        }
    } catch {
        $report += "### Running Containers`n- **Status**: Unable to list containers`n`n"
    }
} catch {
    $report += "### Docker Installation`n- **Status**: Docker not found or not running`n`n"
}

$report += @"
---

## 5. Known Issues and Errors

"@

# Check Laravel logs for recent errors
Write-Host "Analyzing Laravel logs..." -ForegroundColor Yellow
if (Test-Path "storage/logs/laravel.log") {
    $logContent = Get-Content "storage/logs/laravel.log" -Tail 100 -ErrorAction SilentlyContinue
    $errors = $logContent | Select-String "ERROR|CRITICAL|EMERGENCY" | Select-Object -First 10
    
    if ($errors) {
        $report += @"
### Recent Laravel Errors (Last 10)
``````
$($errors -join "`n")
``````

"@
    } else {
        $report += "### Recent Laravel Errors`n- **Status**: No recent errors found in logs`n`n"
    }
} else {
    $report += "### Recent Laravel Errors`n- **Status**: Log file not found`n`n"
}

$report += @"
---

## 6. Performance Baselines

"@

# Check database size
Write-Host "Gathering performance metrics..." -ForegroundColor Yellow
$report += @"
### Database Metrics
- **Assessment**: Run database-query tool to get table sizes and row counts

### Application Metrics
- **Laravel Version**: Check composer.json for exact version
- **PHP Memory Limit**: $(php -r "echo ini_get('memory_limit');" 2>&1)
- **Max Execution Time**: $(php -r "echo ini_get('max_execution_time');" 2>&1)

"@

$report += @"
---

## 7. Recommendations

### Critical Actions Required
1. **Database Backup**: Create full mysqldump before migration
2. **Redis Data Export**: Export all Redis keys and data
3. **Configuration Backup**: Backup all .env and config files
4. **Test Environment**: Verify rollback procedures work

### Migration Readiness
- [ ] Database backup completed
- [ ] Redis data exported
- [ ] Configuration files backed up
- [ ] Rollback procedures documented
- [ ] XAMPP installation ready

---

## 8. Next Steps

1. Run database backup script: ``scripts/environment/backup-database.ps1``
2. Run Redis export script: ``scripts/environment/export-redis-data.ps1``
3. Run configuration backup script: ``scripts/environment/backup-configuration.ps1``
4. Review this assessment report
5. Proceed with XAMPP installation

---

**Report Generated**: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
**Script Version**: 1.0.0
**Requirements**: 8.1, 8.2, 8.4
"@

# Save report
$report | Out-File -FilePath $assessmentFile -Encoding UTF8
Write-Host ""
Write-Host "Assessment complete!" -ForegroundColor Green
Write-Host "Report saved to: $assessmentFile" -ForegroundColor Cyan
Write-Host ""

# Display summary
Write-Host "=== Assessment Summary ===" -ForegroundColor Cyan
Write-Host "- System information documented" -ForegroundColor Gray
Write-Host "- Environment configuration analyzed" -ForegroundColor Gray
Write-Host "- Service status checked" -ForegroundColor Gray
Write-Host "- Known issues identified" -ForegroundColor Gray
Write-Host "- Performance baselines recorded" -ForegroundColor Gray
Write-Host ""
Write-Host "Next: Run backup scripts to complete environment preservation" -ForegroundColor Yellow
