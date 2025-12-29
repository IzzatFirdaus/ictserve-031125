#Requires -Version 7.0
<#
.SYNOPSIS
    Test password validation rules for authenticated users.

.DESCRIPTION
    This script tests various password validation scenarios including:
    - Minimum length requirements
    - Character complexity rules
    - Common password rejection
    - Password strength indicators

.PARAMETER Mode
    Execution mode: Headless, Visual, Demo, Interactive, Recording

.PARAMETER Environment
    Target environment: development, testing, staging, production
#>

[CmdletBinding()]
param(
    [Parameter()]
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual',
    
    [Parameter()]
    [ValidateSet('development', 'testing', 'staging', 'production')]
    [string]$Environment = 'testing'
)

# Import common functions
$ScriptRoot = $PSScriptRoot
. (Join-Path $ScriptRoot "..\..\..\utilities\common-functions.ps1")

# Test configuration
$TestConfig = @{
    Name = "Test Password Validation"
    Category = "Authenticated Workflows - Authentication"
    Description = "Tests password validation rules and strength requirements"
}

# Test data for password validation scenarios
$PasswordScenarios = @(
    @{
        Name = "Too Short Password"
        Password = "123"
        ExpectedResult = "Invalid"
        ExpectedError = "Password must be at least 8 characters"
    },
    @{
        Name = "No Uppercase"
        Password = "password123!"
        ExpectedResult = "Invalid"
        ExpectedError = "Password must contain uppercase letter"
    },
    @{
        Name = "No Lowercase"
        Password = "PASSWORD123!"
        ExpectedResult = "Invalid"
        ExpectedError = "Password must contain lowercase letter"
    },
    @{
        Name = "No Numbers"
        Password = "Password!"
        ExpectedResult = "Invalid"
        ExpectedError = "Password must contain number"
    },
    @{
        Name = "No Special Characters"
        Password = "Password123"
        ExpectedResult = "Invalid"
        ExpectedError = "Password must contain special character"
    },
    @{
        Name = "Common Password"
        Password = "Password123!"
        ExpectedResult = "Invalid"
        ExpectedError = "Password is too common"
    },
    @{
        Name = "Valid Strong Password"
        Password = "MyStr0ng!P@ssw0rd2024"
        ExpectedResult = "Valid"
        ExpectedError = ""
    }
)

function Test-PasswordValidation {
    param([string]$ExecutionMode)
    
    $result = Initialize-TestResult -TestName $TestConfig.Name -Category $TestConfig.Category
    $result.ValidationTests = @()
    
    try {
        Write-TestStep "Initializing browser automation" -Mode $ExecutionMode
        $driver = Initialize-WebDriver -Mode $ExecutionMode
        
        $baseUrl = Get-ConfigValue -Key "BaseUrl"
        
        foreach ($scenario in $PasswordScenarios) {
            Write-TestStep "Testing scenario: $($scenario.Name)" -Mode $ExecutionMode
            
            $scenarioResult = @{
                Name = $scenario.Name
                Password = $scenario.Password
                ExpectedResult = $scenario.ExpectedResult
                ActualResult = ""
                ErrorMessage = ""
                Status = "Running"
                Screenshots = @()
            }
            
            try {
                # Navigate to password change page
                Navigate-ToUrl -Driver $driver -Url "$baseUrl/profile/password" -Mode $ExecutionMode
                
                if ($ExecutionMode -eq 'Demo') {
                    Show-Annotation -Text "Testing password: $($scenario.Name)" -Duration 2000
                }
                
                # Fill current password
                Fill-FormField -Driver $driver -Selector "#current_password" -Value "CurrentPassword123!" -Mode $ExecutionMode -Label "Current Password"
                
                # Fill new password
                Fill-FormField -Driver $driver -Selector "#password" -Value $scenario.Password -Mode $ExecutionMode -Label "New Password"
                
                # Fill password confirmation
                Fill-FormField -Driver $driver -Selector "#password_confirmation" -Value $scenario.Password -Mode $ExecutionMode -Label "Confirm Password"
                
                # Take screenshot before submission
                $screenshotPath = Take-Screenshot -Driver $driver -Name "password-validation-$($scenario.Name.Replace(' ', '-'))" -Mode $ExecutionMode
                $scenarioResult.Screenshots += $screenshotPath
                
                # Submit form
                $submitButton = Find-Element -Driver $driver -Selector "button[type='submit'], .btn-update-password"
                Click-Element -Driver $driver -Element $submitButton -Mode $ExecutionMode
                
                # Wait for response
                Start-Sleep -Milliseconds 1000
                
                # Check for validation errors
                $errorElements = Wait-ForElement -Driver $driver -Selector ".alert-danger, .error-message, .invalid-feedback" -Timeout 5 -Required $false
                
                if ($errorElements) {
                    $scenarioResult.ActualResult = "Invalid"
                    $scenarioResult.ErrorMessage = "Validation error found"
                } else {
                    $successElement = Wait-ForElement -Driver $driver -Selector ".alert-success, .success-message" -Timeout 5 -Required $false
                    if ($successElement) {
                        $scenarioResult.ActualResult = "Valid"
                    } else {
                        $scenarioResult.ActualResult = "Unknown"
                    }
                }
                
                # Verify result matches expectation
                if ($scenarioResult.ActualResult -eq $scenario.ExpectedResult) {
                    $scenarioResult.Status = "Passed"
                    Write-TestOutput "✓ $($scenario.Name): Expected $($scenario.ExpectedResult), Got $($scenarioResult.ActualResult)" -Type Success
                } else {
                    $scenarioResult.Status = "Failed"
                    Write-TestOutput "✗ $($scenario.Name): Expected $($scenario.ExpectedResult), Got $($scenarioResult.ActualResult)" -Type Error
                }
                
                if ($ExecutionMode -eq 'Interactive') {
                    Pause-ForExplanation -Message "Password validation result: $($scenarioResult.Status)"
                }
                
            } catch {
                $scenarioResult.Status = "Failed"
                $scenarioResult.ErrorMessage = $_.Exception.Message
                Write-TestOutput "✗ $($scenario.Name): $($_.Exception.Message)" -Type Error
            }
            
            $result.ValidationTests += $scenarioResult
        }
        
        # Summary
        $passed = ($result.ValidationTests | Where-Object { $_.Status -eq 'Passed' }).Count
        $total = $result.ValidationTests.Count
        
        if ($passed -eq $total) {
            $result.Status = "Passed"
            Write-TestOutput "All password validation tests passed ($passed/$total)" -Type Success
        } else {
            $result.Status = "Failed"
            Write-TestOutput "Password validation tests: $passed/$total passed" -Type Warning
        }
        
    } catch {
        $result.Status = "Failed"
        $result.ErrorMessage = $_.Exception.Message
        $result.StackTrace = $_.ScriptStackTrace
        
        Write-TestOutput "Test failed: $($_.Exception.Message)" -Type Error
        
        # Capture failure screenshot
        if ($driver) {
            $failureScreenshot = Take-Screenshot -Driver $driver -Name "password-validation-failure" -Mode $ExecutionMode
            $result.Screenshots += $failureScreenshot
        }
    } finally {
        # Cleanup
        if ($driver) {
            Close-WebDriver -Driver $driver
        }
        
        $result.EndTime = Get-Date
        $result.Duration = ($result.EndTime - $result.StartTime).TotalSeconds
        
        # Save test result
        Save-TestResult -Result $result
    }
    
    return $result
}

# Execute test
$testResult = Test-PasswordValidation -ExecutionMode $Mode

# Return result for pipeline
return $testResult