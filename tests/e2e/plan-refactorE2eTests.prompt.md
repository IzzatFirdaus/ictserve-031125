# Plan: Resolve E2E Test Failures — Refactor & Stabilize

Goal: Convert failing Playwright end-to-end tests to use the shared fixtures pattern, run the refactored tests, and capture actionable remediation steps for any legitimate app issues (accessibility, performance, missing routes).

High-level steps
1. Refactor failing tests to use `authenticatedPage` fixture (already done for helpdesk/accessibility/performance and loan modules). Keep the original files for reference until confirmed.
2. Run the refactored tests, identify real application issues (the tests are now reliable), and gather failing test contexts. Prioritize smoke tests that cover auth/navigation.
3. For each failing test, categorize the cause: app bug vs. test assertion too strict vs. environmental flakiness. Create remediation tasks for app issues.
4. Where appropriate, improve test assertions: add environment-aware thresholds for performance tests; use `expect.soft` for non-critical visual checks.
5. Add test tags and CI filtering: `@smoke` for quick checks, `@accessibility` and `@performance` for heavy validations.
6. Replace or update original test files once the refactored versions have green CI runs; keep a migration note in the PR.

Detailed action plan
- helpdesk-performance:
  - Ensure all navigation uses `authenticatedPage`.
  - Use `getByRole`/`getByLabel` instead of raw selectors for core elements.
  - Introduce environment-aware performance thresholds. For local/dev, allow relaxed values.
  - Run: `npx playwright test tests/e2e/helpdesk-performance.refactored.spec.ts --grep "@performance"`

- loan-module-accessibility:
  - Convert inline login to `authenticatedPage` where needed.
  - Run the axe checks and collect violations. Common failures will likely be color-contrast and ARIA structure.
  - Use `expect.soft` so a single WCAG failure doesn't block other checks.
  - Save Axe results and screenshots to triage.
  - Run: `npx playwright test tests/e2e/loan-module-accessibility.refactored.spec.ts --grep "@accessibility"`

- loan-module-performance:
  - Use `authenticatedPage` for sessions that require login.
  - Add `waitForLoadState('networkidle')` before performance timing starts (makes metrics consistent).
  - Increase page-level timeouts in CI for slower environments.
  - Run: `npx playwright test tests/e2e/loan-module-performance.refactored.spec.ts --grep "@performance"`

Testing & Validation
- Run the refactored tests with one worker in local: `npx playwright test tests/e2e/*.refactored.spec.ts --workers=1 --reporter=list`
- If tests fail: collect the failure artifacts from `test-results/` (screenshots, videos, and axe output) and open a triage issue.

Remediation & Tracking
- For each failing Axe rule or failing performance threshold:
  - Open an issue in this repo with: failing page, Axe rule or metric, screenshot/video, DOM snippet, reproduction steps, severity (blocker/major/minor), owner tag (frontend or backend).
  - Suggested fix examples: adjust tailwind color tokens for better contrast, add `role="tablist"` containers, add `aria-label` on dialogs, lazy-load or optimize large images.
- For performance issues: run Lighthouse locally, check network waterfall (assets size, server response times), and prioritize network/asset fixes.

CI & Maintenance
- Add Playwright command to CI to run smoke checks on PRs and nightly runs for heavy accessibility/performance tests.
- Maintain the fixture pattern in new tests. Document pattern in `docs/D10_Source_Code_DocumentATION.md` or similar.

Deliverables
- Four refactored tests in `tests/e2e/`: helpdesk+loan accessibility + performance (already implemented).
- A triage doc with all failing issues and recommended fixes.
- PR to replace original tests with refactored versions (or keep both with a migration note), include `trace` IDs.

Notes
- Do not relax Axe rules blindly — if the site intentionally uses color contrast smaller for design reasons, file a design accessibility tradeoff and ensure it's traceable.
- Some performance thresholds may be environment-specific; use environment flags to relax locally and enforce on CI.

Runbook (commands)
```powershell
# Run a single refactored test
npx playwright test tests/e2e/helpdesk-accessibility.refactored.spec.ts --reporter=list
# Run all refactored tests
npx playwright test tests/e2e/*.refactored.spec.ts --reporter=list --workers=1
# Run only smoke tests
npx playwright test --grep "@smoke"
```

Success Criteria
- All refactored tests pass on local and CI smoke runs.
- Identified accessibility defects are triaged with issues and owners assigned.
- Performance issues are triaged and optimized for release.

---
*Plan created on 2025-11-17 — ready for review and refinement.*
