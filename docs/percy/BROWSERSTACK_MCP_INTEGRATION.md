# BrowserStack MCP Integration for Percy Visual Testing

## Overview

This document describes the BrowserStack MCP (Model Context Protocol) integration with Percy visual testing for ICTServe v3.6.1. The integration enables comprehensive cross-platform visual regression testing across real browsers and devices.

## Requirements Validated

- **12.1**: BrowserStack MCP server integration for comprehensive test management
- **12.2**: Real device and browser execution support
- **12.3**: Test Management for organizing Percy visual test cases
- **12.4**: WCAG compliance scanning alongside Percy visual validation
- **12.5**: Live sessions for manual visual testing and debugging
- **12.6**: Comprehensive failure analysis and debugging capabilities
- **12.7**: Cross-browser testing with Percy's visual regression detection
- **12.8**: Performance testing with visual validation across devices
- **12.9**: Automated test execution alongside Percy snapshot capture
- **12.10**: Combined test execution reports (BrowserStack + Percy)

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    ICTServe v3.6.1 Application                  │
├─────────────────────────────────────────────────────────────────┤
│  Laravel 12.43.1 │ Livewire 3.7.3 │ Filament 4.3.1 │ PHP 8.4.1 │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    BrowserStack MCP Server                       │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  │
│  │ Cross-Browser   │  │ Real Device     │  │ Accessibility   │  │
│  │ Testing         │  │ Testing         │  │ Testing         │  │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘  │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  │
│  │ Live Sessions   │  │ Test Management │  │ Performance     │  │
│  │ Debugging       │  │ Integration     │  │ Testing         │  │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Percy Visual Testing Platform                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  │
│  │ Snapshot        │  │ Visual          │  │ Build           │  │
│  │ Capture         │  │ Comparison      │  │ Management      │  │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

## Configuration

### Environment Variables

Add the following to your `.env` file:

```bash
# BrowserStack Credentials
BROWSERSTACK_ENABLED=true
BROWSERSTACK_USERNAME=your_username
BROWSERSTACK_ACCESS_KEY=your_access_key
BROWSERSTACK_PROJECT_NAME=ICTServe v3.6.1 Visual Testing
BROWSERSTACK_BUILD_NAME=Percy Integration Build

# BrowserStack Features
BROWSERSTACK_TEST_MANAGEMENT_ENABLED=true
BROWSERSTACK_ACCESSIBILITY_ENABLED=true
BROWSERSTACK_LIVE_ENABLED=true
BROWSERSTACK_PERFORMANCE_ENABLED=true

# Percy Integration
PERCY_ENABLED=true
PERCY_TOKEN=your_percy_token
PERCY_PROJECT=ictserve-v3.6.1-visual-testing
```

### MCP Server Configuration

The BrowserStack MCP server is configured in `.mcp.json`:

```json
{
  "mcpServers": {
    "browserstack": {
      "command": "php",
      "args": ["artisan", "mcp:browserstack"],
      "env": {
        "BROWSERSTACK_USERNAME": "",
        "BROWSERSTACK_ACCESS_KEY": "",
        "PERCY_TOKEN": "",
        "PERCY_ENABLED": "false"
      },
      "disabled": false,
      "autoApprove": [
        "browserstack_validate_config",
        "browserstack_get_browsers",
        "browserstack_create_percy_session",
        "browserstack_run_percy_visual_test",
        "browserstack_accessibility_test",
        "browserstack_create_live_session",
        "browserstack_get_test_report",
        "browserstack_run_cross_browser_test",
        "browserstack_mobile_visual_test"
      ]
    }
  }
}
```

## Available MCP Tools

### 1. browserstack_validate_config
Validates BrowserStack configuration and credentials.

**Usage:**

```
Validate my BrowserStack configuration
```

### 2. browserstack_get_browsers
Gets available browsers and devices for testing.

**Usage:**

```
Show me available browsers and devices on BrowserStack
```

### 3. browserstack_create_percy_session
Creates a BrowserStack session for Percy visual testing.

**Parameters:**

- `capabilities`: Browser/device capabilities object

**Usage:**

```
Create a Percy session on Chrome Windows 11
```

### 4. browserstack_run_percy_visual_test
Executes Percy visual tests across multiple browsers/devices.

**Parameters:**

- `test_config`: Configuration object with browsers and snapshots

**Usage:**

```
Run Percy visual tests on Chrome, Firefox, and Safari
```

### 5. browserstack_accessibility_test
Runs accessibility testing with Percy visual validation.

**Parameters:**

- `test_config`: Configuration with URL, WCAG level, and Percy options

**Usage:**

```
Run accessibility test on the homepage with WCAG AA compliance
```

### 6. browserstack_create_live_session
Creates a BrowserStack Live session for visual debugging.

**Parameters:**

- `capabilities`: Browser/device capabilities

**Usage:**

```
Create a live debugging session on iPhone 14
```

### 7. browserstack_capture_live_screenshot
Captures a screenshot during a BrowserStack Live session.

**Parameters:**

- `session_id`: Live session ID
- `name`: Screenshot name
- `options`: Screenshot options (description, compare_with_percy)

**Usage:**

```
Capture a screenshot named "Header Issue" in the current live session
```

### 8. browserstack_capture_live_percy_snapshot
Captures a Percy snapshot during a Live session for visual comparison.

**Parameters:**

- `session_id`: Live session ID
- `snapshot_name`: Percy snapshot name
- `options`: Percy snapshot options (widths, min_height, percy_css)

**Usage:**

```
Capture a Percy snapshot for visual comparison during the live session
```

### 9. browserstack_create_visual_issue
Creates a visual issue report from Live session findings.

**Parameters:**

- `session_id`: Live session ID
- `issue_data`: Issue details (title, description, severity, affected_pages, screenshots, percy_snapshots)

**Usage:**

```
Create a visual issue report for the header alignment problem found in the live session
```

### 10. browserstack_start_collaborative_session
Starts a collaborative debugging session for visual issues.

**Parameters:**

- `session_id`: Live session ID
- `owner_name`: Name of the session owner

**Usage:**

```
Start a collaborative debugging session with the QA team
```

### 11. browserstack_get_debugging_workflow
Gets a predefined debugging workflow for visual issues.

**Parameters:**

- `workflow_type`: Workflow type (percy_visual_regression, accessibility_compliance, cross_browser_consistency)

**Usage:**

```
Get the Percy visual regression debugging workflow
```

### 12. browserstack_get_test_report
Gets comprehensive test execution reports combining BrowserStack and Percy results.

**Parameters:**

- `build_id`: BrowserStack build ID

**Usage:**

```
Get the test report for build abc123
```

### 13. browserstack_run_cross_browser_test
Runs cross-browser visual consistency tests with Percy.

**Parameters:**

- `test_config`: Configuration with URL and snapshot name

**Usage:**

```
Run cross-browser visual test on the login page
```

### 9. browserstack_mobile_visual_test
Runs mobile visual regression testing on real devices.

**Parameters:**

- `test_config`: Configuration with URL, devices, and orientations

**Usage:**

```
Run mobile visual tests on iPhone 14 and Samsung Galaxy S23
```

## Playwright Integration

### Helper Class

Use the `BrowserStackPercyHelper` class for Playwright tests:

```typescript
import BrowserStackPercyHelper from '../helpers/browserstack-percy-helper';

const helper = BrowserStackPercyHelper;

// Capture Percy snapshot with BrowserStack context
await helper.capturePercySnapshot(page, 'Homepage', {
    widths: [375, 768, 1280],
    minHeight: 800
});

// Capture responsive snapshots
await helper.captureResponsiveSnapshots(page, 'Dashboard');

// Capture accessibility snapshot
await helper.captureAccessibilitySnapshot(page, 'Login Page', 'AA');
```

### Test Context

The helper supports ICTServe-specific test contexts:

```typescript
const context: ICTServeTestContext = {
    userType: 'guest',           // 'guest' | 'authenticated' | 'admin'
    userRole: 'staff',           // 'staff' | 'admin' | 'superuser'
    bahasaMelayuInterface: true, // Bahasa Melayu interface
    hybridArchitecture: true     // True Hybrid Architecture support
};

await helper.capturePercySnapshot(page, 'Helpdesk Form', {}, context);
```

## Running Tests

### Run BrowserStack Percy Integration Tests

```bash
# Run all BrowserStack integration tests
npx percy exec -- npx playwright test browserstack-percy-integration.spec.ts

# Run specific test suite
npx percy exec -- npx playwright test browserstack-percy-integration.spec.ts --grep "Cross-Browser"

# Run with BrowserStack enabled
BROWSERSTACK_ENABLED=true PERCY_ENABLED=true npx percy exec -- npx playwright test
```

### Run via npm Scripts

```bash
# Run Percy tests with BrowserStack
npm run test:percy:browserstack

# Run cross-browser visual tests
npm run test:percy:cross-browser

# Run mobile visual tests
npm run test:percy:mobile
```

## Test Suites

### 1. Cross-Browser Visual Testing
Tests visual consistency across Chrome, Firefox, Safari, and Edge.

### 2. Mobile Visual Testing
Tests visual regression on real mobile devices (iPhone, Samsung Galaxy, iPad).

### 3. Accessibility Testing with Percy
Combines WCAG compliance scanning with Percy visual validation.

### 4. Live Session Debugging
Enables real-time visual debugging through BrowserStack Live.

### 5. Test Management Integration
Organizes Percy visual test cases in BrowserStack Test Management.

### 6. ICTServe Hybrid Architecture Testing
Tests both guest and authenticated user workflows.

### 7. Performance Visual Testing
Validates visual performance across different load states.

### 8. Livewire Component Testing
Tests Livewire 3.7.3 component visual states.

### 9. Filament Admin Testing
Tests Filament 4.3.1 admin panel visual consistency.

## ICTServe v3.6.1 Specific Features

### True Hybrid Architecture Support

- Guest user workflow visual testing
- Authenticated user workflow visual testing
- Admin panel visual testing
- Nullable user_id FK support

### Bahasa Melayu Interface

- Exclusive Bahasa Melayu interface validation
- Language consistency checking
- Language switcher exclusion in snapshots

### WCAG 2.2 AA Compliance

- Accessibility visual validation
- Focus indicator testing
- Color contrast validation

### Technology Stack Integration

- Laravel 12.43.1 compatibility
- Livewire 3.7.3 component testing
- Filament 4.3.1 admin testing
- Playwright 1.56.1 integration
- Tailwind 4.1.18 styling validation

## Troubleshooting

### Common Issues

1. **BrowserStack credentials not configured**
   - Ensure `BROWSERSTACK_USERNAME` and `BROWSERSTACK_ACCESS_KEY` are set
   - Validate credentials using `browserstack_validate_config` tool

2. **Percy snapshots not capturing**
   - Verify `PERCY_ENABLED=true` and `PERCY_TOKEN` is set
   - Check Percy CLI is installed: `npm install @percy/cli @percy/playwright`

3. **MCP server not starting**
   - Run `php artisan mcp:browserstack` manually to check for errors
   - Verify Laravel MCP package is installed

4. **Cross-browser tests failing**
   - Check BrowserStack account has sufficient parallel sessions
   - Verify browser/device capabilities are valid

### Debug Mode

Enable debug logging:

```bash
BROWSERSTACK_LOGGING_ENABLED=true
BROWSERSTACK_LOG_LEVEL=debug
```

Check logs at: `storage/logs/browserstack.log`

## Resources

- [BrowserStack Documentation](https://www.browserstack.com/docs)
- [Percy Documentation](https://docs.percy.io/)
- [Playwright Documentation](https://playwright.dev/docs/intro)
- [Laravel MCP Documentation](https://laravel.com/docs/mcp)
