---
applyTo: '**'
---

# Frontend Instructions

Purpose  
Defines the standards, conventions, build/test/CI workflow, accessibility and traceability requirements for frontend development in ICTServe (Tailwind 3, Vite, Blade, Livewire/Volt, Filament). This file is normative for frontend developers, maintainers and QA. Reference canonical docs D00–D14 for requirements, design and traceability.

Scope  
Applies to all frontend assets and UI code in this repository including:
- CSS: `resources/css/` (primary: `resources/css/app.css`)
- JavaScript: `resources/js/` (primary: `resources/js/app.js`)
- Blade views and Livewire templates: `resources/views/`, `app/Livewire/`
- Frontend config: `vite.config.js`, `tailwind.config.js`, `postcss.config.js`
- Build artifacts and pipeline configs (`package.json`, `.github/workflows/*`)
(See D00, D11, D12, D13, D14)

Traceability (Mandatory)
- Every feature, UX change or production-visible UI fix MUST reference requirement/design/test IDs from D03/D04/D11 in the PR description and in top-of-file metadata for large components or automation scripts.
  - Example component metadata (top of Blade/JS file):
    ```text
    <!-- name: ticket-form.blade.php
         description: Ticket submission form with real-time validation
         author: frontend@motac.gov.my
         trace: SRS-FR-001; D04 §4.1; D12 §3
         last-updated: 2025-10-21 -->
    ```
- Update the Requirements Traceability Matrix (RTM) when adding new UI features (D03 ↔ D14).

Standards & References (Mandatory)
- D00–D14 documentation set for functional/design traceability
- WCAG 2.2 Level AA (accessibility) — D12/D14
- ISO 9241-210, ISO 9001 for usability & quality
- MOTAC/BPM policies (security, privacy, change management)
- Frontend tooling: Tailwind 3, Vite, Bootstrap where used in Admin templates, FontAwesome/Material icons

Mandatory Rules & Conventions
- Use Tailwind CSS 3 for project styles; keep global tokens in `resources/css/app.css` and theme values in `tailwind.config.js`.
- Use Vite as the build tool. Entry points: `resources/css/app.css` and `resources/js/app.js`. Keep these referenced in `vite.config.js`.
- Put interactive JS inside `resources/js/` (prefer modules); do not add new top-level frontend directories — follow existing structure.
- Blade views go in `resources/views/`; Livewire components in `app/Livewire/`. Filament UI stays under `app/Filament/`.
- Accessibility first: every form element must have a label, required fields must include text markers (not color alone), interactive controls must be keyboard accessible and include clear focus styles.
- Localization: UI primary language is Bahasa Melayu; include English secondary text where helpful (see D15). Use `lang` attributes on elements as needed.
- Asset naming & organization: logical, kebab-case for filenames (e.g., `ticket-form.blade.php`, `user-table.js`).
- Do NOT commit secrets or credentials. Document required environment variables in docs/ or a template `.env.example`.

Developer workflows (local)
- Install / start:
  - npm install
  - composer install
  - npm run dev   # vite dev server (frontend)
  - composer run dev   # project-level dev orchestration (if provided)
- Build for production:
  - npm run build
  - php artisan config:cache && php artisan route:cache (as required in deploy)
- Common scripts (example `package.json`):
  - "dev": "vite"
  - "build": "vite build"
  - "lint:css": "stylelint '**/*.css' --config .stylelintrc"
  - "format": "prettier --write 'resources/**/*.js,css,blade.php'"
- Use `npm ci` on CI for deterministic installs.

Linting, formatting & static checks
- CSS: stylelint configured with Tailwind plugin; fix issues locally.
- JS: ESLint (ES6) rules; prefer modern syntax and keep no-global-state patterns.
- Markup: Blade outputs should escape user content by default ( ). Avoid raw unescaped output unless intentionally required and documented.
- Run formatting/linting before PR: `npm run lint`, `npm run format`.
- Integrate `vendor/bin/pint` and PHPStan for PHP code; front-end PRs that touch Blade/Livewire must also pass PHP static checks.

Testing & Validation
- Unit & feature tests: backend (PHPUnit) remains authoritative for server-rendered behavior (`php artisan test`).
- Livewire/Volt component tests: use `Livewire::test()` or `Volt::test()` to assert component behavior.
- Accessibility testing:
  - Run automated checks: axe / axe-core, Lighthouse (CI), and stylelint a11y plugins where applicable.
  - Include accessibility scan step in CI: `.github/workflows/accessibility.yml` or add to main CI pipeline.
  - Manual checks: keyboard-only navigation, NVDA/VoiceOver smoke checks, color contrast verification (WCAG 4.5:1).
- Visual & regression testing: consider storybook/snapshot tests for complex components (optional; document in docs/ if added).

CI / Build integration (recommended)
- PRs modifying frontend must run jobs to:
  - npm ci && npm run lint && npm run build (or preview build)
  - php artisan test && vendor/bin/phpstan analyse && vendor/bin/pint
  - accessibility scans (lighthouse/axe)
- Ensure dev and CI environments run the same Node and npm versions (document in CONTRIBUTING or `.nvmrc`).

Performance & best practices
- Keep critical CSS small (use Tailwind purge/content scanning).
- Use Vite code-splitting/dynamic imports for large modules.
- Optimize images (webp / appropriate sizes) and use lazy-loading for non-critical assets.
- Cache static assets with long-lived cache headers and versioned filenames.

Accessibility & UI/UX (mandatory)
- WCAG 2.2 Level AA compliance for all production UI (D12/D14).
- Forms:
  - Each input must have a visible label; required fields marked with text and `aria-required="true"`.
  - Errors: inline messages tied via `aria-describedby`; summary at top on submit failure with focus moved there.
- Keyboard:
  - All interactive elements reachable via Tab; modals implement focus trap and restore focus on close.
  - Visible focus indicator (3–4px outline) meets contrast requirements.
- Colors & Contrast:
  - Use color tokens from style guide; test contrast ratios (≥4.5:1 for body text).
- Motion:
  - Respect `prefers-reduced-motion` and provide reduced-motion alternatives.
- Provide Bahasa Melayu content by default; English secondary strings optionally in `span lang="en"`.

Traceable UI documentation
- Document major components and pages under `docs/frontend/` (purpose, inputs/outputs, required secrets, traceability refs, accessibility notes, rollback instructions).
- For any new UI that affects data or workflows, add a corresponding doc in `docs/` and update RTM mappings.

Examples (quick snippets)

- vite.config.js (minimal)
```js
import  defineConfig  from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(
  plugins: [
    laravel(
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
),
,
);
```

- tailwind.config.js pointers
```js
module.exports = 
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Livewire/**/*.php',
,
  theme: 
    extend: 
      colors: 
        'motac-blue': '#003366',
        'motac-amber': '#FFC107',
  ,
,
  ,
  plugins: [],
;
```

PR Checklist (frontend-specific, add to PR body)
- [ ] Does this PR change UI text/labels? If yes: include traceability IDs and i18n note.
- [ ] Tests added/updated (Livewire/Volt or integration tests) and passing locally.
- [ ] Accessibility checks: axe/Lighthouse report attached or CI green.
- [ ] Linting & formatting: `npm run lint` & `npm run format` run successfully.
- [ ] Build verified: `npm run build` completes without errors.
- [ ] No secrets committed; required env vars documented.
- [ ] Docs: updated `docs/frontend/<feature>.md` with usage, accessibility notes and rollback steps.
- [ ] Reviewers: include UI/UX owner, accessibility reviewer, and backend owner when API/data changes are involved.

Deployment notes
- Frontend assets are built as part of the app release pipeline. Ensure `npm run build` runs and generated assets are referenced correctly by backend views.
- In production, use `php artisan config:cache` and `php artisan view:cache` after deploy.
- Monitor Lighthouse scores and key frontend SLOs (p95 load time) post-deploy.

Contacts & owners
- Frontend / UI Owner: design@motac.gov.my
- Accessibility / UX: accessibility@motac.gov.my
- DevOps / Build: devops@motac.gov.my
- Docs & Traceability: docs@motac.gov.my

Notes & governance
- This file is normative for frontend work in this repository. Any deviation that impacts accessibility, privacy, security, or traceability requires formal change management and RTM updates (see D01 §9.3 and D03/D11).
- Review and update at least annually or after major upgrades to Tailwind/Vite or platform changes.
