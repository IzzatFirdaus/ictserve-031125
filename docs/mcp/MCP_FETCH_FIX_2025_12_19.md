# MCP Fetch Server Fix - December 19, 2025

## Issue Summary

The fetch MCP server was failing to start with the following error:

```
npm error 404 Not Found - GET https://registry.npmjs.org/mcp-server-fetch - Not found
npm error 404  'mcp-server-fetch@*' is not in this registry.
```

## Root Cause

The fetch MCP server was incorrectly configured to use `npx` with the package name `mcp-server-fetch`. However, this package is a **Python package**, not a Node.js package, and should be run using `uvx` (the Python package runner from the `uv` tool).

## Solution

### 1. Changed Command from NPX to UVX

**Before**:

```json
{
  "fetch": {
    "command": "npx",
    "args": ["-y", "mcp-server-fetch"],
    "disabled": false,
    "autoApprove": ["fetch"]
  }
}
```

**After**:

```json
{
  "fetch": {
    "command": "uvx",
    "args": ["mcp-server-fetch"],
    "disabled": false,
    "autoApprove": ["fetch"]
  }
}
```

### 2. Enabled Playwright MCP Server

Changed `"disabled": true` to `"disabled": false` for the playwright server.

**Configuration**:

```json
{
  "playwright": {
    "command": "npx",
    "args": ["-y", "@playwright/mcp@latest"],
    "disabled": false,
    "autoApprove": [
      "browser_navigate",
      "browser_click",
      "browser_snapshot",
      "browser_fill",
      "browser_evaluate",
      "browser_close",
      "browser_take_screenshot"
    ]
  }
}
```

### 3. Enabled GitHub MCP Server

Changed `"disabled": true` to `"disabled": false` for the github server and added auto-approve tools.

**Configuration**:

```json
{
  "github": {
    "command": "npx",
    "args": ["-y", "@modelcontextprotocol/server-github"],
    "env": {
      "GITHUB_PERSONAL_ACCESS_TOKEN": "$env:PAT_GITHUB_ACCESS_TOKEN"
    },
    "disabled": false,
    "autoApprove": [
      "create_or_update_file",
      "get_file_contents",
      "get_repository",
      "list_repositories",
      "search_repositories"
    ]
  }
}
```

## Verification

### System Requirements Verified

```powershell
# UVX is available
uvx --version
# Output: uvx 0.9.5 (d5f39331a 2025-10-21)

# Fetch server works
uvx mcp-server-fetch --help
# Output: Shows help message with options
```

### Files Updated

1. ✅ `.mcp.json` - Root MCP configuration
2. ✅ `.kiro/settings/mcp.json` - Kiro workspace configuration

## Active MCP Servers After Fix

| Server | Status | Type | Command |
|--------|--------|------|---------|
| memory | ✅ Active | NPX | `npx @modelcontextprotocol/server-memory` |
| sequentialthinking | ✅ Active | NPX | `npx @modelcontextprotocol/server-sequential-thinking` |
| laravel-boost | ✅ Active | Laravel MCP | `php artisan mcp:start laravel-boost` |
| ictserve | ✅ Active | Laravel MCP | `php artisan mcp:start ictserve` |
| **fetch** | ✅ **FIXED** | **UVX** | `uvx mcp-server-fetch` |
| chrome-devtools | ✅ Active | NPX | `npx chrome-devtools-mcp@latest` |
| **playwright** | ✅ **ENABLED** | NPX | `npx @playwright/mcp@latest` |
| **github** | ✅ **ENABLED** | NPX | `npx @modelcontextprotocol/server-github` |

## Additional Notes

### Fetch Server Features

The fetch MCP server provides web content fetching capabilities:

- Fetches web pages and converts HTML to markdown
- Supports chunked reading with `start_index` parameter
- Respects robots.txt by default (can be disabled)
- Customizable user-agent
- Proxy support

### Playwright Server Requirements

Before using the Playwright server, install the required browsers:

```powershell
npx playwright install
```

### GitHub Server Requirements

The GitHub server requires a Personal Access Token (PAT):

1. Go to GitHub Settings → Developer Settings → Personal Access Tokens
2. Generate a new token with appropriate permissions
3. Set the environment variable: `PAT_GITHUB_ACCESS_TOKEN`

## Testing

### Test Fetch Server

```powershell
# Start the server manually to verify
uvx mcp-server-fetch
# Should start without errors
```

### Test Playwright Server

```powershell
# Verify Playwright is installed
npx playwright --version

# Install browsers if needed
npx playwright install
```

### Test GitHub Server

```powershell
# Verify environment variable is set
echo $env:PAT_GITHUB_ACCESS_TOKEN
# Should show your GitHub token
```

## References

- **Fetch Server Documentation**: <https://mcpservers.org/servers/modelcontextprotocol/fetch>
- **UVX Documentation**: <https://docs.astral.sh/uv/>
- **Playwright MCP**: <https://github.com/microsoft/playwright-mcp>
- **GitHub MCP**: <https://github.com/modelcontextprotocol/servers/tree/main/src/github>

---

**Fix Status**: ✅ COMPLETED  
**Date**: December 19, 2025  
**Verified By**: ICTServe Development Team
