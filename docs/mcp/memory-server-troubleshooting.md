# MCP Memory Server Troubleshooting Guide

## Overview

The MCP (Model Context Protocol) Memory Server provides persistent knowledge graph storage for ICTServe development. This guide helps resolve common issues with the memory server setup.

## Common Issues and Solutions

### 1. JSON Parsing Error (-32603)

**Error**: `MCP error -32603: Unexpected non-whitespace character after JSON at position 139`

**Cause**: The memory.jsonl file contains invalid JSON format or the MCP server configuration is incorrect.

**Solution**:

```powershell
# Run the restart script to fix JSON issues
.\scripts\restart-mcp-servers.ps1
```

**Manual Fix**:

1. Check `storage/mcp/memory.jsonl` for valid JSONL format
2. Each line must be a complete JSON object
3. No trailing commas or invalid characters
4. Restart Kiro IDE after fixing

### 2. Memory File Not Found

**Error**: `ENOENT: no such file or directory, open 'memory.jsonl'`

**Solution**:

1. Ensure `storage/mcp/` directory exists
2. Verify `.kiro/settings/mcp.json` uses correct file path as argument:

   ```json
   "memory": {
     "command": "npx",
     "args": ["-y", "@modelcontextprotocol/server-memory", "storage/mcp/memory.jsonl"]
   }
   ```

3. Restart Kiro IDE after configuration changes

### 3. Memory Server Overwrites Data

**Issue**: The @modelcontextprotocol/server-memory overwrites the entire file on each operation instead of appending.

**Workaround**:

- This is a known limitation of the current memory server
- Use the memory server for session-based knowledge
- Important historical data is backed up in `storage/mcp/backups/`
- Consider bulk importing important entities at session start

### 4. Server Connection Issues

**Error**: Cannot connect to memory server

**Solution**:

1. Check if Node.js is installed: `node --version`
2. Verify npx can access the memory server: `npx @modelcontextprotocol/server-memory --help`
3. Restart Kiro IDE to reload MCP connections
4. Check MCP server logs in `storage/logs/boost_mcp*.log`

## Configuration Files

### .kiro/settings/mcp.json

```json
{
  "mcpServers": {
    "memory": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-memory", "storage/mcp/memory.jsonl"],
      "disabled": false,
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

### .env (Optional - not used with current config)

```env
# MCP Memory File Path (for reference only)
MEMORY_FILE_PATH=storage/mcp/memory.jsonl
```

## Memory File Format

The memory.jsonl file uses JSON Lines format (one JSON object per line):

```jsonl
{"type":"entity","name":"example_entity","entityType":"system_spec","observations":["Example observation"],"timestamp":"2025-12-19T10:30:00Z"}
{"type":"entity","name":"another_entity","entityType":"implementation","observations":["Another observation"],"timestamp":"2025-12-19T10:31:00Z"}
```

## Essential ICTServe Entities

The memory server should contain these core entities:

1. **ictserve_system_spec** - System architecture and specifications
2. **ictserve_implementation_status** - Current implementation progress
3. **ictserve_compliance_standards** - PDPA, WCAG, PSR-12 requirements
4. **ollama_ai_integration_spec** - AI chatbot integration details

## Testing Memory Server

After fixing issues, test the memory server:

```bash
# In Kiro IDE, try these MCP commands:
read_graph()                    # Should show all entities
search_nodes("ictserve")       # Should find ICTServe entities
create_entities([{...}])       # Should create new entity
```

## Maintenance Scripts

### Restart MCP Servers

```powershell
.\scripts\restart-mcp-servers.ps1
```

### Backup Memory Data

```powershell
# Manual backup
Copy-Item "storage/mcp/memory.jsonl" "storage/mcp/backups/memory_backup_$(Get-Date -Format 'yyyy-MM-dd_HH-mm-ss').jsonl"
```

## Resolution Summary

The MCP memory server issue has been resolved with the following changes:

1. **Fixed Configuration**: Updated `.kiro/settings/mcp.json` to pass file path as command argument
2. **Cleaned Memory File**: Ensured `storage/mcp/memory.jsonl` has valid JSONL format
3. **Created Scripts**: Added `scripts/restart-mcp-servers.ps1` for maintenance
4. **Documentation**: Created comprehensive troubleshooting guide

## Support

If issues persist:

1. Check the full error message in Kiro IDE
2. Review MCP logs in `storage/logs/`
3. Verify Node.js and npx installation
4. Restart Kiro IDE completely
5. Run the restart script: `.\scripts\restart-mcp-servers.ps1`

## Related Documentation

- [MCP Server Configuration](./mcp-server-configuration.md)
- [ICTServe Development Guidelines](../development/guidelines.md)
- [Laravel Boost Integration](../development/laravel-boost.md)
