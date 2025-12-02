# Test Memory Server MCP Protocol
# Diagnoses JSON parse errors

Write-Host "Testing Memory Server..." -ForegroundColor Cyan

$nodePath = "C:\Program Files\nodejs\node.exe"
$serverPath = "C:\Users\exatf\AppData\Roaming\npm\node_modules\@modelcontextprotocol\server-memory\dist\index.js"

# Test 1: Check files exist
Write-Host "`n[1] Checking files..." -ForegroundColor Yellow
if (Test-Path $nodePath) {
    Write-Host "  ✓ Node.js found: $nodePath" -ForegroundColor Green
} else {
    Write-Host "  ✗ Node.js NOT found: $nodePath" -ForegroundColor Red
    exit 1
}

if (Test-Path $serverPath) {
    Write-Host "  ✓ Memory server found: $serverPath" -ForegroundColor Green
} else {
    Write-Host "  ✗ Memory server NOT found: $serverPath" -ForegroundColor Red
    Write-Host "  Install with: npm install -g @modelcontextprotocol/server-memory" -ForegroundColor Yellow
    exit 1
}

# Test 2: Start server and capture output
Write-Host "`n[2] Starting server (5 second test)..." -ForegroundColor Yellow
$process = Start-Process -FilePath $nodePath -ArgumentList $serverPath -NoNewWindow -PassThru -RedirectStandardOutput "test-output.txt" -RedirectStandardError "test-error.txt"

Start-Sleep -Seconds 5

if (!$process.HasExited) {
    $process.Kill()
    Write-Host "  ✓ Server started successfully" -ForegroundColor Green
} else {
    Write-Host "  ✗ Server exited immediately (exit code: $($process.ExitCode))" -ForegroundColor Red
}

# Test 3: Check output
Write-Host "`n[3] Checking output..." -ForegroundColor Yellow

if (Test-Path "test-output.txt") {
    $stdout = Get-Content "test-output.txt" -Raw
    if ($stdout) {
        Write-Host "  STDOUT (first 500 chars):" -ForegroundColor Cyan
        Write-Host "  $($stdout.Substring(0, [Math]::Min(500, $stdout.Length)))" -ForegroundColor Gray
        
        # Check for JSON
        if ($stdout -match '^\s*\{') {
            Write-Host "  ✓ Output starts with JSON" -ForegroundColor Green
        } else {
            Write-Host "  ✗ Output does NOT start with JSON (this is the problem!)" -ForegroundColor Red
        }
    } else {
        Write-Host "  (no stdout)" -ForegroundColor Gray
    }
    Remove-Item "test-output.txt" -ErrorAction SilentlyContinue
}

if (Test-Path "test-error.txt") {
    $stderr = Get-Content "test-error.txt" -Raw
    if ($stderr) {
        Write-Host "`n  STDERR (first 500 chars):" -ForegroundColor Cyan
        Write-Host "  $($stderr.Substring(0, [Math]::Min(500, $stderr.Length)))" -ForegroundColor Gray
    }
    Remove-Item "test-error.txt" -ErrorAction SilentlyContinue
}

# Test 4: Recommendations
Write-Host "`n[4] Recommendations:" -ForegroundColor Yellow

Write-Host "  Option A: Reinstall memory server" -ForegroundColor Cyan
Write-Host "    npm uninstall -g @modelcontextprotocol/server-memory" -ForegroundColor Gray
Write-Host "    npm install -g @modelcontextprotocol/server-memory@latest" -ForegroundColor Gray

Write-Host "`n  Option B: Use alternative (filesystem-based)" -ForegroundColor Cyan
Write-Host "    Install: npm install -g @modelcontextprotocol/server-filesystem" -ForegroundColor Gray
Write-Host "    Update config.toml to use filesystem server" -ForegroundColor Gray

Write-Host "`n  Option C: Disable memory server" -ForegroundColor Cyan
Write-Host "    Set disabled = true in config.toml" -ForegroundColor Gray
Write-Host "    Use Laravel Boost and Sequential Thinking only" -ForegroundColor Gray

Write-Host "`nTest complete." -ForegroundColor Green
