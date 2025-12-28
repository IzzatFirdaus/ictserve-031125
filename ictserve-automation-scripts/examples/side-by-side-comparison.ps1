#Requires -Version 7.0
<#
.SYNOPSIS
    Side-by-side browser comparison demonstration.

.DESCRIPTION
    This script demonstrates the side-by-side browser comparison feature,
    showing guest user vs authenticated user workflows simultaneously.

.EXAMPLE
    .\side-by-side-comparison.ps1
    Runs the side-by-side comparison demonstration.

.NOTES
    Version: 1.0.0
    Author: ICTServe Automation Team
#>

[CmdletBinding()]
param(
    [Parameter()]
    [string]$BaseUrl = "http://localhost:8000"
)

# Import required modules
$scriptRoot = Split-Path -Parent $PSScriptRoot
. "$scriptRoot\utilities\common-functions.ps1"
. "$scriptRoot\utilities\browser-automation.ps1"
. "$scriptRoot\utilities\visual-demo-helpers.ps1"

function Start-SideBySideDemo {
    <#
    .SYNOPSIS
        Demonstrates side-by-side browser comparison.
    #>
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║       Side-by-Side Browser Comparison Demonstration           ║" -ForegroundColor Cyan
    Write-Host "║                                                               ║" -ForegroundColor Cyan
    Write-Host "║  Comparing: Guest User vs Authenticated User                  ║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    # Start training session
    $session = Start-TrainingSession -SessionName "User Type Comparison" -Presenter "ICTServe Trainer" -Workflows @("Guest Workflow", "Authenticated Workflow")
    
    try {
        # Initialize side-by-side comparison
        $comparison = Start-SideBySideComparison -LeftTitle "Guest User" -RightTitle "Authenticated User" -WindowWidth 960 -WindowHeight 1080
        
        Add-TrainingNote -Session $session -Note "Starting side-by-side comparison" -Category "Setup"
        
        # Step 1: Navigate to homepage
        Show-WorkflowStep -StepNumber 1 -Title "Navigate to Homepage" -Description "Both users start at the homepage"
        
        Sync-SideBySideAction -Comparison $comparison -Description "Navigate to homepage" -LeftAction {
            Navigate-ToUrl -Url "$BaseUrl" -Description "Guest Homepage"
        } -RightAction {
            Navigate-ToUrl -Url "$BaseUrl" -Description "Authenticated Homepage"
        }
        
        Add-TrainingNote -Session $session -Note "Both browsers navigated to homepage" -Category "Navigation"
        
        # Step 2: Show different UI elements
        Show-WorkflowStep -StepNumber 2 -Title "Compare UI Elements" -Description "Notice the differences in available options"
        
        Show-InfoBox -Title "Key Differences" -Content @(
            "Guest User:",
            "  - Limited menu options",
            "  - Must enter email for each submission",
            "  - No submission history",
            "",
            "Authenticated User:",
            "  - Full menu access",
            "  - Pre-filled user information",
            "  - Access to submission history"
        )
        
        Add-TrainingNote -Session $session -Note "Demonstrated UI differences between user types" -Category "Comparison"
        
        # Step 3: Navigate to helpdesk
        Show-WorkflowStep -StepNumber 3 -Title "Access Helpdesk" -Description "Both users access the helpdesk form"
        
        Sync-SideBySideAction -Comparison $comparison -Description "Navigate to helpdesk" -LeftAction {
            Click-Element -Selector "a[href*='helpdesk']" -Description "Guest Helpdesk Link"
        } -RightAction {
            Click-Element -Selector "a[href*='helpdesk']" -Description "Authenticated Helpdesk Link"
        }
        
        # Step 4: Compare form fields
        Show-WorkflowStep -StepNumber 4 -Title "Compare Form Fields" -Description "Notice the pre-filled fields for authenticated users"
        
        Show-InfoBox -Title "Form Field Comparison" -Content @(
            "Guest User Form:",
            "  - All fields empty",
            "  - Email required for tracking",
            "  - No auto-complete",
            "",
            "Authenticated User Form:",
            "  - Name pre-filled from profile",
            "  - Email pre-filled and verified",
            "  - Department auto-selected"
        )
        
        Add-TrainingNote -Session $session -Note "Form field differences highlighted" -Category "Forms"
        
        # Step 5: Submit tickets
        Show-WorkflowStep -StepNumber 5 -Title "Submit Tickets" -Description "Both users submit their tickets"
        
        # Guest user fills form
        Write-Host "  [Guest] Filling form manually..." -ForegroundColor Yellow
        Type-Text -Selector "#name" -Text "Guest User" -Description "Guest Name"
        Type-Text -Selector "#email" -Text "guest@example.com" -Description "Guest Email"
        
        # Authenticated user has pre-filled form
        Write-Host "  [Authenticated] Form already pre-filled!" -ForegroundColor Green
        
        Add-TrainingNote -Session $session -Note "Demonstrated form submission differences" -Category "Submission"
        
        # Step 6: Compare confirmation
        Show-WorkflowStep -StepNumber 6 -Title "Compare Confirmation" -Description "Different confirmation experiences"
        
        Show-InfoBox -Title "Confirmation Differences" -Content @(
            "Guest User:",
            "  - Ticket number displayed",
            "  - Email confirmation sent",
            "  - Must save ticket number manually",
            "",
            "Authenticated User:",
            "  - Ticket added to dashboard",
            "  - Full history available",
            "  - Real-time status updates"
        )
        
        Add-TrainingNote -Session $session -Note "Confirmation experience comparison complete" -Category "Confirmation"
        
        # End training session
        $session = Stop-TrainingSession -Session $session
        
        # Display final summary
        Show-SuccessMessage -Message "Side-by-side comparison demonstration completed!"
        
    }
    catch {
        Show-ErrorMessage -Message "Demonstration failed: $($_.Exception.Message)"
        throw
    }
}

# Execute the demonstration
Start-SideBySideDemo
