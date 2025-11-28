# ICTServe Component Library Documentation

**Version**: 3.0.0  
**Last Updated**: November 2025  
**Technology Stack**: Laravel 12.x | Livewire 3.x | Tailwind CSS 4.1 | Alpine.js 3.x

---

## Table of Contents

1. [Introduction](#introduction)
2. [Component Categories](#component-categories)
3. [UI Components](#ui-components)
4. [Form Components](#form-components)
5. [Accessibility Components](#accessibility-components)
6. [Layout Compout-components)
7. [Data Components](#data-components)
8. [Alpine.js Patterns](#alpinejs-patterns)
9. [Usage Guidelines](#usage-guidelines)

---

## Introduction

The ICTServe Component Library provides a unified set of reusable Blade components designed for WCAG 2.2 AA compliance, consistent styling, and optimal performance.

### Design Principles

- **WCAG 2.2 AA Compliance**: 4.5:1 text contrast, 44×44px touch targets
- **Mobile-First**: Responsive design from 320px to 1920px
- **Bilingual Support**: All components support Bahasa Melayu and English
- **Performance**: Optimized for Core Web Vitals targets

### Color Palette

| Color | Hex | Usage |
|-------|-----|-------|
| Primary | `#0056b3` | Primary actions, links |
| Success | `#198754` | Success states, confirmations |
| Warning | `#ff8c00` | Warnings, cautions |
| Danger | `#b50c0c` | Errors, destructive actions |

---

## Component Categories

```
resources/views/components/
├── accessibility/     # Skip links, language switcher, ARIA
├── alpine/           # Alpine.js patterns
├── data/             # Tables, pagination, stats
├── form/             # Input, select, textarea, checkbox
├── layout/           # Guest, portal, admin layouts
├── navigation/       # Menu, breadcrumb, tabs
├── responsive/       # Mobile cards, tablet grids
└── ui/               # Button, card, modal, alert, badge
```

---

## UI Components

### x-ui.button

Interactive button component with multiple variants and states.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | `'button'` | Button type (button, submit, reset) |
| `variant` | string | `'primary'` | Visual variant |
| `size` | string | `'md'` | Size (sm, md, lg) |
| `disabled` | bool | `false` | Disabled state |
| `loading` | bool | `false` | Loading state |

#### Variants

- `primary` - Blue background, white text
- `secondary` - Gray background, dark text
- `success` - Green background, white text
- `warning` - Orange background, dark text
- `danger` - Red background, white text
- `outline` - Transparent with border
- `ghost` - Transparent, no border

#### Usage

```blade
{{-- Primary button --}}
<x-ui.button variant="primary">
    {{ __('common.submit') }}
</x-ui.button>

{{-- Loading state --}}
<x-ui.button variant="primary" :loading="$isSubmitting">
    {{ __('common.saving') }}
</x-ui.button>

{{-- With icon --}}
<x-ui.button variant="success">
    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
    {{ __('common.add_new') }}
</x-ui.button>

{{-- Disabled --}}
<x-ui.button variant="primary" :disabled="!$canSubmit">
    {{ __('common.submit') }}
</x-ui.button>
```

#### Accessibility

- Minimum 44×44px touch target
- Focus indicator: 3px outline, 2px offset
- `aria-disabled` when disabled
- `aria-busy` when loading

---

### x-ui.card

Container component with header, body, and footer sections.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `padding` | string | `'p-6'` | Padding classes |
| `shadow` | string | `'shadow'` | Shadow classes |

#### Slots

- `header` - Card header section
- `default` - Card body content
- `footer` - Card footer section

#### Usage

```blade
<x-ui.card>
    <x-slot:header>
        <h3 class="text-lg font-semibold">{{ __('dashboard.statistics') }}</h3>
    </x-slot:header>

    <p>{{ __('dashboard.welcome_message') }}</p>

    <x-slot:footer>
        <x-ui.button variant="primary">{{ __('common.view_all') }}</x-ui.button>
    </x-slot:footer>
</x-ui.card>
```

---

### x-ui.modal

Dialog component with focus trap and keyboard navigation.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | required | Modal identifier |
| `maxWidth` | string | `'md'` | Max width (sm, md, lg, xl, 2xl) |
| `closeable` | bool | `true` | Can be closed by user |

#### Slots

- `title` - Modal title
- `default` - Modal content
- `footer` - Modal footer with actions

#### Usage

```blade
{{-- Trigger --}}
<x-ui.button @click="$dispatch('open-modal', 'confirm-delete')">
    {{ __('common.delete') }}
</x-ui.button>

{{-- Modal --}}
<x-ui.modal name="confirm-delete" maxWidth="sm">
    <x-slot:title>
        {{ __('common.confirm_delete') }}
    </x-slot:title>

    <p>{{ __('common.delete_confirmation_message') }}</p>

    <x-slot:footer>
        <x-ui.button variant="ghost" @click="$dispatch('close-modal')">
            {{ __('common.cancel') }}
        </x-ui.button>
        <x-ui.button variant="danger" wire:click="delete">
            {{ __('common.delete') }}
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>
```

#### Accessibility

- Focus trapped within modal
- Escape key closes modal
- `role="dialog"` and `aria-modal="true"`
- Focus returns to trigger on close

---

### x-ui.alert

Notification component for status messages.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | `'info'` | Alert type (info, success, warning, danger) |
| `dismissible` | bool | `false` | Can be dismissed |
| `icon` | bool | `true` | Show icon |

#### Usage

```blade
{{-- Success alert --}}
<x-ui.alert type="success">
    {{ __('messages.ticket_submitted') }}
</x-ui.alert>

{{-- Dismissible warning --}}
<x-ui.alert type="warning" dismissible>
    {{ __('messages.session_expiring') }}
</x-ui.alert>

{{-- Error with custom content --}}
<x-ui.alert type="danger">
    <strong>{{ __('common.error') }}:</strong>
    {{ $errorMessage }}
</x-ui.alert>
```

---

### x-ui.badge

Small label component for status indicators.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | string | `'default'` | Badge variant |
| `size` | string | `'md'` | Size (sm, md, lg) |

#### Variants

- `default` - Gray background
- `primary` - Blue background
- `success` - Green background
- `warning` - Orange background
- `danger` - Red background

#### Usage

```blade
{{-- Status badges --}}
<x-ui.badge variant="success">{{ __('status.active') }}</x-ui.badge>
<x-ui.badge variant="warning">{{ __('status.pending') }}</x-ui.badge>
<x-ui.badge variant="danger">{{ __('status.overdue') }}</x-ui.badge>
```

---

### x-ui.stats-card

Dashboard statistics card with dynamic styling.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | required | Card title |
| `count` | int | required | Statistic value |
| `type` | string | `'default'` | Card type (default, success, warning, danger) |
| `icon` | string | null | Heroicon name |

#### Dynamic Styling

- When `count = 0` and `type = 'danger'`: Shows green/neutral icon
- When `count > 0` and `type = 'danger'`: Shows red icon

#### Usage

```blade
{{-- Open tickets --}}
<x-ui.stats-card
    :title="__('dashboard.open_tickets')"
    :count="$openTickets"
    type="primary"
    icon="heroicon-o-ticket"
/>

{{-- Overdue items (dynamic styling) --}}
<x-ui.stats-card
    :title="__('dashboard.overdue_items')"
    :count="$overdueItems"
    type="danger"
    icon="heroicon-o-exclamation-triangle"
/>
```

---

### x-ui.user-info-card

Displays verified user information in green/teal card style.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `user` | User | required | User model instance |
| `showDepartment` | bool | `true` | Show department info |

#### Usage

```blade
{{-- In authenticated forms --}}
<x-ui.user-info-card :user="auth()->user()" />

{{-- Without department --}}
<x-ui.user-info-card :user="$user" :showDepartment="false" />
```

---

## Form Components

### x-form.input

Text input component with validation support.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | required | Input name |
| `type` | string | `'text'` | Input type |
| `label` | string | null | Label text |
| `placeholder` | string | null | Placeholder text |
| `required` | bool | `false` | Required field |
| `disabled` | bool | `false` | Disabled state |
| `error` | string | null | Error message |

#### Usage

```blade
{{-- Basic input --}}
<x-form.input
    name="title"
    :label="__('form.title')"
    wire:model="title"
    required
/>

{{-- With error --}}
<x-form.input
    name="email"
    type="email"
    :label="__('form.email')"
    wire:model="email"
    :error="$errors->first('email')"
/>

{{-- Password input --}}
<x-form.input
    name="password"
    type="password"
    :label="__('form.password')"
    wire:model="password"
    required
/>
```

#### Accessibility

- Label associated via `for` attribute
- `aria-describedby` for error messages
- `aria-invalid` when error present
- Focus indicator on input

---

### x-form.select

Dropdown select component with search support.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | required | Select name |
| `label` | string | null | Label text |
| `options` | array | `[]` | Options array |
| `searchable` | bool | `false` | Enable search |
| `multiple` | bool | `false` | Multiple selection |
| `placeholder` | string | null | Placeholder text |

#### Usage

```blade
{{-- Basic select --}}
<x-form.select
    name="category"
    :label="__('form.category')"
    :options="$categories"
    wire:model="category"
/>

{{-- Searchable select (for large lists) --}}
<x-form.select
    name="division"
    :label="__('form.division')"
    :options="$divisions"
    wire:model="division"
    searchable
    :placeholder="__('form.search_division')"
/>

{{-- Multiple select --}}
<x-form.select
    name="assets"
    :label="__('form.select_assets')"
    :options="$assets"
    wire:model="selectedAssets"
    multiple
/>
```

---

### x-form.textarea

Multi-line text input with character counting.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | required | Textarea name |
| `label` | string | null | Label text |
| `rows` | int | `4` | Number of rows |
| `maxLength` | int | null | Maximum characters |
| `showCount` | bool | `false` | Show character count |

#### Usage

```blade
{{-- Basic textarea --}}
<x-form.textarea
    name="description"
    :label="__('form.description')"
    wire:model="description"
    rows="6"
/>

{{-- With character count --}}
<x-form.textarea
    name="message"
    :label="__('form.message')"
    wire:model="message"
    :maxLength="500"
    showCount
/>
```

---

### x-form.checkbox

Checkbox input with proper labeling.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | required | Checkbox name |
| `label` | string | required | Label text |
| `checked` | bool | `false` | Checked state |

#### Usage

```blade
{{-- Single checkbox --}}
<x-form.checkbox
    name="terms"
    :label="__('form.accept_terms')"
    wire:model="acceptTerms"
/>

{{-- Declaration checkbox (mandatory) --}}
<x-form.checkbox
    name="perakuan"
    :label="__('helpdesk.perakuan_text')"
    wire:model="perakuanAccepted"
    required
/>
```

---

### x-form.file-upload

File upload component with drag-and-drop.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | required | Input name |
| `label` | string | null | Label text |
| `accept` | string | `'*'` | Accepted file types |
| `multiple` | bool | `false` | Multiple files |
| `maxSize` | int | `10240` | Max size in KB |
| `maxFiles` | int | `5` | Max number of files |

#### Usage

```blade
{{-- Single file upload --}}
<x-form.file-upload
    name="attachment"
    :label="__('form.attachment')"
    wire:model="attachment"
    accept=".pdf,.doc,.docx"
/>

{{-- Multiple files with drag-drop --}}
<x-form.file-upload
    name="attachments"
    :label="__('form.attachments')"
    wire:model="attachments"
    multiple
    :maxFiles="5"
    :maxSize="10240"
    accept=".jpg,.png,.pdf"
/>
```

---

## Accessibility Components

### x-accessibility.skip-links

Skip navigation links for keyboard users.

#### Usage

```blade
{{-- In layout header --}}
<x-accessibility.skip-links />
```

Provides links to:
- Main content (`#main-content`)
- Navigation (`#main-nav`)
- Search (`#search`)

---

### x-accessibility.language-switcher

Bilingual language toggle with 44×44px touch targets.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `currentLocale` | string | `app()->getLocale()` | Current language |

#### Usage

```blade
{{-- In header --}}
<x-accessibility.language-switcher />
```

---

### x-accessibility.aria-live-region

Dynamic content announcements for screen readers.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `politeness` | string | `'polite'` | Announcement priority (polite, assertive) |

#### Usage

```blade
{{-- Status updates --}}
<x-accessibility.aria-live-region>
    @if($message)
        {{ $message }}
    @endif
</x-accessibility.aria-live-region>

{{-- Error announcements --}}
<x-accessibility.aria-live-region politeness="assertive">
    @if($error)
        {{ $error }}
    @endif
</x-accessibility.aria-live-region>
```

---

### x-accessibility.focus-trap

Traps focus within a container (for modals, dialogs).

#### Usage

```blade
<x-accessibility.focus-trap>
    {{-- Modal content --}}
    <div class="modal-content">
        <input type="text" />
        <button>Submit</button>
        <button>Cancel</button>
    </div>
</x-accessibility.focus-trap>
```

---

## Layout Components

### x-layout.guest

Layout for public/guest pages.

#### Slots

- `title` - Page title
- `default` - Page content

#### Usage

```blade
<x-layout.guest>
    <x-slot:title>{{ __('helpdesk.submit_ticket') }}</x-slot:title>

    {{-- Form content --}}
</x-layout.guest>
```

---

### x-layout.portal

Layout for authenticated portal pages.

#### Slots

- `title` - Page title
- `header` - Page header content
- `default` - Page content

#### Usage

```blade
<x-layout.portal>
    <x-slot:title>{{ __('dashboard.title') }}</x-slot:title>

    <x-slot:header>
        <h1>{{ __('dashboard.welcome', ['name' => auth()->user()->name]) }}</h1>
    </x-slot:header>

    {{-- Dashboard content --}}
</x-layout.portal>
```

---

## Alpine.js Patterns

### Dropdown Pattern

```blade
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" @keydown.escape="open = false">
        {{ __('common.options') }}
    </button>

    <div x-show="open"
         x-transition
         @click.away="open = false"
         class="absolute mt-2 bg-white shadow-lg rounded">
        {{-- Dropdown content --}}
    </div>
</div>
```

### Modal Pattern

```blade
<div x-data="{ showModal: false }">
    <button @click="showModal = true">{{ __('common.open') }}</button>

    <div x-show="showModal"
         x-transition
         x-trap.noscroll="showModal"
         @keydown.escape="showModal = false"
         class="fixed inset-0 z-50">
        {{-- Modal content --}}
    </div>
</div>
```

### Accordion Pattern

```blade
<div x-data="{ activeItem: null }">
    @foreach($items as $index => $item)
        <div>
            <button @click="activeItem = activeItem === {{ $index }} ? null : {{ $index }}"
                    :aria-expanded="activeItem === {{ $index }}">
                {{ $item['title'] }}
            </button>

            <div x-show="activeItem === {{ $index }}" x-collapse>
                {{ $item['content'] }}
            </div>
        </div>
    @endforeach
</div>
```

---

## Usage Guidelines

### Component Naming

- Use kebab-case: `x-ui.stats-card`, `x-form.file-upload`
- Prefix with category: `x-ui.`, `x-form.`, `x-accessibility.`

### Livewire Integration

```blade
{{-- Use wire:model for two-way binding --}}
<x-form.input wire:model="title" />

{{-- Use wire:model.live for real-time updates --}}
<x-form.input wire:model.live="search" />

{{-- Use wire:model.live.debounce for search --}}
<x-form.input wire:model.live.debounce.300ms="search" />
```

### Accessibility Checklist

- [ ] All interactive elements have 44×44px minimum touch target
- [ ] Color contrast meets 4.5:1 for text, 3:1 for UI
- [ ] Focus indicators are visible (3px outline, 2px offset)
- [ ] Form inputs have associated labels
- [ ] Error messages are announced to screen readers
- [ ] Modals trap focus and support Escape key

### Performance Tips

1. Use `wire:model.lazy` for large text fields
2. Use `wire:model.live.debounce.300ms` for search inputs
3. Implement lazy loading for heavy components
4. Use Redis caching for computed properties

---

**Document Compliance**: D00-D15, WCAG 2.2 AA  
**Component Playground**: `/dev/components` (development only)

