---
inclusion: always
description: "Comprehensive accessibility implementation guide, testing protocols, and CI/CD enforcement for ICTServe v3.5.0"
version: "3.5.0"
last_updated: "2025-12-07"
status: "Draft"
---

# 03-ACCESSIBILITY-ictserve-3.5.0

## 1. Executive Summary

**Purpose**
This document serves as the **Implementation Manual** and **Standard Operating Procedure (SOP)** for maintaining WCAG 2.2 Level AA compliance in ICTServe v3.5.0. While `02-WCAG-ictserve-3.5.0.md` details the *current state* (Audit), this document defines the *future state* workflows, coding standards, and automated gates required to prevent regression.

**Target Audience**

- **Developers**: For coding patterns and ARIA usage.
- **QA Engineers**: For testing protocols (Automated & Manual).
- **DevOps**: For CI/CD integration.
- **AI Agents**: As a rule set for generating accessible code.

**Compliance Target**

- **Standard**: WCAG 2.2 Level AA
- **Regional**: MyGOV Digital Service Standards (MyDS) v2.1.0
- **Language**: Bahasa Melayu (Primary) & English (Secondary)

---

## 2. Implementation Standards (The "How-To")

### 2.1. Semantic HTML Structure
**Rule**: Use native HTML5 elements over ARIA roles whenever possible.

| Component | Correct Implementation | Incorrect Implementation |
| :--- | :--- | :--- |
| **Buttons** | `<button type="button">` | `<div role="button" onclick="...">` |
| **Links** | `<a href="...">` | `<span onclick="goto()">` |
| **Headings** | `<h1>` to `<h6>` (Sequential) | `<div class="text-2xl font-bold">` |
| **Inputs** | `<label for="id">` + `<input id="id">` | `<span>Label</span>` + `<input>` |

### 2.2. Focus Management
**Rule**: Every interactive element must have a visible focus state and logical tab order.

**Tailwind Implementation**:

```html
<!-- Global Focus Ring (defined in app.css / theme) -->
<button class="focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none">
    Submit
</button>
```

**Livewire/Alpine Focus Handling**:

```html
<!-- Managing focus in Modals -->
<div x-data x-trap.noscroll="isOpen" @keydown.escape.window="isOpen = false">
    <!-- Modal Content -->
</div>

<!-- Managing focus after dynamic updates -->
<button wire:click="save" wire:loading.attr="disabled">
    Save
</button>
```

### 2.3. Color & Contrast
**Rule**: Minimum contrast ratio of 4.5:1 for normal text, 3:1 for large text/UI components.

**MyDS Token Usage**:

- **Text**: `text-gray-900` (on white), `text-white` (on primary-600).
- **Borders**: `border-gray-300` (inputs), `border-gray-200` (dividers).
- **Errors**: `text-red-700` (bg-red-50).

### 2.4. Forms & Validation
**Rule**: Errors must be text-based, associated with inputs, and announced by screen readers.

**Pattern**:

```blade
<div>
    <label for="email" class="block text-sm font-medium text-gray-900">
        Email Address <span class="text-red-600" aria-hidden="true">*</span>
    </label>
    
    <input 
        type="email" 
        id="email" 
        wire:model="email"
        aria-required="true"
        @error('email') 
            aria-invalid="true" 
            aria-describedby="email-error" 
        @enderror
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
    >

    @error('email')
        <p class="mt-2 text-sm text-red-600" id="email-error" role="alert">
            {{ $message }}
        </p>
    @enderror
</div>
```

---

## 3. Testing Protocols

### 3.1. Automated Testing Suite
**Tools**: Axe-core, Pa11y, Lighthouse CI.

**Workflow**:

1. **Local Dev**: Run `npm run test:a11y` (Playwright + Axe).
2. **CI Gate**: GitHub Actions fails PR if critical a11y violations are found.

**Playwright Test Example (`tests/e2e/accessibility.spec.ts`)**:

```typescript
import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

test.describe('Accessibility Audit', () => {
    test('Guest Loan Form should not have WCAG violations', async ({ page }) => {
        await page.goto('/guest/loan-application');
        
        const accessibilityScanResults = await new AxeBuilder({ page })
            .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
            .analyze();

        expect(accessibilityScanResults.violations).toEqual([]);
    });
});
```

### 3.2. Manual Testing Checklist
**Frequency**: Before every major release.

| Category | Test Action | Expected Outcome |
| :--- | :--- | :--- |
| **Keyboard** | Tab through page | Focus indicator visible; logical order; no traps. |
| **Screen Reader** | NVDA + Firefox | All content announced; forms labeled; dynamic updates announced. |
| **Zoom** | Browser Zoom 200% | No horizontal scroll; no overlapping text; functionality intact. |
| **Color** | Grayscale Mode | Information not conveyed by color alone (e.g., links underlined, errors have icons). |

### 3.3. Screen Reader Setup (NVDA)

1. **Install**: NVDA (Windows) or VoiceOver (Mac).
2. **Settings**:
    - "Speak typed characters" -> OFF.
    - "Mouse tracking" -> ON.
3. **Key Commands**:
    - `Insert + Space`: Toggle Focus/Browse mode.
    - `H`: Next Heading.
    - `D`: Next Landmark.
    - `Tab`: Next Focusable Item.

---

## 4. CI/CD Integration

### 4.1. GitHub Actions Workflow
File: `.github/workflows/accessibility.yml`

```yaml
name: Accessibility Audit
on: [pull_request]

jobs:
  a11y-check:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: '20'
      - name: Install Dependencies
        run: npm ci
      - name: Build Assets
        run: npm run build
      - name: Run Playwright Axe Tests
        run: npx playwright test tests/e2e/accessibility.spec.ts
```

### 4.2. Quality Gates

- **Blocker**: Any WCAG 2.2 Level A violation.
- **Warning**: Level AA contrast issues (manual review allowed for brand colors).
- **Exemption**: Third-party iframes (must be documented).

---

## 5. Remediation Workflows (For Agents)

### 5.1. Fixing "Missing Alt Text"
**Input**: `<img src="logo.png">`
**Agent Action**:

1. Analyze image context.
2. If decorative: `alt=""`.
3. If informative: `alt="Ministry of Tourism, Arts and Culture Logo"`.
4. **Output**: `<img src="logo.png" alt="Ministry of Tourism, Arts and Culture Logo">`

### 5.2. Fixing "Empty Button"
**Input**: `<button><i class="fa fa-times"></i></button>`
**Agent Action**:

1. Identify button purpose (Close).
2. Add `aria-label` or screen-reader text.
3. **Output**:

    ```html
    <button aria-label="Close Modal">
        <i class="fa fa-times" aria-hidden="true"></i>
    </button>
    ```

### 5.3. Fixing "Form Label Missing"
**Input**: `<input type="search" placeholder="Search...">`
**Agent Action**:

1. Add visible label OR `aria-label`.
2. **Output**: `<input type="search" aria-label="Search tickets" placeholder="Search...">`

---

## 6. Maintenance & Governance

### 6.1. Documentation Updates

- Update `02-WCAG-ictserve-3.5.0.md` (Audit Report) quarterly.
- Update this guide (`03-ACCESSIBILITY`) when tech stack changes (e.g., new UI library).

### 6.2. Training

- Developers must complete "Web Accessibility for Developers" (W3C) or equivalent.
- Designers must verify contrast ratios in Figma before handoff.

### 6.3. Feedback Mechanism

- Public "Accessibility Statement" page (`/accessibility`).
- Feedback form for users to report barriers.

---

## 7. Reference Links

- [WCAG 2.2 Guidelines](https://www.w3.org/TR/WCAG22/)
- [WAI-ARIA Authoring Practices](https://www.w3.org/WAI/ARIA/apg/)
- [MyGOV Digital Service Standards](https://www.malaysia.gov.my)
- [Tailwind CSS Accessibility](https://tailwindcss.com/docs/screen-readers)
- [Livewire Accessibility](https://livewire.laravel.com/docs/accessibility)
