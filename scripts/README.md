# ICTServe Scripts Directory

This directory contains organized utility scripts for ICTServe development, testing, and deployment.

## Directory Structure

### `/dev` - Development Scripts
Scripts for starting, stopping, and managing the development environment.

**Files:**

- `start-dev.*` - Start development servers (Laravel, Reverb, Vite)
- `stop-dev.*` - Stop development servers
- `reverb-start.*` - Start Laravel Reverb WebSocket server
- `switch-env.*` - Switch between environment configurations
- `start-pctx-stack.ps1` - Start the full development stack

**Usage:**

```powershell
# Windows PowerShell
.\scripts\dev\start-dev.ps1

# Linux/Mac Bash
./scripts/dev/start-dev.sh
```

### `/testing` - Testing & Quality Assurance
Scripts for running tests and static analysis.

**Files:**

- `run-test.ps1` - Run specific tests
- `run-tests.js` - Run test suites
- `test-changed.ps1` - Run tests for changed files
- `*larastan*` - PHPStan/Larastan static analysis scripts
- `update-test-attributes*.php` - Update test attributes

**Usage:**

```powershell
.\scripts\testing\run-test.ps1 -filter "HelpdeskTest"
```

### `/translations` - Localization Scripts
Scripts for managing translations (English/Malay).

**Files:**

- `check-missing-translations.ps1` - Find missing translation keys
- `extract-translations.*` - Extract translatable strings
- `scan-hardcoded-strings.php` - Find hardcoded strings
- `clean-translation-keys.php` - Clean up translation files

**Usage:**

```powershell
.\scripts\translations\check-missing-translations.ps1
php scripts/translations/extract-translations.php
```

### `/mcp` - Model Context Protocol
Scripts for MCP server management and testing.

**Files:**

- `mcp-health-check.*` - Check MCP server health
- `test-mcp*.ps1` - Test MCP functionality
- `setup-mcp-env-windows.ps1` - Setup MCP environment
- `verify-mcp-config.ps1` - Verify MCP configuration
- `mcp-resources-shim.cjs` - MCP resources shim
- `mcp-stdio-wrapper.js` - MCP stdio wrapper

**Usage:**

```powershell
.\scripts\mcp\mcp-health-check.ps1
.\scripts\mcp\test-mcp-servers.ps1
```

### `/memory` - Knowledge Graph & Memory
Scripts for managing the MCP memory/knowledge graph system.

**Files:**

- `export-memory-graph.php` - Export memory graph
- `convert-memory-jsonl.php` - Convert memory format
- `validate-memory-json.ps1` - Validate memory data
- `verify-memory-import.php` - Verify memory imports
- `execute-memory-import-and-cleanup.ps1` - Import and cleanup workflow

**Usage:**

```bash
php scripts/memory/export-memory-graph.php
.\scripts\memory\validate-memory-json.ps1
```

### `/neo4j` - Neo4j Database Scripts
Cypher queries and PHP scripts for Neo4j knowledge graph operations.

**Files:**

- `*.cypher` - Cypher query files for data import
- `import-*-to-neo4j.php` - Import data to Neo4j
- `create-documentation-entities.php` - Create doc entities
- `verify-neo4j-consolidation.php` - Verify data integrity

**Usage:**

```bash
php scripts/neo4j/import-memory-to-neo4j.php
```

### `/database` - Database Utilities
Scripts for database operations and maintenance.

**Files:**

- `check_admin.php` - Check admin user status
- `check-migrations.php` - Verify migrations
- `reset-password.php` - Reset user passwords

**Usage:**

```bash
php scripts/database/reset-password.php user@example.com
php scripts/database/check_admin.php
```

### `/setup` - Initial Setup Scripts
Scripts for initial project setup and configuration.

**Files:**

- `setup-apache-alias.ps1` - Configure Apache alias
- `setup-vhost.ps1` - Setup virtual host
- `setup-github-token.ps1` - Configure GitHub token
- `verify-github-token.ps1` - Verify GitHub token
- `fix-npm-windows.ps1` - Fix npm issues on Windows

**Usage:**

```powershell
.\scripts\setup\setup-vhost.ps1
.\scripts\setup\setup-github-token.ps1
```

### `/maintenance` - Maintenance & Cleanup
Scripts for code maintenance and cleanup tasks.

**Files:**

- `cleanup-*.ps1` - Various cleanup operations
- `fix-filament-issues.php` - Fix Filament-related issues
- `fix-markdown-*.php` - Fix markdown formatting

**Usage:**

```powershell
.\scripts\maintenance\cleanup-docs.ps1
php scripts/maintenance/fix-markdown-lint-rules.php
```

### `/docker` - Docker Environment
Scripts for Docker-based development environment.

**Files:**

- `start-dev.ps1` - Start Docker containers
- `stop-dev.ps1` - Stop Docker containers
- `artisan.ps1` - Run Artisan commands in container
- `composer.ps1` - Run Composer in container
- `npm.ps1` - Run npm in container
- `memory-mcp.ps1` - MCP memory server in Docker

**Usage:**

```powershell
.\scripts\docker\start-dev.ps1
.\scripts\docker\artisan.ps1 migrate
.\scripts\docker\composer.ps1 install
```

### `/laragon` - Laragon Environment
Scripts specific to Laragon development environment.

**Files:**

- `setup-laragon.ps1` - Setup Laragon environment
- `export-example.ps1` - Export configuration examples
- `drop_helpdesk_table.php` - Database maintenance

### `/tools` - Development Tools
Miscellaneous development tools and utilities.

**Files:**

- `reverb-quickstart.ps1` - Quick Reverb setup
- `verify-*-fixes.*` - Verification scripts
- `fix-filament-imports.bat` - Fix Filament imports

### `/supervisor` - Process Supervision
Supervisor configuration files for process management.

**Files:**

- `reverb.conf` - Reverb supervisor configuration

### `/nova` - Nova AI Testing
Scripts for testing with Nova AI agent.

**Files:**

- `nova_act_*.py` - Nova AI test scripts
- `test_*.py` - Test implementations

### `/deprecated` - Deprecated Scripts
Old scripts kept for reference but no longer actively used.

## Common Workflows

### Starting Development

```powershell
# Full development stack
.\scripts\dev\start-dev.ps1

# Individual services
php artisan serve
php artisan reverb:start
npm run dev
php artisan queue:work
```

### Running Tests

```bash
# All tests
php artisan test

# Specific tests
.\scripts\testing\run-test.ps1 -filter "HelpdeskTest"

# Changed files only
.\scripts\testing\test-changed.ps1

# Static analysis
.\scripts\testing\check-larastan-ready.ps1
```

### Translation Management

```powershell
# Check for missing translations
.\scripts\translations\check-missing-translations.ps1

# Extract new translations
php scripts/translations/extract-translations.php

# Scan for hardcoded strings
php scripts/translations/scan-hardcoded-strings.php
```

### MCP Operations

```powershell
# Health check
.\scripts\mcp\mcp-health-check.ps1

# Test memory server
.\scripts\mcp\test-memory-server.ps1

# Verify configuration
.\scripts\mcp\verify-mcp-config.ps1
```

### Memory & Knowledge Graph

```bash
# Export memory graph
php scripts/memory/export-memory-graph.php

# Validate memory data
.\scripts\memory\validate-memory-json.ps1

# Import to Neo4j
php scripts/neo4j/import-memory-to-neo4j.php
```

## Platform-Specific Notes

### Windows (PowerShell)

- Use `.ps1` scripts
- May require execution policy: `Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass`
- Recommended for Windows development

### Linux/Mac (Bash)

- Use `.sh` scripts
- Ensure executable: `chmod +x script.sh`
- Works with Git Bash on Windows

### Cross-Platform

- `.php` scripts work on all platforms with PHP installed
- `.js` scripts require Node.js
- `.py` scripts require Python 3

## Services Managed by Development Scripts

1. **Redis Server** (WSL/Native) - Cache, Queue, Session storage
2. **Laravel Server** (Port 8000) - Main application
3. **Laravel Reverb** (Port 6001) - WebSocket server for real-time features
4. **Queue Worker** - Background job processing
5. **Vite Dev Server** (Port 5173) - Hot Module Replacement for frontend

## Quick Start

1. Install dependencies:

   ```bash
   composer install
   npm install
   ```

2. Configure environment:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Setup database:

   ```bash
   php artisan migrate --seed
   ```

4. Start development:

   ```powershell
   .\scripts\dev\start-dev.ps1
   ```

5. Verify services:
   - Redis: `redis-cli ping` → `PONG`
   - Laravel: <http://127.0.0.1:8000>
   - Reverb: Check terminal for "Reverb server started"
   - Queue: Check terminal for "Processing:" messages
   - Vite: <http://127.0.0.1:5173>

## Contributing

When adding new scripts:

1. Place in appropriate category directory
2. Use consistent naming: `action-target.extension`
3. Add documentation to this README
4. Include usage examples in script comments
5. Follow PSR-12 for PHP scripts
6. Use strict mode for PowerShell scripts

## Related Documentation

- [D00: System Overview](../docs/D00_SYSTEM_OVERVIEW.md)
- [D01: Development Plan](../docs/D01_SYSTEM_DEVELOPMENT_PLAN.md)
- [D11: Technical Design](../docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md)
- [Tech Stack](../.kiro/steering/tech.md)
- [MCP Configuration](../.kiro/steering/mcp.md)
- [Development Startup Guide](./DEV-STARTUP-GUIDE.md)
- [Redis Setup](../docs/redis/redis-setup.md)

## Troubleshooting

### Scripts Won't Execute (Windows)

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
```

### Permission Denied (Linux/Mac)

```bash
chmod +x scripts/**/*.sh
```

### Redis Connection Failed

Check Redis is running:

```bash
redis-cli ping
```

### Port Already in Use

Kill processes on ports 8000, 6001, or 5173:

```powershell
# Windows
netstat -ano | findstr :8000
taskkill /PID <PID> /F

# Linux/Mac
lsof -ti:8000 | xargs kill -9
```

## Version History

- **v3.6.0** - Reorganized scripts into logical categories
- **v3.5.0** - Added MCP and memory management scripts
- **v3.0.0** - Initial script collection
