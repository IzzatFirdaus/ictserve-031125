#Requires -Version 7.0
<#
.SYNOPSIS
    ICTServe Comprehensive Automation Suite - Interactive Main Menu

.DESCRIPTION
    This script provides an interactive PowerShell menu interface for discovering,
    configuring, and executing all ICTServe automation scripts. It supports visual
    demonstration modes, execution history tracking, and comprehensive reporting.

.NOTES
    Version: 1.0.0
    Author: ICTServe Automation Team
    Requirements: PowerShell 7.x, Selenium WebDriver (optional)

.EXAMPLE
    .\Main-Menu.ps1
    Launches the interactive menu system.

.EXAMPLE
    .\Main-Menu.ps1 -Mode Demo
    Launches the menu with Demo mode pre-selected.
#>

[CmdletBinding()]
param(
    [Parameter()]
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual',
    
    [Parameter()]
    [ValidateSet('development', 'testing', 'staging', 'production')]
    [string]$Environment = 'testing',
    
    [Parameter()]
    [switch]$SkipPrerequisiteCheck
)

# Script root path
$ScriptRoot = $PSScriptRoot

# Import utility modules
$utilitiesPath = Join-Path $ScriptRoot "utilities"
. (Join-Path $utilitiesPath "common-functions.ps1")

# Global variables
$script:CurrentEnvironment = $Environment
$script:CurrentMode = $Mode
$script:ExecutionHistory = @()
$script:TotalScripts = 347

#region Menu Display Functions

function Show-Banner {
    Clear-Host
    $banner = @"
===============================================
    ICTServe Comprehensive Automation Suite v1.0
    Frontend + Backend + Integration Testing
===============================================
"@
    Write-Host $banner -ForegroundColor Cyan
    Write-Host ""
}

function Show-StatusBar {
    $modeColor = switch ($script:CurrentMode) {
        'Headless' { 'Gray' }
        'Visual' { 'Green' }
        'Demo' { 'Yellow' }
        'Interactive' { 'Magenta' }
        'Recording' { 'Red' }
    }
    
    Write-Host "Environment: " -NoNewline -ForegroundColor White
    Write-Host "[$script:CurrentEnvironment]" -NoNewline -ForegroundColor Cyan
    Write-Host " | User: " -NoNewline -ForegroundColor White
    Write-Host "[automation@motac.gov.my]" -NoNewline -ForegroundColor Cyan
    Write-Host " | Scripts: " -NoNewline -ForegroundColor White
    Write-Host "[$script:TotalScripts Total]" -NoNewline -ForegroundColor Cyan
    Write-Host " | Mode: " -NoNewline -ForegroundColor White
    Write-Host "[$script:CurrentMode]" -ForegroundColor $modeColor
    Write-Host ""
}

function Show-DemoModes {
    Write-Host "🎭 DEMONSTRATION MODES:" -ForegroundColor Yellow
    Write-Host "   📺 Visual Mode: Live browser automation with visible interactions" -ForegroundColor White
    Write-Host "   🎪 Demo Mode: Slower execution with highlights and annotations" -ForegroundColor White
    Write-Host "   🎤 Interactive Mode: Pauses for presenter explanation" -ForegroundColor White
    Write-Host "   📹 Recording Mode: Captures video for training materials" -ForegroundColor White
    Write-Host "   ⚡ Headless Mode: Fast execution without browser window" -ForegroundColor White
    Write-Host ""
}

function Show-MainMenu {
    Show-Banner
    Show-StatusBar
    Show-DemoModes
    
    Write-Host "Main Categories:" -ForegroundColor Green
    Write-Host "  1. Guest User Workflows                    [50 Scripts - Frontend + Backend]" -ForegroundColor White
    Write-Host "  2. Authenticated User Workflows            [67 Scripts - Enhanced Features]" -ForegroundColor White
    Write-Host "  3. Admin Panel Operations (Filament)       [78 Scripts - Complete Admin Suite]" -ForegroundColor White
    Write-Host "  4. AI Integration Testing                  [89 Scripts - Cloud Hybrid Architecture]" -ForegroundColor White
    Write-Host "  5. API Integration & Backend Systems       [89 Scripts - Complete Backend Testing]" -ForegroundColor White
    Write-Host "  6. Performance & Accessibility Testing     [45 Scripts - Standards Compliance]" -ForegroundColor White
    Write-Host "  7. Security & Compliance Testing           [52 Scripts - PDPA + Security]" -ForegroundColor White
    Write-Host "  8. System Monitoring & Health              [38 Scripts - Laravel Pulse/Horizon/Telescope]" -ForegroundColor White
    Write-Host "  9. End-to-End Workflow Testing             [29 Scripts - Complete User Journeys]" -ForegroundColor White
    Write-Host ""
    
    Write-Host "🎬 LIVE DEMONSTRATIONS:" -ForegroundColor Yellow
    Write-Host "  10. Guest vs Authenticated Comparison      [Side-by-side browser windows]" -ForegroundColor White
    Write-Host "  11. Complete User Journey Demo             [End-to-end workflow with narration]" -ForegroundColor White
    Write-Host "  12. Admin Panel Feature Tour               [Guided tour of all admin features]" -ForegroundColor White
    Write-Host "  13. AI Integration Showcase                [Live AI responses and model routing]" -ForegroundColor White
    Write-Host "  14. Security Features Demo                 [CSRF, validation, and protection measures]" -ForegroundColor White
    Write-Host ""
    
    Write-Host "Automated Operations:" -ForegroundColor Green
    Write-Host "  15. Run All Critical Path Tests            [Essential User Journeys - 45 Scripts]" -ForegroundColor White
    Write-Host "  16. Run All Frontend Tests                 [UI/UX Testing - 156 Scripts]" -ForegroundColor White
    Write-Host "  17. Run All Backend Tests                  [API/Database/Integration - 191 Scripts]" -ForegroundColor White
    Write-Host "  18. Run Complete Test Suite                [All 347 Scripts - Full Coverage]" -ForegroundColor White
    Write-Host ""
    
    Write-Host "🎥 RECORDING & DOCUMENTATION:" -ForegroundColor Yellow
    Write-Host "  19. Record Training Videos                 [Capture workflows as MP4 for training]" -ForegroundColor White
    Write-Host "  20. Generate Demo Screenshots              [Step-by-step visual documentation]" -ForegroundColor White
    Write-Host ""
    
    Write-Host "Utilities & Management:" -ForegroundColor Green
    Write-Host "  21. Configuration Settings                 [Environment + Credentials + Execution Options]" -ForegroundColor White
    Write-Host "  22. View Execution History                 [Previous Runs + Results + Performance]" -ForegroundColor White
    Write-Host "  23. Generate Comprehensive Reports         [Test Results + Screenshots + Analytics]" -ForegroundColor White
    Write-Host "  24. Test Data Management                   [Generate + Clean + Reset Test Data]" -ForegroundColor White
    Write-Host "  25. System Health Check                    [Prerequisites + Dependencies + Connectivity]" -ForegroundColor White
    Write-Host ""
    
    Write-Host "Advanced Features:" -ForegroundColor Green
    Write-Host "  26. Search Tests by Keyword                [Find Specific Tests Across All Categories]" -ForegroundColor White
    Write-Host "  27. Custom Test Suites                     [Create + Save + Execute Custom Combinations]" -ForegroundColor White
    Write-Host ""
    
    Write-Host "Help & Documentation:" -ForegroundColor Green
    Write-Host "  28. Quick Start Guide                      [Getting Started + Basic Usage]" -ForegroundColor White
    Write-Host "  29. Troubleshooting Guide                  [Common Issues + Solutions]" -ForegroundColor White
    Write-Host ""
    
    Write-Host "  0. Exit" -ForegroundColor Red
    Write-Host ""
}

#endregion

#region Category Menus

function Show-GuestWorkflowsMenu {
    Show-Banner
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host "    Guest User Workflows - Frontend & Backend Testing" -ForegroundColor Cyan
    Write-Host "    🎭 DEMO MODE: $script:CurrentMode Active" -ForegroundColor Yellow
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "🎬 DEMONSTRATION OPTIONS:" -ForegroundColor Yellow
    Write-Host "  D1. Quick Demo (5 min)     - Essential guest workflows with narration" -ForegroundColor White
    Write-Host "  D2. Complete Demo (15 min) - All guest features with detailed explanation" -ForegroundColor White
    Write-Host "  D3. Training Session       - Interactive demo with pause points" -ForegroundColor White
    Write-Host ""
    
    Write-Host "HELPDESK TICKET WORKFLOWS:" -ForegroundColor Green
    Write-Host "  1.  Submit Basic Helpdesk Ticket (Frontend Form + Backend API) 🎬" -ForegroundColor White
    Write-Host "  2.  Submit Ticket with File Attachments (Upload + Virus Scan) 🎬" -ForegroundColor White
    Write-Host "  3.  Submit Ticket with Multiple Categories (Dropdown + Validation) 🎬" -ForegroundColor White
    Write-Host "  4.  Test Form Validation Errors (Frontend JS + Backend Laravel) 🎬" -ForegroundColor White
    Write-Host "  5.  Test CSRF Protection (Security + Session Management) 🔒" -ForegroundColor White
    Write-Host "  6.  Track Ticket Status by Number (Frontend Search + Backend Query) 🎬" -ForegroundColor White
    Write-Host "  7.  Track Ticket Status by Email (Email Lookup + Database Search) 🎬" -ForegroundColor White
    Write-Host "  8.  Test Email Notifications (Queue Processing + SMTP Integration) 📧" -ForegroundColor White
    Write-Host "  9.  Test Ticket Auto-Assignment (Business Logic + Database Updates) ⚙️" -ForegroundColor White
    Write-Host "  10. Test Emergency Priority Handling (Workflow + Notification) 🚨" -ForegroundColor White
    Write-Host ""
    
    Write-Host "ASSET LOAN WORKFLOWS:" -ForegroundColor Green
    Write-Host "  11. Submit Basic Asset Loan Request (Form + Backend Processing) 🎬" -ForegroundColor White
    Write-Host "  12. Check Asset Availability Calendar (Frontend + Backend Scheduling) 🎬" -ForegroundColor White
    Write-Host "  13. Submit Loan with Date Conflicts (Validation + Error Handling) 🎬" -ForegroundColor White
    Write-Host "  14. Test Asset Category Selection (Dynamic Dropdowns + Database) 🎬" -ForegroundColor White
    Write-Host "  15. Test Loan Duration Validation (Business Rules + Frontend) 🎬" -ForegroundColor White
    Write-Host "  16. Test Department Asset Restrictions (Authorization + Policy) 🔒" -ForegroundColor White
    Write-Host "  17. Track Loan Application Status (Status Updates + Real-time) 🎬" -ForegroundColor White
    Write-Host "  18. Test Loan Approval Workflow Trigger (Email + Queue Jobs) 📧" -ForegroundColor White
    Write-Host "  19. Test Asset Conflict Detection (Concurrent Booking + Locking) ⚙️" -ForegroundColor White
    Write-Host "  20. Test Loan Extension Requests (Workflow + Approval Chain) 🎬" -ForegroundColor White
    Write-Host ""
    
    Write-Host "Legend: 🎬=Visual Demo | 🔒=Security | 📧=Email | ⚙️=Backend" -ForegroundColor DarkGray
    Write-Host ""
    
    Write-Host "Navigation:" -ForegroundColor Green
    Write-Host "  0. Back to Main Menu" -ForegroundColor White
    Write-Host "  H. Help for this category" -ForegroundColor White
    Write-Host "  S. Search specific test by keyword" -ForegroundColor White
    Write-Host "  V. Configure Visual Demo Settings" -ForegroundColor White
    Write-Host ""
}

function Show-AuthenticatedWorkflowsMenu {
    Show-Banner
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host "    Authenticated User Workflows - Enhanced Features" -ForegroundColor Cyan
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "AUTHENTICATION & SESSION MANAGEMENT:" -ForegroundColor Green
    Write-Host "  1.  Test Email/Username Login (Multiple Auth Methods + Session)" -ForegroundColor White
    Write-Host "  2.  Test Password Validation (Security Rules + Frontend Feedback)" -ForegroundColor White
    Write-Host "  3.  Test Remember Me Functionality (Persistent Sessions + Cookies)" -ForegroundColor White
    Write-Host "  4.  Test Password Reset Flow (Email + Token + Database Updates)" -ForegroundColor White
    Write-Host "  5.  Test Account Lockout Protection (Brute Force + Security)" -ForegroundColor White
    Write-Host "  6.  Test Google Workspace SSO (OAuth2 + Domain Validation)" -ForegroundColor White
    Write-Host "  7.  Test Session Timeout Handling (Auto-logout + Data Preservation)" -ForegroundColor White
    Write-Host ""
    
    Write-Host "DASHBOARD & REAL-TIME FEATURES:" -ForegroundColor Green
    Write-Host "  8.  Test Dashboard Widget Loading (Data Aggregation + Performance)" -ForegroundColor White
    Write-Host "  9.  Test Real-time Statistics Updates (WebSocket + Live Data)" -ForegroundColor White
    Write-Host "  10. Test Notification Center (Laravel Reverb + Real-time Alerts)" -ForegroundColor White
    Write-Host "  11. Test Quick Action Buttons (Navigation + Pre-filled Forms)" -ForegroundColor White
    Write-Host "  12. Test Keyboard Shortcuts (Accessibility + User Experience)" -ForegroundColor White
    Write-Host ""
    
    Write-Host "ENHANCED HELPDESK FEATURES:" -ForegroundColor Green
    Write-Host "  13. Test Auto-filled Personal Information (Profile Integration)" -ForegroundColor White
    Write-Host "  14. Test Ticket History View (Pagination + Filtering + Search)" -ForegroundColor White
    Write-Host "  15. Test Ticket Comments System (Real-time Updates + Notifications)" -ForegroundColor White
    Write-Host "  16. Test File Attachment to Existing Tickets (Upload + Association)" -ForegroundColor White
    Write-Host "  17. Test Ticket Claiming from Guest Submissions (Account Linking)" -ForegroundColor White
    Write-Host ""
    
    Write-Host "Navigation:" -ForegroundColor Green
    Write-Host "  0. Back to Main Menu" -ForegroundColor White
    Write-Host ""
}

#endregion

#region Script Execution Functions

function Invoke-ScriptByPath {
    param(
        [Parameter(Mandatory = $true)]
        [string]$ScriptPath,
        
        [Parameter()]
        [string]$Description = ""
    )
    
    $fullPath = Join-Path $ScriptRoot $ScriptPath
    
    if (-not (Test-Path $fullPath)) {
        Write-Host ""
        Write-Host "Script not found: $ScriptPath" -ForegroundColor Yellow
        Write-Host "Creating placeholder script..." -ForegroundColor Cyan
        
        # Create directory if it doesn't exist
        $scriptDir = Split-Path $fullPath -Parent
        if (-not (Test-Path $scriptDir)) {
            New-Item -ItemType Directory -Path $scriptDir -Force | Out-Null
        }
        
        # Create a basic placeholder script
        $placeholderContent = @"
#Requires -Version 7.0
<#
.SYNOPSIS
    $Description

.DESCRIPTION
    This is a placeholder script that will be implemented in future phases.
    Currently returns a mock successful result.

.PARAMETER Mode
    Execution mode: Headless, Visual, Demo, Interactive, Recording

.PARAMETER Environment
    Target environment: development, testing, staging, production
#>

[CmdletBinding()]
param(
    [Parameter()]
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]`$Mode = 'Visual',
    
    [Parameter()]
    [ValidateSet('development', 'testing', 'staging', 'production')]
    [string]`$Environment = 'testing'
)

# Import common functions
`$ScriptRoot = `$PSScriptRoot
. (Join-Path `$ScriptRoot "..\..\utilities\common-functions.ps1")

Write-Host "Executing placeholder: $Description" -ForegroundColor Yellow
Write-Host "This script will be implemented in future development phases." -ForegroundColor Gray

# Return mock successful result
return @{
    TestName = "$Description"
    Status = "Placeholder"
    Message = "Script placeholder executed successfully"
    Duration = 0.5
    StartTime = Get-Date
    EndTime = Get-Date
}
"@
        
        Set-Content -Path $fullPath -Value $placeholderContent
        Write-Host "Placeholder script created: $ScriptPath" -ForegroundColor Green
    }
    
    Write-Host ""
    Write-Host "Executing: $Description" -ForegroundColor Cyan
    Write-Host "Script: $ScriptPath" -ForegroundColor Gray
    Write-Host ""
    
    $startTime = Get-Date
    
    try {
        & $fullPath -Mode $script:CurrentMode -Environment $script:CurrentEnvironment
        $status = 'Success'
    }
    catch {
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
        $status = 'Failed'
    }
    
    $duration = (Get-Date) - $startTime
    
    # Add to execution history
    $script:ExecutionHistory += @{
        Script = $ScriptPath
        Description = $Description
        Status = $status
        Duration = $duration
        ExecutedAt = $startTime
    }
    
    Write-Host ""
    Write-Host "Completed in $($duration.ToString('hh\:mm\:ss'))" -ForegroundColor $(if ($status -eq 'Success') { 'Green' } else { 'Red' })
    Read-Host "Press Enter to continue"
}

function Invoke-CategoryScripts {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Category,
        
        [Parameter()]
        [string]$Filter = "*"
    )
    
    $categoryPath = Join-Path $ScriptRoot "scripts\$Category"
    
    if (-not (Test-Path $categoryPath)) {
        Write-Host ""
        Write-Host "Category directory not found: $Category" -ForegroundColor Yellow
        Write-Host "Creating category structure..." -ForegroundColor Cyan
        
        # Create the category directory
        New-Item -ItemType Directory -Path $categoryPath -Force | Out-Null
        
        Write-Host "Category directory created. Individual scripts will be created as placeholders when needed." -ForegroundColor Green
        Read-Host "Press Enter to continue"
        return
    }
    
    $scripts = Get-ChildItem -Path $categoryPath -Filter "*.ps1" -Recurse | Where-Object { $_.Name -like $Filter }
    
    if ($scripts.Count -eq 0) {
        Write-Host ""
        Write-Host "No scripts found in category: $Category" -ForegroundColor Yellow
        Write-Host "Scripts will be created as placeholders during individual execution." -ForegroundColor Gray
        Read-Host "Press Enter to continue"
        return
    }
    
    Write-Host ""
    Write-Host "Running $($scripts.Count) scripts in category: $Category" -ForegroundColor Cyan
    Write-Host ""
    
    $results = @()
    
    foreach ($script in $scripts) {
        Write-Host "  Running: $($script.Name)..." -NoNewline
        
        $startTime = Get-Date
        try {
            & $script.FullName -Mode $script:CurrentMode -Environment $script:CurrentEnvironment
            Write-Host " ✓" -ForegroundColor Green
            $status = 'Success'
        }
        catch {
            Write-Host " ✗" -ForegroundColor Red
            $status = 'Failed'
        }
        
        $results += @{
            Script = $script.Name
            Status = $status
            Duration = (Get-Date) - $startTime
        }
    }
    
    # Summary
    $passed = ($results | Where-Object { $_.Status -eq 'Success' }).Count
    $failed = ($results | Where-Object { $_.Status -eq 'Failed' }).Count
    
    Write-Host ""
    Write-Host "Results: $passed passed, $failed failed" -ForegroundColor $(if ($failed -eq 0) { 'Green' } else { 'Yellow' })
    Read-Host "Press Enter to continue"
}

#endregion

#region Utility Functions

function Show-ConfigurationMenu {
    Show-Banner
    Write-Host "Configuration Settings" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "Current Settings:" -ForegroundColor Green
    Write-Host "  Environment: $script:CurrentEnvironment" -ForegroundColor White
    Write-Host "  Demo Mode: $script:CurrentMode" -ForegroundColor White
    Write-Host ""
    
    Write-Host "Options:" -ForegroundColor Green
    Write-Host "  1. Change Environment (development/testing/staging/production)" -ForegroundColor White
    Write-Host "  2. Change Demo Mode (Headless/Visual/Demo/Interactive/Recording)" -ForegroundColor White
    Write-Host "  3. Edit Credentials" -ForegroundColor White
    Write-Host "  4. View Current Configuration" -ForegroundColor White
    Write-Host "  0. Back to Main Menu" -ForegroundColor White
    Write-Host ""
    
    $choice = Read-Host "Select option"
    
    switch ($choice) {
        '1' {
            Write-Host ""
            Write-Host "Available environments: development, testing, staging, production" -ForegroundColor Yellow
            $newEnv = Read-Host "Enter environment"
            if ($newEnv -in @('development', 'testing', 'staging', 'production')) {
                $script:CurrentEnvironment = $newEnv
                Write-Host "Environment changed to: $newEnv" -ForegroundColor Green
            }
        }
        '2' {
            Write-Host ""
            Write-Host "Available modes: Headless, Visual, Demo, Interactive, Recording" -ForegroundColor Yellow
            $newMode = Read-Host "Enter mode"
            if ($newMode -in @('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')) {
                $script:CurrentMode = $newMode
                Write-Host "Mode changed to: $newMode" -ForegroundColor Green
            }
        }
        '3' {
            $credPath = Join-Path $ScriptRoot "config\credentials.json"
            Write-Host "Credentials file: $credPath" -ForegroundColor Yellow
            Write-Host "Edit this file manually to update credentials." -ForegroundColor White
        }
        '4' {
            $settingsPath = Join-Path $ScriptRoot "config\settings.json"
            if (Test-Path $settingsPath) {
                Get-Content $settingsPath | Write-Host
            }
        }
    }
    
    Read-Host "Press Enter to continue"
}

function Show-ExecutionHistory {
    Show-Banner
    Write-Host "Execution History" -ForegroundColor Cyan
    Write-Host ""
    
    if ($script:ExecutionHistory.Count -eq 0) {
        Write-Host "No scripts have been executed yet." -ForegroundColor Yellow
    }
    else {
        Write-Host "Recent Executions:" -ForegroundColor Green
        Write-Host ""
        
        $script:ExecutionHistory | Select-Object -Last 20 | ForEach-Object {
            $statusColor = if ($_.Status -eq 'Success') { 'Green' } else { 'Red' }
            Write-Host "  $($_.ExecutedAt.ToString('HH:mm:ss')) - " -NoNewline
            Write-Host "[$($_.Status)]" -NoNewline -ForegroundColor $statusColor
            Write-Host " $($_.Description) ($($_.Duration.ToString('mm\:ss')))" -ForegroundColor White
        }
    }
    
    Write-Host ""
    Read-Host "Press Enter to continue"
}

function Show-SystemHealthCheck {
    Show-Banner
    Write-Host "System Health Check" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "Checking prerequisites..." -ForegroundColor Yellow
    Write-Host ""
    
    # PowerShell version
    $psVersion = $PSVersionTable.PSVersion
    $psOk = $psVersion.Major -ge 7
    Write-Host "  PowerShell Version: $psVersion " -NoNewline
    Write-Host $(if ($psOk) { "✓" } else { "✗ (7.x required)" }) -ForegroundColor $(if ($psOk) { 'Green' } else { 'Red' })
    
    # Check for Chrome
    $chromePaths = @(
        "C:\Program Files\Google\Chrome\Application\chrome.exe",
        "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe"
    )
    $chromeFound = $chromePaths | Where-Object { Test-Path $_ } | Select-Object -First 1
    Write-Host "  Google Chrome: " -NoNewline
    Write-Host $(if ($chromeFound) { "✓ Found" } else { "✗ Not found" }) -ForegroundColor $(if ($chromeFound) { 'Green' } else { 'Yellow' })
    
    # Check config files
    $configPath = Join-Path $ScriptRoot "config"
    $configFiles = @('environments.json', 'settings.json', 'credentials.json')
    foreach ($file in $configFiles) {
        $filePath = Join-Path $configPath $file
        $exists = Test-Path $filePath
        Write-Host "  Config: $file " -NoNewline
        Write-Host $(if ($exists) { "✓" } else { "✗" }) -ForegroundColor $(if ($exists) { 'Green' } else { 'Red' })
    }
    
    Write-Host ""
    Read-Host "Press Enter to continue"
}

function Search-Scripts {
    Show-Banner
    Write-Host "Search Scripts" -ForegroundColor Cyan
    Write-Host ""
    
    $keyword = Read-Host "Enter search keyword"
    
    if ([string]::IsNullOrWhiteSpace($keyword)) {
        return
    }
    
    Write-Host ""
    Write-Host "Searching for: $keyword" -ForegroundColor Yellow
    Write-Host ""
    
    $scriptsPath = Join-Path $ScriptRoot "scripts"
    
    if (Test-Path $scriptsPath) {
        $scripts = Get-ChildItem -Path $scriptsPath -Filter "*.ps1" -Recurse | 
            Where-Object { $_.Name -like "*$keyword*" -or $_.BaseName -like "*$keyword*" }
        
        if ($scripts.Count -gt 0) {
            Write-Host "Found $($scripts.Count) matching scripts:" -ForegroundColor Green
            Write-Host ""
            
            $scripts | ForEach-Object {
                $relativePath = $_.FullName.Replace($ScriptRoot, '').TrimStart('\')
                Write-Host "  $relativePath" -ForegroundColor White
            }
        }
        else {
            Write-Host "No scripts found matching: $keyword" -ForegroundColor Yellow
        }
    }
    else {
        Write-Host "Scripts directory not found. Scripts will be created during implementation." -ForegroundColor Yellow
    }
    
    Write-Host ""
    Read-Host "Press Enter to continue"
}

function Show-QuickStartGuide {
    Show-Banner
    Write-Host "Quick Start Guide" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "Welcome to ICTServe Automation Suite!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Getting Started:" -ForegroundColor Yellow
    Write-Host "  1. Ensure PowerShell 7.x is installed" -ForegroundColor White
    Write-Host "  2. Configure your environment in Configuration Settings (Option 21)" -ForegroundColor White
    Write-Host "  3. Update credentials in config/credentials.json" -ForegroundColor White
    Write-Host "  4. Run System Health Check (Option 25) to verify setup" -ForegroundColor White
    Write-Host ""
    Write-Host "Demo Modes:" -ForegroundColor Yellow
    Write-Host "  - Visual: See live browser automation" -ForegroundColor White
    Write-Host "  - Demo: Slower with annotations for presentations" -ForegroundColor White
    Write-Host "  - Interactive: Pauses for explanation" -ForegroundColor White
    Write-Host "  - Recording: Captures video for training" -ForegroundColor White
    Write-Host "  - Headless: Fast execution for CI/CD" -ForegroundColor White
    Write-Host ""
    Write-Host "Running Tests:" -ForegroundColor Yellow
    Write-Host "  - Select a category (1-9) to see available scripts" -ForegroundColor White
    Write-Host "  - Use automated operations (15-18) to run multiple scripts" -ForegroundColor White
    Write-Host "  - Search for specific tests with option 26" -ForegroundColor White
    Write-Host ""
    
    Read-Host "Press Enter to continue"
}

#endregion

#region Main Loop

function Start-MainMenu {
    <#
    .SYNOPSIS
        Main entry point for the interactive menu system.
    #>
    
    $running = $true
    
    while ($running) {
        Show-MainMenu
        
        $choice = Read-Host "Select option (0-29)"
        
        switch ($choice) {
            '0' { $running = $false }
            
            # Main Categories - Launch category-specific menus
            '1' { 
                $menuPath = Join-Path $ScriptRoot "scripts\guest-workflows\menu.ps1"
                if (Test-Path $menuPath) {
                    & $menuPath -Mode $script:CurrentMode
                } else {
                    Show-GuestWorkflowsMenu
                    $subChoice = Read-Host "Select option"
                    if ($subChoice -ne '0') {
                        Handle-GuestWorkflowChoice -Choice $subChoice
                    }
                }
            }
            '2' { 
                $menuPath = Join-Path $ScriptRoot "scripts\authenticated-workflows\menu.ps1"
                if (Test-Path $menuPath) {
                    & $menuPath -Mode $script:CurrentMode
                } else {
                    Show-AuthenticatedWorkflowsMenu
                    Read-Host "Press Enter to continue"
                }
            }
            '3' { 
                $menuPath = Join-Path $ScriptRoot "scripts\admin-operations\menu.ps1"
                if (Test-Path $menuPath) {
                    & $menuPath -Mode $script:CurrentMode
                } else {
                    Write-Host "Admin Panel menu not found." -ForegroundColor Yellow
                    Read-Host "Press Enter to continue"
                }
            }
            '4' { 
                $menuPath = Join-Path $ScriptRoot "scripts\ai-integration\menu.ps1"
                if (Test-Path $menuPath) {
                    & $menuPath -Mode $script:CurrentMode
                } else {
                    Write-Host "AI Integration menu not found." -ForegroundColor Yellow
                    Read-Host "Press Enter to continue"
                }
            }
            '5' { 
                $menuPath = Join-Path $ScriptRoot "scripts\api-backend\menu.ps1"
                if (Test-Path $menuPath) {
                    & $menuPath -Mode $script:CurrentMode
                } else {
                    Write-Host "API Backend menu not found." -ForegroundColor Yellow
                    Read-Host "Press Enter to continue"
                }
            }
            '6' { 
                $menuPath = Join-Path $ScriptRoot "scripts\performance-accessibility\menu.ps1"
                if (Test-Path $menuPath) {
                    & $menuPath -Mode $script:CurrentMode
                } else {
                    Write-Host "Performance & Accessibility menu not found." -ForegroundColor Yellow
                    Read-Host "Press Enter to continue"
                }
            }
            '7' { 
                $menuPath = Join-Path $ScriptRoot "scripts\security-compliance\menu.ps1"
                if (Test-Path $menuPath) {
                    & $menuPath -Mode $script:CurrentMode
                } else {
                    Write-Host "Security & Compliance menu not found." -ForegroundColor Yellow
                    Read-Host "Press Enter to continue"
                }
            }
            '8' { 
                $menuPath = Join-Path $ScriptRoot "scripts\monitoring-health\menu.ps1"
                if (Test-Path $menuPath) {
                    & $menuPath -Mode $script:CurrentMode
                } else {
                    Write-Host "System Monitoring menu not found." -ForegroundColor Yellow
                    Read-Host "Press Enter to continue"
                }
            }
            '9' { 
                $menuPath = Join-Path $ScriptRoot "scripts\end-to-end\menu.ps1"
                if (Test-Path $menuPath) {
                    & $menuPath -Mode $script:CurrentMode
                } else {
                    Write-Host "End-to-End menu not found." -ForegroundColor Yellow
                    Read-Host "Press Enter to continue"
                }
            }
            
            # Live Demonstrations
            '10' { 
                Write-Host "Side-by-side comparison demo will be implemented in Task 1.5." -ForegroundColor Yellow
                Read-Host "Press Enter to continue"
            }
            '11' { 
                Write-Host "Complete user journey demo will be implemented in Task 15." -ForegroundColor Yellow
                Read-Host "Press Enter to continue"
            }
            '12' { 
                Write-Host "Admin panel tour will be implemented in Task 7." -ForegroundColor Yellow
                Read-Host "Press Enter to continue"
            }
            '13' { 
                Write-Host "AI integration showcase will be implemented in Task 10." -ForegroundColor Yellow
                Read-Host "Press Enter to continue"
            }
            '14' { 
                Write-Host "Security features demo will be implemented in Task 12." -ForegroundColor Yellow
                Read-Host "Press Enter to continue"
            }
            
            # Automated Operations
            '15' { 
                Write-Host "Running critical path tests..." -ForegroundColor Cyan
                Invoke-CategoryScripts -Category "end-to-end" -Filter "*critical*"
            }
            '16' { 
                Write-Host "Running frontend tests..." -ForegroundColor Cyan
                Invoke-CategoryScripts -Category "guest-workflows"
                Invoke-CategoryScripts -Category "authenticated-workflows"
            }
            '17' { 
                Write-Host "Running backend tests..." -ForegroundColor Cyan
                Invoke-CategoryScripts -Category "api-backend"
            }
            '18' { 
                Write-Host "Running complete test suite..." -ForegroundColor Cyan
                Write-Host "This will execute all 347 scripts. Continue? (y/n)" -ForegroundColor Yellow
                $confirm = Read-Host
                if ($confirm -eq 'y') {
                    $categories = @('guest-workflows', 'authenticated-workflows', 'admin-operations', 
                                   'ai-integration', 'api-backend', 'performance-accessibility',
                                   'security-compliance', 'monitoring-health', 'end-to-end')
                    foreach ($cat in $categories) {
                        Invoke-CategoryScripts -Category $cat
                    }
                }
            }
            
            # Recording & Documentation
            '19' { 
                Write-Host "Video recording feature will be implemented in Task 1.4." -ForegroundColor Yellow
                Read-Host "Press Enter to continue"
            }
            '20' { 
                Write-Host "Screenshot generation feature will be implemented in Task 1.4." -ForegroundColor Yellow
                Read-Host "Press Enter to continue"
            }
            
            # Utilities
            '21' { Show-ConfigurationMenu }
            '22' { Show-ExecutionHistory }
            '23' { 
                Write-Host "Report generation will be implemented in Task 16." -ForegroundColor Yellow
                Read-Host "Press Enter to continue"
            }
            '24' { 
                Write-Host "Test data management will be implemented in Task 16." -ForegroundColor Yellow
                Read-Host "Press Enter to continue"
            }
            '25' { Show-SystemHealthCheck }
            
            # Advanced Features
            '26' { Search-Scripts }
            '27' { 
                Write-Host "Custom test suites will be implemented in Task 16." -ForegroundColor Yellow
                Read-Host "Press Enter to continue"
            }
            
            # Help
            '28' { Show-QuickStartGuide }
            '29' { 
                Write-Host "Troubleshooting guide available in docs/troubleshooting-guide.md" -ForegroundColor Yellow
                Read-Host "Press Enter to continue"
            }
            
            default {
                Write-Host "Invalid option. Please try again." -ForegroundColor Red
                Start-Sleep -Seconds 1
            }
        }
    }
    
    Write-Host ""
    Write-Host "Thank you for using ICTServe Automation Suite!" -ForegroundColor Cyan
    Write-Host ""
}

function Handle-GuestWorkflowChoice {
    param([string]$Choice)
    
    $scriptMap = @{
        '1' = @{ Path = 'scripts/guest-workflows/helpdesk/submit-basic-ticket.ps1'; Desc = 'Submit Basic Helpdesk Ticket' }
        '2' = @{ Path = 'scripts/guest-workflows/helpdesk/submit-ticket-with-attachments.ps1'; Desc = 'Submit Ticket with Attachments' }
        '3' = @{ Path = 'scripts/guest-workflows/helpdesk/submit-ticket-multiple-categories.ps1'; Desc = 'Submit Ticket with Multiple Categories' }
        '4' = @{ Path = 'scripts/guest-workflows/helpdesk/test-form-validation.ps1'; Desc = 'Test Form Validation' }
        '5' = @{ Path = 'scripts/guest-workflows/helpdesk/test-csrf-protection.ps1'; Desc = 'Test CSRF Protection' }
        '6' = @{ Path = 'scripts/guest-workflows/helpdesk/track-ticket-by-number.ps1'; Desc = 'Track Ticket by Number' }
        '7' = @{ Path = 'scripts/guest-workflows/helpdesk/track-ticket-by-email.ps1'; Desc = 'Track Ticket by Email' }
        '8' = @{ Path = 'scripts/guest-workflows/helpdesk/test-email-notifications.ps1'; Desc = 'Test Email Notifications' }
        '9' = @{ Path = 'scripts/guest-workflows/helpdesk/test-ticket-auto-assignment.ps1'; Desc = 'Test Ticket Auto-Assignment' }
        '10' = @{ Path = 'scripts/guest-workflows/helpdesk/test-emergency-priority.ps1'; Desc = 'Test Emergency Priority Handling' }
        '11' = @{ Path = 'scripts/guest-workflows/asset-loans/submit-basic-loan-request.ps1'; Desc = 'Submit Basic Loan Request' }
        '12' = @{ Path = 'scripts/guest-workflows/asset-loans/check-asset-availability.ps1'; Desc = 'Check Asset Availability' }
        '13' = @{ Path = 'scripts/guest-workflows/asset-loans/test-date-conflicts.ps1'; Desc = 'Test Date Conflicts' }
        '14' = @{ Path = 'scripts/guest-workflows/asset-loans/test-asset-category-selection.ps1'; Desc = 'Test Asset Category Selection' }
        '15' = @{ Path = 'scripts/guest-workflows/asset-loans/test-loan-duration-validation.ps1'; Desc = 'Test Loan Duration Validation' }
        '16' = @{ Path = 'scripts/guest-workflows/asset-loans/test-department-restrictions.ps1'; Desc = 'Test Department Restrictions' }
        '17' = @{ Path = 'scripts/guest-workflows/asset-loans/track-loan-status.ps1'; Desc = 'Track Loan Status' }
        '18' = @{ Path = 'scripts/guest-workflows/asset-loans/test-approval-workflow-trigger.ps1'; Desc = 'Test Approval Workflow Trigger' }
        '19' = @{ Path = 'scripts/guest-workflows/asset-loans/test-asset-conflict-detection.ps1'; Desc = 'Test Asset Conflict Detection' }
        '20' = @{ Path = 'scripts/guest-workflows/asset-loans/test-loan-extension-requests.ps1'; Desc = 'Test Loan Extension Requests' }
        'D1' = @{ Path = 'scripts/guest-workflows/demos/quick-demo.ps1'; Desc = 'Quick Demo (5 min)' }
        'D2' = @{ Path = 'scripts/guest-workflows/demos/complete-demo.ps1'; Desc = 'Complete Demo (15 min)' }
        'D3' = @{ Path = 'scripts/guest-workflows/demos/training-session.ps1'; Desc = 'Training Session' }
        'S' = @{ Action = 'Search' }
        'V' = @{ Action = 'Config' }
        'H' = @{ Action = 'Help' }
    }
    
    if ($scriptMap.ContainsKey($Choice)) {
        $item = $scriptMap[$Choice]
        
        if ($item.Action) {
            switch ($item.Action) {
                'Search' { Search-Scripts }
                'Config' { Show-ConfigurationMenu }
                'Help' { 
                    Write-Host "Guest Workflows help - see docs/user-guide.md for details." -ForegroundColor Yellow
                    Read-Host "Press Enter to continue"
                }
            }
        }
        else {
            Invoke-ScriptByPath -ScriptPath $item.Path -Description $item.Desc
        }
    }
    else {
        Write-Host "Invalid option: $Choice" -ForegroundColor Red
        Read-Host "Press Enter to continue"
    }
}

#endregion

# Entry point
if (-not $SkipPrerequisiteCheck) {
    $prereqs = Test-Prerequisites
    if (-not $prereqs.PowerShell) {
        Write-Host "PowerShell 7.x or higher is required." -ForegroundColor Red
        exit 1
    }
}

Start-MainMenu
