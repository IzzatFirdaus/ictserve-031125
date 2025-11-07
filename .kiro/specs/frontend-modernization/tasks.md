# Frontend Modernization - Implementation Tasks

## Overview

This task liste implementation of frontend modernization for ICTServe, focusing on Livewire 3 pattern migration, Volt component implementation, Tailwind component library creation, Alpine.js patterns, performance optimization, and WCAG 2.2 AA accessibility compliance.

**Current Status**: The codebase already uses Livewire 3 with `#[Computed]` attributes extensively. Many components exist but need standardization and enhancement.

---

## Phase 1: Livewire 3 Pattern Migration & Standardization

### 1.1 Audit and Migrate Livewire Components

- [ ] 1.1.1 Audit all Livewire components for Livewire 3 compliance

  - Review all components in `app/Livewire/` for deprecated patterns
  - Check for `wire:model.defer` usage (should be `wire:model` or `wire:model.live`)
  - Verify namespace is `App\Livewire\` (not `App\Http\Livewire\`)
  - Check for `$this->emit()` usage (should be `$this->dispatch()`)
  - Document components needing migration
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 1.1.2 Add `wire:key` attributes to all loops

  - Search for `@foreach` loops in all Livewire blade templates
  - Add `wire:key` with unique identifiers to each iterated element
  - Test DOM diffing performance improvements
  - _Requirements: 1.5, 3.5_

- [ ] 1.1.3 Implement PHP 8 attributes across components

  - Add `#[Reactive]` to properties that react to parent changes
  - Add `#[Locked]` to properties that shouldn't be modified from frontend
  - Add `#[Session]` to properties that persist across requests
  - Add `#[Layout]` where appropriate
  - _Requirements: 1.3_

- [ ]\* 1.1.4 Create Livewire component tests
  - Write PHPUnit tests for each migrated component
  - Test property updates, method calls, and event dispatching
  - Verify `#[Computed]` caching behavior
  - _Requirements: 8.1, 8.2_

---

## Phase 2: Performance Optimization Implementation

### 2.1 Implement Lazy Loading

- [ ] 2.1.1 Add `#[Lazy]` attribute to dashboard widgets

  - Apply to `Staff\AuthenticatedDashboard` statistics
  - Apply to `Loans\AuthenticatedDashboard` stats
  - Apply to `Helpdesk\Dashboard` quick actions
  - Create placeholder views for loading states
  - _Requirements: 3.2_

- [ ] 2.1.2 Implement loading state indicators
  - Add `wire:loading` to all form submissions
  - Add `wire:loading` to search inputs
  - Add `wire:loading` to filter components
  - Ensure indicators appear within 100ms
  - _Requirements: 3.3_

### 2.2 Optimize Database Queries

- [ ] 2.2.1 Review and optimize `#[Computed]` properties

  - Audit all existing `#[Computed]` methods for caching effectiveness
  - Add eager loading to prevent N+1 queries
  - Implement query result caching where appropriate
  - _Requirements: 3.1_

- [ ] 2.2.2 Implement debounced search inputs
  - Update all search inputs to use `wire:model.live.debounce.300ms`
  - Test search performance with debouncing
  - Verify reduced server requests
  - _Requirements: 3.4_

---

## Phase 3: Tailwind Component Library Creation

### 3.1 Create Toast Notification Component

- [ ] 3.1.1 Build Toast component with variants

  - Create `resources/views/components/toast.blade.php`
  - Implement success, error, warning, info variants
  - Add Alpine.js auto-dismiss after 5 seconds
  - Add ARIA live region attributes (`aria-live="polite"`)
  - Implement click-to-dismiss functionality
  - _Requirements: 4.1, 4.5, 6.3_

- [ ]\* 3.1.2 Create Toast component tests
  - Write Playwright test for toast display and dismissal
  - Test ARIA attributes with screen reader
  - Verify auto-dismiss timing
  - _Requirements: 8.3_

### 3.2 Create Modal Dialog Component

- [ ] 3.2.1 Build Modal component with focus trap

  - Create `resources/views/components/modal.blade.php` (enhance existing)
  - Implement Alpine.js focus trap with `x-trap`
  - Add keyboard navigation (Tab, Shift+Tab, Escape)
  - Implement backdrop click-away behavior
  - Add ARIA attributes (`role="dialog"`, `aria-modal="true"`)
  - Restore focus to trigger element on close
  - _Requirements: 4.2, 6.2_

- [ ]\* 3.2.2 Create Modal component tests
  - Write Playwright test for focus management
  - Test keyboard navigation (Tab, Escape)
  - Verify ARIA attributes
  - Test backdrop click-away
  - _Requirements: 8.3_

### 3.3 Create Dropdown Menu Component

- [ ] 3.3.1 Build Dropdown component with keyboard navigation

  - Enhance existing `resources/views/components/dropdown.blade.php`
  - Implement Arrow Up/Down keyboard navigation
  - Add Enter key selection
  - Add Escape key dismissal
  - Implement `@click.away` for auto-close
  - Add ARIA attributes (`role="menu"`, `role="menuitem"`)
  - _Requirements: 4.3, 6.2_

- [ ]\* 3.3.2 Create Dropdown component tests
  - Write Playwright test for keyboard navigation
  - Test Arrow keys, Enter, Escape
  - Verify ARIA attributes
  - _Requirements: 8.3_

### 3.4 Create Form Wizard Component

- [ ] 3.4.1 Build Form Wizard component

  - Create `resources/views/components/form-wizard.blade.php`
  - Implement multi-step progress indicator
  - Add per-step validation
  - Implement keyboard navigation (Next, Previous, Submit)
  - Add ARIA attributes for step navigation
  - _Requirements: 4.4_

- [ ]\* 3.4.2 Create Form Wizard component tests
  - Write Playwright test for multi-step navigation
  - Test per-step validation
  - Verify keyboard navigation
  - _Requirements: 8.3_

---

## Phase 4: Alpine.js Pattern Documentation

### 4.1 Create Alpine.js Pattern Library

- [ ] 4.1.1 Create Dropdown pattern documentation

  - Create `resources/views/components/alpine/dropdown-pattern.blade.php`
  - Document `x-data`, `@click.away`, `x-transition` usage
  - Add code examples and usage notes
  - _Requirements: 5.1_

- [ ] 4.1.2 Create Modal pattern documentation

  - Create `resources/views/components/alpine/modal-pattern.blade.php`
  - Document `x-trap` for focus management
  - Document `@keydown.escape.window` for dismissal
  - Add code examples and usage notes
  - _Requirements: 5.2_

- [ ] 4.1.3 Create Accordion pattern documentation

  - Create `resources/views/components/alpine/accordion-pattern.blade.php`
  - Document `x-collapse` directive usage
  - Add code examples for smooth height transitions
  - _Requirements: 5.3_

- [ ] 4.1.4 Create Tabs pattern documentation
  - Create `resources/views/components/alpine/tabs-pattern.blade.php`
  - Document `x-show` directive for panel switching
  - Add keyboard navigation examples
  - _Requirements: 5.4_

---

## Phase 5: Volt Single-File Component Implementation

### 5.1 Identify Candidates for Volt Migration

- [ ] 5.1.1 Audit components for Volt suitability
  - Identify simple forms with < 50 lines of PHP logic
  - Identify filter components with basic state management
  - Identify search bars with debounced inputs
  - Identify modal dialogs with simple interactions
  - Document candidates for Volt migration
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

### 5.2 Create Volt Components

- [ ] 5.2.1 Create Volt search component

  - Create `resources/views/livewire/components/search.blade.php` as Volt component
  - Implement `state()` for search query
  - Implement `computed()` for filtered results
  - Add `wire:model.live.debounce.300ms` for debounced input
  - _Requirements: 2.4_

- [ ] 5.2.2 Create Volt filter component

  - Create `resources/views/livewire/components/filter.blade.php` as Volt component
  - Implement `state()` for filter options
  - Implement `computed()` for filtered data
  - Add event handling for filter updates
  - _Requirements: 2.2_

- [ ]\* 5.2.3 Create Volt component tests
  - Write Volt::test() for search component
  - Write Volt::test() for filter component
  - Verify state management and computed properties
  - _Requirements: 8.1, 8.2_

---

## Phase 6: Accessibility Enhancements

### 6.1 Implement ARIA Attributes

- [ ] 6.1.1 Add ARIA labels to icon-only buttons

  - Audit all icon-only buttons across the application
  - Add `aria-label` attributes with descriptive text
  - Verify with screen reader testing
  - _Requirements: 6.1_

- [ ] 6.1.2 Implement ARIA attributes for form fields

  - Add `aria-required` to required form fields
  - Add `aria-describedby` linking to error messages
  - Add `aria-invalid` for fields with errors
  - _Requirements: 6.4_

- [ ] 6.1.3 Add ARIA live regions for notifications
  - Add `aria-live="polite"` to toast notifications
  - Add `aria-atomic="true"` for complete announcements
  - Add `role="alert"` for error messages
  - _Requirements: 6.3_

### 6.2 Implement Focus Management

- [ ] 6.2.1 Add skip links to all pages

  - Create skip link component with `href="#main-content"`
  - Style with `sr-only` and `focus:not-sr-only`
  - Add to all layout files
  - _Requirements: 6.5_

- [ ] 6.2.2 Implement focus indicators

  - Audit all interactive elements for focus indicators
  - Ensure minimum 3px outline with 4.5:1 contrast ratio
  - Test keyboard navigation across all pages
  - _Requirements: 6.6_

- [ ] 6.2.3 Implement modal focus trap
  - Enhance modal component with focus trap
  - Restore focus to trigger element on close
  - Test with keyboard navigation
  - _Requirements: 6.2_

### 6.3 Verify Color Contrast

- [ ] 6.3.1 Audit color contrast ratios

  - Check all text colors against backgrounds (4.5:1 minimum)
  - Check all UI component colors (3:1 minimum)
  - Document any violations and fix
  - _Requirements: 6.7_

- [ ]\* 6.3.2 Run Lighthouse accessibility audits
  - Run Lighthouse on all major pages
  - Achieve score of 90 or higher
  - Document and fix any violations
  - _Requirements: 8.3_

---

## Phase 7: Tailwind Configuration Optimization

### 7.1 Optimize Tailwind Configuration

- [ ] 7.1.1 Update Tailwind content paths

  - Update `tailwind.config.js` with all content paths
  - Add `resources/views/**/*.blade.php`
  - Add `app/Livewire/**/*.php`
  - Add `app/Filament/**/*.php`
  - Add `resources/js/**/*.js`
  - _Requirements: 7.1_

- [ ] 7.1.2 Define custom color tokens

  - Add `motac-blue` color scale to theme
  - Add `motac-yellow` color scale to theme
  - Add `status-success`, `status-warning`, `status-danger` colors
  - Verify all colors meet WCAG 2.2 AA contrast requirements
  - _Requirements: 7.2, 7.5_

- [ ] 7.1.3 Configure production build optimization

  - Enable CSS purging for production builds
  - Verify unused classes are removed
  - Test production build size reduction
  - _Requirements: 7.3_

- [ ] 7.1.4 Extend default Tailwind theme
  - Extend theme without overriding core utilities
  - Add custom spacing, typography, or other utilities as needed
  - Document custom theme extensions
  - _Requirements: 7.4_

---

## Phase 8: Cross-Browser and Responsive Testing

### 8.1 Cross-Browser Compatibility

- [ ] 8.1.1 Test on Chrome, Firefox, Edge, Safari
  - Test all major pages on each browser
  - Document and fix any browser-specific issues
  - Verify consistent rendering across browsers
  - _Requirements: 9.1_

### 8.2 Responsive Design Testing

- [ ] 8.2.1 Test responsive layouts from 320px to 2xl

  - Test all pages at mobile (320px), tablet (768px), desktop (1024px+)
  - Verify touch-friendly tap targets (minimum 44x44px)
  - Test tablet layouts for efficient space utilization
  - _Requirements: 9.2, 9.3, 9.4_

- [ ] 8.2.2 Remove jQuery dependencies
  - Audit codebase for jQuery usage
  - Replace jQuery with vanilla JavaScript or Alpine.js
  - Verify functionality without jQuery
  - _Requirements: 9.5_

---

## Phase 9: Bilingual Support Maintenance

### 9.1 Verify Bilingual Support

- [ ] 9.1.1 Audit all UI components for translation support

  - Check all new components for translation keys
  - Verify Malay (primary) and English (secondary) translations
  - Test language switching functionality
  - _Requirements: 10.1, 10.2_

- [ ] 9.1.2 Implement language preference persistence

  - Verify language preference persists across sessions
  - Test language switching in all new components
  - Verify toast notifications display in selected language
  - _Requirements: 10.3, 10.4_

- [ ] 9.1.3 Add translation keys for new components
  - Add translation keys for Toast component
  - Add translation keys for Modal component
  - Add translation keys for Dropdown component
  - Add translation keys for Form Wizard component
  - _Requirements: 10.5_

---

## Phase 10: Performance Metrics Achievement

### 10.1 Core Web Vitals Optimization

- [ ] 10.1.1 Optimize Largest Contentful Paint (LCP)

  - Measure LCP on dashboard page
  - Optimize images, fonts, and critical CSS
  - Achieve LCP < 2.5 seconds
  - _Requirements: 11.1, 11.3_

- [ ] 10.1.2 Optimize First Input Delay (FID)

  - Measure FID on form submission
  - Optimize JavaScript execution
  - Achieve FID < 100 milliseconds
  - _Requirements: 11.2, 11.4_

- [ ] 10.1.3 Optimize Cumulative Layout Shift (CLS)
  - Measure CLS across all pages
  - Add size attributes to images
  - Reserve space for dynamic content
  - Achieve CLS < 0.1
  - _Requirements: 11.5_

---

## Phase 11: Documentation and Pattern Library

### 11.1 Component Documentation

- [ ] 11.1.1 Document Toast component usage

  - Create usage examples in component file
  - Document all props and default values
  - Add accessibility requirements
  - _Requirements: 12.1, 12.4_

- [ ] 11.1.2 Document Modal component usage

  - Create usage examples in component file
  - Document all props and default values
  - Add accessibility requirements
  - _Requirements: 12.1, 12.4_

- [ ] 11.1.3 Document Dropdown component usage

  - Create usage examples in component file
  - Document all props and default values
  - Add accessibility requirements
  - _Requirements: 12.1, 12.4_

- [ ] 11.1.4 Document Form Wizard component usage
  - Create usage examples in component file
  - Document all props and default values
  - Add accessibility requirements
  - _Requirements: 12.1, 12.4_

### 11.2 Alpine.js Pattern Documentation

- [ ] 11.2.1 Document Alpine.js patterns with examples
  - Add code snippets for each pattern
  - Add explanations for directive usage
  - Document accessibility considerations
  - _Requirements: 12.2_

### 11.3 Accessibility Documentation

- [ ] 11.3.1 Document accessibility requirements

  - Create accessibility testing procedures
  - Document WCAG 2.2 AA compliance checklist
  - Add screen reader testing guidelines
  - _Requirements: 12.3_

- [ ] 11.3.2 Maintain component documentation
  - Store documentation in `resources/views/components/` directory
  - Keep documentation up-to-date with component changes
  - _Requirements: 12.5_

---

## Phase 12: Final Testing and Validation

### 12.1 Comprehensive Testing

- [ ] 12.1.1 Run full PHPUnit test suite

  - Execute `php artisan test`
  - Verify all tests pass
  - Fix any failing tests
  - _Requirements: 8.5_

- [ ] 12.1.2 Run Playwright E2E tests

  - Execute Playwright test suite
  - Test critical user journeys
  - Verify accessibility compliance
  - _Requirements: 8.3_

- [ ] 12.1.3 Run Lighthouse audits
  - Run Lighthouse on all major pages
  - Achieve accessibility score >= 90
  - Achieve performance score >= 85
  - _Requirements: 8.3_

### 12.2 Build and Deploy Validation

- [ ] 12.2.1 Verify production build

  - Run `npm run build`
  - Verify no errors or Tailwind purge issues
  - Test production assets
  - _Requirements: 8.4_

- [ ] 12.2.2 Final quality checks
  - Run `vendor/bin/pint` for PSR-12 compliance
  - Run `vendor/bin/phpstan analyse` for static analysis
  - Verify all quality gates pass
  - _Requirements: 8.5_

---

## Notes

- Tasks marked with `*` are optional testing tasks that can be skipped for faster MVP delivery
- All tasks reference specific requirements from the requirements document
- Each phase builds incrementally on previous phases
- Focus on core functionality first, then enhance with optional features
- Maintain WCAG 2.2 AA compliance throughout all phases
