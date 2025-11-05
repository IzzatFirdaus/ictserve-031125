# ICTServe Documentation Index

**Last Updated**: 2025-11-01  
**Version**: 1.0.0

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

### 🎯 `guides/` - System Guides & Setup

Practical guides for understanding and setting up system components:

- **asset-loan-system-flow.md** - Asset borrowing workflow and process flow
- **helpdesk-system-flow.md** - Helpdesk ticketing system workflow
- **broadcasting-setup.md** - Laravel Reverb broadcast configuration

**When to Use**: Understanding how system features work end-to-end

### 🔧 `features/` - Feature Implementation Details

Detailed implementation guides for specific features:

- **admin-seeding.md** - Database seeding and sample data creation
- **component-metadata-standards.md** - Component metadata and standards
- **component-upgrade-guide.md** - Upgrading component systems
- **helpdesk_form_to_model.md** - Helpdesk form and data model mapping
- **loan_form_to_model.md** - Loan application form and data model mapping

**When to Use**: Implementing specific features or understanding form-to-model relationships

### 🛠️ `technical/` - Technical Implementation

Production implementation guides and technical specifications:

- **EMAIL_NOTIFICATION_SYSTEM.md** - Email notification architecture and API
- **EMAIL_NOTIFICATION_QUICK_START.md** - Quick start guide for email system
- **email-notification-system.md** - Email system implementation details
- **TASKS_10.1_10.2_CHECKLIST.md** - Email notification and dual approval completion checklist
- **PHPSTAN_ANALYSIS_NOTES.md** - Static analysis findings and explanations
- **devtools-mcp-getting-started.md** - Model Context Protocol setup
- **implementation/** - Implementation-specific documentation
  - dual-approval-system-implementation.md
  - email-notification-system-implementation.md
  - HYBRID_FORMS_IMPLEMENTATION.md
- **ollama-laravel/** - AI/LLM integration documentation

**When to Use**: Implementing features, configuring systems, troubleshooting

### 📖 `reference/` - Reference Documentation

Reference materials, audits, and specifications:

- **helpdesk/** - Helpdesk system reference documentation
- **openapi/** - OpenAPI/Swagger specifications
- **rtm/** - Requirements Traceability Matrix
- **testing/** - Testing frameworks and procedures
- **versions/** - Version history and changelog
- **reports/** - Audit and compliance reports
  - email-compliance-report.md
  - email-error-pages-audit-report.md
  - error-pages-compliance-report.md
  - frontend-compliance-audit-report.md
  - layout-components-audit-report.md
  - performance-optimization-report.md
  - shared-components-audit-report.md
- **frontend/** - Frontend development standards
  - Component library documentation
  - Accessibility guidelines
  - Performance optimization guides
  - Responsive design patterns
  - Bilingual support implementation

**When to Use**: Looking up specifications, API definitions, audit findings, compliance reports

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
| Core (Root) | 5 | System overview and entry points |
| Design (D00–D15) | 16 | Canonical specifications and standards |
| Guides | 3 | System workflows and setup procedures |
| Features | 5 | Feature-specific implementation details |
| Technical | 7+ | Technical implementation and configuration |
| Reference | 50+ | Specifications, APIs, audits, reports |
| **Total** | **80+** | Complete system documentation |

---

## 🔍 Finding What You Need

### By Topic

**Email System**:

- technical/EMAIL_NOTIFICATION_SYSTEM.md (architecture)
- technical/EMAIL_NOTIFICATION_QUICK_START.md (quick start)
- technical/TASKS_10.1_10.2_CHECKLIST.md (completion status)

**Helpdesk System**:

- guides/helpdesk-system-flow.md (workflow)
- reference/helpdesk/ (reference docs)
- features/helpdesk_form_to_model.md (data model)

**Loan System**:

- guides/asset-loan-system-flow.md (workflow)
- technical/dual-approval-system-implementation.md (approval system)
- features/loan_form_to_model.md (data model)

**Frontend/UI**:

- D12_UI_UX_DESIGN_GUIDE.md (design)
- D13_UI_UX_FRONTEND_FRAMEWORK.md (frameworks)
- D14_UI_UX_STYLE_GUIDE.md (styling)
- reference/frontend/ (components and guides)

**Database**:

- D09_DATABASE_DOCUMENTATION.md (schema)
- reference/rtm/ (requirements mapping)

**Deployment & Infrastructure**:

- D11_TECHNICAL_DESIGN_DOCUMENTATION.md (systems)
- guides/broadcasting-setup.md (specific setup)
- reference/testing/ (test procedures)

**Compliance & Standards**:

- D15_LANGUAGE_MS_EN.md (localization)
- reference/reports/ (audit reports)
- reference/frontend/accessibility-guidelines.md (WCAG)

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
| 1.0.0 | 2025-11-01 | Initial documentation organization |

---

**Last Updated**: 2025-11-01  
**Status**: ✅ Active and maintained  
**Maintainer**: ICTServe Development Team
