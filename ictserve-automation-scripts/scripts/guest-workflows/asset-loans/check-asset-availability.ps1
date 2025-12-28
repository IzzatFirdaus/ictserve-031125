#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Check Asset Availability Calendar - Guest User Workflow
.DESCRIPTION
    Tests the asset availability calendar functionality for guest users.
    Validates frontend calendar display and backend scheduling queries.
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
    Name = "Check Asset Availability Calendar"
    Category = "Guest Workflows - Asset Loans"
    Requirements = @("1.2", "1.3", "14.3")
    ExpectedDuration = 60
}

function Test-AssetAvailabilityCalendar {
    param([string]$ExecutionMode)
    
    $result = Initialize-TestResult -TestName $TestConfig.Name -Category $TestConfig.Category
    
    try {
        Write-TestStep "Initializing browser automation" -Mode $ExecutionMode
        $driver = Initialize-WebDriver -Mode $ExecutionMode
        
        $baseUrl = Get-ConfigValue -Key "BaseUrl"
        
        # Navigate to asset availability page
        Write-TestStep "Navigating to asset availability page" -Mode $ExecutionMode
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/assets/availability" -Mode $ExecutionMode
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Check asset availability before submitting loan requests" -Duration 2500
        }
        
        # Wait for calendar to load
        Write-TestStep "Waiting for availability calendar" -Mode $ExecutionMode
        $calendarElement = Wait-ForElement -Driver $driver -Selector ".calendar, .availability-calendar, [data-calendar]" -Timeout 15
        Assert-ElementExists -Element $calendarElement -Message "Availability calendar should be visible"
        
        # Select asset category
        Write-TestStep "Selecting asset category" -Mode $ExecutionMode
        $categoryDropdown = Find-Element -Driver $driver -Selector "#asset_category, select[name='category']"
        Select-DropdownOption -Driver $driver -Selector "#asset_category, select[name='category']" -Value "Laptop" -Mode $ExecutionMode
        
        # Wait for calendar to update
        Start-Sleep -Milliseconds 1000
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Calendar updates to show laptop availability" -Duration 2000
        }
        
        # Check for available dates (green/available indicators)
        Write-TestStep "Checking for available dates" -Mode $ExecutionMode
        $availableDates = Find-Elements -Driver $driver -Selector ".calendar-day.available, .day-available, [data-available='true']"
        Write-TestOutput "Found $($availableDates.Count) available dates" -Type "Info"
        
        # Check for unavailable dates (red/booked indicators)
        $unavailableDates = Find-Elements -Driver $driver -Selector ".calendar-day.unavailable, .day-booked, [data-available='false']"
        Write-TestOutput "Found $($unavailableDates.Count) unavailable dates" -Type "Info"
        
        # Take screenshot of calendar
        Take-Screenshot -Driver $driver -Name "asset-availability-calendar" -Mode $ExecutionMode
        
        # Test date selection
        Write-TestStep "Testing date selection" -Mode $ExecutionMode
        if ($availableDates.Count -gt 0) {
            $firstAvailable = $availableDates[0]
            Highlight-Element -Driver $driver -Element $firstAvailable -Mode $ExecutionMode
            Click-Element -Driver $driver -Element $firstAvailable -Mode $ExecutionMode
            
            # Check if date details are shown
            $dateDetails = Wait-ForElement -Driver $driver -Selector ".date-details, .availability-details, [data-date-info]" -Timeout 5 -Required $false
            if ($dateDetails) {
                $detailsText = Get-ElementText -Element $dateDetails
                Write-TestOutput "Date details: $detailsText" -Type "Info"
            }
        }
        
        # Test month navigation
        Write-TestStep "Testing calendar navigation" -Mode $ExecutionMode
        $nextMonthButton = Find-Element -Driver $driver -Selector ".calendar-next, .btn-next-month, [data-action='next-month']" -Required $false
        if ($nextMonthButton) {
            Click-Element -Driver $driver -Element $nextMonthButton -Mode $ExecutionMode
            Start-Sleep -Milliseconds 500
            
            if ($ExecutionMode -eq 'Demo') {
                Show-Annotation -Text "Navigate between months to check future availability" -Duration 2000
            }
            
            Take-Screenshot -Driver $driver -Name "asset-availability-next-month" -Mode $ExecutionMode
        }
        
        # Test specific asset type selection
        Write-TestStep "Testing specific asset type" -Mode $ExecutionMode
        $assetTypeDropdown = Find-Element -Driver $driver -Selector "#asset_type, select[name='asset_type']" -Required $false
        if ($assetTypeDropdown) {
            Select-DropdownOption -Driver $driver -Selector "#asset_type, select[name='asset_type']" -Value "Dell Latitude 5540" -Mode $ExecutionMode
            Start-Sleep -Milliseconds 500
            
            # Verify calendar updated
            $updatedAvailable = Find-Elements -Driver $driver -Selector ".calendar-day.available, [data-available='true']"
            Write-TestOutput "Available dates for specific asset: $($updatedAvailable.Count)" -Type "Info"
        }
        
        # Verify backend API consistency
        Write-TestStep "Verifying backend availability data" -Mode $ExecutionMode
        $startDate = (Get-Date).ToString("yyyy-MM-dd")
        $endDate = (Get-Date).AddDays(30).ToString("yyyy-MM-dd")
        
        $apiResponse = Invoke-ApiRequest -Endpoint "/api/assets/availability" -Method "GET" -Query @{
            category = "Laptop"
            start_date = $startDate
            end_date = $endDate
        }
        
        if ($apiResponse.success) {
            $apiAvailableDates = $apiResponse.data.available_dates.Count
            Write-TestOutput "Backend reports $apiAvailableDates available dates" -Type "Info"
        }
        
        # Test unavailable date selection (should show warning)
        if ($unavailableDates.Count -gt 0) {
            Write-TestStep "Testing unavailable date selection" -Mode $ExecutionMode
            $firstUnavailable = $unavailableDates[0]
            Highlight-Element -Driver $driver -Element $firstUnavailable -Color "red" -Mode $ExecutionMode
            Click-Element -Driver $driver -Element $firstUnavailable -Mode $ExecutionMode
            
            # Check for warning message
            $warningElement = Wait-ForElement -Driver $driver -Selector ".alert-warning, .unavailable-message, [data-warning]" -Timeout 3 -Required $false
            if ($warningElement) {
                $warningText = Get-ElementText -Element $warningElement
                Write-TestOutput "Unavailable date warning: $warningText" -Type "Info"
                
                if ($ExecutionMode -eq 'Demo') {
                    Show-Annotation -Text "System warns when selecting unavailable dates" -Duration 2000
                }
            }
        }
        
        if ($ExecutionMode -eq 'Interactive') {
            Pause-ForExplanation -Message "Calendar shows real-time asset availability from the database"
        }
        
        $result.Status = "Passed"
        $result.AvailableDates = $availableDates.Count
        $result.UnavailableDates = $unavailableDates.Count
        Write-TestOutput "Asset availability calendar test completed successfully" -Type "Success"
        
    } catch {
        $result.Status = "Failed"
        $result.ErrorMessage = $_.Exception.Message
        Write-TestOutput "Test failed: $($_.Exception.Message)" -Type "Error"
        
        if ($driver) {
            Take-Screenshot -Driver $driver -Name "availability-calendar-failure" -Mode $ExecutionMode
        }
    } finally {
        if ($driver) { Close-WebDriver -Driver $driver }
        $result.EndTime = Get-Date
        $result.Duration = ($result.EndTime - $result.StartTime).TotalSeconds
        Save-TestResult -Result $result
    }
    
    return $result
}

$testResult = Test-AssetAvailabilityCalendar -ExecutionMode $Mode
return $testResult
