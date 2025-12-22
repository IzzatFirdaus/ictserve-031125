# MCP Configuration Guide for ICTServe

This directory contains multiple MCP (Model Context Protocol) configuration files for different development environments.

## Configuration Files

### 1. `mcp.json` (Default - XAMPP with Absolute Paths)
**Current Active Configuration**

Uses absolute paths for PHP and artisan to ensure reliable MCP server startup on Windows.

```json
{
  "laravel-boost": {
    "command": "C:\\Users\\exatf\\tools\\php-8.4.11\\php.exe",
    "args": ["C:\\XAMPP\\htdocs\\ictserve-031125\\artisan", "boost:mcp"]
  }
}
```

**Pros:**

- Most reliable on Windows
- No working directory issues
- Direct execution

**Cons:**

- Machine-specific paths
- Requires updating when project location changes

### 2. `mcp.xampp.json` (XAMPP Environment)
Identical to `mcp.json` - configured for XAMPP environment with absolute paths.

### 3. `mcp.xampp-batch.json` (Alternative - Batch Script Approach)
Uses batch scripts that handle working directory changes automatically.

```json
{
  "laravel-boost": {
    "command": "C:\\XAMPP\\htdocs\\ictserve-031125\\scripts\\mcp-laravel-boost.bat",
    "args": []
  }
}
```

**Pros:**

- Cleaner configuration
- Batch scripts handle directory changes
- Easier to maintain

**Cons:**

- Requires batch script files
- Additional layer of indirection

### 4. `mcp.laragon.json` (Laragon Environment)
Optimized for Laragon development environment with relative paths and `cwd` parameter.

### 5. `mcp.workspace.json` (Docker Environment)
Configured for Docker-based development using `docker compose exec` commands.

## Troubleshooting

### Issue: "Could not open input file: artisan"

This error occurs when the MCP server cannot locate the Laravel artisan file.

**Solutions:**

1. **Use Absolute Paths (Recommended for Windows)**
   - Update `mcp.json` with full paths to PHP and artisan
   - Current configuration uses this approach

2. **Use Batch Scripts**
   - Copy `mcp.xampp-batch.json` to `mcp.json`
   - Batch scripts in `scripts/` directory handle working directory

3. **Verify Paths**

   ```powershell
   # Check PHP location
   (Get-Command php).Source
   
   # Check current directory
   Get-Location
   ```

### Issue: MCP Server Not Connecting

1. **Restart Kiro IDE** - Configuration changes require restart
2. **Check Logs** - View MCP logs in Kiro for detailed error messages
3. **Test Manually** - Run the command directly in terminal:

   ```cmd
   php artisan boost:mcp
   ```

### Issue: Wrong Configuration File Being Used

Kiro uses configuration precedence:

1. Workspace-specific: `.kiro/settings/mcp.json` (highest priority)
2. User-level: `~/.kiro/settings/mcp.json`

Ensure you're editing the correct file for your environment.

## Environment-Specific Setup

### XAMPP Setup

1. Use `mcp.json` or `mcp.xampp.json`
2. Update paths if your XAMPP installation differs:
   - PHP path: `C:\Users\exatf\tools\php-8.4.11\php.exe`
   - Project path: `C:\XAMPP\htdocs\ictserve-031125`

### Laragon Setup

1. Copy `mcp.laragon.json` to `mcp.json`
2. Verify `cwd` parameter points to project root

### Docker Setup

1. Copy `mcp.workspace.json` to `mcp.json`
2. Ensure Docker containers are running
3. Verify container names match configuration

## Testing MCP Configuration

### Test Laravel Boost Server

```powershell
# Using absolute paths
C:\Users\exatf\tools\php-8.4.11\php.exe C:\XAMPP\htdocs\ictserve-031125\artisan boost:mcp

# Using batch script
C:\XAMPP\htdocs\ictserve-031125\scripts\mcp-laravel-boost.bat

# Using relative paths (from project root)
php artisan boost:mcp
```

### Test ICTServe MCP Server

```powershell
# Using absolute paths
C:\Users\exatf\tools\php-8.4.11\php.exe C:\XAMPP\htdocs\ictserve-031125\artisan mcp:start ictserve

# Using batch script
C:\XAMPP\htdocs\ictserve-031125\scripts\mcp-ictserve.bat

# Using relative paths (from project root)
php artisan mcp:start ictserve
```

## Available MCP Servers

### Laravel-Specific Servers

1. **laravel-boost** - Laravel development tools
   - Application information and configuration
   - Database queries and schema inspection
   - Artisan command execution
   - Laravel documentation search
   - Tinker integration
   - Browser logs and debugging

2. **ictserve** - ICTServe-specific tools
   - Helpdesk ticket queries
   - Asset status checking
   - Custom business logic tools

### General Development Servers

3. **memory** - Persistent knowledge graph
4. **sequentialthinking** - Complex problem decomposition
5. **fetch** - Web content retrieval
6. **chrome-devtools** - Browser automation and debugging
7. **playwright** - E2E testing and automation
8. **github** - GitHub repository operations

### Optional Enhancement Servers (Disabled by Default)

9. **context7** - Library documentation enhancement
10. **deepl** - Translation services (Bahasa Melayu ↔ English)
11. **firecrawl** - Advanced web scraping

## Maintenance

### Updating Paths After Project Move

If you move the project to a different location:

1. Update `mcp.json`:

   ```json
   {
     "laravel-boost": {
       "command": "C:\\Path\\To\\PHP\\php.exe",
       "args": ["C:\\Path\\To\\Project\\artisan", "boost:mcp"]
     }
   }
   ```

2. Update batch scripts in `scripts/`:

   ```batch
   cd /d "C:\Path\To\Project"
   php artisan boost:mcp
   ```

3. Restart Kiro IDE

### Adding New MCP Servers

1. Add server configuration to `mcp.json`
2. Include `autoApprove` list for common operations
3. Test server connection manually
4. Document in this README

## References

- **D11 §5.2** - MCP server configuration patterns
- **`.kiro/steering/mcp.md`** - Comprehensive MCP guidelines
- **Laravel MCP Documentation** - <https://laravel.com/docs/mcp>
- **MCP Specification** - <https://spec.modelcontextprotocol.io/>

## Support

For issues with MCP configuration:

1. Check this README for common solutions
2. Review MCP logs in Kiro IDE
3. Test commands manually in terminal
4. Consult `.kiro/steering/mcp.md` for detailed guidance
