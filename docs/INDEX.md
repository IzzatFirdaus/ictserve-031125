# ICTServe Documentation Index

**Last Updated**: 2025-11-06
**Version**: 2.0.0

---

## 📋 Quick Navigation

### 🔴 CORE SYSTEM DOCUMENTATION (Root Directory)

Read these first to understand the ICTServe system:

1. **README.md** - Start here! Project overview and quick start
2. **ICTServe_System_Documentation.md** - Complete system overview
3. **GLOSSARY.md** - Key terminology and definitions


### 📚 System Design Documents (D00–D15)

Canonical requirements, design, and standards documentation:

| Document | Purpose |
|----------|---------|
| **D00_SYSTEM_OVERVIEW.md** | System vision, governance, stakeholders |
| **D01_SYSTEM_DEVELOPMENT_PLAN.md** | Development methodology and change management |
| **D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md** | Business requirements and scope |
| **D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md** | Functional and non-functional requirements |
| **D04_SOFTWARE_DESIGN_DOCUMENT.md** | Architecture, system design, workflows |
| **D05_DATA_MIGRATION_PLAN.md** | Data migration strategy and planning |
| **D06_DATA_MIGRATION_SPECIFICATION.md** | Technical data migration procedures |
| **D07_SYSTEM_INTEGRATION_PLAN.md** | Integration strategy with external systems |
| **D08_SYSTEM_INTEGRATION_SPECIFICATION.md** | Technical integration specifications |
| **D09_DATABASE_DOCUMENTATION.md** | Database schema, audit trails, security |
| **D10_SOURCE_CODE_DOCUMENTATION.md** | Source code organization and standards |
| **D11_TECHNICAL_DESIGN_DOCUMENTATION.md** | Infrastructure, deployment, compliance |
| **D12_UI_UX_DESIGN_GUIDE.md** | User interface design standards |
| **D13_UI_UX_FRONTEND_FRAMEWORK.md** | Frontend framework and component system |
| **D14_UI_UX_STYLE_GUIDE.md** | Visual design, typography, colors |
| **D15_LANGUAGE_MS_EN.md** | Bilingual language standards (BM/EN) |

---

## 📂 Documentation Organization

### 🎯 `guides/` - System Guides & Best Practices

Practical guides and best practices for understanding system features:

- **frontend/** - Frontend development guides (16 files)
  - **accessibility-*.md** - WCAG 2.2 AA accessibility standards and patterns
  - **responsive-design-*.md** - Mobile-first responsive design patterns
  - **bilingual-*.md** - Bilingual localization and i18n implementation
  - **component-usage-*.md** - Component library usage guides


- **asset-loan-system-flow.md** - Asset borrowing workflow and process flow
- **helpdesk-system-flow.md** - Helpdesk ticketing system workflow
- **broadcasting-setup.md** - Laravel Reverb broadcast configuration
- **HELPDESK_QUICK_REFERENCE.txt** - Helpdesk system quick reference
- **LOAN_QUICK_REFERENCE.md** - Asset loan system quick reference


**When to Use**: Learning system features, understanding workflows, accessibility standards, responsive design patterns

### 🔧 `features/` - Feature Implementation Details

Detailed implementation guides for specific features (currently transitioning to `technical/`):

- **admin-seeding.md** - Database seeding and sample data creation
- **component-library-audit-2025-11-05.md** - Component library audit report
- **helpdesk_form_to_model.md** - Helpdesk form and data model mapping
- **loan_form_to_model.md** - Loan application form and data model mapping


**When to Use**: Implementing specific features or understanding form-to-model relationships

### 🛠️ `technical/` - Technical Implementation & Specifications

Production implementation guides, infrastructure specs, and technical references:

**Frontend Performance & Optimization** (13 files):

- **performance-optimization-*.md** - Performance tuning and optimization guides
- **component-architecture-*.md** - Frontend component architecture patterns
- **livewire-optimization-*.md** - Livewire component optimization strategies


**Email & Integration System**:

- **EMAIL_NOTIFICATION_SYSTEM.md** - Email notification architecture and API
- **EMAIL_NOTIFICATION_QUICK_START.md** - Quick start guide for email system
- **TASKS_10.1_10.2_CHECKLIST.md** - Email notification and dual approval completion checklist


**Code Quality & Infrastructure**:

- **PHPSTAN_ANALYSIS_NOTES.md** - Static analysis findings and explanations
- **devtools-mcp-getting-started.md** - Model Context Protocol setup
- **pdpa-compliance-implementation.md** - PDPA privacy compliance implementation


**API Documentation**:

- **manifest.yml** - API manifest specification
- **v1.yml** - OpenAPI v1 specification


**Subdirectories**:

- **implementation/** - Implementation-specific documentation
  - dual-approval-system-implementation.md
  - email-notification-system-implementation.md
  - HYBRID_FORMS_IMPLEMENTATION.md
- **ollama-laravel/** - AI/LLM integration documentation (6 files)


**When to Use**: Implementing features, configuring systems, troubleshooting, API integration

### 📖 `testing/` - Testing, QA & Compliance Reports

Testing strategies, quality assurance procedures, and compliance audit reports:

**Frontend Testing** (13 files):

- **E2E_*.md** - End-to-end testing procedures and guides
- **browser-compatibility-testing-*.md** - Cross-browser compatibility testing
- **compliance-*.md** - Compliance validation and testing procedures
- **accessibility-testing-*.md** - Accessibility (WCAG 2.2 AA) testing procedures


**Test Strategy & Quality**:

- **automated-testing-pipeline.md** - CI/CD testing pipeline documentation
- **testing-strategy.md** - Overall QA and testing strategy
- **compliance-verification-procedures.md** - Compliance verification checklists


**Audit & Compliance Reports** (10 files):

- **email-compliance-report.md** - Email system compliance audit
- **frontend-compliance-audit-report.md** - Frontend compliance audit
- **performance-optimization-report.md** - Performance testing results
- **comprehensive-test-suite-report.md** - Complete test suite results
- Additional audit reports for specific modules


**When to Use**: Running tests, QA procedures, compliance verification, audit findings

### 📚 `archive/` - Historical & Versioned Documentation

Previous release documentation and historical reference:

- **versions/2.1.0/** - Complete v2.1.0 release documentation
  - v2.1.0_Dokumentasi_Flow_Sistem_Helpdesk_ServiceDesk_ICTServe(iServe).md
  - v2.1.0_Dokumentasi_Flow_Sistem_Permohonan_Pinjaman_Aset_ICT_ICTServe(iServe).md
  - v2.1.0_Dokumentasi_Reka_Bentuk_Sistem_ICTServe(iServe).md
  - v2.1.0_Dokumentasi_Reka_Bentuk_ICTServe(iServe).md
  - v2.1.0_Dokumentasi_Jadual_Data_Pengguna_Organisasi_Teras_ICTServe(iServe).md
  - v2.1.0_Dokumentasi_Sistem_Notifikasi_E-mel_ICTServe(iServe).md


**When to Use**: Referencing previous version specifications, historical context

### 🗂️ `reference/` - Pure Reference Materials

Minimal reference materials retained for lookup:

- **rtm/** - Requirements Traceability Matrix (4 CSV files)
  - coredata_requirements_rtm.csv
  - helpdesk_requirements_rtm.csv
  - loan_requirements_rtm.csv
  - requirements-traceability.csv


**When to Use**: Mapping requirements to implementation, RTM lookups

---

## 🎓 Reading Paths

### 👨‍💼 For Business Stakeholders

1. README.md (overview)
2. D00_SYSTEM_OVERVIEW.md (vision)
3. D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md (requirements)
4. guides/asset-loan-system-flow.md (workflows)
5. guides/helpdesk-system-flow.md (workflows)


### 👨‍💻 For Developers

1. README.md (start here)
2. D04_SOFTWARE_DESIGN_DOCUMENT.md (architecture)
3. D10_SOURCE_CODE_DOCUMENTATION.md (code organization)
4. technical/EMAIL_NOTIFICATION_SYSTEM.md (feature implementation)
5. reference/frontend/ (component library)
6. D14_UI_UX_STYLE_GUIDE.md (styling)


### 🔐 For DevOps/Infrastructure

1. D11_TECHNICAL_DESIGN_DOCUMENTATION.md (infrastructure)
2. D09_DATABASE_DOCUMENTATION.md (database)
3. guides/broadcasting-setup.md (setup)
4. technical/devtools-mcp-getting-started.md (deployment)


### ♿ For Accessibility/Compliance

1. D12_UI_UX_DESIGN_GUIDE.md (accessible design)
2. reference/frontend/accessibility-guidelines.md (WCAG standards)
3. D15_LANGUAGE_MS_EN.md (bilingual standards)
4. reference/reports/frontend-compliance-audit-report.md (audit)


### 🧪 For QA/Testing

1. reference/testing/ (test frameworks)
2. reference/rtm/ (requirements traceability)
3. reference/reports/ (audit findings)


---

## 📊 Documentation Statistics

| Category | Files | Purpose |
|----------|-------|---------|
| Core (Root D00–D15) | 19 | System overview, design, standards |
| Guides | 18+ | Best practices, workflows, setup procedures |
| Features | 5 | Feature-specific implementation details |
| Technical | 50+ | Implementation specs, infrastructure, APIs, frontend optimization |
| Testing | 40+ | Test strategies, QA procedures, compliance audit reports |
| Archive | 6 | Historical versioned documentation (v2.1.0) |
| Reference | 4 | Requirements Traceability Matrix (RTM CSV files) |
| **Total** | **140+** | Complete, organized system documentation |

---

## 🔍 Finding What You Need

### By Topic

**Email System**:

- technical/EMAIL_NOTIFICATION_SYSTEM.md (architecture)
- technical/EMAIL_NOTIFICATION_QUICK_START.md (quick start)
- technical/TASKS_10.1_10.2_CHECKLIST.md (completion status)
- testing/ (email compliance reports)


**Helpdesk System**:

- guides/helpdesk-system-flow.md (workflow)
- features/helpdesk_form_to_model.md (data model)
- reference/rtm/helpdesk_requirements_rtm.csv (requirements)


**Loan System**:

- guides/asset-loan-system-flow.md (workflow)
- technical/implementation/dual-approval-system-implementation.md (approval system)
- features/loan_form_to_model.md (data model)
- reference/rtm/loan_requirements_rtm.csv (requirements)


**Frontend/UI Development**:

- D12_UI_UX_DESIGN_GUIDE.md (design standards)
- D13_UI_UX_FRONTEND_FRAMEWORK.md (frameworks)
- D14_UI_UX_STYLE_GUIDE.md (styling)
- guides/frontend/ (accessibility, responsive design, bilingual support)
- technical/frontend/ (performance optimization, component architecture)
- testing/frontend/ (E2E testing, compliance validation)


**Database & Data**:

- D09_DATABASE_DOCUMENTATION.md (schema)
- D05_DATA_MIGRATION_PLAN.md (migration strategy)
- D06_DATA_MIGRATION_SPECIFICATION.md (migration specs)
- reference/rtm/ (requirements traceability)


**Testing & Quality Assurance**:

- testing/ (test strategies, procedures, audit reports)
- testing/frontend/ (E2E testing, compliance testing)
- reference/rtm/ (requirements-to-test mapping)


**Deployment & Infrastructure**:

- D11_TECHNICAL_DESIGN_DOCUMENTATION.md (systems)
- guides/broadcasting-setup.md (broadcast setup)
- technical/devtools-mcp-getting-started.md (MCP setup)
- testing/ (compliance verification procedures)


**Compliance & Standards**:

- D15_LANGUAGE_MS_EN.md (localization)
- testing/ (audit reports and compliance reports)
- guides/frontend/accessibility-*.md (WCAG 2.2 AA standards)
- technical/pdpa-compliance-implementation.md (privacy compliance)


**API & Integration**:

- technical/manifest.yml (API manifest)
- technical/v1.yml (OpenAPI v1 specification)
- D07_SYSTEM_INTEGRATION_PLAN.md (integration strategy)
- D08_SYSTEM_INTEGRATION_SPECIFICATION.md (integration specs)


---

## 📝 Contributing to Documentation

When adding new documentation:

1. **Type**: Determine which category it belongs to (guides, features, technical, reference)
2. **Location**: Place in appropriate subdirectory
3. **Naming**: Use descriptive, kebab-case names (e.g., `feature-name.md`)
4. **Format**: Follow existing documentation style
5. **Index**: Update this INDEX.md file if adding a new major section
6. **Links**: Add cross-references to related documents


---

## 🚀 Quick Commands

**View this index**: Open `docs/INDEX.md`

**Find email documentation**: See "Email System" section above

**Browse core documents**: Start with files in root `docs/` directory

**View system design**: Check `D00–D15` documents

**Find API specs**: Check `reference/openapi/`

---

## 📞 Documentation Standards

All documentation in ICTServe follows:

- **Language**: Primary Bahasa Melayu, Secondary English
- **Format**: Markdown (.md files)
- **Standards**:
  - WCAG 2.2 Level AA for accessibility
  - PSR-12 for code examples
  - ISO/IEC/IEEE standards (referenced in D documents)
- **Traceability**: References to D00–D15 where applicable


---

## 📅 Version History

| Version | Date | Changes |
|---------|------|---------|
| 2.0.0 | 2025-11-06 | Major reorganization: files moved from reference/ to purpose-based directories (guides/, technical/, testing/, archive/). Updated all file paths and navigation. |
| 1.0.0 | 2025-11-01 | Initial documentation organization |

---

**Last Updated**: 2025-11-06
**Status**: ✅ Active and maintained
**Maintainer**: ICTServe Development Team
