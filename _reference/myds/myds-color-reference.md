# MYDS Color Reference for ICTServe

This document contains the complete MYDS color token reference for the ICTServe system. All UI components must strictly use these semantic tokens instead of hard-coded hex values.

## Primary Color Tokens

### Background Tokens

- `bg-primary-50` - Lightest primary background
- `bg-primary-100` - Very light primary background
- `bg-primary-200` - Light primary background
- `bg-primary-300` - Medium-light primary background
- `bg-primary-400` - Medium primary background
- `bg-primary-500` - Base primary background
- `bg-primary-600` - **Primary button background** (#2563EB)
- `bg-primary-700` - Dark primary background
- `bg-primary-800` - Darker primary background
- `bg-primary-900` - Very dark primary background
- `bg-primary-950` - Darkest primary background

### Text Tokens

- `text-primary-50` - Lightest primary text
- `text-primary-100` - Very light primary text
- `text-primary-200` - Light primary text
- `text-primary-300` - Medium-light primary text
- `text-primary-400` - Medium primary text
- `text-primary-500` - Base primary text
- `text-primary-600` - **Primary link text** (#2563EB)
- `text-primary-700` - Dark primary text
- `text-primary-800` - Darker primary text
- `text-primary-900` - Very dark primary text
- `text-primary-950` - Darkest primary text

### Border Tokens

- `border-primary-200` - Light primary border
- `border-primary-300` - Medium-light primary border
- `border-primary-600` - **Primary active border** (#2563EB)

## Danger/Critical Color Tokens

### Background Tokens

- `bg-danger-50` - Lightest danger background
- `bg-danger-100` - Very light danger background
- `bg-danger-200` - Light danger background
- `bg-danger-300` - Medium-light danger background
- `bg-danger-400` - Medium danger background
- `bg-danger-500` - Base danger background
- `bg-danger-600` - **Destructive button background** (#DC2626)
- `bg-danger-700` - Dark danger background
- `bg-danger-800` - Darker danger background
- `bg-danger-900` - Very dark danger background
- `bg-danger-950` - Darkest danger background

### Text Tokens

- `text-danger-50` - Lightest danger text
- `text-danger-100` - Very light danger text
- `text-danger-200` - Light danger text
- `text-danger-300` - Medium-light danger text
- `text-danger-400` - Medium danger text
- `text-danger-500` - Base danger text
- `text-danger-600` - **Error message text** (#DC2626)
- `text-danger-700` - Dark danger text
- `text-danger-800` - Darker danger text
- `text-danger-900` - Very dark danger text
- `text-danger-950` - Darkest danger text

## Success Color Tokens

### Background Tokens

- `bg-success-50` - Lightest success background
- `bg-success-100` - Very light success background
- `bg-success-200` - Light success background
- `bg-success-300` - Medium-light success background
- `bg-success-400` - Medium success background
- `bg-success-500` - Base success background
- `bg-success-600` - **Success notification background** (#16A34A)
- `bg-success-700` - Dark success background
- `bg-success-800` - Darker success background
- `bg-success-900` - Very dark success background
- `bg-success-950` - Darkest success background

### Text Tokens

- `text-success-50` - Lightest success text
- `text-success-100` - Very light success text
- `text-success-200` - Light success text
- `text-success-300` - Medium-light success text
- `text-success-400` - Medium success text
- `text-success-500` - Base success text
- `text-success-600` - **Success message text** (#16A34A)
- `text-success-700` - Dark success text
- `text-success-800` - Darker success text
- `text-success-900` - Very dark success text
- `text-success-950` - Darkest success text

## Warning Color Tokens

### Background Tokens

- `bg-warning-50` - Lightest warning background
- `bg-warning-100` - Very light warning background
- `bg-warning-200` - Light warning background
- `bg-warning-300` - Medium-light warning background
- `bg-warning-400` - Medium warning background
- `bg-warning-500` - **Warning notification background** (#EAB308)
- `bg-warning-600` - Base warning background
- `bg-warning-700` - Dark warning background
- `bg-warning-800` - Darker warning background
- `bg-warning-900` - Very dark warning background
- `bg-warning-950` - Darkest warning background

### Text Tokens

- `text-warning-50` - Lightest warning text
- `text-warning-100` - Very light warning text
- `text-warning-200` - Light warning text
- `text-warning-300` - Medium-light warning text
- `text-warning-400` - Medium warning text
- `text-warning-500` - **Warning message text** (#EAB308)
- `text-warning-600` - Base warning text
- `text-warning-700` - Dark warning text
- `text-warning-800` - Darker warning text
- `text-warning-900` - Very dark warning text
- `text-warning-950` - Darkest warning text

## Neutral/Gray Color Tokens

### Background Tokens

- `bg-gray-50` - Lightest gray background (#FAFAFA)
- `bg-gray-100` - Very light gray background (#F4F4F5)
- `bg-gray-200` - Light gray background (#E4E4E7)
- `bg-gray-300` - Medium-light gray background (#D4D4D8)
- `bg-gray-400` - Medium gray background (#A1A1AA)
- `bg-gray-500` - Base gray background (#71717A)
- `bg-gray-600` - Dark gray background (#52525B)
- `bg-gray-700` - Darker gray background (#3F3F46)
- `bg-gray-800` - Very dark gray background (#27272A)
- `bg-gray-900` - Darkest gray background (#18181B)

### Text Tokens

- `text-gray-50` - Lightest gray text
- `text-gray-100` - Very light gray text
- `text-gray-200` - Light gray text
- `text-gray-300` - Medium-light gray text
- `text-gray-400` - Medium gray text
- `text-gray-500` - **Secondary/inactive text** (#71717A)
- `text-gray-600` - Dark gray text
- `text-gray-700` - **Body text** (#3F3F46)
- `text-gray-800` - Darker gray text
- `text-gray-900` - **Primary text** (#18181B)

## Surface and Layout Tokens

### Background Surfaces

- `bg-white` - White background
- `bg-background-light` - Light mode page background
- `bg-background-dark` - Dark mode page background
- `bg-surface` - Card/panel surface
- `bg-surface-elevated` - Elevated surface (modals, dropdowns)

### Borders and Dividers

- `border-divider` - Standard divider color (#E4E4E7)
- `border-input` - Input field border
- `border-focus` - Focused element border

## Focus Ring Tokens

### Focus States

- `ring-primary-300` - Primary focus ring
- `ring-danger-300` - Danger focus ring
- `ring-success-300` - Success focus ring
- `ring-warning-300` - Warning focus ring

## Usage Guidelines

### Mandatory Rules

1. **Never use raw hex codes** - Always use semantic tokens
2. **Primary color only for key actions** - Submit buttons, primary links
3. **Danger color only for errors/destructive actions** - Delete buttons, error messages
4. **Success color only for positive outcomes** - Success notifications, completed states
5. **Warning color for pending/caution states** - In-progress status, warnings

### Required Field Indicators

```html
<label for="field">Field Name <span class="text-danger-600">*</span></label>
```

### Button Color Usage

```html
<!-- Primary Action -->
<button class="bg-primary-600 text-white">Submit</button>

<!-- Destructive Action -->
<button class="bg-danger-600 text-white">Delete</button>

<!-- Secondary Action -->
<button class="bg-gray-200 text-gray-800">Cancel</button>
```

### Status Indicators

```html
<!-- Success Status -->
<span class="bg-success-100 text-success-700">Completed</span>

<!-- Warning Status -->
<span class="bg-warning-100 text-warning-700">Pending</span>

<!-- Danger Status -->
<span class="bg-danger-100 text-danger-700">Failed</span>
```

## Dark Mode Support

All tokens automatically map to appropriate dark mode values. The system handles the contrast and color inversions automatically when using semantic tokens.

## Typography Integration

Combine color tokens with typography classes:

```html
<!-- Page Title -->
<h1 class="font-poppins text-2xl font-semibold text-gray-900">Page Title</h1>

<!-- Section Heading -->
<h2 class="font-poppins text-xl font-semibold text-gray-800">Section</h2>

<!-- Body Text -->
<p class="font-inter text-sm font-normal text-gray-700 leading-relaxed">
  Body text content
</p>

<!-- Form Label -->
<label class="font-inter text-xs font-medium text-gray-600">Field Label</label>
```

This reference ensures all ICTServe components maintain visual consistency and accessibility compliance according to MYDS standards.
