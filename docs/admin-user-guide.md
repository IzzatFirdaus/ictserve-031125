# ICTServe Admin User Guide

## Table of Contents

1. [Getting Started](#getting-started)
2. [Dashboard Overview](#dashboard-overview)
3. [Helpdesk Management](#helpdesk-management)
4. [Asset Loan Management](#asset-loan-management)
5. [Asset Inventory](#asset-inventory)
6. [Reporting and Analytics](#reporting-and-analytics)
7. [Common Workflows](#common-workflows)
8. [Troubleshooting](#troubleshooting)

---

## Getting Started

### Accessing the Admin Panel

1. Navigate to `https://your-domain.com/admin`
2. Enter your email and password
3. Complete two-factor authentication if enabled
4. You will be redirected to the admin dashboard

### User Roles and Permissions

- **Admin**: Full access to helpdesk and asset loan management
- **Superuser**: All admin permissions plus user management and system configuration

---

## Dashboard Overview

The admin dashboard provides a comprehensive overview of system activity:

### Key Metrics Widgets

- **Open Tickets**: Number of unresolved helpdesk tickets
- **Pending Approvals**: Loan applications awaiting approval
- **Active Loans**: Currently issued equipment
- **System Alerts**: Important notifications requiring attention

### Quick Actions

- Create new helpdesk ticket
- Process loan application
- View recent activity
- Access reports

---

## Helpdesk Management

### Viewing Tickets

1. Click **Helpdesk Tickets** in the navigation menu
2. Use filters to narrow down results:
   - Status (Open, In Progress, Resolved, Closed)
   - Priority (Low, Medium, High, Critical)
   - Category (Hardware, Software, Network, etc.)
   - Date range

### Processing Tickets

#### Assigning Tickets

1. Click the **Assign** action on a ticket
2. Select the staff member from the dropdown
3. Add assignment notes if needed
4. Click **Assign**

#### Updating Ticket Status

1. Click **Edit** on the ticket
2. Change the status field
3. Add progress notes in the comments section
4. Click **Save**

#### Adding Comments

1. Open the ticket details
2. Scroll to the **Comments** section
3. Type your comment in the text area
4. Select visibility (Internal/Public)
5. Click **Add Comment**

### Bulk Operations

1. Select multiple tickets using checkboxes
2. Choose from bulk actions:
   - Update Status
   - Assign to User
   - Update Priority
   - Export Selected

---

## Asset Loan Management

### Viewing Applications

1. Navigate to **Loan Applications**
2. Filter by:
   - Status (Pending, Approved, Issued, Returned)
   - Approval Status
   - Asset Type
   - Date Range

### Processing Applications

#### Approving Applications

1. Click **Approve** on a pending application
2. Review asset availability
3. Set loan conditions if needed
4. Click **Confirm Approval**

#### Rejecting Applications

1. Click **Reject** on a pending application
2. Select rejection reason from dropdown
3. Add detailed explanation
4. Click **Confirm Rejection**

#### Issuing Equipment

1. Find approved applications
2. Click **Issue** action
3. Verify asset condition
4. Record any pre-existing issues
5. Confirm issuance

#### Processing Returns

1. Locate issued loans
2. Click **Return** action
3. Inspect returned equipment
4. Select condition:
   - Good: No issues found
   - Fair: Minor wear and tear
   - Damaged: Requires repair
5. Add return notes
6. If damaged, maintenance ticket is automatically created

### Asset Conflict Detection

The system automatically checks for scheduling conflicts:

- Red indicator: Asset unavailable for requested period
- Yellow indicator: Potential conflict requiring review
- Green indicator: Asset available

---

## Asset Inventory

### Managing Assets

#### Adding New Assets

1. Go to **Assets** → **Create**
2. Fill required information:
   - Asset Code (auto-generated if blank)
   - Name and Description
   - Category
   - Serial Number
   - Purchase Information
3. Upload asset photo if available
4. Set initial status and location
5. Click **Create**

#### Updating Asset Information

1. Find the asset in the list
2. Click **Edit**
3. Update necessary fields
4. Save changes

#### Asset Status Management

- **Available**: Ready for loan
- **On Loan**: Currently borrowed
- **Maintenance**: Under repair
- **Retired**: No longer in service

### Maintenance Tracking

1. Assets requiring maintenance show warning indicators
2. Click **Maintenance** action to:
   - Schedule maintenance
   - Record maintenance activities
   - Update asset condition
   - Set return to service date

---

## Reporting and Analytics

### Available Reports

#### Helpdesk Reports

- **SLA Compliance**: Ticket resolution times vs targets
- **Category Analysis**: Issues by type and frequency
- **Staff Performance**: Resolution rates by assignee
- **Trend Analysis**: Ticket volume over time

#### Asset Loan Reports

- **Utilization Report**: Asset usage statistics
- **Overdue Report**: Late returns and follow-up actions
- **Damage Report**: Asset condition trends
- **Popular Assets**: Most requested equipment

### Generating Reports

1. Navigate to **Reports**
2. Select report type
3. Set date range and filters
4. Choose output format (PDF, Excel, CSV)
5. Click **Generate Report**

### Scheduled Reports

1. Go to **Reports** → **Scheduled**
2. Click **Create Schedule**
3. Configure:
   - Report type
   - Frequency (Daily, Weekly, Monthly)
   - Recipients
   - Delivery method
4. Save schedule

---

## Common Workflows

### Workflow 1: Processing Urgent Ticket

1. **Identify**: High/Critical priority tickets appear at top of list
2. **Assign**: Immediately assign to appropriate technician
3. **Escalate**: If needed, change priority and notify supervisor
4. **Track**: Monitor progress through status updates
5. **Close**: Verify resolution with user before closing

### Workflow 2: Equipment Loan Process

1. **Review**: Check application details and asset availability
2. **Approve**: Verify user eligibility and loan duration
3. **Prepare**: Ensure asset is ready for pickup
4. **Issue**: Record condition and provide user instructions
5. **Monitor**: Track return date and send reminders
6. **Return**: Inspect equipment and update records

### Workflow 3: Damaged Equipment Handling

1. **Report**: User reports damage during return process
2. **Assess**: Evaluate damage severity and repair needs
3. **Document**: Take photos and record detailed description
4. **Ticket**: System automatically creates maintenance ticket
5. **Repair**: Coordinate with technical team or vendor
6. **Return**: Update asset status when repairs complete

---

## Troubleshooting

### Common Issues

#### Cannot Access Admin Panel

**Symptoms**: Forbidden error or redirect to login
**Solutions**:

1. Verify your user account has admin or superuser role
2. Clear browser cache and cookies
3. Check if your account is active
4. Contact superuser if role assignment needed

#### Slow Performance

**Symptoms**: Pages load slowly or timeout
**Solutions**:

1. Check your internet connection
2. Clear browser cache
3. Try different browser
4. Contact IT if problem persists

#### Email Notifications Not Received

**Symptoms**: Users not receiving ticket/loan notifications
**Solutions**:

1. Check spam/junk folders
2. Verify email addresses are correct
3. Check email queue status in admin panel
4. Contact system administrator

#### Export Functions Not Working

**Symptoms**: Export buttons don't respond or fail
**Solutions**:

1. Ensure popup blockers are disabled
2. Try smaller date ranges for large datasets
3. Check browser download settings
4. Use different export format

### Getting Help

#### Internal Support

1. **Documentation**: Check this guide and system help
2. **Colleagues**: Ask other admin users
3. **Superuser**: Contact system administrator

#### Technical Support

1. **System Issues**: Contact IT department
2. **Bug Reports**: Use internal ticketing system
3. **Feature Requests**: Submit through proper channels

### Best Practices

#### Daily Tasks

- [ ] Review overnight tickets and assign priorities
- [ ] Check overdue loan returns
- [ ] Monitor system alerts and notifications
- [ ] Update ticket statuses based on progress

#### Weekly Tasks

- [ ] Generate SLA compliance report
- [ ] Review asset utilization statistics
- [ ] Clean up closed tickets older than 30 days
- [ ] Check for assets requiring maintenance

#### Monthly Tasks

- [ ] Generate comprehensive reports for management
- [ ] Review user access and permissions
- [ ] Analyze trends and identify improvement areas
- [ ] Update asset inventory and valuations

---

## Keyboard Shortcuts

| Action | Shortcut |
|--------|----------|
| Global Search | `Ctrl + K` |
| Create New Ticket | `Ctrl + N` |
| Refresh Current Page | `F5` |
| Navigate to Dashboard | `Alt + D` |
| Open Help | `F1` |

---

## Contact Information

- **System Administrator**: <admin@motac.gov.my>
- **IT Support**: <ict@bpm.gov.my>
- **Emergency Contact**: +603-1234-5678

---

*Last Updated: January 6, 2025*
*Version: 3.0.0*
