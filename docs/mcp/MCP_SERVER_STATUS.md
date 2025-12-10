# MCP Server Status & Configuration

**Last Updated* 2025-12-09  
**Memory System**: MCP Memory Server with JSONL storage

## Active MCP Servers (16 Total)

### ✅ Core Servers (Working)

1. **sequentialthinking** - Complex problem decomposition
   - Command: `npx @modelcontextprotocol/server-sequential-thinking`
   - Status: ✅ Operational
   - Auto-approve: sequentialthinking

2. **memory** - Knowledge graph storage
   - Command: `npx @modelcontextprotocol/server-memory`
   - Storage: `storage/mcp/memory.jsonl`
   - Status: ✅ Operational
   - Auto-approve: create_entities, create_relations, add_observations, delete_entities, delete_observations, delete_relations, read_graph, search_nodes, open_nodes

3. **fetch** - HTTP/API requests
   - Command: `uvx --native-tls mcp-server-fetch`
   - Status: ✅ Operational
   - Auto-approve: fetch

### 🔧 Browser & Testing Servers

4. **chrome-devtools** - Browser inspection
   - Command: `npx chrome-devtools-mcp@latest`
   - Status: ✅ Operational
   - Auto-approve: navigate_page, take_snapshot, click, fill, evaluate_script, list_pages, list_console_messages, list_network_requests, get_network_request, take_screenshot, get_console_message, resize_page, emulate, select_page

5. **playwright** - E2E testing
   - Command: `npx @playwright/mcp@latest`
   - Status: ✅ Operational
   - Auto-approve: browser_navigate, browser_click, browser_snapshot, browser_fill, browser_evaluate, browser_close

### 📚 Documentation & Context Servers

6. **context7** - Library documentation
   - Command: `npx @upstash/context7-mcp`
   - Requires: CONTEXT7_API_KEY
   - Status: ⚠️ Requires API key
   - Auto-approve: resolve-library-id, get-library-docs

7. **laravel-boost** - Laravel-specific tools
   - Command: `php artisan boost:mcp`
   - Status: ✅ Operational
   - Auto-approve: application-info, browser-logs, database-connections, database-query, database-schema, get-absolute-url, get-config, last-error, list-artisan-commands, list-available-config-keys, list-available-env-vars, list-routes, read-log-entries, report-feedback, search-docs, tinker

### 🌐 Web & Data Servers

8. **firecrawl** - Web scraping
   - Command: `npx firecrawl-mcp`
   - Requires: FIRECRAWL_API_KEY
   - Status: ⚠️ Requires API key
   - Auto-approve: None (manual approval required)

### 🌍 Translation Servers

9. **deepl** - Translation service
   - Command: `npx deepl-mcp-server`
   - Requires: DEEPL_API_KEY
   - Status: ⚠️ Requires API key
   - Auto-approve: get_source_languages, get_target_languages, translate_text, rephrase_text

### 🔗 Integration Servers

10. **github** - GitHub operations
    - Command: `npx @modelcontextprotocol/server-github`
    - Requires: PAT_GITHUB_ACCESS_TOKEN
    - Status: ⚠️ Requires token
    - Auto-approve: None (manual approval required)

11. **gitkraken** - Git operations
    - Command: `gk mcp`
    - Status: ✅ Operational
    - Auto-approve: git_status, git_add_or_commit, git_log_or_diff, git_push

12. **figma** - Figma design integration
    - URL: <https://mcp.figma.com/mcp>
    - Status: ✅ Operational
    - Auto-approve: None (manual approval required)

### ☁️ AWS Servers

13. **bedrock-opus** - AWS Bedrock Claude models
    - Command: `node mcp-servers/bedrock-server.js`
    - Requires: AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY
    - Status: ⚠️ Requires AWS credentials
    - Auto-approve: invoke_claude_opus, invoke_claude_sonnet, invoke_claude_haiku

14. **agentcore-mcp-server** - AWS AgentCore
    - Command: `uvx awslabs.amazon-bedrock-agentcore-mcp-server@latest`
    - Status: ✅ Operational
    - Auto-approve: search_agentcore_docs, fetch_agentcore_doc, manage_agentcore_runtime, manage_agentcore_memory, manage_agentcore_gateway

15. **strands-mcp-server** - Strands agents
    - Command: `uvx strands-agents-mcp-server`
    - Status: ✅ Operational
    - Auto-approve: search_docs, fetch_doc

## Environment Variables Required

Add these to your `.env` file:

```env
# Context7 (Library Documentation)
CONTEXT7_API_KEY=your_key_here

# Firecrawl (Web Scraping)
FIRECRAWL_API_KEY=your_key_here

# DeepL (Translation)
DEEPL_API_KEY=your_key_here

# GitHub (Repository Operations)
PAT_GITHUB_ACCESS_TOKEN=your_token_here

# AWS Bedrock (Claude Models)
AWS_ACCESS_KEY_ID=your_key_here
AWS_SECRET_ACCESS_KEY=your_secret_here
AWS_BEDROCK_REGION=us-east-1
```

## Memory System Configuration

### Storage Location

- **File**: `storage/mcp/memory.jsonl`
- **Format**: JSON Lines (one entity per line)
- **Backup**: `storage/mcp/memory-backup-*.jsonl`

### Memory Tools Available

- `create_entities` - Create new knowledge entities
- `create_relations` - Link entities with semantic relations
- `add_observations` - Add facts to existing entities
- `delete_entities` - Remove entities
- `delete_observations` - Remove specific facts
- `delete_relations` - Remove connections
- `read_graph` - Read entire knowledge graph
- `search_nodes` - Search by keywords
- `open_nodes` - Retrieve specific entities

### Entity Types

- `canonical_document` - System specs, requirements
- `technical_implementation` - Completed features
- `coding_pattern` - Reusable patterns
- `solved_issue` - Bug fixes
- `compliance_implementation` - Accessibility, security
- `architectural_decision` - Design decisions
- `blocker` - Blocking issues
- `work_session` - Agent session summaries
- `project_milestone` - Major completions
- `analysis_work` - Research, investigations
- `user_request` - Current task context

## Troubleshooting

### Server Won't Start

1. Check if required environment variables are set
2. Verify command is available (`npx`, `uvx`, `php`, `node`, `gk`)
3. Check Kiro IDE MCP Server view for error messages
4. Reconnect server from MCP Server view

### Memory Server Issues

1. Verify file exists: `storage/mcp/memory.jsonl`
2. Check file permissions (read/write)
3. Validate JSON format (each line must be valid JSON)
4. Reconnect memory server from MCP Server view

### API Key Issues

1. Add keys to `.env` file
2. Restart Kiro IDE to reload environment
3. Verify key format matches service requirements

## Removed Services

### ❌ Mimir (Deprecated)

- **Reason**: Replaced by MCP Memory Server
- **Migration**: All knowledge migrated to `storage/mcp/memory.jsonl`
- **Action**: No action required - system uses MCP Memory Server

### ❌ Neo4j (Deprecated)

- **Reason**: No longer needed with JSONL storage
- **Migration**: Complete
- **Action**: No action required

## Configuration Files

All MCP servers are configured in:

- **Kiro IDE**: `.kiro/settings/mcp.json`
- **VS Code**: `.vscode/mcp.json`
- **Cursor**: `.cursor/mcp.json`
- **Other IDEs**: Check respective config directories

## Next Steps

1. ✅ Memory server operational with JSONL storage
2. ⚠️ Add API keys for optional services (Context7, Firecrawl, DeepL, GitHub)
3. ⚠️ Configure AWS credentials for Bedrock (optional)
4. ✅ All core development servers operational
