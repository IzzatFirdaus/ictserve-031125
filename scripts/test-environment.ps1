#Requires -Version 5.1
<#
.SYNOPSIS
    Test ICTServe Environment Configurations

.DESCRIPTION
    Comprehensive testing script for all ICTServe development environments.
    Tests XAMPP, Laragon, and Docker configurations for compatibility and functionality.

.PARAMETER Environment
    Target environment to test: xampp, laragon, docker, or all

.PARAMETER Quick
    Run quick tests only (skip comprehensive checks)

.PARAMETER Fix
    Attempt to fix common issues automatically

.PARAMETER Report
    Generate detailed test report

.EXAMPLE
    .\scripts\test-environment.ps1 -Environment all -Report
    Test all environments and generate report

.EXAMPLE
    .\scripts\test-environment.ps1 -Environment docker -Fix
    Test Docker environment and fix issues

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
    Requires: PowerShell 5.1+
#>

[CmdletBinding()]
param(
    [ValidateSet('xampp', 'laragon', 'docker', 'all')]
    [string]$Environment = 'all',
    [switch]$Quick,
    [switch]$Fix,
    [switch]$Report
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# Script configuration
$script:ScriptRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$script:ProjectRoot = $script:ScriptRoot
$script:TestResults = @()
$script:TestStartTime = Get-Date

#region Utility Functions

function Write-TestResult {
    param(
        [string]$TestName,
        [string]$Environment,
        [ValidateSet('Pass', 'Fail', 'Warning', 'Skip')]
        [string]$Result,
        [string]$Message = '',
        [string]$Details = ''
    )

    $colors = @{
        Pass = 'Green'
        Fail = 'Red'
        Warning = 'Yellow'
        Skip = 'Gray'
    }

    $icons = @{
        Pass = '✅'
        Fail = '❌'
        Warning = '⚠️'
        Skip = '⏭️'
    }

    $testResult = [PSCustomObject]@{
        TestName = $TestName
        Environment = $Environment
        Result = $Result
        Message = $Message
        Details = $Details
        Timestamp = Get-Date
    }

    $script:TestResults += $testResult

    Write-Host "$($icons[$Result]) [$Environment] $TestName`: " -NoNewline -ForegroundColor $colors[$Result]
    Write-Host $Result -ForegroundColor $colors[$Result]

    if ($Message) {
        Write-Host "    $Message" -ForegroundColor Gray
    }
}

function Test-PortAvailable {
    param([int]$Port, [string]$Host = '127.0.0.1')

    try {
        $listener = [System.Net.Sockets.TcpListener]::new([System.Net.IPAddress]::Any, $Port)
        $listener.Start()
        $listener.Stop()
        return $true
    }
    catch {
        return $false
    }
}

function Test-ServiceInstalled {
    param([string]$ServiceName)

    return (Get-Service -Name $ServiceName -ErrorAction SilentlyContinue) -ne $null
}

function Test-ProcessRunning {
    param([string]$ProcessName)

    return (Get-Process -Name $ProcessName -ErrorAction SilentlyContinue) -ne $null
}

function Test-CommandAvailable {
    param([string]$Command)

    return (Get-Command $Command -ErrorAction SilentlyContinue) -ne $null
}

function Test-PathExists {
    param([string]$Path)

    return Test-Path $Path
}

function Test-WebResponse {
    param([string]$Url, [int]$TimeoutSec = 10)

    try {
        $response = Invoke-WebRequest -Uri $Url -TimeoutSec $TimeoutSec -UseBasicParsing
        return @{
            Success = $true
            StatusCode = $response.StatusCode
            ResponseTime = $response.Headers['X-Response-Time']
        }
    }
    catch {
        return @{
            Success = $false
            Error = $_.Exception.Message
        }
    }
}

#endregion

#region Environment Tests

function Test-XamppEnvironment {
    Write-Host "`n🔧 Testing XAMPP Environment" -ForegroundColor Cyan
    Write-Host "=" * 30 -ForegroundColor Cyan

    # Test XAMPP installation
    $xamppPaths = @('C:\xampp', 'D:\xampp', 'E:\xampp')
    $xamppFound = $false
    $xamppPath = $null

    foreach ($path in $xamppPaths) {
        if (Test-PathExists (Join-Path $path 'xampp-control.exe')) {
            $xamppFound = $true
            $xamppPath = $path
            break
        }
    }

    if ($xamppFound) {
        Write-TestResult 'XAMPP Installation' 'XAMPP' 'Pass' "Found at $xamppPath"
    }
    else {
        Write-TestResult 'XAMPP Installation' 'XAMPP' 'Fail' 'XAMPP not found in common locations'
        return
    }

    # Test XAMPP components
    $components = @{
        'Apache' = 'apache\bin\httpd.exe'
        'MySQL' = 'mysql\bin\mysqld.exe'
        'PHP' = 'php\php.exe'
    }

    foreach ($component in $components.Keys) {
        $componentPath = Join-Path $xamppPath $components[$component]
        if (Test-PathExists $componentPath) {
            Write-TestResult "$component Binary" 'XAMPP' 'Pass' "Found at $componentPath"
        }
        else {
            Write-TestResult "$component Binary" 'XAMPP' 'Fail' "Not found at $componentPath"
        }
    }

    # Test XAMPP services
    $services = @('Apache2.4', 'mysql')
    foreach ($service in $services) {
        if (Test-ServiceInstalled $service) {
            $serviceObj = Get-Service -Name $service
            $status = $serviceObj.Status
            if ($status -eq 'Running') {
                Write-TestResult "$service Service" 'XAMPP' 'Pass' "Service is running"
            }
            else {
                Write-TestResult "$service Service" 'XAMPP' 'Warning' "Service installed but not running ($status)"
            }
        }
        else {
            Write-TestResult "$service Service" 'XAMPP' 'Warning' "Service not installed (may be managed by XAMPP directly)"
        }
    }

    # Test ports
    $ports = @{
        'Apache HTTP' = 80
        'Apache HTTPS' = 443
        'MySQL' = 3306
    }

    foreach ($portName in $ports.Keys) {
        $port = $ports[$portName]
        if (Test-PortAvailable $port) {
            Write-TestResult "$portName Port ($port)" 'XAMPP' 'Pass' "Port is available"
        }
        else {
            Write-TestResult "$portName Port ($port)" 'XAMPP' 'Warning' "Port is in use (may be running)"
        }
    }

    # Test environment file
    if (Test-PathExists '.env.xampp') {
        Write-TestResult 'Environment File' 'XAMPP' 'Pass' '.env.xampp exists'
    }
    else {
        Write-TestResult 'Environment File' 'XAMPP' 'Warning' '.env.xampp not found'
    }

    # Test virtual host configuration
    $vhostsConf = Join-Path $xamppPath 'apache\conf\extra\httpd-vhosts.conf'
    if (Test-PathExists $vhostsConf) {
        $vhostsContent = Get-Content $vhostsConf -Raw
        if ($vhostsContent -match 'ictserve\.local') {
            Write-TestResult 'Virtual Host Config' 'XAMPP' 'Pass' 'ICTServe virtual host configured'
        }
        else {
            Write-TestResult 'Virtual Host Config' 'XAMPP' 'Warning' 'ICTServe virtual host not configured'
        }
    }
    else {
        Write-TestResult 'Virtual Host Config' 'XAMPP' 'Warning' 'Virtual hosts configuration file not found'
    }
}

function Test-LaragonEnvironment {
    Write-Host "`n🔧 Testing Laragon Environment" -ForegroundColor Cyan
    Write-Host "=" * 30 -ForegroundColor Cyan

    # Test Laragon installation
    $laragonPaths = @('C:\laragon', 'D:\laragon', 'E:\laragon')
    $laragonFound = $false
    $laragonPath = $null

    foreach ($path in $laragonPaths) {
        if (Test-PathExists (Join-Path $path 'laragon.exe')) {
            $laragonFound = $true
            $laragonPath = $path
            break
        }
    }

    if ($laragonFound) {
        Write-TestResult 'Laragon Installation' 'Laragon' 'Pass' "Found at $laragonPath"
    }
    else {
        Write-TestResult 'Laragon Installation' 'Laragon' 'Fail' 'Laragon not found in common locations'
        return
    }

    # Test Laragon process
    if (Test-ProcessRunning 'laragon') {
        Write-TestResult 'Laragon Process' 'Laragon' 'Pass' 'Laragon is running'
    }
    else {
        Write-TestResult 'Laragon Process' 'Laragon' 'Warning' 'Laragon is not running'
    }

    # Test Laragon components
    $binPath = Join-Path $laragonPath 'bin'
    $components = @{
        'Nginx' = 'nginx\nginx-*'
        'Apache' = 'apache\apache-*'
        'MySQL' = 'mysql\mysql-*'
        'PHP' = 'php\php-*'
        'Node.js' = 'nodejs\node-*'
    }

    foreach ($component in $components.Keys) {
        $componentPattern = Join-Path $binPath $components[$component]
        $componentPath = Get-ChildItem -Path $componentPattern -Directory -ErrorAction SilentlyContinue | Select-Object -First 1

        if ($componentPath) {
            Write-TestResult "$component Binary" 'Laragon' 'Pass' "Found at $($componentPath.FullName)"
        }
        else {
            Write-TestResult "$component Binary" 'Laragon' 'Warning' "Not found (pattern: $componentPattern)"
        }
    }

    # Test ports
    $ports = @{
        'Web Server' = 80
        'Alternative Web' = 8080
        'MySQL' = 3306
        'Redis' = 6379
    }

    foreach ($portName in $ports.Keys) {
        $port = $ports[$portName]
        if (Test-PortAvailable $port) {
            Write-TestResult "$portName Port ($port)" 'Laragon' 'Pass' "Port is available"
        }
        else {
            Write-TestResult "$portName Port ($port)" 'Laragon' 'Warning' "Port is in use (may be running)"
        }
    }

    # Test environment file
    if (Test-PathExists '.env.laragon') {
        Write-TestResult 'Environment File' 'Laragon' 'Pass' '.env.laragon exists'
    }
    else {
        Write-TestResult 'Environment File' 'Laragon' 'Warning' '.env.laragon not found'
    }

    # Test project symlink
    $wwwPath = Join-Path $laragonPath 'www'
    $projectLink = Join-Path $wwwPath 'ictserve'

    if (Test-PathExists $projectLink) {
        $linkTarget = (Get-Item $projectLink).Target
        if ($linkTarget -and $linkTarget -eq $script:ProjectRoot) {
            Write-TestResult 'Project Symlink' 'Laragon' 'Pass' "Correctly linked to project root"
        }
        else {
            Write-TestResult 'Project Symlink' 'Laragon' 'Warning' "Link exists but target may be incorrect"
        }
    }
    else {
        Write-TestResult 'Project Symlink' 'Laragon' 'Warning' 'Project not linked in Laragon www directory'
    }
}

function Test-DockerEnvironment {
    Write-Host "`n🔧 Testing Docker Environment" -ForegroundColor Cyan
    Write-Host "=" * 30 -ForegroundColor Cyan

    # Test Docker installation
    if (Test-CommandAvailable 'docker') {
        try {
            $dockerVersion = docker --version 2>$null
            Write-TestResult 'Docker Installation' 'Docker' 'Pass' $dockerVersion
        }
        catch {
            Write-TestResult 'Docker Installation' 'Docker' 'Fail' 'Docker command failed'
            return
        }
    }
    else {
        Write-TestResult 'Docker Installation' 'Docker' 'Fail' 'Docker command not found'
        return
    }

    # Test Docker Compose
    if (Test-CommandAvailable 'docker') {
        try {
            $composeVersion = docker compose version 2>$null
            if ($composeVersion) {
                Write-TestResult 'Docker Compose' 'Docker' 'Pass' $composeVersion
            }
            else {
                # Try legacy docker-compose
                if (Test-CommandAvailable 'docker-compose') {
                    $legacyVersion = docker-compose --version 2>$null
                    Write-TestResult 'Docker Compose' 'Docker' 'Warning' "Legacy: $legacyVersion"
                }
                else {
                    Write-TestResult 'Docker Compose' 'Docker' 'Fail' 'Docker Compose not available'
                }
            }
        }
        catch {
            Write-TestResult 'Docker Compose' 'Docker' 'Fail' 'Docker Compose command failed'
        }
    }

    # Test Docker daemon
    try {
        docker info | Out-Null
        Write-TestResult 'Docker Daemon' 'Docker' 'Pass' 'Docker daemon is running'
    }
    catch {
        Write-TestResult 'Docker Daemon' 'Docker' 'Fail' 'Docker daemon is not running'
        return
    }

    # Test Docker compose files
    $composeFiles = @('compose.yaml', 'compose.dev.yaml')
    foreach ($file in $composeFiles) {
        if (Test-PathExists $file) {
            Write-TestResult "Compose File ($file)" 'Docker' 'Pass' 'File exists'
        }
        else {
            Write-TestResult "Compose File ($file)" 'Docker' 'Warning' 'File not found'
        }
    }

    # Test Docker containers (if any are running)
    try {
        $containers = docker compose ps --format json 2>$null | ConvertFrom-Json
        if ($containers) {
            $runningCount = ($containers | Where-Object { $_.State -eq 'running' }).Count
            $totalCount = $containers.Count
            Write-TestResult 'Docker Containers' 'Docker' 'Pass' "$runningCount/$totalCount containers running"
        }
        else {
            Write-TestResult 'Docker Containers' 'Docker' 'Warning' 'No containers found'
        }
    }
    catch {
        Write-TestResult 'Docker Containers' 'Docker' 'Warning' 'Could not check container status'
    }

    # Test environment file
    if (Test-PathExists '.env.docker') {
        Write-TestResult 'Environment File' 'Docker' 'Pass' '.env.docker exists'
    }
    else {
        Write-TestResult 'Environment File' 'Docker' 'Warning' '.env.docker not found'
    }

    # Test Dockerfile
    if (Test-PathExists 'Dockerfile') {
        Write-TestResult 'Dockerfile' 'Docker' 'Pass' 'Dockerfile exists'
    }
    else {
        Write-TestResult 'Dockerfile' 'Docker' 'Warning' 'Dockerfile not found'
    }

    # Test ports (if containers are running)
    $ports = @{
        'Application' = 8000
        'Database' = 3306
        'Redis' = 6379
        'WebSocket' = 8080
    }

    foreach ($portName in $ports.Keys) {
        $port = $ports[$portName]
        if (Test-PortAvailable $port) {
            Write-TestResult "$portName Port ($port)" 'Docker' 'Pass' "Port is available"
        }
        else {
            Write-TestResult "$portName Port ($port)" 'Docker' 'Warning' "Port is in use (may be running)"
        }
    }
}

function Test-CommonRequirements {
    Write-Host "`n🔧 Testing Common Requirements" -ForegroundColor Cyan
    Write-Host "=" * 30 -ForegroundColor Cyan

    # Test required commands
    $commands = @{
        'PHP' = 'php'
        'Composer' = 'composer'
        'Node.js' = 'node'
        'NPM' = 'npm'
        'Git' = 'git'
    }

    foreach ($commandName in $commands.Keys) {
        $command = $commands[$commandName]
        if (Test-CommandAvailable $command) {
            try {
                $version = & $command --version 2>$null | Select-Object -First 1
                Write-TestResult "$commandName Command" 'Common' 'Pass' $version
            }
            catch {
                Write-TestResult "$commandName Command" 'Common' 'Pass' 'Available but version check failed'
            }
        }
        else {
            if ($command -eq 'git') {
                Write-TestResult "$commandName Command" 'Common' 'Warning' 'Not found (recommended but not required)'
            }
            else {
                Write-TestResult "$commandName Command" 'Common' 'Fail' 'Command not found in PATH'
            }
        }
    }

    # Test project structure
    $requiredDirs = @(
        'app',
        'config',
        'database',
        'public',
        'resources',
        'routes',
        'storage',
        'tests'
    )

    foreach ($dir in $requiredDirs) {
        if (Test-PathExists $dir) {
            Write-TestResult "Directory ($dir)" 'Common' 'Pass' 'Directory exists'
        }
        else {
            Write-TestResult "Directory ($dir)" 'Common' 'Fail' 'Required directory missing'
        }
    }

    # Test key files
    $requiredFiles = @(
        'composer.json',
        'package.json',
        'artisan',
        '.env.example'
    )

    foreach ($file in $requiredFiles) {
        if (Test-PathExists $file) {
            Write-TestResult "File ($file)" 'Common' 'Pass' 'File exists'
        }
        else {
            Write-TestResult "File ($file)" 'Common' 'Fail' 'Required file missing'
        }
    }

    # Test storage permissions (Windows)
    $storageDir = 'storage'
    if (Test-PathExists $storageDir) {
        try {
            $testFile = Join-Path $storageDir 'test-write.tmp'
            'test' | Out-File -FilePath $testFile -Encoding UTF8
            Remove-Item $testFile -Force
            Write-TestResult 'Storage Permissions' 'Common' 'Pass' 'Storage directory is writable'
        }
        catch {
            Write-TestResult 'Storage Permissions' 'Common' 'Fail' 'Storage directory is not writable'
        }
    }
}

#endregion

#region Main Execution

try {
    Write-Host "`n🧪 ICTServe Environment Testing" -ForegroundColor Cyan
    Write-Host "=" * 40 -ForegroundColor Cyan
    Write-Host "Testing: $Environment" -ForegroundColor Yellow
    Write-Host "Started: $($script:TestStartTime.ToString('yyyy-MM-dd HH:mm:ss'))" -ForegroundColor Yellow
    Write-Host ""

    # Change to project root
    Push-Location $script:ProjectRoot

    # Run tests based on environment parameter
    switch ($Environment) {
        'xampp' {
            Test-XamppEnvironment
            if (-not $Quick) {
                Test-CommonRequirements
            }
        }
        'laragon' {
            Test-LaragonEnvironment
            if (-not $Quick) {
                Test-CommonRequirements
            }
        }
        'docker' {
            Test-DockerEnvironment
            if (-not $Quick) {
                Test-CommonRequirements
            }
        }
        'all' {
            Test-XamppEnvironment
            Test-LaragonEnvironment
            Test-DockerEnvironment
            Test-CommonRequirements
        }
    }

    # Generate summary
    Write-Host "`n📊 Test Summary" -ForegroundColor Cyan
    Write-Host "=" * 15 -ForegroundColor Cyan

    $totalTests = $script:TestResults.Count
    $passedTests = ($script:TestResults | Where-Object { $_.Result -eq 'Pass' }).Count
    $failedTests = ($script:TestResults | Where-Object { $_.Result -eq 'Fail' }).Count
    $warningTests = ($script:TestResults | Where-Object { $_.Result -eq 'Warning' }).Count
    $skippedTests = ($script:TestResults | Where-Object { $_.Result -eq 'Skip' }).Count

    Write-Host "Total Tests: $totalTests" -ForegroundColor White
    Write-Host "Passed: $passedTests" -ForegroundColor Green
    Write-Host "Failed: $failedTests" -ForegroundColor Red
    Write-Host "Warnings: $warningTests" -ForegroundColor Yellow
    Write-Host "Skipped: $skippedTests" -ForegroundColor Gray

    $testDuration = (Get-Date) - $script:TestStartTime
    Write-Host "Duration: $($testDuration.TotalSeconds.ToString('F2')) seconds" -ForegroundColor White

    # Show failed tests
    if ($failedTests -gt 0) {
        Write-Host "`n❌ Failed Tests:" -ForegroundColor Red
        $script:TestResults | Where-Object { $_.Result -eq 'Fail' } | ForEach-Object {
            Write-Host "  [$($_.Environment)] $($_.TestName): $($_.Message)" -ForegroundColor Red
        }
    }

    # Show warnings
    if ($warningTests -gt 0) {
        Write-Host "`n⚠️  Warnings:" -ForegroundColor Yellow
        $script:TestResults | Where-Object { $_.Result -eq 'Warning' } | ForEach-Object {
            Write-Host "  [$($_.Environment)] $($_.TestName): $($_.Message)" -ForegroundColor Yellow
        }
    }

    # Generate report if requested
    if ($Report) {
        $reportFile = "test-report-$(Get-Date -Format 'yyyyMMdd-HHmmss').json"
        $reportData = @{
            TestRun = @{
                Environment = $Environment
                StartTime = $script:TestStartTime
                EndTime = Get-Date
                Duration = $testDuration.TotalSeconds
                Quick = $Quick
            }
            Summary = @{
                Total = $totalTests
                Passed = $passedTests
                Failed = $failedTests
                Warnings = $warningTests
                Skipped = $skippedTests
            }
            Results = $script:TestResults
        }

        $reportData | ConvertTo-Json -Depth 10 | Out-File -FilePath $reportFile -Encoding UTF8
        Write-Host "`n📄 Report generated: $reportFile" -ForegroundColor Cyan
    }

    # Exit with appropriate code
    if ($failedTests -gt 0) {
        Write-Host "`n❌ Some tests failed. Please review and fix issues." -ForegroundColor Red
        exit 1
    }
    elseif ($warningTests -gt 0) {
        Write-Host "`n⚠️  Tests completed with warnings. Review recommended." -ForegroundColor Yellow
        exit 0
    }
    else {
        Write-Host "`n✅ All tests passed successfully!" -ForegroundColor Green
        exit 0
    }
}
catch {
    Write-Host "`n❌ Test execution failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
finally {
    Pop-Location
}

#endregion
