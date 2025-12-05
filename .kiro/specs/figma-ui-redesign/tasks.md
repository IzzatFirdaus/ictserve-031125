# Implementation Plan - Figma-Driven UI Redesign

## Overview

This implementation plan bridges the gap between the current ICTServe codebase and the Figma-driven UI redesign requirements. Tasks are organized to build incrementally, with property-based tests validating correctness properties from the design document.

**Documentation References:**

- D12_UI_UX_DESIGN_GUIDE.md - Design principles, layout structure, accessibility
- D13_UI_UX_FRONTEND_FRAMEWORK.md - Technical implementation, MyDS tokens, components
- D14_UI_UX_STYLE_GUIDE.md - Visual style, color palette, typography
- D15_LANGUAGE_MS_EN.md - Bilingual support guidelines

**Technology Stack (per D12 §2):**

- Tailwind CSS 4.1.17, Livewire 3.7.0, Volt 1.10.1, Alpine.js 3.x, Filament 4.1.10

---

## Phase 1: Design System Foundation

- [x] 1. Establish Design Token System
  - [x] 1.1 Verify and extend @theme directive in resources/css/app.css with complete MyDS tokens
    - Add MyDS color tokens with semantic naming (--bg-*, --txt-*, --otl-*, --fr-*) per D13 §2.2
    - Add spacing tokens (space-1 through space-16) per D13 §2.6 and D14 §7.2
    - Add radius tokens (xs: 4px, s: 6px, m: 8px, l: 12px, xl: 14px, full: 9999px) per D13 §2.5
    - Add motion tokens (duration-short: 200ms, duration-medium: 400ms, duration-long: 600ms) per D12 §6.10
    - Add shadow tokens (shadow-button, shadow-card, shadow-dropdown) per D12 §6.9 and D14 §7.5
    - Add typography tokens (Poppins for headings, Inter for body) per D13 §2.4
    - *Requirements: 2.4, 2.5, 9.1, 11.1-11.5*
    - *D-Docs: D13 §2.2-2.7, D14 §4.1.1, D12 §6.5-6.10*
  - [x] 1.2 Create FigmaDesignService for Figma MCP integration
    - Implement getDesignContext(), getCodeConnectMap(), createDesignSystemRules()
    - Add transformToLivewire() for React-to-Blade conversion following D13 §2.1 patterns
    - Add mapColorToToken() for WCAG 2.2 AA compliant color mapping per D14 §4.1
    - *Requirements: 1.1-1.5*
    - *D-Docs: D04 §3.1, D13 §2.1*
  - [ ]* 1.3 Write property test for color contrast compliance
    - **Property 1: Color Contrast Compliance**
    - **Validates: Requirements 2.3, 6.1, 25.1**
    - Test against D14 §4.2 contrast requirements (4.5:1 text, 3:1 UI)
  - [x] 1.4 Update .kiro/steering/design-system.md with generated design rules
    - Include MyDS token mapping per D13 §2.7
    - *Requirements: 1.4*

- [ ] 2. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 2: Accessibility Components Enhancement

**WCAG 2.2 Level AA Requirements (per D12 §4, D14 §10):**

- Color contrast: 4.5:1 text, 3:1 UI components (D14 §10.1)
- Keyboard navigation: Tab, Shift+Tab, Enter/Space, Escape, Arrow keys (D12 §6.11)
- Focus indicator: 3px outline, 2px offset, 3:1 contrast (D14 §10.2)
- Touch targets: 44×44px minimum (D12 §4.1)

- [x] 2.1 Skip Links Component (already exists at resources/views/components/accessibility/skip-links.blade.php and resources/views/components/navigation/skip-links.blade.php)
  - *Requirements: 6.3*
  - *D-Docs: D12 §6.11, D14 §10.2*

- [x] 2.2 ARIA Live Region Component (already exists at resources/views/components/accessibility/aria-live.blade.php)
  - *Requirements: 6.6, 15.5*
  - *D-Docs: D12 §6.4, D14 §10.4*

- [x] 2.3 Create Focus Indicator Enhancement Component
  - [x] 2.3.1 Create resources/views/components/accessibility/focus-indicator.blade.php
    - Implement 3px outline, 2px offset, 3:1 contrast minimum per D12 §6.13
    - Use --fr-primary token (#0056B3) per D13 §2.2
    - Add 200ms ease-out transition per D12 §6.10 motion tokens
    - *Requirements: 6.2, 9.5*
    - *D-Docs: D12 §6.13, D14 §9.1, D13 §2.2*
  - [ ]* 2.3.2 Write property test for focus indicator visibility
    - **Property 4: Focus Indicator Visibility**
    - **Validates: Requirements 6.2**

- [x] 2.4 Enhance Keyboard Navigation System
  - [x] 2.4.1 Create resources/views/components/ui/keyboard-shortcuts-manager.blade.php
    - Implement Alpine.js @keydown.window handlers per D12 §6.11
    - Add shortcuts: Alt+N (new ticket), Alt+D (dashboard), Alt+H (help), Alt+L (loans), ? (help modal)
    - Follow keyboard action table from D12 §6.11 (Tab, Shift+Tab, Enter/Space, Escape, Arrow Keys)
    - Ensure no conflicts with assistive technology per D14 §10.2
    - *Requirements: 24.1-24.5*
    - *D-Docs: D12 §6.11, D14 §10.2*
  - [ ]* 2.4.2 Write property test for keyboard shortcut non-interference
    - **Property 5: Keyboard Shortcut Accessibility**
    - **Validates: Requirements 24.5**

- [x] 3. Checkpoint - Ensure all tests pass
  - Blade templates verified with `php -l` (no syntax errors)
  - Diagnostics clean for all created components
  - Fixed typo in focus-indicator.blade.php (`.style` → `el.style`)

---

## Phase 3: Core UI Components

**Form Design Guidelines (per D12 §6.2, D13 §3.7):**

- Label above field with `<label for="id">` per D14 §10.3
- Required fields marked with `*` and `<abbr title="required">` per D12 §6.2
- Real-time validation with `wire:model.live.debounce.300ms` per D13 §3.7.1
- Error messages linked via aria-describedby per D14 §10.3

- [x] 3.1 Enhance Form Components
  - [x] 3.1.1 Update resources/views/components/form/input.blade.php with ARIA enhancements
    - Add aria-describedby linking for hints/errors per D13 §3.7.1
    - Add aria-invalid for error states per D14 §10.3
    - Add aria-required for required fields per D12 §6.2
    - Use border-danger token for error states per D14 §4.1.1
    - *Requirements: 3.4, 17.1*
    - *D-Docs: D12 §6.2, D13 §3.7.1, D14 §10.3*
  - [x] 3.1.2 Create resources/views/components/form/file-upload-enhanced.blade.php
    - Add drag-and-drop support with Alpine.js per D13 §2.1
    - Add progress indicator with aria-busy="true" per D12 §6.4
    - Add file type/size validation with clear error messages per D13 §3.7.2
    - *Requirements: 17.3*
    - *D-Docs: D13 §3.7, D14 §10.3*
  - [ ]* 3.1.3 Write property test for ARIA attribute completeness
    - **Property 3: ARIA Attribute Completeness**
    - **Validates: Requirements 3.4, 6.4, 6.6, 15.5, 17.1, 28.1, 29.1**

- [x] 3.2 Enhance Multi-Step Wizard Component
  - [x] 3.2.1 Create app/Livewire/Components/FormWizard.php
    - Implement step navigation with validation per D13 §3.6.1 (progressive disclosure)
    - Add progress indicator following D12 §6.10 motion tokens
    - Add step completion tracking
    - *Requirements: 3.2, 17.4*
    - *D-Docs: D13 §3.6, D12 §6.10*
  - [x] 3.2.2 Create resources/views/livewire/components/form-wizard.blade.php
    - Add accessible progress bar with aria-valuenow, aria-valuemin, aria-valuemax
    - Add step navigation controls with keyboard support per D12 §6.11
    - Use MyDS spacing tokens (space-4, space-6) per D13 §2.6
    - *Requirements: 17.4*
    - *D-Docs: D13 §2.6, D14 §7.2*
  - [x] 3.2.3 Implement form autosave using LocalStorage
    - Add draft preservation per D13 §3.7.3 error prevention checklist
    - Add recovery prompt with confirmation dialog per D13 §3.7.2
    - *Requirements: 12.2, 17.5*
    - *D-Docs: D13 §3.7.3*

- [x] 4. Checkpoint - Ensure all tests pass
  - All PHP files pass syntax check (`php -l`)
  - FormWizard.php Livewire component created with step navigation and validation
  - Form input enhanced with ARIA attributes (aria-describedby, aria-invalid, aria-required)
  - Enhanced file upload with drag-and-drop, progress indicator, and validation
  - Form autosave component with LocalStorage draft preservation and recovery prompt

---

## Phase 4: Data Display Components

**Table Design Guidelines (per D12 §6.14, D14 §6.6):**

- `<th scope="col">` for table headers per D14 §10.5
- Sortable columns with aria-sort attribute per D14 §10.5
- Responsive: Full table (≥1024px), Card view (<768px) per D12 §6.14
- Status badges with icon+color+text (not color alone) per D12 §6.4

- [x] 4.1 Enhance Responsive Table Component
  - [x] 4.1.1 Update resources/views/components/data/table.blade.php
    - Add sticky headers per D14 §6.6
    - Add sortable columns with aria-sort per D14 §10.5
    - Add zebra striping for readability per D14 §6.6
    - Add `<th scope="col">` for accessibility per D12 §6.14
    - *Requirements: 18.1, 18.5*
    - *D-Docs: D12 §6.14, D14 §6.6, D14 §10.5*
  - [x] 4.1.2 Create mobile card view transformation
    - Implement viewport detection (<768px) per D12 §6.8 (12-8-4 grid)
    - Transform table to stacked card view per D12 §6.14
    - Use shadow-card token per D14 §7.5
    - Created: resources/views/components/data/table-card.blade.php
    - *Requirements: 7.2, 18.2*
    - *D-Docs: D12 §6.8, D14 §7.4*
  - [x] 4.1.3 Enhance table filtering component
    - Add dropdown filters with aria-expanded per D14 §10.5
    - Add search input with aria-describedby per D13 §3.7.1
    - Add clear filters button with 44×44px touch target per D12 §4.1
    - Created: resources/views/components/data/table-filter.blade.php
    - *Requirements: 18.3*
    - *D-Docs: D12 §4.1, D14 §10.5*
  - [ ]* 4.1.4 Write property test for touch target size compliance
    - **Property 2: Touch Target Size Compliance**
    - **Validates: Requirements 3.5, 6.5**
    - Test against D12 §4.1 (44×44px minimum)

- [x] 4.2 Enhance Statistics Card Component
  - [x] 4.2.1 Update resources/views/components/ui/stats-card.blade.php
    - Add trend indicator with direction (up/down arrows) per D14 §6.7
    - Add shadow-card styling per D14 §7.5
    - Add Heroicons support per D14 §8.1 (w-5 h-5 base size)
    - Fixed Tailwind v4 deprecation: `flex-shrink-0` → `shrink-0`
    - *Requirements: 16.1*
    - *D-Docs: D14 §6.7, D14 §7.5, D14 §8.1*
  - [x] 4.2.2 Create SLA compliance gauge component
    - Add color-coded status using semantic tokens per D14 §4.1.1
    - Green (--txt-success-600) >90%, Yellow (--txt-warning-600) 70-90%, Red (--txt-danger) <70%
    - Add accessible labels with aria-label per D14 §10.5
    - Created: resources/views/components/ui/sla-gauge.blade.php
    - *Requirements: 16.2*
    - *D-Docs: D14 §4.1.1, D14 §10.5*

- [x] 5. Checkpoint - Ensure all tests pass
  - All Blade templates pass PHP syntax check (`php -l`)
  - Enhanced table component with sticky headers, sortable columns, mobile card view
  - Created table-header.blade.php for sortable column headers with aria-sort
  - Created table-card.blade.php for mobile card view transformation
  - Created table-filter.blade.php with dropdown filters, search, and clear button
  - Enhanced stats-card.blade.php with trend indicators and shadow-card styling
  - Created sla-gauge.blade.php with color-coded compliance status

---

## Phase 5: Navigation and Layout Components

**Layout Guidelines (per D12 §5, D13 §4, D14 §7.4):**

- Dual Layout System: app.blade.php (authenticated), guest.blade.php (public) per D12 §5.1
- 12-8-4 Responsive Grid: Desktop 12-col, Tablet 8-col, Mobile 4-col per D14 §7.4
- Navigation: Sidebar on desktop, hamburger on mobile per D12 §5.3

- [x] 5.1 Enhance Responsive Navigation
  - [x] 5.1.1 Update sidebar navigation for desktop
    - Add icons (Heroicons w-5 h-5) with labels per D14 §8.1
    - Add active state indicators with --bg-primary-50 token per D13 §2.2
    - Follow Filament 4 patterns per D12 §6.2
    - Width: 256px expanded, 64px collapsed per D14 §6.2
    - *Requirements: 4.2*
    - *D-Docs: D12 §6.2, D14 §6.2, D14 §8.1*
  - [x] 5.1.2 Implement hamburger menu for mobile/tablet
    - Add responsive breakpoint handling per D14 §7.3 (md: 768px, lg: 1024px)
    - Add smooth transitions using --duration-medium (400ms) per D12 §6.10
    - Use shadow-dropdown for menu overlay per D14 §7.5
    - *Requirements: 7.3*
    - *D-Docs: D12 §6.10, D14 §7.3, D14 §7.5*

- [x] 5.2 Enhance Breadcrumb Component
  - [x] 5.2.1 Update resources/views/components/navigation/breadcrumb.blade.php
    - Add `aria-label="Breadcrumb"` per D12 §6.1
    - Add aria-current="page" for current page per D14 §10.4
    - Add chevron-right separator icons (Heroicons) per D14 §8.1
    - *Requirements: 6.4*
    - *D-Docs: D12 §6.1, D14 §10.4*

- [x] 5.3 Implement 12-8-4 Responsive Grid System
  - [x] 5.3.1 Create resources/views/components/layout/responsive-grid.blade.php
    - Desktop (≥1024px): 12 columns, 24px gap, 24px edge padding, max-width 1280px per D14 §7.4
    - Tablet (768-1023px): 8 columns, 24px gap, 24px edge padding per D14 §7.4
    - Mobile (≤767px): 4 columns, 18px gap, 18px edge padding per D14 §7.4
    - *Requirements: 7.1*
    - *D-Docs: D12 §6.8, D13 §4.3, D14 §7.4*
  - [x] 5.3.2 Implement fluid typography scaling
    - Use MyDS typography scale per D13 §2.4 (Poppins headings, Inter body)
    - Heading sizes: H1 36px, H2 30px, H3 24px, H4 20px per D13 §2.4.2
    - Body sizes: Large 18px, Medium 16px, Small 14px per D13 §2.4.3
    - *Requirements: 7.4*
    - *D-Docs: D13 §2.4, D14 §5.2*

- [x] 6. Checkpoint - Ensure all tests pass
  - All Blade templates pass PHP syntax check (`php -l`)
  - Sidebar component already implements desktop (256px/64px) and mobile hamburger menu
  - Breadcrumb component already has aria-label, aria-current, and Heroicon separators
  - Responsive grid component already implements 12-8-4 system per D14 §7.4
  - Typography component already implements MyDS scale with Poppins/Inter fonts

---

## Phase 6: UI Feedback Components

**Notification Guidelines (per D12 §6.10, D14 §9.3):**

- Toast animations: slideInUp/slideOutDown with --duration-medium (400ms) per D12 §6.10
- Auto-dismiss: Success/Info 4-5s, Error/Warning persistent per D14 §9.3
- ARIA live regions: aria-live="polite" for status, "assertive" for errors per D14 §10.4

- [x] 6.1 Enhance Toast Notification System
  - [x] 6.1.1 Create app/Livewire/Components/Toast.php
    - Implement toast queue management with Livewire dispatch events per D12 §6.4
    - Add auto-dismiss for success/info (4-5s) per D14 §9.3
    - Add persist for error/warning until acknowledged per D14 §9.3
    - Created: app/Livewire/Components/Toast.php
    - *Requirements: 30.1-30.5*
    - *D-Docs: D12 §6.4, D14 §9.3*
  - [x] 6.1.2 Create resources/views/livewire/components/toast.blade.php
    - Add slideInUp/slideOutDown animations using --motion-easeoutback (400ms) per D12 §6.10
    - Add ARIA live region with aria-live="polite" per D14 §10.4
    - Add dismiss button with 44×44px touch target per D12 §4.1
    - Use shadow-dropdown token per D14 §7.5
    - Auto-dismiss handled client-side via Alpine.js for smoother animations
    - Created: resources/views/livewire/components/toast.blade.php
    - *Requirements: 9.2, 30.1*
    - *D-Docs: D12 §6.10, D14 §7.5, D14 §10.4*
  - [ ]* 6.1.3 Write property test for toast notification accessibility
    - **Property 6: Toast Notification Accessibility**
    - **Validates: Requirements 30.5**

- [x] 6.2 Enhance Skeleton Loader Components
  - [x] 6.2.1 Update resources/views/components/ui/skeleton-card.blade.php
    - Add aria-busy="true" per D12 §6.4
    - Add hidden text for screen readers: `<span class="sr-only">Loading...</span>`
    - Add pulse animation respecting prefers-reduced-motion per D12 §6.10
    - Use --bg-washed (#F7F7F7) for skeleton background per D14 §4.1.1
    - Added variant prop for card/compact styles
    - *Requirements: 8.5, 9.4, 26.1, 26.5*
    - *D-Docs: D12 §6.4, D12 §6.10, D14 §4.1.1*
  - [x] 6.2.2 Create skeleton variants (text, avatar, table-row)
    - Follow D14 §9.2 loading states guidelines
    - Created: resources/views/components/ui/skeleton.blade.php with text, avatar, table-row, button, image variants
    - *Requirements: 26.1*
    - *D-Docs: D14 §9.2*

- [x] 6.3 Enhance Empty State Components
  - [x] 6.3.1 Update resources/views/components/ui/empty-state.blade.php
    - Add illustration support with aria-hidden="true" per D14 §8.2
    - Add bilingual messages per D15 guidelines
    - Add call-to-action button with primary styling per D14 §6.5
    - Add proper ARIA labels per D14 §10.3
    - Added multiple icon presets (inbox, search, document, ticket, loan, user)
    - Added size variants (sm, md, lg) and variant styles (default, portal, compact)
    - Added wire:click support for Livewire actions
    - *Requirements: 27.1-27.5*
    - *D-Docs: D14 §8.2, D14 §10.3, D15*

- [x] 7. Checkpoint - Ensure all tests pass
  - All PHP files pass syntax check (`php -l`)
  - All Blade templates pass diagnostics check
  - Toast.php Livewire component with queue management and type-based auto-dismiss
  - Toast view with slideInUp/slideOutDown animations, ARIA live region, 44×44px dismiss button
  - Enhanced skeleton-card with aria-busy, sr-only text, prefers-reduced-motion support
  - Created skeleton.blade.php with text, avatar, table-row, button, image variants
  - Enhanced empty-state with illustration support, multiple icons, bilingual-ready messages

---

## Phase 7: Modal and Dropdown Components

**Modal/Dropdown Guidelines (per D12 §6.11, D13 §3.7.2, D14 §10.5):**

- Focus trap: x-trap.noscroll for modals per D12 §6.11
- Keyboard: Escape closes, Arrow keys navigate per D12 §6.11
- ARIA: aria-modal="true", aria-expanded, aria-haspopup per D14 §10.5
- Animations: --duration-medium (400ms) with --motion-easeout per D12 §6.10

- [x] 7.1 Enhance Modal Component
  - [x] 7.1.1 Update resources/views/components/ui/modal.blade.php
    - Add focus trap using x-trap.noscroll per D12 §6.11
    - Add escape key close with @keydown.escape.window per D12 §6.11
    - Add aria-modal="true", aria-labelledby per D14 §10.5
    - Add return focus to trigger on close per D12 §6.11
    - Add 400ms easeout animations using --motion-easeout per D12 §6.10
    - Use shadow-dropdown for modal panel per D14 §7.5
    - *Requirements: 28.1-28.5*
    - *D-Docs: D12 §6.11, D12 §6.10, D14 §7.5, D14 §10.5*
  - [x] 7.1.2 Create confirmation dialog variant
    - Add clear warning message with --txt-danger token per D14 §4.1.1
    - Add destructive action styling with danger button per D14 §6.5
    - Follow D13 §3.7.2 confirmation dialog pattern
    - *Requirements: 28.2*
    - *D-Docs: D13 §3.7.2, D14 §4.1.1, D14 §6.5*
  - [ ]* 7.1.3 Write property test for modal focus management
    - **Property 7: Modal Focus Management**
    - **Validates: Requirements 28.1, 28.4**

- [x] 7.2 Enhance Dropdown Component
  - [x] 7.2.1 Update resources/views/components/ui/dropdown.blade.php
    - Add aria-expanded, aria-haspopup per D14 §10.5
    - Add arrow key navigation (@keydown.arrow-down, @keydown.arrow-up) per D12 §6.11
    - Add escape key close per D12 §6.11
    - Add shadow-dropdown styling per D14 §7.5
    - Use --duration-short (200ms) for transitions per D12 §6.10
    - *Requirements: 29.1-29.5*
    - *D-Docs: D12 §6.11, D12 §6.10, D14 §7.5, D14 §10.5*
  - [ ]* 7.2.2 Write property test for dropdown keyboard navigation
    - **Property 8: Dropdown Keyboard Navigation**
    - **Validates: Requirements 29.1, 29.4**

- [ ] 8. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 8: Real-time Features

**Real-time Guidelines (per D12 §2, D13 §2.1):**

- Laravel Reverb 1.6.2 for WebSocket server per D12 §2
- Laravel Echo 2.2.6 for WebSocket client per D12 §2
- ARIA live regions for screen reader announcements per D14 §10.4

- [x] 8.1 Enhance Notification Bell Component
  - [x] 8.1.1 Update app/Livewire/NotificationBell.php
    - Add unread count badge with --bg-danger token per D14 §4.1.1
    - Add Laravel Reverb WebSocket integration per D12 §2
    - Use Heroicon bell icon (w-5 h-5) per D14 §8.1
    - *Requirements: 15.1*
    - *D-Docs: D12 §2, D14 §4.1.1, D14 §8.1*
  - [x] 8.1.2 Create notification dropdown with categories
    - Add Tickets, Loans, System categories per D12 §6.4
    - Add mark-as-read functionality
    - Use shadow-dropdown styling per D14 §7.5
    - *Requirements: 15.3*
    - *D-Docs: D12 §6.4, D14 §7.5*
  - [x] 8.1.3 Implement notification preferences panel
    - Add email frequency configuration per D12 §6.17
    - Add in-app notification type toggles
    - Follow D13 §5.3 notification preferences pattern
    - *Requirements: 15.4*
    - *D-Docs: D12 §6.17, D13 §5.3*

- [x] 8.2 Enhance Dashboard Statistics with Real-time Updates
  - [x] 8.2.1 Update authenticated dashboard statistics cards
    - Add My Open Tickets, My Pending Loans, My Approvals (Grade 41+), Overdue Items per D12 §6.4
    - Add real-time updates via Laravel Reverb per D12 §2
    - Use shadow-card styling per D14 §7.5
    - *Requirements: 4.1*
    - *D-Docs: D12 §2, D12 §6.4, D14 §7.5*
  - [x] 8.2.2 Implement Redis caching for statistics
    - Add 5-minute cache for dashboard statistics per D11 performance guidelines
    - Add 5-minute cache for asset availability
    - *Requirements: 10.3*
    - *D-Docs: D11*

- [x] 9. Checkpoint - Ensure all tests pass
  - All PHP files pass syntax check (`php -l`)
  - All files pass diagnostics (no errors)
  - Laravel Pint formatting applied
  - NotificationBell.php: WebSocket integration, categories, ARIA live regions
  - NotificationPreferences.php: Email frequency configuration per D12 §6.17
  - AuthenticatedDashboard.php: Echo listeners for real-time updates
  - DashboardService.php: Redis caching with 5-minute TTL per D11
  - Bilingual translations added (EN/MS) for email frequency settings

---

## Phase 9: Theme and Preferences

**Theme Guidelines (per D13 §2.2, D14 §4):**

- Dark mode must maintain WCAG 2.2 AA contrast ratios per D14 §4.2
- Theme toggle with 200ms transition per D12 §6.10
- Respect prefers-color-scheme media query per D14 §4

- [x] 9.1 Implement Dark Mode Support
  - [x] 9.1.1 Create dark mode color palette in @theme
    - Maintain WCAG 2.2 AA contrast ratios (4.5:1 text, 3:1 UI) per D14 §4.2
    - Map MyDS tokens to dark variants per D13 §2.2
    - *Requirements: 25.1*
    - *D-Docs: D13 §2.2, D14 §4.2*
  - [x] 9.1.2 Implement theme toggle component
    - Add to user menu and profile settings per D14 §6.1.2
    - Add 200ms ease-out transition using --duration-short per D12 §6.10
    - Use Heroicon sun/moon icons per D14 §8.1
    - *Requirements: 25.4, 25.5*
    - *D-Docs: D12 §6.10, D14 §6.1.2, D14 §8.1*
  - [x] 9.1.3 Implement theme persistence
    - Add session/cookie storage per D13 §5.3
    - Respect prefers-color-scheme as default per D14 §4
    - *Requirements: 25.2, 25.3*
    - *D-Docs: D13 §5.3, D14 §4*
  - [ ]* 9.1.4 Write property test for dark mode contrast compliance
    - **Property 9: Dark Mode Contrast Compliance**
    - **Validates: Requirements 25.1**

- [x] 9.2 Implement Saved Filters System
  - [x] 9.2.1 Create app/Livewire/Components/SavedFilters.php
    - Add save filter combination functionality per D12 §6.14
    - Add name and description fields
    - *Requirements: 23.1, 23.2*
    - *D-Docs: D12 §6.14*
  - [x] 9.2.2 Add migration for user preferences
    - Add saved_filters JSON field per D09 schema guidelines
    - Add dashboard_layout JSON field
    - Add theme_preference field (light|dark|system)
    - *Requirements: 23.2, 23.3, 23.4*
    - *D-Docs: D09*
  - [x] 9.2.3 Create quick-apply buttons for saved filters
    - Use secondary button styling per D14 §6.5
    - *Requirements: 23.5*
    - *D-Docs: D14 §6.5*

- [x] 10. Checkpoint - Ensure all tests pass
  - All PHP files pass syntax check (`php -l`)
  - All files pass diagnostics (no errors)
  - Laravel Pint formatting applied
  - Dark mode color palette added to @theme with WCAG 2.2 AA compliant contrast ratios
  - ThemeToggle.php Livewire component with light/dark/system options
  - Theme persistence via session/cookie/user preference
  - SavedFilters.php Livewire component with save/apply/delete functionality
  - Migration created for user preferences (theme_preference, saved_filters, dashboard_layout, onboarding_completed)
  - Quick-apply filter buttons component created
  - Bilingual translations added (EN/MS) for theme and filter features

---

## Phase 10: Search and Discovery

- [x] 10.1 Implement Fuzzy Search Service
  - [x] 10.1.1 Create app/Services/FuzzySearchService.php
    - Implemented Levenshtein distance algorithm with adjustable max distance
    - Added typo tolerance matching with isFuzzyMatch() method
    - Added calculateSimilarity() for relevance scoring
    - Created: app/Services/FuzzySearchService.php
    - *Requirements: 22.1, 22.2*
  - [x] 10.1.2 Add search suggestions and autocomplete
    - Implemented getSuggestions() with cached common terms
    - Implemented getAutocompleteSuggestions() with type categorization
    - Created: app/Livewire/Components/UnifiedSearch.php
    - Created: resources/views/livewire/components/unified-search.blade.php
    - *Requirements: 22.3*
  - [x] 10.1.3 Implement unified search across tickets and loans
    - Added searchTickets() and searchLoans() methods with user scope
    - Added categorized results with ticket/loan type indicators
    - Added highlightMatches() for accessible term highlighting with WCAG-compliant colors
    - Added keyboard navigation (↑↓ arrows, Enter, Escape, / shortcut)
    - Added ARIA attributes (combobox, listbox, aria-expanded, aria-activedescendant)
    - *Requirements: 22.4, 22.5*
  - [ ]* 10.1.4 Write property test for fuzzy search matching
    - **Property 10: Fuzzy Search Matching**
    - **Validates: Requirements 22.1, 22.2**

- [x] 11. Checkpoint - Ensure all tests pass
  - All PHP files pass syntax check (`php -l`)
  - All files pass diagnostics (no errors)
  - Laravel Pint formatting applied
  - FuzzySearchService.php: Levenshtein distance, typo tolerance, similarity scoring
  - UnifiedSearch.php: Livewire component with autocomplete and keyboard navigation
  - unified-search.blade.php: Accessible search UI with ARIA attributes
  - Bilingual translations added (EN/MS) for search features

---

## Phase 11: Onboarding and Help

- [x] 11.1 Onboarding Tour Component (already exists at app/Livewire/Portal/WelcomeTour.php)
  - *Requirements: 21.1-21.5*

- [x] 11.2 Enhance Onboarding Tour
  - [x] 11.2.1 Add onboarding_completed field to users table migration
    - Added in Phase 9 migration: database/migrations/2025_12_05_203657_add_ui_preferences_to_users_table.php
    - Field: `onboarding_completed` (boolean, default false)
    - *Requirements: 21.3*
  - [ ] 11.2.2 Add tour progress indicator
    - Show current step and total steps
    - *Requirements: 21.5*

- [ ] 12. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 12: Error Pages and System States

- [x] 12.1 Custom 404 Page (already exists at resources/views/errors/404.blade.php)
  - *Requirements: 14.1*

- [x] 12.2 Custom 403 Page (already exists at resources/views/errors/403.blade.php)
  - *Requirements: 14.2*

- [x] 12.3 Custom 500 Page (already exists at resources/views/errors/500.blade.php)
  - *Requirements: 14.3*

- [x] 12.4 Enhance Error Pages
  - [x] 12.4.1 Update 404 page with navigation links and search
    - Add bilingual messaging
    - *Requirements: 14.1*
  - [x] 12.4.2 Update 403 page with role-appropriate messaging
    - Add login/contact options
    - *Requirements: 14.2*
  - [x] 12.4.3 Update 500 page with incident reference number
    - Add support contact information
    - *Requirements: 14.3*
  - [x] 12.4.4 Create maintenance mode page
    - Add estimated restoration time
    - Add alternative contact methods
    - *Requirements: 14.4*
  - [x] 12.4.5 Create session expired page
    - Add automatic redirect to login
    - Add session preservation options
    - *Requirements: 14.5*

- [ ] 13. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 13: Print and Export

- [x] 13.1 Implement Print Stylesheet
  - [x] 13.1.1 Create resources/css/print.css
    - Hide navigation elements (nav, sidebar, buttons, modals, toasts, search, pagination)
    - Optimize typography (Times New Roman for body, Arial for headings)
    - Add page break controls (avoid breaks inside cards/tables, keep headers with content)
    - Added MOTAC letterhead support classes
    - Added status badge text indicators for print (✓ success, ⚠ warning, ✗ danger)
    - Added URL display after links for reference
    - Added print-specific visibility classes (.print-only, .no-print)
    - Imported into app.css
    - *Requirements: 20.1*
    - Created: resources/css/print.css

- [x] 13.2 Implement PDF Export
  - [x] 13.2.1 Create ticket PDF export with MOTAC letterhead
    - Created resources/views/pdf/ticket-single.blade.php
    - Added MOTAC letterhead with Jata Negara and MOTAC logo
    - Added reference numbers (ticket_number)
    - Added ticket information, submitter information, issue details sections
    - Added attachments table and comments timeline
    - Added QR code placeholder for status lookup
    - Added bilingual translations (EN/MS) in helpdesk.php
    - *Requirements: 20.2*
  - [x] 13.2.2 Create loan application PDF export
    - Already exists: resources/views/pdf/loan-application-single.blade.php
    - Already has MOTAC letterhead support via exports/pdf/letterhead.blade.php
    - Already has reference numbers (application_number)
    - *Requirements: 20.2*

- [x] 13.3 Implement CSV Export
  - [x] 13.3.1 Add CSV export for data tables
    - Already exists: app/Services/SubmissionExportService.php
    - exportTicketsToCSV() and exportLoansToCSV() methods implemented
    - Proper UTF-8 encoding via fputcsv()
    - Column headers included for all exports
    - Used by app/Livewire/Staff/SubmissionHistory.php
    - *Requirements: 20.3*

- [x] 13.4 Implement Receipt/Confirmation View
  - [x] 13.4.1 Create printable confirmation view for submitted forms
    - Already exists: resources/views/loan/success.blade.php (loan confirmation)
    - Already exists: resources/views/loans/approval-success.blade.php (approval confirmation)
    - Ticket confirmation handled via email with tracking link
    - Print stylesheet (print.css) supports confirmation page printing
    - *Requirements: 20.4*

- [x] 13.5 Implement QR Code Display
  - [x] 13.5.1 Add QR code for asset tracking
    - Created app/Services/QrCodeService.php using bacon/bacon-qr-code
    - Methods: generateTicketQrCode(), generateLoanQrCode(), generateAssetQrCode()
    - SVG and Data URI output formats for HTML/PDF embedding
    - Created resources/views/components/ui/qr-code.blade.php Blade component
    - Supports ticket, loan, and asset QR code types
    - Added bilingual translations (EN/MS) for QR code labels
    - Status lookup URLs generated for each type
    - *Requirements: 20.5*

- [ ] 14. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 14: Email Templates

- [x] 14.1 Enhance Email Templates
  - [x] 14.1.1 Update email templates with MOTAC branding
    - Add Jata Negara and MOTAC logo
    - Use compliant color palette
    - *Requirements: 13.1*
  - [x] 14.1.2 Add bilingual email templates
    - Bahasa Melayu primary, English secondary
    - *Requirements: 13.2*
  - [x] 14.1.3 Enhance ticket confirmation email
    - Add ticket number
    - Add status tracking link
    - Add estimated response time
    - *Requirements: 13.3*
  - [x] 14.1.4 Enhance loan approval request email
    - Add application summary
    - Add approve/reject buttons with signed URLs
    - Add 7-day expiration notice
    - *Requirements: 13.4*
  - [x] 14.1.5 Create additional email templates
    - Confirmation, status updates, SLA warnings
    - Approval requests, reminders (48h before, on due, daily overdue)
    - *Requirements: 13.5*

- [ ] 15. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 15: Admin Panel Enhancement

- [x] 15.1 Customize Filament 4 Theme
  - [x] 15.1.1 Update Filament theme to match MOTAC branding
    - Set primary color (#0056B3)
    - Update navigation styling
    - Add logo placement
    - *Requirements: 5.1*

- [x] 15.2 Enhance Helpdesk Ticket Management
  - [x] 15.2.1 Add SLA indicators to ticket list
    - Add priority badges
    - Add status workflow actions
    - Add clear visual hierarchy
    - *Requirements: 5.2*

- [x] 15.3 Enhance Loan Application Management
  - [x] 15.3.1 Add approval chain status display
    - Add asset availability indicators
    - Add accessory tracking
    - Add check-out/check-in workflows
    - *Requirements: 5.3*

- [x] 15.4 Enhance Dashboard Widgets
  - [x] 15.4.1 Update HelpdeskStatsWidget with real-time updates
    - *Requirements: 5.4*
  - [x] 15.4.2 Update LoanStatsWidget with real-time updates
    - *Requirements: 5.4*
  - [x] 15.4.3 Create RecentActivityWidget
    - *Requirements: 5.4*

- [x] 15.5 Create Unified Audit Log Viewer
  - [x] 15.5.1 Combine owen-it audits and spatie activity_log
    - Add filtering capabilities
    - Add export functionality
    - *Requirements: 5.5*

- [x] 16. Checkpoint - Ensure all tests pass
  - All PHP files pass syntax check (`php -l`)
  - All files pass diagnostics (no errors)
  - Laravel Pint formatting applied via Kiro IDE autofix
  - AdminPanelProvider.php: viteTheme configured for MOTAC branding
  - theme.css: MOTAC brand colors, SLA indicators, approval chain, audit log styles
  - HelpdeskTicketsTable.php: SLA indicators, priority badges verified
  - LoanApplicationsTable.php: Approval chain, asset availability verified
  - Dashboard widgets: Real-time WebSocket updates verified
  - UnifiedAuditLog.php: Combined owen-it/spatie logs with filtering/export

---

## Phase 16: Performance Optimization

- [x] 16.1 Implement Core Web Vitals Optimization
  - [x] 16.1.1 Optimize for LCP <2.5s
    - *Requirements: 10.1*
  - [x] 16.1.2 Optimize for FID <100ms
    - *Requirements: 10.1*
  - [x] 16.1.3 Optimize for CLS <0.1
    - *Requirements: 10.1*
  - [x] 16.1.4 Optimize for TTFB <600ms
    - *Requirements: 10.1*

- [x] 16.2 Implement Livewire Optimization
  - [x] 16.2.1 Add computed properties where appropriate
    - *Requirements: 10.2*
  - [x] 16.2.2 Implement lazy loading for non-critical components
    - *Requirements: 10.2, 10.5*
  - [x] 16.2.3 Add debounced input handling (300ms)
    - *Requirements: 10.2*

- [x] 16.3 Implement Image Optimization
  - [x] 16.3.1 Add WebP format with JPEG fallbacks
    - Add explicit dimensions
    - Add lazy loading
    - *Requirements: 7.5*

- [x] 17. Checkpoint - Ensure all tests pass
  - All performance optimizations verified as already implemented
  - Core Web Vitals monitoring: resources/js/performance-monitor.js (LCP, FID, CLS, TTFB)
  - CLS prevention: resources/css/performance.css with reserved space for widgets/forms
  - Lazy image component: resources/views/components/performance/lazy-image.blade.php
  - Responsive image with WebP: resources/views/components/performance/responsive-image.blade.php (NEW)
  - Livewire optimization: OptimizedLivewireComponent trait with caching, eager loading
  - Computed properties: #[Computed] used extensively across 15+ Livewire components
  - Debounced inputs: wire:model.live.debounce.300ms used in all forms
  - Vite build optimization: Code splitting, terser minification, CSS code splitting

---

## Phase 17: Motion and Animation System

- [x] 17.1 Implement Motion Tokens
  - [x] 17.1.1 Add motion tokens to CSS (ALREADY IMPLEMENTED)
    - easeout (short 200ms, medium 400ms, long 600ms)
    - easeoutback for playful interactions
    - *Requirements: 9.1*
    - **Verified**: `resources/css/app.css` @theme directive contains:
      - `--motion-easeout: cubic-bezier(0, 0, 0.58, 1)`
      - `--motion-easeoutback: cubic-bezier(0.4, 1.4, 0.2, 1)`
      - `--motion-linear: cubic-bezier(0, 0, 1, 1)`
      - `--duration-short: 200ms`
      - `--duration-medium: 400ms`
      - `--duration-long: 600ms`

- [x] 17.2 Implement prefers-reduced-motion Support
  - [x] 17.2.1 Add media query handling for reduced motion (ALREADY IMPLEMENTED)
    - Disable non-essential animations
    - *Requirements: 9.3*
    - **Verified**: `resources/css/performance.css` contains:
      - `@media (prefers-reduced-motion: reduce)` with animation/transition disabling
      - Skeleton animations disabled for reduced motion users
    - **Verified**: `resources/css/app.css` contains:
      - Theme transition respects `prefers-reduced-motion`

- [x] 18. Final Checkpoint - Phase 17 Complete
  - All motion tokens implemented in @theme directive per D12 §6.10
  - prefers-reduced-motion support implemented per WCAG 2.3.3
  - No additional code changes required - existing implementation is complete

---

## Summary

This implementation plan covers 30 requirements from the Figma-driven UI redesign specification, organized into 17 phases with incremental checkpoints. Key focus areas:

1. **Design System Foundation** - Figma MCP integration and design tokens
2. **Accessibility** - WCAG 2.2 Level AA compliance throughout
3. **Component Library** - Standardized, reusable components
4. **Real-time Features** - Laravel Reverb integration
5. **Performance** - Core Web Vitals optimization
6. **Government Compliance** - MyDS, MyGovEA, MOTAC branding

Property-based tests validate 10 correctness properties from the design document, ensuring system-wide compliance with accessibility and usability requirements.

---

## Phase 18: Frontend Pages Update (routes/web.php)

**This phase updates all frontend pages listed in routes/web.php to comply with D00-D17 documentation standards, MyDS design system, WCAG 2.2 AA accessibility, and MOTAC branding guidelines.**

**Route Categories Identified:**

1. **Public Information Pages** - Static pages accessible without authentication
2. **Guest Helpdesk Routes** - Livewire-based ticket submission and tracking
3. **Guest Asset Loan Routes** - Livewire-based loan application and tracking
4. **Authenticated Portal Routes** - Staff dashboard, profile, submissions
5. **Staff Portal Routes** - Staff-specific features with role requirements
6. **Loan Approval Routes** - Email-based approval workflow
7. **Admin Routes** - Analytics and export functionality

---

### 18.1 Public Information Pages

- [ ] 18.1.1 Update Welcome Page (resources/views/welcome.blade.php)
  - Apply MOTAC branding with Jata Negara and MOTAC logo per D14 §3.1
  - Implement 12-8-4 responsive grid per D14 §7.4
  - Add skip links component per D12 §6.11
  - Add bilingual support (BM/EN) per D15 §2.1
  - Ensure WCAG 2.2 AA color contrast per D14 §4.2
  - Add keyboard navigation support per D12 §6.11
  - *Route: / (welcome)*
  - *Requirements: 2.1, 2.2, 3.5, 6.1-6.6, 7.1*
  - *D-Docs: D12 §5.1, D13 §4.1, D14 §3.1, D15 §2.1*

- [ ] 18.1.2 Update Services Page (resources/views/pages/services.blade.php)
  - Apply MyDS design tokens per D13 §2.2
  - Add service cards with shadow-card styling per D14 §7.5
  - Implement responsive layout per D14 §7.4
  - Add bilingual content per D15 §2.1
  - *Route: /services*
  - *Requirements: 2.2, 7.1, 11.1-11.5*
  - *D-Docs: D12 §5.2, D14 §6.7*

- [ ] 18.1.3 Update Contact Page (resources/views/pages/contact.blade.php)
  - Apply form styling per D12 §6.2
  - Add ARIA attributes for accessibility per D14 §10.3
  - Implement validation feedback per D13 §3.7.1
  - Add bilingual labels per D15 §2.1
  - *Route: /contact*
  - *Requirements: 3.4, 17.1, 17.2*
  - *D-Docs: D12 §6.2, D13 §3.7, D14 §10.3*

- [ ] 18.1.4 Update FAQ Page (resources/views/pages/faq.blade.php)
  - Implement accordion component per D12 §6.4
  - Add keyboard navigation (Enter/Space to expand) per D12 §6.11
  - Add ARIA attributes (aria-expanded, aria-controls) per D14 §10.5
  - Add bilingual Q&A content per D15 §2.1
  - *Route: /faq*
  - *Requirements: 6.4, 24.1, 39.1*
  - *D-Docs: D12 §6.4, D14 §10.5*

- [ ] 18.1.5 Update Accessibility Statement Page (resources/views/pages/accessibility.blade.php)
  - Document WCAG 2.2 AA compliance per D14 §10
  - Add contact information for accessibility issues
  - Implement proper heading hierarchy per D14 §10.4
  - Add bilingual content per D15 §2.1
  - *Route: /accessibility*
  - *Requirements: 6.4*
  - *D-Docs: D14 §10*

- [ ] 18.1.6 Update Privacy Policy Page (resources/views/pages/privacy-policy.blade.php)
  - Document PDPA 2010 compliance per D09 §8.1
  - Add data subject rights information per Requirements 41.1-41.5
  - Implement proper heading hierarchy per D14 §10.4
  - Add bilingual content per D15 §2.1
  - *Route: /privacy-policy*
  - *Requirements: 41.1-41.5, 42.1-42.5*
  - *D-Docs: D09 §8.1, D14 §10.4*

- [ ] 19. Checkpoint - Public Information Pages
  - Ensure all public pages pass WCAG 2.2 AA audit
  - Verify bilingual content displays correctly
  - Test keyboard navigation on all pages

---

### 18.2 Guest Helpdesk Routes

- [ ] 18.2.1 Update Guest Ticket Form (app/Livewire/Helpdesk/GuestTicketForm.php)
  - Apply form styling per D12 §6.2
  - Add PDPA acknowledgement checkbox per D09 §8.1
  - Implement real-time validation with wire:model.live.debounce.300ms per D13 §3.7.1
  - Add form autosave using LocalStorage per D13 §3.7.3
  - Add bilingual labels and error messages per D15 §2.1
  - *Routes: /helpdesk/guest/create, /{locale}/helpdesk/create*
  - *Requirements: 3.1, 3.4, 17.1-17.5, 42.1*
  - *D-Docs: D12 §6.2, D13 §3.7, D14 §10.3*

- [ ] 18.2.2 Update Submit Ticket Component (app/Livewire/Helpdesk/SubmitTicket.php)
  - Apply MyDS design tokens per D13 §2.2
  - Add file upload with drag-and-drop per D13 §3.7
  - Implement progress indicator per D12 §6.4
  - Add ARIA attributes for accessibility per D14 §10.3
  - *Routes: /helpdesk/create, /helpdesk/submit*
  - *Requirements: 3.1, 17.3, 48.1-48.5*
  - *D-Docs: D12 §6.2, D13 §3.7*

- [ ] 18.2.3 Update Track Ticket Component (app/Livewire/Helpdesk/TrackTicket.php)
  - Apply status badge styling with icon+color+text per D12 §6.4
  - Add activity timeline component per D12 §6.4
  - Implement responsive layout per D14 §7.4
  - Add bilingual status labels per D15 §2.1
  - *Routes: /helpdesk/track/{ticketNumber?}, /{locale}/helpdesk/track/{ticketNumber?}*
  - *Requirements: 8.4, 36.1-36.5*
  - *D-Docs: D12 §6.4, D14 §6.7*

- [ ] 18.2.4 Update Ticket Success Component (app/Livewire/Helpdesk/TicketSuccess.php)
  - Apply success state styling per D14 §9.3
  - Add QR code for status tracking per Requirements 20.5
  - Add print-friendly layout per D14 §9.4
  - Add bilingual confirmation message per D15 §2.1
  - *Routes: /helpdesk/success, /{locale}/helpdesk/success*
  - *Requirements: 20.4, 20.5*
  - *D-Docs: D14 §9.3*

- [ ] 20. Checkpoint - Guest Helpdesk Routes
  - Ensure all helpdesk forms pass accessibility audit
  - Verify form validation works correctly
  - Test bilingual content switching

---

### 18.3 Guest Asset Loan Routes

- [ ] 18.3.1 Update Guest Loan Application (app/Livewire/GuestLoanApplication.php)
  - Apply multi-step wizard styling per D13 §3.6.1
  - Add progress indicator with step validation per D12 §6.10
  - Implement asset availability calendar per Requirements 34.1-34.5
  - Add responsible officer section per D09 §4.4
  - Add PDPA acknowledgement per D09 §8.1
  - Add bilingual labels per D15 §2.1
  - *Routes: /loan/apply, /loan/create, /{locale}/loan/apply*
  - *Requirements: 3.2, 17.4, 34.1-34.5, 42.1*
  - *D-Docs: D12 §6.2, D13 §3.6, D14 §10.3*

- [ ] 18.3.2 Update Guest Loan Tracking (app/Livewire/GuestLoanTracking.php)
  - Apply status badge styling per D12 §6.4
  - Add approval chain status display per D12 §6.4
  - Implement activity timeline per D12 §6.4
  - Add bilingual status labels per D15 §2.1
  - *Routes: /loan/tracking/{applicationNumber?}, /loan/track-application*
  - *Requirements: 8.4, 36.1-36.5*
  - *D-Docs: D12 §6.4, D14 §6.7*

- [ ] 18.3.3 Update Loan Application Wizard View (resources/views/livewire/loan/application-wizard.blade.php)
  - Apply wizard step styling per D13 §3.6.1
  - Add keyboard navigation between steps per D12 §6.11
  - Implement form autosave per D13 §3.7.3
  - Add bilingual step labels per D15 §2.1
  - *Route: /loan/wizard*
  - *Requirements: 3.2, 17.4, 17.5*
  - *D-Docs: D13 §3.6*

- [ ] 18.3.4 Update Loan Success Page (resources/views/loan/success.blade.php)
  - Apply success state styling per D14 §9.3
  - Add QR code for status tracking per Requirements 20.5
  - Add print-friendly layout per D14 §9.4
  - Add bilingual confirmation message per D15 §2.1
  - *Route: /loan/success*
  - *Requirements: 20.4, 20.5*
  - *D-Docs: D14 §9.3*

- [ ] 21. Checkpoint - Guest Loan Routes
  - Ensure loan wizard passes accessibility audit
  - Verify asset availability calendar works correctly
  - Test form autosave functionality

---

### 18.4 Unified Status Checker

- [ ] 18.4.1 Update Status Checker Component (app/Livewire/Status/StatusChecker.php)
  - Apply unified search styling per D12 §6.14
  - Add ticket and loan status display per D12 §6.4
  - Implement responsive layout per D14 §7.4
  - Add bilingual labels per D15 §2.1
  - *Routes: /status, /status/{token}*
  - *Requirements: 2.1, 2.2, 37.1-37.5*
  - *D-Docs: D12 §6.14, D14 §6.7*

- [ ] 22. Checkpoint - Status Checker
  - Ensure status checker passes accessibility audit
  - Verify token-based lookup works correctly

---

### 18.5 Authenticated Portal Routes

- [ ] 18.5.1 Update Authenticated Dashboard (app/Livewire/Staff/AuthenticatedDashboard.php)
  - Apply statistics cards styling per D14 §6.7
  - Add real-time updates via Laravel Reverb per D12 §2
  - Implement 12-8-4 responsive grid per D14 §7.4
  - Add skeleton loaders for loading states per D14 §9.2
  - Add bilingual labels per D15 §2.1
  - *Routes: /dashboard, /portal/dashboard, /staff/dashboard*
  - *Requirements: 4.1, 16.1-16.5, 26.1-26.5*
  - *D-Docs: D12 §6.4, D14 §6.7*

- [ ] 18.5.2 Update User Profile Component (app/Livewire/Staff/UserProfile.php)
  - Apply form styling per D12 §6.2
  - Add notification preferences panel per D12 §6.17
  - Add theme toggle (light/dark/system) per D14 §4
  - Add language preference per D15 §3.1
  - Add bilingual labels per D15 §2.1
  - *Routes: /portal/profile, /staff/profile, /profile*
  - *Requirements: 4.4, 15.4, 23.4, 25.5*
  - *D-Docs: D12 §6.2, D13 §5.3*

- [ ] 18.5.3 Update Submission History Component (app/Livewire/Staff/SubmissionHistory.php)
  - Apply responsive table styling per D12 §6.14
  - Add saved filters functionality per D12 §6.14
  - Implement pagination with page size selector per D14 §6.6
  - Add CSV/PDF export buttons per Requirements 20.3
  - Add bilingual column headers per D15 §2.1
  - *Routes: /portal/submissions, /staff/history, /staff/my-submissions*
  - *Requirements: 4.3, 18.1-18.5, 23.1-23.5, 49.1-49.5*
  - *D-Docs: D12 §6.14, D14 §6.6*

- [ ] 18.5.4 Update Submission Detail Component (app/Livewire/SubmissionDetail.php)
  - Apply detail view styling per D12 §6.4
  - Add activity timeline per D12 §6.4
  - Add internal comments section (admin only) per Requirements 38.1-38.5
  - Add bilingual labels per D15 §2.1
  - *Route: /portal/submissions/{id}*
  - *Requirements: 36.1-36.5, 38.1-38.5*
  - *D-Docs: D12 §6.4*

- [ ] 18.5.5 Update Cross Module Search Component (app/Livewire/Staff/CrossModuleSearch.php)
  - Apply unified search styling per D12 §6.14
  - Add fuzzy search with typo tolerance per Requirements 22.1-22.5
  - Add categorized results per D12 §6.4
  - Add keyboard navigation (↑↓ arrows, Enter, Escape) per D12 §6.11
  - Add bilingual labels per D15 §2.1
  - *Route: /portal/search*
  - *Requirements: 22.1-22.5, 37.1-37.5*
  - *D-Docs: D12 §6.14*

- [ ] 18.5.6 Update Account Linking Component (app/Livewire/Staff/AccountLinking.php)
  - Apply card styling per D14 §7.5
  - Add email verification flow per D03 SRS-AUTH-004
  - Add confirmation dialog per D13 §3.7.2
  - Add bilingual labels per D15 §2.1
  - *Routes: /dashboard/link-submissions, /portal/link-submissions, /staff/link-submissions*
  - *Requirements: 4.5, 19.4*
  - *D-Docs: D12 §6.4, D13 §3.7.2*

- [ ] 23. Checkpoint - Authenticated Portal Routes
  - Ensure all portal pages pass accessibility audit
  - Verify real-time updates work correctly
  - Test saved filters functionality

---

### 18.6 Staff Portal Routes (Role-Based)

- [ ] 18.6.1 Update Approval Interface Component (app/Livewire/Staff/ApprovalInterface.php)
  - Apply approval queue styling per D12 §6.14
  - Add urgency indicators per D14 §6.7
  - Add bulk approval actions per D12 §6.4
  - Add bilingual labels per D15 §2.1
  - *Routes: /portal/approvals, /staff/approvals/index*
  - *Requirements: 40.1-40.5*
  - *D-Docs: D12 §6.14, D14 §6.7*

- [ ] 18.6.2 Update Approval Queue Component (app/Livewire/Approver/ApprovalQueue.php)
  - Apply sortable table styling per D12 §6.14
  - Add delegation source information per Requirements 33.1-33.5
  - Add responsive card view for mobile per D12 §6.14
  - Add bilingual labels per D15 §2.1
  - *Route: /staff/approval-queue*
  - *Requirements: 40.2-40.5*
  - *D-Docs: D12 §6.14*

- [ ] 18.6.3 Update Delegation Manager Component (app/Livewire/Staff/DelegationManager.php)
  - Apply delegation form styling per D12 §6.2
  - Add date range validation per Requirements 33.3-33.4
  - Add status indicators (active, pending, expired) per D14 §6.7
  - Add bilingual labels per D15 §2.1
  - *Route: /portal/delegations*
  - *Requirements: 33.1-33.5*
  - *D-Docs: D12 §6.2, D14 §6.7*

- [ ] 18.6.4 Update Claim Submissions Component (app/Livewire/Staff/ClaimSubmissions.php)
  - Apply card styling per D14 §7.5
  - Add confirmation dialog per D13 §3.7.2
  - Add bilingual labels per D15 §2.1
  - *Route: /staff/claim-submissions*
  - *Requirements: 4.5*
  - *D-Docs: D12 §6.4*

- [ ] 18.6.5 Update Notification Center Component (app/Livewire/NotificationCenter.php)
  - Apply notification list styling per D12 §6.4
  - Add category tabs (Tickets, Loans, System) per D12 §6.4
  - Add mark-as-read functionality per Requirements 15.3
  - Add bilingual labels per D15 §2.1
  - *Route: /staff/notifications*
  - *Requirements: 15.1-15.5*
  - *D-Docs: D12 §6.4*

- [ ] 24. Checkpoint - Staff Portal Routes
  - Ensure all staff pages pass accessibility audit
  - Verify role-based access works correctly
  - Test delegation functionality

---

### 18.7 Helpdesk Ticket Routes (Authenticated)

- [ ] 18.7.1 Update My Tickets Component (app/Livewire/Helpdesk/MyTickets.php)
  - Apply responsive table styling per D12 §6.14
  - Add SLA indicators per D14 §6.7
  - Add status badges with icon+color+text per D12 §6.4
  - Add bilingual labels per D15 §2.1
  - *Routes: /staff/tickets, /helpdesk/tickets*
  - *Requirements: 8.4, 18.1-18.5*
  - *D-Docs: D12 §6.14, D14 §6.7*

- [ ] 18.7.2 Update Ticket Details Component (app/Livewire/Helpdesk/TicketDetails.php)
  - Apply detail view styling per D12 §6.4
  - Add activity timeline per D12 §6.4
  - Add internal comments section per Requirements 38.1-38.5
  - Add file attachment display per D14 §8.2
  - Add bilingual labels per D15 §2.1
  - *Routes: /staff/tickets/{ticket}, /helpdesk/tickets/{ticket}*
  - *Requirements: 36.1-36.5, 38.1-38.5, 48.1-48.5*
  - *D-Docs: D12 §6.4*

- [ ] 18.7.3 Update Helpdesk Dashboard Component (app/Livewire/Helpdesk/Dashboard.php)
  - Apply statistics cards styling per D14 §6.7
  - Add SLA compliance gauge per Requirements 16.2
  - Add real-time updates per D12 §2
  - Add bilingual labels per D15 §2.1
  - *Route: /helpdesk/dashboard*
  - *Requirements: 16.1-16.5*
  - *D-Docs: D12 §6.4, D14 §6.7*

- [ ] 25. Checkpoint - Helpdesk Routes
  - Ensure all helpdesk pages pass accessibility audit
  - Verify SLA indicators display correctly
  - Test internal comments functionality

---

### 18.8 Loan Routes (Authenticated)

- [ ] 18.8.1 Update Loan History Component (app/Livewire/Loans/LoanHistory.php)
  - Apply responsive table styling per D12 §6.14
  - Add status badges with icon+color+text per D12 §6.4
  - Add pagination with page size selector per D14 §6.6
  - Add bilingual labels per D15 §2.1
  - *Routes: /staff/loans, /loans/history, /loan/history*
  - *Requirements: 8.4, 18.1-18.5*
  - *D-Docs: D12 §6.14, D14 §6.6*

- [ ] 18.8.2 Update Loan Details Component (app/Livewire/Loans/LoanDetails.php)
  - Apply detail view styling per D12 §6.4
  - Add approval chain status display per D12 §6.4
  - Add activity timeline per D12 §6.4
  - Add asset information display per D14 §6.7
  - Add bilingual labels per D15 §2.1
  - *Routes: /staff/loans/{application}, /loans/applications/{application}*
  - *Requirements: 36.1-36.5*
  - *D-Docs: D12 §6.4*

- [ ] 18.8.3 Update Loan Extension Component (app/Livewire/Loans/LoanExtension.php)
  - Apply form styling per D12 §6.2
  - Add date validation per Requirements 35.1-35.5
  - Add availability check per Requirements 34.3-34.4
  - Add bilingual labels per D15 §2.1
  - *Routes: /staff/loans/{application}/extend, /loans/applications/{application}/extend*
  - *Requirements: 35.1-35.5*
  - *D-Docs: D12 §6.2*

- [ ] 18.8.4 Update Loans Dashboard Component (app/Livewire/Loans/AuthenticatedDashboard.php)
  - Apply statistics cards styling per D14 §6.7
  - Add real-time updates per D12 §2
  - Add bilingual labels per D15 §2.1
  - *Route: /loans/dashboard*
  - *Requirements: 16.1-16.5*
  - *D-Docs: D12 §6.4, D14 §6.7*

- [ ] 26. Checkpoint - Loan Routes
  - Ensure all loan pages pass accessibility audit
  - Verify extension functionality works correctly
  - Test approval chain display

---

### 18.9 Loan Approval Routes (Email-Based)

- [ ] 18.9.1 Update Approval Page View (resources/views/livewire/loan/approval-page.blade.php)
  - Apply MOTAC branding per D14 §3.1
  - Add application summary display per D12 §6.4
  - Add approve/reject buttons with confirmation per D13 §3.7.2
  - Add bilingual labels per D15 §2.1
  - *Route: /loan/approval/review/{token}*
  - *Requirements: 13.4*
  - *D-Docs: D12 §6.4, D13 §3.7.2, D14 §3.1*

- [ ] 18.9.2 Update Approval Success View (resources/views/loans/approval-success.blade.php)
  - Apply success state styling per D14 §9.3
  - Add confirmation message per D14 §9.3
  - Add bilingual content per D15 §2.1
  - *Route: (redirect after approval)*
  - *Requirements: 13.4*
  - *D-Docs: D14 §9.3*

- [ ] 27. Checkpoint - Loan Approval Routes
  - Ensure approval pages pass accessibility audit
  - Verify token-based approval works correctly

---

### 18.10 Data Subject Rights Routes (PDPA Compliance)

- [ ] 18.10.1 Update Data Subject Rights Index View
  - Apply card styling per D14 §7.5
  - Add data export button per Requirements 41.2
  - Add correction request form per Requirements 41.3
  - Add deletion request form per Requirements 41.4
  - Add bilingual labels per D15 §2.1
  - *Route: /staff/data-rights*
  - *Requirements: 41.1-41.5*
  - *D-Docs: D09 §8.1, D14 §7.5*

- [ ] 18.10.2 Update Consent History View
  - Apply timeline styling per D12 §6.4
  - Add consent status indicators per D14 §6.7
  - Add bilingual labels per D15 §2.1
  - *Route: /staff/data-rights/consent-history*
  - *Requirements: 42.4*
  - *D-Docs: D12 §6.4*

- [ ] 28. Checkpoint - Data Subject Rights Routes
  - Ensure PDPA pages pass accessibility audit
  - Verify data export functionality works correctly

---

### 18.11 Authentication Routes

- [ ] 18.11.1 Update Two-Factor Challenge Component (app/Livewire/Auth/TwoFactorChallenge.php)
  - Apply form styling per D12 §6.2
  - Add accessible input with proper ARIA labels per D14 §10.3
  - Add recovery code option per Requirements 32.4
  - Add bilingual labels per D15 §2.1
  - *Route: /two-factor-challenge*
  - *Requirements: 32.1-32.5*
  - *D-Docs: D12 §6.2, D14 §10.3*

- [ ] 18.11.2 Update Profile Edit View (resources/views/profile.blade.php)
  - Apply form styling per D12 §6.2
  - Add 2FA setup section per Requirements 32.1-32.3
  - Add bilingual labels per D15 §2.1
  - *Route: /profile, /profile/edit*
  - *Requirements: 32.1-32.5*
  - *D-Docs: D12 §6.2*

- [ ] 29. Checkpoint - Authentication Routes
  - Ensure auth pages pass accessibility audit
  - Verify 2FA functionality works correctly

---

### 18.12 Admin Routes

- [ ] 18.12.1 Update Analytics Export Controller Views
  - Apply export button styling per D14 §6.5
  - Add progress indicator for large exports per D12 §6.4
  - Add bilingual labels per D15 §2.1
  - *Routes: /admin/analytics/export/csv, /admin/analytics/export/json*
  - *Requirements: 49.1-49.5*
  - *D-Docs: D12 §6.4, D14 §6.5*

- [ ] 30. Checkpoint - Admin Routes
  - Ensure admin pages pass accessibility audit
  - Verify export functionality works correctly

---

### 18.13 Development Routes (Local Only)

- [ ] 18.13.1 Update Components Demo Page (resources/views/dev/components.blade.php)
  - Add all component examples with proper documentation
  - Add accessibility testing section
  - Add bilingual examples per D15 §2.1
  - *Route: /dev/components*
  - *Requirements: 8.1-8.5*
  - *D-Docs: D12 §6.4*

- [ ] 31. Final Checkpoint - All Frontend Pages
  - Run full WCAG 2.2 AA accessibility audit on all pages
  - Verify all bilingual content displays correctly
  - Test keyboard navigation on all interactive elements
  - Verify responsive layout on mobile, tablet, and desktop
  - Run Lighthouse audit for Performance 90+, Accessibility 100

---

## Summary - Phase 18

This phase covers **31 tasks** updating all frontend pages listed in routes/web.php:

| Category | Routes | Tasks |
|----------|--------|-------|
| Public Information Pages | 6 | 6 |
| Guest Helpdesk Routes | 4 | 4 |
| Guest Asset Loan Routes | 4 | 4 |
| Unified Status Checker | 2 | 1 |
| Authenticated Portal Routes | 6 | 6 |
| Staff Portal Routes | 5 | 5 |
| Helpdesk Ticket Routes | 3 | 3 |
| Loan Routes | 4 | 4 |
| Loan Approval Routes | 2 | 2 |
| Data Subject Rights Routes | 2 | 2 |
| Authentication Routes | 2 | 2 |
| Admin Routes | 2 | 1 |
| Development Routes | 1 | 1 |

**Key Standards Applied:**

- **D12 UI/UX Design Guide**: Layout patterns, interaction design, accessibility
- **D13 Frontend Framework**: MyDS tokens, Livewire patterns, form validation
- **D14 UI/UX Style Guide**: MOTAC branding, color palette, typography
- **D15 Language Support**: Bilingual BM/EN implementation
- **WCAG 2.2 Level AA**: Color contrast, keyboard navigation, ARIA attributes
- **PDPA 2010**: Data subject rights, consent management
