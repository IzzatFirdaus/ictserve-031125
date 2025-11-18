---
name: E2E Test Triage - Accessibility/Performance
about: Create a triage issue based on failing E2E accessibility or performance tests
title: '[TRIAGE] E2E - <module> - <problem short description>'
labels: 'triage, e2e'
assignees: ''

---

## Summary

(Short summary of the failing tests and observed failures. Link to `docs/e2e-triage/<file>` where available.)

## Steps to Reproduce

1. Ensure local dev env is running
2. Run the failing test(s):

```
npx playwright test tests/e2e/<refactored_file> --grep "@<tag>" --reporter=list --workers=1
```

3. Inspect `test-results/` artifacts (screenshot/video/axe report).

## Test(s) that fail

- `tests/e2e/<refactored_file>.spec.ts` — `<test name>`

## Observed Behavior
(Describe what fails and the failing metrics/axe violations)

## Expected Behavior
(What must be fixed for tests to pass)

## Suggested Fixes

- Frontend: (color tokens, aria attributes, tablist, alt text)
- Backend: (increase query efficiency, add caching, ensure the route is available)

## Artifacts

- Attach screenshot/video from `test-results/`
- Provide `axe` output if available (copy paste relevant nodes)

## Severity

- Critical / Major / Minor

## Owner

- Assign to `@frontend` for UI issues, `@backend` for API/DB issues, or both as needed

## Traceability

- Trace: D03-FR-007.2 (Performance) / D03-FR-008 (Accessibility)

---
