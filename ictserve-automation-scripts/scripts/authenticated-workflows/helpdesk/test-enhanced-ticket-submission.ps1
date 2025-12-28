#Requires -Version 7.0
<#
.SYNOPSIS
    Tests enhanced helpdesk ticket submission for authenticated users.

.DESCRIPTION
    This script tests authenticated user helpdesk features including:
    - Auto-filled forms with user profile data
    - Ticket history tracking
    - Real-time status updates
    - Comment and attachment functionality

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.EXAMPLE
    .\test-enhanced-ticket-submission.ps1 -Mode Demo

.NOTES
    Version: 1.0.0
    Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6
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
    Name = "Enhanced Ticket Submission Test"
    Category = "Authenticated Workflows - Helpdesk"
    Requirements = @("4.1", "4.2", "4.3", "4.4", "4.5", "4.6")
    ExpectedDuration = 150
}

$TestCredentials = @{
    Email = "test.user@motac.gov.my"
    Password = "TestPassword123!"
}

function Invoke-Login {
    param($Driver, $ExecutionMode)
    
    Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/login" -Mode $ExecutionMode
    Wait-ForElement -Driver $Driver -Selector "form" -Timeout 10
    Fill-FormField -Driver $Driver -Selector "#email, input[name='email']" -Value $TestCredentials.Email -Mode $ExecutionMode
    Fill-FormField -Driver $Driver -Selector "#password, input[name='password']" -Value $TestCredentials.Password -Mode $ExecutionMode
    $submitButton = Find-Element -Driver $Driver -Selector "button[type='submit']"
    Click-Element -Driver $Driver -Element $submitButton -Mode $ExecutionMode
    Start-Sleep -Seconds 3
}

function Test-AutoFilledForms {
    <#
    .SYNOPSIS
        Tests that ticket forms are auto-filled with user profile data.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing auto-filled forms" -Level INFO
    
    $results = @{
        TestName = "Auto-Filled Forms"
        Passed = $false
        Details = @{
            FormFound = $false
            NameAutoFilled = $false
            EmailAutoFilled = $false
            DepartmentAutoFilled = $false
        }
    }
    
    try {
        # Navigate to ticket creation page
        $ticketUrls = @(
            "$BaseUrl/helpdesk/create",
            "$BaseUrl/tickets/create",
            "$BaseUrl/support/new"
        )
        
        foreach ($url in $ticketUrls) {
            Navigate-ToUrl -Driver $Driver -Url $url -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $form = Find-Element -Driver $Driver -Selector "form" -Required $false
            if ($form) {
                $results.Details.FormFound = $true
                break
            }
        }
        
        if ($results.Details.FormFound) {
            # Check if name field is auto-filled
            $nameField = Find-Element -Driver $Driver -Selector "input[name='name'], input[name='full_name'], #name" -Required $false
            if ($nameField) {
                $nameValue = Get-ElementAttribute -Element $nameField -Attribute "value"
                $results.Details.NameAutoFilled = -not [string]::IsNullOrWhiteSpace($nameValue)
                
                if ($ExecutionMode -eq 'Demo' -and $results.Details.NameAutoFilled) {
                    Highlight-Element -Driver $Driver -Element $nameField -Color "green" -Mode $ExecutionMode
                    Show-Annotation -Text "Name auto-filled: $nameValue" -Duration 1500
                }
            }
            
            # Check if email field is auto-filled
            $emailField = Find-Element -Driver $Driver -Selector "input[name='email'], #email" -Required $false
            if ($emailField) {
                $emailValue = Get-ElementAttribute -Element $emailField -Attribute "value"
                $results.Details.EmailAutoFilled = -not [string]::IsNullOrWhiteSpace($emailValue)
            }
            
            # Check if department field is auto-filled
            $deptField = Find-Element -Driver $Driver -Selector "input[name='department'], select[name='department'], #department" -Required $false
            if ($deptField) {
                $deptValue = Get-ElementAttribute -Element $deptField -Attribute "value"
                $results.Details.DepartmentAutoFilled = -not [string]::IsNullOrWhiteSpace($deptValue)
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "auto-filled-form" -Mode $ExecutionMode
        
        $results.Passed = $results.Details.FormFound -and ($results.Details.NameAutoFilled -or $results.Details.EmailAutoFilled)
    }
    catch {
        Write-AutomationLog "Auto-filled forms test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-TicketHistory {
    <#
    .SYNOPSIS
        Tests ticket history tracking for authenticated users.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing ticket history" -Level INFO
    
    $results = @{
        TestName = "Ticket History"
        Passed = $false
        Details = @{
            HistoryPageFound = $false
            TicketsDisplayed = 0
            HasStatusColumn = $false
            HasDateColumn = $false
            HasPagination = $false
        }
    }
    
    try {
        # Navigate to ticket history
        $historyUrls = @(
            "$BaseUrl/helpdesk/my-tickets",
            "$BaseUrl/tickets",
            "$BaseUrl/my-tickets",
            "$BaseUrl/dashboard/tickets"
        )
        
        foreach ($url in $historyUrls) {
            Navigate-ToUrl -Driver $Driver -Url $url -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $ticketList = Find-Element -Driver $Driver -Selector ".ticket-list, table, .tickets, [data-tickets]" -Required $false
            if ($ticketList) {
                $results.Details.HistoryPageFound = $true
                break
            }
        }
        
        if ($results.Details.HistoryPageFound) {
            # Count tickets
            $tickets = Find-Elements -Driver $Driver -Selector ".ticket-row, tr[data-ticket], .ticket-item"
            $results.Details.TicketsDisplayed = $tickets.Count
            
            # Check for status column
            $statusColumn = Find-Element -Driver $Driver -Selector "th:contains('Status'), .status-column, [data-column='status']" -Required $false
            $results.Details.HasStatusColumn = $null -ne $statusColumn
            
            # Check for date column
            $dateColumn = Find-Element -Driver $Driver -Selector "th:contains('Date'), th:contains('Created'), .date-column" -Required $false
            $results.Details.HasDateColumn = $null -ne $dateColumn
            
            # Check for pagination
            $pagination = Find-Element -Driver $Driver -Selector ".pagination, nav[aria-label='Pagination'], .page-links" -Required $false
            $results.Details.HasPagination = $null -ne $pagination
            
            if ($ExecutionMode -eq 'Demo') {
                Show-Annotation -Text "Found $($tickets.Count) tickets in history" -Duration 2000
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "ticket-history" -Mode $ExecutionMode
        
        $results.Passed = $results.Details.HistoryPageFound
    }
    catch {
        Write-AutomationLog "Ticket history test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-RealTimeStatusUpdates {
    <#
    .SYNOPSIS
        Tests real-time status updates for tickets.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing real-time status updates" -Level INFO
    
    $results = @{
        TestName = "Real-Time Status Updates"
        Passed = $false
        Details = @{
            LivewirePresent = $false
            PollingEnabled = $false
            StatusBadgeFound = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/helpdesk/my-tickets" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        # Check for Livewire components
        $lwScript = @"
            return typeof Livewire !== 'undefined';
"@
        $results.Details.LivewirePresent = Execute-JavaScript -Driver $Driver -Script $lwScript
        
        # Check for polling
        $pollingElement = Find-Element -Driver $Driver -Selector "[wire\\:poll], [data-poll]" -Required $false
        $results.Details.PollingEnabled = $null -ne $pollingElement
        
        # Check for status badges
        $statusBadge = Find-Element -Driver $Driver -Selector ".status-badge, .badge, [data-status]" -Required $false
        $results.Details.StatusBadgeFound = $null -ne $statusBadge
        
        if ($statusBadge -and $ExecutionMode -eq 'Demo') {
            Highlight-Element -Driver $Driver -Element $statusBadge -Color "blue" -Mode $ExecutionMode
            Show-Annotation -Text "Status badge with real-time updates" -Duration 1500
        }
        
        $results.Passed = $results.Details.LivewirePresent -or $results.Details.PollingEnabled -or $results.Details.StatusBadgeFound
    }
    catch {
        Write-AutomationLog "Real-time status updates test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-CommentFunctionality {
    <#
    .SYNOPSIS
        Tests comment functionality on tickets.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing comment functionality" -Level INFO
    
    $results = @{
        TestName = "Comment Functionality"
        Passed = $false
        Details = @{
            TicketDetailFound = $false
            CommentFormFound = $false
            ExistingComments = 0
            CanAddComment = $false
        }
    }
    
    try {
        # First get a ticket to view
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/helpdesk/my-tickets" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        # Click on first ticket
        $ticketLink = Find-Element -Driver $Driver -Selector ".ticket-row a, tr[data-ticket] a, .ticket-item a" -Required $false
        if ($ticketLink) {
            Click-Element -Driver $Driver -Element $ticketLink -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $results.Details.TicketDetailFound = $true
            
            # Check for comment form
            $commentForm = Find-Element -Driver $Driver -Selector ".comment-form, form[action*='comment'], textarea[name='comment']" -Required $false
            $results.Details.CommentFormFound = $null -ne $commentForm
            
            # Count existing comments
            $comments = Find-Elements -Driver $Driver -Selector ".comment, .comment-item, [data-comment]"
            $results.Details.ExistingComments = $comments.Count
            
            # Check if we can add a comment
            $submitButton = Find-Element -Driver $Driver -Selector "button[type='submit']:contains('Comment'), button:contains('Add Comment')" -Required $false
            $results.Details.CanAddComment = $null -ne $commentForm -and $null -ne $submitButton
            
            if ($ExecutionMode -eq 'Demo' -and $commentForm) {
                Highlight-Element -Driver $Driver -Element $commentForm -Color "green" -Mode $ExecutionMode
                Show-Annotation -Text "Comment form available" -Duration 1500
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "ticket-comments" -Mode $ExecutionMode
        
        $results.Passed = $results.Details.TicketDetailFound
    }
    catch {
        Write-AutomationLog "Comment functionality test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-AttachmentFunctionality {
    <#
    .SYNOPSIS
        Tests attachment functionality on tickets.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing attachment functionality" -Level INFO
    
    $results = @{
        TestName = "Attachment Functionality"
        Passed = $false
        Details = @{
            FileInputFound = $false
            AcceptedTypes = ""
            MaxSizeDisplayed = $false
            ExistingAttachments = 0
        }
    }
    
    try {
        # Navigate to ticket creation or detail page
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/helpdesk/create" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        # Check for file input
        $fileInput = Find-Element -Driver $Driver -Selector "input[type='file'], .file-upload, [data-file-upload]" -Required $false
        $results.Details.FileInputFound = $null -ne $fileInput
        
        if ($fileInput) {
            # Get accepted file types
            $acceptAttr = Get-ElementAttribute -Element $fileInput -Attribute "accept"
            $results.Details.AcceptedTypes = $acceptAttr
            
            if ($ExecutionMode -eq 'Demo') {
                Highlight-Element -Driver $Driver -Element $fileInput -Color "blue" -Mode $ExecutionMode
                Show-Annotation -Text "File upload: $acceptAttr" -Duration 1500
            }
        }
        
        # Check for max size info
        $maxSizeInfo = Find-Element -Driver $Driver -Selector ".max-size, .file-size-limit, [data-max-size]" -Required $false
        $results.Details.MaxSizeDisplayed = $null -ne $maxSizeInfo
        
        # Check for existing attachments on a ticket detail page
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/helpdesk/my-tickets" -Mode $ExecutionMode
        Start-Sleep -Seconds 1
        
        $ticketLink = Find-Element -Driver $Driver -Selector ".ticket-row a" -Required $false
        if ($ticketLink) {
            Click-Element -Driver $Driver -Element $ticketLink -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $attachments = Find-Elements -Driver $Driver -Selector ".attachment, .file-attachment, [data-attachment]"
            $results.Details.ExistingAttachments = $attachments.Count
        }
        
        Take-Screenshot -Driver $Driver -Name "ticket-attachments" -Mode $ExecutionMode
        
        $results.Passed = $results.Details.FileInputFound
    }
    catch {
        Write-AutomationLog "Attachment functionality test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-EnhancedTicketSubmissionTest {
    <#
    .SYNOPSIS
        Executes the complete enhanced ticket submission test suite.
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
    Write-Host "║        Enhanced Ticket Submission Test Suite                  ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $driver = $null
    
    try {
        $driver = Initialize-WebDriver -Mode $Mode
        
        Write-Host "  Logging in..." -ForegroundColor Gray
        Invoke-Login -Driver $driver -ExecutionMode $Mode
        
        # Test 1: Auto-Filled Forms
        Write-Host "  Test 1: Auto-Filled Forms" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $autoFillResults = Test-AutoFilledForms -Driver $driver -ExecutionMode $Mode
        $results.Tests += $autoFillResults
        $results.Summary.TotalTests++
        if ($autoFillResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Name Auto-Filled: $(if ($autoFillResults.Details.NameAutoFilled) { '✓' } else { '○' })" -ForegroundColor $(if ($autoFillResults.Details.NameAutoFilled) { 'Green' } else { 'Yellow' })
        Write-Host "    Email Auto-Filled: $(if ($autoFillResults.Details.EmailAutoFilled) { '✓' } else { '○' })" -ForegroundColor $(if ($autoFillResults.Details.EmailAutoFilled) { 'Green' } else { 'Yellow' })
        Write-Host "    Result: $(if ($autoFillResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($autoFillResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 2: Ticket History
        Write-Host "  Test 2: Ticket History" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $historyResults = Test-TicketHistory -Driver $driver -ExecutionMode $Mode
        $results.Tests += $historyResults
        $results.Summary.TotalTests++
        if ($historyResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    History Page: $(if ($historyResults.Details.HistoryPageFound) { '✓' } else { '✗' })" -ForegroundColor $(if ($historyResults.Details.HistoryPageFound) { 'Green' } else { 'Red' })
        Write-Host "    Tickets Found: $($historyResults.Details.TicketsDisplayed)" -ForegroundColor White
        Write-Host "    Result: $(if ($historyResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($historyResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 3: Real-Time Status Updates
        Write-Host "  Test 3: Real-Time Status Updates" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $realtimeResults = Test-RealTimeStatusUpdates -Driver $driver -ExecutionMode $Mode
        $results.Tests += $realtimeResults
        $results.Summary.TotalTests++
        if ($realtimeResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Livewire: $(if ($realtimeResults.Details.LivewirePresent) { '✓' } else { '○' })" -ForegroundColor $(if ($realtimeResults.Details.LivewirePresent) { 'Green' } else { 'Yellow' })
        Write-Host "    Polling: $(if ($realtimeResults.Details.PollingEnabled) { '✓' } else { '○' })" -ForegroundColor $(if ($realtimeResults.Details.PollingEnabled) { 'Green' } else { 'Yellow' })
        Write-Host "    Result: $(if ($realtimeResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($realtimeResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 4: Comment Functionality
        Write-Host "  Test 4: Comment Functionality" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $commentResults = Test-CommentFunctionality -Driver $driver -ExecutionMode $Mode
        $results.Tests += $commentResults
        $results.Summary.TotalTests++
        if ($commentResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Comment Form: $(if ($commentResults.Details.CommentFormFound) { '✓' } else { '○' })" -ForegroundColor $(if ($commentResults.Details.CommentFormFound) { 'Green' } else { 'Yellow' })
        Write-Host "    Existing Comments: $($commentResults.Details.ExistingComments)" -ForegroundColor White
        Write-Host "    Result: $(if ($commentResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($commentResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 5: Attachment Functionality
        Write-Host "  Test 5: Attachment Functionality" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $attachResults = Test-AttachmentFunctionality -Driver $driver -ExecutionMode $Mode
        $results.Tests += $attachResults
        $results.Summary.TotalTests++
        if ($attachResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    File Input: $(if ($attachResults.Details.FileInputFound) { '✓' } else { '○' })" -ForegroundColor $(if ($attachResults.Details.FileInputFound) { 'Green' } else { 'Yellow' })
        Write-Host "    Accepted Types: $($attachResults.Details.AcceptedTypes)" -ForegroundColor White
        Write-Host "    Result: $(if ($attachResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($attachResults.Passed) { 'Green' } else { 'Red' })
        
    }
    catch {
        Write-AutomationLog "Enhanced ticket submission test failed: $($_.Exception.Message)" -Level ERROR
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
$testResults = Start-EnhancedTicketSubmissionTest

return $testResults
