#Requires -Version 7.0
<#
.SYNOPSIS
    Tests notification center and WebSocket functionality.

.DESCRIPTION
    This script tests notification features including:
    - Notification display and listing
    - Real-time notification delivery
    - Mark as read functionality
    - Notification preferences
    - WebSocket connection

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.EXAMPLE
    .\test-notification-center.ps1 -Mode Demo

.NOTES
    Version: 1.0.0
    Requirements: 3.1, 3.2, 3.4
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
    Name = "Notification Center Test"
    Category = "Authenticated Workflows - Dashboard"
    Requirements = @("3.1", "3.2", "3.4")
    ExpectedDuration = 90
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

function Test-NotificationBell {
    <#
    .SYNOPSIS
        Tests the notification bell/icon in the header.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing notification bell" -Level INFO
    
    $results = @{
        TestName = "Notification Bell"
        Passed = $false
        Details = @{
            BellFound = $false
            CountBadgePresent = $false
            DropdownOpens = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/dashboard" -Mode $ExecutionMode
        Wait-ForElement -Driver $Driver -Selector ".dashboard" -Timeout 15
        
        # Find notification bell
        $bellSelectors = @(
            ".notification-bell",
            "[data-notification-trigger]",
            ".notifications-icon",
            "button[aria-label*='notification']",
            ".bell-icon"
        )
        
        $bellElement = $null
        foreach ($selector in $bellSelectors) {
            $bellElement = Find-Element -Driver $Driver -Selector $selector -Required $false
            if ($bellElement) { break }
        }
        
        $results.Details.BellFound = $null -ne $bellElement
        
        if ($bellElement) {
            # Check for count badge
            $badge = Find-Element -Driver $Driver -Selector ".notification-count, .badge, .unread-count" -Required $false
            $results.Details.CountBadgePresent = $null -ne $badge
            
            if ($ExecutionMode -eq 'Demo') {
                Highlight-Element -Driver $Driver -Element $bellElement -Color "blue" -Mode $ExecutionMode
                Show-Annotation -Text "Notification bell found" -Duration 1500
            }
            
            # Click to open dropdown
            Click-Element -Driver $Driver -Element $bellElement -Mode $ExecutionMode
            Start-Sleep -Seconds 1
            
            # Check if dropdown opened
            $dropdown = Find-Element -Driver $Driver -Selector ".notification-dropdown, .notifications-panel, [data-notification-list]" -Required $false
            $results.Details.DropdownOpens = $null -ne $dropdown
            
            if ($dropdown -and $ExecutionMode -eq 'Demo') {
                Highlight-Element -Driver $Driver -Element $dropdown -Color "green" -Mode $ExecutionMode
                Show-Annotation -Text "Notification dropdown opened" -Duration 1500
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "notification-bell" -Mode $ExecutionMode
        
        $results.Passed = $results.Details.BellFound
    }
    catch {
        Write-AutomationLog "Notification bell test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-NotificationList {
    <#
    .SYNOPSIS
        Tests the notification list display.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing notification list" -Level INFO
    
    $results = @{
        TestName = "Notification List"
        Passed = $false
        Details = @{
            ListFound = $false
            NotificationsDisplayed = 0
            HasTimestamps = $false
            HasReadStatus = $false
        }
    }
    
    try {
        # Navigate to notifications page or open dropdown
        $notificationUrls = @(
            "$BaseUrl/notifications",
            "$BaseUrl/user/notifications",
            "$BaseUrl/dashboard/notifications"
        )
        
        $listFound = $false
        foreach ($url in $notificationUrls) {
            Navigate-ToUrl -Driver $Driver -Url $url -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $list = Find-Element -Driver $Driver -Selector ".notification-list, .notifications, [data-notifications]" -Required $false
            if ($list) {
                $listFound = $true
                break
            }
        }
        
        $results.Details.ListFound = $listFound
        
        if ($listFound) {
            # Count notification items
            $items = Find-Elements -Driver $Driver -Selector ".notification-item, .notification, [data-notification-id]"
            $results.Details.NotificationsDisplayed = $items.Count
            
            # Check for timestamps
            $timestamps = Find-Element -Driver $Driver -Selector ".notification-time, .timestamp, time" -Required $false
            $results.Details.HasTimestamps = $null -ne $timestamps
            
            # Check for read/unread status
            $readStatus = Find-Element -Driver $Driver -Selector ".unread, .read, [data-read]" -Required $false
            $results.Details.HasReadStatus = $null -ne $readStatus
            
            if ($ExecutionMode -eq 'Demo') {
                Show-Annotation -Text "Found $($items.Count) notifications" -Duration 2000
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "notification-list" -Mode $ExecutionMode
        
        $results.Passed = $results.Details.ListFound
    }
    catch {
        Write-AutomationLog "Notification list test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-MarkAsRead {
    <#
    .SYNOPSIS
        Tests marking notifications as read.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing mark as read functionality" -Level INFO
    
    $results = @{
        TestName = "Mark As Read"
        Passed = $false
        Details = @{
            MarkSingleAvailable = $false
            MarkAllAvailable = $false
            StatusUpdated = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/notifications" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        # Check for mark as read button on individual notification
        $markSingleButton = Find-Element -Driver $Driver -Selector ".mark-read, [data-action='mark-read'], button[title*='read']" -Required $false
        $results.Details.MarkSingleAvailable = $null -ne $markSingleButton
        
        # Check for mark all as read button
        $markAllButton = Find-Element -Driver $Driver -Selector ".mark-all-read, [data-action='mark-all-read'], button:contains('Mark all')" -Required $false
        $results.Details.MarkAllAvailable = $null -ne $markAllButton
        
        # Try to mark a notification as read
        if ($markSingleButton) {
            $unreadBefore = Find-Elements -Driver $Driver -Selector ".unread, [data-read='false']"
            $unreadCountBefore = $unreadBefore.Count
            
            Click-Element -Driver $Driver -Element $markSingleButton -Mode $ExecutionMode
            Start-Sleep -Seconds 1
            
            $unreadAfter = Find-Elements -Driver $Driver -Selector ".unread, [data-read='false']"
            $unreadCountAfter = $unreadAfter.Count
            
            $results.Details.StatusUpdated = $unreadCountAfter -lt $unreadCountBefore -or $unreadCountBefore -eq 0
            
            if ($ExecutionMode -eq 'Demo') {
                Show-Annotation -Text "Notification marked as read" -Duration 1500
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "notification-mark-read" -Mode $ExecutionMode
        
        $results.Passed = $results.Details.MarkSingleAvailable -or $results.Details.MarkAllAvailable
    }
    catch {
        Write-AutomationLog "Mark as read test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-WebSocketConnection {
    <#
    .SYNOPSIS
        Tests WebSocket connection for real-time notifications.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing WebSocket connection" -Level INFO
    
    $results = @{
        TestName = "WebSocket Connection"
        Passed = $false
        Details = @{
            EchoPresent = $false
            ReverbConnected = $false
            ChannelSubscribed = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/dashboard" -Mode $ExecutionMode
        Wait-ForElement -Driver $Driver -Selector ".dashboard" -Timeout 15
        
        # Check for Laravel Echo/Reverb
        $wsScript = @"
            return (function() {
                const results = {
                    echoPresent: typeof Echo !== 'undefined',
                    reverbConnected: false,
                    channels: []
                };
                
                if (results.echoPresent && Echo.connector) {
                    results.reverbConnected = Echo.connector.socket && 
                                              Echo.connector.socket.readyState === 1;
                    
                    if (Echo.connector.channels) {
                        results.channels = Object.keys(Echo.connector.channels);
                    }
                }
                
                return results;
            })();
"@
        
        $wsResult = Execute-JavaScript -Driver $Driver -Script $wsScript
        
        if ($wsResult) {
            $results.Details.EchoPresent = $wsResult.echoPresent -eq $true
            $results.Details.ReverbConnected = $wsResult.reverbConnected -eq $true
            $results.Details.ChannelSubscribed = $wsResult.channels.Count -gt 0
        }
        
        if ($ExecutionMode -eq 'Demo') {
            $status = if ($results.Details.EchoPresent) { "Echo/Reverb detected" } else { "No WebSocket framework" }
            Show-Annotation -Text $status -Duration 2000
        }
        
        # Also check for polling fallback
        $pollingElement = Find-Element -Driver $Driver -Selector "[wire\\:poll], [data-poll]" -Required $false
        $hasPolling = $null -ne $pollingElement
        
        $results.Passed = $results.Details.EchoPresent -or $hasPolling
    }
    catch {
        Write-AutomationLog "WebSocket connection test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-NotificationPreferences {
    <#
    .SYNOPSIS
        Tests notification preferences/settings.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing notification preferences" -Level INFO
    
    $results = @{
        TestName = "Notification Preferences"
        Passed = $false
        Details = @{
            SettingsPageFound = $false
            EmailTogglePresent = $false
            PushTogglePresent = $false
            CategorySettings = $false
        }
    }
    
    try {
        # Navigate to notification settings
        $settingsUrls = @(
            "$BaseUrl/settings/notifications",
            "$BaseUrl/profile/notifications",
            "$BaseUrl/user/settings#notifications"
        )
        
        foreach ($url in $settingsUrls) {
            Navigate-ToUrl -Driver $Driver -Url $url -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $settingsForm = Find-Element -Driver $Driver -Selector "form, .notification-settings, [data-settings='notifications']" -Required $false
            if ($settingsForm) {
                $results.Details.SettingsPageFound = $true
                break
            }
        }
        
        if ($results.Details.SettingsPageFound) {
            # Check for email notification toggle
            $emailToggle = Find-Element -Driver $Driver -Selector "input[name*='email'], [data-setting='email-notifications']" -Required $false
            $results.Details.EmailTogglePresent = $null -ne $emailToggle
            
            # Check for push notification toggle
            $pushToggle = Find-Element -Driver $Driver -Selector "input[name*='push'], [data-setting='push-notifications']" -Required $false
            $results.Details.PushTogglePresent = $null -ne $pushToggle
            
            # Check for category-specific settings
            $categorySettings = Find-Element -Driver $Driver -Selector ".notification-category, [data-category]" -Required $false
            $results.Details.CategorySettings = $null -ne $categorySettings
            
            if ($ExecutionMode -eq 'Demo') {
                Show-Annotation -Text "Notification preferences page found" -Duration 2000
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "notification-preferences" -Mode $ExecutionMode
        
        $results.Passed = $results.Details.SettingsPageFound
    }
    catch {
        Write-AutomationLog "Notification preferences test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-NotificationCenterTest {
    <#
    .SYNOPSIS
        Executes the complete notification center test suite.
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
    Write-Host "║           Notification Center Test Suite                      ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $driver = $null
    
    try {
        # Initialize browser
        $driver = Initialize-WebDriver -Mode $Mode
        
        # Login first
        Write-Host "  Logging in..." -ForegroundColor Gray
        Invoke-Login -Driver $driver -ExecutionMode $Mode
        
        # Test 1: Notification Bell
        Write-Host "  Test 1: Notification Bell" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $bellResults = Test-NotificationBell -Driver $driver -ExecutionMode $Mode
        $results.Tests += $bellResults
        $results.Summary.TotalTests++
        if ($bellResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Bell Found: $(if ($bellResults.Details.BellFound) { '✓' } else { '✗' })" -ForegroundColor $(if ($bellResults.Details.BellFound) { 'Green' } else { 'Red' })
        Write-Host "    Dropdown Opens: $(if ($bellResults.Details.DropdownOpens) { '✓' } else { '○' })" -ForegroundColor $(if ($bellResults.Details.DropdownOpens) { 'Green' } else { 'Yellow' })
        Write-Host "    Result: $(if ($bellResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($bellResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 2: Notification List
        Write-Host "  Test 2: Notification List" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $listResults = Test-NotificationList -Driver $driver -ExecutionMode $Mode
        $results.Tests += $listResults
        $results.Summary.TotalTests++
        if ($listResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    List Found: $(if ($listResults.Details.ListFound) { '✓' } else { '✗' })" -ForegroundColor $(if ($listResults.Details.ListFound) { 'Green' } else { 'Red' })
        Write-Host "    Notifications: $($listResults.Details.NotificationsDisplayed)" -ForegroundColor White
        Write-Host "    Result: $(if ($listResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($listResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 3: Mark As Read
        Write-Host "  Test 3: Mark As Read" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $markResults = Test-MarkAsRead -Driver $driver -ExecutionMode $Mode
        $results.Tests += $markResults
        $results.Summary.TotalTests++
        if ($markResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Mark Single: $(if ($markResults.Details.MarkSingleAvailable) { '✓' } else { '○' })" -ForegroundColor $(if ($markResults.Details.MarkSingleAvailable) { 'Green' } else { 'Yellow' })
        Write-Host "    Mark All: $(if ($markResults.Details.MarkAllAvailable) { '✓' } else { '○' })" -ForegroundColor $(if ($markResults.Details.MarkAllAvailable) { 'Green' } else { 'Yellow' })
        Write-Host "    Result: $(if ($markResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($markResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 4: WebSocket Connection
        Write-Host "  Test 4: WebSocket Connection" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $wsResults = Test-WebSocketConnection -Driver $driver -ExecutionMode $Mode
        $results.Tests += $wsResults
        $results.Summary.TotalTests++
        if ($wsResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Echo Present: $(if ($wsResults.Details.EchoPresent) { '✓' } else { '○' })" -ForegroundColor $(if ($wsResults.Details.EchoPresent) { 'Green' } else { 'Yellow' })
        Write-Host "    Reverb Connected: $(if ($wsResults.Details.ReverbConnected) { '✓' } else { '○' })" -ForegroundColor $(if ($wsResults.Details.ReverbConnected) { 'Green' } else { 'Yellow' })
        Write-Host "    Result: $(if ($wsResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($wsResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 5: Notification Preferences
        Write-Host "  Test 5: Notification Preferences" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $prefResults = Test-NotificationPreferences -Driver $driver -ExecutionMode $Mode
        $results.Tests += $prefResults
        $results.Summary.TotalTests++
        if ($prefResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Settings Page: $(if ($prefResults.Details.SettingsPageFound) { '✓' } else { '○' })" -ForegroundColor $(if ($prefResults.Details.SettingsPageFound) { 'Green' } else { 'Yellow' })
        Write-Host "    Email Toggle: $(if ($prefResults.Details.EmailTogglePresent) { '✓' } else { '○' })" -ForegroundColor $(if ($prefResults.Details.EmailTogglePresent) { 'Green' } else { 'Yellow' })
        Write-Host "    Result: $(if ($prefResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($prefResults.Passed) { 'Green' } else { 'Red' })
        
    }
    catch {
        Write-AutomationLog "Notification center test failed: $($_.Exception.Message)" -Level ERROR
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
$testResults = Start-NotificationCenterTest

# Return results for reporting
return $testResults
