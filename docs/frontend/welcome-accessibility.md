# Welcome page accessibility changes

Trace: SRS-FR-001; D12 §4.2; D14 §3; D15 §2

Summary
- Added semantic landmarks: `#main-content` (role="main"), `role="navigation"` on nav elements, and `role="contentinfo"` on footer.
- Skip link added (`#main-content`) to allow keyboard users to jump to primary content.
- Added ARIA live region `#a11y-live-region` (aria-live="polite", aria-atomic="true") to support screen reader announcements from dynamic components (language switcher, notifications).
- Language switcher emits browser events; client JS updates `document.documentElement.lang` and writes to cookie, then announces change via the ARIA live region.

Testing
- PHPUnit feature tests cover presence of skip link, main landmark, headings, and language switcher markup.
- pa11y CLI is run in CI and uploads `pa11y-welcome-report` artifact for PR review.

How to review
1. Open the welcome page and test keyboard navigation: Tab to "Skip to main content", press Enter — focus should move to the main content region.
2. Use a screen reader (NVDA/VoiceOver) and trigger language change — the ARIA live region should announce the language switch.
3. Check the pa11y artifact from CI for automated findings and prioritize high/critical issues.

Notes
- Automated scans are helpful but manual testing with assistive tech is required for full WCAG AA compliance.
- If additional dynamic notifications are added, ensure they use `aria-live` or `role="status"` and avoid `assertive` unless critical.
