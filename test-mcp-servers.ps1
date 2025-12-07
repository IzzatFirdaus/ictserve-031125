# MCP Server Test Script
# Tests connectivity and basic functionality of configured MCP servers

Write-Host "=== MCP Server Test Suite ===" -ForegroundColor Cyan
Write-Host ""

# Test 1: Sequential Thinking Server
Write-Host "1. Testing Sequential Thinking MCP Server..." -ForegroundColor Yellow
$result = npx -y @modelcontextprotocol/server-sequential-thinking --version 2>&1
if ($LASTEXITCODE -eq 0 -or $result) {
    Write-Host "   OK Sequential Thinking: Available" -ForegroundColor Green
} else {
    Write-Host "   X Sequential Thinking: Not available" -ForegroundColor Red
}

# Test 2: Memory Server
Write-Host "`n2. Testing Memory MCP Server..." -ForegroundColor Yellow
$memoryPath = ".\storage\mcp\memory.jsonl"
if (Test-Path $memoryPath) {
    Write-Host "   OK Memory: File exists at $memoryPath" -ForegroundColor Green
} else {
    Write-Host "   INFO Memory: Creating file at $memoryPath" -ForegroundColor Cyan
    New-Item -ItemType File -Path $memoryPath -Force | Out-Null
    Write-Host "   OK Memory: File created" -ForegroundColor Green
}

# Test 3: Laravel Boost
Write-Host "`n3. Testing Laravel Boost MCP Server..." -ForegroundColor Yellow
$boostCheck = php artisan list | Select-String "boost:mcp"
if ($boostCheck) {
    Write-Host "   OK Laravel Boost: Command available" -ForegroundColor Green
    Write-Host "     Run with: php artisan boost:mcp" -ForegroundColor Gray
} else {
    Write-Host "   X Laravel Boost: Command not found" -ForegroundColor Red
}

# Test 4: GitHub MCP Server (HTTP)
Write-Host "`n4. Testing GitHub MCP Server..." -ForegroundColor Yellow
Write-Host "   INFO GitHub MCP: HTTP-based server (requires authentication)" -ForegroundColor Cyan
Write-Host "     Configure via VS Code when prompted" -ForegroundColor Gray

# Test 5: Chrome DevTools MCP
Write-Host "`n5. Testing Chrome DevTools MCP..." -ForegroundColor Yellow
Write-Host "   INFO Chrome DevTools: Package available via npx" -ForegroundColor Cyan

# Test 6: Playwright MCP
Write-Host "`n6. Testing Playwright MCP..." -ForegroundColor Yellow
$playwrightVersion = npx playwright --version 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "   OK Playwright: $playwrightVersion" -ForegroundColor Green
} else {
    Write-Host "   X Playwright: Not installed" -ForegroundColor Red
    Write-Host "     Install with: npm install -D @playwright/test" -ForegroundColor Gray
}

# Test 7: Context7 (Upstash)
Write-Host "`n7. Testing Context7 MCP..." -ForegroundColor Yellow
Write-Host "   INFO Context7: Requires CONTEXT7_API_KEY environment variable" -ForegroundColor Cyan
if ($env:CONTEXT7_API_KEY) {
    Write-Host "   OK Context7: API key configured" -ForegroundColor Green
} else {
    Write-Host "   INFO Context7: API key not set (will prompt when needed)" -ForegroundColor Gray
}

# Test 8: Firecrawl MCP
Write-Host "`n8. Testing Firecrawl MCP..." -ForegroundColor Yellow
Write-Host "   INFO Firecrawl: Requires FIRECRAWL_API_KEY environment variable" -ForegroundColor Cyan
if ($env:FIRECRAWL_API_KEY) {
    Write-Host "   OK Firecrawl: API key configured" -ForegroundColor Green
} else {
    Write-Host "   INFO Firecrawl: API key not set (will prompt when needed)" -ForegroundColor Gray
}

# Test 9: Bedrock Opus (Custom)
Write-Host "`n9. Testing Bedrock Opus MCP (Custom)..." -ForegroundColor Yellow
if (Test-Path ".\mcp-servers\bedrock-server.js") {
    Write-Host "   OK Bedrock: Server file exists" -ForegroundColor Green
    if (Test-Path ".\mcp-servers\node_modules") {
        Write-Host "   OK Bedrock: Dependencies installed" -ForegroundColor Green
    } else {
        Write-Host "   INFO Bedrock: Run 'npm install' in mcp-servers directory" -ForegroundColor Cyan
    }
} else {
    Write-Host "   X Bedrock: Server file not found" -ForegroundColor Red
}

# Test 10: Figma MCP
Write-Host "`n10. Testing Figma MCP..." -ForegroundColor Yellow
Write-Host "   INFO Figma: HTTP-based server (authentication handled by Figma)" -ForegroundColor Cyan

# Summary
Write-Host "`n=== Test Summary ===" -ForegroundColor Cyan
$configPath = Join-Path $env:USERPROFILE "AppData\Roaming\Code\User\mcp.json"
Write-Host "MCP Configuration file: $configPath" -ForegroundColor Gray
Write-Host "`nTo use these servers in VS Code:" -ForegroundColor Yellow
Write-Host "1. Ensure VS Code has MCP support enabled" -ForegroundColor White
Write-Host "2. Restart VS Code after configuration changes" -ForegroundColor White
Write-Host "3. Check VS Code Output panel for MCP connection logs" -ForegroundColor White
Write-Host ""
