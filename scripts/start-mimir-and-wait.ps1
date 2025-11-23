<#
.SYNOPSIS
  Start Mimir services and wait for Neo4j and Mimir health endpoints to be available.

.DESCRIPTION
  This helper wraps docker compose to bring up Neo4j and Mimir server, waits
  until Neo4j (HTTP) and Mimir (/health) respond, and then tails the Mimir logs.

.NOTES
  - Intended for local development on Windows PowerShell 5.1.
  - Does not delete data; it's safe to run repeatedly.

USAGE
  ./scripts/start-mimir-and-wait.ps1  # start services (neo4j + mimir)
  ./scripts/start-mimir-and-wait.ps1 -Services mimir-server,neo4j -TimeoutSeconds 180
#>

param(
  [string[]]$Services = @('neo4j','mimir-server'),
  [int]$TimeoutSeconds = 120
)

function Wait-HttpOk {
  param(
    [string]$Url,
    [int]$TimeoutSeconds = 60
  )

  $start = Get-Date
  while ((Get-Date) - $start -lt [TimeSpan]::FromSeconds($TimeoutSeconds)) {
    try {
      $resp = Invoke-WebRequest -Uri $Url -UseBasicParsing -Method Get -TimeoutSec 5 -ErrorAction Stop
      if ($resp.StatusCode -ge 200 -and $resp.StatusCode -lt 300) {
        Write-Host "[OK] $Url returned $($resp.StatusCode)"
        return $true
      }
    } catch {
      Write-Host "[WAIT] $Url not ready: $($_.Exception.Message)" -ForegroundColor Yellow
    }
    Start-Sleep -Seconds 2
  }
  Write-Host "[FAIL] Timed out waiting for $Url after $TimeoutSeconds seconds" -ForegroundColor Red
  return $false
}

Write-Host "Starting docker compose for services: $($Services -join ', ')"

# Detect if these service names exist in the repository root compose. If not, fall back to Mimir/docker-compose.yml
$rootServices = & docker compose config --services 2>$null | ForEach-Object { $_ }
$useFile = $null
foreach ($s in $Services) {
  if ($rootServices -notcontains $s) {
    # Use Mimir compose file
    $useFile = "Mimir/docker-compose.yml"
    break
  }
}

if ($useFile) {
  Write-Host "Using compose file: $useFile"
  docker compose -f $useFile up -d @($Services)
} else {
  docker compose up -d @($Services)
}

# Wait for Neo4j (HTTP) and Mimir
if ($Services -contains 'neo4j') {
  Write-Host "Waiting for Neo4j HTTP on http://localhost:7474 ..."
  $okNeo = Wait-HttpOk -Url 'http://localhost:7474' -TimeoutSeconds $TimeoutSeconds
  if (-not $okNeo) { Write-Host 'Neo4j failed to start in time' -ForegroundColor Red }
}

if ($Services -contains 'mimir_server' -or $Services -contains 'mimir-server') {
  Write-Host "Waiting for Mimir health on http://localhost:9042/health ..."
  $okMimir = Wait-HttpOk -Url 'http://localhost:9042/health' -TimeoutSeconds $TimeoutSeconds
  if (-not $okMimir) { Write-Host 'Mimir failed to become healthy in time' -ForegroundColor Red }
}

Write-Host 'Displaying last 200 lines of mimir_server container logs, then follow (Ctrl+C to stop tail)'
$containerName = 'mimir_server'
if ((docker ps -a --format "{{.Names}}" | Select-String -Pattern "^$containerName$") -ne $null) {
  docker logs --tail 200 $containerName
  docker logs -f $containerName
} else {
  # Fallback to `docker compose logs -f` using the selected compose file/service
  if ($useFile) {
    docker compose -f $useFile logs --tail 200 mimir-server
    docker compose -f $useFile logs -f mimir-server
  } else {
    docker compose logs --tail 200 mimir-server
    docker compose logs -f mimir-server
  }
}
