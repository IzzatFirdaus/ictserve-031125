# Mimir Changelog

**Version**: 4.1.0  
**Last Updated**: 2025-12-05

---

## Version 4.1.0 (Current)

**Release Date**: 2025-11-30  
**Status**: ✅ Production

### Features

- ✅ **Vector Embeddings**: Ollama nomic-embed-text (768 dimensions)
- ✅ **LLM Provider**: GitHub Copilot GPT-4.1 via proxy
- ✅ **Knowledge Graph**: Neo4j 5.15-community
- ✅ **MCP Tools**: 17 tools for knowledge management
- ✅ **Workflow Orchestration**: Multi-agent task execution
- ✅ **File Indexing**: Auto-indexing with watch mode
- ✅ **Semantic Search**: Vector-based similarity search

### Configuration

```env
# LLM
MIMIR_DEFAULT_PROVIDER=openai
MIMIR_DEFAULT_MODEL=gpt-4.1
MIMIR_LLM_API=http://copilot-api:4141

# Embeddings
MIMIR_EMBEDDINGS_PROVIDER=ollama
MIMIR_EMBEDDINGS_MODEL=nomic-embed-text
MIMIR_EMBEDDINGS_DIMENSIONS=768

# Database
NEO4J_URI=bolt://localhost:7687
```

### Services

| Service | Version | Port | Status |
|---------|---------|------|--------|
| Mimir Server | 4.1.0 | 9042 | ✅ Healthy |
| Neo4j | 5.15-community | 7474, 7687 | ✅ Healthy |
| Copilot API | Latest | 4141 | ✅ Healthy |
| Ollama | Latest | 11434 | ✅ Healthy |

### Documentation

- ✅ Complete documentation reorganization
- ✅ 11 numbered guides (00-11)
- ✅ Legacy files archived
- ✅ Quick reference guide
- ✅ Troubleshooting guide

---

## Version 4.0.0

**Release Date**: 2025-11-15  
**Status**: Superseded

### Major Changes

- Initial v4 release
- Neo4j knowledge graph integration
- MCP protocol support
- Multi-agent workflows

### Breaking Changes

- Removed v3 file-based storage
- New MCP tool interface
- Updated configuration format

---

## Version 3.x

**Release Date**: 2025-10-01  
**Status**: Deprecated

### Features

- File-based knowledge storage
- Basic LLM integration
- Simple task management

### Limitations

- No vector embeddings
- No knowledge graph
- Limited scalability

---

## ICTServe Integration History

### 2025-12-05: Documentation Reorganization

**Changes**:

- Created numbered documentation structure (00-11)
- Moved 20 legacy files to `legacy/` directory
- Updated all guides to reflect v4.1.0 configuration
- Added comprehensive troubleshooting guide

**Files Created**:

- `00-INDEX.md` - Master navigation
- `01-SETUP.md` - Installation guide
- `02-DOCKER.md` - Docker deployment
- `03-QUICK-REFERENCE.md` - Quick commands
- `04-MCP-INTEGRATION.md` - Kiro IDE integration
- `05-SUBMODULE.md` - Git submodule management
- `06-API-REFERENCE.md` - MCP tools reference
- `07-NEO4J-GUIDE.md` - Knowledge graph usage
- `08-EMBEDDINGS.md` - Vector embeddings
- `09-WORKFLOWS.md` - Workflow orchestration
- `10-TROUBLESHOOTING.md` - Common issues
- `11-CHANGELOG.md` - This file

### 2025-12-04: Embeddings Configuration

**Changes**:

- Switched from GitHub Copilot to Ollama embeddings
- Model: nomic-embed-text (768 dimensions)
- Reason: Lightweight, fast, local processing
- Status: ✅ Operational

**Configuration**:

```env
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_PROVIDER=ollama
MIMIR_EMBEDDINGS_MODEL=nomic-embed-text
MIMIR_EMBEDDINGS_DIMENSIONS=768
```

### 2025-11-30: Initial Setup

**Changes**:

- Installed Mimir v4.1.0 as git submodule
- Configured GitHub Copilot GPT-4.1 as LLM provider
- Set up Neo4j knowledge graph
- Configured Docker Compose services
- Created initial documentation

**Services Deployed**:

- Mimir Server (port 9042)
- Neo4j Database (ports 7474, 7687)
- Copilot API Proxy (port 4141)
- Ollama Server (port 11434)

---

## Configuration Changes

### v4.1.0 → Current

**LLM Provider**:

```diff
- MIMIR_DEFAULT_PROVIDER=copilot
+ MIMIR_DEFAULT_PROVIDER=openai
- MIMIR_DEFAULT_MODEL=gpt-4
+ MIMIR_DEFAULT_MODEL=gpt-4.1
```

**Embeddings**:

```diff
- MIMIR_EMBEDDINGS_PROVIDER=copilot
+ MIMIR_EMBEDDINGS_PROVIDER=ollama
- MIMIR_EMBEDDINGS_MODEL=text-embedding-3-small
+ MIMIR_EMBEDDINGS_MODEL=nomic-embed-text
- MIMIR_EMBEDDINGS_DIMENSIONS=1536
+ MIMIR_EMBEDDINGS_DIMENSIONS=768
```

---

## Known Issues

### Current Issues

None reported for v4.1.0 with current configuration.

### Resolved Issues

#### Neo4j Startup Delay (2025-12-05)

**Issue**: Neo4j container marked unhealthy on first startup

**Cause**: Health check ran before database fully initialized

**Solution**: Wait 45-60 seconds after `docker compose up -d`

**Status**: ✅ Resolved

#### Environment Variable Warnings (2025-12-05)

**Issue**: Docker Compose warnings about unset variables

**Cause**: Optional security variables not set in `.env`

**Solution**: Added empty string defaults to `Mimir/.env`

**Status**: ✅ Resolved

---

## Upgrade Guide

### From v4.0.0 to v4.1.0

1. **Backup Neo4j data**:

   ```bash
   cd Mimir
   docker compose stop neo4j_db
   cp -r neo4j-data neo4j-data-backup
   ```

2. **Update Mimir**:

   ```bash
   cd Mimir
   git pull origin main
   ```

3. **Update configuration**:

   ```bash
   # Update Mimir/.env with new settings
   # See Configuration Changes section
   ```

4. **Restart services**:

   ```bash
   docker compose down
   docker compose up -d
   ```

5. **Verify**:

   ```bash
   docker ps
   curl http://localhost:9042/health
   ```

---

## Roadmap

### Planned Features

- [ ] Multi-workspace support
- [ ] Advanced workflow templates
- [ ] Real-time collaboration
- [ ] Enhanced security features
- [ ] Performance optimizations

### Under Consideration

- [ ] Alternative embedding providers
- [ ] Custom LLM integrations
- [ ] Advanced analytics dashboard
- [ ] Export/import workflows

---

## Support

### Getting Help

- **Documentation**: See `docs/mimir/00-INDEX.md`
- **Troubleshooting**: See `docs/mimir/10-TROUBLESHOOTING.md`
- **GitHub Issues**: <https://github.com/IzzatFirdaus/Mimir/issues>

### Reporting Issues

When reporting issues, include:

1. Mimir version (`docker logs mimir_server | grep version`)
2. Configuration (sanitized `.env`)
3. Error logs (`docker logs mimir_server --tail 100`)
4. Steps to reproduce

---

## Contributors

### ICTServe Integration

- **Izzat Firdaus** - Initial setup and configuration
- **Development Team** - Documentation and testing

### Mimir Core

- See <https://github.com/IzzatFirdaus/Mimir/graphs/contributors>

---

## License

Mimir is licensed under the MIT License. See the Mimir repository for details.

---

**Last Updated**: 2025-12-05  
**Current Version**: 4.1.0  
**Status**: ✅ Production Ready
