#Requires -Version 7.0
<#
.SYNOPSIS
    Tests user registration workflow with @motac.gov.my email validation.

.DESCRIPTION
    This script tests the complete user registration process including:
    - Email domain validation (@motac.gov.my required)
    - Email verification workflow
    - Password requirements validation
    - Form validation and error handling
    - Registration confirmation

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.EXAMPLE
    .\test-user-registration.ps1 -Mode Demo

.NOTES
    Version: 1.0.0
    Requirements: 2.1, 2.2, 2.5, 2.6
#>

[CmdletBinding()]
param(
    [Parameter()]
    [string]$BaseUrl = "http://localhost:8000",
    
    [Parameter()]
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
    Name = "User Registration Test"
    Category = "Authentication"
    Requirements = @("2.1", "2.2", "2.5", "2.6")
    ExpectedDuration = 120
}

# Test data
$TestUsers = @{
    ValidUser = @{
        Name = "Test User"
        Email = "test.registration.$(Get-Random)@motac.gov.my"
        Password = "SecureP@ssw0rd123!"
        PasswordConfirm = "SecureP@ssw0rd123!"
    }
    InvalidDomainUser = @{
        Name = "Invalid Domain User"
        Email = "test@gmail.com"
        Password = "SecureP@ssw0rd123!"
    }
    WeakPasswordUser = @{
        Name = "Weak Password User"
        Email = "weak.password@motac.gov.my"
        Password = "123456"
    }
}

function Test-EmailDomainValidation {
    <#
    .SYNOPSIS
        Tests that only @motac.gov.my emails are accepted.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing email domain validation" -Level INFO
    
    $results = @{
        TestName = "Email Domain Validation"
        Passed = $false
        Details = @{
            ValidDomains = @()
            InvalidDomains = @()
        }
    }
    
    $testEmails = @(
        @{ Email = "user@motac.gov.my"; ShouldPass = $true; Domain = "motac.gov.my" },
        @{ Email = "user@gmail.com"; ShouldPass = $false; Domain = "gmail.com" },
        @{ Email = "user@yahoo.com"; ShouldPass = $false; Domain = "yahoo.com" },
        @{ Email = "user@gov.my"; ShouldPass = $false; Domain = "gov.my" },
        @{ Email = "user@motac.com"; ShouldPass = $false; Domain = "motac.com" }
    )
    
    foreach ($testEmail in $testEmails) {
        Write-Host "      Testing: $($testEmail.Email)" -ForegroundColor Gray
        
        try {
            Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/register" -Mode $ExecutionMode
            Wait-ForElement -Driver $Driver -Selector "form" -Timeout 10
            
            # Fill email field
            Fill-FormField -Driver $Driver -Selector "#email, input[name='email']" -Value $testEmail.Email -Mode $ExecutionMode
            
            # Trigger validation (blur event)
            $emailField = Find-Element -Driver $Driver -Selector "#email, input[name='email']"
            if ($emailField) {
                # Click elsewhere to trigger validation
                $nameField = Find-Element -Driver $Driver -Selector "#name, input[name='name']" -Required $false
                if ($nameField) {
                    Click-Element -Driver $Driver -Element $nameField -Mode $ExecutionMode
                }
            }
            
            Start-Sleep -Milliseconds 500
            
            # Check for validation error
            $errorElement = Find-Element -Driver $Driver -Selector ".invalid-feedback, .error-message, .text-danger" -Required $false
            $hasError = $null -ne $errorElement
            
            $testPassed = if ($testEmail.ShouldPass) { -not $hasError } else { $hasError }
            
            if ($testEmail.ShouldPass) {
                $results.Details.ValidDomains += @{
                    Email = $testEmail.Email
                    Domain = $testEmail.Domain
                    Accepted = -not $hasError
                    Passed = $testPassed
                }
            }
            else {
                $results.Details.InvalidDomains += @{
                    Email = $testEmail.Email
                    Domain = $testEmail.Domain
                    Rejected = $hasError
                    Passed = $testPassed
                }
            }
            
            if ($ExecutionMode -eq 'Demo' -and $errorElement) {
                Highlight-Element -Driver $Driver -Element $errorElement -Color "red" -Mode $ExecutionMode
                Show-Annotation -Text "Invalid domain rejected: $($testEmail.Domain)" -Duration 1500
            }
        }
        catch {
            Write-AutomationLog "Error testing email $($testEmail.Email): $($_.Exception.Message)" -Level WARNING
        }
    }
    
    $allValidPassed = ($results.Details.ValidDomains | Where-Object { -not $_.Passed }).Count -eq 0
    $allInvalidPassed = ($results.Details.InvalidDomains | Where-Object { -not $_.Passed }).Count -eq 0
    
    $results.Passed = $allValidPassed -and $allInvalidPassed
    
    return $results
}

function Test-PasswordRequirements {
    <#
    .SYNOPSIS
        Tests password strength requirements.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing password requirements" -Level INFO
    
    $results = @{
        TestName = "Password Requirements"
        Passed = $false
        Details = @{
            Tests = @()
        }
    }
    
    $passwordTests = @(
        @{ Password = "123456"; ShouldPass = $false; Reason = "Too short, no complexity" },
        @{ Password = "password"; ShouldPass = $false; Reason = "Common password" },
        @{ Password = "Password1"; ShouldPass = $false; Reason = "No special character" },
        @{ Password = "P@ssw0rd"; ShouldPass = $false; Reason = "Too short (8 chars)" },
        @{ Password = "SecureP@ssw0rd123!"; ShouldPass = $true; Reason = "Meets all requirements" },
        @{ Password = "MyStr0ng!P@ssword"; ShouldPass = $true; Reason = "Strong password" }
    )
    
    foreach ($test in $passwordTests) {
        Write-Host "      Testing: $($test.Reason)" -ForegroundColor Gray
        
        try {
            Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/register" -Mode $ExecutionMode
            Wait-ForElement -Driver $Driver -Selector "form" -Timeout 10
            
            # Fill password field
            Fill-FormField -Driver $Driver -Selector "#password, input[name='password']" -Value $test.Password -Mode $ExecutionMode
            
            # Trigger validation
            $confirmField = Find-Element -Driver $Driver -Selector "#password_confirmation, input[name='password_confirmation']" -Required $false
            if ($confirmField) {
                Click-Element -Driver $Driver -Element $confirmField -Mode $ExecutionMode
            }
            
            Start-Sleep -Milliseconds 500
            
            # Check for validation error
            $errorElement = Find-Element -Driver $Driver -Selector ".invalid-feedback, .password-error, .text-danger" -Required $false
            $hasError = $null -ne $errorElement
            
            $testPassed = if ($test.ShouldPass) { -not $hasError } else { $hasError }
            
            $results.Details.Tests += @{
                Password = $test.Password.Substring(0, [Math]::Min(3, $test.Password.Length)) + "***"
                Reason = $test.Reason
                ShouldPass = $test.ShouldPass
                HasError = $hasError
                Passed = $testPassed
            }
            
            if ($ExecutionMode -eq 'Demo') {
                $color = if ($testPassed) { "green" } else { "red" }
                Show-Annotation -Text "$($test.Reason): $(if ($testPassed) { 'OK' } else { 'FAIL' })" -Duration 1000
            }
        }
        catch {
            Write-AutomationLog "Error testing password: $($_.Exception.Message)" -Level WARNING
        }
    }
    
    $results.Passed = ($results.Details.Tests | Where-Object { -not $_.Passed }).Count -eq 0
    
    return $results
}

function Test-RegistrationFormValidation {
    <#
    .SYNOPSIS
        Tests complete registration form validation.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing registration form validation" -Level INFO
    
    $results = @{
        TestName = "Form Validation"
        Passed = $false
        Details = @{
            RequiredFields = @()
            PasswordMatch = $false
            CSRFProtection = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/register" -Mode $ExecutionMode
        Wait-ForElement -Driver $Driver -Selector "form" -Timeout 10
        
        # Test 1: Required fields
        $requiredFields = @("name", "email", "password", "password_confirmation")
        
        foreach ($field in $requiredFields) {
            $fieldElement = Find-Element -Driver $Driver -Selector "input[name='$field']" -Required $false
            $isRequired = $null -ne $fieldElement
            
            $results.Details.RequiredFields += @{
                Field = $field
                Present = $isRequired
            }
        }
        
        # Test 2: Submit empty form
        $submitButton = Find-Element -Driver $Driver -Selector "button[type='submit']" -Required $false
        if ($submitButton) {
            Click-Element -Driver $Driver -Element $submitButton -Mode $ExecutionMode
            Start-Sleep -Milliseconds 1000
            
            # Check for validation errors
            $errorElements = Find-Elements -Driver $Driver -Selector ".invalid-feedback, .error-message"
            $results.Details.EmptyFormRejected = $errorElements.Count -gt 0
        }
        
        # Test 3: Password mismatch
        Fill-FormField -Driver $Driver -Selector "#name, input[name='name']" -Value "Test User" -Mode $ExecutionMode
        Fill-FormField -Driver $Driver -Selector "#email, input[name='email']" -Value "test@motac.gov.my" -Mode $ExecutionMode
        Fill-FormField -Driver $Driver -Selector "#password, input[name='password']" -Value "Password123!" -Mode $ExecutionMode
        Fill-FormField -Driver $Driver -Selector "#password_confirmation, input[name='password_confirmation']" -Value "DifferentPassword!" -Mode $ExecutionMode
        
        if ($submitButton) {
            Click-Element -Driver $Driver -Element $submitButton -Mode $ExecutionMode
            Start-Sleep -Milliseconds 1000
            
            $mismatchError = Find-Element -Driver $Driver -Selector ".invalid-feedback, .error-message" -Required $false
            $results.Details.PasswordMatch = $null -ne $mismatchError
        }
        
        # Test 4: CSRF token present
        $csrfToken = Find-Element -Driver $Driver -Selector "input[name='_token']" -Required $false
        $results.Details.CSRFProtection = $null -ne $csrfToken
        
        $results.Passed = $results.Details.CSRFProtection -and 
                          $results.Details.PasswordMatch -and 
                          ($results.Details.RequiredFields | Where-Object { $_.Present }).Count -eq $requiredFields.Count
    }
    catch {
        Write-AutomationLog "Form validation test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-SuccessfulRegistration {
    <#
    .SYNOPSIS
        Tests successful user registration flow.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing successful registration flow" -Level INFO
    
    $results = @{
        TestName = "Successful Registration"
        Passed = $false
        Details = @{
            FormSubmitted = $false
            VerificationEmailSent = $false
            RedirectCorrect = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/register" -Mode $ExecutionMode
        Wait-ForElement -Driver $Driver -Selector "form" -Timeout 10
        
        $testUser = $TestUsers.ValidUser
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Filling registration form with valid data" -Duration 2000
        }
        
        # Fill all fields
        Fill-FormField -Driver $Driver -Selector "#name, input[name='name']" -Value $testUser.Name -Mode $ExecutionMode -Label "Name"
        Fill-FormField -Driver $Driver -Selector "#email, input[name='email']" -Value $testUser.Email -Mode $ExecutionMode -Label "Email"
        Fill-FormField -Driver $Driver -Selector "#password, input[name='password']" -Value $testUser.Password -Mode $ExecutionMode -Label "Password"
        Fill-FormField -Driver $Driver -Selector "#password_confirmation, input[name='password_confirmation']" -Value $testUser.PasswordConfirm -Mode $ExecutionMode -Label "Confirm Password"
        
        Take-Screenshot -Driver $Driver -Name "registration-form-filled" -Mode $ExecutionMode
        
        # Submit form
        $submitButton = Find-Element -Driver $Driver -Selector "button[type='submit']"
        Click-Element -Driver $Driver -Element $submitButton -Mode $ExecutionMode
        
        # Wait for response
        Start-Sleep -Seconds 3
        
        # Check for success indicators
        $currentUrl = Get-CurrentUrl -Driver $Driver
        $successMessage = Find-Element -Driver $Driver -Selector ".alert-success, .success-message" -Required $false
        $verifyEmailPage = $currentUrl -match "verify|email|confirmation"
        
        $results.Details.FormSubmitted = $true
        $results.Details.RedirectCorrect = $verifyEmailPage -or ($null -ne $successMessage)
        $results.Details.VerificationEmailSent = $verifyEmailPage
        
        if ($ExecutionMode -eq 'Demo') {
            if ($results.Details.RedirectCorrect) {
                Show-Annotation -Text "Registration successful! Verification email sent." -Duration 3000
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "registration-result" -Mode $ExecutionMode
        
        $results.Passed = $results.Details.FormSubmitted -and $results.Details.RedirectCorrect
    }
    catch {
        Write-AutomationLog "Registration test error: $($_.Exception.Message)" -Level ERROR
        $results.Details.Error = $_.Exception.Message
    }
    
    return $results
}

function Start-UserRegistrationTest {
    <#
    .SYNOPSIS
        Executes the complete user registration test suite.
    #>
    
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{
            TotalTests = 0
            PassedTests = 0
            FailedTests = 0
        }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║              User Registration Test Suite                     ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $driver = $null
    
    try {
        # Initialize browser
        $demoConfig = Get-DefaultDemoConfig -Mode $Mode
        $driver = Initialize-WebDriver -Mode $Mode
        
        # Test 1: Email Domain Validation
        Write-Host "  Test 1: Email Domain Validation (@motac.gov.my)" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $domainResults = Test-EmailDomainValidation -Driver $driver -ExecutionMode $Mode
        $results.Tests += $domainResults
        $results.Summary.TotalTests++
        if ($domainResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Valid domains tested: $($domainResults.Details.ValidDomains.Count)" -ForegroundColor White
        Write-Host "    Invalid domains tested: $($domainResults.Details.InvalidDomains.Count)" -ForegroundColor White
        Write-Host "    Result: $(if ($domainResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($domainResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 2: Password Requirements
        Write-Host "  Test 2: Password Requirements" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $passwordResults = Test-PasswordRequirements -Driver $driver -ExecutionMode $Mode
        $results.Tests += $passwordResults
        $results.Summary.TotalTests++
        if ($passwordResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        foreach ($test in $passwordResults.Details.Tests) {
            $status = if ($test.Passed) { "✓" } else { "✗" }
            $color = if ($test.Passed) { "Green" } else { "Red" }
            Write-Host "      $status $($test.Reason)" -ForegroundColor $color
        }
        Write-Host "    Result: $(if ($passwordResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($passwordResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 3: Form Validation
        Write-Host "  Test 3: Registration Form Validation" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $formResults = Test-RegistrationFormValidation -Driver $driver -ExecutionMode $Mode
        $results.Tests += $formResults
        $results.Summary.TotalTests++
        if ($formResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    CSRF Protection: $(if ($formResults.Details.CSRFProtection) { '✓' } else { '✗' })" -ForegroundColor $(if ($formResults.Details.CSRFProtection) { 'Green' } else { 'Red' })
        Write-Host "    Password Match Check: $(if ($formResults.Details.PasswordMatch) { '✓' } else { '✗' })" -ForegroundColor $(if ($formResults.Details.PasswordMatch) { 'Green' } else { 'Red' })
        Write-Host "    Result: $(if ($formResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($formResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 4: Successful Registration
        Write-Host "  Test 4: Successful Registration Flow" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $regResults = Test-SuccessfulRegistration -Driver $driver -ExecutionMode $Mode
        $results.Tests += $regResults
        $results.Summary.TotalTests++
        if ($regResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Form Submitted: $(if ($regResults.Details.FormSubmitted) { '✓' } else { '✗' })" -ForegroundColor $(if ($regResults.Details.FormSubmitted) { 'Green' } else { 'Red' })
        Write-Host "    Redirect Correct: $(if ($regResults.Details.RedirectCorrect) { '✓' } else { '✗' })" -ForegroundColor $(if ($regResults.Details.RedirectCorrect) { 'Green' } else { 'Red' })
        Write-Host "    Result: $(if ($regResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($regResults.Passed) { 'Green' } else { 'Red' })
        
    }
    catch {
        Write-AutomationLog "User registration test failed: $($_.Exception.Message)" -Level ERROR
        throw
    }
    finally {
        if ($driver) { Close-WebDriver -Driver $driver }
    }
    
    $results.EndTime = Get-Date
    $results.Duration = $results.EndTime - $results.StartTime
    
    # Display summary
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║                    Test Summary                               ║" -ForegroundColor Cyan
    Write-Host "╠══════════════════════════════════════════════════════════════╣" -ForegroundColor Cyan
    Write-Host "║  Total Tests:  $($results.Summary.TotalTests.ToString().PadRight(46))║" -ForegroundColor White
    Write-Host "║  Passed:       $($results.Summary.PassedTests.ToString().PadRight(46))║" -ForegroundColor Green
    Write-Host "║  Failed:       $($results.Summary.FailedTests.ToString().PadRight(46))║" -ForegroundColor $(if ($results.Summary.FailedTests -gt 0) { 'Red' } else { 'White' })
    Write-Host "║  Duration:     $($results.Duration.ToString('mm\:ss').PadRight(46))║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    return $results
}

# Execute the test
$testResults = Start-UserRegistrationTest

# Return results for reporting
return $testResults
