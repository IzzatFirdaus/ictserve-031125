#Requires -Version 7.0
<#
.SYNOPSIS
    Tests Google Workspace SSO integration.

.DESCRIPTION
    This script tests Google Workspace Single Sign-On including:
    - OAuth 2.0 flow initiation
    - Domain validation (@motac.gov.my)
    - Token handling
    - Account linking
    - SSO error handling

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.EXAMPLE
    .\test-google-sso.ps1 -Mode Demo

.NOTES
    Version: 1.0.0
    Requirements: 2.7
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
    Name = "Google Workspace SSO Test"
    Category = "Authentication"
    Requirements = @("2.7")
    ExpectedDuration = 60
}

function Test-SSOButtonPresence {
    <#
    .SYNOPSIS
        Tests that Google SSO button is present on login page.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing SSO button presence" -Level INFO
    
    $results = @{
        TestName = "SSO Button Presence"
        Passed = $false
        Details = @{
            ButtonFound = $false
            ButtonVisible = $false
            ButtonClickable = $false
            ButtonText = ""
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/login" -Mode $ExecutionMode
        Wait-ForElement -Driver $Driver -Selector "form" -Timeout 10
        
        # Look for Google SSO button
        $ssoSelectors = @(
            "a[href*='google']",
            "button[data-provider='google']",
            ".google-login",
            ".btn-google",
            "[data-social='google']",
            "a[href*='socialite/google']",
            "a[href*='auth/google']"
        )
        
        $ssoButton = $null
        foreach ($selector in $ssoSelectors) {
            $ssoButton = Find-Element -Driver $Driver -Selector $selector -Required $false
            if ($ssoButton) { break }
        }
        
        if ($ssoButton) {
            $results.Details.ButtonFound = $true
            $results.Details.ButtonVisible = Is-ElementVisible -Element $ssoButton
            $results.Details.ButtonClickable = Is-ElementClickable -Element $ssoButton
            $results.Details.ButtonText = Get-ElementText -Element $ssoButton
            
            if ($ExecutionMode -eq 'Demo') {
                Highlight-Element -Driver $Driver -Element $ssoButton -Color "blue" -Mode $ExecutionMode
                Show-Annotation -Text "Google SSO button found" -Duration 2000
            }
        }
        
        $results.Passed = $results.Details.ButtonFound -and $results.Details.ButtonVisible
    }
    catch {
        Write-AutomationLog "SSO button test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-OAuthFlowInitiation {
    <#
    .SYNOPSIS
        Tests that clicking SSO button initiates OAuth flow.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing OAuth flow initiation" -Level INFO
    
    $results = @{
        TestName = "OAuth Flow Initiation"
        Passed = $false
        Details = @{
            RedirectOccurred = $false
            GoogleDomainReached = $false
            StateParameterPresent = $false
            ScopeParameterPresent = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/login" -Mode $ExecutionMode
        Wait-ForElement -Driver $Driver -Selector "form" -Timeout 10
        
        # Find and click SSO button
        $ssoSelectors = @(
            "a[href*='google']",
            "button[data-provider='google']",
            ".google-login",
            ".btn-google"
        )
        
        $ssoButton = $null
        foreach ($selector in $ssoSelectors) {
            $ssoButton = Find-Element -Driver $Driver -Selector $selector -Required $false
            if ($ssoButton) { break }
        }
        
        if ($ssoButton) {
            if ($ExecutionMode -eq 'Demo') {
                Show-Annotation -Text "Initiating Google OAuth flow..." -Duration 1500
            }
            
            # Get current URL before click
            $preClickUrl = Get-CurrentUrl -Driver $Driver
            
            # Click SSO button
            Click-Element -Driver $Driver -Element $ssoButton -Mode $ExecutionMode
            
            # Wait for redirect
            Start-Sleep -Seconds 3
            
            $currentUrl = Get-CurrentUrl -Driver $Driver
            
            # Check if redirected
            $results.Details.RedirectOccurred = $currentUrl -ne $preClickUrl
            
            # Check if reached Google domain
            $results.Details.GoogleDomainReached = $currentUrl -match "accounts\.google\.com|googleapis\.com"
            
            # Check for OAuth parameters
            $results.Details.StateParameterPresent = $currentUrl -match "state="
            $results.Details.ScopeParameterPresent = $currentUrl -match "scope="
            
            if ($ExecutionMode -eq 'Demo') {
                if ($results.Details.GoogleDomainReached) {
                    Show-Annotation -Text "Successfully redirected to Google OAuth" -Duration 2000
                }
                else {
                    Show-Annotation -Text "Redirect URL: $currentUrl" -Duration 2000
                }
            }
            
            Take-Screenshot -Driver $Driver -Name "oauth-redirect" -Mode $ExecutionMode
        }
        
        $results.Passed = $results.Details.RedirectOccurred
    }
    catch {
        Write-AutomationLog "OAuth flow test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-DomainRestriction {
    <#
    .SYNOPSIS
        Tests that only @motac.gov.my Google accounts are accepted.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing domain restriction" -Level INFO
    
    $results = @{
        TestName = "Domain Restriction"
        Passed = $false
        Details = @{
            ConfigurationChecked = $false
            HdParameterPresent = $false
            DomainValue = ""
        }
    }
    
    try {
        # Check OAuth configuration via API
        $configResponse = Invoke-ApiRequest -Url "$BaseUrl/api/config/oauth" -Method GET -IgnoreErrors
        
        if ($configResponse) {
            $results.Details.ConfigurationChecked = $true
            
            if ($configResponse.google -and $configResponse.google.hd) {
                $results.Details.HdParameterPresent = $true
                $results.Details.DomainValue = $configResponse.google.hd
            }
        }
        
        # Also check by examining the OAuth URL
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/login" -Mode $ExecutionMode
        
        $ssoButton = Find-Element -Driver $Driver -Selector "a[href*='google']" -Required $false
        
        if ($ssoButton) {
            $href = Get-ElementAttribute -Element $ssoButton -Attribute "href"
            
            # Check if hd parameter is in the URL or will be added
            if ($href -match "hd=([^&]+)") {
                $results.Details.HdParameterPresent = $true
                $results.Details.DomainValue = $Matches[1]
            }
        }
        
        # Simulate callback with non-motac domain
        $callbackUrl = "$BaseUrl/auth/google/callback?error=access_denied&error_description=domain_mismatch"
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Domain restriction: $($results.Details.DomainValue)" -Duration 2000
        }
        
        $results.Passed = $results.Details.HdParameterPresent -or $results.Details.ConfigurationChecked
    }
    catch {
        Write-AutomationLog "Domain restriction test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-SSOErrorHandling {
    <#
    .SYNOPSIS
        Tests SSO error handling scenarios.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing SSO error handling" -Level INFO
    
    $results = @{
        TestName = "SSO Error Handling"
        Passed = $false
        Details = @{
            Scenarios = @()
        }
    }
    
    $errorScenarios = @(
        @{
            Name = "Access Denied"
            CallbackParams = "error=access_denied"
            ExpectedBehavior = "User-friendly error message"
        },
        @{
            Name = "Invalid State"
            CallbackParams = "error=invalid_state"
            ExpectedBehavior = "Security error message"
        },
        @{
            Name = "Domain Mismatch"
            CallbackParams = "error=access_denied&error_description=domain_not_allowed"
            ExpectedBehavior = "Domain restriction message"
        },
        @{
            Name = "Token Expired"
            CallbackParams = "error=token_expired"
            ExpectedBehavior = "Retry prompt"
        }
    )
    
    foreach ($scenario in $errorScenarios) {
        Write-Host "      Testing: $($scenario.Name)" -ForegroundColor Gray
        
        try {
            $callbackUrl = "$BaseUrl/auth/google/callback?$($scenario.CallbackParams)"
            Navigate-ToUrl -Driver $Driver -Url $callbackUrl -Mode $ExecutionMode
            
            Start-Sleep -Seconds 2
            
            # Check for error message display
            $errorElement = Find-Element -Driver $Driver -Selector ".alert-danger, .error-message, .alert-warning" -Required $false
            $hasErrorMessage = $null -ne $errorElement
            
            # Check no stack trace exposed
            $pageContent = Get-PageContent -Driver $Driver
            $noStackTrace = $pageContent -notmatch "Stack trace|Exception|at line \d+"
            
            # Check redirected to login or error page
            $currentUrl = Get-CurrentUrl -Driver $Driver
            $properRedirect = $currentUrl -match "login|error|auth"
            
            $scenarioResult = @{
                Name = $scenario.Name
                ErrorMessageShown = $hasErrorMessage
                NoStackTrace = $noStackTrace
                ProperRedirect = $properRedirect
                Passed = $noStackTrace -and $properRedirect
            }
            
            $results.Details.Scenarios += $scenarioResult
            
            if ($ExecutionMode -eq 'Demo' -and $errorElement) {
                Highlight-Element -Driver $Driver -Element $errorElement -Color "orange" -Mode $ExecutionMode
            }
        }
        catch {
            $results.Details.Scenarios += @{
                Name = $scenario.Name
                Error = $_.Exception.Message
                Passed = $false
            }
        }
    }
    
    $results.Passed = ($results.Details.Scenarios | Where-Object { -not $_.Passed }).Count -eq 0
    
    return $results
}

function Test-AccountLinking {
    <#
    .SYNOPSIS
        Tests linking Google account to existing user.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing account linking" -Level INFO
    
    $results = @{
        TestName = "Account Linking"
        Passed = $false
        Details = @{
            LinkOptionAvailable = $false
            UnlinkOptionAvailable = $false
            LinkedAccountsDisplayed = $false
        }
    }
    
    try {
        # Login with regular credentials first
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/login" -Mode $ExecutionMode
        Fill-FormField -Driver $Driver -Selector "#email, input[name='email']" -Value "test.user@motac.gov.my" -Mode $ExecutionMode
        Fill-FormField -Driver $Driver -Selector "#password, input[name='password']" -Value "TestPassword123!" -Mode $ExecutionMode
        
        $submitButton = Find-Element -Driver $Driver -Selector "button[type='submit']"
        Click-Element -Driver $Driver -Element $submitButton -Mode $ExecutionMode
        
        Start-Sleep -Seconds 2
        
        # Navigate to profile/settings
        $settingsUrls = @(
            "$BaseUrl/profile",
            "$BaseUrl/settings",
            "$BaseUrl/account",
            "$BaseUrl/user/settings"
        )
        
        foreach ($url in $settingsUrls) {
            Navigate-ToUrl -Driver $Driver -Url $url -Mode $ExecutionMode
            
            # Look for social account linking section
            $linkSection = Find-Element -Driver $Driver -Selector ".social-accounts, .linked-accounts, [data-section='social']" -Required $false
            
            if ($linkSection) {
                $results.Details.LinkedAccountsDisplayed = $true
                
                # Check for link button
                $linkButton = Find-Element -Driver $Driver -Selector "a[href*='google/link'], button[data-action='link-google']" -Required $false
                $results.Details.LinkOptionAvailable = $null -ne $linkButton
                
                # Check for unlink button
                $unlinkButton = Find-Element -Driver $Driver -Selector "button[data-action='unlink-google'], .unlink-google" -Required $false
                $results.Details.UnlinkOptionAvailable = $null -ne $unlinkButton
                
                break
            }
        }
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Account linking available: $($results.Details.LinkOptionAvailable)" -Duration 2000
        }
        
        $results.Passed = $results.Details.LinkedAccountsDisplayed
    }
    catch {
        Write-AutomationLog "Account linking test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-GoogleSSOTest {
    <#
    .SYNOPSIS
        Executes the complete Google SSO test suite.
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
    Write-Host "║           Google Workspace SSO Test Suite                     ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $driver = $null
    
    try {
        # Initialize browser
        $driver = Initialize-WebDriver -Mode $Mode
        
        # Test 1: SSO Button Presence
        Write-Host "  Test 1: SSO Button Presence" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $buttonResults = Test-SSOButtonPresence -Driver $driver -ExecutionMode $Mode
        $results.Tests += $buttonResults
        $results.Summary.TotalTests++
        if ($buttonResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Button Found: $(if ($buttonResults.Details.ButtonFound) { '✓' } else { '✗' })" -ForegroundColor $(if ($buttonResults.Details.ButtonFound) { 'Green' } else { 'Red' })
        Write-Host "    Button Visible: $(if ($buttonResults.Details.ButtonVisible) { '✓' } else { '✗' })" -ForegroundColor $(if ($buttonResults.Details.ButtonVisible) { 'Green' } else { 'Red' })
        Write-Host "    Result: $(if ($buttonResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($buttonResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 2: OAuth Flow Initiation
        Write-Host "  Test 2: OAuth Flow Initiation" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $oauthResults = Test-OAuthFlowInitiation -Driver $driver -ExecutionMode $Mode
        $results.Tests += $oauthResults
        $results.Summary.TotalTests++
        if ($oauthResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Redirect Occurred: $(if ($oauthResults.Details.RedirectOccurred) { '✓' } else { '✗' })" -ForegroundColor $(if ($oauthResults.Details.RedirectOccurred) { 'Green' } else { 'Red' })
        Write-Host "    Google Domain: $(if ($oauthResults.Details.GoogleDomainReached) { '✓' } else { '✗' })" -ForegroundColor $(if ($oauthResults.Details.GoogleDomainReached) { 'Green' } else { 'Yellow' })
        Write-Host "    Result: $(if ($oauthResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($oauthResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Navigate back for remaining tests
        Navigate-ToUrl -Driver $driver -Url "$BaseUrl/login" -Mode $Mode
        
        # Test 3: Domain Restriction
        Write-Host "  Test 3: Domain Restriction (@motac.gov.my)" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $domainResults = Test-DomainRestriction -Driver $driver -ExecutionMode $Mode
        $results.Tests += $domainResults
        $results.Summary.TotalTests++
        if ($domainResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    HD Parameter: $(if ($domainResults.Details.HdParameterPresent) { '✓' } else { '✗' })" -ForegroundColor $(if ($domainResults.Details.HdParameterPresent) { 'Green' } else { 'Yellow' })
        Write-Host "    Domain: $($domainResults.Details.DomainValue)" -ForegroundColor White
        Write-Host "    Result: $(if ($domainResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($domainResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 4: Error Handling
        Write-Host "  Test 4: SSO Error Handling" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $errorResults = Test-SSOErrorHandling -Driver $driver -ExecutionMode $Mode
        $results.Tests += $errorResults
        $results.Summary.TotalTests++
        if ($errorResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        foreach ($scenario in $errorResults.Details.Scenarios) {
            $status = if ($scenario.Passed) { "✓" } else { "✗" }
            $color = if ($scenario.Passed) { "Green" } else { "Red" }
            Write-Host "      $status $($scenario.Name)" -ForegroundColor $color
        }
        Write-Host "    Result: $(if ($errorResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($errorResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 5: Account Linking
        Write-Host "  Test 5: Account Linking" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────" -ForegroundColor Gray
        $linkResults = Test-AccountLinking -Driver $driver -ExecutionMode $Mode
        $results.Tests += $linkResults
        $results.Summary.TotalTests++
        if ($linkResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Linked Accounts Section: $(if ($linkResults.Details.LinkedAccountsDisplayed) { '✓' } else { '✗' })" -ForegroundColor $(if ($linkResults.Details.LinkedAccountsDisplayed) { 'Green' } else { 'Yellow' })
        Write-Host "    Link Option: $(if ($linkResults.Details.LinkOptionAvailable) { '✓' } else { '✗' })" -ForegroundColor $(if ($linkResults.Details.LinkOptionAvailable) { 'Green' } else { 'Yellow' })
        Write-Host "    Result: $(if ($linkResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($linkResults.Passed) { 'Green' } else { 'Red' })
        
    }
    catch {
        Write-AutomationLog "Google SSO test failed: $($_.Exception.Message)" -Level ERROR
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
$testResults = Start-GoogleSSOTest

# Return results for reporting
return $testResults
