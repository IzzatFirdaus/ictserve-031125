# MCP Documentation Index

**Last Updated**: 2025-12-19  
**Total Documents**: 12 (Consolidated from 23)

This directory contains comprehensive documentation for Model Context Protocol (MCP) server configuration and usage in the ICTServe project.

---

## 📚 Core Documentation

### 🔧 Configuration & Setup

**[MCP_CONFIGURATION.md](MCP_CONFIGURATION.md)** - **START HERE**

- Complete configuration guide for all 12 MCP servers
- Setup instructions for local development and Docker
- API key management and security best practices
- Troubleshooting common issues
- **Use this for**: Initial setup, adding new servers, configuration changes

**[MCP_MEMORY_GUIDE.md](MCP_MEMORY_GUIDE.md)** - **MEMORY SERVER REFERENCE**

- Comprehensive guide to MCP Memory Server
- Entity and relation management
- Usage examples and best practices
- Error resolution and troubleshooting
- **Use this for**: Working with knowledge graphs, storing project context

---

## 🎯 Specialized Guides

### IDE-Specific Setup

**[CODEX_MCP_SETUP.md](CODEX_MCP_SETUP.md)**

- Codex extension configuration
- Local vs Docker mode switching
- Troubleshooting Codex-specific issues

**[GITHUB_MCP_SETUP.md](GITHUB_MCP_SETUP.md)**

- GitHub MCP Server Docker setup
- Personal Access Token configuration
- GitHub integration workflows

### Development Guides

**[LARAVEL_BOOST_MCP_INTEGRATION.md](LARAVEL_BOOST_MCP_INTEGRATION.md)** - **NEW: 2025-12-16**

- Laravel Boost MCP server configuration troubleshooting
- Laravel MCP framework vs direct command approach
- Protocol negotiation and connection timeout solutions
- LaravelBoostCompatServer implementation details
- **Use this for**: Laravel Boost connection issues, MCP integration problems

**[DEVELOPERS_MCP.md](DEVELOPERS_MCP.md)**

- Developer-focused MCP guide
- Cross-IDE best practices (VS Code, JetBrains, Windsurf, Cursor)
- Security and operational best practices
- Troubleshooting duplicate servers

**[LARAVEL_MCP_IMPLEMENTATION.md](LARAVEL_MCP_IMPLEMENTATION.md)**

- Laravel-specific MCP implementation
- Custom tools for ICTServe (QueryHelpdeskTicketsTool, CheckAssetStatusTool)
- Integration with existing MCP Memory

---

## 📊 Reference Documentation

### Server Documentation

**[MCP_SERVER_STATUS.md](MCP_SERVER_STATUS.md)**

- Current status of all 16 MCP servers
- Server capabilities and requirements
- Environment variables needed
- Quick reference for server features

**[MCP_SEQUENTIAL_THINKING_SETUP.md](MCP_SEQUENTIAL_THINKING_SETUP.md)**

- Sequential Thinking Server setup (Docker)
- Use cases for complex problem-solving
- Integration examples with ICTServe

**[MCP_SERVER_BEST_PRACTICES.md](MCP_SERVER_BEST_PRACTICES.md)**

- Operational best practices for all servers
- Performance optimization tips
- Security considerations
- Maintenance schedules

---

## 📝 Historical Records

Historical documentation has been moved to the `archive/` subdirectory:

- `archive/MCP_TEST_RESULTS.md` - Test results from December 4, 2025
- `archive/MCP_RESOLUTION_SUMMARY.md` - Memory server JSON error resolution
- `archive/UPDATE_SUMMARY.md` - Amazon Q configuration update
- `archive/CONSOLIDATION_SUMMARY.md` - Previous consolidation efforts
- `archive/MCP_SETUP_SUMMARY.md` - Docker setup summary
- `archive/MCP_MEMORY_CONFIG_IMPROVEMENTS.md` - Memory configuration improvements
- `archive/MCP_DOCKER_SETUP.md` - Docker-specific setup guide
- `archive/FETCH_MCP.md` - Fetch server documentation

---

## 🚀 Quick Start Guide

### For New Users

1. **Read**: [MCP_CONFIGURATION.md](MCP_CONFIGURATION.md) - Complete setup guide
2. **Configure**: Follow setup instructions for your environment (local or Docker)
3. **Test**: Start with core servers (memory, laravel-boost, sequentialthinking)
4. **Learn**: Read [MCP_MEMORY_GUIDE.md](MCP_MEMORY_GUIDE.md) for memory server usage

### For Existing Users

1. **Reference**: [MCP_SERVER_STATUS.md](MCP_SERVER_STATUS.md) - Check server capabilities
2. **Troubleshoot**: [MCP_CONFIGURATION.md](MCP_CONFIGURATION.md#troubleshooting) - Common issues
3. **Optimize**: [MCP_SERVER_BEST_PRACTICES.md](MCP_SERVER_BEST_PRACTICES.md) - Performance tips

### For Developers

1. **Read**: [DEVELOPERS_MCP.md](DEVELOPERS_MCP.md) - Developer guide
2. **Implement**: [LARAVEL_MCP_IMPLEMENTATION.md](LARAVEL_MCP_IMPLEMENTATION.md) - Custom tools
3. **Best Practices**: [MCP_SERVER_BEST_PRACTICES.md](MCP_SERVER_BEST_PRACTICES.md) - Operational guidelines

### For Laravel Boost Issues

1. **Check**: [LARAVEL_BOOST_MCP_INTEGRATION.md](LARAVEL_BOOST_MCP_INTEGRATION.md) - Connection timeout solutions
2. **Troubleshoot**: Laravel MCP framework vs direct command approach
3. **Configure**: Protocol negotiation and compatibility server setup

---

## 📖 Document Relationships

```
MCP_CONFIGURATION.md (Main Guide)
├── MCP_MEMORY_GUIDE.md (Memory Server Deep Dive)
├── MCP_SERVER_STATUS.md (Server Reference)
├── MCP_SEQUENTIAL_THINKING_SETUP.md (Sequential Thinking Setup)
└── MCP_SERVER_BEST_PRACTICES.md (Best Practices)

IDE-Specific Guides
├── CODEX_MCP_SETUP.md (Codex Extension)
└── GITHUB_MCP_SETUP.md (GitHub Integration)

Developer Guides
├── DEVELOPERS_MCP.md (Cross-IDE Development)
└── LARAVEL_MCP_IMPLEMENTATION.md (Laravel Integration)

Historical Records
├── MCP_TEST_RESULTS.md (Test Snapshot)
├── MCP_RESOLUTION_SUMMARY.md (Error Resolution)
└── UPDATE_SUMMARY.md (Configuration Updates)
```

---

## 🔍 Finding Information

### By Topic

| Topic | Document |
|-------|----------|
| Initial Setup | [MCP_CONFIGURATION.md](MCP_CONFIGURATION.md) |
| Memory Server | [MCP_MEMORY_GUIDE.md](MCP_MEMORY_GUIDE.md) |
| Server Status | [MCP_SERVER_STATUS.md](MCP_SERVER_STATUS.md) |
| Best Practices | [MCP_SERVER_BEST_PRACTICES.md](MCP_SERVER_BEST_PRACTICES.md) |
| Troubleshooting | [MCP_CONFIGURATION.md](MCP_CONFIGURATION.md#troubleshooting) |
| Laravel Integration | [LARAVEL_MCP_IMPLEMENTATION.md](LARAVEL_MCP_IMPLEMENTATION.md) |
| Developer Guide | [DEVELOPERS_MCP.md](DEVELOPERS_MCP.md) |

### By Server

| Server | Primary Document | Additional Info |
|--------|-----------------|-----------------|
| Memory | [MCP_MEMORY_GUIDE.md](MCP_MEMORY_GUIDE.md) | [MCP_CONFIGURATION.md](MCP_CONFIGURATION.md#category-1-core-development--analysis-4-servers) |
| Laravel Boost | [MCP_CONFIGURATION.md](MCP_CONFIGURATION.md#category-2-laravel--php-development-1-server) | [LARAVEL_MCP_IMPLEMENTATION.md](LARAVEL_MCP_IMPLEMENTATION.md) |
| Sequential Thinking | [MCP_SEQUENTIAL_THINKING_SETUP.md](MCP_SEQUENTIAL_THINKING_SETUP.md) | [MCP_CONFIGURATION.md](MCP_CONFIGURATION.md#category-1-core-development--analysis-4-servers) |
| Chrome DevTools | [MCP_CONFIGURATION.md](MCP_CONFIGURATION.md#category-3-browser-automation--debugging-2-servers) | [MCP_SERVER_STATUS.md](MCP_SERVER_STATUS.md) |
| Playwright | [MCP_CONFIGURATION.md](MCP_CONFIGURATION.md#category-3-browser-automation--debugging-2-servers) | [MCP_SERVER_STATUS.md](MCP_SERVER_STATUS.md) |
| GitHub | [GITHUB_MCP_SETUP.md](GITHUB_MCP_SETUP.md) | [MCP_CONFIGURATION.md](MCP_CONFIGURATION.md#category-5-version-control--repository-management-2-servers) |

---

## 🆘 Getting Help

1. **Check documentation**: Start with [MCP_CONFIGURATION.md](MCP_CONFIGURATION.md)
2. **Review troubleshooting**: See troubleshooting sections in main guides
3. **Check server status**: Verify in [MCP_SERVER_STATUS.md](MCP_SERVER_STATUS.md)
4. **Review best practices**: [MCP_SERVER_BEST_PRACTICES.md](MCP_SERVER_BEST_PRACTICES.md)
5. **Contact support**: <devops@motac.gov.my>

---

## 📝 Contributing

When adding new MCP documentation:

1. **Update this README** with links to new documents
2. **Follow naming convention**: `MCP_[TOPIC]_[TYPE].md`
3. **Include last updated date** in document header
4. **Cross-reference** related documents
5. **Update document relationships** diagram above

---

## 🔗 External Resources

- **MCP Specification**: <https://modelcontextprotocol.io>
- **Kiro IDE MCP Docs**: <https://kiro.dev/docs/mcp/>
- **Laravel Boost**: <https://github.com/laravel/boost>
- **ICTServe Main Docs**: `../D00_SYSTEM_OVERVIEW.md` through `../D15_UI_UX_STYLE_GUIDE.md`
- **Steering Documentation**: `../../.kiro/steering/mcp.md`

---

**Maintained by**: ICTServe Development Team  
**Last Review**: 2025-12-09  
**Next Review**: 2025-03-09
