# WP-12: Component Patterns Library (ICTServe v3.5.0)

This document serves as the canonical reference for the ICTServe v3.5.0 Component Patterns Library. It documents the standardized frontend components, their alignment with the MyDS Design System v2025.2, and compliance with WCAG 2.2 Level AA accessibility standards.

**Status:** Active
**Version:** 3.5.0
**Last Updated:** 2025-12-08

---

## 1. Design System Alignment (MyDS v2025.2)

All components in this library are rigorously aligned with the MyDS Design System tokens.

### 1.1 Radius System

We use semantic radius tokens mapped to CSS variables to ensure consistency across the application.

| Token           | CSS Variable         | Value    | Usage                            |
| :-------------- | :------------------- | :------- | :------------------------------- |
| **Radius XS**   | `var(--radius-xs)`   | `4px`    | Checkboxes, Tags, Small badges   |
| **Radius S**    | `var(--radius-s)`    | `6px`    | Close buttons, Inner containers  |
| **Radius M**    | `var(--radius-m)`    | `8px`    | Buttons, Inputs, Cards (Compact) |
| **Radius L**    | `var(--radius-l)`    | `12px`   | Cards, Modals, Dropdowns         |
| **Radius XL**   | `var(--radius-xl)`   | `16px`   | Large Panels                     |
| **Radius Full** | `var(--radius-full)` | `9999px` | Badges, Avatars, Pills           |

**Usage Example:**

```blade
<div class="rounded-m ...">
    <!-- Content -->
</div>
```

### 1.2 Shadow System

Semantic shadow tokens provide depth and elevation consistent with MyDS.

| Token               | Class             | Usage                                                   |
| :------------------ | :---------------- | :------------------------------------------------------ |
| **Shadow SM**       | `shadow-sm`       | Inputs, Subtle borders                                  |
| **Shadow Button**   | `shadow-button`   | Primary/Secondary buttons                               |
| **Shadow Card**     | `shadow-card`     | Content cards, Stats cards                              |
| **Shadow Dropdown** | `shadow-dropdown` | Dropdown menus, Select options                          |
| **Shadow Modal**    | `shadow-dropdown` | Modals, Dialogs (using dropdown shadow for consistence) |

### 1.3 Focus & Accessibility (WCAG 2.2 AA)

- **Global Focus Ring:** A robust, high-contrast focus ring is applied globally via `resources/css/app.css` to all focusable elements. Explicit `focus:ring-*` classes have been removed from individual components to prevent conflicts and ensure consistency.
  - _Style:_ 3px outline with 2px offset. color depends on theme (Primary-500).
- **Touch Targets:** All interactive elements (buttons, inputs) enforce a minimum size of **44x44px** per WCAG 2.5.5.
  - _Implementation:_ `min-h-11 min-w-11` (11 \* 4px = 44px).

---

## 2. Component Reference

### 2.1 Buttons (`x-ui.button`, `x-primary-button`)

Buttons are the primary interactive elements.

- **File:** `resources/views/components/ui/button.blade.php` (and root variants)
- **Key Classes:** `rounded-m`, `min-h-11`, `shadow-button`
- **Variants:** Primary, Secondary, Danger, Ghost

```blade
<x-ui.button variant="primary" icon="plus">
    Create Ticket
</x-ui.button>
```

### 2.2 Theme Toggle (`livewire:components.theme-toggle`)

Bedrock-chat style toggle for Light/Dark selection with localStorage persistence.

- **File:** `resources/views/livewire/components/theme-toggle.blade.php`
- **Placement:** Header actions on `landing`, `front`, `guest`, `app`, `portal`; mobile menus reuse the same component.
- **Behaviour:** 44×44 touch target; uses Heroicon sun/moon, toggles `document.documentElement.classList` and dispatches `themeChanged` events for listeners; respects `<x-theme-init-script />` for FOUT prevention.

```blade
<livewire:components.theme-toggle />
```

### 2.3 Inputs (`x-form.input`, `x-text-input`)

Form inputs standardizing data entry.

- **File:** `resources/views/components/form/input.blade.php`
- **Key Classes:** `rounded-m`, `min-h-11`, `shadow-sm`
- **Features:** Integrated error handling, label support, helper text.

```blade
<x-form.input
    name="email"
    label="Email Address"
    type="email"
    placeholder="user@example.com"
    required
/>
```

### 2.4 Cards (`x-ui.card`, `x-ui.stats-card`)

Containers for grouping related content.

- **File:** `resources/views/components/ui/card.blade.php`, `resources/views/components/ui/stats-card.blade.php`
- **Key Classes:** `rounded-(--radius-l)`, `shadow-card`

```blade
<x-ui.card>
    <h3 class="text-lg font-medium">Card Title</h3>
    <p class="mt-2 text-gray-600">Content goes here.</p>
</x-ui.card>
```

### 2.5 Modals (`x-ui.modal`, `x-modal`)

Dialogs for critical interactions or information.

- **File:** `resources/views/components/ui/modal.blade.php`
- **Key Classes:** `rounded-(--radius-l)`, `shadow-dropdown`
- **Accessibility:** Focus trap, Escape key to close, ARIA attributes.

```blade
<x-ui.modal name="confirm-delete" :show="$showModal">
    <!-- Modal Content -->
</x-ui.modal>
```

### 2.6 Badges (`x-ui.badge`)

Small status indicators.

- **File:** `resources/views/components/ui/badge.blade.php`
- **Key Classes:** `rounded-full`
- **Variants:** Success, Warning, Danger, Info, Neutral

```blade
<x-ui.badge color="success">
    Active
</x-ui.badge>
```

---

## 3. Implementation Guidelines

1. **Prefer `ui/` Components:** Use the components in `resources/views/components/ui` and `resources/views/components/form` namespaces (`x-ui.*`, `x-form.*`) as they are the most feature-rich and standardized.
2. **Avoid Hardcoded Styles:** Do not use arbitrary values like `rounded-md` or `h-10`. Use semantic tokens and standard sizing classes.
3. **Accessibility First:** Always assume a user might be using a screen reader or keyboard only. Ensure high contrast and logical tab order.
4. **Tailwind Class Order:** Follow the logical ordering: Layout -> Sizing -> Spacing -> Typography -> Visual -> State variants.

## 4. Migration Notes (v3.5.0)

- **Radius Update:** All `rounded-lg`, `rounded-md` on buttons/cards have been migrated to `rounded-m` or `rounded-(--radius-*)` where appropriate.
- **Focus Cleanup:** Explicit `focus:ring` classes have been removed in favor of the global focus style.
- **Touch Targets:** `min-h-[44px]` has been updated to `min-h-11` to satisfy Tailwind lint rules while maintaining 44px compliance.
