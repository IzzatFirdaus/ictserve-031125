# WCAG 2.2 Level AA Compliant Component Library

## Overview

This document describes the unified Blade component library for the ICTServe System, implementing WCAG 2.2 Level AA accessibility standards with MOTAC branding.

**Version**: 1.0.0  
**Last Updated**: 2025-10-31  
**Standards**: WCAG 2.2 Level AA, MOTAC Design System  
**Trace**: D03 §5.1, §6.1-6.5, §14.1-14.5, D04 §6.1, D12 §4, D14 §3-5

## MOTAC Compliant Color Palette

### Primary Colors
- **MOTAC Blue**: `#0056b3` (6.8:1 contrast ratio) - Primary brand color
- **Success**: `#198754` (4.9:1 contrast ratio) - Success states
- **Warning**: `#ff8c00` (4.5:1 contrast ratio) - Warning states with black text
- **Danger**: `#b50c0c` (8.2:1 contrast ratio) - Error/danger states
- **Info**: `#0dcaf0` - Informational states

### Deprecated Colors (DO NOT USE)
- ~~Warning Yellow `#F1C40F`~~ - Non-compliant, removed
- ~~Danger Red `#E74C3C`~~ - Non-compliant, removed

## Component Structure

```
resources/views/components/
├── layout/
│   ├── guest.blade.php          # Guest layout (no authentication)
│   ├── app.blade.php            # Authenticated layout
│   ├── header.blade.php         # Public header
│   ├── auth-header.blade.php    # Authenticated header
│   └── footer.blade.php         # Site footer
├── form/
│   ├── input.blade.php          # Text input with validation
│   ├── select.blade.php         # Dropdown select
│   ├── textarea.blade.php       # Multi-line text input
│   ├── checkbox.blade.php       # Checkbox input
│   └── file-upload.blade.php    # File upload with drag-drop
├── ui/
│   ├── button.blade.php         # Button component
│   ├── card.blade.php           # Card container
│   ├── alert.blade.php          # Alert messages
│   ├── badge.blade.php          # Status badges
│   └── modal.blade.php          # Modal dialogs
├── navigation/
│   ├── breadcrumbs.blade.php    # Breadcrumb navigation
│   ├── pagination.blade.php     # Pagination controls
│   └── skip-links.blade.php     # Skip navigation links
├── data/
│   ├── table.blade.php          # Data tables
│   ├── status-badge.blade.php   # Status indicators
│   └── progress-bar.blade.php   # Progress indicators
└── accessibility/
    ├── aria-live.blade.php      # ARIA live regions
    ├── focus-trap.blade.php     # Focus management
    └── language-switcher.blade.php # Language selection
```

## Accessibility Features

### Focus Indicators
- **Outline**: 3-4px solid outline
- **Offset**: 2px from element
- **Contrast**: Minimum 3:1 ratio
- **Color**: MOTAC Blue (#0056b3)

### Touch Targets
- **Minimum Size**: 44×44px for all interactive elements
- **Spacing**: Adequate spacing between targets
- **Visual Feedback**: Clear hover and active states

### Keyboard Navigation
- **Tab Order**: Logical and sequential
- **Skip Links**: Available on all pages
- **Focus Trap**: Implemented in modals
- **Escape Key**: Closes modals and dropdowns

### Screen Reader Support
- **ARIA Labels**: All interactive elements
- **ARIA Landmarks**: header, nav, main, footer
- **ARIA Live Regions**: Dynamic content announcements
- **Semantic HTML**: Proper HTML5 structure

## Component Usage Examples

### Button Component

```blade
<!-- Primary Button -->
<x-ui.button variant="primary" type="submit">
    {{ __('Submit') }}
</x-ui.button>

<!-- Success Button with Icon -->
<x-ui.button variant="success" icon="check">
    {{ __('Approve') }}
</x-ui.button>

<!-- Danger Button -->
<x-ui.button variant="danger" size="lg">
    {{ __('Delete') }}
</x-ui.button>

<!-- Loading State -->
<x-ui.button variant="primary" :loading="true">
    {{ __('Processing...') }}
</x-ui.button>
```

### Form Input Component

```blade
<!-- Text Input with Validation -->
<x-form.input
    name="email"
    type="email"
    label="{{ __('Email Address') }}"
    :required="true"
    placeholder="{{ __('Enter your email') }}"
    help="{{ __('We will never share your email') }}"
/>

<!-- Input with Error -->
<x-form.input
    name="name"
    label="{{ __('Full Name') }}"
    :required="true"
    :value="old('name')"
/>
```

### Alert Component

```blade
<!-- Success Alert -->
<x-ui.alert type="success" :dismissible="true">
    {{ __('Your changes have been saved successfully.') }}
</x-ui.alert>

<!-- Error Alert with Title -->
<x-ui.alert type="error" title="{{ __('Validation Error') }}">
    {{ __('Please correct the errors below.') }}
</x-ui.alert>

<!-- Warning Alert -->
<x-ui.alert type="warning" :border="true">
    {{ __('This action cannot be undone.') }}
</x-ui.alert>
```

### Badge Component

```blade
<!-- Status Badges -->
<x-ui.badge variant="success">{{ __('Active') }}</x-ui.badge>
<x-ui.badge variant="warning">{{ __('Pending') }}</x-ui.badge>
<x-ui.badge variant="danger">{{ __('Closed') }}</x-ui.badge>

<!-- Large Badge -->
<x-ui.badge variant="primary" size="lg">
    {{ __('New') }}
</x-ui.badge>
```

### Progress Bar Component

```blade
<!-- Basic Progress Bar -->
<x-data.progress-bar
    :value="75"
    :max="100"
    label="{{ __('Upload Progress') }}"
/>

<!-- Success Progress Bar -->
<x-data.progress-bar
    :value="100"
    :max="100"
    variant="success"
    label="{{ __('Complete') }}"
/>

<!-- Large Progress Bar -->
<x-data.progress-bar
    :value="50"
    :max="100"
    size="lg"
    variant="primary"
/>
```

### Language Switcher Component

```blade
<!-- Default Variant -->
<x-accessibility.language-switcher />

<!-- Compact Variant -->
<x-accessibility.language-switcher variant="compact" />

<!-- Dropdown Variant -->
<x-accessibility.language-switcher variant="dropdown" />
```

### Breadcrumbs Component

```blade
<x-navigation.breadcrumbs :items="[
    ['label' => __('Dashboard'), 'url' => route('dashboard')],
    ['label' => __('Tickets'), 'url' => route('tickets.index')],
    ['label' => __('View Ticket'), 'url' => '']
]" />
```

### Modal Component

```blade
<!-- Modal Trigger -->
<x-ui.button
    x-data=""
    x-on:click.prevent="$dispatch('open-modal', 'confirm-delete')"
>
    {{ __('Delete') }}
</x-ui.button>

<!-- Modal Definition -->
<x-ui.modal name="confirm-delete" :show="false" focusable>
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Are you sure?') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('This action cannot be undone.') }}
        </p>

        <div class="mt-6 flex justify-end space-x-3">
            <x-ui.button
                variant="secondary"
                x-on:click="$dispatch('close-modal', 'confirm-delete')"
            >
                {{ __('Cancel') }}
            </x-ui.button>

            <x-ui.button variant="danger">
                {{ __('Delete') }}
            </x-ui.button>
        </div>
    </div>
</x-ui.modal>
```

## Responsive Design

### Breakpoints
- **Mobile**: 320px - 414px
- **Tablet**: 768px - 1024px
- **Desktop**: 1280px - 1920px

### Responsive Utilities
```blade
<!-- Responsive Grid -->
<div class="grid-responsive">
    <!-- Content -->
</div>

<!-- Responsive Container -->
<div class="container-responsive">
    <!-- Content -->
</div>
```

## Performance Optimization

### CSS Classes
- Use Tailwind utility classes
- Purge unused styles in production
- Minimize custom CSS

### Component Loading
- Lazy load heavy components
- Use Livewire wire:loading for feedback
- Implement skeleton screens

## Testing Components

### Accessibility Testing
```bash
# Run accessibility checks
php artisan test --filter=AccessibilityTest
```

### Visual Regression Testing
```bash
# Run visual tests
npm run test:visual
```

### Browser Compatibility
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+ (Chromium)

## Component Metadata

All components include standardized metadata:
- **name**: Component identifier
- **description**: Component purpose
- **author**: Development team
- **trace**: Requirements traceability
- **standards**: WCAG compliance level
- **browsers**: Supported browsers
- **created_at**: Creation date
- **updated_at**: Last modification date
- **version**: Semantic version

## Best Practices

### DO
✓ Use semantic HTML5 elements
✓ Include ARIA attributes
✓ Provide keyboard navigation
✓ Ensure 44×44px touch targets
✓ Use compliant color palette
✓ Test with screen readers
✓ Validate with WAVE/axe tools

### DON'T
✗ Use deprecated colors (#F1C40F, #E74C3C)
✗ Rely on color alone for information
✗ Create touch targets smaller than 44×44px
✗ Skip ARIA labels on interactive elements
✗ Use inline styles (use Tailwind classes)
✗ Forget to test keyboard navigation

## Support

For questions or issues with the component library:
- Review this documentation
- Check D12 UI/UX Design Guide
- Consult D14 MOTAC Style Guide
- Contact dev-team@motac.gov.my

## Version History

### 1.0.0 (2025-10-31)
- Initial release
- WCAG 2.2 Level AA compliant components
- MOTAC branding implementation
- Responsive design support
- Bilingual support (BM/EN)
- Complete component library structure
