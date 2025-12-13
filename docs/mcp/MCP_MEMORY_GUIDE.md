# MCP Memory Server - Complete Guide

**Last Updated**: 2025-12-09  
**Status**: ✅ Operational  
**Storage**: `storage/mcp/memory.jsonl`

---

## Table of Contents

1. [Overview](#overview)
2. [Quick Start](#quick-start)
3. [Setup & Configuration](#setup--configuration)
4. [Usage Examples](#usage-examples)
5. [Error Resolution](#error-resolution)
6. [Troubleshooting](#troubleshooting)
7. [Best Practices](#best-practices)

---

## Overview

The MCP Memory Server provides persistent knowledge graph storage across AI assistant sessions. It enables:

- **Entity Management**: Store concepts, facts, decisions, patterns
- **Relationship Mapping**: Link entities with semantic relations
- **Cross-Session Persistence**: Maintain context between coding sessions
- **Semantic Search**: Query stored knowledge by keywords

### Core Concepts

**Entities**: Nodes in the knowledge graph with:

- `name` (unique identifier, PascalCase_Convention)
- `entityType` (e.g., "technical_implementation", "solved_issue")
- `observations[]` (array of atomic facts)

**Relations**: Directed connections between entities:

- `from` (source entity name)
- `to` (target entity name)
- `relationType` (e.g., "implements", "documents", "uses")

**Observations**: Atomic facts about entities stored as strings

---

## Quick Start

### Installation

```bash
# Install globally
npm install -g @modelcontextprotocol/server-memory

# Or use npx (no installation needed)
npx -y @modelcontextprotocol/server-memory
```

### Configuration

**VS Code** (`.vscode/mcp.json`):

```json
{
  "servers": {
    "memory": {
      "command": "npx",
      "args": [
        "-y",
        "@modelcontextprotocol/server-memory",
        "${workspaceFolder}/storage/mcp/memory.jsonl"
      ]
    }
  }
}
```

**Kiro IDE** (`.kiro/settings/mcp.json`):

```json
{
  "mcpServers": {
    "memory": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory"],
      "autoApprove": [
        "create_entities",
        "create_relations",
        "add_observations",
        "delete_entities",
        "delete_observations",
        "delete_relations",
        "read_graph",
        "search_nodes",
        "open_nodes"
      ]
    }
  }
}
```

### Storage Setup

```powershell
# Create storage directory
New-Item -ItemType Directory -Path storage/mcp -Force

# Create empty memory file
New-Item -ItemType File -Path storage/mcp/memory.jsonl -Force
```

---

## Setup & Configuration

### Docker Setup (Alternative)

**Docker Compose** (`docker-compose.yml`):

```yaml
services:
  memory:
    image: mcp/memory:latest
    container_name: ictserve-memory
    volumes:
      - memory-data:/app/dist
    restart: unless-stopped

volumes:
  memory-data:
```

**Start Docker server**:

```powershell
docker compose up -d memory
```

**Configuration for Docker**:

```json
{
  "servers": {
    "memory": {
      "command": "docker",
      "args": [
        "exec",
        "-i",
        "ictserve-memory",
        "node",
        "/app/dist/index.js"
      ]
    }
  }
}
```

### Data Persistence

**Backup memory file**:

```powershell
# Create timestamped backup
Copy-Item storage/mcp/memory.jsonl storage/mcp/memory-backup-$(Get-Date -Format 'yyyyMMdd').jsonl
```

**Restore from backup**:

```powershell
Copy-Item storage/mcp/memory-backup-20251209.jsonl storage/mcp/memory.jsonl -Force
```

---

## Usage Examples

### 1. create_entities

**Purpose**: Create new knowledge entities

**✅ CORRECT Format**:

```json
{
  "entities": [
    {
      "name": "ICTServe_v3_6_Theme_System",
      "entityType": "technical_implementation",
      "observations": [
        "v3.6.0: Implemented light-default theme system with optional dark mode",
        "Theme switcher component updated: removed system option, only light and dark allowed",
        "Default theme: light (immutable), user can opt-in to dark mode",
        "localStorage persistence: theme stored as themePreference key",
        "Tailwind config: darkMode class, min-h-44 and min-w-44 for WCAG touch targets",
        "Status: Phase 1 complete, layouts configured for light-first design"
      ]
    }
  ]
}
```

**❌ WRONG Format** (causes JSON parse error):

```json
{
  "entities": [
    {
      "name": "ICTServe_Theme",
      "entityType": "technical_implementation",
      "observations": [
        "v3.6.0: Implemented light-default theme system
        with optional dark mode"  // ❌ Line break causes error at position ~139
      ]
    }
  ]
}
```

### 2. create_relations

**Purpose**: Link entities with semantic relationships

```json
{
  "relations": [
    {
      "from": "ICTServe_v3_6_Theme_System",
      "to": "D03_Software_Requirements",
      "relationType": "implements"
    },
    {
      "from": "ICTServe_v3_6_Theme_System",
      "to": "Tailwind_Configuration",
      "relationType": "uses"
    }
  ]
}
```

**Common Relation Types**:

- `implements` - Implementation → Requirement
- `documents` - Documentation → Feature
- `uses` - Feature → Library/Tool
- `related_to` - General connection
- `resolves` - Fix → Issue
- `extends` - Enhancement → Base feature

### 3. add_observations

**Purpose**: Add facts to existing entities

```json
{
  "observations": [
    {
      "entityName": "ICTServe_v3_6_Theme_System",
      "contents": [
        "2025-12-08: Added WCAG 2.2 AA touch target minimum sizes",
        "2025-12-08: Verified theme persistence across all layouts",
        "Performance: Theme switch completes in <50ms"
      ]
    }
  ]
}
```

### 4. search_nodes

**Purpose**: Find entities by keyword

```json
{
  "query": "theme system"
}
```

**Returns**: Matching entities with their observations

### 5. open_nodes

**Purpose**: Retrieve specific entities by name

```json
{
  "names": [
    "ICTServe_v3_6_Theme_System",
    "Tailwind_Configuration"
  ]
}
```

### 6. delete_entities

**Purpose**: Remove entities from knowledge graph

```json
{
  "entityNames": [
    "Obsolete_Entity_Name"
  ]
}
```

### 7. delete_observations

**Purpose**: Remove specific facts from entities

```json
{
  "deletions": [
    {
      "entityName": "ICTServe_v3_6_Theme_System",
      "observations": [
        "Outdated observation to remove"
      ]
    }
  ]
}
```

### 8. delete_relations

**Purpose**: Remove connections between entities

```json
{
  "relations": [
    {
      "from": "Source_Entity",
      "to": "Target_Entity",
      "relationType": "obsolete_relation"
    }
  ]
}
```

### 9. read_graph

**Purpose**: Retrieve entire knowledge graph

**No parameters required** - returns all entities and relations

---

## Error Resolution

### Common Error: JSON Parse Error at Position 139

**Error Message**:

```
Error: MCP -32603: Unexpected non-whitespace character after JSON at position 139
```

**Root Cause**: Malformed JSON input to memory tools

**Common Causes**:

1. **Line breaks inside string values** (most common)
2. **Unescaped quotes** within strings
3. **Missing commas** or **trailing commas**
4. **Invalid JSON structure**

**Solution**: Ensure proper JSON formatting

| Mistake | Example | Fix |
|---------|---------|-----|
| Line breaks in strings | `"text\nmore text"` | Use single line or `\\n` |
| Unescaped quotes | `"He said "hello""` | Use `\"`: `"He said \"hello\""` |
| Trailing comma | `["item1", "item2",]` | Remove last comma |
| Missing comma | `["item1" "item2"]` | Add comma: `["item1", "item2"]` |
| Single quotes | `{'key': 'value'}` | Use double quotes: `{"key": "value"}` |

### Validation Methods

**PowerShell**:

```powershell
$json = @'
{
  "entities": [...]
}
'@

try {
    $json | ConvertFrom-Json
    Write-Host "✅ Valid JSON"
} catch {
    Write-Host "❌ Invalid JSON: $($_.Exception.Message)"
}
```

**Online Validator**: <https://jsonlint.com/>

**VS Code**: Create `.json` file and paste - errors show in real-time

---

## Troubleshooting

### Server Won't Start

**Check Node.js installation**:

```bash
node --version  # Should be v18+
npm --version
```

**Install package globally**:

```bash
npm install -g @modelcontextprotocol/server-memory
```

**Test server manually**:

```bash
npx -y @modelcontextprotocol/server-memory ./storage/mcp/memory.jsonl
```

### Memory File Issues

**File not found**:

```powershell
# Create directory and file
New-Item -ItemType Directory -Path storage/mcp -Force
New-Item -ItemType File -Path storage/mcp/memory.jsonl -Force
```

**Permission errors**:

```powershell
# Fix permissions (Windows)
icacls storage\mcp\memory.jsonl /grant:r "$env:USERNAME:(F)"
```

**Corrupted memory file**:

```powershell
# Validate JSONL format
Get-Content storage/mcp/memory.jsonl | ForEach-Object {
    try {
        $_ | ConvertFrom-Json | Out-Null
    } catch {
        Write-Host "Invalid JSON at line: $_"
    }
}
```

### Docker Issues

**Container not running**:

```powershell
# Check status
docker ps --filter "name=ictserve-memory"

# View logs
docker compose logs memory

# Restart container
docker compose restart memory
```

**Volume permission issues**:

```powershell
# Fix volume permissions
docker compose exec memory chmod -R 755 /app/dist
```

---

## Best Practices

### Entity Naming Conventions

**✅ GOOD**:

- `ICTServe_v3_6_Theme_System`
- `Livewire_Volt_Compliance_Audit_2025-01-06`
- `Memory_Graph_Implementation_2025-11-15`

**❌ BAD**:

- `theme system` (spaces)
- `ICTServe-Theme` (inconsistent separator)
- `feature1` (not descriptive)

**Rules**:

- PascalCase with underscores
- Include dates for time-sensitive entities
- Descriptive, not generic
- No spaces or special characters

### Entity Types

Common entity types for ICTServe:

- `technical_implementation` - Completed features
- `solved_issue` - Bug fixes and solutions
- `coding_pattern` - Reusable patterns
- `architectural_decision` - Design decisions
- `compliance_implementation` - WCAG, PDPA compliance
- `work_session` - Agent session summaries
- `project_milestone` - Major completions
- `canonical_document` - System specs (D00-D15)
- `analysis_work` - Research, investigations
- `user_request` - Current task context

### Observation Guidelines

**Keep observations atomic** - One fact per observation:

**✅ GOOD**:

```json
"observations": [
  "Uses Laravel 12",
  "Deployed on Docker",
  "MySQL 8.0 database"
]
```

**❌ BAD**:

```json
"observations": [
  "Uses Laravel 12, deployed on Docker with MySQL 8.0 database and Redis cache"
]
```

### Security Considerations

**❌ NEVER store secrets**:

```json
{
  "observations": [
    "API key: sk_live_abc123..."  // ❌ NEVER
  ]
}
```

**✅ Store references instead**:

```json
{
  "observations": [
    "Uses Stripe API with bearer token from .env (STRIPE_SECRET_KEY)",
    "Token retrieval: config('services.stripe.secret')"
  ]
}
```

### Maintenance

**Weekly**:

- Backup memory file
- Review entity count (prune if >100MB)

**Monthly**:

- Update npm package: `npm update -g @modelcontextprotocol/server-memory`
- Archive old entities (move to separate backup file)

**Quarterly**:

- Review entity naming consistency
- Consolidate duplicate entities
- Update documentation with new patterns

---

## Integration with ICTServe

### Example: Storing New Feature

```json
{
  "entities": [
    {
      "name": "ICTServe_Email_Notification_System_v3_5",
      "entityType": "technical_implementation",
      "observations": [
        "Dual approval workflow implemented with email notifications",
        "Uses Laravel Mailables with Markdown templates",
        "Queued jobs via Redis for async delivery",
        "Bilingual support: English and Bahasa Melayu",
        "PDPA compliant: no sensitive data in email body",
        "SLA tracking: sends reminder if no approval within 24 hours",
        "Test coverage: 100% (12 feature tests)",
        "Status: Production-ready as of 2025-11-30"
      ]
    }
  ]
}
```

**Then link to requirements**:

```json
{
  "relations": [
    {
      "from": "ICTServe_Email_Notification_System_v3_5",
      "to": "D03_Software_Requirements",
      "relationType": "implements"
    },
    {
      "from": "ICTServe_Email_Notification_System_v3_5",
      "to": "Laravel_Queue_Service",
      "relationType": "uses"
    }
  ]
}
```

### Workflow Integration

**At Task Start**:

```json
{
  "entities": [
    {
      "name": "user_request_2025_12_09_Theme_Toggle",
      "entityType": "user_request",
      "observations": [
        "User requested: Implement theme toggle feature",
        "Start time: 2025-12-09T10:00:00Z",
        "Scope: Layouts, Tailwind config, localStorage",
        "Related D-docs: D03-FR-XXX, D04 §X.X"
      ]
    }
  ]
}
```

**During Work**:

```json
{
  "observations": [
    {
      "entityName": "user_request_2025_12_09_Theme_Toggle",
      "contents": [
        "Created ThemeToggle Livewire component",
        "Updated all layouts with theme init script",
        "Configured Tailwind darkMode: 'class'",
        "Added WCAG 2.2 AA touch target sizes"
      ]
    }
  ]
}
```

**At Completion**:

```json
{
  "observations": [
    {
      "entityName": "user_request_2025_12_09_Theme_Toggle",
      "contents": [
        "Status: Completed",
        "Modified files: 8 (layouts, components, config)",
        "Test coverage: 95% (new tests: 4)",
        "WCAG compliance: Verified AA standards",
        "Performance: Theme switch <50ms"
      ]
    }
  ]
}
```

---

## References

- **Official MCP Memory Docs**: <https://github.com/modelcontextprotocol/servers/tree/main/src/memory>
- **MCP Specification**: <https://modelcontextprotocol.io>
- **ICTServe Documentation**: `docs/D00_SYSTEM_OVERVIEW.md` through `D15_UI_UX_STYLE_GUIDE.md`
- **Steering Documentation**: `.kiro/steering/mcp.md`

---

## Support

For issues or questions:

1. Check this guide for troubleshooting steps
2. Review ICTServe documentation in `docs/` directory
3. Check MCP server logs in IDE output panel
4. Contact: <devops@motac.gov.my>

---

**Status**: ✅ Production-ready  
**Last Verified**: 2025-12-09  
**Maintainer**: ICTServe Development Team
