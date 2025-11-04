# Compliance Verification Procedures

**ICTServe System**  
**Version**: 1.0.0  
**Date**: November 4, 2025  
**Task**: 15.1 - Compliance Verification and Validation  
**Requirements**: D03-FR-23.1-23.6, 18.4-18.5, 5.1-5.5, 6.1-6.5

---

## 1. Overview

This document defines comprehensive procedures for verifying ICTServe system compliance with:

- **WCAG 2.2 Level AA** - Web Content Accessibility Guidelines
- **Core Web Vitals** - Performance standards
- **PDPA 2010** - Malaysian Personal Data Protection Act
- **ISO/IEC/IEEE Standards** - 12207, 29148, 15288
- **MyGOV Digital Service Standards** v2.1.0
- **Browser Compatibility** - Chrome 90+, Firefox 88+, Safari 14+, Edge 90+

---

## 2. WCAG 2.2 Level AA Compliance Verification

### 2.1 Automated Accessibility Testing

**Tools Required**:

- axe DevTools (browser extension)
- Lighthouse (Chrome DevTools)
- WAVE (browser extension)
- Pa11y (CLI tool)

**Automated Test Procedure**:

```powershell
# Install Pa11y
npm install -g pa11y pa11y-ci

# Run Pa11y on key pages
pa11y http://localhost:8000/helpdesk --standard WCAG2AA
pa11y http://localhost:8000/loan --standard WCAG2AA
pa11y http://localhost:8000/login --standard WCAG2AA
pa11y http://localhost:8000/admin --standard WCAG2AA

# Run Pa11y CI for multiple pages
pa11y-ci --config .pa11yci.json
```

**Pa11y Configuration** (`.pa11yci.json`):

```json
{
  "defaults": {
    "standard": "WCAG2AA",
    "timeout": 30000,
    "wait": 1000
  },
  "urls": [
    "http://localhost:8000/",
    "http://localhost:8000/helpdesk",
    "http://localhost:8000/loan",
    "http://localhost:8000/login",
    "http://localhost:8000/admin"
  ]
}
```

### 2.2 Manual Accessibility Testing Checklist

#### 2.2.1 Color Contrast Verification

**Requirement**: Minimum 4.5:1 for text, 3:1 for UI components

| Element Type | Color Combination | Contrast Ratio | Status | Notes |
|--------------|-------------------|----------------|--------|-------|
| Body Text | #000000 on #FFFFFF | 21:1 | ✅ PASS | |
| Primary Button | #FFFFFF on #0056b3 | 6.8:1 | ✅ PASS | |
| Success Message | #FFFFFF on #198754 | 4.9:1 | ✅ PASS | |
| Warning Message | #000000 on #ff8c00 | 4.5:1 | ✅ PASS | |
| Danger Message | #FFFFFF on #b50c0c | 8.2:1 | ✅ PASS | |
| Link Text | #0056b3 on #FFFFFF | 6.8:1 | ✅ PASS | |
| Focus Indicator | #0056b3 outline | 3:1 | ⬜ VERIFY | |

**Testing Tools**:

- WebAIM Contrast Checker: <https://webaim.org/resources/contrastchecker/>
- Chrome DevTools Color Picker (shows contrast ratio)

**Procedure**:

1. Open page in Chrome DevTools
2. Inspect element
3. Check color picker for contrast ratio
4. Document any failures
5. Update color palette if needed

#### 2.2.2 Keyboard Navigation Testing

**Requirement**: All interactive elements accessible via keyboard

| Page | Test | Expected Behavior | Status | Notes |
|------|------|-------------------|--------|-------|
| Helpdesk Form | Tab through form | All fields focusable | ⬜ | |
| Helpdesk Form | Submit with Enter | Form submits | ⬜ | |
| Helpdesk Form | Escape closes modal | Modal closes | ⬜ | |
| Loan Form | Tab through form | All fields focusable | ⬜ | |
| Loan Form | Date picker keyboard | Arrow keys work | ⬜ | |
| Login Page | Tab to login button | Button focusable | ⬜ | |
| Admin Panel | Tab through table | All actions accessible | ⬜ | |

**Testing Procedure**:

1. Disconnect mouse
2. Use only keyboard (Tab, Shift+Tab, Enter, Escape, Arrow keys)
3. Navigate through entire page
4. Verify all interactive elements are accessible
5. Check focus indicators are visible (3-4px outline, 2px offset)
6. Document any keyboard traps or inaccessible elements

#### 2.2.3 Screen Reader Testing

**Requirement**: Compatible with NVDA (Windows) and VoiceOver (macOS)

**Screen Readers to Test**:

- NVDA (Windows) - Free, open-source
- JAWS (Windows) - Commercial, widely used
- VoiceOver (macOS) - Built-in

**Testing Checklist**:

| Test | Expected Behavior | NVDA | JAWS | VoiceOver |
|------|-------------------|------|------|-----------|
| Page title announced | Correct page title read | ⬜ | ⬜ | ⬜ |
| Headings navigable | H key navigates headings | ⬜ | ⬜ | ⬜ |
| Landmarks navigable | D key navigates landmarks | ⬜ | ⬜ | ⬜ |
| Form labels read | Labels associated with inputs | ⬜ | ⬜ | ⬜ |
| Error messages announced | Errors read immediately | ⬜ | ⬜ | ⬜ |
| Success messages announced | Success read immediately | ⬜ | ⬜ | ⬜ |
| Button purpose clear | Button text descriptive | ⬜ | ⬜ | ⬜ |
| Link purpose clear | Link text descriptive | ⬜ | ⬜ | ⬜ |
| Images have alt text | Alt text read correctly | ⬜ | ⬜ | ⬜ |
| Tables have headers | Table structure clear | ⬜ | ⬜ | ⬜ |

**NVDA Testing Procedure** (Windows):

1. Download NVDA from <https://www.nvaccess.org/>
2. Install and start NVDA (Ctrl+Alt+N)
3. Navigate to ICTServe page
4. Use NVDA commands:
   - H: Next heading
   - D: Next landmark
   - F: Next form field
   - B: Next button
   - K: Next link
   - T: Next table
5. Document any issues

**VoiceOver Testing Procedure** (macOS):

1. Enable VoiceOver (Cmd+F5)
2. Navigate to ICTServe page
3. Use VoiceOver commands:
   - VO+Right Arrow: Next item
   - VO+Cmd+H: Next heading
   - VO+Cmd+J: Next form control
   - VO+Cmd+L: Next link
4. Document any issues

#### 2.2.4 Touch Target Size Verification

**Requirement**: Minimum 44×44px for all interactive elements

**Testing Procedure**:

1. Open Chrome DevTools
2. Enable device emulation (mobile view)
3. Measure interactive elements
4. Document any elements < 44×44px

**Measurement Tool** (Chrome DevTools):

```javascript
// Run in console to check all interactive elements
document.querySelectorAll('button, a, input, select, textarea').forEach(el => {
  const rect = el.getBoundingClientRect();
  if (rect.width < 44 || rect.height < 44) {
    console.warn('Touch target too small:', el, `${rect.width}×${rect.height}px`);
  }
});
```

**Touch Target Checklist**:

| Element Type | Minimum Size | Actual Size | Status | Notes |
|--------------|--------------|-------------|--------|-------|
| Primary Buttons | 44×44px | ⬜ | ⬜ | |
| Secondary Buttons | 44×44px | ⬜ | ⬜ | |
| Form Inputs | 44×44px | ⬜ | ⬜ | |
| Checkboxes | 44×44px | ⬜ | ⬜ | |
| Radio Buttons | 44×44px | ⬜ | ⬜ | |
| Dropdown Selects | 44×44px | ⬜ | ⬜ | |
| Links in Text | 44×44px | ⬜ | ⬜ | |
| Icon Buttons | 44×44px | ⬜ | ⬜ | |
| Close Buttons | 44×44px | ⬜ | ⬜ | |

---

## 3. Core Web Vitals Verification

### 3.1 Performance Metrics Targets

| Metric | Target | Measurement Tool | Frequency |
|--------|--------|------------------|-----------|
| LCP (Largest Contentful Paint) | < 2.5s | Lighthouse, WebPageTest | Every deploy |
| FID (First Input Delay) | < 100ms | Real User Monitoring | Continuous |
| CLS (Cumulative Layout Shift) | < 0.1 | Lighthouse, WebPageTest | Every deploy |
| TTFB (Time to First Byte) | < 600ms | WebPageTest, Server logs | Continuous |
| Lighthouse Performance | 90+ | Lighthouse CI | Every deploy |
| Lighthouse Accessibility | 100 | Lighthouse CI | Every deploy |

### 3.2 Lighthouse Testing Procedure

**Manual Testing**:

1. Open Chrome DevTools (F12)
2. Navigate to "Lighthouse" tab
3. Select "Desktop" or "Mobile"
4. Check all categories
5. Click "Analyze page load"
6. Review scores and recommendations
7. Document any scores below target

**Automated Testing** (Lighthouse CI):

```powershell
# Install Lighthouse CI
npm install -g @lhci/cli

# Run Lighthouse CI
lhci autorun --config=lighthouserc.json
```

**Lighthouse CI Configuration** (`lighthouserc.json`):

```json
{
  "ci": {
    "collect": {
      "url": [
        "http://localhost:8000/",
        "http://localhost:8000/helpdesk",
        "http://localhost:8000/loan",
        "http://localhost:8000/login"
      ],
      "numberOfRuns": 3
    },
    "assert": {
      "assertions": {
        "categories:performance": ["error", {"minScore": 0.9}],
        "categories:accessibility": ["error", {"minScore": 1.0}],
        "categories:best-practices": ["error", {"minScore": 1.0}],
        "categories:seo": ["error", {"minScore": 1.0}]
      }
    },
    "upload": {
      "target": "temporary-public-storage"
    }
  }
}
```

### 3.3 Real User Monitoring (RUM)

**Implementation**:

```javascript
// resources/js/web-vitals-monitoring.js
import {getCLS, getFID, getLCP, getTTFB} from 'web-vitals';

function sendToAnalytics(metric) {
  // Send to your analytics endpoint
  fetch('/api/analytics/web-vitals', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      name: metric.name,
      value: metric.value,
      id: metric.id,
      delta: metric.delta,
      rating: metric.rating,
      navigationType: metric.navigationType,
      url: window.location.href,
      userAgent: navigator.userAgent
    })
  });
}

// Monitor Core Web Vitals
getCLS(sendToAnalytics);
getFID(sendToAnalytics);
getLCP(sendToAnalytics);
getTTFB(sendToAnalytics);
```

**Monitoring Dashboard**:

Create a dashboard to track Core Web Vitals over time:

| Metric | Current | 7-Day Avg | 30-Day Avg | Trend | Status |
|--------|---------|-----------|------------|-------|--------|
| LCP | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| FID | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| CLS | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| TTFB | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |

---

## 4. PDPA 2010 Compliance Verification

### 4.1 Data Protection Principles

**Seven Principles of PDPA 2010**:

1. General Principle
2. Notice and Choice Principle
3. Disclosure Principle
4. Security Principle
5. Retention Principle
6. Data Integrity Principle
7. Access Principle

### 4.2 Compliance Checklist

#### 4.2.1 Consent Management

| Requirement | Implementation | Status | Evidence |
|-------------|----------------|--------|----------|
| Explicit consent obtained | Checkbox on forms | ⬜ | |
| Consent purpose clearly stated | Privacy notice displayed | ⬜ | |
| Consent recorded in database | `consent_given_at` field | ⬜ | |
| Consent can be withdrawn | Profile settings option | ⬜ | |
| Consent withdrawal recorded | Audit log entry | ⬜ | |

#### 4.2.2 Data Retention

| Data Type | Retention Period | Deletion Method | Status | Evidence |
|-----------|------------------|-----------------|--------|----------|
| Audit logs | 7 years | Automated purge | ⬜ | |
| User accounts | Until deletion request | Soft delete | ⬜ | |
| Helpdesk tickets | 7 years | Automated purge | ⬜ | |
| Loan applications | 7 years | Automated purge | ⬜ | |
| Email logs | 90 days | Automated purge | ⬜ | |

#### 4.2.3 Data Security

| Security Control | Implementation | Status | Evidence |
|------------------|----------------|--------|----------|
| Encryption at rest | AES-256 | ⬜ | |
| Encryption in transit | TLS 1.3 | ⬜ | |
| Password hashing | bcrypt | ⬜ | |
| Access control | RBAC (4 roles) | ⬜ | |
| Audit logging | Laravel Auditing | ⬜ | |
| Data backup | Daily automated | ⬜ | |
| Backup encryption | AES-256 | ⬜ | |

#### 4.2.4 Data Subject Rights

| Right | Implementation | Status | Evidence |
|-------|----------------|--------|----------|
| Right to Access | Profile page, data export | ⬜ | |
| Right to Correction | Profile edit form | ⬜ | |
| Right to Deletion | Account deletion request | ⬜ | |
| Right to Data Portability | CSV/JSON export | ⬜ | |
| Right to Object | Opt-out mechanisms | ⬜ | |
