# Design Document

## Overview

This design document outlines the architecture and approach for creating comprehensive PowerShell (.ps1) automation scripts for the ICTServe system. The solution implements a multi-layered automation strategy that performs all user workflows, system interactions, and advanced features including AI capabilities through web automation, API calls, and direct system commands.

## Architecture

### Automation Script Stack

**Core Automation Technologies:**

- **PowerShell 7.x**: Cross-platform automation scripts for Windows/Linux environments
- **Selenium WebDriver**: Web browser automation for user interface interactions with visual demonstration
- **cURL/Invoke-RestMethod**: HTTP API automation for backend service calls

**Visual Demonstration Features:**

- **Live Browser Automation**: Scripts run in visible Chrome/Edge browser windows showing real user interactions
- **Demonstration Mode**: Slower execution with highlights and annotations for end-user viewing
- **Screenshot Capture**: Automatic screenshots at key workflow steps for documentation
- **Video Recording**: Optional screen recording of complete workflow demonstrations
- **Interactive Pausing**: Ability to pause automation for explanation or user questions

**Specialized Automation Tools:**

- **PowerShell WebDriver Module**: Browser automation specifically for PowerShell with visual demonstration capabilities
- **Selenium Grid**: Multi-browser testing with live visual feedback
- **Browser Developer Tools Integration**: Console logging and network monitoring during demonstrations
- **Screen Recording Tools**: OBS Studio integration for workflow video capture
- **Annotation Overlays**: Visual highlights, arrows, and text annotations during automation
- **JSON/XML Processing**: Data manipulation and API response handling
- **File System Operations**: Document upload/download and file management automation

### Visual Demonstration System

**Browser Automation Modes:**

- **Headless Mode**: Fast execution without browser window (for CI/CD and automated testing)
- **Visual Mode**: Visible browser window showing live automation for demonstrations
- **Demo Mode**: Slower execution with visual highlights and step-by-step annotations
- **Interactive Mode**: Pauses at key steps allowing presenter to explain functionality
- **Recording Mode**: Captures video of entire workflow for training and documentation

**Visual Enhancement Features:**

```powershell
# Example Visual Demo Configuration
$DemoConfig = @{
    Mode = "Visual"                    # Headless, Visual, Demo, Interactive, Recording
    BrowserWindow = "Maximized"        # Maximized, Windowed, Fullscreen
    ExecutionSpeed = "Demo"            # Fast, Normal, Demo, Slow
    HighlightElements = $true          # Highlight form fields and buttons being interacted with
    ShowMouseCursor = $true            # Display animated mouse cursor movements
    AddAnnotations = $true             # Show text annotations explaining each step
    TakeScreenshots = $true            # Capture screenshots at key workflow points
    RecordVideo = $false               # Record entire session as MP4 video
    PauseAtSteps = @("Login", "FormSubmit", "Results")  # Interactive pause points
    AnnotationDelay = 2000             # Milliseconds to show annotations
    StepDelay = 1500                   # Milliseconds between automation steps
    ShowNetworkActivity = $true        # Display API calls and responses in console
    LogUserActions = $true             # Log each user action with timestamps
}

# Example Visual Demo Execution
Start-VisualDemo -Config $DemoConfig -Workflow "GuestHelpdeskTicket" -ShowInBrowser "Chrome"
```

**Demonstration Workflow Example:**

```powershell
# Visual Demo: Guest User Helpdesk Ticket Submission
===============================================
    Live Browser Demonstration
===============================================

🌐 Opening Chrome Browser...
📍 Navigating to: https://ictserve.motac.gov.my
⏸️  [PAUSE] - Showing ICTServe homepage to audience

👤 Demonstrating Guest User Workflow:
📝 Clicking "Submit Helpdesk Ticket" button
⏸️  [PAUSE] - Explaining guest access vs authenticated access

📋 Filling Helpdesk Form:
   ✏️  Name: "Ahmad bin Abdullah" 
   ✏️  Email: "ahmad.test@motac.gov.my"
   ✏️  Department: "Bahagian Pengurusan Maklumat"
   ✏️  Category: "Hardware Issue"
   ✏️  Priority: "Medium"
   ✏️  Description: "Laptop screen flickering intermittently"
⏸️  [PAUSE] - Showing form validation in real-time

📎 Uploading Test File:
   📁 Selecting file: "screenshot-issue.png"
   🔍 Showing ClamAV virus scanning process
   ✅ File upload successful
⏸️  [PAUSE] - Explaining file security measures

🚀 Submitting Form:
   🔄 Showing loading spinner
   📧 Email notification sent
   🎫 Ticket #TK-2024-001234 created
   ✅ Success message displayed
⏸️  [PAUSE] - Explaining ticket tracking process

📊 Backend Verification:
   🔍 Checking database entry
   📧 Verifying email queue processing
   📋 Confirming admin notification
⏸️  [PAUSE] - Showing backend processes

Demo Complete! ✨
📸 Screenshots saved to: ./demo-screenshots/
📹 Video recording: ./demo-videos/helpdesk-guest-workflow.mp4
```

    BrowserMode = "Visual"           # Headless, Visual, Demo, Interactive, Recording
    ExecutionSpeed = "Slow"          # Fast, Normal, Slow, Step-by-Step
    Highlights = $true               # Visual element highlighting
    Annotations = $true              # Text overlays and arrows
    Screenshots = $true              # Capture key workflow steps
    VideoRecording = $false          # Screen recording (optional)
    PauseOnErrors = $true           # Stop on failures for explanation
    ShowNetworkCalls = $true        # Display API calls in console
    ShowDatabaseQueries = $false    # Display SQL queries (optional)
}

```text

**Live Demonstration Features:**

- **Element Highlighting**: Automatic highlighting of form fields, buttons, and interactive elements
- **Action Annotations**: Text overlays explaining what action is being performed
- **Data Display**: Show the test data being entered in real-time
- **Progress Indicators**: Visual progress bar showing workflow completion
- **Error Visualization**: Clear display of validation errors and system responses
- **Success Confirmations**: Visual confirmation of successful operations

**Preset Data Demonstration:**

```powershell
# Example Demo Data for Guest User Helpdesk Ticket
$DemoData = @{
    TicketType = "Hardware Issue"
    Priority = "Medium"
    Subject = "Laptop keyboard not working properly"
    Description = "Several keys on my laptop keyboard are not responding. This started yesterday after a Windows update."
    Department = "Information Management Division (BPM)"
    ContactEmail = "demo.user@motac.gov.my"
    ContactPhone = "03-1234-5678"
    AttachmentFile = "keyboard-issue-photo.jpg"
    ExpectedOutcome = "Repair or replacement of laptop keyboard"
}
```

**Interactive Presentation Features:**

- **Pause Controls**: Spacebar to pause/resume, arrow keys to step through
- **Speed Controls**: Adjust automation speed during demonstration
- **Zoom Features**: Automatic zooming to focus on specific form elements
- **Callout Boxes**: Explanatory text boxes that appear during key actions
- **Before/After Comparisons**: Show system state before and after operations

**Main Menu System:**

- **Main-Menu.ps1**: Interactive PowerShell menu for all automation scripts
- **Run-All.ps1**: Execute all scripts in sequence with reporting

**Script Categories:**

- **Guest User Scripts**: Automation for non-authenticated workflows (.ps1)
- **Authenticated User Scripts**: Login-required workflow automation (.ps1)
- **Admin Panel Scripts**: Administrative operation automation (.ps1)
- **API Integration Scripts**: Backend service interaction automation (.ps1)
- **System Monitoring Scripts**: Health check and monitoring automation (.ps1)
- **AI Integration Scripts**: AI service interaction automation (.ps1)

**Script Structure:**

```text
ictserve-automation-scripts/
├── Main-Menu.ps1                    # Interactive PowerShell main menu (ROOT ONLY)
├── Run-All.ps1                      # Execute all scripts sequentially (ROOT ONLY)
├── README.md                        # Quick start guide and overview (ROOT ONLY)
├── docs/                            # Comprehensive documentation
│   ├── script-inventory.md          # Complete listing of all 347+ scripts
│   ├── visual-demo-scripts.md       # Scripts with visual demonstration capabilities
│   ├── user-guide.md               # Detailed usage instructions and examples
│   ├── configuration-guide.md      # Environment setup and credential management
│   ├── troubleshooting-guide.md    # Common issues and solutions
│   └── training-materials.md       # Presentation and training resources
├── scripts/                         # All automation scripts organized by category
│   ├── guest-workflows/            # Guest user automation scripts (50 scripts)
│   │   ├── menu.ps1                # Category-specific PowerShell menu
│   │   ├── helpdesk/               # Helpdesk ticket workflows (20 scripts)
│   │   │   ├── submit-basic-ticket.ps1
│   │   │   ├── submit-ticket-with-attachments.ps1
│   │   │   ├── test-form-validation.ps1
│   │   │   ├── track-ticket-by-number.ps1
│   │   │   ├── track-ticket-by-email.ps1
│   │   │   └── ... (additional helpdesk scripts)
│   │   ├── asset-loans/            # Asset loan workflows (20 scripts)
│   │   │   ├── submit-basic-loan-request.ps1
│   │   │   ├── check-asset-availability.ps1
│   │   │   ├── test-date-conflicts.ps1
│   │   │   ├── track-loan-status.ps1
│   │   │   └── ... (additional loan scripts)
│   │   └── integration-tests/      # System integration tests (10 scripts)
│   │       ├── test-clamav-scanning.ps1
│   │       ├── test-email-notifications.ps1
│   │       └── ... (additional integration scripts)
│   ├── authenticated-workflows/     # Authenticated user automation scripts (67 scripts)
│   │   ├── menu.ps1                # Category-specific PowerShell menu
│   │   ├── authentication/         # Login and session management (15 scripts)
│   │   │   ├── test-email-login.ps1
│   │   │   ├── test-username-login.ps1
│   │   │   ├── test-google-sso.ps1
│   │   │   ├── test-password-reset.ps1
│   │   │   └── ... (additional auth scripts)
│   │   ├── dashboard/              # Dashboard and real-time features (12 scripts)
│   │   │   ├── test-dashboard-widgets.ps1
│   │   │   ├── test-real-time-updates.ps1
│   │   │   ├── test-notification-center.ps1
│   │   │   └── ... (additional dashboard scripts)
│   │   ├── enhanced-helpdesk/      # Enhanced helpdesk features (20 scripts)
│   │   │   ├── test-auto-filled-forms.ps1
│   │   │   ├── test-ticket-history.ps1
│   │   │   ├── test-ticket-comments.ps1
│   │   │   └── ... (additional enhanced helpdesk scripts)
│   │   ├── enhanced-loans/         # Enhanced loan management (15 scripts)
│   │   │   ├── test-enhanced-application.ps1
│   │   │   ├── test-loan-history.ps1
│   │   │   ├── test-pickup-otp.ps1
│   │   │   └── ... (additional enhanced loan scripts)
│   │   └── profile-management/     # Profile and account management (5 scripts)
│   │       ├── test-profile-updates.ps1
│   │       ├── test-notification-preferences.ps1
│   │       └── test-account-linking.ps1
│   ├── admin-operations/           # Admin panel automation scripts (78 scripts)
│   │   ├── menu.ps1                # Category-specific PowerShell menu
│   │   ├── authentication/         # Admin authentication and access (10 scripts)
│   │   │   ├── test-admin-login.ps1
│   │   │   ├── test-role-permissions.ps1
│   │   │   └── ... (additional admin auth scripts)
│   │   ├── ticket-management/      # Helpdesk ticket administration (20 scripts)
│   │   │   ├── test-ticket-assignment.ps1
│   │   │   ├── test-bulk-operations.ps1
│   │   │   ├── test-ticket-analytics.ps1
│   │   │   └── ... (additional ticket management scripts)
│   │   ├── asset-management/       # Asset inventory administration (20 scripts)
│   │   │   ├── test-asset-registration.ps1
│   │   │   ├── test-maintenance-scheduling.ps1
│   │   │   ├── test-asset-transfers.ps1
│   │   │   └── ... (additional asset management scripts)
│   │   ├── loan-processing/        # Loan application administration (15 scripts)
│   │   │   ├── test-loan-approval.ps1
│   │   │   ├── test-asset-assignment.ps1
│   │   │   └── ... (additional loan processing scripts)
│   │   ├── user-management/        # User administration (8 scripts)
│   │   │   ├── test-user-creation.ps1
│   │   │   ├── test-role-assignment.ps1
│   │   │   └── ... (additional user management scripts)
│   │   └── reporting/              # Analytics and reporting (5 scripts)
│   │       ├── test-report-generation.ps1
│   │       ├── test-dashboard-analytics.ps1
│   │       └── test-compliance-reports.ps1
│   ├── ai-integration/             # AI service automation scripts (89 scripts)
│   │   ├── menu.ps1                # Category-specific PowerShell menu
│   │   ├── ollama-local/           # Local AI server testing (20 scripts)
│   │   │   ├── test-ollama-connectivity.ps1
│   │   │   ├── test-model-loading.ps1
│   │   │   ├── test-faq-responses.ps1
│   │   │   └── ... (additional Ollama scripts)
│   │   ├── aws-bedrock/            # Cloud AI service testing (20 scripts)
│   │   │   ├── test-bedrock-connectivity.ps1
│   │   │   ├── test-claude-models.ps1
│   │   │   ├── test-model-routing.ps1
│   │   │   └── ... (additional Bedrock scripts)
│   │   ├── conversation-management/ # AI conversation features (15 scripts)
│   │   │   ├── test-conversation-creation.ps1
│   │   │   ├── test-conversation-persistence.ps1
│   │   │   └── ... (additional conversation scripts)
│   │   ├── streaming-responses/    # Real-time AI features (12 scripts)
│   │   │   ├── test-sse-streaming.ps1
│   │   │   ├── test-stream-interruption.ps1
│   │   │   └── ... (additional streaming scripts)
│   │   ├── web-augmented/          # Web search integration (12 scripts)
│   │   │   ├── test-duckduckgo-integration.ps1
│   │   │   ├── test-search-filtering.ps1
│   │   │   └── ... (additional web search scripts)
│   │   └── mcp-integration/        # MCP server testing (10 scripts)
│   │       ├── test-mcp-connectivity.ps1
│   │       ├── test-ai-assistant-tools.ps1
│   │       └── ... (additional MCP scripts)
│   ├── api-backend/                # API and backend system testing (89 scripts)
│   │   ├── menu.ps1                # Category-specific PowerShell menu
│   │   ├── sanctum-api/            # Laravel Sanctum API testing (15 scripts)
│   │   │   ├── test-api-authentication.ps1
│   │   │   ├── test-token-management.ps1
│   │   │   └── ... (additional API scripts)
│   │   ├── hrmis-integration/      # HRMIS external system testing (15 scripts)
│   │   │   ├── test-hrmis-connectivity.ps1
│   │   │   ├── test-user-sync.ps1
│   │   │   └── ... (additional HRMIS scripts)
│   │   ├── email-gateway/          # Email system testing (12 scripts)
│   │   │   ├── test-smtp-configuration.ps1
│   │   │   ├── test-email-templates.ps1
│   │   │   └── ... (additional email scripts)
│   │   ├── clamav-scanning/        # Virus scanning testing (10 scripts)
│   │   │   ├── test-clamav-integration.ps1
│   │   │   ├── test-file-scanning.ps1
│   │   │   └── ... (additional ClamAV scripts)
│   │   ├── websocket-realtime/     # WebSocket and real-time testing (12 scripts)
│   │   │   ├── test-websocket-server.ps1
│   │   │   ├── test-real-time-notifications.ps1
│   │   │   └── ... (additional WebSocket scripts)
│   │   ├── redis-caching/          # Redis and caching testing (10 scripts)
│   │   │   ├── test-redis-connectivity.ps1
│   │   │   ├── test-session-storage.ps1
│   │   │   └── ... (additional Redis scripts)
│   │   ├── database-integration/   # Database testing (10 scripts)
│   │   │   ├── test-database-connectivity.ps1
│   │   │   ├── test-transactions.ps1
│   │   │   └── ... (additional database scripts)
│   │   └── queue-monitoring/       # Laravel Horizon queue testing (5 scripts)
│   │       ├── test-horizon-dashboard.ps1
│   │       ├── test-job-processing.ps1
│   │       └── test-failed-jobs.ps1
│   ├── performance-accessibility/  # Performance and accessibility testing (45 scripts)
│   │   ├── menu.ps1                # Category-specific PowerShell menu
│   │   ├── core-web-vitals/        # Performance metrics testing (15 scripts)
│   │   │   ├── test-lcp-performance.ps1
│   │   │   ├── test-fid-performance.ps1
│   │   │   └── ... (additional performance scripts)
│   │   ├── wcag-compliance/        # Accessibility testing (20 scripts)
│   │   │   ├── test-keyboard-navigation.ps1
│   │   │   ├── test-screen-reader.ps1
│   │   │   └── ... (additional accessibility scripts)
│   │   └── cross-browser/          # Browser compatibility testing (10 scripts)
│   │       ├── test-chrome-compatibility.ps1
│   │       ├── test-firefox-compatibility.ps1
│   │       └── ... (additional browser scripts)
│   ├── security-compliance/        # Security and compliance testing (52 scripts)
│   │   ├── menu.ps1                # Category-specific PowerShell menu
│   │   ├── security-validation/    # Security testing (25 scripts)
│   │   │   ├── test-csrf-protection.ps1
│   │   │   ├── test-input-sanitization.ps1
│   │   │   └── ... (additional security scripts)
│   │   ├── pdpa-compliance/        # Data protection testing (15 scripts)
│   │   │   ├── test-data-protection.ps1
│   │   │   ├── test-audit-logging.ps1
│   │   │   └── ... (additional PDPA scripts)
│   │   └── penetration-testing/    # Security penetration testing (12 scripts)
│   │       ├── test-sql-injection.ps1
│   │       ├── test-xss-protection.ps1
│   │       └── ... (additional penetration scripts)
│   ├── monitoring-health/          # System monitoring and health testing (38 scripts)
│   │   ├── menu.ps1                # Category-specific PowerShell menu
│   │   ├── laravel-pulse/          # Performance monitoring testing (12 scripts)
│   │   │   ├── test-pulse-dashboard.ps1
│   │   │   ├── test-performance-metrics.ps1
│   │   │   └── ... (additional Pulse scripts)
│   │   ├── laravel-horizon/        # Queue monitoring testing (13 scripts)
│   │   │   ├── test-horizon-monitoring.ps1
│   │   │   ├── test-queue-workers.ps1
│   │   │   └── ... (additional Horizon scripts)
│   │   ├── laravel-telescope/      # Debugging interface testing (8 scripts)
│   │   │   ├── test-telescope-access.ps1
│   │   │   ├── test-debugging-interface.ps1
│   │   │   └── ... (additional Telescope scripts)
│   │   └── system-health/          # General system health testing (5 scripts)
│   │       ├── test-system-status.ps1
│   │       ├── test-service-health.ps1
│   │       └── test-resource-usage.ps1
│   └── end-to-end/                 # Complete workflow testing (29 scripts)
│       ├── menu.ps1                # Category-specific PowerShell menu
│       ├── complete-helpdesk/      # End-to-end helpdesk workflows (10 scripts)
│       │   ├── guest-to-resolution.ps1
│       │   ├── authenticated-to-resolution.ps1
│       │   └── ... (additional E2E helpdesk scripts)
│       ├── complete-loans/         # End-to-end loan workflows (10 scripts)
│       │   ├── application-to-return.ps1
│       │   ├── approval-workflow.ps1
│       │   └── ... (additional E2E loan scripts)
│       └── cross-module/           # Cross-module integration testing (9 scripts)
│           ├── helpdesk-to-loans.ps1
│           ├── user-journey-complete.ps1
│           └── ... (additional cross-module scripts)
├── utilities/                      # Common helper functions and utilities
│   ├── common-functions.ps1        # Shared PowerShell functions
│   ├── config-loader.ps1           # Configuration management
│   ├── browser-automation.ps1      # Web browser automation utilities
│   ├── api-helpers.ps1             # API interaction utilities
│   ├── data-generators.ps1         # Test data generation utilities
│   ├── visual-demo-helpers.ps1     # Visual demonstration utilities
│   └── reporting-utilities.ps1     # Test reporting and analytics utilities
├── config/                         # Configuration files and settings
│   ├── environments.json           # Environment configurations (dev/test/staging/prod)
│   ├── credentials.json            # Test credentials (encrypted)
│   ├── settings.json               # Script execution settings
│   ├── browser-settings.json       # Browser automation configurations
│   ├── demo-settings.json          # Visual demonstration configurations
│   └── ai-settings.json            # AI service configurations (Ollama/Bedrock)
├── test-data/                      # Test data and fixtures
│   ├── users/                      # User test data
│   ├── tickets/                    # Helpdesk ticket test data
│   ├── assets/                     # Asset and loan test data
│   ├── ai-conversations/           # AI conversation test data
│   └── documents/                  # Test documents and files
├── reports/                        # Generated reports and logs
│   ├── execution-logs/             # Script execution logs
│   ├── screenshots/                # Visual demonstration screenshots
│   ├── videos/                     # Recorded demonstration videos
│   └── analytics/                  # Performance and coverage analytics
└── examples/                       # Example configurations and usage
    ├── quick-start-examples/       # Getting started examples
    ├── advanced-configurations/    # Advanced setup examples
    └── training-scenarios/         # Training and presentation examples
```

### Script Data Management

**Interactive Menu System Design:**

**Main Menu Features:**

- **Numbered Selection**: Users select scripts by entering numbers
- **Category Browsing**: Navigate through script categories with sub-menus
- **Search Functionality**: Find scripts by name or description
- **Execution History**: Track previously run scripts and results
- **Configuration Management**: Set environment, credentials, and execution options
- **Help System**: Built-in help for each script with usage examples

**Menu Interface Design:**

```powershell
# Enhanced Main Menu Interface
===============================================
    ICTServe Comprehensive Automation Suite v1.0
    Frontend + Backend + Integration Testing
===============================================

Environment: [Testing] | User: [admin@motac.gov.my] | Scripts: [347 Total] | Mode: [Visual Demo]

🎭 DEMONSTRATION MODES:
   📺 Visual Mode: Live browser automation with visible interactions
   🎪 Demo Mode: Slower execution with highlights and annotations  
   🎤 Interactive Mode: Pauses for presenter explanation
   📹 Recording Mode: Captures video for training materials
   ⚡ Headless Mode: Fast execution without browser window

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

🎬 LIVE DEMONSTRATIONS:
10. Guest vs Authenticated Comparison      [Side-by-side browser windows]
11. Complete User Journey Demo             [End-to-end workflow with narration]
12. Admin Panel Feature Tour              [Guided tour of all admin features]
13. AI Integration Showcase               [Live AI responses and model routing]
14. Security Features Demo                [CSRF, validation, and protection measures]

Automated Operations:
15. Run All Critical Path Tests           [Essential User Journeys - 45 Scripts]
16. Run All Frontend Tests               [UI/UX Testing - 156 Scripts]
17. Run All Backend Tests                [API/Database/Integration - 191 Scripts]
18. Run Complete Test Suite              [All 347 Scripts - Full Coverage]

🎥 RECORDING & DOCUMENTATION:
19. Record Training Videos               [Capture workflows as MP4 for training]
20. Generate Demo Screenshots            [Step-by-step visual documentation]
21. Create Interactive Presentations     [PowerPoint with embedded recordings]
22. Export Demo Reports                  [Comprehensive demo documentation]

Utilities & Management:
14. Configuration Settings               [Environment + Credentials + Execution Options]
15. View Execution History              [Previous Runs + Results + Performance]
16. Generate Comprehensive Reports       [Test Results + Screenshots + Analytics]
17. Test Data Management                [Generate + Clean + Reset Test Data]
18. System Health Check                 [Prerequisites + Dependencies + Connectivity]

Advanced Features:
19. Search Tests by Keyword             [Find Specific Tests Across All Categories]
20. Custom Test Suites                  [Create + Save + Execute Custom Combinations]
21. Scheduled Test Execution            [Automated Runs + CI/CD Integration]
22. Performance Benchmarking           [Compare Results + Trend Analysis]

Help & Documentation:
23. Quick Start Guide                   [Getting Started + Basic Usage]
24. Troubleshooting Guide              [Common Issues + Solutions]
25. Test Coverage Report               [Requirements Mapping + Coverage Analysis]

0. Exit

Select option (0-25): _
```

**Detailed Category Sub-Menu Design:**

```powershell
# Example: Guest User Workflows - Comprehensive Testing
===============================================
    Guest User Workflows - Frontend & Backend Testing
    🎭 DEMO MODE: Visual Browser Automation Active
===============================================

🎬 DEMONSTRATION OPTIONS:
D1. Quick Demo (5 min)     - Essential guest workflows with narration
D2. Complete Demo (15 min) - All guest features with detailed explanation  
D3. Training Session      - Interactive demo with pause points for questions
D4. Video Recording       - Record complete workflow for training materials
D5. Screenshot Gallery    - Generate step-by-step visual documentation

🎯 EXECUTION MODES:
   📺 Visual: Live browser window showing real interactions
   🎪 Demo: Slower execution with highlights and annotations
   🎤 Interactive: Pauses at key steps for explanation
   📹 Recording: Captures video while running automation
   ⚡ Headless: Fast execution without browser (testing only)

HELPDESK TICKET WORKFLOWS:
1.  Submit Basic Helpdesk Ticket (Frontend Form + Backend API) 🎬
2.  Submit Ticket with File Attachments (Upload + Virus Scan + Storage) 🎬
3.  Submit Ticket with Multiple Categories (Dropdown + Validation) 🎬
4.  Test Form Validation Errors (Frontend JS + Backend Laravel Validation) 🎬
5.  Test CSRF Protection (Security + Session Management) 🔒
6.  Track Ticket Status by Number (Frontend Search + Backend Query) 🎬
7.  Track Ticket Status by Email (Email Lookup + Database Search) 🎬
8.  Test Email Notifications (Queue Processing + SMTP Integration) 📧
9.  Test Ticket Auto-Assignment (Business Logic + Database Updates) ⚙️
10. Test Emergency Priority Handling (Workflow + Notification Escalation) 🚨

ASSET LOAN WORKFLOWS:
11. Submit Basic Asset Loan Request (Form + Backend Processing) 🎬
12. Check Asset Availability Calendar (Frontend Calendar + Backend Scheduling) 🎬
13. Submit Loan with Date Conflicts (Validation + Error Handling) 🎬
14. Test Asset Category Selection (Dynamic Dropdowns + Database Queries) 🎬
15. Test Loan Duration Validation (Business Rules + Frontend Feedback) 🎬
16. Test Department Asset Restrictions (Authorization + Policy Enforcement) 🔒
17. Track Loan Application Status (Status Updates + Real-time Data) 🎬
18. Test Loan Approval Workflow Trigger (Email Generation + Queue Jobs) 📧
19. Test Asset Conflict Detection (Concurrent Booking + Database Locking) ⚙️
20. Test Loan Extension Requests (Workflow + Approval Chain) 🎬

🎬 = Available in Visual Demo Mode with live browser interaction
🔒 = Security testing with visual validation
📧 = Email workflow with live SMTP demonstration  
⚙️ = Backend process visualization with API monitoring
🚨 = Priority workflow with real-time notifications

VISUAL DEMO FEATURES:
- 🖱️  Animated mouse cursor showing click locations
- 🔍 Element highlighting when interacting with forms
- 💬 Real-time annotations explaining each step
- 📸 Automatic screenshots at key workflow points
- 🎥 Optional video recording for training materials
- ⏸️  Interactive pause points for presenter explanation
- 📊 Live backend API monitoring in browser console
- 🎨 Visual indicators for validation success/failure

Navigation:
0. Back to Main Menu
H. Help for this category  
S. Search specific test by keyword
V. Configure Visual Demo Settings

Select option (0-50, D1-D5, V): _

INTEGRATION & SYSTEM TESTING:
21. Test ClamAV File Scanning Integration (Upload + Virus Detection)
22. Test Email Gateway Integration (SMTP + Delivery Confirmation)
23. Test Database Transaction Integrity (ACID + Rollback Scenarios)
24. Test Laravel Queue Processing (Job Dispatch + Worker Processing)
25. Test Redis Session Management (Session Storage + Expiration)
26. Test Rate Limiting (API Protection + Throttling)
27. Test CORS Headers (Cross-Origin + Security)
28. Test Input Sanitization (XSS Protection + Data Cleaning)

PERFORMANCE & ACCESSIBILITY:
29. Test Page Load Performance (Core Web Vitals + Lighthouse)
30. Test Mobile Responsiveness (Viewport + Touch Interactions)
31. Test Keyboard Navigation (Accessibility + Tab Order)
32. Test Screen Reader Compatibility (ARIA + Semantic HTML)
33. Test Form Auto-completion (Browser Integration + UX)
34. Test Offline Behavior (Service Worker + Cache Management)

ERROR HANDLING & EDGE CASES:
35. Test Network Timeout Scenarios (Connection Failures + Retry Logic)
36. Test Database Connection Failures (Failover + Error Messages)
37. Test File Upload Size Limits (Validation + User Feedback)
38. Test Invalid File Type Uploads (Security + Error Handling)
39. Test Concurrent User Scenarios (Race Conditions + Data Integrity)
40. Test Memory Limit Scenarios (Large Files + Resource Management)

AUTOMATED OPERATIONS:
41. Run All Helpdesk Workflows (Complete Frontend + Backend Testing)
42. Run All Asset Loan Workflows (Complete Frontend + Backend Testing)
43. Run All Integration Tests (System-wide Connectivity Testing)
44. Run Critical Path Only (Essential User Journeys)
45. Run Performance Suite (All Performance + Accessibility Tests)
46. Run Security Suite (All Security + Validation Tests)
47. Run Complete Guest User Suite (All 40 Individual Tests)

UTILITIES & REPORTING:
48. Generate Detailed Test Report (Results + Screenshots + Logs)
49. Export Test Data for Analysis (CSV + JSON + Database Dumps)
50. Clean Test Data (Reset + Cleanup + Fresh State)

Navigation:
0. Back to Main Menu
H. Help for this category
S. Search specific test by keyword

Select option (0-50): _
```

```powershell
# Example: Authenticated User Workflows - Comprehensive Testing
===============================================
    Authenticated User Workflows - Frontend & Backend Testing
===============================================

AUTHENTICATION & SESSION MANAGEMENT:
1.  Test Email/Username Login (Multiple Auth Methods + Session Creation)
2.  Test Password Validation (Security Rules + Frontend Feedback)
3.  Test Remember Me Functionality (Persistent Sessions + Cookie Management)
4.  Test Password Reset Flow (Email + Token + Database Updates)
5.  Test Account Lockout Protection (Brute Force + Security Measures)
6.  Test Google Workspace SSO (OAuth2 + Domain Validation + User Provisioning)
7.  Test Session Timeout Handling (Auto-logout + Data Preservation)
8.  Test Concurrent Session Management (Multiple Devices + Session Limits)
9.  Test Profile Data Sync with HRMIS (External API + Data Mapping)
10. Test Two-Factor Authentication (If Implemented + Security Flow)

DASHBOARD & REAL-TIME FEATURES:
11. Test Dashboard Widget Loading (Data Aggregation + Performance)
12. Test Real-time Statistics Updates (WebSocket + Live Data)
13. Test Notification Center (Laravel Reverb + Real-time Alerts)
14. Test Quick Action Buttons (Navigation + Pre-filled Forms)
15. Test Keyboard Shortcuts (Accessibility + User Experience)
16. Test Dashboard Customization (User Preferences + Persistence)
17. Test Mobile Dashboard View (Responsive + Touch Optimization)
18. Test Dashboard Performance (Load Times + Data Caching)
19. Test WebSocket Connection Handling (Reconnection + Error Recovery)
20. Test Push Notification Integration (Browser Notifications + Permissions)

ENHANCED HELPDESK FEATURES:
21. Test Auto-filled Personal Information (Profile Integration + Form Population)
22. Test Ticket History View (Pagination + Filtering + Search)
23. Test Ticket Comments System (Real-time Updates + Notifications)
24. Test File Attachment to Existing Tickets (Upload + Association)
25. Test Ticket Priority Escalation (Business Rules + Workflow)
26. Test Ticket Assignment Requests (User Preferences + Routing)
27. Test Ticket Status Change Notifications (Real-time + Email)
28. Test Ticket Claiming from Guest Submissions (Account Linking)
29. Test Ticket Collaboration Features (Internal Comments + Team Access)
30. Test Ticket Resolution Feedback (Rating + Comments + Analytics)

ENHANCED ASSET LOAN FEATURES:
31. Test Enhanced Loan Application (Auto-fill + Advanced Options)
32. Test Real-time Asset Availability (Live Calendar + Conflict Detection)
33. Test Loan History Management (Complete Audit Trail + Analytics)
34. Test Loan Extension Requests (Workflow + Approval Integration)
35. Test Asset Pickup OTP System (Generation + Verification + Security)
36. Test Loan Return Process (Check-in + Condition Assessment)
37. Test Asset Maintenance Scheduling (Calendar Integration + Notifications)
38. Test Loan Approval Tracking (Real-time Status + Approver Notifications)
39. Test Asset Transfer Between Users (Workflow + Documentation)
40. Test Loan Cancellation Process (Business Rules + Refund Logic)

PROFILE & ACCOUNT MANAGEMENT:
41. Test Profile Viewing (Data Display + Privacy Controls)
42. Test Editable Field Updates (Validation + Persistence + Audit)
43. Test Read-only Field Correction Requests (Helpdesk Integration)
44. Test Notification Preferences (Settings + Real-time Application)
45. Test Account Linking (Guest to Authenticated + Data Migration)
46. Test Privacy Settings Management (PDPA Compliance + User Control)
47. Test Account Deactivation Process (Data Retention + Cleanup)
48. Test Profile Photo Management (Upload + Resize + Storage)
49. Test Contact Information Updates (Validation + Verification)
50. Test Department/Role Change Handling (Permission Updates + Workflow)

INTEGRATION & ADVANCED FEATURES:
51. Test HRMIS Data Synchronization (Scheduled Jobs + Error Handling)
52. Test Email Notification Preferences (Granular Controls + Delivery)
53. Test Calendar Integration (Outlook + Google + Scheduling)
54. Test Document Management (Upload + Categorization + Search)
55. Test Reporting Dashboard (Custom Reports + Data Export)
56. Test API Token Management (Generation + Revocation + Security)
57. Test Audit Trail Access (Personal Activity + Privacy Compliance)
58. Test System Announcement Viewing (Admin Messages + Acknowledgment)
59. Test Feedback System (Bug Reports + Feature Requests)
60. Test Help Documentation Access (Context-sensitive + Search)

AUTOMATED OPERATIONS:
61. Run All Authentication Tests (Complete Login/Session Testing)
62. Run All Dashboard Tests (Real-time Features + Performance)
63. Run All Enhanced Helpdesk Tests (Advanced User Features)
64. Run All Enhanced Asset Loan Tests (Advanced Loan Management)
65. Run All Profile Management Tests (Account + Privacy Features)
66. Run All Integration Tests (External Systems + APIs)
67. Run Complete Authenticated User Suite (All 60 Individual Tests)

Navigation:
0. Back to Main Menu
H. Help for this category
S. Search specific test by keyword

Select option (0-67): _
```

```powershell
# Example: Admin Panel Operations - Comprehensive Testing
===============================================
    Admin Panel Operations (Filament) - Frontend & Backend Testing
===============================================

ADMIN AUTHENTICATION & ACCESS CONTROL:
1.  Test Admin Panel Login (Role-based Access + Security)
2.  Test Multi-role Permission System (Granular Permissions + Enforcement)
3.  Test Admin Session Management (Extended Sessions + Security)
4.  Test Admin Activity Logging (Audit Trail + Compliance)
5.  Test Admin Password Policy (Enhanced Security + Complexity)
6.  Test Admin Account Lockout (Security + Recovery Process)
7.  Test Admin Role Assignment (Permission Management + Validation)
8.  Test Admin Panel Branding (Customization + White-labeling)
9.  Test Admin Notification System (System Alerts + Admin Messages)
10. Test Admin Help Documentation (Context-sensitive + Role-specific)

HELPDESK TICKET MANAGEMENT:
11. Test Ticket Queue Management (Assignment + Prioritization + Routing)
12. Test Ticket Status Updates (Workflow + State Management + Notifications)
13. Test Ticket Assignment System (Manual + Automatic + Load Balancing)
14. Test Ticket Escalation Rules (Time-based + Priority + Workflow)
15. Test Ticket Resolution Workflow (Process + Documentation + Closure)
16. Test Ticket Bulk Operations (Mass Updates + Batch Processing)
17. Test Ticket Search & Filtering (Advanced Queries + Performance)
18. Test Ticket Analytics Dashboard (Metrics + KPIs + Reporting)
19. Test Ticket SLA Management (Time Tracking + Breach Alerts)
20. Test Ticket Template Management (Predefined Responses + Automation)

ASSET INVENTORY MANAGEMENT:
21. Test Asset Registration (Complete Asset Lifecycle + Metadata)
22. Test Asset Category Management (Hierarchical Categories + Rules)
23. Test Asset Availability Calendar (Scheduling + Conflict Resolution)
24. Test Asset Maintenance Scheduling (Preventive + Corrective + Tracking)
25. Test Asset Transfer Management (Department Transfers + Approval)
26. Test Asset Condition Tracking (Status Updates + History + Photos)
27. Test Asset Depreciation Calculation (Financial + Accounting Integration)
28. Test Asset Barcode/QR Management (Generation + Scanning + Tracking)
29. Test Asset Location Tracking (Physical Location + Movement History)
30. Test Asset Disposal Process (End-of-life + Documentation + Compliance)

LOAN APPLICATION PROCESSING:
31. Test Loan Application Review (Approval Workflow + Decision Making)
32. Test Loan Approval Routing (Multi-level Approval + Delegation)
33. Test Loan Asset Assignment (Inventory Management + Allocation)
34. Test Loan Duration Management (Extensions + Modifications + Rules)
35. Test Loan Return Processing (Check-in + Condition Assessment + Billing)
36. Test Loan Violation Handling (Overdue + Damage + Penalty Processing)
37. Test Loan Analytics & Reporting (Usage Statistics + Trends + Forecasting)
38. Test Loan Bulk Operations (Mass Approvals + Batch Processing)
39. Test Loan Calendar Integration (Scheduling + Availability + Conflicts)
40. Test Loan Policy Management (Rules + Restrictions + Enforcement)

USER MANAGEMENT & ADMINISTRATION:
41. Test User Account Creation (Bulk Import + Individual + Validation)
42. Test User Role Assignment (Permission Management + Inheritance)
43. Test User Profile Management (Data Updates + Verification + Audit)
44. Test User Access Control (Feature Permissions + Data Access + Security)
45. Test User Activity Monitoring (Login Tracking + Usage Analytics)
46. Test User Account Suspension (Temporary + Permanent + Reactivation)
47. Test User Data Export (GDPR Compliance + Data Portability)
48. Test User Notification Management (System Messages + Announcements)
49. Test User Feedback Management (Reviews + Ratings + Response)
50. Test User Training & Onboarding (Documentation + Tutorials + Progress)

SYSTEM CONFIGURATION & SETTINGS:
51. Test System Settings Management (Global Configuration + Environment)
52. Test Email Template Management (Customization + Localization + Testing)
53. Test Notification Configuration (Channels + Preferences + Delivery)
54. Test Integration Settings (External APIs + Credentials + Testing)
55. Test Backup & Restore Operations (Data Protection + Recovery + Validation)
56. Test System Maintenance Mode (Scheduled Downtime + User Communication)
57. Test Performance Monitoring (System Health + Alerts + Optimization)
58. Test Security Configuration (Policies + Rules + Compliance)
59. Test Audit Log Management (Retention + Search + Export + Compliance)
60. Test System Update Management (Version Control + Deployment + Rollback)

REPORTING & ANALYTICS:
61. Test Custom Report Builder (Dynamic Reports + Filters + Visualization)
62. Test Scheduled Report Generation (Automation + Delivery + Storage)
63. Test Dashboard Analytics (KPIs + Metrics + Real-time Data)
64. Test Data Export Functions (Multiple Formats + Large Datasets)
65. Test Report Access Control (Permission-based + Data Security)
66. Test Report Performance Optimization (Caching + Indexing + Speed)
67. Test Compliance Reporting (Regulatory + Audit + Documentation)
68. Test Trend Analysis (Historical Data + Forecasting + Insights)
69. Test Real-time Monitoring (Live Dashboards + Alerts + Notifications)
70. Test Report Sharing (Distribution + Collaboration + Version Control)

AUTOMATED OPERATIONS:
71. Run All Admin Authentication Tests (Security + Access Control)
72. Run All Ticket Management Tests (Complete Helpdesk Administration)
73. Run All Asset Management Tests (Complete Inventory Administration)
74. Run All Loan Processing Tests (Complete Loan Administration)
75. Run All User Management Tests (Complete User Administration)
76. Run All System Configuration Tests (Complete System Administration)
77. Run All Reporting Tests (Complete Analytics + Reporting)
78. Run Complete Admin Panel Suite (All 70 Individual Tests)

Navigation:
0. Back to Main Menu
H. Help for this category
S. Search specific test by keyword

Select option (0-78): _
```

```powershell
# Example: AI Integration Testing - Comprehensive Frontend & Backend
===============================================
    AI Integration Testing - Cloud Hybrid Architecture
===============================================

OLLAMA LOCAL AI TESTING:
1.  Test Ollama Server Connectivity (Health Check + Model Loading)
2.  Test Local Model Management (Download + Update + Version Control)
3.  Test FAQ Bot Responses (RAG + Knowledge Base + Accuracy)
4.  Test Sensitive Data Processing (PKS 4.2 Compliance + Local Processing)
5.  Test Embedding Generation (Vector Database + Semantic Search)
6.  Test Conversation Context (Memory Management + Session Persistence)
7.  Test Local AI Performance (Response Time + Resource Usage)
8.  Test Model Switching (Dynamic Loading + Performance Impact)
9.  Test Offline AI Functionality (Network Independence + Reliability)
10. Test Local AI Security (Data Isolation + Access Control)

AWS BEDROCK CLOUD AI TESTING:
11. Test AWS Bedrock Connectivity (Authentication + Service Health)
12. Test Claude Model Integration (Opus + Sonnet + Haiku + Nova)
13. Test Model Routing Logic (Complexity-based + Performance Optimization)
14. Test DLP Filtering (PKS 9.2.1 Compliance + Data Classification)
15. Test Public Data Processing (Cloud Processing + Security Validation)
16. Test API Rate Limiting (Throttling + Queue Management + Fallback)
17. Test Cost Optimization (Model Selection + Usage Tracking + Budgets)
18. Test Multi-region Failover (Availability + Disaster Recovery)
19. Test Cloud AI Security (Encryption + Access Control + Audit)
20. Test Bedrock Model Performance (Response Quality + Speed + Accuracy)

INTELLIGENT MODEL ROUTING:
21. Test Data Sensitivity Detection (Classification + Routing Rules)
22. Test Query Complexity Analysis (Automatic Model Selection)
23. Test Fallback Mechanisms (Service Failures + Graceful Degradation)
24. Test Load Balancing (Request Distribution + Performance Optimization)
25. Test Cost-Performance Optimization (Model Selection + Budget Control)
26. Test Routing Rule Configuration (Admin Controls + Policy Management)
27. Test Routing Analytics (Usage Patterns + Performance Metrics)
28. Test Emergency Routing (Service Outages + Backup Systems)
29. Test Routing Audit Trail (Decision Logging + Compliance)
30. Test Custom Routing Rules (Business Logic + Specialized Workflows)

CONVERSATION MANAGEMENT:
31. Test Conversation Creation (New Sessions + Context Initialization)
32. Test Conversation Persistence (Save + Load + Database Storage)
33. Test Conversation History (Timeline + Search + Export)
34. Test Conversation Sharing (Collaboration + Permission Control)
35. Test Conversation Deletion (Data Cleanup + Privacy Compliance)
36. Test Context Window Management (Token Limits + Optimization)
37. Test Multi-turn Conversations (Context Preservation + Coherence)
38. Test Conversation Analytics (Usage Patterns + Quality Metrics)
39. Test Conversation Export (Data Portability + Format Support)
40. Test Conversation Security (Access Control + Data Protection)

STREAMING & REAL-TIME FEATURES:
41. Test Server-Sent Events (SSE + Real-time Streaming)
42. Test Streaming Response Handling (Progressive Display + User Experience)
43. Test Stream Interruption (User Cancellation + Graceful Termination)
44. Test Stream Error Handling (Network Issues + Recovery)
45. Test Stream Performance (Latency + Throughput + Optimization)
46. Test Multiple Concurrent Streams (Resource Management + Scaling)
47. Test Stream Security (Authentication + Data Protection)
48. Test Stream Analytics (Performance Metrics + Usage Tracking)
49. Test Stream Caching (Response Optimization + Storage)
50. Test Stream Compression (Bandwidth Optimization + Speed)

WEB-AUGMENTED RESPONSES:
51. Test DuckDuckGo Integration (Search API + Result Processing)
52. Test Web Search Filtering (Relevance + Quality + Safety)
53. Test Search Result Synthesis (AI + Web Data + Coherent Responses)
54. Test Search Rate Limiting (API Quotas + Throttling + Management)
55. Test Search Result Caching (Performance + Freshness + Storage)
56. Test Search Security (Safe Browsing + Content Filtering)
57. Test Search Analytics (Query Patterns + Result Quality)
58. Test Search Fallback (Service Failures + Alternative Sources)
59. Test Search Privacy (User Data + Query Anonymization)
60. Test Search Performance (Speed + Accuracy + Relevance)

MCP SERVER INTEGRATION:
61. Test MCP Server Connectivity (Model Context Protocol + Health)
62. Test AI Assistant Tools (3 Integrated Tools + Functionality)
63. Test Tool Authentication (Security + Access Control)
64. Test Tool Performance (Response Time + Reliability)
65. Test Tool Error Handling (Failures + Recovery + User Feedback)
66. Test Tool Configuration (Settings + Customization + Management)
67. Test Tool Analytics (Usage + Performance + Quality Metrics)
68. Test Tool Security (Data Protection + Access Control)
69. Test Tool Integration (Workflow + User Experience + Efficiency)
70. Test Tool Monitoring (Health Checks + Alerts + Maintenance)

AI ADMIN & CONFIGURATION:
71. Test AI Model Configuration (Admin Controls + Settings Management)
72. Test AI Performance Monitoring (Metrics + Alerts + Optimization)
73. Test AI Usage Analytics (Statistics + Trends + Reporting)
74. Test AI Cost Management (Budget Controls + Usage Tracking)
75. Test AI Security Settings (Policies + Access Control + Compliance)
76. Test AI Quality Assurance (Response Quality + Accuracy Monitoring)
77. Test AI Backup & Recovery (Model Data + Configuration + Disaster Recovery)
78. Test AI Compliance Reporting (Regulatory + Audit + Documentation)
79. Test AI User Management (Access Control + Permissions + Roles)
80. Test AI System Integration (APIs + Webhooks + External Systems)

AUTOMATED OPERATIONS:
81. Run All Ollama Local Tests (Complete Local AI Testing)
82. Run All AWS Bedrock Tests (Complete Cloud AI Testing)
83. Run All Model Routing Tests (Complete Intelligent Routing)
84. Run All Conversation Tests (Complete Conversation Management)
85. Run All Streaming Tests (Complete Real-time Features)
86. Run All Web-Augmented Tests (Complete Search Integration)
87. Run All MCP Server Tests (Complete Tool Integration)
88. Run All AI Admin Tests (Complete AI Administration)
89. Run Complete AI Integration Suite (All 80 Individual Tests)

Navigation:
0. Back to Main Menu
H. Help for this category
S. Search specific test by keyword

Select option (0-89): _
```

```powershell
# Example: API Integration & Backend Testing
===============================================
    API Integration & Backend System Testing
===============================================

LARAVEL SANCTUM API TESTING:
1.  Test API Authentication (Token Generation + Validation + Security)
2.  Test API Token Management (Creation + Revocation + Expiration)
3.  Test API Rate Limiting (Throttling + Quotas + Protection)
4.  Test API Permission System (Scopes + Abilities + Authorization)
5.  Test API CORS Configuration (Cross-Origin + Security Headers)
6.  Test API Versioning (Multiple Versions + Backward Compatibility)
7.  Test API Documentation (OpenAPI + Swagger + Auto-generation)
8.  Test API Error Handling (Standard Responses + Error Codes)
9.  Test API Performance (Response Times + Caching + Optimization)
10. Test API Security (Input Validation + SQL Injection + XSS Protection)

HRMIS INTEGRATION TESTING:
11. Test HRMIS Connectivity (External API + Authentication + Health)
12. Test User Data Synchronization (Employee Records + Real-time Updates)
13. Test Grade Verification (Position Validation + Authority Levels)
14. Test Department Mapping (Organizational Structure + Hierarchy)
15. Test HRMIS Error Handling (Service Failures + Retry Logic + Fallback)
16. Test Data Transformation (Format Conversion + Field Mapping)
17. Test HRMIS Performance (Response Times + Batch Processing)
18. Test HRMIS Security (Encrypted Communication + Access Control)
19. Test HRMIS Audit Trail (Integration Logging + Compliance)
20. Test HRMIS Scheduled Sync (Automated Updates + Conflict Resolution)

EMAIL GATEWAY INTEGRATION:
21. Test SMTP Configuration (Server Settings + Authentication + Security)
22. Test Email Template System (Dynamic Content + Localization)
23. Test Email Queue Processing (Laravel Queues + Worker Management)
24. Test Email Delivery Confirmation (Tracking + Status Updates)
25. Test Email Bounce Handling (Failed Delivery + Retry Logic)
26. Test Email Security (SPF + DKIM + DMARC + Anti-spam)
27. Test Email Performance (Bulk Sending + Rate Limiting + Optimization)
28. Test Email Analytics (Open Rates + Click Tracking + Metrics)
29. Test Email Compliance (GDPR + Privacy + Unsubscribe)
30. Test Email Failover (Multiple Providers + Redundancy)

CLAMAV VIRUS SCANNING:
31. Test ClamAV Integration (Service Connectivity + Health Monitoring)
32. Test File Upload Scanning (Real-time + Quarantine + Reporting)
33. Test Virus Definition Updates (Automatic + Scheduled + Verification)
34. Test Scan Performance (File Size Limits + Processing Speed)
35. Test Scan Results Handling (Clean + Infected + Suspicious Files)
36. Test Scan Error Handling (Service Failures + Timeout + Recovery)
37. Test Scan Logging (Audit Trail + Compliance + Reporting)
38. Test Scan Configuration (Settings + Policies + Customization)
39. Test Scan Security (Access Control + Data Protection)
40. Test Scan Analytics (Statistics + Trends + Threat Intelligence)

LARAVEL REVERB WEBSOCKET:
41. Test WebSocket Server (Connection + Health + Performance)
42. Test Real-time Notifications (Broadcasting + Delivery + Reliability)
43. Test Private Channels (Authentication + Authorization + Security)
44. Test Channel Presence (User Status + Online/Offline + Tracking)
45. Test WebSocket Scaling (Multiple Servers + Load Balancing)
46. Test Connection Management (Reconnection + Heartbeat + Cleanup)
47. Test WebSocket Security (Authentication + Encryption + Protection)
48. Test WebSocket Performance (Latency + Throughput + Optimization)
49. Test WebSocket Error Handling (Disconnections + Failures + Recovery)
50. Test WebSocket Analytics (Connection Stats + Usage Metrics)

REDIS & CACHING SYSTEM:
51. Test Redis Connectivity (Server Health + Configuration + Performance)
52. Test Session Storage (User Sessions + Persistence + Security)
53. Test Cache Management (Data Caching + Invalidation + Performance)
54. Test Queue Processing (Job Queues + Workers + Reliability)
55. Test Redis Clustering (High Availability + Failover + Scaling)
56. Test Cache Performance (Hit Rates + Response Times + Optimization)
57. Test Redis Security (Authentication + Access Control + Encryption)
58. Test Redis Monitoring (Health Checks + Alerts + Metrics)
59. Test Redis Backup (Data Persistence + Recovery + Disaster Planning)
60. Test Redis Analytics (Usage Patterns + Performance Metrics)

DATABASE INTEGRATION:
61. Test Database Connectivity (Connection Pooling + Health + Performance)
62. Test Transaction Management (ACID Properties + Rollback + Integrity)
63. Test Database Migrations (Schema Changes + Version Control + Rollback)
64. Test Database Seeding (Test Data + Production Data + Consistency)
65. Test Database Performance (Query Optimization + Indexing + Caching)
66. Test Database Security (Access Control + Encryption + Audit)
67. Test Database Backup (Automated Backups + Recovery + Verification)
68. Test Database Monitoring (Performance Metrics + Health + Alerts)
69. Test Database Scaling (Read Replicas + Sharding + Load Distribution)
70. Test Database Compliance (Data Retention + Privacy + Regulatory)

LARAVEL HORIZON QUEUE MONITORING:
71. Test Horizon Dashboard (Queue Monitoring + Worker Management)
72. Test Job Processing (Queue Workers + Job Execution + Performance)
73. Test Failed Job Handling (Retry Logic + Error Reporting + Recovery)
74. Test Queue Performance (Throughput + Latency + Optimization)
75. Test Queue Security (Job Authentication + Data Protection)
76. Test Queue Scaling (Auto-scaling + Load Management + Resource Allocation)
77. Test Queue Monitoring (Metrics + Alerts + Health Checks)
78. Test Queue Configuration (Settings + Policies + Customization)
79. Test Queue Analytics (Statistics + Trends + Performance Insights)
80. Test Queue Maintenance (Cleanup + Optimization + Troubleshooting)

AUTOMATED OPERATIONS:
81. Run All API Authentication Tests (Complete Sanctum Testing)
82. Run All HRMIS Integration Tests (Complete External System Testing)
83. Run All Email Gateway Tests (Complete Email System Testing)
84. Run All ClamAV Tests (Complete Virus Scanning Testing)
85. Run All WebSocket Tests (Complete Real-time Communication Testing)
86. Run All Redis Tests (Complete Caching & Session Testing)
87. Run All Database Tests (Complete Database Integration Testing)
88. Run All Queue Tests (Complete Background Processing Testing)
89. Run Complete API Integration Suite (All 80 Individual Tests)

Navigation:
0. Back to Main Menu
H. Help for this category
S. Search specific test by keyword

Select option (0-89): _
```

**Data Generation Scripts:**

- **User Data Scripts**: Generate users with various roles and permissions including AI access levels
- **Ticket Data Scripts**: Create helpdesk tickets with different states and AI interaction history
- **Asset Data Scripts**: Generate ICT assets with availability schedules and maintenance records
- **Loan Application Scripts**: Create loan applications with approval workflows and AI assistance
- **Conversation Data Scripts**: Generate AI conversation histories with various complexity levels
- **Document Generation Scripts**: Create test documents for AI analysis and embedding generation

**AI-Specific Data Scripts:**

- **FAQ Knowledge Base Scripts**: Curated FAQ entries for RAG automation
- **Document Corpus Scripts**: Test documents for AI analysis and embedding generation
- **Conversation Scenario Scripts**: Pre-defined conversation flows for AI automation
- **Model Response Scripts**: Cached AI responses for consistent automation

**Menu System Integration:**

All individual automation scripts will be integrated into the menu system through:

- **Script Registration**: Each script registers itself with metadata (name, description, category, requirements)
- **Dynamic Discovery**: Menu system automatically discovers and lists available scripts
- **Execution Wrapper**: Menu system provides consistent execution environment and error handling
- **Result Aggregation**: Menu system collects and reports execution results across all scripts
- **Configuration Injection**: Menu system injects environment and credential settings into scripts

**Cross-Platform Compatibility:**

- **PowerShell Core 7.x**: Ensures scripts work on Windows, Linux, and macOS
- **Unified Interface**: PowerShell menus provide consistent functionality across platforms
- **Environment Detection**: Automatic detection of available execution environments

- **Happy Path Scripts**: Normal user workflows with valid data and successful AI interactions
- **Edge Case Scripts**: Boundary conditions and unusual but valid scenarios including AI edge cases
- **Error Handling Scripts**: Invalid inputs and system failure conditions including AI service failures
- **Security Testing Scripts**: Malicious inputs and unauthorized access attempts including AI prompt injection
- **Performance Load Scripts**: High-load scenarios and AI service rate limiting automation

## Data Models

### Test Configuration Models

**TestSuite Configuration:**

```php
class TestSuiteConfig
{
    public string $environment;           // testing, staging, production
    public array $browsers;              // chrome, firefox, safari, edge
    public array $devices;               // desktop, tablet, mobile
    public bool $headless;               // true for CI, false for debugging
    public int $timeout;                 // default test timeout in seconds
    public array $credentials;           // test user credentials
    public array $endpoints;             // API endpoints for testing
}
```

**User Persona Models:**

```php
class TestPersona
{
    public string $role;                 // guest, staff, admin, superuser, approver
    public array $permissions;           // specific permissions for role
    public array $testData;             // persona-specific test data
    public array $workflows;            // expected workflows for persona
}
```

### Test Scenario Models

**Workflow Test Scenarios:**

```php
class WorkflowScenario
{
    public string $name;                 // scenario identifier
    public string $description;          // scenario description
    public array $steps;                // ordered test steps
    public array $assertions;           // expected outcomes
    public array $dependencies;         // prerequisite conditions
    public array $cleanup;              // post-test cleanup actions
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property-Based Testing Integration

The test suite will implement property-based testing using **Pest's property testing capabilities** to validate universal properties across all system components.

### Core Property Patterns

Based on the analysis of acceptance criteria, the test suite will implement several key property patterns:

**1. Workflow Completeness Properties**

- For any valid user input, the complete workflow should execute successfully
- For any invalid input, proper error handling should occur
- For any workflow state, the system should maintain data consistency

**2. Authentication and Authorization Properties**

- For any user role, access control should be properly enforced
- For any authentication attempt, security measures should be applied
- For any permission check, the correct authorization should be granted or denied

**3. Data Integrity Properties**

- For any data operation, referential integrity should be maintained
- For any user submission, proper validation should occur
- For any system state change, audit trails should be created

**4. Integration Consistency Properties**

- For any external service call, proper error handling should occur
- For any API request, authentication and rate limiting should be enforced
- For any real-time update, WebSocket connections should function correctly

## Correctness Properties

Based on the prework analysis, the following correctness properties will be implemented as property-based tests:

### Property 1: Guest User Workflow Completeness
*For any* valid guest user submission (helpdesk ticket or asset loan application), the complete workflow should execute successfully including form validation, data persistence, email notifications, and status tracking capabilities.
**Validates: Requirements 1.1, 1.2, 1.3, 1.4**

### Property 2: Form Validation Consistency
*For any* form input across all system forms, validation rules should be consistently applied, with proper error messages displayed for invalid inputs and successful processing for valid inputs.
**Validates: Requirements 1.5, 1.6**

### Property 3: Email Notification Reliability
*For any* system event that triggers email notifications, the notification should be properly formatted, delivered to the correct recipients, and logged for audit purposes.
**Validates: Requirements 1.7, 7.1, 10.2**

### Property 4: User Registration and Authentication Security
*For any* user registration or authentication attempt, proper security measures should be applied including email domain validation, credential verification, and session management.
**Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7**

### Property 5: Dashboard Data Accuracy
*For any* authenticated user accessing the dashboard, all displayed data should accurately reflect the current system state with real-time updates functioning correctly.
**Validates: Requirements 3.1, 3.2, 3.4**

### Property 6: User Interface Consistency
*For any* user interface element, accessibility standards should be met, keyboard navigation should function correctly, and responsive design should work across all supported devices and browsers.
**Validates: Requirements 3.5, 3.6, 11.2, 11.4, 11.5, 11.6, 11.7**

### Property 7: Authenticated User Enhancement
*For any* authenticated user performing system operations, enhanced features should function correctly including auto-filled forms, history tracking, and real-time updates.
**Validates: Requirements 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 5.1, 5.2, 5.3, 5.4, 5.5, 5.6**

### Property 8: Profile Management Integrity
*For any* user profile operation, data should be properly validated, updates should persist correctly, and integration with external systems should function as expected.
**Validates: Requirements 6.1, 6.2, 6.3, 6.4, 6.5, 6.6**

### Property 9: Approval Workflow Security
*For any* approval workflow, security tokens should be properly generated and validated, approval actions should be correctly processed, and audit trails should be maintained.
**Validates: Requirements 7.2, 7.3, 7.4, 7.5, 7.6, 7.7**

### Property 10: Administrative Access Control
*For any* administrative operation, role-based access control should be enforced, operations should complete successfully for authorized users, and proper audit logging should occur.
**Validates: Requirements 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 8.7, 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 9.7**

### Property 11: System Integration Reliability
*For any* external system integration, proper error handling should occur, data synchronization should maintain consistency, and service availability should be properly managed.
**Validates: Requirements 10.1, 10.3, 10.4, 10.5, 10.6, 10.7**

### Property 12: Performance and Accessibility Standards
*For any* system operation, performance should meet defined standards, accessibility requirements should be satisfied, and the system should handle concurrent load appropriately.
**Validates: Requirements 11.1, 11.3**

### Property 13: Security and Compliance Enforcement
*For any* system interaction, security measures should be properly applied, data protection requirements should be met, and compliance standards should be maintained.
**Validates: Requirements 12.1, 12.2, 12.3, 12.4, 12.5, 12.6, 12.7**

### Property 14: AI Integration Functionality
*For any* AI-powered feature, data sovereignty requirements should be respected, model routing should function correctly, and AI responses should be properly generated and managed.
**Validates: Requirements 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 13.7, 13.8, 13.9, 13.10**

### Property 15: Asset Management Operations
*For any* asset management operation, inventory tracking should be accurate, maintenance workflows should function correctly, and transfer processes should maintain proper audit trails.
**Validates: Requirements 14.1, 14.2, 14.3, 14.4, 14.5, 14.6, 14.7**

### Property 16: System Monitoring and Reporting
*For any* system monitoring or reporting operation, metrics should be accurately collected, reports should be properly generated, and monitoring dashboards should display correct information.
**Validates: Requirements 15.1, 15.2, 15.3, 15.4, 15.5, 15.6, 15.7**

### Property 17: End-to-End Workflow Integrity
*For any* complete business workflow, all steps should execute in the correct sequence, data should remain consistent throughout the process, and error recovery should function properly.
**Validates: Requirements 16.1, 16.2, 16.3, 16.4, 16.5, 16.6, 16.7**

### Advanced System Features (v3.6.1)

Based on the v3.6.1 documentation review, the design must include testing for these additional advanced features:

**Cloud Hybrid AI Architecture (D18 v1.0.1):**

- **Model Routing Intelligence**: Test automatic routing between Ollama (local) and AWS Bedrock (cloud) based on data sensitivity and query complexity
- **Data Sovereignty Compliance**: Validate PKS 4.2 and 9.2.1 compliance for sensitive data processing
- **Multi-Model Intelligence**: Test Claude Opus 4.5, Sonnet 4.5, Haiku 4.5, Nova Pro/Lite/Micro model selection
- **Streaming Responses**: Validate Server-Sent Events (SSE) for real-time AI responses
- **Web-Augmented Responses**: Test DuckDuckGo integration for enhanced AI responses
- **Conversation Management**: Test enhanced conversation save/load/delete functionality
- **MCP Server Integration**: Validate 3 AI assistant tools integration

**Advanced Monitoring and Performance (v3.6.1):**

- **Laravel Pulse 1.4.7**: Test real-time performance monitoring dashboard
- **Laravel Horizon 5.41.0**: Validate queue management and job monitoring
- **Laravel Telescope 5.16.0**: Test debugging interface (superuser access only)
- **Dual Audit System**: Validate owen-it (compliance) + spatie (operations) audit trails

**Enhanced Authentication and Authorization (v3.6.1):**

- **Self-Registration**: Test @motac.gov.my email domain validation and verification
- **Flexible Login**: Validate email/username login options
- **Google Workspace SSO**: Test OAuth 2.0 integration (optional)
- **Account Linking**: Validate guest-to-authenticated user submission linking

**Real-Time Communication (v3.6.1):**

- **Laravel Reverb 1.6.3**: Test WebSocket server for real-time notifications
- **Broadcasting Channels**: Validate private channels for tickets, loans, and users
- **AI Notifications**: Test real-time AI status and performance alerts

## Error Handling

### Test Failure Management

**Failure Classification:**

- **Critical Failures**: Security vulnerabilities, data corruption, system crashes, AI service outages
- **Major Failures**: Workflow breakdowns, integration failures, performance degradation, model routing failures
- **Minor Failures**: UI inconsistencies, non-critical validation issues, cosmetic problems

**Error Recovery Strategies:**

- **Automatic Retry**: For transient failures in external integrations and AI services
- **Graceful Degradation**: For non-critical feature failures and AI fallback scenarios
- **Circuit Breaker**: For repeated external service failures (AWS Bedrock, Ollama)
- **Rollback Procedures**: For data integrity issues and failed AI model deployments

### Test Environment Isolation

**Database Isolation:**

- Each test runs in a database transaction that is rolled back
- Test data factories ensure consistent baseline data
- Separate test databases for different test types
- AI conversation and embedding data isolation

**Service Isolation:**

- Mock external services for unit and integration tests
- Use test doubles for email, SMS, and file storage services
- Isolated AI service endpoints for testing AI functionality
- Separate Ollama and AWS Bedrock test environments
- Mock MCP server responses for AI assistant testing

**AI Service Isolation:**

- Test Ollama server with dedicated test models
- AWS Bedrock test environment with rate limiting
- Isolated vector embedding databases for testing
- Mock DuckDuckGo responses for web-augmented testing

## Testing Strategy

### Dual Testing Approach

The test suite implements both unit testing and property-based testing as complementary approaches:

**Unit Tests:**

- Verify specific examples and edge cases
- Test individual components in isolation
- Validate error conditions and boundary cases
- Focus on implementation details and specific scenarios

**Property-Based Tests:**

- Verify universal properties across all inputs
- Test system behavior with generated data
- Validate business rules and invariants
- Focus on correctness guarantees and system-wide properties

### Test Execution Strategy

**Test Categorization:**

- **Fast Tests** (Unit): Run on every code change (< 30 seconds)
- **Medium Tests** (Integration): Run on pull requests (< 5 minutes)
- **Slow Tests** (E2E): Run on deployment pipeline (< 30 minutes)
- **Extended Tests** (Performance): Run nightly (< 2 hours)

**Parallel Execution:**

- Unit tests run in parallel across multiple processes
- Browser tests run across multiple browser instances
- Database tests use separate database connections
- API tests use isolated test environments

### Property-Based Test Configuration

**Test Framework**: Pest with property testing capabilities
**Minimum Iterations**: 100 per property test
**Data Generators**: Custom generators for ICTServe domain objects
**Shrinking Strategy**: Automatic test case minimization on failure

**Property Test Tags:**
Each property test will be tagged with the format:
**Feature: ictserve-comprehensive-test-scripts, Property {number}: {property_description}**

### Continuous Integration Integration

**Pipeline Stages:**

1. **Static Analysis**: Code quality, security scanning, dependency checks
2. **Unit Tests**: Fast feedback on code changes
3. **Integration Tests**: Database and service integration validation
4. **Browser Tests**: Cross-browser compatibility and user workflows
5. **Performance Tests**: Core Web Vitals and load testing
6. **Security Tests**: Vulnerability scanning and penetration testing

**Test Reporting:**

- **Coverage Reports**: Code coverage metrics and trends
- **Performance Metrics**: Test execution times and system performance
- **Accessibility Reports**: WCAG compliance validation results
- **Security Reports**: Vulnerability assessments and compliance checks

### Advanced Automation Frameworks and Tools

**AI Automation Scripts:**

- **Ollama Automation**: Local LLM server interaction and model management scripts
- **AWS Bedrock Automation**: Cloud AI service integration and API interaction scripts
- **Vector Database Automation**: Embedding generation and semantic search automation
- **Model Routing Automation**: Intelligent model selection workflow automation
- **Conversation Automation**: AI conversation management and persistence scripts

**Performance and Monitoring Automation:**

- **Laravel Pulse Automation**: Real-time performance metrics collection scripts
- **Laravel Horizon Automation**: Queue management and job monitoring scripts
- **WebSocket Automation**: Real-time communication and broadcasting scripts
- **Streaming Response Automation**: Server-Sent Events (SSE) interaction scripts

**Advanced Integration Automation:**

- **MCP Server Automation**: Model Context Protocol server interaction scripts
- **Google Workspace SSO Automation**: OAuth 2.0 authentication flow scripts
- **DuckDuckGo Integration Automation**: Web-augmented response functionality scripts
- **Data Sovereignty Automation**: PKS compliance and data classification scripts

### Test Data Management

**Factory Patterns:**

- **UserFactory**: Generate users with various roles and permissions including AI access levels
- **TicketFactory**: Create helpdesk tickets with different states and AI interaction history
- **AssetFactory**: Generate ICT assets with availability schedules and maintenance records
- **LoanApplicationFactory**: Create loan applications with approval workflows and AI assistance
- **ConversationFactory**: Generate AI conversation histories with various complexity levels
- **DocumentFactory**: Create test documents for AI analysis and embedding generation

**AI-Specific Test Data:**

- **FAQ Knowledge Base**: Curated test FAQ entries for RAG testing
- **Document Corpus**: Test documents for AI analysis and embedding generation
- **Conversation Scenarios**: Pre-defined conversation flows for AI testing
- **Model Response Fixtures**: Cached AI responses for consistent testing

**Test Scenarios:**

- **Happy Path**: Normal user workflows with valid data and successful AI interactions
- **Edge Cases**: Boundary conditions and unusual but valid scenarios including AI edge cases
- **Error Cases**: Invalid inputs and system failure conditions including AI service failures
- **Security Cases**: Malicious inputs and unauthorized access attempts including AI prompt injection
- **Performance Cases**: High-load scenarios and AI service rate limiting validation

### Monitoring and Alerting

**Test Health Monitoring:**

- **Flaky Test Detection**: Identify and address unreliable tests including AI-dependent tests
- **Performance Regression**: Monitor test execution time trends and AI response times
- **Coverage Tracking**: Ensure comprehensive test coverage maintenance including AI features
- **Failure Analysis**: Automated categorization of test failures including AI service failures
- **AI Service Health**: Monitor Ollama and AWS Bedrock service availability and performance

**Alert Configuration:**

- **Critical Test Failures**: Immediate notification for security or data integrity issues including AI data breaches
- **Performance Degradation**: Alerts for significant performance regressions including AI response time increases
- **Coverage Drops**: Notifications when test coverage falls below thresholds including AI feature coverage
- **Flaky Test Threshold**: Alerts when test reliability drops below acceptable levels including AI test stability
- **AI Service Outages**: Immediate alerts for Ollama or AWS Bedrock service disruptions
- **Model Performance Degradation**: Alerts for AI model accuracy or response quality issues

### Advanced Testing Strategies

**AI-Specific Testing Approaches:**

- **Model Validation Testing**: Verify AI model responses meet quality and accuracy standards
- **Prompt Injection Testing**: Validate security against malicious AI prompts
- **Data Sovereignty Testing**: Ensure sensitive data stays within local processing (Ollama)
- **Model Routing Testing**: Verify intelligent routing between local and cloud AI services
- **Conversation Context Testing**: Validate AI conversation memory and context preservation
- **Embedding Quality Testing**: Verify vector embedding generation and semantic search accuracy

**Performance Testing Enhancements:**

- **AI Response Time Testing**: Validate AI service response times meet SLA requirements
- **Concurrent AI Request Testing**: Test system behavior under multiple simultaneous AI requests
- **Model Switching Testing**: Validate seamless switching between different AI models
- **Streaming Response Testing**: Test Server-Sent Events for real-time AI responses
- **Queue Performance Testing**: Validate background AI job processing performance

**Integration Testing Enhancements:**

- **Multi-Service Integration**: Test coordination between Ollama, AWS Bedrock, and DuckDuckGo
- **Real-Time Notification Testing**: Validate WebSocket notifications for AI events
- **MCP Server Integration Testing**: Test Model Context Protocol server functionality
- **OAuth Integration Testing**: Validate Google Workspace SSO integration
- **Audit Trail Integration Testing**: Verify dual audit system captures all AI interactions
