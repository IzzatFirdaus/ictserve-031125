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

- [ ] 1. Establish Design Token System
  - [ ] 1.1 Verify and extend @theme directive in resources/css/app.css with complete MyDS tokens
    - Add MyDS color tokens with semantic naming (--bg-*, --txt-*, --otl-*, --fr-*) per D13 §2.2
    - Add spacing tokens (space-1 through space-16) per D13 §2.6 and D14 §7.2
    - Add radius tokens (xs: 4px, s: 6px, m: 8px, l: 12px, xl: 14px, full: 9999px) per D13 §2.5
    - Add motion tokens (duration-short: 200ms, duration-medium: 400ms, duration-long: 600ms) per D12 §6.10
    - Add shadow tokens (shadow-button, shadow-card, shadow-dropdown) per D12 §6.9 and D14 §7.5
    - Add typography tokens (Poppins for headings, Inter for body) per D13 §2.4
    - Add MyDS token mapping CSS custom properties per D14 §4.1.1
    - *Requirements: 2.4, 2.5, 9.1, 11.1-11.5*
    - *D-Docs: D13 §2.2-2.7, D14 §4.1.1, D12 §6.5-6.10*
  - [ ] 1.1.1 Configure font loading for Poppins and Inter
    - Add Google Fonts import for Poppins (400, 500, 600) and Inter (400, 500, 600) per D13 §2.4.4
    - Configure --font-heading, --font-body, --font-mono CSS variables
    - *D-Docs: D13 §2.4.1, D13 §2.4.4*
  - [ ] 1.2 Create FigmaDesignService for Figma MCP integration
    - Implement getDesignContext(), getCodeConnectMap(), createDesignSystemRules()
    - Add transformToLivewire() for React-to-Blade conversion following D13 §2.1 patterns
    - Add mapColorToToken() for WCAG 2.2 AA compliant color mapping per D14 §4.1
    - *Requirements: 1.1-1.5*
    - *D-Docs: D04 §3.1, D13 §2.1*
  - [ ]* 1.3 Write property test for color contrast compliance
    - **Property 1: Color Contrast Compliance**
    - **Validates: Requirements 2.3, 6.1, 25.1**
    - Test against D14 §4.2 contrast requirements (4.5:1 text, 3:1 UI)
  - [ ] 1.4 Update .kiro/steering/design-system.md with generated design rules
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

- [ ] 2.3 Create Focus Indicator Enhancement Component
  - [ ] 2.3.1 Create resources/views/components/accessibility/focus-indicator.blade.php
    - Implement 3px outline, 2px offset, 3:1 contrast minimum per D12 §6.13
    - Use --fr-primary token (#0056B3) per D13 §2.2
    - Add 200ms ease-out transition per D12 §6.10 motion tokens
    - *Requirements: 6.2, 9.5*
    - *D-Docs: D12 §6.13, D14 §9.1, D13 §2.2*
  - [ ]* 2.3.2 Write property test for focus indicator visibility
    - **Property 4: Focus Indicator Visibility**
    - **Validates: Requirements 6.2**

- [ ] 2.4 Enhance Keyboard Navigation System
  - [ ] 2.4.1 Create resources/views/components/ui/keyboard-shortcuts-manager.blade.php
    - Implement Alpine.js @keydown.window handlers per D12 §6.11
    - Add shortcuts: Alt+N (new ticket), Alt+D (dashboard), Alt+H (help), Alt+L (loans), ? (help modal)
    - Follow keyboard action table from D12 §6.11 (Tab, Shift+Tab, Enter/Space, Escape, Arrow Keys)
    - Ensure no conflicts with assistive technology per D14 §10.2
    - *Requirements: 24.1-24.5*
    - *D-Docs: D12 §6.11, D14 §10.2*
  - [ ]* 2.4.2 Write property test for keyboard shortcut non-interference
    - **Property 5: Keyboard Shortcut Accessibility**
    - **Validates: Requirements 24.5**

- [ ] 3. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 3: Core UI Components

**Form Design Guidelines (per D12 §6.2, D13 §3.7):**

- Label above field with `<label for="id">` per D14 §10.3
- Required fields marked with `*` and `<abbr title="required">` per D12 §6.2
- Real-time validation with `wire:model.live.debounce.300ms` per D13 §3.7.1
- Error messages linked via aria-describedby per D14 §10.3

- [ ] 3.1 Enhance Form Components
  - [ ] 3.1.1 Update resources/views/components/form/input.blade.php with ARIA enhancements
    - Add aria-describedby linking for hints/errors per D13 §3.7.1
    - Add aria-invalid for error states per D14 §10.3
    - Add aria-required for required fields per D12 §6.2
    - Use border-danger token for error states per D14 §4.1.1
    - *Requirements: 3.4, 17.1*
    - *D-Docs: D12 §6.2, D13 §3.7.1, D14 §10.3*
  - [ ] 3.1.2 Create resources/views/components/form/file-upload-enhanced.blade.php
    - Add drag-and-drop support with Alpine.js per D13 §2.1
    - Add progress indicator with aria-busy="true" per D12 §6.4
    - Add file type/size validation with clear error messages per D13 §3.7.2
    - *Requirements: 17.3*
    - *D-Docs: D13 §3.7, D14 §10.3*
  - [ ]* 3.1.3 Write property test for ARIA attribute completeness
    - **Property 3: ARIA Attribute Completeness**
    - **Validates: Requirements 3.4, 6.4, 6.6, 15.5, 17.1, 28.1, 29.1**

- [ ] 3.2 Enhance Multi-Step Wizard Component
  - [ ] 3.2.1 Create app/Livewire/Components/FormWizard.php
    - Implement step navigation with validation per D13 §3.6.1 (progressive disclosure)
    - Add progress indicator following D12 §6.10 motion tokens
    - Add step completion tracking
    - *Requirements: 3.2, 17.4*
    - *D-Docs: D13 §3.6, D12 §6.10*
  - [ ] 3.2.2 Create resources/views/livewire/components/form-wizard.blade.php
    - Add accessible progress bar with aria-valuenow, aria-valuemin, aria-valuemax
    - Add step navigation controls with keyboard support per D12 §6.11
    - Use MyDS spacing tokens (space-4, space-6) per D13 §2.6
    - *Requirements: 17.4*
    - *D-Docs: D13 §2.6, D14 §7.2*
  - [ ] 3.2.3 Implement form autosave using LocalStorage
    - Add draft preservation per D13 §3.7.3 error prevention checklist
    - Add recovery prompt with confirmation dialog per D13 §3.7.2
    - *Requirements: 12.2, 17.5*
    - *D-Docs: D13 §3.7.3*

- [ ] 4. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 4: Data Display Components

**Table Design Guidelines (per D12 §6.14, D14 §6.6):**

- `<th scope="col">` for table headers per D14 §10.5
- Sortable columns with aria-sort attribute per D14 §10.5
- Responsive: Full table (≥1024px), Card view (<768px) per D12 §6.14
- Status badges with icon+color+text (not color alone) per D12 §6.4

- [ ] 4.1 Enhance Responsive Table Component
  - [ ] 4.1.1 Update resources/views/components/data/table.blade.php
    - Add sticky headers per D14 §6.6
    - Add sortable columns with aria-sort per D14 §10.5
    - Add zebra striping for readability per D14 §6.6
    - Add `<th scope="col">` for accessibility per D12 §6.14
    - *Requirements: 18.1, 18.5*
    - *D-Docs: D12 §6.14, D14 §6.6, D14 §10.5*
  - [ ] 4.1.2 Create mobile card view transformation
    - Implement viewport detection (<768px) per D12 §6.8 (12-8-4 grid)
    - Transform table to stacked card view per D12 §6.14
    - Use shadow-card token per D14 §7.5
    - *Requirements: 7.2, 18.2*
    - *D-Docs: D12 §6.8, D14 §7.4*
  - [ ] 4.1.3 Enhance table filtering component
    - Add dropdown filters with aria-expanded per D14 §10.5
    - Add search input with aria-describedby per D13 §3.7.1
    - Add clear filters button with 44×44px touch target per D12 §4.1
    - *Requirements: 18.3*
    - *D-Docs: D12 §4.1, D14 §10.5*
  - [ ]* 4.1.4 Write property test for touch target size compliance
    - **Property 2: Touch Target Size Compliance**
    - **Validates: Requirements 3.5, 6.5**
    - Test against D12 §4.1 (44×44px minimum)

- [ ] 4.2 Enhance Statistics Card Component
  - [ ] 4.2.1 Update resources/views/components/ui/stats-card.blade.php
    - Add trend indicator with direction (up/down arrows) per D14 §6.7
    - Add shadow-card styling per D14 §7.5
    - Add Heroicons support per D14 §8.1 (w-5 h-5 base size)
    - *Requirements: 16.1*
    - *D-Docs: D14 §6.7, D14 §7.5, D14 §8.1*
  - [ ] 4.2.2 Create SLA compliance gauge component
    - Add color-coded status using semantic tokens per D14 §4.1.1
    - Green (--txt-success-600) >90%, Yellow (--txt-warning-600) 70-90%, Red (--txt-danger) <70%
    - Add accessible labels with aria-label per D14 §10.5
    - *Requirements: 16.2*
    - *D-Docs: D14 §4.1.1, D14 §10.5*

- [ ] 5. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 5: Navigation and Layout Components

**Layout Guidelines (per D12 §5, D13 §4, D14 §7.4):**

- Dual Layout System: app.blade.php (authenticated), guest.blade.php (public) per D12 §5.1
- 12-8-4 Responsive Grid: Desktop 12-col, Tablet 8-col, Mobile 4-col per D14 §7.4
- Navigation: Sidebar on desktop, hamburger on mobile per D12 §5.3

- [ ] 5.1 Enhance Responsive Navigation
  - [ ] 5.1.1 Update sidebar navigation for desktop
    - Add icons (Heroicons w-5 h-5) with labels per D14 §8.1
    - Add active state indicators with --bg-primary-50 token per D13 §2.2
    - Follow Filament 4 patterns per D12 §6.2
    - Width: 256px expanded, 64px collapsed per D14 §6.2
    - *Requirements: 4.2*
    - *D-Docs: D12 §6.2, D14 §6.2, D14 §8.1*
  - [ ] 5.1.2 Implement hamburger menu for mobile/tablet
    - Add responsive breakpoint handling per D14 §7.3 (md: 768px, lg: 1024px)
    - Add smooth transitions using --duration-medium (400ms) per D12 §6.10
    - Use shadow-dropdown for menu overlay per D14 §7.5
    - *Requirements: 7.3*
    - *D-Docs: D12 §6.10, D14 §7.3, D14 §7.5*

- [ ] 5.2 Enhance Breadcrumb Component
  - [ ] 5.2.1 Update resources/views/components/navigation/breadcrumb.blade.php
    - Add `aria-label="Breadcrumb"` per D12 §6.1
    - Add aria-current="page" for current page per D14 §10.4
    - Add chevron-right separator icons (Heroicons) per D14 §8.1
    - *Requirements: 6.4*
    - *D-Docs: D12 §6.1, D14 §10.4*

- [ ] 5.3 Implement 12-8-4 Responsive Grid System
  - [ ] 5.3.1 Create resources/views/components/layout/responsive-grid.blade.php
    - Desktop (≥1024px): 12 columns, 24px gap, 24px edge padding, max-width 1280px per D14 §7.4
    - Tablet (768-1023px): 8 columns, 24px gap, 24px edge padding per D14 §7.4
    - Mobile (≤767px): 4 columns, 18px gap, 18px edge padding per D14 §7.4
    - *Requirements: 7.1*
    - *D-Docs: D12 §6.8, D13 §4.3, D14 §7.4*
  - [ ] 5.3.2 Implement fluid typography scaling
    - Use MyDS typography scale per D13 §2.4 (Poppins headings, Inter body)
    - Heading sizes: H1 36px, H2 30px, H3 24px, H4 20px per D13 §2.4.2
    - Body sizes: Large 18px, Medium 16px, Small 14px per D13 §2.4.3
    - *Requirements: 7.4*
    - *D-Docs: D13 §2.4, D14 §5.2*

- [ ] 6. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 6: UI Feedback Components

**Notification Guidelines (per D12 §6.10, D14 §9.3):**

- Toast animations: slideInUp/slideOutDown with --duration-medium (400ms) per D12 §6.10
- Auto-dismiss: Success/Info 4-5s, Error/Warning persistent per D14 §9.3
- ARIA live regions: aria-live="polite" for status, "assertive" for errors per D14 §10.4

- [ ] 6.1 Enhance Toast Notification System
  - [ ] 6.1.1 Create app/Livewire/Components/Toast.php
    - Implement toast queue management with Livewire dispatch events per D12 §6.4
    - Add auto-dismiss for success/info (4-5s) per D14 §9.3
    - Add persist for error/warning until acknowledged per D14 §9.3
    - *Requirements: 30.1-30.5*
    - *D-Docs: D12 §6.4, D14 §9.3*
  - [ ] 6.1.2 Create resources/views/livewire/components/toast.blade.php
    - Add slideInUp/slideOutDown animations using --motion-easeoutback (400ms) per D12 §6.10
    - Add ARIA live region with aria-live="polite" per D14 §10.4
    - Add dismiss button with 44×44px touch target per D12 §4.1
    - Use shadow-dropdown token per D14 §7.5
    - *Requirements: 9.2, 30.1*
    - *D-Docs: D12 §6.10, D14 §7.5, D14 §10.4*
  - [ ]* 6.1.3 Write property test for toast notification accessibility
    - **Property 6: Toast Notification Accessibility**
    - **Validates: Requirements 30.5**

- [ ] 6.2 Enhance Skeleton Loader Components
  - [ ] 6.2.1 Update resources/views/components/ui/skeleton-card.blade.php
    - Add aria-busy="true" per D12 §6.4
    - Add hidden text for screen readers: `<span class="sr-only">Loading...</span>`
    - Add pulse animation respecting prefers-reduced-motion per D12 §6.10
    - Use --bg-washed (#F7F7F7) for skeleton background per D14 §4.1.1
    - *Requirements: 8.5, 9.4, 26.1, 26.5*
    - *D-Docs: D12 §6.4, D12 §6.10, D14 §4.1.1*
  - [ ] 6.2.2 Create skeleton variants (text, avatar, table-row)
    - Follow D14 §9.2 loading states guidelines
    - *Requirements: 26.1*
    - *D-Docs: D14 §9.2*

- [ ] 6.3 Enhance Empty State Components
  - [ ] 6.3.1 Update resources/views/components/ui/empty-state.blade.php
    - Add illustration support with aria-hidden="true" per D14 §8.2
    - Add bilingual messages per D15 guidelines
    - Add call-to-action button with primary styling per D14 §6.5
    - Add proper ARIA labels per D14 §10.3
    - *Requirements: 27.1-27.5*
    - *D-Docs: D14 §8.2, D14 §10.3, D15*

- [ ] 7. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 7: Modal and Dropdown Components

**Modal/Dropdown Guidelines (per D12 §6.11, D13 §3.7.2, D14 §10.5):**

- Focus trap: x-trap.noscroll for modals per D12 §6.11
- Keyboard: Escape closes, Arrow keys navigate per D12 §6.11
- ARIA: aria-modal="true", aria-expanded, aria-haspopup per D14 §10.5
- Animations: --duration-medium (400ms) with --motion-easeout per D12 §6.10

- [ ] 7.1 Enhance Modal Component
  - [ ] 7.1.1 Update resources/views/components/ui/modal.blade.php
    - Add focus trap using x-trap.noscroll per D12 §6.11
    - Add escape key close with @keydown.escape.window per D12 §6.11
    - Add aria-modal="true", aria-labelledby per D14 §10.5
    - Add return focus to trigger on close per D12 §6.11
    - Add 400ms easeout animations using --motion-easeout per D12 §6.10
    - Use shadow-dropdown for modal panel per D14 §7.5
    - *Requirements: 28.1-28.5*
    - *D-Docs: D12 §6.11, D12 §6.10, D14 §7.5, D14 §10.5*
  - [ ] 7.1.2 Create confirmation dialog variant
    - Add clear warning message with --txt-danger token per D14 §4.1.1
    - Add destructive action styling with danger button per D14 §6.5
    - Follow D13 §3.7.2 confirmation dialog pattern
    - *Requirements: 28.2*
    - *D-Docs: D13 §3.7.2, D14 §4.1.1, D14 §6.5*
  - [ ]* 7.1.3 Write property test for modal focus management
    - **Property 7: Modal Focus Management**
    - **Validates: Requirements 28.1, 28.4**

- [ ] 7.2 Enhance Dropdown Component
  - [ ] 7.2.1 Update resources/views/components/ui/dropdown.blade.php
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

- [ ] 8.1 Enhance Notification Bell Component
  - [ ] 8.1.1 Update app/Livewire/NotificationBell.php
    - Add unread count badge with --bg-danger token per D14 §4.1.1
    - Add Laravel Reverb WebSocket integration per D12 §2
    - Use Heroicon bell icon (w-5 h-5) per D14 §8.1
    - *Requirements: 15.1*
    - *D-Docs: D12 §2, D14 §4.1.1, D14 §8.1*
  - [ ] 8.1.2 Create notification dropdown with categories
    - Add Tickets, Loans, System categories per D12 §6.4
    - Add mark-as-read functionality
    - Use shadow-dropdown styling per D14 §7.5
    - *Requirements: 15.3*
    - *D-Docs: D12 §6.4, D14 §7.5*
  - [ ] 8.1.3 Implement notification preferences panel
    - Add email frequency configuration per D12 §6.17
    - Add in-app notification type toggles
    - Follow D13 §5.3 notification preferences pattern
    - *Requirements: 15.4*
    - *D-Docs: D12 §6.17, D13 §5.3*

- [ ] 8.2 Enhance Dashboard Statistics with Real-time Updates
  - [ ] 8.2.1 Update authenticated dashboard statistics cards
    - Add My Open Tickets, My Pending Loans, My Approvals (Grade 41+), Overdue Items per D12 §6.4
    - Add real-time updates via Laravel Reverb per D12 §2
    - Use shadow-card styling per D14 §7.5
    - *Requirements: 4.1*
    - *D-Docs: D12 §2, D12 §6.4, D14 §7.5*
  - [ ] 8.2.2 Implement Redis caching for statistics
    - Add 5-minute cache for dashboard statistics per D11 performance guidelines
    - Add 5-minute cache for asset availability
    - *Requirements: 10.3*
    - *D-Docs: D11*

- [ ] 9. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 9: Theme and Preferences

**Theme Guidelines (per D13 §2.2, D14 §4):**

- Dark mode must maintain WCAG 2.2 AA contrast ratios per D14 §4.2
- Theme toggle with 200ms transition per D12 §6.10
- Respect prefers-color-scheme media query per D14 §4

- [ ] 9.1 Implement Dark Mode Support
  - [ ] 9.1.1 Create dark mode color palette in @theme
    - Maintain WCAG 2.2 AA contrast ratios (4.5:1 text, 3:1 UI) per D14 §4.2
    - Map MyDS tokens to dark variants per D13 §2.2
    - *Requirements: 25.1*
    - *D-Docs: D13 §2.2, D14 §4.2*
  - [ ] 9.1.2 Implement theme toggle component
    - Add to user menu and profile settings per D14 §6.1.2
    - Add 200ms ease-out transition using --duration-short per D12 §6.10
    - Use Heroicon sun/moon icons per D14 §8.1
    - *Requirements: 25.4, 25.5*
    - *D-Docs: D12 §6.10, D14 §6.1.2, D14 §8.1*
  - [ ] 9.1.3 Implement theme persistence
    - Add session/cookie storage per D13 §5.3
    - Respect prefers-color-scheme as default per D14 §4
    - *Requirements: 25.2, 25.3*
    - *D-Docs: D13 §5.3, D14 §4*
  - [ ]* 9.1.4 Write property test for dark mode contrast compliance
    - **Property 9: Dark Mode Contrast Compliance**
    - **Validates: Requirements 25.1**

- [ ] 9.2 Implement Saved Filters System
  - [ ] 9.2.1 Create app/Livewire/Components/SavedFilters.php
    - Add save filter combination functionality per D12 §6.14
    - Add name and description fields
    - *Requirements: 23.1, 23.2*
    - *D-Docs: D12 §6.14*
  - [ ] 9.2.2 Add migration for user preferences
    - Add saved_filters JSON field per D09 schema guidelines
    - Add dashboard_layout JSON field
    - Add theme_preference field (light|dark|system)
    - *Requirements: 23.2, 23.3, 23.4*
    - *D-Docs: D09*
  - [ ] 9.2.3 Create quick-apply buttons for saved filters
    - Use secondary button styling per D14 §6.5
    - *Requirements: 23.5*
    - *D-Docs: D14 §6.5*

- [ ] 10. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 10: Search and Discovery

- [ ] 10.1 Implement Fuzzy Search Service
  - [ ] 10.1.1 Create app/Services/FuzzySearchService.php
    - Implement Levenshtein distance algorithm
    - Add typo tolerance matching
    - *Requirements: 22.1, 22.2*
  - [ ] 10.1.2 Add search suggestions and autocomplete
    - *Requirements: 22.3*
  - [ ] 10.1.3 Implement unified search across tickets and loans
    - Add categorized results
    - Add matched term highlighting with accessibility
    - *Requirements: 22.4, 22.5*
  - [ ]* 10.1.4 Write property test for fuzzy search matching
    - **Property 10: Fuzzy Search Matching**
    - **Validates: Requirements 22.1, 22.2**

- [ ] 11. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 11: Onboarding and Help

- [x] 11.1 Onboarding Tour Component (already exists at app/Livewire/Portal/WelcomeTour.php)
  - *Requirements: 21.1-21.5*

- [ ] 11.2 Enhance Onboarding Tour
  - [ ] 11.2.1 Add onboarding_completed field to users table migration
    - *Requirements: 21.3*
  - [ ] 11.2.2 Add tour progress indicator
    - Show current step and total steps
    - *Requirements: 21.5*

- [ ] 11.3 Implement Keyboard Shortcuts Help Modal
  - [ ] 11.3.1 Create keyboard shortcuts help modal triggered by ? key
    - Display all available shortcuts per D12 §6.11
    - Use modal component with focus trap
    - *Requirements: 24.3*
    - *D-Docs: D12 §6.11*

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

- [ ] 12.4 Enhance Error Pages
  - [ ] 12.4.1 Update 404 page with navigation links and search
    - Add bilingual messaging
    - *Requirements: 14.1*
  - [ ] 12.4.2 Update 403 page with role-appropriate messaging
    - Add login/contact options
    - *Requirements: 14.2*
  - [ ] 12.4.3 Update 500 page with incident reference number
    - Add support contact information
    - *Requirements: 14.3*
  - [ ] 12.4.4 Create maintenance mode page
    - Add estimated restoration time
    - Add alternative contact methods
    - *Requirements: 14.4*
  - [ ] 12.4.5 Create session expired page
    - Add automatic redirect to login
    - Add session preservation options
    - *Requirements: 14.5*

- [ ] 13. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 13: Print and Export

- [ ] 13.1 Implement Print Stylesheet
  - [ ] 13.1.1 Create resources/css/print.css
    - Hide navigation elements
    - Optimize typography
    - Add page break controls
    - *Requirements: 20.1*

- [ ] 13.2 Implement PDF Export
  - [ ] 13.2.1 Create ticket PDF export with MOTAC letterhead
    - Add reference numbers
    - *Requirements: 20.2*
  - [ ] 13.2.2 Create loan application PDF export
    - Add MOTAC letterhead
    - Add reference numbers
    - *Requirements: 20.2*

- [ ] 13.3 Implement CSV Export
  - [ ] 13.3.1 Add CSV export for data tables
    - Add proper encoding
    - Add column headers
    - *Requirements: 20.3*

- [ ] 13.4 Implement Receipt/Confirmation View
  - [ ] 13.4.1 Create printable confirmation view for submitted forms
    - *Requirements: 20.4*

- [ ] 13.5 Implement QR Code Display
  - [ ] 13.5.1 Add QR code for asset tracking
    - Add status lookup functionality
    - *Requirements: 20.5*

- [ ] 14. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 14: Email Templates

- [ ] 14.1 Enhance Email Templates
  - [ ] 14.1.1 Update email templates with MOTAC branding
    - Add Jata Negara and MOTAC logo
    - Use compliant color palette
    - *Requirements: 13.1*
  - [ ] 14.1.2 Add bilingual email templates
    - Bahasa Melayu primary, English secondary
    - *Requirements: 13.2*
  - [ ] 14.1.3 Enhance ticket confirmation email
    - Add ticket number
    - Add status tracking link
    - Add estimated response time
    - *Requirements: 13.3*
  - [ ] 14.1.4 Enhance loan approval request email
    - Add application summary
    - Add approve/reject buttons with signed URLs
    - Add 7-day expiration notice
    - *Requirements: 13.4*
  - [ ] 14.1.5 Create additional email templates
    - Confirmation, status updates, SLA warnings
    - Approval requests, reminders (48h before, on due, daily overdue)
    - *Requirements: 13.5*

- [ ] 15. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 15: Admin Panel Enhancement

- [ ] 15.1 Customize Filament 4 Theme
  - [ ] 15.1.1 Update Filament theme to match MOTAC branding
    - Set primary color (#0056B3)
    - Update navigation styling
    - Add logo placement
    - *Requirements: 5.1*

- [ ] 15.2 Enhance Helpdesk Ticket Management
  - [ ] 15.2.1 Add SLA indicators to ticket list
    - Add priority badges
    - Add status workflow actions
    - Add clear visual hierarchy
    - *Requirements: 5.2*

- [ ] 15.3 Enhance Loan Application Management
  - [ ] 15.3.1 Add approval chain status display
    - Add asset availability indicators
    - Add accessory tracking
    - Add check-out/check-in workflows
    - *Requirements: 5.3*

- [ ] 15.4 Enhance Dashboard Widgets
  - [ ] 15.4.1 Update HelpdeskStatsWidget with real-time updates
    - *Requirements: 5.4*
  - [ ] 15.4.2 Update LoanStatsWidget with real-time updates
    - *Requirements: 5.4*
  - [ ] 15.4.3 Create RecentActivityWidget
    - *Requirements: 5.4*

- [ ] 15.5 Create Unified Audit Log Viewer
  - [ ] 15.5.1 Combine owen-it audits and spatie activity_log
    - Add filtering capabilities
    - Add export functionality
    - *Requirements: 5.5*

- [ ] 16. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 16: Performance Optimization

- [ ] 16.1 Implement Core Web Vitals Optimization
  - [ ] 16.1.1 Optimize for LCP <2.5s
    - *Requirements: 10.1*
  - [ ] 16.1.2 Optimize for FID <100ms
    - *Requirements: 10.1*
  - [ ] 16.1.3 Optimize for CLS <0.1
    - *Requirements: 10.1*
  - [ ] 16.1.4 Optimize for TTFB <600ms
    - *Requirements: 10.1*

- [ ] 16.2 Implement Livewire Optimization
  - [ ] 16.2.1 Add computed properties where appropriate
    - *Requirements: 10.2*
  - [ ] 16.2.2 Implement lazy loading for non-critical components
    - *Requirements: 10.2, 10.5*
  - [ ] 16.2.3 Add debounced input handling (300ms)
    - *Requirements: 10.2*

- [ ] 16.3 Implement Image Optimization
  - [ ] 16.3.1 Add WebP format with JPEG fallbacks
    - Add explicit dimensions
    - Add lazy loading
    - *Requirements: 7.5*

- [ ] 17. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Phase 17: Motion and Animation System

- [ ] 17.1 Implement Motion Tokens
  - [ ] 17.1.1 Add motion tokens to CSS
    - easeout (short 200ms, medium 400ms, long 600ms)
    - easeoutback for playful interactions
    - *Requirements: 9.1*

- [ ] 17.2 Implement prefers-reduced-motion Support
  - [ ] 17.2.1 Add media query handling for reduced motion
    - Disable non-essential animations
    - *Requirements: 9.3*

- [ ] 18. Final Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

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
