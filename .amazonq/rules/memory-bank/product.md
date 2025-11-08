# ICTServe - Product Overview

## Project Identity

**ICTServe** is an internal ICT management system for BPM MOTAC (Ministry of Tourism, Arts and Culture Malaysia) staff. It provides a comprehensive platform for managing ICT helpdesk tickets and asset loan applications with full accessibility compliance and bilingual support.

**Version**: 3.0.0  
**Status**: Production  
**License**: MIT  
**Organization**: BPM MOTAC (Bahagian Pengurusan MOTAC)

## Purpose & Value Proposition

ICTServe solves critical operational challenges for MOTAC's ICT department:

1. **Streamlined Helpdesk Operations**: Replaces manual ticket tracking with automated SLA monitoring, assignment workflows, and email notifications
2. **Asset Loan Management**: Digitizes asset borrowing process with email-based approval workflows and automated return reminders
3. **Accessibility First**: WCAG 2.2 AA compliant interface ensures all MOTAC staff can access services regardless of ability
4. **Bilingual Support**: Full Bahasa Melayu and English localization for government compliance
5. **Audit Trail**: Comprehensive logging for accountability and compliance with government regulations

## Core Modules

### 1. Helpdesk Module (📞)
**Purpose**: ICT support ticket management system

**Key Features**:

- Guest submission forms (no login required for staff)
- Authenticated user portal with ticket history
- File attachments (images, PDFs, documents up to 5MB)
- SLA tracking with automatic escalation at 25% threshold
- Priority-based ticket routing (Critical, High, Medium, Low)
- Category-based assignment (Hardware, Software, Network, Email, Other)
- Real-time status updates via email notifications
- Internal comments for staff collaboration
- Cross-module integration with asset management

**User Workflows**:

- **Guest Flow**: Submit ticket → Receive confirmation email → Track via unique URL
- **Authenticated Flow**: Login → View dashboard → Submit/track tickets → Claim guest tickets by email
- **Admin Flow**: Triage tickets → Assign to agents → Update status → Close with resolution

### 2. Asset Loan Module (💼)
**Purpose**: ICT asset borrowing and return management

**Key Features**:

- Guest loan application forms
- Authenticated user portal with loan history
- Email-based dual approval workflow (Grade 41+ approvers)
- Asset availability checking and reservation
- Automated return reminders (7 days, 3 days, 1 day before due)
- Overdue notifications with escalation
- Asset condition tracking (Good, Fair, Damaged)
- Cross-module integration (damaged assets trigger helpdesk tickets)
- Loan transaction history and audit trail

**User Workflows**:

- **Guest Flow**: Submit application → Receive approval email → Approve via email link → Collect asset
- **Authenticated Flow**: Login → Browse available assets → Submit application → Track approval status
- **Approver Flow**: Receive email → Review application → Approve/reject via email link
- **Admin Flow**: Manage assets → Process returns → Handle damaged assets

### 3. Admin Panel (🎛️)
**Purpose**: Filament 4-based administration interface

**Key Features**:

- Dashboard with real-time statistics and charts
- User management with role-based access control (Staff, Approver, Admin, Superuser)
- Asset catalog management (categories, specifications, status)
- Ticket management (assignment, status updates, bulk actions)
- Loan application processing (approval, rejection, asset preparation)
- Reporting and analytics (SLA compliance, asset utilization, user activity)
- Email template management
- System configuration and settings
- Audit log viewer with filtering and export

**Admin Roles**:

- **Staff**: Submit tickets/loans, view own submissions
- **Approver**: Grade 41+ staff who can approve loan applications
- **Admin**: Full access to helpdesk and asset management
- **Superuser**: System-wide access including user management and configuration

## Target Users

### Primary Users

1. **MOTAC Staff (Guest Users)**:
   - All MOTAC employees needing ICT support or asset loans
   - No account required for basic submissions
   - Can claim submissions later by authenticating with email

2. **Authenticated Staff**:
   - MOTAC employees with system accounts
   - Access to personal dashboard and submission history
   - Can manage notification preferences

3. **ICT Helpdesk Agents**:
   - Triage and resolve support tickets
   - Assign tickets to specialists
   - Update ticket status and add internal notes

4. **Asset Managers**:
   - Manage asset catalog and availability
   - Process loan applications and returns
   - Track asset condition and maintenance

5. **Approvers (Grade 41+)**:
   - Review and approve/reject loan applications
   - Receive email notifications for pending approvals
   - Approve via email links (no login required)

6. **System Administrators**:
   - Configure system settings and workflows
   - Manage user accounts and permissions
   - Generate reports and analytics
   - Monitor system health and performance

## Key Capabilities

### Accessibility (♿)

- **WCAG 2.2 AA Compliance**: All pages tested with axe-core
- **Screen Reader Support**: ARIA labels, landmarks, and live regions
- **Keyboard Navigation**: Full functionality without mouse
- **Focus Management**: Visible focus indicators (2px outline)
- **Color Contrast**: Minimum 4.5:1 for text, 3:1 for UI components
- **Responsive Design**: Mobile-first approach (320px to 2560px)
- **Touch Targets**: Minimum 44x44px for interactive elements

### Localization (🌐)

- **Bilingual Interface**: Bahasa Melayu (primary) and English
- **Language Switcher**: Persistent cookie-based preference
- **Auto-detection**: Accept-Language header support
- **Translation Coverage**: 100% of UI strings (54 translation keys across 19 files)
- **Date/Time Formatting**: Localized formats (d/m/Y for Malaysia)

### Performance (⚡)

- **Core Web Vitals**: 90+ Lighthouse score
- **First Contentful Paint**: < 1.5s
- **Largest Contentful Paint**: < 2.5s
- **Cumulative Layout Shift**: < 0.1
- **Optimizations**: Asset minification, lazy loading, Redis caching, database indexing

### Security (🔒)

- **CSRF Protection**: All forms protected
- **Rate Limiting**: Prevents spam and abuse
- **Input Sanitization**: XSS prevention
- **Audit Trail**: All actions logged with user, timestamp, IP
- **Role-Based Access Control**: Spatie Laravel Permission package
- **Secure File Upload**: Validation, size limits, private storage
- **Two-Factor Authentication**: Optional for admin accounts

### Email System (📧)

- **Queue-Based**: Laravel Queue with Redis driver
- **60-Second SLA**: Email delivery within 60 seconds
- **Retry Mechanism**: 3 attempts with exponential backoff
- **Email Templates**: 12 Mail classes for different notifications
- **Bilingual Emails**: Bahasa Melayu and English versions
- **WCAG Compliant**: Accessible email templates
- **Email Logging**: All emails logged with status tracking

## Integration Points

### Cross-Module Integration

- **Asset-Ticket Linking**: Damaged assets automatically create helpdesk tickets
- **Loan-Helpdesk Integration**: Maintenance tickets linked to loan applications
- **Unified Analytics**: Combined reporting across modules
- **Shared User Context**: Single user profile across modules

### External Systems (Future)

- **HRMIS Integration**: Staff data synchronization
- **Calendar Integration**: Outlook/Google Calendar for loan schedules
- **SMS Gateway**: SMS notifications for critical updates
- **Active Directory**: LDAP authentication for MOTAC staff

## Technical Highlights

### Modern Stack

- **Laravel 12**: Latest PHP framework with streamlined structure
- **Livewire 3**: Reactive components without JavaScript complexity
- **Filament 4**: Server-driven UI for admin panel
- **Alpine.js 3**: Lightweight JavaScript for interactivity
- **Tailwind CSS 3**: Utility-first styling with dark mode support
- **Vite 7**: Fast build tool with HMR

### Development Experience

- **Laravel Boost**: AI-assisted development with MCP server
- **PHPStan Level 5**: Static analysis for type safety
- **Laravel Pint**: PSR-12 code formatting
- **Playwright**: E2E testing with accessibility checks
- **PHPUnit 11**: Comprehensive unit and feature tests

### Deployment

- **Environment Support**: Development, staging, production
- **Automated Setup**: `composer run setup` for one-command installation
- **CI/CD Ready**: GitHub Actions workflows for testing and deployment
- **Docker Support**: Laravel Sail for containerized development

## Success Metrics

### Operational Efficiency

- **Ticket Resolution Time**: Average 24-48 hours (target: < 72 hours)
- **SLA Compliance**: 95%+ tickets resolved within SLA
- **Asset Utilization**: 80%+ asset availability rate
- **User Satisfaction**: 4.5/5 average rating (future implementation)

### Accessibility Compliance

- **WCAG 2.2 AA**: 100% compliance across all pages
- **Automated Testing**: 0 critical accessibility violations
- **Manual Testing**: Screen reader compatibility verified

### Performance

- **Core Web Vitals**: 90+ Lighthouse score maintained
- **Uptime**: 99.9% availability target
- **Response Time**: < 200ms average API response time

## Future Roadmap

### Phase 1 (Q1 2025) - Enhanced Helpdesk

- [ ] Hybrid helpdesk with authenticated user features
- [ ] Advanced SLA management with custom thresholds
- [ ] Knowledge base integration
- [ ] AI-powered ticket categorization

### Phase 2 (Q2 2025) - Advanced Asset Management

- [ ] Asset maintenance scheduling
- [ ] Barcode/QR code scanning
- [ ] Asset lifecycle tracking
- [ ] Predictive maintenance alerts

### Phase 3 (Q3 2025) - AI Integration

- [ ] Ollama-powered chatbot for common queries
- [ ] Automated ticket routing with ML
- [ ] Sentiment analysis for user feedback
- [ ] Predictive analytics for asset demand

### Phase 4 (Q4 2025) - External Integrations

- [ ] HRMIS staff data synchronization
- [ ] Active Directory authentication
- [ ] Calendar integration (Outlook/Google)
- [ ] SMS gateway for critical notifications

## Competitive Advantages

1. **Government-Specific**: Built for Malaysian government workflows and compliance
2. **Accessibility First**: WCAG 2.2 AA compliance from day one (rare in government systems)
3. **Bilingual Native**: Not an afterthought - full BM/EN support
4. **Modern Stack**: Latest Laravel 12, Livewire 3, Filament 4 (cutting-edge)
5. **Email-Based Approvals**: No login required for approvers (reduces friction)
6. **Cross-Module Integration**: Unified system instead of siloed modules
7. **Comprehensive Audit Trail**: Government compliance and accountability
8. **Open Source**: MIT license allows customization and community contributions

## Contact & Support

**For MOTAC Staff**:

- Email: <ict@bpm.gov.my>
- Phone: +603-1234-5678
- Hours: Monday-Friday, 8:00 AM - 5:00 PM

**For Developers**:

- GitHub: <https://github.com/IzzatFirdaus/ictserve-031125>
- Documentation: docs/ folder (D00-D15 comprehensive guides)
- Issues: GitHub Issues for bug reports and feature requests
