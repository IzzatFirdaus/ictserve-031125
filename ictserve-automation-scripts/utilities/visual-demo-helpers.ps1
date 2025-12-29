#Requires -Version 7.0
<#
.SYNOPSIS
    Visual demonstration helper utilities for ICTServe automation scripts.

.DESCRIPTION
    This module provides advanced visual demonstration features including
    side-by-side browser comparisons, training session management, and
    presentation utilities.

.NOTES
    Version: 1.0.0
    Author: ICTServe Automation Team
#>

# Import common functions
$commonFunctionsPath = Join-Path $PSScriptRoot "common-functions.ps1"
if (Test-Path $commonFunctionsPath) {
    . $commonFunctionsPath
}

#region Training Session Management

function Start-TrainingSession {
    <#
    .SYNOPSIS
        Initializes a training session with interactive features.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$SessionName,
        
        [Parameter()]
        [string]$Presenter = "ICTServe Trainer",
        
        [Parameter()]
        [string[]]$Workflows = @()
    )
    
    $session = @{
        Name = $SessionName
        Presenter = $Presenter
        StartTime = Get-Date
        Workflows = $Workflows
        CurrentStep = 0
        Screenshots = @()
        Notes = @()
    }
    
    Write-AutomationLog "Training session started: $SessionName" -Level INFO
    
    Show-TrainingBanner -SessionName $SessionName -Presenter $Presenter
    
    return $session
}

function Show-TrainingBanner {
    <#
    .SYNOPSIS
        Displays a training session banner.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$SessionName,
        
        [Parameter()]
        [string]$Presenter = ""
    )
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║                    ICTServe Training Session                  ║" -ForegroundColor Cyan
    Write-Host "╠══════════════════════════════════════════════════════════════╣" -ForegroundColor Cyan
    Write-Host "║  Session: $($SessionName.PadRight(50))║" -ForegroundColor White
    if ($Presenter) {
        Write-Host "║  Presenter: $($Presenter.PadRight(48))║" -ForegroundColor White
    }
    Write-Host "║  Date: $((Get-Date).ToString('yyyy-MM-dd HH:mm').PadRight(53))║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
}

function Add-TrainingNote {
    <#
    .SYNOPSIS
        Adds a note to the current training session.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [hashtable]$Session,
        
        [Parameter(Mandatory = $true)]
        [string]$Note,
        
        [Parameter()]
        [string]$Category = "General"
    )
    
    $Session.Notes += @{
        Timestamp = Get-Date
        Category = $Category
        Note = $Note
    }
    
    Write-AutomationLog "Training note added: $Note" -Level DEBUG
}

function Stop-TrainingSession {
    <#
    .SYNOPSIS
        Ends a training session and generates summary.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [hashtable]$Session
    )
    
    $Session.EndTime = Get-Date
    $Session.Duration = $Session.EndTime - $Session.StartTime
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Green
    Write-Host "║                  Training Session Complete                    ║" -ForegroundColor Green
    Write-Host "╠══════════════════════════════════════════════════════════════╣" -ForegroundColor Green
    Write-Host "║  Duration: $($Session.Duration.ToString('hh\:mm\:ss').PadRight(49))║" -ForegroundColor White
    Write-Host "║  Screenshots: $($Session.Screenshots.Count.ToString().PadRight(46))║" -ForegroundColor White
    Write-Host "║  Notes: $($Session.Notes.Count.ToString().PadRight(52))║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Green
    Write-Host ""
    
    Write-AutomationLog "Training session ended: $($Session.Name)" -Level SUCCESS
    
    return $Session
}

#endregion

#region Side-by-Side Comparison

function Start-SideBySideComparison {
    <#
    .SYNOPSIS
        Starts a side-by-side browser comparison for different user types.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$LeftTitle = "Guest User",
        
        [Parameter()]
        [string]$RightTitle = "Authenticated User",
        
        [Parameter()]
        [int]$WindowWidth = 960,
        
        [Parameter()]
        [int]$WindowHeight = 1080
    )
    
    Write-AutomationLog "Starting side-by-side comparison: $LeftTitle vs $RightTitle" -Level INFO
    
    # Placeholder for launching two browser windows side by side
    $comparison = @{
        LeftTitle = $LeftTitle
        RightTitle = $RightTitle
        LeftWindow = $null
        RightWindow = $null
        StartTime = Get-Date
    }
    
    Write-Host ""
    Write-Host "Side-by-Side Comparison" -ForegroundColor Cyan
    Write-Host "─────────────────────────────────────────" -ForegroundColor Gray
    Write-Host "  Left:  $LeftTitle" -ForegroundColor White
    Write-Host "  Right: $RightTitle" -ForegroundColor White
    Write-Host ""
    
    return $comparison
}

function Sync-SideBySideAction {
    <#
    .SYNOPSIS
        Performs synchronized actions on both browser windows.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [hashtable]$Comparison,
        
        [Parameter(Mandatory = $true)]
        [scriptblock]$LeftAction,
        
        [Parameter(Mandatory = $true)]
        [scriptblock]$RightAction,
        
        [Parameter()]
        [string]$Description = ""
    )
    
    if ($Description) {
        Write-Host "  Action: $Description" -ForegroundColor Yellow
    }
    
    # Execute actions (placeholder)
    Write-AutomationLog "Synchronized action: $Description" -Level DEBUG
}

#endregion

#region Presentation Utilities

function Show-WorkflowStep {
    <#
    .SYNOPSIS
        Displays a workflow step with visual formatting.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [int]$StepNumber,
        
        [Parameter(Mandatory = $true)]
        [string]$Title,
        
        [Parameter()]
        [string]$Description = "",
        
        [Parameter()]
        [string]$Icon = "📌"
    )
    
    Write-Host ""
    Write-Host "$Icon Step $StepNumber`: $Title" -ForegroundColor Cyan
    if ($Description) {
        Write-Host "   $Description" -ForegroundColor Gray
    }
    Write-Host ""
}

function Show-SuccessMessage {
    <#
    .SYNOPSIS
        Displays a success message with visual formatting.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Message
    )
    
    Write-Host ""
    Write-Host "✅ $Message" -ForegroundColor Green
    Write-Host ""
}

function Show-ErrorMessage {
    <#
    .SYNOPSIS
        Displays an error message with visual formatting.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Message
    )
    
    Write-Host ""
    Write-Host "❌ $Message" -ForegroundColor Red
    Write-Host ""
}

function Show-WarningMessage {
    <#
    .SYNOPSIS
        Displays a warning message with visual formatting.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Message
    )
    
    Write-Host ""
    Write-Host "⚠️ $Message" -ForegroundColor Yellow
    Write-Host ""
}

function Show-InfoBox {
    <#
    .SYNOPSIS
        Displays an information box with visual formatting.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Title,
        
        [Parameter(Mandatory = $true)]
        [string[]]$Content
    )
    
    $maxLength = ($Content | Measure-Object -Property Length -Maximum).Maximum
    $boxWidth = [Math]::Max($maxLength + 4, $Title.Length + 4)
    
    Write-Host ""
    Write-Host ("┌" + ("─" * $boxWidth) + "┐") -ForegroundColor Cyan
    Write-Host ("│ " + $Title.PadRight($boxWidth - 2) + " │") -ForegroundColor Cyan
    Write-Host ("├" + ("─" * $boxWidth) + "┤") -ForegroundColor Cyan
    
    foreach ($line in $Content) {
        Write-Host ("│ " + $line.PadRight($boxWidth - 2) + " │") -ForegroundColor White
    }
    
    Write-Host ("└" + ("─" * $boxWidth) + "┘") -ForegroundColor Cyan
    Write-Host ""
}

#endregion

#region Progress Tracking

function Show-ProgressBar {
    <#
    .SYNOPSIS
        Displays a visual progress bar.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [int]$Current,
        
        [Parameter(Mandatory = $true)]
        [int]$Total,
        
        [Parameter()]
        [string]$Label = "Progress",
        
        [Parameter()]
        [int]$Width = 50
    )
    
    $percent = [math]::Round(($Current / $Total) * 100)
    $filled = [math]::Round(($Current / $Total) * $Width)
    $empty = $Width - $filled
    
    $bar = ("█" * $filled) + ("░" * $empty)
    
    Write-Host "`r$Label`: [$bar] $percent% ($Current/$Total)" -NoNewline -ForegroundColor Cyan
    
    if ($Current -eq $Total) {
        Write-Host ""
    }
}

#endregion

#region Advanced Animation Functions

function Show-TypewriterText {
    <#
    .SYNOPSIS
        Displays text with typewriter animation effect.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Text,
        
        [Parameter()]
        [int]$DelayMs = 50,
        
        [Parameter()]
        [ConsoleColor]$Color = 'White'
    )
    
    foreach ($char in $Text.ToCharArray()) {
        Write-Host $char -NoNewline -ForegroundColor $Color
        Start-Sleep -Milliseconds $DelayMs
    }
    Write-Host ""
}

function Show-AnimatedBanner {
    <#
    .SYNOPSIS
        Displays an animated banner with fade-in effect.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Title,
        
        [Parameter()]
        [string]$Subtitle = "",
        
        [Parameter()]
        [ConsoleColor]$Color = 'Cyan'
    )
    
    $width = 64
    $padding = [math]::Max(0, ($width - $Title.Length - 4) / 2)
    
    Write-Host ""
    Write-Host ("╔" + ("═" * $width) + "╗") -ForegroundColor $Color
    Write-Host ("║" + (" " * [math]::Floor($padding)) + "  $Title  " + (" " * [math]::Ceiling($padding)) + "║") -ForegroundColor $Color
    if ($Subtitle) {
        $subPadding = [math]::Max(0, ($width - $Subtitle.Length - 4) / 2)
        Write-Host ("║" + (" " * [math]::Floor($subPadding)) + "  $Subtitle  " + (" " * [math]::Ceiling($subPadding)) + "║") -ForegroundColor Gray
    }
    Write-Host ("╚" + ("═" * $width) + "╝") -ForegroundColor $Color
    Write-Host ""
}

function Show-CountdownTimer {
    <#
    .SYNOPSIS
        Displays a countdown timer before starting an action.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [int]$Seconds = 3,
        
        [Parameter()]
        [string]$Message = "Starting in"
    )
    
    for ($i = $Seconds; $i -gt 0; $i--) {
        Write-Host "`r$Message $i..." -NoNewline -ForegroundColor Yellow
        Start-Sleep -Seconds 1
    }
    Write-Host "`r$Message Go!    " -ForegroundColor Green
    Start-Sleep -Milliseconds 500
}

function Show-SpinnerAnimation {
    <#
    .SYNOPSIS
        Shows a spinner animation while waiting.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$Message = "Processing",
        
        [Parameter()]
        [int]$DurationMs = 2000
    )
    
    $spinChars = @('|', '/', '-', '\')
    $endTime = (Get-Date).AddMilliseconds($DurationMs)
    $i = 0
    
    while ((Get-Date) -lt $endTime) {
        Write-Host "`r$Message $($spinChars[$i % 4])" -NoNewline -ForegroundColor Cyan
        Start-Sleep -Milliseconds 100
        $i++
    }
    Write-Host "`r$Message ✓" -ForegroundColor Green
}

#endregion

#region Demo Configuration Management

function Get-DemoConfigFromFile {
    <#
    .SYNOPSIS
        Loads demo configuration from JSON file.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$ConfigPath = ""
    )
    
    if (-not $ConfigPath) {
        $ConfigPath = Join-Path $PSScriptRoot "..\config\demo-settings.json"
    }
    
    if (Test-Path $ConfigPath) {
        $jsonConfig = Get-Content $ConfigPath -Raw | ConvertFrom-Json
        
        $config = [DemoConfig]::new()
        
        if ($jsonConfig.mode) { $config.Mode = $jsonConfig.mode }
        if ($jsonConfig.browserWindow) { $config.BrowserWindow = $jsonConfig.browserWindow }
        if ($jsonConfig.executionSpeed) { $config.ExecutionSpeed = $jsonConfig.executionSpeed }
        if ($null -ne $jsonConfig.highlightElements) { $config.HighlightElements = $jsonConfig.highlightElements }
        if ($null -ne $jsonConfig.showMouseCursor) { $config.ShowMouseCursor = $jsonConfig.showMouseCursor }
        if ($null -ne $jsonConfig.addAnnotations) { $config.AddAnnotations = $jsonConfig.addAnnotations }
        if ($null -ne $jsonConfig.takeScreenshots) { $config.TakeScreenshots = $jsonConfig.takeScreenshots }
        if ($null -ne $jsonConfig.recordVideo) { $config.RecordVideo = $jsonConfig.recordVideo }
        if ($jsonConfig.pauseAtSteps) { $config.PauseAtSteps = $jsonConfig.pauseAtSteps }
        if ($jsonConfig.annotationDelay) { $config.AnnotationDelay = $jsonConfig.annotationDelay }
        if ($jsonConfig.stepDelay) { $config.StepDelay = $jsonConfig.stepDelay }
        if ($null -ne $jsonConfig.showNetworkActivity) { $config.ShowNetworkActivity = $jsonConfig.showNetworkActivity }
        if ($null -ne $jsonConfig.logUserActions) { $config.LogUserActions = $jsonConfig.logUserActions }
        
        return $config
    }
    else {
        Write-AutomationLog "Demo config file not found: $ConfigPath" -Level WARNING
        return [DemoConfig]::new()
    }
}

function Save-DemoConfigToFile {
    <#
    .SYNOPSIS
        Saves demo configuration to JSON file.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [DemoConfig]$Config,
        
        [Parameter()]
        [string]$ConfigPath = ""
    )
    
    if (-not $ConfigPath) {
        $ConfigPath = Join-Path $PSScriptRoot "..\config\demo-settings.json"
    }
    
    $jsonConfig = @{
        mode = $Config.Mode
        browserWindow = $Config.BrowserWindow
        executionSpeed = $Config.ExecutionSpeed
        highlightElements = $Config.HighlightElements
        showMouseCursor = $Config.ShowMouseCursor
        addAnnotations = $Config.AddAnnotations
        takeScreenshots = $Config.TakeScreenshots
        recordVideo = $Config.RecordVideo
        pauseAtSteps = $Config.PauseAtSteps
        annotationDelay = $Config.AnnotationDelay
        stepDelay = $Config.StepDelay
        showNetworkActivity = $Config.ShowNetworkActivity
        logUserActions = $Config.LogUserActions
    }
    
    $jsonConfig | ConvertTo-Json -Depth 3 | Set-Content $ConfigPath -Encoding UTF8
    
    Write-AutomationLog "Demo config saved to: $ConfigPath" -Level SUCCESS
}

function Show-DemoConfigMenu {
    <#
    .SYNOPSIS
        Interactive menu for configuring demo settings.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [DemoConfig]$CurrentConfig = $null
    )
    
    if (-not $CurrentConfig) {
        $CurrentConfig = Get-DemoConfigFromFile
    }
    
    do {
        Clear-Host
        Write-Host ""
        Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
        Write-Host "║              Demo Configuration Settings                      ║" -ForegroundColor Cyan
        Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "  Current Settings:" -ForegroundColor Yellow
        Write-Host "  ─────────────────────────────────────────────────────────────" -ForegroundColor Gray
        Write-Host "  1. Mode:              $($CurrentConfig.Mode)" -ForegroundColor White
        Write-Host "  2. Browser Window:    $($CurrentConfig.BrowserWindow)" -ForegroundColor White
        Write-Host "  3. Execution Speed:   $($CurrentConfig.ExecutionSpeed)" -ForegroundColor White
        Write-Host "  4. Highlight Elements: $($CurrentConfig.HighlightElements)" -ForegroundColor White
        Write-Host "  5. Show Mouse Cursor: $($CurrentConfig.ShowMouseCursor)" -ForegroundColor White
        Write-Host "  6. Add Annotations:   $($CurrentConfig.AddAnnotations)" -ForegroundColor White
        Write-Host "  7. Take Screenshots:  $($CurrentConfig.TakeScreenshots)" -ForegroundColor White
        Write-Host "  8. Record Video:      $($CurrentConfig.RecordVideo)" -ForegroundColor White
        Write-Host "  9. Network Activity:  $($CurrentConfig.ShowNetworkActivity)" -ForegroundColor White
        Write-Host "  ─────────────────────────────────────────────────────────────" -ForegroundColor Gray
        Write-Host "  S. Save Configuration" -ForegroundColor Green
        Write-Host "  R. Reset to Defaults" -ForegroundColor Yellow
        Write-Host "  Q. Exit" -ForegroundColor Red
        Write-Host ""
        
        $choice = Read-Host "  Select option"
        
        switch ($choice) {
            '1' {
                Write-Host "  Select Mode: (1) Headless, (2) Visual, (3) Demo, (4) Interactive, (5) Recording"
                $modeChoice = Read-Host "  Choice"
                $CurrentConfig.Mode = @('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')[[int]$modeChoice - 1]
            }
            '2' {
                Write-Host "  Select Window: (1) Maximized, (2) Windowed, (3) Fullscreen"
                $windowChoice = Read-Host "  Choice"
                $CurrentConfig.BrowserWindow = @('Maximized', 'Windowed', 'Fullscreen')[[int]$windowChoice - 1]
            }
            '3' {
                Write-Host "  Select Speed: (1) Fast, (2) Normal, (3) Demo, (4) Slow"
                $speedChoice = Read-Host "  Choice"
                $CurrentConfig.ExecutionSpeed = @('Fast', 'Normal', 'Demo', 'Slow')[[int]$speedChoice - 1]
            }
            '4' { $CurrentConfig.HighlightElements = -not $CurrentConfig.HighlightElements }
            '5' { $CurrentConfig.ShowMouseCursor = -not $CurrentConfig.ShowMouseCursor }
            '6' { $CurrentConfig.AddAnnotations = -not $CurrentConfig.AddAnnotations }
            '7' { $CurrentConfig.TakeScreenshots = -not $CurrentConfig.TakeScreenshots }
            '8' { $CurrentConfig.RecordVideo = -not $CurrentConfig.RecordVideo }
            '9' { $CurrentConfig.ShowNetworkActivity = -not $CurrentConfig.ShowNetworkActivity }
            'S' {
                Save-DemoConfigToFile -Config $CurrentConfig
                Write-Host "  Configuration saved!" -ForegroundColor Green
                Start-Sleep -Seconds 1
            }
            'R' {
                $CurrentConfig = Get-DefaultDemoConfig -Mode 'Visual'
                Write-Host "  Configuration reset to defaults!" -ForegroundColor Yellow
                Start-Sleep -Seconds 1
            }
        }
    } while ($choice -ne 'Q' -and $choice -ne 'q')
    
    return $CurrentConfig
}

#endregion

#region Workflow Execution Helpers

function Invoke-WorkflowWithDemo {
    <#
    .SYNOPSIS
        Executes a workflow script with demo features enabled.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$ScriptPath,
        
        [Parameter()]
        [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
        [string]$Mode = 'Visual',
        
        [Parameter()]
        [hashtable]$Parameters = @{}
    )
    
    if (-not (Test-Path $ScriptPath)) {
        Write-AutomationLog "Script not found: $ScriptPath" -Level ERROR
        return $false
    }
    
    $demoConfig = Get-DefaultDemoConfig -Mode $Mode
    
    Write-AutomationLog "Executing workflow: $ScriptPath (Mode: $Mode)" -Level INFO
    
    try {
        $Parameters['DemoConfig'] = $demoConfig
        & $ScriptPath @Parameters
        
        Write-AutomationLog "Workflow completed successfully" -Level SUCCESS
        return $true
    }
    catch {
        Write-AutomationLog "Workflow failed: $($_.Exception.Message)" -Level ERROR
        return $false
    }
}

function Start-BatchDemoExecution {
    <#
    .SYNOPSIS
        Executes multiple workflows in sequence with demo features.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string[]]$ScriptPaths,
        
        [Parameter()]
        [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
        [string]$Mode = 'Visual',
        
        [Parameter()]
        [switch]$StopOnError
    )
    
    $results = @{
        Total = $ScriptPaths.Count
        Passed = 0
        Failed = 0
        Skipped = 0
        Details = @()
    }
    
    Show-AnimatedBanner -Title "Batch Demo Execution" -Subtitle "$($ScriptPaths.Count) workflows to execute"
    
    for ($i = 0; $i -lt $ScriptPaths.Count; $i++) {
        $scriptPath = $ScriptPaths[$i]
        $scriptName = Split-Path $scriptPath -Leaf
        
        Show-ProgressBar -Current ($i + 1) -Total $ScriptPaths.Count -Label "Executing"
        
        Write-Host ""
        Write-Host "  [$($i + 1)/$($ScriptPaths.Count)] $scriptName" -ForegroundColor Cyan
        
        $success = Invoke-WorkflowWithDemo -ScriptPath $scriptPath -Mode $Mode
        
        if ($success) {
            $results.Passed++
            $results.Details += @{ Script = $scriptName; Status = 'Passed' }
            Write-Host "    ✓ Passed" -ForegroundColor Green
        }
        else {
            $results.Failed++
            $results.Details += @{ Script = $scriptName; Status = 'Failed' }
            Write-Host "    ✗ Failed" -ForegroundColor Red
            
            if ($StopOnError) {
                Write-Host "  Stopping due to error..." -ForegroundColor Yellow
                break
            }
        }
    }
    
    # Show summary
    Write-Host ""
    Show-InfoBox -Title "Batch Execution Summary" -Content @(
        "Total:   $($results.Total)",
        "Passed:  $($results.Passed)",
        "Failed:  $($results.Failed)",
        "Skipped: $($results.Skipped)"
    )
    
    return $results
}

#endregion

# Export functions only when loaded as a module.
if ($MyInvocation.MyCommand.Module) {
    Export-ModuleMember -Function @(
        'Start-TrainingSession',
        'Show-TrainingBanner',
        'Add-TrainingNote',
        'Stop-TrainingSession',
        'Start-SideBySideComparison',
        'Sync-SideBySideAction',
        'Show-WorkflowStep',
        'Show-SuccessMessage',
        'Show-ErrorMessage',
        'Show-WarningMessage',
        'Show-InfoBox',
        'Show-ProgressBar',
        'Show-TypewriterText',
        'Show-AnimatedBanner',
        'Show-CountdownTimer',
        'Show-SpinnerAnimation',
        'Get-DemoConfigFromFile',
        'Save-DemoConfigToFile',
        'Show-DemoConfigMenu',
        'Invoke-WorkflowWithDemo',
        'Start-BatchDemoExecution'
    )
}
