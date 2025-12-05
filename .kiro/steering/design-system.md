---
inclusion: always
description: "Figma MCP integration guidelines and MyDS Design System token mapping for ICTServe"
version: "2.0.0"
last_updated: "2025-12-05"
---

# Figma MCP Integration Guidelines

## Purpose

This steering file provides guidelines for integrating Figma designs with the ICTServe codebase using the Figma MCP power, aligned with MyDS Design System v2025.2.

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

---

## MyDS Design System Token Mapping (D13 §2.7)

Reference: `resources/css/app.css` @theme directive and `app/Services/FigmaDesignService.php`

### Color Tokens (WCAG 2.2 AA Compliant)

| MyDS Token | CSS Variable | Hex Value | Contrast | Usage |
|------------|--------------|-----------|----------|-------|
| Primary | `--color-primary-500` | #0056B3 | 7.2:1 | Main actions, links, primary buttons |
| Secondary | `--color-secondary-500` | #0B4D8F | 8.1:1 | Secondary actions, supporting elements |
| Success | `--color-success-500` | #1B7C54 | 4.6:1 | Success states, confirmations |
| Warning | `--color-warning-500` | #CC7700 | 4.5:1 | Warning states, cautions |
| Danger | `--color-danger-500` | #B3002D | 7.8:1 | Error states, destructive actions |

### Semantic Color Tokens

#### Background Tokens (--bg-*)

| Token | CSS Variable | Tailwind Class | Usage |
|-------|--------------|----------------|-------|
| White | `--bg-white` | `bg-white` | Cards, panels, default surfaces |
| Washed | `--bg-washed` | `bg-gray-50` | Page backgrounds, muted surfaces |
| Primary Light | `--bg-primary-50` | `bg-primary-50` | Primary status badges, highlights |
| Success Light | `--bg-success-50` | `bg-success-50` | Success notifications |
| Warning Light | `--bg-warning-50` | `bg-warning-50` | Warning notifications |
| Danger Light | `--bg-danger-50` | `bg-danger-50` | Error notifications |

#### Text Tokens (--txt-*)

| Token | CSS Variable | Tailwind Class | Usage |
|-------|--------------|----------------|-------|
| Black 900 | `--txt-black-900` | `text-gray-900` | Headings, primary text |
| Black 700 | `--txt-black-700` | `text-gray-700` | Body text, descriptions |
| Black 500 | `--txt-black-500` | `text-gray-500` | Placeholders, muted text |
| White | `--txt-white` | `text-white` | Text on dark backgrounds |
| Primary 600 | `--txt-primary-600` | `text-primary-600` | Links, interactive text |
| Success 600 | `--txt-success-600` | `text-success` | Success messages |
| Warning 600 | `--txt-warning-600` | `text-warning` | Warning messages |
| Danger 600 | `--txt-danger-600` | `text-danger` | Error messages |

#### Outline Tokens (--otl-*)

| Token | CSS Variable | Tailwind Class | Usage |
|-------|--------------|----------------|-------|
| Divider | `--otl-divider` | `border-gray-200` | Separators, dividers |
| Default | `--otl-default` | `border-gray-300` | Default borders |
| Primary | `--otl-primary` | `border-primary-500` | Active/focused borders |

#### Focus Ring Tokens (--fr-*)

| Token | CSS Variable | Tailwind Class | Usage |
|-------|--------------|----------------|-------|
| Primary | `--fr-primary` | `ring-primary-500` | Default focus state |
| Danger | `--fr-danger` | `ring-danger-500` | Error focus state |

### Typography System (D13 §2.4)

#### Font Families

| Purpose | Font | CSS Variable | Tailwind Class |
|---------|------|--------------|----------------|
| Headings | Poppins | `--font-heading` | `font-heading` |
| Body | Inter | `--font-body` | `font-sans` |
| Monospace | JetBrains Mono | `--font-mono` | `font-mono` |

#### Heading Sizes

| Level | Size | Line Height | Tailwind Class |
|-------|------|-------------|----------------|
| H1 | 36px (2.25rem) | 44px | `text-4xl` |
| H2 | 30px (1.875rem) | 38px | `text-3xl` |
| H3 | 24px (1.5rem) | 32px | `text-2xl` |
| H4 | 20px (1.25rem) | 28px | `text-xl` |
| H5 | 16px (1rem) | 24px | `text-base` |
| H6 | 14px (0.875rem) | 20px | `text-sm` |

#### Body Text Sizes

| Name | Size | Line Height | Tailwind Class |
|------|------|-------------|----------------|
| Large | 18px | 26px | `text-lg` |
| Medium | 16px | 24px | `text-base` |
| Small | 14px | 20px | `text-sm` |
| Extra Small | 12px | 18px | `text-xs` |

### Spacing System (D13 §2.6)

| Token | Value | CSS Variable | Tailwind Class | Usage |
|-------|-------|--------------|----------------|-------|
| space-1 | 4px | `--space-1` | `gap-1`, `p-1` | Micro spacing |
| space-2 | 8px | `--space-2` | `gap-2`, `p-2` | Button groups, field labels |
| space-3 | 12px | `--space-3` | `gap-3`, `p-3` | General component spacing |
| space-4 | 16px | `--space-4` | `gap-4`, `p-4` | General component spacing |
| space-5 | 20px | `--space-5` | `gap-5`, `p-5` | General component spacing |
| space-6 | 24px | `--space-6` | `gap-6`, `p-6` | Sub-sections, cards |
| space-8 | 32px | `--space-8` | `gap-8`, `p-8` | Main sections |
| space-10 | 40px | `--space-10` | `gap-10`, `p-10` | Large blocks |
| space-12 | 48px | `--space-12` | `gap-12`, `p-12` | Extra large blocks |
| space-16 | 64px | `--space-16` | `gap-16`, `p-16` | Page-level separation |

### Radius System (D13 §2.5)

| Name | Size | CSS Variable | Tailwind Class | Usage |
|------|------|--------------|----------------|-------|
| Extra Small | 4px | `--radius-xs` | `rounded-xs` | Context menu items |
| Small | 6px | `--radius-s` | `rounded-s` | Small buttons |
| Medium | 8px | `--radius-m` | `rounded-md` | Buttons, CTAs, context menus |
| Large | 12px | `--radius-l` | `rounded-lg` | Content cards |
| Extra Large | 14px | `--radius-xl` | `rounded-xl` | Context menus with search |
| Full | 9999px | `--radius-full` | `rounded-full` | Avatars, chips, badges |

### Shadow System (D12 §6.9, D14 §7.5)

| Name | CSS Variable | Value | Usage |
|------|--------------|-------|-------|
| Button | `--shadow-button` | `0px 1px 3px 0px rgba(0,0,0,0.07)` | Buttons, small interactive elements |
| Card | `--shadow-card` | `0px 2px 6px rgba(0,0,0,0.05), 0px 6px 24px rgba(0,0,0,0.05)` | Cards, panels |
| Dropdown | `--shadow-dropdown` | `0px 2px 6px rgba(0,0,0,0.05), 0px 12px 50px rgba(0,0,0,0.10)` | Dropdowns, modals |

### Motion System (D12 §6.10)

#### Durations

| Name | CSS Variable | Value | Usage |
|------|--------------|-------|-------|
| Short | `--duration-short` | 200ms | Hover states, focus transitions |
| Medium | `--duration-medium` | 400ms | Toast animations, modal transitions |
| Long | `--duration-long` | 600ms | Page transitions, complex animations |

#### Easing Functions

| Name | CSS Variable | Value | Usage |
|------|--------------|-------|-------|
| Ease Out | `--motion-easeout` | `cubic-bezier(0, 0, 0.58, 1)` | Standard transitions |
| Ease Out Back | `--motion-easeoutback` | `cubic-bezier(0.4, 1.4, 0.2, 1)` | Playful interactions |
| Linear | `--motion-linear` | `cubic-bezier(0, 0, 1, 1)` | Progress indicators |

---

## ICTServe Design System Tokens (Legacy Reference)

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
