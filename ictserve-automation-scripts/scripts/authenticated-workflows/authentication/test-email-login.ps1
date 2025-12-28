#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Test Email/Username Login - Authenticated User Workflow
.DESCRIPTION
    Tests the email and username login functionality for authenticated users.
    Validates multiple authentication methods and session creation.
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
    Name = "Test Email/Username Login"
    Category = "Authenticated Workflows - Authentication"
    Requirements = @("2.1", "2.3", "2.4")
    ExpectedDuration = 60
}

$TestCredentials = @{
    Email = "test.user@motac.gov.my"
    Username = "testuser"
    Password = "TestPassword123!"
}

function Test-EmailUsernameLogin {
    param([string]$ExecutionMode)
    
    $result = Initialize-TestResult -TestName $TestConfig.Name -Category $TestConfig.Category
    $result.LoginTests = @()
    
    try {
        Write-TestStep "Initializing browser automation" -Mode $ExecutionMode
        $driver = Initialize-WebDriver -Mode $ExecutionMode
        
        $baseUrl = Get-ConfigValue -Key "BaseUrl"
        
        # Test 1: Login with email
        Write-TestStep "Test 1: Login with email address" -Mode $ExecutionMode
        
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/login" -Mode $ExecutionMode
        Wait-ForElement -Driver $driver -Selector "form" -Timeout 10
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Testing login with email address" -Duration 2000
        }
        
        # Fill login form with email
        Fill-FormField -Driver $driver -Selector "#email, input[name='email']" -Value $TestCredentials.Email -Mode $ExecutionMode -Label "Email"
        Fill-FormField -Driver $driver -Selector "#password, input[name='password']" -Value $TestCredentials.Password -Mode $ExecutionMode -Label "Password"
        
        Take-Screenshot -Driver $driver -Name "login-email-filled" -Mode $ExecutionMode
        
        # Submit login
        $loginButton = Find-Element -Driver $driver -Selector "button[type='submit'], .btn-login"
        Click-Element -Driver $driver -Element $loginButton -Mode $ExecutionMode
        
        # Wait for redirect to dashboard
        $dashboardElement = Wait-ForElement -Driver $driver -Selector ".dashboard, [data-page='dashboard'], .user-menu" -Timeout 15 -Required $false
        
        if ($dashboardElement) {
            Write-TestOutput "Email login successful" -Type "Success"
            $result.LoginTests += @{ Method = "Email"; Status = "Passed" }
            
            # Verify session was created
            $sessionCookie = Get-Cookie -Driver $driver -Name "laravel_session"
            if ($sessionCookie) {
                Write-TestOutput "Session cookie created" -Type "Info"
            }
            
            # Logout for next test
            Write-TestStep "Logging out for next test" -Mode $ExecutionMode
            Invoke-Logout -Driver $driver -Mode $ExecutionMode
        } else {
            # Check for error message
            $errorElement = Find-Element -Driver $driver -Selector ".alert-danger, .error-message" -Required $false
            if ($errorElement) {
                $errorText = Get-ElementText -Element $errorElement
                Write-TestOutput "Email login failed: $errorText" -Type "Warning"
            }
            $result.LoginTests += @{ Method = "Email"; Status = "Failed" }
        }
        
        # Test 2: Login with username
        Write-TestStep "Test 2: Login with username" -Mode $ExecutionMode
        
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/login" -Mode $ExecutionMode
        Wait-ForElement -Driver $driver -Selector "form" -Timeout 10
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Testing login with username" -Duration 2000
        }
        
        # Fill login form with username
        Fill-FormField -Driver $driver -Selector "#email, input[name='email']" -Value $TestCredentials.Username -Mode $ExecutionMode -Label "Username"
        Fill-FormField -Driver $driver -Selector "#password, input[name='password']" -Value $TestCredentials.Password -Mode $ExecutionMode -Label "Password"
        
        $loginButton = Find-Element -Driver $driver -Selector "button[type='submit'], .btn-login"
        Click-Element -Driver $driver -Element $loginButton -Mode $ExecutionMode
        
        $dashboardElement = Wait-ForElement -Driver $driver -Selector ".dashboard, [data-page='dashboard'], .user-menu" -Timeout 15 -Required $false
        
        if ($dashboardElement) {
            Write-TestOutput "Username login successful" -Type "Success"
            $result.LoginTests += @{ Method = "Username"; Status = "Passed" }
            Invoke-Logout -Driver $driver -Mode $ExecutionMode
        } else {
            $result.LoginTests += @{ Method = "Username"; Status = "Failed" }
        }
        
        # Test 3: Invalid credentials
        Write-TestStep "Test 3: Testing invalid credentials" -Mode $ExecutionMode
        
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/login" -Mode $ExecutionMode
        Wait-ForElement -Driver $driver -Selector "form" -Timeout 10
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Testing invalid credentials handling" -Duration 2000
        }
        
        Fill-FormField -Driver $driver -Selector "#email, input[name='email']" -Value "invalid@motac.gov.my" -Mode $ExecutionMode
        Fill-FormField -Driver $driver -Selector "#password, input[name='password']" -Value "wrongpassword" -Mode $ExecutionMode
        
        $loginButton = Find-Element -Driver $driver -Selector "button[type='submit'], .btn-login"
        Click-Element -Driver $driver -Element $loginButton -Mode $ExecutionMode
        
        # Should show error message
        $errorElement = Wait-ForElement -Driver $driver -Selector ".alert-danger, .error-message, .invalid-feedback" -Timeout 10 -Required $false
        
        if ($errorElement) {
            Write-TestOutput "Invalid credentials correctly rejected" -Type "Success"
            $result.LoginTests += @{ Method = "InvalidCredentials"; Status = "Passed" }
            
            if ($ExecutionMode -eq 'Demo') {
                Highlight-Element -Driver $driver -Element $errorElement -Color "red" -Mode $ExecutionMode
                Show-Annotation -Text "System correctly rejects invalid credentials" -Duration 2000
            }
        } else {
            $result.LoginTests += @{ Method = "InvalidCredentials"; Status = "Failed" }
        }
        
        Take-Screenshot -Driver $driver -Name "login-invalid-credentials" -Mode $ExecutionMode
        
        # Test 4: Verify API authentication
        Write-TestStep "Test 4: Verifying API authentication" -Mode $ExecutionMode
        
        $apiResponse = Invoke-ApiRequest -Endpoint "/api/auth/login" -Method "POST" -Body @{
            email = $TestCredentials.Email
            password = $TestCredentials.Password
        }
        
        if ($apiResponse.success -and $apiResponse.data.token) {
            Write-TestOutput "API authentication successful" -Type "Success"
            $result.LoginTests += @{ Method = "API"; Status = "Passed" }
        } else {
            $result.LoginTests += @{ Method = "API"; Status = "Failed" }
        }
        
        if ($ExecutionMode -eq 'Interactive') {
            Pause-ForExplanation -Message "Login supports both email and username authentication"
        }
        
        # Calculate overall result
        $passedTests = ($result.LoginTests | Where-Object { $_.Status -eq "Passed" }).Count
        $totalTests = $result.LoginTests.Count
        
        if ($passedTests -eq $totalTests) {
            $result.Status = "Passed"
            Write-TestOutput "All login tests passed ($passedTests/$totalTests)" -Type "Success"
        } elseif ($passedTests -gt 0) {
            $result.Status = "Partial"
            Write-TestOutput "Login tests: $passedTests/$totalTests passed" -Type "Warning"
        } else {
            $result.Status = "Failed"
            Write-TestOutput "All login tests failed" -Type "Error"
        }
        
    } catch {
        $result.Status = "Failed"
        $result.ErrorMessage = $_.Exception.Message
        Write-TestOutput "Test failed: $($_.Exception.Message)" -Type "Error"
        
        if ($driver) {
            Take-Screenshot -Driver $driver -Name "login-test-failure" -Mode $ExecutionMode
        }
    } finally {
        if ($driver) { Close-WebDriver -Driver $driver }
        $result.EndTime = Get-Date
        $result.Duration = ($result.EndTime - $result.StartTime).TotalSeconds
        Save-TestResult -Result $result
    }
    
    return $result
}

$testResult = Test-EmailUsernameLogin -ExecutionMode $Mode
return $testResult
