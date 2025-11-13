PS C:\XAMPP\htdocs\ictserve-031125> npx playwright test tests/e2e --reporter=list

Running 293 tests using 2 workers

  ✘    1 [chromium] › tests\e2e\accessibility-compliance.spec.ts:44:3 › Portal Accessibility Compliance (WCAG 2.2 AA) › full accessibility scan with axe-core (11.7s)
  ✓    2 [chromium] › tests\e2e\accessibility-compliance.spec.ts:10:3 › Portal Accessibility Compliance (WCAG 2.2 AA) › keyboard navigation - all interactive elements accessible (8.5s)
  ✓    3 [chromium] › tests\e2e\accessibility-compliance.spec.ts:52:3 › Portal Accessibility Compliance (WCAG 2.2 AA) › focus indicators visible on all interactive elements (11.8s)
  ✓    4 [chromium] › tests\e2e\accessibility-compliance.spec.ts:76:3 › Portal Accessibility Compliance (WCAG 2.2 AA) › skip navigation link present and functional (11.0s)
  ✓    5 [chromium] › tests\e2e\accessibility-compliance.spec.ts:89:3 › Portal Accessibility Compliance (WCAG 2.2 AA) › color contrast meets WCAG AA standards (10.8s)
       6 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Welcome Page
       7 …] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Accessibility Statement
❌ Welcome Page: 1 violation(s) found

1. target-size (serious)
   Description: Ensure touch targets have sufficient size and space
   Help: All touch targets must be 24px large, or leave sufficient space
   Help URL: <https://dequeuniversity.com/rules/axe/4.11/target-size?application=playwright>
   Affected elements: 14
   - Element 1: <a id="skip-to-content" href="#main-content" class="block px-4 py-2 bg-white text-motac-blue rounded...
     Target: #skip-to-content
   - Element 2: <a href="#sidebar-navigation" class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gr...
     Target: a[href$="#sidebar-navigation"]
   - Element 3: <a href="#user-menu" class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gray-100 fo...
     Target: a[href$="#user-menu"]

  ✘    6 …ium] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Welcome Page (16.0s)       8 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Contact Page

❌ Accessibility Statement: 1 violation(s) found

1. target-size (serious)
   Description: Ensure touch targets have sufficient size and space
   Help: All touch targets must be 24px large, or leave sufficient space
   Help URL: <https://dequeuniversity.com/rules/axe/4.11/target-size?application=playwright>
   Affected elements: 14
   - Element 1: <a id="skip-to-content" href="#main-content" class="block px-4 py-2 bg-white text-motac-blue rounded...
     Target: #skip-to-content
   - Element 2: <a href="#sidebar-navigation" class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gr...
     Target: a[href$="#sidebar-navigation"]
   - Element 3: <a href="#user-menu" class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gray-100 fo...
     Target: a[href$="#user-menu"]

  ✘    7 …s\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Accessibility Statement (14.3s)       9 [chromium] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Services Page

❌ Contact Page: 1 violation(s) found

1. target-size (serious)
   Description: Ensure touch targets have sufficient size and space
   Help: All touch targets must be 24px large, or leave sufficient space
   Help URL: <https://dequeuniversity.com/rules/axe/4.11/target-size?application=playwright>
   Affected elements: 17
   - Element 1: <a id="skip-to-content" href="#main-content" class="block px-4 py-2 bg-white text-motac-blue rounded...
     Target: #skip-to-content
   - Element 2: <a href="#sidebar-navigation" class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gr...
     Target: a[href$="#sidebar-navigation"]
   - Element 3: <a href="#user-menu" class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gray-100 fo...
     Target: a[href$="#user-menu"]

  ✘    8 …ium] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Contact Page (15.4s)
❌ Services Page: 1 violation(s) found

1. target-size (serious)
   Description: Ensure touch targets have sufficient size and space
   Help: All touch targets must be 24px large, or leave sufficient space
   Help URL: <https://dequeuniversity.com/rules/axe/4.11/target-size?application=playwright>
   Affected elements: 14
   - Element 1: <a id="skip-to-content" href="#main-content" class="block px-4 py-2 bg-white text-motac-blue rounded...
     Target: #skip-to-content
   - Element 2: <a href="#sidebar-navigation" class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gr...
     Target: a[href$="#sidebar-navigation"]
   - Element 3: <a href="#user-menu" class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gray-100 fo...
     Target: a[href$="#user-menu"]

  ✘    9 …um] › tests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Services Page (14.5s)      10 …ests\e2e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Helpdesk Ticket Form (Guest)      11 …e\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Asset Loan Application Form (Guest)
❌ Helpdesk Ticket Form (Guest): 1 violation(s) found

1. target-size (serious)
   Description: Ensure touch targets have sufficient size and space
   Help: All touch targets must be 24px large, or leave sufficient space
   Help URL: <https://dequeuniversity.com/rules/axe/4.11/target-size?application=playwright>
   Affected elements: 12
   - Element 1: <a class="inline-flex items-ce..." href="http://localhost:800..." wire:navigate="">...
     Target: .px-1.pt-1.border-b-2:nth-child(1)
   - Element 2: <a class="inline-flex items-ce..." href="http://localhost:800..." wire:navigate="">...
     Target: .px-1.pt-1.border-b-2:nth-child(2)
   - Element 3: <button @click="open = ! open" aria-label="Toggle menu" aria-expanded="false" x-bind:aria-expanded="...
     Target: .p-2

  ✘   10 …\accessibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Helpdesk Ticket Form (Guest) (15.9s)✅ Asset Loan Application Form (Guest): No accessibility violations found
✅ Asset Loan Application Form (Guest): 17 accessibility checks passed
  ✓   11 …ibility.comprehensive.spec.ts:95:9 › Task 10.1: Automated Accessibility Testing - Guest Pages › should pass WCAG 2.2 AA compliance: Asset Loan Application Form (Guest) (16.7s)  -   12 …um] › tests\e2e\accessibility.comprehensive.spec.ts:141:9 › Task 10.1: Automated Accessibility Testing - Authenticated Pages › should pass WCAG 2.2 AA compliance: User Profile  -   13 … › tests\e2e\accessibility.comprehensive.spec.ts:141:9 › Task 10.1: Automated Accessibility Testing - Authenticated Pages › should pass WCAG 2.2 AA compliance: Staff DashboardStaff login failed or redirected to unexpected page, skipping authenticated tests

- 14 …tests\e2e\accessibility.comprehensive.spec.ts:141:9 › Task 10.1: Automated Accessibility Testing - Authenticated Pages › should pass WCAG 2.2 AA compliance: Submission HistoryStaff login failed or redirected to unexpected page, skipping authenticated tests
- 15 … tests\e2e\accessibility.comprehensive.spec.ts:141:9 › Task 10.1: Automated Accessibility Testing - Authenticated Pages › should pass WCAG 2.2 AA compliance: Claim SubmissionsStaff login failed or redirected to unexpected page, skipping authenticated tests
      16 …2e\accessibility.comprehensive.spec.ts:180:9 › Task 10.1: Automated Accessibility Testing - Approver Pages › should pass WCAG 2.2 AA compliance: Approval Interface (Grade 41+)Staff login failed or redirected to unexpected page, skipping authenticated tests
- 17 …hromium] › tests\e2e\accessibility.comprehensive.spec.ts:226:9 › Task 10.1: Automated Accessibility Testing - Admin Pages › should pass WCAG 2.2 AA compliance: Admin DashboardAdmin login failed, skipping admin tests
      18 …ests\e2e\accessibility.comprehensive.spec.ts:226:9 › Task 10.1: Automated Accessibility Testing - Admin Pages › should pass WCAG 2.2 AA compliance: Helpdesk Tickets Management
❌ Approval Interface (Grade 41+): 1 violation(s) found

1. target-size (serious)
   Description: Ensure touch targets have sufficient size and space
   Help: All touch targets must be 24px large, or leave sufficient space
   Help URL: <https://dequeuniversity.com/rules/axe/4.11/target-size?application=playwright>
   Affected elements: 9
   - Element 1: <a id="skip-to-content" href="#main-content" class="block px-4 py-2 bg-white text-motac-blue rounded...
     Target: #skip-to-content
   - Element 2: <a href="#sidebar-navigation" class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gr...
     Target: a[href$="#sidebar-navigation"]
   - Element 3: <a href="#user-menu" class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gray-100 fo...
     Target: a[href$="#user-menu"]

  ✘   16 …sibility.comprehensive.spec.ts:180:9 › Task 10.1: Automated Accessibility Testing - Approver Pages › should pass WCAG 2.2 AA compliance: Approval Interface (Grade 41+) (34.5s)  -   19 …sts\e2e\accessibility.comprehensive.spec.ts:226:9 › Task 10.1: Automated Accessibility Testing - Admin Pages › should pass WCAG 2.2 AA compliance: Loan Applications ManagementAdmin login failed, skipping admin tests
      20 …omium] › tests\e2e\accessibility.comprehensive.spec.ts:226:9 › Task 10.1: Automated Accessibility Testing - Admin Pages › should pass WCAG 2.2 AA compliance: Assets Management
❌ Helpdesk Tickets Management: 1 violation(s) found

1. target-size (serious)
   Description: Ensure touch targets have sufficient size and space
   Help: All touch targets must be 24px large, or leave sufficient space
   Help URL: <https://dequeuniversity.com/rules/axe/4.11/target-size?application=playwright>
   Affected elements: 2
   - Element 1: <a href="<http://localhost:8000/profile>" class="text-sm text-primary-600 hover:text-primary-700 focus...
     Target: a[href$="profile"]
   - Element 2: <a href="<http://localhost:8000/contact>" class="text-sm text-primary-600 hover:text-primary-700 focus...
     Target: .text-primary-600.hover\:text-primary-700[href$="contact"]

  ✘   18 …\accessibility.comprehensive.spec.ts:226:9 › Task 10.1: Automated Accessibility Testing - Admin Pages › should pass WCAG 2.2 AA compliance: Helpdesk Tickets Management (46.2s)      21 …tests\e2e\accessibility.comprehensive.spec.ts:256:5 › Task 10.1: Automated Accessibility Testing - Mobile Viewport › should pass WCAG 2.2 AA compliance on mobile: Welcome Page
❌ Welcome Page (Mobile): 1 violation(s) found

1. target-size (serious)
   Description: Ensure touch targets have sufficient size and space
   Help: All touch targets must be 24px large, or leave sufficient space
   Help URL: <https://dequeuniversity.com/rules/axe/4.11/target-size?application=playwright>
   Affected elements: 14
   - Element 1: <a id="skip-to-content" href="#main-content" class="block px-4 py-2 bg-white text-motac-blue rounded...
     Target: #skip-to-content
   - Element 2: <a href="#sidebar-navigation" class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gr...
     Target: a[href$="#sidebar-navigation"]
   - Element 3: <a href="#user-menu" class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gray-100 fo...
     Target: a[href$="#user-menu"]

  ✘   21 …e\accessibility.comprehensive.spec.ts:256:5 › Task 10.1: Automated Accessibility Testing - Mobile Viewport › should pass WCAG 2.2 AA compliance on mobile: Welcome Page (13.9s)  ✓   22 …\accessibility.comprehensive.spec.ts:266:5 › Task 10.1: Automated Accessibility Testing - Mobile Viewport › should pass WCAG 2.2 AA compliance on mobile: Helpdesk Form (45.4s)✅ Helpdesk Form (Mobile): No accessibility violations found
      23 …\accessibility.comprehensive.spec.ts:276:5 › Task 10.1: Automated Accessibility Testing - Mobile Viewport › should pass WCAG 2.2 AA compliance on mobile: Loan Application Form✅ Loan Application Form (Mobile): No accessibility violations found

❌ Assets Management: 3 violation(s) found

1. color-contrast (serious)
   Description: Ensure the contrast between foreground and background colors meets WCAG 2 AA minimum contrast ratio thresholds
   Help: Elements must meet minimum color contrast ratio thresholds
   Help URL: <https://dequeuniversity.com/rules/axe/4.11/color-contrast?application=playwright>
   Affected elements: 1
   - Element 1: <span x-show="$store.sidebar.isOpen" x-transition:enter="fi-transition-enter" x-transition:enter-sta...
     Target: .fi-active.fi-sidebar-item.fi-sidebar-item-has-url > .fi-sidebar-item-btn[x-data="{ tooltip: false }"][x-tooltip\.html="tooltip"] > .fi-sidebar-item-label[x-show="$store.sidebar.isOpen"]

2. link-name (serious)
   Description: Ensure links have discernible text
   Help: Links must have discernible text
   Help URL: <https://dequeuniversity.com/rules/axe/4.11/link-name?application=playwright>
   Affected elements: 11
   - Element 1: <a href="<http://localhost:8000/admin/assets/93>" x-on:click="if (! ($event.altKey || $event.ctrlKey |...
     Target: .fi-striped.fi-ta-row:nth-child(4) > .fi-ta-cell-next-maintenance-date > .fi-ta-col
   - Element 2: <a href="<http://localhost:8000/admin/assets/92>" x-on:click="if (! ($event.altKey || $event.ctrlKey |...
     Target: .fi-ta-row:nth-child(5) > .fi-ta-cell-next-maintenance-date > .fi-ta-col
   - Element 3: <a href="<http://localhost:8000/admin/assets/90>" x-on:click="if (! ($event.altKey || $event.ctrlKey |...
     Target: .fi-ta-row:nth-child(7) > .fi-ta-cell-next-maintenance-date > .fi-ta-col

3. nested-interactive (serious)
   Description: Ensure interactive controls are not nested as they are not always announced by screen readers or can cause focus problems for assistive technologies
   Help: Interactive controls must not be nested
   Help URL: <https://dequeuniversity.com/rules/axe/4.11/nested-interactive?application=playwright>
   Affected elements: 6
   - Element 1: <div x-on:click="$store.sidebar.toggleCollapsedGroup(label)" role="button" x-show="$store.sidebar.is...
     Target: li[x-data="{ label: 'Reference Data' }"] > .fi-sidebar-group-btn[role="button"][x-show="$store.sidebar.isOpen"]
   - Element 2: <div x-on:click="$store.sidebar.toggleCollapsedGroup(label)" role="button" x-show="$store.sidebar.is...
     Target: li[data-group-label="Reports & Analytics"] > .fi-sidebar-group-btn[role="button"][x-show="$store.sidebar.isOpen"]
   - Element 3: <div x-on:click="$store.sidebar.toggleCollapsedGroup(label)" role="button" x-show="$store.sidebar.is...
     Target: li[x-data="{ label: 'Reports' }"] > .fi-sidebar-group-btn[role="button"][x-show="$store.sidebar.isOpen"]

  ✓   23 …bility.comprehensive.spec.ts:276:5 › Task 10.1: Automated Accessibility Testing - Mobile Viewport › should pass WCAG 2.2 AA compliance on mobile: Loan Application Form (19.1s)  ✓   24 …2e\accessibility.comprehensive.spec.ts:288:5 › Task 10.1: Automated Accessibility Testing - Specific WCAG 2.2 Criteria › should have proper focus indicators (SC 2.4.7) (10.5s)  ✘   20 …› tests\e2e\accessibility.comprehensive.spec.ts:226:9 › Task 10.1: Automated Accessibility Testing - Admin Pages › should pass WCAG 2.2 AA compliance: Assets Management (1.6m)  ✓   25 …bility.comprehensive.spec.ts:321:5 › Task 10.1: Automated Accessibility Testing - Specific WCAG 2.2 Criteria › should have minimum touch target size 44x44px (SC 2.5.8) (13.9s)  ✓   26 …essibility.comprehensive.spec.ts:343:5 › Task 10.1: Automated Accessibility Testing - Specific WCAG 2.2 Criteria › should have proper color contrast (SC 1.4.3, 1.4.11) (14.6s)  ✘   27 [chromium] › tests\e2e\branding-smoke.spec.ts:4:3 › Branding smoke checks › header, notification icon, and email asset are available (13.5s)
  ✘   28 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:34:5 › Staff Dashboard Accessibility › keyboard navigation through dashboard elements (1.7m)
  ✘   29 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:81:5 › Staff Dashboard Accessibility › color contrast meets WCAG AA standards (1.8m)
  ✘   30 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:133:5 › Staff Dashboard Accessibility › touch targets meet minimum size requirements (1.7m)
  ✘   31 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:195:5 › Staff Dashboard Accessibility › ARIA attributes and semantic HTML (1.7m)
  ✘   32 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:237:5 › Staff Dashboard Accessibility › screen reader compatibility (1.7m)
  ✘   33 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:275:5 › Staff Dashboard Accessibility › focus management (1.7m)
      34 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:312:5 › Staff Dashboard Accessibility › responsive accessibility across viewports
  ✓   35 [chromium] › tests\e2e\devtools.integration.spec.ts:9:3 › Chrome DevTools Debugging Suite › should capture performance metrics (8.7s)
Performance Metrics: {
  domContentLoaded: 10.300000000745058,
  loadComplete: 0,
  totalTime: 3897.89999999851
}
      36 [chromium] › tests\e2e\devtools.integration.spec.ts:33:3 › Chrome DevTools Debugging Suite › should detect all network requests and responses
Total Requests: 20
Request Log: [
  {
    "url": "http://localhost:8000/",
    "method": "GET",
    "status": 200
  },
  {
    "url": "https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap",
    "method": "GET"
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
  ✓   36 [chromium] › tests\e2e\devtools.integration.spec.ts:33:3 › Chrome DevTools Debugging Suite › should detect all network requests and responses (7.6s)
  ✘   37 [chromium] › tests\e2e\devtools.integration.spec.ts:65:3 › Chrome DevTools Debugging Suite › should capture console messages and errors (7.7s)
Console Logs: {
  logs: [
    '🔍 Browser logger active (MCP server detected). Posting to: http://localhost:8000/_boost/browser-logs'
  ],
  warnings: [],
  errors: [
    'Failed to load resource: net::ERR_CERT_AUTHORITY_INVALID',
    'Failed to load resource: the server responded with a status of 404 (Not Found)'
  ]
}
  ✓   38 [chromium] › tests\e2e\devtools.integration.spec.ts:95:3 › Chrome DevTools Debugging Suite › should check accessibility tree (8.5s)
Accessibility Check:

- Main content landmark: true
- Navigation landmark: true
  ✓   39 [chromium] › tests\e2e\devtools.integration.spec.ts:115:3 › Chrome DevTools Debugging Suite › should validate page security headers (3.9s)
Security Headers: {
  'content-security-policy': undefined,
  'x-frame-options': undefined,
  'x-content-type-options': undefined,
  'x-xss-protection': undefined
}
  ✓   40 [chromium] › tests\e2e\devtools.integration.spec.ts:130:3 › Chrome DevTools Debugging Suite › should check for memory leaks in navigation (10.2s)
Memory Usage by Route: [
  { route: '/', memory: 10000000 },
  { route: '/login', memory: 10000000 }
]
  ✓   41 [chromium] › tests\e2e\devtools.integration.spec.ts:164:3 › Chrome DevTools Debugging Suite › should validate DOM and CSS rendering (10.4s)
DOM Statistics: {
  elementCount: 166,
  styleSheets: 4,
  images: 1,
  links: 27,
  buttons: 4,
  forms: 0
}
  ✘   42 [chromium] › tests\e2e\devtools.integration.spec.ts:185:3 › Chrome DevTools Debugging Suite › should check for unhandled promise rejections (8.4s)
Page Errors: [ 'You must pass your app key when you instantiate Pusher.' ]
  ✓   43 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:30:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 01 - Welcome Page - Initial Load (9.0s)
✓ Screenshot saved: public\images\screenshots\01_welcome_page_home_guest.png
  ✓   44 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:46:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 02 - Welcome Page - Navigate to Helpdesk (19.0s)
  ✘   34 [chromium] › tests\e2e\dashboard-accessibility.spec.ts:312:5 › Staff Dashboard Accessibility › responsive accessibility across viewports (1.7m)
  ✓   45 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:246:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 07 - Navigate to Loan Application Form (30.0s)
✓ Screenshot saved: public\images\screenshots\02_welcome_page_navigation_guest.png
  ✓   46 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:71:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 03 - Helpdesk Form - Loaded (22.2s)
✓ Screenshot saved: public\images\screenshots\07_welcome_loan_navigation_guest.png
  ✓   47 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:271:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 08 - Loan Application Form - Loaded (16.0s)
✓ Screenshot saved: public\images\screenshots\03_helpdesk_form_loaded_guest.png
  ✓   48 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:88:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 04 - Helpdesk Form - Filling Out (15.7s)
✓ Screenshot saved: public\images\screenshots\08_loan_form_loaded_guest.png
  ✓   49 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:288:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 09 - Loan Application Form - Filling Out (14.8s)
✓ Screenshot saved: public\images\screenshots\04_helpdesk_form_filled_guest.png
  ✓   50 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:165:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 05 - Helpdesk Form - Submit (17.7s)
✓ Screenshot saved: public\images\screenshots\09_loan_form_filled_guest.png
  ✓   51 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:365:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 10 - Loan Application Form - Submit (10.3s)
✓ Screenshot saved: public\images\screenshots\05_helpdesk_form_submitted_guest.png
      52 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:212:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 06 - Helpdesk Success Page
✓ Screenshot saved: public\images\screenshots\10_loan_form_submitted_guest.png
  ✓   53 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:410:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 11 - Loan Application Success Page (14.5s)
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

  ✓   54 …ium] › tests\e2e\guest-flow-screenshots.spec.ts:450:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 12 - Complete Flow Summary - Screenshots Verification (222ms)  ✓   55 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:23:3 › Helpdesk Module - Accessibility Compliance › should pass WCAG 2.2 AA automated checks on helpdesk pages (27.4s)
✓ Screenshot saved: public\images\screenshots\06_helpdesk_success_page_guest.png
  ✓   52 [chromium] › tests\e2e\guest-flow-screenshots.spec.ts:212:3 › Guest User Flow - Welcome → Helpdesk → Loan Application › 06 - Helpdesk Success Page (22.7s)
  ✓   56 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:45:3 › Helpdesk Module - Accessibility Compliance › should support full keyboard navigation on helpdesk forms (21.2s)
  ✓   57 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:68:3 › Helpdesk Module - Accessibility Compliance › should have visible focus indicators with 3:1 contrast ratio (29.5s)
  ✘   58 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:101:3 › Helpdesk Module - Accessibility Compliance › should have minimum 44x44px touch targets on mobile (29.2s)
  ✓   59 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:129:3 › Helpdesk Module - Accessibility Compliance › should have proper ARIA landmarks and labels (23.9s)
  ✓   60 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:156:3 › Helpdesk Module - Accessibility Compliance › should have proper color contrast ratios (4.5:1 for text) (28.5s)
  ✓   61 …mium] › tests\e2e\helpdesk-accessibility.spec.ts:177:3 › Helpdesk Module - Accessibility Compliance › should support screen reader announcements with ARIA live regions (25.0s)  ✓   62 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:197:3 › Helpdesk Module - Accessibility Compliance › should have semantic HTML structure with proper headings (27.8s)
  ✓   63 [chromium] › tests\e2e\helpdesk-accessibility.spec.ts:214:3 › Helpdesk Module - Accessibility Compliance › should not rely on color alone for information (26.2s)
  ✓   64 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:66:3 › Helpdesk Module - Cross-Module Integration › should link helpdesk tickets to asset records (9.2s)
✓ Laravel server is running
  ✓   65 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:106:3 › Helpdesk Module - Cross-Module Integration › should send cross-module notifications (9.0s)
✓ Laravel server is running
✓ App is responding (page title: ICTServe)
  ✓   66 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:78:3 › Helpdesk Module - Cross-Module Integration › should display asset information in ticket details (8.6s)
✓ Browser connection is active
  ✓   67 …omium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:113:3 › Helpdesk Module - Cross-Module Integration › should validate referential integrity between modules (10.6s)✓ Page content loaded successfully
  ✓   68 … › tests\e2e\helpdesk-cross-module-integration.spec.ts:85:3 › Helpdesk Module - Cross-Module Integration › should create maintenance ticket when asset returned damaged (10.4s)✓ DOM has 166 elements
  ✓   69 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:120:3 › Helpdesk Module - Cross-Module Integration › should track cross-module audit trail (11.0s)
✓ Navigation working (current URL: <http://localhost:8000/>)
  ✓   70 …omium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:92:3 › Helpdesk Module - Cross-Module Integration › should display unified asset history (loans + tickets) (10.9s)✓ Page language: en
  ✓   71 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:127:3 › Helpdesk Module - Cross-Module Integration › should handle cross-module API endpoints (10.2s)
✓ DOM structure is valid
  ✓   72 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:99:3 › Helpdesk Module - Cross-Module Integration › should maintain data consistency across modules (10.3s)
✓ Playwright request handler available
  ✓   73 [chromium] › tests\e2e\helpdesk-cross-module-integration.spec.ts:134:3 › Helpdesk Module - Cross-Module Integration › should display cross-module dashboard analytics (16.7s)
✓ HTML structure is valid
  ✘   74 [chromium] › tests\e2e\helpdesk-performance.spec.ts:21:3 › Helpdesk Module - Performance Tests › should meet Core Web Vitals targets (LCP <2.5s, FID <100ms, CLS <0.1) (12.5s)
Warning: Could not connect to server. Make sure Laravel is running on <http://localhost:8000>
✓ All tests completed successfully
  ✓   75 [chromium] › tests\e2e\helpdesk-performance.spec.ts:99:3 › Helpdesk Module - Performance Tests › should handle ticket list pagination efficiently (26.2s)
  ✘   76 [chromium] › tests\e2e\helpdesk-performance.spec.ts:76:3 › Helpdesk Module - Performance Tests › should load helpdesk ticket submission form within 2 seconds (29.7s)
  ✓   77 [chromium] › tests\e2e\helpdesk-performance.spec.ts:121:3 › Helpdesk Module - Performance Tests › should optimize database queries (no N+1 issues) (26.2s)
  ✘   78 [chromium] › tests\e2e\helpdesk-performance.spec.ts:141:3 › Helpdesk Module - Performance Tests › should cache static assets effectively (12.8s)
Warning: Could not connect to server. Make sure Laravel is running on <http://localhost:8000>
  ✓   79 [chromium] › tests\e2e\helpdesk-performance.spec.ts:173:3 › Helpdesk Module - Performance Tests › should handle form submission within 2 seconds (28.4s)
  ✓   80 [chromium] › tests\e2e\helpdesk-performance.spec.ts:209:3 › Helpdesk Module - Performance Tests › should optimize image loading with lazy loading (28.2s)
  ✓   81 [chromium] › tests\e2e\helpdesk-performance.spec.ts:233:3 › Helpdesk Module - Performance Tests › should handle concurrent user interactions efficiently (28.4s)
  ✘   82 [chromium] › tests\e2e\helpdesk-performance.spec.ts:260:3 › Helpdesk Module - Performance Tests › should achieve Lighthouse Performance score 90+ (28.6s)
  ✓   83 [chromium] › tests\e2e\helpdesk-performance.spec.ts:286:3 › Helpdesk Module - Performance Tests › should minimize JavaScript bundle size (24.3s)
  ✘   84 [chromium] › tests\e2e\helpdesk.module.spec.ts:15:3 › Helpdesk Ticket Module › should load welcome page without errors (1.7m)
  ✘   85 [chromium] › tests\e2e\helpdesk.module.spec.ts:33:3 › Helpdesk Ticket Module › should navigate to helpdesk module (1.7m)
  ✘   86 [chromium] › tests\e2e\helpdesk.module.spec.ts:46:3 › Helpdesk Ticket Module › should display ticket list without console errors (1.6m)
  ✘   87 [chromium] › tests\e2e\helpdesk.module.spec.ts:71:3 › Helpdesk Ticket Module › should handle ticket creation form (1.7m)
  ✘   88 [chromium] › tests\e2e\helpdesk.module.spec.ts:94:3 › Helpdesk Ticket Module › should respond to filter interactions (1.7m)
  ✘   89 [chromium] › tests\e2e\helpdesk.module.spec.ts:116:3 › Helpdesk Ticket Module › should handle network errors gracefully (1.7m)
  ✘   90 [chromium] › tests\e2e\helpdesk.module.spec.ts:138:3 › Helpdesk Ticket Module › should maintain session across navigation (1.7m)
  ✓   91 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:19:3 › Loan Module Accessibility › Guest loan application form meets WCAG 2.2 AA (6.3s)
  ✓   92 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:29:3 › Loan Module Accessibility › Authenticated loan dashboard meets WCAG 2.2 AA (19.2s)
  ✓   93 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:46:3 › Loan Module Accessibility › Loan history page meets WCAG 2.2 AA (14.8s)
  ✘   94 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:63:3 › Loan Module Accessibility › Keyboard navigation works on loan form (4.2s)
  ✘   95 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:80:3 › Loan Module Accessibility › Form validation errors are announced to screen readers (36.6s)
  ✓   96 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:95:3 › Loan Module Accessibility › Color contrast meets WCAG AA standards (12.8s)
  ✓   97 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:110:3 › Loan Module Accessibility › Images have alt text (9.9s)
  ✓   98 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:121:3 › Loan Module Accessibility › Form labels are properly associated (20.9s)
  ✓   99 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:135:3 › Loan Module Accessibility › Skip links are present and functional (23.2s)
  ✓  100 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:152:3 › Loan Module Accessibility › Language attribute is set correctly (32.5s)
  ✘  101 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:159:3 › Loan Module Accessibility › Page title is descriptive (34.1s)
  ✓  102 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:167:3 › Loan Module Accessibility › Touch targets meet minimum size (44x44px) (29.2s)
  ✓  103 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:181:3 › Loan Module Accessibility › Modal dialogs have proper ARIA attributes (49.7s)
  ✓  104 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:202:3 › Loan Module Accessibility › Tables have proper structure (1.5m)
  ✓  105 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:225:3 › Loan Module Accessibility › Responsive design maintains accessibility (1.6m)
  ✓  106 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:247:3 › Loan Module Accessibility › Loading states are announced (58.7s)
  ✓  107 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:257:3 › Loan Module Accessibility › Focus trap works in modals (1.3m)
  ✓  108 [chromium] › tests\e2e\loan-module-accessibility.spec.ts:287:3 › Loan Module Accessibility › Escape key closes modals (1.5m)
  ✘  109 [chromium] › tests\e2e\loan-module-integration.spec.ts:11:3 › Loan Module Integration Tests › complete guest loan application workflow (1.1m)
  ✘  110 [chromium] › tests\e2e\loan-module-integration.spec.ts:38:3 › Loan Module Integration Tests › authenticated user loan workflow (1.9m)
  ✘  111 [chromium] › tests\e2e\loan-module-integration.spec.ts:61:3 › Loan Module Integration Tests › email approval workflow simulation (32.1s)
  ✘  112 [chromium] › tests\e2e\loan-module-integration.spec.ts:70:3 › Loan Module Integration Tests › loan extension request workflow (1.6m)
  ✘  113 [chromium] › tests\e2e\loan-module-integration.spec.ts:90:3 › Loan Module Integration Tests › asset availability check integration (53.9s)
  ✘  114 [chromium] › tests\e2e\loan-module-integration.spec.ts:107:3 › Loan Module Integration Tests › cross-module navigation (1.5m)
  ✘  115 [chromium] › tests\e2e\loan-module-integration.spec.ts:127:3 › Loan Module Integration Tests › dashboard analytics integration (1.4m)
  ✘  116 [chromium] › tests\e2e\loan-module-integration.spec.ts:147:3 › Loan Module Integration Tests › notification system integration (1.2m)
  ✘  117 [chromium] › tests\e2e\loan-module-integration.spec.ts:165:3 › Loan Module Integration Tests › responsive design integration (54.1s)
  ✘  118 [chromium] › tests\e2e\loan-module-performance.spec.ts:15:3 › Loan Module Performance Tests › measures Core Web Vitals for loan dashboard (42.6s)
  ✘  119 [chromium] › tests\e2e\loan-module-performance.spec.ts:53:3 › Loan Module Performance Tests › loan application form loads quickly (1.6m)
  ✘  120 [chromium] › tests\e2e\loan-module-performance.spec.ts:64:3 › Loan Module Performance Tests › asset availability check is responsive (1.6m)
  ✘  121 [chromium] › tests\e2e\loan-module-performance.spec.ts:77:3 › Loan Module Performance Tests › loan history pagination is smooth (2.3m)
  ✘  122 [chromium] › tests\e2e\loan-module-performance.spec.ts:90:3 › Loan Module Performance Tests › search functionality is fast (2.0m)
  ✘  123 [chromium] › tests\e2e\loan-module-performance.spec.ts:104:3 › Loan Module Performance Tests › dashboard widgets load progressively (1.1m)
  ✘  124 [chromium] › tests\e2e\loan-module-performance.spec.ts:119:3 › Loan Module Performance Tests › measures Time to Interactive (TTI) (1.6m)
  ✓  125 [chromium] › tests\e2e\loan-module-performance.spec.ts:136:3 › Loan Module Performance Tests › checks bundle size impact (18.8s)
  ✘  126 [chromium] › tests\e2e\loan.module.spec.ts:15:3 › Asset Loan Module › should load home page without JavaScript errors (1.7m)
  ✘  127 [chromium] › tests\e2e\loan.module.spec.ts:35:3 › Asset Loan Module › should navigate to loan module (1.7m)
  ✘  128 [chromium] › tests\e2e\loan.module.spec.ts:49:3 › Asset Loan Module › should display loan list without errors (1.7m)
  ✘  129 [chromium] › tests\e2e\loan.module.spec.ts:75:3 › Asset Loan Module › should handle loan request form interaction (1.7m)
  ✘  130 [chromium] › tests\e2e\loan.module.spec.ts:99:3 › Asset Loan Module › should handle asset selection dropdown (1.7m)
  ✘  131 [chromium] › tests\e2e\loan.module.spec.ts:123:3 › Asset Loan Module › should handle approval workflow buttons (1.7m)
  ✘  132 [chromium] › tests\e2e\loan.module.spec.ts:144:3 › Asset Loan Module › should maintain responsive behavior (1.7m)
  ✘  133 [chromium] › tests\e2e\loan.module.spec.ts:161:3 › Asset Loan Module › should handle form validation feedback (1.7m)
     134 [chromium] › tests\e2e\loan.module.spec.ts:187:3 › Asset Loan Module › should handle network requests without failures
  ✘  135 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Welcome Page meets Core Web Vitals thresholds (13.2s)

Welcome Page Performance Metrics:
  LCP: 2540ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 1961ms (target: <600ms)
  Issues: LCP 2540ms exceeds 2.5s threshold, TTFB 1961ms exceeds 600ms threshold
  ✘  136 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Accessibility Statement meets Core Web Vitals thresholds (13.9s)

Accessibility Statement Performance Metrics:
  LCP: 3116ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 2488ms (target: <600ms)
  Issues: LCP 3116ms exceeds 2.5s threshold, TTFB 2488ms exceeds 600ms threshold
  ✘  137 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Contact Page meets Core Web Vitals thresholds (13.0s)

Contact Page Performance Metrics:
  LCP: 2564ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 1830ms (target: <600ms)
  Issues: LCP 2564ms exceeds 2.5s threshold, TTFB 1830ms exceeds 600ms threshold
  ✘  138 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Services Page meets Core Web Vitals thresholds (14.4s)

Services Page Performance Metrics:
  LCP: 2716ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 2057ms (target: <600ms)
  Issues: LCP 2716ms exceeds 2.5s threshold, TTFB 2057ms exceeds 600ms threshold
  ✘  139 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Helpdesk Ticket Form meets Core Web Vitals thresholds (32.2s)
  ✘  134 [chromium] › tests\e2e\loan.module.spec.ts:187:3 › Asset Loan Module › should handle network requests without failures (1.7m)
     140 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Asset Loan Application Form meets Core Web Vitals thresholds

Helpdesk Ticket Form Performance Metrics:
  LCP: 9500ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 8755ms (target: <600ms)
  Issues: LCP 9500ms exceeds 2.5s threshold, TTFB 8755ms exceeds 600ms threshold
  ✘  141 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › Staff Dashboard meets Core Web Vitals thresholds (15ms)
  ✘  142 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › User Profile meets Core Web Vitals thresholds (17ms)
  ✘  143 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › Submission History meets Core Web Vitals thresholds (16ms)
  ✘  144 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › Claim Submissions meets Core Web Vitals thresholds (14ms)
  ✘  145 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › My Tickets meets Core Web Vitals thresholds (19ms)
  ✘  146 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:189:9 › Core Web Vitals - Authenticated Pages › My Loans meets Core Web Vitals thresholds (17ms)
  ✘  147 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Admin Dashboard meets Core Web Vitals thresholds (15ms)
  ✘  148 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Helpdesk Tickets Management meets Core Web Vitals thresholds (15ms)
  ✘  149 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Loan Applications Management meets Core Web Vitals thresholds (16ms)

Asset Loan Application Form Performance Metrics:
  LCP: 10776ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 9900ms (target: <600ms)
  Issues: LCP 10776ms exceeds 2.5s threshold, TTFB 9900ms exceeds 600ms threshold
  ✘  150 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Assets Management meets Core Web Vitals thresholds (17ms)
  ✘  140 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:141:9 › Core Web Vitals - Guest Pages › Asset Loan Application Form meets Core Web Vitals thresholds (37.9s)
  ✘  151 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:239:9 › Core Web Vitals - Admin Pages › Users Management meets Core Web Vitals thresholds (17ms)
     152 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:303:9 › Core Web Vitals - Network Conditions Testing › Performance under Fast 3G conditions
     153 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:303:9 › Core Web Vitals - Network Conditions Testing › Performance under Slow 3G conditions

Welcome Page on Slow 3G:
  LCP: 12116ms
  TTFB: 10717ms

Welcome Page on Fast 3G:
  LCP: 10140ms
  TTFB: 9153ms

Helpdesk Form on Slow 3G:
  LCP: 8476ms
  TTFB: 6906ms

Helpdesk Form on Fast 3G:
  LCP: 12400ms
  TTFB: 11576ms

Loan Application Form on Slow 3G:
  LCP: 18688ms
  TTFB: 17077ms
  ✘  153 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:303:9 › Core Web Vitals - Network Conditions Testing › Performance under Slow 3G conditions (1.9m)
     154 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:303:9 › Core Web Vitals - Network Conditions Testing › Performance under 4G conditions

Loan Application Form on Fast 3G:
  LCP: 18792ms
  TTFB: 17678ms
  ✘  152 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:303:9 › Core Web Vitals - Network Conditions Testing › Performance under Fast 3G conditions (2.3m)
     155 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:370:9 › Core Web Vitals - Mobile vs Desktop Performance › Desktop performance validation

Welcome Page on 4G:
  LCP: 16028ms
  TTFB: 15320ms

Welcome Page on Desktop:
  LCP: 23504ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 22910ms (target: <600ms)

Helpdesk Form on 4G:
  LCP: 18140ms
  TTFB: 17493ms

Helpdesk Form on Desktop:
  LCP: 14524ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 14011ms (target: <600ms)

Loan Application Form on 4G:
  LCP: 15316ms
  TTFB: 14664ms
  ✘  154 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:303:9 › Core Web Vitals - Network Conditions Testing › Performance under 4G conditions (2.1m)
     156 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:370:9 › Core Web Vitals - Mobile vs Desktop Performance › Tablet performance validation

Loan Application Form on Desktop:
  LCP: 14928ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 14231ms (target: <600ms)
  ✘  155 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:370:9 › Core Web Vitals - Mobile vs Desktop Performance › Desktop performance validation (2.3m)
     157 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:370:9 › Core Web Vitals - Mobile vs Desktop Performance › Mobile performance validation

Welcome Page on Tablet:
  LCP: 14640ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.002 (target: <0.1)
  TTFB: 14065ms (target: <600ms)

Welcome Page on Mobile:
  LCP: 8812ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.005 (target: <0.1)
  TTFB: 8196ms (target: <600ms)

Helpdesk Form on Tablet:
  LCP: 15852ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 15202ms (target: <600ms)

Helpdesk Form on Mobile:
  LCP: 9424ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.000 (target: <0.1)
  TTFB: 8782ms (target: <600ms)

Loan Application Form on Tablet:
  LCP: 9592ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.002 (target: <0.1)
  TTFB: 9029ms (target: <600ms)
  ✘  156 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:370:9 › Core Web Vitals - Mobile vs Desktop Performance › Tablet performance validation (1.6m)
  ✘  158 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:412:5 › Core Web Vitals - Performance Regression Testing › Compare current performance against baseline (858ms)
     159 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:547:5 › Core Web Vitals - Performance Report Generation › Generate comprehensive performance report

Loan Application Form on Mobile:
  LCP: 6140ms (target: <2500ms)
  FID: 0ms (target: <100ms)
  CLS: 0.005 (target: <0.1)
  TTFB: 5678ms (target: <600ms)
  ✘  157 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:370:9 › Core Web Vitals - Mobile vs Desktop Performance › Mobile performance validation (1.3m)
  ✘  160 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Welcome Page meets Lighthouse thresholds (15.3s)

Welcome Page Lighthouse Scores:
  Performance: 0/100 (target: ≥90)
  Accessibility: 100/100 (target: ≥100)
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 0 below 90 threshold
  ✘  161 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Accessibility Statement meets Lighthouse thresholds (15.0s)

Accessibility Statement Lighthouse Scores:
  Performance: 0/100 (target: ≥90)
  Accessibility: 100/100 (target: ≥100)
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 0 below 90 threshold
  ✘  162 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Contact Page meets Lighthouse thresholds (13.5s)

Contact Page Lighthouse Scores:
  Performance: 13/100 (target: ≥90)
  Accessibility: 90/100 (target: ≥100)
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 13 below 90 threshold, Accessibility score 90 below 100 threshold
  ✘  163 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Services Page meets Lighthouse thresholds (13.8s)

Services Page Lighthouse Scores:
  Performance: 10/100 (target: ≥90)
  Accessibility: 100/100 (target: ≥100)
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 10 below 90 threshold
  ✘  164 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Helpdesk Ticket Form meets Lighthouse thresholds (13.2s)

Helpdesk Ticket Form Lighthouse Scores:
  Performance: 6/100 (target: ≥90)
  Accessibility: 100/100 (target: ≥100)
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 6 below 90 threshold
     165 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Asset Loan Application Form meets Lighthouse thresholds

========================================
CORE WEB VITALS PERFORMANCE REPORT
========================================

Overall: 0/6 pages passed all thresholds

Welcome Page (/)
  Status: ✗ FAILED
  LCP: 2848ms
  FID: 0ms
  CLS: 0.000
  TTFB: 2291ms
  Issues: LCP 2848ms exceeds 2.5s threshold, TTFB 2291ms exceeds 600ms threshold

Accessibility Statement (/accessibility)
  Status: ✗ FAILED
  LCP: 4492ms
  FID: 0ms
  CLS: 0.000
  TTFB: 3996ms
  Issues: LCP 4492ms exceeds 2.5s threshold, TTFB 3996ms exceeds 600ms threshold

Contact Page (/contact)
  Status: ✗ FAILED
  LCP: 4528ms
  FID: 0ms
  CLS: 0.000
  TTFB: 4035ms
  Issues: LCP 4528ms exceeds 2.5s threshold, TTFB 4035ms exceeds 600ms threshold

Services Page (/services)
  Status: ✗ FAILED
  LCP: 4968ms
  FID: 0ms
  CLS: 0.000
  TTFB: 4474ms
  Issues: LCP 4968ms exceeds 2.5s threshold, TTFB 4474ms exceeds 600ms threshold

Helpdesk Ticket Form (/helpdesk/create)
  Status: ✗ FAILED
  LCP: 4964ms
  FID: 0ms
  CLS: 0.000
  TTFB: 4468ms
  Issues: LCP 4964ms exceeds 2.5s threshold, TTFB 4468ms exceeds 600ms threshold

Asset Loan Application Form (/loan/apply)
  Status: ✗ FAILED
  LCP: 4700ms
  FID: 0ms
  CLS: 0.000
  TTFB: 4187ms
  Issues: LCP 4700ms exceeds 2.5s threshold, TTFB 4187ms exceeds 600ms threshold

  ✘  159 [chromium] › tests\e2e\performance\core-web-vitals.spec.ts:547:5 › Core Web Vitals - Performance Report Generation › Generate comprehensive performance report (1.5m)
  ✘  166 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:179:9 › Lighthouse Audit - Authenticated Pages › Staff Dashboard meets Lighthouse thresholds (167ms)
  ✘  167 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:179:9 › Lighthouse Audit - Authenticated Pages › User Profile meets Lighthouse thresholds (136ms)

Asset Loan Application Form Lighthouse Scores:
  Performance: 11/100 (target: ≥90)
  Accessibility: 100/100 (target: ≥100)
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 11 below 90 threshold
  ✘  165 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:140:9 › Lighthouse Audit - Guest Pages › Asset Loan Application Form meets Lighthouse thresholds (11.1s)
  ✘  168 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:179:9 › Lighthouse Audit - Authenticated Pages › Submission History meets Lighthouse thresholds (125ms)
  ✘  169 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:212:9 › Lighthouse Audit - Admin Pages › Admin Dashboard meets Lighthouse thresholds (175ms)
  ✘  170 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:212:9 › Lighthouse Audit - Admin Pages › Helpdesk Tickets Management meets Lighthouse thresholds (121ms)
     171 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:235:5 › Lighthouse Audit - Comprehensive Report › Generate comprehensive Lighthouse report
     172 …sponsive.spec.ts:57:13 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should display 1 column layout on 320px viewport (iPhone SE)
========================================
LIGHTHOUSE PERFORMANCE AUDIT REPORT
========================================

Overall: 0/6 pages passed all thresholds

Welcome Page (<http://localhost:8000/>)
  Status: ✗ FAILED
  Performance: 0/100
  Accessibility: 100/100
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 0 below 90 threshold

Accessibility Statement (<http://localhost:8000/accessibility>)
  Status: ✗ FAILED
  Performance: 0/100
  Accessibility: 100/100
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 0 below 90 threshold

Contact Page (<http://localhost:8000/contact>)
  Status: ✗ FAILED
  Performance: 13/100
  Accessibility: 90/100
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 13 below 90 threshold, Accessibility score 90 below 100 threshold

Services Page (<http://localhost:8000/services>)
  Status: ✗ FAILED
  Performance: 11/100
  Accessibility: 100/100
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 11 below 90 threshold

Helpdesk Ticket Form (<http://localhost:8000/helpdesk/create>)
  Status: ✗ FAILED
  Performance: 9/100
  Accessibility: 100/100
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 9 below 90 threshold

Asset Loan Application Form (<http://localhost:8000/loan/apply>)
  Status: ✗ FAILED
  Performance: 0/100
  Accessibility: 100/100
  Best Practices: 95/100
  SEO: 90/100
  Issues: Performance score 0 below 90 threshold

Report saved to: test-results/lighthouse-audit-report.json

  ✘  171 [chromium] › tests\e2e\performance\lighthouse-audit.spec.ts:235:5 › Lighthouse Audit - Comprehensive Report › Generate comprehensive Lighthouse report (1.2m)
  ✘  173 …ve.spec.ts:57:13 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should display 1 column layout on 375px viewport (iPhone 8) (1.7m)  ✘  172 …e.spec.ts:57:13 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should display 1 column layout on 320px viewport (iPhone SE) (1.8m)  ✘  174 …s:57:13 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should display 1 column layout on 414px viewport (iPhone 11 Pro Max) (1.8m)  ✘  175 …ponsive.spec.ts:109:9 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should stack quick action buttons vertically on mobile (1.7m)  ✘  176 …sive.spec.ts:136:9 › Staff Dashboard - Responsive Behavior › Mobile Viewports (320px-414px): 1 Column Layout › should display recent activity in single column on mobile (1.8m)  ✘  177 …spec.ts:168:13 › Staff Dashboard - Responsive Behavior › Tablet Viewports (768px-1024px): 2 Column Layout › should display 2 column layout on 768px viewport (iPad Mini) (1.8m)  ✘  178 ….spec.ts:168:13 › Staff Dashboard - Responsive Behavior › Tablet Viewports (768px-1024px): 2 Column Layout › should display 2 column layout on 820px viewport (iPad Air) (2.1m)  ✘  179 …spec.ts:168:13 › Staff Dashboard - Responsive Behavior › Tablet Viewports (768px-1024px): 2 Column Layout › should display 2 column layout on 1000px viewport (iPad Pro) (2.0m)  ✘  180 …ponsive.spec.ts:222:9 › Staff Dashboard - Responsive Behavior › Tablet Viewports (768px-1024px): 2 Column Layout › should display recent activity in 2 columns on tablet (2.5m)  ✘  181 …e.spec.ts:248:13 › Staff Dashboard - Responsive Behavior › Desktop Viewports (1280px+): 4 Column Layout › should display 4 column layout on 1280px viewport (Desktop HD) (2.6m)  ✘  182 …c.ts:248:13 › Staff Dashboard - Responsive Behavior › Desktop Viewports (1280px+): 4 Column Layout › should display 4 column layout on 1920px viewport (Desktop Full HD) (1.7m)  ✘  183 …e.spec.ts:248:13 › Staff Dashboard - Responsive Behavior › Desktop Viewports (1280px+): 4 Column Layout › should display 4 column layout on 2560px viewport (Desktop 4K) (1.7m)  ✘  184 …oard.responsive.spec.ts:308:9 › Staff Dashboard - Responsive Behavior › Desktop Viewports (1280px+): 4 Column Layout › should display all cards in single row on desktop (1.7m)  ✘  185 ….responsive.spec.ts:335:9 › Staff Dashboard - Responsive Behavior › Desktop Viewports (1280px+): 4 Column Layout › should display quick actions in single row on desktop (1.7m)  ✘  186 …board.responsive.spec.ts:361:9 › Staff Dashboard - Responsive Behavior › Touch Target Compliance (44x44px minimum) › should have minimum 44x44px touch targets on mobile (1.7m)  ✘  187 …romium] › tests\e2e\staff-dashboard.responsive.spec.ts:391:9 › Staff Dashboard - Responsive Behavior › Performance and Loading › should load quickly on desktop viewport (1.7m)  ✘  188 …hromium] › tests\e2e\staff-dashboard.responsive.spec.ts:404:9 › Staff Dashboard - Responsive Behavior › Performance and Loading › should load quickly on mobile viewport (1.7m)  ✘  189 … › tests\e2e\staff-dashboard.responsive.spec.ts:419:9 › Staff Dashboard - Responsive Behavior › No Horizontal Scroll › should not have horizontal scroll on any viewport (1.7m)  ✘  190 …omium] › tests\e2e\staff-dashboard.responsive.spec.ts:447:9 › Staff Dashboard - Responsive Behavior › Content Readability › should have readable text on mobile viewport (1.7m)  ✘  191 …e\staff-dashboard.responsive.spec.ts:467:9 › Staff Dashboard - Responsive Behavior › Responsive Image and Icon Handling › should display icons properly on all viewports (1.7m)  ✘  192 …› tests\e2e\staff-dashboard.responsive.spec.ts:492:9 › Staff Dashboard - Responsive Behavior › Viewport Transition Smoothness › should handle viewport resize gracefully (1.2h)     193 …e2e\staff-dashboard.responsive.spec.ts:516:9 › Staff Dashboard - Responsive Behavior › Accessibility on Different Viewports › should maintain focus indicators on all viewports     194 [chromium] › tests\e2e\staff-flow-debug.spec.ts:14:3 › Staff User Debug Flow › Staff journey: Steps 1-5 only

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
  ✘  194 [chromium] › tests\e2e\staff-flow-debug.spec.ts:14:3 › Staff User Debug Flow › Staff journey: Steps 1-5 only (1.1m)
  ✘  195 [chromium] › tests\e2e\staff-flow-optimized.spec.ts:23:3 › Staff User Complete Flow › Staff journey: Welcome to Logout with all features (1.2m)

🚀 Starting optimized staff flow test

📸 Step 1/19: Welcome page
✅ Step 1 complete

📸 Step 2/19: Navigate to login
  ✓  196 …ve.refactored.spec.ts:108:9 › 01 - Automated Accessibility Testing - Guest Pages › 01-1 - Welcome Page should pass WCAG 2.2 AA @accessibility @a11y @wcag @guest @smoke (10.0s)
✅ Welcome Page: No accessibility violations found
✅ Welcome Page: 24 accessibility checks passed
  ✓  197 …ed.spec.ts:108:9 › 01 - Automated Accessibility Testing - Guest Pages › 01-2 - Accessibility Statement should pass WCAG 2.2 AA @accessibility @a11y @wcag @guest @smoke (13.1s)
✅ Accessibility Statement: No accessibility violations found
✅ Accessibility Statement: 24 accessibility checks passed
  ✓  198 …ve.refactored.spec.ts:108:9 › 01 - Automated Accessibility Testing - Guest Pages › 01-3 - Contact Page should pass WCAG 2.2 AA @accessibility @a11y @wcag @guest @smoke (14.7s)
✅ Contact Page: No accessibility violations found
✅ Contact Page: 27 accessibility checks passed
  ✓  199 …e.refactored.spec.ts:108:9 › 01 - Automated Accessibility Testing - Guest Pages › 01-4 - Services Page should pass WCAG 2.2 AA @accessibility @a11y @wcag @guest @smoke (33.0s)
✅ Services Page: No accessibility violations found
✅ Services Page: 24 accessibility checks passed
  ✓  200 …ec.ts:108:9 › 01 - Automated Accessibility Testing - Guest Pages › 01-5 - Helpdesk Ticket Form (Guest) should pass WCAG 2.2 AA @accessibility @a11y @wcag @guest @smoke (31.9s)
✅ Helpdesk Ticket Form (Guest): No accessibility violations found
✅ Helpdesk Ticket Form (Guest): 28 accessibility checks passed
  ✘  193 …ff-dashboard.responsive.spec.ts:516:9 › Staff Dashboard - Responsive Behavior › Accessibility on Different Viewports › should maintain focus indicators on all viewports (1.3h)
     201 ….ts:145:9 › 02 - Automated Accessibility Testing - Authenticated Pages › 02-1 - Staff Dashboard should pass WCAG 2.2 AA @accessibility @a11y @wcag @staff @authenticated @smoke
  ✓  202 …08:9 › 01 - Automated Accessibility Testing - Guest Pages › 01-6 - Asset Loan Application Form (Guest) should pass WCAG 2.2 AA @accessibility @a11y @wcag @guest @smoke (12.7s)
✅ Asset Loan Application Form (Guest): No accessibility violations found
✅ Asset Loan Application Form (Guest): 17 accessibility checks passed
     203 …pec.ts:145:9 › 02 - Automated Accessibility Testing - Authenticated Pages › 02-2 - User Profile should pass WCAG 2.2 AA @accessibility @a11y @wcag @staff @authenticated @smoke

❌ Staff Dashboard: 1 violation(s) found

1. color-contrast (serious)
   Description: Ensure the contrast between foreground and background colors meets WCAG 2 AA minimum contrast ratio thresholds
   Help: Elements must meet minimum color contrast ratio thresholds
   Help URL: <https://dequeuniversity.com/rules/axe/4.11/color-contrast?application=playwright>
   Affected elements: 5
   - Element 1: <p class="text-xs text-slate-500">
                                                    2 days ago
  ...
     Target: .h-full.flex-col.bg-slate-900\/70:nth-child(2) > .px-6.py-4.flex-1 > div:nth-child(2) > .divide-y.divide-slate-800[role="list"] > li:nth-child(1) > .space-x-3.flex > .space-y-1.flex-1 > .text-xs.text-slate-500
   - Element 2: <p class="text-xs text-slate-500">
                                                    2 days ago
  ...
     Target: .h-full.flex-col.bg-slate-900\/70:nth-child(2) > .px-6.py-4.flex-1 > div:nth-child(2) > .divide-y.divide-slate-800[role="list"] > li:nth-child(2) > .space-x-3.flex > .space-y-1.flex-1 > .text-xs.text-slate-500
   - Element 3: <p class="text-xs text-slate-500">
                                                    2 days ago
  ...
     Target: li:nth-child(3) > .space-x-3.flex > .space-y-1.flex-1 > .text-xs.text-slate-500

  ✘  201 …:9 › 02 - Automated Accessibility Testing - Authenticated Pages › 02-1 - Staff Dashboard should pass WCAG 2.2 AA @accessibility @a11y @wcag @staff @authenticated @smoke (1.2m)
  ✓  204 …› 02 - Automated Accessibility Testing - Authenticated Pages › 02-3 - Submission History should pass WCAG 2.2 AA @accessibility @a11y @wcag @staff @authenticated @smoke (2.5m)
✅ User Profile: No accessibility violations found
✅ User Profile: 27 accessibility checks passed
  ✓  203 …145:9 › 02 - Automated Accessibility Testing - Authenticated Pages › 02-2 - User Profile should pass WCAG 2.2 AA @accessibility @a11y @wcag @staff @authenticated @smoke (2.1m)
  ✘  205 … › 02 - Automated Accessibility Testing - Authenticated Pages › 02-4 - Claim Submissions should pass WCAG 2.2 AA @accessibility @a11y @wcag @staff @authenticated @smoke (1.3m)
✅ Submission History: No accessibility violations found
✅ Submission History: 18 accessibility checks passed
  ✓  206 …3 - Automated Accessibility Testing - Approver Pages › 03-1 - Approval Interface (Grade 41+) should pass WCAG 2.2 AA @accessibility @a11y @wcag @approver @authenticated (1.5m)
  ✓  207 …red.spec.ts:227:9 › 04 - Automated Accessibility Testing - Admin Pages › 04-1 - Admin Dashboard should pass WCAG 2.2 AA @accessibility @a11y @wcag @admin @authenticated (1.5m)
✅ Approval Interface (Grade 41+): No accessibility violations found
✅ Approval Interface (Grade 41+): 17 accessibility checks passed
✅ Admin Dashboard: No accessibility violations found
✅ Admin Dashboard: 17 accessibility checks passed
  ✓  208 …227:9 › 04 - Automated Accessibility Testing - Admin Pages › 04-2 - Helpdesk Tickets Management should pass WCAG 2.2 AA @accessibility @a11y @wcag @admin @authenticated (1.7m)
  ✓  209 …27:9 › 04 - Automated Accessibility Testing - Admin Pages › 04-3 - Loan Applications Management should pass WCAG 2.2 AA @accessibility @a11y @wcag @admin @authenticated (2.0m)
✅ Helpdesk Tickets Management: No accessibility violations found
✅ Helpdesk Tickets Management: 19 accessibility checks passed
✅ Loan Applications Management: No accessibility violations found
✅ Loan Applications Management: 19 accessibility checks passed
  ✓  210 …d.spec.ts:227:9 › 04 - Automated Accessibility Testing - Admin Pages › 04-4 - Assets Management should pass WCAG 2.2 AA @accessibility @a11y @wcag @admin @authenticated (3.1m)
  ✓  211 …› 05 - Automated Accessibility Testing - Mobile Viewport › 05-01 - Welcome Page should pass WCAG 2.2 AA on mobile @accessibility @a11y @wcag @mobile @responsive @smoke (23.5s)
✅ Welcome Page (Mobile): No accessibility violations found
  ✓  212 …282:5 › 05 - Automated Accessibility Testing - Mobile Viewport › 05-02 - Helpdesk Form should pass WCAG 2.2 AA on mobile @accessibility @a11y @wcag @mobile @responsive (42.8s)
✅ Helpdesk Form (Mobile): No accessibility violations found
  ✓  213 …05 - Automated Accessibility Testing - Mobile Viewport › 05-03 - Loan Application Form should pass WCAG 2.2 AA on mobile @accessibility @a11y @wcag @mobile @responsive (48.6s)
✅ Assets Management: No accessibility violations found
✅ Assets Management: 17 accessibility checks passed
✅ Loan Application Form (Mobile): No accessibility violations found
  ✓  214 … › 06 - Automated Accessibility Testing - Specific WCAG 2.2 Criteria › 06-01 - Focus indicators should be visible (SC 2.4.7) @accessibility @a11y @wcag @criteria @smoke (1.2m)

- 215 …ts:342:10 › 06 - Automated Accessibility Testing - Specific WCAG 2.2 Criteria › 06-02 - Touch targets should be minimum 44x44px (SC 2.5.8) @accessibility @a11y @wcag @criteria
  ✓  216 …utomated Accessibility Testing - Specific WCAG 2.2 Criteria › 06-03 - Color contrast should be sufficient (SC 1.4.3, 1.4.11) @accessibility @a11y @wcag @criteria @smoke (1.1m)
  ✘  217 …ts:36:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 01 - Keyboard navigation through dashboard elements @accessibility @a11y @dashboard @wcag @smoke @keyboard (2.4m)
  ✘  218 …ed.spec.ts:90:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 02 - Color contrast meets WCAG AA standards @accessibility @a11y @dashboard @wcag @smoke @contrast (2.3m)
  ✘  219 …pec.ts:148:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 03 - Touch targets meet minimum size requirements @accessibility @a11y @dashboard @wcag @smoke @touch (1.9m)
  ✘  220 …refactored.spec.ts:216:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 04 - ARIA attributes and semantic HTML @accessibility @a11y @dashboard @wcag @smoke @aria (1.4m)
  ✘  221 …actored.spec.ts:262:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 05 - Screen reader compatibility @accessibility @a11y @dashboard @wcag @smoke @screen-reader (1.7m)
  ✘  222 …ashboard-accessibility.refactored.spec.ts:302:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 06 - Focus management @accessibility @a11y @dashboard @wcag @focus (1.7m)
  ✘  223 …red.spec.ts:341:5 › Staff Dashboard Accessibility - WCAG 2.2 Level AA › 07 - Responsive accessibility across viewports @accessibility @a11y @dashboard @wcag @responsive (1.7m)
  ✘  224 [chromium] › tests\e2e\filament.components.debug.spec.ts:107:3 › Filament Components Debug Suite › Dashboard widgets render without console errors @filament @debug (1.9m)
  ✘  225 [chromium] › tests\e2e\filament.components.debug.spec.ts:125:5 › Filament Components Debug Suite › Helpdesk Tickets resource loads without failures @filament @debug (1.7m)
  ✘  226 [chromium] › tests\e2e\filament.components.debug.spec.ts:125:5 › Filament Components Debug Suite › Loan Applications resource loads without failures @filament @debug (1.6m)
  ✘  227 [chromium] › tests\e2e\filament.components.debug.spec.ts:125:5 › Filament Components Debug Suite › Asset Inventory resource loads without failures @filament @debug (1.5m)
  ✘  228 [chromium] › tests\e2e\filament.components.debug.spec.ts:137:3 › Filament Components Debug Suite › Asset availability legend exposes all statuses @filament @debug (1.5m)
  ✘  229 …m] › tests\e2e\filament.components.debug.spec.ts:166:3 › Filament Components Debug Suite › Critical alerts widget surfaces empty state or alert actions @filament @debug (1.6m)
  ✘  230 …tests\e2e\helpdesk.refactored.spec.ts:23:3 › Helpdesk Ticket Module - Best Practices Architecture › 01 - Helpdesk Module Navigation @smoke @helpdesk @module @navigation (1.5m)
  ✘  231 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:38:3 › Helpdesk Ticket Module - Best Practices Architecture › 02 - Helpdesk Ticket List View @smoke @helpdesk @module (1.7m)
  ✘  232 … tests\e2e\helpdesk.refactored.spec.ts:59:3 › Helpdesk Ticket Module - Best Practices Architecture › 03 - Create New Ticket - Form Accessibility @helpdesk @module @form (1.7m)
  ✘  233 …helpdesk.refactored.spec.ts:105:3 › Helpdesk Ticket Module - Best Practices Architecture › 05 - Create New Ticket - Successful Submission @smoke @helpdesk @module @form (1.7m)
  ✘  234 …e\helpdesk.refactored.spec.ts:87:3 › Helpdesk Ticket Module - Best Practices Architecture › 04 - Create New Ticket - Form Validation @helpdesk @module @form @validation (1.6m)
  ✘  235 …omium] › tests\e2e\helpdesk.refactored.spec.ts:135:3 › Helpdesk Ticket Module - Best Practices Architecture › 06 - Ticket Filtering and Search @helpdesk @module @filter (1.6m)
  ✘  236 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:160:3 › Helpdesk Ticket Module - Best Practices Architecture › 07 - View Ticket Details @helpdesk @module @detail (1.6m)
  ✓  237 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:187:3 › Helpdesk Ticket Module - Best Practices Architecture › 08 - Ticket Status Update @helpdesk @module @status (2.2m)
  ✘  238 …desk.refactored.spec.ts:219:3 › Helpdesk Ticket Module - Best Practices Architecture › 09 - Module Navigation - Return to Dashboard @smoke @helpdesk @module @navigation (2.0m)
  ✓  239 …ium] › tests\e2e\helpdesk.refactored.spec.ts:239:3 › Helpdesk Ticket Module - Best Practices Architecture › 10 - Module Console Error Check @helpdesk @module @debugging (1.7m)
  ✘  240 [chromium] › tests\e2e\loan.refactored.spec.ts:23:3 › Loan Module - Best Practices Architecture › 01 - Loan Module Navigation @smoke @loan @module @navigation (1.4m)
  ✘  241 [chromium] › tests\e2e\loan.refactored.spec.ts:38:3 › Loan Module - Best Practices Architecture › 02 - Loan Application List View @smoke @loan @module (1.6m)
  ✘  242 …romium] › tests\e2e\loan.refactored.spec.ts:59:3 › Loan Module - Best Practices Architecture › 03 - Create New Loan Application - Form Accessibility @loan @module @form (1.6m)
     243 …mium] › tests\e2e\loan.refactored.spec.ts:91:3 › Loan Module - Best Practices Architecture › 04 - Create New Loan Application - Form Validation @loan @module @form @validation
     244 …um] › tests\e2e\loan.refactored.spec.ts:109:3 › Loan Module - Best Practices Architecture › 05 - Create New Loan Application - Successful Submission @smoke @loan @module @form

  ✘  234 …e\helpdesk.refactored.spec.ts:87:3 › Helpdesk Ticket Module - Best Practices Architecture › 04 - Create New Ticket - Form Validation @helpdesk @module @form @validation (1.6m)
  ✘  235 …omium] › tests\e2e\helpdesk.refactored.spec.ts:135:3 › Helpdesk Ticket Module - Best Practices Architecture › 06 - Ticket Filtering and Search @helpdesk @module @filter (1.6m)
  ✘  236 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:160:3 › Helpdesk Ticket Module - Best Practices Architecture › 07 - View Ticket Details @helpdesk @module @detail (1.6m)
  ✓  237 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:187:3 › Helpdesk Ticket Module - Best Practices Architecture › 08 - Ticket Status Update @helpdesk @module @status (2.2m)
  ✘  238 …desk.refactored.spec.ts:219:3 › Helpdesk Ticket Module - Best Practices Architecture › 09 - Module Navigation - Return to Dashboard @smoke @helpdesk @module @navigation (2.0m)
  ✓  239 …ium] › tests\e2e\helpdesk.refactored.spec.ts:239:3 › Helpdesk Ticket Module - Best Practices Architecture › 10 - Module Console Error Check @helpdesk @module @debugging (1.7m)
  ✘  240 [chromium] › tests\e2e\loan.refactored.spec.ts:23:3 › Loan Module - Best Practices Architecture › 01 - Loan Module Navigation @smoke @loan @module @navigation (1.4m)
  ✘  241 [chromium] › tests\e2e\loan.refactored.spec.ts:38:3 › Loan Module - Best Practices Architecture › 02 - Loan Application List View @smoke @loan @module (1.6m)
  ✘  242 …romium] › tests\e2e\loan.refactored.spec.ts:59:3 › Loan Module - Best Practices Architecture › 03 - Create New Loan Application - Form Accessibility @loan @module @form (1.6m)
     243 …mium] › tests\e2e\loan.refactored.spec.ts:91:3 › Loan Module - Best Practices Architecture › 04 - Create New Loan Application - Form Validation @loan @module @form @validation
     244 …um] › tests\e2e\loan.refactored.spec.ts:109:3 › Loan Module - Best Practices Architecture › 05 - Create New Loan Application - Successful Submission @smoke @loan @module @form

  ✘  234 …e\helpdesk.refactored.spec.ts:87:3 › Helpdesk Ticket Module - Best Practices Architecture › 04 - Create New Ticket - Form Validation @helpdesk @module @form @validation (1.6m)
  ✘  235 …omium] › tests\e2e\helpdesk.refactored.spec.ts:135:3 › Helpdesk Ticket Module - Best Practices Architecture › 06 - Ticket Filtering and Search @helpdesk @module @filter (1.6m)
  ✘  236 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:160:3 › Helpdesk Ticket Module - Best Practices Architecture › 07 - View Ticket Details @helpdesk @module @detail (1.6m)
  ✓  237 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:187:3 › Helpdesk Ticket Module - Best Practices Architecture › 08 - Ticket Status Update @helpdesk @module @status (2.2m)
  ✘  238 …desk.refactored.spec.ts:219:3 › Helpdesk Ticket Module - Best Practices Architecture › 09 - Module Navigation - Return to Dashboard @smoke @helpdesk @module @navigation (2.0m)
  ✓  239 …ium] › tests\e2e\helpdesk.refactored.spec.ts:239:3 › Helpdesk Ticket Module - Best Practices Architecture › 10 - Module Console Error Check @helpdesk @module @debugging (1.7m)
  ✘  240 [chromium] › tests\e2e\loan.refactored.spec.ts:23:3 › Loan Module - Best Practices Architecture › 01 - Loan Module Navigation @smoke @loan @module @navigation (1.4m)
  ✘  241 [chromium] › tests\e2e\loan.refactored.spec.ts:38:3 › Loan Module - Best Practices Architecture › 02 - Loan Application List View @smoke @loan @module (1.6m)
  ✘  242 …romium] › tests\e2e\loan.refactored.spec.ts:59:3 › Loan Module - Best Practices Architecture › 03 - Create New Loan Application - Form Accessibility @loan @module @form (1.6m)
     243 …mium] › tests\e2e\loan.refactored.spec.ts:91:3 › Loan Module - Best Practices Architecture › 04 - Create New Loan Application - Form Validation @loan @module @form @validation
     244 …um] › tests\e2e\loan.refactored.spec.ts:109:3 › Loan Module - Best Practices Architecture › 05 - Create New Loan Application - Successful Submission @smoke @loan @module @form

  ✘  234 …e\helpdesk.refactored.spec.ts:87:3 › Helpdesk Ticket Module - Best Practices Architecture › 04 - Create New Ticket - Form Validation @helpdesk @module @form @validation (1.6m)
  ✘  235 …omium] › tests\e2e\helpdesk.refactored.spec.ts:135:3 › Helpdesk Ticket Module - Best Practices Architecture › 06 - Ticket Filtering and Search @helpdesk @module @filter (1.6m)
  ✘  236 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:160:3 › Helpdesk Ticket Module - Best Practices Architecture › 07 - View Ticket Details @helpdesk @module @detail (1.6m)
  ✓  237 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:187:3 › Helpdesk Ticket Module - Best Practices Architecture › 08 - Ticket Status Update @helpdesk @module @status (2.2m)
  ✘  238 …desk.refactored.spec.ts:219:3 › Helpdesk Ticket Module - Best Practices Architecture › 09 - Module Navigation - Return to Dashboard @smoke @helpdesk @module @navigation (2.0m)
  ✓  239 …ium] › tests\e2e\helpdesk.refactored.spec.ts:239:3 › Helpdesk Ticket Module - Best Practices Architecture › 10 - Module Console Error Check @helpdesk @module @debugging (1.7m)
  ✘  234 …e\helpdesk.refactored.spec.ts:87:3 › Helpdesk Ticket Module - Best Practices Architecture › 04 - Create New Ticket - Form Validation @helpdesk @module @form @validation (1.6m)
  ✘  234 …e\helpdesk.refactored.spec.ts:87:3 › Helpdesk Ticket Module - Best Practices Architecture › 04 - Create New Ticket - Form Validation @helpdesk @module @form @validation (1.6m)
  ✘  235 …omium] › tests\e2e\helpdesk.refactored.spec.ts:135:3 › Helpdesk Ticket Module - Best Practices Architecture › 06 - Ticket Filtering and Search @helpdesk @module @filter (1.6m)
  ✘  236 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:160:3 › Helpdesk Ticket Module - Best Practices Architecture › 07 - View Ticket Details @helpdesk @module @detail (1.6m)
  ✓  237 [chromium] › tests\e2e\helpdesk.refactored.spec.ts:187:3 › Helpdesk Ticket Module - Best Practices Architecture › 08 - Ticket Status Update @helpdesk @module @status (2.2m)
  ✘  238 …desk.refactored.spec.ts:219:3 › Helpdesk Ticket Module - Best Practices Architecture › 09 - Module Navigation - Return to Dashboard @smoke @helpdesk @module @navigation (2.0m)
  ✓  239 …ium] › tests\e2e\helpdesk.refactored.spec.ts:239:3 › Helpdesk Ticket Module - Best Practices Architecture › 10 - Module Console Error Check @helpdesk @module @debugging (1.7m)
  ✘  240 [chromium] › tests\e2e\loan.refactored.spec.ts:23:3 › Loan Module - Best Practices Architecture › 01 - Loan Module Navigation @smoke @loan @module @navigation (1.4m)
  ✘  241 [chromium] › tests\e2e\loan.refactored.spec.ts:38:3 › Loan Module - Best Practices Architecture › 02 - Loan Application List View @smoke @loan @module (1.6m)
  ✘  242 …romium] › tests\e2e\loan.refactored.spec.ts:59:3 › Loan Module - Best Practices Architecture › 03 - Create New Loan Application - Form Accessibility @loan @module @form (1.6m)
  ✘  243 … tests\e2e\loan.refactored.spec.ts:91:3 › Loan Module - Best Practices Architecture › 04 - Create New Loan Application - Form Validation @loan @module @form @validation (2.1m)
  ✘  244 …ests\e2e\loan.refactored.spec.ts:109:3 › Loan Module - Best Practices Architecture › 05 - Create New Loan Application - Successful Submission @smoke @loan @module @form (2.0m)
  ✓  245 [chromium] › tests\e2e\loan.refactored.spec.ts:147:3 › Loan Module - Best Practices Architecture › 06 - Loan Application Filtering and Search @loan @module @filter (1.4m)
  ✓  246 [chromium] › tests\e2e\loan.refactored.spec.ts:172:3 › Loan Module - Best Practices Architecture › 07 - View Loan Application Details @loan @module @detail (1.4m)
  ✓  247 [chromium] › tests\e2e\loan.refactored.spec.ts:199:3 › Loan Module - Best Practices Architecture › 08 - Loan Status Filter @loan @module @filter (1.4m)
  ✓  248 [chromium] › tests\e2e\loan.refactored.spec.ts:225:3 › Loan Module - Best Practices Architecture › 09 - Loan Approval Workflow (if admin) @loan @module @approval (1.7m)
  ✘  249 …m] › tests\e2e\loan.refactored.spec.ts:251:3 › Loan Module - Best Practices Architecture › 10 - Module Navigation - Return to Dashboard @smoke @loan @module @navigation (1.5m)
  ✘  250 [chromium] › tests\e2e\loan.refactored.spec.ts:271:3 › Loan Module - Best Practices Architecture › 11 - Module Console Error Check @loan @module @debugging (1.6m)
  ✘  251 …actored.spec.ts:53:9 › 01 - Staff Dashboard Responsive Behavior - Mobile Viewports › 01-1 - Single column layout on 320px (iPhone SE) @responsive @mobile @layout @smoke (1.5m)
Console errors detected: [
  'Failed to send logs: TypeError: Failed to fetch\n' +
    '    at flushLogs (http://localhost:8000/login:57:9)'
]
  ✘  252 …factored.spec.ts:53:9 › 01 - Staff Dashboard Responsive Behavior - Mobile Viewports › 01-2 - Single column layout on 375px (iPhone 8) @responsive @mobile @layout @smoke (1.3m)
  ✘  253 …spec.ts:53:9 › 01 - Staff Dashboard Responsive Behavior - Mobile Viewports › 01-3 - Single column layout on 414px (iPhone 11 Pro Max) @responsive @mobile @layout @smoke (1.3m)
  ✓  254 …actored.spec.ts:96:5 › 01 - Staff Dashboard Responsive Behavior - Mobile Viewports › 01-04 - Quick action buttons stack vertically on mobile @responsive @mobile @layout (1.4m)
  ✘  255 …ed.spec.ts:118:5 › 01 - Staff Dashboard Responsive Behavior - Mobile Viewports › 01-05 - Recent activity displays in single column on mobile @responsive @mobile @layout (1.6m)
  ✓  256 …sive.refactored.spec.ts:144:9 › 02 - Staff Dashboard Responsive Behavior - Tablet Viewports › 02-1 - Two column layout on 768px (iPad Mini) @responsive @tablet @layout (59.8s)
  ✓  257 …onsive.refactored.spec.ts:144:9 › 02 - Staff Dashboard Responsive Behavior - Tablet Viewports › 02-2 - Two column layout on 820px (iPad Air) @responsive @tablet @layout (1.2m)
  ✓  258 …nsive.refactored.spec.ts:144:9 › 02 - Staff Dashboard Responsive Behavior - Tablet Viewports › 02-3 - Two column layout on 1000px (iPad Pro) @responsive @tablet @layout (1.0m)
  ✘  259 …ctored.spec.ts:184:5 › 02 - Staff Dashboard Responsive Behavior - Tablet Viewports › 02-04 - Recent activity displays in 2 columns on tablet @responsive @tablet @layout (1.5m)
  ✓  260 …ored.spec.ts:205:9 › 03 - Staff Dashboard Responsive Behavior - Desktop Viewports › 03-1 - Four column layout on 1280px (Desktop HD) @responsive @desktop @layout @smoke (1.0m)
  ✓  261 …pec.ts:205:9 › 03 - Staff Dashboard Responsive Behavior - Desktop Viewports › 03-2 - Four column layout on 1920px (Desktop Full HD) @responsive @desktop @layout @smoke (54.0s)
  ✓  262 …ored.spec.ts:205:9 › 03 - Staff Dashboard Responsive Behavior - Desktop Viewports › 03-3 - Four column layout on 2560px (Desktop 4K) @responsive @desktop @layout @smoke (1.1m)
  ✘  263 …ed.spec.ts:250:5 › 03 - Staff Dashboard Responsive Behavior - Desktop Viewports › 03-04 - All cards display in single row on desktop @responsive @desktop @layout @smoke (1.3m)
  ✓  264 …tored.spec.ts:273:5 › 03 - Staff Dashboard Responsive Behavior - Desktop Viewports › 03-05 - Quick actions display in single row on desktop @responsive @desktop @layout (1.3m)
  ✓  265 …rd.responsive.refactored.spec.ts:298:5 › 04 - Touch Target Compliance (WCAG 2.2 AA) › 04-01 - Minimum 44x44px touch targets on mobile @accessibility @wcag @touch @smoke (1.2m)
  ✘  266 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:330:5 › 05 - Performance and Loading › 05-01 - Quick load on desktop viewport @performance (1.8m)
  ✘  267 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:341:5 › 05 - Performance and Loading › 05-02 - Quick load on mobile viewport @performance (1.5m)
     268 …aff-dashboard.responsive.refactored.spec.ts:356:5 › 06 - No Horizontal Scroll (WCAG 2.2 AA) › 06-01 - No horizontal scroll on all viewports @accessibility @wcag @layout @smoke
     269 [chromium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:388:5 › 07 - Content Readability › 07-01 - Readable text on mobile viewport @accessibility @readability

📐 Testing iPhone SE (320×568)

📐 Testing iPhone 8 (375×667)

📐 Testing iPhone 11 Pro Max (414×896)

📐 Testing iPad Mini (768×1024)

📐 Testing iPad Air (820×1180)

📐 Testing iPad Pro (1000×1366)

📐 Testing Desktop HD (1280×720)

📐 Testing Desktop Full HD (1920×1080)

📐 Testing Desktop 4K (2560×1440)
  ✓  268 …hboard.responsive.refactored.spec.ts:356:5 › 06 - No Horizontal Scroll (WCAG 2.2 AA) › 06-01 - No horizontal scroll on all viewports @accessibility @wcag @layout @smoke (1.6m)
  ✘  270 …s\e2e\staff-dashboard.responsive.refactored.spec.ts:408:5 › 08 - Responsive Image and Icon Handling › 08-01 - Icons display properly on all viewports @responsive @icons (1.3m)
  ✓  269 …omium] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:388:5 › 07 - Content Readability › 07-01 - Readable text on mobile viewport @accessibility @readability (1.3m)
  ✓  271 … tests\e2e\staff-dashboard.responsive.refactored.spec.ts:431:5 › 09 - Viewport Transition Smoothness › 09-01 - Graceful viewport resize handling @responsive @transition (1.1m)
     272 …] › tests\e2e\staff-dashboard.responsive.refactored.spec.ts:455:5 › 10 - Accessibility on Different Viewports › 10-01 - Focus indicators on all viewports @accessibility @focus
     273 …timized.refactored.spec.ts:28:3 › Staff User Optimized Complete Journey › Complete staff journey: Welcome to Logout (optimized single session) @smoke @staff @optimization @e2e

🚀 Starting optimized staff flow test

📸 Step 1/15: Welcome page
  ✓  274 …um] › tests\e2e\staff-flow-refactored.spec.ts:26:3 › Staff User Complete Flow - Best Practices Architecture › 01 - Welcome Page Accessibility Check @smoke @staff @flow (23.2s)
✅ Step 1 complete

📸 Step 2/15: Navigate to login
  ✓  275 …m] › tests\e2e\staff-flow-refactored.spec.ts:43:3 › Staff User Complete Flow - Best Practices Architecture › 02 - Navigate to Staff Login @smoke @staff @authentication (36.8s)
  ✓  276 …ests\e2e\staff-flow-refactored.spec.ts:63:3 › Staff User Complete Flow - Best Practices Architecture › 03 - Login Page Form Accessibility @smoke @staff @authentication (16.0s)
  ✘  277 … › tests\e2e\staff-flow-refactored.spec.ts:79:3 › Staff User Complete Flow - Best Practices Architecture › 04 - Successful Authentication @smoke @staff @authentication (55.2s)
  ✘  278 … › tests\e2e\staff-flow-refactored.spec.ts:94:3 › Staff User Complete Flow - Best Practices Architecture › 05 - Dashboard Main View After Login @smoke @staff @dashboard (1.5m)
  ✘  279 …m] › tests\e2e\staff-flow-refactored.spec.ts:113:3 › Staff User Complete Flow - Best Practices Architecture › 06 - Dashboard Quick Actions Interaction @staff @dashboard (1.1m)
  ✘  280 …› tests\e2e\staff-flow-refactored.spec.ts:127:3 › Staff User Complete Flow - Best Practices Architecture › 07 - Navigate to Helpdesk Module @staff @helpdesk @navigation (1.1m)
  ✘  281 …romium] › tests\e2e\staff-flow-refactored.spec.ts:142:3 › Staff User Complete Flow - Best Practices Architecture › 08 - Navigate to Loan Module @staff @loan @navigation (1.3m)
  ✘  282 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:157:3 › Staff User Complete Flow - Best Practices Architecture › 09 - View User Profile @staff @profile (1.8m)
     283 [chromium] › tests\e2e\staff-flow-refactored.spec.ts:174:3 › Staff User Complete Flow - Best Practices Architecture › 10 - Complete Logout @smoke @staff @authentication
     284 [chromium] › tests\e2e\staff-flow.best-practices.spec.ts:32:3 › Staff User Complete Flow - Best Practices › 01 - Welcome page is accessible
