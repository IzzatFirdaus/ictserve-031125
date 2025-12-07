# Plan: ICTServe v3.5.0 — Frontend Development Implementation ✅

### TL;DR
Systematic implementation of ICTServe's True Hybrid Architecture frontend following D00–D17 specifications. Focus areas:

- WCAG 2.2 AA compliance
- Livewire 3 / Volt patterns
- Tailwind CSS v4 & MyDS alignment
- Bilingual support (BM/ENG)
- Real-time broadcasting (Laravel Reverb / Echo)
- v3.5.0 feature completion (self-registration, account linking, API tokens, Pulse dashboard)

This plan is organized in phases (P0–P3) with tasks, documentation links, and actionable commands.

---
## Steps / Phases

### Phase 1 — Foundation & Compliance Fixes (P0 — Critical) ⚠️
Perform a rigorous accessibility and foundations audit and remediate critical issues.

Primary tasks:

- [ ] Run WCAG 2.2 AA audits (axe DevTools, WAVE, Lighthouse) against:
  - `resources/views/layouts/app.blade.php`
  - `resources/views/layouts/guest.blade.php`
- [ ] Standardize Livewire/Volt patterns (D13 §5):
  - Use Volt functional API for simple UI components
  - Use class-based Livewire for complex flows (multi-step wizards)
- [ ] Complete localization coverage:
  - Audit `resources/lang/en/` and `resources/lang/ms/`
  - Add missing translation keys for v3.5.0 features
- [ ] Produce documentation:
  - `docs/frontend/01-accessibility-compliance-audit-ictserve-3.5.0.md`
  - `docs/frontend/02-component-standardization-guide-ictserve-3.5.0.md`

### Phase 2 — v3.5.0 Missing UI Components (P1 — High) ⚙️
Implement and style v3.5.0 UI flows and components.

Primary tasks:

- [ ] Implement Self-Registration UI with `@motac.gov.my` validation:
  - File: `resources/views/auth/register.blade.php`
  - Behavior: input validation, email verification flow, friendly UX for staff and guests
- [ ] Create additional UX components (Volt functional):
  - Email verification page
  - Guest → Account linking prompt
  - Notification preferences panel
- [ ] Build API token management UI (Sanctum):
  - Consider a Filament resource or a UI page under `resources/views` or `app/Filament/Resources`
- [ ] Add Google SSO button (optional) and test OAuth flows via `config/services.php`
- [ ] Integrate Laravel Pulse dashboard widget (admin/superuser)

### Phase 3 — Real-Time Broadcasting (P1 — High) 🔔
Complete event broadcasting & client subscriptions for user and ticket channels.

Primary tasks:

- [ ] Implement client listeners in `resources/js/portal-echo.js` for events:
  - `EmailVerified`
  - `AccountLinked`
  - `ApiTokenCreated`
  - `GoogleSsoLinked`
- [ ] Implement reconnection logic and robust error handling for Echo/Reverb
- [ ] Test channel patterns and authorization:
  - `private-user.{id}`
  - `private-ticket.{uuid}`
- [ ] Update `resources/js/submission-echo.js` to reflect guest submission updates in the UI

### Phase 4 — Performance & Optimization (P2 — Medium) 🏎️
Improve performance and delivery of frontend assets.

Primary tasks:

- [ ] Reduce Tailwind CSS output (<50KB gzipped) by auditing `resources/css/app.css` and purging unused utilities
- [ ] Implement lazy loading for images and `x-cloak` in Alpine.js components to avoid FOUC
- [ ] Configure Core Web Vitals tracking and Laravel Pulse dashboards (LCP <2.5s, FID <100ms, TTFB <500ms)
- [ ] Add Playwright-based Lighthouse audits in CI (`playwright.config.ts`) and aim for score ≥ 90

### Phase 5 — Documentation & Testing Suite (P2 — Medium) 🧪
Write docs and tests to ensure maintainability and compliance.

Primary tasks:

- [ ] Create a component library and patterns documentation:
  - `docs/frontend/03-component-patterns-library-ictserve-3.5.0.md`
- [ ] Write Playwright E2E tests for critical flows:
  - `tests/Feature` & `playwright` scenarios for self-registration, guest→auth linking, token management
- [ ] Add Livewire/Volt tests using `Volt::test()` for the Volt components (D13 §6.4)
- [ ] Generate accessibility audit report `docs/frontend/04-wcag-audit-report-ictserve-3.5.0.md`

### Phase 6 — Production Preparation (P3 — Low) 🚀
Final checks and deployment readiness.

Primary tasks:

- [ ] Run quality checks:
  - `vendor/bin/phpstan analyse`
  - `vendor/bin/pint --dirty`
  - `php artisan test`
  - `npm run lint`
- [ ] Build and validate assets:
  - `npm run build`
  - Confirm Vite manifest contains the new asset entries
- [ ] Validate bilingual support and language-switcher on all new pages
- [ ] Update `docs/D10_SOURCE_CODE_DOCUMENTATION.md` with component references and traceability headers

---
## Further Considerations / Notes 💡

- **MyDS Design System alignment**: Verify `resources/css/app.css` against MyDS v2025.2 tokens. Decision point: create `resources/css/myds-tokens.css` vs extending `@theme` in `app.css`.
- **Filament Pulse integration**: Consider `app/Filament/Pages/PulseMonitoring.php` (Filament page) vs a standalone admin route. Use Filament `canAccess()` for authorization.
- **Broadcast testing strategy**: For true end-to-end safety, implement both Playwright WebSocket tests and Livewire/Volt unit tests. Optionally add `test-mcp-servers.ps1` to CI for automated broadcasting verification.

---
## Next steps & Timeline ⏳

- Phase 1 (P0) — 1 week ✅ (audit + fixes)
- Phase 2 (P1) — 2–3 weeks ✅ (UI build + SSO + Pulse integration)
- Phase 3 (P1) — 1 week ✅ (Broadcasting + Echo updates)
- Phase 4–5 (P2) — 1–2 weeks ✅ (Performance, tests, docs)
- Phase 6 (P3) — 1 week ✅ (Deployment readiness)

Estimated total: 6–8 weeks. Re-evaluate timeline after Phase 1.

---
## Quick Commands (Dev & Audit) 🔧

```powershell
# Run local checks
composer install --no-interaction; vendor/bin/pint --dirty; vendor/bin/phpstan analyse; php artisan test --filter=Frontend

# Playwright e2e
npm ci; npm run test:e2e

# Build production assets
npm ci; npm run build
```

---
_If you want, I can also open the next tasks as individual issues or create the missing docs in `docs/frontend/` (01–04). Would you like me to add them now?_
