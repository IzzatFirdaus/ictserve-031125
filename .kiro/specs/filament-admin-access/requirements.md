# Filament Admin Access Requirements

## Overview

Filament 4 admin panel for ICTServe with four-role RBAC, cross-module integration, and WCAG 2.2 AA compliance.

**Traceability**: D03-FR-004 (Admin Panel), D04 §3-7 (Architecture)

## Glossary

- **Filament Panel**: Admin interface at `/admin` (Filament 4)
- **RBAC**: 4-role access (Staff, Approver, Admin, Superuser)
- **Resource**: Filament CRUD for Eloquent models
- **Widget**: Dashboard statistics component
- **SLA**: 60s email delivery, 5s ticket creation
- **Audit Trail**: 7-year retention (PDPA 2010)
- **Cross-Module**: Auto-link tickets ↔ loans

## Requirements

### Requirement 1: Panel Authentication ✅

**User Story**: As an ICT admin, I need secure panel access.

#### Acceptance Criteria

1. `/admin` SHALL display Filament login
2. Valid credentials SHALL redirect to dashboard
3. Access restricted to `admin`, `superuser` roles
4. Unauthorized access returns 403
5. CSRF protection on all forms

**Status**: Implemented (Phase 1.1, 1.3)  
**Traceability**: D03-FR-004.1, D03-FR-004.2, D04 §3.1

### Requirement 2: Four-Role RBAC ✅

**User Story**: As a system admin, I need role-based access.

#### Acceptance Criteria

1. Roles: `staff`, `approver`, `admin`, `superuser`
2. `staff`: read-only own submissions
3. `approver` (Grade 41+): loan approval interface
4. `admin`: full CRUD helpdesk + loans
5. `superuser`: all features + user mgmt + config

**Status**: Implemented (Phase 1.2) - 27 permissions, policy-based  
**Traceability**: D03-FR-004.3, D03-FR-004.4, D04 §3.2

### Requirement 3: Helpdesk Ticket Resource ✅

**User Story**: As an ICT admin, I need centralized ticket management.

#### Acceptance Criteria

1. `HelpdeskTicketResource` displays all tickets
2. Columns: ticket_number, title, priority, status, created_at
3. Filters: priority, status, category, date_range, division (deferred by default)
4. Edit form: guest info, attachments, comments, history
5. Actions: `Filament\Actions\Action` namespace (Filament 4)

**Status**: Implemented (Phase 2) - SLA tracking, state machine  
**Traceability**: D03-FR-001.1, D03-FR-001.2, D03-FR-001.3, D04 §4.1

### Requirement 4: Asset Loan Resource ✅

**User Story**: As an ICT admin, I need loan management.

#### Acceptance Criteria

1. `LoanApplicationResource` displays all applications
2. Columns: application_number, applicant_name, assets, status, dates
3. Filters: status, approval_status, asset_category, date_range (deferred by default)
4. Edit form: applicant details, loan_items, approval_history
5. Actions: `Filament\Actions\Action` namespace (Filament 4)

**Status**: Implemented (Phase 3) - Condition tracking, auto-tickets  
**Traceability**: D03-FR-002.1, D03-FR-002.2, D03-FR-002.3, D04 §4.2

### Requirement 5: Asset Inventory Resource ✅

**User Story**: As an ICT admin, I need inventory management.

#### Acceptance Criteria

1. `AssetResource` displays all assets
2. Columns: asset_tag, name, category, status, availability
3. Filters: category, status, availability, location (deferred by default)
4. Edit form: specifications (Repeater component), maintenance_history, current_loan
5. Actions: `Filament\Actions\Action` namespace (Filament 4)

**Status**: Implemented (Phase 4) - Utilization analytics  
**Traceability**: D03-FR-002.4, D03-FR-002.5, D04 §4.3

### Requirement 6: User Management (Superuser Only) ✅

**User Story**: As a superuser, I need user management.

#### Acceptance Criteria

1. `UserResource` accessible only to `superuser` via `shouldRegisterNavigation()`
2. Columns: name, email, role, division, status
3. Filters: role, division, grade, account_status (deferred by default)
4. Edit form: role, division, grade, status changes
5. Prevent removing last superuser via policy

**Status**: Implemented (Phase 5) - Welcome emails, activity tracking  
**Traceability**: D03-FR-004.5, D03-FR-004.6, D04 §3.3

### Requirement 7: Unified Dashboard ✅

**User Story**: As an ICT admin, I need a dashboard.

#### Acceptance Criteria

1. Display stats: helpdesk + asset loan modules
2. Widgets: ticket_stats, loan_stats, sla_compliance, alerts
3. Auto-refresh: 30s intervals
4. Widget clicks navigate to filtered views
5. Role-based widget visibility

**Status**: Implemented (Phase 6) - 6 widgets, real-time updates  
**Traceability**: D03-FR-005.1, D03-FR-005.2, D04 §5.1

### Requirement 8: Cross-Module Integration ✅

**User Story**: As an ICT admin, I need auto-integration.

#### Acceptance Criteria

1. Damaged asset return auto-creates ticket (5s SLA)
2. Link via `cross_module_integrations` table
3. Loan view displays related ticket link
4. Ticket view displays related loan link
5. All actions logged in audit trail

**Status**: Implemented (Phase 7) - Unified search, referential integrity  
**Traceability**: D03-FR-003.1, D03-FR-003.2, D04 §6.1

### Requirement 9: Reporting and Export ✅

**User Story**: As an ICT admin, I need reports and exports.

#### Acceptance Criteria

1. Export formats: CSV, Excel, PDF
2. Exports respect active filters
3. Pre-built: monthly_tickets, loan_utilization, sla_compliance
4. Charts for key metrics
5. Automated scheduling + email delivery

**Status**: Implemented (Phase 8) - 5 templates, visualization tools  
**Traceability**: D03-FR-006.1, D03-FR-006.2, D04 §7.1

### Requirement 10: Audit Trail and Security ✅

**User Story**: As a superuser, I need audit logging.

#### Acceptance Criteria

1. Log all CUD via Laravel Auditing
2. `AuditResource`: user, action, model, old/new values, timestamp
3. Filters: user, model_type, action, date_range
4. Security events email superusers (60s SLA)
5. 7-year retention (PDPA 2010)

**Status**: Implemented (Phase 9) - Security monitoring, incident alerts  
**Traceability**: D03-FR-007.1, D03-FR-007.2, D03-FR-007.3, D09 §9, D11 §8

### Requirement 11: Notification Management

**User Story**: As an ICT admin, I need notification management to monitor communication delivery.

#### Acceptance Criteria

1. `NotificationResource` SHALL display: recipient, type, status, delivery_timestamp
2. Filters: notification_type, status, date_range (deferred by default)
3. Failed notifications SHALL show error details with retry action (`Filament\Actions\Action`)
4. System SHALL track 60-second SLA compliance
5. Email queue status SHALL display in dashboard widget

**Traceability**: D03-FR-008.1, D03-FR-008.2, D04 §8.1

### Requirement 12: Advanced Search and Filtering

**User Story**: As an ICT admin, I need global search to quickly locate records.

#### Acceptance Criteria

1. Global search SHALL query: tickets, loans, assets, users
2. Results SHALL group by resource type with relevance ranking
3. Each resource SHALL support multi-criteria filters (deferred by default)
4. Filter state SHALL persist in URL for bookmarking
5. User filter preferences SHALL save per resource in `filament_saved_filters` table

**Traceability**: D03-FR-009.1, D03-FR-009.2, D04 §9.1

### Requirement 13: System Configuration (Superuser Only)

**User Story**: As a superuser, I need system configuration to customize behavior.

#### Acceptance Criteria

1. Settings page SHALL be superuser-only access
2. Configurable: email_settings, notification_preferences, sla_thresholds
3. All changes SHALL validate before save
4. Configuration changes SHALL log in `filament_audit_logs`
5. Superuser SHALL configure: maintenance_mode, system_announcements

**Traceability**: D03-FR-010.1, D03-FR-010.2, D04 §10.1

### Requirement 14: Performance Monitoring (Superuser Only)

**User Story**: As a superuser, I need performance monitoring to identify bottlenecks.

#### Acceptance Criteria

1. Performance dashboard SHALL be superuser-only access
2. Metrics: Core Web Vitals (LCP <2.5s, FID <100ms, CLS <0.1)
3. Database stats: slow queries, N+1 detection, query count
4. Threshold breaches SHALL alert superusers within 60 seconds
5. Historical data SHALL display with trend analysis charts

**Traceability**: D03-FR-011.1, D03-FR-011.2, D04 §11.1

### Requirement 15: WCAG 2.2 AA Compliance

**User Story**: As an ICT admin with accessibility needs, I need full accessibility support.

#### Acceptance Criteria

1. Color contrast: 4.5:1 text, 3:1 UI components
2. Keyboard navigation SHALL support all interactive elements
3. ARIA attributes SHALL include: labels, roles, landmarks
4. Focus indicators: 3:1 contrast minimum, visible on all elements
5. Lighthouse accessibility score SHALL be 100

**Traceability**: D03-FR-012.1, D03-FR-012.2, D12 §4, D14 §3

### Requirement 16: Bilingual Support

**User Story**: As an ICT admin, I need bilingual interface for language preference.

#### Acceptance Criteria

1. Language switcher SHALL display in navigation bar
2. Language preference SHALL persist in session + cookie
3. All UI elements SHALL translate: labels, messages, validation errors
4. Supported languages: Bahasa Melayu (primary), English
5. Language preference SHALL persist across navigation

**Traceability**: D03-FR-013.1, D03-FR-013.2, D15 §2

### Requirement 17: Email Notification Management

**User Story**: As an ICT admin, I need email template management for communication control.

#### Acceptance Criteria

1. `EmailTemplateResource` SHALL manage: subject, body, variables
2. Template editor SHALL provide preview functionality
3. Templates SHALL validate for WCAG 2.2 AA compliance
4. Failed emails SHALL log error with manual retry action (`Filament\Actions\Action`)
5. System SHALL track: delivery_rate, sla_compliance (60s target)

**Traceability**: D03-FR-014.1, D03-FR-014.2, D04 §12.1

### Requirement 18: Testing and Quality Assurance

**User Story**: As a developer, I need comprehensive testing for reliability.

#### Acceptance Criteria

1. Unit tests SHALL cover: services, policies, observers (80% minimum)
2. Feature tests SHALL cover: CRUD operations, authorization, workflows
3. Livewire tests SHALL cover: components, widgets, actions
4. Accessibility tests SHALL verify: WCAG 2.2 AA, Lighthouse score 100
5. Performance tests SHALL verify: Core Web Vitals targets

**Traceability**: D03-FR-015.1, D03-FR-015.2, D04 §13.1

---

## Non-Functional Requirements

### Performance

- Dashboard load: <2s
- Table pagination: 25 items/page
- Widget refresh: 30s intervals
- Export generation: <10s for 1000 records
- Cache duration: 300s (5 minutes)

### Accessibility

- WCAG 2.2 AA compliance
- Keyboard navigation support
- Screen reader compatibility
- Focus indicators: 2px outline, 3:1 contrast
- Touch targets: 44×44px minimum

### Security

- CSRF protection on all forms
- Rate limiting: 60 requests/minute/user
- Session timeout: 30 minutes
- Password: 8+ chars, mixed case, numbers, symbols
- Audit retention: 7 years (PDPA 2010)

### Localization

- Bilingual: Bahasa Melayu (primary), English
- Date format: d/m/Y
- Time format: 24-hour (H:i)
- Currency: MYR

---

## Technical Constraints

- **Framework**: Filament 4.1+
- **Laravel**: 12.x
- **PHP**: 8.2+
- **Database**: MySQL 8.0+ / MariaDB 10.6+
- **Packages**: `owen-it/laravel-auditing` ^14.0, `spatie/laravel-permission` ^6.23
- **Colors**: MOTAC Blue (#0056b3), Success (#198754), Warning (#ff8c00), Danger (#b50c0c)

---

## Compliance

- **D03-FR-004**: Admin Panel Requirements
- **D04 §3-8**: Architecture and Design
- **D09**: Database Schema (30+ tables)
- **D10**: PSR-12 Code Standards
- **D12**: UI/UX Design Guide
- **D14**: WCAG 2.2 AA Accessibility
- **D15**: Bilingual Localization
