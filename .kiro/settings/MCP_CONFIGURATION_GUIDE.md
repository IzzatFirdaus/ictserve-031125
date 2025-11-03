# Kiro IDE MCP Server Configuration Guide

**Last Updated**: November 1, 2025  
**Repository**: ictserve-091025  
**Configuration Version**: 2.0

## Overview

This repository includes comprehensive MCP (Model Context Protocol) server configuration for Kiro IDE, providing AI-powered development tools for the ICTServe Laravel 12 application.

**Total MCP Servers**: 12 servers across 6 categories  
**Active by Default**: 8 servers  
**Optional (Disabled)**: 4 servers

---

## Configuration Files

### Workspace Configuration

**File**: `.kiro/settings/mcp.json`  
**Purpose**: Shared team development environment  
**Version Control**: ✅ Committed to repository  
**Secrets**: ❌ Never hardcoded (uses $input:variableName)

### User Configuration  

**File**: `C:\Users\[USERNAME]\.kiro\settings\mcp.json`  
**Purpose**: Personal API keys and user-specific settings  
**Version Control**: ❌ Not committed (personal)  
**Secrets**: ✅ Store actual API key values here

---

## Configured MCP Servers

### Category 1: Core Development & Analysis (4 servers)

#### 1. fetch ✅ ACTIVE

- **Command**: `uvx mcp-server-fetch`
- **Purpose**: HTTP requests and API interactions
- **Auto-Approve**: `fetch`
- **Use Case**: Testing API endpoints, external service integration

#### 2. memory ✅ ACTIVE

- **Command**: `npx -y @modelcontextprotocol/server-memory`
- **Purpose**: Knowledge graph management with entity-relationship modeling
- **Auto-Approve**: All 9 tools (create_entities, create_relations, add_observations, delete_entities, delete_observations, delete_relations, read_graph, search_nodes, open_nodes)
- **Use Case**: Architectural decision tracking, technical pattern storage

#### 3. sequentialthinking ✅ ACTIVE

- **Command**: `npx -y @modelcontextprotocol/server-sequential-thinking`
- **Purpose**: Complex problem decomposition and multi-step planning
- **Auto-Approve**: `sequentialthinking`
- **Use Case**: Breaking down Laravel features, refactoring planning

#### 4. context7 ✅ ACTIVE

- **Command**: `npx -y @upstash/context7-mcp`
- **Purpose**: Enhanced context understanding and library documentation
- **Requires**: `CONTEXT7_API_KEY`
- **Auto-Approve**: `resolve-library-id`, `get-library-docs`
- **Use Case**: Laravel/Filament/Livewire version-specific documentation

---

### Category 2: Laravel & PHP Development (1 server)

#### 5. laravel-boost ✅ ACTIVE - CRITICAL

- **Command**: `php artisan boost:mcp`
- **Purpose**: Laravel-specific development operations
- **Auto-Approve**: 16 tools (all Laravel operations)
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

---

### Category 3: Browser Automation & Debugging (2 servers)

#### 6. chrome-devtools ✅ ACTIVE

- **Command**: `npx chrome-devtools-mcp@latest`
- **Purpose**: Browser inspection and frontend debugging
- **Auto-Approve**: navigate_page, take_snapshot, click, fill, evaluate_script
- **Use Case**: Filament admin debugging, Livewire UI testing

#### 7. playwright ⏸️ DISABLED (Opt-in)

- **Command**: `npx @playwright/mcp@latest`
- **Purpose**: Cross-browser E2E testing and automation
- **Auto-Approve**: 7 core tools pre-configured
- **Tools**: 40+ browser automation tools
- **Use Case**: E2E testing workflows, visual regression testing
- **To Enable**: Change `"disabled": true` to `"disabled": false`

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

---

## API Keys Required

Configure these in your user-level config (`C:\Users\[USERNAME]\.kiro\settings\mcp.json`):

| Service | Variable Name | Where to Get | Free Tier |
|---------|---------------|--------------|-----------|
| Context7 | `CONTEXT7_API_KEY` | <https://upstash.com> | Yes |
| Firecrawl | `FIRECRAWL_API_KEY` | <https://www.firecrawl.dev> | Yes |
| GitHub | `GITHUB_TOKEN` | GitHub Settings → Developer → Personal Access Token | Yes |
| DeepL | `DEEPL_API_KEY` | <https://www.deepl.com/pro-api> | 500k chars/month |

---

## Security & Compliance

### Auto-Approval Strategy

**✅ Auto-Approved** (Read-only, safe operations):

- Database schema inspection (`database-schema`)
- Configuration reading (`get-config`, `list-routes`)
- Documentation search (`search-docs`)
- Translation operations (all DeepL tools)
- Memory graph operations (knowledge management)

**⏸️ Requires User Approval** (Write/External operations):

- External data scraping (Firecrawl: all tools)
- Repository modifications (GitHub PRs, issues)
- Database write operations (Redis when enabled)

### PDPA 2010 Compliance

All MCP operations follow Malaysian Personal Data Protection Act 2010:

- ✅ External data validated before use (Firecrawl requires approval)
- ✅ Personal data sanitization enforced per `docs/D09`
- ✅ Audit trail requirements met
- ✅ Rate limiting for external services

### WCAG 2.2 AA Compliance

DeepL translation server supports accessibility:

- Professional Bahasa Melayu ↔ English translation
- Formality control for government standards
- Consistent terminology across UI

---

## Quick Start

### 1. Enable Required API Keys

Create/edit your user config:

```json

  "mcpServers": 
    "deepl": 
      "env": 
        "DEEPL_API_KEY": "your-actual-api-key-here"
  
,
    "context7": 
      "env": 
        "CONTEXT7_API_KEY": "your-actual-api-key-here"
  
,
    "firecrawl": 
      "env": 
        "FIRECRAWL_API_KEY": "your-actual-api-key-here"
  

  

```

### 2. Restart Kiro IDE

After configuration changes, fully restart Kiro IDE to load the new MCP servers.

### 3. Verify Servers Are Running

Look for MCP server indicators in Kiro IDE status bar or MCP panel.

---

## Enabling Optional Servers

### Enable Playwright (E2E Testing)

In `.kiro/settings/mcp.json`, change:

```json
"playwright": 
  "disabled": true  // Change to false

```

### Enable GitHub Integration

In `.kiro/settings/mcp.json`, change:

```json
"github": 
  "disabled": true  // Change to false

```

Then add your GitHub token in user config.

### Enable Redis Integration

1. Ensure Redis is running: `redis-server`
2. In `.kiro/settings/mcp.json`, change:

```json
"redis": 
  "disabled": true  // Change to false

```

3. Update the `--url` parameter for your Redis instance

### Enable GitKraken CLI

1. Install GitKraken CLI:
   - macOS: `brew install gitkraken-cli`
   - Windows: `winget install gitkraken.cli`
2. Authenticate: `gk auth login`
3. In `.kiro/settings/mcp.json`, change:

```json
"gitkraken": 
  "disabled": true  // Change to false

```

---

## Troubleshooting

### Server Won't Start

1. Check Node.js/npm installation: `node --version && npm --version`
2. Check Python/uv for `uvx` commands: `uvx --version`
3. Verify package names are correct
4. Check Kiro IDE logs for errors

### API Key Issues

1. Verify API key is correctly set in user config
2. Check API key has not expired
3. Verify API key has necessary permissions
4. Test API key with curl/postman first

### Laravel Boost Connection Issues

1. Ensure you're in the Laravel project root
2. Check `php artisan boost:mcp` runs successfully
3. Verify `APP_ENV=local` in `.env`
4. Check Laravel logs: `storage/logs/laravel.log`

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

---

## Support

For issues or questions:

1. Check `.kiro/steering/mcp.md` for comprehensive MCP usage guidelines
2. Review ICTServe documentation in `docs/` directory
3. Check Laravel Boost documentation
4. Contact: <devops@motac.gov.my>

---

**Configuration Status**: ✅ Production-ready  
**Last Verified**: November 1, 2025  
**Maintainer**: ICTServe Development Team
