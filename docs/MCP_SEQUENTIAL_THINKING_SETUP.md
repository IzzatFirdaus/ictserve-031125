# MCP Sequential Thinking Server Setup (Docker)

## Quick Start

```powershell
# Start Sequential Thinking Server
docker compose up -d sequential-thinking

# Check status
docker compose ps sequential-thinking

# View logs
docker compose logs -f sequential-thinking
```

## What is Sequential Thinking?

A structured problem-solving tool that:
- Breaks complex problems into manageable steps
- Revises and refines thoughts as understanding deepens
- Branches into alternative reasoning paths
- Adjusts thought count dynamically
- Generates and verifies solution hypotheses

## Tool: sequential_thinking

### Inputs

- `thought` (string): Current thinking step
- `nextThoughtNeeded` (boolean): Whether another step is needed
- `thoughtNumber` (integer): Current thought number
- `totalThoughts` (integer): Estimated total thoughts needed
- `isRevision` (boolean, optional): Revising previous thinking
- `revisesThought` (integer, optional): Which thought to reconsider
- `branchFromThought` (integer, optional): Branching point
- `branchId` (string, optional): Branch identifier
- `needsMoreThoughts` (boolean, optional): Need more thoughts

## Use Cases

Perfect for:
- Breaking down complex ICTServe features
- Planning database migrations with revision
- Analyzing architecture decisions
- Debugging with course correction
- Tasks where scope isn't clear initially
- Filtering irrelevant information

## Integration with Claude Desktop

Add to `claude_desktop_config.json`:

```json
{
  "mcpServers": {
    "sequential-thinking": {
      "command": "docker",
      "args": ["run", "--rm", "-i", "mcp/sequentialthinking"]
    }
  }
}
```

## Integration with VS Code

Add to `.vscode/mcp.json`:

```json
{
  "servers": {
    "sequential-thinking": {
      "command": "docker",
      "args": ["run", "--rm", "-i", "mcp/sequentialthinking"]
    }
  }
}
```

## Configuration

### Disable Thought Logging

Set environment variable in `docker-compose.yml`:

```yaml
environment:
  - DISABLE_THOUGHT_LOGGING=true
```

## Example Usage

### Problem: Design Dual Approval Workflow

```javascript
// Thought 1: Understand requirements
sequential_thinking({
  thought: "Need dual approval for loan applications. First approver: Unit Head, Second: Manager",
  nextThoughtNeeded: true,
  thoughtNumber: 1,
  totalThoughts: 5
})

// Thought 2: Consider database schema
sequential_thinking({
  thought: "Create loan_approvals table with level (1,2), approver_id, status, signed_link_hash",
  nextThoughtNeeded: true,
  thoughtNumber: 2,
  totalThoughts: 5
})

// Thought 3: Revise - add email workflow
sequential_thinking({
  thought: "Need email-based approval with JWT tokens. Add email_sent_at, approved_at timestamps",
  nextThoughtNeeded: true,
  thoughtNumber: 3,
  totalThoughts: 6,
  isRevision: true,
  revisesThought: 2,
  needsMoreThoughts: true
})

// Continue until solution complete...
```

## ICTServe Use Cases

### 1. Feature Planning
```
Thought 1: Understand requirement (SRS-FR-001)
Thought 2: Design database schema
Thought 3: Plan service layer
Thought 4: Design Livewire components
Thought 5: Plan testing strategy
```

### 2. Bug Investigation
```
Thought 1: Reproduce error (500 Internal Server Error)
Thought 2: Check logs (storage/logs/laravel.log)
Thought 3: Identify root cause (DB connection)
Thought 4: Branch A - Fix .env config
Thought 4: Branch B - Check MySQL service
Thought 5: Verify fix works
```

### 3. Architecture Decision
```
Thought 1: Problem - Choose between Filament vs Livewire
Thought 2: Analyze requirements (CRUD + bulk actions)
Thought 3: Compare capabilities
Thought 4: Decision - Use Filament (bulk actions critical)
Thought 5: Document rationale
```

## Helper Scripts

```powershell
# Start
docker compose up -d sequential-thinking

# Stop
docker compose stop sequential-thinking

# Restart
docker compose restart sequential-thinking

# View logs
docker compose logs -f sequential-thinking

# Check status
docker compose ps sequential-thinking
```

## Troubleshooting

### Container won't start
```powershell
docker compose logs sequential-thinking
docker compose restart sequential-thinking
```

### Tool not responding
```powershell
# Check container is running
docker compose ps sequential-thinking

# Restart container
docker compose restart sequential-thinking
```

## References

- Official Docs: https://github.com/modelcontextprotocol/servers/tree/main/src/sequentialthinking
- MCP Protocol: https://modelcontextprotocol.io
- ICTServe Memory Guide: `.amazonq/rules/Memory.md`
