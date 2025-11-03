# ICTServe Accessibility Guidelines

## WCAG 2.2 Level AA Compliance

This document outlines the accessibility standards and implementation guidelines for the ICTServe application to ensure WCAG 2.2 Level AA compliance.

## Table of Contents

1. [Overview](#overview)
2. [Color Contrast Requirements](#color-contrast-requirements)
3. [Keyboard Navigation](#keyboard-navigation)
4. [ARIA Labels and Semantic HTML](#aria-labels-and-semantic-html)
5. [Screen Reader Compatibility](#screen-reader-compatibility)
6. [Touch Target Sizes](#touch-target-sizes)
7. [Forms Accessibility](#forms-accessibility)
8. [Accessible Components](#accessible-components)
9. [Testing Procedures](#testing-procedures)
10. [Common Patterns](#common-patterns)

## Overview

The ICTServe application adheres to WCAG 2.2 Level AA standards to ensure accessibility for all users, including those with disabilities. This includes:

- Visual impairments (color blindness, low vision, blindness)
- Motor impairments (limited dexterity, tremors)
- Cognitive impairments
- Hearing impairments

## Color Contrast Requirements

### WCAG AA Standards

- **Normal text** (< 18pt or < 14pt bold): Minimum contrast ratio of **4.5:1**
- **Large text** (≥ 18pt or ≥ 14pt bold): Minimum contrast ratio of **3:1**
- **UI components and graphical objects**: Minimum contrast ratio of **3:1**

### Approved Color Palette

All colors in the MOTAC palette meet WCAG AA requirements when used appropriately:

#### Primary Colors

- `blue-900` (#1e3a8a) - Primary brand color
- `blue-800` (#1e40af)
- `blue-700` (#1d4ed8)
- `blue-600` (#2563eb)

#### Status Colors

- **Success**: `green-700` (#15803d), `green-600` (#16a34a)
- **Warning**: `yellow-700` (#a16207), `yellow-600` (#ca8a04)
- **Error**: `red-700` (#b91c1c), `red-600` (#dc2626)
- **Info**: `blue-700` (#1d4ed8), `blue-600` (#2563eb)

### Usage Guidelines

```blade
<!-- ✅ Good: Sufficient contrast -->
<div class="bg-blue-900 text-white">High contrast text</div>
<div class="bg-white text-gray-900">High contrast text</div>

<!-- ❌ Bad: Insufficient contrast -->
<div class="bg-gray-200 text-gray-400">Low contrast text</div>
```

## Keyboard Navigation

### Requirements

All interactive elements must be:
1. **Keyboard accessible** - Operable via keyboard alone
2. **Focusable** - Visible focus indicator with 2px outline
3. **Logical tab order** - Sequential and intuitive
4. **Escape mechanisms** - Ability to exit modals and menus

### Focus Indicators

Use the standard focus class for all interactive elements:

```blade
<button class="focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
    Click me
</button>
```

### Skip Links

Every page must include a skip-to-content link:

```blade
<x-accessible.skip-link target="#main-content" />
```

### Keyboard Shortcuts

| Key | Action |
|-----|--------|
| `Tab` | Move focus forward |
| `Shift + Tab` | Move focus backward |
| `Enter` / `Space` | Activate button or link |
| `Escape` | Close modal or dropdown |
| `Arrow keys` | Navigate within menus and lists |

## ARIA Labels and Semantic HTML

### Semantic HTML Structure

Use proper HTML5 semantic elements:

```blade
<header role="banner">
    <nav role="navigation" aria-label="Main navigation">
        <!-- Navigation items -->
    </nav>
</header>

<main id="main-content" role="main">
    <!-- Main content -->
</main>

<footer role="contentinfo">
    <!-- Footer content -->
</footer>
```

### ARIA Landmarks

Required landmarks for every page:

- `banner` - Site header
- `navigation` - Navigation menus
- `main` - Main content area
- `complementary` - Sidebar content
- `contentinfo` - Footer
- `search` - Search functionality
- `form` - Forms

### ARIA Labels

Provide descriptive labels for all interactive elements:

```blade
<!-- Button with icon only -->
<button aria-label="{{ __('Close modal') }}">
    <svg aria-hidden="true"><!-- Icon --></svg>
</button>

<!-- Form input -->
<label for="email">{{ __('Email Address') }}</label>
<input
    id="email"
    type="email"
    aria-required="true"
    aria-describedby="email-help"
>
<p id="email-help">{{ __('We will never share your email') }}</p>
```

### ARIA Live Regions

Use for dynamic content updates:

```blade
<!-- Polite announcements (default) -->
<div aria-live="polite" aria-atomic="true">
    {{ $statusMessage }}
</div>

<!-- Assertive announcements (urgent) -->
<div aria-live="assertive" aria-atomic="true">
    {{ $errorMessage }}
</div>
```

## Screen Reader Compatibility

### Screen Reader Only Text

Use the `.sr-only` class for screen reader only content:

```blade
<button>
    <svg aria-hidden="true"><!-- Icon --></svg>
    <span class="sr-only">{{ __('Delete item') }}</span>
</button>
```

### Meaningful Link Text

```blade
<!-- ✅ Good: Descriptive link text -->
<a href="/reports">{{ __('View detailed analytics report') }}</a>

<!-- ❌ Bad: Generic link text -->
<a href="/reports">{{ __('Click here') }}</a>
```

### Image Alt Text

```blade
<!-- Informative image -->
<img src="chart.png" alt="{{ __('Monthly ticket statistics showing 45% increase') }}">

<!-- Decorative image -->
<img src="decoration.png" alt="" aria-hidden="true">
```

## Touch Target Sizes

### WCAG 2.2 Requirements

All touch targets must be at least **44x44 pixels** (WCAG 2.2 Level AA).

### Implementation

```blade
<!-- Buttons -->
<x-accessible.button size="md">
    <!-- Minimum 44x44px -->
</x-accessible.button>

<!-- Links -->
<a href="#" class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-4 py-2">
    Link text
</a>

<!-- Icon buttons -->
<button class="p-2 min-h-[44px] min-w-[44px] flex items-center justify-center">
    <svg class="h-6 w-6"><!-- Icon --></svg>
</button>
```

## Forms Accessibility

### Form Structure

```blade
<form method="POST" action="/submit">
    @csrf

    <!-- Required field indicator -->
    <x-accessible.form-input
        name="name"
        label="{{ __('Full Name') }}"
        required
        help="{{ __('Enter your full legal name') }}"
        :error="$errors->first('name')"
    />

    <!-- Submit button -->
    <x-accessible.button type="submit">
        {{ __('Submit Form') }}
    </x-accessible.button>
</form>
```

### Error Handling

```blade
<!-- Error summary at top of form -->
@if($errors->any())
    <x-accessible.alert type="error" title="{{ __('Please correct the following errors:') }}" role="alert">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-accessible.alert>
@endif
```

### Field Validation

- Provide inline validation messages
- Use `aria-invalid="true"` for invalid fields
- Use `aria-describedby` to link error messages
- Show success indicators for valid fields

## Accessible Components

### Using Accessible Components

The application provides pre-built accessible components:

```blade
<!-- Button -->
<x-accessible.button
    variant="primary"
    size="md"
    ariaLabel="{{ __('Save changes') }}"
>
    {{ __('Save') }}
</x-accessible.button>

<!-- Form Input -->
<x-accessible.form-input
    name="email"
    type="email"
    label="{{ __('Email Address') }}"
    required
    help="{{ __('We will send confirmation to this email') }}"
/>

<!-- Alert -->
<x-accessible.alert
    type="success"
    title="{{ __('Success!') }}"
    dismissible
>
    {{ __('Your changes have been saved.') }}
</x-accessible.alert>

<!-- Modal -->
<x-accessible.modal
    name="confirm-delete"
    title="{{ __('Confirm Deletion') }}"
    maxWidth="md"
>
    <p>{{ __('Are you sure you want to delete this item?') }}</p>

    <div class="mt-4 flex justify-end gap-3">
        <x-accessible.button variant="outline" x-on:click="$dispatch('close-modal', 'confirm-delete')">
            {{ __('Cancel') }}
        </x-accessible.button>
        <x-accessible.button variant="danger">
            {{ __('Delete') }}
        </x-accessible.button>
    </div>
</x-accessible.modal>

<!-- Table -->
<x-accessible.table caption="{{ __('User List') }}">
    <thead>
        <tr>
            <th scope="col">{{ __('Name') }}</th>
            <th scope="col">{{ __('Email') }}</th>
            <th scope="col">{{ __('Role') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <th scope="row">{{ $user->name }}</th>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
            </tr>
        @endforeach
    </tbody>
</x-accessible.table>
```

## Testing Procedures

### Automated Testing

1. **Lighthouse Accessibility Audit**
   ```bash
   npm run lighthouse
   ```
   Target score: ≥ 90

2. **axe DevTools**
   - Install browser extension
   - Run on every page
   - Fix all violations

3. **WAVE Tool**
   - Use for visual feedback
   - Check contrast ratios
   - Verify ARIA usage

### Manual Testing

1. **Keyboard Navigation**
   - Navigate entire site using only keyboard
   - Verify all interactive elements are reachable
   - Check focus indicators are visible
   - Test modal and dropdown escape mechanisms

2. **Screen Reader Testing**
   - Test with NVDA (Windows) or VoiceOver (Mac)
   - Verify all content is announced
   - Check heading hierarchy
   - Test form labels and error messages

3. **Color Contrast**
   - Use browser DevTools color picker
   - Verify all text meets 4.5:1 ratio
   - Test in dark mode

4. **Touch Target Testing**
   - Use mobile device or emulator
   - Verify all buttons are easily tappable
   - Check spacing between interactive elements

5. **Zoom Testing**
   - Test at 200% zoom
   - Verify no horizontal scrolling
   - Check text reflow

## Common Patterns

### Loading States

```blade
<x-accessible.button loading wire:loading.attr="disabled">
    <span wire:loading.remove>{{ __('Save') }}</span>
    <span wire:loading>{{ __('Saving...') }}</span>
</x-accessible.button>

<div wire:loading aria-live="polite" aria-busy="true">
    <span class="sr-only">{{ __('Loading content') }}</span>
    <!-- Loading spinner -->
</div>
```

### Status Messages

```blade
<div role="status" aria-live="polite" aria-atomic="true">
    @if(session('success'))
        <x-accessible.alert type="success">
            {{ session('success') }}
        </x-accessible.alert>
    @endif
</div>
```

### Pagination

```blade
<nav aria-label="{{ __('Pagination') }}">
    <ul class="flex gap-2">
        <li>
            <a href="?page=1" aria-label="{{ __('Go to first page') }}">
                {{ __('First') }}
            </a>
        </li>
        <!-- More pagination items -->
    </ul>
</nav>
```

### Breadcrumbs

```blade
<nav aria-label="{{ __('Breadcrumb') }}">
    <ol class="flex gap-2">
        <li>
            <a href="/">{{ __('Home') }}</a>
        </li>
        <li aria-current="page">
            {{ __('Current Page') }}
        </li>
    </ol>
</nav>
```

## Resources

- [WCAG 2.2 Guidelines](https://www.w3.org/WAI/WCAG22/quickref/)
- [ARIA Authoring Practices](https://www.w3.org/WAI/ARIA/apg/)
- [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/)
- [Lighthouse Documentation](https://developers.google.com/web/tools/lighthouse)
- [axe DevTools](https://www.deque.com/axe/devtools/)

## Support

For accessibility questions or issues, contact the development team or refer to the [CONTRIBUTING.md](../CONTRIBUTING.md) guide.
