# Filament Admin Access - Implementation Tasks

## Overview

Filament 4 admin panel implementation for ICTServe with four-role RBAC, cross-module integration, and WCAG 2.2 AA compliance.

**Status**: Ready for Implementation
**Last Updated**: 2025-01-06
**Framework**: Filament 4.1+, Laravel 12.x, PHP 8.2+

## Progress Tracking

**Total Phases**: 18
**Completed**: 0/18 (0%)
**In Progress**: Phase 1
**Blocked**: None

---

## Phase 1: Panel Configuration & Authentication (0/3)

### 1.1 Configure Filament Panel

- [ ] Install Filament 4: `composer require filament/filament`
- [ ] Create AdminPanelProvider with WCAG colors (#0056b3, #198754, #ff8c00, #b50c0c)
- [ ] Configure navigation groups (5): Helpdesk, Asset, User, System, Reports
- [ ] Enable database notifications (30s polling), global search (Ctrl+K), SPA mode
- [ ] Add bilingual support (MS primary, EN secondary)

**Requirements**: 1, 16
**Files**: `app/Providers/Filament/AdminPanelProvider.php`

### 1.2 Implement RBAC

- [ ] Create RolePermissionSeeder (27 permissions, 4 roles)
- [ ] Add `shouldRegisterNavigation()` to all resources
- [ ] Create policies: HelpdeskTicketPolicy, LoanApplicationPolicy, AssetPolicy, UserPolicy
- [ ] Register policies in AppServiceProvider
- [ ] Test authorization (24 tests)

**Requirements**: 2, 6
**Files**: `database/seeders/RolePermissionSeeder.php`, `app/Policies/*.php`

### 1.3 Configure Security

- [ ] Create PasswordValidationServiceProvider (8+ chars, complexity)
- [ ] Verify SessionTimeoutMiddleware (30min)
- [ ] Verify AdminRateLimitMiddleware (60 req/min)
- [ ] Verify SecurityMonitoringMiddleware (SQL injection, XSS)
- [ ] Test security (18 tests)

**Requirements**: 1, 10
**Files**: `app/Providers/PasswordValidationServiceProvider.php`

---

## Phase 2: Helpdesk Ticket Resource (0/5)

### 2.1 Enhance Ticket Table

- [ ] Add filters: date_range, division (deferred by default)
- [ ] Configure `->deferFilters()` (Filament 4)
- [ ] Add `->persistFiltersInSession()`
- [ ] Verify SLA column, pagination (25/page)

**Requirements**: 3
**Files**: `app/Filament/Resources/Helpdesk/Tables/HelpdeskTicketsTable.php`

### 2.2 Create Assignment Action

- [ ] Create `AssignTicketAction` extends `Filament\Actions\Action`
- [ ] Add form: division, user, priority (live filtering)
- [ ] Implement SLA calculation (match expression: urgent=4h, high=24h, normal=72h, low=168h)
- [ ] Queue TicketAssignedMail (60s SLA)
- [ ] Create bilingual email template

**Requirements**: 3
**Files**: `app/Filament/Resources/Helpdesk/Actions/AssignTicketAction.php`, `app/Mail/Helpdesk/TicketAssignedMail.php`

### 2.3 Implement Status Transitions

- [ ] Create TicketStatusTransitionService (state machine)
- [ ] Add valid transitions: open→assigned/in_progress/closed, etc.
- [ ] Create status update action (`Filament\Actions\Action`)
- [ ] Queue TicketStatusChangedMail
- [ ] Create bilingual email template

**Requirements**: 3
**Files**: `app/Services/TicketStatusTransitionService.php`, `app/Mail/Helpdesk/TicketStatusChangedMail.php`

### 2.4 Add Bulk Operations

- [ ] Create bulk assign (`Filament\Actions\BulkAction`)
- [ ] Create bulk status update with success/failure reporting
- [ ] Create bulk export (CSV/Excel/PDF placeholder)
- [ ] Add `->deselectRecordsAfterCompletion()`
- [ ] Test bulk operations

**Requirements**: 3, 9
**Files**: `app/Filament/Resources/Helpdesk/Tables/HelpdeskTicketsTable.php`

### 2.5 Enhance Ticket Detail View

- [ ] Verify ViewHelpdeskTicket quick actions (assign, update status, export)
- [ ] Verify HelpdeskTicketInfolist (asset card)
- [ ] Verify RelationManagers (5): Comments, Attachments, AssignmentHistory, StatusTimeline, CrossModuleIntegrations

**Requirements**: 3, 8
**Files**: `app/Filament/Resources/Helpdesk/Pages/ViewHelpdeskTicket.php`

---

## Phase 3: Asset Loan Resource (0/4)

### 3.1 Enhance Loan Table

- [ ] Add filters: date_range, asset_category (deferred by default)
- [ ] Add overdue indicator column (badge)
- [ ] Configure `->deferFilters()`
- [ ] Verify pagination, search

**Requirements**: 4
**Files**: `app/Filament/Resources/Loans/Tables/LoanApplicationsTable.php`

### 3.2 Create Issuance Action

- [ ] Create `ProcessIssuanceAction` extends `Filament\Actions\Action`
- [ ] Add 4-section form: Issuance Info, Condition Assessment, Accessory Checklist, Special Instructions
- [ ] Implement condition options: excellent, good, fair
- [ ] Add 7 accessories: power adapter, mouse, keyboard, cable, bag, manual, warranty card
- [ ] Queue LoanIssuedMail (60s SLA)

**Requirements**: 4
**Files**: `app/Filament/Resources/Loans/Actions/ProcessIssuanceAction.php`, `app/Mail/Loans/LoanIssuedMail.php`

### 3.3 Create Return Action

- [ ] Create `ProcessReturnAction` extends `Filament\Actions\Action`
- [ ] Add 4-section form: Return Info, Condition Assessment, Accessory Verification, Additional Notes
- [ ] Implement condition options: excellent, good, fair, poor, damaged
- [ ] Add auto-maintenance ticket for poor/damaged (5s SLA)
- [ ] Create CrossModuleIntegration record
- [ ] Queue LoanReturnedMail

**Requirements**: 4, 8
**Files**: `app/Filament/Resources/Loans/Actions/ProcessReturnAction.php`, `app/Mail/Loans/LoanReturnedMail.php`

### 3.4 Create Calendar Widget

- [ ] Create AssetAvailabilityCalendarWidget
- [ ] Integrate FullCalendar.js (monthly/weekly view)
- [ ] Add color coding: green (available), yellow (on_loan), red (maintenance)
- [ ] Add category filter
- [ ] Add click-to-view-details

**Requirements**: 4
**Files**: `app/Filament/Widgets/AssetAvailabilityCalendarWidget.php`

---

## Phase 4: Asset Inventory Resource (0/4)

### 4.1 Enhance Asset Table

- [ ] Verify filters: category, status, availability, location, condition (deferred by default)
- [ ] Verify `->deferFilters()`
- [ ] Verify badges, pagination

**Requirements**: 5
**Files**: `app/Filament/Resources/Assets/Tables/AssetsTable.php`

### 4.2 Enhance Asset Detail View

- [ ] Verify ViewAsset quick actions
- [ ] Verify AssetInfolist (specifications with Repeater component)
- [ ] Verify RelationManagers: LoanHistory, HelpdeskTickets

**Requirements**: 5, 8
**Files**: `app/Filament/Resources/Assets/Pages/ViewAsset.php`

### 4.3 Create Condition Tracking

- [ ] Verify UpdateConditionAction (`Filament\Actions\Action`)
- [ ] Verify condition options: excellent, good, fair, poor, damaged
- [ ] Verify auto-status update (poor/damaged → maintenance)
- [ ] Verify auto-maintenance ticket creation

**Requirements**: 5
**Files**: `app/Filament/Resources/Assets/Actions/UpdateConditionAction.php`

### 4.4 Create Utilization Analytics

- [ ] Verify AssetUtilizationService (7 metrics)
- [ ] Verify AssetUtilizationAnalyticsWidget (charts)

**Requirements**: 5
**Files**: `app/Services/AssetUtilizationService.php`, `app/Filament/Widgets/AssetUtilizationAnalyticsWidget.php`

---

## Phase 5: User Management (0/4)

### 5.1 Implement Authorization

- [ ] Add `shouldRegisterNavigation()` to UserResource (superuser-only)
- [ ] Create UserPolicy (superuser-only CRUD)
- [ ] Add validation: prevent removing last superuser

**Requirements**: 6
**Files**: `app/Filament/Resources/UserResource.php`, `app/Policies/UserPolicy.php`

### 5.2 Enhance User Table

- [ ] Verify filters: role, division, grade, account_status (deferred by default)
- [ ] Verify role badges
- [ ] Verify bulk actions (`Filament\Actions\BulkAction`)

**Requirements**: 6
**Files**: `app/Filament/Resources/Users/Tables/UsersTable.php`

### 5.3 Create User Creation

- [ ] Verify CreateUser with generateSecurePassword()
- [ ] Verify UserWelcomeMail (temporary password, require_password_change flag)

**Requirements**: 6
**Files**: `app/Filament/Resources/Users/Pages/CreateUser.php`, `app/Mail/UserWelcomeMail.php`

### 5.4 Create Activity Dashboard

- [ ] Verify UserActivityWidget (login history, recent actions, failed attempts)

**Requirements**: 6
**Files**: `app/Filament/Widgets/UserActivityWidget.php`

---

## Phase 6: Unified Dashboard (0/6)

### 6.1 Create Statistics Widget

- [ ] Verify UnifiedDashboardOverview (6 metrics, 300s refresh)

**Requirements**: 7
**Files**: `app/Filament/Widgets/UnifiedDashboardOverview.php`

### 6.2 Create Trend Charts

- [ ] Verify TicketVolumeChart, ResolutionTimeChart, TicketsByStatusChart

**Requirements**: 7
**Files**: `app/Filament/Widgets/TicketVolumeChart.php`, etc.

### 6.3 Create Utilization Chart

- [ ] Verify AssetUtilizationWidget, UnifiedAnalyticsChart

**Requirements**: 7
**Files**: `app/Filament/Widgets/AssetUtilizationWidget.php`

### 6.4 Create Activity Feed

- [ ] Create RecentActivityFeedWidget (60s polling)
- [ ] Display: tickets, loans, approvals, status changes
- [ ] Add click-to-view-details

**Requirements**: 7
**Files**: `app/Filament/Widgets/RecentActivityFeedWidget.php`

### 6.5 Create Quick Actions

- [ ] Create QuickActionsWidget (permission-based)
- [ ] Add actions: create ticket, process loan, assign asset

**Requirements**: 7
**Files**: `app/Filament/Widgets/QuickActionsWidget.php`

### 6.6 Create Critical Alerts

- [ ] Create CriticalAlertsWidget (60s polling)
- [ ] Add alerts: SLA breaches (15min), overdue returns (24h), pending approvals (48h)

**Requirements**: 7
**Files**: `app/Filament/Widgets/CriticalAlertsWidget.php`

---

## Phase 7: Cross-Module Integration (0/5)

### 7.1 Enhance Ticket Asset Card

- [ ] Enhance HelpdeskTicketInfolist asset card
- [ ] Add clickable asset link, loan status, 5 recent loans

**Requirements**: 8
**Files**: `app/Filament/Resources/Helpdesk/Schemas/HelpdeskTicketInfolist.php`

### 7.2 Verify Related Tickets Tab

- [ ] Verify HelpdeskTicketsRelationManager in AssetResource

**Requirements**: 8
**Files**: `app/Filament/Resources/Assets/RelationManagers/HelpdeskTicketsRelationManager.php`

### 7.3 Verify Auto-Ticket Creation

- [ ] Verify ProcessReturnAction auto-ticket logic (5s SLA)

**Requirements**: 8
**Files**: `app/Filament/Resources/Loans/Actions/ProcessReturnAction.php`

### 7.4 Create Unified Search

- [ ] Create UnifiedSearch page
- [ ] Create UnifiedSearchService (multi-resource, caching, relevance ranking)
- [ ] Add keyboard shortcuts (Ctrl+K)

**Requirements**: 8, 12
**Files**: `app/Filament/Pages/UnifiedSearch.php`, `app/Services/UnifiedSearchService.php`

### 7.5 Add Referential Integrity

- [ ] Create migration: foreign key constraints (CASCADE/RESTRICT)
- [ ] Test referential integrity

**Requirements**: 8
**Files**: `database/migrations/*_add_referential_integrity_constraints.php`

---

## Phase 8: Reporting & Export (0/5)

### 8.1 Create Report Builder

- [ ] Create ReportBuilder page (module selection, date range, status filters, format)
- [ ] Create ReportBuilderService (data extraction, formatting)
- [ ] Add preview functionality

**Requirements**: 9
**Files**: `app/Filament/Pages/ReportBuilder.php`, `app/Services/ReportBuilderService.php`

### 8.2 Implement Automated Reports

- [ ] Create report_schedules table migration
- [ ] Create ReportSchedule model
- [ ] Create AutomatedReportService (daily/weekly/monthly)
- [ ] Create GenerateScheduledReportsCommand
- [ ] Create ScheduledReportMail
- [ ] Create ReportScheduleResource

**Requirements**: 9
**Files**: `app/Services/AutomatedReportService.php`, `app/Filament/Resources/Reports/ReportScheduleResource.php`

### 8.3 Create Export Service

- [ ] Create DataExportService (CSV/Excel/PDF, WCAG compliant, 50MB limit)

**Requirements**: 9
**Files**: `app/Services/DataExportService.php`

### 8.4 Create Report Templates

- [ ] Create ReportTemplateService (5 templates)
- [ ] Create ReportTemplates page (one-click generation)

**Requirements**: 9
**Files**: `app/Services/ReportTemplateService.php`, `app/Filament/Pages/ReportTemplates.php`

### 8.5 Create Visualization Tools

- [ ] Create DataVisualizationService (5 chart types, drill-down)
- [ ] Create DataVisualization page (export PNG/PDF/SVG)

**Requirements**: 9
**Files**: `app/Services/DataVisualizationService.php`, `app/Filament/Pages/DataVisualization.php`

---

## Phase 9: Audit & Security (0/4)

### 9.1 Create Audit Resource

- [ ] Create AuditResource (superuser-only, 7-year retention)
- [ ] Add filters: date_range, user, action_type, entity (deferred by default)
- [ ] Add export functionality

**Requirements**: 10
**Files**: `app/Filament/Resources/System/AuditResource.php`

### 9.2 Create Security Monitoring

- [ ] Create SecurityMonitoringService
- [ ] Create SecurityMonitoring page (superuser-only)
- [ ] Add real-time alerts (60s SLA)

**Requirements**: 10
**Files**: `app/Services/SecurityMonitoringService.php`, `app/Filament/Pages/SecurityMonitoring.php`

### 9.3 Create Audit Export

- [ ] Create AuditExportService (CSV/PDF/Excel/JSON, 50MB limit)

**Requirements**: 10
**Files**: `app/Services/AuditExportService.php`

### 9.4 Create Security Incidents

- [ ] Create SecurityIncidentService (5min detection, 60s alert)
- [ ] Create SecurityIncidentMail

**Requirements**: 10
**Files**: `app/Services/SecurityIncidentService.php`, `app/Mail/Security/SecurityIncidentMail.php`

---

## Phase 10: Notification Management (0/5)

### 10.1 Create Notification Center

- [ ] Create NotificationCenter page (filtering, real-time updates)
- [ ] Add navigation badge (unread count)

**Requirements**: 11
**Files**: `app/Filament/Pages/NotificationCenter.php`

### 10.2 Implement Real-time Notifications

- [ ] Create RealTimeNotificationService (SLA breaches, overdue returns, pending approvals)

**Requirements**: 11
**Files**: `app/Services/RealTimeNotificationService.php`

### 10.3 Add Notification Detail View

- [ ] Integrate into NotificationCenter (action buttons, dismissal)

**Requirements**: 11
**Files**: `app/Filament/Pages/NotificationCenter.php`

### 10.4 Create Notification Preferences

- [ ] Create NotificationPreferences page (delivery methods, frequency, quiet hours)

**Requirements**: 11
**Files**: `app/Filament/Pages/NotificationPreferences.php`

### 10.5 Add Urgent Highlighting

- [ ] Integrate into NotificationCenter (priority-based visual indicators)

**Requirements**: 11
**Files**: `app/Filament/Pages/NotificationCenter.php`

---

## Phase 11: Advanced Search (0/3)

### 11.1 Enhance Global Search

- [ ] Create GlobalSearchService (caching 5min, filtering, relevance scoring)
- [ ] Enhance UnifiedSearch page (keyboard shortcuts, suggestions)

**Requirements**: 12
**Files**: `app/Services/GlobalSearchService.php`, `app/Filament/Pages/UnifiedSearch.php`

### 11.2 Create Filter Presets

- [ ] Create FilterPresetService (save/load, URL generation)
- [ ] Create FilterPresets page

**Requirements**: 12
**Files**: `app/Services/FilterPresetService.php`, `app/Filament/Pages/FilterPresets.php`

### 11.3 Create Search History

- [ ] Create SearchHistoryService (50 items max, analytics)

**Requirements**: 12
**Files**: `app/Services/SearchHistoryService.php`

---

## Phase 12: System Configuration (0/4)

### 12.1 Create Approval Matrix

- [ ] Create ApprovalMatrixService (grade-based routing, asset value thresholds)
- [ ] Create ApprovalMatrixConfiguration page (superuser-only)

**Requirements**: 13
**Files**: `app/Services/ApprovalMatrixService.php`, `app/Filament/Pages/ApprovalMatrixConfiguration.php`

### 12.2 Create SLA Management

- [ ] Create SLAThresholdService (category-based, escalation rules)
- [ ] Create SLAThresholdManagement page (superuser-only)

**Requirements**: 13
**Files**: `app/Services/SLAThresholdService.php`, `app/Filament/Pages/SLAThresholdManagement.php`

### 12.3 Create Workflow Automation

- [ ] Create workflow_rules table migration
- [ ] Create WorkflowRule model
- [ ] Create WorkflowAutomationService (if-then logic, rule execution)
- [ ] Create WorkflowAutomationConfiguration page (superuser-only)

**Requirements**: 13
**Files**: `app/Services/WorkflowAutomationService.php`, `app/Filament/Pages/WorkflowAutomationConfiguration.php`

### 12.4 Create Email Template Management

- [ ] Create email_templates table migration
- [ ] Create EmailTemplate model
- [ ] Create EmailTemplateService (variable substitution, WCAG validation)
- [ ] Create EmailTemplateManagement page (superuser-only, rich editor, preview)

**Requirements**: 13, 17
**Files**: `app/Services/EmailTemplateService.php`, `app/Filament/Pages/EmailTemplateManagement.php`

---

## Phase 13: Performance Monitoring (0/5)

### 13.1 Create Performance Dashboard

- [ ] Create PerformanceMonitoringService (real-time metrics, 60s refresh)
- [ ] Create PerformanceMonitoring page (superuser-only)

**Requirements**: 14
**Files**: `app/Services/PerformanceMonitoringService.php`, `app/Filament/Pages/PerformanceMonitoring.php`

### 13.2 Add Performance Trends

- [ ] Integrate into PerformanceMonitoring page (hourly/daily/weekly/monthly charts)

**Requirements**: 14
**Files**: `app/Filament/Pages/PerformanceMonitoring.php`

### 13.3 Add Integration Health

- [ ] Integrate into PerformanceMonitoring page (HRMIS, email, Redis, MySQL status)

**Requirements**: 14
**Files**: `app/Filament/Pages/PerformanceMonitoring.php`

### 13.4 Add Performance Alerts

- [ ] Integrate into PerformanceMonitoringService (threshold alerts, email notifications)

**Requirements**: 14
**Files**: `app/Services/PerformanceMonitoringService.php`

### 13.5 Add Diagnostic Tools

- [ ] Integrate into PerformanceMonitoring page (slow queries, cache stats, queue status)

**Requirements**: 14
**Files**: `app/Filament/Pages/PerformanceMonitoring.php`

---

## Phase 14: WCAG Compliance (0/5)

### 14.1 Verify Color Contrast

- [ ] Create AccessibilityComplianceService (contrast validation 4.5:1 text, 3:1 UI)
- [ ] Test with color contrast analyzer

**Requirements**: 15
**Files**: `app/Services/AccessibilityComplianceService.php`

### 14.2 Verify Keyboard Navigation

- [ ] Test focus indicators (3-4px outline, 2px offset, 3:1 contrast)
- [ ] Test logical tab order
- [ ] Test keyboard shortcuts

**Requirements**: 15
**Files**: N/A (testing)

### 14.3 Verify ARIA Attributes

- [ ] Test ARIA landmarks (navigation, main, complementary)
- [ ] Test with screen readers (NVDA, JAWS)

**Requirements**: 15
**Files**: N/A (testing)

### 14.4 Verify ARIA Live Regions

- [ ] Test live regions (polite, assertive)
- [ ] Test with screen readers

**Requirements**: 15
**Files**: N/A (testing)

### 14.5 Verify Form Accessibility

- [ ] Test labels, error messages, required indicators, help text
- [ ] Test with screen readers

**Requirements**: 15
**Files**: N/A (testing)

---

## Phase 15: Bilingual Support (0/5)

### 15.1 Create Language Switcher

- [ ] Create BilingualSupportService
- [ ] Add language switcher to navigation (44×44px touch target, ARIA attributes)

**Requirements**: 16
**Files**: `app/Services/BilingualSupportService.php`

### 15.2 Add Translation Files

- [ ] Create MS translation files (all admin interface text)
- [ ] Create EN translation files
- [ ] Translate email templates

**Requirements**: 16
**Files**: `lang/ms/*.php`, `lang/en/*.php`

### 15.3 Implement Language Persistence

- [ ] Add session + cookie storage (1-year expiration)
- [ ] Add auto-detection on first login

**Requirements**: 16
**Files**: `app/Services/BilingualSupportService.php`

### 15.4 Configure Locale Detection

- [ ] Implement priority: session > cookie > Accept-Language > config fallback
- [ ] Validate against ['en', 'ms']

**Requirements**: 16
**Files**: `app/Services/BilingualSupportService.php`

### 15.5 Add Real-time Switching

- [ ] Implement Livewire-based language switching (no page reload)
- [ ] Update date/number formats based on locale

**Requirements**: 16
**Files**: `app/Services/BilingualSupportService.php`

---

## Phase 16: Email Management (0/3)

### 16.1 Create Email Dashboard

- [ ] Create EmailNotificationService
- [ ] Create EmailLogResource (sent emails, delivery status, failed deliveries)

**Requirements**: 17
**Files**: `app/Services/EmailNotificationService.php`, `app/Filament/Resources/System/EmailLogResource.php`

### 16.2 Add Queue Monitoring

- [ ] Create EmailQueueMonitoringService
- [ ] Add queue status display (pending, processing, completed, failed)

**Requirements**: 17
**Files**: `app/Services/EmailQueueMonitoringService.php`

### 16.3 Implement Retry Mechanism

- [ ] Configure retry (3 attempts, exponential backoff)
- [ ] Add manual retry action (`Filament\Actions\Action`)

**Requirements**: 17
**Files**: `app/Services/EmailNotificationService.php`

---

## Phase 17: Security Enhancements (0/3)

### 17.1 Implement 2FA

- [ ] Create TwoFactorAuthService (TOTP, backup codes)
- [ ] Create TwoFactorAuthentication page (setup wizard)

**Requirements**: 10
**Files**: `app/Services/TwoFactorAuthService.php`, `app/Filament/Pages/TwoFactorAuthentication.php`

### 17.2 Add Data Encryption

- [ ] Create DataEncryptionService (AES-256, approval tokens, personal data)
- [ ] Configure TLS 1.3

**Requirements**: 10
**Files**: `app/Services/DataEncryptionService.php`

### 17.3 Implement Re-authentication

- [ ] Create RequireReauthentication middleware (user deletion, role changes, config updates)

**Requirements**: 10
**Files**: `app/Http/Middleware/RequireReauthentication.php`

---

## Phase 18: Testing & QA (0/5)

### 18.1 Create Feature Tests

- [ ] Test HelpdeskTicketResource CRUD
- [ ] Test LoanApplicationResource workflows
- [ ] Test AssetResource management
- [ ] Test UserResource (superuser-only)
- [ ] Test authorization

**Requirements**: 18
**Files**: `tests/Feature/Filament/*ResourceTest.php`

### 18.2 Create Integration Tests

- [ ] Test cross-module integration (asset-ticket linking)
- [ ] Test auto-maintenance ticket creation
- [ ] Test email notifications
- [ ] Test audit logging
- [ ] Test bulk operations

**Requirements**: 18
**Files**: `tests/Feature/CrossModule/*Test.php`

### 18.3 Create Accessibility Tests

- [ ] Test keyboard navigation
- [ ] Test screen reader compatibility
- [ ] Test color contrast
- [ ] Test ARIA attributes
- [ ] Test with axe, WAVE

**Requirements**: 18
**Files**: `tests/Feature/Filament/AccessibilityTest.php`

### 18.4 Create Performance Tests

- [ ] Test dashboard load times
- [ ] Test table pagination with large datasets
- [ ] Test search performance
- [ ] Test export functionality
- [ ] Test real-time notifications

**Requirements**: 18
**Files**: `tests/Feature/Filament/PerformanceTest.php`

### 18.5 Create Security Tests

- [ ] Test authentication/authorization
- [ ] Test CSRF protection
- [ ] Test rate limiting
- [ ] Test data encryption
- [ ] Test session timeout

**Requirements**: 18
**Files**: `tests/Feature/Filament/SecurityTest.php`

---

## Implementation Notes

### Filament 4.x Compliance

- ✅ All actions extend `Filament\Actions\Action` (not `Filament\Tables\Actions`)
- ✅ Bulk actions use `Filament\Actions\BulkAction`
- ✅ Filters are deferred by default (`->deferFilters()`)
- ✅ File visibility is `private` by default
- ✅ Layout components use `Filament\Schemas\Components`
- ✅ Repeater component for forms
- ✅ Icons use `Filament\Support\Icons\Heroicon` Enum

### Code Quality Standards

- PSR-12 compliance (run `vendor/bin/pint`)
- PHPStan level 5 (run `vendor/bin/phpstan analyse`)
- 80%+ test coverage
- `declare(strict_types=1);` in all PHP files
- Type hints on all parameters and return types

### Performance Targets

- Dashboard load: <2s
- LCP: <2.5s
- FID: <100ms
- CLS: <0.1
- Email delivery: <60s
- Ticket creation: <5s

### Security Requirements

- CSRF protection on all forms
- Rate limiting: 60 requests/minute/user
- Session timeout: 30 minutes
- Password: 8+ chars, mixed case, numbers, symbols
- Audit retention: 7 years (PDPA 2010)

---

## References

- **Requirements**: `requirements.md` (18 functional requirements)
- **Design**: `design.md` (architecture, components, code examples)
- **Filament 4 Rules**: `.amazonq/rules/Filament.md`
- **Laravel 12 Rules**: `.amazonq/rules/Laravel.md`
- **Livewire 3 Rules**: `.amazonq/rules/Livewire.md`
