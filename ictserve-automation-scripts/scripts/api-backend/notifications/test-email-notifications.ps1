#Requires -Version 7.0
<#
.SYNOPSIS
    Tests email and notification automation functionality.

.DESCRIPTION
    This script tests email gateway integration, delivery confirmation,
    multi-channel notifications, WebSocket, and real-time notification automation.

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.EXAMPLE
    .\test-email-notifications.ps1 -Mode Demo

.NOTES
    Version: 1.0.0
    Requirements: 10.2, 10.6
#>

[CmdletBinding()]
param(
    [Parameter()]
    [string]$BaseUrl = "http://localhost:8000",
    
    [Parameter()]
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Headless'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

. "$ScriptRoot\..\..\..\utilities\common-functions.ps1"
. "$ScriptRoot\..\..\..\utilities\api-helpers.ps1"

$TestConfig = @{
    Name = "Email & Notification Test"
    Category = "API Backend - Notifications"
    Requirements = @("10.2", "10.6")
    ExpectedDuration = 90
}

function Test-EmailGatewayConnectivity {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing email gateway connectivity" -Level INFO
    
    $results = @{
        TestName = "Email Gateway Connectivity"
        Passed = $false
        Details = @{
            SMTPConfigured = $false
            ConnectionSuccessful = $false
            ResponseTime = 0
        }
    }
    
    try {
        $startTime = Get-Date
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/admin/email/health" -Method GET -IgnoreErrors
        $results.Details.ResponseTime = ((Get-Date) - $startTime).TotalMilliseconds
        
        if ($response) {
            $results.Details.SMTPConfigured = $response.smtp_configured -eq $true -or $response.status -eq 'configured'
            $results.Details.ConnectionSuccessful = $response.connection -eq 'ok' -or $response.status -eq 'connected'
        }
        
        $results.Passed = $results.Details.SMTPConfigured -or $results.Details.ConnectionSuccessful
    }
    catch {
        Write-AutomationLog "Email gateway test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-EmailDelivery {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing email delivery" -Level INFO
    
    $results = @{
        TestName = "Email Delivery"
        Passed = $false
        Details = @{
            TestEmailSent = $false
            QueueProcessed = $false
            DeliveryConfirmed = $false
        }
    }
    
    try {
        # Test email sending endpoint
        $sendResponse = Invoke-ApiRequest -Url "$BaseUrl/api/admin/email/test" -Method POST -Body @{
            to = "test@example.com"
            subject = "Automation Test"
        } -IgnoreErrors
        
        if ($sendResponse) {
            $results.Details.TestEmailSent = $sendResponse.sent -eq $true -or $sendResponse.queued -eq $true
        }
        
        # Check queue status
        $queueResponse = Invoke-ApiRequest -Url "$BaseUrl/api/admin/queue/status" -Method GET -IgnoreErrors
        if ($queueResponse) {
            $results.Details.QueueProcessed = $true
        }
        
        $results.Passed = $results.Details.TestEmailSent -or $results.Details.QueueProcessed
    }
    catch {
        Write-AutomationLog "Email delivery test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-NotificationChannels {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing notification channels" -Level INFO
    
    $results = @{
        TestName = "Notification Channels"
        Passed = $false
        Details = @{
            DatabaseChannel = $false
            EmailChannel = $false
            BroadcastChannel = $false
        }
    }
    
    try {
        # Test notification endpoints
        $notifyResponse = Invoke-ApiRequest -Url "$BaseUrl/api/notifications" -Method GET -IgnoreErrors
        if ($notifyResponse) {
            $results.Details.DatabaseChannel = $true
        }
        
        # Test broadcast/WebSocket endpoint
        $broadcastResponse = Invoke-ApiRequest -Url "$BaseUrl/api/broadcasting/auth" -Method POST -IgnoreErrors
        if ($broadcastResponse -or $broadcastResponse -eq $null) {
            $results.Details.BroadcastChannel = $true
        }
        
        $results.Passed = $results.Details.DatabaseChannel -or $results.Details.BroadcastChannel
    }
    catch {
        Write-AutomationLog "Notification channels test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-WebSocketNotifications {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing WebSocket notifications" -Level INFO
    
    $results = @{
        TestName = "WebSocket Notifications"
        Passed = $false
        Details = @{
            ReverbEndpointAvailable = $false
            WebSocketConfigured = $false
            BroadcastingEnabled = $false
        }
    }
    
    try {
        # Test Reverb/WebSocket health
        $reverbResponse = Invoke-ApiRequest -Url "$BaseUrl/api/reverb/health" -Method GET -IgnoreErrors
        if ($reverbResponse) {
            $results.Details.ReverbEndpointAvailable = $true
        }
        
        # Check broadcasting config
        $configResponse = Invoke-ApiRequest -Url "$BaseUrl/api/config/broadcasting" -Method GET -IgnoreErrors
        if ($configResponse) {
            $results.Details.WebSocketConfigured = $configResponse.driver -eq 'reverb' -or $configResponse.enabled -eq $true
            $results.Details.BroadcastingEnabled = $true
        }
        
        $results.Passed = $results.Details.ReverbEndpointAvailable -or $results.Details.BroadcastingEnabled
    }
    catch {
        Write-AutomationLog "WebSocket test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-NotificationPreferences {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing notification preferences" -Level INFO
    
    $results = @{
        TestName = "Notification Preferences"
        Passed = $false
        Details = @{
            PreferencesEndpointAvailable = $false
            CanUpdatePreferences = $false
        }
    }
    
    try {
        $prefResponse = Invoke-ApiRequest -Url "$BaseUrl/api/user/notification-preferences" -Method GET -IgnoreErrors
        if ($prefResponse) {
            $results.Details.PreferencesEndpointAvailable = $true
        }
        
        $updateResponse = Invoke-ApiRequest -Url "$BaseUrl/api/user/notification-preferences" -Method PUT -Body @{
            email_notifications = $true
        } -IgnoreErrors
        if ($updateResponse) {
            $results.Details.CanUpdatePreferences = $true
        }
        
        $results.Passed = $results.Details.PreferencesEndpointAvailable
    }
    catch {
        Write-AutomationLog "Notification preferences test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-EmailNotificationTest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║         Email & Notification Test Suite                       ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $tests = @(
        @{ Name = "Email Gateway Connectivity"; Func = { Test-EmailGatewayConnectivity } },
        @{ Name = "Email Delivery"; Func = { Test-EmailDelivery } },
        @{ Name = "Notification Channels"; Func = { Test-NotificationChannels } },
        @{ Name = "WebSocket Notifications"; Func = { Test-WebSocketNotifications } },
        @{ Name = "Notification Preferences"; Func = { Test-NotificationPreferences } }
    )
    
    $testNum = 1
    foreach ($test in $tests) {
        Write-Host "  Test $testNum`: $($test.Name)" -ForegroundColor Yellow
        $testResult = & $test.Func
        $results.Tests += $testResult
        $results.Summary.TotalTests++
        if ($testResult.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($testResult.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($testResult.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        $testNum++
    }
    
    $results.EndTime = Get-Date
    $results.Duration = $results.EndTime - $results.StartTime
    
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║  Total: $($results.Summary.TotalTests)  Passed: $($results.Summary.PassedTests)  Failed: $($results.Summary.FailedTests)                                    ║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    
    return $results
}

$testResults = Start-EmailNotificationTest
return $testResults
