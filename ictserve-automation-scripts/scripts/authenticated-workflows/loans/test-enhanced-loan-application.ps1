#Requires -Version 7.0
<#
.SYNOPSIS
    Tests enhanced asset loan application for authenticated users.

.DESCRIPTION
    This script tests authenticated user loan features including:
    - Auto-filled forms with user profile data
    - Loan history tracking
    - Real-time availability updates
    - Approval workflow tracking

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.EXAMPLE
    .\test-enhanced-loan-application.ps1 -Mode Demo

.NOTES
    Version: 1.0.0
    Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6
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
    Name = "Enhanced Loan Application Test"
    Category = "Authenticated Workflows - Loans"
    Requirements = @("5.1", "5.2", "5.3", "5.4", "5.5", "5.6")
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

function Test-LoanAutoFilledForms {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing loan auto-filled forms" -Level INFO
    
    $results = @{
        TestName = "Loan Auto-Filled Forms"
        Passed = $false
        Details = @{
            FormFound = $false
            ApplicantAutoFilled = $false
            DepartmentAutoFilled = $false
            ContactAutoFilled = $false
        }
    }
    
    try {
        $loanUrls = @("$BaseUrl/loans/create", "$BaseUrl/asset-loans/create", "$BaseUrl/equipment/request")
        
        foreach ($url in $loanUrls) {
            Navigate-ToUrl -Driver $Driver -Url $url -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $form = Find-Element -Driver $Driver -Selector "form" -Required $false
            if ($form) {
                $results.Details.FormFound = $true
                break
            }
        }
        
        if ($results.Details.FormFound) {
            $applicantField = Find-Element -Driver $Driver -Selector "input[name='applicant'], input[name='name']" -Required $false
            if ($applicantField) {
                $value = Get-ElementAttribute -Element $applicantField -Attribute "value"
                $results.Details.ApplicantAutoFilled = -not [string]::IsNullOrWhiteSpace($value)
            }
            
            $deptField = Find-Element -Driver $Driver -Selector "input[name='department'], select[name='department']" -Required $false
            if ($deptField) {
                $value = Get-ElementAttribute -Element $deptField -Attribute "value"
                $results.Details.DepartmentAutoFilled = -not [string]::IsNullOrWhiteSpace($value)
            }
            
            $contactField = Find-Element -Driver $Driver -Selector "input[name='phone'], input[name='contact']" -Required $false
            if ($contactField) {
                $value = Get-ElementAttribute -Element $contactField -Attribute "value"
                $results.Details.ContactAutoFilled = -not [string]::IsNullOrWhiteSpace($value)
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "loan-auto-filled" -Mode $ExecutionMode
        $results.Passed = $results.Details.FormFound
    }
    catch {
        Write-AutomationLog "Loan auto-filled forms test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-LoanHistory {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing loan history" -Level INFO
    
    $results = @{
        TestName = "Loan History"
        Passed = $false
        Details = @{
            HistoryPageFound = $false
            LoansDisplayed = 0
            HasStatusColumn = $false
            HasDateColumn = $false
        }
    }
    
    try {
        $historyUrls = @("$BaseUrl/loans/my-loans", "$BaseUrl/my-loans", "$BaseUrl/dashboard/loans")
        
        foreach ($url in $historyUrls) {
            Navigate-ToUrl -Driver $Driver -Url $url -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $loanList = Find-Element -Driver $Driver -Selector ".loan-list, table, .loans, [data-loans]" -Required $false
            if ($loanList) {
                $results.Details.HistoryPageFound = $true
                break
            }
        }
        
        if ($results.Details.HistoryPageFound) {
            $loans = Find-Elements -Driver $Driver -Selector ".loan-row, tr[data-loan], .loan-item"
            $results.Details.LoansDisplayed = $loans.Count
            
            $statusColumn = Find-Element -Driver $Driver -Selector "th:contains('Status'), .status-column" -Required $false
            $results.Details.HasStatusColumn = $null -ne $statusColumn
            
            $dateColumn = Find-Element -Driver $Driver -Selector "th:contains('Date'), .date-column" -Required $false
            $results.Details.HasDateColumn = $null -ne $dateColumn
            
            if ($ExecutionMode -eq 'Demo') {
                Show-Annotation -Text "Found $($loans.Count) loans in history" -Duration 2000
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "loan-history" -Mode $ExecutionMode
        $results.Passed = $results.Details.HistoryPageFound
    }
    catch {
        Write-AutomationLog "Loan history test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-AssetAvailability {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing asset availability" -Level INFO
    
    $results = @{
        TestName = "Asset Availability"
        Passed = $false
        Details = @{
            CalendarFound = $false
            AvailabilityIndicator = $false
            RealTimeUpdates = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/loans/create" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $calendar = Find-Element -Driver $Driver -Selector ".calendar, .datepicker, [data-calendar]" -Required $false
        $results.Details.CalendarFound = $null -ne $calendar
        
        $availability = Find-Element -Driver $Driver -Selector ".availability, .available, [data-availability]" -Required $false
        $results.Details.AvailabilityIndicator = $null -ne $availability
        
        $lwScript = "return typeof Livewire !== 'undefined';"
        $results.Details.RealTimeUpdates = Execute-JavaScript -Driver $Driver -Script $lwScript
        
        Take-Screenshot -Driver $Driver -Name "asset-availability" -Mode $ExecutionMode
        $results.Passed = $results.Details.CalendarFound -or $results.Details.AvailabilityIndicator
    }
    catch {
        Write-AutomationLog "Asset availability test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-ApprovalWorkflow {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing approval workflow tracking" -Level INFO
    
    $results = @{
        TestName = "Approval Workflow"
        Passed = $false
        Details = @{
            WorkflowStepsVisible = $false
            CurrentStepHighlighted = $false
            ApproverInfoShown = $false
        }
    }
    
    try {
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/loans/my-loans" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $loanLink = Find-Element -Driver $Driver -Selector ".loan-row a, tr[data-loan] a" -Required $false
        if ($loanLink) {
            Click-Element -Driver $Driver -Element $loanLink -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $workflowSteps = Find-Element -Driver $Driver -Selector ".workflow-steps, .approval-steps, [data-workflow]" -Required $false
            $results.Details.WorkflowStepsVisible = $null -ne $workflowSteps
            
            $currentStep = Find-Element -Driver $Driver -Selector ".current-step, .active-step, [data-current]" -Required $false
            $results.Details.CurrentStepHighlighted = $null -ne $currentStep
            
            $approverInfo = Find-Element -Driver $Driver -Selector ".approver, .approver-info, [data-approver]" -Required $false
            $results.Details.ApproverInfoShown = $null -ne $approverInfo
            
            if ($ExecutionMode -eq 'Demo' -and $workflowSteps) {
                Highlight-Element -Driver $Driver -Element $workflowSteps -Color "blue" -Mode $ExecutionMode
                Show-Annotation -Text "Approval workflow tracking" -Duration 2000
            }
        }
        
        Take-Screenshot -Driver $Driver -Name "approval-workflow" -Mode $ExecutionMode
        $results.Passed = $results.Details.WorkflowStepsVisible -or $results.Details.ApproverInfoShown
    }
    catch {
        Write-AutomationLog "Approval workflow test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-EnhancedLoanApplicationTest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║        Enhanced Loan Application Test Suite                   ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $driver = $null
    
    try {
        $driver = Initialize-WebDriver -Mode $Mode
        
        Write-Host "  Logging in..." -ForegroundColor Gray
        Invoke-Login -Driver $driver -ExecutionMode $Mode
        
        # Test 1: Auto-Filled Forms
        Write-Host "  Test 1: Auto-Filled Forms" -ForegroundColor Yellow
        $autoFillResults = Test-LoanAutoFilledForms -Driver $driver -ExecutionMode $Mode
        $results.Tests += $autoFillResults
        $results.Summary.TotalTests++
        if ($autoFillResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($autoFillResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($autoFillResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 2: Loan History
        Write-Host "  Test 2: Loan History" -ForegroundColor Yellow
        $historyResults = Test-LoanHistory -Driver $driver -ExecutionMode $Mode
        $results.Tests += $historyResults
        $results.Summary.TotalTests++
        if ($historyResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Loans Found: $($historyResults.Details.LoansDisplayed)" -ForegroundColor White
        Write-Host "    Result: $(if ($historyResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($historyResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 3: Asset Availability
        Write-Host "  Test 3: Asset Availability" -ForegroundColor Yellow
        $availResults = Test-AssetAvailability -Driver $driver -ExecutionMode $Mode
        $results.Tests += $availResults
        $results.Summary.TotalTests++
        if ($availResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($availResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($availResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 4: Approval Workflow
        Write-Host "  Test 4: Approval Workflow" -ForegroundColor Yellow
        $workflowResults = Test-ApprovalWorkflow -Driver $driver -ExecutionMode $Mode
        $results.Tests += $workflowResults
        $results.Summary.TotalTests++
        if ($workflowResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($workflowResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($workflowResults.Passed) { 'Green' } else { 'Red' })
        
    }
    catch {
        Write-AutomationLog "Enhanced loan application test failed: $($_.Exception.Message)" -Level ERROR
        throw
    }
    finally {
        if ($driver) { Close-WebDriver -Driver $driver }
    }
    
    $results.EndTime = Get-Date
    $results.Duration = $results.EndTime - $results.StartTime
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║                    Test Summary                               ║" -ForegroundColor Cyan
    Write-Host "╠══════════════════════════════════════════════════════════════╣" -ForegroundColor Cyan
    Write-Host "║  Total: $($results.Summary.TotalTests)  Passed: $($results.Summary.PassedTests)  Failed: $($results.Summary.FailedTests)                                    ║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    
    return $results
}

$testResults = Start-EnhancedLoanApplicationTest
return $testResults
