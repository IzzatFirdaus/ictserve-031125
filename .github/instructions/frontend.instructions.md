---
applyTo: "resources/**,vite.config.js,tailwind.config.js,postcss.config.js,package.json"
description: "Frontend standards: Tailwind 3, Vite, Livewire Volt, Alpine.js, and build pipelines for ICTServe"
---

# Frontend Development Instructions

**Purpose**
Defines mandatory standards for frontend development in ICTServe. Ensures consistency across Tailwind CSS, Alpine.js, and Livewire Volt components while enforcing WCAG 2.2 AA accessibility.

**Scope**
Applies to `resources/css`, `resources/js`, `resources/views`, and build configuration files.

## 1. Technology Stack

- **Build System**: Vite 6.x
- **CSS Framework**: Tailwind CSS 3.4+ (Utility-first)
- **Interactivity**: Alpine.js 3.14+ (Declarative)
- **Components**: Livewire 3 / Volt (Functional API preferred)

## 2. Directory Structure

- **Volt Components**: `resources/views/livewire/{domain}/{component}.blade.php`
- **Blade Components**: `resources/views/components/` (Stateless UI elements)
- **Layouts**: `resources/views/layouts/`
- **Pages**: `resources/views/pages/` (Full-page Volt components)
- **Assets**:
  - CSS: `resources/css/app.css`
  - JS: `resources/js/app.js`

## 3. Coding Standards

### Tailwind CSS
- **Mobile First**: Write base styles for mobile, then add breakpoints.
  - *Bad*: `class="w-1/2 sm:w-full"`
  - *Good*: `class="w-full sm:w-1/2"`
- **Dark Mode**: Use the `dark:` variant for all color-related classes.
  - *Example*: `class="bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100"`
- **Arbitrary Values**: Avoid `w-[123px]`. Use theme values or extend `tailwind.config.js`.

### Alpine.js
- **State**: Use `x-data` for local state.
- **Directives**: Use shorthand (`@click`, `:class`) instead of `x-on:click`.
- **Modularity**: Extract complex logic into `resources/js/alpine-patterns.js` if reused.

### Livewire Volt
- **Separation**: Keep PHP logic in the `@php` block at the top.
- **Naming**: Use kebab-case for filenames (`create-ticket.blade.php`).
- **Validation**: Define rules server-side in the Volt state.

## 4. Build & Development Workflow

### Commands
- **Dev Server**: `npm run dev` (Hot Module Replacement)
- **Production Build**: `npm run build` (Minification & Versioning)
- **Linting**: `npm run lint` (ESLint/Prettier)

### Configuration (`vite.config.js`)
Ensure the Laravel plugin is correctly configured:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
````

## 5\. Accessibility (Mandatory)

Refer to `accessibility.instructions.md` for full details.

  - **Forms**: Every input must have a visible `<label>`.
  - **Focus**: Never remove outline without replacing it (`focus:ring`).
  - **Semantic HTML**: Use `<button>`, `<nav>`, `<main>` correctly.

## 6\. Performance Best Practices

  - **Lazy Loading**: Use `loading="lazy"` on images.
  - **Livewire**: Use `wire:model.blur` or `.live.debounce.300ms` to reduce network requests.
  - **Fonts**: Preload critical fonts; use `font-display: swap`.
  - **Bundle Size**: Import only used JavaScript modules.

## 7\. Troubleshooting

  - **Missing Styles**: Ensure the file path is included in `tailwind.config.js` `content` array.
  - **Vite Connection Error**: Ensure the dev server port (5173) is exposed if using Docker.
  - **Alpine/Livewire Conflict**: Use `wire:ignore` on elements modified by 3rd-party JS libraries.
