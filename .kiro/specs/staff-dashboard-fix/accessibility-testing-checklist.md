# Staff Dashboard Accessibility Testing Checklist

## Overview

This document provides a comprehensive manual testing checklist for verifying WCAG 2.2 Level AA compliance on the staff dashboard. Use this checklist to systematically verify all accessibility requirements.

**Test Date**: ******\_******  
**Tester**: ******\_******  
**Browser**: ******\_******  
**Screen Reader** (if applicable): ******\_******

---

## 1. Keyboard Navigation Testing

### 1.1 Tab Order and Focus Indicators

**Objective**: Verify logical tab order and visible focus indicators

**Steps**:

1. Navigate to `/dashboard` while logged in
2. Press `Tab` key repeatedly to move through interactive elements
3. Verify focus indicators are visible on each element

**Expected Tab Order**:

-   [ ] Skip links (if present)
-   [ ] Refresh button
-   [ ] Statistics card links (4 cards: Open Tickets, Pending Loans, Approvals, Overdue)
-   [ ] Quick action buttons (New Ticket, Request Loan, View Services)
-   [ ] Recent activity links (Tickets and Loans sections)

**Focus Indicator Requirements**:

-   [ ] Focus ring is visible (3-4px outline)
-   [ ] Focus ring has 2px offset from element
-   [ ] Focus ring has minimum 3:1 contrast ratio
-   [ ] Focus ring color: MOTAC Blue (#0056b3)

**Screenshot**: `accessibility-keyboard-navigation.png`

---

### 1.2 Keyboard Shortcuts

**Objective**: Verify keyboard shortcuts work correctly

**Steps**:

1. Test `Alt+M` - Skip to main content
2. Test `Alt+S` - Skip to sidebar
3. Test `Alt+U` - Skip to user menu

**Checklist**:

-   [ ] `Alt+M` moves focus to main content
-   [ ] `Alt+S` moves focus to sidebar navigation
-   [ ] `Alt+U` moves focus to user menu
-   [ ] Shortcuts work in all major browsers

---

### 1.3 Interactive Element Activation

**Objective**: Verify all interactive elements can be activated via keyboard

**Steps**:

1. Tab to each interactive element
2. Press `Enter` or `Space` to activate

**Checklist**:

-   [ ] Refresh button activates with `Enter`
-   [ ] Statistics card links activate with `Enter`
-   [ ] Quick action buttons activate with `Enter`
-   [ ] Recent activity links activate with `Enter`
-   [ ] No keyboard traps (can tab out of all elements)

---

## 2. Screen Reader Testing

### 2.1 Screen Reader Compatibility

**Objective**: Verify dashboard works with screen readers (NVDA, JAWS, VoiceOver)

**Screen Reader**: ******\_****** (NVDA / JAWS / VoiceOver)

**Steps**:

1. Enable screen reader
2. Navigate to `/dashboard`
3. Use screen reader navigation commands

**Checklist**:

-   [ ] Page title is announced: "Dashboard - ICTServe"
-   [ ] Heading hierarchy is correct (H1 → H2 → H3)
-   [ ] Statistics are announced with values
-   [ ] Button labels are descriptive
-   [ ] Link text is meaningful (no "click here")
-   [ ] Loading states are announced
-   [ ] Status badges are announced correctly

---

### 2.2 ARIA Attributes

**Objective**: Verify ARIA attributes are correctly implemented

**Steps**:

1. Inspect elements using browser DevTools
2. Verify ARIA attributes

**Checklist**:

-   [ ] Refresh button has `aria-label="Refresh dashboard"`
-   [ ] Decorative icons have `aria-hidden="true"`
-   [ ] Recent activity lists have `role="list"`
-   [ ] Loading overlay has ARIA live region
-   [ ] Statistics cards have descriptive labels

**DevTools Inspection**:

```html
<!-- Expected ARIA structure -->
<button aria-label="Refresh dashboard">...</button>
<svg aria-hidden="true">...</svg>
<ul role="list">
    ...
</ul>
<div wire:loading aria-live="polite">...</div>
```

---

### 2.3 Semantic HTML

**Objective**: Verify proper semantic HTML structure

**Checklist**:

-   [ ] Page has single `<h1>` heading: "Dashboard"
-   [ ] Sections use `<h2>` headings: "Quick Actions", "My Recent Tickets", "My Recent Loans"
-   [ ] Statistics use `<dl>`, `<dt>`, `<dd>` elements
-   [ ] Lists use `<ul>` and `<li>` elements
-   [ ] Buttons use `<button>` elements (not `<div>` or `<a>`)
-   [ ] Links use `<a>` elements with `href` attributes

---

## 3. Color Contrast Testing

### 3.1 Text Contrast

**Objective**: Verify text meets WCAG AA 4.5:1 contrast ratio

**Tool**: Browser DevTools or [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/)

**Elements to Test**:

| Element          | Foreground | Background | Ratio    | Pass/Fail |
| ---------------- | ---------- | ---------- | -------- | --------- |
| H1 Heading       | #111827    | #FFFFFF    | \_\_\_:1 | [ ]       |
| Body Text        | #6B7280    | #FFFFFF    | \_\_\_:1 | [ ]       |
| Button Text      | #FFFFFF    | #0056b3    | \_\_\_:1 | [ ]       |
| Link Text        | #0056b3    | #FFFFFF    | \_\_\_:1 | [ ]       |
| Statistics Value | #111827    | #FFFFFF    | \_\_\_:1 | [ ]       |

**Required Ratios**:

-   Normal text (< 18pt): 4.5:1 minimum
-   Large text (≥ 18pt or 14pt bold): 3:1 minimum

---

### 3.2 UI Component Contrast

**Objective**: Verify UI components meet WCAG AA 3:1 contrast ratio

**Elements to Test**:

| Component             | Color   | Background | Ratio    | Pass/Fail |
| --------------------- | ------- | ---------- | -------- | --------- |
| Primary Icon (Blue)   | #0056b3 | #FFFFFF    | \_\_\_:1 | [ ]       |
| Success Icon (Green)  | #198754 | #FFFFFF    | \_\_\_:1 | [ ]       |
| Warning Icon (Orange) | #ff8c00 | #FFFFFF    | \_\_\_:1 | [ ]       |
| Danger Icon (Red)     | #b50c0c | #FFFFFF    | \_\_\_:1 | [ ]       |
| Button Border         | #D1D5DB | #FFFFFF    | \_\_\_:1 | [ ]       |
| Focus Ring            | #0056b3 | #FFFFFF    | \_\_\_:1 | [ ]       |

**Required Ratio**: 3:1 minimum for UI components

---

### 3.3 Compliant Color Palette Verification

**Objective**: Verify only WCAG-compliant colors are used

**Compliant Colors** (from `tailwind.config.js`):

-   Primary: `#0056b3` (6.8:1 contrast)
-   Success: `#198754` (4.9:1 contrast)
-   Warning: `#ff8c00` (4.5:1 contrast)
-   Danger: `#b50c0c` (8.2:1 contrast)

**Checklist**:

-   [ ] No deprecated colors used (old warning, old danger)
-   [ ] All status badges use compliant colors
-   [ ] All icons use compliant colors
-   [ ] All buttons use compliant colors

**Screenshot**: `accessibility-color-contrast.png`

---

## 4. Touch Target Testing

### 4.1 Minimum Size Requirements

**Objective**: Verify all interactive elements meet WCAG 2.5.8 (44×44px minimum)

**Tool**: Browser DevTools → Inspect Element → Computed Styles

**Elements to Measure**:

| Element              | Width    | Height   | Pass/Fail |
| -------------------- | -------- | -------- | --------- |
| Refresh Button       | \_\_\_px | \_\_\_px | [ ]       |
| New Ticket Button    | \_\_\_px | \_\_\_px | [ ]       |
| Request Loan Button  | \_\_\_px | \_\_\_px | [ ]       |
| View Services Button | \_\_\_px | \_\_\_px | [ ]       |
| Statistics Card Link | \_\_\_px | \_\_\_px | [ ]       |
| Recent Activity Link | \_\_\_px | \_\_\_px | [ ]       |

**Required Size**: Minimum 44×44px for all interactive elements

---

### 4.2 Touch Target Spacing

**Objective**: Verify adequate spacing between touch targets

**Checklist**:

-   [ ] Quick action buttons have visible gaps (gap-4 = 16px)
-   [ ] Statistics cards have visible gaps (gap-5 = 20px)
-   [ ] No overlapping touch targets
-   [ ] Touch targets don't extend beyond visible boundaries

**Screenshot**: `accessibility-touch-targets.png`

---

## 5. Responsive Accessibility Testing

### 5.1 Mobile Viewport (375×667px)

**Device**: iPhone SE / Small Mobile

**Checklist**:

-   [ ] Statistics grid displays 1 column
-   [ ] All touch targets remain ≥44×44px
-   [ ] No horizontal scrolling
-   [ ] Text remains readable (no truncation)
-   [ ] Focus indicators visible
-   [ ] Buttons stack vertically with adequate spacing

**Screenshot**: `accessibility-mobile-viewport.png`

---

### 5.2 Tablet Viewport (768×1024px)

**Device**: iPad / Tablet

**Checklist**:

-   [ ] Statistics grid displays 2 columns
-   [ ] All touch targets remain ≥44×44px
-   [ ] No horizontal scrolling
-   [ ] Text remains readable
-   [ ] Focus indicators visible
-   [ ] Layout adapts smoothly

**Screenshot**: `accessibility-tablet-viewport.png`

---

### 5.3 Desktop Viewport (1280×720px)

**Device**: Desktop / Laptop

**Checklist**:

-   [ ] Statistics grid displays 4 columns (or 3 for non-approvers)
-   [ ] All touch targets remain ≥44×44px
-   [ ] No horizontal scrolling
-   [ ] Text remains readable
-   [ ] Focus indicators visible
-   [ ] Layout uses full width effectively

**Screenshot**: `accessibility-desktop-viewport.png`

---

## 6. Dynamic Content Testing

### 6.1 Loading States

**Objective**: Verify loading states are accessible

**Steps**:

1. Click refresh button
2. Observe loading overlay

**Checklist**:

-   [ ] Loading overlay is announced by screen reader
-   [ ] Loading text is visible: "Refreshing..."
-   [ ] Loading spinner has animation
-   [ ] Focus is managed during loading
-   [ ] Loading overlay has `aria-live="polite"`

---

### 6.2 Real-Time Updates

**Objective**: Verify wire:poll updates are accessible

**Steps**:

1. Wait for 30-second auto-refresh (wire:poll.30s)
2. Observe updates

**Checklist**:

-   [ ] Updates don't disrupt keyboard navigation
-   [ ] Screen reader announces updates
-   [ ] Focus position is maintained
-   [ ] No layout shifts (CLS < 0.1)

---

## 7. Browser Compatibility

### 7.1 Cross-Browser Testing

**Objective**: Verify accessibility works across browsers

**Browsers to Test**:

-   [ ] Chrome (latest)
-   [ ] Firefox (latest)
-   [ ] Safari (latest)
-   [ ] Edge (latest)

**Checklist for Each Browser**:

-   [ ] Focus indicators visible
-   [ ] Keyboard navigation works
-   [ ] Color contrast maintained
-   [ ] Touch targets adequate
-   [ ] ARIA attributes recognized

---

## 8. Automated Testing Tools

### 8.1 Lighthouse Accessibility Audit

**Tool**: Chrome DevTools → Lighthouse

**Steps**:

1. Open Chrome DevTools
2. Navigate to Lighthouse tab
3. Select "Accessibility" category
4. Run audit

**Target Score**: 100/100

**Checklist**:

-   [ ] Score: \_\_\_/100
-   [ ] No critical issues
-   [ ] No serious issues
-   [ ] Minor issues documented

**Screenshot**: `accessibility-lighthouse-report.png`

---

### 8.2 axe DevTools

**Tool**: [axe DevTools Extension](https://www.deque.com/axe/devtools/)

**Steps**:

1. Install axe DevTools extension
2. Navigate to `/dashboard`
3. Run axe scan

**Checklist**:

-   [ ] 0 critical issues
-   [ ] 0 serious issues
-   [ ] Minor issues documented
-   [ ] Best practices followed

**Screenshot**: `accessibility-axe-report.png`

---

### 8.3 WAVE Evaluation Tool

**Tool**: [WAVE Browser Extension](https://wave.webaim.org/extension/)

**Steps**:

1. Install WAVE extension
2. Navigate to `/dashboard`
3. Run WAVE evaluation

**Checklist**:

-   [ ] 0 errors
-   [ ] 0 contrast errors
-   [ ] Alerts reviewed and justified
-   [ ] Structure is logical

**Screenshot**: `accessibility-wave-report.png`

---

## 9. Compliance Verification

### 9.1 WCAG 2.2 Level AA Checklist

**Principle 1: Perceivable**

-   [ ] 1.1.1 Non-text Content (A)
-   [ ] 1.3.1 Info and Relationships (A)
-   [ ] 1.3.2 Meaningful Sequence (A)
-   [ ] 1.4.1 Use of Color (A)
-   [ ] 1.4.3 Contrast (Minimum) (AA) - 4.5:1 text, 3:1 UI
-   [ ] 1.4.10 Reflow (AA) - No horizontal scroll at 320px
-   [ ] 1.4.11 Non-text Contrast (AA) - 3:1 for UI components
-   [ ] 1.4.12 Text Spacing (AA)

**Principle 2: Operable**

-   [ ] 2.1.1 Keyboard (A)
-   [ ] 2.1.2 No Keyboard Trap (A)
-   [ ] 2.4.1 Bypass Blocks (A) - Skip links
-   [ ] 2.4.2 Page Titled (A)
-   [ ] 2.4.3 Focus Order (A)
-   [ ] 2.4.6 Headings and Labels (AA)
-   [ ] 2.4.7 Focus Visible (AA)
-   [ ] 2.5.8 Target Size (Minimum) (AA) - 44×44px

**Principle 3: Understandable**

-   [ ] 3.1.1 Language of Page (A)
-   [ ] 3.2.1 On Focus (A)
-   [ ] 3.2.2 On Input (A)
-   [ ] 3.3.1 Error Identification (A)
-   [ ] 3.3.2 Labels or Instructions (A)

**Principle 4: Robust**

-   [ ] 4.1.1 Parsing (A)
-   [ ] 4.1.2 Name, Role, Value (A)
-   [ ] 4.1.3 Status Messages (AA)

---

### 9.2 D12 §9 Compliance

**ICTServe Specific Requirements**:

-   [ ] Compliant color palette used throughout
-   [ ] MOTAC branding maintained
-   [ ] Bilingual support (Malay/English)
-   [ ] Core Web Vitals targets met (LCP <2.5s, FID <100ms, CLS <0.1)
-   [ ] Audit trail for accessibility changes

---

## 10. Issue Tracking

### Issues Found

| #   | Severity | Issue Description | WCAG Criterion | Status |
| --- | -------- | ----------------- | -------------- | ------ |
| 1   |          |                   |                |        |
| 2   |          |                   |                |        |
| 3   |          |                   |                |        |

**Severity Levels**:

-   **Critical**: Blocks access for users with disabilities
-   **Serious**: Significantly impacts accessibility
-   **Moderate**: Impacts some users
-   **Minor**: Best practice improvement

---

## 11. Sign-Off

### Testing Summary

**Total Tests**: **\_**  
**Passed**: **\_**  
**Failed**: **\_**  
**Pass Rate**: **\_**%

**Overall Compliance**: [ ] WCAG 2.2 Level AA Compliant

**Tester Signature**: **********\_**********  
**Date**: **********\_**********

**Reviewer Signature**: **********\_**********  
**Date**: **********\_**********

---

## Appendix A: Testing Tools

### Recommended Tools

1. **Screen Readers**:

    - NVDA (Windows) - Free
    - JAWS (Windows) - Commercial
    - VoiceOver (macOS/iOS) - Built-in

2. **Browser Extensions**:

    - axe DevTools
    - WAVE Evaluation Tool
    - Lighthouse (Chrome DevTools)
    - Color Contrast Analyzer

3. **Online Tools**:
    - WebAIM Contrast Checker
    - WAVE Web Accessibility Evaluation Tool
    - Accessibility Insights

### Testing Resources

-   [WCAG 2.2 Guidelines](https://www.w3.org/WAI/WCAG22/quickref/)
-   [WebAIM WCAG 2 Checklist](https://webaim.org/standards/wcag/checklist)
-   [Deque University](https://dequeuniversity.com/)
-   [A11y Project Checklist](https://www.a11yproject.com/checklist/)

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-05  
**Author**: System Maintenance Team  
**Related Specs**: staff-dashboard-fix
