#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Submit Basic Helpdesk Ticket - Guest User Workflow
.DESCRIPTION
    Automates the submission of a basic helpdesk ticket as a guest user.
    Tests frontend form validation and backend API processing.
.PARAMETER Mode
    Execution mode: Headless, Visual, Demo, Interactive, Recording
.EXAMPLE
    .\submit-basic-ticket.ps1 -Mode Visual
#>

param(
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

# Import utilities
. "$ScriptRoot\..\..\..\utilities\common-functions.ps1"
. "$ScriptRoot\..\..\..\utilities\browser-automation.ps1"
. "$ScriptRoot\..\..\..\utilities\visual-demo-helpers.ps1"
. "$ScriptRoot\..\..\..\utilities\api-helpers.ps1"

# Test configuration
$TestConfig = @{
    Name = "Submit Basic Helpdesk Ticket"
    Category = "Guest Workflows - Helpdesk"
    Requirements = @("1.1", "1.2", "1.5", "1.6")
    ExpectedDuration = 60  # seconds
}

# Test data
$TestData = @{
    Name = "Ahmad bin Abdullah"
    Email = "ahmad.test@motac.gov.my"
    Phone = "03-8000-8000"
    Department = "Bahagian Pengurusan Maklumat"
    Category = "Hardware Issue"
    Priority = "Medium"
    Subject = "Laptop screen flickering intermittently"
    Description = "My laptop screen has been flickering intermittently since yesterday. The issue occurs randomly and affects my work productivity. Model: Dell Latitude 5520."
}

function Test-SubmitBasicTicket {
    param([string]$ExecutionMode)
    
    $result = Initialize-TestResult -TestName $TestConfig.Name -Category $TestConfig.Category
    
    try {
        Write-TestStep "Initializing browser automation" -Mode $ExecutionMode
        $driver = Initialize-WebDriver -Mode $ExecutionMode
        
        # Step 1: Navigate to helpdesk page
        Write-TestStep "Navigating to helpdesk ticket submission page" -Mode $ExecutionMode
        $baseUrl = Get-ConfigValue -Key "BaseUrl"
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/helpdesk/create" -Mode $ExecutionMode
        
        if ($ExecutionMode -eq 'Demo' -or $ExecutionMode -eq 'Interactive') {
            Show-Annotation -Text "Guest users can submit helpdesk tickets without logging in" -Duration 3000
        }
        
        # Step 2: Verify form is displayed
        Write-TestStep "Verifying helpdesk form is displayed" -Mode $ExecutionMode
        $formElement = Wait-ForElement -Driver $driver -Selector "form[action*='helpdesk']" -Timeout 10
        Assert-ElementExists -Element $formElement -Message "Helpdesk form should be visible"
        
        # Step 3: Fill in personal information
        Write-TestStep "Filling in personal information" -Mode $ExecutionMode
        
        Fill-FormField -Driver $driver -Selector "#name" -Value $TestData.Name -Mode $ExecutionMode -Label "Full Name"
        Fill-FormField -Driver $driver -Selector "#email" -Value $TestData.Email -Mode $ExecutionMode -Label "Email Address"
        Fill-FormField -Driver $driver -Selector "#phone" -Value $TestData.Phone -Mode $ExecutionMode -Label "Phone Number"
        
        if ($ExecutionMode -eq 'Interactive') {
            Pause-ForExplanation -Message "Personal information fields support @motac.gov.my email validation"
        }
        
        # Step 4: Select department and category
        Write-TestStep "Selecting department and category" -Mode $ExecutionMode
        
        Select-DropdownOption -Driver $driver -Selector "#department" -Value $TestData.Department -Mode $ExecutionMode
        Select-DropdownOption -Driver $driver -Selector "#category" -Value $TestData.Category -Mode $ExecutionMode
        Select-DropdownOption -Driver $driver -Selector "#priority" -Value $TestData.Priority -Mode $ExecutionMode
        
        # Step 5: Fill in ticket details
        Write-TestStep "Filling in ticket details" -Mode $ExecutionMode
        
        Fill-FormField -Driver $driver -Selector "#subject" -Value $TestData.Subject -Mode $ExecutionMode -Label "Subject"
        Fill-FormField -Driver $driver -Selector "#description" -Value $TestData.Description -Mode $ExecutionMode -Label "Description"
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Form validation occurs in real-time as user types" -Duration 2000
        }
        
        # Step 6: Take screenshot before submission
        Write-TestStep "Capturing pre-submission screenshot" -Mode $ExecutionMode
        $screenshotPath = Take-Screenshot -Driver $driver -Name "helpdesk-form-filled" -Mode $ExecutionMode
        
        # Step 7: Submit the form
        Write-TestStep "Submitting helpdesk ticket" -Mode $ExecutionMode
        
        $submitButton = Find-Element -Driver $driver -Selector "button[type='submit']"
        Highlight-Element -Driver $driver -Element $submitButton -Mode $ExecutionMode
        Click-Element -Driver $driver -Element $submitButton -Mode $ExecutionMode
        
        # Step 8: Wait for success response
        Write-TestStep "Waiting for submission confirmation" -Mode $ExecutionMode
        
        $successElement = Wait-ForElement -Driver $driver -Selector ".alert-success, .notification-success, [data-ticket-number]" -Timeout 15
        Assert-ElementExists -Element $successElement -Message "Success message should be displayed"
        
        # Step 9: Extract ticket number
        $ticketNumber = Get-ElementText -Driver $driver -Selector "[data-ticket-number]"
        Write-TestOutput "Ticket created: $ticketNumber" -Type "Success"
        
        if ($ExecutionMode -eq 'Demo' -or $ExecutionMode -eq 'Interactive') {
            Show-Annotation -Text "Ticket $ticketNumber created successfully! Email notification sent." -Duration 3000
        }
        
        # Step 10: Verify backend API
        Write-TestStep "Verifying backend ticket creation" -Mode $ExecutionMode
        
        $apiResponse = Invoke-ApiRequest -Endpoint "/api/tickets/search" -Method "GET" -Query @{ number = $ticketNumber }
        Assert-ApiSuccess -Response $apiResponse -Message "Ticket should exist in database"
        
        # Step 11: Take final screenshot
        $finalScreenshot = Take-Screenshot -Driver $driver -Name "helpdesk-ticket-created" -Mode $ExecutionMode
        
        # Mark test as passed
        $result.Status = "Passed"
        $result.TicketNumber = $ticketNumber
        $result.Screenshots = @($screenshotPath, $finalScreenshot)
        
        Write-TestOutput "Test completed successfully" -Type "Success"
        
    } catch {
        $result.Status = "Failed"
        $result.ErrorMessage = $_.Exception.Message
        $result.StackTrace = $_.ScriptStackTrace
        
        Write-TestOutput "Test failed: $($_.Exception.Message)" -Type "Error"
        
        # Capture failure screenshot
        if ($driver) {
            $failureScreenshot = Take-Screenshot -Driver $driver -Name "helpdesk-submit-failure" -Mode $ExecutionMode
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
$testResult = Test-SubmitBasicTicket -ExecutionMode $Mode

# Return result for pipeline
return $testResult
