# MCP Configuration Changes — 2025-11-10

Action: Updated workspace MCP server configuration at `.kiro/settings/mcp.json`.

What changed:

- Removed hard-coded API keys from the workspace file and replaced them with user-level placeholders (`$input:...`) per security policy.
- Added explicit `disabled: true` entries for several duplicate/alternate server identifiers (examples: `github/github-mcp-server`, `microsoft/playwright-mcp`, `upstash/context7`, `gitkraken`, `java-app-modernization-deploy`) so the IDE does not display duplicate MCP servers.
- Left canonical workspace servers (e.g., `laravel-boost`, `memory`, `context7`, `deepl`) configured and active by default.


Rationale:

- Workspace-level API keys and multiple server identifiers caused the IDE to show duplicate MCP server entries. Moving secrets to user-level config and disabling duplicate identifiers reduces noise and follows repository security guidance.


Verification steps:

1. Restart VS Code / Kiro IDE or reload the MCP extension.
2. Open the MCP Servers panel and confirm duplicates are suppressed.
3. If duplicates persist, check the user-level config at `C:\Users\[USERNAME]\.kiro\settings\mcp.json` and set `disabled: true` for undesired entries.


Timestamp: 2025-11-10
Author: automated edit by agent
