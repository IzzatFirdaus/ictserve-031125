# ICTServe Product Overview

## Purpose

ICTServe is an internal ICT management platform for BPM MOTAC (Ministry of Tourism, Arts and Culture Malaysia) staff. It provides a centralized system for managing ICT helpdesk tickets and asset loan applications with full accessibility compliance and bilingual support.

## Core Value Proposition

- **Guest-Friendly Access**: Staff can submit helpdesk tickets and loan applications without authentication
- **Email-Based Approvals**: Dual approval workflow using secure email tokens for loan applications
- **Full Accessibility**: WCAG 2.2 AA compliant with screen reader support and keyboard navigation
- **Bilingual Interface**: Complete Bahasa Melayu (primary) and English support
- **Comprehensive Audit Trail**: All actions logged for compliance and security
- **Performance Optimized**: Core Web Vitals score 90+ with optimized asset delivery

## Key Features

### Helpdesk Module

- **Guest Ticket Submission**: Submit ICT support tickets without login
- **File Attachments**: Support for multiple file uploads with validation
- **SLA Tracking**: Automatic SLA monitoring with breach alerts
- **Status Updates**: Real-time ticket status tracking with email notifications
- **Internal Comments**: Staff-only communication thread for ticket resolution
- **Ticket Assignment**: Automatic and manual assignment to support staff
- **Category Management**: Organized ticket categories for efficient triage

### Asset Loan Module

- **Guest Loan Applications**: Apply for ICT asset loans without authentication
- **Multi-Asset Selection**: Request multiple assets in single application
- **Dual Approval Workflow**: Email-based approval from two authorized approvers
- **Asset Availability Check**: Real-time asset availability validation
- **Loan Extension Requests**: Request extensions for active loans
- **Return Management**: Track asset returns with condition assessment
- **Overdue Notifications**: Automated reminders for overdue returns

### Admin Panel (Filament 4)

- **Dashboard Analytics**: Real-time statistics and performance metrics
- **Ticket Management**: Comprehensive ticket triage and assignment
- **Asset Management**: Full CRUD operations for ICT assets
- **User Management**: Role-based access control with Spatie permissions
- **Report Generation**: Automated and on-demand reports
- **Email Log Monitoring**: Track all system emails with delivery status
- **Audit Trail Viewer**: Complete audit log with filtering and export

### Staff Portal

- **Authenticated Dashboard**: Personalized dashboard for logged-in staff
- **Submission History**: View all personal helpdesk and loan submissions
- **Quick Actions**: Fast access to common tasks
- **Recent Activity Timeline**: Track recent system activities
- **Notification Center**: Centralized notification management
- **Profile Management**: Update personal information and preferences
- **Security Settings**: Two-factor authentication and session management

## Target Users

### Primary Users

- **MOTAC Staff**: Internal employees submitting helpdesk tickets and loan requests
- **ICT Support Team**: Technical staff managing tickets and asset loans
- **System Administrators**: IT administrators managing system configuration

### User Roles

- **Guest**: Unauthenticated users (can submit tickets/loans)
- **Staff**: Authenticated MOTAC employees
- **ICT Support**: Helpdesk technicians
- **Asset Manager**: Asset loan approvers
- **Administrator**: System administrators with full access

## Use Cases

### Helpdesk Workflow

1. Staff submits ICT issue via guest form
2. System creates ticket and sends confirmation email
3. ICT support team receives notification
4. Ticket assigned to available technician
5. Technician updates status and adds internal comments
6. Staff receives status update notifications
7. Ticket resolved and closed with satisfaction survey

### Asset Loan Workflow

1. Staff submits loan application for ICT assets
2. System validates asset availability
3. First approver receives email with approval link
4. After first approval, second approver receives email
5. Upon dual approval, staff receives confirmation
6. Asset prepared and handed over to staff
7. System tracks loan period and sends reminders
8. Staff returns asset and condition assessed
9. Loan closed and asset returned to inventory

### Cross-Module Integration

1. Damaged asset returned from loan
2. System automatically creates maintenance ticket
3. Asset marked as unavailable for loans
4. Maintenance ticket assigned to technician
5. After repair, asset returned to available pool
6. Audit trail links loan and maintenance records

## Technical Capabilities

### Performance

- First Contentful Paint < 1.5s
- Largest Contentful Paint < 2.5s
- Cumulative Layout Shift < 0.1
- Time to Interactive < 3s
- Lighthouse Performance Score 90+

### Accessibility

- WCAG 2.2 AA compliant
- Screen reader compatible (NVDA, JAWS)
- Full keyboard navigation
- Color contrast ratios 4.5:1 minimum
- ARIA landmarks and labels
- Focus management and indicators
- Touch targets 44x44px minimum

### Security

- CSRF protection on all forms
- Rate limiting on submissions
- Input sanitization and validation
- Secure file upload with virus scanning
- Role-based access control
- Audit logging for all actions
- Two-factor authentication support
- Session timeout with warnings

### Localization

- Bahasa Melayu (primary language)
- English (secondary language)
- Auto-detection from browser settings
- Cookie-based language persistence
- Date/time formatting per locale
- 100% bilingual coverage

### Integration

- Email notifications via Laravel Mail
- Queue system for async processing
- Real-time notifications via Laravel Echo
- Export functionality (CSV, Excel, PDF)
- Calendar integration for loan schedules
- HRMIS integration (planned)

## Success Metrics

- **Ticket Resolution Time**: Average < 24 hours
- **Loan Approval Time**: Average < 4 hours
- **System Uptime**: 99.9% availability
- **User Satisfaction**: 4.5/5 rating
- **Accessibility Compliance**: 100% WCAG 2.2 AA
- **Performance Score**: Lighthouse 90+
- **Email Delivery Rate**: 99%+
