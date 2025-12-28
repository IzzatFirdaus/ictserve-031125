#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Track Ticket Status by Number - Guest User Workflow
.DESCRIPTION
    Automates tracking a helpdesk ticket status using the ticket number.
    Tests frontend search and backend query functionality.
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
    Name = "Track Ticket Status by Number"
    Category = "Guest Workflows - Helpdesk"
    Requirements = @("1.3", "1.4")
    ExpectedDuration = 45
}

function Test-TrackTicketByNumber {
    param([string]$ExecutionMode)
    
    $result = Initialize-TestResult -TestName $TestConfig.Name -Category $TestConfig.Category
    
    try {
        Write-TestStep "Initializing browser automation" -Mode $ExecutionMode
        $driver = Initialize-WebDriver -Mode $ExecutionMode
        
        $baseUrl = Get-ConfigValue -Key "BaseUrl"
        
        # First, create a ticket to track (or use existing test ticket)
        Write-TestStep "Getting or creating test ticket" -Mode $ExecutionMode
        $testTicket = Get-OrCreateTestTicket -Type "Helpdesk"
        $ticketNumber = $testTicket.TicketNumber
        
        Write-TestOutput "Using ticket: $ticketNumber" -Type "Info"
        
        # Navigate to ticket tracking page
        Write-TestStep "Navigating to ticket tracking page" -Mode $ExecutionMode
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/helpdesk/track" -Mode $ExecutionMode
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Guest users can track tickets without logging in" -Duration 2500
        }
        
        # Find and fill ticket number field
        Write-TestStep "Entering ticket number" -Mode $ExecutionMode
        $ticketInput = Wait-ForElement -Driver $driver -Selector "#ticket-number, input[name='ticket_number']" -Timeout 10
        Fill-FormField -Driver $driver -Selector "#ticket-number, input[name='ticket_number']" -Value $ticketNumber -Mode $ExecutionMode -Label "Ticket Number"
        
        # Click search/track button
        Write-TestStep "Searching for ticket" -Mode $ExecutionMode
        $searchButton = Find-Element -Driver $driver -Selector "button[type='submit'], .btn-search, .btn-track"
        Highlight-Element -Driver $driver -Element $searchButton -Mode $ExecutionMode
        Click-Element -Driver $driver -Element $searchButton -Mode $ExecutionMode
        
        # Wait for results
        Write-TestStep "Waiting for ticket details" -Mode $ExecutionMode
        $ticketDetails = Wait-ForElement -Driver $driver -Selector ".ticket-details, .ticket-status, [data-ticket-info]" -Timeout 15
        
        if ($ExecutionMode -eq 'Interactive') {
            Pause-ForExplanation -Message "Ticket details are displayed to the guest user"
        }
        
        # Verify ticket information is displayed
        Write-TestStep "Verifying ticket information" -Mode $ExecutionMode
        
        # Check ticket number is displayed
        $displayedNumber = Get-ElementText -Driver $driver -Selector ".ticket-number, [data-ticket-number]"
        Assert-Contains -Actual $displayedNumber -Expected $ticketNumber -Message "Ticket number should be displayed"
        
        # Check status is displayed
        $statusElement = Find-Element -Driver $driver -Selector ".ticket-status, [data-status]"
        Assert-ElementExists -Element $statusElement -Message "Ticket status should be displayed"
        $status = Get-ElementText -Element $statusElement
        Write-TestOutput "Ticket status: $status" -Type "Info"
        
        # Check subject is displayed
        $subjectElement = Find-Element -Driver $driver -Selector ".ticket-subject, [data-subject]" -Required $false
        if ($subjectElement) {
            $subject = Get-ElementText -Element $subjectElement
            Write-TestOutput "Ticket subject: $subject" -Type "Info"
        }
        
        # Check timeline/history if available
        $timelineElements = Find-Elements -Driver $driver -Selector ".ticket-timeline .timeline-item, .status-history li"
        if ($timelineElements.Count -gt 0) {
            Write-TestOutput "Ticket has $($timelineElements.Count) status updates" -Type "Info"
            
            if ($ExecutionMode -eq 'Demo') {
                Show-Annotation -Text "Status timeline shows complete ticket history" -Duration 2000
            }
        }
        
        # Take screenshot of ticket details
        Take-Screenshot -Driver $driver -Name "ticket-tracking-result" -Mode $ExecutionMode
        
        # Verify backend data matches
        Write-TestStep "Verifying backend data consistency" -Mode $ExecutionMode
        $apiResponse = Invoke-ApiRequest -Endpoint "/api/tickets/$ticketNumber" -Method "GET"
        Assert-ApiSuccess -Response $apiResponse
        
        # Compare frontend and backend data
        $backendStatus = $apiResponse.data.status
        if ($status -ne $backendStatus) {
            Write-TestOutput "Status mismatch: Frontend='$status', Backend='$backendStatus'" -Type "Warning"
        }
        
        # Test invalid ticket number
        Write-TestStep "Testing invalid ticket number" -Mode $ExecutionMode
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/helpdesk/track" -Mode $ExecutionMode
        Fill-FormField -Driver $driver -Selector "#ticket-number, input[name='ticket_number']" -Value "INVALID-12345" -Mode $ExecutionMode
        $searchButton = Find-Element -Driver $driver -Selector "button[type='submit'], .btn-search"
        Click-Element -Driver $driver -Element $searchButton -Mode $ExecutionMode
        
        # Verify error message for invalid ticket
        $errorElement = Wait-ForElement -Driver $driver -Selector ".alert-danger, .error-message, .not-found" -Timeout 10
        Assert-ElementExists -Element $errorElement -Message "Error message should be shown for invalid ticket"
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Appropriate error shown for invalid ticket numbers" -Duration 2000
        }
        
        Take-Screenshot -Driver $driver -Name "ticket-not-found" -Mode $ExecutionMode
        
        $result.Status = "Passed"
        $result.TrackedTicket = $ticketNumber
        Write-TestOutput "Ticket tracking test completed successfully" -Type "Success"
        
    } catch {
        $result.Status = "Failed"
        $result.ErrorMessage = $_.Exception.Message
        Write-TestOutput "Test failed: $($_.Exception.Message)" -Type "Error"
        
        if ($driver) {
            Take-Screenshot -Driver $driver -Name "ticket-tracking-failure" -Mode $ExecutionMode
        }
    } finally {
        if ($driver) { Close-WebDriver -Driver $driver }
        $result.EndTime = Get-Date
        $result.Duration = ($result.EndTime - $result.StartTime).TotalSeconds
        Save-TestResult -Result $result
    }
    
    return $result
}

$testResult = Test-TrackTicketByNumber -ExecutionMode $Mode
return $testResult
