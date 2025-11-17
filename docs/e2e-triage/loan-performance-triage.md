# Loan Module - Performance Triage

This document records performance regressions uncovered in Playwright E2E tests for the loan module and suggests fixes.

Test file:

- `tests/e2e/loan-module-performance.refactored.spec.ts`

Failing tests identified locally:

- `02 - Guest loan request form loads quickly` — form load time exceeded threshold (26.7s measured)
- `07 - Time to Interactive (TTI) is acceptable` — TTI exceeded threshold (11.7s measured)
- `11 - Form submission response time is fast` — form was not visible or the route returned 404

Reproduction steps and quick triage:

1. Run the loan performance tests:

```powershell
npx playwright test tests/e2e/loan-module-performance.refactored.spec.ts --grep "@performance" --reporter=list --workers=1
```

2. Check server logs for slow backend responses to `/loans/request` and `api/loans` endpoints. Run profiler or `telescope` to capture SQL queries.

Common causes and recommendations:

- Missing front-end page for `/loans/request` (Page 404). Implement or route the page to the correct Livewire or Blade view.
- Large asset sizes: optimize images, compress/serve via CDN, implement tree-shaking for JS.
- Inefficient database queries: look for unbounded queries or queries executed per row (N+1); add `->with()` eager loading.
- Uncached API endpoints: add caching for non-sensitive list endpoints (short cache time). Consider using Redis.
- CI environment timeouts can be stricter than local — add timeouts or use environment-aware thresholds.

Suggested owner: `@backend` + `@frontend` for rendering/route fixes.

Next steps:

- Create one issue per bullet (404 page, slow API, bundle size) with screenshots and trace.
- Consider adding Lighthouse to nightly jobs for TTI/LCP tracking.

Traceability: D03-FR-007.2, D11 (Technical Design)

---
