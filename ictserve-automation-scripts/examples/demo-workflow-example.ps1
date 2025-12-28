#Requires -Version 7.0
<#
.SYNOPSIS
    Example demonstration workflow showing visual automation features.

.DESCRIPTION
    This script demonstrates how to use the visual demonstration features
    including element highlighting, annotations, step indicators, and
    network monitoring for training and presentation purposes.

.EXAMPLE
    .\demo-workflow-example.ps1 -Mode Demo
    Runs the demonstration in Demo mode with annotations and highlights.

.EXAMPLE
    .\demo-workflow-example.ps1 -Mode Interactive
    Runs with pause points for presenter explanation.

.NOTES
    Version: 1.0.0
    Author: ICTServe Automation Team
#>

[CmdletBinding()]
param(
    [Parameter()]
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Demo',
    
    [Parameter()]
    [string]$BaseUrl = "http://localhost:8000"
)

# Import required modules
$scriptRoot = Split-Path -Parent $PSScriptRoot
. "$scriptRoot\utilities\common-functions.ps1"
. "$scriptRoot\utilities\browser-automation.ps1"
. "$scriptRoot\utilities\visual-demo-helpers.ps1"

# Initialize demo configuration
$demoConfig = Get-DefaultDemoConfig -Mode $Mode

# Define workflow steps
$workflowSteps = @(
    "Navigate to Homepage",
    "Open Helpdesk Form",
    "Fill Form Fields",
    "Submit Ticket",
    "View Confirmation"
)

function Start-DemoWorkflow {
    <#
    .SYNOPSIS
        Executes the demonstration workflow with visual features.
    #>
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║         ICTServe Visual Demonstration Workflow                ║" -ForegroundColor Cyan
    Write-Host "║                                                               ║" -ForegroundColor Cyan
    Write-Host "║  Mode: $($Mode.PadRight(54))║" -ForegroundColor White
    Write-Host "║  Features: Highlights, Annotations, Step Indicators          ║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    try {
        # Start browser session
        $session = Start-BrowserSession -DemoSettings $demoConfig
        
        # Start network monitoring if enabled
        if ($demoConfig.ShowNetworkActivity) {
            Start-NetworkMonitoring
        }
        
        # Step 1: Navigate to Homepage
        Show-StepIndicator -CurrentStep 1 -TotalSteps 5 -StepTitle "Helpdesk Ticket Submission" -AllSteps $workflowSteps
        Show-WorkflowStep -StepNumber 1 -Title "Navigate to Homepage" -Description "Opening the ICTServe portal homepage"
        
        Navigate-ToUrl -Url "$BaseUrl" -Description "ICTServe Homepage"
        
        if ($Mode -eq 'Interactive') {
            Pause-ForInteraction -StepName "Navigation" -Message "Homepage loaded. Press Enter to continue to the helpdesk form..."
        }
        
        # Step 2: Open Helpdesk Form
        Show-StepIndicator -CurrentStep 2 -TotalSteps 5 -StepTitle "Helpdesk Ticket Submission" -AllSteps $workflowSteps
        Show-WorkflowStep -StepNumber 2 -Title "Open Helpdesk Form" -Description "Navigating to the helpdesk ticket submission form"
        
        # Highlight and click the helpdesk link
        Pulse-Element -Selector "a[href*='helpdesk']" -Color "#4CAF50" -Pulses 2
        Click-Element -Selector "a[href*='helpdesk']" -Description "Helpdesk Menu Link"
        
        # Step 3: Fill Form Fields
        Show-StepIndicator -CurrentStep 3 -TotalSteps 5 -StepTitle "Helpdesk Ticket Submission" -AllSteps $workflowSteps
        Show-WorkflowStep -StepNumber 3 -Title "Fill Form Fields" -Description "Entering ticket details into the form"
        
        # Demonstrate form field annotations
        Show-FormFieldAnnotation -Selector "#name" -Text "Enter your full name" -Position "right" -Duration 2000
        Type-Text -Selector "#name" -Text "Ahmad bin Abdullah" -Description "Name Field"
        
        Show-FormFieldAnnotation -Selector "#email" -Text "Use your @motac.gov.my email" -Position "right" -Duration 2000
        Type-Text -Selector "#email" -Text "ahmad@motac.gov.my" -Description "Email Field"
        
        Show-FormFieldAnnotation -Selector "#category" -Text "Select the appropriate category" -Position "right" -Duration 2000
        Select-DropdownOption -Selector "#category" -Value "hardware" -Description "Category Dropdown"
        
        Show-FormFieldAnnotation -Selector "#description" -Text "Describe your issue in detail" -Position "top" -Duration 2000
        Type-Text -Selector "#description" -Text "My laptop screen is flickering intermittently. The issue started yesterday and occurs every few minutes." -Description "Description Field"
        
        # Show network activity if enabled
        if ($demoConfig.ShowNetworkActivity) {
            Show-NetworkActivity -MaxEntries 5
        }
        
        if ($Mode -eq 'Interactive') {
            Pause-ForInteraction -StepName "FormFill" -Message "Form filled. Press Enter to submit..."
        }
        
        # Step 4: Submit Ticket
        Show-StepIndicator -CurrentStep 4 -TotalSteps 5 -StepTitle "Helpdesk Ticket Submission" -AllSteps $workflowSteps
        Show-WorkflowStep -StepNumber 4 -Title "Submit Ticket" -Description "Submitting the helpdesk ticket"
        
        # Draw arrow from form to submit button
        Draw-Arrow -FromSelector "#description" -ToSelector "button[type='submit']" -Color "#FF6B6B" -Duration 2000
        
        Pulse-Element -Selector "button[type='submit']" -Color "#4CAF50" -Pulses 3
        Click-Element -Selector "button[type='submit']" -Description "Submit Button"
        
        # Take screenshot of submission
        Take-Screenshot -Name "ticket-submission" -Description "Ticket submission moment"
        
        # Step 5: View Confirmation
        Show-StepIndicator -CurrentStep 5 -TotalSteps 5 -StepTitle "Helpdesk Ticket Submission" -AllSteps $workflowSteps
        Show-WorkflowStep -StepNumber 5 -Title "View Confirmation" -Description "Viewing the ticket confirmation"
        
        # Wait for confirmation page
        Wait-ForElement -Selector ".success-message, .ticket-number" -TimeoutSeconds 10
        
        # Highlight the ticket number
        Highlight-Element -Selector ".ticket-number" -Color "#4CAF50" -BorderWidth 4 -DurationMs 3000
        
        Show-Annotation -Text "✅ Ticket submitted successfully! Note your ticket number for tracking." -Duration 4000 -Position "center"
        
        # Final screenshot
        Take-Screenshot -Name "ticket-confirmation" -Description "Ticket confirmation page"
        
        # Remove step indicator
        Remove-StepIndicator
        
        # Show success message
        Show-SuccessMessage -Message "Demonstration workflow completed successfully!"
        
        # Display summary
        Show-InfoBox -Title "Workflow Summary" -Content @(
            "Steps Completed: 5/5",
            "Screenshots Taken: 2",
            "Mode: $Mode",
            "Duration: ~2 minutes"
        )
        
    }
    catch {
        Show-ErrorMessage -Message "Workflow failed: $($_.Exception.Message)"
        throw
    }
    finally {
        # Cleanup
        if ($demoConfig.ShowNetworkActivity) {
            Stop-NetworkMonitoring
        }
        Stop-BrowserSession
    }
}

# Execute the demonstration
Start-DemoWorkflow
