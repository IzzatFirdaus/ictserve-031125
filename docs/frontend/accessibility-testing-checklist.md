# Accessibility Testing Checklist

## WCAG 2.2 Level AA Compliance Testing

This checklist provides a comprehensive guide for manual accessibility testing of the ICTServe application.

## Table of Contents

1. [Keyboard Navigation Testing](#keyboard-navigation-testing)
2. [Screen Reader Testing](#screen-reader-testing)
3. [Color Contrast Testing](#color-contrast-testing)
4. [Touch Target Testing](#touch-target-testing)
5. [Form Accessibility Testing](#form-accessibility-testing)
6. [Content Structure Testing](#content-structure-testing)
7. [Responsive Design Testing](#responsive-design-testing)
8. [Automated Testing](#automated-testing)

---

## Keyboard Navigation Testing

### Test Procedure

- [ ] **Tab Navigation**
  - [ ] Press `Tab` to move forward through all interactive elements
  - [ ] Verify focus indicator is visible on all elements
  - [ ] Verify tab order is logical and follows visual layout
  - [ ] Verify no keyboard traps (can always move forward/backward)

- [ ] **Shift + Tab Navigation**
  - [ ] Press `Shift + Tab` to move backward through interactive elements
  - [ ] Verify reverse tab order matches forward order

- [ ] **Enter/Space Activation**
  - [ ] Press `Enter` on links and buttons to activate
  - [ ] Press `Space` on buttons to activate
  - [ ] Verify all interactive elements respond to keyboard

- [ ] **Escape Key**
  - [ ] Press `Escape` to close modals
  - [ ] Press `Escape` to close dropdowns
  - [ ] Verify focus returns to trigger element after closing

- [ ] **Arrow Keys**
  - [ ] Use arrow keys in dropdown menus
  - [ ] Use arrow keys in radio button groups
  - [ ] Use arrow keys in tab panels

- [ ] **Skip Links**
  - [ ] Press `Tab` on page load
  - [ ] Verify "Skip to main content" link appears
  - [ ] Activate skip link and verify focus moves to main content

### Pass Criteria

✅ All interactive elements are keyboard accessible
✅ Focus indicators are visible (2px outline minimum)
✅ Tab order is logical
✅ No keyboard traps exist
✅ Skip links work correctly

---

## Screen Reader Testing

### Tools

- **Windows**: NVDA (free) or JAWS
- **macOS**: VoiceOver (built-in)
- **Linux**: Orca

### Test Procedure

- [ ] **Page Structure**
  - [ ] Verify page title is announced
  - [ ] Verify headings are announced with correct levels
  - [ ] Verify landmarks are announced (banner, navigation, main, contentinfo)
  - [ ] Verify lists are announced as lists with item counts

- [ ] **Links and Buttons**
  - [ ] Verify link text is descriptive
  - [ ] Verify button labels are clear
  - [ ] Verify icon-only buttons have aria-labels
  - [ ] Verify link purpose is clear from context

- [ ] **Forms**
  - [ ] Verify all form fields have labels
  - [ ] Verify required fields are announced
  - [ ] Verify error messages are announced
  - [ ] Verify help text is associated with fields
  - [ ] Verify field validation states are announced

- [ ] **Images**
  - [ ] Verify informative images have descriptive alt text
  - [ ] Verify decorative images have empty alt text or aria-hidden
  - [ ] Verify complex images have long descriptions

- [ ] **Tables**
  - [ ] Verify table captions are announced
  - [ ] Verify column headers are announced
  - [ ] Verify row headers are announced
  - [ ] Verify table relationships are clear

- [ ] **Dynamic Content**
  - [ ] Verify live regions announce updates
  - [ ] Verify loading states are announced
  - [ ] Verify success/error messages are announced
  - [ ] Verify modal dialogs are announced

### Pass Criteria

✅ All content is accessible to screen readers
✅ Navigation is logical and efficient
✅ Form interactions are clear
✅ Dynamic updates are announced appropriately

---

## Color Contrast Testing

### Tools

- **Browser DevTools**: Color picker with contrast ratio
- **WebAIM Contrast Checker**: https://webaim.org/resources/contrastchecker/
- **Colour Contrast Analyser**: Desktop application

### Test Procedure

- [ ] **Normal Text (< 18pt or < 14pt bold)**
  - [ ] Verify contrast ratio ≥ 4.5:1
  - [ ] Test all text colors against backgrounds
  - [ ] Test hover and focus states
  - [ ] Test disabled states

- [ ] **Large Text (≥ 18pt or ≥ 14pt bold)**
  - [ ] Verify contrast ratio ≥ 3:1
  - [ ] Test headings and large text

- [ ] **UI Components**
  - [ ] Verify button borders have ≥ 3:1 contrast
  - [ ] Verify form field borders have ≥ 3:1 contrast
  - [ ] Verify focus indicators have ≥ 3:1 contrast
  - [ ] Verify icons have ≥ 3:1 contrast

- [ ] **Status Indicators**
  - [ ] Verify success messages meet contrast requirements
  - [ ] Verify error messages meet contrast requirements
  - [ ] Verify warning messages meet contrast requirements
  - [ ] Verify info messages meet contrast requirements

- [ ] **Dark Mode**
  - [ ] Repeat all contrast tests in dark mode
  - [ ] Verify dark mode meets same standards

### Pass Criteria

✅ All text meets 4.5:1 contrast ratio (normal) or 3:1 (large)
✅ All UI components meet 3:1 contrast ratio
✅ Dark mode meets same standards as light mode

---

## Touch Target Testing

### Tools

- **Mobile Device** or **Browser DevTools** (mobile emulation)
- **Ruler** or **measurement tool**

### Test Procedure

- [ ] **Button Size**
  - [ ] Verify all buttons are ≥ 44x44 pixels
  - [ ] Verify icon-only buttons are ≥ 44x44 pixels
  - [ ] Verify touch targets don't overlap

- [ ] **Link Size**
  - [ ] Verify all links have adequate padding
  - [ ] Verify inline links are easily tappable
  - [ ] Verify navigation links are ≥ 44x44 pixels

- [ ] **Form Controls**
  - [ ] Verify checkboxes are ≥ 44x44 pixels (including label)
  - [ ] Verify radio buttons are ≥ 44x44 pixels (including label)
  - [ ] Verify input fields have adequate height (≥ 44px)

- [ ] **Spacing**
  - [ ] Verify adequate spacing between touch targets (≥ 8px)
  - [ ] Verify no accidental activations occur

### Pass Criteria

✅ All touch targets are ≥ 44x44 pixels
✅ Adequate spacing between interactive elements
✅ No accidental activations on mobile devices

---

## Form Accessibility Testing

### Test Procedure

- [ ] **Labels**
  - [ ] Verify all inputs have associated labels
  - [ ] Verify labels are visible (not placeholder-only)
  - [ ] Verify labels are properly associated (for/id)

- [ ] **Required Fields**
  - [ ] Verify required fields are marked visually
  - [ ] Verify required fields have aria-required="true"
  - [ ] Verify required indicator is not color-only

- [ ] **Error Handling**
  - [ ] Verify error messages are clear and specific
  - [ ] Verify errors are associated with fields (aria-describedby)
  - [ ] Verify fields with errors have aria-invalid="true"
  - [ ] Verify error summary is provided at top of form
  - [ ] Verify errors are announced to screen readers

- [ ] **Help Text**
  - [ ] Verify help text is associated with fields
  - [ ] Verify help text is accessible to screen readers
  - [ ] Verify help text doesn't disappear on focus

- [ ] **Validation**
  - [ ] Verify inline validation provides clear feedback
  - [ ] Verify validation doesn't rely on color alone
  - [ ] Verify success states are indicated clearly

- [ ] **Autocomplete**
  - [ ] Verify autocomplete attributes are used appropriately
  - [ ] Verify autocomplete suggestions are keyboard accessible

### Pass Criteria

✅ All form fields have proper labels
✅ Required fields are clearly indicated
✅ Error messages are clear and accessible
✅ Form validation is accessible

---

## Content Structure Testing

### Test Procedure

- [ ] **Headings**
  - [ ] Verify heading hierarchy is logical (h1 → h2 → h3)
  - [ ] Verify no heading levels are skipped
  - [ ] Verify only one h1 per page
  - [ ] Verify headings describe content sections

- [ ] **Landmarks**
  - [ ] Verify page has banner landmark (header)
  - [ ] Verify page has navigation landmark
  - [ ] Verify page has main landmark
  - [ ] Verify page has contentinfo landmark (footer)
  - [ ] Verify landmarks have unique labels if multiple

- [ ] **Lists**
  - [ ] Verify lists use proper markup (ul, ol, dl)
  - [ ] Verify list items are properly nested
  - [ ] Verify navigation menus use list markup

- [ ] **Tables**
  - [ ] Verify data tables have captions
  - [ ] Verify tables have proper headers (th)
  - [ ] Verify headers have scope attributes
  - [ ] Verify complex tables have proper associations

- [ ] **Language**
  - [ ] Verify html lang attribute is set
  - [ ] Verify lang changes are marked (lang attribute)
  - [ ] Verify language is correct for content

### Pass Criteria

✅ Heading hierarchy is logical
✅ All required landmarks are present
✅ Lists and tables use proper markup
✅ Language is properly declared

---

## Responsive Design Testing

### Test Procedure

- [ ] **Viewport Sizes**
  - [ ] Test at 320px width (small mobile)
  - [ ] Test at 768px width (tablet)
  - [ ] Test at 1024px width (desktop)
  - [ ] Test at 1920px width (large desktop)

- [ ] **Zoom Testing**
  - [ ] Test at 200% zoom
  - [ ] Verify no horizontal scrolling
  - [ ] Verify text reflows properly
  - [ ] Verify all content remains accessible

- [ ] **Orientation**
  - [ ] Test in portrait orientation
  - [ ] Test in landscape orientation
  - [ ] Verify content adapts appropriately

- [ ] **Touch Interactions**
  - [ ] Verify all interactions work on touch devices
  - [ ] Verify no hover-only interactions
  - [ ] Verify gestures have alternatives

### Pass Criteria

✅ Site works at all viewport sizes
✅ Content is accessible at 200% zoom
✅ No horizontal scrolling occurs
✅ Touch interactions work properly

---

## Automated Testing

### Tools and Commands

#### Lighthouse

```bash
# Run Lighthouse accessibility audit
npm run lighthouse

# Or manually
npx lighthouse http://localhost:8000 --only-categories=accessibility --view
```

**Target Score**: ≥ 90

#### axe DevTools

1. Install browser extension
2. Open DevTools
3. Navigate to "axe DevTools" tab
4. Click "Scan ALL of my page"
5. Review and fix all violations

**Target**: 0 violations

#### WAVE

1. Visit https://wave.webaim.org/
2. Enter page URL
3. Review errors and warnings
4. Fix all errors

**Target**: 0 errors

#### Pa11y

```bash
# Install pa11y
npm install -g pa11y

# Run accessibility test
pa11y http://localhost:8000

# Run with specific standard
pa11y --standard WCAG2AA http://localhost:8000
```

**Target**: 0 errors

### Automated Test Suite

```bash
# Run PHPUnit accessibility tests
php artisan test --filter=AccessibilityTest

# Run all tests
php artisan test
```

---

## Testing Schedule

### Before Each Release

- [ ] Run automated Lighthouse tests
- [ ] Run automated axe tests
- [ ] Run PHPUnit accessibility tests
- [ ] Perform keyboard navigation testing
- [ ] Perform screen reader testing (sample pages)
- [ ] Verify color contrast on new components

### Monthly

- [ ] Full manual accessibility audit
- [ ] Screen reader testing (all pages)
- [ ] Touch target verification
- [ ] Responsive design testing

### After Major Changes

- [ ] Run full automated test suite
- [ ] Perform targeted manual testing on changed areas
- [ ] Verify no regressions in existing features

---

## Issue Reporting

When accessibility issues are found:

1. **Document the Issue**
   - Page/component affected
   - WCAG criterion violated
   - Steps to reproduce
   - Expected vs actual behavior

2. **Severity Classification**
   - **Critical**: Blocks access to core functionality
   - **High**: Significantly impacts user experience
   - **Medium**: Causes inconvenience but has workaround
   - **Low**: Minor issue with minimal impact

3. **Create Issue**
   - Use GitHub Issues or project management tool
   - Tag with "accessibility" label
   - Assign appropriate priority
   - Link to WCAG criterion

---

## Resources

- [WCAG 2.2 Guidelines](https://www.w3.org/WAI/WCAG22/quickref/)
- [WebAIM Resources](https://webaim.org/resources/)
- [A11y Project Checklist](https://www.a11yproject.com/checklist/)
- [Deque University](https://dequeuniversity.com/)
- [MDN Accessibility](https://developer.mozilla.org/en-US/docs/Web/Accessibility)

---

## Sign-off

**Tester Name**: ___________________________

**Date**: ___________________________

**Test Results**: ☐ Pass  ☐ Fail

**Notes**: ___________________________
