# Scripts Directory Structure

Visual representhe organized scripts directory.

```text
scripts/
├── README.md                           # Main documentation
├── QUICK-REFERENCE.md                  # Quick command reference
├── DIRECTORY-STRUCTURE.md              # This file
├── DEV-STARTUP-GUIDE.md               # Detailed startup guide
│
├── dev/                                # Development environment
│   ├── start-dev.bat                  # Windows batch start
│   ├── start-dev.ps1                  # PowerShell start
│   ├── start-dev.sh                   # Bash start
│   ├── stop-dev.bat                   # Windows batch stop
│   ├── stop-dev.ps1                   # PowerShell stop
│   ├── reverb-start.ps1               # Reverb WebSocket server
│   ├── reverb-start.sh                # Reverb (Bash)
│   ├── switch-env.ps1                 # Environment switcher
│   ├── switch-env.sh                  # Environment switcher (Bash)
│   └── start-pctx-stack.ps1           # Full stack starter
│
├── testing/                            # Testing & QA
│   ├── run-test.ps1                   # Run specific tests
│   ├── run-tests.js                   # Test suite runner
│   ├── run-individual-tests.js        # Individual test runner
│   ├── test-changed.ps1               # Test changed files
│   ├── check-larastan-ready.ps1       # Larastan readiness check
│   ├── check-larastan-ready.sh        # Larastan check (Bash)
│   ├── consolidate-larastan-outputs.sh # Consolidate analysis
│   ├── save-larastan-outputs.ps1      # Save analysis results
│   ├── save-larastan-outputs.sh       # Save results (Bash)
│   ├── update-test-attributes-v2.php  # Update test attributes
│   ├── update-test-attributes.php     # Update test attributes
│   └── testNameAccessor.php           # Test name accessor utility
│
├── translations/                       # Localization (EN/MS)
│   ├── check-missing-translations.ps1 # Find missing keys
│   ├── check-translations.php         # Check translation files
│   ├── clean-translation-keys.php     # Clean up keys
│   ├── extract-translations.php       # Extract strings
│   ├── extract-translations.ps1       # Extract (PowerShell)
│   ├── find_missing_translations.py   # Find missing (Python)
│   └── scan-hardcoded-strings.php     # Scan for hardcoded text
│
├── mcp/                                # Model Context Protocol
│   ├── detect-mcp-docker.ps1          # Detect Docker MCP
│   ├── detect-mcp-docker.sh           # Detect Docker (Bash)
│   ├── load-mcp-env.ps1               # Load MCP environment
│   ├── mcp-health-check.ps1           # Health check
│   ├── mcp-health-check.sh            # Health check (Bash)
│   ├── mcp-resources-shim.cjs         # Resources shim
│   ├── mcp-stdio-wrapper.js           # Stdio wrapper
│   ├── setup-mcp-env-windows.ps1      # Windows setup
│   ├── switch-mcp-mode.ps1            # Mode switcher
│   ├── test-mcp-memory.php            # Test memory server
│   ├── test-mcp-servers.ps1           # Test all servers
│   ├── test-mcp-startup.ps1           # Test startup
│   ├── test-mcp.ps1                   # General MCP test
│   ├── test-memory-server.ps1         # Memory server test
│   ├── verify-mcp-config.ps1          # Verify configuration
│   └── mcp-debug.log                  # Debug log
│
├── memory/                             # Knowledge graph & memory
│   ├── check_memory_entity.php        # Check entity
│   ├── convert-memory-jsonl.php       # Convert format
│   ├── execute-memory-import-and-cleanup.ps1 # Import workflow
│   ├── export-memory-graph.php        # Export graph
│   ├── generate-memory-cypher.php     # Generate Cypher
│   ├── register-memory-sync.ps1       # Register sync
│   ├── validate-memory-json.ps1       # Validate data
│   └── verify-memory-import.php       # Verify import
│
├── neo4j/                              # Neo4j knowledge graph
│   ├── candidate_docs_check.cypher    # Check candidate docs
│   ├── create-doc-relationships.cypher # Create relationships
│   ├── create-documentation-entities.php # Create entities
│   ├── doc_refs_cypher_nocomment.cypher # Doc refs (no comments)
│   ├── doc_refs_cypher.cypher         # Doc references
│   ├── doc_refs_cypher2_nobom.cypher  # Doc refs v2 (no BOM)
│   ├── doc_refs_cypher2.cypher        # Doc refs v2
│   ├── doc_refs_cypher3.cypher        # Doc refs v3
│   ├── doc_refs_unwind.cypher         # Doc refs unwind
│   ├── import-entities-to-neo4j.php   # Import entities
│   ├── import-memory-to-neo4j.php     # Import memory
│   ├── import-phase2-entities.cypher  # Phase 2 import
│   ├── import-phase5-entities.cypher  # Phase 5 import
│   ├── missing_docs_import.cypher     # Missing docs import
│   ├── missing_docs_import2.cypher    # Missing docs v2
│   ├── missing_docs_import3.cypher    # Missing docs v3
│   └── verify-neo4j-consolidation.php # Verify consolidation
│
├── database/                        # Database utilities
│   ├── check_admin.php                # Check admin user
│   ├── check-migrations.php           # Verify migrations
│   └── reset-password.php             # Reset user password
│
├── setup/                              # Initial setup
│   ├── setup-apache-alias.ps1         # Apache alias config
│   ├── setup-github-token.ps1         # GitHub token setup
│   ├── setup-vhost.ps1                # Virtual host setup
│   ├── verify-github-token.ps1        # Verify token
│   └── fix-npm-windows.ps1            # Fix npm on Windows
│
├── maintenance/                        # Maintenance & cleanup
│   ├── cleanup-deprecated-files.ps1   # Remove deprecated files
│   ├── cleanup-docs-simple.ps1        # Simple doc cleanup
│   ├── cleanup-docs.ps1               # Full doc cleanup
│   ├── fix-filament-issues.php        # Fix Filament issues
│   ├── fix-markdown-lint-rules.php    # Fix markdown linting
│   └── fix-markdown-trailing-spaces.php # Fix trailing spaces
│
├── docker/                             # Docker environment
│   ├── artisan.ps1                    # Artisan in Docker
│   ├── build.ps1                      # Build Docker images
│   ├── check-assets.ps1               # Check assets
│   ├── composer.ps1                   # Composer in Docker
│   ├── fix-storage-symlink.ps1        # Fix storage symlink
│   ├── fix-styling.ps1                # Fix code styling
│   ├── import-memory.ps1              # Import memory
│   ├── init-dev.ps1                   # Initialize dev
│   ├── mcp-servers.ps1                # MCP servers
│   ├── memory-mcp.ps1                 # Memory MCP server
│   ├── npm.ps1                        # npm in Docker
│   ├── start-dev.ps1                  # Start Docker dev
│   ├── start-sequential-thinking.ps1  # Start seq thinking
│   ├── status-dev.ps1                 # Check dev status
│   ├── stop-dev.ps1                   # Stop Docker dev
│   ├── stop-sequential-thinking.ps1   # Stop seq thinking
│   └── wait-for-db.sh                 # Wait for database
│
├── laragon/                            # Laragon environment
│   ├── drop_helpdesk_table.php        # Drop helpdesk table
│   ├── export-example.ps1             # Export example
│   ├── README.md                      # Laragon docs
│   └── setup-laragon.ps1              # Setup Laragon
│
├── tools/                              # Development tools
│   ├── fix-filament-imports.bat       # Fix Filament imports
│   ├── reverb-quickstart.ps1          # Quick Reverb setup
│   ├── verify-email-notification-fixes.bat # Verify email fixes
│   ├── verify-ui-fixes.bat            # Verify UI fixes
│   └── verify-ui-fixes.sh             # Verify UI (Bash)
│
├── supervisor/                         # Process supervision
│   └── reverb.conf                    # Reverb supervisor config
│
├── nova/                               # Nova AI testing
│   ├── nova_act_config.py             # Nova config
│   ├── nova_act_initial_test.py       # Initial test
│   ├── nova_act_qa_test.py            # QA test
│   ├── nova_act_simple_test.py        # Simple test
│   ├── nova_act_tasks.py              # Nova tasks
│   ├── test_ictserve_helpdesk.py      # Helpdesk test
│   └── test_nova_act.py               # Nova test
│
├── docs/                               # Documentation scripts
│   └── candidate_docs.txt             # Candidate docs list
│
├── storage/                            # Storage utilities
│   └── .gitignore                     # Git ignore
│
├── tmp/                                # Temporary files
│   ├── tmp_dump_filament_keys.php     # Dump Filament keys
│   └── tmp_filament_keys.py           # Filament keys (Python)
│
└── deprecated/                         # Deprecated scripts
    ├── candidate_docs.txt             # Old candidate docs
    ├── doc_refs_debug.csv             # Debug CSV
    ├── extract_doc_refs_debug.ps1     # Extract debug
    ├── extract_doc_refs.ps1           # Extract refs
    ├── finalize-phase5.ps1            # Phase 5 finalize
    ├── generate-output.txt            # Generated output
    ├── missing_docs_names.txt         # Missing doc names
    └── missing_docs_paths.txt         # Missing doc paths
```

## Category Summary

| Category | Files | Purpose |
|----------|-------|---------|
| dev | 10 | Development environment management |
| testing | 12 | Testing and quality assurance |
| translations | 7 | Localization management |
| mcp | 16 | Model Context Protocol operations |
| memory | 8 | Knowledge graph and memory |
| neo4j | 17 | Neo4j database operations |
| database | 3 | Database utilities |
| setup | 5 | Initial project setup |
| maintenance | 6 | Code maintenance and cleanup |
| docker | 21 | Docker environment |
| laragon | 4 | Laragon environment |
| tools | 5 | Development tools |
| supervisor | 1 | Process supervision |
| nova | 7 | Nova AI testing |
| docs | 1 | Documentation |
| storage | 1 | Storage utilities |
| tmp | 2 | Temporary files |
| deprecated | 8 | Deprecated scripts |

**Total Categories:** 18  
**Total Script Files:** ~133

## File Type Distribution

- **PowerShell (.ps1):** ~60 files
- **PHP (.php):** ~30 files
- **Bash (.sh):** ~10 files
- **JavaScript (.js, .cjs):** ~5 files
- **Python (.py):** ~7 files
- **Cypher (.cypher):** ~15 files
- **Batch (.bat):** ~3 files
- **Config (.conf):** ~1 file
- **Text/Data (.txt, .csv, .log):** ~5 files

## Usage Patterns

### Most Used Categories

1. **dev/** - Daily development workflow
2. **testing/** - Continuous integration and testing
3. **mcp/** - MCP server management
4. **docker/** - Containerized development

### Occasional Use

- **translations/** - When adding new features
- **memory/** - Knowledge graph maintenance
- **database/** - Database troubleshooting
- **maintenance/** - Code cleanup tasks

### Setup Only

- **setup/** - Initial project configuration
- **laragon/** - Laragon-specific setup

### Reference Only

- **deprecated/** - Historical reference
- **tmp/** - Temporary development files

## Navigation Tips

1. **Start here:** `README.md` for full documentation
2. **Quick commands:** `QUICK-REFERENCE.md` for common tasks
3. **Development:** `dev/` directory for daily workflow
4. **Testing:** `testing/` directory for QA tasks
5. **Troubleshooting:** Check category-specific README files

## Maintenance Notes

- Scripts are organized by function, not by file type
- Cross-platform scripts (PHP, JS, Python) work everywhere
- Platform-specific scripts (.ps1, .sh, .bat) in same categories
- Deprecated scripts preserved for reference
- Temporary files isolated in `tmp/` directory

## Related Documentation

- [Main README](./README.md)
- [Quick Reference](./QUICK-REFERENCE.md)
- [Dev Startup Guide](./DEV-STARTUP-GUIDE.md)
- [Tech Stack](../.kiro/steering/tech.md)
- [MCP Configuration](../.kiro/steering/mcp.md)
