# Test Mimir MCP Server with correct stdio invocation
# This script tests the native stdio MCP communication with Mimir

Write-Host "Testing Mimir MCP Server..." -ForegroundColor Cyan

# Test 1: Simple initialize request
Write-Host "`n[Test 1] Testing initialize request..." -ForegroundColor Yellow

$init_request = @{
    jsonrpc = "2.0"
    id = "init-test"
    method = "initialize"
    params = @{}
} | ConvertTo-Json

Write-Host "Sending: $init_request" -ForegroundColor Gray

# Send test via docker
$result = ($init_request + "`n" + '{"jsonrpc":"2.0","id":"list","method":"tools/list"}' + "`n") | docker exec -i mimir_server node build/index.js 2>&1 | Select-String -Pattern '{"jsonrpc"' | Select-Object -First 2

if ($result) {
    Write-Host "✅ Mimir responded!" -ForegroundColor Green
    Write-Host $result
} else {
    Write-Host "⏳ No JSON responses yet (Mimir may still be initializing)..." -ForegroundColor Yellow
}

Write-Host "`nTest complete. Check docker logs for details:" -ForegroundColor Cyan
Write-Host "docker logs mimir_server" -ForegroundColor Gray
