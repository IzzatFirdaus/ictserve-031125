# Visual Demonstration Scripts - ICTServe Automation Suite

## Overview

This document details the 284+ automation scripts that support full visual demonstration capabilities, allowing trainers and presenters to show end users exactly how the ICTServe system works through live browser interactions.

## Visual Demonstration Modes

### Available Execution Modes

| Mode | Description | Use Case | Browser Window | Speed | Annotations |
|------|-------------|----------|----------------|-------|-------------|
| **Visual** | Live browser automation with visible interactions | Development and debugging | Visible | Normal | Optional |
| **Demo** | Slower execution with highlights and annotations | Training presentations | Visible | Slow | Always on |
| **Interactive** | Pauses at key steps for explanation | Live training sessions | Visible | Variable | Always on |
| **Recording** | Captures video while running automation | Training material creation | Visible | Normal | Optional |
| **Headless** | Fast execution without browser window | CI/CD and batch testing | Hidden | Fast | None |

### Visual Enhancement Features

- **🖱️ Animated Mouse Cursor**: Shows click locations and movements
- **🔍 Element Highlighting**: Highlights form fields and buttons being interacted with
- **💬 Real-time Annotations**: Text overlays explaining each step
- **📸 Screenshot Capture**: Automatic screenshots at key workflow points
- **🎥 Video Recording**: Optional MP4 recording for training materials
- **⏸️ Interactive Pauses**: Pause points for presenter explanation
- **📊 Backend Monitoring**: Live API calls and responses in browser console
- **🎨 Visual Indicators**: Success/failure indicators and validation feedback

## Guest User Workflows - Visual Demonstrations (50 Scripts)

### Helpdesk Ticket Workflows with Visual Demo

#### 1. Basic Ticket Submission Demo
**Script**: `submit-basic-ticket.ps1`
**Demo Features**:

- 🎬 Live form filling with preset guest user data
- 🔍 Real-time form validation highlighting
- 📧 Email notification demonstration
- 📸 Screenshots: Homepage → Form → Validation → Success
- ⏸️ Pause points: Form explanation, validation demo, success confirmation

**Demo Scenario**:

```
🌐 Opening ICTServe homepage in Chrome...
👤 Demonstrating Guest User Access
📝 Clicking "Submit Helpdesk Ticket" button
📋 Filling form with demo data:
   ✏️ Name: "Ahmad bin Abdullah"
   ✏️ Email: "ahmad.demo@motac.gov.my"
   ✏️ Department: "Bahagian Pengurusan Maklumat"
   ✏️ Issue: "Laptop screen flickering"
⏸️ [PAUSE] - Explaining form validation
🚀 Submitting form and showing success message
📧 Demonstrating email notification process
```

#### 2. File Upload with Virus Scanning Demo
**Script**: `submit-ticket-with-attachments.ps1`
**Demo Features**:

- 📁 Live file selection and upload
- 🔍 ClamAV virus scanning visualization
- ⚡ Real-time upload progress
- 🛡️ Security validation demonstration

**Demo Scenario**:

```
📎 Selecting test file: "screenshot-issue.png"
🔍 Showing ClamAV virus scanning process
⚡ Upload progress bar demonstration
✅ File security validation success
📸 Screenshot capture: Upload → Scan → Success
```

#### 3. Form Validation Error Demo
**Script**: `test-form-validation-errors.ps1`
**Demo Features**:

- ❌ Intentional validation errors
- 🔍 Frontend JavaScript validation highlighting
- 🛡️ Backend Laravel validation demonstration
- 🎨 Error message styling and positioning

**Demo Scenario**:

```
❌ Submitting form with invalid email format
🔍 Frontend validation error highlighting
❌ Submitting with missing required fields
🛡️ Backend validation error demonstration
🎨 Error message styling and user feedback
```

### Asset Loan Workflows with Visual Demo

#### 4. Asset Availability Calendar Demo
**Script**: `check-asset-availability.ps1`
**Demo Features**:

- 📅 Interactive calendar navigation
- 🔍 Real-time availability checking
- ⚠️ Conflict detection visualization
- 📊 Booking status indicators

**Demo Scenario**:

```
📅 Opening asset availability calendar
🔍 Selecting asset category: "Laptops"
📊 Showing availability status indicators
⚠️ Demonstrating date conflict detection
✅ Successful booking confirmation
```

#### 5. Loan Application with Validation Demo
**Script**: `test-loan-duration-validation.ps1`
**Demo Features**:

- 📋 Dynamic form field updates
- ⏰ Duration calculation visualization
- 🔍 Business rule validation
- 💡 User guidance and suggestions

**Demo Scenario**:

```
📋 Filling loan application form
⏰ Selecting loan duration with live calculation
🔍 Business rule validation demonstration
💡 Showing user guidance for optimal duration
```

## Authenticated User Workflows - Visual Demonstrations (67 Scripts)

### Authentication Process Demos

#### 6. Email Login Demonstration
**Script**: `test-email-login.ps1`
**Demo Features**:

- 🔐 Live login process with real credentials
- 🔍 Session creation visualization
- 📊 Dashboard loading demonstration
- 🎨 User interface personalization

**Demo Scenario**:

```
🔐 Navigating to login page
✏️ Entering email: "demo.user@motac.gov.my"
🔒 Password entry (masked for security)
🔍 Session creation and validation
📊 Dashboard loading with personalized data
🎨 User interface customization display
```

#### 7. Google Workspace SSO Demo
**Script**: `test-google-sso.ps1`
**Demo Features**:

- 🔗 OAuth2 flow visualization
- 🌐 Google authentication redirect
- ✅ Domain validation demonstration
- 👤 User provisioning process

**Demo Scenario**:

```
🔗 Clicking "Sign in with Google" button
🌐 Redirecting to Google authentication
✅ Domain validation: @motac.gov.my
👤 User provisioning and profile creation
🔄 Redirecting back to ICTServe dashboard
```

### Dashboard and Real-Time Features Demos

#### 8. Real-Time Dashboard Demo
**Script**: `test-real-time-statistics.ps1`
**Demo Features**:

- 📊 Live data updates via WebSocket
- 🔔 Real-time notifications
- 📈 Dynamic chart updates
- ⚡ Performance metrics visualization

**Demo Scenario**:

```
📊 Loading dashboard with live statistics
🔔 Demonstrating real-time notifications
📈 Showing dynamic chart updates
⚡ Performance metrics live updates
🎬 WebSocket connection visualization
```

#### 9. Notification Center Demo
**Script**: `test-notification-center.ps1`
**Demo Features**:

- 🔔 Live notification delivery
- 📱 Push notification demonstration
- 🎨 Notification styling and animation
- ⚙️ Preference management interface

**Demo Scenario**:

```
🔔 Opening notification center
📱 Demonstrating push notifications
🎨 Notification animation and styling
⚙️ Notification preference settings
✅ Real-time notification delivery test
```

### Enhanced Helpdesk Features Demos

#### 10. Auto-Filled Forms Demo
**Script**: `test-auto-filled-forms.ps1`
**Demo Features**:

- 📝 Profile data integration
- ⚡ Automatic form population
- 🔍 Data validation and verification
- 💾 Form state persistence

**Demo Scenario**:

```
📝 Opening new ticket form
⚡ Automatic population from user profile
🔍 Data validation and verification
💾 Form state persistence demonstration
✅ Enhanced user experience showcase
```

#### 11. Ticket History and Comments Demo
**Script**: `test-ticket-comments.ps1`
**Demo Features**:

- 📋 Historical ticket display
- 💬 Real-time comment system
- 🔔 Comment notifications
- 🎨 Comment threading and formatting

**Demo Scenario**:

```
📋 Loading ticket history interface
💬 Adding new comment with rich formatting
🔔 Real-time comment notification delivery
🎨 Comment threading and reply system
📸 Screenshot: History → Comment → Notification
```

## Admin Panel Operations - Visual Demonstrations (78 Scripts)

### Admin Authentication and Interface Demos

#### 12. Filament Admin Panel Demo
**Script**: `test-admin-login.ps1`
**Demo Features**:

- 🔐 Admin authentication process
- 🎨 Filament interface showcase
- 🛡️ Role-based access demonstration
- 📊 Admin dashboard overview

**Demo Scenario**:

```
🔐 Admin login with elevated credentials
🎨 Filament interface loading and customization
🛡️ Role-based access control demonstration
📊 Admin dashboard with comprehensive metrics
🔍 Navigation through admin modules
```

#### 13. Ticket Management Demo
**Script**: `test-ticket-assignment.ps1`
**Demo Features**:

- 📋 Ticket queue visualization
- 👥 Assignment workflow demonstration
- 🔄 Status update process
- 📊 Performance analytics display

**Demo Scenario**:

```
📋 Opening ticket management interface
👥 Demonstrating ticket assignment workflow
🔄 Live status updates and notifications
📊 Performance analytics and reporting
⚡ Bulk operations demonstration
```

### Asset Management Demos

#### 14. Asset Registration Demo
**Script**: `test-asset-registration.ps1`
**Demo Features**:

- 📝 Complete asset registration form
- 📷 Photo upload and management
- 🏷️ Barcode generation and printing
- 📊 Asset lifecycle tracking

**Demo Scenario**:

```
📝 Opening asset registration form
📷 Uploading asset photos and documentation
🏷️ Barcode generation and QR code creation
📊 Asset lifecycle status initialization
✅ Registration completion and confirmation
```

#### 15. Maintenance Scheduling Demo
**Script**: `test-maintenance-scheduling.ps1`
**Demo Features**:

- 📅 Maintenance calendar interface
- ⚙️ Preventive maintenance scheduling
- 🔔 Maintenance alerts and notifications
- 📊 Maintenance history tracking

**Demo Scenario**:

```
📅 Opening maintenance scheduling calendar
⚙️ Creating preventive maintenance schedule
🔔 Setting up maintenance alerts
📊 Reviewing maintenance history and trends
📸 Screenshots: Calendar → Schedule → Alerts
```

## AI Integration Testing - Visual Demonstrations (89 Scripts)

### Ollama Local AI Demos

#### 16. FAQ Bot Response Demo
**Script**: `test-faq-responses.ps1`
**Demo Features**:

- 🤖 Live AI conversation interface
- 💬 Real-time response generation
- 🔍 Knowledge base search visualization
- 📊 Response quality metrics

**Demo Scenario**:

```
🤖 Opening AI FAQ bot interface
💬 Asking sample question: "How do I reset my password?"
🔍 Knowledge base search visualization
💬 Real-time AI response generation
📊 Response quality and accuracy metrics
⏸️ [PAUSE] - Explaining local AI processing
```

#### 17. Model Routing Demo
**Script**: `test-model-routing.ps1`
**Demo Features**:

- 🧠 Intelligent model selection
- 🔄 Routing decision visualization
- 🛡️ Data sensitivity classification
- ⚡ Performance comparison display

**Demo Scenario**:

```
🧠 Submitting query with sensitive data
🔄 Model routing decision visualization
🛡️ Data sensitivity classification display
⚡ Local processing vs cloud comparison
📊 Performance metrics and compliance status
```

### AWS Bedrock Cloud AI Demos

#### 18. Claude Model Integration Demo
**Script**: `test-claude-models.ps1`
**Demo Features**:

- ☁️ Cloud AI service connection
- 🎯 Model selection based on complexity
- 💬 Streaming response visualization
- 📊 Cost and performance tracking

**Demo Scenario**:

```
☁️ Connecting to AWS Bedrock service
🎯 Automatic model selection: Claude Sonnet
💬 Streaming response with live updates
📊 Cost tracking and performance metrics
🔍 DLP filtering demonstration
```

#### 19. Web-Augmented Responses Demo
**Script**: `test-duckduckgo-integration.ps1`
**Demo Features**:

- 🔍 Live web search integration
- 🌐 DuckDuckGo API demonstration
- 📝 Response synthesis visualization
- 🛡️ Content filtering and safety

**Demo Scenario**:

```
🔍 AI query requiring web search
🌐 DuckDuckGo search API integration
📝 Response synthesis with web data
🛡️ Content filtering and safety checks
💬 Enhanced AI response delivery
```

### Conversation Management Demos

#### 20. Conversation Persistence Demo
**Script**: `test-conversation-persistence.ps1`
**Demo Features**:

- 💾 Conversation save/load functionality
- 📋 Conversation history interface
- 🔍 Search and filtering capabilities
- 📤 Export and sharing options

**Demo Scenario**:

```
💾 Saving current AI conversation
📋 Loading conversation history interface
🔍 Searching previous conversations
📤 Exporting conversation for documentation
🔄 Resuming saved conversation context
```

## End-to-End Workflow Demonstrations (29 Scripts)

### Complete User Journey Demos

#### 21. Guest to Resolution Demo
**Script**: `guest-to-resolution.ps1`
**Demo Features**:

- 🎬 Complete ticket lifecycle visualization
- 👥 Multi-user perspective demonstration
- 📧 Email notification flow
- ✅ Resolution and feedback process

**Demo Scenario**:

```
🎬 Starting as guest user submitting ticket
📧 Email notifications to admin and user
👥 Switching to admin perspective for processing
🔄 Status updates and user notifications
✅ Ticket resolution and user feedback
📊 Complete workflow analytics display
```

#### 22. Application to Return Demo
**Script**: `application-to-return.ps1`
**Demo Features**:

- 📋 Complete loan application process
- ✅ Multi-level approval workflow
- 📱 OTP pickup demonstration
- 🔄 Return and condition assessment

**Demo Scenario**:

```
📋 Guest submitting loan application
✅ Approval workflow with email notifications
📱 OTP generation and pickup process
🔄 Asset return and condition assessment
📊 Complete loan lifecycle analytics
```

### Cross-Module Integration Demos

#### 23. Helpdesk to Loans Integration Demo
**Script**: `helpdesk-to-loans.ps1`
**Demo Features**:

- 🔗 Cross-module data integration
- 🔄 Workflow transition demonstration
- 📊 Unified reporting interface
- 🎨 Consistent user experience

**Demo Scenario**:

```
🔗 Starting with helpdesk ticket
🔄 Converting to asset loan request
📊 Unified tracking and reporting
🎨 Consistent UI across modules
✅ Integrated workflow completion
```

## Visual Demo Configuration Options

### Execution Speed Settings

```powershell
# Fast execution (for testing)
-Speed Fast

# Normal execution (for development)
-Speed Normal

# Demo execution (for presentations)
-Speed Demo

# Slow execution (for detailed training)
-Speed Slow
```

### Annotation and Highlighting Options

```powershell
# Enable all visual enhancements
-VisualMode Full -Annotations On -Highlighting On

# Minimal visual mode
-VisualMode Basic -Annotations Off -Highlighting On

# Recording mode with annotations
-VisualMode Recording -Annotations On -VideoOutput ".\videos\"
```

### Browser and Display Settings

```powershell
# Maximize browser window
-BrowserWindow Maximized

# Windowed mode for side-by-side comparison
-BrowserWindow Windowed

# Full screen for presentations
-BrowserWindow Fullscreen
```

## Training and Presentation Features

### Interactive Pause Points

All visual demo scripts include strategic pause points:

- **🏠 Homepage Navigation**: Explain system overview and navigation
- **📝 Form Interaction**: Demonstrate form filling and validation
- **🔍 Search and Filtering**: Show search capabilities and results
- **📊 Data Visualization**: Explain charts, metrics, and analytics
- **✅ Success Confirmation**: Highlight successful operations
- **❌ Error Handling**: Demonstrate error scenarios and recovery

### Screenshot and Video Capture

```powershell
# Automatic screenshot capture
-CaptureScreenshots On -ScreenshotPath ".\screenshots\"

# Video recording for training materials
-RecordVideo On -VideoPath ".\training-videos\" -VideoFormat MP4

# Combined capture for comprehensive documentation
-CaptureAll On -OutputPath ".\demo-materials\"
```

### Multi-Browser Demonstrations

```powershell
# Side-by-side comparison (Guest vs Authenticated)
-MultiWindow On -ComparisonMode "Guest-vs-Auth"

# Cross-browser compatibility demonstration
-MultiBrowser On -Browsers "Chrome,Firefox,Safari"

# Mobile and desktop comparison
-ResponsiveDemo On -Devices "Desktop,Tablet,Mobile"
```

## Demo Script Execution Examples

### Basic Visual Demo

```powershell
# Run single script in demo mode
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Mode Demo

# Run with interactive pauses
.\scripts\guest-workflows\helpdesk\submit-basic-ticket.ps1 -Mode Interactive
```

### Training Session Setup

```powershell
# Complete training session with recording
.\Main-Menu.ps1 -TrainingMode On -RecordSession On -OutputPath ".\training-session-2024\"

# Quick demo for stakeholder presentation
.\Main-Menu.ps1 -QuickDemo On -Duration 15min -HighlightFeatures "AI,RealTime,Mobile"
```

### Batch Demo Execution

```powershell
# Run all guest workflow demos
.\scripts\guest-workflows\menu.ps1 -RunAllDemos On -Mode Demo

# Run critical path demos only
.\Main-Menu.ps1 -CriticalPath On -Mode Demo -Duration 30min
```

## Customization and Configuration

### Demo Data Customization

All visual demo scripts use configurable demo data:

```json
{
  "demoUser": {
    "name": "Ahmad bin Abdullah",
    "email": "ahmad.demo@motac.gov.my",
    "department": "Bahagian Pengurusan Maklumat",
    "phone": "03-1234-5678"
  },
  "demoTicket": {
    "category": "Hardware Issue",
    "priority": "Medium",
    "subject": "Laptop screen flickering",
    "description": "Screen flickers intermittently, especially when using external monitor"
  },
  "demoAsset": {
    "type": "Laptop",
    "model": "Dell Latitude 5520",
    "duration": "2 weeks",
    "purpose": "Training and development"
  }
}
```

### Visual Theme Customization

```json
{
  "visualTheme": {
    "highlightColor": "#007bff",
    "annotationFont": "Arial, 14px",
    "pauseIndicator": "⏸️",
    "successIndicator": "✅",
    "errorIndicator": "❌",
    "animationSpeed": "medium"
  }
}
```

## Best Practices for Visual Demonstrations

### Preparation Checklist

- ✅ Test all demo scripts in target environment
- ✅ Verify demo data is appropriate for audience
- ✅ Check browser compatibility and screen resolution
- ✅ Prepare backup scenarios for technical issues
- ✅ Review pause points and talking points
- ✅ Test audio/video recording if needed

### During Presentation

- 🎤 Explain each step before executing
- ⏸️ Use pause points for audience questions
- 🔍 Highlight key features and benefits
- 📊 Explain backend processes and integrations
- 🎨 Demonstrate responsive design and accessibility
- 💡 Show error handling and recovery scenarios

### Post-Demonstration

- 📹 Provide recorded sessions for reference
- 📸 Share screenshots and documentation
- 📋 Collect feedback and questions
- 🔄 Update demo scripts based on feedback
- 📊 Analyze demo effectiveness and engagement

---

*This document covers all 284+ visual demonstration scripts in the ICTServe Comprehensive Automation Suite. Each script is designed to provide engaging, educational demonstrations of system functionality while maintaining technical accuracy and professional presentation quality.*
