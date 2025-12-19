# ICTServe Complete Environment Assessment and Backup Script
# Purpose: Master script to run all assessment and backup tasks
# Requirements: 8.1, 8.2, 8.4

param(
    [string]$OutputDir = "storage/backups",
    [switch]$SkipDatabase = $false,
    [switch]$SkipRedis = $false,
    [switch]$SkipConfiguration = $false,
    [switch]$SkipPerformance = $false,
    [switch]$Verbose
)

$ErrorActionPreference = "Continue"
$timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"

Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "           ICTServe Complete Environment Assessment" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Timestamp: $timestamp" -ForegroundColor Gray
Write-Host "Output Directory: $OutputDir" -ForegroundColor Gray
Write-Host ""

# Create main output directory
if (-not (Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir -Force | Out-Null
}

# Initialize summary report
$summary = @{
    timestamp = $timestamp
    tasks_completed = @()
    tasks_failed = @()
    total_duration_minutes = 0
    files_created = @()
    backup_sizes_mb = @{}
}

$startTime = Get-Date

# Task 1: Environment Assessment
Write-Host "=== Task 1: Environment Assessment ===" -ForegroundColor Yellow
try {
    $assessmentStart = Get-Date
    & "scripts/environment/assess-current-environment.ps1" -OutputDir "$OutputDir/environment-assessment" -Verbose:$Verbose
    $assessmentEnd = Get-Date
    $assessmentDuration = ($assessmentEnd - $assessmentStart).TotalMinutes
    
    $summary.tasks_completed += @{
        task = "Environment Assessment"
        duration_minutes = [math]::Round($assessmentDuration, 2)
        status = "Success"
    }
    
    Write-Host "✓ Environment assessment completed ($([math]::Round($assessmentDuration, 1)) minutes)" -ForegroundColor Green
} catch {
    Write-Error "Environment assessment failed: $($_.Exception.Message)"
    $summary.tasks_failed += @{
        task = "Environment Assessment"
        error = $_.Exception.Message
    }
}

Write-Host ""

# Task 2: Database Backup
if (-not $SkipDatabase) {
    Write-Host "=== Task 2: Database Backup ===" -ForegroundColor Yellow
    try {
        $dbBackupStart = Get-Date
        & "scripts/environment/backup-database.ps1" -OutputDir "$OutputDir/database" -Verbose:$Verbose
        $dbBackupEnd = Get-Date
        $dbBackupDuration = ($dbBackupEnd - $dbBackupStart).TotalMinutes
        
        # Calculate backup size
        $dbBackupSize = 0
        if (Test-Path "$OutputDir/database") {
            $dbBackupSize = [math]::Round((Get-ChildItem -Path "$OutputDir/database" -Recurse | Measure-Object -Property Length -Sum).Sum / 1MB, 2)
        }
        
        $summary.tasks_completed += @{
            task = "Database Backup"
            duration_minutes = [math]::Round($dbBackupDuration, 2)
            status = "Success"
        }
        $summary.backup_sizes_mb["database"] = $dbBackupSize
        
        Write-Host "✓ Database backup completed ($([math]::Round($dbBackupDuration, 1)) minutes, $dbBackupSize MB)" -ForegroundColor Green
    } catch {
        Write-Error "Database backup failed: $($_.Exception.Message)"
        $summary.tasks_failed += @{
            task = "Database Backup"
            error = $_.Exception.Message
        }
    }
} else {
    Write-Host "=== Task 2: Database Backup (SKIPPED) ===" -ForegroundColor Gray
}

Write-Host ""

# Task 3: Redis Export
if (-not $SkipRedis) {
    Write-Host "=== Task 3: Redis Data Export ===" -ForegroundColor Yellow
    try {
        $redisExportStart = Get-Date
        & "scripts/environment/export-redis-data.ps1" -OutputDir "$OutputDir/redis" -Verbose:$Verbose
        $redisExportEnd = Get-Date
        $redisExportDuration = ($redisExportEnd - $redisExportStart).TotalMinutes
        
        # Calculate Redis export size
        $redisExportSize = 0
        if (Test-Path "$OutputDir/redis") {
            $redisExportSize = [math]::Round((Get-ChildItem -Path "$OutputDir/redis" -Recurse | Measure-Object -Property Length -Sum).Sum / 1MB, 2)
        }
        
        $summary.tasks_completed += @{
            task = "Redis Data Export"
            duration_minutes = [math]::Round($redisExportDuration, 2)
            status = "Success"
        }
        $summary.backup_sizes_mb["redis"] = $redisExportSize
        
        Write-Host "✓ Redis export completed ($([math]::Round($redisExportDuration, 1)) minutes, $redisExportSize MB)" -ForegroundColor Green
    } catch {
        Write-Error "Redis export failed: $($_.Exception.Message)"
        $summary.tasks_failed += @{
            task = "Redis Data Export"
            error = $_.Exception.Message
        }
    }
} else {
    Write-Host "=== Task 3: Redis Data Export (SKIPPED) ===" -ForegroundColor Gray
}

Write-Host ""

# Task 4: Configuration Backup
if (-not $SkipConfiguration) {
    Write-Host "=== Task 4: Configuration Backup ===" -ForegroundColor Yellow
    try {
        $configBackupStart = Get-Date
        & "scripts/environment/backup-configuration.ps1" -OutputDir "$OutputDir/configuration" -Verbose:$Verbose
        $configBackupEnd = Get-Date
        $configBackupDuration = ($configBackupEnd - $configBackupStart).TotalMinutes
        
        # Calculate configuration backup size
        $configBackupSize = 0
        if (Test-Path "$OutputDir/configuration") {
            $configBackupSize = [math]::Round((Get-ChildItem -Path "$OutputDir/configuration" -Recurse | Measure-Object -Property Length -Sum).Sum / 1MB, 2)
        }
        
        $summary.tasks_completed += @{
            task = "Configuration Backup"
            duration_minutes = [math]::Round($configBackupDuration, 2)
            status = "Success"
        }
        $summary.backup_sizes_mb["configuration"] = $configBackupSize
        
        Write-Host "✓ Configuration backup completed ($([math]::Round($configBackupDuration, 1)) minutes, $configBackupSize MB)" -ForegroundColor Green
    } catch {
        Write-Error "Configuration backup failed: $($_.Exception.Message)"
        $summary.tasks_failed += @{
            task = "Configuration Backup"
            error = $_.Exception.Message
        }
    }
} else {
    Write-Host "=== Task 4: Configuration Backup (SKIPPED) ===" -ForegroundColor Gray
}

Write-Host ""

# Task 5: Performance Baseline
if (-not $SkipPerformance) {
    Write-Host "=== Task 5: Performance Baseline Documentation ===" -ForegroundColor Yellow
    try {
        $perfBaselineStart = Get-Date
        & "scripts/environment/document-performance-baselines.ps1" -OutputDir "$OutputDir/performance" -Verbose:$Verbose
        $perfBaselineEnd = Get-Date
        $perfBaselineDuration = ($perfBaselineEnd - $perfBaselineStart).TotalMinutes
        
        # Calculate performance documentation size
        $perfDocSize = 0
        if (Test-Path "$OutputDir/performance") {
            $perfDocSize = [math]::Round((Get-ChildItem -Path "$OutputDir/performance" -Recurse | Measure-Object -Property Length -Sum).Sum / 1MB, 2)
        }
        
        $summary.tasks_completed += @{
            task = "Performance Baseline"
            duration_minutes = [math]::Round($perfBaselineDuration, 2)
            status = "Success"
        }
        $summary.backup_sizes_mb["performance"] = $perfDocSize
        
        Write-Host "✓ Performance baseline completed ($([math]::Round($perfBaselineDuration, 1)) minutes, $perfDocSize MB)" -ForegroundColor Green
    } catch {
        Write-Error "Performance baseline failed: $($_.Exception.Message)"
        $summary.tasks_failed += @{
            task = "Performance Baseline"
            error = $_.Exception.Message
        }
    }
} else {
    Write-Host "=== Task 5: Performance Baseline Documentation (SKIPPED) ===" -ForegroundColor Gray
}

Write-Host ""

# Task 6: Rollback Procedures
Write-Host "=== Task 6: Rollback Procedures Creation ===" -ForegroundColor Yellow
try {
    $rollbackStart = Get-Date
    & "scripts/environment/create-rollback-procedures.ps1" -OutputDir "$OutputDir/rollback" -Verbose:$Verbose
    $rollbackEnd = Get-Date
    $rollbackDuration = ($rollbackEnd - $rollbackStart).TotalMinutes
    
    $summary.tasks_completed += @{
        task = "Rollback Procedures"
        duration_minutes = [math]::Round($rollbackDuration, 2)
        status = "Success"
    }
    
    Write-Host "✓ Rollback procedures created ($([math]::Round($rollbackDuration, 1)) minutes)" -ForegroundColor Green
} catch {
    Write-Error "Rollback procedures creation failed: $($_.Exception.Message)"
    $summary.tasks_failed += @{
        task = "Rollback Procedures"
        error = $_.Exception.Message
    }
}

# Calculate total duration
$endTime = Get-Date
$totalDuration = ($endTime - $startTime).TotalMinutes
$summary.total_duration_minutes = [math]::Round($totalDuration, 2)

# Generate files list
$summary.files_created = Get-ChildItem -Path $OutputDir -Recurse -File | ForEach-Object {
    @{
        path = $_.FullName.Replace((Get-Location).Path + "\", "")
        size_mb = [math]::Round($_.Length / 1MB, 3)
        created = $_.CreationTime.ToString("yyyy-MM-dd HH:mm:ss")
    }
}

# Save summary report
$summaryFile = "$OutputDir/assessment_summary_$timestamp.json"
$summary | ConvertTo-Json -Depth 4 | Out-File -FilePath $summaryFile -Encoding UTF8

# Create comprehensive report
$comprehensiveReport = @"
# ICTServe Environment Assessment and Backup Report

**Generated**: $timestamp  
**Total Duration**: $([math]::Round($totalDuration, 1)) minutes  
**Requirements**: 8.1, 8.2, 8.4

---

## Executive Summary

This report documents the complete environment assessment and backup process for ICTServe v3.6.1 before migration to XAMPP environment.

### Tasks Completed: $($summary.tasks_completed.Count)
### Tasks Failed: $($summary.tasks_failed.Count)
### Total Files Created: $($summary.files_created.Count)
### Total Backup Size: $([math]::Round(($summary.backup_sizes_mb.Values | Measure-Object -Sum).Sum, 2)) MB

---

## Task Summary

$(foreach ($task in $summary.tasks_completed) {
"### ✓ $($task.task)
- **Duration**: $($task.duration_minutes) minutes
- **Status**: $($task.status)

"
})

$(if ($summary.tasks_failed.Count -gt 0) {
"## Failed Tasks

$(foreach ($task in $summary.tasks_failed) {
"### ✗ $($task.task)
- **Error**: $($task.error)

"
})
"
})

---

## Backup Sizes

$(foreach ($backup in $summary.backup_sizes_mb.Keys) {
"- **$backup**: $($summary.backup_sizes_mb[$backup]) MB"
})

---

## Files Created

$(foreach ($file in $summary.files_created) {
"- ``$($file.path)`` ($($file.size_mb) MB) - $($file.created)"
})

---

## Next Steps

1. **Review Assessment Results**:
   - Check environment assessment report
   - Verify all backups completed successfully
   - Review performance baselines

2. **Prepare for XAMPP Migration**:
   - Install XAMPP with MySQL 8.0+
   - Set up WSL Redis environment
   - Review migration procedures

3. **Execute Migration**:
   - Follow XAMPP installation tasks
   - Update Laravel configuration
   - Migrate data using backup files

4. **Post-Migration Validation**:
   - Compare performance with baselines
   - Verify all functionality works
   - Run comprehensive tests

---

## Emergency Procedures

If migration fails, use the rollback procedures:

``````powershell
# Emergency rollback
.\$OutputDir\rollback\emergency-rollback.ps1

# Verify rollback
.\$OutputDir\rollback\verify-rollback.ps1
``````

---

## Support Information

**Technical Contact**: ict-support@motac.gov.my  
**System Administrator**: admin@motac.gov.my  
**Business Contact**: bpm@motac.gov.my

---

**Report Generated**: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")  
**Script Version**: 1.0.0  
**Requirements**: 8.1, 8.2, 8.4
"@

$comprehensiveReport | Out-File -FilePath "$OutputDir/comprehensive_report_$timestamp.md" -Encoding UTF8

# Display final summary
Write-Host ""
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "           ENVIRONMENT ASSESSMENT COMPLETED" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Total Duration: $([math]::Round($totalDuration, 1)) minutes" -ForegroundColor Gray
Write-Host "Tasks Completed: $($summary.tasks_completed.Count)" -ForegroundColor Green
Write-Host "Tasks Failed: $($summary.tasks_failed.Count)" -ForegroundColor $(if ($summary.tasks_failed.Count -eq 0) { "Green" } else { "Red" })
Write-Host "Files Created: $($summary.files_created.Count)" -ForegroundColor Gray
Write-Host "Total Backup Size: $([math]::Round(($summary.backup_sizes_mb.Values | Measure-Object -Sum).Sum, 2)) MB" -ForegroundColor Gray
Write-Host ""

if ($summary.tasks_failed.Count -eq 0) {
    Write-Host "✓ All assessment and backup tasks completed successfully!" -ForegroundColor Green
} else {
    Write-Host "⚠ Some tasks failed. Review the comprehensive report for details." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Key Files:" -ForegroundColor Cyan
Write-Host "  - Summary: $(Split-Path $summaryFile -Leaf)" -ForegroundColor Gray
Write-Host "  - Comprehensive Report: comprehensive_report_$timestamp.md" -ForegroundColor Gray
Write-Host "  - Emergency Rollback: rollback/emergency-rollback.ps1" -ForegroundColor Gray
Write-Host ""
Write-Host "Next: Proceed with XAMPP installation and configuration" -ForegroundColor Yellow
Write-Host "      Follow tasks in: .kiro/specs/xampp-environment-revert/tasks.md" -ForegroundColor Cyan