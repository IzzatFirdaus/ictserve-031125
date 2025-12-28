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

# Export functions (only when loaded as a module)
if ($MyInvocation.MyCommand.ScriptBlock.Module) {
    Export-ModuleMember -Function @(
        'Write-AutomationLog',
        'Start-ScriptExecution',
        'Stop-ScriptExecution',
        'Get-AutomationConfig',
        'Set-AutomationConfig',
        'Get-EnvironmentConfig',
        'Invoke-SafeOperation',
        'New-TestReport',
        'ConvertTo-HtmlReport',
        'Test-Prerequisites',
        'Get-TestDataPath',
        'New-UniqueId'
    )
}
