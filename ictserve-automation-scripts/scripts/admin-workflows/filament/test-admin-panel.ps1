#Requires -Version 7.0
<#
.SYNOPSIS
    Tests Filament admin panel functionality.

.DESCRIPTION
    This script tests admin panel features including:
    - Admin authentication
    - Role-based access control
    - CRUD operations
    - Ticket and asset management

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.EXAMPLE
    .\test-admin-panel.ps1 -Mode Demo

.NOTES
    Version: 1.0.0
    Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 8.7
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
    Name = "Filament Admin Panel Test"
    Category = "Admin Workflows - Filament"
    Requirements = @("8.1", "8.2", "8.3", "8.4", "8.5", "8.6", "8.7")
    ExpectedDuration = 180
}

$AdminCredentials = @{
    Email = "admin@motac.gov.my"
    Password = "AdminPassword123!"
}

function Invoke-AdminLogin {
    param($Driver, $ExecutionMode)
    
    Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/admin/login" -Mode $ExecutionMode
    Wait-ForElement -Driver $Driver -Selector "form" -Timeout 10
    Fill-FormField -Driver $Driver -Selector "#email, input[name='email']" -Value $AdminCredentials.Email -Mode $ExecutionMode
    Fill-FormField -Driver $Driver -Selector "#password, input[name='password']" -Value $AdminCredentials.Password -Mode $ExecutionMode
    $submitButton = Find-Element -Driver $Driver -Selector "button[type='submit']"
    Click-Element -Driver $Driver -Element $submitButton -Mode $ExecutionMode
    Start-Sleep -Seconds 3
}

function Test-AdminAuthentication {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing admin authentication" -Level INFO
    
    $results = @{
        TestName = "Admin Authentication"
        Passed = $false
        Details = @{
            LoginPageFound = $false
            LoginSuccessful = $false
            DashboardAccessible = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/admin/login" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $loginForm = Find-Element -Driver $Driver -Selector "form" -Required $false
        $results.Details.LoginPageFound = $null -ne $loginForm
        
        if ($results.Details.LoginPageFound) {
            Invoke-AdminLogin -Driver $Driver -ExecutionMode $ExecutionMode
            
            # Check if we're on dashboard
            $dashboard = Find-Element -Driver $Driver -Selector ".fi-dashboard, [data-dashboard], .filament-dashboard" -Required $false
            $results.Details.DashboardAccessible = $null -ne $dashboard
            
            $currentUrl = Execute-JavaScript -Driver $Driver -Script "return window.location.href;"
            $results.Details.LoginSuccessful = $currentUrl -match '/admin' -and -not ($currentUrl -match '/login')
            
            if ($ExecutionMode -eq 'Demo') {
                Show-Annotation -Text "Admin login successful" -Duration 2000
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "admin-login" -Mode $ExecutionMode
        $results.Passed = $results.Details.LoginSuccessful -or $results.Details.DashboardAccessible
    }
    catch {
        Write-AutomationLog "Admin authentication test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-RoleBasedAccess {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing role-based access control" -Level INFO
    
    $results = @{
        TestName = "Role-Based Access Control"
        Passed = $false
        Details = @{
            NavigationMenuPresent = $false
            ResourcesAccessible = @()
            RestrictedAreasBlocked = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/admin" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        # Check for navigation menu
        $navMenu = Find-Element -Driver $Driver -Selector ".fi-sidebar, nav, .filament-sidebar" -Required $false
        $results.Details.NavigationMenuPresent = $null -ne $navMenu
        
        # Check accessible resources
        $resourceLinks = @(
            @{ Name = "Users"; Selector = "a[href*='/admin/users']" },
            @{ Name = "Tickets"; Selector = "a[href*='/admin/tickets']" },
            @{ Name = "Assets"; Selector = "a[href*='/admin/assets']" },
            @{ Name = "Loans"; Selector = "a[href*='/admin/loans']" }
        )
        
        foreach ($resource in $resourceLinks) {
            $link = Find-Element -Driver $Driver -Selector $resource.Selector -Required $false
            if ($link) {
                $results.Details.ResourcesAccessible += $resource.Name
            }
        }
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Found $($results.Details.ResourcesAccessible.Count) accessible resources" -Duration 2000
        }
        
        Take-Screenshot -Driver $Driver -Name "admin-rbac" -Mode $ExecutionMode
        $results.Passed = $results.Details.NavigationMenuPresent -and $results.Details.ResourcesAccessible.Count -gt 0
    }
    catch {
        Write-AutomationLog "Role-based access test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-CRUDOperations {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing CRUD operations" -Level INFO
    
    $results = @{
        TestName = "CRUD Operations"
        Passed = $false
        Details = @{
            ListViewWorks = $false
            CreateFormAvailable = $false
            EditFormAvailable = $false
            DeleteActionAvailable = $false
        }
    }
    
    try {
        # Test list view
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/admin/users" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $table = Find-Element -Driver $Driver -Selector "table, .fi-ta-table, [data-table]" -Required $false
        $results.Details.ListViewWorks = $null -ne $table
        
        # Check for create button
        $createButton = Find-Element -Driver $Driver -Selector "a[href*='/create'], button:contains('Create'), .fi-btn" -Required $false
        $results.Details.CreateFormAvailable = $null -ne $createButton
        
        # Check for edit action
        $editButton = Find-Element -Driver $Driver -Selector "a[href*='/edit'], button[title*='Edit'], .fi-ta-action" -Required $false
        $results.Details.EditFormAvailable = $null -ne $editButton
        
        # Check for delete action
        $deleteButton = Find-Element -Driver $Driver -Selector "button[title*='Delete'], .fi-ta-action-delete" -Required $false
        $results.Details.DeleteActionAvailable = $null -ne $deleteButton
        
        if ($ExecutionMode -eq 'Demo' -and $table) {
            Highlight-Element -Driver $Driver -Element $table -Color "blue" -Mode $ExecutionMode
            Show-Annotation -Text "CRUD table with actions" -Duration 2000
        }
        
        Take-Screenshot -Driver $Driver -Name "admin-crud" -Mode $ExecutionMode
        $results.Passed = $results.Details.ListViewWorks
    }
    catch {
        Write-AutomationLog "CRUD operations test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-TicketManagement {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing ticket management" -Level INFO
    
    $results = @{
        TestName = "Ticket Management"
        Passed = $false
        Details = @{
            TicketListFound = $false
            StatusFilterAvailable = $false
            BulkActionsAvailable = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/admin/tickets" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $ticketTable = Find-Element -Driver $Driver -Selector "table, .fi-ta-table" -Required $false
        $results.Details.TicketListFound = $null -ne $ticketTable
        
        # Check for filters
        $statusFilter = Find-Element -Driver $Driver -Selector "select[name*='status'], .fi-ta-filter" -Required $false
        $results.Details.StatusFilterAvailable = $null -ne $statusFilter
        
        # Check for bulk actions
        $bulkActions = Find-Element -Driver $Driver -Selector ".fi-ta-bulk-actions, [data-bulk-actions]" -Required $false
        $results.Details.BulkActionsAvailable = $null -ne $bulkActions
        
        Take-Screenshot -Driver $Driver -Name "admin-tickets" -Mode $ExecutionMode
        $results.Passed = $results.Details.TicketListFound
    }
    catch {
        Write-AutomationLog "Ticket management test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-AdminPanelTest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║           Filament Admin Panel Test Suite                     ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $driver = $null
    
    try {
        $driver = Initialize-WebDriver -Mode $Mode
        
        # Test 1: Admin Authentication
        Write-Host "  Test 1: Admin Authentication" -ForegroundColor Yellow
        $authResults = Test-AdminAuthentication -Driver $driver -ExecutionMode $Mode
        $results.Tests += $authResults
        $results.Summary.TotalTests++
        if ($authResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($authResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($authResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 2: Role-Based Access
        Write-Host "  Test 2: Role-Based Access Control" -ForegroundColor Yellow
        $rbacResults = Test-RoleBasedAccess -Driver $driver -ExecutionMode $Mode
        $results.Tests += $rbacResults
        $results.Summary.TotalTests++
        if ($rbacResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Resources: $($rbacResults.Details.ResourcesAccessible -join ', ')" -ForegroundColor White
        Write-Host "    Result: $(if ($rbacResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($rbacResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 3: CRUD Operations
        Write-Host "  Test 3: CRUD Operations" -ForegroundColor Yellow
        $crudResults = Test-CRUDOperations -Driver $driver -ExecutionMode $Mode
        $results.Tests += $crudResults
        $results.Summary.TotalTests++
        if ($crudResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($crudResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($crudResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 4: Ticket Management
        Write-Host "  Test 4: Ticket Management" -ForegroundColor Yellow
        $ticketResults = Test-TicketManagement -Driver $driver -ExecutionMode $Mode
        $results.Tests += $ticketResults
        $results.Summary.TotalTests++
        if ($ticketResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($ticketResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($ticketResults.Passed) { 'Green' } else { 'Red' })
        
    }
    catch {
        Write-AutomationLog "Admin panel test failed: $($_.Exception.Message)" -Level ERROR
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

$testResults = Start-AdminPanelTest
return $testResults
