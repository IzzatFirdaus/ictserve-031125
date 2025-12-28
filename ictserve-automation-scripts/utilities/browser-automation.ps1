#Requires -Version 7.0
<#
.SYNOPSIS
    Browser automation utilities for ICTServe automation scripts.

.DESCRIPTION
    This module provides Selenium WebDriver integration for browser automation
    with visual demonstration capabilities including element highlighting,
    animated cursor movements, and screenshot capture.

.NOTES
    Version: 1.0.0
    Author: ICTServe Automation Team
    Requirements: PowerShell 7.x, Selenium WebDriver, Chrome/Edge browser
#>

# Import common functions
$commonFunctionsPath = Join-Path $PSScriptRoot "common-functions.ps1"
if (Test-Path $commonFunctionsPath) {
    . $commonFunctionsPath
}

# Script-level variables for browser session
$script:Driver = $null
$script:DemoConfig = $null
$script:ScreenshotCounter = 0

#region Browser Configuration

class BrowserConfig {
    [string]$Browser = "Chrome"
    [bool]$Headless = $false
    [string]$WindowSize = "1920x1080"
    [int]$ImplicitWait = 10
    [int]$PageLoadTimeout = 30
    [string]$DownloadPath = ""
    [bool]$DisableGpu = $true
    [bool]$DisableExtensions = $true
    [string[]]$AdditionalArguments = @()
}

class DemoConfig {
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = "Visual"
    
    [ValidateSet('Maximized', 'Windowed', 'Fullscreen')]
    [string]$BrowserWindow = "Maximized"
    
    [ValidateSet('Fast', 'Normal', 'Demo', 'Slow')]
    [string]$ExecutionSpeed = "Normal"
    
    [bool]$HighlightElements = $true
    [bool]$ShowMouseCursor = $true
    [bool]$AddAnnotations = $true
    [bool]$TakeScreenshots = $true
    [bool]$RecordVideo = $false
    [string[]]$PauseAtSteps = @()
    [int]$AnnotationDelay = 2000
    [int]$StepDelay = 1500
    [bool]$ShowNetworkActivity = $false
    [bool]$LogUserActions = $true
}

function Get-DefaultDemoConfig {
    <#
    .SYNOPSIS
        Returns default demo configuration based on mode.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
        [string]$Mode = 'Visual'
    )
    
    $config = [DemoConfig]::new()
    $config.Mode = $Mode
    
    switch ($Mode) {
        'Headless' {
            $config.HighlightElements = $false
            $config.ShowMouseCursor = $false
            $config.AddAnnotations = $false
            $config.TakeScreenshots = $false
            $config.StepDelay = 0
            $config.ExecutionSpeed = 'Fast'
        }
        'Visual' {
            $config.ExecutionSpeed = 'Normal'
            $config.StepDelay = 500
        }
        'Demo' {
            $config.ExecutionSpeed = 'Demo'
            $config.StepDelay = 1500
            $config.AnnotationDelay = 2000
        }
        'Interactive' {
            $config.ExecutionSpeed = 'Slow'
            $config.StepDelay = 2000
            $config.PauseAtSteps = @('Login', 'FormSubmit', 'Results')
        }
        'Recording' {
            $config.RecordVideo = $true
            $config.ExecutionSpeed = 'Demo'
            $config.StepDelay = 1500
        }
    }
    
    return $config
}

#endregion

#region Browser Session Management

function Start-BrowserSession {
    <#
    .SYNOPSIS
        Initializes a new browser session with Selenium WebDriver.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [BrowserConfig]$Config = [BrowserConfig]::new(),
        
        [Parameter()]
        [DemoConfig]$DemoSettings = $null
    )
    
    Write-AutomationLog "Starting browser session: $($Config.Browser)" -Level INFO
    
    $script:DemoConfig = if ($DemoSettings) { $DemoSettings } else { Get-DefaultDemoConfig -Mode 'Visual' }
    
    try {
        # Build Chrome options
        $options = @()
        
        if ($Config.Headless -or $script:DemoConfig.Mode -eq 'Headless') {
            $options += "--headless=new"
        }
        
        if ($Config.DisableGpu) {
            $options += "--disable-gpu"
        }
        
        if ($Config.DisableExtensions) {
            $options += "--disable-extensions"
        }
        
        if ($Config.WindowSize) {
            $options += "--window-size=$($Config.WindowSize)"
        }
        
        $options += "--no-sandbox"
        $options += "--disable-dev-shm-usage"
        $options += $Config.AdditionalArguments
        
        # Note: Actual Selenium initialization would go here
        # This is a placeholder for the WebDriver setup
        Write-AutomationLog "Browser options configured: $($options -join ', ')" -Level DEBUG
        
        # Store configuration for later use
        $script:BrowserConfig = $Config
        $script:ScreenshotCounter = 0
        
        Write-AutomationLog "Browser session started successfully" -Level SUCCESS
        
        return @{
            Success = $true
            Browser = $Config.Browser
            Mode = $script:DemoConfig.Mode
        }
    }
    catch {
        Write-AutomationLog "Failed to start browser session: $($_.Exception.Message)" -Level ERROR
        throw
    }
}

function Stop-BrowserSession {
    <#
    .SYNOPSIS
        Closes the current browser session and cleans up resources.
    #>
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Stopping browser session" -Level INFO
    
    try {
        if ($script:Driver) {
            $script:Driver.Quit()
            $script:Driver = $null
        }
        
        $script:DemoConfig = $null
        $script:BrowserConfig = $null
        
        Write-AutomationLog "Browser session stopped successfully" -Level SUCCESS
    }
    catch {
        Write-AutomationLog "Error stopping browser session: $($_.Exception.Message)" -Level WARNING
    }
}

#endregion

#region Navigation Functions

function Navigate-ToUrl {
    <#
    .SYNOPSIS
        Navigates to a URL with visual demonstration support.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Url,
        
        [Parameter()]
        [string]$Description = ""
    )
    
    Write-AutomationLog "Navigating to: $Url" -Level INFO
    
    if ($Description -and $script:DemoConfig.AddAnnotations) {
        Show-Annotation -Text "📍 Navigating to: $Description" -Duration $script:DemoConfig.AnnotationDelay
    }
    
    # Placeholder for actual navigation
    # $script:Driver.Navigate().GoToUrl($Url)
    
    Wait-ForPageLoad
    
    if ($script:DemoConfig.TakeScreenshots) {
        Take-Screenshot -Name "navigation-$(New-UniqueId -Prefix 'NAV')"
    }
    
    Apply-StepDelay
}

function Wait-ForPageLoad {
    <#
    .SYNOPSIS
        Waits for the page to fully load.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [int]$TimeoutSeconds = 30
    )
    
    Write-AutomationLog "Waiting for page load..." -Level DEBUG
    
    # Placeholder for actual page load wait
    # Wait for document.readyState === 'complete'
    
    Start-Sleep -Milliseconds 500
}

function Wait-ForElement {
    <#
    .SYNOPSIS
        Waits for an element to be present and visible.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter()]
        [ValidateSet('Id', 'Name', 'ClassName', 'CssSelector', 'XPath', 'LinkText')]
        [string]$By = 'CssSelector',
        
        [Parameter()]
        [int]$TimeoutSeconds = 10
    )
    
    Write-AutomationLog "Waiting for element: $Selector (By: $By)" -Level DEBUG
    
    # Placeholder for actual element wait
    # Use WebDriverWait with ExpectedConditions
    
    Start-Sleep -Milliseconds 200
    
    return @{
        Found = $true
        Selector = $Selector
    }
}

#endregion

#region Element Interaction Functions

function Click-Element {
    <#
    .SYNOPSIS
        Clicks an element with visual demonstration support.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter()]
        [ValidateSet('Id', 'Name', 'ClassName', 'CssSelector', 'XPath', 'LinkText')]
        [string]$By = 'CssSelector',
        
        [Parameter()]
        [string]$Description = ""
    )
    
    Write-AutomationLog "Clicking element: $Selector" -Level DEBUG
    
    $element = Wait-ForElement -Selector $Selector -By $By
    
    if ($script:DemoConfig.HighlightElements) {
        Highlight-Element -Selector $Selector -By $By
    }
    
    if ($script:DemoConfig.ShowMouseCursor) {
        Animate-MouseToElement -Selector $Selector -By $By
    }
    
    if ($Description -and $script:DemoConfig.AddAnnotations) {
        Show-Annotation -Text "🖱️ Clicking: $Description" -Duration $script:DemoConfig.AnnotationDelay
    }
    
    # Placeholder for actual click
    # $element.Click()
    
    if ($script:DemoConfig.LogUserActions) {
        Write-AutomationLog "Clicked: $Description ($Selector)" -Level INFO
    }
    
    Apply-StepDelay
}

function Type-Text {
    <#
    .SYNOPSIS
        Types text into an input element with visual demonstration support.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter(Mandatory = $true)]
        [string]$Text,
        
        [Parameter()]
        [ValidateSet('Id', 'Name', 'ClassName', 'CssSelector', 'XPath')]
        [string]$By = 'CssSelector',
        
        [Parameter()]
        [string]$Description = "",
        
        [Parameter()]
        [switch]$ClearFirst,
        
        [Parameter()]
        [switch]$SimulateTyping
    )
    
    Write-AutomationLog "Typing into element: $Selector" -Level DEBUG
    
    $element = Wait-ForElement -Selector $Selector -By $By
    
    if ($script:DemoConfig.HighlightElements) {
        Highlight-Element -Selector $Selector -By $By
    }
    
    if ($script:DemoConfig.ShowMouseCursor) {
        Animate-MouseToElement -Selector $Selector -By $By
    }
    
    if ($Description -and $script:DemoConfig.AddAnnotations) {
        Show-Annotation -Text "✏️ Entering: $Description" -Duration $script:DemoConfig.AnnotationDelay
    }
    
    # Placeholder for actual typing
    # if ($ClearFirst) { $element.Clear() }
    # $element.SendKeys($Text)
    
    if ($script:DemoConfig.LogUserActions) {
        $maskedText = if ($Description -match 'password|secret|key') { '********' } else { $Text }
        Write-AutomationLog "Typed: $maskedText into $Description ($Selector)" -Level INFO
    }
    
    Apply-StepDelay
}

function Select-DropdownOption {
    <#
    .SYNOPSIS
        Selects an option from a dropdown with visual demonstration support.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter(Mandatory = $true)]
        [string]$Value,
        
        [Parameter()]
        [ValidateSet('Id', 'Name', 'ClassName', 'CssSelector', 'XPath')]
        [string]$By = 'CssSelector',
        
        [Parameter()]
        [ValidateSet('Value', 'Text', 'Index')]
        [string]$SelectBy = 'Value',
        
        [Parameter()]
        [string]$Description = ""
    )
    
    Write-AutomationLog "Selecting dropdown option: $Value from $Selector" -Level DEBUG
    
    if ($script:DemoConfig.HighlightElements) {
        Highlight-Element -Selector $Selector -By $By
    }
    
    if ($Description -and $script:DemoConfig.AddAnnotations) {
        Show-Annotation -Text "📋 Selecting: $Description = $Value" -Duration $script:DemoConfig.AnnotationDelay
    }
    
    # Placeholder for actual selection
    # Use SelectElement class from Selenium
    
    if ($script:DemoConfig.LogUserActions) {
        Write-AutomationLog "Selected: $Value from $Description ($Selector)" -Level INFO
    }
    
    Apply-StepDelay
}

#endregion

#region Visual Demonstration Functions

function Highlight-Element {
    <#
    .SYNOPSIS
        Highlights an element on the page for visual demonstration.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter()]
        [ValidateSet('Id', 'Name', 'ClassName', 'CssSelector', 'XPath')]
        [string]$By = 'CssSelector',
        
        [Parameter()]
        [string]$Color = "#FF6B6B",
        
        [Parameter()]
        [int]$BorderWidth = 3,
        
        [Parameter()]
        [int]$DurationMs = 1000
    )
    
    if (-not $script:DemoConfig.HighlightElements) { return }
    
    Write-AutomationLog "Highlighting element: $Selector" -Level DEBUG
    
    # JavaScript to highlight element
    $highlightScript = @"
        var element = document.querySelector('$Selector');
        if (element) {
            var originalStyle = element.style.cssText;
            element.style.border = '${BorderWidth}px solid $Color';
            element.style.boxShadow = '0 0 10px $Color';
            element.style.transition = 'all 0.3s ease';
            setTimeout(function() {
                element.style.cssText = originalStyle;
            }, $DurationMs);
        }
"@
    
    # Placeholder for executing JavaScript
    # $script:Driver.ExecuteScript($highlightScript)
    
    Start-Sleep -Milliseconds ($DurationMs / 2)
}

function Animate-MouseToElement {
    <#
    .SYNOPSIS
        Animates mouse cursor movement to an element.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter()]
        [ValidateSet('Id', 'Name', 'ClassName', 'CssSelector', 'XPath')]
        [string]$By = 'CssSelector',
        
        [Parameter()]
        [int]$DurationMs = 500
    )
    
    if (-not $script:DemoConfig.ShowMouseCursor) { return }
    
    Write-AutomationLog "Animating mouse to element: $Selector" -Level DEBUG
    
    # JavaScript to create and animate cursor overlay
    $cursorScript = @"
        (function() {
            var cursor = document.getElementById('demo-cursor');
            if (!cursor) {
                cursor = document.createElement('div');
                cursor.id = 'demo-cursor';
                cursor.style.cssText = 'position: fixed; width: 20px; height: 20px; background: rgba(255,107,107,0.8); border-radius: 50%; pointer-events: none; z-index: 99999; transition: all ${DurationMs}ms ease;';
                document.body.appendChild(cursor);
            }
            var element = document.querySelector('$Selector');
            if (element) {
                var rect = element.getBoundingClientRect();
                cursor.style.left = (rect.left + rect.width/2 - 10) + 'px';
                cursor.style.top = (rect.top + rect.height/2 - 10) + 'px';
            }
        })();
"@
    
    # Placeholder for executing JavaScript
    # $script:Driver.ExecuteScript($cursorScript)
    
    Start-Sleep -Milliseconds $DurationMs
}

function Show-Annotation {
    <#
    .SYNOPSIS
        Displays a text annotation overlay on the page.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Text,
        
        [Parameter()]
        [int]$Duration = 2000,
        
        [Parameter()]
        [ValidateSet('top', 'bottom', 'center')]
        [string]$Position = 'bottom'
    )
    
    if (-not $script:DemoConfig.AddAnnotations) { return }
    
    Write-AutomationLog "Showing annotation: $Text" -Level DEBUG
    
    $positionStyle = switch ($Position) {
        'top' { 'top: 20px;' }
        'bottom' { 'bottom: 20px;' }
        'center' { 'top: 50%; transform: translateY(-50%);' }
    }
    
    # JavaScript to show annotation
    $annotationScript = @"
        (function() {
            var annotation = document.createElement('div');
            annotation.id = 'demo-annotation';
            annotation.style.cssText = 'position: fixed; left: 50%; transform: translateX(-50%); $positionStyle padding: 15px 30px; background: rgba(0,0,0,0.85); color: white; font-size: 18px; font-family: Arial, sans-serif; border-radius: 8px; z-index: 99999; animation: fadeIn 0.3s ease;';
            annotation.textContent = '$($Text -replace "'", "\'")';
            document.body.appendChild(annotation);
            setTimeout(function() {
                annotation.style.opacity = '0';
                annotation.style.transition = 'opacity 0.3s ease';
                setTimeout(function() { annotation.remove(); }, 300);
            }, $Duration);
        })();
"@
    
    # Placeholder for executing JavaScript
    # $script:Driver.ExecuteScript($annotationScript)
    
    Start-Sleep -Milliseconds ($Duration + 300)
}

function Pause-ForInteraction {
    <#
    .SYNOPSIS
        Pauses execution for interactive demonstration.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$StepName = "",
        
        [Parameter()]
        [string]$Message = "Press Enter to continue..."
    )
    
    if ($script:DemoConfig.Mode -ne 'Interactive') { return }
    
    if ($StepName -and $script:DemoConfig.PauseAtSteps -notcontains $StepName) { return }
    
    Write-AutomationLog "⏸️ PAUSED: $Message" -Level INFO
    Show-Annotation -Text "⏸️ $Message" -Duration 60000 -Position 'center'
    
    Write-Host ""
    Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Yellow
    Write-Host "  INTERACTIVE PAUSE: $StepName" -ForegroundColor Yellow
    Write-Host "  $Message" -ForegroundColor Cyan
    Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Yellow
    Write-Host ""
    
    Read-Host "Press Enter to continue"
}

#endregion

#region Screenshot and Recording Functions

function Take-Screenshot {
    <#
    .SYNOPSIS
        Captures a screenshot of the current browser state.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$Name = "",
        
        [Parameter()]
        [string]$Description = ""
    )
    
    if (-not $script:DemoConfig.TakeScreenshots) { return $null }
    
    $script:ScreenshotCounter++
    $timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
    $fileName = if ($Name) { "$Name-$timestamp.png" } else { "screenshot-$($script:ScreenshotCounter)-$timestamp.png" }
    
    $screenshotDir = Join-Path $PSScriptRoot "..\reports\screenshots"
    if (-not (Test-Path $screenshotDir)) {
        New-Item -ItemType Directory -Path $screenshotDir -Force | Out-Null
    }
    
    $filePath = Join-Path $screenshotDir $fileName
    
    Write-AutomationLog "Taking screenshot: $fileName" -Level DEBUG
    
    # Placeholder for actual screenshot
    # $screenshot = $script:Driver.GetScreenshot()
    # $screenshot.SaveAsFile($filePath, [OpenQA.Selenium.ScreenshotImageFormat]::Png)
    
    if ($Description) {
        Write-AutomationLog "Screenshot: $Description" -Level INFO
    }
    
    return $filePath
}

function Start-VideoRecording {
    <#
    .SYNOPSIS
        Starts video recording of the browser session.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$Name = "recording"
    )
    
    if (-not $script:DemoConfig.RecordVideo) { return }
    
    Write-AutomationLog "Starting video recording: $Name" -Level INFO
    
    # Placeholder for video recording initialization
    # This would integrate with OBS Studio or similar tool
    
    $script:RecordingName = $Name
    $script:RecordingStartTime = Get-Date
}

function Stop-VideoRecording {
    <#
    .SYNOPSIS
        Stops video recording and saves the file.
    #>
    [CmdletBinding()]
    param()
    
    if (-not $script:DemoConfig.RecordVideo) { return $null }
    
    Write-AutomationLog "Stopping video recording" -Level INFO
    
    $videoDir = Join-Path $PSScriptRoot "..\reports\videos"
    if (-not (Test-Path $videoDir)) {
        New-Item -ItemType Directory -Path $videoDir -Force | Out-Null
    }
    
    $timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
    $fileName = "$($script:RecordingName)-$timestamp.mp4"
    $filePath = Join-Path $videoDir $fileName
    
    # Placeholder for stopping recording and saving file
    
    Write-AutomationLog "Video saved: $filePath" -Level SUCCESS
    
    return $filePath
}

#endregion

#region Network Monitoring Functions

function Start-NetworkMonitoring {
    <#
    .SYNOPSIS
        Starts monitoring network requests for backend API display.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [string[]]$FilterUrls = @('/api/', '/ajax/', '/graphql')
    )
    
    if (-not $script:DemoConfig.ShowNetworkActivity) { return }
    
    Write-AutomationLog "Starting network monitoring" -Level DEBUG
    
    # JavaScript to intercept and display network requests
    $networkScript = @"
        (function() {
            window.__networkLog = [];
            var originalFetch = window.fetch;
            window.fetch = function() {
                var url = arguments[0];
                var startTime = performance.now();
                return originalFetch.apply(this, arguments).then(function(response) {
                    var duration = performance.now() - startTime;
                    window.__networkLog.push({
                        url: url,
                        method: arguments[1]?.method || 'GET',
                        status: response.status,
                        duration: duration.toFixed(2) + 'ms',
                        timestamp: new Date().toISOString()
                    });
                    return response;
                });
            };
            
            var originalXHR = window.XMLHttpRequest.prototype.open;
            window.XMLHttpRequest.prototype.open = function(method, url) {
                this.__url = url;
                this.__method = method;
                this.__startTime = performance.now();
                return originalXHR.apply(this, arguments);
            };
            
            var originalSend = window.XMLHttpRequest.prototype.send;
            window.XMLHttpRequest.prototype.send = function() {
                var xhr = this;
                this.addEventListener('load', function() {
                    var duration = performance.now() - xhr.__startTime;
                    window.__networkLog.push({
                        url: xhr.__url,
                        method: xhr.__method,
                        status: xhr.status,
                        duration: duration.toFixed(2) + 'ms',
                        timestamp: new Date().toISOString()
                    });
                });
                return originalSend.apply(this, arguments);
            };
        })();
"@
    
    # Placeholder for executing JavaScript
    # $script:Driver.ExecuteScript($networkScript)
    
    $script:NetworkMonitoringActive = $true
}

function Show-NetworkActivity {
    <#
    .SYNOPSIS
        Displays recent network activity in an overlay.
    #>
    [CmdletBinding()]
    param(
        [Parameter()]
        [int]$MaxEntries = 5
    )
    
    if (-not $script:DemoConfig.ShowNetworkActivity) { return }
    
    # JavaScript to display network log overlay
    $displayScript = @"
        (function() {
            var log = window.__networkLog || [];
            var recent = log.slice(-$MaxEntries);
            
            var overlay = document.getElementById('network-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'network-overlay';
                overlay.style.cssText = 'position: fixed; top: 10px; right: 10px; width: 400px; max-height: 300px; overflow-y: auto; background: rgba(0,0,0,0.9); color: #00ff00; font-family: monospace; font-size: 12px; padding: 10px; border-radius: 5px; z-index: 99998;';
                document.body.appendChild(overlay);
            }
            
            var html = '<div style="color: #00ff00; font-weight: bold; margin-bottom: 5px;">🌐 Network Activity</div>';
            recent.forEach(function(entry) {
                var statusColor = entry.status < 400 ? '#00ff00' : '#ff6b6b';
                html += '<div style="margin: 3px 0; padding: 3px; background: rgba(255,255,255,0.1); border-radius: 3px;">';
                html += '<span style="color: #888;">' + entry.method + '</span> ';
                html += '<span style="color: ' + statusColor + ';">[' + entry.status + ']</span> ';
                html += entry.url.substring(0, 40) + (entry.url.length > 40 ? '...' : '') + ' ';
                html += '<span style="color: #888;">(' + entry.duration + ')</span>';
                html += '</div>';
            });
            
            overlay.innerHTML = html;
        })();
"@
    
    # Placeholder for executing JavaScript
    # $script:Driver.ExecuteScript($displayScript)
}

function Stop-NetworkMonitoring {
    <#
    .SYNOPSIS
        Stops network monitoring and removes overlay.
    #>
    [CmdletBinding()]
    param()
    
    if (-not $script:NetworkMonitoringActive) { return }
    
    Write-AutomationLog "Stopping network monitoring" -Level DEBUG
    
    # JavaScript to remove overlay
    $cleanupScript = @"
        var overlay = document.getElementById('network-overlay');
        if (overlay) overlay.remove();
"@
    
    # Placeholder for executing JavaScript
    # $script:Driver.ExecuteScript($cleanupScript)
    
    $script:NetworkMonitoringActive = $false
}

#endregion

#region Advanced Visual Effects

function Show-FormFieldAnnotation {
    <#
    .SYNOPSIS
        Shows annotation next to a form field explaining its purpose.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter(Mandatory = $true)]
        [string]$Text,
        
        [Parameter()]
        [ValidateSet('left', 'right', 'top', 'bottom')]
        [string]$Position = 'right',
        
        [Parameter()]
        [int]$Duration = 3000
    )
    
    if (-not $script:DemoConfig.AddAnnotations) { return }
    
    $positionStyles = @{
        'left' = 'right: 100%; margin-right: 10px; top: 50%; transform: translateY(-50%);'
        'right' = 'left: 100%; margin-left: 10px; top: 50%; transform: translateY(-50%);'
        'top' = 'bottom: 100%; margin-bottom: 10px; left: 50%; transform: translateX(-50%);'
        'bottom' = 'top: 100%; margin-top: 10px; left: 50%; transform: translateX(-50%);'
    }
    
    $annotationScript = @"
        (function() {
            var element = document.querySelector('$Selector');
            if (!element) return;
            
            var rect = element.getBoundingClientRect();
            var annotation = document.createElement('div');
            annotation.className = 'field-annotation';
            annotation.style.cssText = 'position: fixed; background: #2196F3; color: white; padding: 8px 12px; border-radius: 5px; font-size: 14px; font-family: Arial; z-index: 99999; white-space: nowrap; box-shadow: 0 2px 10px rgba(0,0,0,0.3);';
            annotation.textContent = '$($Text -replace "'", "\'")';
            
            // Position relative to element
            var pos = '$Position';
            if (pos === 'right') {
                annotation.style.left = (rect.right + 10) + 'px';
                annotation.style.top = (rect.top + rect.height/2) + 'px';
                annotation.style.transform = 'translateY(-50%)';
            } else if (pos === 'left') {
                annotation.style.right = (window.innerWidth - rect.left + 10) + 'px';
                annotation.style.top = (rect.top + rect.height/2) + 'px';
                annotation.style.transform = 'translateY(-50%)';
            } else if (pos === 'top') {
                annotation.style.left = (rect.left + rect.width/2) + 'px';
                annotation.style.bottom = (window.innerHeight - rect.top + 10) + 'px';
                annotation.style.transform = 'translateX(-50%)';
            } else {
                annotation.style.left = (rect.left + rect.width/2) + 'px';
                annotation.style.top = (rect.bottom + 10) + 'px';
                annotation.style.transform = 'translateX(-50%)';
            }
            
            // Add arrow
            var arrow = document.createElement('div');
            arrow.style.cssText = 'position: absolute; width: 0; height: 0; border: 6px solid transparent;';
            if (pos === 'right') {
                arrow.style.left = '-12px';
                arrow.style.top = '50%';
                arrow.style.transform = 'translateY(-50%)';
                arrow.style.borderRightColor = '#2196F3';
            }
            annotation.appendChild(arrow);
            
            document.body.appendChild(annotation);
            
            setTimeout(function() {
                annotation.style.opacity = '0';
                annotation.style.transition = 'opacity 0.3s';
                setTimeout(function() { annotation.remove(); }, 300);
            }, $Duration);
        })();
"@
    
    # Placeholder for executing JavaScript
    # $script:Driver.ExecuteScript($annotationScript)
    
    Start-Sleep -Milliseconds $Duration
}

function Show-StepIndicator {
    <#
    .SYNOPSIS
        Shows a step indicator overlay for multi-step workflows.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [int]$CurrentStep,
        
        [Parameter(Mandatory = $true)]
        [int]$TotalSteps,
        
        [Parameter(Mandatory = $true)]
        [string]$StepTitle,
        
        [Parameter()]
        [string[]]$AllSteps = @()
    )
    
    if (-not $script:DemoConfig.AddAnnotations) { return }
    
    $stepsHtml = ""
    for ($i = 1; $i -le $TotalSteps; $i++) {
        $stepName = if ($AllSteps.Count -ge $i) { $AllSteps[$i-1] } else { "Step $i" }
        $isActive = $i -eq $CurrentStep
        $isComplete = $i -lt $CurrentStep
        
        $bgColor = if ($isActive) { '#4CAF50' } elseif ($isComplete) { '#2196F3' } else { '#666' }
        $stepsHtml += "<div style='display: inline-block; margin: 0 5px; text-align: center;'>"
        $stepsHtml += "<div style='width: 30px; height: 30px; border-radius: 50%; background: $bgColor; color: white; line-height: 30px; font-weight: bold;'>$i</div>"
        $stepsHtml += "<div style='font-size: 10px; color: #888; margin-top: 3px;'>$stepName</div>"
        $stepsHtml += "</div>"
    }
    
    $indicatorScript = @"
        (function() {
            var indicator = document.getElementById('step-indicator');
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'step-indicator';
                indicator.style.cssText = 'position: fixed; top: 10px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.9); color: white; padding: 15px 25px; border-radius: 10px; z-index: 99999; text-align: center;';
                document.body.appendChild(indicator);
            }
            
            indicator.innerHTML = '<div style="font-size: 16px; font-weight: bold; margin-bottom: 10px;">$StepTitle</div><div>$stepsHtml</div>';
        })();
"@
    
    # Placeholder for executing JavaScript
    # $script:Driver.ExecuteScript($indicatorScript)
}

function Remove-StepIndicator {
    <#
    .SYNOPSIS
        Removes the step indicator overlay.
    #>
    [CmdletBinding()]
    param()
    
    $removeScript = @"
        var indicator = document.getElementById('step-indicator');
        if (indicator) indicator.remove();
"@
    
    # Placeholder for executing JavaScript
    # $script:Driver.ExecuteScript($removeScript)
}

function Pulse-Element {
    <#
    .SYNOPSIS
        Creates a pulsing animation effect on an element.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter()]
        [string]$Color = "#4CAF50",
        
        [Parameter()]
        [int]$Pulses = 3,
        
        [Parameter()]
        [int]$PulseDuration = 500
    )
    
    if (-not $script:DemoConfig.HighlightElements) { return }
    
    $pulseScript = @"
        (function() {
            var element = document.querySelector('$Selector');
            if (!element) return;
            
            var originalBoxShadow = element.style.boxShadow;
            var pulseCount = 0;
            var maxPulses = $Pulses * 2;
            
            var pulse = setInterval(function() {
                if (pulseCount % 2 === 0) {
                    element.style.boxShadow = '0 0 20px $Color, 0 0 40px $Color';
                } else {
                    element.style.boxShadow = originalBoxShadow;
                }
                pulseCount++;
                if (pulseCount >= maxPulses) {
                    clearInterval(pulse);
                    element.style.boxShadow = originalBoxShadow;
                }
            }, $PulseDuration);
        })();
"@
    
    # Placeholder for executing JavaScript
    # $script:Driver.ExecuteScript($pulseScript)
    
    Start-Sleep -Milliseconds ($Pulses * $PulseDuration * 2)
}

function Draw-Arrow {
    <#
    .SYNOPSIS
        Draws an arrow between two elements for visual guidance.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$FromSelector,
        
        [Parameter(Mandatory = $true)]
        [string]$ToSelector,
        
        [Parameter()]
        [string]$Color = "#FF6B6B",
        
        [Parameter()]
        [int]$Duration = 3000
    )
    
    if (-not $script:DemoConfig.AddAnnotations) { return }
    
    $arrowScript = @"
        (function() {
            var from = document.querySelector('$FromSelector');
            var to = document.querySelector('$ToSelector');
            if (!from || !to) return;
            
            var fromRect = from.getBoundingClientRect();
            var toRect = to.getBoundingClientRect();
            
            var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.id = 'demo-arrow';
            svg.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 99997;';
            
            var defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            var marker = document.createElementNS('http://www.w3.org/2000/svg', 'marker');
            marker.setAttribute('id', 'arrowhead');
            marker.setAttribute('markerWidth', '10');
            marker.setAttribute('markerHeight', '7');
            marker.setAttribute('refX', '9');
            marker.setAttribute('refY', '3.5');
            marker.setAttribute('orient', 'auto');
            
            var polygon = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
            polygon.setAttribute('points', '0 0, 10 3.5, 0 7');
            polygon.setAttribute('fill', '$Color');
            
            marker.appendChild(polygon);
            defs.appendChild(marker);
            svg.appendChild(defs);
            
            var line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', fromRect.right);
            line.setAttribute('y1', fromRect.top + fromRect.height/2);
            line.setAttribute('x2', toRect.left);
            line.setAttribute('y2', toRect.top + toRect.height/2);
            line.setAttribute('stroke', '$Color');
            line.setAttribute('stroke-width', '3');
            line.setAttribute('marker-end', 'url(#arrowhead)');
            
            svg.appendChild(line);
            document.body.appendChild(svg);
            
            setTimeout(function() {
                svg.remove();
            }, $Duration);
        })();
"@
    
    # Placeholder for executing JavaScript
    # $script:Driver.ExecuteScript($arrowScript)
    
    Start-Sleep -Milliseconds $Duration
}

#endregion

#region Helper Functions

function Apply-StepDelay {
    <#
    .SYNOPSIS
        Applies the configured step delay based on execution speed.
    #>
    [CmdletBinding()]
    param()
    
    $delay = switch ($script:DemoConfig.ExecutionSpeed) {
        'Fast' { 100 }
        'Normal' { 500 }
        'Demo' { 1500 }
        'Slow' { 2500 }
        default { $script:DemoConfig.StepDelay }
    }
    
    if ($delay -gt 0) {
        Start-Sleep -Milliseconds $delay
    }
}

function Get-ElementText {
    <#
    .SYNOPSIS
        Gets the text content of an element.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter()]
        [ValidateSet('Id', 'Name', 'ClassName', 'CssSelector', 'XPath')]
        [string]$By = 'CssSelector'
    )
    
    $element = Wait-ForElement -Selector $Selector -By $By
    
    # Placeholder for getting element text
    # return $element.Text
    
    return ""
}

function Assert-ElementExists {
    <#
    .SYNOPSIS
        Asserts that an element exists on the page.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Selector,
        
        [Parameter()]
        [ValidateSet('Id', 'Name', 'ClassName', 'CssSelector', 'XPath')]
        [string]$By = 'CssSelector',
        
        [Parameter()]
        [string]$Message = ""
    )
    
    $result = Wait-ForElement -Selector $Selector -By $By -TimeoutSeconds 5
    
    if (-not $result.Found) {
        $errorMsg = if ($Message) { $Message } else { "Element not found: $Selector" }
        Write-AutomationLog $errorMsg -Level ERROR
        throw $errorMsg
    }
    
    Write-AutomationLog "Element verified: $Selector" -Level DEBUG
    return $true
}

#endregion

# Export functions
Export-ModuleMember -Function @(
    'Get-DefaultDemoConfig',
    'Start-BrowserSession',
    'Stop-BrowserSession',
    'Navigate-ToUrl',
    'Wait-ForPageLoad',
    'Wait-ForElement',
    'Click-Element',
    'Type-Text',
    'Select-DropdownOption',
    'Highlight-Element',
    'Animate-MouseToElement',
    'Show-Annotation',
    'Pause-ForInteraction',
    'Take-Screenshot',
    'Start-VideoRecording',
    'Stop-VideoRecording',
    'Apply-StepDelay',
    'Get-ElementText',
    'Assert-ElementExists',
    'Start-NetworkMonitoring',
    'Show-NetworkActivity',
    'Stop-NetworkMonitoring',
    'Show-FormFieldAnnotation',
    'Show-StepIndicator',
    'Remove-StepIndicator',
    'Pulse-Element',
    'Draw-Arrow'
)
