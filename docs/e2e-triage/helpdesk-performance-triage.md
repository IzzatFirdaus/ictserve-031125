# Helpdesk - Performance Triage

This document captures performance failures surfaced by Playwright E2E tests and recommended fixes or investigation steps.

Test file:

- `tests/e2e/helpdesk-performance.refactored.spec.ts`

Failing tests seen locally:

- `02 - Helpdesk ticket list loads within acceptable time`: page load exceeded threshold
- `03 - Ticket submission form loads quickly`: form load exceeded threshold
- `10 - Time to Interactive (TTI) is acceptable`: TTI exceeded threshold

Why this matters:

- Slow LCP/TTI can negatively affect user experience and SEO.
- Slow form load reduces conversions and user satisfaction.

Reproduction steps:

1. Start local dev server: `php artisan serve` (if applicable) or ensure `npm run dev` running.
2. Run the single test locally with debug reporter:

```powershell
npx playwright test tests/e2e/helpdesk-performance.refactored.spec.ts --grep "@performance" --reporter=list --workers=1
```

3. Inspect failure artifacts in `test-results/` (screenshots/videos) and the network waterfall via browser devtools.

Common causes and fixes:

- Large JS bundles: Use code splitting and lazy loading for non-critical helpdesk components.
- Large image assets: Optimize images, use WebP/AVIF; add `loading="lazy"` where appropriate.
- Uncached assets: Ensure static assets have cache control headers (set `Cache-Control` via server or S3/CloudFront).
- Server response time: Profile backend endpoints used to load the ticket list. Add database indexes, optimize queries (avoid N+1), cache frequently-used data.
- Too many network requests: Consolidate requests; debounce or batch requests when necessary.

Suggested owner: `@frontend` and `@backend` teams (both) — front-end has to optimize bundles and images; backend must check API performance and DB queries.

Quick wins:

- Add server-side caching or use a short in-memory cache for ticket lists.
- Implement lazy-loading for images and non-essential widgets.
- Add a Lighthouse job in CI to measure the LCP & TTI baseline so we can track regressions.

Next steps:

- Create issues for: JS bundle optimization, image optimization, caching ticket list.
- Add Lighthouse/metrics job in `.github/workflows` for nightly performance runs.

Traceability: D03-FR-007.2, D11 (Technical Design), D00 (system overview)

---
