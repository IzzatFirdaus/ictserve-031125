# MCP Memory Server JSON Validator
# Validates memory.jsonl file format

param(
    [string]$FilePath = "storage\mcp\memory.jsonl"
)

Write-Host ""
Write-Host "MCP Memory Server JSON Validator" -ForegroundColor Cyan
Write-Host ("=" * 60)

$FullPath = Join-Path (Join-Path $PSScriptRoot "..") $FilePath

if (-not (Test-Path $FullPath)) {
    Write-Host "ERROR: File not found: $FullPath" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Checking file: $FullPath" -ForegroundColor Yellow

# Read file
$lines = Get-Content $FullPath
$totalLines = $lines.Count
Write-Host "Total lines: $totalLines"

# Validate each line
$validationErrors = @()
$lineNum = 0

foreach ($line in $lines) {
    $lineNum++
    
    if ([string]::IsNullOrWhiteSpace($line)) {
        Write-Host "WARNING: Line $lineNum is empty (will be skipped)" -ForegroundColor Yellow
        continue
    }
    
    try {
        $json = $line | ConvertFrom-Json
        
        # Validate required fields
        if (-not $json.name) {
            $validationErrors += "Line $lineNum - Missing 'name' field"
        }
        if (-not $json.entityType) {
            $validationErrors += "Line $lineNum - Missing 'entityType' field"
        }
        if (-not $json.observations) {
            $validationErrors += "Line $lineNum - Missing 'observations' field"
        }
        
        Write-Host "OK: Line $lineNum valid: $($json.name)" -ForegroundColor Green
        
    } catch {
        $errorMsg = $_.Exception.Message
        $validationErrors += "Line $lineNum - Invalid JSON - $errorMsg"
        Write-Host "ERROR: Line $lineNum - Invalid JSON" -ForegroundColor Red
        Write-Host "   Error: $errorMsg" -ForegroundColor DarkRed
        
        # Show the problematic line (truncated)
        $preview = if ($line.Length -gt 100) { $line.Substring(0, 100) + "..." } else { $line }
        Write-Host "   Content: $preview" -ForegroundColor DarkGray
    }
}

Write-Host ""
Write-Host ("=" * 60)

if ($validationErrors.Count -eq 0) {
    Write-Host "SUCCESS: All lines valid! Memory file is ready to use." -ForegroundColor Green
    Write-Host ""
    Write-Host "Summary:" -ForegroundColor Cyan
    Write-Host "   Total lines: $totalLines"
    Write-Host "   Valid entries: $totalLines"
    Write-Host "   Errors: 0"
    exit 0
} else {
    Write-Host "FAILED: Found $($validationErrors.Count) error(s):" -ForegroundColor Red
    foreach ($validationError in $validationErrors) {
        Write-Host "   - $validationError" -ForegroundColor Red
    }
    
    Write-Host ""
    Write-Host "Summary:" -ForegroundColor Cyan
    Write-Host "   Total lines: $totalLines"
    Write-Host "   Valid entries: $($totalLines - $validationErrors.Count)"
    Write-Host "   Errors: $($validationErrors.Count)"
    
    Write-Host ""
    Write-Host "TIP: Fix the invalid JSON lines and run this script again." -ForegroundColor Yellow
    Write-Host "     See docs/mcp/MCP_MEMORY_USAGE_EXAMPLES.md for correct format." -ForegroundColor Yellow
    exit 1
}
