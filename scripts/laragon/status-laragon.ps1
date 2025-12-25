#Requires -Version 5.1
<#
.SYNOPSIS
    Check status of Laragon services for ICTServe development

.DESCRIPTION
    Provides comprehensive status information for Laragon environment including
    services, ports, database connectivity, and application health.

.PARAMETER LaragonPath
    Path to Laragon installation (default: C:\laragon)

.PARAMETER Detailed
    Show detailed information including process details and configuration

.EXAMPLE
    .\scripts\laragon\status-laragon.ps1 -Detailed
    Show detailed status information

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
#>

[CmdletBinding()]
param(
    [string]$LaragonPath = 'C:\laragon',
    [switch]$Detailed
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Continue'

#region Utility Functions

function Write-Status {
    param(
        [string]$Message,
        [ValidateSet('Info', 'Success', 'Warning', 'Error')]
        [string]$Type = 'Info'
    )

    $colors = @{
        Info = 'Cyan'
        Success = 'Green'
        Warning = 'Yellow'
        Error = 'Red'
    }

    $icons = @{
        Info = 'ℹ️'
        Success = '✅'
        Warning = '⚠️'
        Error = '❌'
    }

    Write-Host "$($icons[$Type]) $Message" -ForegroundColor $colors[$Type]
}

function Test-ServiceRunning {
    param([string]$ServiceName)

    $service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue
    return $service -and $service.Status -eq 'Running'
}

function Test-PortOpen {
    param([int]$Port, [string]$Host = '127.0.0.1')

    try {
        $tcpClient = New-Object System.Net.Sockets.TcpClient
        $tcpClient.Connect($Host, $Port)
        $tcpClient.Close()
        return $true
    }
    catch {
        return $false
    }
}

function Get-ServiceDetails {
    param([string]$ServiceName)

    $service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue
    if (-not $service) {
        return @{
            Status = 'Not Found'
            StartType = 'N/A'
            ProcessId = 'N/A'
        }
    }

    $processId = 'N/A'
    try {
        $process = Get-WmiObject -Class Win32_Service | Where-Object { $_.Name -eq $ServiceName }
        if ($process) {
            $processId = $process.ProcessId
        }
    }
    catch {
        # Ignore WMI errors
    }

    return @{
        Status = $service.Status
        StartType = $service.StartType
        ProcessId = $processId
    }
}

function Test-DatabaseConnection {
    # Find MySQL executable in Laragon
    $mysqlPath = Join-Path $LaragonPath 'bin\mysql\mysql-*\bin\mysql.exe'
    $mysqlExe = Get-ChildItem -Path $mysqlPath -ErrorAction SilentlyContinue | Select-Object -First 1

    if (-not $mysqlExe) {
        return @{
            Available = $false
            Error = 'MySQL executable not found'
        }
    }

    try {
        $result = & $mysqlExe.FullName -u root -e "SELECT VERSION();" 2>$null
        if ($LASTEXITCODE -eq 0) {
            $version = ($result | Select-Object -Skip 1) -join ''
            return @{
                Available = $true
                Version = $version.Trim()
                Path = $mysqlExe.FullName
            }
        }
        else {
            return @{
                Available = $false
                Error = 'Connection failed'
            }
        }
    }
    catch {
        return @{
            Available = $false
            Error = $_.Exception.Message
        }
    }
}

function Test-WebServerResponse {
    param([string]$Url)

    try {
        $response = Invoke-WebRequest -Uri $Url -TimeoutSec 5 -UseBasicParsing
        return @{
            Available = $true
            StatusCode = $response.StatusCode
            Server = $response.Headers['Server']
        }
    }
    catch {
        return @{
            Available = $false
            Error = $_.Exception.Message
        }
    }
}

function Get-LaragonVersion {
    $versionFile = Join-Path $LaragonPath 'usr\bin\laragon\version'
    if (Test-Path $versionFile) {
        return Get-Content $versionFile -First 1
    }

    # Try to get version from executable
    $laragonExe = Join-Path $LaragonPath 'laragon.exe'
    if (Test-Path $laragonExe) {
        try {
            $versionInfo = (Get-Item $laragonExe).VersionInfo
            return "$($versionInfo.ProductVersion)"
        }
        catch {
            return 'Unknown'
        }
    }

    return 'Unknown'
}

function Get-PHPVersion {
    $phpPath = Join-Path $LaragonPath 'bin\php\php-*\php.exe'
    $phpExe = Get-ChildItem -Path $phpPath -ErrorAction SilentlyContinue | Select-Object -Last 1

    if ($phpExe) {
        try {
            $version = & $phpExe.FullName -v 2>$null | Select-Object -First 1
            return $version -replace 'PHP ', '' -replace ' \(.*', ''
        }
        catch {
            return 'Unknown'
        }
    }

    return 'Not Found'
}

function Get-NginxVersion {
    $nginxPath = Join-Path $LaragonPath 'bin\nginx\nginx-*\nginx.exe'
    $nginxExe = Get-ChildItem -Path $nginxPath -ErrorAction SilentlyContinue | Select-Object -Last 1

    if ($nginxExe) {
        try {
            $version = & $nginxExe.FullName -v 2>&1 | Select-Object -First 1
            return ($version -split '/')[1]
        }
        catch {
            return 'Unknown'
        }
    }

    return 'Not Found'
}

#endregion

#region Status Checks

function Show-LaragonOverview {
    Write-Host "`n🔧 Laragon Environment Status for ICTServe" -ForegroundColor Cyan
    Write-Host "=" * 50 -ForegroundColor Cyan
    Write-Host ""

    # Laragon Installation
    if (Test-Path $LaragonPath) {
        Write-Status "Laragon Installation: Found at $LaragonPath" -Type Success
        $version = Get-LaragonVersion
        Write-Host "   Version: $version" -ForegroundColor White
    }
    else {
        Write-Status "Laragon Installation: Not found at $LaragonPath" -Type Error
        return
    }

    # Laragon Application
    $laragonProcess = Get-Process -Name 'laragon' -ErrorAction SilentlyContinue
    if ($laragonProcess) {
        Write-Status "Laragon Application: Running (PID: $($laragonProcess.Id))" -Type Success
    }
    else {
        Write-Status "Laragon Application: Not running" -Type Warning
    }

    # Component versions
    Write-Host "`nInstalled Components:" -ForegroundColor White
    Write-Host "  PHP: $(Get-PHPVersion)" -ForegroundColor White
    Write-Host "  Nginx: $(Get-NginxVersion)" -ForegroundColor White
}

function Show-ServiceStatus {
    Write-Host "`n📊 Service Status:" -ForegroundColor Cyan
    Write-Host "-" * 20 -ForegroundColor Cyan

    $services = @{
        'nginx' = 'Nginx Web Server'
        'mysql' = 'MySQL Database'
        'redis' = 'Redis Cache'
        'Apache2.4' = 'Apache Web Server'
    }

    foreach ($serviceName in $services.Keys) {
        $displayName = $services[$serviceName]
        $details = Get-ServiceDetails $serviceName

        Write-Host "`n$displayName ($serviceName):" -ForegroundColor White

        switch ($details.Status) {
            'Running' {
                Write-Status "  Status: Running" -Type Success
                Write-Host "  Start Type: $($details.StartType)" -ForegroundColor White
                if ($details.ProcessId -ne 'N/A') {
                    Write-Host "  Process ID: $($details.ProcessId)" -ForegroundColor White
                }
            }
            'Stopped' {
                Write-Status "  Status: Stopped" -Type Warning
                Write-Host "  Start Type: $($details.StartType)" -ForegroundColor White
            }
            'Not Found' {
                Write-Status "  Status: Service not installed" -Type Info

                # Check for processes that might be running without service
                $processNames = @{
                    'nginx' = @('nginx')
                    'mysql' = @('mysqld', 'mysql')
                    'redis' = @('redis-server')
                    'Apache2.4' = @('httpd', 'apache')
                }

                if ($processNames.ContainsKey($serviceName)) {
                    $found = $false
                    foreach ($processName in $processNames[$serviceName]) {
                        $processes = Get-Process -Name $processName -ErrorAction SilentlyContinue
                        if ($processes) {
                            Write-Status "  Process: Running as $processName (PID: $($processes[0].Id))" -Type Success
                            $found = $true
                            break
                        }
                    }
                    if (-not $found) {
                        Write-Status "  Process: Not running" -Type Warning
                    }
                }
            }
            default {
                Write-Status "  Status: $($details.Status)" -Type Warning
                Write-Host "  Start Type: $($details.StartType)" -ForegroundColor White
            }
        }
    }
}

function Show-PortStatus {
    Write-Host "`n🌐 Port Status:" -ForegroundColor Cyan
    Write-Host "-" * 15 -ForegroundColor Cyan

    $ports = @{
        80 = 'HTTP (Nginx/Apache)'
        8080 = 'HTTP Alternative (Nginx)'
        443 = 'HTTPS (Nginx/Apache SSL)'
        3306 = 'MySQL Database'
        6379 = 'Redis Cache'
        11211 = 'Memcached'
    }

    foreach ($port in $ports.Keys) {
        $service = $ports[$port]
        $isOpen = Test-PortOpen $port

        if ($isOpen) {
            Write-Status "Port $port ($service): In Use" -Type Success
        }
        else {
            Write-Status "Port $port ($service): Available" -Type Warning
        }
    }
}

function Show-DatabaseStatus {
    Write-Host "`n🗄️  Database Status:" -ForegroundColor Cyan
    Write-Host "-" * 20 -ForegroundColor Cyan

    $dbStatus = Test-DatabaseConnection

    if ($dbStatus.Available) {
        Write-Status "MySQL Connection: Available" -Type Success
        Write-Host "  Version: $($dbStatus.Version)" -ForegroundColor White
        Write-Host "  Path: $($dbStatus.Path)" -ForegroundColor White

        # Test ICTServe database
        try {
            $mysqlExe = $dbStatus.Path
            $databases = & $mysqlExe -u root -e "SHOW DATABASES LIKE 'ictserve';" 2>$null
            if ($databases -match 'ictserve') {
                Write-Status "  ICTServe Database: Exists" -Type Success
            }
            else {
                Write-Status "  ICTServe Database: Not found" -Type Warning
            }
        }
        catch {
            Write-Status "  ICTServe Database: Cannot check" -Type Warning
        }
    }
    else {
        Write-Status "MySQL Connection: Failed" -Type Error
        Write-Host "  Error: $($dbStatus.Error)" -ForegroundColor Red
    }
}

function Show-WebServerStatus {
    Write-Host "`n🌐 Web Server Status:" -ForegroundColor Cyan
    Write-Host "-" * 22 -ForegroundColor Cyan

    $urls = @{
        'http://localhost' = 'Default Site (Port 80)'
        'http://localhost:8080' = 'Alternative Site (Port 8080)'
        'http://ictserve.local' = 'Virtual Host'
        'http://localhost/phpmyadmin' = 'phpMyAdmin'
    }

    foreach ($url in $urls.Keys) {
        $description = $urls[$url]
        $response = Test-WebServerResponse $url

        if ($response.Available) {
            $serverInfo = if ($response.Server) { " ($($response.Server))" } else { "" }
            Write-Status "$description`: Accessible (HTTP $($response.StatusCode))$serverInfo" -Type Success
        }
        else {
            Write-Status "$description`: Not accessible" -Type Warning
            if ($Detailed -and $response.Error) {
                Write-Host "    Error: $($response.Error)" -ForegroundColor Red
            }
        }
    }
}

function Show-EnvironmentStatus {
    Write-Host "`n⚙️  Environment Configuration:" -ForegroundColor Cyan
    Write-Host "-" * 30 -ForegroundColor Cyan

    # Check .env file
    if (Test-Path '.env') {
        Write-Status "Environment File: Present" -Type Success

        $envContent = Get-Content '.env' -Raw

        # Check database configuration
        if ($envContent -match 'DB_HOST=127\.0\.0\.1') {
            Write-Status "  Database Host: Configured for localhost" -Type Success
        }
        else {
            Write-Status "  Database Host: Not configured for localhost" -Type Warning
        }

        # Check APP_KEY
        if ($envContent -match 'APP_KEY=base64:') {
            Write-Status "  Application Key: Set" -Type Success
        }
        else {
            Write-Status "  Application Key: Missing or invalid" -Type Warning
        }

        # Check APP_URL
        if ($envContent -match 'APP_URL=http://localhost' -or $envContent -match 'APP_URL=http://ictserve\.local') {
            Write-Status "  Application URL: Configured for local development" -Type Success
        }
        else {
            Write-Status "  Application URL: May need adjustment for local development" -Type Warning
        }
    }
    else {
        Write-Status "Environment File: Missing" -Type Error
    }

    # Check .env.laragon
    if (Test-Path '.env.laragon') {
        Write-Status "Laragon Environment Template: Available" -Type Success
    }
    else {
        Write-Status "Laragon Environment Template: Not found" -Type Warning
    }
}

function Show-LaravelStatus {
    Write-Host "`n🚀 Laravel Application Status:" -ForegroundColor Cyan
    Write-Host "-" * 32 -ForegroundColor Cyan

    # Check if PHP is available
    if (Get-Command php -ErrorAction SilentlyContinue) {
        Write-Status "PHP: Available" -Type Success

        try {
$phpVersion = php -v | Select-Object -First 1
            Write-Host "  Version: $($phpVersion -replace 'PHP ', '' -replace ' \(.*', '')" -ForegroundColor White
        }
        catch {
            Write-Host "  Version: Cannot determine" -ForegroundColor Yellow
        }

        # Check Laravel
        if (Test-Path 'artisan') {
            Write-Status "Laravel: Detected" -Type Success

            try {
                $laravelVersion = php artisan --version 2>$null
                if ($laravelVersion) {
                    Write-Host "  Version: $($laravelVersion -replace 'Laravel Framework ', '')" -ForegroundColor White
                }
            }
            catch {
                Write-Host "  Version: Cannot determine" -ForegroundColor Yellow
            }

            # Check if migrations are needed
            try {
                $migrationStatus = php artisan migrate:status 2>$null
                if ($LASTEXITCODE -eq 0) {
                    Write-Status "  Database: Migrations up to date" -Type Success
                }
                else {
                    Write-Status "  Database: Migrations may be needed" -Type Warning
                }
            }
            catch {
                Write-Status "  Database: Cannot check migration status" -Type Warning
            }
        }
        else {
            Write-Status "Laravel: Not detected (artisan file missing)" -Type Error
        }
    }
    else {
        Write-Status "PHP: Not available in PATH" -Type Error
    }

    # Check Composer
    if (Get-Command composer -ErrorAction SilentlyContinue) {
        Write-Status "Composer: Available" -Type Success

        if (Test-Path 'vendor') {
            Write-Status "  Dependencies: Installed" -Type Success
        }
        else {
            Write-Status "  Dependencies: Not installed (run 'composer install')" -Type Warning
        }
    }
    else {
        Write-Status "Composer: Not available in PATH" -Type Warning
    }

    # Check Node.js/NPM
    if (Get-Command npm -ErrorAction SilentlyContinue) {
        Write-Status "NPM: Available" -Type Success

        if (Test-Path 'node_modules') {
            Write-Status "  Dependencies: Installed" -Type Success
        }
        else {
            Write-Status "  Dependencies: Not installed (run 'npm ci')" -Type Warning
        }
    }
    else {
        Write-Status "NPM: Not available in PATH" -Type Warning
    }
}

function Show-DetailedInformation {
    if (-not $Detailed) {
        return
    }

    Write-Host "`n🔍 Detailed Information:" -ForegroundColor Cyan
    Write-Host "-" * 25 -ForegroundColor Cyan

    # Running processes
    Write-Host "`nLaragon-related Processes:" -ForegroundColor White
    $processNames = @('laragon', 'nginx', 'mysqld', 'mysql', 'redis-server', 'php')
    $foundProcesses = @()

    foreach ($processName in $processNames) {
        $processes = Get-Process -Name $processName -ErrorAction SilentlyContinue
        if ($processes) {
            foreach ($process in $processes) {
                $foundProcesses += [PSCustomObject]@{
                    Name = $process.ProcessName
                    PID = $process.Id
                    CPU = $process.CPU
                    Memory = [math]::Round($process.WorkingSet64 / 1MB, 2)
                }
            }
        }
    }

    if ($foundProcesses.Count -gt 0) {
        $foundProcesses | Format-Table -AutoSize
    }
    else {
        Write-Host "  No Laragon-related processes found" -ForegroundColor Yellow
    }

    # Configuration files
    Write-Host "Configuration Files:" -ForegroundColor White
    $configFiles = @{
        (Join-Path $LaragonPath 'usr\etc\nginx\nginx.conf') = 'Nginx Main Config'
        (Join-Path $LaragonPath 'usr\etc\nginx\sites-enabled\auto.ictserve.local.conf') = 'Nginx Virtual Host'
        (Join-Path $LaragonPath 'bin\mysql\mysql-*\my.ini') = 'MySQL Configuration'
        (Join-Path $LaragonPath 'bin\php\php-*\php.ini') = 'PHP Configuration'
    }

    foreach ($file in $configFiles.Keys) {
        $description = $configFiles[$file]
        $expandedPath = Get-ChildItem -Path $file -ErrorAction SilentlyContinue | Select-Object -First 1

        if ($expandedPath) {
            $lastModified = $expandedPath.LastWriteTime.ToString('yyyy-MM-dd HH:mm:ss')
            Write-Host "  $description`: Present (Modified: $lastModified)" -ForegroundColor Green
        }
        else {
            Write-Host "  $description`: Not found" -ForegroundColor Red
        }
    }
}

#endregion

#region Main Execution

try {
    # Change to project root if we're in scripts directory
    if ((Get-Location).Path -like '*\scripts*') {
        $projectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
        Push-Location $projectRoot
    }

    Show-LaragonOverview
    Show-ServiceStatus
    Show-PortStatus
    Show-DatabaseStatus
    Show-WebServerStatus
    Show-EnvironmentStatus
    Show-LaravelStatus
    Show-DetailedInformation

    Write-Host "`n📝 Summary:" -ForegroundColor Cyan
    Write-Host "-" * 10 -ForegroundColor Cyan

    $laragonRunning = Get-Process -Name 'laragon' -ErrorAction SilentlyContinue
    $webAccessible = (Test-WebServerResponse 'http://localhost').Available -or (Test-WebServerResponse 'http://localhost:8080').Available
    $dbAccessible = (Test-DatabaseConnection).Available

    if ($laragonRunning -and $webAccessible -and $dbAccessible) {
        Write-Status "Laragon Environment: Fully operational" -Type Success
    }
    elseif ($laragonRunning -and ($webAccessible -or $dbAccessible)) {
        Write-Status "Laragon Environment: Partially operational" -Type Warning
    }
    elseif ($laragonRunning) {
        Write-Status "Laragon Environment: Running but services may need attention" -Type Warning
    }
    else {
        Write-Status "Laragon Environment: Not running" -Type Error
    }

    Write-Host "`n🔧 Quick Actions:" -ForegroundColor Cyan
    Write-Host "  - Start services: .\scripts\laragon\start-laragon.ps1" -ForegroundColor White
    Write-Host "  - Stop services: .\scripts\laragon\stop-laragon.ps1" -ForegroundColor White
    Write-Host "  - Setup environment: .\scripts\laragon\setup-laragon.ps1" -ForegroundColor White
    Write-Host "  - Open Laragon: Start-Process '$LaragonPath\laragon.exe'" -ForegroundColor White
    Write-Host ""
}
catch {
    Write-Host "`n❌ Status check failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
finally {
    if ((Get-Location).Path -like '*\scripts*') {
        Pop-Location
    }
}

#endregion
