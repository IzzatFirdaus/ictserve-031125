# ICTServe Administrator Guide

**Version**: 3.0.0  
**Last Updated**: November 2025  
**Audience**: System Administrators, IT Staff, Superusers

---

## Table of Contents

1. [Introduction](#introduction)
2. [System Architecture](#system-architecture)
3. [Admin Panel Access](#admin-panel-access)
4. [User Management](#user-management)
5. [Helpdesk Administration](#helpdesk-administration)
6. [Asset Loan Administration](#asset-loan-administration)
7. [System Configuration](#system-configuration)
8. [Security & Compliance](#security--compliance)
9. [Monitoring & Reporting](#monitoring--reporting)
10. [Troubleshooting](#troubleshooting)

---

## Introduction

This guide provides comprehensive instructions for administering the ICTServe system. It covers user management, ticket handling, asset loan processing, system configuration, and security compliance.

### Administrator Roles

| Role | Access Level | Responsibilities |
|------|--------------|------------------|
| **Staff** | Portal only | Submit tickets, apply for loans |
| **Approver (Grade 41+)** | Portal + Approval | Approve/reject loan applications |
| **Admin** | Filament Panel | Manage tickets, assets, users |
| **Superuser** | Full Access | System configuration, all admin functions |

---

## System Architecture

### Technology Stack

- **Framework**: Laravel 12.x with PHP 8.3+
- **UI Framework**: Livewire 3.x, Volt 1
- **Admin Panel**: Filament 4
- **CSS**: Tailwind CSS 4.1
- **JavaScript**: Alpine.js 3.x
- **Database**: MySQL 8.x (Production), SQLite (Development)
- **Cache**: Redis
- **Queue**: Redis-based Laravel Queue

### Three-Tier Architecture

1. **Guest Layer**: Public forms (no authentication)
2. **Portal Layer**: Authenticated staff interface
3. **Admin Layer**: Filament administrative panel

---

## Admin Panel Access

### Accessing Filament Admin

1. Navigate to `https://ictserve.motac.gov.my/admin`
2. Login with admin credentials
3. Dashboard displays system overview

### Admin Dashboard Widgets

| Widget | Description |
|--------|-------------|
| **Ticket Queue** | Pending helpdesk tickets |
| **Loan Approval Queue** | Pending loan applications |
| **System Statistics** | Overall system metrics |
| **Recent Activity** | Latest system events |

### Impersonation Feature

Admins can view the portal as any user for debugging:

1. Go to **Users** resource
2. Click **View as User** action
3. Yellow banner indicates impersonation mode
4. Click **Stop Impersonating** to return

**Security Note**: Critical actions (password change, email update) are blocked during impersonation.

---

## User Management

### Creating Users

1. Navigate to **Users** in sidebar
2. Click **Create User**
3. Fill required fields:
   - Name
   - Email (unique)
   - Staff ID
   - Grade
   - Department/Division
   - Role (Staff, Approver, Admin, Superuser)
4. Click **Create**

### Editing Users

1. Find user in list or search
2. Click **Edit** action
3. Modify fields as needed
4. Click **Save**

### Role Assignment

| Role | Permissions |
|------|-------------|
| **Staff** | Submit tickets, apply loans, view own submissions |
| **Approver** | Staff permissions + approve/reject loans |
| **Admin** | Manage tickets, assets, users, view reports |
| **Superuser** | Full system access, configuration |

### Bulk Operations

1. Select multiple users using checkboxes
2. Choose bulk action:
   - **Activate/Deactivate**
   - **Change Role**
   - **Export**

---

## Helpdesk Administration

### Ticket Management

#### Viewing Tickets

1. Navigate to **Helpdesk Tickets**
2. Use filters:
   - Status (New, In Progress, Resolved, Closed)
   - Category (Hardware, Software, Network, etc.)
   - Priority (Low, Normal, High, Critical)
   - Date range

#### Processing Tickets

1. Click on ticket to view details
2. Update status as work progresses
3. Add internal comments for team communication
4. Assign to specific staff member if needed

#### Status Workflow

```
New → In Progress → Awaiting Feedback → Resolved → Closed
                  ↓
              Escalated
```

### SLA Management

| Priority | Response Time | Resolution Time |
|----------|---------------|-----------------|
| Critical | 1 hour | 4 hours |
| High | 4 hours | 24 hours |
| Normal | 8 hours | 72 hours |
| Low | 24 hours | 1 week |

### Category Configuration

1. Navigate to **Settings > Ticket Categories**
2. Add/Edit/Delete categories
3. Set default SLA for each category

---

## Asset Loan Administration

### Asset Management

#### Adding Assets

1. Navigate to **Assets**
2. Click **Create Asset**
3. Fill details:
   - Asset Tag (unique identifier)
   - Name/Description
   - Category (Projector, Laptop, Camera, etc.)
   - Condition (New, Good, Fair, Poor)
   - Location
4. Click **Create**

#### Asset Status

| Status | Description |
|--------|-------------|
| **Available** | Ready for loan |
| **On Loan** | Currently borrowed |
| **Maintenance** | Under repair |
| **Retired** | No longer in service |

### Loan Application Processing

#### Approval Queue

1. Navigate to **Loan Applications**
2. Filter by **Pending Approval** status
3. Review application details:
   - Applicant information
   - Requested assets
   - Loan dates
   - Purpose

#### Approval Actions

- **Approve**: Generates pickup OTP, notifies applicant
- **Reject**: Requires reason, notifies applicant
- **Request Info**: Asks applicant for clarification

### Asset Handover (OTP Verification)

1. Applicant arrives with OTP
2. Navigate to **Loan Applications > Ready for Pickup**
3. Click **Verify Handover**
4. Enter 4-digit OTP
5. System validates and marks as **Issued**

### Asset Return Processing

1. Navigate to **Loan Applications > Due for Return**
2. Click **Process Return**
3. Inspect asset condition
4. Select condition:
   - **Good**: Normal return
   - **Damaged**: Creates Helpdesk ticket automatically
5. Confirm return

---

## System Configuration

### General Settings

Navigate to **Settings > General**:

| Setting | Description |
|---------|-------------|
| **Site Name** | Display name for the system |
| **Support Email** | Contact email for users |
| **Default Language** | System default (ms/en) |
| **Timezone** | Server timezone |

### Email Configuration

Navigate to **Settings > Email**:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.motac.gov.my
MAIL_PORT=587
MAIL_USERNAME=ictserve@motac.gov.my
MAIL_PASSWORD=********
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=ictserve@motac.gov.my
MAIL_FROM_NAME="ICTServe MOTAC"
```

### Queue Configuration

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Cache Configuration

```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### Working Days Configuration

Navigate to **Settings > Working Days**:

1. Configure weekend days (Saturday, Sunday)
2. Add Malaysian public holidays
3. Set minimum lead time (default: 3 days)

---

## Security & Compliance

### Authentication

- **Session Timeout**: 120 minutes
- **Password Policy**: Minimum 8 characters, mixed case, numbers
- **Email Verification**: Required for new accounts
- **Two-Factor Authentication**: Optional (recommended for admins)

### Authorization (RBAC)

Permissions are managed via Spatie Laravel Permission:

```php
// Example permission check
$user->hasRole('admin');
$user->can('manage-tickets');
```

### Audit Trail

All actions are logged via Laravel Auditing:

1. Navigate to **Audit Logs**
2. Filter by:
   - User
   - Action type
   - Date range
   - Model type
3. View detailed change history

**Retention**: 7 years (PDPA 2010 compliance)

### Data Protection (PDPA 2010)

| Requirement | Implementation |
|-------------|----------------|
| **Consent** | Declaration checkbox on forms |
| **Access** | Users can view their data |
| **Correction** | Request via Helpdesk ticket |
| **Retention** | 7-year audit trail |
| **Security** | AES-256 encryption for sensitive data |

### Security Headers

Configured in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SecurityHeaders::class,
    ]);
})
```

Headers applied:

- `X-Frame-Options: DENY`
- `X-Content-Type-Options: nosniff`
- `X-XSS-Protection: 1; mode=block`
- `Strict-Transport-Security: max-age=31536000`

---

## Monitoring & Reporting

### Dashboard Metrics

| Metric | Description |
|--------|-------------|
| **Ticket Volume** | Daily/weekly/monthly ticket counts |
| **SLA Compliance** | Percentage of tickets resolved within SLA |
| **Asset Utilization** | Loan frequency by asset type |
| **Overdue Items** | Assets past return date |

### Report Generation

1. Navigate to **Reports**
2. Select report type:
   - Helpdesk Summary
   - Asset Utilization
   - SLA Performance
   - User Activity
3. Set date range
4. Export format (CSV, PDF, Excel)

### Scheduled Reports

Configure automated reports:

1. Navigate to **Settings > Scheduled Reports**
2. Create new schedule:
   - Report type
   - Frequency (daily, weekly, monthly)
   - Recipients (email addresses)
   - Format

### Performance Monitoring

#### Core Web Vitals Targets

| Metric | Target |
|--------|--------|
| **LCP** | < 2.5 seconds |
| **FID** | < 100 milliseconds |
| **CLS** | < 0.1 |
| **TTFB** | < 600 milliseconds |

#### Queue Monitoring

```bash
# Check queue status
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

---

## Troubleshooting

### Common Issues

#### Email Not Sending

1. Check queue worker is running:

   ```bash
   php artisan queue:work
   ```

2. Verify SMTP configuration
3. Check failed jobs table
4. Review Laravel logs: `storage/logs/laravel.log`

#### Slow Performance

1. Clear caches:

   ```bash
   php artisan optimize:clear
   ```

2. Check Redis connection
3. Review database queries (N+1 issues)
4. Check server resources

#### Login Issues

1. Verify user exists and is active
2. Check email verification status
3. Reset password if needed
4. Review authentication logs

#### Asset Availability Issues

1. Check asset status in database
2. Verify no conflicting bookings
3. Clear availability cache:

   ```bash
   php artisan cache:forget asset_availability
   ```

### Maintenance Commands

```bash
# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize

# Run migrations
php artisan migrate

# Seed database (development only)
php artisan db:seed

# Generate IDE helpers
php artisan ide-helper:generate
php artisan ide-helper:models

# Run tests
php artisan test

# Code formatting
vendor/bin/pint

# Static analysis
vendor/bin/phpstan analyse
```

### Log Files

| Log | Location | Purpose |
|-----|----------|---------|
| **Application** | `storage/logs/laravel.log` | General application logs |
| **Queue** | `storage/logs/queue.log` | Queue worker logs |
| **Audit** | Database `audits` table | User action audit trail |

---

## Appendix

### Environment Variables Reference

```env
# Application
APP_NAME=ICTServe
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ictserve.motac.gov.my

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=ictserve_user
DB_PASSWORD=********

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.motac.gov.my
MAIL_PORT=587

# Filament
FILAMENT_FILESYSTEM_DISK=public
```

### Support Contacts

- **Technical Support**: <ict@motac.gov.my>
- **System Administrator**: <admin@ictserve.motac.gov.my>
- **Emergency**: 03-8000 8000 ext. 1234

---

**Document Compliance**: D00-D15, WCAG 2.2 AA, PDPA 2010  
**Technology Stack**: Laravel 12.x | Livewire 3.x | Filament 4 | Tailwind CSS 4.1
