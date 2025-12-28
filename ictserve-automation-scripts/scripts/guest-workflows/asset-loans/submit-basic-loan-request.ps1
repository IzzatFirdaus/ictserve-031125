#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Submit Basic Asset Loan Request - Guest User Workflow
.DESCRIPTION
    Automates submission of a basic asset loan request as a guest user.
    Tests frontend form and backend processing for loan applications.
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
    Name = "Submit Basic Asset Loan Request"
    Category = "Guest Workflows - Asset Loans"
    Requirements = @("1.2", "1.3", "1.4")
    ExpectedDuration = 75
}

$TestData = @{
    Name = "Mohd Razak bin Ismail"
    Email = "razak.ismail@motac.gov.my"
    Phone = "03-8000-8002"
    Department = "Bahagian Kewangan"
    EmployeeId = "MOTAC-2024-001"
    AssetCategory = "Laptop"
    AssetType = "Dell Latitude 5540"
    Purpose = "Official meeting and presentation at external venue"
    StartDate = (Get-Date).AddDays(3).ToString("yyyy-MM-dd")
    EndDate = (Get-Date).AddDays(5).ToString("yyyy-MM-dd")
    PickupLocation = "IT Store Room - Level 2"
    ReturnLocation = "IT Store Room - Level 2"
    AdditionalNotes = "Required for presentation at Ministry of Finance meeting."
}

function Test-SubmitBasicLoanRequest {
    param([string]$ExecutionMode)
    
    $result = Initialize-TestResult -TestName $TestConfig.Name -Category $TestConfig.Category
    
    try {
        Write-TestStep "Initializing browser automation" -Mode $ExecutionMode
        $driver = Initialize-WebDriver -Mode $ExecutionMode
        
        $baseUrl = Get-ConfigValue -Key "BaseUrl"
        
        # Navigate to asset loan page
        Write-TestStep "Navigating to asset loan request page" -Mode $ExecutionMode
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/loans/create" -Mode $ExecutionMode
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Guest users can request asset loans for official purposes" -Duration 2500
        }
        
        # Wait for form to load
        $formElement = Wait-ForElement -Driver $driver -Selector "form[action*='loan']" -Timeout 10
        Assert-ElementExists -Element $formElement -Message "Loan request form should be visible"
        
        # Fill personal information
        Write-TestStep "Filling in personal information" -Mode $ExecutionMode
        Fill-FormField -Driver $driver -Selector "#name" -Value $TestData.Name -Mode $ExecutionMode -Label "Full Name"
        Fill-FormField -Driver $driver -Selector "#email" -Value $TestData.Email -Mode $ExecutionMode -Label "Email"
        Fill-FormField -Driver $driver -Selector "#phone" -Value $TestData.Phone -Mode $ExecutionMode -Label "Phone"
        Fill-FormField -Driver $driver -Selector "#employee_id" -Value $TestData.EmployeeId -Mode $ExecutionMode -Label "Employee ID"
        Select-DropdownOption -Driver $driver -Selector "#department" -Value $TestData.Department -Mode $ExecutionMode
        
        if ($ExecutionMode -eq 'Interactive') {
            Pause-ForExplanation -Message "Personal information is validated against HRMIS records"
        }
        
        # Select asset category and type
        Write-TestStep "Selecting asset category and type" -Mode $ExecutionMode
        Select-DropdownOption -Driver $driver -Selector "#asset_category" -Value $TestData.AssetCategory -Mode $ExecutionMode
        
        # Wait for asset types to load (dynamic dropdown)
        Start-Sleep -Milliseconds 500
        Select-DropdownOption -Driver $driver -Selector "#asset_type" -Value $TestData.AssetType -Mode $ExecutionMode
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Asset types are filtered based on selected category" -Duration 2000
        }
        
        # Fill loan details
        Write-TestStep "Filling in loan details" -Mode $ExecutionMode
        Fill-FormField -Driver $driver -Selector "#purpose" -Value $TestData.Purpose -Mode $ExecutionMode -Label "Purpose"
        
        # Set dates using date picker
        Write-TestStep "Setting loan dates" -Mode $ExecutionMode
        Set-DateField -Driver $driver -Selector "#start_date" -Date $TestData.StartDate -Mode $ExecutionMode
        Set-DateField -Driver $driver -Selector "#end_date" -Date $TestData.EndDate -Mode $ExecutionMode
        
        # Calculate and display loan duration
        $startDate = [DateTime]::Parse($TestData.StartDate)
        $endDate = [DateTime]::Parse($TestData.EndDate)
        $duration = ($endDate - $startDate).Days + 1
        Write-TestOutput "Loan duration: $duration days" -Type "Info"
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Loan duration: $duration days (automatically calculated)" -Duration 2000
        }
        
        # Select pickup and return locations
        Write-TestStep "Setting pickup and return locations" -Mode $ExecutionMode
        Select-DropdownOption -Driver $driver -Selector "#pickup_location" -Value $TestData.PickupLocation -Mode $ExecutionMode
        Select-DropdownOption -Driver $driver -Selector "#return_location" -Value $TestData.ReturnLocation -Mode $ExecutionMode
        
        # Add additional notes
        Fill-FormField -Driver $driver -Selector "#notes" -Value $TestData.AdditionalNotes -Mode $ExecutionMode -Label "Additional Notes"
        
        # Take screenshot before submission
        Take-Screenshot -Driver $driver -Name "loan-request-form-filled" -Mode $ExecutionMode
        
        # Submit the form
        Write-TestStep "Submitting loan request" -Mode $ExecutionMode
        $submitButton = Find-Element -Driver $driver -Selector "button[type='submit']"
        Highlight-Element -Driver $driver -Element $submitButton -Mode $ExecutionMode
        Click-Element -Driver $driver -Element $submitButton -Mode $ExecutionMode
        
        # Wait for success response
        Write-TestStep "Waiting for submission confirmation" -Mode $ExecutionMode
        $successElement = Wait-ForElement -Driver $driver -Selector ".alert-success, [data-loan-number], .loan-confirmation" -Timeout 15
        Assert-ElementExists -Element $successElement -Message "Success message should be displayed"
        
        # Extract loan reference number
        $loanNumber = Get-ElementText -Driver $driver -Selector "[data-loan-number]"
        Write-TestOutput "Loan request created: $loanNumber" -Type "Success"
        
        if ($ExecutionMode -eq 'Demo' -or $ExecutionMode -eq 'Interactive') {
            Show-Annotation -Text "Loan request $loanNumber submitted! Pending approval." -Duration 3000
        }
        
        # Verify backend processing
        Write-TestStep "Verifying backend loan creation" -Mode $ExecutionMode
        $apiResponse = Invoke-ApiRequest -Endpoint "/api/loans/search" -Method "GET" -Query @{ number = $loanNumber }
        Assert-ApiSuccess -Response $apiResponse -Message "Loan should exist in database"
        
        # Verify loan status
        $loanStatus = $apiResponse.data.status
        Assert-Equal -Actual $loanStatus -Expected "pending_approval" -Message "Loan should be pending approval"
        
        # Verify approval workflow was triggered
        Write-TestStep "Verifying approval workflow" -Mode $ExecutionMode
        $workflowResponse = Invoke-ApiRequest -Endpoint "/api/loans/$loanNumber/workflow" -Method "GET"
        if ($workflowResponse.success) {
            Write-TestOutput "Approval workflow initiated" -Type "Info"
        }
        
        # Take final screenshot
        Take-Screenshot -Driver $driver -Name "loan-request-submitted" -Mode $ExecutionMode
        
        $result.Status = "Passed"
        $result.LoanNumber = $loanNumber
        $result.Duration = $duration
        Write-TestOutput "Loan request test completed successfully" -Type "Success"
        
    } catch {
        $result.Status = "Failed"
        $result.ErrorMessage = $_.Exception.Message
        Write-TestOutput "Test failed: $($_.Exception.Message)" -Type "Error"
        
        if ($driver) {
            Take-Screenshot -Driver $driver -Name "loan-request-failure" -Mode $ExecutionMode
        }
    } finally {
        if ($driver) { Close-WebDriver -Driver $driver }
        $result.EndTime = Get-Date
        $result.Duration = ($result.EndTime - $result.StartTime).TotalSeconds
        Save-TestResult -Result $result
    }
    
    return $result
}

$testResult = Test-SubmitBasicLoanRequest -ExecutionMode $Mode
return $testResult
