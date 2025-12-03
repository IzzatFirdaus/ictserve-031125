---
inclusion: always
description: "P Integration and Design System Guidelines for ICTServe"
version: "1.0.0"
last_updated: "2025-12-04"
---

# Figma MCP Integration Guidelines

## Purpose

This steering file provides guidelines for integrating Figma designs with the ICTServe codebase using the Figma MCP power.

## Design-to-Code Principles

### 1. Treat Figma Output as Design Reference

- Treat the Figma MCP output (React + Tailwind) as a representation of design and behavior, not as final code style
- Replace Tailwind utility classes with the project's preferred utilities/design-system tokens when applicable
- Reuse existing components (e.g., buttons, inputs, typography, icon wrappers) instead of duplicating functionality

### 2. Use Project Design System

- Use the project's color system, typography scale, and spacing tokens consistently
- Respect existing routing, state management, and data-fetch patterns already adopted in the repo
- Follow ICTServe's established Livewire/Volt component patterns

### 3. Visual Parity

- Strive for 1:1 visual parity with the Figma design
- When conflicts arise, prefer design-system tokens and adjust spacing or sizes minimally to match visuals
- Validate the final UI against the Figma screenshot for both look and behavior

## ICTServe Design System Tokens

### Colors (from Tailwind v4 @theme)

Reference `resources/css/app.css` for the complete color palette:

- **Primary**: Brand colors for main actions and highlights
- **Secondary**: Supporting colors for secondary elements
- **Neutral**: Grays for text, borders, backgrounds
- **Semantic**: Success (green), Warning (amber), Error (red), Info (blue)

### Typography Scale

- **Headings**: Use `text-xl`, `text-2xl`, `text-3xl` for hierarchy
- **Body**: `text-base` (16px) for main content
- **Small**: `text-sm` for secondary text, labels
- **Micro**: `text-xs` for captions, metadata

### Spacing System

- Use Tailwind's spacing scale: `p-2`, `p-4`, `p-6`, `p-8`
- Prefer `gap-*` utilities over margins for flex/grid layouts
- Standard component padding: `p-4` or `p-6`

### Component Patterns

#### Buttons

```blade
{{-- Primary Button --}}
<button class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
    Action
</button>

{{-- Secondary Button --}}
<button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
    Cancel
</button>
```

#### Form Inputs

```blade
<input type="text" 
       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
       placeholder="Enter value">
```

#### Cards

```blade
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    {{-- Card content --}}
</div>
```

## Figma-to-Livewire Workflow

### Step 1: Get Design from Figma

1. Use Figma MCP to fetch component design
2. Review the generated React/Tailwind code

### Step 2: Convert to Livewire/Blade

1. Transform React JSX to Blade syntax
2. Replace React state with Livewire properties
3. Convert event handlers to `wire:click`, `wire:submit`, etc.

### Step 3: Apply ICTServe Patterns

1. Use existing Blade components from `resources/views/components/`
2. Follow Volt functional or class-based patterns
3. Ensure WCAG 2.2 AA compliance

### Step 4: Validate

1. Compare visual output with Figma design
2. Test responsive behavior
3. Verify accessibility (contrast, focus states, ARIA)

## Code Connect Mapping

When connecting code to Figma components:

1. **Component Location**: Map Figma components to their Blade/Livewire equivalents
2. **Naming Convention**: Use consistent naming between Figma and code
3. **Documentation**: Document the mapping in component comments

Example mapping comment:

```blade
{{-- 
    Figma Component: Button/Primary
    Figma URL: https://figma.com/file/xxx/component-id
    Last synced: 2025-12-04
--}}
```

## WCAG 2.2 AA Compliance

All Figma-derived components must meet:

- **Color Contrast**: 4.5:1 for text, 3:1 for UI elements
- **Focus Indicators**: Visible focus rings on interactive elements
- **Touch Targets**: Minimum 44x44px for touch interactions
- **Motion**: Respect `prefers-reduced-motion`

## Related Documentation

- D12: UI/UX Design Guide
- D13: UI/UX Frontend Framework
- D14: UI/UX Style Guide
- D15: Language (MS/EN) Localization
