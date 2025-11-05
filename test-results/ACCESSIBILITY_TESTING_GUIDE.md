# ICTServe Accessibility Testing Guide - Task 10.1

## Overview

This document provides comprehensive guidance for executing Task 10.1: Automated Accessibility Testing for the ICTServe frontend pages redesign project.

**Requirements**: 25.1, 6.1, 24.1  
**Standards**: WCAG 2.2 Level AA, D12 UI/UX Design Guide, D14 UI/UX Style Guide  
**Target**: 100/100 Lighthouse Accessibility Score

## Testing Tools

### 1. Axe-Core (Automated Testing)

- **Tool**: @axe-core/playwright
- **Coverage**: WCAG 2.0, 2.1, 2.2 Level A and AA
- **Tests**: 90+ accessibility rules
- **Integration**: Playwright test framework

### 2. Lighthouse (Performance + Accessibility)

- **Tool**: Chrome DevTools Lighthouse
- **Metrics**: Performance, Accessibility, Best Practices, SEO
- **Target Scores**:
  - Accessibility: 100/100
  - Performance: 90+/100
  - Best Practices: 100/100

### 3. Manual Testing Tools

- **Screen Readers**: NVDA (Windows), JAWS (Windows), VoiceOver (macOS/iOS)
- **Keyboard Navigation**: Tab, Shift+Tab, Arrow keys, Enter, Space
- **Browser DevTools**: Accessibility tree, contrast checker

## Running Automated Tests

### Quick Start

```bash
# Install dependencies (if not already installed)
npm install

# Run comprehensive accessibility tests
npm run test:accessibility

# Generate accessibility report
npm run test:accessibility:report

# Run both tests and generate report
npm run test:accessibility:all
```

### Test Coverage

#### Guest Pages (No Authentication Required)

- ✅ Welcome Page (/)
- ✅ Accessibility Statement (/accessibility)
- ✅ Contact Page (/contact)
- ✅ Services Page (/services)
- ✅ Helpdesk Ticket Form (/helpdesk/create)
- ✅ Asset Loan Application Form (/loan/guest/apply)

#### Authenticated Pages (Staff Portal)

- ✅ Staff Dashboard (/staff/dashboard)
- ✅ User Profile (/staff/profile)
- ✅ Submission History (/staff/history)
- ✅ Claim Submissions (/staff/claim-submissions)

#### Approver Pages (Grade 41+)

- ✅ Approval Interface (/staff/approvals)

#### Admin Pages (Filament Panel)

- ✅ Admin Dashboard (/admin)
- ✅ Helpdesk Tickets Management (/admin/helpdesk-tickets)
- ✅ Loan Applications Management (/admin/loan-applications)
- ✅ Assets Management (/admin/assets)

### Mobile Testing

- ✅ Mobile viewport (390x844 - iPhone 12 Pro)
- ✅ Touch target size validation (44x44px minimum)
- ✅ Responsive design verification

## WCAG 2.2 Level AA Success Criteria

### Perceivable

- **1.3.1 Info and Relationships**: Semantic HTML5, ARIA landmarks
- **1.4.3 Contrast (Minimum)**: 4.5:1 for text, 3:1 for UI components
- **1.4.11 Non-text Contrast**: 3:1 for UI components and graphics

### Operable

- **2.1.1 Keyboard**: Full keyboard accessibility
- **2.4.1 Bypass Blocks**: Skip links for efficient navigation
- **2.4.6 Headings and Labels**: Proper heading hierarchy
- **2.4.7 Focus Visible**: Visible focus indicators (3:1 contrast minimum)
- **2.4.11 Focus Not Obscured (NEW)**: Focus not hidden by other content
- **2.5.8 Target Size (Minimum) (NEW)**: 44×44px minimum touch targets

### Understandable

- **3.1.1 Language of Page**: Proper lang attribute
- **3.2.1 On Focus**: No unexpected context changes
- **3.3.1 Error Identification**: Clear error messages
- **3.3.2 Labels or Instructions**: Proper form labels

### Robust

- **4.1.2 Name, Role, Value**: Proper ARIA attributes
- **4.1.3 Status Messages**: ARIA live regions for dynamic content

## Test Results Interpretation

### Violation Severity Levels

1. **Critical** (Must Fix Immediately)
   - Blocks access for users with disabilities
   - Examples: Missing alt text, insufficient contrast, keyboard traps
   - Action: Fix before deployment

2. **Serious** (Must Fix Before Release)
   - Significantly impacts accessibility
   - Examples: Missing form labels, improper heading structure
   - Action: Fix in current sprint

3. **Moderate** (Should Fix)
   - Impacts usability for some users
   - Examples: Redundant links, missing landmarks
   - Action: Fix in next sprint

4. **Minor** (Nice to Fix)
   - Minor usability improvements
   - Examples: Missing title attributes, minor ARIA improvements
   - Action: Fix when time permits

### Success Metrics

- **Target**: 0 critical and serious violations
- **Acceptable**: 0-5 moderate violations (with documented justification)
- **Goal**: 100% WCAG 2.2 Level AA compliance

## Accessibility Report

After running tests, reports are generated in:

- **HTML Report**: `test-results/accessibility-reports/accessibility-report.html`
- **JSON Report**: `test-results/accessibility-reports/accessibility-report.json`

### Report Contents

- Summary statistics (violations, passes, compliance rate)
- Per-page results with violation details
- Affected elements with HTML snippets
- Links to WCAG documentation for each violation
- Severity classification (critical, serious, moderate, minor)

## Manual Testing Checklist

### Keyboard Navigation

- [ ] Tab through all interactive elements
- [ ] Verify focus indicators are visible (3-4px outline, 2px offset, 3:1 contrast)
- [ ] Test skip links (Alt+M for main content, Alt+S for sidebar, Alt+U for user menu)
- [ ] Verify no keyboard traps
- [ ] Test form submission with Enter key
- [ ] Test dropdown menus with Arrow keys

### Screen Reader Testing

#### NVDA (Windows)

- [ ] Navigate by headings (H key)
- [ ] Navigate by landmarks (D key)
- [ ] Navigate by forms (F key)
- [ ] Verify all images have alt text
- [ ] Verify form labels are announced
- [ ] Test ARIA live regions for dynamic content

#### JAWS (Windows)

- [ ] Same tests as NVDA
- [ ] Verify table navigation (Ctrl+Alt+Arrow keys)
- [ ] Test forms mode (Enter/Escape)

#### VoiceOver (macOS/iOS)

- [ ] Navigate with VO+Right Arrow
- [ ] Test rotor navigation (VO+U)
- [ ] Verify touch gestures on iOS
- [ ] Test form controls

### Visual Testing

- [ ] Verify color contrast with browser DevTools
- [ ] Test with 200% zoom (text should reflow)
- [ ] Test with high contrast mode
- [ ] Verify touch target sizes (44x44px minimum)
- [ ] Test responsive design (320px, 768px, 1280px, 1920px)

### Form Testing

- [ ] Verify all form fields have labels
- [ ] Test error messages (clear, specific, accessible)
- [ ] Test required field indicators
- [ ] Verify autocomplete attributes
- [ ] Test validation feedback (ARIA live regions)

## Common Issues and Fixes

### Issue 1: Insufficient Color Contrast
**Problem**: Text or UI components don't meet 4.5:1 (text) or 3:1 (UI) contrast ratio  
**Fix**: Use compliant color palette:

- Primary: #0056b3 (6.8:1 contrast)
- Success: #198754 (4.9:1 contrast)
- Warning: #ff8c00 (4.5:1 contrast)
- Danger: #b50c0c (8.2:1 contrast)

### Issue 2: Missing Form Labels
**Problem**: Form inputs lack associated labels  
**Fix**: Use `<label for="input-id">` or `aria-label` attribute

### Issue 3: Missing Alt Text
**Problem**: Images lack descriptive alt text  
**Fix**: Add `alt="descriptive text"` to all images (empty alt="" for decorative images)

### Issue 4: Keyboard Trap
**Problem**: Users can't navigate away from element with keyboard  
**Fix**: Ensure Tab/Shift+Tab works, implement proper focus management in modals

### Issue 5: Missing Focus Indicators
**Problem**: No visible focus indicator on interactive elements  
**Fix**: Add CSS: `outline: 3px solid #0056b3; outline-offset: 2px;`

### Issue 6: Small Touch Targets
**Problem**: Interactive elements smaller than 44x44px  
**Fix**: Add padding or increase element size to meet minimum

## Integration with CI/CD

### GitHub Actions Workflow

```yaml
name: Accessibility Testing

on: [push, pull_request]

jobs:
  accessibility:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: actions/setup-node@v3
        with:
          node-version: '18'
      - run: npm install
      - run: npm run test:accessibility:all
      - uses: actions/upload-artifact@v3
        with:
          name: accessibility-reports
          path: test-results/accessibility-reports/
```

## Next Steps (Task 10.2-10.5)

After completing automated testing (Task 10.1):

1. **Task 10.2**: Manual accessibility testing with screen readers
2. **Task 10.3**: Fix all identified accessibility issues
3. **Task 10.4**: Cross-browser testing (Chrome, Firefox, Safari, Edge)
4. **Task 10.5**: Final validation and stakeholder approval

## Resources

### WCAG 2.2 Documentation

- [WCAG 2.2 Guidelines](https://www.w3.org/WAI/WCAG22/quickref/)
- [Understanding WCAG 2.2](https://www.w3.org/WAI/WCAG22/Understanding/)
- [Techniques for WCAG 2.2](https://www.w3.org/WAI/WCAG22/Techniques/)

### Testing Tools

- [axe DevTools](https://www.deque.com/axe/devtools/)
- [Lighthouse](https://developers.google.com/web/tools/lighthouse)
- [WAVE](https://wave.webaim.org/)
- [Pa11y](https://pa11y.org/)

### Screen Readers

- [NVDA](https://www.nvaccess.org/) (Free, Windows)
- [JAWS](https://www.freedomscientific.com/products/software/jaws/) (Commercial, Windows)
- [VoiceOver](https://www.apple.com/accessibility/voiceover/) (Built-in, macOS/iOS)

### ICTServe Documentation

- D12: UI/UX Design Guide
- D14: UI/UX Style Guide
- Requirements: 25.1, 6.1, 24.1

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-05  
**Author**: Frontend Engineering Team  
**Status**: Ready for Execution
