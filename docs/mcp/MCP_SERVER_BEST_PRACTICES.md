# MCP Server Configuration Best Practices

## Overview
Model Context Protocol (MCP) servers extend AI assistant capabilities. This guide covers optimal configuration for ICTServe project.

---

## Core Servers (Always Enabled)

### 1. Memory Server (`@modelcontextprotocol/server-memory`)
**Purpose**: Persistent knowledge graph across sessions

**Best Practices**:
- ✅ Keep enabled for all development work
- ✅ Use for storing project patterns, solutions, decisions
- ✅ Query before creating new entities (avoid duplicates)
- ⚠️ Backup memory file periodically: `~/.mcp-memory/memory.jsonl`

**Usage Pattern**:
```javascript
// Start session: Query existing knowledge
search_nodes('topic')
open_nodes(['Entity_Name'])

// During work: Update memory
add_observations(['Entity'], ['New fact'])

// End session: Store session summary
create_entities([{name: 'Session_Summary', ...}])
```

**Timeout**: 120s (sufficient for large graphs)

---

### 2. Sequential Thinking (`@modelcontextprotocol/server-sequential-thinking`)
**Purpose**: Multi-step reasoning for complex problems

**Best Practices**:
- ✅ Use for architectural decisions
- ✅ Use for debugging complex issues
- ✅ Use for planning multi-file changes
- ❌ Don't use for simple queries

**Usage Pattern**:
```javascript
// Complex problem solving
sequentialthinking({
  thought: "Step 1: Analyze requirements",
  thoughtNumber: 1,
  totalThoughts: 5,
  nextThoughtNeeded: true
})
```

**Timeout**: 120s (reasoning can be intensive)

---

### 3. Laravel Boost (`php artisan boost:mcp`)
**Purpose**: Laravel-specific development tools

**Best Practices**:
- ✅ Always query `application-info` at session start
- ✅ Use `search-docs` before implementation
- ✅ Use `database-query` for read-only DB operations
- ✅ Use `tinker` for testing code snippets
- ❌ Don't use `tinker` for production operations

**Critical Tools**:
- `application-info` - Package versions, models
- `search-docs` - Version-specific Laravel docs
- `database-query` - Safe DB reads
- `tinker` - PHP REPL for testing
- `list-artisan-commands` - Available commands

**Timeout**: 120s (Artisan can be slow on first run)

---

## Browser Automation Servers

### 4. Chrome DevTools (`chrome-devtools-mcp`)
**Purpose**: Browser automation and debugging

**Best Practices**:
- ✅ Enable for frontend development
- ✅ Use for accessibility testing
- ✅ Use for performance monitoring
- ⚠️ Disable if not doing browser work (saves resources)

**Usage Pattern**:
```javascript
// Navigate and inspect
navigate_page('http://localhost:8000')
take_snapshot()  // Accessibility tree
evaluate_script('() => document.title')
```

**Timeout**: 120s (browser startup can be slow)

---

### 5. Playwright (`@playwright/mcp`)
**Purpose**: E2E testing automation

**Best Practices**:
- ⚠️ Enable only when writing/debugging E2E tests
- ✅ Use for cross-browser testing
- ✅ Use for screenshot comparisons
- ❌ Disable during normal development (heavy resource usage)

**Usage Pattern**:
```javascript
// E2E test workflow
browser_navigate('http://localhost:8000')
browser_click('button[type="submit"]')
browser_snapshot()
```

**Timeout**: 120s (browser + Playwright initialization)

---

## Optional External Services
## Optional External Services

### 6. Firecrawl (`firecrawl-mcp`)
**Purpose**: Web scraping and crawling

**Best Practices**:
- ❌ Disabled by default (requires API key)
- ✅ Enable for documentation scraping
- ✅ Enable for competitor analysis
- ⚠️ Respect rate limits

**Setup**: Get API key from https://firecrawl.dev

---

### 8. Context7 (`@upstash/context7-mcp`)
**Purpose**: Documentation search across frameworks

**Best Practices**:
- ❌ Disabled by default (requires API key)
- ✅ Enable for multi-framework projects
- ⚠️ Laravel Boost `search-docs` is better for Laravel-specific queries

**Setup**: Get API key from https://upstash.com

---

### 9. Fetch (`fetch-mcp`)
**Purpose**: HTTP requests and API testing

**Best Practices**:
- ⚠️ Enable only when testing external APIs
- ✅ Use for API documentation fetching
- ❌ Don't use for authenticated requests (security risk)

**Usage Pattern**:
```javascript
fetch('https://api.example.com/docs')
```

---

### 10. DeepL (`deepl-mcp-server`)
**Purpose**: Professional translation service

**Best Practices**:
- ❌ Disabled by default (requires API key)
- ✅ Enable for bilingual projects (MS/EN)
- ✅ Use for translating documentation
- ⚠️ ICTServe already has Laravel localization

**Setup**: Get API key from https://www.deepl.com/pro-api

---

## Configuration Best Practices

### Windows-Specific
```toml
# ✅ CORRECT: Use .cmd extension
command = 'npx.cmd'

# ❌ WRONG: Missing .cmd on Windows
command = 'npx'
```

### Timeouts
```toml
# ✅ RECOMMENDED: 120s for most servers
startup_timeout_sec = 120

# ⚠️ INCREASE: For slow systems or large projects
startup_timeout_sec = 180

# ❌ TOO LOW: May cause startup failures
startup_timeout_sec = 30
```

### Docker vs Local
```toml
# ✅ LOCAL: Faster, easier debugging
[mcp_servers.memory]
disabled = false
command = 'node'
args = ['path/to/server']

# ✅ DOCKER: Isolated, reproducible
[mcp_servers.memory-docker]
disabled = false
command = 'docker'
args = ['exec', '-i', 'container', 'node', '/app/index.js']
```

**When to use Docker**:
- CI/CD pipelines
- Team consistency
- Production-like environment

**When to use Local**:
- Active development
- Debugging server issues
- Faster iteration

---

## Performance Optimization

### 1. Disable Unused Servers
```toml
# ❌ BAD: All servers enabled
[mcp_servers.playwright]
disabled = false  # Not needed for backend work

# ✅ GOOD: Only enable what you need
[mcp_servers.playwright]
disabled = true  # Enable only for E2E testing
```

### 2. Startup Order
Servers start in config order. Put frequently-used servers first:
1. Memory (always needed)
2. Sequential Thinking (complex reasoning)
3. Laravel Boost (project-specific)
4. Browser tools (only if needed)

### 3. Resource Management
- **Memory Server**: ~50MB RAM
- **Sequential Thinking**: ~30MB RAM
- **Laravel Boost**: ~100MB RAM (PHP + Laravel)
- **Chrome DevTools**: ~200MB RAM (browser)
- **Playwright**: ~300MB RAM (browser + automation)

**Total for core servers**: ~180MB RAM
**Total with browser tools**: ~680MB RAM

---

## Troubleshooting

### Server Won't Start
```powershell
# Check if command exists
where npx.cmd
where node
where php

# Test server manually
node "C:\Users\exatf\AppData\Roaming\npm\node_modules\@modelcontextprotocol\server-memory\dist\index.js"

# Check logs
cat C:\XAMPP\htdocs\ictserve-031125\scripts\mcp-debug.log
```

### JSON Parse Errors
- Remove wrapper scripts (use direct commands)
- Check server output for non-JSON data
- Increase `startup_timeout_sec`

### Docker Connection Issues
```powershell
# Verify container running
docker ps | findstr mcp

# Check container logs
docker logs ictserve-mcp-memory

# Test connection
docker exec -i ictserve-mcp-memory node /app/dist/index.js
```

---

## Recommended Configuration for ICTServe

### Development (Local)
```toml
[mcp_servers.memory]
disabled = false  # ✅ Always

[mcp_servers.sequentialthinking]
disabled = false  # ✅ Always

[mcp_servers.laravel-boost]
disabled = false  # ✅ Always

[mcp_servers.chrome-devtools]
disabled = false  # ✅ For frontend work

[mcp_servers.playwright]
disabled = true   # ⚠️ Enable only for E2E testing
```

### CI/CD (Docker)
```toml
[mcp_servers.memory-docker]
disabled = false

[mcp_servers.sequential-thinking-docker]
disabled = false

[mcp_servers.playwright-docker]
disabled = false  # For E2E tests
```

---

## Security Considerations

### API Keys
```toml
# ✅ SECURE: Use $input: prefix
env = { API_KEY = '$input:API_KEY' }

# ❌ INSECURE: Hardcoded keys
env = { API_KEY = 'sk-1234567890' }
```

### File Permissions
- Memory file: `~/.mcp-memory/memory.jsonl` (user read/write only)
- Config file: `~/.codex/config.toml` (user read/write only)

### Network Access
- Laravel Boost: Local PHP only (no network)
- Browser tools: Localhost only (no external sites without approval)

---

## Maintenance

### Weekly
- Backup memory file: `~/.mcp-memory/memory.jsonl`
- Review enabled servers (disable unused)
- Check for server updates: `npm outdated -g`

### Monthly
- Update MCP servers: `npm update -g @modelcontextprotocol/*`
- Review memory graph size (prune if >100MB)
- Audit API key usage (external services)

### Quarterly
- Review configuration against best practices
- Update documentation with new patterns
- Archive old memory entities

---

## References

- MCP Specification: https://modelcontextprotocol.io
- Laravel Boost: https://github.com/laravel/boost
- Memory Server: https://github.com/modelcontextprotocol/servers
- ICTServe MCP Integration: `.amazonq/rules/MCP-Integration.md`

---

**Last Updated**: 2025-01-22
**Version**: 1.0.0
**Maintainer**: ICTServe Development Team
