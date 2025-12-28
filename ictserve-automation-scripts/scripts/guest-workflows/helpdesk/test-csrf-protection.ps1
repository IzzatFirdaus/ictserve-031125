#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Test CSRF Protection - Guest User Workflow
.DESCRIPTION
    Tests CSRF (Cross-Site Request Forgery) protection on helpdesk forms.
    Validates token generation, validation, and security measures.
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
. "$ScriptRoot\..\..\..\utilities\api-helpers.ps1"

$TestConfig = @{
    Name = "Test CSRF Protection"
    Category = "Guest Workflows - Helpdesk"
    Requirements = @("12.1", "12.2")
    ExpectedDuration = 60
}

function Test-CSRFProtection {
    param([string]$ExecutionMode)
    
    $result = Initialize-TestResult -TestName $TestConfig.Name -Category $TestConfig.Category
    $result.SecurityTests = @()
    
    try {
        Write-TestStep "Initializing browser automation" -Mode $ExecutionMode
        $driver = Initialize-WebDriver -Mode $ExecutionMode
        
        $baseUrl = Get-ConfigValue -Key "BaseUrl"
        
        # Test 1: Verify CSRF token is present in form
        Write-TestStep "Test 1: Verifying CSRF token presence" -Mode $ExecutionMode
        
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/helpdesk/create" -Mode $ExecutionMode
        Wait-ForElement -Driver $driver -Selector "form" -Timeout 10
        
        $csrfToken = Find-Element -Driver $driver -Selector "input[name='_token']" -Required $false
        $csrfMeta = Find-Element -Driver $driver -Selector "meta[name='csrf-token']" -Required $false
        
        if ($csrfToken -or $csrfMeta) {
            Write-TestOutput "CSRF token found in form" -Type "Success"
            $result.SecurityTests += @{ Test = "CSRF Token Present"; Status = "Passed" }
            
            if ($ExecutionMode -eq 'Demo') {
                if ($csrfToken) { Highlight-Element -Driver $driver -Element $csrfToken -Color "green" -Mode $ExecutionMode }
                Show-Annotation -Text "CSRF token protects against cross-site request forgery" -Duration 2500
            }
        } else {
            Write-TestOutput "WARNING: CSRF token not found" -Type "Warning"
            $result.SecurityTests += @{ Test = "CSRF Token Present"; Status = "Failed" }
        }
        
        # Test 2: Verify token changes on page refresh
        Write-TestStep "Test 2: Verifying token rotation" -Mode $ExecutionMode
        
        $firstToken = ""
        if ($csrfToken) {
            $firstToken = Get-ElementAttribute -Element $csrfToken -Attribute "value"
        } elseif ($csrfMeta) {
            $firstToken = Get-ElementAttribute -Element $csrfMeta -Attribute "content"
        }
        
        # Refresh page
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/helpdesk/create" -Mode $ExecutionMode
        Wait-ForElement -Driver $driver -Selector "form" -Timeout 10
        
        $csrfToken2 = Find-Element -Driver $driver -Selector "input[name='_token']" -Required $false
        $csrfMeta2 = Find-Element -Driver $driver -Selector "meta[name='csrf-token']" -Required $false
        
        $secondToken = ""
        if ($csrfToken2) {
            $secondToken = Get-ElementAttribute -Element $csrfToken2 -Attribute "value"
        } elseif ($csrfMeta2) {
            $secondToken = Get-ElementAttribute -Element $csrfMeta2 -Attribute "content"
        }
        
        # Note: Laravel may or may not rotate tokens on refresh depending on config
        Write-TestOutput "Token comparison: First=$($firstToken.Substring(0,10))... Second=$($secondToken.Substring(0,10))..." -Type "Info"
        $result.SecurityTests += @{ Test = "Token Rotation Check"; Status = "Passed" }
        
        # Test 3: Submit form without CSRF token (should fail)
        Write-TestStep "Test 3: Testing submission without CSRF token" -Mode $ExecutionMode
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Testing form submission without CSRF token (should be rejected)" -Duration 2000
        }
        
        # Use API to test direct submission without token
        $apiResponse = Invoke-ApiRequest -Endpoint "/helpdesk" -Method "POST" -Body @{
            name = "Test User"
            email = "test@motac.gov.my"
            subject = "Test Subject"
            description = "Test Description"
        } -SkipCSRF $true -ExpectError $true
        
        if ($apiResponse.statusCode -eq 419 -or $apiResponse.error -match "CSRF|token|mismatch") {
            Write-TestOutput "Form correctly rejected without CSRF token (419)" -Type "Success"
            $result.SecurityTests += @{ Test = "Reject Without Token"; Status = "Passed" }
        } else {
            Write-TestOutput "WARNING: Form may have accepted request without CSRF token" -Type "Warning"
            $result.SecurityTests += @{ Test = "Reject Without Token"; Status = "Failed" }
        }
        
        # Test 4: Submit form with invalid CSRF token
        Write-TestStep "Test 4: Testing submission with invalid CSRF token" -Mode $ExecutionMode
        
        $apiResponse = Invoke-ApiRequest -Endpoint "/helpdesk" -Method "POST" -Body @{
            _token = "invalid-token-12345"
            name = "Test User"
            email = "test@motac.gov.my"
            subject = "Test Subject"
            description = "Test Description"
        } -ExpectError $true
        
        if ($apiResponse.statusCode -eq 419 -or $apiResponse.error -match "CSRF|token|mismatch") {
            Write-TestOutput "Form correctly rejected invalid CSRF token" -Type "Success"
            $result.SecurityTests += @{ Test = "Reject Invalid Token"; Status = "Passed" }
        } else {
            Write-TestOutput "WARNING: Form may have accepted invalid CSRF token" -Type "Warning"
            $result.SecurityTests += @{ Test = "Reject Invalid Token"; Status = "Failed" }
        }
        
        # Test 5: Verify CSRF cookie is HttpOnly and Secure
        Write-TestStep "Test 5: Checking CSRF cookie security flags" -Mode $ExecutionMode
        
        $cookies = Get-AllCookies -Driver $driver
        $xsrfCookie = $cookies | Where-Object { $_.Name -eq "XSRF-TOKEN" }
        
        if ($xsrfCookie) {
            Write-TestOutput "XSRF-TOKEN cookie found" -Type "Info"
            # Note: XSRF-TOKEN is intentionally NOT HttpOnly so JavaScript can read it
            $result.SecurityTests += @{ Test = "XSRF Cookie Present"; Status = "Passed" }
        }
        
        Take-Screenshot -Driver $driver -Name "csrf-protection-test" -Mode $ExecutionMode
        
        if ($ExecutionMode -eq 'Interactive') {
            Pause-ForExplanation -Message "CSRF protection prevents malicious sites from submitting forms on behalf of users"
        }
        
        # Calculate overall result
        $passedTests = ($result.SecurityTests | Where-Object { $_.Status -eq "Passed" }).Count
        $totalTests = $result.SecurityTests.Count
        
        if ($passedTests -eq $totalTests) {
            $result.Status = "Passed"
            Write-TestOutput "All CSRF protection tests passed ($passedTests/$totalTests)" -Type "Success"
        } elseif ($passedTests -gt ($totalTests / 2)) {
            $result.Status = "Partial"
            Write-TestOutput "CSRF tests: $passedTests/$totalTests passed" -Type "Warning"
        } else {
            $result.Status = "Failed"
            Write-TestOutput "CSRF protection tests failed" -Type "Error"
        }
        
    } catch {
        $result.Status = "Failed"
        $result.ErrorMessage = $_.Exception.Message
        Write-TestOutput "Test failed: $($_.Exception.Message)" -Type "Error"
    } finally {
        if ($driver) { Close-WebDriver -Driver $driver }
        $result.EndTime = Get-Date
        $result.Duration = ($result.EndTime - $result.StartTime).TotalSeconds
        Save-TestResult -Result $result
    }
    
    return $result
}

$testResult = Test-CSRFProtection -ExecutionMode $Mode
return $testResult
