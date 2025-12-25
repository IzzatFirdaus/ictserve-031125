# MCP Server Configuration Guide

**Last Updated**: 2025-12-19  
**Version**: 4.0  
**Environment**: Local Development (XAMPP/Laragon) - Docker-Free Configuration

---

## Table of Contents

1. [Overview](#overview)
2. [Quick Start](#quick-start)
3. [Server Catalog](#server-catalog)
4. [Configuration Files](#configuration-files)
5. [Setup Instructions](#setup-instructions)
6. [API Keys & Credentials](#api-keys--credentials)
7. [Troubleshooting](#troubleshooting)
8. [Best Practices](#best-practices)

---

## Overview

This repository includes comprehensive MCP (Model Context Protocol) server configuration for Kiro IDE and VS Code, providing AI-powered development tools for the ICTServe Laravel 12 application.

**Total MCP Servers**: 11 servers across 6 categories  
**Active by Default**: 6 servers  
**Optional (Disabled)**: 5 servers  
**Configuration Type**: Local development (no Docker dependency)

### What is MCP?

Model Context Protocol (MCP) extends AI assistant capabilities through specialized servers that provide:

- **Memory**: Persistent knowledge graphs
- **Browser Automation**: Chrome DevTools, Playwright
- **Laravel Tools**: Laravel Boost for Laravel-specific operations
- **Documentation**: Context7 for library docs
- **Translation**: DeepL for bilingual support
- **Web Scraping**: Firecrawl for data extraction
- **Version Control**: GitHub integration

---

## Quick Start

### Prerequisites

```bash
# Check Node.js (required for npm-based servers)
node --version  # Should be v20+

# Check PHP (required for Laravel Boost)
php --version   # Should be 8.2+

# Check npm/npx
npx --version
```

### 1. Verify Environment Variables

Open `.env` file and confirm these are present:

```env
# Application (for Laravel Boost)
APP_ENV=local
APP_DEBUG=true

# MCP API Keys (optional services)
FIRECRAWL_API_KEY=your_key_here
CONTEXT7_API_KEY=your_key_here
DEEPL_API_KEY=your_key_here
PAT_GITHUB_ACCESS_TOKEN=your_token_here
```

### 2. Load Environment Variables (Windows)

**Option A: Windows Environment Variables** (Recommended - Persistent)

1. Open: System Properties → Advanced → Environment Variables
2. Add User variables for each API key
3. Restart VS Code/Kiro IDE

**Option B: PowerShell Profile** (Session-based)

```powershell
# Edit profile
notepad $PROFILE

# Add these lines
$env:FIRECRAWL_API_KEY="your_key_here"
$env:CONTEXT7_API_KEY="your_key_here"
$env:DEEPL_API_KEY="your_key_here"
$env:PAT_GITHUB_ACCESS_TOKEN="your_token_here"

# Reload profile
. $PROFILE
```

**Option C: PowerShell Script** (Per-session)

```powershell
# Load environment from .env
.\scripts\load-mcp-env.ps1

# Load and start VS Code
.\scripts\load-mcp-env.ps1 -StartVSCode
```

### 3. Verify Storage Directory

```powershell
# Ensure memory storage exists
New-Item -ItemType Directory -Path storage/mcp -Force
New-Item -ItemType File -Path storage/mcp/memory.jsonl -Force
```

### 4. Start MCP Servers

**In VS Code/Kiro IDE**:

1. Press `Ctrl+Shift+P`
2. Type "MCP: List Servers"
3. Select each server and click "Start"

---

## Server Catalog

### Category 1: Core Development & Analysis (4 servers)

#### 1. fetch ✅ ACTIVE

- **Command**: `uvx mcp-server-fetch`
- **Purpose**: HTTP requests and API interactions
- **Auto-Approve**: `fetch`
- **Use Case**: Testing API endpoints, external service integration
- **Requirements**: Python + uvx

#### 2. memory ✅ ACTIVE

- **Command**: `npx -y @modelcontextprotocol/server-memory`
- **Purpose**: Knowledge graph management with entity-relationship modeling
- **Storage**: `storage/mcp/memory.jsonl`
- **Auto-Approve**: All 9 tools
- **Use Case**: Architectural decision tracking, technical pattern storage
- **Requirements**: Node.js

#### 3. sequentialthinking ✅ ACTIVE

- **Command**: `npx -y @modelcontextprotocol/server-sequential-thinking`
- **Purpose**: Complex problem decomposition and multi-step planning
- **Auto-Approve**: `sequentialthinking`
- **Use Case**: Breaking down Laravel features, refactoring planning
- **Requirements**: Node.js

#### 4. context7 ✅ ACTIVE

- **Command**: `npx -y @upstash/context7-mcp`
- **Purpose**: Enhanced context understanding and library documentation
- **Requires**: `CONTEXT7_API_KEY`
- **Auto-Approve**: `resolve-library-id`, `get-library-docs`
- **Use Case**: Laravel/Filament/Livewire version-specific documentation
- **Requirements**: Node.js + API key

---

### Category 2: Laravel & PHP Development (1 server)

#### 5. laravel-boost ✅ ACTIVE - CRITICAL

- **Command**: `php artisan boost:mcp`
- **Purpose**: Laravel-specific development operations
- **Auto-Approve**: 16 tools
- **Environment**:
  - `APP_ENV=local`
  - `MCP_CONNECTION_MODE=persistent`
- **Tools**:
  - Application: application-info, get-config, list-available-config-keys, list-available-env-vars
  - Database: database-connections, database-query, database-schema
  - Debugging: browser-logs, read-log-entries, last-error
  - Development: list-artisan-commands, list-routes, tinker
  - Documentation: search-docs
  - Utilities: get-absolute-url, report-feedback
- **Requirements**: PHP 8.2+, Laravel Boost package

---

### Category 3: Browser Automation & Debugging (2 servers)

#### 6. chrome-devtools ✅ ACTIVE

- **Command**: `npx chrome-devtools-mcp@latest`
- **Purpose**: Browser inspection and frontend debugging
- **Auto-Approve**: navigate_page, take_snapshot, click, fill, evaluate_script
- **Use Case**: Filament admin debugging, Livewire UI testing
- **Requirements**: Node.js, Chrome/Chromium

#### 7. playwright ⏸️ DISABLED (Opt-in)

- **Command**: `npx @playwright/mcp@latest`
- **Purpose**: Cross-browser E2E testing and automation
- **Auto-Approve**: 7 core tools pre-configured
- **Tools**: 40+ browser automation tools
- **Use Case**: E2E testing workflows, visual regression testing
- **To Enable**: Change `"disabled": true` to `"disabled": false`
- **Requirements**: Node.js, Playwright browsers (`npx playwright install`)

---

### Category 4: Web Scraping & Data Extraction (1 server)

#### 8. firecrawl ✅ ACTIVE

- **Command**: `npx -y firecrawl-mcp`
- **Purpose**: Web scraping, crawling, and data extraction
- **Requires**: `FIRECRAWL_API_KEY`
- **Auto-Approve**: None (requires explicit user approval)
- **Tools**:
  - `firecrawl_scrape` - Single page content
  - `firecrawl_batch_scrape` - Multiple known URLs
  - `firecrawl_map` - Discover URLs on site
  - `firecrawl_crawl` - Multi-page extraction
  - `firecrawl_search` - Web search
  - `firecrawl_extract` - Structured data extraction
- **Use Case**: Reference documentation parsing, competitive research
- **Requirements**: Node.js + API key

---

### Category 5: Version Control & Repository Management (2 servers)

#### 9. github ⏸️ DISABLED (Opt-in)

- **Command**: `npx -y github-mcp-server`
- **Purpose**: GitHub repository management and collaboration
- **Requires**: `GITHUB_TOKEN`
- **Auto-Approve**: 6 tools pre-configured
- **Tools**: list_repositories, get_repository, get_file, create_pull_request, create_issue, list_commits
- **Use Case**: PR workflows, issue tracking, code reviews
- **To Enable**: Change `"disabled": true` to `"disabled": false`
- **Requirements**: Node.js + GitHub PAT

#### 10. gitkraken ⏸️ DISABLED (Opt-in)

- **Command**: `gk`
- **Purpose**: GitKraken CLI integration with AI-powered features
- **Requires**: GitKraken CLI installation
- **Features**: Work items, AI commit messages, AI PR generation
- **Installation**:
  - macOS: `brew install gitkraken-cli`
  - Windows: `winget install gitkraken.cli`
  - Unix: Download from releases page
- **Use Case**: Enhanced git workflows with AI assistance

---

### Category 6: Database & Caching (1 server)

#### 11. redis ⏸️ DISABLED (Opt-in)

- **Command**: `uvx --from redis-mcp-server@latest redis-mcp-server --url redis://localhost:6379/0`
- **Purpose**: Natural language interface for Redis operations
- **Features**:
  - Full Redis support (strings, hashes, lists, sets, sorted sets, streams)
  - Vector search and indexing
  - EntraID authentication for Azure Managed Redis
- **Tools**: String, hash, list, set, sorted set, pub/sub, streams, JSON, query engine
- **Use Case**: Redis database management, caching operations
- **Configuration**: Update `--url` parameter for your Redis instance
- **Requirements**: Python + uvx, Redis server

---

### Category 7: Translation & Localization (1 server)

#### 12. deepl ✅ ACTIVE - CRITICAL FOR i18n

- **Command**: `npx -y deepl-mcp-server`
- **Purpose**: Professional translation and rephrasing
- **Requires**: `DEEPL_API_KEY` (500,000 characters/month FREE tier)
- **Auto-Approve**: All 4 tools
- **Tools**:
  - `get-source-languages` - List available source languages
  - `get-target-languages` - List available target languages
  - `translate-text` - Translate text with formality control
  - `rephrase-text` - Rephrase text in same/different language
- **ICTServe Integration**:
  - ✅ Supports Bahasa Melayu (ms) ↔ English (en)
  - ✅ WCAG 2.2 AA compliance (professional translation)
  - ✅ PDPA 2010 alignment (official language requirements)
  - ✅ Formality control for government communication
- **API Key**: Sign up at <https://www.deepl.com/pro-api>
- **Requirements**: Node.js + API key

---

## Configuration Files

### Workspace Configuration

**File**: `.kiro/settings/mcp.json` (Kiro IDE) or `.vscode/mcp.json` (VS Code)  
**Purpose**: Shared team development environment  
**Version Control**: ✅ Committed to repository  
**Secrets**: ❌ Never hardcoded (uses $input:variableName or environment variables)

**Example** (`.kiro/settings/mcp.json`):

```json
{
  "mcpServers": {
    "memory": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory"],
      "autoApprove": ["create_entities", "search_nodes", "open_nodes"]
    },
    "laravel-boost": {
      "command": "php",
      "args": ["artisan", "boost:mcp"],
      "env": {
        "APP_ENV": "local",
        "MCP_CONNECTION_MODE": "persistent"
      },
      "autoApprove": ["application-info", "database-query", "search-docs"]
    }
  }
}
```

### User Configuration

**File**: `C:\Users\[USERNAME]\.kiro\settings\mcp.json` (Kiro) or `%APPDATA%\Code\User\mcp.json` (VS Code)  
**Purpose**: Personal API keys and user-specific settings  
**Version Control**: ❌ Not committed (personal)  
**Secrets**: ✅ Store actual API key values here

**Example**:

```json
{
  "mcpServers": {
    "deepl": {
      "env": {
        "DEEPL_API_KEY": "your-actual-api-key-here"
      }
    },
    "context7": {
      "env": {
        "CONTEXT7_API_KEY": "your-actual-api-key-here"
      }
    },
    "firecrawl": {
      "env": {
        "FIRECRAWL_API_KEY": "your-actual-api-key-here"
      }
    }
  }
}
```

---

## Setup Instructions

### Method 1: Local Development (Recommended)

**For**: XAMPP, Laragon, Herd, or any local PHP environment

**Steps**:

1. Install Node.js 20+ from <https://nodejs.org>
2. Install PHP 8.2+ (included with XAMPP/Laragon)
3. Verify installations:

   ```bash
   node --version
   php --version
   npx --version
   ```

4. Configure workspace MCP file (`.kiro/settings/mcp.json`)
5. Add API keys to user config or environment variables
6. Restart IDE

**Advantages**:

- ✅ Faster startup (no container overhead)
- ✅ Easier debugging
- ✅ Direct file system access

### Method 2: Docker Development

**For**: Consistent environment across team, CI/CD pipelines

**Steps**:

1. Install Docker Desktop from <https://www.docker.com/products/docker-desktop>
2. Start Docker Desktop
3. Build and start MCP containers:

   ```powershell
   docker compose up -d mcp-memory mcp-sequential-thinking
   ```

4. Configure workspace MCP file to use Docker:

   ```json
   {
     "mcpServers": {
       "memory": {
         "command": "docker",
         "args": ["exec", "-i", "ictserve-mcp-memory", "node", "/app/dist/index.js"]
       }
     }
   }
   ```

5. Restart IDE

**Advantages**:

- ✅ Isolated environment
- ✅ Reproducible across machines
- ✅ Production-like setup

---

## API Keys & Credentials

Configure these in your user-level config or system environment:

| Service       | Variable Name       | Where to Get                                        | Free Tier        |
| ------------- | ------------------- | --------------------------------------------------- | ---------------- |
| Context7      | `CONTEXT7_API_KEY`  | <https://upstash.com>                               | Yes              |
| Firecrawl     | `FIRECRAWL_API_KEY` | <https://www.firecrawl.dev>                         | Yes              |
| GitHub        | `GITHUB_TOKEN`      | GitHub Settings → Developer → Personal Access Token | Yes              |
| DeepL         | `DEEPL_API_KEY`     | <https://www.deepl.com/pro-api>                     | 500k chars/month |

### Security Best Practices

1. **Never commit API keys** to version control
2. **Use environment variables** or IDE secret stores
3. **Rotate keys regularly** (quarterly recommended)
4. **Use fine-grained tokens** with minimal permissions
5. **Monitor API usage** to detect unauthorized access

---

## Troubleshooting

### Laravel Boost Connection Timeouts

**Symptoms**:

- `MCP error -32001: Request timed out`
- `MCP error -32000: Connection closed`
- `MCP server connection and syncing tools and resources timed out after 5 minutes`

**Solution**: Use Laravel MCP framework instead of direct command:

```json
{
  "laravel-boost": {
    "command": "php",
    "args": ["artisan", "mcp:start", "laravel-boost"],
    "cwd": "/path/to/laravel/project",
    "env": {
      "APP_ENV": "local"
    }
  }
}
```

**Details**: See [LARAVEL_BOOST_MCP_INTEGRATION.md](LARAVEL_BOOST_MCP_INTEGRATION.md)

### Server Won't Start

**Issue**: "Command not found" or "Cannot find module"

**Fix**:

```bash
# Install the package globally
npm install -g @modelcontextprotocol/server-memory
npm install -g @playwright/mcp
npm install -g firecrawl-mcp
npm install -g @upstash/context7-mcp
npm install -g deepl-mcp-server
npm install -g github-mcp-server
```

### API Key Not Found

**Issue**: "API key missing" or "401 Unauthorized"

**Verify**:

```powershell
# Check if environment variable is set
echo $env:FIRECRAWL_API_KEY
echo $env:CONTEXT7_API_KEY
echo $env:DEEPL_API_KEY
echo $env:PAT_GITHUB_ACCESS_TOKEN
```

**Fix**: Follow [Setup Instructions](#setup-instructions) to load environment variables

### Laravel Boost: "No commands in boost namespace"

**Issue**: Boost commands not available

**Fix**:

```bash
# Ensure APP_ENV=local and APP_DEBUG=true in .env
# Then clear caches
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear

# Verify commands available
php artisan list boost
```

### Memory Server: File Not Found

**Issue**: Cannot find `storage/mcp/memory.jsonl`

**Fix**:

```powershell
# Create storage directory and file
New-Item -ItemType Directory -Path storage/mcp -Force
New-Item -ItemType File -Path storage/mcp/memory.jsonl -Force
```

### Playwright: Browsers Not Installed

**Issue**: "Executable doesn't exist"

**Fix**:

```bash
# Install Playwright browsers
npx playwright install

# Verify installation
npx playwright --version
```

### Docker Servers Not Starting

**Issue**: "Cannot connect to Docker daemon"

**Fix**:

1. Launch Docker Desktop
2. Wait for Docker to fully start (whale icon in system tray)
3. Retry starting the MCP server

---

## Best Practices

### 1. Workspace vs User Configuration

**Workspace** (`.kiro/settings/mcp.json`):

- ✅ Server definitions
- ✅ Default auto-approve lists
- ✅ Disabled status flags
- ❌ Never store actual API keys

**User** (`C:\Users\[USERNAME]\.kiro\settings\mcp.json`):

- ✅ Actual API key values
- ✅ Personal overrides
- ✅ User-specific servers
- ❌ Don't commit to version control

### 2. Security

- Never commit API keys to repository
- Use `$input:variableName` for workspace config
- Store actual keys only in user-level config
- Regularly rotate API keys
- Use least-privilege tokens

### 3. Performance

- Keep only essential servers active
- Disable unused servers to reduce overhead
- Monitor API usage to avoid rate limits
- Use auto-approve judiciously for frequently-used safe tools

### 4. Maintenance

**Weekly**:

- Backup memory file: `storage/mcp/memory.jsonl`
- Review enabled servers (disable unused)

**Monthly**:

- Update MCP servers: `npm update -g @modelcontextprotocol/*`
- Review memory graph size (prune if >100MB)
- Audit API key usage (external services)

**Quarterly**:

- Review configuration against best practices
- Update documentation with new patterns
- Archive old memory entities
- Rotate API keys

---

## Integration with ICTServe Workflows

### Translation Workflow (DeepL)

```
1. Write English UI strings in resources/lang/en/
2. Use DeepL MCP to translate to Bahasa Melayu
3. Save to resources/lang/ms/
4. Verify formality level matches government standards
5. Commit both language files together
```

### Development Workflow (Laravel Boost)

```
1. Use tinker to test code snippets
2. Check database-schema before migrations
3. Use search-docs for version-specific Laravel help
4. Verify list-routes after adding new routes
5. Check last-error when debugging issues
```

### Testing Workflow (Playwright - when enabled)

```
1. Write E2E test scenarios
2. Use browser_navigate to visit pages
3. Use browser_snapshot for accessibility verification
4. Use browser_fill_form for form testing
5. Capture evidence with browser_take_screenshot
```

---

## References

- **MCP Specification**: <https://modelcontextprotocol.io/specification>
- **Kiro IDE MCP Docs**: <https://kiro.dev/docs/mcp/>
- **Laravel Boost**: <https://github.com/laravel/boost>
- **ICTServe Documentation**: `docs/D00_SYSTEM_OVERVIEW.md` through `D15_UI_UX_STYLE_GUIDE.md`
- **Steering Documentation**: `.kiro/steering/mcp.md`
- **Memory Guide**: `docs/mcp/MCP_MEMORY_GUIDE.md`

---

## Support

For issues or questions:

1. Check this guide for troubleshooting steps
2. Review `.kiro/steering/mcp.md` for comprehensive MCP usage guidelines
3. Check ICTServe documentation in `docs/` directory
4. Check Laravel Boost documentation
5. Contact: <devops@motac.gov.my>

---

**Configuration Status**: ✅ Production-ready  
**Last Verified**: 2025-12-09  
**Maintainer**: ICTServe Development Team
