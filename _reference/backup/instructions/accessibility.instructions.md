---
applyTo: '**'
---


# Accessibility Instructions

**Purpose**  
This file provides mandatory accessibility (a11y) standards, developer guardrails, testing workflow, CI integration, and remediation procedures for the ICTServe codebase. It is normative and must be followed for all UI code, Filament resources, Livewire/Volt components, documentation, and public-facing text to ensure compliance with WCAG 2.2 Level AA and project policies (D12–D14, D15). See D00–D15 for traceability and requirements.

**Scope**  
Applies to:
- All frontend UI (Blade, Livewire/Volt, Filament) under `resources/views`, `app/Livewire`, `app/Filament`.
- Accessible content for public and admin interfaces.
- Component libraries, modals, dialogs, tables, forms, status indicators, and client-side scripts.
- CI pipelines and pre-merge checks that evaluate accessibility.
See D12 UI/UX Design Guide, D13 Frontend Framework, D14 Style Guide, and D15 Language docs for related policies.

**Standards & References (Mandatory)**
- WCAG 2.2 Level AA (primary)
- ISO 9241-210, ISO 9241-110, ISO 9241-11 (usability / human-centred design)
- ISO/IEC/IEEE 1016, ISO/IEC 27701, ISO/IEC 38505-1 (data governance, privacy)
- Project docs: D00–D15 (esp. D12, D13, D14, D15)
- Browser/tool references: axe-core, Lighthouse, NVDA, WAVE

**Traceability (Mandatory)**
- All accessibility changes must reference requirement/design IDs (D03/D04/D11) in PRs and in component/file metadata where relevant.
  - Example header for a component:
    ```php
    // name: ticket-form
    // description: Accessible ticket submission form
    // author: frontend@motac.gov.my
    // trace: SRS-FR-001; D12 §3; D14 §9
    // last-updated: 2025-10-22
    ```

**Mandatory Rules (Summary)**
- All production UI must meet WCAG 2.2 Level AA success criteria (see D12 §4, D14 §9).
- Do not rely on color alone to convey meaning—include text labels and icons (D14 §5).
- Use semantic HTML and ARIA where appropriate. Use native controls whenever possible (D12 §4.1, D14 §2).
- Ensure keyboard-only operation for all interactive features (focus management, keyboard shortcuts, modals with focus trap) (D12 §4.2, D14 §5).
- Provide meaningful alt text for images and accessible names for controls (aria-label/aria-labelledby as needed) (D12 §7.5, D14 §9).
- Respect users' reduced-motion preference (`prefers-reduced-motion`) and avoid flashing content (D12 §7.8).
- Provide Bahasa Melayu primary UI text; include English secondary text per D15 where necessary (use lang attributes) (D15 §2.1).
- Include accessibility test reports and remediation steps in PRs that modify UI (D12 §8, D14 §10).


## Developer Guidance — Component Patterns

**Landmarks & Structure**
- Use semantic elements: `<header>`, `<nav aria-label>`, `<main>`, `<aside>`, `<footer>` (D12 §4.1).
- Provide a "Skip to main content" link, visible on keyboard focus (D12 §4.1, D14 §2).

**Forms**
- Every input must have a visible `<label for="id">` bound to the input id (D12 §7.2).
- Required fields must use textual markers (e.g., "Nama Penuh *") and `aria-required` (D12 §7.2).
- Validation errors must be announced: include an error summary at top with focus move and `aria-describedby` on the input (D12 §7.2, D14 §9).

**Buttons & Links**
- Use `<button>` for actions; do not use `<a>` or `<div>` styled as buttons without role and keyboard support (D12 §7.3).
- Icon-only buttons require accessible name (`aria-label`) and visible text on hover/focus if appropriate (D12 §7.3).

**Tables**
- Use `<table>`, `<thead>`, `<tbody>`, `<th scope="col">` and captions. Provide `aria-sort` on sortable headers (D12 §7.4).
- On small screens use responsive table wrappers (horizontal scroll) rather than truncating content (D12 §7.4).

**Modals & Dialogs**
- `role="dialog"`, `aria-modal="true"`, `aria-labelledby` and `aria-describedby` set (D12 §7.6).
- Implement focus trap and restore focus to the triggering control on close (D12 §7.6).
- Escape key closes modal; provide a visible close button (D12 §7.6).

**Focus & Keyboard**
- Visible focus indicator (3–4px solid outline) for all interactive elements (D14 §5).
- Ensure focus order follows visual order; avoid `tabindex > 0` (D12 §4.2).

**Color & Contrast**
- Minimum contrast 4.5:1 for normal text; 3:1 for large text and UI components where applicable (D14 §9.1).
- Provide alternative non-color indicators (icons, text) for status badges (D14 §5).

**Images & Media**
- Provide meaningful alt text for informative images, empty alt for decorative images (D12 §7.5).
- Provide captions/transcripts for audio/video content; do not autoplay media (D12 §7.5).

**Internationalization & Language**
- Set page-level `lang="ms"` for Bahasa Melayu pages, and `lang="en"` where English segments appear (D15 §2.1).
- Ensure screen readers can detect language changes (D15 §2.1).


## Testing & Tools

**Local/Manual**
- Keyboard testing: Tab/Shift+Tab/Enter/Escape and arrow keys for controls. Verify no keyboard trap (D12 §8).
- Screen reader: NVDA (Windows), VoiceOver (macOS). Verify announcements for page title, errors, dialog opens, live regions (D12 §8).
- Color contrast: WebAIM Contrast Checker or Lighthouse (D14 §9.1).

**Automated Tools (Recommended)**
- axe-core / axe DevTools (browser extension or programmatic)
- Lighthouse Accessibility (score target ≥ 90 on audit pages)
- pa11y, WAVE for spot checks

**Component & Story Checks**
- If Storybook or component preview is used, include automated axe checks per story (D12 §8).

**CI / GitHub Actions (Recommended)**
- Add a job on PRs that runs:
  - `npm ci && npm run build` (or vite build)
  - Headless accessibility scan (axe-ci / Pa11y / lighthouse-ci) against a preview server or static build
  - Fail PR if critical/high violations found (configurable)
  - Example minimal Actions step (adjust to repo tooling):
    ```yaml
    - name: Run axe headless
      uses: dequelabs/axe-action@v2
      with:
        url: http://localhost:5173  # preview server address
        wait-for: 2000
    ```


## Step-by-Step Workflow for Implementing Accessibility Fixes
1. Evaluate change impact: identify pages/components affected; add trace IDs (D03/D04/D11).
2. Implement semantic HTML & ARIA, labels, focus management (D12 §4, D14 §5).
3. Run local automated checks (axe, Lighthouse) and manual keyboard + screen reader testing (D12 §8).
4. Add/adjust unit/integration/Livewire tests where behavior changes (e.g., focus move after submit) (D12 §8, D14 §10).
5. Include accessibility report summary and remediation notes in the PR body (D12 §8).
6. Assign accessibility reviewer (see Owners below).
7. Merge when fixes pass CI and reviewers approve.
8. Post-merge: monitor production for a11y regressions and user feedback (D12 §8).


### PR Checklist (Accessibility-Specific, Include in PR Body)
- [ ] Label association: every input has an explicit label (D12 §7.2)
- [ ] Keyboard operability verified (Tab/Shift+Tab/Enter/Escape) (D12 §4.2)
- [ ] Focus management: modals & page-level focus changes implemented (D12 §7.6)
- [ ] ARIA attributes only used where necessary and correctly (D12 §4.1)
- [ ] Color is not the only means to convey information (D14 §5)
- [ ] Contrast ratios >= WCAG 2.2 AA requirements (D14 §9.1)
- [ ] Images have alt text or are decorative (alt="") (D12 §7.5)
- [ ] Reduced-motion respected (`prefers-reduced-motion`) (D12 §7.8)
- [ ] Automated axe/Lighthouse scan attached or CI green (D12 §8)
- [ ] Accessibility reviewer assigned and feedback addressed
- [ ] Traceability: D03/D04/D11 IDs included


## Accessibility Audit & Remediation Process
- Periodic audit: schedule automated and manual audits (quarterly)—run axe + Lighthouse + manual screen reader tests (D12 §8).
- New regressions: open GitHub issue labeled accessibility; tag assignees (frontend owner, accessibility owner).
- Severity & SLA:
  - Critical (screen-reader blocking, missing labels, modal trap)—fix within 48 hours.
  - High (contrast fails on primary content, keyboard inaccessible flows)—fix within 2 weeks.
  - Medium/Low: tracked and prioritized in backlog.
- Maintain an accessibility report artifact per release (CI artifact) and store in `docs/` or CI artifacts (D12 §8).


## Testing Examples (Code Snippets)

**Skip link (visible on focus):**
```blade
<a href="#main-content" class="skip-link visually-hidden-focusable">Langkau ke kandungan utama</a>
```

**Error summary pattern:**
```blade
@if ($errors->any())
  <div id="form-errors" class="alert alert-danger" role="alert" tabindex="-1">
    <p>Terjadi ralat: sila betulkan item berikut.</p>
    <ul>
      @foreach ($errors->all() as $err)
        <li> $err </li>
      @endforeach
    </ul>
  </div>
  <script>document.getElementById('form-errors')?.focus()</script>
@endif
```

**Modal basics (focus trap is recommended via library or helper):**
```blade
<div id="confirmModal" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
  <h2 id="confirmTitle">Sahkan</h2>
  <!-- modal content -->
</div>
```


## Ownership, Reviewers & Training
- Accessibility owner: accessibility@motac.gov.my — assign for reviews and policy updates.
- Frontend owner: design@motac.gov.my — UI/UX conformance and visual checks.
- DevOps/CI owner: devops@motac.gov.my — CI accessibility jobs and artifact retention.
- Security/Compliance: security@motac.gov.my — when accessibility intersects with privacy/security.
- Provide training: regular a11y workshops for developers and reviewers; create checklist templates and sample fixes in `docs/`.


## Reporting & Metrics
- Track key metrics in each release:
  - Number of accessibility violations (critical/high/medium)
  - Lighthouse accessibility score (per major pages)
  - Number of a11y issues opened vs resolved
- Include accessibility status in release notes.


## Appendices

**A. Useful Tools & Links**
- axe-core: https://www.deque.com/axe/
- Lighthouse: https://developers.google.com/web/tools/lighthouse
- NVDA: https://www.nvaccess.org/
- WAVE: https://wave.webaim.org/
- WebAIM Contrast Checker: https://webaim.org/resources/contrastchecker/
- Pa11y: https://pa11y.org/

**B. Useful References in Repo**
- D12_UI_UX_DESIGN_GUIDE.md — component-level a11y rules
- D14_UI_UX_STYLE_GUIDE.md — color palette & focus styles
- D15_LANGUAGE_MS_EN.md — language and lang attribute conventions


## Contacts
- Accessibility / UX: accessibility@motac.gov.my
- Frontend / UI owner: design@motac.gov.my
- DevOps / CI: devops@motac.gov.my
- Documentation & Traceability: docs@motac.gov.my
- Security / Compliance: security@motac.gov.my


## Notes & Governance
- This file is normative. Any deviation that impacts accessibility, privacy, or traceability requires formal change management and RTM updates (see D01 §9.3). Review and update this document annually or after major framework upgrades (e.g., Livewire/Volt, Tailwind, Vite).
- When in doubt, be conservative: prefer semantic native controls and clear labels over complex ARIA solutions.
