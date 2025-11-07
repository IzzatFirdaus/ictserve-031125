# Product Overview - ICTServe

## Project Identity
**Name:** ICTServe (iServe)  
**Version:** 3.0.0  
**Type:** Internal Staff Portal & Service Management System  
**Organization:** BPM MOTAC (Ministry of Tourism, Arts and Culture Malaysia)  
**Status:** Active Production

## Purpose & Value Proposition

ICTServe is an internal-only digital service platform for MOTAC staff to manage ICT support requests and asset loans. The system replaces legacy manual processes (Excel, email, paper forms) with a modern web-based solution that enforces SLA compliance, approval workflows, and comprehensive audit trails.

**Core Value:**

- Centralized ICT service management for MOTAC employees
- Automated approval workflows with role-based access control
- Full audit trail for compliance and accountability
- WCAG 2.2 AA accessibility compliance
- Bilingual support (Malay/English)

## Key Features & Capabilities

### 1. Helpdesk Ticketing System

- **Internal ticket submission** with real-time validation
- **Category-based SLA tracking** with automated alerts
- **File attachments** (up to 5 files, auto-optimized to WebP)
- **Email notifications** for ticket status updates
- **Internal comments** for staff collaboration
- **Automated assignment** to admin staff
- **SLA breach warnings** for supervisors
- **Comprehensive reporting** dashboard

### 2. ICT Asset Loan Management

- **Asset reservation system** with conflict detection
- **Multi-level approval workflow** based on staff grade (Grade 41+)
- **Email-based approval links** with signed tokens (no login required for approvers)
- **Asset lifecycle tracking** (checkout, return, damage reporting)
- **Automated reminders** for due dates and overdue items
- **Asset utilization analytics** and damage tracking
- **Integration with helpdesk** for maintenance tickets

### 3. Administrative Panel (Filament v4)

- **Role-based access** (admin, superuser)
- **Ticket management** with status transitions
- **Asset inventory management** with transaction history
- **Approval workflow monitoring**
- **SLA performance dashboards**
- **Audit log viewer** with comprehensive filtering
- **Email log tracking** for delivery monitoring
- **Configurable alert system** for operational events

### 4. Cross-Module Integration

- **Linked asset-ticket relationships** for damage reporting
- **Automated maintenance ticket creation** from damaged asset returns
- **Unified analytics dashboard** combining helpdesk and loan metrics
- **Shared notification system** for consistent communication

## Target Users & Use Cases

### Primary Users

1. **MOTAC Staff (Internal Users)**
   - Submit ICT support tickets for hardware/software issues
   - Request asset loans for official duties
   - Track ticket and loan application status
   - Receive automated notifications

2. **Department Heads / Approvers (Grade 41+)**
   - Review and approve/reject loan applications via email links
   - No system login required for approval actions
   - Receive approval request notifications

3. **Admin Staff (BPM ICT Team)**
   - Process helpdesk tickets and manage SLA compliance
   - Manage asset inventory and loan transactions
   - Generate operational reports
   - Monitor system performance

4. **Superuser (BPM Management)**
   - Configure system settings and SLA parameters
   - Manage user roles and permissions
   - Review audit logs and compliance reports
   - Oversee integrations and security

### Common Use Cases

- **UC-01:** Staff submits helpdesk ticket for laptop repair
- **UC-02:** Staff requests projector loan for meeting
- **UC-03:** Department head approves asset loan via email link
- **UC-04:** Admin processes ticket and updates status
- **UC-05:** System sends automated reminder for overdue asset
- **UC-06:** Damaged asset return triggers maintenance ticket
- **UC-07:** Superuser generates monthly SLA compliance report

## Technical Highlights

- Built on Laravel 12 framework with PHP 8.2
- Livewire v3 for reactive UI components
- Filament v4 for admin panel
- Spatie Laravel Permission for RBAC
- Owen-it Laravel Auditing for comprehensive audit trails
- Queue-based email/SMS notifications
- WCAG 2.2 AA accessibility compliance
- Lighthouse performance score ≥90
- Bilingual interface (Malay/English)
- SQLite database for development, MySQL for production
