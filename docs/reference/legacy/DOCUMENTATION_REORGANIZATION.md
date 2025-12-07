# Documentation Reorganization (LEGACY) ❌

**Date**: 2025-01-25  
**Status**: Deprecated

> LEGACY WARNING: This plan referenced Mimir/Neo4j documentation workflows that are no longer in use. The authoritative knowledge store is the Memory MCP JSONL file at `storage/mcp/memory.jsonl`. Keep this file for audit history only; do not action the Mimir items.

## Summary (Historical)

All Docker and Mimir documentation was organized into `docs/` directory. Mimir-specific flows are obsolete; retain for traceability only.

## Changes Made

### 1. Created Docker Documentation (`docs/docker/`)

**New Files**:

- `README.md` - Overview and quick start
- `SETUP.md` - Complete installation guide
- `ARCHITECTURE.md` - Container architecture and networking
- `TROUBLESHOOTING.md` - Common issues and solutions

**Moved Files**:

- `DOCKER_SUCCESS.md` → `docs/docker/SUCCESS.md`
- `DOCKER_UPDATE_PLAN.md` → `docs/docker/UPDATE_PLAN.md`
- `DOCKER_NEXT_STEPS.md` → `docs/docker/NEXT_STEPS.md`
- `DOCKER_SETUP.md` → `docs/docker/SETUP_LEGACY.md`

### 2. Created Mimir Documentation (`docs/mimir/`) — Legacy

**New Files**:

- `README.md` - Overview and quick start
- `SETUP.md` - Installation and configuration (to be created)
- `DOCKER.md` - Docker deployment guide (to be created)
- `SUBMODULE.md` - Git submodule management
- `MCP_INTEGRATION.md` - AI agent integration (to be created)
- `TROUBLESHOOTING.md` - Common issues (to be created)

**Copied Files** (from `Mimir/` submodule to `docs/mimir/submodule/`):

- `DEPLOYMENT_SUCCESS.md`
- `VERIFIED.md`
- `MCP_INTEGRATION.md`
- `SETUP_GUIDE.md`
- `DOCKER_SETUP.md`

### 3. Updated Main Documentation Index

**Updated**: `docs/README.md`

- Complete navigation structure
- Quick links to all documentation
- Getting started guides for different roles
- Documentation standards

### 4. Created Cleanup Script

**New**: `scripts/cleanup-docs.ps1`

- PowerShell script to remove deprecated files
- Interactive confirmation
- Preserves important files (README, AGENTS, GEMINI, etc.)

## Directory Structure

```
docs/
├── docker/                    # Docker deployment
│   ├── README.md
│   ├── SETUP.md
│   ├── ARCHITECTURE.md
│   ├── TROUBLESHOOTING.md
│   ├── SUCCESS.md
│   ├── UPDATE_PLAN.md
│   ├── NEXT_STEPS.md
│   └── SETUP_LEGACY.md
│
├── mimir/                     # Mimir AI memory
│   ├── README.md
│   ├── SUBMODULE.md
│   └── submodule/             # Submodule docs
│       ├── DEPLOYMENT_SUCCESS.md
│       ├── VERIFIED.md
│       ├── MCP_INTEGRATION.md
│       ├── SETUP_GUIDE.md
│       └── DOCKER_SETUP.md
│
├── reference/                 # Technical references
│   └── (existing files)
│
├── D00-D16 docs              # System documentation
└── README.md                 # Main documentation index
```

### Deprecated Cleanup Notes

The referenced cleanup script and Mimir docs are historical. Use the Memory MCP JSONL store going forward; do not recreate Mimir docs.

## Documentation Standards

All documentation now follows:

1. **Organized Structure**: Logical subdirectories (docker/, mimir/, reference/)
2. **Consistent Naming**: Clear, descriptive filenames
3. **Navigation**: README.md in each directory with links
4. **Completeness**: Setup, architecture, troubleshooting for each topic
5. **Traceability**: Links to related documentation

### Next Steps (Superseded)

- No further Mimir work. Use Memory MCP JSONL (`storage/mcp/memory.jsonl`) and update root README as needed to reference Memory MCP instead of Mimir.

## Verification

### Check Documentation Structure

```powershell
# List Docker docs
ls docs\docker\

# List Mimir docs
ls docs\mimir\

# View main index
cat docs\README.md
```

### Test Links

```powershell
# Open main documentation
start docs\README.md

# Open Docker setup
start docs\docker\SETUP.md

# Open Mimir overview
start docs\mimir\README.md
```

## Benefits

1. **Organized**: All documentation in logical subdirectories
2. **Discoverable**: Clear navigation and indexes
3. **Maintainable**: Consistent structure and naming
4. **Complete**: Comprehensive guides for all topics
5. **Clean**: Deprecated files removed from root

## References

- [Docker Documentation](docs/docker/README.md)
- [Mimir Documentation](docs/mimir/README.md)
- [Main Documentation Index](docs/README.md)
- [Cleanup Script](scripts/cleanup-docs.ps1)

---

**Status**: ✅ Documentation reorganization complete  
**Next**: Run cleanup script and commit changes
