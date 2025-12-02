# ICTServe Documentation Index

**Last Updated**: 2025-11-29
**Version**: 3.3.0

---

## 📋 Quick Navigation

### 🔴 Core System Documentation (Root Directory)

Read these first to understand the ICTServe system:

| Document | Purpose |
|:---------|:--------|
| [README.md](README.md) | Project overview and quick start |
| [ICTServe_System_Documentation.md](ICTServe_System_Documentation.md) | Complete system overview |
| [GLOSSARY.md](GLOSSARY.md) | Key terminology and definitions |

### 📚 System Design Documents (D00–D17)

Canonical requirements, design, and standards documentation:

| Document | Purpose |
|:---------|:--------|
| [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md) | System vision, governance, stakeholders |
| [D01_SYSTEM_DEVELOPMENT_PLAN.md](D01_SYSTEM_DEVELOPMENT_PLAN.md) | Development methodology and change management |
| [D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md](D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md) | Business requirements and scope |
| [D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md](D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md) | Functional and non-functional requirements |
| [D04_SOFTWARE_DESIGN_DOCUMENT.md](D04_SOFTWARE_DESIGN_DOCUMENT.md) | Architecture, system design, workflows |
| [D05_DATA_MIGRATION_PLAN.md](D05_DATA_MIGRATION_PLAN.md) | Data migration strategy and planning |
| [D06_DATA_MIGRATION_SPECIFICATION.md](D06_DATA_MIGRATION_SPECIFICATION.md) | Technical data migration procedures |
| [D07_SYSTEM_INTEGRATION_PLAN.md](D07_SYSTEM_INTEGRATION_PLAN.md) | Integration strategy with external systems |
| [D08_SYSTEM_INTEGRATION_SPECIFICATION.md](D08_SYSTEM_INTEGRATION_SPECIFICATION.md) | Technical integration specifications |
| [D09_DATABASE_DOCUMENTATION.md](D09_DATABASE_DOCUMENTATION.md) | Database schema, audit trails, security |
| [D10_SOURCE_CODE_DOCUMENTATION.md](D10_SOURCE_CODE_DOCUMENTATION.md) | Source code organization and standards |
| [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) | Infrastructure, deployment, compliance |
| [D12_UI_UX_DESIGN_GUIDE.md](D12_UI_UX_DESIGN_GUIDE.md) | User interface design standards |
| [D13_UI_UX_FRONTEND_FRAMEWORK.md](D13_UI_UX_FRONTEND_FRAMEWORK.md) | Frontend framework and component system |
| [D14_UI_UX_STYLE_GUIDE.md](D14_UI_UX_STYLE_GUIDE.md) | Visual design, typography, colors |
| [D15_LANGUAGE_MS_EN.md](D15_LANGUAGE_MS_EN.md) | Bilingual language standards (BM/EN) |
| [D16_BROADCASTING_SETUP.md](D16_BROADCASTING_SETUP.md) | Broadcasting & WebSocket configuration |
| [D17_QUEUE_MANAGEMENT_HORIZON.md](D17_QUEUE_MANAGEMENT_HORIZON.md) | Queue management & Laravel Horizon |

### � Laravel Package Documentation

| Document | Purpose |
|:---------|:--------|
| [Laravel-Folio.md](Laravel-Folio.md) | Page-based routing documentation |
| [Laravel-Fortify.md](Laravel-Fortify.md) | Authentication backend documentation |
| [Laravel-Pint.md](Laravel-Pint.md) | Code style fixer documentation |
| [Laravel-Pulse.md](Laravel-Pulse.md) | Application monitoring documentation |
| [Laravel-Sail.md](Laravel-Sail.md) | Docker development environment |
| [Laravel-Sanctum.md](Laravel-Sanctum.md) | API authentication documentation |
| [Laravel-Socialite-Google.md](Laravel-Socialite-Google.md) | Google OAuth integration |
| [Laravel-Telescope.md](Laravel-Telescope.md) | Debug assistant documentation |

---

## 📂 Documentation Organization

### 🖥️ `frontend/` - Frontend Implementation Documentation

Frontend component and service documentation:

| Document | Purpose |
|:---------|:--------|
| [README.md](frontend/README.md) | Frontend documentation overview |
| [guest-loan-application.md](frontend/guest-loan-application.md) | Multi-step loan application wizard |
| [asset-availability-service.md](frontend/asset-availability-service.md) | Real-time asset availability checking |
| [loan-application-service.md](frontend/loan-application-service.md) | Loan application business logic |
| [alpine-patterns.md](frontend/alpine-patterns.md) | Alpine.js integration patterns |
| [livewire-patterns.md](frontend/livewire-patterns.md) | Livewire 3.x best practices |
| [volt-guidelines.md](frontend/volt-guidelines.md) | Volt single-file component guidelines |
| [ARCHITECTURAL_IMPROVEMENTS_SUMMARY.md](frontend/ARCHITECTURAL_IMPROVEMENTS_SUMMARY.md) | Architecture improvement summary |
| [COMPLIANCE_INTEGRATION_SUMMARY.md](frontend/COMPLIANCE_INTEGRATION_SUMMARY.md) | Compliance integration summary |
| [LIVEWIRE_MIGRATION_PROGRESS.md](frontend/LIVEWIRE_MIGRATION_PROGRESS.md) | Livewire migration progress |
| [PORTAL_AUDIT_FIXES.md](frontend/PORTAL_AUDIT_FIXES.md) | Portal audit fixes |
| [PR_LIVEWIRE_3_UPDATES.md](frontend/PR_LIVEWIRE_3_UPDATES.md) | Livewire 3 update notes |
| [VISUAL_AUDIT_FIXES.md](frontend/VISUAL_AUDIT_FIXES.md) | Visual audit fixes |
| [VOLT_CONVERSION_STRATEGY.md](frontend/VOLT_CONVERSION_STRATEGY.md) | Volt conversion strategy |

**When to Use**: Implementing frontend features, understanding Livewire components, service integration

### 🔐 `security/` - Security Implementation Documentation

Security features and abuse prevention:

| Document | Purpose |
|:---------|:--------|
| [README.md](security/README.md) | Security documentation overview |
| [ip-blocking-system.md](security/ip-blocking-system.md) | IP-based blocking for abuse prevention |
| [rate-limiting.md](security/rate-limiting.md) | Request rate limiting for guest forms |

**When to Use**: Implementing security features, configuring rate limits, managing blocked IPs

### 🐳 `docker/` - Docker & Container Documentation

Docker setup and containerization guides:

| Document | Purpose |
|:---------|:--------|
| [README.md](docker/README.md) | Docker documentation overview |
| [SETUP.md](docker/SETUP.md) | Docker setup guide |
| [ARCHITECTURE.md](docker/ARCHITECTURE.md) | Container architecture |
| [CONTAINER_SPECS.md](docker/CONTAINER_SPECS.md) | Container specifications |
| [CONTAINER_VERSIONS.md](docker/CONTAINER_VERSIONS.md) | Container version information |
| [TROUBLESHOOTING.md](docker/TROUBLESHOOTING.md) | Docker troubleshooting guide |
| [WINDOWS.md](docker/WINDOWS.md) | Windows-specific Docker setup |
| [DOCKER_FIX_502.md](docker/DOCKER_FIX_502.md) | Fix for 502 errors |
| [QUICK_FIXES.md](docker/QUICK_FIXES.md) | Quick fix solutions |

**When to Use**: Setting up Docker environment, troubleshooting container issues

### 🔧 `mcp/` - Model Context Protocol Documentation

MCP server setup and integration guides:

| Document | Purpose |
|:---------|:--------|
| [DEVELOPERS_MCP.md](mcp/DEVELOPERS_MCP.md) | MCP for developers |
| [LARAVEL_MCP_IMPLEMENTATION.md](mcp/LARAVEL_MCP_IMPLEMENTATION.md) | Laravel MCP implementation |
| [MCP_SERVER_BEST_PRACTICES.md](mcp/MCP_SERVER_BEST_PRACTICES.md) | MCP server best practices |
| [MCP_MEMORY_SERVER_FIX.md](mcp/MCP_MEMORY_SERVER_FIX.md) | Memory server fixes |
| [MCP_MEMORY_SETUP.md](mcp/MCP_MEMORY_SETUP.md) | Memory server setup |
| [MCP_SEQUENTIAL_THINKING_SETUP.md](mcp/MCP_SEQUENTIAL_THINKING_SETUP.md) | Sequential thinking setup |
| [GITHUB_MCP_SETUP.md](mcp/GITHUB_MCP_SETUP.md) | GitHub MCP setup |
| [CODEX_MCP_SETUP.md](mcp/CODEX_MCP_SETUP.md) | Codex MCP setup |

**When to Use**: Setting up MCP servers, integrating AI tools

### 🗂️ `reference/` - Reference Materials

Reference materials and traceability:

| Document | Purpose |
|:---------|:--------|
| [deployment-checklist.md](reference/deployment-checklist.md) | Deployment checklist |
| [performance-optimization-guide.md](reference/performance-optimization-guide.md) | Performance optimization guide |
| [missing-translation-keys-report.md](reference/missing-translation-keys-report.md) | Translation keys report |
| [LARASTAN_RESOLUTION_GUIDE.md](reference/LARASTAN_RESOLUTION_GUIDE.md) | Larastan resolution guide |
| [IDE_FALSE_POSITIVES.md](reference/IDE_FALSE_POSITIVES.md) | IDE false positives |
| [TESTING_AGILE.md](reference/TESTING_AGILE.md) | Agile testing guide |

**Subdirectory - `reference/rtm/`**: Requirements Traceability Matrix (CSV files)

**When to Use**: Mapping requirements to implementation, deployment checklists

---

## 🎓 Reading Paths

### 👨‍💼 For Business Stakeholders

1. [README.md](README.md) - Overview
2. [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md) - Vision
3. [D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md](D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md) - Requirements
4. [D04_SOFTWARE_DESIGN_DOCUMENT.md](D04_SOFTWARE_DESIGN_DOCUMENT.md) - System design

### 👨‍💻 For Developers

1. [README.md](README.md) - Start here
2. [D04_SOFTWARE_DESIGN_DOCUMENT.md](D04_SOFTWARE_DESIGN_DOCUMENT.md) - Architecture
3. [D10_SOURCE_CODE_DOCUMENTATION.md](D10_SOURCE_CODE_DOCUMENTATION.md) - Code organization
4. [frontend/](frontend/) - Frontend patterns
5. [D14_UI_UX_STYLE_GUIDE.md](D14_UI_UX_STYLE_GUIDE.md) - Styling

### 🔐 For DevOps/Infrastructure

1. [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) - Infrastructure
2. [D09_DATABASE_DOCUMENTATION.md](D09_DATABASE_DOCUMENTATION.md) - Database
3. [D16_BROADCASTING_SETUP.md](D16_BROADCASTING_SETUP.md) - WebSocket & broadcasting
4. [D17_QUEUE_MANAGEMENT_HORIZON.md](D17_QUEUE_MANAGEMENT_HORIZON.md) - Queue management
5. [docker/](docker/) - Docker setup

### ♿ For Accessibility/Compliance

1. [D12_UI_UX_DESIGN_GUIDE.md](D12_UI_UX_DESIGN_GUIDE.md) - Accessible design
2. [D15_LANGUAGE_MS_EN.md](D15_LANGUAGE_MS_EN.md) - Bilingual standards
3. [frontend/COMPLIANCE_INTEGRATION_SUMMARY.md](frontend/COMPLIANCE_INTEGRATION_SUMMARY.md) - Compliance

---

## 📊 Documentation Statistics

| Category | Files | Purpose |
|:---------|:------|:--------|
| Core (D00–D17) | 18 | System overview, design, standards |
| Root docs | 12 | Core documentation and Laravel packages |
| Frontend | 14 | Livewire components, services, patterns |
| Security | 3 | IP blocking, rate limiting |
| Docker | 13 | Container setup and troubleshooting |
| MCP | 9 | Model Context Protocol integration |
| Reference | 12+ | Traceability, deployment, optimization |
| **Total** | **80+** | Complete system documentation |

---

## 🔍 Finding What You Need

### By Topic

**Frontend/UI Development**:

- [D12_UI_UX_DESIGN_GUIDE.md](D12_UI_UX_DESIGN_GUIDE.md) - Design standards
- [D13_UI_UX_FRONTEND_FRAMEWORK.md](D13_UI_UX_FRONTEND_FRAMEWORK.md) - Frameworks
- [D14_UI_UX_STYLE_GUIDE.md](D14_UI_UX_STYLE_GUIDE.md) - Styling
- [frontend/](frontend/) - Livewire components, services, patterns

**Security & Abuse Prevention**:

- [security/ip-blocking-system.md](security/ip-blocking-system.md) - IP blocking
- [security/rate-limiting.md](security/rate-limiting.md) - Rate limiting
- [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) - Security architecture

**Database & Data**:

- [D09_DATABASE_DOCUMENTATION.md](D09_DATABASE_DOCUMENTATION.md) - Schema
- [D05_DATA_MIGRATION_PLAN.md](D05_DATA_MIGRATION_PLAN.md) - Migration strategy
- [D06_DATA_MIGRATION_SPECIFICATION.md](D06_DATA_MIGRATION_SPECIFICATION.md) - Migration specs

**Deployment & Infrastructure**:

- [D11_TECHNICAL_DESIGN_DOCUMENTATION.md](D11_TECHNICAL_DESIGN_DOCUMENTATION.md) - Systems
- [D16_BROADCASTING_SETUP.md](D16_BROADCASTING_SETUP.md) - WebSocket setup
- [D17_QUEUE_MANAGEMENT_HORIZON.md](D17_QUEUE_MANAGEMENT_HORIZON.md) - Queue management
- [docker/](docker/) - Docker setup

**API & Integration**:

- [D07_SYSTEM_INTEGRATION_PLAN.md](D07_SYSTEM_INTEGRATION_PLAN.md) - Integration strategy
- [D08_SYSTEM_INTEGRATION_SPECIFICATION.md](D08_SYSTEM_INTEGRATION_SPECIFICATION.md) - Integration specs

**Compliance & Standards**:

- [D15_LANGUAGE_MS_EN.md](D15_LANGUAGE_MS_EN.md) - Localization
- [frontend/COMPLIANCE_INTEGRATION_SUMMARY.md](frontend/COMPLIANCE_INTEGRATION_SUMMARY.md) - Compliance

---

## 📝 Contributing to Documentation

When adding new documentation:

1. **Type**: Determine which category it belongs to
2. **Location**: Place in appropriate subdirectory
3. **Naming**: Use descriptive, kebab-case names (e.g., `feature-name.md`)
4. **Format**: Follow existing documentation style
5. **Index**: Update this INDEX.md file if adding a new major section
6. **Links**: Add cross-references to related documents

---

## 📞 Documentation Standards

All documentation in ICTServe follows:

- **Language**: Primary Bahasa Melayu, Secondary English
- **Format**: Markdown (.md files)
- **Standards**:
  - WCAG 2.2 Level AA for accessibility
  - PSR-12 for code examples
  - ISO/IEC/IEEE standards (referenced in D documents)
- **Traceability**: References to D00–D17 where applicable

---

## 📅 Version History

| Version | Date | Changes |
|:--------|:-----|:--------|
| 3.3.0 | 2025-11-29 | Updated to reflect Guest-First architecture completion (v3.3.0). All core documents (D00-D17) aligned with guest forms and admin-only authentication. |
| 2.3.0 | 2025-11-29 | Major cleanup: removed references to non-existent directories (guides/, features/, technical/, testing/, archive/). Updated to reflect actual directory structure (frontend/, security/, docker/, mcp/, reference/). |
| 2.2.0 | 2025-11-29 | Added D16 and D17 to documentation index |
| 2.1.0 | 2025-11-27 | Added frontend/ and security/ directories |
| 2.0.0 | 2025-11-06 | Major reorganization |
| 1.0.0 | 2025-11-01 | Initial documentation organization |

---

**Last Updated**: 2025-11-29
**Status**: ✅ Active and maintained
**Maintainer**: ICTServe Development Team
