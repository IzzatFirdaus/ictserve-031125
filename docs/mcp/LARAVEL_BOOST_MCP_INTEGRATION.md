# Laravel Boost MCP Integration - Configuration Guide

**Last Updated**: 2025-12-16  
**Version**: ICTServe v3.6.0  
**Laravel Boost**: v1.8.3  
**Laravel MCP**: v0.3.4

---

## Overview

This document details the proper configuration of Laravel Boost MCP server integration within the ICTServe Laravel 12 application, including troubleshooting connection timeouts and protocol negotiation issues.

## Architecture: Two MCP Server Approaches

ICTServe implements **dual Laravel Boost MCP server support**:

### 1. Direct Laravel Boost MCP Server

- **Command**: `php artisan boost:mcp`
- **Type**: Standalone MCP server
- **Use Case**: Direct integration with AI tools
- **Issues**: Connection timeouts, protocol negotiation problems

### 2. Laravel MCP Framework Integration

- **Command**: `php artisan mcp:start laravel-boost`
- **Type**: Laravel MCP framework wrapper
- **Use Case**: Integrated with Laravel's MCP ecosystem
- **Benefits**: Better protocol negotiation, improved stability

## Problem Analysis

### Connection Timeout Issues

**Symptoms**:

```
[error] [laravel-boost] Error connecting to MCP server: MCP error -32001: Request timed out
[error] [laravel-boost] Error connecting to MCP server: MCP error -32000: Connection closed
[error] [laravel-boost] MCP server connection and syncing tools and resources timed out after 5 minutes
```

**Root Causes**:

1. **Missing Environment Variables**: `MCP_CONNECTION_MODE=persistent` not set
2. **Working Directory Issues**: MCP server not running from correct Laravel root
3. **Protocol Negotiation**: Direct `boost:mcp` command incompatible with Kiro IDE expectations

## Solution Implementation

### Updated MCP Configuration

**Before** (Problematic):

```json
{
  "laravel-boost": {
    "command": "php",
    "args": ["artisan", "boost:mcp"],
    "disabled": false
  }
}
```

**After** (Fixed):

```json
{
  "laravel-boost": {
    "command": "php",
    "args": ["artisan", "mcp:start", "laravel-boost"],
    "cwd": "C:\\XAMPP\\htdocs\\ictserve-031125",
    "env": {
      "APP_ENV": "local"
    },
    "disabled": false,
    "autoApprove": [
      "application-info",
      "browser-logs",
      "database-connections",
      "database-query",
      "database-schema",
      "get-absolute-url",
      "get-config",
      "last-error",
      "list-artisan-commands",
      "list-available-config-keys",
      "list-available-env-vars",
      "list-routes",
      "read-log-entries",
      "report-feedback",
      "search-docs",
      "tinker"
    ]
  }
}
```

### Key Configuration Changes

1. **Command Change**:
   - From: `["artisan", "boost:mcp"]`
   - To: `["artisan", "mcp:start", "laravel-boost"]`

2. **Working Directory**: Added `"cwd": "C:\\XAMPP\\htdocs\\ictserve-031125"`

3. **Environment Simplification**:
   - Removed: `"MCP_CONNECTION_MODE": "persistent"`
   - Kept: `"APP_ENV": "local"`

4. **Laravel MCP Integration**: Uses `LaravelBoostCompatServer` class

## Laravel MCP Framework Integration

### Server Registration

**File**: `routes/ai.php`

```php
<?php

declare(strict_types=1);

use App\Mcp\Servers\ICTServeServer;
use App\Mcp\Servers\LaravelBoostCompatServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('ictserve', ICTServeServer::class);

app()->booted(function (): void {
    if (class_exists(\Laravel\Boost\Mcp\Boost::class)) {
        Mcp::local('laravel-boost', LaravelBoostCompatServer::class);
    }
});
```

### LaravelBoostCompatServer Implementation

**File**: `app/Mcp/Servers/LaravelBoostCompatServer.php`

```php
<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use Laravel\Boost\Mcp\Boost;
use Laravel\Mcp\Server\Methods\Initialize;
use Laravel\Mcp\Server\ServerContext;
use Laravel\Mcp\Server\Transport\JsonRpcRequest;

class LaravelBoostCompatServer extends Boost
{
    protected function handleInitializeMessage(JsonRpcRequest $request, ServerContext $context): void
    {
        $requestedVersion = $request->params['protocolVersion'] ?? null;

        if (is_string($requestedVersion) && ! in_array($requestedVersion, $context->supportedProtocolVersions, true)) {
            $request->params['protocolVersion'] = $this->negotiateProtocolVersion(
                $requestedVersion,
                $context->supportedProtocolVersions,
            );
        }

        $response = (new Initialize)->handle($request, $context);

        $this->transport->send($response->toJson(), $this->generateSessionId());
    }

    /**
     * @param  array<int, string>  $supportedProtocolVersions
     */
    private function negotiateProtocolVersion(string $requestedVersion, array $supportedProtocolVersions): string
    {
        foreach ($supportedProtocolVersions as $supported) {
            if ($supported <= $requestedVersion) {
                return $supported;
            }
        }

        return $supportedProtocolVersions[array_key_last($supportedProtocolVersions)];
    }
}
```

## Benefits of Laravel MCP Integration

### 1. Protocol Negotiation

- **Automatic version negotiation** between client and server
- **Fallback handling** for unsupported protocol versions
- **Better error handling** during initialization

### 2. Framework Integration

- **Laravel service container** integration
- **Middleware support** for authentication/authorization
- **Consistent error handling** with Laravel patterns

### 3. Stability Improvements

- **Reduced connection timeouts** through better protocol handling
- **Improved startup reliability** with proper initialization
- **Better resource management** within Laravel lifecycle

## Verification Commands

### Test Laravel MCP Server

```bash
# Test if the server starts properly
php artisan mcp:start laravel-boost

# Use MCP inspector for debugging
php artisan mcp:inspector laravel-boost

# List available MCP servers
php artisan route:list | grep mcp
```

### Test Direct Laravel Boost (Legacy)

```bash
# Direct Laravel Boost command (may have timeout issues)
php artisan boost:mcp

# Check Laravel Boost installation
php artisan boost:install
composer show laravel/boost
```

## Troubleshooting

### Connection Timeouts

1. **Check working directory**: Ensure `cwd` points to Laravel root
2. **Verify environment**: Confirm `APP_ENV=local` is set
3. **Test Laravel MCP**: Use `mcp:start` instead of `boost:mcp`

### Protocol Errors

1. **Check Laravel MCP version**: Ensure `laravel/mcp` v0.3.4+ installed
2. **Verify server registration**: Check `routes/ai.php` configuration
3. **Test compatibility server**: Ensure `LaravelBoostCompatServer` exists

### Missing Tools

1. **Verify auto-approve list**: Check all 16 Laravel Boost tools listed
2. **Test tool availability**: Use MCP inspector to verify tools
3. **Check Laravel Boost config**: Ensure `config/boost.php` enabled

## Available Tools (16 Total)

| Category | Tools |
|----------|-------|
| **Application** | application-info, get-config, list-available-config-keys, list-available-env-vars |
| **Database** | database-connections, database-query, database-schema |
| **Development** | list-artisan-commands, list-routes, tinker |
| **Debugging** | browser-logs, read-log-entries, last-error |
| **Documentation** | search-docs |
| **Utilities** | get-absolute-url, report-feedback |

## Best Practices

### 1. Use Laravel MCP Framework

- **Prefer**: `php artisan mcp:start laravel-boost`
- **Avoid**: `php artisan boost:mcp` (direct command)
- **Reason**: Better protocol negotiation and stability

### 2. Environment Configuration

- **Set working directory**: Always specify `cwd` in MCP config
- **Minimal environment**: Only set essential variables (`APP_ENV`)
- **Avoid complex env**: Don't set `MCP_CONNECTION_MODE` unless needed

### 3. Server Management

- **Use compatibility server**: Leverage `LaravelBoostCompatServer`
- **Register properly**: Ensure server registered in `routes/ai.php`
- **Test thoroughly**: Use MCP inspector for verification

## Related Documentation

- **Laravel Boost**: `docs/mcp/LARAVEL_BOOST_README.md`
- **Laravel MCP**: `docs/Laravel_MCP.md`
- **MCP Configuration**: `docs/mcp/MCP_CONFIGURATION.md`
- **Server Status**: `docs/mcp/MCP_SERVER_STATUS.md`

---

**Status**: ✅ Resolved  
**Configuration**: Updated `.kiro/settings/mcp.json`  
**Testing**: Verified with MCP inspector  
**Integration**: Laravel MCP framework approach confirmed working
