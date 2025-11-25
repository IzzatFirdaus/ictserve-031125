# ICTServe Documentation

Complete documentation for ICTServe system - BPM MOTAC ICT Management Platform.

## Quick Navigation

### 📦 Docker Deployment
- [Docker README](docker/README.md) - Overview and quick start
- [Setup Guide](docker/SETUP.md) - Complete installation
- [Architecture](docker/ARCHITECTURE.md) - Container design
- [Troubleshooting](docker/TROUBLESHOOTING.md) - Common issues

### 🧠 Mimir AI Memory
- [Mimir README](mimir/README.md) - Overview and quick start
- [Setup Guide](mimir/SETUP.md) - Installation and configuration
- [Docker Deployment](mimir/DOCKER.md) - Docker setup
- [Submodule Management](mimir/SUBMODULE.md) - Git submodule operations
- [MCP Integration](mimir/MCP_INTEGRATION.md) - AI agent integration

### 📚 System Documentation (D00-D16)
- [D00 - System Overview](D00_SYSTEM_OVERVIEW.md)
- [D01 - Development Plan](D01_SYSTEM_DEVELOPMENT_PLAN.md)
- [D02 - Business Requirements](D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md)
- [D03 - Software Requirements](D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md)
- [D04 - Software Design](D04_SOFTWARE_DESIGN_DOCUMENT.md)
- [D05 - Data Migration Plan](D05_DATA_MIGRATION_PLAN.md)
- [D06 - Data Migration Spec](D06_DATA_MIGRATION_SPECIFICATION.md)
- [D07 - Integration Plan](D07_SYSTEM_INTEGRATION_PLAN.md)
- [D08 - Integration Spec](D08_SYSTEM_INTEGRATION_SPECIFICATION.md)
- [D09 - Database Documentation](D09_DATABASE_DOCUMENTATION.md)
- [D10 - Source Code Documentation](D10_SOURCE_CODE_DOCUMENTATION.md)
- [D11 - Technical Design](D11_TECHNICAL_DESIGN_DOCUMENTATION.md)
- [D12 - UI/UX Design Guide](D12_UI_UX_DESIGN_GUIDE.md)
- [D13 - Frontend Framework](D13_UI_UX_FRONTEND_FRAMEWORK.md)
- [D14 - Style Guide](D14_UI_UX_STYLE_GUIDE.md)
- [D15 - Localization](D15_LANGUAGE_MS_EN.md)
- [D16 - Broadcasting Setup](D16_BROADCASTING_SETUP.md)

### 🔧 Technical References
- [MCP Setup](MCP_MEMORY_SETUP.md) - MCP Memory server
- [Sequential Thinking](MCP_SEQUENTIAL_THINKING_SETUP.md) - MCP Sequential Thinking
- [Neo4j Guide](NEO4J_KNOWLEDGE_GRAPH_GUIDE.md) - Knowledge graph
- [Performance Guide](performance-optimization-guide.md) - Optimization
- [Deployment Checklist](deployment-checklist.md) - Production deployment
- [Glossary](GLOSSARY.md) - Terminology
- [Index](INDEX.md) - Complete documentation index

## Documentation Structure

```
docs/
├── docker/              # Docker deployment guides
│   ├── README.md
│   ├── SETUP.md
│   ├── ARCHITECTURE.md
│   ├── TROUBLESHOOTING.md
│   ├── SUCCESS.md       # Deployment verification
│   ├── UPDATE_PLAN.md   # Update history
│   └── NEXT_STEPS.md    # Future improvements
│
├── mimir/               # Mimir AI memory system
│   ├── README.md
│   ├── SETUP.md
│   ├── DOCKER.md
│   ├── SUBMODULE.md
│   ├── MCP_INTEGRATION.md
│   └── submodule/       # Mimir submodule docs
│       ├── DEPLOYMENT_SUCCESS.md
│       ├── VERIFIED.md
│       ├── SETUP_GUIDE.md
│       └── DOCKER_SETUP.md
│
├── reference/           # Technical references
│   └── (various guides)
│
└── D00-D16 docs         # System documentation
```

## Getting Started

### For Developers

1. **Setup Development Environment**
   - [Docker Setup](docker/SETUP.md) - Containerized development
   - [D10 - Source Code](D10_SOURCE_CODE_DOCUMENTATION.md) - Code standards

2. **Understand Architecture**
   - [D04 - Software Design](D04_SOFTWARE_DESIGN_DOCUMENT.md) - System architecture
   - [Docker Architecture](docker/ARCHITECTURE.md) - Container design

3. **Configure AI Tools**
   - [Mimir Setup](mimir/SETUP.md) - AI memory system
   - [MCP Integration](mimir/MCP_INTEGRATION.md) - AI agent tools

### For System Administrators

1. **Deploy Application**
   - [Docker Setup](docker/SETUP.md) - Production deployment
   - [Deployment Checklist](deployment-checklist.md) - Pre-deployment checks

2. **Configure Services**
   - [D11 - Technical Design](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) - Infrastructure
   - [D16 - Broadcasting](D16_BROADCASTING_SETUP.md) - Real-time features

3. **Monitor Performance**
   - [Performance Guide](performance-optimization-guide.md) - Optimization
   - [Docker Troubleshooting](docker/TROUBLESHOOTING.md) - Issue resolution

### For Business Analysts

1. **Understand Requirements**
   - [D02 - Business Requirements](D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md)
   - [D03 - Software Requirements](D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md)

2. **Review Design**
   - [D12 - UI/UX Design](D12_UI_UX_DESIGN_GUIDE.md)
   - [D14 - Style Guide](D14_UI_UX_STYLE_GUIDE.md)

3. **Plan Integration**
   - [D07 - Integration Plan](D07_SYSTEM_INTEGRATION_PLAN.md)
   - [D08 - Integration Spec](D08_SYSTEM_INTEGRATION_SPECIFICATION.md)

## Documentation Standards

All documentation follows:
- **Semantic Versioning** (SemVer) for version tracking
- **Markdown** format for readability
- **Bilingual** support (Bahasa Melayu primary, English secondary)
- **Traceability** to requirements (D00-D16 framework)

## Contributing

When updating documentation:

1. Follow existing structure and naming conventions
2. Update version numbers and dates
3. Add traceability references where applicable
4. Test all code examples and commands
5. Update this README if adding new sections

## Support

- **GitHub Issues**: [Report issues](https://github.com/IzzatFirdaus/ictserve-031125/issues)
- **Email**: ict@bpm.gov.my
- **Documentation**: This directory

## Version History

- **v3.0.0** (2025-01-25) - Docker deployment, Mimir integration
- **v2.0.0** (2024-11-01) - Complete D00-D16 documentation
- **v1.0.0** (2024-03-01) - Initial release

---

**Last Updated**: 2025-01-25  
**Maintained By**: BPM MOTAC ICT Team
