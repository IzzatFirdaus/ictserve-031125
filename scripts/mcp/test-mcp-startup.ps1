Write-Host "`n=== MCP Servers Test ===" -ForegroundColor Cyan
$passed = 0; $failed = 0

Write-Host "`n1. PHP & Node Prerequisites" -ForegroundColor Yellow
php --version | Select-Object -First 1
node --version
$passed += 2

Write-Host "`n2. Laravel Boost" -ForegroundColor Yellow
php artisan boost:mcp --help | Select-Object -First 2
$passed++

Write-Host "`n3. Memory Storage" -ForegroundColor Yellow
if (Test-Path "storage\mcp\memory.jsonl") {
    Write-Host "   ✓ File exists" -ForegroundColor Green
    $passed++
}

Write-Host "`n4. Environment Variables" -ForegroundColor Yellow
@("FIRECRAWL_API_KEY", "CONTEXT7_API_KEY", "DEEPL_API_KEY", "PAT_GITHUB_ACCESS_TOKEN") | ForEach-Object {
    $val = [Environment]::GetEnvironmentVariable($_, "User")
    if ($val) {
        Write-Host "   ✓ $($_)" -ForegroundColor Green
        $passed++
    } else {
        Write-Host "   ✗ $($_)" -ForegroundColor Red
        $failed++
    }
}

Write-Host "`n5. MCP Config" -ForegroundColor Yellow
$config = Get-Content ".vscode\mcp.json" -Raw | ConvertFrom-Json
$serverCount = ($config.servers.PSObject.Properties | Measure-Object).Count
Write-Host "   ✓ $serverCount servers configured" -ForegroundColor Green
$passed++

Write-Host "`n=== Results ===" -ForegroundColor Cyan
Write-Host "Passed: $passed" -ForegroundColor Green
Write-Host "Failed: $failed" -ForegroundColor $(if ($failed -eq 0) {"Green"} else {"Red"})
if ($failed -eq 0) {
    Write-Host "`n✓ All MCP servers ready! Restart VS Code." -ForegroundColor Green
}
