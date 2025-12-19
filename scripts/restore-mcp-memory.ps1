#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Restore MCP Memory Server data for ICTServe project
    
.DESCRIPTION
    Restores MCP memory knowledge graph from backup files.
    Includes safety checks and verification options.
    
.PARAMETER BackupFile
    Specific backup file to restore from
    
.PARAMETER BackupDir
    Directory containing backups (default: storage/mcp/backups)
    
.PARAMETER Latest
    Restore from the most recent backup
    
.PARAMETER Verify
    Verify restore integrity after completion
    
.PARAMETER Force
    Skip confirmation prompts
    
.EXAMPLE
    .\scripts\restore-mcp-memory.ps1 -Latest
    
.EXAMPLE
    .\scripts\restore-mcp-memory.ps1 -BackupFile "storage\mcp\backups\memory_backup_20251216_143000.jsonl" -Verify
#>

param(
    [string]$BackupFile,
    [string]$BackupDir = "storage\mcp\backups",
    [switch]$Latest,
    [switch]$Verify,
    [switch]$Force
)

# Configuration
$MemoryFile = "storage\mcp\memory.jsonl"

Write-Host "🔄 ICTServe MCP Memory Restore" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan

# Determine backup file to restore
if ($Latest) {
    $AvailableBackups = Get-ChildItem $BackupDir -Filter "memory_backup_*.jsonl" -ErrorAction SilentlyContinue
    if (-not $AvailableBackups) {
        Write-Host "❌ No backup files found in: $BackupDir" -ForegroundColor Red
        exit 1
    }
    $BackupFile = ($AvailableBackups | Sort-Object LastWriteTime -Descending | Select-Object -First 1).FullName
    Write-Host "📁 Using latest backup: $BackupFile" -ForegroundColor Green
} elseif (-not $BackupFile) {
    # Show available backups and let user choose
    $AvailableBackups = Get-ChildItem $BackupDir -Filter "memory_backup_*.jsonl" -ErrorAction SilentlyContinue
    if (-not $AvailableBackups) {
        Write-Host "❌ No backup files found in: $BackupDir" -ForegroundColor Red
        exit 1
    }
    
    Write-Host "📋 Available backups:" -ForegroundColor Cyan
    for ($i = 0; $i -lt $AvailableBackups.Count; $i++) {
        $Backup = $AvailableBackups[$i]
        $Age = (Get-Date) - $Backup.LastWriteTime
        $AgeStr = if ($Age.Days -gt 0) { "$($Age.Days)d ago" } elseif ($Age.Hours -gt 0) { "$($Age.Hours)h ago" } else { "$($Age.Minutes)m ago" }
        $SizeKB = [math]::Round($Backup.Length / 1KB, 2)
        Write-Host "   [$($i + 1)] $($Backup.Name) ($AgeStr, $SizeKB KB)" -ForegroundColor White
    }
    
    $Selection = Read-Host "Select backup number (1-$($AvailableBackups.Count))"
    try {
        $SelectedIndex = [int]$Selection - 1
        if ($SelectedIndex -lt 0 -or $SelectedIndex -ge $AvailableBackups.Count) {
            throw "Invalid selection"
        }
        $BackupFile = $AvailableBackups[$SelectedIndex].FullName
    } catch {
        Write-Host "❌ Invalid selection: $Selection" -ForegroundColor Red
        exit 1
    }
}

# Verify backup file exists
if (-not (Test-Path $BackupFile)) {
    Write-Host "❌ Backup file not found: $BackupFile" -ForegroundColor Red
    exit 1
}

# Get backup file info
$BackupInfo = Get-Item $BackupFile
$BackupSizeKB = [math]::Round($BackupInfo.Length / 1KB, 2)

Write-Host "📊 Backup file: $BackupFile" -ForegroundColor White
Write-Host "📊 Size: $BackupSizeKB KB" -ForegroundColor White
Write-Host "📊 Created: $($BackupInfo.LastWriteTime)" -ForegroundColor White

# Check current memory file
if (Test-Path $MemoryFile) {
    $CurrentInfo = Get-Item $MemoryFile
    $CurrentSizeKB = [math]::Round($CurrentInfo.Length / 1KB, 2)
    Write-Host "⚠️  Current memory file will be replaced:" -ForegroundColor Yellow
    Write-Host "   File: $MemoryFile" -ForegroundColor Yellow
    Write-Host "   Size: $CurrentSizeKB KB" -ForegroundColor Yellow
    Write-Host "   Last modified: $($CurrentInfo.LastWriteTime)" -ForegroundColor Yellow
    
    if (-not $Force) {
        $Confirm = Read-Host "Continue with restore? (y/N)"
        if ($Confirm -ne 'y' -and $Confirm -ne 'Y') {
            Write-Host "❌ Restore cancelled by user" -ForegroundColor Red
            exit 0
        }
    }
    
    # Create backup of current file
    $CurrentBackup = "storage\mcp\memory_pre_restore_$(Get-Date -Format 'yyyyMMdd_HHmmss').jsonl"
    Copy-Item $MemoryFile $CurrentBackup -Force
    Write-Host "💾 Current memory backed up to: $CurrentBackup" -ForegroundColor Green
}

# Validate backup file (basic JSONL check)
Write-Host "🔍 Validating backup file..." -ForegroundColor Yellow
try {
    $LineCount = 0
    Get-Content $BackupFile | ForEach-Object {
        $LineCount++
        if ($_.Trim()) {
            $_ | ConvertFrom-Json | Out-Null
        }
    }
    Write-Host "✅ Backup validation successful ($LineCount lines)" -ForegroundColor Green
} catch {
    Write-Host "❌ Backup validation failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "💡 Line $LineCount may contain invalid JSON" -ForegroundColor Yellow
    exit 1
}

# Perform restore
try {
    Copy-Item $BackupFile $MemoryFile -Force
    Write-Host "✅ Memory restored from backup" -ForegroundColor Green
    
    # Verify restore if requested
    if ($Verify) {
        Write-Host "🔍 Verifying restore integrity..." -ForegroundColor Yellow
        
        $BackupHash = Get-FileHash $BackupFile -Algorithm SHA256
        $RestoredHash = Get-FileHash $MemoryFile -Algorithm SHA256
        
        if ($BackupHash.Hash -eq $RestoredHash.Hash) {
            Write-Host "✅ Restore verification successful" -ForegroundColor Green
        } else {
            Write-Host "❌ Restore verification failed - hashes don't match" -ForegroundColor Red
            exit 1
        }
    }
    
} catch {
    Write-Host "❌ Restore failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Summary
Write-Host "" -ForegroundColor White
Write-Host "📈 Restore Summary:" -ForegroundColor Cyan
Write-Host "   Restored from: $BackupFile" -ForegroundColor White
Write-Host "   Restored to: $MemoryFile" -ForegroundColor White
Write-Host "   File size: $BackupSizeKB KB" -ForegroundColor White

Write-Host "" -ForegroundColor White
Write-Host "✅ MCP Memory restore completed successfully!" -ForegroundColor Green
Write-Host "💡 Restart Kiro IDE to reload the memory server with restored data" -ForegroundColor Yellow
