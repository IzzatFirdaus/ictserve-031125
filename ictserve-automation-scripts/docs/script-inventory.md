# ICTServe Automation Script Inventory

## Overview

This document provides a complete inventory of all 347+ automation scripts organized by category.

## Script Categories

### 1. Guest User Workflows (50 Scripts)

#### Helpdesk Ticket Workflows (20 Scripts)

| # | Script | Description | Visual Demo |
|---|--------|-------------|-------------|
| 1 | submit-basic-ticket.ps1 | Submit basic helpdesk ticket | ✅ |
| 2 | submit-ticket-with-attachments.ps1 | Submit ticket with file attachments | ✅ |
| 3 | submit-ticket-multiple-categories.ps1 | Submit ticket with multiple categories | ✅ |
| 4 | test-form-validation.ps1 | Test form validation errors | ✅ |
| 5 | test-csrf-protection.ps1 | Test CSRF protection | 🔒 |
| 6 | track-ticket-by-number.ps1 | Track ticket by number | ✅ |
| 7 | track-ticket-by-email.ps1 | Track ticket by email | ✅ |
| 8 | test-email-notifications.ps1 | Test email notifications | 📧 |
| 9 | test-ticket-auto-assignment.ps1 | Test auto-assignment | ⚙️ |
| 10 | test-emergency-priority.ps1 | Test emergency priority | 🚨 |

#### Asset Loan Workflows (20 Scripts)

| # | Script | Description | Visual Demo |
|---|--------|-------------|-------------|
| 11 | submit-basic-loan-request.ps1 | Submit basic loan request | ✅ |
| 12 | check-asset-availability.ps1 | Check asset availability | ✅ |
| 13 | test-date-conflicts.ps1 | Test date conflict validation | ✅ |
| 14 | test-asset-category-selection.ps1 | Test category selection | ✅ |
| 15 | test-loan-duration-validation.ps1 | Test duration validation | ✅ |
| 16 | test-department-restrictions.ps1 | Test department restrictions | 🔒 |
| 17 | track-loan-status.ps1 | Track loan status | ✅ |
| 18 | test-approval-workflow-trigger.ps1 | Test approval workflow | 📧 |
| 19 | test-asset-conflict-detection.ps1 | Test conflict detection | ⚙️ |
| 20 | test-loan-extension-requests.ps1 | Test extension requests | ✅ |

#### Integration Tests (10 Scripts)

| # | Script | Description |
|---|--------|-------------|
| 21 | test-clamav-scanning.ps1 | ClamAV virus scanning |
| 22 | test-email-gateway.ps1 | Email gateway integration |
| 23 | test-database-transactions.ps1 | Database transaction integrity |
| 24 | test-queue-processing.ps1 | Laravel queue processing |
| 25 | test-redis-sessions.ps1 | Redis session management |

### 2. Authenticated User Workflows (67 Scripts)

#### Authentication (15 Scripts)

- test-email-login.ps1
- test-username-login.ps1
- test-google-sso.ps1
- test-password-reset.ps1
- test-account-lockout.ps1
- test-session-timeout.ps1
- test-concurrent-sessions.ps1
- test-hrmis-sync.ps1
- test-two-factor-auth.ps1
- test-remember-me.ps1

#### Dashboard (12 Scripts)

- test-widget-loading.ps1
- test-realtime-updates.ps1
- test-notification-center.ps1
- test-quick-actions.ps1
- test-keyboard-shortcuts.ps1
- test-dashboard-customization.ps1
- test-mobile-dashboard.ps1
- test-dashboard-performance.ps1
- test-websocket-connection.ps1
- test-push-notifications.ps1

#### Enhanced Helpdesk (20 Scripts)

- test-auto-filled-forms.ps1
- test-ticket-history.ps1
- test-ticket-comments.ps1
- test-file-attachment.ps1
- test-priority-escalation.ps1
- test-assignment-requests.ps1
- test-status-notifications.ps1
- test-ticket-claiming.ps1
- test-collaboration.ps1
- test-resolution-feedback.ps1

#### Enhanced Loans (15 Scripts)

- test-enhanced-application.ps1
- test-realtime-availability.ps1
- test-loan-history.ps1
- test-extension-requests.ps1
- test-pickup-otp.ps1
- test-loan-return.ps1
- test-maintenance-scheduling.ps1
- test-approval-tracking.ps1
- test-asset-transfer.ps1
- test-loan-cancellation.ps1

#### Profile Management (5 Scripts)

- test-profile-viewing.ps1
- test-field-updates.ps1
- test-correction-requests.ps1
- test-notification-preferences.ps1
- test-account-linking.ps1

### 3. Admin Panel Operations (78 Scripts)

#### Admin Authentication (10 Scripts)
#### Ticket Management (20 Scripts)
#### Asset Management (20 Scripts)
#### Loan Processing (15 Scripts)
#### User Management (8 Scripts)
#### Reporting (5 Scripts)

### 4. AI Integration (89 Scripts)

#### Ollama Local (20 Scripts)

- test-ollama-connectivity.ps1
- test-model-loading.ps1
- test-faq-responses.ps1
- test-sensitive-data-processing.ps1
- test-embedding-generation.ps1

#### AWS Bedrock (20 Scripts)

- test-bedrock-connectivity.ps1
- test-claude-models.ps1
- test-model-routing.ps1
- test-dlp-filtering.ps1
- test-public-data-processing.ps1

#### Conversation Management (15 Scripts)
#### Streaming Responses (12 Scripts)
#### Web-Augmented (12 Scripts)
#### MCP Integration (10 Scripts)

### 5. API Backend (89 Scripts)

#### Sanctum API (15 Scripts)
#### HRMIS Integration (15 Scripts)
#### Email Gateway (12 Scripts)
#### ClamAV Scanning (10 Scripts)
#### WebSocket/Real-time (12 Scripts)
#### Redis/Caching (10 Scripts)
#### Database (10 Scripts)
#### Queue Monitoring (5 Scripts)

### 6. Performance & Accessibility (45 Scripts)

#### Core Web Vitals (15 Scripts)
#### WCAG Compliance (20 Scripts)
#### Cross-Browser (10 Scripts)

### 7. Security & Compliance (52 Scripts)

#### Security Validation (25 Scripts)
#### PDPA Compliance (15 Scripts)
#### Penetration Testing (12 Scripts)

### 8. System Monitoring (38 Scripts)

#### Laravel Pulse (12 Scripts)
#### Laravel Horizon (13 Scripts)
#### Laravel Telescope (8 Scripts)
#### System Health (5 Scripts)

### 9. End-to-End Workflows (29 Scripts)

#### Complete Helpdesk (10 Scripts)
#### Complete Loans (10 Scripts)
#### Cross-Module (9 Scripts)

## Legend

- ✅ Visual Demo Available
- 🔒 Security Testing
- 📧 Email Workflow
- ⚙️ Backend Process
- 🚨 Priority Workflow

## Total Script Count: 347+
