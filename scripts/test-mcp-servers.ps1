# Test MCP Server Connections
Write-Host "Testing MCP Server Connections..." -ForegroundColor Cyan

$servers = @(
    @{Name="sequentialthinking"; Command="npx"; Args=@("-y", "@modelcontextprotocol/server-sequential-thinking", "--version")},
    @{Name="memory"; Command="npx"; Args=@("-y", "@modelcontextprotocol/server-memory", "--version")},
    @{Name="chrome-devtools"; Command="npx"; Args=@("chrome-devtools-mcp@latest", "--version")},
    @{Name="playwright"; Command="npx"; Args=@("@playwright/mcp@latest", "--version")},
    @{Name="laravel-boost"; Command="php"; Args=@("artisan", "boost:mcp", "--help")}
)

foreach ($server in $servers) {
    Write-Host "`nTesting $($server.Name)..." -ForegroundColor Yellow
    try {
        $result = & $server.Command $server.Args 2>&1
        if ($LASTEXITCODE -eq 0 -or $result) {
            Write-Host "OK $($server.Name)" -ForegroundColor Green
        } else {
            Write-Host "FAIL $($server.Name)" -ForegroundColor Red
        }
    } catch {
        Write-Host "ERROR $($server.Name)" -ForegroundColor Red
    }
}

Write-Host "`nTest complete" -ForegroundColor Cyan
