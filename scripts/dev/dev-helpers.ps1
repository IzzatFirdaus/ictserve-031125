# ICTServe Development Helper Functions
# Quick commands for common development tasks

param(
    [Parameter(Position=0)]
    [ValidateSet("test", "format", "analyse", "build", "clean", "setup", "status", "logs", "help")]
    [string]$Command = "help",
    
    [switch]$Watch,
    [switch]$Coverage,
    [string]$Filter = ""
)

# Helper function to run commands with proper error handling
function Invoke-DevCommand {
    param(
        [string]$Command,
        [string]$Description,
        [switch]$Critical = $false
    )
    
    $timestamp = Get-Date -Format "HH:mm:ss"
    Write-Host "[$timestamp] $Description..." -ForegroundColor Cyan
    
    try {
        Invoke-Expression $Command
        if ($LASTEXITCODE -eq 0) {
            Write-Host "[$timestamp] ✓ $Description completed successfully" -ForegroundColor Green
        } else {
            Write-Host "[$timestamp] ✗ $Description failed (exit code: $LASTEXITCODE)" -ForegroundColor Red
            if ($Critical) { exit $LASTEXITCODE }
        }
    }
    catch {
        Write-Host "[$timestamp] ✗ $Description failed: $_" -ForegroundColor Red
        if ($Critical) { throw }
    }
}

# Main command dispatcher
switch ($Command) {
    "test" {
        Write-Host "ICTServe Testing Suite" -ForegroundColor Yellow
        Write-Host "=====================" -ForegroundColor Yellow
        
        if ($Filter) {
            Invoke-DevCommand "php artisan test --filter=$Filter" "Running filtered tests ($Filter)"
        } elseif ($Coverage) {
            Invoke-DevCommand "php artisan test --coverage --min=80" "Running tests with coverage analysis"
        } elseif ($Watch) {
            Write-Host "Watch mode not available for PHPUnit. Use --filter for specific tests." -ForegroundColor Yellow
            Invoke-DevCommand "php artisan test" "Running all tests"
        } else {
            Invoke-DevCommand "php artisan test" "Running PHPUnit test suite"
        }
        
        Write-Host ""
        Write-Host "E2E Testing Options:" -ForegroundColor Gray
        Write-Host "  npm run test:e2e              # Run all E2E tests" -ForegroundColor Gray
        Write-Host "  npm run test:e2e:helpdesk     # Test helpdesk module" -ForegroundColor Gray
        Write-Host "  npm run test:e2e:loan         # Test asset loan module" -ForegroundColor Gray
        Write-Host "  npm run test:accessibility     # Test WCAG 2.2 AA compliance" -ForegroundColor Gray
    }
    
    "format" {
        Write-Host "ICTServe Code Formatting (PSR-12)" -ForegroundColor Yellow
        Write-Host "=================================" -ForegroundColor Yellow
        
        Invoke-DevCommand "vendor/bin/pint" "Formatting PHP code with Laravel Pint" -Critical
        
        Write-Host ""
        Write-Host "Frontend Formatting:" -ForegroundColor Gray
        Write-Host "  npm run lint:fix               # Fix ESLint issues" -ForegroundColor Gray
        Write-Host "  npm run prettier:fix           # Fix Prettier formatting" -ForegroundColor Gray
    }
    
    "analyse" {
        Write-Host "ICTServe Static Analysis" -ForegroundColor Yellow
        Write-Host "=======================" -ForegroundColor Yellow
        
        Invoke-DevCommand "vendor/bin/phpstan analyse" "Running PHPStan Level 9 analysis"
        
        if (Test-Path "vendor/bin/phpinsights") {
            Invoke-DevCommand "vendor/bin/phpinsights --no-interaction" "Running PHP Insights quality analysis"
        }
        
        Write-Host ""
        Write-Host "Additional Analysis:" -ForegroundColor Gray
        Write-Host "  composer analyse:save          # Save analysis results" -ForegroundColor Gray
        Write-Host "  composer insights              # Detailed code insights" -ForegroundColor Gray
    }
    
    "build" {
        Write-Host "ICTServe Asset Building" -ForegroundColor Yellow
        Write-Host "======================" -ForegroundColor Yellow
        
        Invoke-DevCommand "npm run build" "Building production assets with Vite" -Critical
        
        Write-Host ""
        Write-Host "Build Verification:" -ForegroundColor Gray
        Write-Host "  Check public/build/ directory for compiled assets" -ForegroundColor Gray
        Write-Host "  Verify Tailwind CSS compilation" -ForegroundColor Gray
        Write-Host "  Test Livewire component loading" -ForegroundColor Gray
    }
    
    "clean" {
        Write-Host "ICTServe Environment Cleanup" -ForegroundColor Yellow
        Write-Host "===========================" -ForegroundColor Yellow
        
        $cleanupTasks = @(
            @{ Command = "php artisan config:clear"; Description = "Clearing configuration cache" },
            @{ Command = "php artisan route:clear"; Description = "Clearing route cache" },
            @{ Command = "php artisan view:clear"; Description = "Clearing view cache" },
            @{ Command = "php artisan cache:clear"; Description = "Clearing application cache" },
            @{ Command = "composer dump-autoload"; Description = "Regenerating autoload files" }
        )
        
        foreach ($task in $cleanupTasks) {
            Invoke-DevCommand $task.Command $task.Description
        }
        
        # Clean node_modules if requested
        $cleanNode = Read-Host "Clean node_modules and reinstall? (y/N)"
        if ($cleanNode -eq 'y' -or $cleanNode -eq 'Y') {
            if (Test-Path "node_modules") {
                Remove-Item "node_modules" -Recurse -Force
                Write-Host "Removed node_modules directory" -ForegroundColor Green
            }
            Invoke-DevCommand "npm install" "Reinstalling Node.js dependencies"
        }
    }
    
    "setup" {
        Write-Host "ICTServe Development Setup" -ForegroundColor Yellow
        Write-Host "=========================" -ForegroundColor Yellow
        
        $setupTasks = @(
            @{ Command = "composer install"; Description = "Installing PHP dependencies" },
            @{ Command = "npm install"; Description = "Installing Node.js dependencies" },
            @{ Command = "php artisan key:generate"; Description = "Generating application key" },
            @{ Command = "php artisan migrate"; Description = "Running database migrations" },
            @{ Command = "php artisan db:seed"; Description = "Seeding database" },
            @{ Command = "npm run build"; Description = "Building initial assets" }
        )
        
        foreach ($task in $setupTasks) {
            Invoke-DevCommand $task.Command $task.Description -Critical
        }
        
        Write-Host ""
        Write-Host "Setup Complete! Next steps:" -ForegroundColor Green
        Write-Host "  1. Configure .env file with your database settings" -ForegroundColor Gray
        Write-Host "  2. Run: .\scripts\dev\start-dev.ps1" -ForegroundColor Gray
        Write-Host "  3. Visit: http://127.0.0.1:8000" -ForegroundColor Gray
    }
    
    "status" {
        Write-Host "ICTServe Development Status" -ForegroundColor Yellow
        Write-Host "==========================" -ForegroundColor Yellow
        
        # Check running services
        $services = @(
            @{ Name = "Laravel Server"; Port = 8000 },
            @{ Name = "Vite Dev Server"; Port = 5173 },
            @{ Name = "Laravel Reverb"; Port = 6001 },
            @{ Name = "Redis Server"; Port = 6379 }
        )
        
        foreach ($service in $services) {
            try {
                $result = Test-NetConnection -ComputerName 127.0.0.1 -Port $service.Port -WarningAction SilentlyContinue
                if ($result.TcpTestSucceeded) {
                    Write-Host "  ✓ $($service.Name) - Running on port $($service.Port)" -ForegroundColor Green
                } else {
                    Write-Host "  ✗ $($service.Name) - Not running on port $($service.Port)" -ForegroundColor Red
                }
            }
            catch {
                Write-Host "  ? $($service.Name) - Status unknown" -ForegroundColor Yellow
            }
        }
        
        # Check processes
        Write-Host ""
        Write-Host "Active Processes:" -ForegroundColor White
        $processes = Get-Process | Where-Object { 
            $_.ProcessName -match "php|node|npm" -and 
            $_.MainWindowTitle -match "Laravel|Vite|Queue|Reverb"
        }
        
        if ($processes) {
            $processes | ForEach-Object {
                Write-Host "  • $($_.ProcessName) (PID: $($_.Id)) - $($_.MainWindowTitle)" -ForegroundColor Gray
            }
        } else {
            Write-Host "  No development processes found" -ForegroundColor Gray
        }
    }
    
    "logs" {
        Write-Host "ICTServe Development Logs" -ForegroundColor Yellow
        Write-Host "========================" -ForegroundColor Yellow
        
        $logOptions = @(
            @{ Key = "1"; Description = "Laravel Application Logs"; Command = "php artisan pail" },
            @{ Key = "2"; Description = "Laravel Queue Logs"; Command = "php artisan queue:monitor" },
            @{ Key = "3"; Description = "Browser Console Logs"; Command = "# Use Laravel Boost: browser-logs 20" },
            @{ Key = "4"; Description = "Reverb WebSocket Logs"; Command = "# Check Reverb terminal window" }
        )
        
        Write-Host "Available log sources:" -ForegroundColor White
        foreach ($option in $logOptions) {
            Write-Host "  $($option.Key). $($option.Description)" -ForegroundColor Gray
        }
        
        $choice = Read-Host "Select log source (1-4)"
        switch ($choice) {
            "1" { Invoke-DevCommand "php artisan pail --timeout=0" "Starting Laravel Pail log viewer" }
            "2" { Invoke-DevCommand "php artisan queue:monitor" "Starting queue monitor" }
            "3" { Write-Host "Use Laravel Boost MCP server: browser-logs 20" -ForegroundColor Cyan }
            "4" { Write-Host "Check the Reverb terminal window for WebSocket logs" -ForegroundColor Cyan }
            default { Write-Host "Invalid selection" -ForegroundColor Red }
        }
    }
    
    "help" {
        Write-Host "ICTServe Development Helper" -ForegroundColor Cyan
        Write-Host "==========================" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Usage: .\scripts\dev\dev-helpers.ps1 <command> [options]" -ForegroundColor White
        Write-Host ""
        Write-Host "Commands:" -ForegroundColor Yellow
        Write-Host "  test      Run PHPUnit tests" -ForegroundColor White
        Write-Host "            -Filter <pattern>  Run specific tests" -ForegroundColor Gray
        Write-Host "            -Coverage          Include coverage analysis" -ForegroundColor Gray
        Write-Host ""
        Write-Host "  format    Format code with Laravel Pint (PSR-12)" -ForegroundColor White
        Write-Host "  analyse   Run static analysis (PHPStan Level 9)" -ForegroundColor White
        Write-Host "  build     Build production assets with Vite" -ForegroundColor White
        Write-Host "  clean     Clear caches and cleanup environment" -ForegroundColor White
        Write-Host "  setup     Initial project setup" -ForegroundColor White
        Write-Host "  status    Check development services status" -ForegroundColor White
        Write-Host "  logs      View application logs" -ForegroundColor White
        Write-Host "  help      Show this help message" -ForegroundColor White
        Write-Host ""
        Write-Host "Examples:" -ForegroundColor Yellow
        Write-Host "  .\scripts\dev\dev-helpers.ps1 test -Filter HelpdeskTest" -ForegroundColor Gray
        Write-Host "  .\scripts\dev\dev-helpers.ps1 test -Coverage" -ForegroundColor Gray
        Write-Host "  .\scripts\dev\dev-helpers.ps1 format" -ForegroundColor Gray
        Write-Host "  .\scripts\dev\dev-helpers.ps1 status" -ForegroundColor Gray
        Write-Host ""
        Write-Host "ICTServe v3.6.0 Compliance:" -ForegroundColor Yellow
        Write-Host "  • PDPA 2010: Personal data protection" -ForegroundColor Gray
        Write-Host "  • WCAG 2.2 AA: Accessibility standards" -ForegroundColor Gray
        Write-Host "  • PSR-12: PHP coding standards" -ForegroundColor Gray
        Write-Host "  • MyGOV: Malaysian government standards" -ForegroundColor Gray
    }
    
    default {
        Write-Host "Unknown command: $Command" -ForegroundColor Red
        Write-Host "Run '.\scripts\dev\dev-helpers.ps1 help' for available commands" -ForegroundColor Yellow
    }
}
