#Requires -Version 7.0
<#
.SYNOPSIS
    Tests PDPA compliance automation.

.DESCRIPTION
    This script tests data protection, audit logging, compliance reporting,
    file upload security, and malware protection automation.

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.NOTES
    Version: 1.0.0
    Requirements: 12.4, 12.5, 12.6, 12.7
#>

[CmdletBinding()]
param(
    [Parameter()]
    [string]$BaseUrl = "http://localhost:8000",
    
    [Parameter()]
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Headless'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

. "$ScriptRoot\..\..\utilities\common-functions.ps1"
. "$ScriptRoot\..\..\utilities\api-helpers.ps1"

$TestConfig = @{
    Name = "PDPA Compliance Test"
    Category = "Security - Compliance"
    Requirements = @("12.4", "12.5", "12.6", "12.7")
    ExpectedDuration = 90
}

function Test-DataProtection {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing data protection" -Level INFO
    
    $results = @{
        TestName = "Data Protection"
        Passed = $false
        Details = @{ EncryptionEnabled = $false; DataMasked = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/admin/compliance/data-protection" -Method GET -IgnoreErrors
        if ($response) {
            $results.Details.EncryptionEnabled = $response.encryption_enabled -eq $true
            $results.Details.DataMasked = $response.data_masking -eq $true
        }
        
        $results.Passed = $results.Details.EncryptionEnabled -or $results.Details.DataMasked
    }
    catch {
        Write-AutomationLog "Data protection test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-AuditLogging {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing audit logging" -Level INFO
    
    $results = @{
        TestName = "Audit Logging"
        Passed = $false
        Details = @{ AuditLogEndpointAvailable = $false; LogsRecorded = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/admin/audit-logs" -Method GET -IgnoreErrors
        if ($response) {
            $results.Details.AuditLogEndpointAvailable = $true
            $results.Details.LogsRecorded = $response.Count -ge 0
        }
        
        $results.Passed = $results.Details.AuditLogEndpointAvailable
    }
    catch {
        Write-AutomationLog "Audit logging test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-FileUploadSecurity {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing file upload security" -Level INFO
    
    $results = @{
        TestName = "File Upload Security"
        Passed = $false
        Details = @{ FileScanningEnabled = $false; MalwareProtection = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/admin/security/file-scanning" -Method GET -IgnoreErrors
        if ($response) {
            $results.Details.FileScanningEnabled = $response.clamav_enabled -eq $true
            $results.Details.MalwareProtection = $response.malware_protection -eq $true
        }
        
        $results.Passed = $results.Details.FileScanningEnabled -or $results.Details.MalwareProtection
    }
    catch {
        Write-AutomationLog "File upload security test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-PDPAComplianceTest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║          PDPA Compliance Test Suite                           ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $tests = @(
        @{ Name = "Data Protection"; Func = { Test-DataProtection } },
        @{ Name = "Audit Logging"; Func = { Test-AuditLogging } },
        @{ Name = "File Upload Security"; Func = { Test-FileUploadSecurity } }
    )
    
    $testNum = 1
    foreach ($test in $tests) {
        Write-Host "  Test $testNum`: $($test.Name)" -ForegroundColor Yellow
        $testResult = & $test.Func
        $results.Tests += $testResult
        $results.Summary.TotalTests++
        if ($testResult.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($testResult.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($testResult.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        $testNum++
    }
    
    $results.EndTime = Get-Date
    $results.Duration = $results.EndTime - $results.StartTime
    
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║  Total: $($results.Summary.TotalTests)  Passed: $($results.Summary.PassedTests)  Failed: $($results.Summary.FailedTests)                                    ║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    
    return $results
}

$testResults = Start-PDPAComplianceTest
return $testResults
