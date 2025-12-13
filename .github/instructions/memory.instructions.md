---
applyTo: "**"
description: "MCP Memory Server reference guide. Documents 9 core tools for persistent knowledge graph management across AI assistant sessions."
---

# MCP Memory Server — Tool Reference

**Purpose**: MCP (Model Context Protocol) Memory Server provides persistent knowledge graph storage, enabling AI assistants to maintain context across sessions through structured entity and relationship management.

**Storage**: `storage/mcp/memory.jsonl` (JSON Lines format)  
**Tools**: 9 core MCP tools for CRUD operations  
**Reference**: See `docs/mcp/MCP_MEMORY_GUIDE.md` for complete documentation

---

## AI Agent Memory Protocol

**CRITICAL**: AI agents MUST follow these steps for each interaction:

### 1. User Identification
- Assume you are interacting with `default_user` unless otherwise specified
- Proactively identify the user if not already known

### 2. Memory Retrieval (REQUIRED)
- **Always begin your chat by saying only "Remembering..."** and retrieve all relevant information from your knowledge graph using `search_nodes()` and `open_nodes()`
- Always refer to your knowledge graph as your "memory"
- Search for user-specific information, project context, and relevant technical details

### 3. Memory Collection Categories
While conversing with the user, be attentive to any new information in these categories:

- **Basic Identity**: age, gender, location, job title, education level, etc.
- **Behaviors**: interests, habits, coding patterns, workflow preferences, etc.
- **Preferences**: communication style, preferred language, frameworks, tools, etc.
- **Goals**: project goals, learning targets, aspirations, feature implementations, etc.
- **Relationships**: personal and professional relationships up to 3 degrees of separation

### 4. Memory Update (REQUIRED)
If any new information was gathered during the interaction, update your memory as follows:

- **Create entities** for recurring organizations, people, and significant events
- **Connect them** to current entities using relations
- **Store facts** about them as observations
- **Update existing entities** with new observations when relevant

### 5. Memory Best Practices
- Store atomic facts (one piece of information per observation)
- Use PascalCase_With_Underscores for entity names
- Create meaningful relations with semantic types (`implements`, `uses`, `documents`, etc.)
- Include temporal context in observations (e.g., "2025-12-13: Implemented feature X")
- Archive obsolete information instead of deleting (e.g., "ARCHIVED: Old pattern (2025-12-13)")

---

## Quick Start Workflow

```javascript
// 1. Search for existing knowledge
search_nodes('your topic')

// 2. Load entities with full details
open_nodes(['Entity_Name_1', 'Entity_Name_2'])

// 3. Create new entity if needed
create_entities([{
  name: 'Your_Entity_Name',
  entityType: 'technical_implementation',
  observations: ['Fact 1', 'Fact 2']
}])

// 4. Link to related entities
create_relations([{
  from: 'Your_Entity_Name',
  to: 'Related_Entity',
  relationType: 'implements'
}])

// 5. Update with new facts
add_observations([{
  entityName: 'Your_Entity_Name',
  contents: ['New fact', 'Another fact']
}])
```

---

## Core Tools Reference

### 1. create_entities

Create new nodes in the knowledge graph.

```json
{
  "entities": [
    {
      "name": "PascalCase_Entity_Name",
      "entityType": "technical_implementation",
      "observations": ["Fact 1", "Fact 2"]
    }
  ]
}
```

**Entity Types**: `technical_implementation`, `solved_issue`, `coding_pattern`, `architectural_decision`, `work_session`, `project_milestone`, `analysis_work`

---

### 2. create_relations

Create directed semantic connections between entities.

```json
{
  "relations": [
    {
      "from": "Source_Entity",
      "to": "Target_Entity",
      "relationType": "implements"
    }
  ]
}
```

**Relation Types**: `implements`, `documents`, `uses`, `resolves`, `extends`, `related_to`, `depends_on`, `blocks`

---

### 3. add_observations

Add new facts to existing entities (non-destructive).

```json
{
  "observations": [
    {
      "entityName": "Existing_Entity",
      "contents": ["New fact 1", "New fact 2"]
    }
  ]
}
```

---

### 4. search_nodes

Semantic search for entities by keyword/topic.

```json
{
  "query": "authentication patterns"
}
```

Returns array of matching entity names.

---

### 5. open_nodes

Retrieve complete entity details (observations + relations).

```json
{
  "names": ["Entity_Name_1", "Entity_Name_2"]
}
```

Returns full entity objects with all data.

---

### 6. read_graph

Retrieve entire knowledge graph.

```json
{}
```

Returns complete graph with all nodes and edges. ⚠️ Large graphs may exceed token limits.

---

### 7. delete_entities

Remove entities and associated relations.

```json
{
  "entityNames": ["Obsolete_Entity_1", "Obsolete_Entity_2"]
}
```

⚠️ Deletion is permanent. Consider archiving via observations instead.

---

### 8. delete_observations

Remove specific facts from entities.

```json
{
  "deletions": [
    {
      "entityName": "Entity_Name",
      "observations": ["Outdated fact to remove"]
    }
  ]
}
```

---

### 9. delete_relations

Remove connections between entities.

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

---

## Entity Naming Conventions

**Format**: `PascalCase_With_Underscores`

**Good Examples**:
- `JWT_Token_Implementation`
- `Error_Handling_Patterns`
- `Database_Migration_Strategy`
- `Accessibility_Checklist`

**Avoid**:
- ❌ `jwt` (too vague)
- ❌ `thing_1` (meaningless)
- ❌ `New-Component-123` (hyphens, numbers)

---

## Best Practices

1. **Search First**: Always check for existing entities before creating
2. **Atomic Observations**: One fact per observation
3. **Link Everything**: Connect implementations to patterns/requirements
4. **Temporal Tracking**: Date observations for context (`2025-12-15: Updated X`)
5. **Archive Instead of Delete**: Mark obsolete patterns (`ARCHIVED: Old pattern (date)`)
6. **Semantic Relations**: Use specific relation types, not just `related_to`

---

## JSON Validation

| Issue | Fix |
|-------|-----|
| Line breaks in strings | Use single line or `\\n` |
| Unescaped quotes | Use `\"` |
| Trailing commas | Remove comma |
| Single quotes | Use double quotes |

---

## Configuration

**VS Code** (`.vscode/mcp.json`):
```json
{
  "servers": {
    "memory": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory", "${workspaceFolder}/storage/mcp/memory.jsonl"]
    }
  }
}
```

---

## Security

**Never store secrets** in observations. Store references instead:
```javascript
// ❌ BAD: "API key: sk_live_abc123..."
// ✅ GOOD: "Uses API with bearer token from .env (API_SECRET_KEY)"
```

---

**Package**: `@modelcontextprotocol/server-memory`
