# MCP Memory Server JSON Error Fix

## Problem
```
Mcp error: -32603: Unexpected non-whitespace character after JSON at position 139
```

## Root Cause
Memory server outputs debug/error messages before JSON-RPC responses, breaking the protocol.

---

## Quick Fix (Recommended)

### Option 1: Use Minimal Config (Immediate)

1. **Copy minimal config**:
```powershell
Copy-Item "C:\Users\exatf\.codex\config-minimal.toml" "C:\Users\exatf\.codex\config.toml" -Force
```

2. **Restart IDE** - You'll have:
   - ✅ Sequential Thinking (working)
   - ✅ Laravel Boost (working)
   - ❌ Memory server (disabled)

3. **Continue work** without memory server

---

### Option 2: Reinstall Memory Server

1. **Uninstall**:
```powershell
npm uninstall -g @modelcontextprotocol/server-memory
```

2. **Clear cache**:
```powershell
npm cache clean --force
```

3. **Reinstall**:
```powershell
npm install -g @modelcontextprotocol/server-memory@latest
```

4. **Test**:
```powershell
.\scripts\test-memory-server.ps1
```

5. **If working**: Enable in config.toml
```toml
[mcp_servers.memory]
disabled = false
```

---

### Option 3: Use Filesystem Server (Alternative)

1. **Install filesystem server**:
```powershell
npm install -g @modelcontextprotocol/server-filesystem
```

2. **Enable in config.toml**:
```toml
[mcp_servers.filesystem]
disabled = false
command = 'npx.cmd'
args = ['-y', '@modelcontextprotocol/server-filesystem', 'C:\\XAMPP\\htdocs\\ictserve-031125']
```

3. **Restart IDE**

**Pros**: More reliable, direct file access
**Cons**: No graph database, simpler queries

---

## Diagnostic Steps

### 1. Test Server Manually
```powershell
.\scripts\test-memory-server.ps1
```

**Expected output**:
- ✓ Files found
- ✓ Server starts
- ✓ Output starts with JSON `{`

**Problem output**:
- ✗ Output starts with text (debug messages)
- ✗ Server exits immediately

---

### 2. Check Server Version
```powershell
npm list -g @modelcontextprotocol/server-memory
```

**Expected**: `@modelcontextprotocol/server-memory@0.x.x`

---

### 3. Check Node Version
```powershell
node --version
```

**Required**: Node.js 18+ (you have multiple versions, ensure correct one)

---

## Current Configuration Status

**Working Servers**:
- ✅ Sequential Thinking
- ✅ Laravel Boost
- ✅ Chrome DevTools (if enabled)

**Broken Servers**:
- ❌ Memory (JSON parse error)
- ❌ Mimir (depends on memory)

**Disabled Servers**:
- ⚠️ Playwright (heavy, enable only for E2E)
- ⚠️ External services (require API keys)

---

## Workarounds

### Without Memory Server

**Use Laravel Boost for project memory**:
```javascript
// Query application info
application-info()

// Search Laravel docs
search-docs(['topic'])

// Test code
tinker('Model::first()')
```

**Use Sequential Thinking for complex tasks**:
```javascript
sequentialthinking({
  thought: "Analyze problem step by step",
  thoughtNumber: 1,
  totalThoughts: 5
})
```

**Use filesystem for file operations**:
- Read files directly
- Search codebase
- No need for memory graph

---

## Prevention

### 1. Pin Server Versions
```powershell
# Install specific version
npm install -g @modelcontextprotocol/server-memory@0.5.0
```

### 2. Use Docker (Isolated)
```toml
[mcp_servers.memory-docker]
disabled = false
command = 'docker'
args = ['exec', '-i', 'ictserve-mcp-memory', 'node', '/app/dist/index.js']
```

### 3. Regular Testing
```powershell
# Weekly check
.\scripts\test-memory-server.ps1
```

---

## Support

**If issue persists**:
1. Check Node.js path conflicts (you have 2 installations)
2. Use minimal config (Option 1)
3. Report to MCP server maintainers: https://github.com/modelcontextprotocol/servers

**Recommended for ICTServe**:
- Use **minimal config** for now
- Focus on Laravel Boost + Sequential Thinking
- Add memory server later when stable

---

**Last Updated**: 2025-01-22
**Status**: Memory server disabled, core servers working
