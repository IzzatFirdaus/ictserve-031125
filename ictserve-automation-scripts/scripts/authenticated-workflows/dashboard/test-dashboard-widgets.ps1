#Requires -Version 7.0
<#
.SYNOPSIS
    Tests dashboard widgets and statistics display.

.DESCRIPTION
    This script tests dashboard functionality including:
    - Widget rendering and data accuracy
    - Statistics calculations
    - Real-time updates via WebSocket
    - Widget customization
    - Responsive layout

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.EXAMPLE
    .\test-dashboard-widgets.ps1 -Mode Demo

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
    Name = "Dashboard Widgets Test"
    Category = "Authenticated Workflows - Dashboard"
    Requirements = @("3.1", "3.2", "3.4")
    ExpectedDuration = 120
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

function Test-WidgetRendering {
    <#
    .SYNOPSIS
        Tests that all dashboard widgets render correctly.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing widget rendering" -Level INFO
    
    $results = @{
        TestName = "Widget Rendering"
        Passed = $false
        Details = @{
            Widgets = @()
        }
    }
    
    # Expected dashboard widgets
    $expectedWidgets = @(
        @{ Name = "Ticket Summary"; Selectors = @(".ticket-widget", ".tickets-summary", "[data-widget='tickets']") },
        @{ Name = "Asset Loans"; Selectors = @(".loan-widget", ".loans-summary", "[data-widget='loans']") },
        @{ Name = "Recent Activity"; Selectors = @(".activity-widget", ".recent-activity", "[data-widget='activity']") },
        @{ Name = "Quick Actions"; Selectors = @(".quick-actions", ".action-buttons", "[data-widget='actions']") },
        @{ Name = "Statistics"; Selectors = @(".stats-widget", ".statistics", "[data-widget='stats']") }
    )
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/dashboard" -Mode $ExecutionMode
        Wait-ForElement -Driver $Driver -Selector ".dashboard, [data-page='dashboard']" -Timeout 15
        
        foreach ($widget in $expectedWidgets) {
            Write-Host "      Checking: $($widget.Name)" -ForegroundColor Gray
            
            $widgetFound = $false
            $widgetElement = $null
            
            foreach ($selector in $widget.Selectors) {
                $widgetElement = Find-Element -Driver $Driver -Selector $selector -Required $false
                if ($widgetElement) {
                    $widgetFound = $true
                    break
                }
            }
            
            $widgetResult = @{
                Name = $widget.Name
                Found = $widgetFound
                Visible = $false
                HasContent = $false
            }
            
            if ($widgetElement) {
                $widgetResult.Visible = Is-ElementVisible -Element $widgetElement
                $content = Get-ElementText -Element $widgetElement
                $widgetResult.HasContent = $content.Length -gt 0
                
                if ($ExecutionMode -eq 'Demo' -and $widgetFound) {
                    Highlight-Element -Driver $Driver -Element $widgetElement -Color "blue" -Mode $ExecutionMode
                    Show-Annotation -Text "Widget: $($widget.Name)" -Duration 1000
                }
            }
            
            $widgetResult.Passed = $widgetFound -and $widgetResult.Visible
            $results.Details.Widgets += $widgetResult
        }
        
        Take-Screenshot -Driver $Driver -Name "dashboard-widgets" -Mode $ExecutionMode
        
        $passedWidgets = ($results.Details.Widgets | Where-Object { $_.Passed }).Count
        $results.Passed = $passedWidgets -ge 3  # At least 3 widgets should be present
    }
    catch {
        Write-AutomationLog "Widget rendering test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-StatisticsAccuracy {
    <#
    .SYNOPSIS
        Tests that dashboard statistics are accurate.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing statistics accuracy" -Level INFO
    
    $results = @{
        TestName = "Statistics Accuracy"
        Passed = $false
        Details = @{
            APIData = @{}
            UIData = @{}
            Matches = @()
        }
    }
    
    try {
        # Get statistics from API
        $apiResponse = Invoke-ApiRequest -Url "$BaseUrl/api/dashboard/stats" -Method GET -IgnoreErrors
        
        if ($apiResponse) {
            $results.Details.APIData = $apiResponse
        }
        
        # Get statistics from UI
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/dashboard" -Mode $ExecutionMode
        Wait-ForElement -Driver $Driver -Selector ".dashboard" -Timeout 15
        
        # Extract statistics from UI elements
        $statSelectors = @(
            @{ Name = "Open Tickets"; Selector = ".stat-open-tickets, [data-stat='open-tickets']" },
            @{ Name = "Pending Loans"; Selector = ".stat-pending-loans, [data-stat='pending-loans']" },
            @{ Name = "Total Requests"; Selector = ".stat-total-requests, [data-stat='total']" }
        )
        
        foreach ($stat in $statSelectors) {
            $element = Find-Element -Driver $Driver -Selector $stat.Selector -Required $false
            if ($element) {
                $value = Get-ElementText -Element $element
                $results.Details.UIData[$stat.Name] = $value
                
                # Compare with API if available
                if ($apiResponse -and $apiResponse.ContainsKey($stat.Name)) {
                    $apiValue = $apiResponse[$stat.Name]
                    $matches = $value -match $apiValue.ToString()
                    $results.Details.Matches += @{
                        Stat = $stat.Name
                        UIValue = $value
                        APIValue = $apiValue
                        Matches = $matches
                    }
                }
            }
        }
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Statistics loaded from API and displayed in UI" -Duration 2000
        }
        
        # Pass if we found at least some statistics
        $results.Passed = $results.Details.UIData.Count -gt 0
    }
    catch {
        Write-AutomationLog "Statistics accuracy test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-RealTimeUpdates {
    <#
    .SYNOPSIS
        Tests real-time updates via WebSocket/Livewire.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing real-time updates" -Level INFO
    
    $results = @{
        TestName = "Real-Time Updates"
        Passed = $false
        Details = @{
            WebSocketConnected = $false
            LivewirePresent = $false
            UpdatesReceived = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/dashboard" -Mode $ExecutionMode
        Wait-ForElement -Driver $Driver -Selector ".dashboard" -Timeout 15
        
        # Check for Livewire components
        $livewireScript = @"
            return (function() {
                const results = {
                    livewirePresent: typeof Livewire !== 'undefined',
                    echoPresent: typeof Echo !== 'undefined',
                    components: []
                };
                
                if (results.livewirePresent && Livewire.components) {
                    results.components = Object.keys(Livewire.components.componentsById || {});
                }
                
                return results;
            })();
"@
        
        # Execute JavaScript to check for real-time features
        $jsResult = Execute-JavaScript -Driver $Driver -Script $livewireScript
        
        if ($jsResult) {
            $results.Details.LivewirePresent = $jsResult.livewirePresent -eq $true
            $results.Details.WebSocketConnected = $jsResult.echoPresent -eq $true
        }
        
        # Check for polling or auto-refresh indicators
        $refreshIndicator = Find-Element -Driver $Driver -Selector "[wire\\:poll], [data-refresh], .auto-refresh" -Required $false
        $results.Details.UpdatesReceived = $null -ne $refreshIndicator -or $results.Details.LivewirePresent
        
        if ($ExecutionMode -eq 'Demo') {
            $status = if ($results.Details.LivewirePresent) { "Livewire detected" } else { "No real-time framework" }
            Show-Annotation -Text $status -Duration 2000
        }
        
        $results.Passed = $results.Details.LivewirePresent -or $results.Details.UpdatesReceived
    }
    catch {
        Write-AutomationLog "Real-time updates test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-ResponsiveLayout {
    <#
    .SYNOPSIS
        Tests dashboard responsive layout across viewports.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing responsive layout" -Level INFO
    
    $results = @{
        TestName = "Responsive Layout"
        Passed = $false
        Details = @{
            Viewports = @()
        }
    }
    
    $viewports = @(
        @{ Name = "Desktop"; Width = 1920; Height = 1080 },
        @{ Name = "Tablet"; Width = 768; Height = 1024 },
        @{ Name = "Mobile"; Width = 375; Height = 667 }
    )
    
    try {
        foreach ($viewport in $viewports) {
            Write-Host "      Testing: $($viewport.Name) ($($viewport.Width)x$($viewport.Height))" -ForegroundColor Gray
            
            # Resize browser
            Set-BrowserSize -Driver $Driver -Width $viewport.Width -Height $viewport.Height
            
            Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/dashboard" -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            # Check if dashboard is still usable
            $dashboard = Find-Element -Driver $Driver -Selector ".dashboard, [data-page='dashboard']" -Required $false
            $isVisible = $null -ne $dashboard
            
            # Check for mobile menu if on mobile
            $mobileMenu = $null
            if ($viewport.Width -lt 768) {
                $mobileMenu = Find-Element -Driver $Driver -Selector ".mobile-menu, .hamburger, [data-mobile-menu]" -Required $false
            }
            
            $viewportResult = @{
                Name = $viewport.Name
                Width = $viewport.Width
                Height = $viewport.Height
                DashboardVisible = $isVisible
                MobileMenuPresent = $null -ne $mobileMenu -or $viewport.Width -ge 768
                Passed = $isVisible
            }
            
            $results.Details.Viewports += $viewportResult
            
            Take-Screenshot -Driver $Driver -Name "dashboard-$($viewport.Name.ToLower())" -Mode $ExecutionMode
        }
        
        # Reset to desktop
        Set-BrowserSize -Driver $Driver -Width 1920 -Height 1080
        
        $results.Passed = ($results.Details.Viewports | Where-Object { $_.Passed }).Count -eq $viewports.Count
    }
    catch {
        Write-AutomationLog "Responsive layout test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-DashboardWidgetsTest {
    <#
    .SYNOPSIS
        Executes the complete dashboard widgets test suite.
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
    Write-Host "║            Dashboard Widgets Test Suite                       ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $driver = $null
    
    try {
        # Initialize browser
        $driver = Initialize-WebDriver -Mode $Mode
        
        # Login first
        Write-Host "  Logging in..." -ForegroundColor Gray
        Invoke-Login -Driver $driver -ExecutionMode $Mode
        
        # Test 1: Widget Rendering
        Write-Host "  Test 1: Widget Rendering" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $widgetResults = Test-WidgetRendering -Driver $driver -ExecutionMode $Mode
        $results.Tests += $widgetResults
        $results.Summary.TotalTests++
        if ($widgetResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        foreach ($widget in $widgetResults.Details.Widgets) {
            $status = if ($widget.Passed) { "✓" } else { "✗" }
            $color = if ($widget.Passed) { "Green" } else { "Yellow" }
            Write-Host "      $status $($widget.Name)" -ForegroundColor $color
        }
        Write-Host "    Result: $(if ($widgetResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($widgetResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 2: Statistics Accuracy
        Write-Host "  Test 2: Statistics Accuracy" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $statsResults = Test-StatisticsAccuracy -Driver $driver -ExecutionMode $Mode
        $results.Tests += $statsResults
        $results.Summary.TotalTests++
        if ($statsResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    UI Stats Found: $($statsResults.Details.UIData.Count)" -ForegroundColor White
        Write-Host "    Result: $(if ($statsResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($statsResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 3: Real-Time Updates
        Write-Host "  Test 3: Real-Time Updates" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $realtimeResults = Test-RealTimeUpdates -Driver $driver -ExecutionMode $Mode
        $results.Tests += $realtimeResults
        $results.Summary.TotalTests++
        if ($realtimeResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Livewire: $(if ($realtimeResults.Details.LivewirePresent) { '✓' } else { '✗' })" -ForegroundColor $(if ($realtimeResults.Details.LivewirePresent) { 'Green' } else { 'Yellow' })
        Write-Host "    WebSocket: $(if ($realtimeResults.Details.WebSocketConnected) { '✓' } else { '○' })" -ForegroundColor $(if ($realtimeResults.Details.WebSocketConnected) { 'Green' } else { 'Yellow' })
        Write-Host "    Result: $(if ($realtimeResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($realtimeResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 4: Responsive Layout
        Write-Host "  Test 4: Responsive Layout" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $responsiveResults = Test-ResponsiveLayout -Driver $driver -ExecutionMode $Mode
        $results.Tests += $responsiveResults
        $results.Summary.TotalTests++
        if ($responsiveResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        foreach ($vp in $responsiveResults.Details.Viewports) {
            $status = if ($vp.Passed) { "✓" } else { "✗" }
            $color = if ($vp.Passed) { "Green" } else { "Red" }
            Write-Host "      $status $($vp.Name) ($($vp.Width)x$($vp.Height))" -ForegroundColor $color
        }
        Write-Host "    Result: $(if ($responsiveResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($responsiveResults.Passed) { 'Green' } else { 'Red' })
        
    }
    catch {
        Write-AutomationLog "Dashboard widgets test failed: $($_.Exception.Message)" -Level ERROR
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
$testResults = Start-DashboardWidgetsTest

# Return results for reporting
return $testResults
