---
mode: agent
---

# Accessibility Audit Workflow (WCAG 2.2 AA)

You are an accessibility expert performing a WCAG 2.2 Level AA compliance audit on ICTServe. Systematically check for accessibility barriers and provide actionable fixes.

## Context

**ICTServe Accessibility Requirements:**
- WCAG 2.2 Level AA compliance
- Keyboard navigation support
- Screen reader compatibility
- Color contrast ratios (4.5:1 normal, 3:1 large text)
- Bilingual support (Bahasa Melayu + English)

## Accessibility Audit Steps

### 1. Scope Definition

**Task:** Identify audit scope with user

**Ask User:**
- What pages/components should be audited? (default: all)
- Are there specific accessibility concerns?
- Previous accessibility findings to verify?

**Output:** List of pages/components to audit

---

### 2. Automated Testing

**Task:** Run automated accessibility checkers

**Tools:**

**1. Lighthouse (Chrome DevTools)**
```bash
# Run from command line
lighthouse https://ictserve.local --only-categories=accessibility --output=html --output-path=./accessibility-report.html
```

**2. axe DevTools (Browser Extension)**
- Install: https://www.deque.com/axe/devtools/
- Run full page scan
- Export results

**3. Pa11y (Command Line)**
```bash
npm install -g pa11y
pa11y https://ictserve.local --standard WCAG2AA --reporter html > pa11y-report.html
```

**Output:** List of automated findings

---

### 3. Manual Keyboard Navigation Test

**Task:** Verify keyboard accessibility

**Test Steps:**

1. **Tab Order**
   - Press Tab repeatedly through page
   - Verify logical tab order (top → bottom, left → right)
   - All interactive elements reachable

2. **Focus Indicators**
   - Visible focus ring on all focusable elements
   - Focus not trapped unexpectedly

3. **Keyboard Shortcuts**
   - Enter/Space activates buttons
   - Escape closes modals
   - Arrow keys navigate dropdowns

**Check Code:**
```bash
# Find interactive elements without keyboard support
grep_search: "onclick" in "resources/views/**"
```

**Vulnerabilities:**
```html
<!-- ❌ BAD: Div as button (not keyboard accessible) -->
<div onclick="doSomething()">Click Me</div>

<!-- ✅ GOOD: Button (naturally keyboard accessible) -->
<button @click="doSomething()">Click Me</button>

<!-- ✅ GOOD: Div with full keyboard support -->
<div 
    role="button" 
    tabindex="0"
    @click="doSomething()"
    @keydown.enter="doSomething()"
    @keydown.space.prevent="doSomething()"
>
    Click Me
</div>
```

**Checklist:**
- [ ] All interactive elements keyboard accessible
- [ ] Logical tab order
- [ ] Visible focus indicators
- [ ] Modals trap focus
- [ ] Skip navigation link present

---

### 4. Screen Reader Testing

**Task:** Test with screen readers

**Tools:**
- **Windows**: NVDA (free) — https://www.nvaccess.org/
- **Mac**: VoiceOver (built-in) — Cmd+F5
- **Chrome Extension**: ChromeVox

**Test Scenarios:**

1. **Navigation**
   - Can screen reader announce page structure?
   - Are landmarks properly labeled?

2. **Forms**
   - Are all inputs labeled?
   - Are error messages announced?

3. **Dynamic Content**
   - Are loading states announced?
   - Are success messages announced?

**Check Code:**
```bash
# Find inputs without labels
grep_search: "<input" in "resources/views/**"
```

**Vulnerabilities:**
```html
<!-- ❌ BAD: No label -->
<input type="text" name="name" placeholder="Nama">

<!-- ✅ GOOD: Explicit label -->
<label for="name">Nama</label>
<input type="text" id="name" name="name">

<!-- ✅ GOOD: ARIA label (if visual label not possible) -->
<input type="text" name="search" aria-label="Cari aset">
```

**Checklist:**
- [ ] All form inputs have labels
- [ ] Images have alt text
- [ ] Buttons have accessible names
- [ ] Landmarks defined (nav, main, aside)
- [ ] Dynamic content uses `aria-live`

---

### 5. Color Contrast Check

**Task:** Verify color contrast ratios

**Requirements:**
- Normal text: **4.5:1** minimum
- Large text (18px+ or 14px+ bold): **3:1** minimum

**Tools:**
- **WebAIM Contrast Checker**: https://webaim.org/resources/contrastchecker/
- **Chrome DevTools**: Inspect element → Contrast ratio shown in color picker

**Check Code:**
```bash
# Find text color classes
grep_search: "text-gray|text-blue|bg-" in "resources/views/**"
```

**Common Tailwind Issues:**
```html
<!-- ❌ BAD: text-gray-400 on white (2.8:1) — FAILS -->
<p class="text-gray-400 bg-white">Low contrast text</p>

<!-- ✅ GOOD: text-gray-900 on white (21:1) — PASSES -->
<p class="text-gray-900 bg-white">High contrast text</p>

<!-- ✅ GOOD: text-gray-600 on white (4.6:1) — PASSES -->
<p class="text-gray-600 bg-white">Readable text</p>
```

**Checklist:**
- [ ] All text meets 4.5:1 contrast (or 3:1 for large text)
- [ ] Link colors distinguishable from surrounding text
- [ ] Focus indicators have 3:1 contrast
- [ ] Color not sole indicator of meaning

---

### 6. Semantic HTML Check

**Task:** Verify proper HTML structure

**Search For:**
```bash
# Find divs used as buttons
grep_search: '<div.*onclick' in "resources/views/**"

# Find heading hierarchy
grep_search: '<h[1-6]' in "resources/views/**"
```

**Vulnerabilities:**
```html
<!-- ❌ BAD: Non-semantic structure -->
<div class="header">
    <div class="nav">...</div>
</div>
<div class="content">...</div>

<!-- ✅ GOOD: Semantic HTML5 -->
<header role="banner">
    <nav role="navigation" aria-label="Navigasi utama">...</nav>
</header>
<main role="main">...</main>
```

**Heading Hierarchy:**
```html
<!-- ❌ BAD: Skip heading levels -->
<h1>Page Title</h1>
<h3>Section Title</h3> <!-- Skipped h2 -->

<!-- ✅ GOOD: Sequential headings -->
<h1>Page Title</h1>
<h2>Section Title</h2>
<h3>Subsection Title</h3>
```

**Checklist:**
- [ ] Semantic HTML5 elements used (`<header>`, `<nav>`, `<main>`, `<article>`, `<footer>`)
- [ ] ARIA landmarks defined
- [ ] Heading hierarchy sequential (h1 → h2 → h3)
- [ ] One h1 per page
- [ ] Lists use `<ul>`, `<ol>`, `<li>`

---

### 7. Forms Accessibility

**Task:** Audit form accessibility

**Search For:**
```bash
# Find form elements
grep_search: "<form|<input|<select|<textarea" in "resources/views/**"
```

**Checklist:**

**Labels:**
```html
<!-- ✅ All inputs have labels -->
<label for="email">E-mel</label>
<input type="email" id="email" name="email">
```

**Required Fields:**
```html
<label for="name">
    Nama <span class="text-red-600" aria-hidden="true">*</span>
</label>
<input type="text" id="name" name="name" required aria-required="true">
```

**Error Messages:**
```html
<input 
    type="email" 
    id="email"
    aria-describedby="email-error"
    aria-invalid="true"
>
<p id="email-error" class="text-red-600" role="alert">
    Format e-mel tidak sah.
</p>
```

**Checklist:**
- [ ] All inputs have associated labels
- [ ] Required fields marked with `aria-required="true"`
- [ ] Error messages linked with `aria-describedby`
- [ ] Invalid fields marked with `aria-invalid="true"`
- [ ] Fieldsets used for radio/checkbox groups

---

### 8. Images & Media

**Task:** Check alternative text

**Search For:**
```bash
# Find images
grep_search: "<img" in "resources/views/**"
```

**Rules:**

**Informative Images:**
```html
<img src="laptop.jpg" alt="Laptop Dell Latitude 5420 dengan skrin 14 inci">
```

**Decorative Images:**
```html
<img src="decorative-border.png" alt="">
```

**Functional Images (icons as buttons):**
```html
<button aria-label="Padam aset">
    <svg aria-hidden="true">...</svg>
</button>
```

**Checklist:**
- [ ] All `<img>` have `alt` attribute
- [ ] Alt text descriptive (not "image" or filename)
- [ ] Decorative images have empty alt (`alt=""`)
- [ ] Complex images have long descriptions
- [ ] Videos have captions

---

### 9. Responsive & Zoom

**Task:** Test responsive design and zoom

**Tests:**

1. **Mobile Responsiveness**
   - Test at 320px, 768px, 1024px, 1920px
   - No horizontal scrolling
   - Touch targets ≥ 44px × 44px

2. **Zoom to 200%**
   - Browser zoom to 200%
   - Content still readable
   - No overlapping text
   - No loss of functionality

**Checklist:**
- [ ] Responsive at all breakpoints
- [ ] Content readable at 200% zoom
- [ ] Touch targets ≥ 44px × 44px (mobile)
- [ ] No horizontal scrolling

---

### 10. ARIA Attributes

**Task:** Verify ARIA usage

**Search For:**
```bash
# Find ARIA attributes
grep_search: "aria-" in "resources/views/**"
```

**Common ARIA Patterns:**

**Live Regions:**
```html
<div aria-live="polite" aria-atomic="true">
    3 item baharu ditambah.
</div>
```

**Expanded State:**
```html
<button 
    aria-expanded="false" 
    aria-controls="dropdown"
    @click="open = !open"
>
    Menu
</button>
<div id="dropdown" x-show="open">...</div>
```

**Hidden Content:**
```html
<svg aria-hidden="true">...</svg> <!-- Decorative icon -->
```

**Checklist:**
- [ ] `aria-label` provides accessible names
- [ ] `aria-labelledby` references labels correctly
- [ ] `aria-describedby` links descriptions
- [ ] `aria-live` announces dynamic content
- [ ] `aria-hidden="true"` only on decorative elements
- [ ] `aria-expanded` reflects state

---

## Accessibility Report

**After Audit, Generate Report:**

```markdown
# ICTServe Accessibility Audit Report (WCAG 2.2 AA)
Date: [YYYY-MM-DD]

## Summary
- Pages Audited: X
- Total Issues: X
- Critical (A): X
- Serious (AA): X
- Moderate: X
- Minor: X

## Automated Testing Results
- Lighthouse Accessibility Score: X/100
- axe DevTools Issues: X
- Pa11y Issues: X

## Manual Testing Results

### Keyboard Navigation
- Status: [PASS / FAIL]
- Issues: [List issues]

### Screen Reader Compatibility
- Status: [PASS / FAIL]
- Issues: [List issues]

### Color Contrast
- Status: [PASS / FAIL]
- Issues: [List issues]

## Detailed Findings

### Critical Issues (Level A)
1. [Issue description]
   - Location: [file:line]
   - Fix: [Code example]

### Serious Issues (Level AA)
1. [Issue description]
   - Location: [file:line]
   - Fix: [Code example]

## Recommendations
1. [Priority 1 recommendations]
2. [Priority 2 recommendations]

## WCAG 2.2 AA Compliance
- Status: [COMPLIANT / NON-COMPLIANT]
- Compliance Score: X%
```

---

## Quick Fixes

**Common Issues & Fixes:**

**1. Missing Alt Text**
```bash
# Find all images without alt
grep_search: '<img(?![^>]*alt=)' in "resources/views/**" --isRegexp=true
```

**2. Inputs Without Labels**
```bash
# Find inputs without associated labels
grep_search: '<input(?![^>]*id=)' in "resources/views/**" --isRegexp=true
```

**3. Low Contrast**
```bash
# Find common low-contrast Tailwind classes
grep_search: "text-gray-400|text-gray-300|text-blue-300" in "resources/views/**"
```

---

## References

- `.github/instructions/a11y.instructions.md`
- WCAG 2.2: https://www.w3.org/WAI/WCAG22/quickref/
- WebAIM: https://webaim.org/
- D14 (UI/UX Design Guide)
- D15 (Accessibility Requirements)
