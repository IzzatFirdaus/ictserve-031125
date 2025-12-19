# ICTServe Database Backup Script
# Purpose: Create comprehensive database backup (mysqldump)
# Requirements: 8.1, 8.2, 8.4

param(
    [string]$OutputDir = "storage/backups/database",
    [string]$ConfigFile = ".env",
    [switch]$Verbose,
    [switch]$CompressBackup = $true
)

$ErrorActionPreference = "Stop"
$timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"

# Create output directory
if (-not (Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir -Force | Out-Null
}

Write-Host "=== ICTServe Database Backup ===" -ForegroundColor Cyan
Write-Host "Timestamp: $timestamp" -ForegroundColor Gray
Write-Host ""

# Read database configuration from .env file
if (-not (Test-Path $ConfigFile)) {
    Write-Error ".env file not found at: $ConfigFile"
    exit 1
}

Write-Host "Reading database configuration..." -ForegroundColor Yellow
$envContent = Get-Content $ConfigFile -Raw

# Extract database configuration
$dbConnection = ($envContent | Select-String "DB_CONNECTION=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
$dbHost = ($envContent | Select-String "DB_HOST=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
$dbPort = ($envContent | Select-String "DB_PORT=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
$dbDatabase = ($envContent | Select-String "DB_DATABASE=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
$dbUsername = ($envContent | Select-String "DB_USERNAME=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
$dbPassword = ($envContent | Select-String "DB_PASSWORD=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })

# Validate configuration
if (-not $dbConnection -or -not $dbHost -or -not $dbDatabase) {
    Write-Error "Invalid database configuration. Missing required parameters."
    exit 1
}

Write-Host "Database Configuration:" -ForegroundColor Cyan
Write-Host "  Connection: $dbConnection" -ForegroundColor Gray
Write-Host "  Host: $dbHost" -ForegroundColor Gray
Write-Host "  Port: $dbPort" -ForegroundColor Gray
Write-Host "  Database: $dbDatabase" -ForegroundColor Gray
Write-Host "  Username: $dbUsername" -ForegroundColor Gray
Write-Host ""

# Check if mysqldump is available
try {
    $mysqldumpVersion = mysqldump --version 2>&1
    Write-Host "Using: $mysqldumpVersion" -ForegroundColor Green
} catch {
    Write-Error "mysqldump not found. Please ensure MySQL client tools are installed."
    exit 1
}

# Define backup file paths
$backupFile = "$OutputDir/ictserve_backup_$timestamp.sql"
$schemaFile = "$OutputDir/ictserve_schema_$timestamp.sql"
$dataFile = "$OutputDir/ictserve_data_$timestamp.sql"
$infoFile = "$OutputDir/backup_info_$timestamp.json"

# Create backup info file
$backupInfo = @{
    timestamp = $timestamp
    database = $dbDatabase
    host = $dbHost
    port = $dbPort
    connection = $dbConnection
    backup_type = "full"
    files = @{
        full_backup = Split-Path $backupFile -Leaf
        schema_only = Split-Path $schemaFile -Leaf
        data_only = Split-Path $dataFile -Leaf
    }
    compression = $CompressBackup
    laravel_version = ""
    php_version = ""
} | ConvertTo-Json -Depth 3

# Get Laravel and PHP versions
try {
    $laravelVersion = php artisan --version 2>&1
    $phpVersion = php -v 2>&1 | Select-Object -First 1
    
    $backupInfoObj = $backupInfo | ConvertFrom-Json
    $backupInfoObj.laravel_version = $laravelVersion
    $backupInfoObj.php_version = $phpVersion
    $backupInfo = $backupInfoObj | ConvertTo-Json -Depth 3
} catch {
    Write-Warning "Could not determine Laravel/PHP versions"
}

# Build mysqldump command parameters
$mysqldumpParams = @(
    "--host=$dbHost"
    "--user=$dbUsername"
    "--single-transaction"
    "--routines"
    "--triggers"
    "--events"
    "--add-drop-table"
    "--create-options"
    "--disable-keys"
    "--extended-insert"
    "--quick"
    "--lock-tables=false"
)

if ($dbPort) {
    $mysqldumpParams += "--port=$dbPort"
}

if ($dbPassword) {
    $mysqldumpParams += "--password=$dbPassword"
}

# Function to run mysqldump with error handling
function Invoke-MySQLDump {
    param(
        [string[]]$Parameters,
        [string]$OutputFile,
        [string]$Description
    )
    
    Write-Host "Creating $Description..." -ForegroundColor Yellow
    
    try {
        $command = "mysqldump $($Parameters -join ' ') $dbDatabase"
        if ($Verbose) {
            Write-Host "Command: $command" -ForegroundColor Gray
        }
        
        # Execute mysqldump
        $process = Start-Process -FilePath "mysqldump" -ArgumentList ($Parameters + $dbDatabase) -RedirectStandardOutput $OutputFile -RedirectStandardError "temp_error.log" -Wait -PassThru -NoNewWindow
        
        if ($process.ExitCode -ne 0) {
            $errorContent = Get-Content "temp_error.log" -Raw -ErrorAction SilentlyContinue
            Remove-Item "temp_error.log" -ErrorAction SilentlyContinue
            throw "mysqldump failed with exit code $($process.ExitCode): $errorContent"
        }
        
        Remove-Item "temp_error.log" -ErrorAction SilentlyContinue
        
        $fileSize = (Get-Item $OutputFile).Length
        Write-Host "  ✓ $Description completed ($([math]::Round($fileSize/1MB, 2)) MB)" -ForegroundColor Green
        
        return $fileSize
    } catch {
        Write-Error "Failed to create $Description`: $($_.Exception.Message)"
        throw
    }
}

# Create full backup
Write-Host "Starting database backup process..." -ForegroundColor Cyan
$fullBackupSize = Invoke-MySQLDump -Parameters $mysqldumpParams -OutputFile $backupFile -Description "full database backup"

# Create schema-only backup
$schemaParams = $mysqldumpParams + @("--no-data")
$schemaBackupSize = Invoke-MySQLDump -Parameters $schemaParams -OutputFile $schemaFile -Description "schema-only backup"

# Create data-only backup
$dataParams = $mysqldumpParams + @("--no-create-info")
$dataBackupSize = Invoke-MySQLDump -Parameters $dataParams -OutputFile $dataFile -Description "data-only backup"

# Compress backups if requested
if ($CompressBackup) {
    Write-Host "Compressing backup files..." -ForegroundColor Yellow
    
    try {
        # Compress full backup
        Compress-Archive -Path $backupFile -DestinationPath "$backupFile.zip" -Force
        Remove-Item $backupFile
        
        # Compress schema backup
        Compress-Archive -Path $schemaFile -DestinationPath "$schemaFile.zip" -Force
        Remove-Item $schemaFile
        
        # Compress data backup
        Compress-Archive -Path $dataFile -DestinationPath "$dataFile.zip" -Force
        Remove-Item $dataFile
        
        Write-Host "  ✓ Backup files compressed" -ForegroundColor Green
        
        # Update backup info
        $backupInfoObj = $backupInfo | ConvertFrom-Json
        $backupInfoObj.files.full_backup = "$($backupInfoObj.files.full_backup).zip"
        $backupInfoObj.files.schema_only = "$($backupInfoObj.files.schema_only).zip"
        $backupInfoObj.files.data_only = "$($backupInfoObj.files.data_only).zip"
        $backupInfo = $backupInfoObj | ConvertTo-Json -Depth 3
        
    } catch {
        Write-Warning "Compression failed: $($_.Exception.Message)"
    }
}

# Save backup information
$backupInfo | Out-File -FilePath $infoFile -Encoding UTF8

# Create backup verification script
$verificationScript = @"
# Database Backup Verification Script
# Generated: $timestamp

# Backup Information
Write-Host "=== ICTServe Database Backup Verification ===" -ForegroundColor Cyan
Write-Host "Backup Date: $timestamp" -ForegroundColor Gray
Write-Host "Database: $dbDatabase" -ForegroundColor Gray
Write-Host ""

# Check backup files exist
`$backupDir = "$OutputDir"
`$files = @(
    "$(Split-Path $backupFile -Leaf)$(if ($CompressBackup) { '.zip' })",
    "$(Split-Path $schemaFile -Leaf)$(if ($CompressBackup) { '.zip' })",
    "$(Split-Path $dataFile -Leaf)$(if ($CompressBackup) { '.zip' })",
    "$(Split-Path $infoFile -Leaf)"
)

foreach (`$file in `$files) {
    `$filePath = Join-Path `$backupDir `$file
    if (Test-Path `$filePath) {
        `$size = (Get-Item `$filePath).Length
        Write-Host "✓ `$file ($([math]::Round(`$size/1MB, 2)) MB)" -ForegroundColor Green
    } else {
        Write-Host "✗ `$file (missing)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Backup verification complete." -ForegroundColor Cyan
"@

$verificationScript | Out-File -FilePath "$OutputDir/verify_backup_$timestamp.ps1" -Encoding UTF8

# Display summary
Write-Host ""
Write-Host "=== Backup Summary ===" -ForegroundColor Cyan
Write-Host "Database: $dbDatabase" -ForegroundColor Gray
Write-Host "Backup Directory: $OutputDir" -ForegroundColor Gray
Write-Host "Files Created:" -ForegroundColor Gray
Write-Host "  - Full backup: $(Split-Path $backupFile -Leaf)$(if ($CompressBackup) { '.zip' })" -ForegroundColor Gray
Write-Host "  - Schema only: $(Split-Path $schemaFile -Leaf)$(if ($CompressBackup) { '.zip' })" -ForegroundColor Gray
Write-Host "  - Data only: $(Split-Path $dataFile -Leaf)$(if ($CompressBackup) { '.zip' })" -ForegroundColor Gray
Write-Host "  - Backup info: $(Split-Path $infoFile -Leaf)" -ForegroundColor Gray
Write-Host "  - Verification script: verify_backup_$timestamp.ps1" -ForegroundColor Gray
Write-Host ""
Write-Host "Total backup size: $([math]::Round(($fullBackupSize + $schemaBackupSize + $dataBackupSize)/1MB, 2)) MB" -ForegroundColor Cyan
Write-Host ""
Write-Host "✓ Database backup completed successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "To verify backup integrity, run:" -ForegroundColor Yellow
Write-Host "  .\$OutputDir\verify_backup_$timestamp.ps1" -ForegroundColor Cyan