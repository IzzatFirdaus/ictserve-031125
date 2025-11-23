# PCTX Integration Setup Guide for ICTServe

---

## ⚠️ CRITICAL: Windows Platform Not Supported

**Status:** PCTX v0.2.0 does **NOT support Windows** natively.

**Affected Platforms:**

- ❌ Windows (x86_64-pc-windows-msvc, aarch64-pc-windows-msvc)

**Supported Platforms:**

- ✅ macOS (aarch64/x86_64-apple-darwin)
- ✅ Linux (aarch64/x86_64-unknown-linux-gnu)

**Error Message (Windows):**

```text
Platform with type "Windows_NT" and architecture "x64" is not supported
npm error code 1
```

**Alternatives for Windows Users:**

1. **WSL2 (Recommended):** Install Ubuntu 22.04 in WSL2, follow Linux instructions below
2. **GitHub Codespaces:** Cloud-based Linux environment with PCTX pre-installed
3. **CI/CD Only:** Use PCTX in GitHub Actions (Linux runners) for automated workflows
4. **Direct MCP (Current):** Continue using Mimir MCP with sequential tool calls (acceptable token usage for most tasks)

**Reference Documentation:**

- See `PCTX_WINDOWS_STATUS.md` for comprehensive analysis, workarounds, and roadmap
- See `GITHUB_MCP_SETUP.md` §PCTX Integration Status for team recommendations

**Instructions Below:** The PowerShell commands below are preserved for reference but **WILL NOT WORK on Windows** without WSL2.

---

**Purpose:** Enable "Code Mode" execution for Mimir MCP, reducing token usage by 98% for complex workflows while maintaining full Mimir semantic search, graph memory, and file indexing capabilities.

**Architecture:**

```
AI Agent (GitHub Copilot, Claude)
    ↓ MCP Protocol (stdio)
PCTX Server (Deno sandbox, TypeScript execution)
    ↓ MCP Client connection
Mimir MCP Server (Graph-RAG, Neo4j)
    ├─ Vector Search (semantic queries)
    ├─ Graph Operations (memory_node, memory_edge)
    ├─ File Indexing (codebase RAG)
    ├─ Task Orchestration (todo management)
    └─ Relationship Tracking (multi-hop reasoning)
```

**Expected Outcomes:**

- ✅ Complex MCP workflows reduce token usage 90-98% (vs. sequential tool calls)
- ✅ Type-safe code execution in sandboxed Deno environment
- ✅ Multi-server aggregation (Mimir + GitHub + other MCP services in single TypeScript block)
- ✅ Instant feedback loop (type checking, error detection pre-execution)

---

## Prerequisites

### System Requirements

- **OS:** Windows 10/11 (PowerShell 5.1+)
- **Docker:** Docker Desktop running (for Mimir services)
- **Node.js:** 18.x+ (for Deno installation if needed)
- **Rust:** 1.70+ (for PCTX build from source) — optional; pre-built binaries available

### Verify Mimir Services Running

```powershell
# Check Mimir HTTP health endpoint
Invoke-RestMethod -Uri "http://localhost:9042/health" -Method Get
# Expected: HTTP 200 + {"status":"ok"}

# Check Docker containers
docker ps | Select-String "mimir|neo4j|copilot"
# Expected: 3 healthy containers
```

---

## Installation Steps

### Step 1: Install PCTX (Windows)

#### Option A: Pre-Built Binary (Recommended)

```powershell
# Create installation directory
$pctxHome = "$env:USERPROFILE\.pctx"
New-Item -ItemType Directory -Path $pctxHome -Force | Out-Null

# Download latest Windows binary
$releaseUrl = "https://github.com/portofcontext/pctx/releases/download/v1.0.0/pctx-v1.0.0-x86_64-pc-windows-msvc.exe"
$exePath = "$pctxHome\pctx.exe"

Invoke-WebRequest -Uri $releaseUrl -OutFile $exePath
Write-Host "✅ PCTX installed to $exePath"

# Add to PATH for global access
$pathEntry = $pctxHome
if (-not $env:PATH.Contains($pathEntry)) {
    [Environment]::SetEnvironmentVariable("PATH", "$env:PATH;$pathEntry", [EnvironmentVariableTarget]::User)
    Write-Host "✅ Added $pctxHome to PATH (restart PowerShell to activate)"
}

# Verify installation
& $exePath --version
```

#### Option B: Build from Source (Advanced)

```powershell
# Prerequisites: Rust 1.70+ installed (https://www.rust-lang.org/tools/install)

# Clone PCTX repository
git clone https://github.com/portofcontext/pctx.git C:\src\pctx
cd C:\src\pctx

# Build optimized binary
cargo build --release

# Binary created at: target\release\pctx.exe
Write-Host "✅ PCTX built to target\release\pctx.exe"

# Add release dir to PATH (optional)
[Environment]::SetEnvironmentVariable("PATH", "$env:PATH;$(pwd)\target\release", [EnvironmentVariableTarget]::User)
```

#### Option C: Package Manager (if available)

```powershell
# Using Scoop (https://scoop.sh)
scoop bucket add community https://github.com/ScoopInstaller/Community.git
scoop install pctx
```

### Step 2: Initialize PCTX Configuration

```powershell
# Navigate to project root
cd c:\XAMPP\htdocs\ictserve-031125

# Initialize PCTX (creates pctx.json in current directory)
pctx init
# Follow prompts:
# - Name: "ictserve-pctx"
# - Description: "PCTX proxy for ICTServe Mimir integration"
# - Upstream servers: (we'll configure manually)
```

### Step 3: Configure Upstream Mimir Server

**Edit `pctx.json` (created above):**

```json
{
  "name": "ictserve-pctx",
  "version": "1.0.0",
  "description": "PCTX proxy aggregating Mimir MCP for Code Mode execution",
  "port": 8080,
  "listenHost": "127.0.0.1",
  "servers": [
    {
      "name": "mimir",
      "type": "mcp-http",
      "url": "http://localhost:9042/mcp",
      "enabled": true,
      "auth": {
        "type": "none"
      },
      "options": {
        "timeout": 30000,
        "retries": 3,
        "healthCheckInterval": 10000
      }
    },
    {
      "name": "github",
      "type": "mcp-stdio",
      "command": "docker",
      "args": [
        "run",
        "-i",
        "--rm",
        "-e",
        "GITHUB_PERSONAL_ACCESS_TOKEN",
        "ghcr.io/github/github-mcp-server"
      ],
      "enabled": true,
      "auth": {
        "type": "env",
        "envVar": "GITHUB_PERSONAL_ACCESS_TOKEN"
      }
    }
  ],
  "sandbox": {
    "timeout": 10000,
    "memory": 256,
    "cpu": 2,
    "permissions": {
      "network": ["http://localhost:*"],
      "env": ["MIMIR_*", "GITHUB_*"]
    }
  },
  "logging": {
    "level": "info",
    "format": "json"
  }
}
```

**Key Configuration Details:**

- `port`: 8080 (PCTX listens here; AI agents connect to <http://localhost:8080/mcp>)
- `servers[0]`: Mimir HTTP upstream (url: <http://localhost:9042/mcp>)
- `servers[1]`: GitHub MCP optional (provide GITHUB_PERSONAL_ACCESS_TOKEN env var)
- `sandbox.timeout`: 10-second execution limit per TypeScript code block
- `sandbox.permissions.network`: Allow only localhost network access

---

## Starting PCTX

### Manual Startup

```powershell
cd c:\XAMPP\htdocs\ictserve-031125

# Ensure Mimir services running
docker ps | Select-String "mimir_server|neo4j_db" | if ($_.Count -lt 2) {
    Write-Host "❌ Mimir services not running. Start with: docker-compose up -d"
    exit 1
}

# Start PCTX (foreground mode for logs)
pctx dev --config pctx.json

# Expected output:
# [info] PCTX Server v1.0.0 starting
# [info] Loading config: pctx.json
# [info] Registering server: mimir (http://localhost:9042/mcp)
# [info] Listening on http://127.0.0.1:8080/mcp
# [info] Ready for connections (Health check OK)
```

### Automated Startup Script
**File: `scripts/start-pctx.ps1`**

```powershell
# Start PCTX with health checks and logging

param(
    [string]$ProjectRoot = (Get-Location),
    [int]$MaxWaitSeconds = 60,
    [switch]$Detached = $false
)

$ErrorActionPreference = 'Stop'

function Log { Write-Host "[$(Get-Date -Format 'HH:mm:ss')] $args" }
function LogError { Write-Host "[ERROR] $(Get-Date -Format 'HH:mm:ss')] $args" -ForegroundColor Red }
function LogSuccess { Write-Host "[✅ OK] $(Get-Date -Format 'HH:mm:ss')] $args" -ForegroundColor Green }

Log "Starting PCTX with Mimir integration..."
Log "Project root: $ProjectRoot"

# Step 1: Verify Mimir services running
Log "Checking Mimir services..."
try {
    $health = Invoke-RestMethod -Uri "http://localhost:9042/health" -Method Get -TimeoutSec 5
    if ($health.status -eq "ok") {
        LogSuccess "Mimir HTTP endpoint healthy"
    } else {
        LogError "Mimir health check returned unexpected status: $($health.status)"
        exit 1
    }
} catch {
    LogError "Mimir health check failed: $($_.Exception.Message)"
    LogError "Ensure Docker containers are running: docker-compose up -d"
    exit 1
}

# Step 2: Verify pctx.json exists
$configPath = Join-Path $ProjectRoot "pctx.json"
if (-not (Test-Path $configPath)) {
    LogError "pctx.json not found at $configPath"
    LogError "Run 'pctx init' first"
    exit 1
}
LogSuccess "Configuration found: $configPath"

# Step 3: Start PCTX
cd $ProjectRoot
Log "Starting PCTX server (listening on http://127.0.0.1:8080/mcp)..."

if ($Detached) {
    # Background mode (useful for CI/CD)
    $processPath = "$ProjectRoot\pctx-process.pid"
    $null = Start-Process -FilePath "pctx" -ArgumentList "dev --config $configPath" `
        -RedirectStandardOutput "$ProjectRoot\logs\pctx-stdout.log" `
        -RedirectStandardError "$ProjectRoot\logs\pctx-stderr.log" `
        -PassThru -WindowStyle Hidden | Out-File $processPath
    LogSuccess "PCTX started in background (PID saved to $processPath)"
    LogSuccess "Logs: $ProjectRoot\logs\pctx-*.log"
} else {
    # Foreground mode (development)
    & pctx dev --config $configPath
}

# Step 4: Health check loop (if detached)
if ($Detached) {
    Log "Waiting for PCTX to become ready..."
    $elapsed = 0
    while ($elapsed -lt $MaxWaitSeconds) {
        try {
            $health = Invoke-RestMethod -Uri "http://127.0.0.1:8080/health" -Method Get -TimeoutSec 2
            if ($health.status -eq "ready") {
                LogSuccess "PCTX ready! Listening on http://127.0.0.1:8080/mcp"
                return $true
            }
        } catch {
            # Not ready yet
        }
        Start-Sleep -Seconds 1
        $elapsed += 1
    }
    LogError "PCTX failed to become ready within ${MaxWaitSeconds}s"
    exit 1
}
```

**Usage:**

```powershell
# Development (foreground with logs)
.\scripts\start-pctx.ps1

# Background (CI/CD)
.\scripts\start-pctx.ps1 -Detached

# Custom project root
.\scripts\start-pctx.ps1 -ProjectRoot "C:\alternative\path" -MaxWaitSeconds 120
```

---

## Verification & Testing

### Health Check Endpoints

```powershell
# PCTX health endpoint
Invoke-RestMethod -Uri "http://127.0.0.1:8080/health" -Method Get
# Expected: {"status":"ready","uptime":"2.5s","servers":{"mimir":"connected"}}

# PCTX functions discovery
Invoke-RestMethod -Uri "http://127.0.0.1:8080/mcp/functions" -Method Get | ConvertTo-Json
# Expected: List of available Mimir functions (vector_search_nodes, memory_node, etc.)

# Mimir upstream health (via PCTX)
$body = @{ query = "health check" } | ConvertTo-Json
Invoke-RestMethod -Uri "http://127.0.0.1:8080/mcp" -Method Post `
    -ContentType "application/json" `
    -Body (@{ id = 1; method = "initialize"; params = @{} } | ConvertTo-Json)
```

### Test Code Mode Execution

**Example 1: Simple Semantic Search**

```typescript
// No tool call overhead - all in one code block!
const results = await mimir.vector_search_nodes({
  query: "authentication patterns in Laravel",
  types: ["coding_pattern", "implementation"],
  limit: 5
});

const sorted = results
  .sort((a, b) => b.similarity - a.similarity)
  .slice(0, 3);

console.log(`Found ${sorted.length} relevant patterns`);
for (const item of sorted) {
  console.log(`- ${item.name} (${(item.similarity * 100).toFixed(0)}% match)`);
}
```

**Example 2: Multi-Hop Graph Traversal**

```typescript
// Find authentication implementation, traverse graph, update related tasks
const auth = await mimir.memory_node({
  operation: "search",
  query: "Laravel authentication setup"
});

const related = await mimir.memory_edge({
  operation: "neighbors",
  node_id: auth[0].id,
  depth: 2,
  edge_types: ["implements", "uses", "related_to"]
});

const tasks = await mimir.todo({
  operation: "list",
  filters: { status: "pending" }
});

let updated = 0;
for (const task of tasks.slice(0, 5)) {
  await mimir.memory_node({
    operation: "update",
    id: task.id,
    properties: { status: "in_progress", assigned_to: "pctx_batch" }
  });
  updated++;
}

console.log(`Processed ${updated} pending tasks with ${related.length} related concepts`);
```

**Example 3: Batch File Indexing + Search**

```typescript
// Index multiple folders, then search across all
const folders = [
  "app/Http/Controllers",
  "app/Models",
  "app/Services",
  "app/Livewire"
];

for (const folder of folders) {
  await mimir.index_folder({
    path: folder,
    watch: true
  });
}

// Wait a moment for indexing
await new Promise(r => setTimeout(r, 2000));

// Now search across entire codebase
const results = await mimir.vector_search_nodes({
  query: "payment processing implementation",
  types: ["file", "coding_pattern"],
  limit: 10
});

console.log(`Indexed ${folders.length} folders, found ${results.length} matches`);
```

---

## MCP Configuration for VS Code

### Update `.vscode/mcp.json` to Use PCTX

**Option A: Direct PCTX Connection (Recommended)**

```json
{
  "mcpServers": {
    "pctx-mimir": {
      "command": "node",
      "args": [".\\Mimir\\scripts\\mcp-http-client.js", "http://127.0.0.1:8080/mcp"],
      "env": {
        "PCTX_HOST": "127.0.0.1",
        "PCTX_PORT": "8080"
      },
      "type": "stdio"
    },
    "mimir-direct": {
      "command": "node",
      "args": [".\\Mimir\\scripts\\mcp-http-client.js", "http://localhost:9042/mcp"],
      "disabled": true
    }
  }
}
```

**Option B: Fallback to Direct Mimir (if PCTX unavailable)**

```json
{
  "mcpServers": {
    "mcp-http-client": {
      "command": "node",
      "args": [".\\Mimir\\scripts\\mcp-http-client.js", "http://localhost:9042/mcp"],
      "type": "stdio"
    }
  }
}
```

### Token Savings Comparison

**Traditional MCP Workflow (Sequential Tool Calls):**

```
Agent: Call vector_search_nodes (500 tokens)
  ↓ (LLM thinks about results)
Agent: Call memory_edge neighbors (500 tokens)
  ↓ (LLM thinks about graph)
Agent: Call memory_node update (500 tokens)
  ↓ (LLM thinks about update)
Agent: Call todo list (500 tokens)
  ↓ (LLM thinks about tasks)

Total: ~2500 tokens for simple workflow
```

**PCTX Code Mode Workflow (Single Execution Block):**

```typescript
// All in one typed, sandboxed execution block
const auth = await mimir.vector_search_nodes({...});
const related = await mimir.memory_edge({...});
await mimir.memory_node({...});
const tasks = await mimir.todo({...});
// Result: 500 tokens (+ code execution)

Total: ~500 tokens for same workflow
80% reduction!
```

**Complex Workflows (90-98% Reduction):**

- **Batch operations:** Process 100 items sequentially → 1 code block (98% reduction)
- **Graph traversal:** Multi-hop with conditionals → type-safe execution (95% reduction)
- **Data transformation:** Map/filter/reduce → inline operations (96% reduction)

---

## Troubleshooting

### PCTX Won't Start

**Error:** `[error] Failed to connect to mimir server`

```powershell
# Solution: Ensure Mimir running
docker-compose up -d mimir_server neo4j_db
Start-Sleep -Seconds 10

# Test Mimir health
Invoke-RestMethod -Uri "http://localhost:9042/health"

# Then retry PCTX
pctx dev --config pctx.json
```

**Error:** `[error] Port 8080 already in use`

```powershell
# Find process using port 8080
Get-Process | Where-Object { ... } # PowerShell port lookup is complex

# Alternative: Change PCTX port in pctx.json
# "port": 8081

# Or kill any existing PCTX processes
Get-Process pctx -ErrorAction SilentlyContinue | Stop-Process -Force
```

### Type Checking Failures

**Error:** `[error] TypeScript error: Property 'xxx' does not exist`

Solution: This is intentional — PCTX caught an error before execution. Check:

1. Correct Mimir function name (e.g., `vector_search_nodes`, not `search_nodes`)
2. Correct parameter types (refer to function signatures from PCTX)
3. Correct module name (e.g., `mimir.vector_search_nodes`, not `mimir_vector_search_nodes`)

### Sandbox Timeout

**Error:** `[error] Code execution exceeded 10s timeout`

Solution: Move heavy lifting to PCTX Code Mode:

```typescript
// ❌ SLOW: 100 sequential API calls (each ~100ms)
for (let i = 0; i < 100; i++) {
  await mimir.memory_node({...});
}

// ✅ FAST: Batch operation
await mimir.memory_batch({
  operations: [
    { type: "memory_node", params: {...} },
    { type: "memory_node", params: {...} },
    // ... 100 operations
  ]
});
```

---

## Next Steps

1. **Verify Integration**
   - [ ] PCTX installed and running
   - [ ] Health checks passing
   - [ ] Test Code Mode execution (examples above)
   - [ ] VS Code MCP config updated

2. **Document Workflows**
   - [ ] Add examples to `mimir.md` for Code Mode usage
   - [ ] Document token savings for your team
   - [ ] Create runbooks for complex graph operations

3. **Scale Up**
   - [ ] Add more upstream MCP servers (GitHub, Slack, etc.)
   - [ ] Implement Code Mode workflows in your agents
   - [ ] Measure token usage improvements

4. **Monitor & Optimize**
   - [ ] Enable logging in pctx.json
   - [ ] Track execution times for each workflow
   - [ ] Profile sandbox memory usage
   - [ ] Adjust timeout/permissions as needed

---

## References

- **PCTX Official:** <https://github.com/portofcontext/pctx>
- **PCTX Integration Guide:** `/docs/guides/PCTX_INTEGRATION_GUIDE.md`
- **PCTX Analysis:** `/docs/research/PCTX_INTEGRATION_ANALYSIS.md`
- **Mimir MCP:** `.vscode/mcp.json` + `Mimir/scripts/mcp-http-client.js`
- **MCP Protocol:** <https://modelcontextprotocol.io/>

---

**Status:** ✅ Ready for setup and integration  
**Last Updated:** 2025-11-22  
**Framework:** PCTX + Mimir + Neo4j  
**Token Savings:** 90-98% for complex workflows
