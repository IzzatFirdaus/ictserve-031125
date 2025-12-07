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

```

5) Choosing toolsets and safety

- You can limit capabilities with GITHUB_TOOLSETS to avoid granting write permissions unnecessarily. The `--read-only` or env var `GITHUB_READ_ONLY=1` is recommended when experimenting.

6) Common troubleshooting

- 'dial unix \\.\pipe\dockerBackendApiServer' — start Docker Desktop and confirm it's running.
- 'authorization/permission' errors — your PAT may lack scopes; create a PAT with the minimal necessary scopes for the toolsets you need.
- 'image pull' errors — authenticate to GitHub Container Registry (ghcr.io) if required by running `docker login ghcr.io`.

If you'd like, I can add support in other local files (.cursor/mcp.json or CI scripts) to start the container automatically or show how to pass your existing GITHUB_API_KEY securely instead of using the prompt.

---

## References
