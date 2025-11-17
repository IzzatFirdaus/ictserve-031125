# Loan Module - Accessibility Triage

This file captures accessibility problems detected by Playwright E2E axe checks during refactored tests.

Test file:

- `tests/e2e/loan-module-accessibility.refactored.spec.ts`

Failing rules seen locally (select examples):

- `color-contrast` (WCAG 2.2 AA): multiple elements not meeting 4.5:1 contrast ratio — e.g., text with color `#64748b` on `#0b1224` background (receipt date)
- `color-contrast-enhanced` (WCAG AAA / enhanced): some buttons and chips do not meet 7:1 ratio in narrow font sizes
- `aria-required-children` & `aria-required-parent` (ARIA tab roles): buttons with role `tab` present without the `tablist` parent; `role=tablist` might not be properly set
- `aria-valid-attr-value` & `aria-hidden-focus`: ARIA attributes contain invalid values or elements with `aria-hidden` are still focusable

Why this matters:

- Color contrast creates readability issues for visually impaired users. Must meet 4.5:1 for normal text.
- ARIA misconfigurations cause screen readers to misinterpret content and navigation.

Reproduction steps:

1. Run the loan module accessibility test:

```powershell
npx playwright test tests/e2e/loan-module-accessibility.refactored.spec.ts --grep "@accessibility" --reporter=list --workers=1
```

2. Open the generated report artifact in `test-results/` and inspect `axe-results.json` (if created) or the screenshot.

Suggested fixes:

- Change design tokens to meet minimum contrast ratios. Common helper: `text-slate-400` -> `text-slate-300` or choose more visible accent color for small text
- For `tab` components: wrap `role=tab` with a parent container `role=tablist`, ensure `aria-controls` points to a valid `id` on the panel
- For `aria-hidden` skip link: set `tabindex=-1` on non-visible elements or remove focusable children from `aria-hidden` regions
- Add `aria-label` or `aria-labelledby` to important `nav`/`dialog`/`modal` components

Suggested owner: `@frontend` (Tailwind CSS + UI components) and `@UX` for trade-off decisions on color choices.

Severity: high (critical/major for some ARIA failures); open issues and assign to frontend.

Next steps:

- Create triage issues for each rule with example target selectors (from axe output). Add screenshots.
- Fix UI components in `resources/views` or `app/Livewire` components as needed.
- Add visual regression tests if needed to block reintroducing bad colors.

Traceability: D12 (UI/UX Accessibility) — D14 (Style Guide) — D03-FR-008 (Accessibility Requirements)

---
