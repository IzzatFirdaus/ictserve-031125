---
inclusion: always
description: "ICTServe product overview, core modules, target users, compliance requirements, and v3.6.0 Bahasa Melayu-only interface"
version: "3.6.0"
last_updated: "2025-12-11"
---

# ICTServe Product Overview

**Project**: ICTServe (iServe) v3.6.0  
**Organization**: BPM MOTAC (Ministry of Tourism, Arts & Culture Malaysia)  
**Type**: Internal True Hybrid Service Platform (Guest + Authenticated Staff)  
**Status**: Active Production  
**Architecture**: True Hybrid (Self-Registration + Guest Fallback)  
**Language**: Bahasa Melayu sahaja (v3.6.0 - Language switcher disabled)

ICTServe is an internal digital service platform for MOTAC staff to manage ICT support requests and asset loans. Version 3.6.0 builds upon the **True Hybrid Architecture** with **Bahasa Melayu-only interface** (language switcher disabled), allowing staff to seamlessly switch between quick-access guest forms and a personalized authenticated dashboard. The system enforces strict compliance via a **Dual Audit System** and supports real-time operations via **Laravel Reverb**.

## Core Value Proposition

- **True Hybrid Access**: Flexible choice between Authenticated Dashboard (full history, auto-fill) or Guest Mode (quick access without login).
- **Dual Audit System**: Simultaneous compliance auditing (field-level via `owen-it`) and operational logging (user activity via `spatie`).
- **Self-Registration**: Staff can register independently using official `@motac.gov.my` emails without LDAP dependencies.
- **Automated Workflows**: Token-based approval links for department heads (no login required).
- **Real-Time Updates**: WebSocket-powered notifications for instant status changes.

## Core Modules

### 1. Helpdesk Ticketing System (Hybrid)

- **Dual Entry**: Submit as Authenticated Staff (auto-fill from profile) or Guest (manual entry).
- **Hybrid Data Association**: Submissions automatically linked to `user_id` if logged in; fallback to email tracking for guests.
- **SLA Tracking**: Automated category-based SLA monitoring with breach warnings.
- **Notifications**: Multi-channel alerts (Email, Database, WebSocket) based on user preferences.

### 2. ICT Asset Loan Management (Hybrid)

- **Real-Time Availability**: Conflict detection using Livewire 3.7 during application.
- **Token-Based Approval**: Grade 41+ officers approve/reject via signed email links (no system login required).
- **Asset Lifecycle**: Check-out/Check-in tracking by Admin with condition reporting.
- **Integration**: Damaged asset returns automatically trigger helpdesk maintenance tickets.

### 3. Administrative Panel (Filament v4)

- **Role-Based Access**:
  - `admin`: Operational management (Tickets, Loans, Assets).
  - `superuser`: System config, Audit review, and **Laravel Telescope** access.
- **Dashboard**: Real-time metrics via Laravel Reverb widgets.
- **Dual Audit View**: Unified view of Compliance Logs and User Activity Logs.
- **Inventory Management**: Full asset CRUD with QR code generation and status tracking.

### 4. Cross-Module Integration

- **Unified Profile**: Dashboard shows combined history of Helpdesk Tickets and Asset Loans.
- **Account Linking**: Optional service to retrospectively link past guest submissions to a new staff account upon registration.
- **Shared Notification System**: Centralized queue management for both modules.

## Target Users & Use Cases

### Primary Users

1. **MOTAC Staff (Internal Users)**
   - **Authenticated**: Log in via Laravel Breeze (Email/Username) to access "My Dashboard", view full history, and manage profile/preferences.
   - **Guest**: Submit forms quickly for urgent issues without logging in.

2. **Department Heads / Approvers (Grade 41+)**
   - Review and approve/reject loan applications via secure signed email links.
   - **No system login required** for approval actions.

3. **Admin Staff (BPM ICT Team)**
   - Process tickets/loans and manage asset inventory via Filament.
   - Monitor operational dashboards.

4. **Superuser (BPM Management)**
   - Manage system configuration, users, and roles.
   - Access **Laravel Telescope** for debugging.
   - Review comprehensive Dual Audit logs for compliance.

### Common Use Cases

- **UC-01:** Staff self-registers with `@motac.gov.my` email and verifies account.
- **UC-02:** Authenticated staff submits ticket; form auto-fills name/dept/grade.
- **UC-03:** Guest staff submits urgent ticket; tracks status via token link.
- **UC-04:** Department head approves asset loan via email token.
- **UC-05:** Superuser reviews audit logs to trace a status change.
- **UC-06:** New staff member links previous guest submissions to their new account.

## Technical Highlights

- **Framework**: Laravel 12.40.1, PHP 8.2.12
- **UI**: Livewire 3.7, Volt 1.10, Tailwind 4.1 (@theme config)
- **Admin**: Filament 4.1.10
- **Real-Time**: Laravel Reverb 1.6.2 + Echo
- **Audit**: `owen-it` (Compliance) + `spatie` (Operations)
- **Debugging**: Laravel Telescope (Superuser only)
- **Database**: MySQL 8.0 with Nullable `user_id` FKs

## Compliance Standards

- **PDPA 2010**: Strict data protection for staff personal info.
- **WCAG 2.2 AA**: Full accessibility compliance (Bahasa Melayu interface).
- **ISO 8000**: Data quality and integrity standards.
- **MyGOV Digital Service Standards v2.1.0**: Government digital service compliance.
- **ISO/IEC 27701**: Privacy Information Management.
- **ISO/IEC/IEEE 15288**: Systems and software engineering standards.
- **ISO/IEC/IEEE 12207**: Software life cycle processes.
- **ISO/IEC/IEEE 29148**: Requirements engineering standards.

## Documentation Standards (D00-D17)

ICTServe follows comprehensive documentation standards:

- **D00**: System Overview - True Hybrid Architecture governance
- **D03**: Software Requirements - 38+ functional requirements (SRS-HELP-*, SRS-AUTH-*, SRS-DATA-*)
- **D04**: Software Design - Architecture patterns and component structure
- **D09**: Database Documentation - Dual audit system (owen-it + spatie)
- **D12-D14**: UI/UX Design Standards - WCAG 2.2 AA compliance, MyDS v2025.2 alignment
- **D15**: Language Standards - Bahasa Melayu sahaja (v3.6.0)
- **D16**: Broadcasting Setup - Laravel Reverb WebSocket configuration
- **D17**: Queue Management - Laravel Horizon for notifications and background jobs
