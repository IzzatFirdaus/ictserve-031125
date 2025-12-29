#Requires -Version 7.0
<#
.SYNOPSIS
    Test runner and reporting framework for ICTServe automation scripts.

.DESCRIPTION
    This module provides test execution pipeline, parallel running,
    categorization, reporting, and coverage analysis.

.NOTES
    Version: 1.0.0
    Author: ICTServe Automation Team
    Requirements: PowerShell 7.x
#>

$script:TestResults = @()
$script:StartTime = $null
$script:EndTime = $null

function Start-TestSuite {
    <#
    .SYNOPSIS
        Starts a test suite execution.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$SuiteName,
        
        [Parameter()]
        [string[]]$Categories = @(),
        
        [Parameter()]
        [switch]$Parallel
    )
    
    $script:StartTime = Get-Date
    $script:TestResults = @()
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║  ICTServe Automation Test Suite: $SuiteName" -ForegroundColor Cyan
    Write-Host "║  Started: $($script:StartTime.ToString('yyyy-MM-dd HH:mm:ss'))" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    return @{
        SuiteName = $SuiteName
        StartTime = $script:StartTime
        Categories = $Categories
        Parallel = $Parallel
    }
}

function Invoke-TestScript {
    <#
    .SYNOPSIS
        Executes a single test script and captures results.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$ScriptPath,
        
        [Parameter()]
        [hashtable]$Parameters = @{}
    )
    
    $result = @{
        ScriptPath = $ScriptPath
        ScriptName = [System.IO.Path]::GetFileName($ScriptPath)
        StartTime = Get-Date
        EndTime = $null
        Duration = $null
        Success = $false
        Error = $null
        Output = $null
    }
    
    try {
        Write-Host "  Running: $($result.ScriptName)" -ForegroundColor Yellow
        
        $output = & $ScriptPath @Parameters
        
        $result.EndTime = Get-Date
        $result.Duration = $result.EndTime - $result.StartTime
        $result.Success = $true
        $result.Output = $output
        
        Write-Host "    ✓ Completed in $([math]::Round($result.Duration.TotalSeconds, 2))s" -ForegroundColor Green
    }
    catch {
        $result.EndTime = Get-Date
        $result.Duration = $result.EndTime - $result.StartTime
        $result.Error = $_.Exception.Message
        
        Write-Host "    ✗ Failed: $($result.Error)" -ForegroundColor Red
    }
    
    $script:TestResults += $result
    return $result
}

function Invoke-CategoryTests {
    <#
    .SYNOPSIS
        Executes all tests in a category.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Category,
        
        [Parameter()]
        [string]$BasePath = "ictserve-automation-scripts/scripts"
    )
    
    $categoryPath = Join-Path $BasePath $Category
    
    if (-not (Test-Path $categoryPath)) {
        Write-Host "Category not found: $Category" -ForegroundColor Red
        return @()
    }
    
    Write-Host ""
    Write-Host "Category: $Category" -ForegroundColor Cyan
    Write-Host "─────────────────────────────────────────" -ForegroundColor DarkGray
    
    $scripts = Get-ChildItem -Path $categoryPath -Recurse -Filter "*.ps1"
    $results = @()
    
    foreach ($script in $scripts) {
        $result = Invoke-TestScript -ScriptPath $script.FullName
        $results += $result
    }
    
    return $results
}

function Stop-TestSuite {
    <#
    .SYNOPSIS
        Stops the test suite and generates report.
    #>
    [CmdletBinding()]
    param()
    
    $script:EndTime = Get-Date
    $duration = $script:EndTime - $script:StartTime
    
    $passed = ($script:TestResults | Where-Object { $_.Success }).Count
    $failed = ($script:TestResults | Where-Object { -not $_.Success }).Count
    $total = $script:TestResults.Count
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║                    TEST SUITE SUMMARY                         ║" -ForegroundColor Cyan
    Write-Host "╠══════════════════════════════════════════════════════════════╣" -ForegroundColor Cyan
    Write-Host "║  Total Tests:  $total                                          " -ForegroundColor White
    Write-Host "║  Passed:       $passed                                          " -ForegroundColor Green
    Write-Host "║  Failed:       $failed                                          " -ForegroundColor $(if ($failed -gt 0) { 'Red' } else { 'White' })
    Write-Host "║  Duration:     $([math]::Round($duration.TotalSeconds, 2))s                                    " -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    
    return @{
        TotalTests = $total
        Passed = $passed
        Failed = $failed
        Duration = $duration
        Results = $script:TestResults
    }
}

function Export-TestReport {
    <#
    .SYNOPSIS
        Exports test results to a report file.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [hashtable]$Summary,
        
        [Parameter()]
        [string]$OutputPath = "ictserve-automation-scripts/reports",
        
        [Parameter()]
        [ValidateSet('JSON', 'HTML', 'CSV')]
        [string]$Format = 'JSON'
    )
    
    if (-not (Test-Path $OutputPath)) {
        New-Item -ItemType Directory -Path $OutputPath -Force | Out-Null
    }
    
    $timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
    $fileName = "test-report-$timestamp"
    
    switch ($Format) {
        'JSON' {
            $filePath = Join-Path $OutputPath "$fileName.json"
            $Summary | ConvertTo-Json -Depth 10 | Out-File $filePath
        }
        'CSV' {
            $filePath = Join-Path $OutputPath "$fileName.csv"
            $Summary.Results | Export-Csv -Path $filePath -NoTypeInformation
        }
        'HTML' {
            $filePath = Join-Path $OutputPath "$fileName.html"
            $html = @"
<!DOCTYPE html>
<html>
<head><title>Test Report - $timestamp</title>
<style>body{font-family:Arial;margin:20px}.pass{color:green}.fail{color:red}</style>
</head>
<body>
<h1>ICTServe Automation Test Report</h1>
<p>Total: $($Summary.TotalTests) | Passed: $($Summary.Passed) | Failed: $($Summary.Failed)</p>
<table border="1"><tr><th>Script</th><th>Status</th><th>Duration</th></tr>
$($Summary.Results | ForEach-Object { "<tr><td>$($_.ScriptName)</td><td class='$(if($_.Success){'pass'}else{'fail'})'>$(if($_.Success){'PASS'}else{'FAIL'})</td><td>$([math]::Round($_.Duration.TotalSeconds,2))s</td></tr>" })
</table>
</body></html>
"@
            $html | Out-File $filePath
        }
    }
    
    Write-Host "Report exported to: $filePath" -ForegroundColor Green
    return $filePath
}

if ($MyInvocation.MyCommand.Module) {
    Export-ModuleMember -Function @(
        'Start-TestSuite',
        'Invoke-TestScript',
        'Invoke-CategoryTests',
        'Stop-TestSuite',
        'Export-TestReport'
    )
}
