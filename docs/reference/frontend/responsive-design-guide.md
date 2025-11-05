# Responsive Design Guide

## Mobile-First Responsive Design for ICTServe

This guide outlines the responsive design standards and implementation guidelines for the ICTServe application to ensure optimal user experience across all devices.

## Table of Contents

1. [Breakpoint System](#breakpoint-system)
2. [Touch Target Guidelines](#touch-target-guidelines)
3. [Responsive Components](#responsive-components)
4. [Image Optimization](#image-optimization)
5. [Typography Scaling](#typography-scaling)
6. [Layout Patterns](#layout-patterns)
7. [Testing Guidelines](#testing-guidelines)

---

## Breakpoint System

### Standard Breakpoints

The ICTServe application uses a mobile-first approach with the following breakpoints:

| Breakpoint | Min Width | Target Devices | Usage |
|------------|-----------|----------------|-------|
| `xs` | 475px | Small phones | Extra small adjustments |
| `sm` | 640px | Large phones | Mobile landscape |
| `md` | 768px | Tablets | Tablet portrait |
| `lg` | 1024px | Small laptops | Tablet landscape, small desktop |
| `xl` | 1280px | Desktops | Standard desktop |
| `2xl` | 1536px | Large desktops | Large screens |
| `3xl` | 1600px | Extra large | Ultra-wide displays |

### Tailwind Configuration

```javascript
// tailwind.config.js
screens: {
    'xs': '475px',
    'sm': '640px',
    'md': '768px',
    'lg': '1024px',
    'xl': '1280px',
    '2xl': '1536px',
    '3xl': '1600px',
}
```

### Usage Examples

```blade
<!-- Mobile-first approach -->
<div class="w-full md:w-1/2 lg:w-1/3">
    <!-- Full width on mobile, half on tablet, third on desktop -->
</div>

<!-- Responsive padding -->
<div class="px-4 sm:px-6 lg:px-8">
    <!-- Increases padding on larger screens -->
</div>

<!-- Responsive grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- 1 column mobile, 2 tablet, 3 desktop -->
</div>
```

---

## Touch Target Guidelines

### WCAG 2.2 Requirements

All interactive elements must meet the following minimum sizes:

- **Minimum touch target**: 44x44 pixels
- **Recommended touch target**: 48x48 pixels
- **Minimum spacing**: 8 pixels between targets

### Implementation

```blade
<!-- Button with minimum touch target -->
<button class="min-h-[44px] min-w-[44px] px-4 py-2">
    Click me
</button>

<!-- Icon button with adequate size -->
<button class="p-2 min-h-[44px] min-w-[44px] flex items-center justify-center">
    <svg class="h-6 w-6"><!-- Icon --></svg>
</button>

<!-- Link with adequate padding -->
<a href="#" class="inline-flex items-center min-h-[44px] px-4 py-2">
    Link text
</a>

<!-- Form input with minimum height -->
<input type="text" class="w-full min-h-[44px] px-3 py-2">
```

### Spacing Between Targets

```blade
<!-- Adequate spacing with gap utility -->
<div class="flex gap-2">
    <button class="min-h-[44px] min-w-[44px]">Button 1</button>
    <button class="min-h-[44px] min-w-[44px]">Button 2</button>
</div>

<!-- Adequate spacing with margin -->
<button class="min-h-[44px] mr-2">Button 1</button>
<button class="min-h-[44px]">Button 2</button>
```

---

## Responsive Components

### Using Responsive Components

The application provides pre-built responsive components:

#### Responsive Container

```blade
<x-responsive.container maxWidth="7xl">
    <!-- Content automatically centered with responsive padding -->
</x-responsive.container>

<!-- Without padding -->
<x-responsive.container maxWidth="7xl" :padding="false">
    <!-- Content without padding -->
</x-responsive.container>
```

#### Responsive Grid

```blade
<x-responsive.grid
    cols="1"
    smCols="2"
    mdCols="3"
    lgCols="4"
    gap="4"
>
    <div>Item 1</div>
    <div>Item 2</div>
    <div>Item 3</div>
    <div>Item 4</div>
</x-responsive.grid>
```

#### Responsive Image

```blade
<x-responsive.image
    src="/images/hero.jpg"
    alt="Hero image"
    srcset="/images/hero-320.jpg 320w,
            /images/hero-640.jpg 640w,
            /images/hero-1024.jpg 1024w,
            /images/hero-1920.jpg 1920w"
    sizes="(max-width: 640px) 100vw,
           (max-width: 1024px) 50vw,
           33vw"
    loading="lazy"
/>
```

---

## Image Optimization

### Responsive Images

Use `srcset` and `sizes` attributes for responsive images:

```blade
<img
    src="/images/default.jpg"
    srcset="/images/small.jpg 320w,
            /images/medium.jpg 640w,
            /images/large.jpg 1024w,
            /images/xlarge.jpg 1920w"
    sizes="(max-width: 640px) 100vw,
           (max-width: 1024px) 50vw,
           33vw"
    alt="Descriptive alt text"
    loading="lazy"
    decoding="async"
>
```

### Image Formats

Use modern image formats with fallbacks:

```blade
<picture>
    <source srcset="/images/hero.avif" type="image/avif">
    <source srcset="/images/hero.webp" type="image/webp">
    <img src="/images/hero.jpg" alt="Hero image">
</picture>
```

### Lazy Loading

Enable lazy loading for images below the fold:

```blade
<img src="/images/photo.jpg" alt="Photo" loading="lazy">
```

### Image Sizing

Always specify width and height to prevent layout shift:

```blade
<img
    src="/images/photo.jpg"
    alt="Photo"
    width="800"
    height="600"
    loading="lazy"
>
```

---

## Typography Scaling

### Responsive Font Sizes

Use responsive typography that scales with viewport:

```blade
<!-- Heading that scales -->
<h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold">
    Responsive Heading
</h1>

<!-- Body text that scales -->
<p class="text-sm sm:text-base md:text-lg">
    Responsive paragraph text
</p>
```

### Line Height Adjustments

```blade
<p class="text-base leading-relaxed md:leading-loose">
    Text with responsive line height
</p>
```

### Text Wrapping

```blade
<!-- Prevent orphans on larger screens -->
<h2 class="text-balance">
    Heading with balanced text wrapping
</h2>

<!-- Better paragraph wrapping -->
<p class="text-pretty">
    Paragraph with optimized wrapping
</p>
```

---

## Layout Patterns

### Responsive Navigation

```blade
<!-- Desktop navigation -->
<nav class="hidden md:flex space-x-8">
    <a href="/">Home</a>
    <a href="/about">About</a>
    <a href="/contact">Contact</a>
</nav>

<!-- Mobile navigation -->
<div class="md:hidden">
    <button class="min-h-[44px] min-w-[44px]" aria-label="Open menu">
        <svg><!-- Hamburger icon --></svg>
    </button>
</div>
```

### Responsive Cards

```blade
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-2">Card Title</h3>
        <p class="text-gray-600">Card content</p>
    </div>
    <!-- More cards -->
</div>
```

### Responsive Tables

```blade
<!-- Horizontal scroll on mobile -->
<div class="overflow-x-auto">
    <table class="min-w-full">
        <thead>
            <tr>
                <th>Column 1</th>
                <th>Column 2</th>
                <th>Column 3</th>
            </tr>
        </thead>
        <tbody>
            <!-- Table rows -->
        </tbody>
    </table>
</div>

<!-- Or use card layout on mobile -->
<div class="block md:hidden">
    <!-- Card layout for mobile -->
</div>
<div class="hidden md:block">
    <!-- Table layout for desktop -->
</div>
```

### Responsive Forms

```blade
<form class="space-y-4">
    <!-- Full width on mobile, half on desktop -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="first-name">First Name</label>
            <input
                type="text"
                id="first-name"
                class="w-full min-h-[44px] px-3 py-2"
            >
        </div>
        <div>
            <label for="last-name">Last Name</label>
            <input
                type="text"
                id="last-name"
                class="w-full min-h-[44px] px-3 py-2"
            >
        </div>
    </div>

    <!-- Full width email field -->
    <div>
        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            class="w-full min-h-[44px] px-3 py-2"
        >
    </div>

    <!-- Responsive button group -->
    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
        <button type="submit" class="min-h-[44px] px-6 py-2">
            Submit
        </button>
        <button type="button" class="min-h-[44px] px-6 py-2">
            Cancel
        </button>
    </div>
</form>
```

### Responsive Modals

```blade
<!-- Modal that adapts to screen size -->
<div class="fixed inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-md sm:max-w-lg md:max-w-2xl">
        <div class="p-4 sm:p-6">
            <!-- Modal content -->
        </div>
    </div>
</div>
```

---

## Testing Guidelines

### Viewport Testing

Test at the following viewport sizes:

1. **320px** - Small mobile (iPhone SE)
2. **375px** - Standard mobile (iPhone 12)
3. **768px** - Tablet portrait (iPad)
4. **1024px** - Tablet landscape / Small laptop
5. **1280px** - Standard desktop
6. **1920px** - Large desktop

### Browser DevTools

Use browser DevTools for responsive testing:

```
Chrome DevTools:
1. Press F12 or Cmd+Option+I (Mac)
2. Click device toolbar icon (Cmd+Shift+M)
3. Select device or enter custom dimensions
4. Test at different zoom levels (100%, 200%)
```

### Physical Device Testing

Test on actual devices when possible:

- **Mobile**: iPhone, Android phones
- **Tablet**: iPad, Android tablets
- **Desktop**: Various screen sizes

### Orientation Testing

Test both orientations:

- Portrait mode
- Landscape mode

### Zoom Testing

Test at different zoom levels:

- 100% (default)
- 150%
- 200% (WCAG requirement)

### Touch Testing

Verify touch interactions:

- All buttons are easily tappable
- No accidental activations
- Adequate spacing between targets
- Swipe gestures work (if applicable)

---

## Common Responsive Patterns

### Hide/Show Based on Screen Size

```blade
<!-- Show only on mobile -->
<div class="block md:hidden">
    Mobile content
</div>

<!-- Show only on desktop -->
<div class="hidden md:block">
    Desktop content
</div>

<!-- Show on tablet and up -->
<div class="hidden sm:block">
    Tablet and desktop content
</div>
```

### Responsive Spacing

```blade
<!-- Responsive padding -->
<div class="p-4 sm:p-6 lg:p-8">
    Content with responsive padding
</div>

<!-- Responsive margin -->
<div class="mb-4 sm:mb-6 lg:mb-8">
    Content with responsive margin
</div>

<!-- Responsive gap -->
<div class="flex gap-2 sm:gap-4 lg:gap-6">
    Items with responsive spacing
</div>
```

### Responsive Flexbox

```blade
<!-- Stack on mobile, row on desktop -->
<div class="flex flex-col md:flex-row gap-4">
    <div class="flex-1">Column 1</div>
    <div class="flex-1">Column 2</div>
</div>

<!-- Responsive alignment -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
    <div>Left content</div>
    <div>Right content</div>
</div>
```

### Responsive Text Alignment

```blade
<!-- Center on mobile, left on desktop -->
<div class="text-center md:text-left">
    Responsive text alignment
</div>
```

---

## Performance Considerations

### Minimize Layout Shifts

- Always specify image dimensions
- Use aspect ratio utilities
- Reserve space for dynamic content

```blade
<!-- Prevent layout shift with aspect ratio -->
<div class="aspect-video bg-gray-200">
    <img src="/video-thumbnail.jpg" alt="Video" class="w-full h-full object-cover">
</div>
```

### Optimize Images

- Use appropriate image sizes for each breakpoint
- Implement lazy loading
- Use modern image formats (WebP, AVIF)
- Compress images appropriately

### Reduce JavaScript

- Use CSS for responsive behavior when possible
- Minimize JavaScript for layout calculations
- Use Intersection Observer for lazy loading

---

## Checklist

### Before Deployment

- [ ] Test at all standard breakpoints
- [ ] Verify touch targets are ≥ 44x44px
- [ ] Test with keyboard navigation
- [ ] Test at 200% zoom
- [ ] Verify no horizontal scrolling
- [ ] Test on physical devices
- [ ] Test in both orientations
- [ ] Verify images are optimized
- [ ] Check performance metrics
- [ ] Validate with Lighthouse

---

## Resources

- [Tailwind CSS Responsive Design](https://tailwindcss.com/docs/responsive-design)
- [MDN Responsive Design](https://developer.mozilla.org/en-US/docs/Learn/CSS/CSS_layout/Responsive_Design)
- [Google Web Fundamentals](https://developers.google.com/web/fundamentals/design-and-ux/responsive)
- [WCAG 2.2 Reflow](https://www.w3.org/WAI/WCAG22/Understanding/reflow.html)
