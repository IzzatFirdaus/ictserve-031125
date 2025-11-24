# Sequential Thinking MCP Server Setup

## Overview

Sequential Thinking MCP Server provides structured problem-solving through step-by-step thinking processes. Perfect for complex ICTServe development tasks.

## Quick Start

### Docker Setup (Recommended)

1. **Start the server**:
   ```powershell
   ./scripts/docker/start-sequential-thinking.ps1
   ```

2. **Stop the server**:
   ```powershell
   ./scripts/docker/stop-sequential-thinking.ps1
   ```

### Manual Docker Commands

```bash
# Pull and run the image
docker run --rm -i mcp/sequentialthinking

# Or build from source
docker build -t mcp/sequentialthinking -f src/sequentialthinking/Dockerfile .
```

## VS Code Integration

The MCP configuration is already set up in `.vscode/mcp.json`. The Sequential Thinking server will be available in VS Code with the MCP extension.

## Usage

The `sequential_thinking` tool helps with:

- Breaking down complex ICTServe features into manageable steps
- Planning Livewire component architecture
- Analyzing database schema changes
- Debugging multi-step processes
- Refining implementation approaches

### Tool Parameters

- `thought` (string): Current thinking step
- `nextThoughtNeeded` (boolean): Whether another step is needed
- `thoughtNumber` (integer): Current step number
- `totalThoughts` (integer): Estimated total steps
- `isRevision` (boolean): Whether revising previous thinking
- `revisesThought` (integer): Which thought to reconsider
- `branchFromThought` (integer): Branching point
- `branchId` (string): Branch identifier
- `needsMoreThoughts` (boolean): If more steps needed

## Environment Variables

- `DISABLE_THOUGHT_LOGGING=false` - Enable thought logging (default)

## Status Check

```powershell
docker ps --filter "name=ictserve_sequential_thinking"
docker logs ictserve_sequential_thinking
```

## Integration with ICTServe

Use Sequential Thinking for:

1. **Feature Planning**: Break down complex features like dual approval workflow
2. **Architecture Decisions**: Analyze Filament vs Livewire component choices
3. **Database Design**: Plan migration sequences and relationships
4. **Debugging**: Step through complex error scenarios
5. **Code Review**: Systematic analysis of implementation approaches

## Troubleshooting

- Ensure Docker is running
- Check network connectivity: `docker network ls | grep ictserve`
- View logs: `docker logs ictserve_sequential_thinking --tail=50`