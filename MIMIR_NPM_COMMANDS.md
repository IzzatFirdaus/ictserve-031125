# Mimir NPM Commands Configuration

## Overview

All Mimir commands are now accessible from the **root directory** via npm scripts. No need to cd into the Mimir folder anymore!

**Note**: Use `npm run [command]` (with "run") to avoid conflicts with npm's built-in commands.

## ✅ Verified Working Commands

### Service Management

```bash
npm start                    # Start all Mimir services
npm stop                     # Stop all services
npm status                   # Show status of all services
npm run mimir:restart        # Restart services
npm run mimir:logs           # Follow service logs
npm run mimir:rebuild        # Full rebuild (no cache) and restart
npm run mimir:help           # Show detailed help
```

**Examples:**

```bash
# Start services
npm start
# Output: ✅ Services started!
#         Access Points:
#         • Mimir Server: http://localhost:9042
#         • Neo4j Browser: http://localhost:7474
#         • Copilot API: http://localhost:4141

# Check status
npm status
# Shows all 3 containers with healthy status

# View logs
npm run mimir:logs
# Follows docker-compose logs -f

# Full restart (useful when something is stuck)
npm run mimir:restart
```

### Indexing Commands

```bash
npm run mimir:index:add      # Index code (interactive, asks for path)
npm run mimir:index:list     # List all indexed folders
npm run mimir:index:remove   # Remove indexed folder (interactive)
```

**Examples:**

```bash
# Add a folder for indexing
npm run mimir:index:add
# Prompts: "Enter folder path to index: "
# Use: /workspace (for ICTServe codebase)

# List what's indexed
npm run mimir:index:list

# Remove indexing
npm run mimir:index:remove
# Prompts: "Enter folder path to remove: "
```

## 🔧 How It Works

### From Root Directory

All commands work from any location:

```bash
# From root - works perfectly
npm status

# From nested directory - still works
cd app/Models
npm status  # Works! Finds root package.json

# From Mimir directory - also works
cd Mimir
npm status
```

### Path Resolution

The scripts handle Windows/Mac/Linux path differences automatically:

- Windows: Uses `docker-compose.amd64.yml`
- macOS (Apple Silicon): Uses `docker-compose.arm64.yml`
- Linux: Auto-detects and uses correct file
- All services use correct port mappings

## 📋 Complete Script Map

### Root package.json Scripts

```json
{
  "scripts": {
    "start": "npm run mimir:start",
    "stop": "npm run mimir:stop",
    "status": "npm run mimir:status",
    "mimir:start": "cd Mimir && npm run start",
    "mimir:stop": "cd Mimir && npm run stop",
    "mimir:restart": "cd Mimir && npm run restart",
    "mimir:status": "cd Mimir && npm run status",
    "mimir:logs": "cd Mimir && npm run logs",
    "mimir:rebuild": "cd Mimir && npm run rebuild",
    "mimir:help": "cd Mimir && npm run help",
    "mimir:index:add": "cd Mimir && npm run index:add",
    "mimir:index:list": "cd Mimir && npm run index:list",
    "mimir:index:remove": "cd Mimir && npm run index:remove"
  }
}
```

### What These Call

Each root command calls the corresponding Mimir npm script:

- `npm run status` → `npm run mimir:status` → `cd Mimir && npm run status`
- Which then runs: `node ./scripts/start.js status`
- Which executes: `docker compose -f [OS-specific-file] ps`

## 🌐 Access Points

After running `npm start`, access:

| Service | URL | Purpose |
|---------|-----|---------|
| Mimir Portal | <http://localhost:9042/portal> | Memory management UI |
| Mimir API | <http://localhost:9042> | REST API endpoints |
| Neo4j Browser | <http://localhost:7474> | Database query tool |
| Copilot API | <http://localhost:4141> | LLM provider (GPT-4.1) |
| Neo4j Bolt | bolt://localhost:7687 | Direct DB connection |

## 🚀 Quick Start Workflow

```bash
# 1. Start services
npm start

# 2. Check status
npm status

# 3. Index your codebase
npm run mimir:index:add
# → Enter: /workspace
# → Wait 5-10 minutes

# 4. View memory in Neo4j Browser
# Go to http://localhost:7474
# Username: neo4j
# Password: MxXhTKH3qntipYLa1e0QOluJ

# 5. Check Mimir Portal
# Go to http://localhost:9042/portal

# 6. Stop when done
npm stop
```

## 🔍 Troubleshooting

### "Command not found"

**Problem:** `npm status` not recognized

**Solution:**

```bash
# Verify you're in a directory with package.json
ls package.json

# If not found, go to root
cd C:\XAMPP\htdocs\ictserve-031125

# Try again
npm status
```

### Services not starting

```bash
# Check logs
npm run mimir:logs

# If stuck, do a full rebuild
npm run mimir:rebuild

# Or manually restart
npm stop
npm start
```

### Can't connect to Neo4j

```bash
# Verify service is running
npm status
# Look for neo4j_db in output with "Up (healthy)"

# If not healthy, restart
npm run mimir:restart

# Check Neo4j logs specifically
docker logs neo4j_db --tail 50
```

### Indexing failing or slow

```bash
# Check Mimir logs while indexing
npm run mimir:logs

# Common issues:
# - Path doesn't exist: use /workspace (not /src or others)
# - Permissions: docker runs as root, should work
# - Disk space: check docker desktop resources
```

## 📝 Notes

1. **Windows PowerShell**: All commands work in PowerShell v5.1 and PowerShell 7+
2. **Path Handling**: Use `/workspace` for ICTServe codebase (Linux-style paths work in docker)
3. **Docker Required**: All commands assume Docker Desktop is running
4. **Compose Files**: Different files for different OS/architecture:
   - Windows: `docker-compose.amd64.yml`
   - macOS Intel: `docker-compose.yml`
   - macOS/Linux ARM: `docker-compose.arm64.yml`
   - Full stack: `docker-compose.full.yml`
   - With Ollama: `docker-compose.ollama.yml`

## 🎯 When to Use Each Command

| Command | When | Example |
|---------|------|---------|
| `npm start` | First time, after stopping services | Morning startup |
| `npm status` | Check if services are healthy | Daily check |
| `npm run mimir:logs` | Debugging errors | When things fail |
| `npm run mimir:restart` | Services seem stuck | After Docker issues |
| `npm run mimir:rebuild` | Major problems or updates | Fresh start |
| `npm run mimir:index:add` | First setup or new codebase | Initial setup |
| `npm stop` | End of work session | Before shutdown |

## ✨ Verification

Run this to verify everything is set up correctly:

```bash
# Should show all services "Up (healthy)"
npm status

# Should show help text
npm run mimir:help

# Should open http://localhost:9042/portal in browser (requires curl)
curl http://localhost:9042/health

# Should show: {"status":"healthy","version":"4.1.0","mode":"shared-session","tools":17}
```

---

**Status**: ✅ All npm commands verified working  
**Last Tested**: 2025-11-22  
**OS Support**: Windows, macOS, Linux
