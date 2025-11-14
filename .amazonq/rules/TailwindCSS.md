---
applyTo:
  - 'resources/views/**'
  - 'app/Livewire/**'
  - 'app/Filament/**'
  - '**/*.blade.php'
description: |
  Tailwind CSS 3 utility classes, responsive design, dark mode patterns,
  and component composition for ICTServe project.
tags:
  - tailwind
  - css
  - responsive
  - dark-mode
  - accessibility
version: '1.0.0'
lastUpdated: '2025-01-06'
---

# Tailwind CSS 3 — ICTServe Styling Standards

## Overview

This rule defines Tailwind CSS 3 utility class conventions for ICTServe. Covers responsive design, dark mode support, spacing, typography, component patterns, and accessibility-focused styling aligned with WCAG 2.2 AA.

**Framework**: Tailwind CSS 3.x
**Applies To**: Blade views, Livewire components, Filament customizations
**Traceability**: D14 (UI/UX Design Guide), D15 (Accessibility Requirements), WCAG 2.2 AA

## Core Principles

1. **Utility-First**: Use utility classes before creating custom CSS
2. **Responsive by Default**: Mobile-first design with breakpoint modifiers
3. **Dark Mode Support**: Use `dark:` prefix for dark mode variants
4. **Consistent Spacing**: Use Tailwind's spacing scale (1 unit = 0.25rem)
5. **Accessible Colors**: Ensure sufficient contrast ratios (WCAG 2.2 AA)

---

## Spacing System

**Tailwind Spacing Scale** (1 unit = 0.25rem = 4px):

| Class | Size | Pixels | Use Case |
|-------|------|--------|----------|
| `p-1` | 0.25rem | 4px | Tight padding |
| `p-2` | 0.5rem | 8px | Small padding |
| `p-4` | 1rem | 16px | Standard padding |
| `p-6` | 1.5rem | 24px | Medium padding |
| `p-8` | 2rem | 32px | Large padding |
| `p-12` | 3rem | 48px | Extra large padding |

**Use `gap` for Spacing Lists** (NOT margins):

```html
<!-- ✅ GOOD: Use gap utilities -->
<div class="flex gap-8">
    <div>Item 1</div>
    <div>Item 2</div>
    <div>Item 3</div>
</div>

<!-- ❌ BAD: Using margins -->
<div class="flex">
    <div class="mr-8">Item 1</div>
    <div class="mr-8">Item 2</div>
    <div>Item 3</div>
</div>
```

---

## Responsive Design (Mobile-First)

**Breakpoint Modifiers**:

| Prefix | Min Width | Device |
|--------|-----------|--------|
| (none) | 0px | Mobile (default) |
| `sm:` | 640px | Small tablets |
| `md:` | 768px | Tablets |
| `lg:` | 1024px | Laptops |
| `xl:` | 1280px | Desktops |
| `2xl:` | 1536px | Large screens |

**Mobile-First Pattern**:

```html
<!-- Start with mobile, add breakpoints upward -->
<div class="w-full md:w-1/2 lg:w-1/3">
    <!-- Full width on mobile, half on tablets, third on laptops -->
</div>

<div class="text-sm sm:text-base lg:text-lg">
    <!-- Small text on mobile, base on tablets, large on laptops -->
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- 1 column mobile, 2 columns tablet, 3 columns laptop -->
    <div>Item 1</div>
    <div>Item 2</div>
    <div>Item 3</div>
</div>
```

---

## Dark Mode Support

**Enable Dark Mode** (`tailwind.config.js`):

```javascript
module.exports = {
  darkMode: 'class', // or 'media' for system preference
  // ... rest of config
}
```

**Dark Mode Classes**:

```html
<!-- Background colors -->
<div class="bg-white dark:bg-gray-800">
    Content adapts to dark mode
</div>

<!-- Text colors -->
<p class="text-gray-900 dark:text-gray-100">
    Text with dark mode variant
</p>

<!-- Border colors -->
<div class="border border-gray-300 dark:border-gray-700">
    Border adapts to dark mode
</div>
```

**Complete Dark Mode Component**:

```html
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        Tajuk Kad
    </h2>
    <p class="text-gray-600 dark:text-gray-400">
        Keterangan yang sesuai untuk mod gelap dan terang.
    </p>
    <button class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-4 py-2 rounded">
        Tindakan
    </button>
</div>
```

---

## Layout Patterns

### Flexbox

**Horizontal Layout**:

```html
<div class="flex items-center justify-between gap-4">
    <div>Left</div>
    <div>Center</div>
    <div>Right</div>
</div>
```

**Vertical Layout**:

```html
<div class="flex flex-col gap-4">
    <div>Top</div>
    <div>Middle</div>
    <div>Bottom</div>
</div>
```

**Centered Content**:

```html
<div class="flex items-center justify-center min-h-screen">
    <div>Centered Vertically & Horizontally</div>
</div>
```

---

### Grid Layout

**Equal Columns**:

```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div>Column 1</div>
    <div>Column 2</div>
    <div>Column 3</div>
</div>
```

**Custom Column Widths**:

```html
<div class="grid grid-cols-12 gap-4">
    <div class="col-span-12 md:col-span-8">Main Content (8/12)</div>
    <div class="col-span-12 md:col-span-4">Sidebar (4/12)</div>
</div>
```

**Auto-Fit Grid** (responsive without breakpoints):

```html
<div class="grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-4">
    <!-- Columns auto-adjust based on container width -->
    <div>Card 1</div>
    <div>Card 2</div>
    <div>Card 3</div>
</div>
```

---

## Typography

### Text Sizes

```html
<h1 class="text-4xl font-bold">Heading 1</h1>
<h2 class="text-3xl font-semibold">Heading 2</h2>
<h3 class="text-2xl font-semibold">Heading 3</h3>
<p class="text-base">Body text (16px default)</p>
<small class="text-sm">Small text (14px)</small>
```

### Font Weights

```html
<p class="font-thin">Thin (100)</p>
<p class="font-light">Light (300)</p>
<p class="font-normal">Normal (400)</p>
<p class="font-medium">Medium (500)</p>
<p class="font-semibold">Semibold (600)</p>
<p class="font-bold">Bold (700)</p>
<p class="font-extrabold">Extra Bold (800)</p>
```

### Text Colors (Accessible)

**Light Mode**:

```html
<p class="text-gray-900">Primary text (highest contrast)</p>
<p class="text-gray-600">Secondary text</p>
<p class="text-gray-400">Muted text</p>
```

**Dark Mode**:

```html
<p class="dark:text-gray-100">Primary text (dark mode)</p>
<p class="dark:text-gray-400">Secondary text (dark mode)</p>
<p class="dark:text-gray-600">Muted text (dark mode)</p>
```

---

## Color System

**Semantic Colors** (ICTServe Palette):

```html
<!-- Primary (Blue) -->
<button class="bg-blue-600 hover:bg-blue-700 text-white">Primary Action</button>

<!-- Success (Green) -->
<div class="bg-green-100 border border-green-500 text-green-700 p-4">
    Operasi berjaya!
</div>

<!-- Warning (Amber) -->
<div class="bg-amber-100 border border-amber-500 text-amber-700 p-4">
    Amaran: Sila semak maklumat.
</div>

<!-- Danger (Red) -->
<div class="bg-red-100 border border-red-500 text-red-700 p-4">
    Ralat: Sesuatu tidak kena.
</div>

<!-- Info (Cyan) -->
<div class="bg-cyan-100 border border-cyan-500 text-cyan-700 p-4">
    Maklumat: Nota penting.
</div>
```

---

## Component Patterns

### Card Component

```html
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
            Tajuk Kad
        </h3>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
            Keterangan kad yang ringkas dan jelas.
        </p>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
            Tindakan
        </button>
    </div>
</div>
```

### Button Variants

```html
<!-- Primary Button -->
<button class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium px-4 py-2 rounded transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
    Primary
</button>

<!-- Secondary Button -->
<button class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium px-4 py-2 rounded transition focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
    Secondary
</button>

<!-- Outline Button -->
<button class="border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-medium px-4 py-2 rounded transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
    Outline
</button>

<!-- Danger Button -->
<button class="bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-medium px-4 py-2 rounded transition focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
    Delete
</button>

<!-- Disabled Button -->
<button disabled class="bg-gray-300 text-gray-500 cursor-not-allowed px-4 py-2 rounded">
    Disabled
</button>
```

### Form Input

```html
<div class="mb-4">
    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Nama Aset
    </label>
    <input
        type="text"
        id="name"
        name="name"
        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        placeholder="Masukkan nama aset"
    >
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Nama penuh aset yang ingin didaftarkan.
    </p>
</div>

<!-- Error State -->
<div class="mb-4">
    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        E-mel
    </label>
    <input
        type="email"
        id="email"
        class="w-full px-4 py-2 border border-red-500 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
    >
    <p class="mt-1 text-sm text-red-600">
        Format e-mel tidak sah.
    </p>
</div>
```

### Badge Component

```html
<!-- Status Badges -->
<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
    Tersedia
</span>

<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
    Dipinjam
</span>

<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
    Dilupuskan
</span>
```

### Alert Component

```html
<!-- Success Alert -->
<div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4" role="alert">
    <div class="flex">
        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                Aset berjaya didaftarkan!
            </p>
        </div>
    </div>
</div>

<!-- Error Alert -->
<div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4" role="alert">
    <div class="flex">
        <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-red-800 dark:text-red-200">
                Ralat: Kod aset telah wujud.
            </p>
        </div>
    </div>
</div>
```

### Modal/Dialog

```html
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full p-6">
            <h3 id="modal-title" class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Padam Aset
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Adakah anda pasti mahu memadam aset ini? Tindakan ini tidak boleh dibatalkan.
            </p>
            <div class="flex justify-end gap-3">
                <button class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    Batal
                </button>
                <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                    Padam
                </button>
            </div>
        </div>
    </div>
</div>
```

---

## Accessibility (WCAG 2.2 AA)

### Focus States (REQUIRED)

**Always include visible focus indicators**:

```html
<!-- ✅ GOOD: Visible focus ring -->
<button class="bg-blue-600 text-white px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
    Button with Focus State
</button>

<!-- ❌ BAD: No focus indicator -->
<button class="bg-blue-600 text-white px-4 py-2 rounded focus:outline-none">
    Button WITHOUT Focus State
</button>
```

### Color Contrast

**Minimum Contrast Ratios** (WCAG 2.2 AA):

- **Normal text**: 4.5:1
- **Large text** (18px+ or 14px+ bold): 3:1

**Accessible Color Combinations**:

```html
<!-- ✅ GOOD: High contrast -->
<p class="text-gray-900 bg-white">Black on White (21:1)</p>
<p class="text-white bg-blue-600">White on Blue-600 (4.5:1)</p>

<!-- ❌ BAD: Low contrast -->
<p class="text-gray-400 bg-white">Gray-400 on White (2.8:1) — FAILS</p>
```

### Screen Reader Support

**Use Semantic HTML + ARIA**:

```html
<!-- ✅ GOOD: Descriptive button -->
<button aria-label="Padam aset bernama Laptop Dell">
    <svg class="w-5 h-5">...</svg>
</button>

<!-- ✅ GOOD: Hidden decorative icon -->
<svg aria-hidden="true" class="w-5 h-5">...</svg>

<!-- ✅ GOOD: Loading state -->
<button aria-busy="true">
    <span class="sr-only">Sedang memuatkan...</span>
    <svg class="animate-spin">...</svg>
</button>
```

**Screen Reader Only Text**:

```html
<span class="sr-only">Maklumat untuk pembaca skrin sahaja</span>
```

---

## Animations & Transitions

**Smooth Transitions**:

```html
<!-- Hover transitions -->
<button class="bg-blue-600 hover:bg-blue-700 transition duration-200">
    Hover Me
</button>

<!-- Multiple property transitions -->
<div class="opacity-0 hover:opacity-100 transform hover:scale-105 transition-all duration-300">
    Fade & Scale
</div>
```

**Loading Spinner**:

```html
<svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
</svg>
```

**Respect Reduced Motion Preference**:

```html
<div class="transition-transform duration-300 motion-reduce:transition-none">
    Animation disabled if user prefers reduced motion
</div>
```

---

## Custom Utilities (Extend Tailwind)

**Add Custom Colors** (`tailwind.config.js`):

```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        'motac': {
          '50': '#eff6ff',
          '100': '#dbeafe',
          '500': '#3b82f6',
          '600': '#2563eb',
          '700': '#1d4ed8',
        },
      },
    },
  },
}
```

**Usage**:

```html
<button class="bg-motac-600 hover:bg-motac-700 text-white">
    MOTAC Brand Button
</button>
```

---

## Performance Best Practices

1. **Purge Unused CSS** (Vite handles automatically):

```javascript
// tailwind.config.js
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Filament/**/*.php',
  ],
}
```

2. **Avoid `@apply` in Production** (use utility classes directly)

3. **Use JIT Mode** (enabled by default in Tailwind 3):

```javascript
// tailwind.config.js
module.exports = {
  mode: 'jit', // Just-In-Time compilation
}
```

---

## Common Pitfalls

### ❌ Avoid These Patterns

**1. Using Inline Styles Instead of Utilities**

```html
❌ <div style="padding: 16px; background: white;">Bad</div>
✅ <div class="p-4 bg-white">Good</div>
```

**2. Overusing `@apply` Directive**

```css
❌ /* styles.css */
.btn {
    @apply bg-blue-600 text-white px-4 py-2 rounded;
}
```

**3. Missing Dark Mode Variants**

```html
❌ <div class="bg-white text-gray-900">No dark mode</div>
✅ <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">With dark mode</div>
```

**4. Poor Color Contrast**

```html
❌ <p class="text-gray-400 bg-white">Low contrast (WCAG fail)</p>
✅ <p class="text-gray-900 bg-white">High contrast (WCAG pass)</p>
```

**5. No Focus States**

```html
❌ <button class="focus:outline-none">No visible focus</button>
✅ <button class="focus:outline-none focus:ring-2 focus:ring-blue-500">Visible focus</button>
```

---

## References & Resources

- **Tailwind CSS 3 Documentation**: <https://tailwindcss.com/docs>
- **Tailwind UI Components**: <https://tailwindui.com>
- **Heroicons** (Free SVG icons): <https://heroicons.com>
- **Color Contrast Checker**: <https://webaim.org/resources/contrastchecker/>
- **ICTServe Traceability**: D14 (UI/UX Design Guide), D15 (Accessibility Requirements), WCAG 2.2 AA

---

## Compliance Checklist

When using Tailwind CSS, ensure:

- [ ] Use utility classes instead of custom CSS
- [ ] Apply mobile-first responsive design
- [ ] Include dark mode variants with `dark:` prefix
- [ ] Use `gap` utilities for spacing (not margins)
- [ ] Ensure WCAG 2.2 AA color contrast (4.5:1 for text)
- [ ] Add visible focus states to all interactive elements
- [ ] Use semantic HTML with ARIA attributes
- [ ] Include `sr-only` class for screen reader text
- [ ] Respect `motion-reduce` preference for animations
- [ ] Test across mobile, tablet, and desktop breakpoints

---

**Status**: ✅ Active for ICTServe Tailwind CSS 3 development
**Version**: 1.0.0
**Last Updated**: 2025-01-06
