# ARIA Landmarks and Semantic Structure Implementation

## Overview

This document describes the comprehensive ARIA landmarks and semantic HTML structure implementation for the ICTServe application, ensuring WCAG 2.2 Level AA compliance for accessibility.

## Document Metadata

- **Name**: ARIA Landmarks and Semantic Structure Documentation
- **Author**: dev-team@motac.gov.my
- **Trace**: D03 §2.2 (Accessibility Requirements), D04 §3.1 (Accessibility Design), D12 §4.1 (Semantic Structure), D14 §9.4 (Accessibility)
- **Standards**: WCAG 2.2 Level AA (SC 1.3.1, 2.4.1, 2.4.6, 4.1.3)
- **Last Updated**: 2025-10-29

## WCAG 2.2 Success Criteria Addressed

### SC 1.3.1 Info and Relationships (Level A)

- Proper semantic HTML5 elements (`<header>`, `<nav>`, `<main>`, `<footer>`, `<section>`, `<article>`, `<aside>`)
- ARIA landmarks for programmatic identification of page regions
- Proper heading hierarchy (H1-H6)

### SC 2.4.1 Bypass Blocks (Level A)

- Skip links to main content, navigation, and footer
- Keyboard shortcuts for quick navigation
- Proper focus management

### SC 2.4.6 Headings and Labels (Level AA)

- Descriptive headings that describe topic or purpose
- Proper heading hierarchy without skipping levels
- Semantic heading component for consistency

### SC 4.1.3 Status Messages (Level AA)

- ARIA live regions for dynamic content announcements
- Proper politeness levels (polite, assertive)
- Screen reader compatible announcements

## Implementation Components

### 1. ARIA Live Region Component

**Location**: `resources/views/components/accessibility/aria-live-region.blade.php`

**Purpose**: Announces dynamic content changes to screen readers without requiring focus.

**Features**:
- Configurable politeness levels (polite, assertive, off)
- Atomic updates for complete message reading
- JavaScript helper function for programmatic announcements
- Livewire event integration
- Auto-clear after 5 seconds to avoid clutter

**Usage**:
```blade
<x-accessibility.aria-live-region id="aria-live-region" />

<!-- Announce from JavaScript -->
<script>
window.announceToScreenReader('Form submitted successfully', 'polite');
</script>

<!-- Announce from Livewire -->
$this->dispatch('announce', message: 'Ticket created', politeness: 'polite');
```

### 2. Heading Hierarchy Component

**Location**: `resources/views/components/accessibility/heading-hierarchy.blade.php`

**Purpose**: Ensures proper heading hierarchy while allowing flexible visual styling.

**Features**:
- Semantic level (H1-H6) separate from visual styling
- Default styling based on level
- Customizable classes
- Optional ID for anchor links

**Usage**:
```blade
<!-- Semantic H2 with H1 visual styling -->
<x-accessibility.heading-hierarchy :level="2" :visualLevel="1">
    Page Title
</x-accessibility.heading-hierarchy>

<!-- Standard H3 -->
<x-accessibility.heading-hierarchy :level="3" id="section-heading">
    Section Title
</x-accessibility.heading-hierarchy>
```

### 3. Keyboard Shortcuts Component

**Location**: `resources/views/components/accessibility/keyboard-shortcuts.blade.php`

**Purpose**: Provides keyboard shortcuts for enhanced navigation and accessibility.

**Default Shortcuts**:
- `Alt+H`: Go to homepage
- `Alt+M`: Skip to main content
- `Alt+N`: Skip to navigation
- `Alt+F`: Skip to footer
- `Alt+S`: Focus search input
- `?`: Show keyboard shortcuts help dialog

**Features**:
- Modal dialog with shortcut list
- Keyboard-triggered help (press `?`)
- Escape key to close
- Focus management
- Bilingual support

**Usage**:
```blade
<x-accessibility.keyboard-shortcuts />

<!-- Custom shortcuts -->
<x-accessibility.keyboard-shortcuts :shortcuts="[
    ['key' => 'Alt+D', 'action' => 'dashboard', 'description' => 'common.Go to dashboard'],
    ['key' => 'Alt+T', 'action' => 'tickets', 'description' => 'common.Go to tickets']
]" />
```

### 4. Skip Links Component

**Location**: `resources/views/components/accessibility/skip-links.blade.php`

**Purpose**: Provides skip links for keyboard users to bypass repetitive content.

**Features**:
- Hidden until focused
- High contrast styling
- Smooth scroll to target
- Customizable links
- Bilingual labels

**Usage**:
```blade
<x-accessibility.skip-links :links="[
    ['href' => '#main-content', 'label' => 'common.Skip to main content'],
    ['href' => '#main-navigation', 'label' => 'common.Skip to navigation'],
    ['href' => '#footer', 'label' => 'common.Skip to footer']
]" />
```

## ARIA Landmarks Implementation

### Banner Landmark (`role="banner"`)

**Purpose**: Identifies the header/masthead of the page.

**Implementation**:
- Applied to `<header>` elements in layouts
- Contains site branding, logo, and primary navigation
- Should appear once per page
- Includes `aria-label` for clarity

**Example**:
```blade
<header role="banner" aria-label="{{ __('common.Site header') }}">
    <!-- Logo, navigation, user menu -->
</header>
```

### Navigation Landmark (`role="navigation"`)

**Purpose**: Identifies navigation sections.

**Implementation**:
- Applied to `<nav>` elements
- Each navigation has descriptive `aria-label`
- Multiple navigations distinguished by labels
- Includes breadcrumbs, main menu, footer links

**Example**:
```blade
<nav role="navigation" aria-label="{{ __('common.Main navigation') }}">
    <!-- Navigation links -->
</nav>

<nav role="navigation" aria-label="{{ __('common.Breadcrumb') }}">
    <!-- Breadcrumb trail -->
</nav>
```

### Main Landmark (`role="main"`)

**Purpose**: Identifies the main content of the page.

**Implementation**:
- Applied to `<main>` element
- Should appear once per page
- Includes `tabindex="-1"` for programmatic focus
- Target of skip links

**Example**:
```blade
<main id="main-content" 
      role="main" 
      aria-label="{{ __('common.Main content') }}"
      tabindex="-1">
    <!-- Page content -->
</main>
```

### Complementary Landmark (`role="complementary"`)

**Purpose**: Identifies supporting content related to main content.

**Implementation**:
- Applied to `<aside>` elements
- Used for sidebars, announcements, related content
- Includes descriptive `aria-label`

**Example**:
```blade
<aside role="complementary" aria-label="{{ __('common.Announcement') }}">
    <!-- Announcement content -->
</aside>
```

### Contentinfo Landmark (`role="contentinfo"`)

**Purpose**: Identifies the footer with site-wide information.

**Implementation**:
- Applied to `<footer>` element
- Contains copyright, contact info, footer links
- Should appear once per page
- Includes `aria-label` for clarity

**Example**:
```blade
<footer role="contentinfo" aria-label="{{ __('common.Site footer') }}">
    <!-- Footer content -->
</footer>
```

### Region Landmark (`role="region"`)

**Purpose**: Identifies significant sections that users may want to navigate to.

**Implementation**:
- Applied to `<section>` elements or divs
- Always includes `aria-label` or `aria-labelledby`
- Used for major page sections

**Example**:
```blade
<section role="region" aria-labelledby="stats-heading">
    <h2 id="stats-heading">System Statistics</h2>
    <!-- Statistics content -->
</section>
```

## Semantic HTML Structure

### Proper Element Usage

1. **`<header>`**: Page or section header
2. **`<nav>`**: Navigation sections
3. **`<main>`**: Primary page content
4. **`<article>`**: Self-contained content (blog posts, cards)
5. **`<section>`**: Thematic grouping of content
6. **`<aside>`**: Tangentially related content
7. **`<footer>`**: Page or section footer
8. **`<address>`**: Contact information

### Heading Hierarchy

**Rules**:
- Start with H1 (one per page)
- Don't skip levels (H1 → H2 → H3, not H1 → H3)
- Use headings to create document outline
- Headings describe content that follows

**Example Structure**:
```
H1: Page Title (ICTServe)
  H2: Main Section
    H3: Subsection
    H3: Another Subsection
  H2: Another Main Section
    H3: Subsection
```

## Layout-Specific Implementation

### App Layout (`resources/views/layouts/app.blade.php`)

**Landmarks**:
- Banner: Header with logo and navigation
- Navigation: Main navigation menu
- Navigation: Breadcrumbs (when present)
- Complementary: Announcement banner
- Main: Page content
- Contentinfo: Footer

**Features**:
- Skip links to main content, navigation, footer
- ARIA live region for announcements
- Keyboard shortcuts helper
- Proper heading hierarchy

### Guest Layout (`resources/views/layouts/guest.blade.php`)

**Landmarks**:
- Banner: Header with branding
- Main: Authentication form
- Contentinfo: Footer

**Features**:
- Skip links to main content and footer
- ARIA live region for form feedback
- Keyboard shortcuts helper
- Simplified structure for auth pages

### Public Layout (`resources/views/components/layout/public.blade.php`)

**Landmarks**:
- Banner: Navbar component
- Navigation: Main navigation
- Main: Page content
- Contentinfo: Footer with multiple sections

**Features**:
- Skip links to all major sections
- ARIA live region for dynamic updates
- Keyboard shortcuts helper
- Comprehensive footer navigation

## Screen Reader Compatibility

### Announcements

**Polite Announcements** (don't interrupt):
- Form submission success
- Content loaded
- Non-critical updates

**Assertive Announcements** (interrupt current reading):
- Errors
- Critical alerts
- Time-sensitive information

### Hidden Content

**`.sr-only` Class**: Content visible only to screen readers
```css
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}
```

**`aria-hidden="true"`**: Hide decorative elements from screen readers
```blade
<svg aria-hidden="true"><!-- Decorative icon --></svg>
```

## Focus Management

### Programmatic Focus

**Skip Link Targets**:
```javascript
const mainContent = document.getElementById('main-content');
mainContent.setAttribute('tabindex', '-1');
mainContent.focus();
```

**Modal Dialogs**:
- Focus first interactive element on open
- Trap focus within modal
- Return focus to trigger on close

### Focus Indicators

**Requirements**:
- Visible focus indicator (3px outline minimum)
- 3:1 contrast ratio with background
- 2px offset from element
- Consistent across all interactive elements

## Testing Checklist

### Manual Testing

- [ ] All skip links work and focus correct elements
- [ ] Keyboard shortcuts function correctly
- [ ] Heading hierarchy is logical (no skipped levels)
- [ ] All landmarks are properly labeled
- [ ] ARIA live regions announce updates
- [ ] Focus indicators are visible
- [ ] Tab order is logical
- [ ] No keyboard traps

### Screen Reader Testing

- [ ] NVDA (Windows): All landmarks announced
- [ ] JAWS (Windows): Navigation works correctly
- [ ] VoiceOver (macOS): Proper structure read
- [ ] TalkBack (Android): Mobile navigation works
- [ ] Live regions announce updates appropriately

### Automated Testing

```bash
# Lighthouse accessibility audit
npm run lighthouse

# axe DevTools scan
npm run axe

# Pa11y automated testing
npm run pa11y
```

## Browser Support

- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support
- Mobile browsers: Full support

## Related Documentation

- [Color Contrast and Visual Accessibility](./color-contrast-accessibility.md)
- [Form Accessibility](./form-accessibility.md)
- [Keyboard Navigation](./keyboard-navigation.md)
- [Screen Reader Compatibility](./screen-reader-compatibility.md)

## References

- [WCAG 2.2 Guidelines](https://www.w3.org/WAI/WCAG22/quickref/)
- [ARIA Authoring Practices Guide](https://www.w3.org/WAI/ARIA/apg/)
- [MDN Web Docs: ARIA](https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA)
- [WebAIM: Semantic Structure](https://webaim.org/techniques/semanticstructure/)
