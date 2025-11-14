---
applyTo: "**"
description: "MCP Memory Server configuration, installation, and usage for ICTServe development"
---

# MCP Memory Server Configuration & Usage

## Purpose & Scope

This document provides comprehensive setup, configuration, and usage instructions for the Model Context Protocol (MCP) Memory Server in ICTServe. The memory server provides persistent knowledge graph storage capabilities for MCP clients, enabling cross-session data persistence and retrieval while maintaining compliance with security and data handling standards.

**Scope**: MCP Memory Server installation, configuration, API tools, integration patterns, and troubleshooting for all development environments (VSCode, Kiro IDE, and other MCP-compatible platforms).

**Standards & References**:
- MCP (Model Context Protocol) specification
- Knowledge graph data modeling
- VSCode/Kiro IDE MCP integration guidelines
- ICTServe security requirements (PDPA 2010, D09-D11)

## Core Purpose

The MCP Memory Server serves ICTServe development by:
- **Persisting knowledge** across sessions without manual context transfer
- **Building project graph** with entities (models, features, decisions) and relationships
- **Storing solutions** to common problems for discovery and reuse
- **Tracking decisions** with rationale for architectural accountability
- **Enabling search** across accumulated project learnings

## Startup Protocol (CRITICAL)

**Execute this sequence before first use:**

1. **Install MCP Memory Server**:
   ```bash
   # Using npm (recommended)
   npm install -g @modelcontextprotocol/server-memory

   # Verify installation
   npx @modelcontextprotocol/server-memory --help
   ```

2. **Configure in Development Environment**:
   - Choose configuration method below (VSCode or Kiro IDE)
   - Create config file with proper JSON syntax
   - Verify server starts without errors

3. **Test Connection**:
   ```bash
   # Test server availability
   npx @modelcontextprotocol/server-memory --help

   # Or in your IDE: test with create_entities tool
   ```

4. **Initialize Project Entities**:
   - Create initial entities (project, frameworks, key models)
   - Establish relations between components
   - Add initial observations (status, version info)

## Configuration & Installation

### Core Concepts

#### Entities
The primary nodes in the knowledge graph:
- **Name**: Unique identifier (string, typically snake_case)
- **Entity Type**: Classification category (e.g., "project", "framework", "model", "feature")
- **Observations**: Array of atomic fact strings about the entity

#### Relations
Directed connections between entities:
- **From**: Source entity name (origin)
- **To**: Target entity name (destination)
- **Relation Type**: Active-voice relationship (e.g., "uses", "depends_on", "owns", "references")

#### Observations
Atomic facts attached to entities:
- Stored as individual strings (one fact per observation)
- Independently addable and removable
- Should be concise and specific
- Example: "Updated 2025-11-01", "Status: production-ready"

### Installation Methods

#### Method 1: VSCode User Configuration (Recommended)

**File Location**: `C:\Users\[USERNAME]\.kiro\settings\mcp.json` (Windows) or `~/.kiro/settings/mcp.json` (Unix)

**Configuration**:
```json

  "servers": 
    "memory": 
      "type": "stdio",
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory"]

  

```

**Steps**:
1. Create directory if missing: `mkdir -p ~/.kiro/settings`
2. Create/edit `mcp.json` with configuration above
3. Restart VSCode or IDE
4. Memory server should load automatically

#### Method 2: VSCode Workspace Configuration

**File Location**: `.vscode/mcp.json` (in workspace root)

**Configuration**:
```json

  "servers": 
    "memory": 
      "type": "stdio",
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory"]

  

```

**Benefits**: Workspace-specific memory; can commit to version control (don't store sensitive data)

#### Method 3: Docker Deployment (Alternative)

**Configuration**:
```json

  "servers": 
    "memory": 
      "command": "docker",
      "args": ["run", "-i", "-v", "claude-memory:/app/dist", "--rm", "mcp/memory"]

  

```

**Requirements**: Docker must be installed and running

#### Method 4: Custom Configuration with Storage Path

**Configuration**:
```json

  "servers": 
    "memory": 
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory"],
      "env": 
        "MEMORY_FILE_PATH": "/custom/path/to/memory.jsonl"
  

  

```

**Environment Variables**:
- `MEMORY_FILE_PATH`: Full path to memory storage file (default: `memory.jsonl` in server directory)
- **Note**: Ensure directory exists and has write permissions

## MCP Memory Server API Tools

### Entity Management

#### create_entities
**Purpose**: Add new entities to the knowledge graph

**Input**:
```json

  "entities": [
    
      "name": "ICTServe_Project",
      "entityType": "project",
      "observations": [
        "Laravel 12 enterprise application",
        "PDPA 2010 compliant",
        "Status: Active Development"
    



```

**Behavior**: Ignores entities if name already exists (no duplicates)

#### delete_entities
**Purpose**: Remove entities and cascading relations

**Input**:
```json

  "entityNames": ["Entity_Name_1", "Entity_Name_2"]

```

**Behavior**: Cascades deletion to remove all related relations

### Relation Management

#### create_relations
**Purpose**: Establish connections between entities

**Input**:
```json

  "relations": [
    
      "from": "ICTServe_Project",
      "to": "Laravel_12",
      "relationType": "uses"
,
    
      "from": "ICTServe_Project",
      "to": "Livewire_3",
      "relationType": "uses"



```

**Behavior**: Skips duplicate relations; always use active voice for relationType

#### delete_relations
**Purpose**: Remove specific relations

**Input**:
```json

  "relations": [
    
      "from": "ICTServe_Project",
      "to": "OutdatedLib",
      "relationType": "previously_used"



```

**Behavior**: Only removes specified relations; does not affect entities

### Observation Management

#### add_observations
**Purpose**: Add facts to existing entities

**Input**:
```json

  "observations": [
    
      "entityName": "ICTServe_Project",
      "contents": [
        "Last Updated: 2025-11-01",
        "Test Coverage: 85%",
        "Accessibility: WCAG 2.2 AA Compliant"
    



```

**Behavior**: Fails if entity doesn't exist; use create_entities first

#### delete_observations
**Purpose**: Remove specific facts from entities

**Input**:
```json

  "deletions": [
    
      "entityName": "Asset_Borrow_Feature",
      "observations": ["Status: Deprecated", "Replaced by new system"]



```

**Behavior**: Removes only specified observations; entity remains

### Graph Query Operations

#### read_graph
**Purpose**: Retrieve entire knowledge graph

**Input**: None (no parameters)

**Output**: Complete graph structure with all entities and all relations

**Use Case**: Backup, analysis, or complete graph inspection

#### search_nodes
**Purpose**: Find entities matching query string

**Input**:
```json

  "query": "Borrowing"

```

**Search Scope**: Searches entity names, entity types, and observation content

**Output**: All matching entities with their relations

#### open_nodes
**Purpose**: Retrieve specific entities by name

**Input**:
```json

  "names": ["Laravel_12", "Filament_4", "Livewire_3"]

```

**Output**: Requested entities and all relations between them (subgraph)

## Usage Examples & Patterns

### Example 1: Initialize ICTServe Project Graph

**Step 1: Create Project Entity**
```json

  "entities": [
    
      "name": "ICTServe_Project",
      "entityType": "project",
      "observations": [
        "Enterprise Laravel 12 application",
        "PDPA 2010 compliant",
        "WCAG 2.2 AA accessible",
        "Status: Active Development",
        "Repository: github.com/IzzatFirdaus/ictserve-091025"
    



```

**Step 2: Create Framework Entities**
```json

  "entities": [
    
      "name": "Laravel_12",
      "entityType": "framework",
      "observations": ["PHP 8.2+", "MVC Framework", "Artisan CLI", "Version: 12"]
,
    
      "name": "Livewire_3",
      "entityType": "library",
      "observations": ["Real-time reactive components", "Volt syntax", "Alpine.js integration"]
,
    
      "name": "Filament_4",
      "entityType": "library",
      "observations": ["Admin panel framework", "SDUI (Server-Driven UI)", "Resource management"]



```

**Step 3: Create Relations**
```json

  "relations": [
    
      "from": "ICTServe_Project",
      "to": "Laravel_12",
      "relationType": "uses"
,
    
      "from": "ICTServe_Project",
      "to": "Livewire_3",
      "relationType": "uses"
,
    
      "from": "ICTServe_Project",
      "to": "Filament_4",
      "relationType": "uses"
,
    
      "from": "Filament_4",
      "to": "Livewire_3",
      "relationType": "built_on"



```

### Example 2: Track Feature Development

**Create Feature Entity**:
```json

  "entities": [
    
      "name": "Asset_Borrowing_Feature",
      "entityType": "feature",
      "observations": [
        "Requirement: D03-FR-042",
        "Status: In Development",
        "Started: 2025-10-15",
        "Priority: High",
        "Uses authorization: Spatie roles + policies"
    



```

**Link to Related Entities**:
```json

  "relations": [
    
      "from": "Asset_Borrowing_Feature",
      "to": "Asset_Model",
      "relationType": "implements"
,
    
      "from": "Asset_Borrowing_Feature",
      "to": "BorrowingPolicy",
      "relationType": "uses"



```

### Example 3: Store and Retrieve Solutions

**Store Solution**:
```json

  "entities": [
    
      "name": "500_Error_Resolution",
      "entityType": "solution",
      "observations": [
        "Problem: 500 Internal Server Error on deployment",
        "Root Cause: bootstrap/cache permissions missing",
        "Solution: mkdir -p bootstrap/cache && chmod 775 bootstrap/cache",
        "Success Rate: 99%",
        "Date Documented: 2025-10-20"
    



```

**Search for Related Solutions**:
```json

  "query": "bootstrap cache"

```

## Data Storage & Persistence

### Storage Format

- **Format**: JSONL (JSON Lines) — one JSON object per line
- **Default File**: `memory.jsonl` (in server directory)
- **Custom Path**: Configured via `MEMORY_FILE_PATH` environment variable
- **Permissions**: Ensure directory has write permissions

### Data Persistence

```
Session 1: Create entities and relations
↓
memory.jsonl saved to disk
↓
Session 2: Read existing graph
↓
All previous entities/relations restored
↓
Add new observations or entities
↓
memory.jsonl updated automatically
```

### Backup & Recovery

**Backup Procedures**:
1. Regular backup of `memory.jsonl` to version control
2. Use `read_graph` tool to export complete graph as JSON
3. Store backups in secure location (separate from development)

**Recovery Procedures**:
1. If `memory.jsonl` corrupted: restore from backup
2. If data lost: recreate using documented patterns
3. Use version control history if committed

## Security & Compliance

### Data Handling

**PDPA 2010 Compliance** (Malaysian privacy law):
- ❌ **Never store**: Personal data, identifiers, sensitive information
- ✅ **Store**: Technical facts, patterns, configuration insights
- ✅ **Store**: Feature statuses, architectural decisions, code patterns
- ✅ **Store**: Problem solutions (anonymized)

**Example - WRONG** ❌:
```json

  "observations": ["User email: john@example.com", "Phone: +60-123-456-789"]

```

**Example - CORRECT** ✅:
```json

  "observations": ["User entity type: person", "Has permission: admin_access"]

```

### Access Control

- Store memory files in secure locations
- Implement file system permissions (restrict read/write)
- In shared environments: use workspace-scoped memory
- Regularly audit stored information

### Security Best Practices

- Keep entity names generic (avoid company secrets)
- Sanitize observation content (remove credentials)
- Use descriptive but non-identifying names
- Review memory periodically for sensitive data leakage

## Troubleshooting & Recovery

### Common Issues

**Issue: Server won't start**
- Check Node.js/npm installation: `node --version && npm --version`
- Verify npx availability: `npx --version`
- Confirm package installed: `npm list -g @modelcontextprotocol/server-memory`
- Fix: `npm install -g @modelcontextprotocol/server-memory`

**Issue: Memory file not found**
- Check file permissions: `ls -la memory.jsonl`
- Verify custom path configured correctly in `MEMORY_FILE_PATH`
- Ensure directory exists and is writable
- Fix: `mkdir -p $(dirname $MEMORY_FILE_PATH) && chmod 775 $(dirname $MEMORY_FILE_PATH)`

**Issue: Connection failures**
- Restart IDE after configuration changes
- Check MCP server logs for errors
- Verify JSON syntax in configuration files: `cat ~/.kiro/settings/mcp.json | jq`
- Try creating simple entity to test connection

### Debug Commands

```bash
# Check server availability
npx @modelcontextprotocol/server-memory --help

# List installed MCP servers
npm list -g | grep mcp

# Test Node.js/npm
node --version
npm --version
npx --version

# Validate JSON config
cat ~/.kiro/settings/mcp.json | jq .

# Check file permissions
ls -la memory.jsonl
```

## Integration with ICTServe Development

### Project Memory Structure Template

Start with this entity structure for ICTServe:

```
Entities:
├── ICTServe_Project (project)
├── Laravel_12 (framework)
├── Livewire_3 (library)
├── Filament_4 (library)
├── Asset_Model (model)
├── BorrowingPolicy (policy)
├── AccessibilityCompliance (feature)
└── DatabaseMigration_* (task)

Relations:
├── ICTServe_Project → uses → Laravel_12
├── ICTServe_Project → uses → Livewire_3
├── ICTServe_Project → uses → Filament_4
├── Filament_4 → built_on → Livewire_3
├── Asset_Model → uses → BorrowingPolicy
├── ICTServe_Project → implements → AccessibilityCompliance
```

### Development Workflow Integration

1. **Session Start**: Load memory entities for current feature
2. **During Work**: Add observations as you discover patterns
3. **Problem Solving**: Search memory for related solutions
4. **Completion**: Document final state and lessons learned
5. **Session End**: Review and update observations before closing

## Performance Guidelines

**Optimal Memory Usage**:
- Keep entity names **concise but descriptive** (e.g., `Asset_Borrow_Policy` not `Policy_for_borrowing_assets_in_the_system`)
- Use **consistent naming conventions** (snake_case for entity names, active voice for relations)
- Store **atomic observations** (one fact per observation, not paragraphs)
- **Regular cleanup** of outdated information (mark deprecated, then delete when safe)
- **Monitor file size**: Use `ls -la memory.jsonl` periodically

**Performance Tips**:
- Use specific entity names for `open_nodes` instead of broad `search_nodes` when possible
- Organize entities by type for easier mental model
- Limit observations per entity to essential facts (50-100 max)
- Archive resolved features to separate memory periodically

## References & Documentation

**MCP Memory Server Official Resources**:
- MCP Memory Server Repository: https://github.com/modelcontextprotocol/servers/tree/main/src/memory
- MCP Specification: https://modelcontextprotocol.io/specification
- VSCode MCP Documentation: https://code.visualstudio.com/docs/copilot/mcp
- Knowledge Graph Concepts: https://en.wikipedia.org/wiki/Knowledge_graph

**ICTServe Documentation**:
- `docs/D00_SYSTEM_OVERVIEW.md` — System context and governance
- `docs/D09_DATABASE_DOCUMENTATION.md` — Audit and security requirements
- `docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md` — Infrastructure and compliance
- `.kiro/steering/mcp.md` — Complete MCP servers reference (PRIMARY)
- `.kiro/steering/behavior.md` — Core operational guardrails
- `.agents/memory.instruction.md` — Basic memory management
- `.agents/memory.instructions.md` — Extended learnings and patterns

**For broader MCP context**, refer to `.kiro/steering/mcp.md` which documents:
- All 9 MCP servers with tools and use cases
- Security policies and compliance requirements
- Integration patterns and workflows
- Error handling and recovery procedures

---

**MCP Memory Server Status**: ✅ Production-ready for ICTServe development  
**Last Updated**: 2025-11-01  
**Version**: 2.0.0

The MCP Memory Server is a persistent storage system that implements a knowledge graph for storing and retrieving information across MCP client sessions. It provides tools for managing entities, relations, and observations in a structured graph format.

### Core Concepts

#### Entities
Entities are the primary nodes in the knowledge graph:
- **Name**: Unique identifier (string)
- **Entity Type**: Classification (e.g., "person", "organization", "project")
- **Observations**: Array of string observations about the entity

#### Relations
Relations define directed connections between entities:
- **From**: Source entity name
- **To**: Target entity name
- **Relation Type**: Active voice relationship type (e.g., "works_at", "depends_on")

#### Observations
Atomic pieces of information attached to entities:
- Stored as strings
- Can be added or removed independently
- Should contain one fact per observation

---

## Installation & Setup

### VSCode Configuration

Add the memory server to your MCP configuration:

**Method 1: User Configuration (Recommended)**
```json

  "servers": 
    "memory": 
      "type": "stdio",
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory"]

  

```

**Method 2: Workspace Configuration**
Create `.vscode/mcp.json` in your workspace:
```json

  "servers": 
    "memory": 
      "type": "stdio",
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory"]

  

```

### Docker Setup (Alternative)

```json

  "servers": 
    "memory": 
      "command": "docker",
      "args": ["run", "-i", "-v", "claude-memory:/app/dist", "--rm", "mcp/memory"]

  

```

### Custom Configuration

Configure storage location and other settings:

```json

  "servers": 
    "memory": 
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory"],
      "env": 
        "MEMORY_FILE_PATH": "/path/to/custom/memory.jsonl"
  

  

```

**Environment Variables:**
- `MEMORY_FILE_PATH`: Path to memory storage file (default: `memory.jsonl`)

---

## API Tools

### create_entities
Create multiple new entities in the knowledge graph.

**Input:**
```json

  "entities": [
    
      "name": "string",
      "entityType": "string",
      "observations": ["string"]



```

**Behavior:** Ignores entities with existing names.

### create_relations
Create multiple relations between entities.

**Input:**
```json

  "relations": [
    
      "from": "string",
      "to": "string",
      "relationType": "string"



```

**Behavior:** Skips duplicate relations. Relations must be in active voice.

### add_observations
Add new observations to existing entities.

**Input:**
```json

  "observations": [
    
      "entityName": "string",
      "contents": ["string"]



```

**Behavior:** Fails if entity doesn't exist.

### delete_entities
Remove entities and their associated relations.

**Input:**
```json

  "entityNames": ["string"]

```

**Behavior:** Cascading deletion of relations.

### delete_observations
Remove specific observations from entities.

**Input:**
```json

  "deletions": [
    
      "entityName": "string",
      "observations": ["string"]



```

### delete_relations
Remove specific relations from the graph.

**Input:**
```json

  "relations": [
    
      "from": "string",
      "to": "string",
      "relationType": "string"



```

### read_graph
Read the entire knowledge graph.

**Input:** None

**Output:** Complete graph structure with all entities and relations.

### search_nodes
Search for nodes based on query string.

**Input:**
```json

  "query": "string"

```

**Behavior:** Searches entity names, types, and observation content.

### open_nodes
Retrieve specific nodes by name.

**Input:**
```json

  "names": ["string"]

```

**Output:** Requested entities and relations between them.

---

## Usage Examples

### Basic Entity Creation
```json

  "entities": [
    
      "name": "John_Smith",
      "entityType": "person",
      "observations": ["Speaks fluent Spanish", "Graduated in 2019"]



```

### Creating Relations
```json

  "relations": [
    
      "from": "John_Smith",
      "to": "Anthropic",
      "relationType": "works_at"



```

### Adding Observations
```json

  "observations": [
    
      "entityName": "John_Smith",
      "contents": ["Prefers morning meetings"]



```

### Searching the Graph
```json

  "query": "Spanish"

```

---

## System Prompt Integration

For optimal memory usage, configure your MCP client with a system prompt that guides memory creation:

```
Follow these steps for each interaction:

1. User Identification:
   - Identify the current user context
   - Create entities for recurring people, organizations, and events

2. Memory Retrieval:
   - Begin by retrieving relevant information from the knowledge graph
   - Use "Remembering..." to indicate memory retrieval

3. Memory Categories:
   - Basic Identity (age, gender, location, job title, education)
   - Behaviors (interests, habits, communication preferences)
   - Preferences (communication style, preferred language)
   - Goals (goals, targets, aspirations)
   - Relationships (personal and professional connections)

4. Memory Update:
   - Create entities for new organizations, people, and significant events
   - Connect them using relations
   - Store facts as observations
```

---

## Data Persistence

The memory server stores data in a JSONL (JSON Lines) format by default:
- File: `memory.jsonl` (configurable via `MEMORY_FILE_PATH`)
- Format: One JSON object per line
- Location: Server directory or custom path

### Backup Considerations
- Regular backup of memory files
- Version control for critical memory data
- Recovery procedures for memory file corruption

---

## Troubleshooting

### Common Issues

**Server won't start:**
- Verify Node.js/npm installation
- Check npx availability
- Confirm package name: `@modelcontextprotocol/server-memory`

**Memory file not found:**
- Check file permissions
- Verify custom `MEMORY_FILE_PATH` if configured
- Ensure directory exists and is writable

**Connection failures:**
- Restart VSCode after configuration changes
- Check MCP server logs
- Verify JSON syntax in configuration files

### Debug Commands

Check if server is running:
```bash
npx @modelcontextprotocol/server-memory --help
```

Test basic connectivity:
```bash
# Use MCP client tools to verify connection
```

---

## Integration Patterns

### Development Workflow
1. Configure memory server in VSCode
2. Create entities for project components
3. Establish relations between components
4. Add observations for implementation details
5. Use search to retrieve relevant information

### Project Memory Structure
```
Project Entities:
├── ICTServe (project)
├── Laravel_12 (framework)
├── Livewire_3 (component_system)
└── Filament_4 (admin_panel)

Relations:
├── ICTServe → uses → Laravel_12
├── ICTServe → uses → Livewire_3
└── ICTServe → uses → Filament_4

Observations:
├── Laravel_12: ["PHP 8.2+", "MVC framework", "Artisan CLI"]
├── Livewire_3: ["Real-time components", "Volt syntax", "Alpine.js integration"]
└── Filament_4: ["Admin panels", "Resource management", "Form builders"]
```

---

## Security Considerations

- Memory files contain potentially sensitive information
- Store memory files in secure locations
- Implement access controls for shared environments
- Regular audit of stored information
- Backup encryption for sensitive data

---

## Performance Guidelines

- Keep entity names descriptive but concise
- Use consistent naming conventions
- Limit observation size (prefer atomic facts)
- Regular cleanup of outdated information
- Monitor memory file size growth

---

## References

- MCP Memory Server GitHub [mcp-gh]
- MCP Specification [mcp-spec]
- VSCode MCP Documentation [vscode-mcp]
- Knowledge Graph Concepts [kg]

[mcp-gh]: https://github.com/modelcontextprotocol/servers/tree/main/src/memory
[mcp-spec]: https://modelcontextprotocol.io/specification
[vscode-mcp]: https://code.visualstudio.com/docs/copilot/mcp
[kg]: https://en.wikipedia.org/wiki/Knowledge_graph

---

**This MCP Memory Server enables persistent, structured information storage across development sessions, enhancing context awareness and knowledge management in ICTServe development workflows.**
