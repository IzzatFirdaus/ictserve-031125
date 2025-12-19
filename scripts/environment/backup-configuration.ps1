# ICTServe Configuration Backup Script
# Purpose: Backup Laravel configuration files (.env, config/)
# Requirements: 8.1, 8.2, 8.4

param(
    [string]$OutputDir = "storage/backups/configuration",
    [switch]$Verbose,
    [switch]$IncludeVendor = $false
)

$ErrorActionPreference = "Continue"
$timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"

# Create output directory
if (-not (Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir -Force | Out-Null
}

Write-Host "=== ICTServe Configuration Backup ===" -ForegroundColor Cyan
Write-Host "Timestamp: $timestamp" -ForegroundColor Gray
Write-Host ""

# Define backup structure
$backupStructure = @{
    "Environment Files" = @(
        ".env",
        ".env.example",
        ".env.local",
        ".env.production",
        ".env.staging",
        ".env.testing"
    )
    "Configuration Files" = @(
        "config/"
    )
    "Composer Files" = @(
        "composer.json",
        "composer.lock"
    )
    "Package Files" = @(
        "package.json",
        "package-lock.json"
    )
    "Laravel Files" = @(
        "artisan",
        "bootstrap/app.php",
        "bootstrap/providers.php"
    )
    "Route Files" = @(
        "routes/"
    )
    "Database Files" = @(
        "database/migrations/",
        "database/seeders/"
    )
    "Resource Files" = @(
        "resources/lang/",
        "resources/views/layouts/",
        "resources/views/components/"
    )
}

# Create backup archive name
$backupArchive = "$OutputDir/ictserve_config_backup_$timestamp.zip"
$backupManifest = "$OutputDir/backup_manifest_$timestamp.json"

# Initialize manifest
$manifest = @{
    timestamp = $timestamp
    backup_type = "configuration"
    laravel_version = ""
    php_version = ""
    files_backed_up = @()
    files_missing = @()
    total_size_mb = 0
    backup_archive = Split-Path $backupArchive -Leaf
}

# Get Laravel and PHP versions
try {
    $manifest.laravel_version = php artisan --version 2>&1 | Out-String
    $manifest.php_version = php -v 2>&1 | Select-Object -First 1
} catch {
    Write-Warning "Could not determine Laravel/PHP versions"
}

Write-Host "Scanning files for backup..." -ForegroundColor Yellow

$filesToBackup = @()
$totalSize = 0

foreach ($category in $backupStructure.Keys) {
    Write-Host "  Checking $category..." -ForegroundColor Gray
    
    foreach ($path in $backupStructure[$category]) {
        if (Test-Path $path) {
            if ((Get-Item $path).PSIsContainer) {
                # Directory - get all files recursively
                $files = Get-ChildItem -Path $path -Recurse -File
                foreach ($file in $files) {
                    $relativePath = $file.FullName.Replace((Get-Location).Path + "\", "")
                    $filesToBackup += $relativePath
                    $totalSize += $file.Length
                    $manifest.files_backed_up += @{
                        path = $relativePath
                        size = $file.Length
                        category = $category
                        last_modified = $file.LastWriteTime.ToString("yyyy-MM-dd HH:mm:ss")
                    }
                }
            } else {
                # Single file
                $file = Get-Item $path
                $filesToBackup += $path
                $totalSize += $file.Length
                $manifest.files_backed_up += @{
                    path = $path
                    size = $file.Length
                    category = $category
                    last_modified = $file.LastWriteTime.ToString("yyyy-MM-dd HH:mm:ss")
                }
            }
        } else {
            Write-Warning "    File not found: $path"
            $manifest.files_missing += $path
        }
    }
}

$manifest.total_size_mb = [math]::Round($totalSize / 1MB, 2)

Write-Host ""
Write-Host "Backup Summary:" -ForegroundColor Cyan
Write-Host "  Files to backup: $($filesToBackup.Count)" -ForegroundColor Gray
Write-Host "  Total size: $($manifest.total_size_mb) MB" -ForegroundColor Gray
Write-Host "  Missing files: $($manifest.files_missing.Count)" -ForegroundColor Gray
Write-Host ""

# Create backup archive
Write-Host "Creating backup archive..." -ForegroundColor Yellow

try {
    # Create temporary directory for organized backup
    $tempBackupDir = "$OutputDir/temp_$timestamp"
    New-Item -ItemType Directory -Path $tempBackupDir -Force | Out-Null
    
    # Copy files to temporary directory maintaining structure
    foreach ($file in $filesToBackup) {
        $sourceFile = $file
        $destFile = Join-Path $tempBackupDir $file
        $destDir = Split-Path $destFile -Parent
        
        if (-not (Test-Path $destDir)) {
            New-Item -ItemType Directory -Path $destDir -Force | Out-Null
        }
        
        Copy-Item -Path $sourceFile -Destination $destFile -Force
        
        if ($Verbose) {
            Write-Host "    Copied: $file" -ForegroundColor Gray
        }
    }
    
    # Create archive
    Compress-Archive -Path "$tempBackupDir\*" -DestinationPath $backupArchive -Force
    
    # Clean up temporary directory
    Remove-Item -Path $tempBackupDir -Recurse -Force
    
    Write-Host "  ✓ Backup archive created" -ForegroundColor Green
    
} catch {
    Write-Error "Failed to create backup archive: $($_.Exception.Message)"
    exit 1
}

# Create environment comparison script
$comparisonScript = @"
# Configuration Comparison Script
# Generated: $timestamp

param(
    [string]`$BackupArchive = "$backupArchive",
    [string]`$CompareDir = "temp_compare_$timestamp"
)

Write-Host "=== Configuration Comparison ===" -ForegroundColor Cyan
Write-Host "Comparing current configuration with backup from: $timestamp" -ForegroundColor Gray
Write-Host ""

# Extract backup for comparison
if (-not (Test-Path `$BackupArchive)) {
    Write-Error "Backup archive not found: `$BackupArchive"
    exit 1
}

Write-Host "Extracting backup for comparison..." -ForegroundColor Yellow
Expand-Archive -Path `$BackupArchive -DestinationPath `$CompareDir -Force

# Compare key configuration files
`$filesToCompare = @(
    ".env",
    "config/app.php",
    "config/database.php",
    "config/cache.php",
    "config/session.php",
    "composer.json"
)

Write-Host "Comparing configuration files..." -ForegroundColor Yellow
foreach (`$file in `$filesToCompare) {
    `$currentFile = `$file
    `$backupFile = Join-Path `$CompareDir `$file
    
    if ((Test-Path `$currentFile) -and (Test-Path `$backupFile)) {
        `$currentHash = Get-FileHash `$currentFile -Algorithm MD5
        `$backupHash = Get-FileHash `$backupFile -Algorithm MD5
        
        if (`$currentHash.Hash -eq `$backupHash.Hash) {
            Write-Host "  ✓ `$file (unchanged)" -ForegroundColor Green
        } else {
            Write-Host "  ⚠ `$file (modified)" -ForegroundColor Yellow
        }
    } elseif (Test-Path `$currentFile) {
        Write-Host "  + `$file (new file)" -ForegroundColor Cyan
    } elseif (Test-Path `$backupFile) {
        Write-Host "  - `$file (deleted)" -ForegroundColor Red
    }
}

# Clean up
Remove-Item -Path `$CompareDir -Recurse -Force -ErrorAction SilentlyContinue

Write-Host ""
Write-Host "Configuration comparison complete." -ForegroundColor Cyan
"@

$comparisonScript | Out-File -FilePath "$OutputDir/compare_config_$timestamp.ps1" -Encoding UTF8

# Create restore script
$restoreScript = @"
# Configuration Restore Script
# Generated: $timestamp

param(
    [string]`$BackupArchive = "$backupArchive",
    [string]`$RestoreDir = ".",
    [switch]`$Force = `$false
)

Write-Host "=== Configuration Restore ===" -ForegroundColor Cyan
Write-Host "Restoring from: `$BackupArchive" -ForegroundColor Gray
Write-Host "Target directory: `$RestoreDir" -ForegroundColor Gray
Write-Host ""

if (-not (Test-Path `$BackupArchive)) {
    Write-Error "Backup archive not found: `$BackupArchive"
    exit 1
}

if (-not `$Force) {
    `$confirmation = Read-Host "This will overwrite existing configuration files. Continue? (y/N)"
    if (`$confirmation -ne "y" -and `$confirmation -ne "Y") {
        Write-Host "Restore cancelled." -ForegroundColor Yellow
        exit 0
    }
}

Write-Host "Extracting configuration backup..." -ForegroundColor Yellow
try {
    Expand-Archive -Path `$BackupArchive -DestinationPath `$RestoreDir -Force
    Write-Host "✓ Configuration restored successfully" -ForegroundColor Green
} catch {
    Write-Error "Restore failed: `$(`$_.Exception.Message)"
    exit 1
}

Write-Host ""
Write-Host "Configuration restore completed!" -ForegroundColor Green
Write-Host ""
Write-Host "Important: Review the following files for environment-specific settings:" -ForegroundColor Yellow
Write-Host "  - .env (database and Redis connections)" -ForegroundColor Gray
Write-Host "  - config/database.php" -ForegroundColor Gray
Write-Host "  - config/cache.php" -ForegroundColor Gray
Write-Host "  - config/session.php" -ForegroundColor Gray
"@

$restoreScript | Out-File -FilePath "$OutputDir/restore_config_$timestamp.ps1" -Encoding UTF8

# Save manifest
$manifest | ConvertTo-Json -Depth 4 | Out-File -FilePath $backupManifest -Encoding UTF8

# Create backup verification script
$verificationScript = @"
# Configuration Backup Verification Script
# Generated: $timestamp

Write-Host "=== Configuration Backup Verification ===" -ForegroundColor Cyan
Write-Host "Backup Date: $timestamp" -ForegroundColor Gray
Write-Host ""

# Check backup files
`$backupFiles = @(
    "$backupArchive",
    "$backupManifest",
    "$OutputDir/compare_config_$timestamp.ps1",
    "$OutputDir/restore_config_$timestamp.ps1"
)

foreach (`$file in `$backupFiles) {
    if (Test-Path `$file) {
        `$size = (Get-Item `$file).Length
        Write-Host "✓ `$(Split-Path `$file -Leaf) ($([math]::Round(`$size/1KB, 2)) KB)" -ForegroundColor Green
    } else {
        Write-Host "✗ `$(Split-Path `$file -Leaf) (missing)" -ForegroundColor Red
    }
}

# Verify archive contents
Write-Host ""
Write-Host "Archive Contents:" -ForegroundColor Cyan
try {
    `$archiveContents = Get-ChildItem -Path "$backupArchive" -Recurse 2>&1
    if (`$archiveContents) {
        Write-Host "✓ Archive contains files" -ForegroundColor Green
    } else {
        Write-Host "⚠ Archive appears empty" -ForegroundColor Yellow
    }
} catch {
    Write-Host "✗ Cannot read archive contents" -ForegroundColor Red
}

Write-Host ""
Write-Host "Backup verification complete." -ForegroundColor Cyan
"@

$verificationScript | Out-File -FilePath "$OutputDir/verify_config_$timestamp.ps1" -Encoding UTF8

# Display final summary
Write-Host ""
Write-Host "=== Configuration Backup Summary ===" -ForegroundColor Cyan
Write-Host "Backup Archive: $(Split-Path $backupArchive -Leaf)" -ForegroundColor Gray
Write-Host "Files Backed Up: $($filesToBackup.Count)" -ForegroundColor Gray
Write-Host "Total Size: $($manifest.total_size_mb) MB" -ForegroundColor Gray
Write-Host "Missing Files: $($manifest.files_missing.Count)" -ForegroundColor Gray
Write-Host ""
Write-Host "Files Created:" -ForegroundColor Gray
Write-Host "  - Backup archive: $(Split-Path $backupArchive -Leaf)" -ForegroundColor Gray
Write-Host "  - Manifest: $(Split-Path $backupManifest -Leaf)" -ForegroundColor Gray
Write-Host "  - Comparison script: compare_config_$timestamp.ps1" -ForegroundColor Gray
Write-Host "  - Restore script: restore_config_$timestamp.ps1" -ForegroundColor Gray
Write-Host "  - Verification script: verify_config_$timestamp.ps1" -ForegroundColor Gray
Write-Host ""
Write-Host "✓ Configuration backup completed successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "To verify backup integrity, run:" -ForegroundColor Yellow
Write-Host "  .\$OutputDir\verify_config_$timestamp.ps1" -ForegroundColor Cyan
Write-Host ""
Write-Host "To restore configuration, run:" -ForegroundColor Yellow
Write-Host "  .\$OutputDir\restore_config_$timestamp.ps1" -ForegroundColor Cyan