# ICTServe - Product Overview

## Project Identity

**ICTServe** is an internal ICT management system for BPM MOTAC (Bahagian Pengurusan Maklumat, Kementerian Pelancongan, Seni dan Budaya Malaysia). The system streamlines ICT complaint handling and asset borrowing workflows for the Ministry of Tourism, Arts and Culture Malaysia.

## Core Purpose

ICTServe serves as a centralized platform to:

- **Manage ICT Helpdesk Operations**: Handle technical support requests and complaints from ministry staff
- **Facilitate Asset Borrowing**: Enable staff to request and track ICT equipment loans with approval workflows
- **Ensure Compliance**: Maintain audit trails and accessibility standards (WCAG 2.2 AA)
- **Support Bilingual Operations**: Provide full Bahasa Melayu and English language support

## Key Features

### 1. Public Helpdesk System

- **Guest Access**: Submit complaints without authentication
- **Ticket Management**: Track complaint status and resolution
- **Email Notifications**: Automated updates on ticket progress
- **SLA Tracking**: Monitor response and resolution times

### 2. Asset Borrowing Module

- **Loan Applications**: Staff can request ICT equipment
- **Approval Workflow**: Multi-level authorization via email
- **Asset Tracking**: Monitor equipment availability and usage
- **Return Management**: Track borrowed items and due dates

### 3. Admin Panel (Filament 4)

- **Dashboard**: Real-time statistics and system health
- **User Management**: Role-based access control (RBAC)
- **Audit Logs**: Comprehensive activity tracking
- **Reports**: Export data in PDF, Excel, CSV formats
- **Configuration**: System settings and customization

### 4. Compliance & Standards

- **Accessibility**: WCAG 2.2 AA compliant UI/UX
- **Security**: CSRF protection, input validation, secure authentication
- **Audit Trail**: Complete logging via Laravel Auditing
- **Bilingual**: Full MS/EN translation support

### 5. Real-Time Features

- **Broadcasting**: Laravel Reverb for WebSocket connections
- **Queue Management**: Background job processing with Horizon
- **Notifications**: In-app and email alerts
- **Live Updates**: Real-time status changes

## Target Users

### Primary Users

1. **Ministry Staff**: Submit helpdesk tickets and borrow equipment
2. **ICT Support Team**: Manage tickets and resolve technical issues
3. **Asset Managers**: Approve loans and track equipment
4. **System Administrators**: Configure system and manage users

### User Roles

- **Guest**: Submit helpdesk tickets (no login required)
- **Staff**: Authenticated users who can borrow assets
- **Technician**: Handle helpdesk tickets and technical support
- **Manager**: Approve asset loans and view reports
- **Admin**: Full system access and configuration

## Value Proposition

### For Ministry Staff

- **Easy Access**: Submit complaints without complex authentication
- **Transparency**: Track ticket and loan status in real-time
- **Efficiency**: Faster response times with automated workflows
- **Convenience**: Bilingual interface in preferred language

### For ICT Department

- **Centralized Management**: Single platform for all ICT operations
- **Automation**: Reduce manual work with email notifications and approvals
- **Visibility**: Dashboard insights and comprehensive reporting
- **Compliance**: Built-in audit trails and accessibility standards

### For Management

- **Accountability**: Complete audit logs and activity tracking
- **Insights**: Performance metrics and SLA monitoring
- **Standards**: WCAG 2.2 AA accessibility compliance
- **Security**: Role-based access and secure authentication

## Use Cases

### Use Case 1: Staff Reports Computer Issue

1. Staff member visits helpdesk form (no login required)
2. Fills out complaint details (equipment, issue description)
3. System generates ticket and sends confirmation email
4. Technician receives notification and assigns ticket
5. Staff receives updates as ticket progresses
6. Ticket closed when issue resolved

### Use Case 2: Staff Borrows Projector

1. Staff logs in and navigates to asset borrowing
2. Selects projector and specifies loan period
3. System sends approval request to manager via email
4. Manager approves via email link
5. Staff receives approval notification
6. Asset marked as borrowed in system
7. Reminder sent before return date

### Use Case 3: Admin Generates Audit Report

1. Admin accesses Filament panel
2. Navigates to unified audit log
3. Filters by date range and activity type
4. Exports report as PDF with MOTAC branding
5. Report includes all compliance and activity logs

## Technical Highlights

- **Modern Stack**: Laravel 12, Livewire 3, Filament 4
- **Real-Time**: WebSocket support via Laravel Reverb
- **Scalable**: Queue-based processing with Redis
- **Tested**: PHPUnit, Playwright E2E, accessibility tests
- **Documented**: Comprehensive D00-D17 technical documentation
- **AI-Enhanced**: Laravel Boost MCP integration for development

## Project Status

**Version**: 3.5.0  
**Status**: Active Development  
**Environment**: Production-ready with Docker support  
**License**: Proprietary (BPM MOTAC Internal Use)
