PS C:\xampp\htdocs\ictserve-031125> npm run test:e2e -- --list

> test:e2e
> playwright test

Running 293 tests using 2 workers

  ✓    1 [chromium] › tests\e2e\accessibility-compliance.spec.ts:44:3 › Portal Accessibility Compliance (WCAG 2.2 AA) › full accessibility scan with axe-core (7.6s)
  ✓    2 [chromium] › tests\e2e\accessibility-compliance.spec.ts:10:3 › Portal Accessibility Compliance (WCAG 2.2 AA) › keyboard navigation - all interactive elements accessible (7.0s)
  ✓    3 [chromium] › tests\e2e\accessibility-compliance.spec.ts:52:3 › Portal Accessibility Compliance (WCAG 2.2 AA) › focus indicators visible on all interactive elements (3.2s)
  ✓    4 [chromium] › tests\e2e\accessibility-compliance.spec.ts:76:3 › Portal Accessibility Compliance (WCAG 2.2 AA) › skip navigation link present and functional (2.9s)
  ✓    5 [chromium] › tests\e2e\accessibility-compliance.spec.ts:89:3 › Portal Accessibility Compliance (WCAG 2.2 AA) › color contrast meets WCAG AA standards (3.0s)
  ✓    6 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Welcome Page (5.7s)
  ✓    7 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Accessibility Statement (7.3s)
✅ Welcome Page: No accessibility violations found
✅ Welcome Page: 24 accessibility checks passed
  ✓    8 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Contact Page (6.9s)
✅ Accessibility Statement: No accessibility violations found
✅ Accessibility Statement: 24 accessibility checks passed
  ✓    9 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Services Page (6.0s)
✅ Contact Page: No accessibility violations found
✅ Contact Page: 27 accessibility checks passed
  ✓   10 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Helpdesk Ticket Form (Guest) (8.4s)
✅ Services Page: No accessibility violations found
✅ Services Page: 24 accessibility checks passed
  ✓   11 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Asset Loan Application Form (Guest) (6.6s)
✅ Helpdesk Ticket Form (Guest): No accessibility violations found
✅ Helpdesk Ticket Form (Guest): 28 accessibility checks passed
  ✓   12 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:146:9 › Task 10.1: Automated Accessibility Testing - Authenticated Pages › should pass WCAG 2.2 AA compliance: Staff Dashboard (15.6s)
✅ Asset Loan Application Form (Guest): No accessibility violations found
✅ Asset Loan Application Form (Guest): 17 accessibility checks passed
  ✓   13 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:146:9 › Task 10.1: Automated Accessibility Testing - Authenticated Pages › should pass WCAG 2.2 AA compliance: User Profile (16.3s)
✅ Staff Dashboard: No accessibility violations found
✅ Staff Dashboard: 27 accessibility checks passed
  ✓   14 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:146:9 › Task 10.1: Automated Accessibility Testing - Authenticated Pages › should pass WCAG 2.2 AA compliance: Submission History (13.1s)
✅ User Profile: No accessibility violations found
✅ User Profile: 27 accessibility checks passed
  ✓   15 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:146:9 › Task 10.1: Automated Accessibility Testing - Authenticated Pages › should pass WCAG 2.2 AA compliance: Claim Submissions (12.9s)
✅ Submission History: No accessibility violations found
✅ Submission History: 18 accessibility checks passed
  ✓   16 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:194:9 › Task 10.1: Automated Accessibility Testing - Approver Pages › should pass WCAG 2.2 AA compliance: Approval Interface (Grade 41+) (37.4s)
✅ Claim Submissions: No accessibility violations found
✅ Claim Submissions: 27 accessibility checks passed

- 17 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:244:9 › Task 10.1: Automated Accessibility Testing - Admin Pages › should pass WCAG 2.2 AA compliance: Admin Dashboard
✅ Approval Interface (Grade 41+): No accessibility violations found
✅ Approval Interface (Grade 41+): 29 accessibility checks passed
- 18 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:244:9 › Task 10.1: Automated Accessibility Testing - Admin Pages › should pass WCAG 2.2 AA compliance: Helpdesk Tickets Management
Admin login failed or no admin redirect, skipping admin tests
- 19 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:244:9 › Task 10.1: Automated Accessibility Testing - Admin Pages › should pass WCAG 2.2 AA compliance: Loan Applications Management
Admin login failed or no admin redirect, skipping admin tests
- 20 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:244:9 › Task 10.1: Automated Accessibility Testing - Admin Pages › should pass WCAG 2.2 AA compliance: Assets Management
Admin login failed or no admin redirect, skipping admin tests
  ✓   21 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:274:5 › Task 10.1: Automated Accessibility Testing - Mobile Viewport › should pass WCAG 2.2 AA compliance on mobile: Welcome Page (4.6s)
✅ Welcome Page (Mobile): No accessibility violations found
  ✓   22 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:284:5 › Task 10.1: Automated Accessibility Testing - Mobile Viewport › should pass WCAG 2.2 AA compliance on mobile: Helpdesk Form (4.4s)
✅ Helpdesk Form (Mobile): No accessibility violations found
  ✓   23 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:294:5 › Task 10.1: Automated Accessibility Testing - Mobile Viewport › should pass WCAG 2.2 AA compliance on mobile: Loan Application Form (2.4s)
✅ Loan Application Form (Mobile): No accessibility violations found
  ✓   24 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:306:5 › Task 10.1: Automated Accessibility Testing - Specific WCAG 2.2 Criteria › should have proper focus indicators (SC 2.4.7) (3.7s)
  ✓   25 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:339:5 › Task 10.1: Automated Accessibility Testing - Specific WCAG 2.2 Criteria › should have minimum touch target size 44x44px (SC 2.5.8) (3.8s)
  ✓   26 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:361:5 › Task 10.1: Automated Accessibility Testing - Specific WCAG 2.2 Criteria › should have proper color contrast (SC 1.4.3, 1.4.11) (4.2s)
Admin login failed or no admin redirect, skipping admin tests
  ✘   27 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:45:5 › Staff Dashboard Accessibility › keyboard navigation through dashboard elements (12.8s)
  ✓   28 [chromium] › tests\e2e\branding-smoke.spec.ts:4:3 › Branding smoke checks › header, notification icon, and email asset are available (4.8s)
  ✓   29 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:92:5 › Staff Dashboard Accessibility › color contrast meets WCAG AA standards (7.6s)
  ✓   30 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:144:5 › Staff Dashboard Accessibility › touch targets meet minimum size requirements (8.7s)
  ✓   31 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:206:5 › Staff Dashboard Accessibility › ARIA attributes and semantic HTML (9.1s)
  ✓   32 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:248:5 › Staff Dashboard Accessibility › screen reader compatibility (9.3s)
  ✓   33 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:286:5 › Staff Dashboard Accessibility › focus management (9.3s)
      34 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:323:5 › Staff Dashboard Accessibility › responsive accessibility across viewports
  ✓   35 [chromium] › tests\e2e\devtools.integration.spec.ts:9:3 › Chrome DevTools Debugging Suite › should capture performance metrics (4.7s)
Performance Metrics: {
  domContentLoaded: 4.199999988079071,
  loadComplete: 0,
  totalTime: 2465.199999988079
}
      36 [chromium] › tests\e2e\devtools.integration.spec.ts:35:3 › Chrome DevTools Debugging Suite › should detect all network requests and responses
Total Requests: 23
Request Log: [
  {
    "url": "http://localhost:8000/",
    "method": "GET",
    "status": 200
  },
  {
    "url": "https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap",
    "method": "GET",
    "status": 200
  },
  {
    "url": "http://[::1]:5173/@vite/client",
    "method": "GET",
    "status": 200
  },
  {
    "url": "http://[::1]:5173/resources/css/app.css",
    "method": "GET",
    "status": 200
  },
  {
    "url": "http://[::1]:5173/resources/js/app.js",
    "method": "GET",
    "status": 200
  },
  {
    "url": "http://localhost:8000/livewire/livewire.js?id=df3a17f2",
    "method": "GET",
    "status": 200
  },
  {
    "url": "http://[::1]:5173/resources/js/bootstrap.js",
    "method": "GET",
    "status": 200
  },
  {
    "url": "http://[::1]:5173/resources/js/portal-mobile.js",
    "method": "GET",
    "status": 200
  },
  {
    "url": "http://[::1]:5173/resources/js/portal-echo.js",
    "method": "GET",
    "status": 200
  },
  {
    "url": "http://[::1]:5173/resources/js/submission-echo.js",
    "method": "GET",
    "status": 200
  }
]
  ✓   36 [chromium] › tests\e2e\devtools.integration.spec.ts:35:3 › Chrome DevTools Debugging Suite › should detect all network requests and responses (4.3s)
  ✓   37 [chromium] › tests\e2e\devtools.integration.spec.ts:68:3 › Chrome DevTools Debugging Suite › should capture console messages and errors (5.8s)
  ✓   34 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:323:5 › Staff Dashboard Accessibility › responsive accessibility across viewports (15.1s)
      38 [chromium] › tests\e2e\devtools.integration.spec.ts:100:3 › Chrome DevTools Debugging Suite › should check accessibility tree
Console Logs: {
  logs: [
    '🔍 Browser logger active (MCP server detected). Posting to: http://localhost:8000/_boost/browser-logs'
  ],
  warnings: [],
  errors: []
}
  ✓   39 [chromium] › tests\e2e\devtools.integration.spec.ts:121:3 › Chrome DevTools Debugging Suite › should validate page security headers (3.5s)
Security Headers: {
  'content-security-policy': undefined,
  'x-frame-options': undefined,
  'x-content-type-options': undefined,
  'x-xss-protection': undefined
}
      40 [chromium] › tests\e2e\devtools.integration.spec.ts:136:3 › Chrome DevTools Debugging Suite › should check for memory leaks in navigation
Accessibility Check:
- Main content landmark: true
- Navigation landmark: true
  ✓   38 [chromium] › tests\e2e\devtools.integration.spec.ts:100:3 › Chrome DevTools Debugging Suite › should check accessibility tree (5.3s)
  ✓   41 [chromium] › tests\e2e\devtools.integration.spec.ts:170:3 › Chrome DevTools Debugging Suite › should validate DOM and CSS rendering (5.9s)
DOM Statistics: {
  elementCount: 166,
  styleSheets: 4,
  images: 1,
  links: 27,
  buttons: 4,
  forms: 0
}
  ✓   42 [chromium] › tests\e2e\devtools.integration.spec.ts:191:3 › Chrome DevTools Debugging Suite › should check for unhandled promise rejections (5.8s)
Memory Usage by Route: [
  { route: '/', memory: 10000000 },
  { route: '/login', memory: 10000000 }
]
  ✓   40 [chromium] › tests\e2e\devtools.integration.spec.ts:136:3 › Chrome DevTools Debugging Suite › should check for memory leaks in navigation (6.6s)
  ✓   43 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:30:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 01 - Welcome Page - Initial Load (4.2s)
✓ Screenshot saved: public\images\screenshots\01_welcome_page_home_guest.png
  ✓   44 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:46:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 02 - Welcome Page - Navigate to Helpdesk (7.1s)
Page Errors: [ 'You must pass your app key when you instantiate Pusher.' ]
  ✓   45 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:246:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 07 - Navigate to Loan Application Form (7.3s)
✓ Screenshot saved: public\images\screenshots\02_welcome_page_navigation_guest.png
  ✓   46 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:71:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 03 - Helpdesk Form - Loaded (4.9s)
✓ Screenshot saved: public\images\screenshots\07_welcome_loan_navigation_guest.png
  ✓   47 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:271:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 08 - Loan Application Form - Loaded (6.1s)
✓ Screenshot saved: public\images\screenshots\03_helpdesk_form_loaded_guest.png
  ✓   48 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:88:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 04 - Helpdesk Form - Filling Out (5.8s)
✓ Screenshot saved: public\images\screenshots\08_loan_form_loaded_guest.png
  ✓   49 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:288:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 09 - Loan Application Form - Filling Out (5.4s)
✓ Screenshot saved: public\images\screenshots\04_helpdesk_form_filled_guest.png
  ✓   50 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:165:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 05 - Helpdesk Form - Submit (6.8s)
✓ Screenshot saved: public\images\screenshots\09_loan_form_filled_guest.png
  ✓   51 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:365:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 10 - Loan Application Form - Submit (4.9s)
✓ Screenshot saved: public\images\screenshots\05_helpdesk_form_submitted_guest.png
      52 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:212:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 06 - Helpdesk Success Page
✓ Screenshot saved: public\images\screenshots\10_loan_form_submitted_guest.png
  ✓   53 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:410:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 11 - Loan Application Success Page (5.1s)
✓ Screenshot saved: public\images\screenshots\11_loan_success_page_guest.png
      54 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:450:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 12 - Complete Flow Summary - Screenshots Verification

╔════════════════════════════════════════════════════════════╗
║     Guest User Flow - Screenshots Captured                  ║
╚════════════════════════════════════════════════════════════╝

1. [Step 01] welcome - page (home)
   📸 Location: /public/images/screenshots/01_welcome_page_home_guest.png

2. [Step 01] welcome - page (home)
   📸 Location: /public/images/screenshots/01_welcome_page_home_staff.png

3. [Step 02] welcome - page (navigate)
   📸 Location: /public/images/screenshots/02_welcome_page_navigate_to_login_staff.png

4. [Step 02] welcome - page (navigation)
   📸 Location: /public/images/screenshots/02_welcome_page_navigation_guest.png

5. [Step 03] helpdesk - form (loaded)
   📸 Location: /public/images/screenshots/03_helpdesk_form_loaded_guest.png

6. [Step 03] login - page (fill)
   📸 Location: /public/images/screenshots/03_login_page_fill_credentials_staff.png

7. [Step 04] helpdesk - form (filled)
   📸 Location: /public/images/screenshots/04_helpdesk_form_filled_guest.png

8. [Step 04] login - submit (authenticate)
   📸 Location: /public/images/screenshots/04_login_submit_authenticate_staff.png

9. [Step 05] dashboard - main (view)
   📸 Location: /public/images/screenshots/05_dashboard_main_view_staff.png

10. [Step 05] helpdesk - form (submitted)
   📸 Location: /public/images/screenshots/05_helpdesk_form_submitted_guest.png

11. [Step 06] dashboard - quick (actions)
   📸 Location: /public/images/screenshots/06_dashboard_quick_actions_staff.png

12. [Step 06] helpdesk - success (page)
   📸 Location: /public/images/screenshots/06_helpdesk_success_page_guest.png

13. [Step 07] navigate - to (helpdesk)
   📸 Location: /public/images/screenshots/07_navigate_to_helpdesk_form_staff.png

14. [Step 07] welcome - loan (navigation)
   📸 Location: /public/images/screenshots/07_welcome_loan_navigation_guest.png

15. [Step 08] helpdesk - form (fill)
   📸 Location: /public/images/screenshots/08_helpdesk_form_fill_details_staff.png

16. [Step 08] loan - form (loaded)
   📸 Location: /public/images/screenshots/08_loan_form_loaded_guest.png

17. [Step 09] helpdesk - form (submit)
   📸 Location: /public/images/screenshots/09_helpdesk_form_submit_staff.png

18. [Step 09] loan - form (filled)
   📸 Location: /public/images/screenshots/09_loan_form_filled_guest.png

Total Screenshots Captured: 18
Screenshot Directory: ./public/images/screenshots

  ✓   54 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:450:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 12 - Complete Flow Summary - Screenshots Verification (113ms)
  ✓   55 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:23:3 › Helpdesk Module - Accessibility Compliance › should pass WCAG 2.2 AA automated checks on helpdesk pages (8.8s)
✓ Screenshot saved: public\images\screenshots\06_helpdesk_success_page_guest.png
  ✓   52 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:212:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 06 - Helpdesk Success Page (7.1s)
  ✓   56 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:45:3 › Helpdesk Module - Accessibility Compliance › should support full keyboard navigation on helpdesk forms (4.3s)
  ✓   57 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:68:3 › Helpdesk Module - Accessibility Compliance › should have visible focus indicators with 3:1 contrast ratio (6.6s)
  ✘   58 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:101:3 › Helpdesk Module - Accessibility Compliance › should have minimum 44x44px touch targets on mobile (7.4s)
  ✓   59 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:129:3 › Helpdesk Module - Accessibility Compliance › should have proper ARIA landmarks and labels (7.7s)
  ✓   60 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:156:3 › Helpdesk Module - Accessibility Compliance › should have proper color contrast ratios (4.5:1 for text) (7.9s)
  ✓   61 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:177:3 › Helpdesk Module - Accessibility Compliance › should support screen reader announcements with ARIA live regions (6.2s)
  ✓   62 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:197:3 › Helpdesk Module - Accessibility Compliance › should have semantic HTML structure with proper headings (8.9s)
  ✓   63 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:214:3 › Helpdesk Module - Accessibility Compliance › should not rely on color alone for information (8.5s)
  ✓   64 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:66:3 › Helpdesk Module - Cross-Module Integration › should link helpdesk tickets to asset records (3.0s)
  ✓   65 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:106:3 › Helpdesk Module - Cross-Module Integration › should send cross-module notifications (3.0s)
✓ Laravel server is running
✓ Laravel server is running
✓ App is responding (page title: ICTServe)
  ✓   66 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:78:3 › Helpdesk Module - Cross-Module Integration › should display asset information in ticket details (2.7s)
✓ Browser connection is active
  ✓   67 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:113:3 › Helpdesk Module - Cross-Module Integration › should validate referential integrity between modules (3.1s)
✓ Page content loaded successfully
  ✓   68 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:85:3 › Helpdesk Module - Cross-Module Integration › should create maintenance ticket when asset returned damaged (3.7s)
✓ DOM has 166 elements
  ✓   69 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:120:3 › Helpdesk Module - Cross-Module Integration › should track cross-module audit trail (4.2s)
✓ Navigation working (current URL: <http://localhost:8000/>)
  ✓   70 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:92:3 › Helpdesk Module - Cross-Module Integration › should display unified asset history (loans + tickets) (3.2s)
✓ Page language: en
  ✓   71 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:127:3 › Helpdesk Module - Cross-Module Integration › should handle cross-module API endpoints (3.9s)
✓ DOM structure is valid
  ✓   72 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:99:3 › Helpdesk Module - Cross-Module Integration › should maintain data consistency across modules (3.6s)
✓ Playwright request handler available
  ✓   73 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:134:3 › Helpdesk Module - Cross-Module Integration › should display cross-module dashboard analytics (5.0s)
✓ HTML structure is valid
  ✓   74 [chromium] › tests\e2e\helpdesk-performance.spec.ts:21:3 › Helpdesk Module - Performance Tests › should meet Core Web Vitals targets (LCP <2.5s, FID <100ms, CLS <0.1) (12.1s)
✓ All tests completed successfully
  ✓   75 [chromium] › tests\e2e\helpdesk-performance.spec.ts:76:3 › Helpdesk Module - Performance Tests › should load helpdesk ticket submission form within 2 seconds (6.7s)
  ✓   76 [chromium] › tests\e2e\helpdesk-performance.spec.ts:99:3 › Helpdesk Module - Performance Tests › should handle ticket list pagination efficiently (7.5s)
  ✓   77 [chromium] › tests\e2e\helpdesk-performance.spec.ts:121:3 › Helpdesk Module - Performance Tests › should optimize database queries (no N+1 issues) (6.4s)
  ✓   78 [chromium] › tests\e2e\helpdesk-performance.spec.ts:141:3 › Helpdesk Module - Performance Tests › should cache static assets effectively (14.0s)
  ✓   79 [chromium] › tests\e2e\helpdesk-performance.spec.ts:173:3 › Helpdesk Module - Performance Tests › should handle form submission within 2 seconds (7.2s)
  ✓   80 [chromium] › tests\e2e\helpdesk-performance.spec.ts:209:3 › Helpdesk Module - Performance Tests › should optimize image loading with lazy loading (6.9s)
  ✓   81 [chromium] › tests\e2e\helpdesk-performance.spec.ts:233:3 › Helpdesk Module - Performance Tests › should handle concurrent user interactions efficiently (8.3s)
  ✘   82 [chromium] › tests\e2e\helpdesk-performance.spec.ts:260:3 › Helpdesk Module - Performance Tests › should achieve Lighthouse Performance score 90+ (8.4s)
  ✓   83 [chromium] › tests\e2e\helpdesk-performance.spec.ts:286:3 › Helpdesk Module - Performance Tests › should minimize JavaScript bundle size (7.2s)
  ✘   84 [chromium] › tests\e2e\helpdesk.module.spec.ts:15:3 › Helpdesk Ticket Module › should load welcome page without errors (1.6m)
  ✘   85 [chromium] › tests\e2e\helpdesk.module.spec.ts:33:3 › Helpdesk Ticket Module › should navigate to helpdesk module (1.6m)
  ✘   86 [chromium] › tests\e2e\helpdesk.module.spec.ts:46:3 › Helpdesk Ticket Module › should display ticket list without console errors (1.6m)
  ✘   87 [chromium] › tests\e2e\helpdesk.module.spec.ts:71:3 › Helpdesk Ticket Module › should handle ticket creation form (1.6m)
  ✘   88 [chromium] › tests\e2e\helpdesk.module.spec.ts:94:3 › Helpdesk Ticket Module › should respond to filter interactions (1.6m)
  ✘   89 [chromium] › tests\e2e\helpdesk.module.spec.ts:116:3 › Helpdesk Ticket Module › should handle network errors gracefully (1.6m)
      90 [chromium] › tests\e2e\helpdesk.module.spec.ts:138:3 › Helpdesk Ticket Module › should maintain session across navigation
  ✓   91 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:19:3 › Loan Module Accessibility › Guest loan application form meets WCAG 2.2 AA (5.4s)
  ✓   92 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:29:3 › Loan Module Accessibility › Authenticated loan dashboard meets WCAG 2.2 AA (7.3s)
  ✓   93 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:46:3 › Loan Module Accessibility › Loan history page meets WCAG 2.2 AA (5.9s)
  ✘   94 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:63:3 › Loan Module Accessibility › Keyboard navigation works on loan form (3.6s)
  ✘   95 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:80:3 › Loan Module Accessibility › Form validation errors are announced to screen readers (34.3s)
  ✓   96 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:95:3 › Loan Module Accessibility › Color contrast meets WCAG AA standards (3.7s)
  ✓   97 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:110:3 › Loan Module Accessibility › Images have alt text (2.6s)
  ✓   98 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:121:3 › Loan Module Accessibility › Form labels are properly associated (2.9s)
  ✓   99 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:135:3 › Loan Module Accessibility › Skip links are present and functional (2.6s)
  ✓  100 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:152:3 › Loan Module Accessibility › Language attribute is set correctly (2.4s)
  ✘  101 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:159:3 › Loan Module Accessibility › Page title is descriptive (2.0s)
  ✓  102 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:167:3 › Loan Module Accessibility › Touch targets meet minimum size (44x44px) (2.6s)
  ✓  103 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:181:3 › Loan Module Accessibility › Modal dialogs have proper ARIA attributes (4.1s)
  ✓  104 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:202:3 › Loan Module Accessibility › Tables have proper structure (6.5s)
  ✓  105 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:225:3 › Loan Module Accessibility › Responsive design maintains accessibility (5.8s)
  ✓  106 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:247:3 › Loan Module Accessibility › Loading states are announced (2.7s)
  ✘   90 [chromium] › tests\e2e\helpdesk.module.spec.ts:138:3 › Helpdesk Ticket Module › should maintain session across navigation (1.6m)
  ✓  107 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:257:3 › Loan Module Accessibility › Focus trap works in modals (6.4s)
  ✓  108 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:287:3 › Loan Module Accessibility › Escape key closes modals (6.9s)
  ✘  109 [chromium] › tests\e2e\loan-module-integration.spec.ts:11:3 › Loan Module Integration Tests › complete guest loan application workflow (35.7s)
  ✘  110 [chromium] › tests\e2e\loan-module-integration.spec.ts:38:3 › Loan Module Integration Tests › authenticated user loan workflow (13.1s)
  ✘  111 [chromium] › tests\e2e\loan-module-integration.spec.ts:61:3 › Loan Module Integration Tests › email approval workflow simulation (7.2s)
  ✘  112 [chromium] › tests\e2e\loan-module-integration.spec.ts:70:3 › Loan Module Integration Tests › loan extension request workflow (35.4s)
  ✘  113 [chromium] › tests\e2e\loan-module-integration.spec.ts:90:3 › Loan Module Integration Tests › asset availability check integration (32.8s)
  ✘  114 [chromium] › tests\e2e\loan-module-integration.spec.ts:107:3 › Loan Module Integration Tests › cross-module navigation (35.3s)
  ✘  115 [chromium] › tests\e2e\loan-module-integration.spec.ts:127:3 › Loan Module Integration Tests › dashboard analytics integration (11.5s)
  ✘  116 [chromium] › tests\e2e\loan-module-integration.spec.ts:147:3 › Loan Module Integration Tests › notification system integration (35.8s)
  ✘  117 [chromium] › tests\e2e\loan-module-integration.spec.ts:165:3 › Loan Module Integration Tests › responsive design integration (8.8s)
  ✓  118 [chromium] › tests\e2e\loan-module-performance.spec.ts:15:3 › Loan Module Performance Tests › measures Core Web Vitals for loan dashboard (7.8s)
  ✘  119 [chromium] › tests\e2e\loan-module-performance.spec.ts:53:3 › Loan Module Performance Tests › loan application form loads quickly (36.0s)
  ✘  120 [chromium] › tests\e2e\loan-module-performance.spec.ts:64:3 › Loan Module Performance Tests › asset availability check is responsive (34.8s)
  ✘  121 [chromium] › tests\e2e\loan-module-performance.spec.ts:77:3 › Loan Module Performance Tests › loan history pagination is smooth (55.1s)
  ✘  122 [chromium] › tests\e2e\loan-module-performance.spec.ts:90:3 › Loan Module Performance Tests › search functionality is fast (54.2s)
  ✘  123 [chromium] › tests\e2e\loan-module-performance.spec.ts:104:3 › Loan Module Performance Tests › dashboard widgets load progressively (1.5m)
  ✘  124 [chromium] › tests\e2e\loan-module-performance.spec.ts:119:3 › Loan Module Performance Tests › measures Time to Interactive (TTI) (2.4m)
  ✓  125 [chromium] › tests\e2e\loan-module-performance.spec.ts:136:3 › Loan Module Performance Tests › checks bundle size impact (24.9s)
  ✘  126 [chromium] › tests\e2e\loan.module.spec.ts:15:3 › Asset Loan Module › should load home page without JavaScript errors (1.6m)
  ✘  127 [chromium] › tests\e2e\loan.module.spec.ts:35:3 › Asset Loan Module › should navigate to loan module (1.6m)
  ✘  128 [chromium] › tests\e2e\loan.module.spec.ts:49:3 › Asset Loan Module › should display loan list without errors (1.6m)
  ✘  129 [chromium] › tests\e2e\loan.module.spec.ts:75:3 › Asset Loan Module › should handle loan request form interaction (1.6m)
  ✘  130 [chromium] › tests\e2e\loan.module.spec.ts:99:3 › Asset Loan Module › should handle asset selection dropdown (1.6m)
  ✘  131 [chromium] › tests\e2e\loan.module.spec.ts:123:3 › Asset Loan Module › should handle approval workflow buttons (1.6m)
  ✘  132 [chromium] › tests\e2e\loan.module.spec.ts:144:3 › Asset Loan Module › should maintain responsive behavior (1.6m)
  ✘  133 [chromium] › tests\e2e\loan.module.spec.ts:161:3 › Asset Loan Module › should handle form validation feedback (1.7m)
     134 [chromium] › tests\e2e\loan.module.spec.ts:187:3 › Asset Loan Module › should handle network requests without failures
  ✘  135 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Welcome Page meets Core Web Vitals thresholds (9.1s)

Welcome Page Performance Metrics:
  LCP: 1952ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.002 (target: <0.1)
  TTFB: 1030ms (target: <600ms)
  Issues: TTFB 1030ms exceeds 600ms threshold
  ✘  136 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Accessibility Statement meets Core Web Vitals thresholds (16.0s)

Accessibility Statement Performance Metrics:
  LCP: 2964ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 2165ms (target: <600ms)
  Issues: LCP 2964ms exceeds 2.5s threshold, TTFB 2165ms exceeds 600ms threshold
  ✘  137 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Contact Page meets Core Web Vitals thresholds (22.1s)

Contact Page Performance Metrics:
  LCP: 2628ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 1823ms (target: <600ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 1823ms (target: <600ms)
  Issues: LCP 2628ms exceeds 2.5s threshold, TTFB 1823ms exceeds 600ms threshold
  CLS: 0.000 (target: <0.1)
  TTFB: 1823ms (target: <600ms)
  Issues: LCP 2628ms exceeds 2.5s threshold, TTFB 1823ms exceeds 600ms threshold
  ✘  138 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Services Page meets Core Web Vitals thresholds (11.0s)

Services Page Performance Metrics:
  LCP: 3504ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 2623ms (target: <600ms)
  Issues: LCP 3504ms exceeds 2.5s threshold, TTFB 2623ms exceeds 600ms threshold
     139 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Helpdesk Ticket Form meets Core Web Vitals thresholds
  ✘  134 [chromium] › tests\e2e\loan.module.spec.ts:187:3 › Asset Loan Module › should handle network requests without failures (1.6m)
     140 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Asset Loan Application Form meets  ✓  140 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Asset Loan Application Form meets Core Web Vitals thresholds (8.2s)
Helpdesk Ticket Form Performance Metrics:
  LCP: 2648ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 1808ms (target: <600ms)
  Issues: LCP 2648ms exceeds 2.5s threshold, TTFB 1808ms exceeds 600ms threshold

Asset Loan Application Form Performance Metrics:
  LCP: 1408ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 596ms (target: <600ms)

     141 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › Staff Dashboard meets Cor  ✘  141 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › Staff Dashboard meets Core Web Vitals thresholds (5ms)
  ✘  139 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Helpdesk Ticket Form meets Core Web Vitals thresholds (13.3s)
     142 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › User Profile meets Core W  ✘  142 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › User Profile meets Core W     143 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › Submission History meets   ✘  143 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › Submission History meets      144 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › Claim Submissions meets C  ✘  144 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › Claim Submissions meets C     145 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › My Tickets meets Core Web  ✘  145 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › My Tickets meets Core Web     146 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › My Loans meets Core Web V  ✘  146 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › My Loans meets Core Web V     147 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Admin Dashboard meets Core Web Vi  ✘  147 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Admin Dashboard meets Core Web Vi     148 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Helpdesk Tickets Management meets  ✘  148 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Helpdesk Tickets Management meets     149 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Loan Applications Management meet  ✘  149 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Loan Applications Management meet     150 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Assets Management meets Core Web   ✘  150 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Assets Management meets Core Web      151 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Users Management meets Core Web V  ✘  151 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Users Management meets Core Web V     152 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:303:9 › Core Web Vitals - Network Conditions Testing › Performance under Fast 3G conditions
     153 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:303:9 › Core Web Vitals - Network Conditions Testing › Performance under Slow 3G conditions

Welcome Page on Fast 3G:
  LCP: 2876ms
  TTFB: 1637ms

Helpdesk Form on Slow 3G:
  LCP: 3152ms
  TTFB: 1771ms
  ✘  152 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:303:9 › Core Web Vitals - Network Conditions Testing › Performance under Fast 3G conditions (37.2s)
     154 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:303:9 › Core Web Vitals - Network Conditions Testing › Performance under   ✘  154 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:303:9 › Core Web Vitals - Network Conditions Testing › Performance under 4G conditions (25.5s)
Welcome Page on 4G:
  LCP: 1260ms
  TTFB: 658ms
     155 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:370:9 › Core Web Vitals - Mobile vs Desktop Performance › Desktop perform  ✘  155 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:370:9 › Core Web Vitals - Mobile vs Desktop Performance › Desktop performance validation (28.2s)
Welcome Page on Desktop:
  LCP: 1764ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 1293ms (target: <600ms)
     156 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:370:9 › Core Web Vitals - Mobile vs Desktop Performance › Tablet performance validation

Welcome Page on Tablet:
  LCP: 1144ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 648ms (target: <600ms)

Loan Application Form on Slow 3G:
  LCP: 4320ms
  TTFB: 2126ms
  ✘  153 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:303:9 › Core Web Vitals - Network Conditions Testing › Performance under Slow 3G conditions (2.0m)
     157 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:370:9 › Core Web Vitals - Mobile vs Desktop Performance › Mobile performance validation

Helpdesk Form on Tablet:
  LCP: 1300ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 524ms (target: <600ms)

Welcome Page on Mobile:
  LCP: 1700ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 541ms (target: <600ms)

Loan Application Form on Tablet:
  LCP: 1608ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 580ms (target: <600ms)
  ✘  156 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:370:9 › Core Web Vitals - Mobile vs Desktop Performance › Tablet performance validation (36.2s)
     158 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:412:5 › Core Web Vitals - Performance Regression Testing › Compare curren  ✘  158 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:412:5 › Core Web Vitals - Performance Regression Testing › Compare curren     159 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:547:5 › Core Web Vitals - Performance Report Generation › Generate comprehensive performance report

Helpdesk Form on Mobile:
  LCP: 1332ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 566ms (target: <600ms)

Loan Application Form on Mobile:
  LCP: 1472ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 737ms (target: <600ms)
  ✘  157 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:370:9 › Core Web Vitals - Mobile vs Desktop Performance › Mobile performance validation (20.6s)
     160 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Welcome Page meets Lighthouse t  ✘  160 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Welcome Page meets Lighthouse thresholds (5.6s)
Welcome Page Lighthouse Scores:
  Performance: 29/100 (target: ≥90)
  Accessibility: 100/100 (target: ≥100)
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 29 below 90 threshold
     161 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Accessibility Statement meets L  ✘  161 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Accessibility Statement meets Lighthouse thresholds (3.8s)
Accessibility Statement Lighthouse Scores:
  Performance: 37/100 (target: ≥90)
  Accessibility: 100/100 (target: ≥100)
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 37 below 90 threshold
     162 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Contact Page meets Lighthouse t  ✘  162 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Contact Page meets Lighthouse thresholds (4.0s)
Contact Page Lighthouse Scores:
  Performance: 33/100 (target: ≥90)
  Accessibility: 90/100 (target: ≥100)
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 33 below 90 threshold, Accessibility score 90 below 100 threshold
     163 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Services Page meets Lighthouse   ✘  163 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Services Page meets Lighthouse thresholds (4.4s)
Services Page Lighthouse Scores:
  Performance: 27/100 (target: ≥90)
  Accessibility: 100/100 (target: ≥100)
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 27 below 90 threshold
     164 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Helpdesk Ticket Form meets Ligh  ✘  164 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Helpdesk Ticket Form meets Lighthouse thresholds (3.8s)
Helpdesk Ticket Form Lighthouse Scores:
  Performance: 40/100 (target: ≥90)
  Accessibility: 100/100 (target: ≥100)
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 40 below 90 threshold
     165 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Asset Loan Application Form mee  ✘  165 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Asset Loan Application Form meets Lighthouse thresholds (4.1s)
Asset Loan Application Form Lighthouse Scores:
  Performance: 33/100 (target: ≥90)
  Accessibility: 100/100 (target: ≥100)
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 33 below 90 threshold
     166 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:179:9 › Lighthouse Audit - Authenticated Pages › Staff Dashboard meets L  ✘  166 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:179:9 › Lighthouse Audit - Authenticated Pages › Staff Dashboard meets L     167 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:179:9 › Lighthouse Audit - Authenticated Pages › User Profile meets Ligh  ✘  167 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:179:9 › Lighthouse Audit - Authenticated Pages › User Profile meets Lighthouse thresholds (63ms)
========================================
CORE WEB VITALS PERFORMANCE REPORT
========================================

Overall: 1/6 pages passed all thresholds

Welcome Page (/)
  Status: ✗ FAILED
  LCP: 2252ms
  FID: 0ms
  CLS: 0.002
  TTFB: 709ms
  Issues: TTFB 709ms exceeds 600ms threshold

Accessibility Statement (/accessibility)
  Status: ✗ FAILED
  LCP: 1420ms
  FID: 0ms
  CLS: 0.000
  TTFB: 618ms
  Issues: TTFB 618ms exceeds 600ms threshold

Contact Page (/contact)
  Status: ✓ PASSED
  LCP: 1096ms
  FID: 0ms
  CLS: 0.000
  TTFB: 567ms

Services Page (/services)
  Status: ✗ FAILED
  LCP: 1472ms
  FID: 0ms
  CLS: 0.000
  TTFB: 674ms
  Issues: TTFB 674ms exceeds 600ms threshold

Helpdesk Ticket Form (/helpdesk/create)
  Status: ✗ FAILED
  LCP: 2284ms
  FID: 0ms
  CLS: 0.000
  TTFB: 1402ms
  Issues: TTFB 1402ms exceeds 600ms threshold

Asset Loan Application Form (/loan/apply)
  Status: ✗ FAILED
  LCP: 1460ms
  FID: 0ms
  CLS: 0.000
  TTFB: 649ms
  Issues: TTFB 649ms exceeds 600ms threshold

  ✘  159 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:547:5 › Core Web Vitals - Performance Report Generation › Generate comprehensive performance report (42.1s)
     168 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:179:9 › Lighthouse Audit - Authenticated Pages › Submission History meet  ✘  168 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:179:9 › Lighthouse Audit - Authenticated Pages › Submission History meet     169 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:212:9 › Lighthouse Audit - Admin Pages › Admin Dashboard meets Lighthous  ✘  169 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:212:9 › Lighthouse Audit - Admin Pages › Admin Dashboard meets Lighthous     170 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:212:9 › Lighthouse Audit - Admin Pages › Helpdesk Tickets Management mee  ✘  170 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:212:9 › Lighthouse Audit - Admin Pages › Helpdesk Tickets Management mee     171 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:235:5 › Lighthouse report
     172 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:57:13 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should display 1 column layout on 320px viewport (iPhone SE)

========================================
LIGHTHOUSE PERFORMANCE AUDIT REPORT
========================================

Overall: 0/6 pages passed all thresholds

Welcome Page (<http://localhost:8000/>)
  Status: ✗ FAILED
  Performance: 24/100
  Accessibility: 100/100
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 24 below 90 threshold

Accessibility Statement (<http://localhost:8000/accessibility>)
  Status: ✗ FAILED
  Performance: 25/100
  Accessibility: 100/100
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 25 below 90 threshold

Contact Page (<http://localhost:8000/contact>)
  Status: ✗ FAILED
  Performance: 29/100
  Accessibility: 90/100
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 29 below 90 threshold, Accessibility score 90 below 100 threshold

Services Page (<http://localhost:8000/services>)
  Status: ✗ FAILED
  Performance: 35/100
  Accessibility: 100/100
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 35 below 90 threshold

Helpdesk Ticket Form (<http://localhost:8000/helpdesk/create>)
  Status: ✗ FAILED
  Performance: 30/100
  Accessibility: 100/100
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 30 below 90 threshold

Asset Loan Application Form (<http://localhost:8000/loan/apply>)
  Status: ✗ FAILED
  Performance: 38/100
  Accessibility: 100/100
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 38 below 90 threshold

Report saved to: test-results/lighthouse-audit-report.json

  ✘  171 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:235:5 › Lighthouse Audit - Comprehensive Report › Generate comprehensive Lighthouse report (25.4s)
     173 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:57:13 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should display 1 column layout on 375px viewport (iPhone 8)
  ✘  172 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:57:13 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-41  ✘  173 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:57:13 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should display 1 column layout on 375px viewport (iPhone 8) (1.6m) - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should display 1 column layout on 414px viewport (iPhone 11 Pro Max)
  ✘  174 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:57:13 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should display 1 column layout on 414px viewport (iPhone 11 Pro Max) (1.6m)
  ✘  175 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:109:9 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should stack quick action buttons vertically on mobile (1.6m)
  ✘  176 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:136:9 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should display recent activity in single column on mobile (1.6m)
  ✘  177 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:168:13 › Staff Dashboard - Responsive Behavior › Tablet Viewports (768px-1024px): 2 Column Layout › should display 2 column layout on 768px viewport (iPad Mini) (1.6m)
  ✘  178 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:168:13 › Staff Dashboard - Responsive Behavior › Tablet Viewports (768px-1024px): 2 Column Layout › should display 2 column layout on 820px viewport (iPad Air) (1.6m)
  ✘  179 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:168:13 › Staff Dashboard - Responsive Behavior › Tablet Viewports (768px-1024px): 2 Column Layout › should display 2 column layout on 1000px viewport (iPad Pro) (1.6m)
  ✘  180 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:222:9 › Staff Dashboard - Responsive Behavior › Tablet Viewports (768px-1024px): 2 Column Layout › should display recent activity in 2 columns on tablet (1.6m)
  ✘  181 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:248:13 › Staff Dashboard - Responsive Behavior › Desktop Viewports (1280px+): 4 Column Layout › should display 4 column layout on 1280px viewport (Desktop HD) (1.6m)
  ✘  182 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:248:13 › Staff Dashboard - Responsive Behavior › Desktop Viewports (1280px+): 4 Column Layout › should display 4 column layout on 1920px viewport (Desktop Full HD) (1.6m)
  ✘  183 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:248:13 › Staff Dashboard - Responsive Behavior › Desktop Viewports (1280px+): 4 Column Layout › should display 4 column layout on 2560px viewport (Desktop 4K) (1.6m)
  ✘  184 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:308:9 › Staff Dashboard - Responsive Behavior › Desktop Viewports (1280px+): 4 Column Layout › should display all cards in single row on desktop (1.6m)
  ✘  185 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:335:9 › Staff Dashboard - Responsive Behavior › Desktop Viewports (1280px+): 4 Column Layout › should display quick actions in single row on desktop (1.6m)
✘  187 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:391:9 › Staff Dashboard - Responsive Behavior › Performance and Loading › should load quickly on desktop viewport (1.6m)
✘  188 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:404:9 › Staff Dashboard - Responsive Behavior › Performance and Loading › should load quickly on mobile viewport (1.7m)
  ✘  189 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:419:9 › Staff Dashboard - Responsive Behavior › No Horizontal Scroll › should not have horizontal scroll on any viewport (1.7m)
  ✘  190 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:447:9 › Staff Dashboard - Responsive Behavior › Content Readability › should have readable text on mobile viewport (1.6m)
  ✘  191 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:467:9 › Staff Dashboard - Responsive Behavior › Responsive Image and Icon Handling › should display icons properly on all viewports (1.8m)
  ✘  192 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:492:9 › Staff Dashboard - Responsive Behavior › Viewport Transition Smoothness › should handle viewport resize gracefully (1.6m)
     193 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:516:9 › Staff Dashboard - Responsive Behavior › Accessibility on Different Viewports › should maintain focus indicators on all viewports
     194 [chromium] › tests\e2e\staff-flow-debug.spec.ts:14:3 › Staff User Debug Flow › Staff journey: Steps 1-5 only

🔧 Debug test - Steps 1-5 only

📸 Step 1/5: Welcome page
✅ Step 1 complete

📸 Step 2/5: Navigate to login
🔗 Found login link, clicking...
⏳ Waiting for login page to load...
✅ Login page loaded
✅ Step 2 complete

📸 Step 3/5: Fill login credentials
✅ Step 3 complete

📸 Step 4/5: Submit login
✅ Step 4 complete - Authenticated!

📸 Step 5/5: Dashboard main view
✅ Step 5 complete

🎉 Debug test complete - All 5 steps passed!
  ✓  194 [chromium] › tests\e2e\staff-flow-debug.spec.ts:14:3 › Staff User Debug Flow › Staff journey: Steps 1-5 only (11.0s)
     195 [chromium] › tests\e2e\staff-flow-optimized.spec.ts:23:3 › Staff User Complete Flow › Staff journey: Welcome to Logout with all features

🚀 Starting optimized staff flow test

📸 Step 1/19: Welcome page
✅ Step 1 complete

📸 Step 2/19: Navigate to login
  ✘  193 [chromium] › tests\e2e\staff-dashboard.responsive.spec.ts:516:9 › Staff Dashboard - Responsive Behavior › Accessibility on Different Viewports › should maintain focus indicators on all viewports (1.6m)
  ✓  196 [chromium] › tests\e2e\accessibility.comprehensive.refactored.spec.ts:108:9 › 01 - Automated Accessibility Testing - Guest Pages › 01-1 - Welcome Page should pass WCAG 2.2 AA @accessibility @a11y @wcag @guest @smoke (6.2s)
✅ Welcome Page: No accessibility violations found
✅ Welcome Page: 24 accessibility checks passed
  ✓  197 [chromium] › tests\e2e\accessibility.comprehensive.refactored.spec.ts:108:9 › 01 - Automated Accessibility Testing - Guest Pages › 01-2 - Accessibility Statement should pass WCAG 2.2 AA @accessibility @a11y @wcag @guest @smoke (6.5s)
✅ Accessibility Statement: No accessibility violations found
✅ Accessibility Statement: 24 accessibility checks passed
  ✓  198 [chromium] › tests\e2e\accessibility.comprehensive.refactored.spec.ts:108:9 › 01 - Automated Accessibility Testing - Guest Pages › 01-3 - Contact Page should pass WCAG 2.2 AA @accessibility @a11y @wcag @guest @smoke (5.1s)
✅ Contact Page: No accessibility violations found
✅ Contact Page: 27 accessibility checks passed
  ✓  199 [chromium] › tests\e2e\accessibility.comprehensive.refactored.spec.ts:108:9 › 01 - Automated Accessibility Testing - Guest Pages › 01-4 - Services Page should pass WCAG 2.2 AA @accessibility @a11y @wcag @guest @smoke (7.7s)
  ✘  195 [chromium] › tests\e2e\staff-flow-optimized.spec.ts:23:3 › Staff User Complete Flow › Staff journey: Welcome to Logout with all features (34.0s)
  ✘  200 …mium] › tests\e2e\accessibility.comprehensive.refactored.spec.ts:108:9 › 01 - Automated Accessibility Testing - Guest Pages › 01-5 - Helpdesk Ticket Form (Guest) should pass WCAG 2.2 AA @accessibility @a11y @wcag @guest @smoke (12.1s)
✅ Services Page: No accessibility violations found
✅ Services Page: 24 accessibility checks passed
  ✓  201 … tests\e2e\accessibility.comprehensive.refactored.spec.ts:108:9 › 01 - Automated Accessibility Testing - Guest Pages › 01-6 - Asset Loan Application Form (Guest) should pass WCAG 2.2 AA @accessibility @a11y @wcag @guest @smoke (11.1s)
  ✘  202 …sts\e2e\accessibility.comprehensive.refactored.spec.ts:145:9 › 02 - Automated Accessibility Testing - Authenticated Pages › 02-1 - Staff Dashboard should pass WCAG 2.2 AA @accessibility @a11y @wcag @staff @authenticated @smoke (26.4s)
✅ Asset Loan Application Form (Guest): No accessibility violations found
✅ Asset Loan Application Form (Guest): 17 accessibility checks passed
  ✘  203 … tests\e2e\accessibility.comprehensive.refactored.spec.ts:145:9 › 02 - Automated Accessibility Testing - Authenticated Pages › 02-2 - User Profile should pass WCAG 2.2 AA @accessibility @a11y @wcag @staff @authenticated @smoke (23.2s)  ✓  204 …\e2e\accessibility.comprehensive.refactored.spec.ts:145:9 › 02 - Automated Accessibility Testing - Authenticated Pages › 02-3 - Submission History should pass WCAG 2.2 AA @accessibility @a11y @wcag @staff @authenticated @smoke (18.1s)
  ✓  205 …s\e2e\accessibility.comprehensive.refactored.spec.ts:145:9 › 02 - Automated Accessibility Testing - Authenticated Pages › 02-4 - Claim Submissions should pass WCAG 2.2 AA @accessibility @a11y @wcag @staff @authenticated @smoke (29.6s)
✅ Submission History: No accessibility violations found
✅ Submission History: 18 accessibility checks passed
  ✓  206 …e\accessibility.comprehensive.refactored.spec.ts:183:9 › 03 - Automated Accessibility Testing - Approver Pages › 03-1 - Approval Interface (Grade 41+) should pass WCAG 2.2 AA @accessibility @a11y @wcag @approver @authenticated (20.8s)
✅ Claim Submissions: No accessibility violations found
✅ Claim Submissions: 27 accessibility checks passed
✅ Approval Interface (Grade 41+): No accessibility violations found
✅ Approval Interface (Grade 41+): 17 accessibility checks passed
  ✓  207 [chromium] › tests\e2e\accessibility.comprehensive.refactored.spec.ts:227:9 › 04 - Automated Accessibility Testing - Admin Pages › 04-1 - Admin Dashboard should pass WCAG 2.2 AA @accessibility @a11y @wcag @admin @authenticated (26.3s)
  ✓  208 … tests\e2e\accessibility.comprehensive.refactored.spec.ts:227:9 › 04 - Automated Accessibility Testing - Admin Pages › 04-2 - Helpdesk Tickets Management should pass WCAG 2.2 AA @accessibility @a11y @wcag @admin @authenticated (26.3s)
✅ Admin Dashboard: No accessibility violations found
✅ Admin Dashboard: 17 accessibility checks passed
✅ Helpdesk Tickets Management: No accessibility violations found
✅ Helpdesk Tickets Management: 19 accessibility checks passed
  ✓  209 …tests\e2e\accessibility.comprehensive.refactored.spec.ts:227:9 › 04 - Automated Accessibility Testing - Admin Pages › 04-3 - Loan Applications Management should pass WCAG 2.2 AA @accessibility @a11y @wcag @admin @authenticated (23.8s)
  ✓  210 …hromium] › tests\e2e\accessibility.comprehensive.refactored.spec.ts:227:9 › 04 - Automated Accessibility Testing - Admin Pages › 04-4 - Assets Management should pass WCAG 2.2 AA @accessibility @a11y @wcag @admin @authenticated (23.9s)
✅ Assets Management: No accessibility violations found
✅ Assets Management: 17 accessibility checks passed
✅ Loan Applications Management: No accessibility violations found
✅ Loan Applications Management: 19 accessibility checks passed
  ✓  211 …s\e2e\accessibility.comprehensive.refactored.spec.ts:270:5 › 05 - Automated Accessibility Testing - Mobile Viewport › 05-01 - Welcome Page should pass WCAG 2.2 AA on mobile @accessibility @a11y @wcag @mobile @responsive @smoke (10.4s)
  ✓  212 …› tests\e2e\accessibility.comprehensive.refactored.spec.ts:282:5 › 05 - Automated Accessibility Testing - Mobile Viewport › 05-02 - Helpdesk Form should pass WCAG 2.2 AA on mobile @accessibility @a11y @wcag @mobile @responsive (11.4s)
✅ Welcome Page (Mobile): No accessibility violations found
  ✓  213 …\e2e\accessibility.comprehensive.refactored.spec.ts:292:5 › 05 - Automated Accessibility Testing - Mobile Viewport › 05-03 - Loan Application Form should pass WCAG 2.2 AA on mobile @accessibility @a11y @wcag @mobile @responsive (4.0s)
✅ Helpdesk Form (Mobile): No accessibility violations found
  ✓  214 …ts\e2e\accessibility.comprehensive.refactored.spec.ts:306:5 › 06 - Automated Accessibility Testing - Specific WCAG 2.2 Criteria › 06-01 - Focus indicators should be visible (SC 2.4.7) @accessibility @a11y @wcag @criteria @smoke (3.7s)
✅ Loan Application Form (Mobile): No accessibility violations found

- 215 …m] › tests\e2e\accessibility.comprehensive.refactored.spec.ts:342:10 › 06 - Automated Accessibility Testing - Specific WCAG 2.2 Criteria › 06-02 - Touch targets should be minimum 44x44px (SC 2.5.8) @accessibility @a11y @wcag @criteria  ✓  216 …cessibility.comprehensive.refactored.spec.ts:368:5 › 06 - Automated Accessibility Testing - Specific WCAG 2.2 Criteria › 06-03 - Color contrast should be sufficient (SC 1.4.3, 1.4.11) @accessibility @a11y @wcag @criteria @smoke (4.4s)
  ✓  217 …mium] › tests\e2e\dashboard-accessibility.refactored.spec.ts:36:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 01 - Keyboard navigation through dashboard elements @accessibility @a11y @dashboard @wcag @smoke @keyboard (14.8s)
  ✓  218 [chromium] › tests\e2e\dashboard-accessibility.refactored.spec.ts:90:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 02 - Color contrast meets WCAG AA standards @accessibility @a11y @dashboard @wcag @smoke @contrast (20.0s)
  ✓  219 [chromium] › tests\e2e\dashboard-accessibility.refactored.spec.ts:148:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 03 - Touch targets meet minimum size requirements @accessibility @a11y @dashboard @wcag @smoke @touch (13.8s)
✅ Found 3 colored icons with WCAG-compliant palette
  ✓  220 [chromium] › tests\e2e\dashboard-accessibility.refactored.spec.ts:216:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 04 - ARIA attributes and semantic HTML @accessibility @a11y @dashboard @wcag @smoke @aria (20.3s)
  ✓  221 [chromium] › tests\e2e\dashboard-accessibility.refactored.spec.ts:262:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 05 - Screen reader compatibility @accessibility @a11y @dashboard @wcag @smoke @screen-reader (14.0s)
✅ Found 5 lists with explicit role="list"
  ✓  222 [chromium] › tests\e2e\dashboard-accessibility.refactored.spec.ts:302:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 06 - Focus management @accessibility @a11y @dashboard @wcag @focus (20.7s)
  ✘  223 [chromium] › tests\e2e\dashboard-accessibility.refactored.spec.ts:341:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 07 - Responsive accessibility across viewports @accessibility @a11y @dashboard @wcag @responsive (30.2s)

📱 Testing mobile viewport (375×667)
✅ mobile viewport accessibility checks complete

📱 Testing tablet viewport (768×1024)
  ✘  224 [chromium] › tests\e2e\filament.components.debug.spec.ts:107:3 › Filament Components Debug Suite › Dashboard widgets render without console errors @filament @debug (10.0s)
✅ tablet viewport accessibility checks complete

📱 Testing desktop viewport (1280×720)
  ✘  225 [chromium] › tests\e2e\filament.components.debug.spec.ts:125:5 › Filament Components Debug Suite › Helpdesk Tickets resource loads without failures @filament @debug (3.4s)
  ✘  226 [chromium] › tests\e2e\filament.components.debug.spec.ts:125:5 › Filament Components Debug Suite › Loan Applications resource loads without failures @filament @debug (4.4s)
  ✘  227 [chromium] › tests\e2e\filament.components.debug.spec.ts:125:5 › Filament Components Debug Suite › Asset Inventory resource loads without failures @filament @debug (3.8s)
  ✘  228 [chromium] › tests\e2e\filament.components.debug.spec.ts:137:3 › Filament Components Debug Suite › Asset availability legend exposes all statuses @filament @debug (3.6s)
  ✘  229 [chromium] › tests\e2e\filament.components.debug.spec.ts:166:3 › Filament Components Debug Suite › Critical alerts widget surfaces empty state or alert actions @filament @debug (12.1s)
  ✓  230 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:23:3 › Helpdesk Ticket Module - Best Practices Architecture › 01 - Helpdesk Module Navigation @smoke @helpdesk @module @navigation (28.0s)
  ✘  231 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:38:3 › Helpdesk Ticket Module - Best Practices Architecture › 02 - Helpdesk Ticket List View @smoke @helpdesk @module (31.2s)
  ✘  232 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:59:3 › Helpdesk Ticket Module - Best Practices Architecture › 03 - Create New Ticket - Form Accessibility @helpdesk @module @form (35.0s)
  ✘  233 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:87:3 › Helpdesk Ticket Module - Best Practices Architecture › 04 - Create New Ticket - Form Validation @helpdesk @module @form @validation (43.4s)
  ✘  234 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:105:3 › Helpdesk Ticket Module - Best Practices Architecture › 05 - Create New Ticket - Successful Submission @smoke @helpdesk @module @form (46.2s)
  ✘  233 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:87:3 › Helpdesk Ticket Module - Best Practices Architecture › 04 - Create New Ticket - Form Validation @helpdesk @module @form @validation (43.4s)
  ✘  234 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:105:3 › Helpdesk Ticket Module - Best Practices Architecture › 05 - Create New Ticket - Successful Submission @smoke @helpdesk @module @form (46.2s)
  ✓  235 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:135:3 › Helpdesk Ticket Module - Best Practices Architecture › 06 - Ticket Filtering and Search @helpdesk @module @filter (18.3s)
  ✘  236 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:160:3 › Helpdesk Ticket Module - Best Practices Architecture › 07 - View Ticket Details @helpdesk @module @detail (17.5s)
  ✘  237 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:187:3 › Helpdesk Ticket Module - Best Practices Architecture › 08 - Ticket Status Update @helpdesk @module @status (45.2s)
  ✘  233 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:87:3 › Helpdesk Ticket Module - Best Practices Architecture › 04 - Create New Ticket - Form Validation @helpdesk @module @form @validation (43.4s)
  ✘  234 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:105:3 › Helpdesk Ticket Module - Best Practices Architecture › 05 - Create New Ticket - Successful Submission @smoke @helpdesk @module @form (46.2s)
  ✓  235 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:135:3 › Helpdesk Ticket Module - Best Practices Architecture › 06 - Ticket Filtering and Search @helpdesk @module @filter (18.3s)
  ✘  236 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:160:3 › Helpdesk Ticket Module - Best Practices Architecture › 07 - View Ticket Details @helpdesk @module @detail (17.5s)
  ✘  237 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:187:3 › Helpdesk Ticket Module - Best Practices Architecture › 08 - Ticket Status Update @helpdesk @module @status (45.2s)
  ✘  238 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:219:3 › Helpdesk Ticket Module - Best Practices Architecture › 09 - Module Navigation - Return to Dashboard @smoke @helpdesk @module @navigation (15.7s)
  ✘  239 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:239:3 › Helpdesk Ticket Module - Best Practices Architecture › 10 - Module Console Error Check @helpdesk @module @debugging (13.3s)
Console errors detected: [
  'Failed to send logs: TypeError: Failed to fetch\n' +
    '    at flushLogs (http://localhost:8000/login:57:9)'
]
  ✘  240 [chromium] › tests\e2e\loan.refactored.spec.ts:23:3 › Loan Module - Best Practices Architecture › 01 - Loan Module Navigation @smoke @loan @module @navigation (18.3s)
     241 [chromium] › tests\e2e\loan.refactored.spec.ts:38:3 › Loan Module - Best Practices Architecture › 02 - Loan Application List View @smoke @loan @module
  ✘  241 [chromium] › tests\e2e\loan.refactored.spec.ts:38:3 › Loan Module - Best Practices Architecture › 02 - Loan Application List View @smoke @loan @module (31.7s)@module @form

  ✘  243 [chromium] › tests\e2e\loan.refactored.spec.ts:91:3 › Loan Module - Best Practices Architecture › 04 - Create New Loan Application - Form Validation @loan @module @form @validation (42.4s)

   245 [chromium] › tests\e2e\loan.refactored.spec.ts:147:3 › Loan Module - Best Practices Architecture › 06 - Loan Application Filtering a  ✓  245 [chromium] › tests\e2e\loan.refactored.spec.ts:147:3 › Loan Module - Best Practices Architecture › 06 - Loan Application Filtering a  ✘  244 [chromium] › tests\e2e\loan.refactored.spec.ts:109:3 › Loan Module - Best Practices Architecture › 05 - Create New Loan Application - Successful Submission @smoke @loan @module @form (42.0s)2:3 › Loan Module - Best Practices Architecture › 07 - View Loan Application Details @loan @module @detail

  ✓  246 [chromium] › tests\e2e\loan.refactored.spec.ts:172:3 › Loan Module - Best Practices Architecture › 07 - View Loan Application Details @loan @module @detail (13.4s)
  ✓  247 [chromium] › tests\e2e\loan.refactored.spec.ts:199:3 › Loan Module - Best Practices Architecture › 08 - Loan Status Filter @loan @module @filter (15.5s)@approval
  ✓  248 [chromium] › tests\e2e\loan.refactored.spec.ts:225:3 › Loan Module - Best Practices Architecture › 09 - Loan Approval Workflow (if admin) @loan @module @approval (15.2s)igation
  ✓  249 [chromium] › tests\e2e\loan.refactored.spec.ts:251:3 › Loan Module - Best Practices Architecture › 10 - Module Navigation - Return to Dashboard @smoke @loan @module @navigation (21.0s)
  ✘  250 [chromium] › tests\e2e\loan.refactored.spec.ts:271:3 › Loan Module - Best Practices Architecture › 11 - Module Console Error Check @loan @module @debugging (18.0s)ut on 320px (iPhone SE) @responsive @mobile @layout @smoke
Console errors detected: [
  'Failed to send logs: TypeError: Failed to fetch\n' +
    '    at flushLogs (http://localhost:8000/login:57:9)',
  'Failed to load resource: the server responded with a status of 500 (Internal Server Error)'
]
     252 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:53:9 › 01 - Staff Dashboard Responsive Behavior - Mobile Viewports › 01-2 - Single column layout on 375px (iPhone 8) @responsive @mobile @layout @smoke
  ✘  252 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:53:9 › 01 - Staff Dashboard Responsive Behavior - Mobile Viewports › 01-2 - Single column layout on 375px (iPhone 8) @responsive @mobile @layout @smoke (15.1s)e
  ✘  253 …romium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:53:9 › 01 - Staff Dashboard Responsive Behavior - Mobile Viewports › 01-3 - Single column layout on 414px (iPhone 11 Pro Max) @responsive @mobile @layout @smoke (13.7s)
  ✓  254 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:96:5 › 01 - Staff Dashboard Responsive Behavior - Mobile Viewports › 01-04 - Quick action buttons stack vertically on mobile @responsive @mobile @layout (12.7s)
     256 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:144:9 › 02 - Staff Dashboard Responsive Behavior - Tablet Viewp  ✓  256 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:144:9 › 02 - Staff Dashboard Responsive Behavior - Tablet Viewp     257 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:144:9 › 02 - Staff Dashboard Responsive Behavior - Tablet Viewp  ✘  255 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:118:5 › 01 - Staff Dashboard Responsive Behavior - Mobile Viewports › 01-05 - Recent activity displays in single column on mobile @responsive @mobile @layout (45.5s)oard Responsive Behavior - Tablet Viewp  ✓  258 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:144:9 › 02 - Staff Dashboard Responsive Behavior - Tablet Viewp     259 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:184:5 › 02 - Staff Dashboard Responsive Behavior - Tablet Viewports › 02-04 - Recent activity displays in 2 columns on tablet @responsive @tablet @layout
     260 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:205:9 › 03 - Staff Dashboard Responsive Behavior - Desktop View  ✓  260 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:205:9 › 03 - Staff Dashboard Responsive Behavior - Desktop View  ✘  259 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:184:5 › 02 - Staff Dashboard Responsive Behavior - Tablet Viewports › 02-04 - Recent activity displays in 2 columns on tablet @responsive @tablet @layout (39.5s)shboard Responsive Behavior - Desktop Viewp     262 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:205:9 › 03 - Staff Dashboard Responsive Behavior - Desktop Viewports › 03-3 - Four column layout on 2560px (Desktop 4K) @responsive @desktop @layout @smoke
  ✓  262 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:205:9 › 03 - Staff Dashboard Responsive Behavior - Desktop Viewports › 03-3 - Four column layout on 2560px (Desktop 4K) @responsive @desktop @layout @smoke (10.7s)
  ✓  264 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:273:5 › 03 - Staff Dashboard Responsive Behavior - Desktop Viewports › 03-05 - Quick actions display in single row on desktop @responsive @desktop @layout (12.5s)
  ✓  265 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:298:5 › 04 - Touch Target Compliance (WCAG 2.2 AA) › 04-01 - Minimum 44x44px touch targets on mobile @accessibility @wcag @touch @smoke (12.8s)
  ✓  266 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:330:5 › 05 - Performance and Loading › 05-01 - Quick load on desktop viewport @performance (15.3s)
     268 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:356:5 › 06 - No Horizontal Scroll (WCAG 2.2 AA) › 06-01 - No horizontal scroll on all viewports @accessibility @wcag @layout @smoke

📐 Testing iPhone SE (320×568)

📐 Testing iPhone 8 (375×667)

📐 Testing iPhone 11 Pro Max (414×896)

📐 Testing iPad Mini (768×1024)

📐 Testing iPad Air (820×1180)

📐 Testing iPad Pro (1000×1366)

📐 Testing Desktop HD (1280×720)

📐 Testing Desktop Full HD (1920×1080)

📐 Testing Desktop 4K (2560×1440)
  ✓  267 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:341:5 › 05 - Performance and Loading › 05-02 - Quick load on mobile viewport @performance (14.9s)
     269 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:388:5 › 07 - Content Readability › 07-01 - Readable text on mobile viewport @accessibility @readability
  ✓  268 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:356:5 › 06 - No Horizontal Scroll (WCAG 2.2 AA) › 06-01 - No ho  ✓  269 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:388:5 › 07 - Content Readability › 07-01 - Readable text on mobile viewport @accessibility @readability (11.5s)responsive.refactored.spec.ts:408:5 › 08 - Responsive Image and Icon Handling › 08-01 - Icons display properly on all viewports @responsive @icons
     271 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:431:5 › 09 - Viewport Transition Smoothness › 09-01 - Graceful   ✘  270 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:408:5 › 08 - Responsive Image and Icon Handling › 08-01 - Icons display properly on all viewports @responsive @icons (24.7s)factored.spec.ts:455:5 › 10 - Accessibility on Different Viewports › 10-01 - Focus indicators on all viewports @accessibility @focus
  ✓  272 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:455:5 › 10 - Accessibility on Different Viewports › 10-01 - Focus indicators on all viewports @accessibility @focus (11.9s) @optimization @e2e
     274 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:26:3 › Staff User Complete Flow - Best Practices Architecture › 01 - Welcome Pa  ✓  274 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:26:3 › Staff User Complete Flow - Best Practices Architecture › 01 - Welcome Page Accessibility Check @smoke @staff @flow (3.4s)
  ✘  273 [chromium] › tests\e2e\staff-flow-optimized.refactored.spec.ts:28:3 › Staff User Optimized Complete Journey › Complete staff journey: Welcome to Logout (optimized single session) @smoke @staff @optimization @e2e (21.3s)
📸 Step 1/15: Welcome page
     275 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:43:3 › Staff User Complete Flow - Best Practices Architecture › 02 - Navigate t  ✓  275 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:43:3 › Staff User Complete Flow - Best Practices Architecture › 02 - Navigate to Staff Login @smoke @staff @authentication (6.0s)

📸 Step 2/15: Navigate to login
     276 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:63:3 › Staff User Complete Flow - Best Practices Architecture › 03 - Login Page  ✓  276 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:63:3 › Staff User Complete Flow - Best Practices Architecture › 03 - Login Page     277 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:79:3 › Staff User Complete Flow - Best Practices Architecture › 04 - Successful Authentication @smoke @staff @authentication
  ✓  277 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:79:3 › Staff User Complete Flow - Best Practices Architecture › 04 - Successful Authentication @smoke @staff @authentication (10.4s)
     279 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:113:3 › Staff User Complete Flow - Best Practices Architecture › 06 - Dashboard  ✘  278 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:94:3 › Staff User Complete Flow - Best Practices Architecture › 05 - Dashboard Main View After Login @smoke @staff @dashboard (28.4s)spec.ts:127:3 › Staff User Complete Flow - Best Practices Architecture › 07 - Navigate to Helpdesk Module @staff @helpdesk @navigation
  ✓  280 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:127:3 › Staff User Complete Flow - Best Practices Architecture › 07 - Navigate to Helpdesk Module @staff @helpdesk @navigation (15.6s)
  ✓  281 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:142:3 › Staff User Complete Flow - Best Practices Architecture › 08 - Navigate to Loan Module @staff @loan @navigation (16.8s)
  ✓  282 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:157:3 › Staff User Complete Flow - Best Practices Architecture › 09 - View User Profile @staff @profile (16.1s)tion
     284 [chromium] › tests\e2e\staff-flow.best-practices.spec.ts:32:3 › Staff User Complete Flow - Best Practices › 01 - Welcome page is acc  ✓  283 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:174:3 › Staff User Complete Flow - Best Practices Architecture › 10 - Complete Logout @smoke @staff @authentication (22.0s)est-practices.spec.ts:46:3 › Staff User Complete Flow - Best Practices › 02 - Can navigate to staff login
  ✓  285 [chromium] › tests\e2e\staff-flow.best-practices.spec.ts:46:3 › Staff User Complete Flow - Best Practices › 02 - Can navigate to staff login (7.0s)ials
  ✓  286 [chromium] › tests\e2e\staff-flow.best-practices.spec.ts:65:3 › Staff User Complete Flow - Best Practices › 03 - Can login with valid staff credentials (5.8s)
  ✘  287 [chromium] › tests\e2e\staff-flow.best-practices.spec.ts:78:3 › Staff User Complete Flow - Best Practices › 04 - Dashboard displays main components (18.0s)
  ✓  288 [chromium] › tests\e2e\staff-flow.best-practices.spec.ts:93:3 › Staff User Complete Flow - Best Practices › 05 - Can navigate to Helpdesk module (15.8s)
  ✘  289 [chromium] › tests\e2e\staff-flow.best-practices.spec.ts:107:3 › Staff User Complete Flow - Best Practices › 06 - Can navigate to Asset Loan module (16.3s)
  ✘  290 [chromium] › tests\e2e\staff-flow.best-practices.spec.ts:119:3 › Staff User Complete Flow - Best Practices › 07 - Can return to dashboard from modules (18.0s)
  ✓  291 [chromium] › tests\e2e\staff-flow.best-practices.spec.ts:136:3 › Staff User Complete Flow - Best Practices › 08 - Can navigate to profile (19.0s)
  ✘  292 [chromium] › tests\e2e\staff-flow.best-practices.spec.ts:151:3 › Staff User Complete Flow - Best Practices › 09 - Can logout successfully (45.2s)
