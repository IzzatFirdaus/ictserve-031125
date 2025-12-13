---
inclusion: always
description: "Figma MCP integration guidelines, MyDS Design System v2025.2 token mapping, component decision matrix, and ICTServe v3.6.0 usage patterns"
version: "2.2.0"
last_updated: "2025-12-11"
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

We use semantic radius tokens mapped to CSS variables to ensure consistency across the application.

| Token | CSS Variable | Value | Tailwind Class | Usage |
|-------|--------------|-------|----------------|-------|
| **Radius XS** | `var(--radius-xs)` | 4px | `rounded-xs` | Checkboxes, Tags, Small badges |
| **Radius S** | `var(--radius-s)` | 6px | `rounded-s` | Close buttons, Inner containers |
| **Radius M** | `var(--radius-m)` | 8px | `rounded-m` | Buttons, Inputs, Cards (Compact) |
| **Radius L** | `var(--radius-l)` | 12px | `rounded-l` | Cards, Modals, Dropdowns |
| **Radius XL** | `var(--radius-xl)` | 16px | `rounded-xl` | Large Panels |
| **Radius Full** | `var(--radius-full)` | 9999px | `rounded-full` | Badges, Avatars, Pills |

**Usage Example:**

```blade
<div class="rounded-m ...">
    <!-- Content -->
</div>
```

### Shadow System (D12 §6.9, D14 §7.5)

Semantic shadow tokens provide depth and elevation consistent with MyDS.

| Token | Class | Usage |
|-------|-------|-------|
| **Shadow SM** | `shadow-sm` | Inputs, Subtle borders |
| **Shadow Button** | `shadow-button` | Primary/Secondary buttons |
| **Shadow Card** | `shadow-card` | Content cards, Stats cards |
| **Shadow Dropdown** | `shadow-dropdown` | Dropdown menus, Select options |
| **Shadow Modal** | `shadow-dropdown` | Modals, Dialogs (using dropdown shadow for consistency) |

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

## WCAG 2.2 AA Compliance & Focus Management

All Figma-derived components must meet:

- **Color Contrast**: 4.5:1 for text, 3:1 for UI elements
- **Focus Indicators**: Visible focus rings on interactive elements
- **Touch Targets**: Minimum 44x44px for touch interactions
- **Motion**: Respect `prefers-reduced-motion`

### Global Focus Ring Implementation

- **Global Focus Ring:** A robust, high-contrast focus ring is applied globally via `resources/css/app.css` to all focusable elements. Explicit `focus:ring-*` classes have been removed from individual components to prevent conflicts and ensure consistency.
  - *Style:* 3px outline with 2px offset. Color depends on theme (Primary-500).
- **Touch Targets:** All interactive elements (buttons, inputs) enforce a minimum size of **44x44px** per WCAG 2.5.5.
  - *Implementation:* `min-h-11 min-w-11` (11 × 4px = 44px).

## ICTServe Component Usage Guidelines

### Component Decision Matrix

| Scenario | Technology | Reason |
|----------|-----------|--------|
| **Simple search/filter UI** | Volt Functional | Minimal state, no complex lifecycle |
| **Multi-step wizard** | Livewire Class-Based | Complex validation, step management |
| **Admin CRUD interface** | Filament Resource | Built-in table, form, authorization |
| **Dropdown menu (UI only)** | Alpine.js | No server state, pure client interaction |
| **Real-time notifications** | Livewire + Echo | Server-driven with WebSocket |
| **Modal dialog (form)** | Livewire Component | Server validation required |
| **Tooltip (info only)** | Alpine.js | Static content, no server interaction |
| **Data table with filters** | Livewire Class-Based | Server-side pagination, eager loading |

### Volt vs Livewire Class-Based Guidelines

#### ✅ Use Volt Functional API For

- Read-only data display components
- Simple forms (≤5 fields, basic validation)
- Search and filter interfaces
- Status badges and indicators
- Navigation components
- Language switcher
- Notification bell (counter only)

#### ❌ Use Livewire Class-Based For

- Multi-step forms/wizards
- Complex authorization logic (multiple policies)
- File uploads with chunking
- Components with `mount()`, `hydrate()`, `dehydrate()` hooks
- Heavy trait usage (beyond base traits)
- Components requiring extensive testing mocks

**Rule of Thumb**: If `mount()` method has >10 lines, use class-based Livewire.

### Available Component Categories

ICTServe provides organized Blade components in `resources/views/components/`:

- **UI Components** (`ui/`): buttons, cards, modals, alerts, badges, stats-card, user-info-card
- **Form Components** (`form/`): inputs, selects, textareas, file uploads
- **Layout Components** (`layout/`, `layouts/`): guest, portal, admin layouts
- **Accessibility Components** (`accessibility/`): skip links, language switcher, ARIA helpers
- **Navigation Components** (`navigation/`): menus, breadcrumbs, tabs
- **Data Components** (`data/`): tables, pagination, statistics

### Component Implementation Guidelines

1. **Prefer `ui/` Components:** Use the components in `resources/views/components/ui` and `resources/views/components/form` namespaces (`x-ui.*`, `x-form.*`) as they are the most feature-rich and standardized.
2. **Avoid Hardcoded Styles:** Do not use arbitrary values like `rounded-md` or `h-10`. Use semantic tokens and standard sizing classes.
3. **Accessibility First:** Always assume a user might be using a screen reader or keyboard only. Ensure high contrast and logical tab order.
4. **Tailwind Class Order:** Follow the logical ordering: Layout → Sizing → Spacing → Typography → Visual → State variants.

### Component Naming Convention

- Use kebab-case: `x-ui.stats-card`, `x-form.file-upload`
- Prefix with category: `x-ui.`, `x-form.`, `x-accessibility.`

### Livewire Integration Patterns

```blade
{{-- Use wire:model for two-way binding --}}
<x-form.input wire:model="title" />

{{-- Use wire:model.live for real-time updates --}}
<x-form.input wire:model.live="search" />

{{-- Use wire:model.live.debounce for search --}}
<x-form.input wire:model.live.debounce.300ms="search" />
```

### Accessibility Requirements (WCAG 2.2 AA)

- All interactive elements have 44×44px minimum touch target
- Color contrast meets 4.5:1 for text, 3:1 for UI elements
- Focus indicators are visible (3px outline, 2px offset)
- Form inputs have associated labels via `for` attribute
- Error messages use `aria-describedby` and `aria-invalid`
- Modals trap focus and support Escape key

### Performance Guidelines

1. Use `wire:model.lazy` for large text fields
2. Use `wire:model.live.debounce.300ms` for search inputs
3. Implement lazy loading for heavy components
4. Use Redis caching for computed properties

### Migration Notes (v3.5.0 → v3.6.0)

- **Radius Update:** All `rounded-lg`, `rounded-md` on buttons/cards have been migrated to `rounded-m` or `rounded-(--radius-*)` where appropriate.
- **Focus Cleanup:** Explicit `focus:ring` classes have been removed in favor of the global focus style.
- **Touch Targets:** `min-h-[44px]` has been updated to `min-h-11` to satisfy Tailwind lint rules while maintaining 44px compliance.
- **Component Consolidation:** All component patterns from the separate library have been integrated into this comprehensive guide.

## Related Documentation

- D12: UI/UX Design Guide
- D13: UI/UX Frontend Framework
- D14: UI/UX Style Guide
- D15: Language (MS/EN) Localization
