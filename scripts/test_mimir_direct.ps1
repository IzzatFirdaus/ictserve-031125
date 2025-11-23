# Test Mimir MCP Server - Direct stdio test
Write-Host "Testing Mimir MCP Server (stdio mode)..." -ForegroundColor Cyan

# Test 1: Initialize
$test1 = @{
    jsonrpc = "2.0"
    id = "test-1"
    method = "initialize"
    params = @{}
}

# Test 2: List tools
$test2 = @{
    jsonrpc = "2.0"
    id = "test-2"
    method = "tools/list"
    params = @{}
}

Write-Host "`n[Test 1] Sending initialize request..." -ForegroundColor Yellow
$init_json = $test1 | ConvertTo-Json
Write-Host $init_json -ForegroundColor Gray

# Send both requests in sequence
$requests = @(
    ($test1 | ConvertTo-Json),
    ($test2 | ConvertTo-Json)
) -join "`n"

Write-Host "`nSending requests to Mimir..." -ForegroundColor Yellow
try {
    # Pipe requests directly to docker exec using PowerShell pipeline
    $result = $requests | docker exec -i mimir_server node build/index.js 2>&1

    Write-Host "`nMimir Output (first 30 lines):" -ForegroundColor Cyan
    $lines = $result -split "`n"
    $lines[0..29] | ForEach-Object { Write-Host $_ }

    # Look for JSON responses
    $jsonLines = $lines | Where-Object { $_ -match '^\{.*"jsonrpc"' }
    if ($jsonLines) {
        Write-Host "`nJSON Responses Found:" -ForegroundColor Green
        $jsonLines | ForEach-Object { Write-Host $_ }
    } else {
        Write-Host "`n⚠️  No JSON-RPC responses found in output" -ForegroundColor Yellow
    }

} catch {
    Write-Host "Error: $_" -ForegroundColor Red
}

Write-Host "`nTest complete!" -ForegroundColor Cyan
