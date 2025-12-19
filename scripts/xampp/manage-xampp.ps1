# XAMPP Service Management Script for ICTServe v3.6.1
# Purpose: Manage XAMPP MySQL and Apache services for development environment
# Requirements: XAMPP installed in C:\xampp (default location)

param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("start", "stop", "restart", "status", "install", "optimize")]
    [string]$Action,
    
    [Parameter(Mandatory=$false)]
    [string]$XamppPath = "C:\xampp"
)

# Color output functions
function Write-Success { param($Message) Write-Host "✅ $Message" -ForegroundColor Green }
function Write-Error { param($Message) Write-Host "❌ $Message" -ForegroundColor Red }
function Write-Info { param($Message) Write-Host "ℹ️  $Message" -ForegroundColor Cyan }
function Write-Warning { param($Message) Write-Host "⚠️  $Message" -ForegroundColor Yellow }

# Check if running as Administrator
function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Validate XAMPP installation
function Test-XamppInstallation {
    if (-not (Test-Path $XamppPath)) {
        Write-Error "XAMPP not found at $XamppPath"
        Write-Info "Please install XAMPP or specify correct path with -XamppPath parameter"
        return $false
    }
    
    $requiredFiles = @(
        "$XamppPath\mysql\bin\mysqld.exe",
        "$XamppPath\apache\bin\httpd.exe",
        "$XamppPath\xampp-control.exe"
    )
    
    foreach ($file in $requiredFiles) {
        if (-not (Test-Path $file)) {
            Write-Error "Required XAMPP file not found: $file"
            return $false
        }
    }
    
    Write-Success "XAMPP installation validated at $XamppPath"
    return $true
}

# Test MySQL connection
function Test-MySQLConnection {
    try {
        $connectionString = "Server=127.0.0.1;Port=3306;Database=information_schema;Uid=root;Pwd=;"
        $connection = New-Object MySql.Data.MySqlClient.MySqlConnection($connectionString)
        $connection.Open()
        $connection.Close()
        Write-Success "MySQL connection successful (127.0.0.1:3306)"
        return $true
    }
    catch {
        Write-Error "MySQL connection failed: $($_.Exception.Message)"
        return $false
    }
}

# Test Apache connection
function Test-ApacheConnection {
    try {
        $response = Invoke-WebRequest -Uri "http://127.0.0.1" -TimeoutSec 5 -UseBasicParsing
        if ($response.StatusCode -eq 200) {
            Write-Success "Apache is responding (http://127.0.0.1)"
            return $true
        }
    }
    catch {
        Write-Error "Apache connection failed: $($_.Exception.Message)"
        return $false
    }
}

# Start XAMPP services
function Start-XamppServices {
    Write-Info "Starting XAMPP services..."
    
    # Check if services are already running
    $mysqlProcess = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
    $apacheProcess = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
    
    # Start MySQL if not running
    if (-not $mysqlProcess) {
        Write-Info "Starting MySQL..."
        try {
            Start-Process -FilePath "$XamppPath\mysql\bin\mysqld.exe" -ArgumentList "--defaults-file=$XamppPath\mysql\bin\my.ini" -WindowStyle Hidden
            Start-Sleep -Seconds 3
            
            if (Test-MySQLConnection) {
                Write-Success "MySQL started successfully"
            } else {
                Write-Error "MySQL failed to start properly"
                return $false
            }
        }
        catch {
            Write-Error "Failed to start MySQL: $($_.Exception.Message)"
            return $false
        }
    } else {
        Write-Info "MySQL is already running"
    }
    
    # Start Apache if not running
    if (-not $apacheProcess) {
        Write-Info "Starting Apache..."
        try {
            Start-Process -FilePath "$XamppPath\apache\bin\httpd.exe" -WindowStyle Hidden
            Start-Sleep -Seconds 3
            
            if (Test-ApacheConnection) {
                Write-Success "Apache started successfully"
            } else {
                Write-Error "Apache failed to start properly"
                return $false
            }
        }
        catch {
            Write-Error "Failed to start Apache: $($_.Exception.Message)"
            return $false
        }
    } else {
        Write-Info "Apache is already running"
    }
    
    Write-Success "XAMPP services are running"
    return $true
}

# Stop XAMPP services
function Stop-XamppServices {
    Write-Info "Stopping XAMPP services..."
    
    # Stop MySQL
    $mysqlProcesses = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
    if ($mysqlProcesses) {
        Write-Info "Stopping MySQL..."
        $mysqlProcesses | Stop-Process -Force
        Write-Success "MySQL stopped"
    } else {
        Write-Info "MySQL is not running"
    }
    
    # Stop Apache
    $apacheProcesses = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
    if ($apacheProcesses) {
        Write-Info "Stopping Apache..."
        $apacheProcesses | Stop-Process -Force
        Write-Success "Apache stopped"
    } else {
        Write-Info "Apache is not running"
    }
    
    Write-Success "XAMPP services stopped"
}

# Get service status
function Get-XamppStatus {
    Write-Info "Checking XAMPP service status..."
    
    $mysqlProcess = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
    $apacheProcess = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
    
    # MySQL status
    if ($mysqlProcess) {
        Write-Success "MySQL: Running (PID: $($mysqlProcess.Id))"
        Test-MySQLConnection | Out-Null
    } else {
        Write-Error "MySQL: Not running"
    }
    
    # Apache status
    if ($apacheProcess) {
        Write-Success "Apache: Running (PID: $($apacheProcess.Id))"
        Test-ApacheConnection | Out-Null
    } else {
        Write-Error "Apache: Not running"
    }
    
    # Port status
    Write-Info "Port status:"
    $ports = @(3306, 80, 443)
    foreach ($port in $ports) {
        $connection = Test-NetConnection -ComputerName "127.0.0.1" -Port $port -WarningAction SilentlyContinue
        if ($connection.TcpTestSucceeded) {
            Write-Success "Port $port: Open"
        } else {
            Write-Warning "Port $port: Closed"
        }
    }
}

# Install XAMPP (download and basic setup)
function Install-Xampp {
    Write-Info "XAMPP installation helper..."
    
    if (Test-Path $XamppPath) {
        Write-Warning "XAMPP directory already exists at $XamppPath"
        $continue = Read-Host "Continue with configuration? (y/n)"
        if ($continue -ne "y") {
            return
        }
    }
    
    Write-Info "Please download XAMPP from: https://www.apachefriends.org/download.html"
    Write-Info "Install to: $XamppPath"
    Write-Info "After installation, run: .\manage-xampp.ps1 -Action optimize"
    
    $installed = Read-Host "Have you installed XAMPP? (y/n)"
    if ($installed -eq "y") {
        if (Test-XamppInstallation) {
            Write-Success "XAMPP installation verified"
            Optimize-XamppConfiguration
        }
    }
}

# Optimize XAMPP configuration for ICTServe
function Optimize-XamppConfiguration {
    Write-Info "Optimizing XAMPP configuration for ICTServe..."
    
    # MySQL configuration optimization
    $mysqlConfig = "$XamppPath\mysql\bin\my.ini"
    if (Test-Path $mysqlConfig) {
        Write-Info "Backing up MySQL configuration..."
        Copy-Item $mysqlConfig "$mysqlConfig.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')"
        
        # Add ICTServe-specific MySQL settings
        $optimizations = @"

# ICTServe v3.6.1 Optimizations
max_connections = 200
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
query_cache_type = 1
query_cache_size = 32M
tmp_table_size = 64M
max_heap_table_size = 64M

# Character set for ICTServe (Bahasa Melayu support)
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
default-time-zone = '+08:00'

# Performance tuning
slow_query_log = 1
slow_query_log_file = "$XamppPath\mysql\data\slow-queries.log"
long_query_time = 2
"@
        
        Add-Content -Path $mysqlConfig -Value $optimizations
        Write-Success "MySQL configuration optimized"
    }
    
    # Apache configuration (basic optimization)
    $apacheConfig = "$XamppPath\apache\conf\httpd.conf"
    if (Test-Path $apacheConfig) {
        Write-Info "Apache configuration is ready for Laravel development"
        Write-Info "Document root: $XamppPath\htdocs"
        Write-Success "Apache configuration verified"
    }
    
    Write-Success "XAMPP optimization completed"
    Write-Info "Restart XAMPP services to apply changes: .\manage-xampp.ps1 -Action restart"
}

# Main execution
if (-not (Test-Administrator)) {
    Write-Warning "This script should be run as Administrator for best results"
}

if (-not (Test-XamppInstallation) -and $Action -ne "install") {
    Write-Error "XAMPP installation not found. Run with -Action install first."
    exit 1
}

switch ($Action) {
    "start" {
        Start-XamppServices
    }
    "stop" {
        Stop-XamppServices
    }
    "restart" {
        Stop-XamppServices
        Start-Sleep -Seconds 2
        Start-XamppServices
    }
    "status" {
        Get-XamppStatus
    }
    "install" {
        Install-Xampp
    }
    "optimize" {
        Optimize-XamppConfiguration
    }
}

Write-Info "XAMPP management completed. Use 'Get-Help .\manage-xampp.ps1' for usage information."