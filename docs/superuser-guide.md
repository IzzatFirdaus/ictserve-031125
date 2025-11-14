# ICTServe Superuser Guide

## Table of Contents

1. [Superuser Overview](#superuser-overview)
2. [User Management](#user-management)
3. [System Configuration](#system-configuration)
4. [Security Management](#security-management)
5. [Performance Monitoring](#performance-monitoring)
6. [Email & Notification Management](#email--notification-management)
7. [Audit & Compliance](#audit--compliance)
8. [Backup & Recovery](#backup--recovery)
9. [Advanced Troubleshooting](#advanced-troubleshooting)


---

## Superuser Overview

As a superuser, you have complete administrative control over the ICTServe system. This includes user management, system configuration, security settings, and monitoring capabilities.

### Exclusive Superuser Features

- User account management and role assignment
- System configuration and settings
- Security policy management
- Performance monitoring and optimization
- Audit log access and compliance reporting
- Email template and notification management
- Two-factor authentication management
- System backup and recovery operations


---

## User Management

### Accessing User Management

1. Navigate to **Users** in the admin panel
2. Only superusers can access this section


### Creating New Users

1. Click **Create User**
2. Fill required information:
   - **Name**: Full name of the user
   - **Email**: Must be unique in the system
   - **Password**: Must meet security requirements
   - **Role**: Select appropriate role (Staff, Approver, Admin, Superuser)
   - **Division**: User's department
   - **Grade**: Government service grade (if applicable)
   - **Position**: Job title


3. Set account status:
   - **Active**: User can log in and use the system
   - **Inactive**: Account disabled but preserved
   - **Suspended**: Temporarily disabled


4. Click **Create User**


### Managing User Roles

#### Available Roles

- **Staff**: Basic portal access for submissions
- **Approver**: Can approve loan applications (Grade 41+)
- **Admin**: Full helpdesk and asset management
- **Superuser**: Complete system administration


#### Changing User Roles

1. Find the user in the list
2. Click **Edit**
3. Select new role from dropdown
4. **Important**: System prevents removing the last superuser
5. Save changes


### User Account Security

#### Password Requirements

- Minimum 8 characters
- Must contain uppercase and lowercase letters
- Must contain at least one number
- Must contain at least one special character
- Cannot be a common password


#### Account Lockout Policy

- 5 failed login attempts trigger lockout
- Lockout duration: 15 minutes
- Superusers can manually unlock accounts


#### Two-Factor Authentication

1. Navigate to **Security** → **Two-Factor Authentication**
2. View 2FA status for all users
3. Force 2FA setup for specific roles
4. Generate emergency bypass codes


---

## System Configuration

### Accessing System Settings

1. Go to **System Configuration**
2. Available only to superusers


### General Settings

#### Application Settings

- **System Name**: Display name for the application
- **Default Language**: System-wide language preference
- **Timezone**: Server timezone setting
- **Maintenance Mode**: Enable/disable system access


#### Email Configuration

- **SMTP Settings**: Mail server configuration
- **From Address**: Default sender email
- **Reply-To Address**: Support email address
- **Email Queue**: Enable/disable queued email delivery


### SLA Configuration

#### Helpdesk SLA Thresholds

1. Navigate to **SLA Settings**
2. Configure by priority level:
   - **Critical**: 2 hours
   - **High**: 4 hours
   - **Medium**: 24 hours
   - **Low**: 72 hours


3. Set escalation rules:
   - **Warning**: 75% of SLA time elapsed
   - **Breach**: SLA time exceeded
   - **Auto-escalate**: Automatic priority increase


#### Asset Loan Policies

- **Maximum Loan Duration**: Default and limits by asset type
- **Approval Requirements**: Grade-based approval matrix
- **Overdue Penalties**: Late return policies
- **Damage Assessment**: Condition evaluation criteria


### Approval Matrix Configuration

1. Go to **Approval Matrix**
2. Configure approval rules:
   - **By Asset Value**: Monetary thresholds
   - **By User Grade**: Government service grades
   - **By Asset Type**: Category-specific rules
   - **By Duration**: Loan period requirements


3. Set approver assignment:
   - **Automatic**: Based on organizational hierarchy
   - **Manual**: Specific user assignment
   - **Round-robin**: Distribute load evenly


---

## Security Management

### Security Dashboard

Access comprehensive security overview:

- Failed login attempts
- Account lockouts
- Permission changes
- Security policy violations
- Two-factor authentication status


### Access Control

#### Role-Based Permissions

1. Navigate to **Permissions**
2. Configure permissions by role:
   - **View**: Read-only access
   - **Create**: Add new records
   - **Update**: Modify existing records
   - **Delete**: Remove records
   - **Export**: Download data


#### IP Restrictions

1. Go to **Security** → **IP Restrictions**
2. Configure allowed IP ranges:
   - **Whitelist**: Only specified IPs allowed
   - **Blacklist**: Block specific IPs
   - **Geographic**: Restrict by country/region


### Data Encryption

#### Encryption Settings

1. Access **Security** → **Encryption**
2. Configure encryption for:
   - **Personal Data**: IC numbers, phone numbers
   - **Approval Tokens**: Email approval links
   - **Two-Factor Secrets**: TOTP keys
   - **Backup Codes**: Recovery codes


#### Key Management

- **Encryption Keys**: Rotate encryption keys
- **Key Backup**: Secure key storage
- **Key Recovery**: Emergency access procedures


### Security Monitoring

#### Real-time Alerts

Configure alerts for:

- Multiple failed login attempts
- Privilege escalation attempts
- Unusual access patterns
- Data export activities
- Configuration changes


#### Security Reports

Generate reports for:

- **Access Log**: User login/logout activities
- **Permission Changes**: Role and access modifications
- **Failed Attempts**: Security violation attempts
- **Compliance Status**: Security policy adherence


---

## Performance Monitoring

### Performance Dashboard

Access **Performance Monitoring** for:

- **Response Times**: Page load performance
- **Database Queries**: Query execution times
- **Memory Usage**: System resource utilization
- **Cache Performance**: Hit/miss ratios
- **Queue Status**: Background job processing


### Core Web Vitals Monitoring

Track key performance metrics:

- **LCP (Largest Contentful Paint)**: Target < 2.5s
- **FID (First Input Delay)**: Target < 100ms
- **CLS (Cumulative Layout Shift)**: Target < 0.1


### Database Optimization

#### Query Performance

1. Navigate to **Performance** → **Database**
2. Monitor:
   - **Slow Queries**: Queries taking > 1 second
   - **N+1 Problems**: Inefficient relationship loading
   - **Missing Indexes**: Unoptimized table scans
   - **Lock Contention**: Database blocking issues


#### Optimization Actions

- **Add Indexes**: Create database indexes for slow queries
- **Query Optimization**: Rewrite inefficient queries
- **Cache Configuration**: Adjust cache settings
- **Connection Pooling**: Optimize database connections


### System Resources

#### Server Monitoring

- **CPU Usage**: Processor utilization
- **Memory Usage**: RAM consumption
- **Disk Space**: Storage utilization
- **Network I/O**: Bandwidth usage


#### Capacity Planning

- **Growth Trends**: Resource usage over time
- **Peak Usage**: High-traffic periods
- **Scaling Recommendations**: Infrastructure upgrades
- **Bottleneck Identification**: Performance constraints


---

## Email & Notification Management

### Email Queue Monitoring

1. Access **Email Queue Monitoring**
2. Monitor queue status:
   - **Pending**: Emails waiting to be sent
   - **Processing**: Currently being delivered
   - **Failed**: Delivery failures
   - **Completed**: Successfully sent


### Email Template Management

#### Template Configuration

1. Navigate to **Email Templates**
2. Customize templates for:
   - **Ticket Created**: New helpdesk ticket notifications
   - **Loan Approved**: Application approval notifications
   - **Asset Overdue**: Late return reminders
   - **Maintenance Due**: Asset maintenance alerts


#### Template Variables

Available variables for personalization:

- `{user_name}`: Recipient's name
- `{ticket_number}`: Helpdesk ticket reference
- `{asset_name}`: Equipment name
- `{due_date}`: Return or deadline date
- `{approval_link}`: Email approval URL


### Notification Settings

#### Delivery Preferences

Configure notification delivery:

- **Email**: SMTP delivery
- **SMS**: Text message alerts (if configured)
- **In-App**: System notifications
- **Push**: Browser notifications


#### Escalation Rules

Set up automatic escalations:

- **Time-based**: After specified duration
- **Priority-based**: By ticket/application priority
- **Role-based**: Escalate to higher authority
- **Condition-based**: Based on specific criteria


---

## Audit & Compliance

### Audit Log Management

#### Accessing Audit Logs

1. Navigate to **Audit Logs**
2. Filter by:
   - **User**: Specific user actions
   - **Action Type**: Create, Update, Delete
   - **Resource**: Affected system component
   - **Date Range**: Time period
   - **IP Address**: Source location


#### Audit Trail Analysis

Review critical activities:

- **User Management**: Account creation/modification
- **Permission Changes**: Role assignments
- **Data Access**: Sensitive information viewing
- **Configuration Changes**: System modifications
- **Security Events**: Login failures, lockouts


### Compliance Reporting

#### PDPA 2010 Compliance

Generate reports for:

- **Data Access**: Who accessed personal data
- **Data Retention**: Data storage duration
- **Data Deletion**: Purged records
- **Consent Management**: User permissions


#### Retention Policies

Configure data retention:

- **Audit Logs**: 7 years (legal requirement)
- **Email Logs**: 1 year
- **User Activity**: 2 years
- **System Logs**: 6 months


### Data Export & Purging

#### Data Export

1. Go to **Data Management** → **Export**
2. Select data types:
   - **User Data**: Account information
   - **Activity Logs**: System usage
   - **Audit Records**: Compliance data
   - **Configuration**: System settings


#### Data Purging

Configure automatic purging:

- **Closed Tickets**: After 2 years
- **Completed Loans**: After 1 year
- **Inactive Users**: After 3 years
- **Old Logs**: Based on retention policy


---

## Backup & Recovery

### Backup Configuration

#### Automated Backups

1. Navigate to **Backup Settings**
2. Configure backup schedule:
   - **Daily**: Database and critical files
   - **Weekly**: Full system backup
   - **Monthly**: Archive backup
   - **On-demand**: Manual backup triggers


#### Backup Storage

Configure backup destinations:

- **Local Storage**: Server disk space
- **Network Storage**: Shared drives
- **Cloud Storage**: AWS S3, Azure Blob
- **Offsite Storage**: Remote locations


### Recovery Procedures

#### Database Recovery

1. Access **Recovery** → **Database**
2. Select recovery point
3. Choose recovery type:
   - **Full Restore**: Complete database replacement
   - **Partial Restore**: Specific tables only
   - **Point-in-Time**: Restore to specific timestamp


#### File Recovery

1. Navigate to **Recovery** → **Files**
2. Select file categories:
   - **Application Files**: System code
   - **User Uploads**: Attachments and documents
   - **Configuration**: Settings and preferences
   - **Logs**: System and audit logs


### Disaster Recovery

#### Recovery Planning

- **RTO (Recovery Time Objective)**: 4 hours
- **RPO (Recovery Point Objective)**: 1 hour
- **Backup Verification**: Weekly restore tests
- **Documentation**: Updated recovery procedures


#### Emergency Procedures

1. **Assess Damage**: Determine scope of issue
2. **Activate Plan**: Follow disaster recovery checklist
3. **Restore Systems**: Use latest verified backup
4. **Verify Integrity**: Test all system functions
5. **Resume Operations**: Return to normal service


---

## Advanced Troubleshooting

### System Diagnostics

#### Health Checks

1. Access **System Health**
2. Review system status:
   - **Database Connectivity**: Connection status
   - **Email Service**: SMTP functionality
   - **Queue Processing**: Background jobs
   - **File System**: Disk space and permissions
   - **External APIs**: Third-party integrations


#### Performance Analysis

Use built-in tools:

- **Query Analyzer**: Database performance
- **Memory Profiler**: RAM usage patterns
- **Cache Inspector**: Cache effectiveness
- **Log Analyzer**: Error pattern detection


### Common Issues & Solutions

#### High CPU Usage

**Symptoms**: Slow response times, timeouts
**Diagnosis**:

1. Check **Performance** → **System Resources**
2. Identify resource-intensive processes
3. Review recent configuration changes


**Solutions**:

- Optimize database queries
- Increase server resources
- Enable caching
- Review background job processing


#### Database Connection Issues

**Symptoms**: Connection timeouts, database errors
**Diagnosis**:

1. Check database server status
2. Review connection pool settings
3. Examine network connectivity


**Solutions**:

- Restart database service
- Adjust connection limits
- Optimize query performance
- Check firewall settings


#### Email Delivery Problems

**Symptoms**: Emails not being sent or received
**Diagnosis**:

1. Check **Email Queue Monitoring**
2. Review SMTP configuration
3. Examine email logs


**Solutions**:

- Verify SMTP credentials
- Check spam filters
- Review email templates
- Test with different email providers


### Emergency Contacts

- **System Administrator**: <admin@motac.gov.my>
- **Database Administrator**: <dba@motac.gov.my>
- **Network Administrator**: <network@motac.gov.my>
- **Security Officer**: <security@motac.gov.my>


---

*Last Updated: January 6, 2025*
*Version: 3.0.0*
