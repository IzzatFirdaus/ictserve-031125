# MCP Memory Server - Correct Usage Examples

## ⚠️ Common Error

```text
Error: MCP -32603: Unexpected non-whitespace character after JSON at position 139
```

**Cause**: Malformed JSON input to memory tools (quotes not properly escaped, line breaks in strings, etc.)

---

## ✅ Correct JSON Format

### 1. create_entities

**CORRECT** - Single line, properly escaped:

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
        "Theme toggle in landing page: top-right before auth buttons",
        "Theme toggle in guest layout: fixed top-right corner",
        "Status: Phase 1 complete, layouts configured for light-first design"
      ]
    }
  ]
}
```

**WRONG** - Line breaks in observation strings:

```json
{
  "entities": [
    {
      "name": "ICTServe_Theme",
      "entityType": "technical_implementation",
      "observations": [
        "v3.6.0: Implemented light-default theme system
        with optional dark mode",  // ❌ Line break causes error
        "Theme switcher updated"
      ]
    }
  ]
}
```

---

### 2. create_relations

**CORRECT**:

```json
{
  "relations": [
    {
      "from": "ICTServe_v3_6_Theme_System",
      "to": "D03_Software_Requirements",
      "relationType": "implements"
    },
    {
      "from": "ICTServe_v3_6_Theme_System",
      "to": "Tailwind_Configuration",
      "relationType": "uses"
    }
  ]
}
```

---

### 3. add_observations

**CORRECT**:

```json
{
  "observations": [
    {
      "entityName": "ICTServe_v3_6_Theme_System",
      "contents": [
        "2025-12-08: Added WCAG 2.2 AA touch target minimum sizes",
        "2025-12-08: Verified theme persistence across all layouts"
      ]
    }
  ]
}
```

---

### 4. search_nodes

**CORRECT**:

```json
{
  "query": "theme system"
}
```

---

### 5. open_nodes

**CORRECT**:

```json
{
  "names": [
    "ICTServe_v3_6_Theme_System",
    "Tailwind_Configuration"
  ]
}
```

---

## 🔧 Rules for Valid JSON

1. **No line breaks inside strings** - Use `\n` or combine into single line
2. **Escape quotes** - Use `\"` for quotes inside strings
3. **No trailing commas** - Last item in array/object should have no comma
4. **Valid JSON only** - Test with `jq` or online validator

---

## 🧪 Test Your JSON

### PowerShell validation

```powershell
# Test if JSON is valid
$json = Get-Content "test.json" -Raw
try {
    $json | ConvertFrom-Json
    Write-Host "✅ Valid JSON"
} catch {
    Write-Host "❌ Invalid JSON: $($_.Exception.Message)"
}
```

---

## 📝 Entity Naming Conventions

✅ **GOOD**:

- `ICTServe_v3_6_Theme_System`
- `Livewire_Volt_Compliance_Audit_2025-01-06`
- `Memory_Graph_Implementation_2025-11-15`

❌ **BAD**:

- `theme system` (spaces)
- `ICTServe-Theme` (inconsistent separator)
- `feature1` (not descriptive)

**Rules**:

- PascalCase with underscores
- Include dates for time-sensitive entities
- Descriptive, not generic
- No spaces or special characters

---

## 🎯 Example: Storing New Feature

```json
{
  "entities": [
    {
      "name": "ICTServe_Email_Notification_System_v3_5",
      "entityType": "technical_implementation",
      "observations": [
        "Dual approval workflow implemented with email notifications",
        "Uses Laravel Mailables with Markdown templates",
        "Queued jobs via Redis for async delivery",
        "Bilingual support: English and Bahasa Melayu",
        "PDPA compliant: no sensitive data in email body",
        "SLA tracking: sends reminder if no approval within 24 hours",
        "Test coverage: 100% (12 feature tests)",
        "Status: Production-ready as of 2025-11-30"
      ]
    }
  ]
}
```

Then link it:

```json
{
  "relations": [
    {
      "from": "ICTServe_Email_Notification_System_v3_5",
      "to": "D03_Software_Requirements",
      "relationType": "implements"
    },
    {
      "from": "ICTServe_Email_Notification_System_v3_5",
      "to": "Laravel_Queue_Service",
      "relationType": "uses"
    }
  ]
}
```

---

## 🚫 What NOT to Store

❌ **Secrets**:

```json
{
  "observations": [
    "API key: sk_live_abc123..."  // ❌ NEVER
  ]
}
```

✅ **Instead**:

```json
{
  "observations": [
    "Uses Stripe API with bearer token from .env (STRIPE_SECRET_KEY)",
    "Token retrieval: config('services.stripe.secret')"
  ]
}
```

---

## 📚 See Also

- Official docs: <https://github.com/modelcontextprotocol/servers/tree/main/src/memory>
- Local setup: `docs/mcp/MCP_MEMORY_SETUP.md`
- Troubleshooting: `docs/mcp/MCP_MEMORY_SERVER_FIX.md`
