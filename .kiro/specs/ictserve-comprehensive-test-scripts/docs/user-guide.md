# ICTServe Automation Scripts - User Guide

## Quick Start

### Prerequisites

Before running the ICTServe automation scripts, ensure you have:

- **PowerShell 7.x** for cross-platform automation script execution
- **Google Chrome** or **Microsoft Edge** browser installed
- **Network access** to the ICTServe system (development, testing, or production environment)
- **Test credentials** configured in the `config/credentials.json` file
- **Selenium WebDriver** for browser automation (automatically downloaded if needed)

### Getting Started in 3 Steps

1. **Navigate to the automation scripts directory**

   ```cmd
   cd ictserve-automation-scripts
   ```

2. **Run the main menu**

   ```powershell
   # PowerShell users
   .\Main-Menu.ps1
   ```

3. **Select your testing scenario**
   - Choose from 9 main categories
   - Select individual scripts or automated operations
   - Configure visual demonstration mode if needed

## Main Menu System

### Interactive Menu Navigation

The main menu provides easy access to all 347+ automation scripts organized by category:

```
===============================================
    ICTServe Comprehensive Automation Suite v1.0
    Frontend + Backend + Integration Testing
===============================================

Environment: [Testing] | User: [admin@motac.gov.my] | Scripts: [347 Total] | Mode: [Visual Demo]

Main Categories:
1. Guest User Workflows                    [50 Scripts - Frontend + Backend]
2. Authenticated User Workflows            [67 Scripts - Enhanced Features]  
3. Admin Panel Operations (Filament)       [78 Scripts - Complete Admin Suite]
4. AI Integration Testing                  [89 Scripts - Cloud Hybrid Architecture]
5. API Integration & Backend Systems       [89 Scripts - Complete Backend Testing]
6. Performance & Accessibility Testing     [45 Scripts - Standards Compliance]
7. Security & Compliance Testing          [52 Scripts - PDPA + Security]
8. System Monitoring & Health             [38 Scripts - Laravel Pulse/Horizon/Telescope]
9. End-to-End Workflow Testing            [29 Scripts - Complete User Journeys]

Select option (1-9): _
```

### Menu Features

- **🔍 Search Functionality**: Find scripts by keyword across all categories
- **📋 Execution History**: View and re-run previously executed scripts
- **⚙️ Configuration Management**: Set environment, credentials, and execution options
- **📊 Automated Operations**: Run multiple scripts or complete categories
- **🎬 Visual Demo Modes**: Configure live browser demonstrations
- **📈 Reporting**: Generate execution reports and analytics

## Script Categories Overview

### 1. Guest User Workflows (50 Scripts)

Test all functionality available to users without authentication:

**Helpdesk Ticket Workflows (20 Scripts)**

- Basic ticket submission with form validation
- File attachment upload with virus scanning
- Ticket tracking and status updates
- Email notification testing

**Asset Loan Workflows (20 Scripts)**

- Loan application submission
- Asset availability checking
- Date conflict validation
- Approval workflow initiation

**Integration Testing (10 Scripts)**

- ClamAV virus scanning integration
- Email gateway functionality
- Database transaction integrity
- Queue processing validation

### 2. Authenticated User Workflows (67 Scripts)

Test enhanced features for logged-in users:

**Authentication & Session Management (15 Scripts)**

- Email and username login options
- Google Workspace SSO integration
- Password reset and security features
- Session management and timeout handling

**Dashboard & Real-Time Features (12 Scripts)**

- Live dashboard widgets and statistics
- Real-time notifications via WebSocket
- Quick action buttons and shortcuts
- Mobile-responsive dashboard testing

**Enhanced Helpdesk & Loan Features (35 Scripts)**

- Auto-filled forms with profile data
- Ticket and loan history management
- Real-time comments and collaboration
- Advanced search and filtering

**Profile Management (5 Scripts)**

- Profile updates and data synchronization
- Notification preferences
- Account linking and data migration

### 3. Admin Panel Operations (78 Scripts)

Test comprehensive administrative functionality:

**Admin Authentication & Access Control (10 Scripts)**

- Role-based access control
- Multi-role permission system
- Admin activity logging and audit trails

**Ticket & Asset Management (55 Scripts)**

- Complete ticket lifecycle management
- Asset inventory and maintenance scheduling
- Loan application processing and approval
- Bulk operations and analytics

**User Management & Reporting (13 Scripts)**

- User account creation and management
- Custom report generation
- Compliance and audit reporting

### 4. AI Integration Testing (89 Scripts)

Test the Cloud Hybrid AI Architecture:

**Ollama Local AI (20 Scripts)**

- Local LLM server connectivity
- FAQ bot responses and accuracy
- Sensitive data processing (PKS 4.2 compliance)
- Model management and performance

**AWS Bedrock Cloud AI (20 Scripts)**

- Claude model integration (Opus, Sonnet, Haiku, Nova)
- Intelligent model routing
- DLP filtering and data classification
- Cost optimization and monitoring

**Advanced AI Features (49 Scripts)**

- Conversation management and persistence
- Streaming responses via Server-Sent Events
- Web-augmented responses with DuckDuckGo
- MCP server integration and AI assistant tools

### 5. API Integration & Backend Systems (89 Scripts)

Test all backend systems and integrations:

**Laravel Sanctum API (15 Scripts)**

- API authentication and token management
- Rate limiting and security validation
- CORS configuration and versioning

**External System Integration (59 Scripts)**

- HRMIS integration and user synchronization
- Email gateway and notification system
- ClamAV virus scanning integration
- WebSocket real-time communication
- Redis caching and session management
- Database integration and performance

**Queue Monitoring (15 Scripts)**

- Laravel Horizon queue management
- Job processing and failure handling
- Performance monitoring and analytics

### 6. Performance & Accessibility Testing (45 Scripts)

Ensure standards compliance:

**Core Web Vitals (15 Scripts)**

- Largest Contentful Paint (LCP) optimization
- First Input Delay (FID) measurement
- Cumulative Layout Shift (CLS) validation
- Overall performance optimization

**WCAG 2.2 AA Compliance (20 Scripts)**

- Keyboard navigation and accessibility
- Screen reader compatibility
- Color contrast and visual accessibility
- Form and content accessibility

**Cross-Browser Compatibility (10 Scripts)**

- Chrome, Firefox, Safari, Edge testing
- Mobile browser compatibility
- Responsive design validation

### 7. Security & Compliance Testing (52 Scripts)

Validate security measures and compliance:

**Security Validation (25 Scripts)**

- CSRF protection and input sanitization
- SQL injection and XSS prevention
- Authentication and session security
- File upload security validation

**PDPA Compliance (15 Scripts)**

- Personal data protection validation
- Consent management and data minimization
- Data retention and portability
- Privacy by design implementation

**Penetration Testing (12 Scripts)**

- Authentication and authorization bypass testing
- Business logic vulnerability assessment
- Security vulnerability scanning

### 8. System Monitoring & Health (38 Scripts)

Test monitoring and debugging tools:

**Laravel Pulse (12 Scripts)**

- Performance monitoring dashboard
- Real-time metrics and analytics
- Slow query and request monitoring

**Laravel Horizon (13 Scripts)**

- Queue monitoring and worker management
- Job processing and failure handling
- Performance metrics and scaling

**Laravel Telescope (8 Scripts)**

- Debugging interface (superuser access)
- Request and database monitoring
- Exception tracking and analysis

**System Health (5 Scripts)**

- Overall system health checks
- Service dependency monitoring
- Resource utilization tracking

### 9. End-to-End Workflow Testing (29 Scripts)

Test complete business processes:

**Complete Helpdesk Workflows (10 Scripts)**

- Guest to resolution lifecycle
- Authenticated user workflows
- Multi-department ticket handling

**Complete Loan Workflows (10 Scripts)**

- Application to return process
- Multi-level approval workflows
- Asset maintenance and transfer

**Cross-Module Integration (9 Scripts)**

- Helpdesk to loans integration
- Complete user journey testing
- Data consistency validation

## Execution Modes

### Standard Execution

Run scripts for functional testing and validation:

```powershell
# Run individual script
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1

# Run with specific environment
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Environment Testing
```

### Visual Demonstration Mode

Run scripts with live browser automation for training and presentations:

```powershell
# Visual mode - live browser window
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Mode Visual

# Demo mode - slower with annotations
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Mode Demo

# Interactive mode - pauses for explanation
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Mode Interactive

# Recording mode - captures video
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Mode Recording -OutputPath ".\videos\"
```

### Automated Operations

Run multiple scripts or complete categories:

```powershell
# Run all scripts in a category
.\scripts\guest-workflows\menu.ps1 -RunAll

# Run critical path tests only
.\Main-Menu.ps1 -CriticalPath

# Run complete test suite
.\Run-All.ps1
```

## Configuration Management

### Environment Configuration

Configure different environments in `config/environments.json`:

```json
{
  "development": {
    "baseUrl": "http://localhost:8000",
    "database": "ictserve_dev",
    "debug": true
  },
  "testing": {
    "baseUrl": "https://test.ictserve.motac.gov.my",
    "database": "ictserve_test",
    "debug": false
  },
  "staging": {
    "baseUrl": "https://staging.ictserve.motac.gov.my",
    "database": "ictserve_staging",
    "debug": false
  },
  "production": {
    "baseUrl": "https://ictserve.motac.gov.my",
    "database": "ictserve_prod",
    "debug": false
  }
}
```

### Credential Management

Configure test credentials in `config/credentials.json` (encrypted):

```json
{
  "guest": {
    "name": "Ahmad bin Abdullah",
    "email": "ahmad.test@motac.gov.my",
    "phone": "03-1234-5678",
    "department": "Bahagian Pengurusan Maklumat"
  },
  "authenticated": {
    "email": "demo.user@motac.gov.my",
    "password": "SecurePassword123!",
    "username": "demo.user"
  },
  "admin": {
    "email": "admin@motac.gov.my",
    "password": "AdminPassword123!",
    "role": "administrator"
  },
  "approver": {
    "email": "approver@motac.gov.my",
    "password": "ApproverPassword123!",
    "grade": "41"
  }
}
```

### Browser Configuration

Configure browser settings in `config/browser-settings.json`:

```json
{
  "defaultBrowser": "chrome",
  "headless": false,
  "windowSize": {
    "width": 1920,
    "height": 1080
  },
  "timeout": 30000,
  "implicitWait": 10000,
  "pageLoadTimeout": 60000,
  "downloadPath": "./downloads",
  "userAgent": "ICTServe-Automation-Suite/1.0"
}
```

### Visual Demo Configuration

Configure demonstration settings in `config/demo-settings.json`:

```json
{
  "visualMode": {
    "highlightElements": true,
    "showMouseCursor": true,
    "addAnnotations": true,
    "takeScreenshots": true,
    "recordVideo": false,
    "pauseAtSteps": ["Login", "FormSubmit", "Results"],
    "annotationDelay": 2000,
    "stepDelay": 1500,
    "showNetworkActivity": true,
    "logUserActions": true
  },
  "recording": {
    "format": "mp4",
    "quality": "high",
    "frameRate": 30,
    "outputPath": "./videos"
  },
  "screenshots": {
    "format": "png",
    "quality": 100,
    "outputPath": "./screenshots"
  }
}
```

## Advanced Usage

### Custom Test Suites

Create custom combinations of tests:

```powershell
# Create custom test suite
.\Main-Menu.ps1 -CreateSuite "Critical-Path" -Scripts @(
    "guest-workflows/helpdesk/submit-basic-ticket",
    "authenticated-workflows/authentication/test-email-login",
    "admin-operations/ticket-management/test-ticket-assignment"
)

# Run custom test suite
.\Main-Menu.ps1 -RunSuite "Critical-Path"
```

### Scheduled Execution

Set up automated test execution:

```powershell
# Schedule daily execution
.\Main-Menu.ps1 -Schedule Daily -Time "02:00" -Suite "Complete-Suite" -EmailReport

# Schedule weekly comprehensive testing
.\Main-Menu.ps1 -Schedule Weekly -Day Monday -Time "01:00" -Suite "Full-Regression"
```

### CI/CD Integration

Integrate with continuous integration pipelines:

```powershell
# Run in CI/CD mode (headless, fast, with reporting)
.\Run-All.ps1 -Mode CI -OutputFormat JUnit -ReportPath "./test-results"

# Run specific category for pull request validation
.\scripts\guest-workflows\menu.ps1 -RunAll -Mode CI -FailFast
```

### Performance Monitoring

Monitor script execution performance:

```powershell
# Run with performance monitoring
.\Main-Menu.ps1 -MonitorPerformance -BenchmarkMode -OutputPath "./performance-reports"

# Compare performance across environments
.\Main-Menu.ps1 -CompareEnvironments "testing,staging" -Suite "Performance-Suite"
```

## Reporting and Analytics

### Execution Reports

Generate comprehensive execution reports:

```powershell
# Generate detailed HTML report
.\Main-Menu.ps1 -GenerateReport HTML -IncludeScreenshots -IncludePerformanceMetrics

# Generate executive summary
.\Main-Menu.ps1 -GenerateReport Executive -EmailTo "management@motac.gov.my"

# Generate technical report with logs
.\Main-Menu.ps1 -GenerateReport Technical -IncludeLogs -IncludeErrorDetails
```

### Analytics Dashboard

Access real-time analytics:

```powershell
# Start analytics dashboard
.\utilities\reporting-utilities.ps1 -StartDashboard -Port 8080

# View execution trends
.\utilities\reporting-utilities.ps1 -ViewTrends -Period "30days"

# Analyze failure patterns
.\utilities\reporting-utilities.ps1 -AnalyzeFailures -Category "Authentication"
```

## Troubleshooting

### Common Issues

**1. Browser Driver Issues**

```powershell
# Update browser drivers
.\utilities\browser-automation.ps1 -UpdateDrivers

# Check driver compatibility
.\utilities\browser-automation.ps1 -CheckCompatibility
```

**2. Network Connectivity Issues**

```powershell
# Test connectivity to ICTServe
.\utilities\common-functions.ps1 -TestConnectivity -Environment Testing

# Check API endpoints
.\utilities\api-helpers.ps1 -TestEndpoints -Environment Testing
```

**3. Authentication Issues**

```powershell
# Validate credentials
.\utilities\config-loader.ps1 -ValidateCredentials -Environment Testing

# Reset test data
.\utilities\data-generators.ps1 -ResetTestData -Environment Testing
```

**4. Performance Issues**

```powershell
# Check system resources
.\utilities\common-functions.ps1 -CheckSystemResources

# Optimize execution settings
.\utilities\common-functions.ps1 -OptimizeSettings -Mode Performance
```

### Debug Mode

Run scripts in debug mode for detailed troubleshooting:

```powershell
# Enable debug mode
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Debug -Verbose

# Enable trace mode for detailed logging
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Trace -LogLevel Detailed
```

### Log Analysis

Analyze execution logs:

```powershell
# View recent logs
.\utilities\reporting-utilities.ps1 -ViewLogs -Recent 24hours

# Search logs for specific errors
.\utilities\reporting-utilities.ps1 -SearchLogs -Pattern "authentication failed"

# Export logs for analysis
.\utilities\reporting-utilities.ps1 -ExportLogs -Format CSV -DateRange "2024-01-01,2024-01-31"
```

## Best Practices

### Script Execution

1. **Environment Validation**: Always validate the target environment before running scripts
2. **Credential Security**: Use encrypted credentials and rotate them regularly
3. **Resource Management**: Monitor system resources during automated operations
4. **Error Handling**: Review error logs and implement fixes promptly
5. **Documentation**: Keep execution logs and reports for audit purposes

### Visual Demonstrations

1. **Preparation**: Test all demo scripts before presentations
2. **Audience Consideration**: Adjust demo speed and detail level for audience
3. **Backup Plans**: Have alternative scenarios ready for technical issues
4. **Engagement**: Use interactive pauses for questions and explanations
5. **Follow-up**: Provide recorded sessions and documentation after demos

### Maintenance

1. **Regular Updates**: Keep scripts updated with system changes
2. **Performance Monitoring**: Monitor script execution times and optimize
3. **Coverage Analysis**: Ensure comprehensive test coverage across all features
4. **Feedback Integration**: Incorporate user feedback and improvement suggestions
5. **Version Control**: Use Git for script version management and collaboration

## Support and Resources

### Documentation

- **Script Inventory**: Complete listing of all 347+ scripts
- **Visual Demo Guide**: Detailed guide for demonstration capabilities
- **Configuration Guide**: Environment and credential setup instructions
- **Troubleshooting Guide**: Common issues and solutions
- **Training Materials**: Presentation templates and training resources

### Community and Support

- **Internal Support**: Contact the ICTServe development team
- **Documentation Updates**: Submit feedback and improvement suggestions
- **Training Sessions**: Schedule training sessions for new team members
- **Best Practice Sharing**: Share successful automation patterns and techniques

### Continuous Improvement

The automation suite is continuously improved based on:

- **User Feedback**: Regular feedback collection and implementation
- **System Updates**: Adaptation to ICTServe system changes and enhancements
- **Technology Evolution**: Integration of new testing tools and methodologies
- **Performance Optimization**: Ongoing optimization for speed and reliability
- **Coverage Enhancement**: Addition of new test scenarios and edge cases

---

*This user guide provides comprehensive instructions for using the ICTServe Comprehensive Automation Suite. For additional support or questions, please contact the development team or refer to the detailed documentation in the docs/ directory.*
