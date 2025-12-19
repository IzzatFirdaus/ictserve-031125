# MyDS v2025.2 Compliance Guide

**Version**: 1.0.0  
**Last Updated**: 2025-12-17  
**Status**: Active  
**Applies To**: All frontend components, forms, and UI elements

## Overview

This guide documents the implementation of **Malaysia Government Design System (MyDS) v2025.2** and **WCAG 2.2 AA** compliance standards across the ICTServe frontend codebase.

## Design Tokens

### Touch Targets

- **Minimum Height**: `min-h-11` (44px) for all interactive elements
- **Applies To**: Buttons, inputs, links, clickable cards

### Border Radius

- **Standard**: `rounded-lg` (8px) for inputs, buttons, cards
- **Small**: `rounded-md` (6px) for badges, tags
- **Full**: `rounded-full` for avatars, pills

### Focus Indicators

- **Ring Width**: `focus-visible:ring-3` (3px)
- **Ring Color**: `focus-visible:ring-primary-500`
- **Danger Actions**: `focus-visible:ring-danger-500`

### Semantic Colors

| Token | Usage | Tailwind Class |
|-------|-------|----------------|
| `primary` | Primary actions, links | `bg-primary-500`, `text-primary-600` |
| `success` | Success states, confirmations | `bg-success-500`, `text-success-600` |
| `warning` | Warnings, caution states | `bg-warning-500`, `text-warning-600` |
| `danger` | Errors, destructive actions | `bg-danger-500`, `text-danger-600` |
| `info` | Informational messages | `bg-info-500`, `text-info-600` |

## Component Standards

### Text Inputs

```blade
<input 
    type="text"
    class="min-h-11 rounded-lg border-gray-300 
           focus-visible:ring-3 focus-visible:ring-primary-500 
           focus-visible:border-primary-500"
/>
```

### Primary Buttons

```blade
<button 
    type="submit"
    class="min-h-11 px-4 rounded-lg bg-primary-600 
           text-white hover:bg-primary-700
           focus-visible:ring-3 focus-visible:ring-primary-500"
>
    Submit
</button>
```

### Danger Buttons

```blade
<button 
    type="button"
    class="min-h-11 px-4 rounded-lg bg-danger-600 
           text-white hover:bg-danger-700
           focus-visible:ring-3 focus-visible:ring-danger-500"
>
    Delete
</button>
```

## Updated Components

### Core Components (`resources/views/components/`)

#### ✅ text-input.blade.php

- Changed: `rounded-md` → `rounded-lg`
- Added: `focus-visible:ring-3 focus-visible:ring-primary-500`
- Ensured: `min-h-11`

#### ✅ primary-button.blade.php

- Changed: `rounded-md` → `rounded-lg`
- Added: `focus-visible:ring-3`
- Ensured: `min-h-11`

#### ✅ secondary-button.blade.php

- Changed: `rounded-md` → `rounded-lg`
- Updated: Focus ring styles

#### ✅ danger-button.blade.php

- Changed: `rounded-md` → `rounded-lg`
- Added: `focus-visible:ring-danger-500`

### Authentication Forms

#### ✅ Login (`filament/pages/auth/login.blade.php`)

- Standardized input height: `min-h-11`
- Border radius: `rounded-lg`
- Focus indicators: `focus-visible:ring-3`

#### ✅ Register (`livewire/pages/auth/register.blade.php`)

- Password strength indicators: Updated to semantic tokens
- Email validation: Uses `success` and `info` tokens
- All inputs: `min-h-11`, `rounded-lg`

#### ✅ Forgot Password (`livewire/pages/auth/forgot-password.blade.php`)

- Applied standard input/button styling

#### ✅ Reset Password (`livewire/pages/auth/reset-password.blade.php`)

- Applied standard styling to all fields

#### ✅ Verify Email (`livewire/pages/auth/verify-email.blade.php`)

- Alert colors: Updated to `info` and `success` tokens
- Button styling: Standardized

#### ✅ Confirm Password (`livewire/pages/auth/confirm-password.blade.php`)

- Applied standard styling

### Profile Forms

#### ✅ Update Profile (`livewire/profile/update-profile-information-form.blade.php`)

- Text inputs: `min-h-11 rounded-lg focus-visible:ring-3`
- Verification button: Standardized

#### ✅ Update Password (`livewire/profile/update-password-form.blade.php`)

- All password fields: Standard styling applied

#### ✅ Delete Account (`livewire/profile/delete-user-form.blade.php`)

- Danger buttons: Updated
- Modal inputs: Standardized

### Filament Admin Pages

#### ✅ Admin Dashboard (`filament/pages/admin-dashboard.blade.php`)

- Touch targets: Verified `min-h-11`
- Colors: Updated to semantic tokens

#### ✅ Notification Center (`livewire/notification-center.blade.php`)

- Touch targets: Verified
- Colors: Updated

#### ✅ Unified Search (`filament/pages/unified-search.blade.php`)

- Touch targets: Verified
- Colors: Updated

#### ✅ Accessibility Compliance Page

- Updated to use semantic tokens

#### ✅ Submission History Page

- Table headers: `min-h-11`
- Sort buttons: Verified touch targets

### Staff Dashboard

#### ✅ Authenticated Dashboard (`livewire/staff/authenticated-dashboard.blade.php`)

- Updated to MyDS standards

#### ✅ Account Linking (`livewire/staff/account-linking.blade.php`)

- Updated to MyDS standards

## Verification Checklist

### Automated Checks

- [x] `npm run build` passes without errors
- [x] No CSS compilation warnings
- [x] Tailwind purge working correctly

### Manual Verification

- [x] Touch targets ≥44px (`min-h-11`)
- [x] Focus indicators visible (`ring-3`)
- [x] Color contrast meets WCAG 2.2 AA
- [x] Border radius consistent (`rounded-lg`)
- [x] Semantic color tokens used

### Browser Testing

- [ ] Chrome/Edge (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

## Migration Notes

### Breaking Changes

- **Border Radius**: Changed from `rounded-md` (6px) to `rounded-lg` (8px)
- **Visual Impact**: Slightly rounder corners on all inputs and buttons
- **Component Propagation**: Changes in base components affect entire application

### Non-Breaking Changes

- **Touch Targets**: Increased from `h-10` (40px) to `min-h-11` (44px)
- **Focus Rings**: Enhanced from `ring-2` to `ring-3` for better visibility

## Common Patterns

### Form Field with Label

```blade
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Field Label
    </label>
    <input 
        type="text"
        class="w-full min-h-11 rounded-lg border-gray-300
               focus-visible:ring-3 focus-visible:ring-primary-500"
    />
</div>
```

### Alert Messages

```blade
<!-- Success -->
<div class="p-4 rounded-lg bg-success-50 border border-success-200">
    <p class="text-success-800">Operation successful!</p>
</div>

<!-- Warning -->
<div class="p-4 rounded-lg bg-warning-50 border border-warning-200">
    <p class="text-warning-800">Please review this information.</p>
</div>

<!-- Danger -->
<div class="p-4 rounded-lg bg-danger-50 border border-danger-200">
    <p class="text-danger-800">An error occurred.</p>
</div>

<!-- Info -->
<div class="p-4 rounded-lg bg-info-50 border border-info-200">
    <p class="text-info-800">Additional information available.</p>
</div>
```

### Button Group

```blade
<div class="flex gap-3">
    <button class="min-h-11 px-4 rounded-lg bg-primary-600 text-white">
        Primary
    </button>
    <button class="min-h-11 px-4 rounded-lg bg-gray-200 text-gray-800">
        Secondary
    </button>
    <button class="min-h-11 px-4 rounded-lg bg-danger-600 text-white">
        Delete
    </button>
</div>
```

## Troubleshooting

### Issue: Focus ring not visible
**Solution**: Ensure `focus-visible:ring-3` is present and not overridden by `outline-none`

### Issue: Touch target too small
**Solution**: Use `min-h-11` instead of fixed `h-10` or `h-9`

### Issue: Wrong color token
**Solution**: Replace `amber-*` with `warning-*`, use semantic tokens

### Issue: Border radius inconsistent
**Solution**: Use `rounded-lg` for standard components, `rounded-md` only for small elements

## References

- **D12**: UI/UX Design Guide
- **D13**: UI/UX Frontend Framework
- **D14**: UI/UX Style Guide
- **MyDS v2025.2**: Malaysia Government Design System
- **WCAG 2.2 AA**: Web Content Accessibility Guidelines

## Compliance Status

| Category | Status | Notes |
|----------|--------|-------|
| Touch Targets | ✅ Complete | All interactive elements ≥44px |
| Focus Indicators | ✅ Complete | `ring-3` applied globally |
| Color Contrast | ✅ Complete | Semantic tokens validated |
| Border Radius | ✅ Complete | `rounded-lg` standardized |
| Semantic Colors | ✅ Complete | Replaced `amber-*` with `warning-*` |

---

**Maintained By**: BPM MOTAC Development Team  
**Next Review**: 2026-06-17
