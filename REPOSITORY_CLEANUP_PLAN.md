# Repository Cleanup Plan - November 23, 2025

**Status**: Ready for Execution  
**Imported to Neo4j**: ✅ All 34 documentation files + relationships created  
**Next Action**: Remove deprecated files after Neo4j verification

---

## ✅ Files Successfully Imported to Neo4j Memory (Keep Source Files)

### Core Documentation (Keep - Active Reference)
- `docs/INDEX.md` - Main documentation index
- `docs/README.md` - Documentation management guide
- `docs/ICTServe_System_Documentation.md` - Master system documentation
- `docs/GLOSSARY.md` - System glossary
- `docs/DEPLOYMENT_GUIDE.md` - Active deployment guide
- `docs/deployment-checklist.md` - Active checklist
- `docs/DASHBOARD_PERFORMANCE_OPTIMIZATION.md` - Performance guide
- `docs/performance-optimization-guide.md` - Comprehensive performance guide
- `docs/docker-troubleshooting.md` - Active troubleshooting guide
- `docs/IMPROVEMENT_GAP_ANALYSIS.md` - Gap analysis report

### MCP Documentation (Keep - Active Development)
- `docs/DEVELOPERS_MCP.md` - MCP developer guide
- `docs/GITHUB_MCP_SETUP.md` - GitHub MCP setup
- `docs/technical/devtools-mcp-getting-started.md` - DevTools MCP guide

### Mimir Documentation (Keep - Active System)
- `docs/mimir/MIMIR_DOCS_INDEX.md` - Mimir documentation index
- `docs/mimir/MIMIR_QUICK_START.md` - Quick start guide
- `docs/mimir/MIMIR_SETUP.md` - Setup guide
- `docs/mimir/MIMIR_INTEGRATION_COMPLETE.md` - Integration completion
- `docs/mimir/MIMIR_NEO4J_COMPLETE.md` - Neo4j integration complete
- `docs/mimir/MIMIR_NEO4J_VERIFICATION.md` - Verification report
- `docs/mimir/MIMIR_NPM_COMMANDS.md` - NPM commands reference
- `docs/mimir/ISSUE_RESOLUTION_2025-11-22.md` - Issue resolution log

### PCTX Documentation (Keep - Platform Analysis)
- `docs/PCTX_INTEGRATION_SETUP.md` - PCTX setup (blocked on Windows)
- `docs/PCTX_IMPLEMENTATION_STATUS.md` - Implementation status
- `docs/PCTX_CODE_MODE_EXAMPLES.md` - Code mode examples

### E2E Testing Documentation (Keep - Active Testing)
- `docs/e2e-triage/helpdesk-performance-triage.md` - Helpdesk performance issues
- `docs/e2e-triage/loan-accessibility-triage.md` - Loan accessibility issues
- `docs/e2e-triage/loan-performance-triage.md` - Loan performance issues

### Broadcasting Documentation (Keep - Active Feature)
- `.github/BROADCASTING_SETUP_GUIDE.md` - Broadcasting setup guide

---

## 🗑️ Files to DELETE (Already in Neo4j + Deprecated)

### Root Directory Completion Reports (Delete - Archived in Neo4j)
```powershell
Remove-Item "DOCUMENTATION_IMPORT_COMPLETE.md" -Force
```
**Reason**: Completion report now in Neo4j memory. Historical record preserved.

### _reference Directory Completion Reports (Delete - Archived in Neo4j)
```powershell
Remove-Item "_reference\USER_ACCESS_VERIFICATION_COMPLETE.md" -Force
Remove-Item "_reference\SLA_THRESHOLD_REFACTOR_SUMMARY.md" -Force
Remove-Item "_reference\REVERB_SETUP_COMPLETE.md" -Force
Remove-Item "_reference\QUICK_FIX_REFERENCE.md" -Force
```
**Reason**: Completion reports from completed work phases. Now in Neo4j for reference.

### _reference/backup Directory (Delete - Outdated Backups)
```powershell
Remove-Item "_reference\backup\mcp-config-changes-2025-11-10.md" -Force
Remove-Item "_reference\backup\README.md" -Force
```
**Reason**: Old backup documentation from November 2025. Content superseded by current docs.

---

## 📦 Directories to Archive (Not Delete - Keep for Reference)

### _reference/FUTURE_IMPLEMENTATION_AI_CHATBOT_USING_OLLAMA.md
**Action**: KEEP - Future implementation plan  
**Reason**: Active planning document for future AI chatbot feature using Ollama

---

## 🔄 Files Already in Neo4j - Consider for Removal After Verification

### Root Directory Files (Verify Neo4j First)
These files are imported but still actively used by workflows:
- `mimir.md` - Keep (active development guide)
- `openmemory.md` - DELETE (deprecated, replaced by Mimir)
- `AGENTS.md` - Keep (active agent instructions)
- `CLAUDE.md` - Keep (active AI assistant instructions)
- `GEMINI.md` - Keep (active AI assistant instructions)

### Root Documentation Files (Already Duplicated in docs/)
```powershell
# These are duplicates - docs/ versions are canonical
Remove-Item "ISSUE_RESOLUTION_2025-11-22.md" -Force  # Duplicate of docs/mimir/ISSUE_RESOLUTION_2025-11-22.md
Remove-Item "MIMIR_DOCS_INDEX.md" -Force  # Duplicate of docs/mimir/MIMIR_DOCS_INDEX.md
Remove-Item "MIMIR_INTEGRATION_COMPLETE.md" -Force  # Duplicate of docs/mimir/MIMIR_INTEGRATION_COMPLETE.md
Remove-Item "MIMIR_NEO4J_COMPLETE.md" -Force  # Duplicate of docs/mimir/MIMIR_NEO4J_COMPLETE.md
Remove-Item "MIMIR_NEO4J_VERIFICATION.md" -Force  # Duplicate of docs/mimir/MIMIR_NEO4J_VERIFICATION.md
Remove-Item "MIMIR_NPM_COMMANDS.md" -Force  # Duplicate of docs/mimir/MIMIR_NPM_COMMANDS.md
Remove-Item "MIMIR_QUICK_START.md" -Force  # Duplicate of docs/mimir/MIMIR_QUICK_START.md
Remove-Item "MIMIR_SETUP_COMPLETE.md" -Force  # Deprecated completion report
Remove-Item "MIMIR_SETUP.md" -Force  # Duplicate of docs/mimir/MIMIR_SETUP.md
```

---

## 📊 Summary

| Category | Action | Count |
|----------|--------|-------|
| **Core Documentation** | Keep | 10 files |
| **MCP Documentation** | Keep | 3 files |
| **Mimir Documentation** | Keep | 6 files |
| **PCTX Documentation** | Keep | 3 files |
| **E2E Testing** | Keep | 3 files |
| **Broadcasting** | Keep | 1 file |
| **Completion Reports** | Delete | 5 files |
| **Backup Documentation** | Delete | 2 files |
| **Root Duplicates** | Delete | 9 files |
| **Future Planning** | Keep | 1 file |
| **TOTAL TO DELETE** | | **16 files** |

---

## ✅ Verification Before Deletion

Before executing deletions, verify Neo4j contains all content:

```powershell
# Check total entities
php artisan tinker --execute="echo App\Models\MemoryEntity::count();"

# Check relationships
docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ "MATCH ()-[r]->() RETURN count(r) as total_relationships"

# Verify specific documents are in Neo4j
docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ "MATCH (n:MemoryEntity) WHERE n.name CONTAINS 'MIMIR' OR n.name CONTAINS 'PCTX' RETURN n.name ORDER BY n.name"
```

---

## 🚀 Execution Command

After verification, execute cleanup:

```powershell
# Delete completion reports
Remove-Item "DOCUMENTATION_IMPORT_COMPLETE.md" -Force
Remove-Item "_reference\USER_ACCESS_VERIFICATION_COMPLETE.md" -Force
Remove-Item "_reference\SLA_THRESHOLD_REFACTOR_SUMMARY.md" -Force
Remove-Item "_reference\REVERB_SETUP_COMPLETE.md" -Force
Remove-Item "_reference\QUICK_FIX_REFERENCE.md" -Force

# Delete backup documentation
Remove-Item "_reference\backup\mcp-config-changes-2025-11-10.md" -Force
Remove-Item "_reference\backup\README.md" -Force

# Delete root duplicates (canonical versions in docs/mimir/)
Remove-Item "MIMIR_DOCS_INDEX.md" -Force
Remove-Item "MIMIR_INTEGRATION_COMPLETE.md" -Force
Remove-Item "MIMIR_NEO4J_COMPLETE.md" -Force
Remove-Item "MIMIR_NEO4J_VERIFICATION.md" -Force
Remove-Item "MIMIR_NPM_COMMANDS.md" -Force
Remove-Item "MIMIR_QUICK_START.md" -Force
Remove-Item "MIMIR_SETUP_COMPLETE.md" -Force
Remove-Item "MIMIR_SETUP.md" -Force

# Delete deprecated OpenMemory file
Remove-Item "openmemory.md" -Force

# Verify cleanup
Write-Host "✅ Cleanup complete. Verifying removed files..."
Get-ChildItem -Path . -Filter "MIMIR_*.md" -File | Select-Object Name
Get-ChildItem -Path "_reference" -Filter "*_COMPLETE.md" -File | Select-Object Name
```
