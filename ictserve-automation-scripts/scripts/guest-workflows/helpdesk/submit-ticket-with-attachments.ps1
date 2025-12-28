#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Submit Helpdesk Ticket with File Attachments - Guest User Workflow
.DESCRIPTION
    Automates submission of a helpdesk ticket with file attachments.
    Tests file upload, ClamAV virus scanning, and storage integration.
.PARAMETER Mode
    Execution mode: Headless, Visual, Demo, Interactive, Recording
#>

param(
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

# Import utilities
. "$ScriptRoot\..\..\..\utilities\common-functions.ps1"
. "$ScriptRoot\..\..\..\utilities\browser-automation.ps1"
. "$ScriptRoot\..\..\..\utilities\visual-demo-helpers.ps1"
. "$ScriptRoot\..\..\..\utilities\api-helpers.ps1"

$TestConfig = @{
    Name = "Submit Ticket with File Attachments"
    Category = "Guest Workflows - Helpdesk"
    Requirements = @("1.1", "1.2", "1.5", "1.6", "10.2")
    ExpectedDuration = 90
}

$TestData = @{
    Name = "Siti binti Hassan"
    Email = "siti.hassan@motac.gov.my"
    Phone = "03-8000-8001"
    Department = "Bahagian Teknologi Maklumat"
    Category = "Software Issue"
    Priority = "High"
    Subject = "Application crash with error screenshot"
    Description = "The internal application crashes when generating reports. Please see attached screenshot and error log."
    Attachments = @(
        @{ Name = "error-screenshot.png"; Type = "image/png"; Size = 150KB }
        @{ Name = "error-log.txt"; Type = "text/plain"; Size = 5KB }
    )
}

function Test-SubmitTicketWithAttachments {
    param([string]$ExecutionMode)
    
    $result = Initialize-TestResult -TestName $TestConfig.Name -Category $TestConfig.Category
    
    try {
        Write-TestStep "Initializing browser automation" -Mode $ExecutionMode
        $driver = Initialize-WebDriver -Mode $ExecutionMode
        
        # Navigate to helpdesk page
        Write-TestStep "Navigating to helpdesk ticket submission page" -Mode $ExecutionMode
        $baseUrl = Get-ConfigValue -Key "BaseUrl"
        Navigate-ToUrl -Driver $driver -Url "$baseUrl/helpdesk/create" -Mode $ExecutionMode
        
        # Fill basic form fields
        Write-TestStep "Filling in ticket information" -Mode $ExecutionMode
        Fill-FormField -Driver $driver -Selector "#name" -Value $TestData.Name -Mode $ExecutionMode
        Fill-FormField -Driver $driver -Selector "#email" -Value $TestData.Email -Mode $ExecutionMode
        Select-DropdownOption -Driver $driver -Selector "#department" -Value $TestData.Department -Mode $ExecutionMode
        Select-DropdownOption -Driver $driver -Selector "#category" -Value $TestData.Category -Mode $ExecutionMode
        Select-DropdownOption -Driver $driver -Selector "#priority" -Value $TestData.Priority -Mode $ExecutionMode
        Fill-FormField -Driver $driver -Selector "#subject" -Value $TestData.Subject -Mode $ExecutionMode
        Fill-FormField -Driver $driver -Selector "#description" -Value $TestData.Description -Mode $ExecutionMode
        
        # Upload attachments
        Write-TestStep "Uploading file attachments" -Mode $ExecutionMode
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "File uploads are scanned by ClamAV for viruses" -Duration 2500
        }
        
        $fileInput = Find-Element -Driver $driver -Selector "input[type='file']"
        
        # Generate test files
        $testFilesPath = Get-TestDataPath -Category "documents"
        foreach ($attachment in $TestData.Attachments) {
            $filePath = Join-Path $testFilesPath $attachment.Name
            
            # Create test file if it doesn't exist
            if (-not (Test-Path $filePath)) {
                New-TestFile -Path $filePath -Type $attachment.Type -Size $attachment.Size
            }
            
            Upload-File -Driver $driver -Element $fileInput -FilePath $filePath -Mode $ExecutionMode
            
            # Wait for upload progress
            Write-TestStep "Waiting for file upload: $($attachment.Name)" -Mode $ExecutionMode
            Wait-ForElement -Driver $driver -Selector ".upload-progress-complete, .file-uploaded" -Timeout 30
            
            if ($ExecutionMode -eq 'Interactive') {
                Pause-ForExplanation -Message "File '$($attachment.Name)' uploaded and scanned successfully"
            }
        }
        
        # Verify attachments are listed
        Write-TestStep "Verifying uploaded attachments" -Mode $ExecutionMode
        $attachmentList = Find-Elements -Driver $driver -Selector ".attachment-item, .uploaded-file"
        Assert-Count -Actual $attachmentList.Count -Expected $TestData.Attachments.Count -Message "All attachments should be listed"
        
        # Take screenshot before submission
        $screenshotPath = Take-Screenshot -Driver $driver -Name "ticket-with-attachments" -Mode $ExecutionMode
        
        # Submit the form
        Write-TestStep "Submitting ticket with attachments" -Mode $ExecutionMode
        $submitButton = Find-Element -Driver $driver -Selector "button[type='submit']"
        Click-Element -Driver $driver -Element $submitButton -Mode $ExecutionMode
        
        # Wait for success
        Write-TestStep "Waiting for submission confirmation" -Mode $ExecutionMode
        $successElement = Wait-ForElement -Driver $driver -Selector ".alert-success, [data-ticket-number]" -Timeout 20
        
        $ticketNumber = Get-ElementText -Driver $driver -Selector "[data-ticket-number]"
        Write-TestOutput "Ticket created: $ticketNumber with $($TestData.Attachments.Count) attachments" -Type "Success"
        
        # Verify attachments in backend
        Write-TestStep "Verifying attachments in backend" -Mode $ExecutionMode
        $apiResponse = Invoke-ApiRequest -Endpoint "/api/tickets/$ticketNumber/attachments" -Method "GET"
        Assert-ApiSuccess -Response $apiResponse
        Assert-Count -Actual $apiResponse.data.Count -Expected $TestData.Attachments.Count -Message "Backend should have all attachments"
        
        # Verify ClamAV scan results
        foreach ($attachment in $apiResponse.data) {
            Assert-Equal -Actual $attachment.scan_status -Expected "clean" -Message "File should pass virus scan"
        }
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "All files passed ClamAV virus scanning" -Duration 2000
        }
        
        $result.Status = "Passed"
        $result.TicketNumber = $ticketNumber
        $result.AttachmentCount = $TestData.Attachments.Count
        
    } catch {
        $result.Status = "Failed"
        $result.ErrorMessage = $_.Exception.Message
        Write-TestOutput "Test failed: $($_.Exception.Message)" -Type "Error"
        
        if ($driver) {
            Take-Screenshot -Driver $driver -Name "attachment-upload-failure" -Mode $ExecutionMode
        }
    } finally {
        if ($driver) { Close-WebDriver -Driver $driver }
        $result.EndTime = Get-Date
        $result.Duration = ($result.EndTime - $result.StartTime).TotalSeconds
        Save-TestResult -Result $result
    }
    
    return $result
}

$testResult = Test-SubmitTicketWithAttachments -ExecutionMode $Mode
return $testResult
