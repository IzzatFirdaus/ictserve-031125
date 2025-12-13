# ICTServe Documentation

Complete documentation for ICTServe system - BPM MOTAC ICT Management Platform.

## Quick Navigation

### Docker Deployment

- **[Docker Documentation](docker/README.md)** - Overview and quick start
  - [Setup Guide](docker/setup.md) - Complete installation
  - [Architecture](docker/architecture.md) - Container design
  - [Troubleshooting](docker/troubleshooting.md) - Common issues
  - [Windows Guide](docker/windows.md) - Windows-specific instructions
  - [Container Specs](docker/container-specs.md) - Container specifications

### System Documentation (D00-D17)

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
- [D17 - Queue Management](D17_QUEUE_MANAGEMENT_HORIZON.md)

### Technical References

- **[Reference Documentation](reference/README.md)** - Setup guides and operational procedures
  - [Laragon Setup](reference/laragon-setup.md)
  - [Laravel Boost Setup](reference/laravel-boost-setup.md)
  - [Virtual Host Setup](reference/vhost-setup-guide.md)
  - [Deployment Checklist](reference/deployment-checklist.md)
  - [Performance Guide](reference/performance-optimization-guide.md)
  - [Production Troubleshooting](reference/troubleshooting-production.md)

### Additional Resources

- [Glossary](GLOSSARY.md) - Terminology
- [Index](INDEX.md) - Complete documentation index

## Documentation Structure

```text
docs/
├── docker/              # Docker deployment guides
│   ├── README.md
│   ├── SETUP.md
│   ├── ARCHITECTURE.md
│   ├── TROUBLESHOOTING.md
│   └── (deployment docs)
│
├── reference/           # Technical references
│   ├── README.md
│   ├── laragon-setup.md
│   ├── laravel-boost-setup.md
│   ├── vhost-setup-guide.md
│   ├── deployment-checklist.md
│   ├── performance-optimization-guide.md
│   ├── troubleshooting-production.md
│   ├── rtm/             # Requirements traceability
│   └── legacy/          # Archived docs
│
├── mcp/                 # MCP server documentation
├── frontend/            # Frontend guides
├── security/            # Security documentation
├── api/                 # API documentation
│
└── D00-D17.md           # System documentation
```

## Getting Started

### For Developers

1. **Setup Development Environment**

   - [Docker Setup](docker/SETUP.md) - Containerized development
   - [D10 - Source Code](D10_SOURCE_CODE_DOCUMENTATION.md) - Code standards

2. **Understand Architecture**
   - [D04 - Software Design](D04_SOFTWARE_DESIGN_DOCUMENT.md) - System architecture
   - [Docker Architecture](docker/ARCHITECTURE.md) - Container design

### For System Administrators

1. **Deploy Application**

   - [Docker Setup](docker/SETUP.md) - Production deployment
   - [Deployment Checklist](reference/deployment-checklist.md) - Pre-deployment checks

2. **Configure Services**

   - [D11 - Technical Design](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) - Infrastructure
   - [D16 - Broadcasting](D16_BROADCASTING_SETUP.md) - Real-time features
   - [D17 - Queue Management](D17_QUEUE_MANAGEMENT_HORIZON.md) - Background jobs

3. **Monitor Performance**
   - [Performance Guide](reference/performance-optimization-guide.md) - Optimization
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
- **Traceability** to requirements (D00-D17 framework)

## Contributing

When updating documentation:

1. Follow existing structure and naming conventions
2. Update version numbers and dates
3. Add traceability references where applicable
4. Test all code examples and commands
5. Update this README if adding new sections

## Support

- **GitHub Issues**: [Report issues](https://github.com/IzzatFirdaus/ictserve-031125/issues)
- **Email**: <ict@bpm.gov.my>
- **Documentation**: This directory

## Version History

| Version | Date       | Description                                       |
| ------- | ---------- | ------------------------------------------------- |
| v3.5.0  | 2025-12-05 | Reference docs reorganization |
| v3.1.0  | 2025-11-29 | Added D17 Queue Management, documentation cleanup |
| v3.0.0  | 2025-01-25 | Docker deployment integration                     |
| v2.0.0  | 2024-11-01 | Complete D00-D16 documentation                    |
| v1.0.0  | 2024-03-01 | Initial release                                   |

---

**Last Updated**: 2025-12-05  
**Maintained By**: BPM MOTAC ICT Team
