# ICTServe v4.0 - PKS Compliance Migration Status

## Project Status: ⚠️ PKS COMPLIANCE MIGRATION REQUIRED

**Status Date**: 24 Disember 2025  
**Target Version**: ICTServe v4.0 (PKS Compliant)  
**Classification**: Migration Required  
**Compliance**: KRISA D00-D18 v4.0 Standards - PKS 5.2.1, 9.2.1, 4.2, 5.4.3

## Executive Summary

The ICTServe system requires migration from v3.6.1 to v4.0 to comply with KRISA v4.0 PKS (Polisi Keselamatan Siber) requirements. The major changes involve eliminating Guest Mode, implementing mandatory LDAP/Active Directory SSO, HRMIS auto-provisioning, and DLP filtering for cloud AI services.

## PKS Compliance Requirements

### PKS 5.2.1 - Accountability (CRITICAL)

**Requirement**: "Semua aktiviti sistem mesti boleh dikesan kepada individu yang bertanggungjawab"

**Migration Tasks**:

- [ ] Remove guest_name, guest_email, guest_phone columns from helpdesk_tickets
- [ ] Remove applicant_name, applicant_email, applicant_phone columns from loan_applications
- [ ] Update user_id columns to NOT NULL with foreign key constraints
- [ ] Remove isGuestSubmission() methods from all models
- [ ] Remove guest form components and routes
- [ ] Update WebSocket channels to authenticated-only (no UUID-based guest channels)

### PKS 9.2.1 - Data Transfer (CRITICAL)

**Requirement**: Data transfer procedures must protect confidentiality - DLP required for cloud AI

**Migration Tasks**:

- [ ] Implement DLPFilteringService for cloud AI data classification
- [ ] Create data classification engine (SENSITIVE vs PUBLIC)
- [ ] Block sensitive data from AWS Bedrock transmission
- [ ] Route sensitive queries to local Ollama only
- [ ] Add DLP audit logging for all classification decisions

### PKS 4.2 - Data Sovereignty (CRITICAL)

**Requirement**: Sensitive government data must be processed within Malaysian jurisdiction

**Migration Tasks**:

- [ ] Configure intranet-only deployment
- [ ] Configure Ollama as primary processor for all sensitive data
- [ ] Implement data residency logging for AI operations
- [ ] Implement MyGovCloud prioritization for cloud services

### PKS 5.4.3 - Password Policy (REQUIRED)

**Requirement**: 8 chars minimum, 90-day expiry, 3 attempts lockout

**Migration Tasks**:

- [ ] Implement LdapRecord-Laravel for LDAP/Active Directory SSO
- [ ] Configure password policy enforcement via Active Directory
- [ ] Implement 3 failed attempts lockout with 30-minute unlock
- [ ] Log all authentication attempts for security monitoring

## Phase Migration Status

| Phase | v3.6.1 Status | v4.0 Status | Migration Required |
|-------|---------------|-------------|-------------------|
| **Phase 0: PKS Migration** | N/A | ⬜ NOT STARTED | NEW PHASE |
| **Phase 1: Foundation** | ✅ Complete | ⬜ RESET | LDAP SSO, HRMIS |
| **Phase 2: Core Modules** | ✅ Complete | ⬜ RESET | Remove guest forms |
| **Phase 3: Integration** | ✅ Complete | ⬜ RESET | Auth channels only |
| **Phase 4: Frontend** | ✅ Complete | ⬜ RESET | Walk-in/Kiosk SSO |
| **Phase 5: Monitoring** | ✅ Complete | ✅ Partial | Channel updates |
| **Phase 6: Testing** | ✅ Complete | ⬜ RESET | PKS compliance tests |
| **Phase 7: Documentation** | ✅ Complete | ⬜ RESET | D00-D18 v4.0 |
| **Phase 8: Cloud Hybrid AI** | ✅ Complete | ⬜ RESET | DLP filtering |
| **Phase 9: PKS Extended** | N/A | ⬜ NOT STARTED | CSIRT, BCP/DRP, Training, Change Mgmt, Third-Party |
| **Final Checkpoint** | ✅ Complete | ⬜ RESET | PKS validation |

## Requirements Migration Matrix

### Updated Requirements (v4.0)

| Req # | v3.6.1 Title | v4.0 Title | Status |
|-------|--------------|------------|--------|
| 1 | True Hybrid Access Architecture | PKS 5.2.1 Compliant SSO-Only Architecture | ⬜ RESET |
| 2 | Self-Registration and Account Management | HRMIS Auto-Provisioning and Account Management | ⬜ RESET |
| 3 | Dual Audit System and Compliance | Dual Audit System and PKS Compliance | ⬜ RESET |
| 5 | API Integration and External Authentication | API Integration and LDAP/Active Directory SSO | ⬜ RESET |
| 6 | Real-Time Communication and Notifications | Real-Time Communication (Authenticated Only) | ⬜ RESET |
| 8 | Enhanced Helpdesk Module | Enhanced Helpdesk Module with SSO | ⬜ RESET |
| 9 | Enhanced Asset Loan Module | Enhanced Asset Loan Module with SSO | ⬜ RESET |
| 24 | Laravel Reverb Real-Time Communication | Laravel Reverb (PKS 5.2.1 Compliant) | ⬜ RESET |

### New Requirements (v4.0)

| Req # | Title | PKS Reference | Status |
|-------|-------|---------------|--------|
| 25 | PKS 9.2.1 Data Transfer and DLP Compliance | PKS 9.2.1 | ⬜ NEW |
| 26 | PKS 4.2 Data Sovereignty Compliance | PKS 4.2 | ⬜ NEW |
| 27 | PKS 5.4.3 Password Policy Compliance | PKS 5.4.3 | ⬜ NEW |
| 28 | PKS CSIRT Integration and Incident Response | PKS Incident Mgmt | ⬜ NEW |
| 29 | PKS Business Continuity and Disaster Recovery | PKS BCP/DRP | ⬜ NEW |
| 30 | PKS Security Awareness and Training Compliance | PKS Training | ⬜ NEW |
| 31 | PKS Change Management Compliance | PKS Change Mgmt | ⬜ NEW |
| 32 | PKS Third-Party Security Management | PKS Third-Party | ⬜ NEW |
| 33 | PSPM Strategic Alignment - Digital Service Integration | PSPM 2022-2026 | ⬜ NEW |

## Technology Stack Changes (v3.6.1 → v4.0)

| Component | v3.6.1 | v4.0 | Change |
|-----------|--------|------|--------|
| Laravel | 12.42.0 | 12.43.1 | Upgrade |
| PHP | 8.2.12 | 8.4.1 | Upgrade |
| Livewire | 3.7.1 | 3.7.3 | Upgrade |
| Filament | 4.1.10 | 4.3.1 | Upgrade |
| Tailwind CSS | 4.1.17 | 4.1.18 | Upgrade |
| Laravel Socialite | 5.24.0 | REMOVED | PKS 5.2.1 |
| LdapRecord-Laravel | N/A | 3.x | NEW - PKS 5.2.1 |
| DLP Filtering Service | N/A | Custom | NEW - PKS 9.2.1 |

## Database Schema Changes

### Tables to Modify

**helpdesk_tickets**:

- REMOVE: guest_name, guest_email, guest_phone columns
- MODIFY: user_id from NULLABLE to NOT NULL with FK constraint

**loan_applications**:

- REMOVE: applicant_name, applicant_email, applicant_phone columns
- MODIFY: user_id from NULLABLE to NOT NULL with FK constraint
- ADD: hrmis_verified_at (datetime, nullable)

**users**:

- ADD: hrmis_synced_at (datetime, nullable)
- ADD: ldap_guid (string, nullable)
- ADD: is_active (boolean, default true)
- ADD: security_training_completed_at (datetime, nullable) - PKS Training
- ADD: training_expiry_date (date, nullable) - PKS Training
- ADD: is_third_party (boolean, default false) - PKS Third-Party
- ADD: contract_end_date (date, nullable) - PKS Third-Party
- ADD: nda_acknowledged_at (datetime, nullable) - PKS Third-Party
- REMOVE: google_id (Google SSO removed)

**bedrock_conversations**:

- MODIFY: user_id from NULLABLE to NOT NULL with FK constraint

## Key Services to Create/Modify

### New Services (v4.0)

- `DLPFilteringService` - Data Loss Prevention for cloud AI (PKS 9.2.1)
- `HRMISIntegrationService` - User auto-provisioning from HRMIS
- `LDAPAuthenticationService` - LDAP/Active Directory SSO (PKS 5.2.1)
- `DataSovereigntyService` - Data residency compliance (PKS 4.2)
- `SecurityIncidentService` - CSIRT integration and incident response (PKS Incident Mgmt)
- `BCPDRPService` - Business continuity and disaster recovery (PKS BCP/DRP)
- `SecurityTrainingService` - Training compliance tracking (PKS Training)
- `ChangeManagementService` - Change request workflows (PKS Change Mgmt)
- `ThirdPartyAccessService` - Vendor/contractor access control (PKS Third-Party)

### Services to Modify

- `ModelRouter` - Add DLP filtering before cloud routing
- `BedrockClient` - Block sensitive data transmission
- `OllamaClient` - Primary processor for sensitive data
- `DualApprovalService` - Add HRMIS verification

## Migration Checklist

### Pre-Migration

- [ ] Backup current database
- [ ] Document existing guest submissions for migration
- [ ] Prepare LDAP/Active Directory connection details
- [ ] Prepare HRMIS integration API credentials
- [ ] Review and update D00-D18 documentation

### Phase 0: PKS Migration

- [ ] 0.1 PKS 5.2.1 Accountability Migration
- [ ] 0.2 PKS 9.2.1 Data Transfer Compliance
- [ ] 0.3 PKS 4.2 Data Sovereignty Compliance
- [ ] 0.4 PKS 5.4.3 Password Policy Compliance

### Post-Migration

- [ ] Run full test suite with PKS compliance tests
- [ ] Validate all user_id FK constraints
- [ ] Verify DLP filtering for cloud AI
- [ ] Confirm LDAP/Active Directory SSO working
- [ ] Update all documentation to v4.0
- [ ] Obtain stakeholder sign-off for PKS compliance

## Risk Assessment

### High Risk Items

1. **Guest Data Migration**: Existing guest submissions need user_id assignment
2. **LDAP Integration**: Requires MOTAC Active Directory access
3. **HRMIS Integration**: Requires HRMIS API access and credentials
4. **DLP Implementation**: New service with complex classification rules

### Mitigation Strategies

1. Create migration script to link guest submissions to users by email
2. Coordinate with MOTAC IT for LDAP connection details
3. Coordinate with HR for HRMIS API access
4. Implement DLP with configurable rules and superuser override

## Timeline Estimate

| Phase | Duration | Dependencies |
|-------|----------|--------------|
| Phase 0: PKS Migration | 2-3 weeks | LDAP, HRMIS access |
| Phase 1-4: Core Updates | 2-3 weeks | Phase 0 complete |
| Phase 5-7: Testing & Docs | 1-2 weeks | Phase 1-4 complete |
| Phase 8: AI DLP | 1 week | Phase 0.2 complete |
| Final Validation | 1 week | All phases complete |
| **Total Estimate** | **7-10 weeks** | |

## Conclusion

The ICTServe system requires significant migration to comply with KRISA v4.0 PKS requirements. The primary changes involve eliminating Guest Mode (PKS 5.2.1), implementing DLP filtering for cloud AI (PKS 9.2.1), ensuring data sovereignty (PKS 4.2), and enforcing password policies via LDAP (PKS 5.4.3).

All previously completed tasks that involve guest access, authentication, or AI data handling have been reset and require re-implementation with PKS compliance.

**Project Status**: ⚠️ **PKS COMPLIANCE MIGRATION REQUIRED**

---

**Document Version**: 2.0  
**Last Updated**: 24 Disember 2025  
**Prepared By**: ICTServe Development Team  
**PKS Compliance Reference**: KRISA D00-D18 v4.0
