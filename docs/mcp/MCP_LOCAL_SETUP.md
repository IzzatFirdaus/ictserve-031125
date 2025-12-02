# MCP Configuration Guide for Local Development (XAMPP/Laragon)

**Environment**: Local Development (Windows + XAMPP/Laragon)  
**Last Updated**: 2025-12-02  
**Total MCP Servers**: 10  
**Status**: ✅ Configured for Local Development

---

## 🎯 Overview

This configuration is optimized for **local development** using XAMPP, Laragon, or similar local PHP environments. All Docker-based servers have been replaced with native npm/npx alternatives, and sensitive API keys are stored in `.env` file.

---

## ✅ Configured MCP Servers

### 1. Laravel Boost (PHP/Artisan)
**Purpose**: Laravel-specific development tools  
**Command**: `php artisan boost:mcp`  
**Requirements**: ✅ PHP in PATH, Laravel Boost installed

**Features**:

- Application info (versions, packages, models)
- Database schema & queries
- Route inspection
- Artisan commands
- Tinker integration
- Browser logs
- Documentation search

**Environment Variables**:

```env
APP_ENV=local
APP_DEBUG=true
BOOST_ENABLED=true
```

---

### 2. Memory (Persistent Context)
**Purpose**: Store conversation context across sessions  
**Command**: `npx -y @modelcontextprotocol/server-memory`  
**Requirements**: ✅ Node.js, npx installed  
**Storage**: `storage/mcp/memory.jsonl`

**Features**:

- Create entities (facts, decisions, patterns)
- Link entities with relationships
- Search stored knowledge
- Persist across sessions

**Use Cases**:

- Remember architectural decisions
- Store coding patterns
- Track project-specific conventions
- Maintain context between coding sessions

---

### 3. Fetch (HTTP Requests)
**Purpose**: Make HTTP requests and fetch web content  
**Command**: `npx -y fetch-mcp`  
**Requirements**: ✅ Node.js, npx installed

**Features**:

- GET/POST/PUT/DELETE requests
- Custom headers
- Parse responses
- Download files

---

### 4. Playwright (Browser Automation)
**Purpose**: E2E testing and browser automation  
**Command**: `npx -y @playwright/mcp`  
**Requirements**: ✅ Node.js, npx installed

**Features**:

- Multi-browser support (Chrome, Firefox, Edge)
- Take screenshots
- Fill forms
- Click elements
- Run JavaScript

**Installation** (if needed):

```bash
npx playwright install
```

---

### 5. Firecrawl (Web Scraping)
**Purpose**: Advanced web scraping service  
**Command**: `npx -y firecrawl-mcp`  
**Requirements**: ✅ Firecrawl API key in `.env`

**Environment Variable**:

```env
FIRECRAWL_API_KEY=fc-3daa684db8a843029bc947742ea9347c
```

**Features**:

- Scrape single pages
- Crawl entire websites
- Extract structured data
- Handle JavaScript-heavy sites

**API Plan**: Check limits at <https://firecrawl.dev>

---

### 6. Context7 (Semantic Search)
**Purpose**: Semantic search and context management  
**Command**: `npx -y @upstash/context7-mcp`  
**Requirements**: ✅ Context7 API key in `.env`

**Environment Variable**:

```env
CONTEXT7_API_KEY=ctx7sk-c9ed2f9d-cb54-4436-9879-81ccbc95af03
```

**Features**:

- Semantic search
- Vector embeddings
- Context retrieval
- Knowledge base queries

---

### 7. DeepL (Translation)
**Purpose**: Professional translation service  
**Command**: `npx -y deepl-mcp-server`  
**Requirements**: ✅ DeepL API key in `.env`

**Environment Variable**:

```env
DEEPL_API_KEY=58f6f571-d767-498a-a0cb-332d3fd5ed06:fx
```

**Features**:

- High-quality translations
- 30+ languages including Bahasa Melayu
- Glossary support
- Formality levels

---

### 8. GitHub Integration
**Purpose**: GitHub repository operations  
**Command**: `npx -y @modelcontextprotocol/server-github`  
**Requirements**: ✅ GitHub PAT in `.env`

**Environment Variable**:

```env
PAT_GITHUB_ACCESS_TOKEN=github_pat_<your-token>
```

**Features**:

- Repository information
- Issue management
- Pull request operations
- Code search
- Branch management

**Required Permissions**:

- `repo` - Full repository access
- `read:org` - Read organization
- `read:user` - Read user profile

---

### 9. Sequential Thinking
**Purpose**: Structured problem solving and reasoning  
**Command**: `npx -y @modelcontextprotocol/server-sequential-thinking`  
**Requirements**: ✅ Node.js, npx installed

**Features**:

- Break down complex problems
- Dynamic thought adjustment
- Branching reasoning paths
- Hypothesis verification

---

### 10. Filesystem
**Purpose**: Direct file system access  
**Command**: `npx -y @modelcontextprotocol/server-filesystem c:\XAMPP\htdocs\ictserve-031125`  
**Requirements**: ✅ Node.js, npx installed

**Features**:

- Read/Write files
- List directories
- File search
- Restricted to workspace root

---

## 🔧 Setup Instructions

### Step 1: Verify Node.js and PHP

```bash
# Check Node.js (required for npx-based servers)
node --version
# Should be v20+ or higher

# Check npm/npx
npx --version

# Check PHP (required for Laravel Boost)
php --version
# Should be 8.2 or higher
```

### Step 2: Verify Environment Variables

Open `.env` file and confirm these are present:

```env
# Application (for Laravel Boost)
APP_ENV=local
APP_DEBUG=true

# MCP API Keys
FIRECRAWL_API_KEY=fc-3daa684db8a843029bc947742ea9347c
CONTEXT7_API_KEY=ctx7sk-c9ed2f9d-cb54-4436-9879-81ccbc95af03
DEEPL_API_KEY=58f6f571-d767-498a-a0cb-332d3fd5ed06:fx
PAT_GITHUB_ACCESS_TOKEN=github_pat_11BC7S4YI045LIb...
```

### Step 3: Load Environment Variables in Windows

For VS Code to access `.env` variables, you need to:

**Option A: Use Windows Environment Variables** (Recommended)

1. Open System Properties → Advanced → Environment Variables
2. Add User variables:
   - `FIRECRAWL_API_KEY` = `fc-3daa684db8a843029bc947742ea9347c`
   - `CONTEXT7_API_KEY` = `ctx7sk-c9ed2f9d-cb54-4436-9879-81ccbc95af03`
   - `DEEPL_API_KEY` = `58f6f571-d767-498a-a0cb-332d3fd5ed06:fx`
   - `PAT_GITHUB_ACCESS_TOKEN` = `github_pat_11BC7S4YI045LIb...`
3. Restart VS Code

**Option B: Use PowerShell Profile** (Session-based)

Create/edit PowerShell profile:

```powershell
notepad $PROFILE
```

Add these lines:

```powershell
$env:FIRECRAWL_API_KEY="fc-3daa684db8a843029bc947742ea9347c"
$env:CONTEXT7_API_KEY="ctx7sk-c9ed2f9d-cb54-4436-9879-81ccbc95af03"
$env:DEEPL_API_KEY="58f6f571-d767-498a-a0cb-332d3fd5ed06:fx"
$env:PAT_GITHUB_ACCESS_TOKEN="github_pat_11BC7S4YI045LIb..."
```

Save and reload:

```powershell
. $PROFILE
```

**Option C: Load Before Starting VS Code** (Manual)

Create `start-vscode.ps1`:

```powershell
# Load environment variables
$env:FIRECRAWL_API_KEY="fc-3daa684db8a843029bc947742ea9347c"
$env:CONTEXT7_API_KEY="ctx7sk-c9ed2f9d-cb54-4436-9879-81ccbc95af03"
$env:DEEPL_API_KEY="58f6f571-d767-498a-a0cb-332d3fd5ed06:fx"
$env:PAT_GITHUB_ACCESS_TOKEN="github_pat_11BC7S4YI045LIb..."

# Start VS Code
code .
```

Run it:

```powershell
.\start-vscode.ps1
```

### Step 4: Verify Storage Directory

```bash
# Ensure memory storage exists
mkdir storage/mcp -Force
New-Item storage/mcp/memory.jsonl -ItemType File -Force
```

### Step 5: Test MCP Servers

**In VS Code**:

1. Press `Ctrl+Shift+P`
2. Type "MCP: List Servers"
3. Select each server and click "Start"
4. Check for errors in Output panel

---

## 🚀 Starting MCP Servers

### In VS Code

1. **Open Command Palette**: `Ctrl+Shift+P`
2. **Type**: "MCP: List Servers"
3. **Select**: Server name (e.g., `laravel-boost`)
4. **Click**: "Start server"

### Status Indicator

Look for server status in VS Code status bar (bottom-right corner)

### Verify Server is Running

Test with AI assistant:

```
User: "List all routes in the application"
AI should use: laravel-boost → list-routes tool
```

---

## 🔍 Troubleshooting

### Server Won't Start

**Issue**: "Command not found" or "Cannot find module"

**Fix**:

```bash
# Install the package globally
npm install -g @modelcontextprotocol/server-memory
npm install -g fetch-mcp
npm install -g @playwright/mcp
npm install -g firecrawl-mcp
npm install -g @upstash/context7-mcp
npm install -g deepl-mcp-server
npm install -g @modelcontextprotocol/server-github
```

---

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

**Fix**: Follow Step 3 above to load environment variables

---

### Laravel Boost: "No commands in boost namespace"

**Issue**: Boost commands not available

**Fix**:

```bash
# Ensure APP_ENV=local and APP_DEBUG=true in .env
# Then clear caches
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
```

---

### Memory Server: File Not Found

**Issue**: Cannot find `storage/mcp/memory.jsonl`

**Fix**:

```powershell
# Create storage directory and file
New-Item storage/mcp/memory.jsonl -ItemType File -Force
```

---

### Playwright: Browsers Not Installed

**Issue**: "Executable doesn't exist"

**Fix**:

```bash
# Install Playwright browsers
npx playwright install
```

---

## 🔒 Security Best Practices

### 1. Git Ignore Sensitive Files

Add to `.gitignore`:

```gitignore
.env
.env.local
.env.*.local
.vscode/mcp.json
```

### 2. Rotate API Keys Regularly

- **Firecrawl**: <https://firecrawl.dev/account>
- **Context7**: <https://upstash.com>
- **DeepL**: <https://www.deepl.com/pro-account>
- **GitHub**: <https://github.com/settings/tokens>

### 3. Use Fine-Grained GitHub Tokens

Create tokens with minimal required permissions:

- ✅ `repo` - Only for specific repositories
- ✅ `read:org` - Only if needed
- ❌ Avoid `admin:*` permissions

### 4. Never Commit API Keys

Always use `.env` file or Windows environment variables

---

## 📊 Server Status Checklist

- [x] **Laravel Boost**: ✅ Configured (php artisan boost:mcp)
- [x] **Memory**: ✅ Configured (npx persistent storage)
- [x] **Fetch**: ✅ Configured (npx HTTP client)
- [x] **Playwright**: ✅ Configured (npx browser automation)
- [x] **Firecrawl**: ✅ Configured (API key in .env)
- [x] **Context7**: ✅ Configured (API key in .env)
- [x] **DeepL**: ✅ Configured (API key in .env)
- [x] **GitHub**: ✅ Configured (PAT in .env)

**Removed for Local Development**:

- ❌ Sequential Thinking (Docker) - Not needed for local dev
- ❌ Chrome DevTools (unstable on Windows) - Use Playwright instead

---

## 📝 Configuration File

**Location**: `.vscode/mcp.json`

**Key Points**:

- ✅ All servers use `npx` (Node.js) or `php` (local)
- ✅ No Docker dependencies
- ✅ API keys reference environment variables `${VAR_NAME}`
- ✅ Works with XAMPP, Laragon, Herd, or any local PHP

---

## 🎓 Usage Examples

### Example 1: Laravel Boost

```
User: "Show me all Eloquent models"
AI: Uses laravel-boost → application-info
```

### Example 2: Memory

```
User: "Remember: We use Service pattern for all business logic"
AI: Uses memory → create_entities
Later: "What patterns do we use?"
AI: Uses memory → search_nodes
```

### Example 3: Firecrawl

```
User: "Scrape the Laravel 12 release notes"
AI: Uses firecrawl → scrape
```

### Example 4: DeepL

```
User: "Translate 'Asset borrowed successfully' to Bahasa Melayu"
AI: Uses deepl → translate
Result: "Aset berjaya dipinjam"
```

### Example 5: GitHub

```
User: "List all open issues in this repository"
AI: Uses github → list_issues
```

---

## 🔄 Maintenance

### Update MCP Packages

```bash
# Update all npm packages to latest
npx -y npm-check-updates -u

# Or update individually
# Update: Change `@mendable/firecrawl-mcp` -> `firecrawl-mcp`
npm update -g @modelcontextprotocol/server-memory
npm update -g @playwright/mcp
npm update -g firecrawl-mcp
# etc...
```

### Update Laravel Boost

```bash
composer update laravel/boost
php artisan boost:update
```

### Clean Memory Storage

```bash
# Backup current memory
cp storage/mcp/memory.jsonl storage/mcp/memory-backup-$(date +%Y%m%d).jsonl

# Start fresh (optional)
echo '[]' > storage/mcp/memory.jsonl
```

---

## 📚 Resources

- **Laravel Boost**: <https://boost.laravel.com>
- **MCP Specification**: <https://modelcontextprotocol.io>
- **Playwright Docs**: <https://playwright.dev>
- **Firecrawl**: <https://docs.firecrawl.dev>
- **DeepL API**: <https://www.deepl.com/docs-api>

---

## ✅ Final Checklist

Before using MCP servers, ensure:

- [x] Node.js 20+ installed (`node --version`)
- [x] PHP 8.2+ installed (`php --version`)
- [x] Laravel Boost package installed (`composer show laravel/boost`)
- [x] `.env` file has all API keys
- [x] Environment variables loaded (Windows env or PowerShell profile)
- [x] `storage/mcp/memory.jsonl` file exists
- [x] VS Code MCP extension active

---

**Status**: ✅ All 8 MCP servers configured for local development  
**Environment**: XAMPP/Laragon compatible  
**API Keys**: Secured in `.env` file  
**Last Verified**: 2025-12-02
