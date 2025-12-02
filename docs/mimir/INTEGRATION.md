# Mimir Integration for ICTServe

## Overview

Mimir provides AI-powered memory and knowledge graph capabilities for ICTServe development. It enables persistent memory across AI agent sessions, semantic code search, and multi-agent orchestration.

## Quick Start

### 1. Start Mimir Services

```powershell
# Start all Mimir services (Neo4j, Mimir Server, Copilot API)
.\scripts\mimir\start.ps1
```

**First-time setup**: The script automatically creates `.env` from `env.example` and configures the workspace path.

### 2. Check Status

```powershell
# Verify services are running
.\scripts\mimir\status.ps1
```

**Expected output**:

- Mimir Server: Running on port 9042
- Neo4j: Running on ports 7474 (HTTP) and 7687 (Bolt)
- Copilot API: Running on port 4141

### 3. Index ICTServe Project

```powershell
# Index project for semantic search (without embeddings - faster)
.\scripts\mimir\index-project.ps1

# With embeddings for semantic search (slower, requires Ollama)
.\scripts\mimir\index-project.ps1 -WithEmbeddings
```

**Note**: Large projects may take several minutes to index. Watch the logs to see progress.

### 4. Stop Services

```powershell
# Stop all Mimir services
.\scripts\mimir\stop.ps1
```

## Access Points

| Service | URL | Credentials |
|---------|-----|-------------|
| **Mimir Portal** | <http://localhost:9042/portal> | None (dev mode) |
| **Neo4j Browser** | <http://localhost:7474> | user: `neo4j`, pass: `password` |
| **MCP API** | <http://localhost:9042/mcp> | None (dev mode) |
| **Health Check** | <http://localhost:9042/health> | None |

## Configuration

### Environment Variables

Mimir configuration is stored in `Mimir/.env`. Key settings:

```bash
# Workspace (auto-configured by start script)
HOST_WORKSPACE_ROOT=D:\\xampp\\htdocs\\ictserve-031125

# LLM Provider (Copilot API by default)
MIMIR_DEFAULT_PROVIDER=copilot
MIMIR_LLM_API=http://copilot-api:4141/v1
MIMIR_DEFAULT_MODEL=gpt-4.1

# Embeddings (for semantic search)
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_MODEL=mxbai-embed-large
MIMIR_EMBEDDINGS_DIMENSIONS=1024

# Neo4j
NEO4J_PASSWORD=password
```

### Switching LLM Providers

**Option 1: Copilot API** (default - requires GitHub Copilot license):

```bash
MIMIR_DEFAULT_PROVIDER=copilot
MIMIR_LLM_API=http://copilot-api:4141/v1
```

**Option 2: Local Ollama** (offline, fully local):

```bash
MIMIR_DEFAULT_PROVIDER=ollama
MIMIR_LLM_API=http://ollama:11434
MIMIR_DEFAULT_MODEL=qwen2.5-coder
```

**Option 3: OpenAI API** (cloud-based):

```bash
MIMIR_DEFAULT_PROVIDER=openai
MIMIR_LLM_API=https://api.openai.com/v1
MIMIR_LLM_API_KEY=sk-...
MIMIR_DEFAULT_MODEL=gpt-4
```

After changing providers, restart Mimir:

```powershell
.\scripts\mimir\stop.ps1
.\scripts\mimir\start.ps1
```

## Features

### 1. Persistent Memory

Mimir stores AI agent conversations and context in Neo4j graph database:

- **Tasks**: TODOs, feature requests, bug reports
- **Files**: Indexed codebase with semantic search
- **Relationships**: Links between tasks, files, and concepts
- **Context**: Conversation history and agent decisions

### 2. Semantic Code Search

Search your codebase by meaning, not just keywords:

```bash
# Via Mimir Portal
http://localhost:9042/portal

# Via MCP API
curl -X POST http://localhost:9042/mcp \
  -H "Content-Type: application/json" \
  -d '{
    "jsonrpc": "2.0",
    "method": "tools/call",
    "params": {
      "name": "vector_search_nodes",
      "arguments": {
        "query": "authentication middleware",
        "types": ["file"],
        "limit": 10
      }
    },
    "id": 1
  }'
```

### 3. Multi-Agent Orchestration

Mimir supports multi-agent workflows:

- **PM Agent**: Task decomposition and planning
- **Worker Agent**: Implementation and execution
- **QC Agent**: Quality control and verification

### 4. File Indexing

Automatically watches and indexes your codebase:

**Supported file types**:

- Languages: PHP, JavaScript, TypeScript, Python, Java, Go, Rust, C/C++, C#, Ruby
- Markup: Markdown, HTML, XML
- Data: JSON, YAML, SQL
- Styles: CSS, SCSS

**Automatically skipped**:

- Images, videos, audio files
- Archives, binaries, compiled files
- `node_modules/`, `vendor/`, `dist/`, `build/`
- Files in `.gitignore`

## Integration with ICTServe

### Use Cases

1. **Code Documentation**: Ask AI about ICTServe codebase
   - "How does the dual approval workflow work?"
   - "Show me all Livewire components"
   - "Explain the asset loan process"

2. **Feature Development**: Track feature implementation
   - Create TODOs for new features
   - Link tasks to related files
   - Track dependencies between tasks

3. **Bug Tracking**: Persistent bug investigation
   - Store bug reports with context
   - Link bugs to affected files
   - Track resolution steps

4. **Knowledge Management**: Build project knowledge graph
   - Document architectural decisions
   - Link related components
   - Track technical debt

### Example Workflow

```powershell
# 1. Start Mimir
.\scripts\mimir\start.ps1

# 2. Index ICTServe project
.\scripts\mimir\index-project.ps1

# 3. Open Mimir Portal
Start-Process "http://localhost:9042/portal"

# 4. Search codebase
# In Portal: "Show me all Filament resources"

# 5. Create TODO
# In Portal: Create task "Implement email notification for overdue loans"

# 6. Link files to task
# In Portal: Add context files (app/Mail/*, app/Jobs/*)

# 7. Stop Mimir when done
.\scripts\mimir\stop.ps1
```

## Troubleshooting

### Services won't start

```powershell
# Check Docker is running
docker info

# Check for port conflicts
docker compose ps

# View logs
Push-Location Mimir
docker compose logs
Pop-Location
```

### Can't connect to Neo4j

```powershell
# Wait for Neo4j to fully start (30-60 seconds)
Push-Location Mimir
docker compose logs neo4j
Pop-Location

# Check it's responding
curl http://localhost:7474
```

### Indexing fails

```powershell
# Check workspace path in .env
Get-Content Mimir\.env | Select-String "HOST_WORKSPACE_ROOT"

# Verify path is accessible
Test-Path (Get-Content Mimir\.env | Select-String "HOST_WORKSPACE_ROOT" | ForEach-Object { $_.Line.Split('=')[1] })

# Check Mimir logs
Push-Location Mimir
docker compose logs mimir-server
Pop-Location
```

### Embeddings not working

```powershell
# Check embeddings configuration
Get-Content Mimir\.env | Select-String "EMBEDDINGS"

# If using Ollama, check it's running
curl http://localhost:11434/api/tags

# Pull embedding model if missing
Push-Location Mimir
docker exec -it ollama_server ollama pull mxbai-embed-large
Pop-Location
```

## Data Persistence

Mimir data is stored in:

```
Mimir/
├── data/neo4j/     # Database files (tasks, relationships, etc.)
├── logs/           # Application logs
└── copilot-data/   # GitHub authentication tokens
```

**✓ Stopping containers doesn't delete data!** Your tasks and knowledge graph persist.

To reset Mimir data:

```powershell
Push-Location Mimir
docker compose down
Remove-Item -Recurse -Force data\neo4j
docker compose up -d
Pop-Location
```

## References

- **Official Documentation**: `Mimir/docs/README-official.md`
- **Quick Start Guide**: `Mimir/docs/getting-started/QUICKSTART.md`
- **IDE Integration**: `Mimir/docs/guides/IDE_INTEGRATION_GUIDE.md`
- **Memory Guide**: `Mimir/docs/guides/MEMORY_GUIDE.md`
- **File Indexing**: `Mimir/docs/architecture/FILE_INDEXING_SYSTEM.md`

## Support

- **GitHub Issues**: <https://github.com/orneryd/Mimir/issues>
- **ICTServe Documentation**: `docs/`
- **Mimir Documentation**: `Mimir/docs/`
