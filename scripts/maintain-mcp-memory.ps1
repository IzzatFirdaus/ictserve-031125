#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Maintenance utilities for MCP Memory Server
    
.DESCRIPTION
    Provides maintenance operations for the MCP memory knowledge graph:
    - Analyze memory usage and statistics
    - Validate memory file integrity
    - Optimize memory file (remove duplicates, compact)
    - Generate memory reports
    
.PARAMETER Action
    Maintenance action: analyze, validate, optimize, report
    
.PARAMETER OutputFormat
    Output format for reports: console, json, csv
    
.EXAMPLE
    .\scripts\maintain-mcp-memory.ps1 -Action analyze
    
.EXAMPLE
    .\scripts\maintain-mcp-memory.ps1 -Action report -OutputFormat json
#>

param(
    [ValidateSet("analyze", "validate", "optimize", "report")]
    [string]$Action = "analyze",
    
    [ValidateSet("console", "json", "csv")]
    [string]$OutputFormat = "console"
)

# Configuration
$MemoryFile = "storage\mcp\memory.jsonl"

Write-Host "🔧 ICTServe MCP Memory Maintenance" -ForegroundColor Cyan
Write-Host "===================================" -ForegroundColor Cyan

# Check if memory file exists
if (-not (Test-Path $MemoryFile)) {
    Write-Host "❌ Memory file not found: $MemoryFile" -ForegroundColor Red
    Write-Host "💡 Run backup script to create initial memory file" -ForegroundColor Yellow
    exit 1
}

# Load and parse memory data
Write-Host "📖 Loading memory data..." -ForegroundColor Yellow
$MemoryData = @()
$LineNumber = 0
$ParseErrors = @()

try {
    Get-Content $MemoryFile | ForEach-Object {
        $LineNumber++
        if ($_.Trim()) {
            try {
                $MemoryData += $_ | ConvertFrom-Json
            } catch {
                $ParseErrors += @{
                    Line = $LineNumber
                    Content = $_
                    Error = $_.Exception.Message
                }
            }
        }
    }
} catch {
    Write-Host "❌ Failed to load memory file: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Loaded $($MemoryData.Count) memory entries" -ForegroundColor Green

if ($ParseErrors.Count -gt 0) {
    Write-Host "⚠️  Found $($ParseErrors.Count) parse errors" -ForegroundColor Yellow
}

# Execute requested action
switch ($Action) {
    "analyze" {
        Write-Host "" -ForegroundColor White
        Write-Host "📊 Memory Analysis:" -ForegroundColor Cyan
        
        # File statistics
        $FileInfo = Get-Item $MemoryFile
        $FileSizeKB = [math]::Round($FileInfo.Length / 1KB, 2)
        $FileSizeMB = [math]::Round($FileInfo.Length / 1MB, 2)
        
        Write-Host "   File size: $FileSizeKB KB ($FileSizeMB MB)" -ForegroundColor White
        Write-Host "   Total lines: $LineNumber" -ForegroundColor White
        Write-Host "   Valid entries: $($MemoryData.Count)" -ForegroundColor White
        Write-Host "   Parse errors: $($ParseErrors.Count)" -ForegroundColor White
        Write-Host "   Last modified: $($FileInfo.LastWriteTime)" -ForegroundColor White
        
        # Entity analysis
        $Entities = $MemoryData | Where-Object { $_.type -eq "entity" }
        $Relations = $MemoryData | Where-Object { $_.type -eq "relation" }
        
        Write-Host "" -ForegroundColor White
        Write-Host "📈 Content Analysis:" -ForegroundColor Cyan
        Write-Host "   Entities: $($Entities.Count)" -ForegroundColor White
        Write-Host "   Relations: $($Relations.Count)" -ForegroundColor White
        
        if ($Entities.Count -gt 0) {
            # Entity types
            $EntityTypes = $Entities | Group-Object entityType | Sort-Object Count -Descending
            Write-Host "" -ForegroundColor White
            Write-Host "🏷️  Entity Types:" -ForegroundColor Cyan
            $EntityTypes | ForEach-Object {
                Write-Host "   $($_.Name): $($_.Count)" -ForegroundColor White
            }
            
            # Observation statistics
            $TotalObservations = ($Entities | ForEach-Object { $_.observations.Count } | Measure-Object -Sum).Sum
            $AvgObservations = [math]::Round($TotalObservations / $Entities.Count, 2)
            
            Write-Host "" -ForegroundColor White
            Write-Host "📝 Observations:" -ForegroundColor Cyan
            Write-Host "   Total observations: $TotalObservations" -ForegroundColor White
            Write-Host "   Average per entity: $AvgObservations" -ForegroundColor White
        }
        
        if ($Relations.Count -gt 0) {
            # Relation types
            $RelationTypes = $Relations | Group-Object relationType | Sort-Object Count -Descending
            Write-Host "" -ForegroundColor White
            Write-Host "🔗 Relation Types:" -ForegroundColor Cyan
            $RelationTypes | ForEach-Object {
                Write-Host "   $($_.Name): $($_.Count)" -ForegroundColor White
            }
        }
    }
    
    "validate" {
        Write-Host "" -ForegroundColor White
        Write-Host "🔍 Memory Validation:" -ForegroundColor Cyan
        
        $ValidationErrors = @()
        
        # Check for parse errors
        if ($ParseErrors.Count -gt 0) {
            $ValidationErrors += "Parse errors found at lines: $($ParseErrors.Line -join ', ')"
        }
        
        # Check for duplicate entities
        $Entities = $MemoryData | Where-Object { $_.type -eq "entity" }
        $DuplicateNames = $Entities | Group-Object name | Where-Object { $_.Count -gt 1 }
        if ($DuplicateNames) {
            $ValidationErrors += "Duplicate entity names: $($DuplicateNames.Name -join ', ')"
        }
        
        # Check for orphaned relations
        $Relations = $MemoryData | Where-Object { $_.type -eq "relation" }
        $EntityNames = $Entities.name
        $OrphanedRelations = $Relations | Where-Object { 
            $_.from -notin $EntityNames -or $_.to -notin $EntityNames 
        }
        if ($OrphanedRelations) {
            $ValidationErrors += "Orphaned relations found: $($OrphanedRelations.Count) relations reference non-existent entities"
        }
        
        if ($ValidationErrors.Count -eq 0) {
            Write-Host "✅ Memory validation passed - no issues found" -ForegroundColor Green
        } else {
            Write-Host "❌ Memory validation failed:" -ForegroundColor Red
            $ValidationErrors | ForEach-Object {
                Write-Host "   • $_" -ForegroundColor Red
            }
        }
    }
    
    "optimize" {
        Write-Host "" -ForegroundColor White
        Write-Host "⚡ Memory Optimization:" -ForegroundColor Cyan
        
        # Create backup before optimization
        $BackupFile = "storage\mcp\memory_pre_optimize_$(Get-Date -Format 'yyyyMMdd_HHmmss').jsonl"
        Copy-Item $MemoryFile $BackupFile -Force
        Write-Host "💾 Created backup: $BackupFile" -ForegroundColor Green
        
        $OriginalCount = $MemoryData.Count
        $OptimizedData = @()
        
        # Remove duplicates (keep latest)
        $UniqueEntities = @{}
        $UniqueRelations = @{}
        
        foreach ($Item in $MemoryData) {
            if ($Item.type -eq "entity") {
                $UniqueEntities[$Item.name] = $Item
            } elseif ($Item.type -eq "relation") {
                $Key = "$($Item.from)-$($Item.relationType)-$($Item.to)"
                $UniqueRelations[$Key] = $Item
            }
        }
        
        $OptimizedData += $UniqueEntities.Values
        $OptimizedData += $UniqueRelations.Values
        
        # Save optimized data
        $OptimizedData | ConvertTo-Json -Compress | Set-Content $MemoryFile -Encoding UTF8
        
        $OptimizedCount = $OptimizedData.Count
        $Savings = $OriginalCount - $OptimizedCount
        $SavingsPercent = [math]::Round(($Savings / $OriginalCount) * 100, 2)
        
        Write-Host "✅ Optimization completed:" -ForegroundColor Green
        Write-Host "   Original entries: $OriginalCount" -ForegroundColor White
        Write-Host "   Optimized entries: $OptimizedCount" -ForegroundColor White
        Write-Host "   Removed duplicates: $Savings ($SavingsPercent%)" -ForegroundColor White
    }
    
    "report" {
        # Generate comprehensive report
        $Report = @{
            Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
            File = @{
                Path = $MemoryFile
                SizeKB = [math]::Round((Get-Item $MemoryFile).Length / 1KB, 2)
                LastModified = (Get-Item $MemoryFile).LastWriteTime.ToString("yyyy-MM-dd HH:mm:ss")
            }
            Statistics = @{
                TotalLines = $LineNumber
                ValidEntries = $MemoryData.Count
                ParseErrors = $ParseErrors.Count
                Entities = ($MemoryData | Where-Object { $_.type -eq "entity" }).Count
                Relations = ($MemoryData | Where-Object { $_.type -eq "relation" }).Count
            }
            EntityTypes = @()
            RelationTypes = @()
        }
        
        # Add entity types
        $Entities = $MemoryData | Where-Object { $_.type -eq "entity" }
        if ($Entities.Count -gt 0) {
            $Report.EntityTypes = $Entities | Group-Object entityType | Sort-Object Count -Descending | ForEach-Object {
                @{ Type = $_.Name; Count = $_.Count }
            }
        }
        
        # Add relation types
        $Relations = $MemoryData | Where-Object { $_.type -eq "relation" }
        if ($Relations.Count -gt 0) {
            $Report.RelationTypes = $Relations | Group-Object relationType | Sort-Object Count -Descending | ForEach-Object {
                @{ Type = $_.Name; Count = $_.Count }
            }
        }
        
        # Output report
        switch ($OutputFormat) {
            "json" {
                $ReportFile = "storage\mcp\memory_report_$(Get-Date -Format 'yyyyMMdd_HHmmss').json"
                $Report | ConvertTo-Json -Depth 10 | Set-Content $ReportFile -Encoding UTF8
                Write-Host "📄 Report saved: $ReportFile" -ForegroundColor Green
            }
            "csv" {
                # Create CSV summary
                $CsvData = @()
                $CsvData += [PSCustomObject]@{
                    Metric = "File Size (KB)"
                    Value = $Report.File.SizeKB
                }
                $CsvData += [PSCustomObject]@{
                    Metric = "Total Entries"
                    Value = $Report.Statistics.ValidEntries
                }
                $CsvData += [PSCustomObject]@{
                    Metric = "Entities"
                    Value = $Report.Statistics.Entities
                }
                $CsvData += [PSCustomObject]@{
                    Metric = "Relations"
                    Value = $Report.Statistics.Relations
                }
                
                $CsvFile = "storage\mcp\memory_report_$(Get-Date -Format 'yyyyMMdd_HHmmss').csv"
                $CsvData | Export-Csv $CsvFile -NoTypeInformation -Encoding UTF8
                Write-Host "📊 CSV report saved: $CsvFile" -ForegroundColor Green
            }
            default {
                Write-Host "" -ForegroundColor White
                Write-Host "📄 Memory Report:" -ForegroundColor Cyan
                Write-Host "   Generated: $($Report.Timestamp)" -ForegroundColor White
                Write-Host "   File: $($Report.File.Path)" -ForegroundColor White
                Write-Host "   Size: $($Report.File.SizeKB) KB" -ForegroundColor White
                Write-Host "   Entities: $($Report.Statistics.Entities)" -ForegroundColor White
                Write-Host "   Relations: $($Report.Statistics.Relations)" -ForegroundColor White
                
                if ($Report.EntityTypes.Count -gt 0) {
                    Write-Host "" -ForegroundColor White
                    Write-Host "🏷️  Top Entity Types:" -ForegroundColor Cyan
                    $Report.EntityTypes | Select-Object -First 5 | ForEach-Object {
                        Write-Host "   $($_.Type): $($_.Count)" -ForegroundColor White
                    }
                }
            }
        }
    }
}

Write-Host "" -ForegroundColor White
Write-Host "✅ Memory maintenance completed!" -ForegroundColor Green
