# Implementation Plan: Filament Admin Frontend Redesign

## Overview

This implementation plan breaks down the Filament admin frontend redesign into discrete, manageable tasks. The focus is on implementing MyDS v2025.2 compliance, WCAG 2.2 AA accessibility, and consistent styling across all admin interfaces. Tasks are organized by priority, with widgets and core components taking precedence.

## Tasks

- [x] 1. Setup and Configuration
  - Configure Filament AdminPanelProvider with MyDS colors and settings
  - Create CSS custom properties file for MyDS tokens
  - Set up translation keys for Bahasa Melayu admin interface
  - Configure theme system with user preference persistence
  - _Requirements: 6.1, 6.2, 10.1_

- [-] 2. Admin Login Page Redesign
  - [x] 2.1 Update login page layout with centered form and MOTAC branding
    - Implement centered layout with MyDS shadow-card
    - Add MOTAC logo and branding colors
    - Apply MyDS spacing tokens (--space-6)
    - _Requirements: 1.1, 1.2_

  - [x] 2.2 Implement focus indicators on login form fields
    - Apply focus-visible:ring-3 and ring-primary-500 classes
    - Ensure 3px outline with 2px offset
    - Test keyboard navigation and tab order
    - _Requirements: 1.3, 1.6_

  - [x] 2.3 Add validation error styling with proper contrast
    - Apply text-danger-600 and border-danger-600 classes
    - Add error icons with heroicon-o-exclamation-circle
    - Ensure 4.5:1 contrast ratio
    - Include ARIA attributes (aria-invalid, aria-describedby)
    - _Requirements: 1.4_

  - [ ]* 2.4 Write property test for login form focus indicators
    - **Property 1: Focus Indicators on Form Fields**
    - **Validates: Requirements 1.3**

  - [ ]* 2.5 Write property test for role-based login access
    - **Property 3: Role-Based Login Access**
    - **Validates: Requirements 1.5**

  - [ ] 2.6 Implement role-based access restriction (admin/superuser only)
    - Add authMiddleware with role:admin|superuser
    - Create 403 error page for unauthorized access
    - _Requirements: 1.5_

  - [ ]* 2.7 Write unit tests for login page accessibility
    - Test ARIA labels on "Remember me" and "Forgot password"
    - Test keyboard navigation
    - Test screen reader compatibility
    - _Requirements: 1.7_

- [ ] 3. Checkpoint - Verify login page implementation
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 4. Dashboard Layout Structure
  - [ ] 4.1 Implement responsive grid system (12-8-4 columns)
    - Configure grid for desktop (12-col), tablet (8-col), mobile (4-col)
    - Apply MyDS spacing between sections (--space-6)
    - Test responsive breakpoints
    - _Requirements: 2.2, 2.7_

  - [ ] 4.2 Create collapsible sidebar navigation
    - Implement sidebar with 256px expanded, 64px collapsed width
    - Add collapse/expand toggle button
    - Show icon-only navigation when collapsed with tooltips
    - Show icons with text labels when expanded
    - _Requirements: 2.1, 2.3, 2.4_

  - [ ] 4.3 Implement sidebar state persistence
    - Save collapse/expand state to user preferences
    - Restore state on page load
    - _Requirements: 4.6_

  - [ ]* 4.4 Write property test for sidebar state persistence
    - **Property 14: Sidebar State Persistence**
    - **Validates: Requirements 4.6**

  - [ ] 4.5 Create sticky header with user menu, notifications, and theme toggle
    - Implement sticky positioning
    - Add user dropdown menu
    - Add notification bell icon
    - Add theme toggle button
    - _Requirements: 2.5_

  - [ ]* 4.6 Write unit tests for dashboard layout responsiveness
    - Test grid columns at different breakpoints
    - Test sidebar collapse/expand functionality
    - Test sticky header behavior
    - _Requirements: 2.2, 2.3, 2.4, 2.5_

- [ ] 5. Widget Component Styling (Priority 1)
  - [ ] 5.1 Create base widget component with MyDS styling
    - Apply bg-white/dark:bg-gray-800 background
    - Apply rounded-lg border-radius (12px)
    - Apply shadow-card elevation
    - Add proper padding (p-6)
    - _Requirements: 2.6, 3.1, 3.7_

  - [ ]* 5.2 Write property test for widget shadow elevation
    - **Property 4: Widget Shadow Elevation**
    - **Validates: Requirements 2.6**

  - [ ]* 5.3 Write property test for widget color tokens
    - **Property 5: Widget Color Tokens**
    - **Validates: Requirements 3.1**

  - [ ]* 5.4 Write property test for widget border radius
    - **Property 10: Widget Border Radius**
    - **Validates: Requirements 3.7**

  - [ ] 5.5 Implement widget header styling
    - Apply text-xl font-semibold for headers
    - Use Poppins font family
    - Add flex layout for header with actions
    - _Requirements: 3.2_

  - [ ]* 5.6 Write property test for widget header typography
    - **Property 6: Widget Header Typography**
    - **Validates: Requirements 3.2**

  - [ ] 5.7 Style stats widgets (HelpdeskStatsOverview, AssetLoanStatsOverview)
    - Apply text-3xl for metric numbers
    - Add color coding (success/warning/danger)
    - Use text-sm text-gray-600 for labels
    - Add icons with w-5 h-5 sizing
    - _Requirements: 3.3_

  - [ ]* 5.8 Write property test for widget metric display
    - **Property 7: Widget Metric Display**
    - **Validates: Requirements 3.3**

  - [ ] 5.9 Add ARIA labels to all widgets
    - Add aria-label or aria-labelledby to widget containers
    - Add aria-describedby for widget descriptions
    - Ensure screen reader compatibility
    - _Requirements: 3.4_

  - [ ]* 5.10 Write property test for widget ARIA labels
    - **Property 8: Widget ARIA Labels**
    - **Validates: Requirements 3.4**

  - [ ] 5.11 Implement interactive widget hover states
    - Apply hover:bg-gray-100/dark:hover:bg-gray-700
    - Add smooth transitions
    - _Requirements: 3.6_

  - [ ]* 5.12 Write property test for interactive widget hover states
    - **Property 9: Interactive Widget Hover States**
    - **Validates: Requirements 3.6**

  - [ ] 5.13 Create skeleton loaders for widget loading states
    - Implement animate-pulse skeleton
    - Add aria-busy="true" attribute
    - Match widget layout structure
    - _Requirements: 3.8, 9.2_

  - [ ]* 5.14 Write property test for loading state skeleton
    - **Property 11: Loading State Skeleton**
    - **Validates: Requirements 3.8, 9.2**

- [ ] 6. Checkpoint - Verify widget styling implementation
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 7. Chart Widget Styling
  - [ ] 7.1 Style chart widgets (TicketVolumeChart, AssetUtilizationWidget)
    - Set min-h-[300px] for chart containers
    - Style chart legends with text-sm
    - Implement dark tooltips with white text
    - Ensure 3:1 contrast for chart elements
    - _Requirements: 3.5_

  - [ ] 7.2 Implement chart theme adaptation
    - Update chart colors when theme changes
    - Maintain contrast ratios in both themes
    - _Requirements: 6.7_

  - [ ]* 7.3 Write property test for chart theme adaptation
    - **Property 24: Chart Theme Adaptation**
    - **Validates: Requirements 6.7**

  - [ ]* 7.4 Write unit tests for chart rendering
    - Test chart data loading
    - Test chart responsiveness
    - Test chart accessibility
    - _Requirements: 3.5_

- [ ] 8. Table Widget Styling
  - [ ] 8.1 Style table widgets (RecentTicketsTable, HealthCheckTableWidget)
    - Apply zebra striping (odd:bg-gray-50/dark:odd:bg-gray-700)
    - Make headers sticky (sticky top-0)
    - Add row hover states
    - Set cell padding (px-4 py-3)
    - _Requirements: 5.1, 5.2_

  - [ ]* 8.2 Write property test for resource table zebra striping
    - **Property 16: Resource Table Zebra Striping**
    - **Validates: Requirements 5.1**

  - [ ]* 8.3 Write property test for sticky table headers
    - **Property 17: Sticky Table Headers**
    - **Validates: Requirements 5.2**

  - [ ]* 8.4 Write unit tests for table widget functionality
    - Test table sorting
    - Test table filtering
    - Test table pagination
    - _Requirements: 5.1, 5.2_

- [ ] 9. Navigation Sidebar Enhancement
  - [ ] 9.1 Style navigation menu items
    - Apply w-5 h-5 sizing to Heroicons
    - Implement active state highlighting (bg-primary-50)
    - Add focus indicators (focus-visible:ring-3)
    - _Requirements: 4.1, 4.2, 4.3_

  - [ ]* 9.2 Write property test for active navigation highlighting
    - **Property 12: Active Navigation Highlighting**
    - **Validates: Requirements 4.2**

  - [ ]* 9.3 Write property test for navigation focus indicators
    - **Property 13: Navigation Focus Indicators**
    - **Validates: Requirements 4.3**

  - [ ] 9.4 Implement keyboard navigation support
    - Support Tab, Enter, Arrow keys
    - Ensure logical focus order
    - _Requirements: 4.4_

  - [ ] 9.5 Add chevron icons for nested navigation items
    - Show chevron-down when collapsed
    - Show chevron-up when expanded
    - Animate transitions
    - _Requirements: 4.5_

  - [ ] 9.6 Implement tooltips for collapsed sidebar
    - Show tooltips on hover with 200ms delay
    - Position tooltips to the right of icons
    - _Requirements: 4.7_

  - [ ] 9.7 Implement role-based menu filtering
    - Filter menu items based on user role
    - Show admin-only items to admins
    - Show superuser-only items to superusers
    - _Requirements: 4.8_

  - [ ]* 9.8 Write property test for role-based navigation filtering
    - **Property 15: Role-Based Navigation Filtering**
    - **Validates: Requirements 4.8**

  - [ ]* 9.9 Write unit tests for navigation functionality
    - Test keyboard navigation
    - Test nested item expansion
    - Test tooltip display
    - _Requirements: 4.4, 4.5, 4.7_

- [ ] 10. Checkpoint - Verify navigation implementation
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 11. Resource Page Styling
  - [ ] 11.1 Update resource table styling
    - Apply zebra striping
    - Make headers sticky
    - Add sortable column indicators
    - Implement accessible pagination
    - _Requirements: 5.1, 5.2, 5.6, 5.7_

  - [ ] 11.2 Style action buttons in resource pages
    - Apply MyDS button tokens (shadow-button)
    - Ensure min-h-11 for touch targets
    - Add focus indicators
    - _Requirements: 5.3, 5.8_

  - [ ]* 11.3 Write property test for action button styling
    - **Property 18: Action Button Styling**
    - **Validates: Requirements 5.3**

  - [ ]* 11.4 Write property test for minimum touch target size
    - **Property 20: Minimum Touch Target Size**
    - **Validates: Requirements 5.8, 8.4**

  - [ ] 11.5 Update resource form styling
    - Apply min-h-11 px-3 rounded-lg to inputs
    - Add focus indicators
    - Style validation errors
    - _Requirements: 5.4, 5.5_

  - [ ]* 11.6 Write property test for form input dimensions
    - **Property 19: Form Input Dimensions**
    - **Validates: Requirements 5.4**

  - [ ]* 11.7 Write property test for validation error styling
    - **Property 2: Validation Error Styling**
    - **Validates: Requirements 1.4, 5.5**

  - [ ]* 11.8 Write unit tests for resource page functionality
    - Test CRUD operations
    - Test form validation
    - Test table sorting and filtering
    - _Requirements: 5.1, 5.2, 5.4, 5.5_

- [ ] 12. Theme System Implementation
  - [ ] 12.1 Create theme toggle widget
    - Add sun/moon icons
    - Implement toggle functionality
    - Position in header
    - _Requirements: 6.1_

  - [ ] 12.2 Implement theme preference persistence
    - Save theme to user settings
    - Load theme on page load
    - _Requirements: 6.2_

  - [ ]* 12.3 Write property test for theme preference persistence
    - **Property 21: Theme Preference Persistence**
    - **Validates: Requirements 6.2**

  - [ ] 12.4 Apply dark mode color tokens
    - Add dark: variants to all components
    - Ensure 4.5:1 contrast in dark mode
    - _Requirements: 6.3_

  - [ ]* 12.5 Write property test for dark mode color application
    - **Property 22: Dark Mode Color Application**
    - **Validates: Requirements 6.3**

  - [ ] 12.6 Implement smooth theme transitions
    - Add transition classes
    - Use --motion-easeout timing
    - Set 200ms duration
    - _Requirements: 6.4_

  - [ ] 12.7 Implement reactive theme switching
    - Update all components without page reload
    - Use Livewire for reactive updates
    - _Requirements: 6.5_

  - [ ]* 12.8 Write property test for theme change without reload
    - **Property 23: Theme Change Without Reload**
    - **Validates: Requirements 6.5**

  - [ ] 12.9 Implement system preference detection
    - Detect prefers-color-scheme on first visit
    - Set initial theme based on system preference
    - _Requirements: 6.6_

  - [ ]* 12.10 Write unit tests for theme system
    - Test theme toggle functionality
    - Test theme persistence
    - Test system preference detection
    - _Requirements: 6.1, 6.2, 6.6_

- [ ] 13. Checkpoint - Verify theme system implementation
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 14. Accessibility Compliance
  - [ ] 14.1 Add skip-to-content link
    - Position at top of page
    - Make visible on focus
    - Link to main content area
    - _Requirements: 7.1_

  - [ ] 14.2 Implement focus indicators on all interactive elements
    - Apply focus-visible:ring-3 with 2px offset
    - Use primary color for focus ring
    - _Requirements: 7.2_

  - [ ]* 14.3 Write property test for interactive element focus indicators
    - **Property 25: Interactive Element Focus Indicators**
    - **Validates: Requirements 7.2**

  - [ ] 14.4 Add semantic HTML and ARIA landmarks
    - Use banner, main, navigation, contentinfo roles
    - Add aria-label to landmarks
    - _Requirements: 7.3_

  - [ ] 14.5 Associate form labels with inputs
    - Use matching for and id attributes
    - Ensure all inputs have labels
    - _Requirements: 7.4_

  - [ ]* 14.6 Write property test for form label association
    - **Property 26: Form Label Association**
    - **Validates: Requirements 7.4**

  - [ ] 14.7 Add alt text to all images
    - Provide meaningful alt text for informative images
    - Use aria-hidden="true" for decorative images
    - _Requirements: 7.5_

  - [ ]* 14.8 Write property test for image alt text
    - **Property 27: Image Alt Text**
    - **Validates: Requirements 7.5**

  - [ ] 14.9 Implement modal focus trapping
    - Trap focus within modal when open
    - Restore focus to trigger on close
    - _Requirements: 7.6_

  - [ ]* 14.10 Write property test for modal focus trapping
    - **Property 28: Modal Focus Trapping**
    - **Validates: Requirements 7.6**

  - [ ] 14.11 Add aria-live regions for dynamic content
    - Use aria-live="polite" for non-critical updates
    - Use aria-live="assertive" for critical updates
    - _Requirements: 7.7_

  - [ ]* 14.12 Write property test for dynamic content announcements
    - **Property 29: Dynamic Content Announcements**
    - **Validates: Requirements 7.7**

  - [ ] 14.13 Add non-color indicators for color-coded information
    - Add icons to status indicators
    - Add text labels to color-coded elements
    - _Requirements: 7.8_

  - [ ]* 14.14 Write property test for non-color information indicators
    - **Property 30: Non-Color Information Indicators**
    - **Validates: Requirements 7.8**

  - [ ]* 14.15 Write accessibility audit tests
    - Test with axe-core for WCAG compliance
    - Test keyboard navigation
    - Test screen reader compatibility
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.7, 7.8_

- [ ] 15. Responsive Design Implementation
  - [ ] 15.1 Implement mobile navigation (hamburger menu)
    - Hide sidebar below 768px
    - Show hamburger menu button
    - Implement slide-out menu
    - _Requirements: 8.1_

  - [ ] 15.2 Configure responsive grid layouts
    - 8-column grid for tablet (768px-1023px)
    - 12-column grid for desktop (1024px+)
    - _Requirements: 8.2, 8.3_

  - [ ] 15.3 Implement responsive table behavior
    - Convert to card view on mobile
    - Or enable horizontal scroll
    - _Requirements: 8.5_

  - [ ] 15.4 Ensure minimum font size
    - Set base font size to 16px
    - Test readability on mobile
    - _Requirements: 8.6_

  - [ ] 15.5 Implement responsive form layouts
    - Stack fields vertically on mobile
    - Use full width for inputs
    - _Requirements: 8.7_

  - [ ]* 15.6 Write unit tests for responsive design
    - Test at different viewport sizes
    - Test mobile navigation
    - Test responsive tables
    - Test responsive forms
    - _Requirements: 8.1, 8.2, 8.3, 8.5, 8.7_

- [ ] 16. Component Library Documentation
  - [ ] 16.1 Document custom Filament components
    - Create documentation in resources/views/filament/components
    - Include usage examples
    - Document props and slots
    - _Requirements: 10.3, 10.5_

  - [ ] 16.2 Add accessibility annotations to component docs
    - Document ARIA attributes
    - Document keyboard interactions
    - Document screen reader behavior
    - _Requirements: 10.7_

  - [ ] 16.3 Ensure custom components extend Filament base classes
    - Verify all custom components extend base classes
    - Refactor any standalone components
    - _Requirements: 10.2_

  - [ ]* 16.4 Write property test for custom component extension
    - **Property 31: Custom Component Extension**
    - **Validates: Requirements 10.2**

  - [ ] 16.5 Verify Tailwind CSS usage
    - Ensure all components use Tailwind v4 utilities
    - Remove any custom CSS
    - _Requirements: 10.4_

  - [ ]* 16.6 Write property test for Tailwind CSS utility usage
    - **Property 32: Tailwind CSS Utility Usage**
    - **Validates: Requirements 10.4**

- [ ] 17. Performance Optimization
  - [ ] 17.1 Implement lazy loading for below-the-fold components
    - Lazy load widgets not in viewport
    - Use Intersection Observer
    - _Requirements: 9.3_

  - [ ] 17.2 Implement chart resize debouncing
    - Debounce resize events by 300ms
    - Prevent excessive re-renders
    - _Requirements: 9.4_

  - [ ] 17.3 Implement page prefetching on hover
    - Prefetch linked pages on hover
    - Improve perceived performance
    - _Requirements: 9.6_

  - [ ] 17.4 Configure static asset caching
    - Set appropriate cache headers
    - Implement cache busting
    - _Requirements: 9.7_

  - [ ]* 17.5 Write performance tests
    - Test lazy loading behavior
    - Test debounce functionality
    - Test prefetching
    - _Requirements: 9.3, 9.4, 9.6_

- [ ] 18. Final Integration and Testing
  - [ ] 18.1 Run full test suite
    - Run all unit tests
    - Run all property-based tests
    - Run all integration tests
    - Ensure 100% pass rate

  - [ ] 18.2 Perform visual regression testing
    - Test all pages with Playwright
    - Compare screenshots against baseline
    - Fix any visual regressions

  - [ ] 18.3 Perform accessibility audit
    - Run axe-core on all pages
    - Fix any WCAG violations
    - Verify keyboard navigation

  - [ ] 18.4 Perform cross-browser testing
    - Test on Chrome, Firefox, Safari, Edge
    - Test on mobile browsers
    - Fix any browser-specific issues

  - [ ] 18.5 Perform performance audit
    - Run Lighthouse on all pages
    - Ensure LCP < 2.5s, FID < 100ms, CLS < 0.1
    - Optimize any performance issues

  - [ ] 18.6 Update documentation
    - Update README with new features
    - Update component documentation
    - Update developer guide

- [ ] 19. Final Checkpoint - Complete implementation
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties
- Unit tests validate specific examples and edge cases
- Priority 1: Widgets (tasks 5-8)
- Priority 2: Pages and resources (tasks 11, 15)
- All styling must use Tailwind CSS v4 utility classes
- All components must be accessible (WCAG 2.2 AA)
- All components must support dark mode
- All components must be responsive (mobile-first)
