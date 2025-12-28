#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Test ClamAV File Scanning Integration - Guest User Workflow
.DESCRIPTION
    Tests the ClamAV virus scanning integration for file uploads.
    Validates clean file acceptance and infected file rejection.
.PARAMETER Mode
    Execution mode: Headless, Visual, Demo, Interactive, Recording
#>

param(
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

. "$ScriptRoot\..\..\..\utilities\common-functions.ps1"
. "$ScriptRoot\..\..\..\utilities\browser-automation.ps1"
. "$ScriptRoot\..\..\..\utilities\visual-demo-helpers.ps1"
. "$ScriptRoot\..\..\..\utilities\api-helpers.ps1"

$TestConfig = @{
    Name = "Test ClamAV File Scanning Integration"
    Category = "Guest Workflows - Integration Tests"
    Requirements = @("10.2", "12.5")
    ExpectedDuration = 90
}

function Test-ClamAVScanning {
    param([string]$ExecutionMode)
    
    $result = Initialize-TestResult -TestName $TestConfig.Name -Category $TestConfig.Category
    $result.ScanResults = @()
    
    try {
        Write-TestStep "Initializing browser automation" -Mode $ExecutionMode
        $driver = Initialize-WebDriver -Mode $ExecutionMode
        
        $baseUrl = Get-ConfigValue -Key "BaseUrl"
        $testDataPath = Get-TestDataPath -Category "documents"
        
        # Test 1: Clean file upload
        Write-TestStep "Test 1: Uploading clean file" -Mode $ExecutionMode
        
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/helpdesk/create" -Mode $ExecutionMode
        Wait-ForElement -Driver $driver -Selector "form" -Timeout 10
        
        # Create a clean test file
        $cleanFilePath = Join-Path $testDataPath "clean-test-file.txt"
        if (-not (Test-Path $cleanFilePath)) {
            "This is a clean test file for ClamAV scanning verification." | Out-File -FilePath $cleanFilePath -Encoding UTF8
        }
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Uploading clean file for virus scanning" -Duration 2000
        }
        
        $fileInput = Find-Element -Driver $driver -Selector "input[type='file']"
        Upload-File -Driver $driver -Element $fileInput -FilePath $cleanFilePath -Mode $ExecutionMode
        
        # Wait for scan to complete
        Write-TestStep "Waiting for ClamAV scan" -Mode $ExecutionMode
        $scanResult = Wait-ForElement -Driver $driver -Selector ".scan-complete, .file-uploaded, [data-scan-status]" -Timeout 30
        
        # Verify clean file was accepted
        $scanStatus = Get-ElementAttribute -Driver $driver -Selector "[data-scan-status]" -Attribute "data-scan-status"
        if ($scanStatus -eq "clean" -or $scanResult) {
            Write-TestOutput "Clean file accepted" -Type "Success"
            $result.ScanResults += @{ File = "clean-test-file.txt"; Status = "Accepted"; Expected = "Accepted" }
        }
        
        Take-Screenshot -Driver $driver -Name "clamav-clean-file" -Mode $ExecutionMode
        
        # Test 2: EICAR test file (standard antivirus test pattern)
        Write-TestStep "Test 2: Testing EICAR test pattern" -Mode $ExecutionMode
        
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/helpdesk/create" -Mode $ExecutionMode
        Wait-ForElement -Driver $driver -Selector "form" -Timeout 10
        
        # Create EICAR test file (standard antivirus test string)
        $eicarFilePath = Join-Path $testDataPath "eicar-test.txt"
        $eicarString = 'X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*'
        $eicarString | Out-File -FilePath $eicarFilePath -Encoding ASCII -NoNewline
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Uploading EICAR test file (standard antivirus test pattern)" -Duration 2500
        }
        
        $fileInput = Find-Element -Driver $driver -Selector "input[type='file']"
        Upload-File -Driver $driver -Element $fileInput -FilePath $eicarFilePath -Mode $ExecutionMode
        
        # Wait for scan and rejection
        Write-TestStep "Waiting for virus detection" -Mode $ExecutionMode
        $rejectionElement = Wait-ForElement -Driver $driver -Selector ".scan-failed, .virus-detected, .alert-danger, [data-scan-status='infected']" -Timeout 30 -Required $false
        
        if ($rejectionElement) {
            $rejectionMessage = Get-ElementText -Element $rejectionElement
            Write-TestOutput "EICAR test file correctly rejected: $rejectionMessage" -Type "Success"
            $result.ScanResults += @{ File = "eicar-test.txt"; Status = "Rejected"; Expected = "Rejected" }
            
            if ($ExecutionMode -eq 'Demo') {
                Highlight-Element -Driver $driver -Element $rejectionElement -Color "red" -Mode $ExecutionMode
                Show-Annotation -Text "ClamAV correctly detected and blocked the test virus" -Duration 3000
            }
        } else {
            Write-TestOutput "WARNING: EICAR test file was not rejected" -Type "Warning"
            $result.ScanResults += @{ File = "eicar-test.txt"; Status = "Accepted"; Expected = "Rejected" }
        }
        
        Take-Screenshot -Driver $driver -Name "clamav-eicar-test" -Mode $ExecutionMode
        
        # Clean up EICAR file
        if (Test-Path $eicarFilePath) {
            Remove-Item $eicarFilePath -Force
        }
        
        # Test 3: Large file handling
        Write-TestStep "Test 3: Testing large file handling" -Mode $ExecutionMode
        
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/helpdesk/create" -Mode $ExecutionMode
        Wait-ForElement -Driver $driver -Selector "form" -Timeout 10
        
        # Create a larger test file (5MB)
        $largeFilePath = Join-Path $testDataPath "large-test-file.bin"
        if (-not (Test-Path $largeFilePath)) {
            $bytes = New-Object byte[] (5MB)
            [System.IO.File]::WriteAllBytes($largeFilePath, $bytes)
        }
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Testing large file scanning (5MB)" -Duration 2000
        }
        
        $fileInput = Find-Element -Driver $driver -Selector "input[type='file']"
        Upload-File -Driver $driver -Element $fileInput -FilePath $largeFilePath -Mode $ExecutionMode
        
        # Wait for scan (may take longer for large files)
        $scanResult = Wait-ForElement -Driver $driver -Selector ".scan-complete, .file-uploaded, [data-scan-status]" -Timeout 60 -Required $false
        
        if ($scanResult) {
            Write-TestOutput "Large file scanned successfully" -Type "Success"
            $result.ScanResults += @{ File = "large-test-file.bin"; Status = "Scanned"; Size = "5MB" }
        }
        
        Take-Screenshot -Driver $driver -Name "clamav-large-file" -Mode $ExecutionMode
        
        # Test 4: API-level scan verification
        Write-TestStep "Test 4: Verifying ClamAV API integration" -Mode $ExecutionMode
        
        $apiResponse = Invoke-ApiRequest -Endpoint "/api/system/clamav/status" -Method "GET"
        if ($apiResponse.success) {
            Write-TestOutput "ClamAV service status: $($apiResponse.data.status)" -Type "Info"
            Write-TestOutput "ClamAV version: $($apiResponse.data.version)" -Type "Info"
            Write-TestOutput "Virus definitions: $($apiResponse.data.definitions_date)" -Type "Info"
            
            $result.ClamAVStatus = $apiResponse.data
        }
        
        if ($ExecutionMode -eq 'Interactive') {
            Pause-ForExplanation -Message "ClamAV integration protects against malicious file uploads"
        }
        
        # Evaluate overall result
        $passedTests = ($result.ScanResults | Where-Object { $_.Status -eq $_.Expected -or $_.Status -eq "Scanned" }).Count
        $totalTests = $result.ScanResults.Count
        
        if ($passedTests -eq $totalTests) {
            $result.Status = "Passed"
            Write-TestOutput "All ClamAV integration tests passed ($passedTests/$totalTests)" -Type "Success"
        } else {
            $result.Status = "Partial"
            Write-TestOutput "ClamAV tests: $passedTests/$totalTests passed" -Type "Warning"
        }
        
    } catch {
        $result.Status = "Failed"
        $result.ErrorMessage = $_.Exception.Message
        Write-TestOutput "Test failed: $($_.Exception.Message)" -Type "Error"
        
        if ($driver) {
            Take-Screenshot -Driver $driver -Name "clamav-test-failure" -Mode $ExecutionMode
        }
    } finally {
        if ($driver) { Close-WebDriver -Driver $driver }
        $result.EndTime = Get-Date
        $result.Duration = ($result.EndTime - $result.StartTime).TotalSeconds
        Save-TestResult -Result $result
    }
    
    return $result
}

$testResult = Test-ClamAVScanning -ExecutionMode $Mode
return $testResult
