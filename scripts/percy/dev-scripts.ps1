# Percy Development Scripts for ICTServe v3.6.1
# PowerShell script for Windows development environment

param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("quick", "dashboard", "forms", "accessibility", "responsive", "hybrid", "bahasa", "setup", "validate", "help")]
    [string]$Command,
    
    [Parameter(Mandatory=$false)]
    [string]$TestFile = "",
    
    [Parameter(Mandatory=$false)]
    [switch]$NoPercy = $false,
    
    [Parameter(Mandatory=$false)]
    [switch]$Debug = $false
)

# Colors for output
$Green = "Green"
$Yellow = "Yellow"
$Red = "Red"
$Cyan = "Cyan"

function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

function Show-Help {
    Write-ColorOutput "🎨 Percy Development Scripts for ICTServe v3.6.1" $Cyan
    Write-ColorOutput ""
    Write-ColorOutput "Usage: .\scripts\percy\dev-scripts.ps1 -Command <command> [options]" $Yellow
    Write-ColorOutput ""
    Write-ColorOutput "Commands:" $Green
    Write-ColorOutput "  quick        - Quick Percy validation test" $White
    Write-ColorOutput "  dashboard    - Test dashboard components with Percy" $White
    Write-ColorOutput "  forms        - Test form components (helpdesk, loan)" $White
    Write-ColorOutput "  accessibility- Test accessibility features" $White
    Write-ColorOutput "  responsive   - Test responsive design" $White
    Write-ColorOutput "  hybrid       - Test True Hybrid Architecture" $White
    Write-ColorOutput "  bahasa       - Test Bahasa Melayu interface" $White
    Write-ColorOutput "  setup        - Run Percy setup validation" $White
    Write-ColorOutput "  validate     - Validate Percy configuration" $White
    Write-ColorOutput "  help         - Show this help message" $White
    Write-ColorOutput ""
    Write-ColorOutput "Options:" $Green
    Write-ColorOutput "  -TestFile    - Specific test file to run" $White
    Write-ColorOutput "  -NoPercy     - Run tests without Percy" $White
    Write-ColorOutput "  -Debug       - Run tests in debug mode" $White
    Write-ColorOutput ""
    Write-ColorOutput "Examples:" $Yellow
    Write-ColorOutput "  .\scripts\percy\dev-scripts.ps1 -Command quick" $White
    Write-ColorOutput "  .\scripts\percy\dev-scripts.ps1 -Command dashboard -Debug" $White
    Write-ColorOutput "  .\scripts\percy\dev-scripts.ps1 -Command forms -NoPercy" $White
    Write-ColorOutput "  .\scripts\percy\dev-scripts.ps1 -Command accessibility -TestFile 'accessibility.comprehensive.spec.ts'" $White
}

function Test-Prerequisites {
    Write-ColorOutput "🔍 Checking prerequisites..." $Cyan
    
    # Check if npm is available
    try {
        $npmVersion = npm --version
        Write-ColorOutput "✓ npm version: $npmVersion" $Green
    } catch {
        Write-ColorOutput "❌ npm not found. Please install Node.js and npm." $Red
        exit 1
    }
    
    # Check if Percy token is set
    if (-not $env:PERCY_TOKEN) {
        Write-ColorOutput "⚠️  PERCY_TOKEN not set. Percy features may not work." $Yellow
    } else {
        Write-ColorOutput "✓ PERCY_TOKEN is configured" $Green
    }
    
    # Check if Laravel server is running
    try {
        $response = Invoke-WebRequest -Uri "http://127.0.0.1:8000" -TimeoutSec 5 -UseBasicParsing
        Write-ColorOutput "✓ Laravel server is running" $Green
    } catch {
        Write-ColorOutput "⚠️  Laravel server not running. Starting server..." $Yellow
        Start-Process -FilePath "php" -ArgumentList "artisan", "serve", "--host=127.0.0.1", "--port=8000" -WindowStyle Hidden
        Start-Sleep -Seconds 3
    }
}

function Run-Command {
    param([string]$NpmScript, [string]$Description)
    
    Write-ColorOutput "🚀 $Description" $Cyan
    Write-ColorOutput "Running: npm run $NpmScript" $Yellow
    
    try {
        if ($Debug) {
            $NpmScript = $NpmScript -replace "test:", "test:e2e:debug "
        }
        
        npm run $NpmScript
        
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✅ $Description completed successfully!" $Green
        } else {
            Write-ColorOutput "❌ $Description failed with exit code $LASTEXITCODE" $Red
        }
    } catch {
        Write-ColorOutput "❌ Error running $Description`: $($_.Exception.Message)" $Red
    }
}

# Main script logic
Write-ColorOutput "🎨 Percy Development Scripts for ICTServe v3.6.1" $Cyan
Write-ColorOutput "Technology Stack: Laravel 12.43.1, Livewire 3.7.3, Filament 4.3.1, Playwright 1.56.1" $Yellow
Write-ColorOutput ""

switch ($Command) {
    "help" {
        Show-Help
        exit 0
    }
    
    "validate" {
        Write-ColorOutput "🔧 Validating Percy configuration..." $Cyan
        npm run percy:package-validate
        npm run percy:config-validate
        exit $LASTEXITCODE
    }
    
    "quick" {
        Test-Prerequisites
        if ($NoPercy) {
            Run-Command "test:e2e:no-percy" "Quick test without Percy"
        } else {
            Run-Command "dev:percy:quick" "Quick Percy validation"
        }
    }
    
    "dashboard" {
        Test-Prerequisites
        if ($NoPercy) {
            Run-Command "test:e2e" "Dashboard tests without Percy"
        } else {
            Run-Command "dev:percy:dashboard" "Dashboard tests with Percy"
        }
    }
    
    "forms" {
        Test-Prerequisites
        if ($NoPercy) {
            Run-Command "test:e2e:helpdesk" "Form tests without Percy"
            Run-Command "test:e2e:loan" "Loan form tests without Percy"
        } else {
            Run-Command "dev:percy:forms" "Form tests with Percy"
        }
    }
    
    "accessibility" {
        Test-Prerequisites
        if ($TestFile) {
            $script = if ($NoPercy) { "test:accessibility:no-percy" } else { "test:accessibility:percy" }
            Run-Command $script "Accessibility tests for $TestFile"
        } else {
            if ($NoPercy) {
                Run-Command "test:accessibility:no-percy" "Accessibility tests without Percy"
            } else {
                Run-Command "dev:percy:accessibility" "Accessibility tests with Percy"
            }
        }
    }
    
    "responsive" {
        Test-Prerequisites
        if ($NoPercy) {
            Run-Command "test:e2e" "Responsive tests without Percy"
        } else {
            Run-Command "dev:percy:responsive" "Responsive design tests with Percy"
        }
    }
    
    "hybrid" {
        Test-Prerequisites
        if ($NoPercy) {
            Run-Command "test:e2e" "Hybrid architecture tests without Percy"
        } else {
            Run-Command "dev:percy:hybrid" "True Hybrid Architecture tests with Percy"
        }
    }
    
    "bahasa" {
        Test-Prerequisites
        if ($NoPercy) {
            Run-Command "test:e2e" "Bahasa Melayu tests without Percy"
        } else {
            Run-Command "dev:percy:bahasa" "Bahasa Melayu interface tests with Percy"
        }
    }
    
    "setup" {
        Test-Prerequisites
        Run-Command "test:e2e:percy:setup" "Percy setup validation"
    }
    
    default {
        Write-ColorOutput "❌ Unknown command: $Command" $Red
        Write-ColorOutput "Use -Command help to see available commands." $Yellow
        exit 1
    }
}

Write-ColorOutput ""
Write-ColorOutput "🎉 Percy development script completed!" $Green
Write-ColorOutput "For more information, visit: https://docs.percy.io/docs/playwright" $Cyan