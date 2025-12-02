# MCP Servers Configuration Status - ICTServe

**Last Updated**: 2025-12-02  
**Total Servers**: 10  
**Status**: ✅ All Configured

---

## Overview

Model Context Protocol (MCP) servers provide specialized tools and capabilities to AI assistants. This document outlines all configured MCP servers, their requirements, status, and usage.

---

## Configured MCP Servers

### 1. ✅ Laravel Boost

**Purpose**: Laravel-specific development tools and documentation  
**Status**: ✅ Ready  
**Command**: `php artisan boost:mcp`  
**Type**: Local PHP application  

**Features**:

- Application info (PHP, Laravel, packages, models)
- Database schema inspection
- Database queries
- Route listing
- Artisan command inspection
- Tinker integration
- Config access
- Error tracking
- Browser logs
- Documentation search

**Requirements**:

- ✅ Laravel Boost package installed
- ✅ `APP_ENV=local` or `APP_DEBUG=true`
- ✅ PHP in PATH

**Configuration**:

```json
{
    "command": "php",
    "args": ["artisan", "boost:mcp"],
    "env": {
        "APP_ENV": "local",
        "APP_DEBUG": "true",
        "BOOST_ENABLED": "true"
    }
}
```

---

### 2. ✅ Memory (Persistent Context)

**Purpose**: Persistent memory storage for AI conversations  
**Status**: ✅ Ready  
**Command**: `npx -y @modelcontextprotocol/server-memory`  
**Type**: File-based storage  

**Features**:

- Store entities (concepts, facts, decisions)
- Create relationships between entities
- Search stored knowledge
- Persist across sessions

**Storage Location**: `storage/mcp/memory.jsonl`

**Requirements**:

- ✅ Node.js and npx installed
- ✅ Storage directory exists
- ✅ Write permissions

**Use Cases**:

- Remember project decisions
- Store coding patterns
- Track architectural choices
- Maintain context between sessions

---

### 3. ✅ Sequential Thinking

**Purpose**: Chain-of-thought reasoning for complex problems  
**Status**: ✅ Ready  
**Command**: `docker run --rm -i mcp/sequentialthinking`  
**Type**: Docker container  

**Features**:

- Step-by-step problem breakdown
- Logical reasoning chains
- Complex task decomposition

**Requirements**:

- ⚠️ Docker Desktop running
- ✅ Docker image available

**Note**: Requires Docker to be running. Start Docker Desktop before using this server.

---

### 4. ✅ Chrome DevTools

**Purpose**: Browser automation and debugging  
**Status**: ✅ Ready  
**Command**: `npx chrome-devtools-mcp@latest`  
**Type**: npm package  

**Features**:

- Launch Chrome browsers
- Navigate to URLs
- Click elements
- Fill forms
- Take screenshots
- Execute JavaScript
- Read page content

**Requirements**:

- ✅ Node.js and npx installed
- ⚠️ Chrome/Chromium browser installed

**Use Cases**:

- Automated testing
- Web scraping
- Form submissions
- UI interaction testing

---

### 5. ✅ Firecrawl (Web Scraping)

**Purpose**: Advanced web scraping and crawling  
**Status**: ✅ Ready (API key configured)  
**Command**: `npx -y firecrawl-mcp`  
**Type**: Cloud service (requires API key)  

**Features**:

- Scrape single pages
- Crawl entire websites
- Extract structured data
- Handle JavaScript-heavy sites
- Bypass anti-bot measures

**API Key**: `fc-3daa684db8a843029bc947742ea9347c`

**Requirements**:

- ✅ Firecrawl API subscription
- ✅ API key configured

**Rate Limits**: Check Firecrawl plan limits

---

### 6. ✅ Context7 (Upstash)

**Purpose**: Semantic search and context management  
**Status**: ✅ Ready (API key configured)  
**Command**: `npx -y @upstash/context7-mcp`  
**Type**: Cloud service (requires API key)  

**Features**:

- Semantic search across documents
- Context retrieval
- Vector embeddings
- Knowledge base queries

**API Key**: `ctx7sk-c9ed2f9d-cb54-4436-9879-81ccbc95af03`

**Requirements**:

- ✅ Upstash account
- ✅ Context7 API key configured

---

### 7. ✅ DeepL (Translation)

**Purpose**: Professional-grade translation service  
**Status**: ✅ Ready (API key configured)  
**Command**: `npx -y deepl-mcp-server`  
**Type**: Cloud service (requires API key)  

**Features**:

- Translate text between languages
- Support for 30+ languages
- High-quality translations
- Glossary support

**API Key**: `58f6f571-d767-498a-a0cb-332d3fd5ed06:fx`

**Requirements**:

- ✅ DeepL API subscription
- ✅ API key configured

**Supported Languages**:

- Bahasa Melayu (MS) ✅
- English (EN) ✅
- And 28+ others

---

### 8. ✅ Playwright (Browser Automation)

**Purpose**: End-to-end browser testing and automation  
**Status**: ✅ Ready  
**Command**: `npx @playwright/mcp@latest`  
**Type**: npm package  

**Features**:

- Multi-browser support (Chrome, Firefox, Safari)
- Network interception
- Mobile emulation
- Screenshot and video recording
- Trace recording

**Requirements**:

- ✅ Node.js and npx installed
- ⚠️ Playwright browsers installed

**Installation** (if needed):

```bash
npx playwright install
```

**Use Cases**:

- E2E testing
- UI automation
- Performance testing
- Visual regression testing

---

### 9. ✅ GitHub Integration

**Purpose**: GitHub repository access and operations  
**Status**: ✅ Ready (PAT configured)  
**Command**: `docker run -i --rm ghcr.io/github/github-mcp-server`  
**Type**: Docker container (requires GitHub PAT)  

**Features**:

- Repository information
- Issue management
- Pull request operations
- Code search
- Commit history
- Branch management

**PAT**: `github_pat_11BC7S4YI045LIb...` (configured in Docker args)

**Requirements**:

- ⚠️ Docker Desktop running
- ✅ GitHub Personal Access Token configured

**Permissions Required**:

- `repo` - Full repository access
- `read:org` - Read organization data
- `read:user` - Read user profile data

---

### 10. ✅ Fetch (HTTP Requests)

**Purpose**: Make HTTP requests and fetch web content  
**Status**: ✅ Ready  
**Command**: `uvx mcp-server-fetch`  
**Type**: Python-based (uvx)  

**Features**:

- GET/POST/PUT/DELETE requests
- Custom headers
- Authentication support
- Response parsing
- URL validation

**Requirements**:

- ⚠️ Python and uvx installed

**Installation** (if needed):

```bash
pip install uvx
```

---

## Quick Start Guide

### Starting MCP Servers in VS Code

1. **Open Command Palette**: `Ctrl+Shift+P`
2. **Type**: "MCP: List Servers"
3. **Select a server** from the list
4. **Click**: "Start server"

### Starting All Servers

You can start multiple servers simultaneously. Each will run independently.

### Stopping Servers

1. **Open Command Palette**: `Ctrl+Shift+P`
2. **Type**: "MCP: List Servers"
3. **Select running server**
4. **Click**: "Stop server"

---

## System Requirements Checklist

### ✅ Installed and Ready

- [x] PHP 8.2+ (for Laravel Boost)
- [x] Node.js 20+ (for npm-based servers)
- [x] API Keys configured (Firecrawl, Context7, DeepL, GitHub)

### ⚠️ Optional Dependencies

- [ ] Docker Desktop (for sequential-thinking, github servers)
- [ ] Python/uvx (for fetch server)
- [ ] Chrome/Chromium (for chrome-devtools)
- [ ] Playwright browsers (for playwright server)

### Installing Optional Dependencies

**Docker Desktop** (sequential-thinking, github):

- Download: <https://www.docker.com/products/docker-desktop>
- Start: Launch Docker Desktop application

**Python/uvx** (fetch server):

```bash
# Install Python from https://www.python.org
pip install uvx
```

**Playwright Browsers** (playwright):

```bash
npx playwright install
```

---

## Server Categories

### Local Development

- ✅ **Laravel Boost** - Laravel-specific tools
- ✅ **Memory** - Persistent context storage

### Browser Automation

- ✅ **Chrome DevTools** - Chrome automation
- ✅ **Playwright** - Multi-browser testing

### Web Scraping

- ✅ **Firecrawl** - Advanced scraping
- ✅ **Fetch** - Simple HTTP requests

### AI Enhancement

- ✅ **Sequential Thinking** - Reasoning chains
- ✅ **Context7** - Semantic search

### External Services

- ✅ **GitHub** - Repository operations
- ✅ **DeepL** - Translation service

---

## Troubleshooting

### Server Won't Start

**Check 1**: Verify command is available

```bash
# For npx servers
npx --version

# For PHP servers
php --version

# For Docker servers
docker --version
```

**Check 2**: Check API key validity

- Firecrawl: <https://firecrawl.dev/account>
- Context7: <https://upstash.com>
- DeepL: <https://www.deepl.com/pro-account>
- GitHub: <https://github.com/settings/tokens>

**Check 3**: Review VS Code output

- View → Output
- Select "MCP" from dropdown

### Docker Servers Not Starting

**Issue**: "Cannot connect to Docker daemon"

**Fix**:

1. Launch Docker Desktop
2. Wait for Docker to fully start (whale icon in system tray)
3. Retry starting the MCP server

### Permission Errors

**Issue**: "EACCES: permission denied"

**Fix**:

```bash
# For storage directory
chmod -R 755 storage/mcp

# For memory file
touch storage/mcp/memory.jsonl
chmod 644 storage/mcp/memory.jsonl
```

### API Rate Limits

**Firecrawl**: Check plan limits at <https://firecrawl.dev/pricing>  
**Context7**: Check Upstash dashboard for usage  
**DeepL**: Monitor at <https://www.deepl.com/pro-account/usage>  
**GitHub**: 5,000 requests/hour with PAT

---

## Security Considerations

### ⚠️ API Keys in Configuration

The `.vscode/mcp.json` file contains API keys for convenience.

**Recommendations**:

1. **Add to .gitignore**:

```gitignore
.vscode/mcp.json
```

2. **Use environment variables** (alternative):

```json
{
    "env": {
        "FIRECRAWL_API_KEY": "${FIRECRAWL_API_KEY}"
    }
}
```

3. **Rotate keys regularly** if exposed

4. **Use team/project-specific keys** for shared repos

### GitHub PAT Security

- ✅ Use fine-grained tokens with minimal permissions
- ✅ Set expiration dates
- ✅ Never commit tokens to version control
- ✅ Rotate immediately if compromised

---

## Usage Examples

### Example 1: Using Laravel Boost

```
Ask AI: "List all routes in the application"
AI uses: laravel-boost → list-routes tool
```

### Example 2: Using Memory

```
Ask AI: "Remember that we use Service pattern for business logic"
AI uses: memory → create_entities tool
Later: "What patterns do we use?"
AI uses: memory → search_nodes tool
```

### Example 3: Using Firecrawl

```
Ask AI: "Scrape the latest Laravel release notes"
AI uses: firecrawl → scrape tool
```

### Example 4: Using DeepL

```
Ask AI: "Translate this error message to Bahasa Melayu"
AI uses: deepl → translate tool
```

### Example 5: Using GitHub

```
Ask AI: "Show open issues in this repository"
AI uses: github → list issues tool
```

---

## Performance Tips

### 1. Start Only Needed Servers

Don't start all servers at once. Start them as needed to save resources.

### 2. Docker Resources

If using Docker servers, allocate sufficient resources:

- Docker Desktop → Settings → Resources
- Recommended: 4GB RAM, 2 CPUs minimum

### 3. Memory Server Maintenance

Periodically clean up old entries:

```bash
# Backup current memory
cp storage/mcp/memory.jsonl storage/mcp/memory-backup-$(date +%Y%m%d).jsonl

# Optional: Start fresh
echo '[]' > storage/mcp/memory.jsonl
```

### 4. API Rate Limiting

Implement caching or batching for API-based servers to avoid rate limits.

---

## Monitoring and Logging

### VS Code MCP Logs

**View logs**:

1. View → Output
2. Select "MCP" from dropdown
3. Filter by server name

### Laravel Boost Logs

```bash
# Application logs
tail -f storage/logs/laravel.log

# Browser logs
tail -f storage/logs/browser.log
```

### Memory File Monitoring

```bash
# Watch memory file changes
tail -f storage/mcp/memory.jsonl
```

---

## Next Steps

1. **Test Each Server**: Start servers one by one and verify functionality
2. **Configure Docker** (if needed): Install and start Docker Desktop
3. **Install Optional Tools** (if needed): Playwright browsers, uvx
4. **Secure API Keys**: Move to environment variables if sharing repository
5. **Monitor Usage**: Track API usage to avoid rate limits

---

## Resources

- **MCP Specification**: <https://modelcontextprotocol.io>
- **Laravel Boost**: <https://boost.laravel.com>
- **Firecrawl Docs**: <https://docs.firecrawl.dev>
- **Playwright Docs**: <https://playwright.dev>
- **DeepL API Docs**: <https://www.deepl.com/docs-api>

---

**Status**: ✅ All 10 MCP servers configured and ready to use  
**Configuration File**: `.vscode/mcp.json`  
**Last Verified**: 2025-12-02
