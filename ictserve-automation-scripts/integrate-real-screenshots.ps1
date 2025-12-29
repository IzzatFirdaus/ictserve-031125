#Requires -Version 7.0
<#
.SYNOPSIS
    Integrates real screenshot functionality into the ICTServe automation system.

.DESCRIPTION
    This script sets up and tests real browser-based screenshot capture using Playwright,
    replacing placeholder text files with actual PNG images of the application.

.NOTES
    Requires Node.js and Playwright to be installed for real screenshot capture.
#>

[CmdletBinding()]
param(
    [Parameter()]
    [switch]$TestOnly,
    
    [Parameter()]
    [switch]$SetupPlaywright,
    
    [Parameter()]
    [string]$BaseUrl = "http://127.0.0.1:8000"
)

# Import common functions
$ScriptRoot = $PSScriptRoot
. (Join-Path $ScriptRoot "utilities\common-functions.ps1")

Write-Host "ICTServe Real Screenshot Integration" -ForegroundColor Cyan
Write-Host "===================================" -ForegroundColor Cyan
Write-Host ""

# Check prerequisites
Write-Host "🔍 Checking Prerequisites..." -ForegroundColor Yellow

# Check Node.js
try {
    $nodeVersion = node --version 2>$null
    if ($nodeVersion) {
        Write-Host "  ✅ Node.js: $nodeVersion" -ForegroundColor Green
    } else {
        Write-Host "  ❌ Node.js not found" -ForegroundColor Red
        Write-Host "     Install Node.js from https://nodejs.org/" -ForegroundColor Yellow
        exit 1
    }
} catch {
    Write-Host "  ❌ Node.js not found" -ForegroundColor Red
    Write-Host "     Install Node.js from https://nodejs.org/" -ForegroundColor Yellow
    exit 1
}

# Check if Playwright is available
$playwrightCheck = npm list playwright 2>$null
if ($playwrightCheck -match "playwright@") {
    Write-Host "  ✅ Playwright is installed" -ForegroundColor Green
} else {
    Write-Host "  ⚠️ Playwright not found in current directory" -ForegroundColor Yellow
    
    if ($SetupPlaywright) {
        Write-Host "  📦 Installing Playwright..." -ForegroundColor Cyan
        npm install playwright
        npx playwright install chromium
        Write-Host "  ✅ Playwright installation completed" -ForegroundColor Green
    } else {
        Write-Host "     Run with -SetupPlaywright to install Playwright" -ForegroundColor Yellow
        Write-Host "     Or manually run: npm install playwright && npx playwright install" -ForegroundColor Yellow
    }
}

# Ensure the working CJS screenshot script exists
$screenshotScript = Join-Path $ScriptRoot "take-single-screenshot.cjs"
Write-Host "📝 Ensuring CJS screenshot script exists: $screenshotScript" -ForegroundColor Cyan

if (-not (Test-Path $screenshotScript)) {
    Write-Host "  📝 Creating CJS screenshot script..." -ForegroundColor Yellow
    
    $scriptContent = @"
/**
 * Single Screenshot Capture Script
 * Takes a screenshot of a specific URL using Playwright
 */

const { chromium } = require("playwright");
const path = require("path");
const fs = require("fs");

async function takeScreenshot() {
    const args = process.argv.slice(2);
    const url = args[0] || "$BaseUrl";
    const outputPath = args[1] || "screenshot.png";
    const fullPage = args[2] !== "false";
    
    let browser = null;
    
    try {
        console.log("📸 Taking screenshot of: " + url);
        console.log("💾 Output path: " + outputPath);
        console.log("📄 Full page: " + fullPage);
        
        browser = await chromium.launch({
            headless: true,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--disable-gpu'
            ]
        });
        
        const page = await browser.newPage();
        await page.setViewportSize({ width: 1920, height: 1080 });
        
        // Navigate to URL
        console.log("🌐 Navigating to URL...");
        await page.goto(url, { waitUntil: 'networkidle', timeout: 30000 });
        
        // Wait for page to be fully loaded
        console.log("⏳ Waiting for page to stabilize...");
        await page.waitForTimeout(2000);
        
        // Ensure output directory exists
        const outputDir = path.dirname(outputPath);
        if (!fs.existsSync(outputDir)) {
            console.log("📁 Creating directory: " + outputDir);
            fs.mkdirSync(outputDir, { recursive: true });
        }
        
        // Take screenshot
        console.log("📷 Capturing screenshot...");
        await page.screenshot({
            path: outputPath,
            fullPage: fullPage,
            animations: "disabled",
            type: 'png'
        });
        
        // Verify file was created
        if (fs.existsSync(outputPath)) {
            const stats = fs.statSync(outputPath);
            console.log("✅ Screenshot saved successfully!");
            console.log("📊 File size: " + (stats.size / 1024).toFixed(2) + " KB");
        } else {
            throw new Error('Screenshot file was not created');
        }
        
    } catch (error) {
        console.error("❌ Screenshot failed: " + error.message);
        process.exit(1);
    } finally {
        if (browser) {
            console.log("🔒 Closing browser...");
            await browser.close();
        }
    }
}

takeScreenshot();
"@
    
    Set-Content -Path $screenshotScript -Value $scriptContent
    Write-Host "  ✅ CJS screenshot script created successfully" -ForegroundColor Green
} else {
    Write-Host "  ✅ CJS screenshot script already exists" -ForegroundColor Green
}

if ($TestOnly) {
    Write-Host ""
    Write-Host "🧪 Testing Screenshot Functionality..." -ForegroundColor Yellow
    
    # Test screenshot scenarios
    $testScenarios = @(
        @{ Name = "homepage"; Url = "$BaseUrl"; Description = "Homepage" },
        @{ Name = "helpdesk"; Url = "$BaseUrl/helpdesk/create"; Description = "Helpdesk Form" },
        @{ Name = "loan"; Url = "$BaseUrl/loan/create"; Description = "Loan Form" }
    )
    
    $testResults = @()
    
    foreach ($scenario in $testScenarios) {
        Write-Host ""
        Write-Host "📸 Testing: $($scenario.Description)" -ForegroundColor Cyan
        
        try {
            # Use the Take-Screenshot function from common-functions.ps1
            $screenshotPath = Take-Screenshot -Driver @{SessionId="test-$($scenario.Name)"} -Name "test-$($scenario.Name)" -Mode 'Headless' -Url $scenario.Url
            
            if (Test-Path $screenshotPath) {
                $fileInfo = Get-Item $screenshotPath
                if ($fileInfo.Length -gt 1000) {
                    Write-Host "  ✅ Success: Real screenshot captured ($([math]::Round($fileInfo.Length/1024, 2)) KB)" -ForegroundColor Green
                    $testResults += @{ Scenario = $scenario.Name; Status = "Success"; Size = $fileInfo.Length }
                } else {
                    Write-Host "  ⚠️ Warning: Small file created ($($fileInfo.Length) bytes) - likely a placeholder" -ForegroundColor Yellow
                    $testResults += @{ Scenario = $scenario.Name; Status = "Placeholder"; Size = $fileInfo.Length }
                }
            } else {
                Write-Host "  ❌ Failed: No file created" -ForegroundColor Red
                $testResults += @{ Scenario = $scenario.Name; Status = "Failed"; Size = 0 }
            }
        } catch {
            Write-Host "  ❌ Error: $($_.Exception.Message)" -ForegroundColor Red
            $testResults += @{ Scenario = $scenario.Name; Status = "Error"; Size = 0; Error = $_.Exception.Message }
        }
    }
    
    # Summary
    Write-Host ""
    Write-Host "📊 Test Results Summary:" -ForegroundColor Cyan
    $successful = ($testResults | Where-Object { $_.Status -eq "Success" }).Count
    $placeholders = ($testResults | Where-Object { $_.Status -eq "Placeholder" }).Count
    $failed = ($testResults | Where-Object { $_.Status -in @("Failed", "Error") }).Count
    
    Write-Host "  ✅ Successful: $successful" -ForegroundColor Green
    Write-Host "  ⚠️ Placeholders: $placeholders" -ForegroundColor Yellow
    Write-Host "  ❌ Failed: $failed" -ForegroundColor Red
    
    if ($successful -gt 0) {
        Write-Host ""
        Write-Host "🎉 Real screenshot functionality is working!" -ForegroundColor Green
        Write-Host "   The automation system can now capture actual browser screenshots." -ForegroundColor White
    } elseif ($placeholders -gt 0) {
        Write-Host ""
        Write-Host "⚠️ Screenshot functionality is partially working." -ForegroundColor Yellow
        Write-Host "   Placeholders are being created instead of real images." -ForegroundColor White
        Write-Host "   Check that the Laravel application is running at: $BaseUrl" -ForegroundColor Yellow
    } else {
        Write-Host ""
        Write-Host "❌ Screenshot functionality is not working." -ForegroundColor Red
        Write-Host "   Check Node.js, Playwright installation, and application availability." -ForegroundColor White
    }
}

Write-Host ""
Write-Host "✅ Real Screenshot Integration Complete!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Next Steps:" -ForegroundColor Cyan
Write-Host "  1. Ensure your Laravel application is running at: $BaseUrl" -ForegroundColor White
Write-Host "  2. Run the autonomous test: .\autonomous-full-menu-test.ps1" -ForegroundColor White
Write-Host "  3. Check the reports\screenshots directory for real PNG files" -ForegroundColor White
Write-Host ""
Write-Host "🔧 Troubleshooting:" -ForegroundColor Yellow
Write-Host "  - If screenshots are still placeholders, check that Node.js and Playwright are properly installed" -ForegroundColor White
Write-Host "  - Verify the Laravel application is accessible at the specified URL" -ForegroundColor White
Write-Host "  - Check the PowerShell execution policy allows script execution" -ForegroundColor White
Write-Host ""