# ICTServe Development Environment Help
# Usage guide and troubleshooting for the development scripts

Write-Host "ICTServe v3.6.0 Development Environment Help" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "USAGE:" -ForegroundColor White
Write-Host "  .\scripts\dev\start-dev.ps1 [OPTIONS]" -ForegroundColor Gray
Write-Host ""

Write-Host "OPTIONS:" -ForegroundColor White
Write-Host "  -Profile <string>    Service profile to start (default: full)" -ForegroundColor Gray
Write-Host "  -SkipChecks         Skip environment validation checks" -ForegroundColor Gray
Write-Host "  -NoBrowser          Don't open browser automatically" -ForegroundColor Gray
Write-Host "  -InstallRedis       Auto-install Redis via WSL if missing" -ForegroundColor Gray
Write-Host "  -WSL                Force WSL Redis usage" -ForegroundColor Gray
Write-Host "  -NoMCP              Skip MCP server startup" -ForegroundColor Gray
Write-Host ""

Write-Host "PROFILES:" -ForegroundColor White
Write-Host "  minimal             Laravel + Vite only" -ForegroundColor Gray
Write-Host "  backend             Redis + Laravel + Reverb + Queue" -ForegroundColor Gray
Write-Host "  frontend            Laravel + Vite" -ForegroundColor Gray
Write-Host "  full                All services (default)" -ForegroundColor Gray
Write-Host "  testing             Full + Browser auto-open" -ForegroundColor Gray
Write-Host "  ai                  Full + MCP + Ollama integration" -ForegroundColor Gray
Write-Host ""

Write-Host "EXAMPLES:" -ForegroundColor White
Write-Host "  # Start full development environment" -ForegroundColor Green
Write-Host "  .\scripts\dev\start-dev.ps1" -ForegroundColor Gray
Write-Host ""
Write-Host "  # Start minimal environment (Laravel + Vite only)" -ForegroundColor Green
Write-Host "  .\scripts\dev\start-dev.ps1 -Profile minimal" -ForegroundColor Gray
Write-Host ""
Write-Host "  # Install Redis and start full environment" -ForegroundColor Green
Write-Host "  .\scripts\dev\start-dev.ps1 -InstallRedis" -ForegroundColor Gray
Write-Host ""
Write-Host "  # Quick start without checks or browser" -ForegroundColor Green
Write-Host "  .\scripts\dev\start-dev.ps1 -SkipChecks -NoBrowser" -ForegroundColor Gray
Write-Host ""

Write-Host "SERVICES STARTED:" -ForegroundColor White
Write-Host "  [REDIS]   Redis Server (127.0.0.1:6379)" -ForegroundColor Red
Write-Host "            Cache, Sessions, Queues, Reverb Backend" -ForegroundColor Gray
Write-Host ""
Write-Host "  [LARAVEL] Laravel Server (127.0.0.1:8000)" -ForegroundColor Blue
Write-Host "            ICTServe Application - True Hybrid Architecture" -ForegroundColor Gray
Write-Host ""
Write-Host "  [REVERB]  Laravel Reverb (127.0.0.1:8080)" -ForegroundColor Magenta
Write-Host "            WebSocket Broadcasting for Real-time Features" -ForegroundColor Gray
Write-Host ""
Write-Host "  [QUEUE]   Laravel Queue Worker" -ForegroundColor Cyan
Write-Host "            Background Jobs, Email Processing, Audit Logging" -ForegroundColor Gray
Write-Host ""
Write-Host "  [VITE]    Vite Dev Server (127.0.0.1:5173)" -ForegroundColor Green
Write-Host "            Frontend Assets, Hot Module Replacement" -ForegroundColor Gray
Write-Host ""
Write-Host "  [MCP]     Laravel MCP Server" -ForegroundColor DarkCyan
Write-Host "            Model Context Protocol for AI Integration" -ForegroundColor Gray
Write-Host ""

Write-Host "REQUIREMENTS:" -ForegroundColor White
Write-Host "  • PHP 8.2.12+ (required)" -ForegroundColor Gray
Write-Host "  • Node.js 22.12+ (for Vite)" -ForegroundColor Gray
Write-Host "  • Laravel 12.42.0+ (required)" -ForegroundColor Gray
Write-Host "  • WSL with Ubuntu/Debian (for Redis)" -ForegroundColor Gray
Write-Host "  • Composer dependencies installed" -ForegroundColor Gray
Write-Host ""

Write-Host "TROUBLESHOOTING:" -ForegroundColor White
Write-Host ""
Write-Host "  Redis Issues:" -ForegroundColor Yellow
Write-Host "    • Install via WSL: .\scripts\install-redis.ps1" -ForegroundColor Gray
Write-Host "    • Manual start: wsl.exe redis-server --daemonize yes" -ForegroundColor Gray
Write-Host "    • Check status: wsl.exe redis-cli ping" -ForegroundColor Gray
Write-Host ""
Write-Host "  npm/Node.js Issues:" -ForegroundColor Yellow
Write-Host "    • Fix permissions: npm config set prefix `$env:APPDATA\npm" -ForegroundColor Gray
Write-Host "    • Reinstall Node.js from: https://nodejs.org/" -ForegroundColor Gray
Write-Host "    • Clear cache: npm cache clean --force" -ForegroundColor Gray
Write-Host ""
Write-Host "  Laravel Issues:" -ForegroundColor Yellow
Write-Host "    • Install dependencies: composer install" -ForegroundColor Gray
Write-Host "    • Generate key: php artisan key:generate" -ForegroundColor Gray
Write-Host "    • Clear cache: php artisan config:clear" -ForegroundColor Gray
Write-Host ""
Write-Host "  Port Conflicts:" -ForegroundColor Yellow
Write-Host "    • Laravel (8000): Change with --port=8001" -ForegroundColor Gray
Write-Host "    • Vite (5173): Configure in vite.config.js" -ForegroundColor Gray
Write-Host "    • Reverb (8080): Configure in config/reverb.php" -ForegroundColor Gray
Write-Host ""

Write-Host "COMPLIANCE STANDARDS:" -ForegroundColor White
Write-Host "  • PDPA 2010: Personal data encryption and audit logging" -ForegroundColor Gray
Write-Host "  • WCAG 2.2 AA: 4.5:1 text contrast, 3:1 UI contrast" -ForegroundColor Gray
Write-Host "  • MyGOV Standards: Bahasa Melayu only, mobile-first" -ForegroundColor Gray
Write-Host "  • PSR-12: Code formatting via vendor/bin/pint" -ForegroundColor Gray
Write-Host ""

Write-Host "DEVELOPMENT COMMANDS:" -ForegroundColor White
Write-Host "  php artisan test                Run PHPUnit tests" -ForegroundColor Gray
Write-Host "  vendor/bin/pint                 Format code (PSR-12)" -ForegroundColor Gray
Write-Host "  vendor/bin/phpstan analyse      Static analysis" -ForegroundColor Gray
Write-Host "  npm run build                   Build frontend assets" -ForegroundColor Gray
Write-Host "  npm run dev                     Start Vite dev server" -ForegroundColor Gray
Write-Host ""

Write-Host "QUICK ACCESS URLS:" -ForegroundColor White
Write-Host "  • Application:     http://127.0.0.1:8000" -ForegroundColor Gray
Write-Host "  • Admin Panel:     http://127.0.0.1:8000/admin" -ForegroundColor Gray
Write-Host "  • Telescope:       http://127.0.0.1:8000/telescope" -ForegroundColor Gray
Write-Host "  • Pulse:           http://127.0.0.1:8000/pulse" -ForegroundColor Gray
Write-Host ""

Write-Host "ADDITIONAL SCRIPTS:" -ForegroundColor White
Write-Host "  .\scripts\install-redis.ps1     Install Redis via WSL" -ForegroundColor Gray
Write-Host "  .\scripts\dev\help.ps1          Show this help" -ForegroundColor Gray
Write-Host ""

Write-Host "For more information, see the ICTServe documentation in /docs/" -ForegroundColor Cyan
Write-Host ""
