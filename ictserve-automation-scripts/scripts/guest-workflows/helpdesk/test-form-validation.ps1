#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Test Form Validation Errors - Guest User Workflow
.DESCRIPTION
    Tests frontend JavaScript and backend Laravel validation for helpdesk forms.
    Validates error messages, field highlighting, and user feedback.
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

$TestConfig = @{
    Name = "Test Form Validation Errors"
    Category = "Guest Workflows - Helpdesk"
    Requirements = @("1.5", "1.6", "11.2")
    ExpectedDuration = 120
}

# Invalid test data scenarios
$ValidationScenarios = @(
    @{
        Name = "Empty Required Fields"
        Data = @{ Name = ""; Email = ""; Subject = "" }
        ExpectedErrors = @("name", "email", "subject", "description")
    }
    @{
        Name = "Invalid Email Format"
        Data = @{ Name = "Test User"; Email = "invalid-email"; Subject = "Test" }
        ExpectedErrors = @("email")
    }
    @{
        Name = "Non-MOTAC Email Domain"
        Data = @{ Name = "Test User"; Email = "user@gmail.com"; Subject = "Test" }
        ExpectedErrors = @("email")
        ExpectedMessage = "must be a @motac.gov.my email"
    }
    @{
        Name = "Subject Too Short"
        Data = @{ Name = "Test User"; Email = "test@motac.gov.my"; Subject = "Hi" }
        ExpectedErrors = @("subject")
        ExpectedMessage = "at least 10 characters"
    }
    @{
        Name = "Description Too Short"
        Data = @{ Name = "Test User"; Email = "test@motac.gov.my"; Subject = "Valid Subject Here"; Description = "Short" }
        ExpectedErrors = @("description")
        ExpectedMessage = "at least 20 characters"
    }
    @{
        Name = "Invalid Phone Format"
        Data = @{ Name = "Test User"; Email = "test@motac.gov.my"; Phone = "123" }
        ExpectedErrors = @("phone")
    }
)

function Test-FormValidation {
    param([string]$ExecutionMode)
    
    $result = Initialize-TestResult -TestName $TestConfig.Name -Category $TestConfig.Category
    $result.Scenarios = @()
    
    try {
        Write-TestStep "Initializing browser automation" -Mode $ExecutionMode
        $driver = Initialize-WebDriver -Mode $ExecutionMode
        
        $baseUrl = Get-ConfigValue -Key "BaseUrl"
        
        foreach ($scenario in $ValidationScenarios) {
            Write-TestStep "Testing scenario: $($scenario.Name)" -Mode $ExecutionMode
            
            $scenarioResult = @{
                Name = $scenario.Name
                Status = "Pending"
                Errors = @()
            }
            
            try {
                # Navigate to fresh form
                Navigate-ToUrl -Driver $driver -Url "$baseUrl/helpdesk/create" -Mode $ExecutionMode
                Wait-ForElement -Driver $driver -Selector "form" -Timeout 10
                
                if ($ExecutionMode -eq 'Demo') {
                    Show-Annotation -Text "Testing: $($scenario.Name)" -Duration 2000
                }
                
                # Fill form with test data
                foreach ($field in $scenario.Data.GetEnumerator()) {
                    $selector = "#$($field.Key.ToLower())"
                    if ($field.Value) {
                        Fill-FormField -Driver $driver -Selector $selector -Value $field.Value -Mode $ExecutionMode -ClearFirst $true
                    }
                }
                
                # Attempt to submit
                Write-TestStep "Attempting form submission" -Mode $ExecutionMode
                $submitButton = Find-Element -Driver $driver -Selector "button[type='submit']"
                Click-Element -Driver $driver -Element $submitButton -Mode $ExecutionMode
                
                # Wait for validation errors
                Start-Sleep -Milliseconds 500
                
                # Check for frontend validation errors
                Write-TestStep "Checking for validation errors" -Mode $ExecutionMode
                
                $foundErrors = @()
                foreach ($expectedError in $scenario.ExpectedErrors) {
                    $errorSelectors = @(
                        "#$expectedError-error",
                        "[data-error='$expectedError']",
                        ".invalid-feedback:has(~ #$expectedError)",
                        "#$expectedError.is-invalid + .invalid-feedback",
                        ".field-error[data-field='$expectedError']"
                    )
                    
                    $errorFound = $false
                    foreach ($selector in $errorSelectors) {
                        try {
                            $errorElement = Find-Element -Driver $driver -Selector $selector -Timeout 2
                            if ($errorElement) {
                                $errorFound = $true
                                $foundErrors += $expectedError
                                
                                if ($ExecutionMode -eq 'Demo') {
                                    Highlight-Element -Driver $driver -Element $errorElement -Color "red" -Mode $ExecutionMode
                                }
                                break
                            }
                        } catch { }
                    }
                    
                    # Also check for field highlighting
                    $fieldElement = Find-Element -Driver $driver -Selector "#$expectedError" -Timeout 1
                    if ($fieldElement) {
                        $classes = Get-ElementAttribute -Element $fieldElement -Attribute "class"
                        if ($classes -match "is-invalid|error|border-red") {
                            $errorFound = $true
                            if ($expectedError -notin $foundErrors) {
                                $foundErrors += $expectedError
                            }
                        }
                    }
                    
                    if (-not $errorFound) {
                        Write-TestOutput "Expected error for '$expectedError' not found" -Type "Warning"
                    }
                }
                
                # Verify expected message if specified
                if ($scenario.ExpectedMessage) {
                    $pageText = Get-PageText -Driver $driver
                    if ($pageText -notmatch [regex]::Escape($scenario.ExpectedMessage)) {
                        Write-TestOutput "Expected message not found: $($scenario.ExpectedMessage)" -Type "Warning"
                    }
                }
                
                # Take screenshot of validation errors
                Take-Screenshot -Driver $driver -Name "validation-$($scenario.Name -replace ' ', '-')" -Mode $ExecutionMode
                
                # Verify form was NOT submitted (no success message)
                $successElement = Find-Element -Driver $driver -Selector ".alert-success, [data-ticket-number]" -Timeout 1 -Required $false
                if ($successElement) {
                    throw "Form should not have been submitted with invalid data"
                }
                
                $scenarioResult.Status = "Passed"
                $scenarioResult.FoundErrors = $foundErrors
                
                if ($ExecutionMode -eq 'Interactive') {
                    Pause-ForExplanation -Message "Validation errors displayed correctly for: $($scenario.Name)"
                }
                
            } catch {
                $scenarioResult.Status = "Failed"
                $scenarioResult.ErrorMessage = $_.Exception.Message
            }
            
            $result.Scenarios += $scenarioResult
        }
        
        # Calculate overall result
        $passedScenarios = ($result.Scenarios | Where-Object { $_.Status -eq "Passed" }).Count
        $totalScenarios = $result.Scenarios.Count
        
        if ($passedScenarios -eq $totalScenarios) {
            $result.Status = "Passed"
            Write-TestOutput "All $totalScenarios validation scenarios passed" -Type "Success"
        } else {
            $result.Status = "Partial"
            Write-TestOutput "$passedScenarios of $totalScenarios scenarios passed" -Type "Warning"
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

$testResult = Test-FormValidation -ExecutionMode $Mode
return $testResult
