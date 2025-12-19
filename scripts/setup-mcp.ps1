# ICTServe MCP Setup Script
# Configures MCP servers for local development (no Docker required)

param(
    [switch]$VerifyOnly,
    [switch]$InstallDependencies
)

Write-Host "🚀 ICTServe MCP Setup Script" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan

# Check prerequisites
Write-Host "`n📋 Checking Prerequisites..." -ForegroundColor Yellow

# Check Node.js
try {
    $nodeVersion = node --version
    Write-Host "✅ Node.js: $nodeVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Node.js not found. Please install Node.js 20+ from https://nodejs.org" -ForegroundColor Red
    exit 1
}

# Check PHP
try {
    $phpVersion = php --version | Select-String "PHP (\d+\.\d+)" | ForEach-Object { $_.Matches[0].Groups[1].Value }
    if ([version]$phpVersion -ge [version]"8.2") {
        Write-Host "✅ PHP: $phpVersion" -ForegroundColor Green
    } else {
        Write-Host "❌ PHP version $phpVersion is too old. Requires PHP 8.2+" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "❌ PHP not found. Please install PHP 8.2+" -ForegroundColor Red
    exit 1
}

# Check NPX
try {
    $npxVersion = npx --version
    Write-Host "✅ NPX: $npxVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ NPX not found. Please install Node.js with NPM" -ForegroundColor Red
    exit 1
}

# Check UVX (required for fetch server)
try {
    $uvxVersion = uvx --version
    Write-Host "✅ UVX: $uvxVersion" -ForegroundColor Green
} catch {
    Write-Host "⚠️  UVX not found. Install with: pip install uv" -ForegroundColor Yellow
    Write-Host "   UVX is required for the fetch MCP server" -ForegroundColor Yellow
}

if ($VerifyOnly) {
    Write-Host "`n✅ All prerequisites met!" -ForegroundColor Green
    exit 0
}

# Create storage directory
Write-Host "`n📁 Setting up storage directory..." -ForegroundColor Yellow
$storageDir = "storage/mcp"
if (!(Test-Path $storageDir)) {
    New-Item -ItemType Directory -Path $storageDir -Force | Out-Null
    Write-Host "✅ Created $storageDir" -ForegroundColor Green
} else {
    Write-Host "✅ $storageDir already exists" -ForegroundColor Green
}

# Create memory file
$memoryFile = "$storageDir/memory.jsonl"
if (!(Test-Path $memoryFile)) {
    New-Item -ItemType File -Path $memoryFile -Force | Out-Null
    Write-Host "✅ Created $memoryFile" -ForegroundColor Green
} else {
    Write-Host "✅ $memoryFile already exists" -ForegroundColor Green
}

# Install MCP dependencies if requested
if ($InstallDependencies) {
    Write-Host "`n📦 Installing MCP Server Dependencies..." -ForegroundColor Yellow
    
    $packages = @(
        "@modelcontextprotocol/server-memory",
        "@modelcontextprotocol/server-sequential-thinking", 
        "chrome-devtools-mcp@latest",
        "@playwright/mcp@latest",
        "@modelcontextprotocol/server-github"
    )
    
    # Test UVX packages separately
    $uvxPackages = @("mcp-server-fetch")
    
    foreach ($package in $packages) {
        Write-Host "Installing $package..." -ForegroundColor Cyan
        try {
            npx -y $package --help | Out-Null
            Write-Host "✅ $package ready" -ForegroundColor Green
        } catch {
            Write-Host "⚠️  $package may need manual installation" -ForegroundColor Yellow
        }
    }
    
    # Test UVX packages
    foreach ($package in $uvxPackages) {
        Write-Host "Installing $package (UVX)..." -ForegroundColor Cyan
        try {
            uvx $package --help | Out-Null
            Write-Host "✅ $package ready" -ForegroundColor Green
        } catch {
            Write-Host "⚠️  $package may need manual installation or UVX not available" -ForegroundColor Yellow
        }
    }
}

# Check Laravel MCP
Write-Host "`n🔧 Checking Laravel MCP..." -ForegroundColor Yellow
try {
    $mcpCommands = php artisan list mcp 2>$null
    if ($mcpCommands -match "mcp:start") {
        Write-Host "✅ Laravel MCP commands available" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Laravel MCP not installed. Run: composer require laravel/mcp" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️  Cannot check Laravel MCP. Ensure you're in Laravel project root" -ForegroundColor Yellow
}

# Check configuration files
Write-Host "`n⚙️  Checking Configuration Files..." -ForegroundColor Yellow

$kiroConfig = ".kiro/settings/mcp.json"
if (Test-Path $kiroConfig) {
    Write-Host "✅ Kiro MCP config found: $kiroConfig" -ForegroundColor Green
} else {
    Write-Host "⚠️  Kiro MCP config not found: $kiroConfig" -ForegroundColor Yellow
}

$rootConfig = ".mcp.json"
if (Test-Path $rootConfig) {
    Write-Host "✅ Root MCP config found: $rootConfig" -ForegroundColor Green
} else {
    Write-Host "⚠️  Root MCP config not found: $rootConfig" -ForegroundColor Yellow
}

# Environment variables check
Write-Host "`n🔐 Checking Environment Variables..." -ForegroundColor Yellow
$envVars = @(
    "CONTEXT7_API_KEY",
    "DEEPL_API_KEY", 
    "FIRECRAWL_API_KEY",
    "PAT_GITHUB_ACCESS_TOKEN"
)

foreach ($var in $envVars) {
    $envValue = [Environment]::GetEnvironmentVariable($var)
    if ($envValue) {
        Write-Host "✅ $var is set" -ForegroundColor Green
    } else {
        Write-Host "⚠️  $var not set (optional)" -ForegroundColor Yellow
    }
}

Write-Host "`n🎉 MCP Setup Complete!" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Cyan

Write-Host "`n📖 Next Steps:" -ForegroundColor Yellow
Write-Host "1. Restart your IDE (Kiro/VS Code)" -ForegroundColor White
Write-Host "2. Open MCP server panel and start servers" -ForegroundColor White
Write-Host "3. Test with: Ctrl+Shift+P -> 'MCP: List Servers'" -ForegroundColor White
Write-Host "4. For API-based servers, set environment variables" -ForegroundColor White

Write-Host "`n📚 Documentation:" -ForegroundColor Yellow
Write-Host "- MCP Configuration: docs/mcp/MCP_CONFIGURATION.md" -ForegroundColor White
Write-Host "- Resolution Guide: docs/mcp/MCP_RESOLUTION_DOCKER_TO_LOCAL.md" -ForegroundColor White
Write-Host "- Laravel MCP: docs/mcp/LARAVEL_MCP_IMPLEMENTATION.md" -ForegroundColor White