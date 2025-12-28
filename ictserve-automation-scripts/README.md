# ICTServe Comprehensive Automation Suite

A complete PowerShell automation framework for testing the ICTServe system with visual demonstration capabilities.

## Overview

This suite contains **347+ PowerShell automation scripts** covering all aspects of the ICTServe system:

| Category | Scripts | Description |
|----------|---------|-------------|
| Guest User Workflows | 50 | Public-facing functionality without authentication |
| Authenticated User Workflows | 67 | Enhanced features for logged-in users |
| Admin Panel Operations | 78 | Filament admin interface automation |
| AI Integration Testing | 89 | Cloud Hybrid AI (Ollama + AWS Bedrock) |
| API Integration & Backend | 89 | Laravel Sanctum, HRMIS, Email, Redis |
| Performance & Accessibility | 45 | Core Web Vitals, WCAG 2.2 AA |
| Security & Compliance | 52 | PDPA, CSRF, Input Validation |
| System Monitoring & Health | 38 | Laravel Pulse, Horizon, Telescope |
| End-to-End Workflows | 29 | Complete user journeys |

## Quick Start

### Prerequisites

- PowerShell 7.x or higher
- Google Chrome or Microsoft Edge
- Selenium WebDriver (optional, for browser automation)

### Running the Menu

```powershell
# Launch interactive menu
.\Main-Menu.ps1

# Launch with specific mode
.\Main-Menu.ps1 -Mode Demo -Environment testing
```

### Running All Tests

```powershell
# Run all tests in headless mode
.\Run-All.ps1 -Mode Headless -Environment testing

# Run specific category
.\Run-All.ps1 -Category guest-workflows -Mode Visual
```

## Demonstration Modes

| Mode | Description | Use Case |
|------|-------------|----------|
| **Headless** | Fast execution without browser window | CI/CD pipelines |
| **Visual** | Live browser automation visible | Development/debugging |
| **Demo** | Slower with highlights and annotations | Presentations |
| **Interactive** | Pauses at key steps | Training sessions |
| **Recording** | Captures video for training | Documentation |

## Directory Structure

```
ictserve-automation-scripts/
├── Main-Menu.ps1              # Interactive menu system
├── Run-All.ps1                # Execute all scripts
├── README.md                  # This file
├── config/                    # Configuration files
│   ├── environments.json      # Environment settings
│   ├── settings.json          # General settings
│   ├── credentials.json       # Test credentials (update before use)
│   └── demo-settings.json     # Visual demo configuration
├── utilities/                 # Shared PowerShell modules
│   ├── common-functions.ps1   # Logging, config, reporting
│   ├── browser-automation.ps1 # Selenium WebDriver utilities
│   └── api-helpers.ps1        # HTTP API utilities
├── scripts/                   # All automation scripts by category
│   ├── guest-workflows/       # 50 scripts
│   ├── authenticated-workflows/ # 67 scripts
│   ├── admin-operations/      # 78 scripts
│   ├── ai-integration/        # 89 scripts
│   ├── api-backend/           # 89 scripts
│   ├── performance-accessibility/ # 45 scripts
│   ├── security-compliance/   # 52 scripts
│   ├── monitoring-health/     # 38 scripts
│   └── end-to-end/            # 29 scripts
├── test-data/                 # Test data and fixtures
├── reports/                   # Generated reports
│   ├── execution-logs/        # Script execution logs
│   ├── screenshots/           # Visual demo screenshots
│   ├── videos/                # Recorded demonstrations
│   └── analytics/             # Performance analytics
└── docs/                      # Documentation
```

## Configuration

### Environment Setup

Edit `config/environments.json` to configure your target environments:

```json
{
  "testing": {
    "baseUrl": "https://test.ictserve.motac.gov.my",
    "apiUrl": "https://test.ictserve.motac.gov.my/api",
    "timeout": 30
  }
}
```

### Credentials

Update `config/credentials.json` with your test credentials:

```json
{
  "testUsers": {
    "staff": {
      "email": "staff.test@motac.gov.my",
      "password": "YOUR_TEST_PASSWORD"
    }
  }
}
```

**⚠️ Never commit real credentials to version control!**

## Visual Demonstration Features

- **Element Highlighting**: Form fields and buttons are highlighted during interaction
- **Animated Cursor**: Mouse movements are visualized
- **Text Annotations**: Real-time explanations of each step
- **Screenshot Capture**: Automatic screenshots at key workflow points
- **Video Recording**: Complete workflow capture for training materials
- **Interactive Pauses**: Stop at key steps for presenter explanation

## Menu Navigation

The main menu provides:

1. **Category Browsing**: Navigate through script categories
2. **Search**: Find scripts by keyword
3. **Execution History**: Track previous runs
4. **Configuration**: Manage environments and settings
5. **Health Check**: Verify prerequisites
6. **Automated Operations**: Run multiple scripts at once

## Reports

Reports are generated in `reports/` directory:

- **HTML Reports**: Visual test results with pass/fail status
- **JSON Reports**: Machine-readable results for CI/CD
- **CSV Reports**: Spreadsheet-compatible data
- **Screenshots**: Visual documentation of workflows
- **Videos**: Recorded demonstrations (when Recording mode is used)

## Support

For issues or questions:

1. Check `docs/troubleshooting-guide.md`
2. Run System Health Check (Menu option 25)
3. Review execution logs in `reports/execution-logs/`

## License

Internal use only - MOTAC ICT Division
