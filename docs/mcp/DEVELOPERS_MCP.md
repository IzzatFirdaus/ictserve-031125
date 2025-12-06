## MCP (Model Context Protocol) — Developer Guide for ICTServe

Purpose
-------
This document summarizes recommended MCP server configuration and runtime best-practices for development IDEs (VS Code, JetBrains, Windsurf, Cursor) and LLM connectors (GitHub Copilot, OpenAI Codex, Google Gemini/Vertex, Amazon Q). It reflects project-specific rules in `memory.instructions.md` and cross-vendor best practices (security, portability, resilience).

Quick wins (do these first)
---------------------------------

- Do NOT store secrets in workspace files. Use IDE secret stores or OS-level secret managers. Examples: VS Code SecretStorage, JetBrains Password Safe, AWS Secrets Manager.
- Use `${workspaceFolder}` not absolute paths in `.vscode/mcp.json`.
- Run MCP servers in separate processes (wrapper script + stdio or network) to avoid blocking the editor.
- Keep workspace-level `mcp.json` for shareable, non-sensitive server configs and user-level config for tokens/overrides.

Config guidance by IDE
---------------------

- VS Code (recommended)
  - Workspace file: `.vscode/mcp.json` for shareable server definitions.
  - User file: `C:\Users\<you>\.kiro\settings\mcp.json` for personal server keys and disabled duplicates.
  - Store secrets in SecretStorage (do not commit tokens).
  - Use a small wrapper script (e.g. scripts/mcp-stdio-wrapper.js) to make commands cross-platform.

- JetBrains (IntelliJ/PyCharm)
  - Implement connectors as background services/processes; avoid work at startup.
  - Use Password Safe for secrets and PersistentStateComponent to store small non-sensitive settings.

- Windsurf & Cursor (cloud/web IDEs)
  - Use per-user connectors and the product’s integrations panel.
  - Rely on web-based secure token flows; keep workspace-level connector definitions local to the workspace.

LLM / Extension-level rules
---------------------------

- Always store API tokens securely; never check them into VCS.
- Provide a transparent model selection (which endpoint + model/version) and a fallback policy for throttling/errors.
- For enterprise deployments, prefer private endpoints, managed services (AWS/GCP), or local runs for sensitive projects.

Operational & Security Best Practices
-----------------------------------

- Secrets: Use secure stores (SecretStorage or OS-managed). Rotate tokens and apply least-privilege IAM policies.
- Health checks: MCP servers should expose a health/readiness path (HTTP endpoint or stdio handshake), IDE should surface startup errors.
- Observability: Keep logs accessible (developer readable), emit lightweight telemetry and request metrics (opt-in, privacy-safe).
- Rate limiting & fallback: Client-side throttling with exponential backoff + local fallback for private/offline usage.
- Portability: Use `${workspaceFolder}` and wrapper scripts to hide OS-specific command differences (node vs npx vs npx.cmd).

Examples — workspace and user configs
-------------------------------------
Workspace (commitable) — `.vscode/mcp.json` (non-sensitive):

{
  "servers": {
    "memory": {
      "type": "stdio",
      "command": "node",
      "args": ["${workspaceFolder}/scripts/mcp-stdio-wrapper.js","npx","-y","@modelcontextprotocol/server-memory","${workspaceFolder}/storage/mcp/memory.jsonl"]
    }
  }
}

User (private) — `~/.kiro/settings/mcp.json` (example — DO NOT COMMIT):

{
  "servers": {
    "memory": {
      "type": "stdio",
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory", "--token", "$input:MEMORY_API_TOKEN"]
    }
  }
}

Health-check pattern
--------------------

- MCP server: expose either a short `--health` HTTP path or a small JSON `ping` handshake over stdio.
- IDE: attempt a handshake during server startup and display logs if handshake times out.

Next steps & automation ideas
----------------------------

- Add CI smoke tests that start MCP servers and assert the health endpoint returns ready.
- Add a small CLI script `scripts/mcp-health-check.ps1` (Windows) and `scripts/mcp-health-check.sh` (Unix) for developers.

Troubleshooting — duplicate / auto-starting MCP servers
-----------------------------------------------------

Symptoms

- You see repeated retry logs or OAuth monitor windows from a Docker-managed MCP stack (MCP_DOCKER) while VS Code also tries to start stdio servers. IDE shows duplicate MCP server entries or noisy OAuth flows.

Short diagnosis

- Many developer tools (Docker Desktop + MCP Docker extension, or a local Docker compose MCP stack) auto-start MCP servers. If the auto-started Docker servers differ from the workspace `.vscode/mcp.json` (stdio servers) you will see duplicate entries, mismatched endpoints, and retry loops.

How to isolate & fix (recommended)

 1. Disable the Docker MCP extension / stop Docker-managed MCP stack:
    - Docker Desktop → Extensions → disable/uninstall MCP/MCP_DOCKER OR
    - stop the container/compose session you used to start the MCP_DOCKER stack.
 2. Ensure `.vscode/mcp.json` (workspace) is the ONLY MCP config you intend to use for this project (the file in this repo follows stdio conventions).
 3. Check user-level settings (Windows: `%USERPROFILE%\.kiro\settings\mcp.json`) for any `github.copilot.chat.mcpServers` or other MCP entries referencing Docker exec/http endpoints — remove or disable them.
 4. Quit VS Code fully and re-open it (do not just reload). This ensures the MCP extension will only try to start the stdio servers defined in the workspace config.
 5. Start servers via the MCP extension UI in VS Code and confirm only the intended stdio servers are launching (chrome-devtools, laravel-boost, memory, sequentialthinking, etc.).

If you prefer Docker-managed MCP servers

- Add them *explicitly* to `.vscode/mcp.json` (workspace) or user-level config so they are authorized and configured instead of relying on auto-starting instances.
- Avoid leaving an auto-starting MCP_DOCKER instance running when you also use workspace stdio servers (this prevents duplicates and half-configured endpoints).

Example: remove Docker-backed Copilot entry from user settings

```json
{
  // remove or disable this entry if it points to a Docker / HTTP endpoint you don't want
  "github.copilot.chat.mcpServers": []
}
```

References
----------

- Project docs: `_reference/backup/instructions/memory.instructions.md`.
- VS Code: Language Server / SecretStorage docs — <https://code.visualstudio.com/api>
- JetBrains Plugin SDK: <https://plugins.jetbrains.com/docs>
- Amazon Q, Google Vertex/ Gemini, GitHub Copilot public docs (access control + enterprise guides)

If you'd like, I can add: a health-check script for Windows, a CI job to smoke-test MCP servers, or a `.kiro/settings/mcp.example.json` template for users.
