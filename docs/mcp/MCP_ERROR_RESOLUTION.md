# MCP Memory Server Error Resolution Guide

## 🔴 Error: "Unexpected non-whitespace character after JSON at position 139"

### Root Cause
This error occurs when the JSON input to MCP memory tools is **malformed**. Common causes:

1. **Line breaks inside string values** (most common)
2. **Unescaped quotes** within strings
3. **Missing commas** or **trailing commas**
4. **Invalid JSON structure**

---

## ✅ Solution: Proper JSON Formatting

### The Problem in Your Screenshot

Looking at your error, the JSON you're trying to send has **line breaks in the observation strings**. This breaks JSON syntax.

**❌ WRONG** (what you likely tried):

```json
{
  "entities": [
    {
      "name": "ICTServe_v3_6_Theme_System",
      "entityType": "technical_implementation",
      "observations": [
        "v3.6.0: Implemented light-default theme system
        with optional dark mode",
        "Theme switcher component updated: removed system option,
        only light and dark allowed"
      ]
    }
  ]
}
```

The line breaks inside the strings at positions 139+ cause the error.

---

### ✅ CORRECT Format (Single-Line Strings)

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
        "All layouts updated with theme init script in head before stylesheets",
        "Portal layout: removed class='dark' from HTML tag (light is default)",
        "Theme toggle in authenticated header: between notifications and user menu",
        "Theme toggle in landing page: top-right, before auth buttons",
        "Theme toggle in guest layout: fixed top-right corner",
        "Status: Phase 1 complete, layouts configured for light-first design"
      ]
    }
  ]
}
```

---

## 🧪 How to Validate Your JSON Before Sending

### Method 1: PowerShell Validation

```powershell
# Test your JSON
$json = @'
{
  "entities": [
    {
      "name": "Test_Entity",
      "entityType": "technical_implementation",
      "observations": ["Test observation"]
    }
  ]
}
'@

try {
    $json | ConvertFrom-Json
    Write-Host "✅ Valid JSON"
} catch {
    Write-Host "❌ Invalid JSON: $($_.Exception.Message)"
}
```

### Method 2: Online JSON Validator

- **JSONLint**: <https://jsonlint.com/>
- Copy your JSON, paste, click "Validate"

### Method 3: VS Code JSON Validation

1. Create a `.json` file
2. Paste your JSON
3. VS Code will show errors in real-time

---

## 📋 MCP Memory Tools - Correct Usage

### 1. create_entities

```json
{
  "entities": [
    {
      "name": "Entity_Name_PascalCase",
      "entityType": "technical_implementation",
      "observations": [
        "Observation 1: atomic fact",
        "Observation 2: another atomic fact",
        "Observation 3: status or date"
      ]
    }
  ]
}
```

### 2. create_relations

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

### 3. add_observations

```json
{
  "observations": [
    {
      "entityName": "Existing_Entity",
      "contents": [
        "New observation 1",
        "New observation 2"
      ]
    }
  ]
}
```

### 4. search_nodes

```json
{
  "query": "search term"
}
```

### 5. open_nodes

```json
{
  "names": ["Entity_1", "Entity_2"]
}
```

---

## 🚨 Common JSON Mistakes

| Mistake | Example | Fix |
|---------|---------|-----|
| Line breaks in strings | `"text\nmore text"` | Use single line or `\\n` |
| Unescaped quotes | `"He said "hello""` | Use `\"`: `"He said \"hello\""` |
| Trailing comma | `["item1", "item2",]` | Remove last comma |
| Missing comma | `["item1" "item2"]` | Add comma: `["item1", "item2"]` |
| Single quotes | `{'key': 'value'}` | Use double quotes: `{"key": "value"}` |

---

## 🎯 Best Practices

### 1. Keep Observations Atomic
✅ **GOOD** - One fact per observation:

```json
"observations": [
  "Uses Laravel 12",
  "Deployed on Docker",
  "MySQL 8.0 database"
]
```

❌ **BAD** - Multiple facts crammed together:

```json
"observations": [
  "Uses Laravel 12, deployed on Docker with MySQL 8.0 database and Redis cache"
]
```

### 2. Use Descriptive Entity Names
✅ **GOOD**: `ICTServe_Email_Notification_System_v3_5`  
❌ **BAD**: `email_system` or `feature1`

### 3. Test JSON Locally First
Before sending to MCP server, validate with:

```powershell
.\scripts\validate-memory-json.ps1
```

---

## 🔧 Troubleshooting Checklist

- [ ] **No line breaks inside string values**
- [ ] **All quotes properly escaped** (`\"` for internal quotes)
- [ ] **No trailing commas** after last array/object item
- [ ] **Commas between all items** (except last)
- [ ] **Double quotes only** (no single quotes)
- [ ] **Valid JSON structure** (test with validator)
- [ ] **Observations are strings**, not objects
- [ ] **Entity names follow PascalCase_Convention**

---

## 📚 Additional Resources

- **Official MCP Memory Docs**: <https://github.com/modelcontextprotocol/servers/tree/main/src/memory>
- **Usage Examples**: `docs/mcp/MCP_MEMORY_USAGE_EXAMPLES.md`
- **Setup Guide**: `docs/mcp/MCP_MEMORY_SETUP.md`
- **Validation Script**: `scripts/validate-memory-json.ps1`

---

## ✅ Verified Configuration

Your MCP configuration (`.vscode/mcp.json`) is correct:

```json
{
  "servers": {
    "memory": {
      "command": "npx",
      "args": [
        "-y",
        "@modelcontextprotocol/server-memory",
        "c:\\XAMPP\\htdocs\\ictserve-031125\\storage\\mcp\\memory.jsonl"
      ]
    }
  }
}
```

Your `memory.jsonl` file is valid (42 entries, all validated ✅).

**The error is in the JSON you're sending TO the memory server, not the stored data.**

---

## 💡 Quick Fix Summary

1. **Identify the problematic JSON** - Look at what you're trying to send
2. **Check for line breaks** - Remove or use `\\n` escape
3. **Validate JSON** - Use online validator or PowerShell
4. **Use single-line strings** - No multi-line strings in JSON
5. **Test with simple example first** - Start small, then expand

---

**Last Updated**: 2025-12-08  
**Status**: ✅ Your memory.jsonl is valid. Focus on input JSON formatting.
