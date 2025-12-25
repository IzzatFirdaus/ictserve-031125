# ICTServe Environment Rollback Procedures Creation Script
# Purpose: Create environment rollback procedures
# Requirements: 8.1, 8.2, 8.4

param(
    [string]$OutputDir = "storage/backups/rollback",
    [switch]$Verbose
)

$ErrorActionPreference = "Continue"
$timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"

# Create output directory
if (-not (Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir -Force | Out-Null
}

Write-Host "=== ICTServe Rollback Procedures Creation ===" -ForegroundColor Cyan
Write-Host "Timestamp: $timestamp" -ForegroundColor Gray
Write-Host ""

# Create comprehensive rollback documentation
$rollbackDoc = @"
# ICTServe Environment Rollback Procedures

**Created**: $timestamp  
**Purpose**: Emergency rollback from XAMPP to previous environment  
**Requirements**: 8.1, 8.2, 8.4

---

## Overview

This document provides step-by-step procedures to rollback the ICTServe system from XAMPP environment to the previous configuration in case of migration issues.

**CRITICAL**: Only use these procedures if the XAMPP migration fails or causes critical issues.

---

## Pre-Rollback Checklist

Before initiating rollback, verify:

- [ ] XAMPP migration has failed or is causing critical issues
- [ ] All backup files are available and verified
- [ ] Current XAMPP environment is documented for future reference
- [ ] Stakeholders are notified of rollback decision
- [ ] Maintenance window is scheduled

---

## Rollback Procedures

### Phase 1: Stop XAMPP Services

1. **Stop XAMPP MySQL**:
   ``````powershell
   # Stop XAMPP MySQL service
   Stop-Process -Name "mysqld" -Force -ErrorAction SilentlyContinue
   
   # Or use XAMPP Control Panel
   # C:\xampp\xampp-control.exe
   ``````

2. **Stop XAMPP Apache**:
   ``````powershell
   # Stop XAMPP Apache service
   Stop-Process -Name "httpd" -Force -ErrorAction SilentlyContinue
   ``````

3. **Verify Services Stopped**:
   ``````powershell
   Get-Process -Name "mysqld","httpd" -ErrorAction SilentlyContinue
   # Should return no processes
   ``````

### Phase 2: Restore Database

1. **Start Previous Database Service**:
   ``````powershell
   # Start previous MySQL/MariaDB service
   Start-Service -Name "MySQL*" -ErrorAction SilentlyContinue
   # OR
   Start-Service -Name "MariaDB*" -ErrorAction SilentlyContinue
   ``````

2. **Restore Database from Backup**:
   ``````powershell
   # Navigate to backup directory
   cd storage/backups/database
   
   # Find latest backup
   `$latestBackup = Get-ChildItem -Filter "*ictserve_backup_*.sql*" | Sort-Object LastWriteTime -Descending | Select-Object -First 1
   
   # Restore database
   if (`$latestBackup.Extension -eq ".zip") {
       Expand-Archive -Path `$latestBackup.FullName -DestinationPath "temp_restore"
       `$sqlFile = Get-ChildItem -Path "temp_restore" -Filter "*.sql" | Select-Object -First 1
       mysql -u root -p ictserve < `$sqlFile.FullName
       Remove-Item -Path "temp_restore" -Recurse -Force
   } else {
       mysql -u root -p ictserve < `$latestBackup.FullName
   }
   ``````

3. **Verify Database Restoration**:
   ``````powershell
   # Test database connection
   php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully';"
   ``````

### Phase 3: Restore Redis Configuration

1. **Start Previous Redis Service**:
   ``````powershell
   # If Redis was running as Windows service
   Start-Service -Name "Redis*" -ErrorAction SilentlyContinue
   
   # If Redis was running in WSL
   wsl sudo service redis-server start
   ``````

2. **Restore Redis Data**:
   ``````powershell
   # Navigate to Redis backup directory
   cd storage/backups/redis
   
   # Find latest Redis backup
   `$latestRedisBackup = Get-ChildItem -Filter "*redis_data_*.txt" | Sort-Object LastWriteTime -Descending | Select-Object -First 1
   
   # Run restore script
   if (`$latestRedisBackup) {
       `$restoreScript = Get-ChildItem -Filter "restore_redis_*.ps1" | Sort-Object LastWriteTime -Descending | Select-Object -First 1
       if (`$restoreScript) {
           & `$restoreScript.FullName
       }
   }
   ``````

3. **Verify Redis Restoration**:
   ``````powershell
   # Test Redis connection
   redis-cli ping
   # Should return "PONG"
   ``````

### Phase 4: Restore Laravel Configuration

1. **Restore Configuration Files**:
   ``````powershell
   # Navigate to configuration backup directory
   cd storage/backups/configuration
   
   # Find latest configuration backup
   `$latestConfigBackup = Get-ChildItem -Filter "*ictserve_config_backup_*.zip" | Sort-Object LastWriteTime -Descending | Select-Object -First 1
   
   # Run restore script
   if (`$latestConfigBackup) {
       `$restoreScript = Get-ChildItem -Filter "restore_config_*.ps1" | Sort-Object LastWriteTime -Descending | Select-Object -First 1
       if (`$restoreScript) {
           & `$restoreScript.FullName -Force
       }
   }
   ``````

2. **Update Environment Configuration**:
   ``````powershell
   # Restore original .env settings
   # Ensure database and Redis connections point to original services
   
   # Example .env updates:
   # DB_HOST=original_host
   # DB_PORT=original_port
   # REDIS_HOST=original_redis_host
   # REDIS_PORT=original_redis_port
   ``````

3. **Clear Laravel Caches**:
   ``````powershell
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ``````

### Phase 5: Restart Services

1. **Start Laravel Application**:
   ``````powershell
   # Start Laravel development server
   php artisan serve --host=127.0.0.1 --port=8000
   ``````

2. **Start Background Services**:
   ``````powershell
   # Start Laravel Horizon (if used)
   php artisan horizon &
   
   # Start Laravel Reverb (if used)
   php artisan reverb:start &
   ``````

### Phase 6: Verification and Testing

1. **System Health Check**:
   ``````powershell
   # Run system health checks
   php artisan health:check
   
   # Test database connectivity
   php artisan tinker --execute="User::count(); echo 'Database working';"
   
   # Test Redis connectivity
   php artisan tinker --execute="Cache::put('test', 'value'); echo Cache::get('test');"
   ``````

2. **Application Testing**:
   ``````powershell
   # Run test suite
   php artisan test
   
   # Test critical application functions
   # - User authentication
   # - Helpdesk ticket creation
   # - Asset loan application
   # - Admin panel access
   ``````

3. **Performance Verification**:
   ``````powershell
   # Run performance comparison
   cd storage/backups/performance
   `$compareScript = Get-ChildItem -Filter "compare-performance.ps1" | Select-Object -First 1
   if (`$compareScript) {
       & `$compareScript.FullName
   }
   ``````

---

## Emergency Rollback Script

For rapid rollback execution, use the automated script:

``````powershell
.\storage\backups\rollback\emergency-rollback.ps1
``````

---

## Post-Rollback Actions

After successful rollback:

1. **Document Issues**:
   - Record what caused the rollback
   - Document any data loss or issues
   - Note lessons learned for future migration

2. **Notify Stakeholders**:
   - Inform users that system is restored
   - Provide timeline for next migration attempt
   - Document any temporary limitations

3. **Plan Next Steps**:
   - Analyze migration failure causes
   - Update migration procedures
   - Schedule next migration attempt

---

## Rollback Verification Checklist

- [ ] All services are running on original configuration
- [ ] Database is accessible and contains expected data
- [ ] Redis is working with cached data restored
- [ ] Laravel application is responding correctly
- [ ] All tests pass
- [ ] Performance is within acceptable ranges
- [ ] Users can access all system functions
- [ ] Admin panel is accessible
- [ ] Background jobs are processing
- [ ] Real-time features are working

---

## Support Contacts

**Technical Issues**:
- ICT Support: ict-support@motac.gov.my
- System Administrator: admin@motac.gov.my

**Business Issues**:
- BPM MOTAC: bpm@motac.gov.my

---

**Document Version**: 1.0  
**Last Updated**: $timestamp  
**Requirements**: 8.1, 8.2, 8.4
"@

$rollbackDoc | Out-File -FilePath "$OutputDir/rollback-procedures.md" -Encoding UTF8

# Create emergency rollback script
$emergencyScript = @"
# Emergency Rollback Script
# Purpose: Automated rollback from XAMPP to previous environment
# Generated: $timestamp

param(
    [switch]`$Force = `$false,
    [switch]`$SkipConfirmation = `$false
)

`$ErrorActionPreference = "Continue"

Write-Host "=== ICTServe Emergency Rollback ===" -ForegroundColor Red
Write-Host "This will rollback from XAMPP to the previous environment" -ForegroundColor Yellow
Write-Host ""

if (-not `$SkipConfirmation -and -not `$Force) {
    `$confirmation = Read-Host "Are you sure you want to proceed with rollback? (yes/no)"
    if (`$confirmation -ne "yes") {
        Write-Host "Rollback cancelled." -ForegroundColor Yellow
        exit 0
    }
}

Write-Host "Starting emergency rollback..." -ForegroundColor Red
Write-Host ""

# Phase 1: Stop XAMPP Services
Write-Host "Phase 1: Stopping XAMPP services..." -ForegroundColor Cyan
try {
    Stop-Process -Name "mysqld" -Force -ErrorAction SilentlyContinue
    Stop-Process -Name "httpd" -Force -ErrorAction SilentlyContinue
    Write-Host "  ✓ XAMPP services stopped" -ForegroundColor Green
} catch {
    Write-Warning "  ⚠ Error stopping XAMPP services: `$(`$_.Exception.Message)"
}

# Phase 2: Start Previous Services
Write-Host "Phase 2: Starting previous services..." -ForegroundColor Cyan
try {
    # Try to start MySQL service
    `$mysqlService = Get-Service -Name "*mysql*","*mariadb*" -ErrorAction SilentlyContinue | Select-Object -First 1
    if (`$mysqlService) {
        Start-Service -Name `$mysqlService.Name -ErrorAction SilentlyContinue
        Write-Host "  ✓ Database service started: `$(`$mysqlService.Name)" -ForegroundColor Green
    } else {
        Write-Warning "  ⚠ No MySQL/MariaDB service found"
    }
    
    # Try to start Redis
    `$redisService = Get-Service -Name "*redis*" -ErrorAction SilentlyContinue | Select-Object -First 1
    if (`$redisService) {
        Start-Service -Name `$redisService.Name -ErrorAction SilentlyContinue
        Write-Host "  ✓ Redis service started: `$(`$redisService.Name)" -ForegroundColor Green
    } else {
        # Try WSL Redis
        try {
            wsl sudo service redis-server start 2>&1 | Out-Null
            Write-Host "  ✓ WSL Redis started" -ForegroundColor Green
        } catch {
            Write-Warning "  ⚠ Could not start Redis service"
        }
    }
} catch {
    Write-Warning "  ⚠ Error starting services: `$(`$_.Exception.Message)"
}

# Phase 3: Restore Configuration
Write-Host "Phase 3: Restoring configuration..." -ForegroundColor Cyan
try {
    # Find and restore latest configuration backup
    `$configBackupDir = "storage/backups/configuration"
    if (Test-Path `$configBackupDir) {
        `$latestConfigBackup = Get-ChildItem -Path `$configBackupDir -Filter "*ictserve_config_backup_*.zip" | Sort-Object LastWriteTime -Descending | Select-Object -First 1
        if (`$latestConfigBackup) {
            Expand-Archive -Path `$latestConfigBackup.FullName -DestinationPath "." -Force
            Write-Host "  ✓ Configuration restored from: `$(`$latestConfigBackup.Name)" -ForegroundColor Green
        }
    }
} catch {
    Write-Warning "  ⚠ Error restoring configuration: `$(`$_.Exception.Message)"
}

# Phase 4: Clear Laravel Caches
Write-Host "Phase 4: Clearing Laravel caches..." -ForegroundColor Cyan
try {
    php artisan config:clear 2>&1 | Out-Null
    php artisan cache:clear 2>&1 | Out-Null
    php artisan route:clear 2>&1 | Out-Null
    php artisan view:clear 2>&1 | Out-Null
    Write-Host "  ✓ Laravel caches cleared" -ForegroundColor Green
} catch {
    Write-Warning "  ⚠ Error clearing caches: `$(`$_.Exception.Message)"
}

# Phase 5: Verification
Write-Host "Phase 5: Verifying rollback..." -ForegroundColor Cyan
try {
    # Test database connection
    `$dbTest = php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>&1
    if (`$dbTest -match "OK") {
        Write-Host "  ✓ Database connection verified" -ForegroundColor Green
    } else {
        Write-Warning "  ⚠ Database connection test failed"
    }
    
    # Test Redis connection
    `$redisTest = redis-cli ping 2>&1
    if (`$redisTest -eq "PONG") {
        Write-Host "  ✓ Redis connection verified" -ForegroundColor Green
    } else {
        # Try WSL Redis
        `$wslRedisTest = wsl redis-cli ping 2>&1
        if (`$wslRedisTest -eq "PONG") {
            Write-Host "  ✓ WSL Redis connection verified" -ForegroundColor Green
        } else {
            Write-Warning "  ⚠ Redis connection test failed"
        }
    }
} catch {
    Write-Warning "  ⚠ Error during verification: `$(`$_.Exception.Message)"
}

Write-Host ""
Write-Host "=== Rollback Summary ===" -ForegroundColor Cyan
Write-Host "Emergency rollback completed." -ForegroundColor Yellow
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Test application functionality thoroughly" -ForegroundColor Gray
Write-Host "2. Run full test suite: php artisan test" -ForegroundColor Gray
Write-Host "3. Verify all services are working correctly" -ForegroundColor Gray
Write-Host "4. Document any issues encountered" -ForegroundColor Gray
Write-Host "5. Plan next migration attempt" -ForegroundColor Gray
Write-Host ""
Write-Host "For detailed rollback procedures, see:" -ForegroundColor Yellow
Write-Host "  storage/backups/rollback/rollback-procedures.md" -ForegroundColor Cyan
"@

$emergencyScript | Out-File -FilePath "$OutputDir/emergency-rollback.ps1" -Encoding UTF8

# Create rollback verification script
$verificationScript = @"
# Rollback Verification Script
# Purpose: Verify successful rollback to previous environment
# Generated: $timestamp

Write-Host "=== Rollback Verification ===" -ForegroundColor Cyan
Write-Host "Verifying system after rollback from XAMPP" -ForegroundColor Gray
Write-Host ""

`$allTestsPassed = `$true

# Test 1: Database Connection
Write-Host "Testing database connection..." -ForegroundColor Yellow
try {
    `$dbTest = php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connected';" 2>&1
    if (`$dbTest -match "Connected") {
        Write-Host "  ✓ Database connection successful" -ForegroundColor Green
    } else {
        Write-Host "  ✗ Database connection failed" -ForegroundColor Red
        `$allTestsPassed = `$false
    }
} catch {
    Write-Host "  ✗ Database test error: `$(`$_.Exception.Message)" -ForegroundColor Red
    `$allTestsPassed = `$false
}

# Test 2: Redis Connection
Write-Host "Testing Redis connection..." -ForegroundColor Yellow
try {
    `$redisTest = redis-cli ping 2>&1
    if (`$redisTest -eq "PONG") {
        Write-Host "  ✓ Redis connection successful" -ForegroundColor Green
    } else {
        # Try WSL Redis
        `$wslRedisTest = wsl redis-cli ping 2>&1
        if (`$wslRedisTest -eq "PONG") {
            Write-Host "  ✓ WSL Redis connection successful" -ForegroundColor Green
        } else {
            Write-Host "  ✗ Redis connection failed" -ForegroundColor Red
            `$allTestsPassed = `$false
        }
    }
} catch {
    Write-Host "  ✗ Redis test error: `$(`$_.Exception.Message)" -ForegroundColor Red
    `$allTestsPassed = `$false
}

# Test 3: Laravel Application
Write-Host "Testing Laravel application..." -ForegroundColor Yellow
try {
    `$artisanTest = php artisan route:list --json 2>&1
    if (`$artisanTest -match "^\[") {
        Write-Host "  ✓ Laravel application responding" -ForegroundColor Green
    } else {
        Write-Host "  ✗ Laravel application test failed" -ForegroundColor Red
        `$allTestsPassed = `$false
    }
} catch {
    Write-Host "  ✗ Laravel test error: `$(`$_.Exception.Message)" -ForegroundColor Red
    `$allTestsPassed = `$false
}

# Test 4: Cache Functionality
Write-Host "Testing cache functionality..." -ForegroundColor Yellow
try {
    `$cacheTest = php artisan tinker --execute="Cache::put('rollback_test', 'success'); echo Cache::get('rollback_test');" 2>&1
    if (`$cacheTest -match "success") {
        Write-Host "  ✓ Cache functionality working" -ForegroundColor Green
    } else {
        Write-Host "  ✗ Cache functionality failed" -ForegroundColor Red
        `$allTestsPassed = `$false
    }
} catch {
    Write-Host "  ✗ Cache test error: `$(`$_.Exception.Message)" -ForegroundColor Red
    `$allTestsPassed = `$false
}

# Test 5: Service Status
Write-Host "Checking service status..." -ForegroundColor Yellow
`$mysqlService = Get-Service -Name "*mysql*","*mariadb*" -ErrorAction SilentlyContinue | Where-Object { `$_.Status -eq "Running" }
if (`$mysqlService) {
    Write-Host "  ✓ Database service running: `$(`$mysqlService.Name)" -ForegroundColor Green
} else {
    Write-Host "  ⚠ No running database service found" -ForegroundColor Yellow
}

`$redisService = Get-Service -Name "*redis*" -ErrorAction SilentlyContinue | Where-Object { `$_.Status -eq "Running" }
if (`$redisService) {
    Write-Host "  ✓ Redis service running: `$(`$redisService.Name)" -ForegroundColor Green
} else {
    Write-Host "  ℹ Redis may be running in WSL" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "=== Verification Summary ===" -ForegroundColor Cyan
if (`$allTestsPassed) {
    Write-Host "✓ All verification tests passed!" -ForegroundColor Green
    Write-Host "System rollback appears successful." -ForegroundColor Green
} else {
    Write-Host "✗ Some verification tests failed!" -ForegroundColor Red
    Write-Host "Manual investigation required." -ForegroundColor Red
}

Write-Host ""
Write-Host "Recommended next steps:" -ForegroundColor Yellow
Write-Host "1. Run full test suite: php artisan test" -ForegroundColor Gray
Write-Host "2. Test user-facing functionality" -ForegroundColor Gray
Write-Host "3. Monitor system performance" -ForegroundColor Gray
Write-Host "4. Document any remaining issues" -ForegroundColor Gray
"@

$verificationScript | Out-File -FilePath "$OutputDir/verify-rollback.ps1" -Encoding UTF8

# Create rollback checklist
$checklistDoc = @"
# ICTServe Rollback Checklist

**Date**: _______________  
**Performed By**: _______________  
**Reason for Rollback**: _______________

---

## Pre-Rollback Preparation

- [ ] Backup current XAMPP configuration for analysis
- [ ] Notify stakeholders of rollback decision
- [ ] Schedule maintenance window
- [ ] Verify backup files are available
- [ ] Document current issues causing rollback

---

## Rollback Execution

### Phase 1: Stop XAMPP Services
- [ ] Stop XAMPP MySQL service
- [ ] Stop XAMPP Apache service
- [ ] Verify no XAMPP processes running

### Phase 2: Restore Previous Services
- [ ] Start previous MySQL/MariaDB service
- [ ] Start previous Redis service (Windows or WSL)
- [ ] Verify services are running correctly

### Phase 3: Restore Data
- [ ] Restore database from latest backup
- [ ] Restore Redis data from backup
- [ ] Verify data integrity

### Phase 4: Restore Configuration
- [ ] Restore Laravel configuration files
- [ ] Update .env file for previous environment
- [ ] Clear Laravel caches

### Phase 5: Restart Application
- [ ] Start Laravel application server
- [ ] Start background services (Horizon, Reverb)
- [ ] Verify application is accessible

---

## Post-Rollback Verification

### System Tests
- [ ] Database connection test passed
- [ ] Redis connection test passed
- [ ] Laravel application responding
- [ ] Cache functionality working
- [ ] Session management working

### Application Tests
- [ ] User authentication working
- [ ] Helpdesk module functional
- [ ] Asset loan module functional
- [ ] Admin panel accessible
- [ ] Real-time features working

### Performance Tests
- [ ] Database query performance acceptable
- [ ] Redis response times acceptable
- [ ] Application response times acceptable
- [ ] Resource usage within normal ranges

### Integration Tests
- [ ] Email notifications working
- [ ] File uploads working
- [ ] Background jobs processing
- [ ] API endpoints responding
- [ ] WebSocket connections working

---

## Post-Rollback Actions

### Documentation
- [ ] Document rollback completion time
- [ ] Record any issues encountered during rollback
- [ ] Document current system status
- [ ] Update incident report

### Communication
- [ ] Notify stakeholders of rollback completion
- [ ] Inform users system is restored
- [ ] Provide timeline for next migration attempt
- [ ] Document lessons learned

### Planning
- [ ] Analyze XAMPP migration failure causes
- [ ] Update migration procedures based on lessons learned
- [ ] Schedule post-mortem meeting
- [ ] Plan next migration attempt

---

## Sign-off

**Technical Lead**: _______________  **Date**: _______________  
**System Administrator**: _______________  **Date**: _______________  
**BPM Representative**: _______________  **Date**: _______________

---

**Checklist Version**: 1.0  
**Created**: $timestamp  
**Requirements**: 8.1, 8.2, 8.4
"@

$checklistDoc | Out-File -FilePath "$OutputDir/rollback-checklist.md" -Encoding UTF8

# Display summary
Write-Host ""
Write-Host "=== Rollback Procedures Summary ===" -ForegroundColor Cyan
Write-Host "Documentation created for emergency rollback scenarios" -ForegroundColor Gray
Write-Host ""
Write-Host "Files Created:" -ForegroundColor Gray
Write-Host "  - Rollback procedures: rollback-procedures.md" -ForegroundColor Gray
Write-Host "  - Emergency script: emergency-rollback.ps1" -ForegroundColor Gray
Write-Host "  - Verification script: verify-rollback.ps1" -ForegroundColor Gray
Write-Host "  - Rollback checklist: rollback-checklist.md" -ForegroundColor Gray
Write-Host ""
Write-Host "✓ Rollback procedures documentation completed!" -ForegroundColor Green
Write-Host ""
Write-Host "Emergency Rollback Usage:" -ForegroundColor Yellow
Write-Host "  .\$OutputDir\emergency-rollback.ps1" -ForegroundColor Cyan
Write-Host ""
Write-Host "Rollback Verification:" -ForegroundColor Yellow
Write-Host "  .\$OutputDir\verify-rollback.ps1" -ForegroundColor Cyan