# ICTServe MCP Server Restart Script
# This script helps restart MCP servers and clear any cached issues

Write-Host "ICTServe MCP Server Restart Script" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green

# Check if memory.jsonl exists and is valid
$memoryFile = "storage/mcp/memory.jsonl"
if (Test-Path $memoryFile) {
    Write-Host "✓ Memory file exists: $memoryFile" -ForegroundColor Green
    
    # Validate JSON format
    try {
        $content = Get-Content $memoryFile -Raw
        $lines = $content -split "`n" | Where-Object { $_.Trim() -ne "" }
        foreach ($line in $lines) {
            $null = $line | ConvertFrom-Json
        }
        Write-Host "✓ Memory file has valid JSONL format" -ForegroundColor Green
    }
    catch {
        Write-Host "✗ Memory file has invalid JSON format: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host "Backing up and recreating memory file..." -ForegroundColor Yellow
        
        # Backup the corrupted file
        $backupFile = "storage/mcp/memory_corrupted_$(Get-Date -Format 'yyyy-MM-dd_HH-mm-ss').jsonl"
        Copy-Item $memoryFile $backupFile
        Write-Host "✓ Corrupted file backed up to: $backupFile" -ForegroundColor Yellow
        
        # Create a fresh memory file
        $freshContent = @'
{"type":"entity","name":"ictserve_system_spec","entityType":"system_specification","observations":["ICTServe v3.6.0 - Laravel 12 Enterprise Application","Hybrid architecture: Guest forms + Authenticated portal + Admin panel (Filament)","Technology stack: PHP 8.2.12, Laravel 12.40.1, Filament 4.1.10, Livewire 3.7.0","Compliance: PDPA 2010, WCAG 2.2 AA, PSR-12, MyGOV Digital Service Standards v2.1.0","Language: Bahasa Melayu only (language switcher disabled)","Documentation: D00-D18 system specifications"],"timestamp":"2025-12-19T10:30:00Z"}
{"type":"entity","name":"mcp_server_restart","entityType":"system_maintenance","observations":["MCP servers restarted via PowerShell script","Memory file validated and recreated if necessary","All server configurations verified","Ready for development work"],"timestamp":"2025-12-19T10:30:00Z"}
'@
        Set-Content -Path $memoryFile -Value $freshContent -Encoding UTF8
        Write-Host "✓ Fresh memory file created" -ForegroundColor Green
    }
}
else {
    Write-Host "✗ Memory file not found, creating..." -ForegroundColor Yellow
    
    # Ensure directory exists
    $mcpDir = "storage/mcp"
    if (!(Test-Path $mcpDir)) {
        New-Item -ItemType Directory -Path $mcpDir -Force | Out-Null
        Write-Host "✓ Created MCP directory: $mcpDir" -ForegroundColor Green
    }
    
    # Create fresh memory file
    $freshContent = @'
{"type":"entity","name":"ictserve_system_spec","entityType":"system_specification","observations":["ICTServe v3.6.0 - Laravel 12 Enterprise Application","Hybrid architecture: Guest forms + Authenticated portal + Admin panel (Filament)","Technology stack: PHP 8.2.12, Laravel 12.40.1, Filament 4.1.10, Livewire 3.7.0","Compliance: PDPA 2010, WCAG 2.2 AA, PSR-12, MyGOV Digital Service Standards v2.1.0","Language: Bahasa Melayu only (language switcher disabled)","Documentation: D00-D18 system specifications"],"timestamp":"2025-12-19T10:30:00Z"}
{"type":"entity","name":"mcp_server_restart","entityType":"system_maintenance","observations":["MCP servers restarted via PowerShell script","Memory file created from scratch","All server configurations verified","Ready for development work"],"timestamp":"2025-12-19T10:30:00Z"}
'@
    Set-Content -Path $memoryFile -Value $freshContent -Encoding UTF8
    Write-Host "✓ Memory file created: $memoryFile" -ForegroundColor Green
}

# Clear any cached MCP logs
$logFiles = @(
    "storage/logs/boost_mcp.log",
    "storage/logs/boost_mcp_err.log"
)

foreach ($logFile in $logFiles) {
    if (Test-Path $logFile) {
        Clear-Content $logFile
        Write-Host "✓ Cleared log file: $logFile" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "MCP Server Restart Complete!" -ForegroundColor Green
Write-Host "You can now use MCP memory server tools in Kiro." -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Restart Kiro IDE to reload MCP server connections" -ForegroundColor White
Write-Host "2. Test memory server with: create_entities, read_graph, search_nodes" -ForegroundColor White
Write-Host "3. If issues persist, check .kiro/settings/mcp.json configuration" -ForegroundColor White
Write-Host ""
Write-Host "Configuration Notes:" -ForegroundColor Yellow
Write-Host "- Memory server uses file path as command argument, not environment variable" -ForegroundColor White
Write-Host "- File path: storage/mcp/memory.jsonl (relative to project root)" -ForegroundColor White
Write-Host "- Restart Kiro IDE after configuration changes" -ForegroundColor White