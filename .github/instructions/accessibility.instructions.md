# Accessibility (WCAG 2.2 AA) Instructions

**Purpose**
Defines mandatory accessibility (a11y) standards, developer guardrails, and testing workflows for ICTServe. This file is normative for all UI code to ensure compliance with WCAG 2.2 Level AA and D12/D14/D15 policies.

**Scope**
Applies to `resources/views`, `app/Livewire`, `app/Filament`, and all client-side interactions (Alpine.js).

## 1. Mandatory Requirements (WCAG 2.2 AA)

### Perceivable
- **Text Alternatives**: All `<img>` tags MUST have `alt`. Use `alt=""` for decorative images.
- **Contrast**: Normal text **4.5:1**, Large text **3:1**. User interface components **3:1**.
- **Content Scaling**: UI must remain usable at 200% zoom without horizontal scrolling (Reflow).

### Operable
- **Keyboard Access**: All functionality must be accessible via keyboard (Tab, Enter, Space, Arrow Keys).
- **Focus Visible**: All interactive elements MUST have a visible focus indicator (e.g., Tailwind `focus:ring`).
- **No Keyboard Traps**: Focus must not get stuck in a component unless it's a modal (which must provide an escape).
- **Target Size**: Touch targets must be at least **24x24px** (WCAG 2.2 AA), preferably **44x44px** (Mobile Best Practice).

### Understandable
- **Language**: The `<html>` element must have a valid `lang` attribute (e.g., `ms` or `en`).
- **Labels**: All form inputs must have a visible label or `aria-label`. Placeholders are NOT labels.
- **Error Identification**: Validation errors must be described in text and linked via `aria-describedby`.

### Robust
- **Parsing**: IDs must be unique. HTML must be nested correctly.
- **Status Messages**: Use `aria-live` regions for dynamic content updates (e.g., loading states, toast notifications) without moving focus.

## 2. Component Implementation Patterns

### Semantic Forms
All inputs must be associated with labels and error messages.

```html
<div class="mb-4">
    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
        Alamat E-mel
    </label>
    <input 
        type="email" 
        id="email" 
        name="email"
        aria-describedby="email-error"
        aria-required="true"
        class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"
    >
    
    @error('email')
        <p id="email-error" class="mt-2 text-sm text-red-600" role="alert">
            {{ $message }}
        </p>
    @enderror
</div>
````

### Focus Management (Modals & Flyouts)

Use Alpine.js `x-trap` (Focus plugin) to contain focus within modals.

```html
<div 
    x-data="{ open: false }" 
    x-show="open" 
    x-trap.noscroll="open"
    role="dialog" 
    aria-modal="true"
    aria-labelledby="modal-title"
    @keydown.escape="open = false"
>
    <div class="modal-content">
        <h2 id="modal-title">Pengesahan</h2>
        <p>Adakah anda pasti?</p>
        <button @click="open = false">Tutup</button>
    </div>
</div>
```

### Skip Navigation

Provide a skip link as the first focusable element in the DOM.

```html
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-blue-600 focus:rounded-md focus:shadow-lg">
    Langkau ke kandungan utama
</a>

<main id="main-content" tabindex="-1" class="outline-none">
    </main>
```

### Screen Reader Only (`.sr-only`)

Use for visually hidden content that provides context to screen readers (e.g., icon buttons).

```html
<button type="button" class="p-2 text-gray-400 hover:text-gray-500">
    <span class="sr-only">Tutup Menu</span>
    <x-heroicon-o-x-mark class="w-6 h-6" aria-hidden="true" />
</button>
```

## 3\. Testing & Validation

### Automated Testing

  - **CI Pipeline**: Run `npm run test:accessibility` (uses Axe Core/Playwright) on every PR.
  - **Local Check**: Use **Axe DevTools** extension or **Lighthouse** (Accessibility category).

### Manual Testing Checklist

1.  **Keyboard**: Unplug mouse. Tab through the entire flow. Can you reach/activate everything? Are focus rings visible?
2.  **Zoom**: Set browser zoom to 200%. Is content readable? Does the layout break?
3.  **Screen Reader**: Use NVDA (Windows) or VoiceOver (Mac).
      * Are images described?
      * Are form errors announced?
      * Do modals trap focus?

### Traceability

All accessibility fixes must reference the relevant D12/D15 section in commit messages.

  * *Example*: `fix(ui): add aria-labels to pagination (Ref: D15 §2.4)`
