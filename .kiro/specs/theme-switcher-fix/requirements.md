# Requirements Document: Theme Switcher Fix

## Introduction

The theme switcher on the ICTServe landing page (welcome.blade.php) has stopped working properly after recent edits to the landing.blade.php layout. This document outlines the requirements to diagnose and fix the theme switching functionality while maintaining WCAG 2.2 AA compliance and the existing v3.6.0 theme system architecture.

## Glossary

- **Theme Switcher**: A UI component (button) that allows users to toggle between light and dark themes
- **Landing Layout**: The `resources/views/layouts/landing.blade.php` file that provides the base structure for guest-facing pages
- **Theme Toggle Component**: The Livewire component at `resources/views/livewire/components/theme-toggle.blade.php`
- **Theme Init Script**: The inline script component at `resources/views/components/theme-init-script.blade.php` that prevents FOUT
- **FOUT**: Flash of Unstyled Theme - visual flash when theme changes
- **LocalStorage**: Browser storage mechanism used to persist theme preference

## Requirements

### Requirement 1: Theme Switcher Functionality

**User Story:** As a guest user visiting the ICTServe landing page, I want to toggle between light and dark themes, so that I can view the site in my preferred color scheme.

#### Acceptance Criteria

1. WHEN a user clicks the theme toggle button in the header THEN the system SHALL switch between light and dark themes immediately
2. WHEN the theme is changed THEN the system SHALL persist the user's preference in localStorage
3. WHEN the page is reloaded THEN the system SHALL apply the user's previously selected theme without visual flash (FOUT prevention)
4. WHEN multiple theme toggle buttons exist on the same page THEN all buttons SHALL synchronize their state
5. WHEN the theme changes THEN the system SHALL update all theme-dependent UI elements (icons, colors, backgrounds) across the entire page

### Requirement 2: Component Integration

**User Story:** As a developer, I want the theme toggle component to integrate properly with the landing layout, so that the theme switching functionality works reliably.

#### Acceptance Criteria

1. WHEN the landing layout renders THEN the system SHALL include the theme-init-script component in the <head> section
2. WHEN the theme toggle component is rendered THEN the system SHALL initialize its JavaScript without conflicts with Alpine.js
3. WHEN the theme toggle button is clicked THEN the system SHALL execute the toggle logic using event delegation
4. WHEN the theme changes THEN the system SHALL dispatch a 'themeChanged' custom event for other components to listen to
5. WHEN the page loads THEN the system SHALL apply the correct icon state (sun for light, moon for dark) based on current theme

### Requirement 3: WCAG 2.2 AA Compliance

**User Story:** As a user with accessibility needs, I want the theme switcher to be fully accessible, so that I can use it with keyboard navigation and screen readers.

#### Acceptance Criteria

1. WHEN a user navigates with keyboard THEN the theme toggle button SHALL be focusable and operable with Enter/Space keys
2. WHEN the theme toggle button receives focus THEN the system SHALL display a visible focus indicator with 3:1 contrast ratio
3. WHEN a screen reader encounters the theme toggle button THEN the system SHALL announce "Tukar tema" (Switch theme) as the button label
4. WHEN the theme changes THEN the system SHALL maintain minimum 4.5:1 contrast ratio for text and 3:1 for UI components
5. WHEN the theme toggle button is rendered THEN the system SHALL have minimum touch target size of 44x44px

### Requirement 4: Error Handling and Debugging

**User Story:** As a developer, I want clear error messages and debugging capabilities, so that I can quickly identify and fix theme-related issues.

#### Acceptance Criteria

1. WHEN the theme toggle JavaScript fails to initialize THEN the system SHALL log a descriptive error to the browser console
2. WHEN localStorage is unavailable THEN the system SHALL gracefully fallback to light theme without breaking functionality
3. WHEN the theme toggle button is not found in the DOM THEN the system SHALL log a warning but not throw an error
4. WHEN multiple theme toggle initializations occur THEN the system SHALL prevent duplicate event listeners using the `window.themeToggleInitialized` flag
5. WHEN debugging is needed THEN developers SHALL be able to inspect theme state via browser DevTools

### Requirement 5: Performance and Optimization

**User Story:** As a user, I want the theme switcher to respond instantly, so that the interface feels smooth and responsive.

#### Acceptance Criteria

1. WHEN the theme toggle button is clicked THEN the theme SHALL change within 50ms (perceived as instant)
2. WHEN the page loads THEN the theme initialization SHALL complete before first paint to prevent FOUT
3. WHEN the theme changes THEN the system SHALL use CSS transitions (200ms duration) for smooth visual feedback
4. WHEN multiple theme toggles exist THEN the system SHALL use event delegation to minimize memory usage
5. WHEN the theme state is read THEN the system SHALL retrieve it from localStorage without network requests

## Technical Context

### Current Implementation

**Theme Toggle Component** (`resources/views/livewire/components/theme-toggle.blade.php`):

- Uses unique IDs per instance to support multiple toggles
- Implements event delegation for click handling
- Stores theme preference in localStorage
- Synchronizes all toggle buttons on the page
- Dispatches 'themeChanged' custom event

**Theme Init Script** (`resources/views/components/theme-init-script.blade.php`):

- Runs inline in <head> before stylesheets
- Reads theme from localStorage (defaults to 'light')
- Applies 'dark' class to <html> element if needed
- Sets data-theme attribute for CSS targeting

**Landing Layout** (`resources/views/layouts/landing.blade.php`):

- Includes theme-init-script in <head>
- Uses Alpine.js for mobile menu (x-data="{ open: false }")
- Renders theme-toggle component in header (desktop and mobile)
- Applies theme-transition class for smooth theme changes

### Known Issues

1. Theme toggle button may not be responding to clicks
2. Possible JavaScript initialization conflict
3. Alpine.js x-data scope may be interfering with theme toggle
4. Event delegation may not be working correctly
5. Theme state may not be persisting correctly

### Investigation Areas

1. **JavaScript Console Errors**: Check for any errors when clicking theme toggle
2. **Event Listener Registration**: Verify click event is properly attached
3. **DOM Element Selection**: Confirm theme toggle button exists in DOM
4. **LocalStorage Access**: Verify localStorage is accessible and writable
5. **Alpine.js Conflicts**: Check if Alpine.js is interfering with vanilla JS
6. **CSS Class Application**: Verify 'dark' class is being added/removed from <html>
7. **Icon Visibility**: Confirm sun/moon icons are toggling correctly

## Success Criteria

The theme switcher fix will be considered successful when:

1. ✅ Theme toggle button responds to clicks on both desktop and mobile
2. ✅ Theme preference persists across page reloads
3. ✅ No FOUT (Flash of Unstyled Theme) occurs on page load
4. ✅ All theme-dependent UI elements update correctly
5. ✅ WCAG 2.2 AA compliance is maintained
6. ✅ No JavaScript errors in browser console
7. ✅ Theme switcher works in all major browsers (Chrome, Firefox, Safari, Edge)
8. ✅ Performance remains optimal (< 50ms theme switch time)

## References

- D12 §6.10: Motion and Animation Guidelines
- D14 §6.1.2: Theme System Architecture
- D14 §8.1: Accessibility Compliance
- D00-PREPLANNING §2.1-2.4: Theme System Requirements
- WCAG 2.2 SC 1.4.3: Contrast (Minimum)
- WCAG 2.2 SC 2.1.1: Keyboard
- WCAG 2.2 SC 2.4.7: Focus Visible
- WCAG 2.2 SC 2.3.1: Three Flashes or Below Threshold
