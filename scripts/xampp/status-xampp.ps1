#Requires -Version 5.1
<#
.SYNOPSIS
    Check status of XAMPP services for ICTServe development

.DESCRIPTION
    Provides comprehensive status information for XAMPP environment including
    services, ports, database connectivity, and application health.

.PARAMETER XamppPath
    Path to XAMPP installation (default: C:\xampp)

.PARAMETER Detailed
    Show detailed information including process details and configuration

.EXAMPLE
    .\scripts\xampp\status-xampp.ps1 -Detailed
    Show detailed status information

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
#>

[CmdletBinding()]
param(
    [string]$XamppPath = 'C:\xampp',
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
    $mysqlPath = Join-Path $XamppPath 'mysql\bin\mysql.exe'
    if (-not (Test-Path $mysqlPath)) {
        return @{
            Available = $false
            Error = 'MySQL executable not found'
        }
    }

    try {
        $result = & $mysqlPath -u root -e "SELECT VERSION();" 2>$null
        if ($LASTEXITCODE -eq 0) {
            $version = ($result | Select-Object -Skip 1) -join ''
            return @{
                Available = $true
                Version = $version.Trim()
                Path = $mysqlPath
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

function Get-XamppVersion {
    $versionFile = Join-Path $XamppPath 'readme_en.txt'
    if (Test-Path $versionFile) {
        $content = Get-Content $versionFile -First 10
        $versionLine = $content | Where-Object { $_ -match 'XAMPP.*\d+\.\d+\.\d+' } | Select-Object -First 1
        if ($versionLine) {
            return ($versionLine -replace '.*XAMPP\s+', '' -replace '\s+.*', '')
        }
    }

    # Try to get version from control panel
    $xamppControl = Join-Path $XamppPath 'xampp-control.exe'
    if (Test-Path $xamppControl) {
        try {
            $versionInfo = (Get-Item $xamppControl).VersionInfo
            return "$($versionInfo.ProductVersion)"
        }
        catch {
            return 'Unknown'
        }
    }

    return 'Unknown'
}

function Get-PHPVersion {
    $phpPath = Join-Path $XamppPath 'php\php.exe'
    if (Test-Path $phpPath) {
        try {
            $version = & $phpPath -v 2>$null | Select-Object -First 1
            return $version -replace 'PHP ', '' -replace ' \(.*', ''
        }
        catch {
            return 'Unknown'
        }
    }

    return 'Not Found'
}

function Get-ApacheVersion {
    $apachePath = Join-Path $XamppPath 'apache\bin\httpd.exe'
    if (Test-Path $apachePath) {
        try {
            $version = & $apachePath -v 2>$null | Select-Object -First 1
            return ($version -split '/')[1] -replace ' .*', ''
        }
        catch {
            return 'Unknown'
        }
    }

    return 'Not Found'
}

#endregion

#region Status Checks

function Show-XamppOverview {
    Write-Host "`n🔧 XAMPP Environment Status for ICTServe" -ForegroundColor Cyan
    Write-Host "=" * 50 -ForegroundColor Cyan
    Write-Host ""

    # XAMPP Installation
    if (Test-Path $XamppPath) {
        Write-Status "XAMPP Installation: Found at $XamppPath" -Type Success
        $version = Get-XamppVersion
        Write-Host "   Version: $version" -ForegroundColor White
    }
    else {
        Write-Status "XAMPP Installation: Not found at $XamppPath" -Type Error
        return
    }

    # XAMPP Control Panel
    $xamppControl = Join-Path $XamppPath 'xampp-control.exe'
    if (Test-Path $xamppControl) {
        Write-Status "XAMPP Control Panel: Available" -Type Success
    }
    else {
        Write-Status "XAMPP Control Panel: Not found" -Type Warning
    }

    # Component versions
    Write-Host "`nInstalled Components:" -ForegroundColor White
    Write-Host "  PHP: $(Get-PHPVersion)" -ForegroundColor White
    Write-Host "  Apache: $(Get-ApacheVersion)" -ForegroundColor White
}

function Show-ServiceStatus {
    Write-Host "`n📊 Service Status:" -ForegroundColor Cyan
    Write-Host "-" * 20 -ForegroundColor Cyan

    $services = @{
        'Apache2.4' = 'Apache Web Server'
        'mysql' = 'MySQL Database'
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
                    'Apache2.4' = @('httpd', 'apache')
                    'mysql' = @('mysqld', 'mysql')
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
        80 = 'HTTP (Apache)'
        443 = 'HTTPS (Apache SSL)'
        3306 = 'MySQL Database'
        6379 = 'Redis Cache (if installed)'
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
    Write-Host "`nDatabase Status:" -ForegroundColor Cyan
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
        'http://ictserve.local' = 'Virtual Host'
        'http://localhost/phpmyadmin' = 'phpMyAdmin'
        'http://localhost/dashboard' = 'XAMPP Dashboard'
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

    # Check .env.xampp
    if (Test-Path '.env.xampp') {
        Write-Status "XAMPP Environment Template: Available" -Type Success
    }
    else {
        Write-Status "XAMPP Environment Template: Not found" -Type Warning
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
            $cleanVersion = $phpVersion -replace 'PHP ', '' -replace ' \(.*', ''
            Write-Host "  Version: $cleanVersion" -ForegroundColor White
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
    Write-Host "`nXAMPP-related Processes:" -ForegroundColor White
    $processNames = @('httpd', 'apache', 'mysqld', 'mysql', 'php')
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
        Write-Host "  No XAMPP-related processes found" -ForegroundColor Yellow
    }

    # Configuration files
    Write-Host "Configuration Files:" -ForegroundColor White
    $configFiles = @{
        (Join-Path $XamppPath 'apache\conf\httpd.conf') = 'Apache Main Config'
        (Join-Path $XamppPath 'apache\conf\extra\httpd-vhosts.conf') = 'Apache Virtual Hosts'
        (Join-Path $XamppPath 'mysql\bin\my.ini') = 'MySQL Configuration'
        (Join-Path $XamppPath 'php\php.ini') = 'PHP Configuration'
    }

    foreach ($file in $configFiles.Keys) {
        $description = $configFiles[$file]

        if (Test-Path $file) {
            $lastModified = (Get-Item $file).LastWriteTime.ToString('yyyy-MM-dd HH:mm:ss')
            Write-Host "  $description`: Present (Modified: $lastModified)" -ForegroundColor Green
        }
        else {
            Write-Host "  $description`: Not found" -ForegroundColor Red
        }
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

    Show-XamppOverview
    Show-ServiceStatus
    Show-PortStatus
    Show-DatabaseStatus
    Show-WebServerStatus
    Show-EnvironmentStatus
    Show-LaravelStatus
    Show-DetailedInformation

    Write-Host "`n📝 Summary:" -ForegroundColor Cyan
    Write-Host "-" * 10 -ForegroundColor Cyan

    $apacheRunning = Test-ServiceRunning 'Apache2.4'
    $mysqlRunning = Test-ServiceRunning 'mysql'
    $webAccessible = (Test-WebServerResponse 'http://localhost').Available
    $dbAccessible = (Test-DatabaseConnection).Available

    if ($apacheRunning -and $mysqlRunning -and $webAccessible -and $dbAccessible) {
        Write-Status "XAMPP Environment: Functional" -Type Success
    }
    elseif (($apacheRunning -or $mysqlRunning) -and ($webAccessible -or $dbAccessible)) {
        Write-Status "XAMPP Environment: Partially operational" -Type Warning
    }
    else {
        Write-Status "XAMPP Environment: Not running" -Type Error
    }

    Write-Host "`n🔧 Quick Actions:" -ForegroundColor Cyan
    Write-Host "  - Start services: .\scripts\xampp\start-xampp.ps1" -ForegroundColor White
    Write-Host "  - Stop services: .\scripts\xampp\stop-xampp.ps1" -ForegroundColor White
    Write-Host "  - Setup environment: .\scripts\xampp\setup-xampp.ps1" -ForegroundColor White
    Write-Host "  - Open XAMPP Control: Start-Process '$XamppPath\xampp-control.exe'" -ForegroundColor White
    Write-Host ""
}
catch {
    Write-Host "`nStatus check failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
finally {
    if ((Get-Location).Path -like '*\scripts*') {
        Pop-Location
    }
}

#endregion
