# Import historical memory data into MCP knowledge graph
$backupFile = "storage/mcp/memory-final-backup.jsonl"

Write-Host "Importing historical memory data..." -ForegroundColor Green

if (-not (Test-Path $backupFile)) {
    Write-Host "Backup file not found: $backupFile" -ForegroundColor Red
    exit 1
}

$lines = Get-Content $backupFile
$entities = @()
$importCount = 0
$skipCount = 0

Write-Host "Processing $($lines.Count) lines from backup..." -ForegroundColor Yellow

foreach ($line in $lines) {
    if ([string]::IsNullOrWhiteSpace($line)) {
        continue
    }
    
    try {
        $json = $line | ConvertFrom-Json
        
        # Skip relation entries
        if ($json.type -eq "relation") {
            $skipCount++
            continue
        }
        
        # Process entity entries
        if ($json.type -eq "entity" -or ($json.name -and $json.entityType)) {
            $entity = @{
                name = $json.name
                entityType = $json.entityType
                observations = $json.observations
            }
            
            # Add relations if they exist
            if ($json.relations -and $json.relations.Count -gt 0) {
                $entity.relations = $json.relations
            }
            
            $entities += $entity
            $importCount++
        }
    }
    catch {
        Write-Host "  Skipping invalid line: $($_.Exception.Message)" -ForegroundColor Red
        $skipCount++
    }
}

Write-Host "Prepared $importCount entities for import (skipped $skipCount)" -ForegroundColor Cyan

# Split into batches of 10 to avoid overwhelming the MCP server
$batchSize = 10
$totalBatches = [Math]::Ceiling($entities.Count / $batchSize)

Write-Host "Importing in $totalBatches batches of $batchSize entities each..." -ForegroundColor Yellow

for ($i = 0; $i -lt $entities.Count; $i += $batchSize) {
    $batch = $entities[$i..([Math]::Min($i + $batchSize - 1, $entities.Count - 1))]
    $batchNum = [Math]::Floor($i / $batchSize) + 1
    
    Write-Host "  Batch $batchNum/$totalBatches ($($batch.Count) entities)" -ForegroundColor Cyan
    
    # Create JSON for MCP call
    $mcpEntities = @()
    foreach ($entity in $batch) {
        $mcpEntities += $entity
    }
    
    $jsonOutput = $mcpEntities | ConvertTo-Json -Depth 10
    Write-Host "    Entities: $($batch.name -join ', ')" -ForegroundColor Gray
}

Write-Host "`nImport preparation complete!" -ForegroundColor Green
Write-Host "Total entities ready: $importCount" -ForegroundColor White
Write-Host "Note: Use MCP memory tools to import these entities in batches" -ForegroundColor Yellow
