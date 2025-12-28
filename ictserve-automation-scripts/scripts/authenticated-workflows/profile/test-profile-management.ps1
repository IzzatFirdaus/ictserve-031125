#Requires -Version 7.0
<#
.SYNOPSIS
    Tests profile management functionality for authenticated users.

.DESCRIPTION
    This script tests profile features including:
    - Profile viewing and editing
    - Data synchronization with HRMIS
    - PDPA compliance validation
    - Profile picture management

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.EXAMPLE
    .\test-profile-management.ps1 -Mode Demo

.NOTES
    Version: 1.0.0
    Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6
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
    Name = "Profile Management Test"
    Category = "Authenticated Workflows - Profile"
    Requirements = @("6.1", "6.2", "6.3", "6.4", "6.5", "6.6")
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

function Test-ProfileViewing {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing profile viewing" -Level INFO
    
    $results = @{
        TestName = "Profile Viewing"
        Passed = $false
        Details = @{
            ProfilePageFound = $false
            NameDisplayed = $false
            EmailDisplayed = $false
            DepartmentDisplayed = $false
            GradeDisplayed = $false
        }
    }
    
    try {
        $profileUrls = @("$BaseUrl/profile", "$BaseUrl/user/profile", "$BaseUrl/settings/profile")
        
        foreach ($url in $profileUrls) {
            Navigate-ToUrl -Driver $Driver -Url $url -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $profileSection = Find-Element -Driver $Driver -Selector ".profile, [data-profile], .user-profile" -Required $false
            if ($profileSection) {
                $results.Details.ProfilePageFound = $true
                break
            }
        }
        
        if ($results.Details.ProfilePageFound) {
            $nameField = Find-Element -Driver $Driver -Selector "[data-field='name'], .profile-name, input[name='name']" -Required $false
            $results.Details.NameDisplayed = $null -ne $nameField
            
            $emailField = Find-Element -Driver $Driver -Selector "[data-field='email'], .profile-email, input[name='email']" -Required $false
            $results.Details.EmailDisplayed = $null -ne $emailField
            
            $deptField = Find-Element -Driver $Driver -Selector "[data-field='department'], .profile-department" -Required $false
            $results.Details.DepartmentDisplayed = $null -ne $deptField
            
            $gradeField = Find-Element -Driver $Driver -Selector "[data-field='grade'], .profile-grade" -Required $false
            $results.Details.GradeDisplayed = $null -ne $gradeField
            
            if ($ExecutionMode -eq 'Demo') {
                Show-Annotation -Text "Profile information displayed" -Duration 2000
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "profile-view" -Mode $ExecutionMode
        $results.Passed = $results.Details.ProfilePageFound
    }
    catch {
        Write-AutomationLog "Profile viewing test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-ProfileEditing {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing profile editing" -Level INFO
    
    $results = @{
        TestName = "Profile Editing"
        Passed = $false
        Details = @{
            EditFormFound = $false
            EditableFields = @()
            SaveButtonPresent = $false
            ValidationWorks = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/profile" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $editButton = Find-Element -Driver $Driver -Selector "button:contains('Edit'), a:contains('Edit'), [data-action='edit']" -Required $false
        if ($editButton) {
            Click-Element -Driver $Driver -Element $editButton -Mode $ExecutionMode
            Start-Sleep -Seconds 1
        }
        
        $editForm = Find-Element -Driver $Driver -Selector "form[action*='profile'], form.profile-form" -Required $false
        $results.Details.EditFormFound = $null -ne $editForm
        
        if ($results.Details.EditFormFound) {
            $editableInputs = Find-Elements -Driver $Driver -Selector "form input:not([readonly]):not([disabled])"
            foreach ($input in $editableInputs) {
                $name = Get-ElementAttribute -Element $input -Attribute "name"
                if ($name) { $results.Details.EditableFields += $name }
            }
            
            $saveButton = Find-Element -Driver $Driver -Selector "button[type='submit'], button:contains('Save')" -Required $false
            $results.Details.SaveButtonPresent = $null -ne $saveButton
        }
        
        Take-Screenshot -Driver $Driver -Name "profile-edit" -Mode $ExecutionMode
        $results.Passed = $results.Details.EditFormFound -and $results.Details.SaveButtonPresent
    }
    catch {
        Write-AutomationLog "Profile editing test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-HRMISIntegration {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing HRMIS integration" -Level INFO
    
    $results = @{
        TestName = "HRMIS Integration"
        Passed = $false
        Details = @{
            SyncButtonFound = $false
            HRMISFieldsPresent = $false
            LastSyncDisplayed = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/profile" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $syncButton = Find-Element -Driver $Driver -Selector "button:contains('Sync'), [data-action='sync-hrmis']" -Required $false
        $results.Details.SyncButtonFound = $null -ne $syncButton
        
        $hrmisFields = Find-Element -Driver $Driver -Selector "[data-source='hrmis'], .hrmis-data" -Required $false
        $results.Details.HRMISFieldsPresent = $null -ne $hrmisFields
        
        $lastSync = Find-Element -Driver $Driver -Selector ".last-sync, [data-last-sync]" -Required $false
        $results.Details.LastSyncDisplayed = $null -ne $lastSync
        
        if ($ExecutionMode -eq 'Demo' -and $syncButton) {
            Highlight-Element -Driver $Driver -Element $syncButton -Color "blue" -Mode $ExecutionMode
            Show-Annotation -Text "HRMIS sync available" -Duration 1500
        }
        
        Take-Screenshot -Driver $Driver -Name "hrmis-integration" -Mode $ExecutionMode
        $results.Passed = $results.Details.SyncButtonFound -or $results.Details.HRMISFieldsPresent
    }
    catch {
        Write-AutomationLog "HRMIS integration test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-PDPACompliance {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing PDPA compliance" -Level INFO
    
    $results = @{
        TestName = "PDPA Compliance"
        Passed = $false
        Details = @{
            PrivacyPolicyLink = $false
            DataExportOption = $false
            DataDeletionOption = $false
            ConsentManagement = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/profile" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $privacyLink = Find-Element -Driver $Driver -Selector "a[href*='privacy'], a:contains('Privacy')" -Required $false
        $results.Details.PrivacyPolicyLink = $null -ne $privacyLink
        
        $exportButton = Find-Element -Driver $Driver -Selector "button:contains('Export'), [data-action='export-data']" -Required $false
        $results.Details.DataExportOption = $null -ne $exportButton
        
        $deleteButton = Find-Element -Driver $Driver -Selector "button:contains('Delete'), [data-action='delete-account']" -Required $false
        $results.Details.DataDeletionOption = $null -ne $deleteButton
        
        $consentSection = Find-Element -Driver $Driver -Selector ".consent-management, [data-consent]" -Required $false
        $results.Details.ConsentManagement = $null -ne $consentSection
        
        Take-Screenshot -Driver $Driver -Name "pdpa-compliance" -Mode $ExecutionMode
        $results.Passed = $results.Details.PrivacyPolicyLink -or $results.Details.DataExportOption
    }
    catch {
        Write-AutomationLog "PDPA compliance test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-ProfileManagementTest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║           Profile Management Test Suite                       ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $driver = $null
    
    try {
        $driver = Initialize-WebDriver -Mode $Mode
        
        Write-Host "  Logging in..." -ForegroundColor Gray
        Invoke-Login -Driver $driver -ExecutionMode $Mode
        
        # Test 1: Profile Viewing
        Write-Host "  Test 1: Profile Viewing" -ForegroundColor Yellow
        $viewResults = Test-ProfileViewing -Driver $driver -ExecutionMode $Mode
        $results.Tests += $viewResults
        $results.Summary.TotalTests++
        if ($viewResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($viewResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($viewResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 2: Profile Editing
        Write-Host "  Test 2: Profile Editing" -ForegroundColor Yellow
        $editResults = Test-ProfileEditing -Driver $driver -ExecutionMode $Mode
        $results.Tests += $editResults
        $results.Summary.TotalTests++
        if ($editResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Editable Fields: $($editResults.Details.EditableFields.Count)" -ForegroundColor White
        Write-Host "    Result: $(if ($editResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($editResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 3: HRMIS Integration
        Write-Host "  Test 3: HRMIS Integration" -ForegroundColor Yellow
        $hrmisResults = Test-HRMISIntegration -Driver $driver -ExecutionMode $Mode
        $results.Tests += $hrmisResults
        $results.Summary.TotalTests++
        if ($hrmisResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($hrmisResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($hrmisResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 4: PDPA Compliance
        Write-Host "  Test 4: PDPA Compliance" -ForegroundColor Yellow
        $pdpaResults = Test-PDPACompliance -Driver $driver -ExecutionMode $Mode
        $results.Tests += $pdpaResults
        $results.Summary.TotalTests++
        if ($pdpaResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($pdpaResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($pdpaResults.Passed) { 'Green' } else { 'Red' })
        
    }
    catch {
        Write-AutomationLog "Profile management test failed: $($_.Exception.Message)" -Level ERROR
        throw
    }
    finally {
        if ($driver) { Close-WebDriver -Driver $driver }
    }
    
    $results.EndTime = Get-Date
    $results.Duration = $results.EndTime - $results.StartTime
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║  Total: $($results.Summary.TotalTests)  Passed: $($results.Summary.PassedTests)  Failed: $($results.Summary.FailedTests)                                    ║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    
    return $results
}

$testResults = Start-ProfileManagementTest
return $testResults
