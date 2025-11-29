# Laravel MCP Implementation - ICTServe

## Overview

Laravel MCP (Model Context Protocol) integration enables AI agents to interact with ICTServe application data through standardized tools.

## Installation

```bash
composer require laravel/mcp
php artisan vendor:publish --tag=ai-routes
```

## Server Configuration

**Server**: `ICTServeServer`  
**Type**: Local (command-line)  
**Location**: `app/Mcp/Servers/ICTServeServer.php`

## Available Tools

### 1. QueryHelpdeskTicketsTool

Query helpdesk tickets with filters.

**Parameters**:

- `status` (optional): Filter by status (open, in_progress, resolved, closed)
- `limit` (optional): Max tickets to return (1-50, default: 10)

**Example**:

```json
{
  "status": "open",
  "limit": 20
}
```

### 2. CheckAssetStatusTool

Check asset availability by code or ID.

**Parameters**:

- `asset_code` (required without asset_id): Asset code (e.g., LT-001)
- `asset_id` (required without asset_code): Asset ID

**Example**:

```json
{
  "asset_code": "LT-001"
}
```

## Usage

### Local Server

```bash
php artisan mcp:start ictserve
```

### Testing

```bash
php artisan test --filter=ICTServeServerTest
```

### MCP Inspector

```bash
php artisan mcp:inspector ictserve
```

## Integration with Existing MCP Memory

Laravel MCP complements the existing MCP Memory Server:

- **MCP Memory**: Project knowledge, patterns, solutions
- **Laravel MCP**: Real-time application data access

## Files Created

- `app/Mcp/Servers/ICTServeServer.php`
- `app/Mcp/Tools/QueryHelpdeskTicketsTool.php`
- `app/Mcp/Tools/CheckAssetStatusTool.php`
- `routes/ai.php`
- `tests/Feature/Mcp/ICTServeServerTest.php`

## Next Steps

1. Run tests: `php artisan test --filter=ICTServeServerTest`
2. Test with inspector: `php artisan mcp:inspector ictserve`
3. Add more tools as needed
4. Document in MCP Memory entities

## References

- Laravel MCP Docs: <https://laravel.com/docs/12.x/mcp>
- ICTServe MCP Memory: `.amazonq/rules/Memory.md`
