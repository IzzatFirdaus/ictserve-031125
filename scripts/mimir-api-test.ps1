# Test Mimir HTTP API
# Based on official Mimir MCP API documentation

param(
    [ValidateSet('health', 'memory', 'search', 'todo')]
    [string]$Test = 'health'
)

$baseUrl = "http://localhost:9042"

Write-Host "=== Testing Mimir API ===" -ForegroundColor Cyan

switch ($Test) {
    'health' {
        Write-Host "`nTesting health endpoint..." -ForegroundColor Yellow
        $response = Invoke-RestMethod -Uri "$baseUrl/health" -Method Get
        Write-Host "Status: $($response.status)" -ForegroundColor Green
        Write-Host "Version: $($response.version)" -ForegroundColor Gray
        Write-Host "Tools: $($response.tools)" -ForegroundColor Gray
    }
    
    'memory' {
        Write-Host "`nCreating memory node..." -ForegroundColor Yellow
        $body = @{
            method = "memory_node"
            params = @{
                action = "create"
                data = @{
                    type = "task"
                    content = "Test task from PowerShell"
                    metadata = @{
                        priority = "high"
                        created_by = "mimir-api-test"
                    }
                }
            }
        } | ConvertTo-Json -Depth 10
        
        $response = Invoke-RestMethod -Uri "$baseUrl/mcp" -Method Post -Body $body -ContentType "application/json"
        Write-Host "Response:" -ForegroundColor Green
        $response | ConvertTo-Json -Depth 5
    }
    
    'search' {
        Write-Host "`nSearching with vector search..." -ForegroundColor Yellow
        $body = @{
            method = "vector_search_nodes"
            params = @{
                query = "authentication tasks"
                limit = 5
            }
        } | ConvertTo-Json -Depth 10
        
        $response = Invoke-RestMethod -Uri "$baseUrl/mcp" -Method Post -Body $body -ContentType "application/json"
        Write-Host "Response:" -ForegroundColor Green
        $response | ConvertTo-Json -Depth 5
    }
    
    'todo' {
        Write-Host "`nCreating TODO..." -ForegroundColor Yellow
        $body = @{
            method = "todo"
            params = @{
                operation = "create"
                title = "Test TODO from PowerShell"
                priority = "high"
                status = "pending"
            }
        } | ConvertTo-Json -Depth 10
        
        $response = Invoke-RestMethod -Uri "$baseUrl/mcp" -Method Post -Body $body -ContentType "application/json"
        Write-Host "Response:" -ForegroundColor Green
        $response | ConvertTo-Json -Depth 5
    }
}

Write-Host "`n=== Test Complete ===" -ForegroundColor Green
