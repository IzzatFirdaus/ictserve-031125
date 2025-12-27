#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Backup MCP Memory Server data for ICTServe project
    
.DESCRIPTION
    Creates timestamped backups of the MCP memory knowledge graph.
    Supports automatic cleanup of old backups and verification.
    
.PARAMETER BackupDir
    Directory to store backups (default: storage/mcp/backups)
    
.PARAMETER KeepDays
    Number of days to keep backups (default: 30)
    
.PARAMETER Verify
    Verify backup integrity after creation
    
.EXAMPLE
    .\scripts\backup-mcp-memory.ps1
    
.EXAMPLE
    .\scripts\backup-mcp-memory.ps1 -KeepDays 60 -Verify
#>

param(
    [string]$BackupDir = "storage\mcp\backups",
    [int]$KeepDays = 30,
    [switch]$Verify
)

# Configuration
$MemoryFile = "storage\mcp\memory.jsonl"
$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$BackupFile = "$BackupDir\memory_backup_$Timestamp.jsonl"

Write-Host "🔄 ICTServe MCP Memory Backup" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan

# Create backup directory if it doesn't exist
if (-not (Test-Path $BackupDir)) {
    New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
    Write-Host "📁 Created backup directory: $BackupDir" -ForegroundColor Green
}

# Check if memory file exists
if (-not (Test-Path $MemoryFile)) {
    Write-Host "❌ Memory file not found: $MemoryFile" -ForegroundColor Red
    Write-Host "💡 Creating empty memory file..." -ForegroundColor Yellow
    New-Item -ItemType File -Path $MemoryFile -Force | Out-Null
}

# Get memory file info
$MemoryInfo = Get-Item $MemoryFile
$FileSizeKB = [math]::Round($MemoryInfo.Length / 1KB, 2)

Write-Host "📊 Memory file: $MemoryFile" -ForegroundColor White
Write-Host "📊 Size: $FileSizeKB KB" -ForegroundColor White
Write-Host "📊 Last modified: $($MemoryInfo.LastWriteTime)" -ForegroundColor White

# Create backup
try {
    Copy-Item $MemoryFile $BackupFile -Force
    Write-Host "✅ Backup created: $BackupFile" -ForegroundColor Green
    
    # Verify backup if requested
    if ($Verify) {
        Write-Host "🔍 Verifying backup integrity..." -ForegroundColor Yellow
        
        $OriginalHash = Get-FileHash $MemoryFile -Algorithm SHA256
        $BackupHash = Get-FileHash $BackupFile -Algorithm SHA256
        
        if ($OriginalHash.Hash -eq $BackupHash.Hash) {
            Write-Host "✅ Backup verification successful" -ForegroundColor Green
        } else {
            Write-Host "❌ Backup verification failed - hashes don't match" -ForegroundColor Red
            exit 1
        }
    }
    
} catch {
    Write-Host "❌ Backup failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Cleanup old backups
Write-Host "🧹 Cleaning up backups older than $KeepDays days..." -ForegroundColor Yellow

$CutoffDate = (Get-Date).AddDays(-$KeepDays)
$OldBackups = Get-ChildItem $BackupDir -Filter "memory_backup_*.jsonl" | Where-Object { $_.LastWriteTime -lt $CutoffDate }

if ($OldBackups) {
    foreach ($OldBackup in $OldBackups) {
        Remove-Item $OldBackup.FullName -Force
        Write-Host "🗑️  Removed old backup: $($OldBackup.Name)" -ForegroundColor Gray
    }
    Write-Host "✅ Cleaned up $($OldBackups.Count) old backup(s)" -ForegroundColor Green
} else {
    Write-Host "✅ No old backups to clean up" -ForegroundColor Green
}

# Summary
$AllBackups = Get-ChildItem $BackupDir -Filter "memory_backup_*.jsonl"
Write-Host "" -ForegroundColor White
Write-Host "📈 Backup Summary:" -ForegroundColor Cyan
Write-Host "   Total backups: $($AllBackups.Count)" -ForegroundColor White
Write-Host "   Latest backup: $BackupFile" -ForegroundColor White
Write-Host "   Backup size: $FileSizeKB KB" -ForegroundColor White

# Show recent backups
if ($AllBackups.Count -gt 0) {
    Write-Host "" -ForegroundColor White
    Write-Host "📋 Recent backups:" -ForegroundColor Cyan
    $AllBackups | Sort-Object LastWriteTime -Descending | Select-Object -First 5 | ForEach-Object {
        $Age = (Get-Date) - $_.LastWriteTime
        $AgeStr = if ($Age.Days -gt 0) { "$($Age.Days)d ago" } elseif ($Age.Hours -gt 0) { "$($Age.Hours)h ago" } else { "$($Age.Minutes)m ago" }
        Write-Host "   $($_.Name) ($AgeStr)" -ForegroundColor Gray
    }
}

Write-Host "" -ForegroundColor White
Write-Host "✅ MCP Memory backup completed successfully!" -ForegroundColor Green
