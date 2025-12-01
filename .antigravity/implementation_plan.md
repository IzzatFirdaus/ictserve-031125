# Implementation Plan - ICTServe

Based on **D01 System Development Plan**, Version 3.2.0.

## Goal Description

Develop the **Helpdesk & ICT Asset Loan** system for MOTAC BPM, adhering to **ISO/IEC/IEEE 12207** and **WCAG 2.2 AA**.
The system will be a web-based intranet application using Laravel 12, Livewire 3, Filament 4, and Volt.

## User Review Required

- **Deployment Architecture**: Confirm Docker Compose setup for development and Linux/Nginx for production.
- **Security**: Confirm "Internal Use Only" policy and lack of public login (Guest forms only).
- **Integration**: Confirm SMTP and SMS Gateway integration details.

## Proposed Changes

### Core Framework

- **Laravel 12.40.1**: Base framework.
- **Livewire 3.7.0**: Frontend reactivity.
- **Filament 4.1.10**: Admin panel.
- **Volt 1.10.1**: Functional components.
- **Laravel Reverb**: Real-time updates.

### Modules

#### Helpdesk Ticketing

- Guest submission form (WCAG compliant).
- Admin management via Filament.
- Email notifications via Queue.

#### Asset Loan

- Guest application form.
- Approval workflow via signed email links.
- Inventory management in Filament.

#### Authentication

- Laravel Breeze for Admin/Superuser login.
- Spatie Permissions for RBAC.

### Database

- MySQL 8.0.
- Models: `User`, `HelpdeskTicket`, `LoanApplication`, `Asset`.
- Audit Trail: `owen-it/laravel-auditing`.

## Verification Plan

### Automated Tests

- **Unit Tests**: PHPUnit for models and logic.
- **Feature Tests**: Laravel HTTP tests for flows.
- **E2E Tests**: Playwright for browser automation.
- **Accessibility**: Axe-core scans.

### Manual Verification

- **UAT**: User Acceptance Testing with BPM staff.
- **Visual Inspection**: Verify UI against D12-D14 guidelines.
