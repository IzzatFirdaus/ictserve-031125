---
name: chrome-devtools-mcp-getting-started
description: Quick start for running chrome-devtools-mcp in this repository
author: dev-team@motac.gov.my
trace: SRS-FR-XXX; D11 §7
last-updated: 2025-10-23
---

# Chrome DevTools MCP — Getting started

This short HOWTO explains how to run the Chrome DevTools Model Context Protocol (MCP) server locally for development and debugging.

## Prerequisites

- Node.js (recommended v20.x or later) and npm installed and available on PATH.
- A Chromium/Chrome installation (stable, beta or canary) if you want the server to launch a browser instance.
- This repository includes starter wrappers in `scripts/` and an MCP entry in `.mcp.json`.

## Files added to this repo

- `scripts/start-chrome-devtools-mcp.ps1` — PowerShell wrapper for Windows.
- `scripts/start-chrome-devtools-mcp.sh` — POSIX shell wrapper for macOS/Linux.
- `.mcp.json` — contains a `chrome-devtools` entry that invokes `npx chrome-devtools-mcp@latest`.

## Quick examples

Open a PowerShell terminal in the repository root and run the PowerShell wrapper:

```powershell
# Start the MCP server, launch Chrome headless, use a temporary profile (isolated)
.\n+\scripts\start-chrome-devtools-mcp.ps1 -Headless -Isolated
```

On macOS/Linux (POSIX shell):

```bash
./scripts/start-chrome-devtools-mcp.sh --headless --isolated
```

You can also run the upstream package directly via npx (this is what the wrappers call):

```powershell
npx -y chrome-devtools-mcp@latest --headless=true --isolated=true
```

## Useful CLI flags

- `--headless` — run Chrome without a visible UI (useful for CI or background tasks).
- `--isolated` — create a temporary user-data directory that is removed when the browser closes.
- `--executablePath <path>` — point to a custom Chrome/Chromium binary.
- `--wsEndpoint` / `--browserUrl` — connect to an already-running browser instance instead of launching one.
- `--channel` — choose a Chrome channel (stable, beta, dev, canary).
- `--logFile <path>` and set `DEBUG=*` environment variable for verbose logs.

See the upstream help for the full CLI: `npx chrome-devtools-mcp@latest --help`.

## .mcp.json example

Add or update the repository's `.mcp.json` to include:

```json

  "mcpServers": 
    "chrome-devtools": 
      "command": "npx",
      "args": ["chrome-devtools-mcp@latest"]

  

```

Clients that understand MCP can reference `chrome-devtools` as the server name and invoke it.

## Security notes

- Do not expose the DevTools remote debugging endpoint to public networks. Use local-only connections or secure tunnels.
- When connecting to an external browser (via `--wsEndpoint`), ensure any auth headers are managed securely (the package supports `--wsHeaders`).

## Troubleshooting

- If `npx` fails, ensure Node.js and npm are installed and that `npx` is available on PATH.
- Use `--executablePath` to point to a Chrome binary if the server cannot find Chrome automatically.
- Enable verbose logs with `DEBUG=*` and `--logFile <path>` and inspect the trace.

## Next steps

- If you want, I can start the MCP server here in headless+isolated mode and capture the WebSocket endpoint printed to the terminal.
- I can also update other docs or add a short README snippet in `scripts/` if you prefer.

---
