# Requirements Document

## Introduction

This specification addresses the critical compliance gaps identified across the **entire KRISA ICTServe documentation suite (D01-D17)** for MOTAC's intranet-only deployment. Based on comprehensive compliance analysis against MOTAC's Cyber Security Policy (PKS) and Strategic Digitalization Plan (PSPM), multiple documents contain **NON-COMPLIANT** elements that violate government security and digitalization requirements.

**Critical Compliance Violations Identified:**

- **PKS 5.2.1 Violation**: "Guest" access without authentication violates the Accountability (Non-repudiation) principle across all user-facing documents
- **PKS 9.2.1 & 4.2 Risk**: AWS Bedrock cloud connection creates data sovereignty risks for intranet systems (affects D02, D04, D18)
- **KRISA Standard Gaps**: Missing standard notation tables, CRUD indicators, and MAMPU-compliant formatting across all documents
- **PKS 5.4.3 Gap**: Password policy not explicitly documented per cyber security requirements
- **PSPM Non-alignment**: Documents don't reflect MyGovCloud prioritization and strategic digitalization objectives

**Expanded Scope Impact**: This update covers the complete KRISA documentation suite:

- **D01**: System Development Plan - Architecture compliance
- **D02**: Business Requirements Specification - Core compliance violations  
- **D03-D04**: System Requirements & Design - Technical compliance
- **D05-D08**: Migration & Integration Plans - Data sovereignty compliance
- **D09-D10**: Database & Source Code Documentation - Security compliance
- **D15**: Migration Report - Implementation compliance
- **D17**: User Manuals - Access control compliance

**Intranet Context Impact**: Since ICTServe operates on MOTAC's internal network, all documents must reflect heightened security posture requirements with complete traceability and data sovereignty compliance.

## Glossary

- **PKS**: Polisi Keselamatan Siber (Cyber Security Policy) MOTAC
- **PSPM**: Pelan Strategik Pendigitalan MOTAC (Strategic Digitalization Plan)
- **KRISA**: Kejuruteraan Sistem Aplikasi Sektor Awam (Public Sector Application System Engineering)
- **BRS**: Business Requirements Specification (Spesifikasi Keperluan Bisnes)
- **True_Hybrid**: Current architecture allowing guest access without authentication
- **SSO_Authentication**: Single Sign-On authentication via LDAP/Active Directory
- **Data_Sovereignty**: Requirement to keep sensitive data within Malaysian jurisdiction
- **Accountability_Principle**: PKS requirement that all actions must be traceable to specific staff members
- **CRUD_Indicators**: Create, Read, Update, Delete indicators required in KRISA data tables

## Requirements

### Requirement 1: Eliminate Guest Access Non-Compliance Across All KRISA Documents (PKS 5.2.1)

**User Story:** As a MOTAC security officer, I want to eliminate anonymous "guest" access references from all KRISA documentation (D01-D17), so that we comply with PKS 5.2.1 accountability requirements and ensure all system designs mandate traceability to specific staff members.

#### Acceptance Criteria

1. WHEN reviewing D02 (BRS), D03 (SRS), and D04 (Design), THE Documents SHALL replace "Guest Mode" with "Walk-in/Kiosk Mode using SSO authentication"
2. WHEN documenting user access in D01 (Development Plan) and D17 (User Manual), THE Documents SHALL specify mandatory LDAP/Active Directory integration for all users
3. WHEN describing system architecture in D04 (Design Document), THE System SHALL link all activities to authenticated staff ID per PKS 5.2.1 "Usage of accounts belonging to others is prohibited"
4. WHEN updating D09 (Database Documentation), THE Schema SHALL show user_id as mandatory foreign key (no NULL values for anonymous users)
5. WHEN revising D15 (Migration Report), THE Document SHALL include migration strategy to link historical guest submissions to authenticated accounts

### Requirement 2: Cloud AI Data Sovereignty Compliance Across Technical Documents (PKS 9.2.1 & 4.2)

**User Story:** As a MOTAC data protection officer, I want to ensure AWS Bedrock cloud AI integration complies with data sovereignty requirements across all technical documentation (D02, D03, D04, D08, D18), so that we don't violate PKS data transfer procedures and PSPM MyGovCloud prioritization.

#### Acceptance Criteria

1. WHEN documenting AI services in D02 (BRS) and D03 (SRS), THE Documents SHALL prioritize local Ollama processing for all sensitive data per PSPM MyGovCloud preference
2. WHEN specifying technical architecture in D04 (Design Document), THE System SHALL implement strict Data Loss Prevention (DLP) filters to mask Official Secrets before cloud processing
3. WHEN describing integration plans in D08 (Integration Specification), THE Documents SHALL document secure API gateway configuration that maintains intranet air-gap policies
4. WHEN updating D18 (AI Documentation), THE Document SHALL classify data sensitivity and route accordingly (Local: Sensitive, Cloud: Public only)
5. WHEN documenting system requirements across D01-D17, THE Documents SHALL explicitly address PKS 9.2.1 "Data transfer procedures must protect confidentiality"

### Requirement 3: KRISA Standard Structure Compliance Across All Documents (MAMPU Requirements)

**User Story:** As a MAMPU auditor, I want all KRISA documents (D01-D17) to follow exact KRISA formatting standards and templates, so that they meet government documentation requirements and pass compliance reviews.

#### Acceptance Criteria

1. WHEN reviewing D02 (BRS) section 3.1.2, THE Document SHALL include standard KRISA notation tables for Business Functions following the template format in "Pemodelan Fungsi Bisnes [F1.3]"
2. WHEN reviewing D03 (SRS) section 2.1, THE Document SHALL include standard KRISA notation tables for System Functions following "Pemodelan Fungsi Sistem [F2.2]" format
3. WHEN reviewing D09 (Database Documentation), THE Document SHALL include CRUD indicators (C), (R), (U), (D) next to each data field per KRISA template format
4. WHEN reviewing D02 (BRS) section 3.2.2 and D03 (SRS) section 5.3, THE Documents SHALL replace "Syarat Pasca" with standard "Aktiviti Sebelum" and "Aktiviti Selepas" table fields per KRISA template
5. WHEN reviewing D03 (SRS) section 7, THE Document SHALL include detailed Function Point Analysis with Value Adjustment Factor (VAF) calculation per KRISA standard format

### Requirement 4: Self-Registration Compliance Enhancement Across User Documentation (PKS 5.2.1)

**User Story:** As a MOTAC HR integration officer, I want self-registration processes documented across D01 (Development Plan), D02 (BRS), D17 (User Manual) to integrate with HRMIS/Active Directory, so that only active staff can access the system and account provisioning is automated.

#### Acceptance Criteria

1. WHEN documenting system requirements in D02 (BRS) and D03 (SRS), THE Documents SHALL specify auto-provisioning accounts by syncing with HR System (HRMIS) upon first SSO login
2. WHEN describing user management in D04 (Design Document), THE System SHALL verify active employment status against HRMIS before granting access
3. WHEN updating D17 (User Manual), THE Document SHALL replace manual "@motac.gov.my registration" with "HRMIS-integrated auto-provisioning" procedures
4. WHEN documenting lifecycle management in D01 (Development Plan), THE System SHALL automatically deactivate accounts based on HRMIS status updates when staff leave MOTAC
5. WHEN specifying security requirements across D02-D04, THE Documents SHALL explicitly document PKS 5.4.3 requirements (8 chars, 90-day expiry, 3 attempts)

### Requirement 5: Architecture Redesign for Intranet Context Across Technical Documents

**User Story:** As a BPM system architect, I want to redesign the "True Hybrid" architecture documented across D01 (Development Plan), D04 (Design Document), and D08 (Integration Specification) to comply with intranet security requirements, so that we maintain quick access while ensuring accountability.

#### Acceptance Criteria

1. WHEN updating D04 (Design Document), THE System SHALL replace "Guest Mode" with "Walk-in/Kiosk Mode" using SSO authentication in all architectural diagrams
2. WHEN documenting user flows in D02 (BRS) and D03 (SRS), THE System SHALL auto-populate forms from LDAP/HR system data while maintaining simplified interface
3. WHEN specifying database design in D09 (Database Documentation), THE Schema SHALL use nullable user_id foreign keys linked to authenticated staff accounts only (no NULL for anonymous)
4. WHEN describing AI integration in D04 (Design) and D08 (Integration), THE System SHALL implement data masking/filtering before any cloud API calls
5. WHEN updating D01 (Development Plan) and all technical documents, THE Documents SHALL explicitly state "Intranet-only deployment with mandatory authentication"

### Requirement 6: Data Residency and Hosting Compliance Across All Documents

**User Story:** As a MOTAC infrastructure officer, I want to ensure the system hosting and data residency requirements are documented consistently across all KRISA documents (D01-D17), so that we comply with government data residency requirements.

#### Acceptance Criteria

1. WHEN updating D01 (Development Plan) and D04 (Design Document), THE Documents SHALL specify "Sistem ini akan dihoskan sepenuhnya di Pusat Data MOTAC (Intranet)"
2. WHEN documenting AI services in D02 (BRS), D04 (Design), and D18 (AI Documentation), THE Documents SHALL state "Penggunaan AI (AWS Bedrock) akan melalui Secure API Gateway dengan penapisan data sensitif (Data Masking) sebelum dihantar ke awan"
3. WHEN specifying data classification in D03 (SRS) and D09 (Database Documentation), THE Documents SHALL ensure only non-sensitive, public data can be sent to AWS Bedrock
4. WHEN updating D01 (Development Plan), THE Document SHALL reference PSPM prioritization of MyGovCloud over public cloud services
5. WHEN documenting audit requirements in D05-D08 (Migration & Integration Plans), THE Documents SHALL track all data sent to external cloud services for compliance monitoring

### Requirement 7: Enhanced Security Documentation Across All KRISA Documents (PKS Compliance)

**User Story:** As a security auditor, I want comprehensive security measures documented consistently across all KRISA documents (D01-D17), so that PKS compliance verification is straightforward and complete.

#### Acceptance Criteria

1. WHEN updating D02 (BRS), D03 (SRS), and D04 (Design), THE Documents SHALL specify integration with MOTAC Active Directory/LDAP per PKS requirements
2. WHEN documenting access control in D04 (Design) and D17 (User Manual), THE Documents SHALL detail role-based permissions aligned with PKS principles (Need-to-Know, Minimum Privilege)
3. WHEN specifying audit requirements in D01 (Development Plan) and D09 (Database Documentation), THE Documents SHALL specify dual audit system with 7-year retention per PKS compliance standards
4. WHEN updating D05-D08 (Migration & Integration Plans), THE Documents SHALL include explicit PDPA 2010 compliance measures and data classification procedures
5. WHEN describing network security across D01, D04, and D08, THE Documents SHALL specify intranet-only deployment with documented exceptions for secure cloud API access

### Requirement 8: Process Flow Corrections for SSO Authentication Across Process Documents

**User Story:** As a business process analyst, I want accurate process flows documented across D02 (BRS), D03 (SRS), and D17 (User Manual) that reflect the new SSO-based authentication model, so that implementation teams have correct guidance.

#### Acceptance Criteria

1. WHEN updating D02 (BRS) helpdesk process flows, THE Process SHALL show SSO authentication as mandatory first step (remove guest path)
2. WHEN documenting D03 (SRS) asset loan workflow, THE Process SHALL integrate with HR system for automatic user verification and approval routing
3. WHEN describing D04 (Design) AI interaction flow, THE Process SHALL include data classification and routing decisions (Local vs Cloud)
4. WHEN updating D17 (User Manual) approval workflows, THE Process SHALL maintain email-based approvals but link to authenticated accounts only
5. WHEN documenting error handling across D02-D04, THE Process SHALL include fallback procedures for SSO/LDAP unavailability

### Requirement 9: Compliance Risk Assessment and Mitigation Across All Documents

**User Story:** As a MOTAC risk management officer, I want a comprehensive compliance risk assessment documented across all KRISA documents (D01-D17), so that all identified violations are properly addressed and mitigation strategies are clearly defined.

#### Acceptance Criteria

1. WHEN updating D01 (Development Plan), THE Document SHALL include a compliance risk matrix showing current violations across all KRISA documents and their severity levels
2. WHEN documenting D02 (BRS) and D03 (SRS) guest access risks, THE Documents SHALL explicitly state "Risk: Guest actions on Intranet cannot be traced to specific staff member, violating Accountability principle"
3. WHEN updating D04 (Design) and D08 (Integration) cloud AI risks, THE Documents SHALL state "Risk: Connecting Intranet system to Public Cloud API creates bridge that may bypass air-gap/firewall policies"
4. WHEN providing mitigation strategies in D01-D04, THE Documents SHALL specify "Fix: Replace Guest with Single Sign-On (SSO). Use LDAP/Active Directory to auto-authenticate staff"
5. WHEN addressing data sovereignty in D04 (Design) and D18 (AI Documentation), THE Documents SHALL recommend "Ideally replace Bedrock with local high-performance LLMs hosted on MyGovCloud or on-premise GPU servers"

### Requirement 10: Documentation Updates and Version Control Following KRISA Standards

**User Story:** As a document controller, I want proper version control and change tracking for all updated KRISA documents (D01-D17) following KRISA template standards, so that all stakeholders are aware of compliance-driven changes and governance requirements are met.

#### Acceptance Criteria

1. WHEN updating all KRISA documents, THE Documents SHALL increment versions following KRISA template guidelines (major changes: D01: v2.0, D02: v4.0, D03: v3.0, D04: v3.0, minor changes: increment decimal only)
2. WHEN documenting changes in section iii "Kawalan Dokumen" across D01-D17, THE Documents SHALL include detailed change logs explaining specific PKS/PSPM compliance updates per KRISA template format
3. WHEN referencing policies in section viii "Sumber Rujukan" of any KRISA document, THE Documents SHALL cite specific PKS sections (5.2.1, 9.2.1, 4.2, 5.4.3) and PSPM strategic objectives with page references
4. WHEN updating technical specifications in D03-D10, THE Documents SHALL maintain traceability to complete D01-D17 documentation series following KRISA cross-reference standards
5. WHEN finalizing updates in section ii "Semakan dan Pengesahan Dokumen", THE Documents SHALL require approval from CDO and BPM Director per MOTAC governance requirements and include compliance officer sign-off per KRISA template

### Requirement 11: KRISA Template Structure Compliance Across All Documents

**User Story:** As a KRISA compliance officer, I want all KRISA documents (D01-D17) to strictly follow the official KRISA template structure and formatting, so that they meet MAMPU documentation standards and government audit requirements.

#### Acceptance Criteria

1. WHEN updating any KRISA document, THE Document SHALL follow the exact section structure from official KRISA templates (i. Keterangan Dokumen, ii. Semakan dan Pengesahan, iii. Kawalan Dokumen, iv. Kandungan, v. Senarai Gambarajah, vi. Senarai Jadual, vii. Definisi dan Akronim, viii. Sumber Rujukan)
2. WHEN documenting D02 (BRS) business functions, THE Document SHALL use KRISA notation format "BF-ICT-XX-YY" and reference "Pemodelan Fungsi Bisnes [F1.3]" methodology
3. WHEN documenting D03 (SRS) system functions, THE Document SHALL use KRISA notation format and reference "Pemodelan Fungsi Sistem [F2.2]", "Pemodelan Use Case [F2.1]", and "Pemodelan Keperluan Data [F2.3]" methodologies
4. WHEN creating D09 (Database Documentation), THE Document SHALL follow template structure with sections for "Ringkasan Maklumat Pangkalan Data Fizikal" and "Skrip Pangkalan Data" per official template
5. WHEN preparing any document for approval, THE Document SHALL include proper KRISA template headers with NAMA AGENSI (BPM), NAMA AGENSI INDUK (MOTAC), TARIKH DOKUMEN, and VERSI DOKUMEN fields
