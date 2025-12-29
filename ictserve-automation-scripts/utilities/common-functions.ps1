#Requires -Version 7.0
<#
.SYNOPSIS
    Common utility functions for ICTServe automation scripts.

.DESCRIPTION
    This module provides shared PowerShell functions used across all automation scripts
    including logging, configuration management, error handling, and reporting utilities.

.NOTES
    Version: 1.0.0
    Author: ICTServe Automation Team
    Requirements: PowerShell 7.x, Selenium WebDriver
#>

# Script-level variables
$script:LogPath = Join-Path $PSScriptRoot "..\reports\execution-logs"
$script:ConfigPath = Join-Path $PSScriptRoot "..\config"
$script:ScreenshotPath = Join-Path $PSScriptRoot "..\reports\screenshots"
$script:VideoPath = Join-Path $PSScriptRoot "..\reports\videos"

#region Logging Functions

function Write-AutomationLog {
    <#
    .SYNOPSIS
        Writes a log entry with timestamp and severity level.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Message,
        
        [Parameter()]
        [ValidateSet('INFO', 'WARNING', 'ERROR', 'DEBUG', 'SUCCESS')]
        [string]$Level = 'INFO',
        
        [Parameter()]
        [string]$LogFile = "automation-$(Get-Date -Format 'yyyy-MM-dd').log"
    )
    
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss.fff"
    $logEntry = "[$timestamp] [$Level] $Message"
    
    # Ensure log directory exists
    if (-not (Test-Path $script:LogPath)) {
        New-Item -ItemType Directory -Path $script:LogPath -Force | Out-Null
    }
    
    $fullLogPath = Join-Path $script:LogPath $LogFile
    Add-Content -Path $fullLogPath -Value $logEntry
    
    # Console output with color coding
    $color = switch ($Level) {
        'INFO'    { 'Cyan' }
        'WARNING' { 'Yellow' }
        'ERROR'   { 'Red' }
        'DEBUG'   { 'Gray' }
        'SUCCESS' { 'Green' }
        default   { 'White' }
    }
    
    Write-Host $logEntry -ForegroundColor $color
}

function Start-ScriptExecution {
    <#
    .SYNOPSIS
        Initializes script execution with logging and timing.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$ScriptName,
        
        [Parameter()]
        [string]$Description = ""
    )
    
    $script:ExecutionStartTime = Get-Date
    $script:CurrentScriptName = $ScriptName
    
    Write-AutomationLog "========================================" -Level INFO
    Write-AutomationLog "Starting: $ScriptName" -Level INFO
    if ($Description) {
        Write-AutomationLog "Description: $Description" -Level INFO
    }
    Write-AutomationLog "Start Time: $($script:ExecutionStartTime.ToString('yyyy-MM-dd HH:mm:ss'))" -Level INFO
    Write-AutomationLog "========================================" -Level INFO
    
    return @{
        ScriptName = $ScriptName
        StartTime = $script:ExecutionStartTime
        Status = 'Running'
    }
}

function Stop-ScriptExecution {
    <#
    .SYNOPSIS
        Finalizes script execution with summary and timing.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [ValidateSet('Success', 'Failed', 'Skipped', 'Warning')]
        [string]$Status = 'Success',
        
        [Parameter()]
        [string]$Summary = ""
    )
    
    $endTime = Get-Date
    $duration = $endTime - $script:ExecutionStartTime
    
    Write-AutomationLog "========================================" -Level INFO
    Write-AutomationLog "Completed: $($script:CurrentScriptName)" -Level INFO
    Write-AutomationLog "Status: $Status" -Level $(if ($Status -eq 'Success') { 'SUCCESS' } elseif ($Status -eq 'Failed') { 'ERROR' } else { 'WARNING' })
    Write-AutomationLog "Duration: $($duration.ToString('hh\:mm\:ss\.fff'))" -Level INFO
    if ($Summary) {
        Write-AutomationLog "Summary: $Summary" -Level INFO
    }
    Write-AutomationLog "========================================" -Level INFO
    
    return @{
        ScriptName = $script:CurrentScriptName
        StartTime = $script:ExecutionStartTime
        EndTime = $endTime
        Duration = $duration
        Status = $Status
        Summary = $Summary
    }
}

#endregion

#region Configuration Functions

function Get-AutomationConfig {
    <#
    .SYNOPSIS
        Loads automation configuration from JSON files.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [ValidateSet('environments', 'credentials', 'settings', 'browser-settings', 'demo-settings', 'ai-settings')]
        [string]$ConfigType = 'settings'
    )
    
    $configFile = Join-Path $script:ConfigPath "$ConfigType.json"
    
    if (-not (Test-Path $configFile)) {
        Write-AutomationLog "Configuration file not found: $configFile" -Level WARNING
        return $null
    }
    
    try {
        $config = Get-Content $configFile -Raw | ConvertFrom-Json
        Write-AutomationLog "Loaded configuration: $ConfigType" -Level DEBUG
        return $config
    }
    catch {
        Write-AutomationLog "Failed to load configuration: $($_.Exception.Message)" -Level ERROR
        return $null
    }
}

function Set-AutomationConfig {
    <#
    .SYNOPSIS
        Saves automation configuration to JSON files.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [ValidateSet('environments', 'credentials', 'settings', 'browser-settings', 'demo-settings', 'ai-settings')]
        [string]$ConfigType,
        
        [Parameter(Mandatory = $true)]
        [PSObject]$Config
    )
    
    # Ensure config directory exists
    if (-not (Test-Path $script:ConfigPath)) {
        New-Item -ItemType Directory -Path $script:ConfigPath -Force | Out-Null
    }
    
    $configFile = Join-Path $script:ConfigPath "$ConfigType.json"
    
    try {
        $Config | ConvertTo-Json -Depth 10 | Set-Content $configFile
        Write-AutomationLog "Saved configuration: $ConfigType" -Level DEBUG
        return $true
    }
    catch {
        Write-AutomationLog "Failed to save configuration: $($_.Exception.Message)" -Level ERROR
        return $false
    }
}

function Get-EnvironmentConfig {
    <#
    .SYNOPSIS
        Gets configuration for a specific environment.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [ValidateSet('development', 'testing', 'staging', 'production')]
        [string]$Environment = 'testing'
    )
    
    $envConfig = Get-AutomationConfig -ConfigType 'environments'
    
    if ($envConfig -and $envConfig.$Environment) {
        return $envConfig.$Environment
    }
    
    # Return default configuration
    return @{
        BaseUrl = "https://ictserve.motac.gov.my"
        ApiUrl = "https://ictserve.motac.gov.my/api"
        Timeout = 30
        Headless = $false
    }
}

#endregion

#region Error Handling Functions

function Invoke-SafeOperation {
    <#
    .SYNOPSIS
        Executes a script block with error handling and retry logic.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [scriptblock]$ScriptBlock,
        
        [Parameter()]
        [string]$OperationName = "Operation",
        
        [Parameter()]
        [int]$MaxRetries = 3,
        
        [Parameter()]
        [int]$RetryDelaySeconds = 2,
        
        [Parameter()]
        [switch]$ContinueOnError
    )
    
    $attempt = 0
    $lastError = $null
    
    while ($attempt -lt $MaxRetries) {
        $attempt++
        
        try {
            Write-AutomationLog "Executing: $OperationName (Attempt $attempt/$MaxRetries)" -Level DEBUG
            $result = & $ScriptBlock
            Write-AutomationLog "Completed: $OperationName" -Level DEBUG
            return @{
                Success = $true
                Result = $result
                Attempts = $attempt
            }
        }
        catch {
            $lastError = $_
            Write-AutomationLog "Failed: $OperationName - $($_.Exception.Message)" -Level WARNING
            
            if ($attempt -lt $MaxRetries) {
                Write-AutomationLog "Retrying in $RetryDelaySeconds seconds..." -Level DEBUG
                Start-Sleep -Seconds $RetryDelaySeconds
            }
        }
    }
    
    $errorMessage = "Operation failed after $MaxRetries attempts: $OperationName - $($lastError.Exception.Message)"
    
    if ($ContinueOnError) {
        Write-AutomationLog $errorMessage -Level ERROR
        return @{
            Success = $false
            Error = $lastError
            Attempts = $attempt
        }
    }
    else {
        throw $errorMessage
    }
}

#endregion

#region Reporting Functions

function New-TestReport {
    <#
    .SYNOPSIS
        Creates a new test execution report.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$ReportName,
        
        [Parameter()]
        [array]$TestResults = @(),
        
        [Parameter()]
        [ValidateSet('JSON', 'HTML', 'CSV')]
        [string]$Format = 'JSON'
    )
    
    $reportDir = Join-Path $PSScriptRoot "..\reports\analytics"
    if (-not (Test-Path $reportDir)) {
        New-Item -ItemType Directory -Path $reportDir -Force | Out-Null
    }
    
    $timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
    $reportFileName = "$ReportName-$timestamp"
    
    $report = @{
        ReportName = $ReportName
        GeneratedAt = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        TotalTests = $TestResults.Count
        Passed = ($TestResults | Where-Object { $_.Status -eq 'Success' }).Count
        Failed = ($TestResults | Where-Object { $_.Status -eq 'Failed' }).Count
        Skipped = ($TestResults | Where-Object { $_.Status -eq 'Skipped' }).Count
        Results = $TestResults
    }
    
    switch ($Format) {
        'JSON' {
            $reportPath = Join-Path $reportDir "$reportFileName.json"
            $report | ConvertTo-Json -Depth 10 | Set-Content $reportPath
        }
        'CSV' {
            $reportPath = Join-Path $reportDir "$reportFileName.csv"
            $TestResults | Export-Csv -Path $reportPath -NoTypeInformation
        }
        'HTML' {
            $reportPath = Join-Path $reportDir "$reportFileName.html"
            $htmlContent = ConvertTo-HtmlReport -Report $report
            Set-Content -Path $reportPath -Value $htmlContent
        }
    }
    
    Write-AutomationLog "Report generated: $reportPath" -Level SUCCESS
    return $reportPath
}

function ConvertTo-HtmlReport {
    <#
    .SYNOPSIS
        Converts a report object to HTML format.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [hashtable]$Report
    )
    
    $passRate = if ($Report.TotalTests -gt 0) { 
        [math]::Round(($Report.Passed / $Report.TotalTests) * 100, 2) 
    } else { 0 }
    
    $html = @"
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$($Report.ReportName) - Test Report</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .summary { display: flex; gap: 20px; margin: 20px 0; }
        .stat-card { flex: 1; padding: 15px; border-radius: 8px; text-align: center; }
        .stat-card.total { background: #e3f2fd; }
        .stat-card.passed { background: #e8f5e9; }
        .stat-card.failed { background: #ffebee; }
        .stat-card.skipped { background: #fff3e0; }
        .stat-value { font-size: 2em; font-weight: bold; }
        .stat-label { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: 600; }
        .status-success { color: #28a745; }
        .status-failed { color: #dc3545; }
        .status-skipped { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <h1>$($Report.ReportName)</h1>
        <p>Generated: $($Report.GeneratedAt)</p>
        
        <div class="summary">
            <div class="stat-card total">
                <div class="stat-value">$($Report.TotalTests)</div>
                <div class="stat-label">Total Tests</div>
            </div>
            <div class="stat-card passed">
                <div class="stat-value">$($Report.Passed)</div>
                <div class="stat-label">Passed</div>
            </div>
            <div class="stat-card failed">
                <div class="stat-value">$($Report.Failed)</div>
                <div class="stat-label">Failed</div>
            </div>
            <div class="stat-card skipped">
                <div class="stat-value">$($Report.Skipped)</div>
                <div class="stat-label">Skipped</div>
            </div>
        </div>
        
        <p><strong>Pass Rate:</strong> $passRate%</p>
        
        <table>
            <thead>
                <tr>
                    <th>Test Name</th>
                    <th>Status</th>
                    <th>Duration</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
"@
    
    foreach ($result in $Report.Results) {
        $statusClass = switch ($result.Status) {
            'Success' { 'status-success' }
            'Failed' { 'status-failed' }
            default { 'status-skipped' }
        }
        
        $html += @"
                <tr>
                    <td>$($result.Name)</td>
                    <td class="$statusClass">$($result.Status)</td>
                    <td>$($result.Duration)</td>
                    <td>$($result.Message)</td>
                </tr>
"@
    }
    
    $html += @"
            </tbody>
        </table>
    </div>
</body>
</html>
"@
    
    return $html
}

#endregion

#region Utility Functions

function Test-Prerequisites {
    <#
    .SYNOPSIS
        Checks if all required prerequisites are available.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [switch]$Selenium,
        
        [Parameter()]
        [switch]$Chrome,
        
        [Parameter()]
        [switch]$Edge
    )
    
    $results = @{
        PowerShell = $PSVersionTable.PSVersion.Major -ge 7
        AllPassed = $true
    }
    
    if (-not $results.PowerShell) {
        Write-AutomationLog "PowerShell 7.x or higher is required" -Level ERROR
        $results.AllPassed = $false
    }
    
    if ($Selenium) {
        try {
            $seleniumModule = Get-Module -ListAvailable -Name Selenium
            $results.Selenium = $null -ne $seleniumModule
            if (-not $results.Selenium) {
                Write-AutomationLog "Selenium PowerShell module not found. Install with: Install-Module Selenium" -Level WARNING
                $results.AllPassed = $false
            }
        }
        catch {
            $results.Selenium = $false
            $results.AllPassed = $false
        }
    }
    
    if ($Chrome) {
        $chromePath = @(
            "C:\Program Files\Google\Chrome\Application\chrome.exe",
            "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe",
            "$env:LOCALAPPDATA\Google\Chrome\Application\chrome.exe"
        ) | Where-Object { Test-Path $_ } | Select-Object -First 1
        
        $results.Chrome = $null -ne $chromePath
        if (-not $results.Chrome) {
            Write-AutomationLog "Google Chrome not found" -Level WARNING
        }
    }
    
    if ($Edge) {
        $edgePath = @(
            "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe",
            "C:\Program Files\Microsoft\Edge\Application\msedge.exe"
        ) | Where-Object { Test-Path $_ } | Select-Object -First 1
        
        $results.Edge = $null -ne $edgePath
        if (-not $results.Edge) {
            Write-AutomationLog "Microsoft Edge not found" -Level WARNING
        }
    }
    
    return $results
}

function Get-TestDataPath {
    <#
    .SYNOPSIS
        Gets the path to test data files.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [ValidateSet('users', 'tickets', 'assets', 'ai-conversations', 'documents')]
        [string]$DataType = 'users'
    )
    
    return Join-Path $PSScriptRoot "..\test-data\$DataType"
}

function New-UniqueId {
    <#
    .SYNOPSIS
        Generates a unique identifier for test data.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$Prefix = "TEST"
    )
    
    $timestamp = Get-Date -Format "yyyyMMddHHmmss"
    $random = Get-Random -Minimum 1000 -Maximum 9999
    return "$Prefix-$timestamp-$random"
}

#endregion

#region Test Framework Functions

function Initialize-TestResult {
    <#
    .SYNOPSIS
        Initializes a test result object with standard properties.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$TestName,
        
        [Parameter()]
        [string]$Category = "General",
        
        [Parameter()]
        [string]$Description = ""
    )
    
    return @{
        TestName = $TestName
        Category = $Category
        Description = $Description
        Status = "Running"
        StartTime = Get-Date
        EndTime = $null
        Duration = 0
        Screenshots = @()
        ErrorMessage = ""
        StackTrace = ""
        Steps = @()
        Metadata = @{}
    }
}

function Save-TestResult {
    <#
    .SYNOPSIS
        Saves a test result to the results directory.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [hashtable]$Result
    )
    
    $resultsDir = Join-Path $PSScriptRoot "..\reports\test-results"
    if (-not (Test-Path $resultsDir)) {
        New-Item -ItemType Directory -Path $resultsDir -Force | Out-Null
    }
    
    $timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
    # Clean the test name to remove invalid filename characters
    $cleanTestName = $Result.TestName -replace '[\\/:*?"<>|]', '-' -replace '\s+', '-'
    $fileName = "$cleanTestName-$timestamp.json"
    $filePath = Join-Path $resultsDir $fileName
    
    try {
        $Result | ConvertTo-Json -Depth 10 | Set-Content $filePath
        Write-AutomationLog "Test result saved: $filePath" -Level DEBUG
    }
    catch {
        Write-AutomationLog "Failed to save test result: $($_.Exception.Message)" -Level ERROR
    }
}

function Write-TestStep {
    <#
    .SYNOPSIS
        Logs a test step with appropriate formatting based on execution mode.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Message,
        
        [Parameter()]
        [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
        [string]$Mode = 'Visual',
        
        [Parameter()]
        [int]$StepNumber = 0
    )
    
    $prefix = if ($StepNumber -gt 0) { "Step $StepNumber" + ": " } else { "" }
    $fullMessage = "$prefix$Message"
    
    Write-AutomationLog $fullMessage -Level INFO
    
    # Add delays for demo modes
    switch ($Mode) {
        'Demo' { Start-Sleep -Milliseconds 1500 }
        'Interactive' { 
            Write-Host "Press Enter to continue..." -ForegroundColor Yellow
            Read-Host
        }
        'Recording' { Start-Sleep -Milliseconds 2000 }
    }
}

function Write-TestOutput {
    <#
    .SYNOPSIS
        Writes test output with appropriate formatting.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Message,
        
        [Parameter()]
        [ValidateSet('Info', 'Success', 'Warning', 'Error')]
        [string]$Type = 'Info'
    )
    
    $level = switch ($Type) {
        'Success' { 'SUCCESS' }
        'Warning' { 'WARNING' }
        'Error' { 'ERROR' }
        default { 'INFO' }
    }
    
    Write-AutomationLog $Message -Level $level
}

#endregion

#region Browser Automation Functions

function Initialize-WebDriver {
    <#
    .SYNOPSIS
        Initializes a web driver for browser automation.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
        [string]$Mode = 'Visual',
        
        [Parameter()]
        [ValidateSet('Chrome', 'Edge', 'Firefox')]
        [string]$Browser = 'Chrome'
    )
    
    Write-AutomationLog "Initializing $Browser WebDriver in $Mode mode" -Level DEBUG
    
    # For now, return a mock driver object
    # In full implementation, this would use Selenium WebDriver
    return @{
        Browser = $Browser
        Mode = $Mode
        SessionId = (New-Guid).ToString()
        Initialized = Get-Date
    }
}

function Close-WebDriver {
    <#
    .SYNOPSIS
        Closes and cleans up a web driver instance.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver
    )
    
    Write-AutomationLog "Closing WebDriver session: $($Driver.SessionId)" -Level DEBUG
    # In full implementation, this would properly close the Selenium WebDriver
}

function Navigate-ToUrl {
    <#
    .SYNOPSIS
        Navigates the browser to a specified URL.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$Url,
        
        [Parameter()]
        [string]$Mode = 'Visual'
    )
    
    Write-AutomationLog "Navigating to: $Url" -Level DEBUG
    
    if ($Mode -eq 'Demo') {
        Write-Host "🌐 Navigating to" + ": $Url" -ForegroundColor Cyan
        Start-Sleep -Milliseconds 1000
    }
    
    # In full implementation, this would use Selenium WebDriver navigation
}

function Wait-ForElement {
    <#
    .SYNOPSIS
        Waits for an element to be present and visible on the page.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter()]
        [int]$Timeout = 10,
        
        [Parameter()]
        [bool]$Required = $true
    )
    
    Write-AutomationLog "Waiting for element: $Selector (timeout: ${Timeout}s)" -Level DEBUG
    
    # Mock implementation - in real scenario, this would use Selenium WebDriver
    Start-Sleep -Milliseconds 500
    
    return @{
        Selector = $Selector
        Found = $true
        Timestamp = Get-Date
    }
}

function Find-Element {
    <#
    .SYNOPSIS
        Finds an element on the page by selector.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$Selector
    )
    
    Write-AutomationLog "Finding element: $Selector" -Level DEBUG
    
    return @{
        Selector = $Selector
        Found = $true
        Timestamp = Get-Date
    }
}

function Fill-FormField {
    <#
    .SYNOPSIS
        Fills a form field with the specified value.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter(Mandatory = $true)]
        [string]$Value,
        
        [Parameter()]
        [string]$Mode = 'Visual',
        
        [Parameter()]
        [string]$Label = ""
    )
    
    $displayLabel = if ($Label) { $Label } else { $Selector }
    Write-AutomationLog "Filling field '$displayLabel' with value: $Value" -Level DEBUG
    
    if ($Mode -eq 'Demo') {
        Write-Host "✏️ Filling $displayLabel" + ": $Value" -ForegroundColor Green
        Start-Sleep -Milliseconds 800
    }
}

function Select-DropdownOption {
    <#
    .SYNOPSIS
        Selects an option from a dropdown element.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter(Mandatory = $true)]
        [string]$Value,
        
        [Parameter()]
        [string]$Mode = 'Visual'
    )
    
    Write-AutomationLog "Selecting dropdown option '$Value' from: $Selector" -Level DEBUG
    
    if ($Mode -eq 'Demo') {
        Write-Host "📋 Selecting" + ": $Value" -ForegroundColor Blue
        Start-Sleep -Milliseconds 600
    }
}

function Click-Element {
    <#
    .SYNOPSIS
        Clicks on an element.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver,
        
        [Parameter(Mandatory = $true)]
        [object]$Element,
        
        [Parameter()]
        [string]$Mode = 'Visual'
    )
    
    Write-AutomationLog "Clicking element: $($Element.Selector)" -Level DEBUG
    
    if ($Mode -eq 'Demo') {
        Write-Host "👆 Clicking element" -ForegroundColor Yellow
        Start-Sleep -Milliseconds 500
    }
}

function Take-Screenshot {
    <#
    .SYNOPSIS
        Takes a screenshot of the current browser state using Playwright automation.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$Name,
        
        [Parameter()]
        [string]$Mode = 'Visual',
        
        [Parameter()]
        [string]$Url = "",
        
        [Parameter()]
        [switch]$FullPage = $true
    )
    
    $timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
    $fileName = "$Name-$timestamp.png"
    
    # Use a fresh path calculation to avoid corruption
    $screenshotDir = Join-Path $PSScriptRoot "..\reports\screenshots"
    $filePath = Join-Path $screenshotDir $fileName
    
    # Ensure screenshot directory exists
    if (-not (Test-Path $screenshotDir)) {
        New-Item -ItemType Directory -Path $screenshotDir -Force | Out-Null
    }
    
    Write-AutomationLog "Taking screenshot: $fileName" -Level DEBUG
    
    if ($Mode -eq 'Demo') {
        Write-Host "📸 Taking screenshot: $Name" -ForegroundColor Magenta
    }
    
    try {
        # Use Playwright for actual screenshot capture
        $playwrightScript = Join-Path $PSScriptRoot "..\take-single-screenshot.cjs"
        
        # Create a single screenshot script if it doesn't exist
        if (-not (Test-Path $playwrightScript)) {
            $scriptContent = @"
/**
 * Single Screenshot Capture Script
 * Takes a screenshot of a specific URL using Playwright
 */

import { chromium } from "playwright";
import path from "path";
import fs from "fs";

async function takeScreenshot() {
    const args = process.argv.slice(2);
    const url = args[0] || "http://127.0.0.1:8000";
    const outputPath = args[1] || "screenshot.png";
    const fullPage = args[2] !== "false";
    
    let browser = null;
    
    try {
        console.log(`📸 Taking screenshot of: `${url}`);
        
        browser = await chromium.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });
        
        const page = await browser.newPage();
        await page.setViewportSize({ width: 1920, height: 1080 });
        
        // Navigate to URL
        await page.goto(url, { waitUntil: 'networkidle', timeout: 30000 });
        
        // Wait for page to be fully loaded
        await page.waitForTimeout(2000);
        
        // Ensure output directory exists
        const outputDir = path.dirname(outputPath);
        if (!fs.existsSync(outputDir)) {
            fs.mkdirSync(outputDir, { recursive: true });
        }
        
        // Take screenshot
        await page.screenshot({
            path: outputPath,
            fullPage: fullPage,
            animations: "disabled"
        });
        
        console.log(`✅ Screenshot saved: `${outputPath}`);
        
    } catch (error) {
        console.error(`❌ Screenshot failed: `${error.message}`);
        process.exit(1);
    } finally {
        if (browser) {
            await browser.close();
        }
    }
}

takeScreenshot();
"@
            Set-Content -Path $playwrightScript -Value $scriptContent
        }
        
        # Determine URL to screenshot
        $targetUrl = if ($Url) { $Url } else { 
            $config = Get-EnvironmentConfig
            $config.BaseUrl
        }
        
        # Execute Playwright screenshot
        $nodeCommand = "node"
        $arguments = @("`"$playwrightScript`"", "`"$targetUrl`"", "`"$filePath`"", "$FullPage")
        
        Write-AutomationLog "Executing: $nodeCommand $($arguments -join ' ')" -Level DEBUG
        
        $process = Start-Process -FilePath $nodeCommand -ArgumentList $arguments -Wait -PassThru -NoNewWindow -RedirectStandardOutput "$filePath.log" -RedirectStandardError "$filePath.err"
        
        if ($process.ExitCode -eq 0 -and (Test-Path $filePath)) {
            Write-AutomationLog "Screenshot captured successfully: $fileName" -Level SUCCESS
            
            # Clean up log files
            Remove-Item "$filePath.log" -ErrorAction SilentlyContinue
            Remove-Item "$filePath.err" -ErrorAction SilentlyContinue
            
            return $filePath
        } else {
            # Read error details
            $errorDetails = ""
            if (Test-Path "$filePath.err") {
                $errorDetails = Get-Content "$filePath.err" -Raw
            }
            
            Write-AutomationLog "Screenshot failed: $errorDetails" -Level WARNING
            
            # Fallback: create a placeholder with error info
            $placeholderContent = @"
Screenshot Error - $Name
Timestamp: $(Get-Date)
Target URL: $targetUrl
Error: $errorDetails
Mode: $Mode
Driver: $($Driver.SessionId)
"@
            Set-Content -Path $filePath -Value $placeholderContent
            
            # Clean up log files
            Remove-Item "$filePath.log" -ErrorAction SilentlyContinue
            Remove-Item "$filePath.err" -ErrorAction SilentlyContinue
            
            return $filePath
        }
        
    } catch {
        Write-AutomationLog "Screenshot exception: $($_.Exception.Message)" -Level ERROR
        
        # Fallback: create a placeholder file
        $placeholderContent = @"
Screenshot Placeholder - $Name
Timestamp: $(Get-Date)
Exception: $($_.Exception.Message)
Mode: $Mode
Driver: $($Driver.SessionId)
Note: This is a placeholder. Real screenshot capture failed.
"@
        Set-Content -Path $filePath -Value $placeholderContent
        
        return $filePath
    }
}

function Highlight-Element {
    <#
    .SYNOPSIS
        Highlights an element on the page for demonstration purposes.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver,
        
        [Parameter(Mandatory = $true)]
        [object]$Element,
        
        [Parameter()]
        [string]$Mode = 'Visual'
    )
    
    if ($Mode -in @('Demo', 'Interactive', 'Recording')) {
        Write-AutomationLog "Highlighting element: $($Element.Selector)" -Level DEBUG
        Start-Sleep -Milliseconds 1000
    }
}

function Get-ElementText {
    <#
    .SYNOPSIS
        Gets the text content of an element.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$Selector
    )
    
    Write-AutomationLog "Getting text from element: $Selector" -Level DEBUG
    
    # Mock implementation - return sample text
    return "TICKET-$(Get-Random -Minimum 100000 -Maximum 999999)"
}

function Get-AllCookies {
    <#
    .SYNOPSIS
        Gets all cookies from the current browser session.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver
    )
    
    Write-AutomationLog "Getting all cookies from browser session" -Level DEBUG
    
    # Mock implementation
    return @(
        @{ Name = "XSRF-TOKEN"; Value = "mock-token"; HttpOnly = $true; Secure = $true }
        @{ Name = "laravel_session"; Value = "mock-session"; HttpOnly = $true; Secure = $true }
    )
}

function Get-Cookie {
    <#
    .SYNOPSIS
        Gets a specific cookie from the current browser session.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$Name
    )
    
    Write-AutomationLog "Getting cookie: $Name" -Level DEBUG
    
    # Mock implementation
    switch ($Name) {
        "XSRF-TOKEN" { return @{ Name = "XSRF-TOKEN"; Value = "mock-token-$(Get-Random)"; HttpOnly = $true; Secure = $true } }
        "laravel_session" { return @{ Name = "laravel_session"; Value = "mock-session-$(Get-Random)"; HttpOnly = $true; Secure = $true } }
        "remember_token" { return @{ Name = "remember_token"; Value = "mock-remember-$(Get-Random)"; HttpOnly = $true; Secure = $true } }
        default { return $null }
    }
}

#endregion

#region API Testing Functions

function Invoke-ApiRequest {
    <#
    .SYNOPSIS
        Makes an HTTP API request for testing purposes.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Endpoint,
        
        [Parameter()]
        [ValidateSet('GET', 'POST', 'PUT', 'DELETE', 'PATCH')]
        [string]$Method = 'GET',
        
        [Parameter()]
        [hashtable]$Body = @{},
        
        [Parameter()]
        [hashtable]$Headers = @{},
        
        [Parameter()]
        [hashtable]$Query = @{}
    )
    
    $config = Get-EnvironmentConfig
    $baseUrl = $config.ApiUrl
    $fullUrl = "$baseUrl$Endpoint"
    
    if ($Query.Count -gt 0) {
        $queryString = ($Query.GetEnumerator() | ForEach-Object { "$($_.Key)=$($_.Value)" }) -join "&"
        $fullUrl += "?$queryString"
    }
    
    Write-AutomationLog "API Request: $Method $fullUrl" -Level DEBUG
    
    try {
        # Mock implementation - simulate API call
        Start-Sleep -Milliseconds 200
        
        # Return success response for mock
        return @{
            StatusCode = 200
            Success = $true
            Data = @{
                message = "Mock API response"
                timestamp = Get-Date
                endpoint = $Endpoint
                method = $Method
            }
        }
    }
    catch {
        Write-AutomationLog "API Error: $($_.Exception.Message)" -Level ERROR
        return @{
            StatusCode = 0
            Success = $false
            Error = $_.Exception.Message
        }
    }
}

function Assert-ApiSuccess {
    <#
    .SYNOPSIS
        Asserts that an API response indicates success.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [hashtable]$Response,
        
        [Parameter()]
        [string]$Message = "API request should succeed"
    )
    
    if (-not $Response.Success -or $Response.StatusCode -lt 200 -or $Response.StatusCode -ge 300) {
        throw "API assertion failed: $Message. Status: $($Response.StatusCode)"
    }
    
    Write-AutomationLog "API assertion passed: $Message" -Level DEBUG
}

#endregion

#region Assertion Functions

function Assert-ElementExists {
    <#
    .SYNOPSIS
        Asserts that an element exists on the page.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Element,
        
        [Parameter()]
        [string]$Message = "Element should exist"
    )
    
    if (-not $Element -or -not $Element.Found) {
        throw "Element assertion failed: $Message"
    }
    
    Write-AutomationLog "Element assertion passed: $Message" -Level DEBUG
}

#endregion

#region Configuration Helper Functions

function Get-ConfigValue {
    <#
    .SYNOPSIS
        Gets a configuration value by key.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Key,
        
        [Parameter()]
        [string]$Environment = 'testing'
    )
    
    $config = Get-EnvironmentConfig -Environment $Environment
    
    switch ($Key) {
        'BaseUrl' { return $config.BaseUrl }
        'ApiUrl' { return $config.ApiUrl }
        'Timeout' { return $config.Timeout }
        default { return $null }
    }
}

#endregion

#region Demo and Annotation Functions

function Show-Annotation {
    <#
    .SYNOPSIS
        Shows an annotation or tooltip during demo mode.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Text,
        
        [Parameter()]
        [int]$Duration = 2000
    )
    
    Write-Host "💡 $Text" -ForegroundColor Yellow
    Start-Sleep -Milliseconds $Duration
}

function Pause-ForExplanation {
    <#
    .SYNOPSIS
        Pauses execution for explanation during interactive mode.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Message
    )
    
    Write-Host "ℹ️ $Message" -ForegroundColor Cyan
    Write-Host "Press Enter to continue..." -ForegroundColor Yellow
    Read-Host
}

#endregion

#region Test Data Functions

function Get-OrCreateTestTicket {
    <#
    .SYNOPSIS
        Gets or creates a test ticket for testing purposes.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [ValidateSet('Helpdesk', 'AssetLoan')]
        [string]$Type = 'Helpdesk'
    )
    
    # Mock implementation - return a test ticket
    return @{
        TicketNumber = "TEST-$(Get-Random -Minimum 100000 -Maximum 999999)"
        Type = $Type
        Status = "Open"
        CreatedAt = Get-Date
    }
}

function Invoke-Logout {
    <#
    .SYNOPSIS
        Logs out the current user session.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [object]$Driver,
        
        [Parameter()]
        [string]$Mode = 'Visual'
    )
    
    Write-AutomationLog "Logging out user session" -Level DEBUG
    
    try {
        # Navigate to logout URL or click logout button
        $logoutButton = Find-Element -Driver $Driver -Selector ".logout-btn, [href*='logout'], .user-menu .dropdown-item[href*='logout']"
        if ($logoutButton) {
            Click-Element -Driver $Driver -Element $logoutButton -Mode $Mode
        } else {
            # Fallback: navigate to logout URL
            $baseUrl = Get-ConfigValue -Key "BaseUrl"
            Navigate-ToUrl -Driver $Driver -Url "$baseUrl/logout" -Mode $Mode
        }
        
        # Wait for redirect to login page
        Wait-ForElement -Driver $Driver -Selector "form[action*='login'], .login-form, #login-form" -Timeout 10
        
        Write-AutomationLog "User logged out successfully" -Level DEBUG
        return $true
    }
    catch {
        Write-AutomationLog "Logout failed: $($_.Exception.Message)" -Level WARNING
        return $false
    }
}

#endregion

# Export functions (only when loaded as a module)
if ($MyInvocation.MyCommand.ScriptBlock.Module) {
    Export-ModuleMember -Function @(
        # Logging Functions
        'Write-AutomationLog',
        'Start-ScriptExecution',
        'Stop-ScriptExecution',
        
        # Configuration Functions
        'Get-AutomationConfig',
        'Set-AutomationConfig',
        'Get-EnvironmentConfig',
        'Get-ConfigValue',
        
        # Error Handling Functions
        'Invoke-SafeOperation',
        
        # Reporting Functions
        'New-TestReport',
        'ConvertTo-HtmlReport',
        
        # Test Framework Functions
        'Initialize-TestResult',
        'Save-TestResult',
        'Write-TestStep',
        'Write-TestOutput',
        
        # Browser Automation Functions
        'Initialize-WebDriver',
        'Close-WebDriver',
        'Navigate-ToUrl',
        'Wait-ForElement',
        'Find-Element',
        'Fill-FormField',
        'Select-DropdownOption',
        'Click-Element',
        'Take-Screenshot',
        'Highlight-Element',
        'Get-ElementText',
        'Get-AllCookies',
        'Get-Cookie',
        
        # API Testing Functions
        'Invoke-ApiRequest',
        'Assert-ApiSuccess',
        
        # Assertion Functions
        'Assert-ElementExists',
        
        # Demo Functions
        'Show-Annotation',
        'Pause-ForExplanation',
        
        # Test Data Functions
        'Get-OrCreateTestTicket',
        'Invoke-Logout',
        
        # Utility Functions
        'Test-Prerequisites',
        'Get-TestDataPath',
        'New-UniqueId'
    )
}
