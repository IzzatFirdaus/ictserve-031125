---
title: Devtools - MCP Servers (Getting Started)
---

<!-- Title: MCP Servers for Developer Workflows (front matter used) -->

This document explains how to configure Model Context Protocol (MCP) servers for local development in VS Code. The repository includes workspace-level MCP configuration in `.vscode/mcp.json` and workgroup configs in `.mcp.json`, `.opencode/mcp.json`, `.kiro/settings/mcp.json`, and `.junie/mcp/mcp.json`.

## Key points

- Sensitive API keys are not stored in repository files. Use the interactive VS Code input prompts or set environment variables (recommended) to enable third-party servers.
- Servers that require keys (e.g., Firecrawl, Context7, DeepL) are disabled by default — enable them in VS Code settings when you are ready.
- The Laravel Boost server (`laravel-boost`) uses the local Artisan command to provide Laravel-specific capabilities like tinker, listing artisan commands, and database schema lookups.

## Quick setup (Windows)

1. Open the workspace in VS Code.
2. Install MCP extension (if required) that reads `.vscode/mcp.json` and starts server processes.
3. Set API keys through VS Code prompts or add them to your user-level Kiro/VS Code settings.
   - FIRECRAWL_API_KEY
   - CONTEXT7_API_KEY
   - DEEPL_API_KEY

Example: set a user-level Kiro config in `C:\Users\<username>\.kiro\settings\mcp.json`:

```json
{
  "mcpServers": {
    "firecrawl": { "env": { "FIRECRAWL_API_KEY": "fc-xxxx" }, "disabled": false }
  }
}
```

1. Start servers via the MCP extension UI: start `memory`, `laravel-boost`, `playwright`, `chrome-devtools` (if you need browser interactions), and `sequentialthinking`.

1. If you need to run local memory with a persistent file, the workspace uses: `C:\XAMPP\htdocs\ictserve-031125\storage\mcp\memory.jsonl`.

## Enabling a server

In VS Code open the command palette: `> MCP: Start Server <server-name>` or use the MCP extension UI. For third-party servers:

- Provide API keys: when you start the server, VS Code will request `FIRECRAWL_API_KEY`, `CONTEXT7_API_KEY`, or `DEEPL_API_KEY` via the `input:` prompt. Alternatively add those to your user settings.

## Troubleshooting

- If a server fails to start, check the server `cwd` and that required programs are installed (`php`, `npx`, `uvx`) on PATH.
- Laravel Boost requires `php` in PATH and that the laravel repo is available in `cwd` (`C:\XAMPP\htdocs\ictserve-031125`). If `php` is not in PATH, add it or set full path in your user-level mcp config (e.g., `C:\Program Files\PHP\php.exe`).
- Memory server uses `@modelcontextprotocol/server-memory` — ensure node present (`npx` will download it automatically).

## Security

- Do not commit API keys or secrets to source control.
- Use `$input:*` placeholders in workspace files (these prompt VS Code to ask for the secret from the user). For long-lived environments, use user-level config with locked access.

---
Steps we applied in this repository:

1. `.vscode/mcp.json` now contains a complete set of servers for local dev and is the file read by the MCP extension to start servers.

2. Sensitive keys were removed from `.opencode`, `.junie`, and `.kiro` and replaced with `$input:` placeholders; those servers are disabled by default.

If you want, I can: enable specific servers for you (with placeholders), or show how to store values in Windows environment variables so they persist between sessions.
