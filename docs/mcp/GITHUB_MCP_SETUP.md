# GitHub MCP Server — local Docker setup

This file shows quick steps to run the official GitHub MCP Server locally using Docker and how to configure your workspace so VS Code and other MCP hosts can run it.

Quick summary:

- Ensure Docker Desktop / Docker Engine is running on your machine.
- Provide a GitHub Personal Access Token (PAT) — we store this securely when prompted.
- Use the provided `.vscode/mcp.json` workspace file to start the server from your IDE or run the `docker run` command manually.

1) Docker must be running (Windows)

Windows tip: the `dial unix \\.\pipe\dockerBackendApiServer` error is usually because Docker Desktop is not running or accessible. Start Docker Desktop, wait until it reports 'Docker is running'. If you still see a connection error, try restarting Docker Desktop. If you're using WSL2, ensure the Docker WSL integration is enabled.

2) Use the workspace configuration (recommended)

The repo contains `.vscode/mcp.json` — if you open Copilot Chat / other MCP host in your editor it will prompt for a GitHub PAT and then start the container using Docker.

3) Manual Docker run (CLI)

Replace `<YOUR_PAT>` with your token (do NOT commit it anywhere). Example commands (PowerShell):

```powershell
# interactive, read-only mode or customize toolsets via GITHUB_TOOLSETS
docker run -i --rm `
  -e GITHUB_PERSONAL_ACCESS_TOKEN=<YOUR_PAT> `
  ghcr.io/github/github-mcp-server

# With toolsets enabled and read-only mode
docker run -i --rm `
  -e GITHUB_PERSONAL_ACCESS_TOKEN=<YOUR_PAT> `
  -e GITHUB_TOOLSETS="repos,issues,pull_requests,actions,code_security" `
  -e GITHUB_READ_ONLY=1 `
  ghcr.io/github/github-mcp-server
```

4) Using an existing environment variable (optional)

If you'd rather not be prompted by VS Code, you can set an environment variable in your shell session:

```powershell
$env:GITHUB_PERSONAL_ACCESS_TOKEN = 'YOUR_PAT'
docker run -i --rm -e GITHUB_PERSONAL_ACCESS_TOKEN ghcr.io/github/github-mcp-server

Helper: start Mimir & wait (Windows PowerShell)
You can use the project's helper to reliably start Neo4j and Mimir and wait for both services to be healthy:

```powershell
# From repo root
./scripts/start-mimir-and-wait.ps1

# With custom timeout and services
./scripts/start-mimir-and-wait.ps1 -Services mimir_server,neo4j -TimeoutSeconds 240
```

```

5) Choosing toolsets and safety

- You can limit capabilities with GITHUB_TOOLSETS to avoid granting write permissions unnecessarily. The `--read-only` or env var `GITHUB_READ_ONLY=1` is recommended when experimenting.

6) Common troubleshooting

- 'dial unix \\.\pipe\dockerBackendApiServer' — start Docker Desktop and confirm it's running.
- 'authorization/permission' errors — your PAT may lack scopes; create a PAT with the minimal necessary scopes for the toolsets you need.
- 'image pull' errors — authenticate to GitHub Container Registry (ghcr.io) if required by running `docker login ghcr.io`.

If you'd like, I can add support in other local files (.cursor/mcp.json or CI scripts) to start the container automatically or show how to pass your existing GITHUB_API_KEY securely instead of using the prompt.

---

## PCTX Integration Status (Code Mode)

**Status:** ⚠️ Windows Not Supported

PCTX (Code Mode for Mimir) provides 90-98% token reduction by enabling TypeScript code execution instead of sequential MCP tool calls. However, PCTX currently only supports:

- ✅ macOS (Apple Silicon & Intel)
- ✅ Linux (ARM64 & x64)
- ❌ Windows (not supported by npm package or prebuilt binaries)

### Alternative Approaches for Windows

#### Option 1: WSL2 (Recommended)
Run PCTX inside Windows Subsystem for Linux:

```powershell
# Install WSL2 with Ubuntu
wsl --install

# Inside WSL2
curl --proto '=https' --tlsv1.2 -LsSf https://github.com/portofcontext/pctx/releases/download/v0.2.0/pctx-installer.sh | sh

# Configure to connect to Mimir on Windows host
cd /mnt/c/XAMPP/htdocs/ictserve-031125
pctx start
```

**Note:** Docker Desktop must have WSL2 integration enabled for Neo4j/Mimir access.

#### Option 2: Direct Mimir MCP (Current Setup)
Continue using Mimir directly via MCP (stdio) as currently configured in `.vscode/mcp.json`. This provides:

- ✅ All 13 Mimir tools available
- ✅ Graph memory operations
- ✅ Vector search & file indexing
- ⚠️ Sequential tool calls (higher token usage)
- ⚠️ No Code Mode (TypeScript execution)

#### Option 3: GitHub Codespaces / Linux Dev Container
Use cloud Linux environment:

```yaml
# .devcontainer/devcontainer.json
{
  "name": "ICTServe Dev",
  "image": "mcr.microsoft.com/devcontainers/php:8.2",
  "postCreateCommand": "curl --proto '=https' --tlsv1.2 -LsSf https://github.com/portofcontext/pctx/releases/download/v0.2.0/pctx-installer.sh | sh"
}
```

### Token Usage Comparison

| Approach | Token Usage | Speed | Setup Complexity |
|----------|-------------|-------|------------------|
| **PCTX Code Mode** (Linux/Mac) | 500-2K tokens | Fast (1-2s) | Medium |
| **Direct Mimir MCP** (Windows) | 5K-50K tokens | Medium (5-10s) | Low (current) |
| **WSL2 + PCTX** | 500-2K tokens | Fast | High |

### Recommendation for ICTServe Team

1. **Development (Windows):** Continue using direct Mimir MCP as configured
2. **CI/CD (Linux):** Implement PCTX for automated workflows
3. **Future:** Monitor PCTX Windows support (track issue: <https://github.com/portofcontext/pctx/issues>)

---

## References

- **PCTX Releases:** <https://github.com/portofcontext/pctx/releases>
- **Mimir PCTX Guide:** <https://github.com/orneryd/Mimir/blob/main/docs/guides/PCTX_INTEGRATION_GUIDE.md>
- **Supported Platforms:** aarch64-apple-darwin, aarch64-unknown-linux-gnu, x86_64-unknown-linux-gnu
