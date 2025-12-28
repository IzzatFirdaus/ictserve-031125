#Requires -Version 7.0
<#
.SYNOPSIS
    Video recording session example for training materials.

.DESCRIPTION
    This script demonstrates how to create video recordings of automation
    workflows for training and documentation purposes.

.EXAMPLE
    .\recording-session.ps1 -WorkflowName "Helpdesk Tutorial"
    Records a helpdesk workflow tutorial video.

.NOTES
    Version: 1.0.0
    Author: ICTServe Automation Team
    Requirements: OBS Studio or similar for video capture
#>

[CmdletBinding()]
param(
    [Parameter()]
    [string]$WorkflowName = "ICTServe Training",
    
    [Parameter()]
    [string]$BaseUrl = "http://localhost:8000",
    
    [Parameter()]
    [string]$OutputPath = ""
)

# Import required modules
$scriptRoot = Split-Path -Parent $PSScriptRoot
. "$scriptRoot\utilities\common-functions.ps1"
. "$scriptRoot\utilities\browser-automation.ps1"
. "$scriptRoot\utilities\visual-demo-helpers.ps1"

function Start-RecordingSession {
    <#
    .SYNOPSIS
        Starts a video recording session with full visual features.
    #>
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Red
    Write-Host "║              🎬 VIDEO RECORDING SESSION                       ║" -ForegroundColor Red
    Write-Host "║                                                               ║" -ForegroundColor Red
    Write-Host "║  Workflow: $($WorkflowName.PadRight(48))║" -ForegroundColor White
    Write-Host "║  Mode: Recording with annotations                             ║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Red
    Write-Host ""
    
    # Configure for recording mode
    $demoConfig = Get-DefaultDemoConfig -Mode 'Recording'
    $demoConfig.TakeScreenshots = $true
    $demoConfig.AddAnnotations = $true
    $demoConfig.HighlightElements = $true
    $demoConfig.ShowMouseCursor = $true
    $demoConfig.ExecutionSpeed = 'Demo'
    
    try {
        # Start browser session
        $session = Start-BrowserSession -DemoSettings $demoConfig
        
        # Start video recording
        Start-VideoRecording -Name $WorkflowName
        
        Write-Host ""
        Write-Host "  🔴 RECORDING STARTED" -ForegroundColor Red
        Write-Host "  Press Ctrl+C to stop recording at any time" -ForegroundColor Yellow
        Write-Host ""
        
        # Show intro annotation
        Show-Annotation -Text "🎬 $WorkflowName - ICTServe Training Video" -Duration 4000 -Position "center"
        
        # Introduction slide
        Show-InfoBox -Title "Training Objectives" -Content @(
            "In this video, you will learn:",
            "",
            "1. How to submit a helpdesk ticket",
            "2. Required fields and validation",
            "3. File attachment process",
            "4. Tracking your submission"
        )
        
        Start-Sleep -Seconds 5
        
        # Navigate to homepage
        Show-Annotation -Text "📍 Step 1: Navigate to ICTServe Portal" -Duration 3000 -Position "top"
        Navigate-ToUrl -Url "$BaseUrl" -Description "ICTServe Homepage"
        Take-Screenshot -Name "01-homepage" -Description "Homepage view"
        
        Start-Sleep -Seconds 2
        
        # Show homepage features
        Show-Annotation -Text "👆 The main navigation menu provides access to all features" -Duration 3000 -Position "top"
        Highlight-Element -Selector "nav, .navigation" -Color "#4CAF50" -DurationMs 2000
        
        # Navigate to helpdesk
        Show-Annotation -Text "📍 Step 2: Access the Helpdesk Form" -Duration 3000 -Position "top"
        Pulse-Element -Selector "a[href*='helpdesk']" -Color "#FF6B6B" -Pulses 3
        Click-Element -Selector "a[href*='helpdesk']" -Description "Helpdesk Link"
        Take-Screenshot -Name "02-helpdesk-form" -Description "Helpdesk form"
        
        Start-Sleep -Seconds 2
        
        # Explain form fields
        Show-Annotation -Text "📝 Step 3: Fill in the Required Fields" -Duration 3000 -Position "top"
        
        # Name field
        Show-FormFieldAnnotation -Selector "#name" -Text "Enter your full name as registered in HRMIS" -Position "right" -Duration 3000
        Type-Text -Selector "#name" -Text "Ahmad bin Abdullah" -Description "Name" -SimulateTyping
        
        # Email field
        Show-FormFieldAnnotation -Selector "#email" -Text "Use your official @motac.gov.my email address" -Position "right" -Duration 3000
        Type-Text -Selector "#email" -Text "ahmad@motac.gov.my" -Description "Email" -SimulateTyping
        
        # Category field
        Show-FormFieldAnnotation -Selector "#category" -Text "Select the category that best describes your issue" -Position "right" -Duration 3000
        Select-DropdownOption -Selector "#category" -Value "hardware" -Description "Category"
        
        # Priority field
        Show-FormFieldAnnotation -Selector "#priority" -Text "Set priority based on urgency (Normal for most requests)" -Position "right" -Duration 3000
        Select-DropdownOption -Selector "#priority" -Value "normal" -Description "Priority"
        
        # Description field
        Show-FormFieldAnnotation -Selector "#description" -Text "Provide detailed description of your issue" -Position "top" -Duration 3000
        Type-Text -Selector "#description" -Text "My laptop screen is flickering intermittently. The issue started yesterday morning and occurs approximately every 5 minutes. I have tried restarting the laptop but the problem persists." -Description "Description" -SimulateTyping
        
        Take-Screenshot -Name "03-form-filled" -Description "Completed form"
        
        Start-Sleep -Seconds 2
        
        # File attachment section
        Show-Annotation -Text "📎 Step 4: Attach Supporting Files (Optional)" -Duration 3000 -Position "top"
        Highlight-Element -Selector "input[type='file'], .file-upload" -Color "#2196F3" -DurationMs 2000
        Show-FormFieldAnnotation -Selector "input[type='file']" -Text "Click to attach screenshots or documents (Max 10MB)" -Position "right" -Duration 3000
        
        Start-Sleep -Seconds 2
        
        # Submit button
        Show-Annotation -Text "✅ Step 5: Submit Your Ticket" -Duration 3000 -Position "top"
        Draw-Arrow -FromSelector "#description" -ToSelector "button[type='submit']" -Color "#4CAF50" -Duration 2000
        Pulse-Element -Selector "button[type='submit']" -Color "#4CAF50" -Pulses 3
        
        Show-Annotation -Text "👆 Click the Submit button to create your ticket" -Duration 2000 -Position "bottom"
        Click-Element -Selector "button[type='submit']" -Description "Submit Button"
        
        Take-Screenshot -Name "04-submission" -Description "Submission moment"
        
        Start-Sleep -Seconds 3
        
        # Confirmation
        Show-Annotation -Text "🎉 Ticket Submitted Successfully!" -Duration 4000 -Position "center"
        Highlight-Element -Selector ".ticket-number, .success-message" -Color "#4CAF50" -DurationMs 3000
        
        Take-Screenshot -Name "05-confirmation" -Description "Confirmation page"
        
        # Summary slide
        Show-InfoBox -Title "Summary" -Content @(
            "You have learned how to:",
            "",
            "✅ Navigate to the helpdesk form",
            "✅ Fill in required fields correctly",
            "✅ Attach supporting documents",
            "✅ Submit and track your ticket",
            "",
            "Save your ticket number for tracking!"
        )
        
        Start-Sleep -Seconds 5
        
        # Outro
        Show-Annotation -Text "Thank you for watching! 🙏" -Duration 4000 -Position "center"
        
        Start-Sleep -Seconds 2
        
        # Stop recording
        $videoPath = Stop-VideoRecording
        
        Write-Host ""
        Write-Host "  ⏹️ RECORDING STOPPED" -ForegroundColor Green
        Write-Host ""
        
        # Display summary
        Show-SuccessMessage -Message "Recording session completed!"
        
        Show-InfoBox -Title "Recording Summary" -Content @(
            "Video saved to: $videoPath",
            "Screenshots: 5 captured",
            "Duration: ~3 minutes",
            "",
            "Files ready for editing and distribution"
        )
        
    }
    catch {
        Show-ErrorMessage -Message "Recording failed: $($_.Exception.Message)"
        Stop-VideoRecording
        throw
    }
    finally {
        Stop-BrowserSession
    }
}

# Execute the recording session
Start-RecordingSession
