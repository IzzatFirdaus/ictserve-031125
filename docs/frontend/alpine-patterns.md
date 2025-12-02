# Alpine.js 3.x Patterns Documentation

## Overview

Alpine.js 3.x provides lightweight client-side interactivity for the ICTServe system. Use Alpine for UI interactions that don't require server communication.

**Requirements:** R05 (Alpine.js 3.x Client-Side Interactivity)  
**Design Reference:** [design.md Alpine.js Patterns](file:///c:/XAMPP/htdocs/ictserve-031125/.kiro/specs/updated-frontend/design.md#alpine-js-3x-patterns)

**Principle:** Minimize Alpine usage in favor of Livewire for server-driven interactions.

## Core Directives

### x-data (Component Initialization)

```html
<div x-data="{ open: false }">
 <button @click="open = !open">Toggle</button>
 <div x-show="open">Content</div>
</div>

<!-- Complex state -->
<div
 x-data="{
    count: 0,
    items: [],
    loading: false
}"
>
 <!-- Component logic -->
</div>
```

### x-show (Conditional Visibility)

```html
<!-- Simple toggle with transitions -->
<div x-data="{ open: false }">
 <button @click="open = !open">Show/Hide</button>

 <div x-show="open" x-transition class="mt-2">Toggleable content</div>
</div>
```

**vs x-if:** Use `x-show` for frequent toggles (keeps DOM), `x-if` for one-time rendering

### x-transition (Smooth Animations)

```html
<!-- Simple fade and scale -->
<div x-show="open" x-transition>Fade and scale transition</div>

<!-- Custom duration -->
<div x-show="open" x-transition.duration.500ms>500ms transition</div>

<!-- Custom classes -->
<div
 x-show="open"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform scale-90"
 x-transition:enter-end="opacity-100 transform scale-100"
 x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0"
>
 Custom transition
</div>
```

### x-trap (Focus Management)

```html
<!-- Modal with focus trap -->
<div x-data="{ open: false }" @keydown.escape.window="open = false">
 <button @click="open = true">Open Modal</button>

 <div x-show="open" x-trap="open" class="fixed inset-0 z-50">
  <input type="text" />
  <!-- Tab cycles within modal -->
  <button @click="open = false">Close</button>
 </div>
</div>
```

**Accessibility:** Essential for modals and overlays to trap keyboard navigation

## Event Handling

### Click Events

```html
<!-- Basic click -->
<button @click="count++">Increment</button>

<!-- Prevent default -->
<form @submit.prevent="handleSubmit()">
 <button type="submit">Submit</button>
</form>

<!-- Stop propagation -->
<div @click="parentClick()">
 <button @click.stop="childClick()">Child</button>
</div>
```

### Click Away

```html
<!-- Dropdown that closes when clicking outside -->
<div x-data="{ open: false }" @click.away="open = false">
 <button @click="open = !open">Toggle Menu</button>

 <div x-show="open" class="absolute">
  <!-- Menu items -->
 </div>
</div>
```

**Use for:** Dropdowns, popovers, context menus

### Keyboard Events

```html
<!-- Escape key to close -->
<div x-data="{ open: true }" @keydown.escape.window="open = false">
 <!-- Modal content -->
</div>

<!-- Enter  key to submit -->
<input type="text" @keydown.enter="submit()" />

<!-- Global keyboard shortcuts -->
<div x-data x-init @keydown.window.alt.n="$dispatch('new-ticket')">
 <!-- App content -->
</div>
```

## Common Patterns

### Dropdown Pattern

```html
<div x-data="{ open: false }" @click.away="open = false" class="relative">
 <!-- Trigger -->
 <button
  @click="open = !open"
  @keydown.escape="open = false"
  :aria-expanded="open"
  aria-haspopup="true"
  class="min-h-[44px] min-w-[44px]"
 >
  <span>Options</span>
  <svg :class="{ 'rotate-180': open }"><!-- Icon --></svg>
 </button>

 <!-- Dropdown -->
 <div
  x-show="open"
  x-transition
  @keydown.escape.window="open = false"
  role="menu"
  class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg"
 >
  <a href="#" role="menuitem" class="block px-4 py-2 hover:bg-gray-100">
   Option 1
  </a>
  <a href="#" role="menuitem" class="block px-4 py-2 hover:bg-gray-100">
   Option 2
  </a>
 </div>
</div>
```

**WCAG Compliance:**

- 44×44px touch target
- ARIA attributes (`aria-expanded`, `role="menu"`)
- Keyboard navigation (Escape key)
- Click-away detection

### Modal Pattern

```html
<div x-data="{ open: false }">
 <!-- Trigger -->
 <button @click="open = true">Open Modal</button>

 <!-- Modal -->
 <div
  x-show="open"
  @keydown.escape.window="open = false"
  x-trap="open"
  class="fixed inset-0 z-50 overflow-y-auto"
  role="dialog"
  aria-modal="true"
  aria-labelledby="modal-title"
 >
  <!-- Backdrop -->
  <div
   x-show="open"
   x-transition:enter="ease-out duration-300"
   x-transition:enter-start="opacity-0"
   x-transition:enter-end="opacity-100"
   @click="open = false"
   class="fixed inset-0 bg-black bg-opacity-50"
  ></div>

  <!-- Modal Content -->
  <div
   x-show="open"
   x-transition
   @click.stop
   class="relative bg-white rounded-lg max-w-lg mx-auto mt-20 p-6"
  >
   <h2 id="modal-title" class="text-xl font-bold">Modal Title</h2>
   <div class="mt-4">Modal content</div>

   <button
    @click="open = false"
    class="mt-4 px-4 py-2 bg-primary-600 text-white rounded"
   >
    Close
   </button>
  </div>
 </div>
</div>
```

**Accessibility Features:**

- Focus trap (`x-trap`)
- Escape key to close
- ARIA attributes (`role="dialog"`, `aria-modal`)
- Backdrop click to close

### Accordion Pattern

```html
<div x-data="{ activeItem: null }">
 <!-- Item 1 -->
 <div class="border-b">
  <button
   @click="activeItem = activeItem === 1 ? null : 1"
   :aria-expanded="activeItem === 1"
   class="w-full text-left px-4 py-3 flex justify-between min-h-[44px]"
  >
   <span>Item 1</span>
   <svg :class="{ 'rotate-180': activeItem === 1 }"><!-- Icon --></svg>
  </button>

  <div x-show="activeItem === 1" x-collapse>
   <div class="px-4 py-3 bg-gray-50">Content 1</div>
  </div>
 </div>

 <!-- Item 2 -->
 <div class="border-b">
  <button
   @click="activeItem = activeItem === 2 ? null : 2"
   :aria-expanded="activeItem === 2"
   class="w-full text-left px-4 py-3 flex justify-between min-h-[44px]"
  >
   <span>Item 2</span>
   <svg :class="{ 'rotate-180': activeItem === 2 }"><!-- Icon --></svg>
  </button>

  <div x-show="activeItem === 2" x-collapse>
   <div class="px-4 py-3 bg-gray-50">Content 2</div>
  </div>
 </div>
</div>
```

### Tabs Pattern

```html
<div x-data="{ activeTab: 'tab1' }">
 <!-- Tab Navigation -->
 <div role="tablist" class="flex border-b">
  <button
   @click="activeTab = 'tab1'"
   :aria-selected="activeTab === 'tab1'"
   :class="{ 'border-b-2 border-primary-600': activeTab === 'tab1' }"
   role="tab"
   class="px-4 py-2 min-h-[44px]"
  >
   Tab 1
  </button>
  <button
   @click="activeTab = 'tab2'"
   :aria-selected="activeTab === 'tab2'"
   :class="{ 'border-b-2 border-primary-600': activeTab === 'tab2' }"
   role="tab"
   class="px-4 py-2 min-h-[44px]"
  >
   Tab 2
  </button>
 </div>

 <!-- Tab Panels -->
 <div x-show="activeTab === 'tab1'" role="tabpanel" class="p-4">
  Tab 1 Content
 </div>
 <div x-show="activeTab === 'tab2'" role="tabpanel" class="p-4">
  Tab 2 Content
 </div>
</div>
```

## Integration with Livewire

### Two-Way Binding with @entangle

```html
<div
 x-data="{ 
    search: @entangle('search'),
    showResults: false 
}"
>
 <!-- Alpine controls UI, Livewire handles data -->
 <input
  type="text"
  x-model="search"
  @focus="showResults = true"
  @click.away="showResults = false"
 />

 <div x-show="showResults" x-transition>
  <!-- Livewire component renders results -->
  <livewire:search-results :search="$search" />
 </div>
</div>
```

**Best Practice:** Use `@entangle` for state that needs both client-side UI control and server-side persistence

### Listening to Livewire Events

```html
<div
 x-data="{ notificationVisible: false, message: '' }"
 @notify.window="
        message = $event.detail.message;
        notificationVisible = true;
        setTimeout(() => notificationVisible = false, 3000);
     "
>
 <!-- Toast Notification -->
 <div
  x-show="notificationVisible"
  x-transition
  class="fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg"
 >
  <span x-text="message"></span>
 </div>
</div>
```

**Livewire Side:**

```php
$this->dispatch('notify', message: 'Operation successful!');
```

## Accessibility Best Practices

### ARIA Attributes

```html
<!-- Dynamic ARIA attributes -->
<button
 @click="open = !open"
 :aria-expanded="open"
 :aria-label="open ? 'Close menu' : 'Open menu'"
 class="min-h-[44px] min-w-[44px]"
>
 Menu
</button>

<!-- ARIA live regions for screen readers -->
<div x-data="{ count: 0 }">
 <button @click="count++">Add Item</button>

 <div role="status" aria-live="polite" aria-atomic="true" class="sr-only">
  <span x-text="`${count} items in cart`"></span>
 </div>
</div>
```

### Keyboard Navigation

```html
<!-- Focus management -->
<div
 x-data="{ focusIndex: 0, items: ['Item 1', 'Item 2', 'Item 3'] }"
 @keydown.arrow-down.prevent="focusIndex = Math.min(focusIndex + 1, items.length - 1)"
 @keydown.arrow-up.prevent="focusIndex = Math.max(focusIndex - 1, 0)"
>
 <template x-for="(item, index) in items" :key="index">
  <button
   :tabindex="focusIndex === index ? 0 : -1"
   @focus="focusIndex = index"
   class="block w-full text-left px-4 py-2"
  >
   <span x-text="item"></span>
  </button>
 </template>
</div>
```

## Performance Tips

1. **Minimize State**: Only track what's necessary in `x-data`
2. **Use x-cloak**: Prevent FOUC (Flash of Unstyled Content)

   ```html
   <div x-data="{ loaded: false }" x-init="loaded = true" x-cloak>
    <!-- Content -->
   </div>
   ```

3. **Debounce Expensive Operations**: Use `x-on:input.debounce.500ms`
4. **Lazy Load Alpine**: Use `x-init` for initialization logic

## Security Considerations

```html
<!-- ❌ NEVER use x-html with user input -->
<div x-html="userInput"></div>
<!-- XSS vulnerability! -->

<!-- ✅ Use x-text for user content -->
<div x-text="userInput"></div>

<!-- ✅ Or use Blade for proper escaping -->
<div>{{ $userInput }}</div>
```

## Testing Alpine Components

```javascript
// Playwright test example
test("dropdown opens and closes", async ({ page }) => {
 await page.goto("/components/dropdown");

 // Initially closed
 await expect(page.locator('[role="menu"]')).not.toBeVisible();

 // Click to open
 await page.click('button:has-text("Options")');
 await expect(page.locator('[role="menu"]')).toBeVisible();

 // Escape to close
 await page.keyboard.press("Escape");
 await expect(page.locator('[role="menu"]')).not.toBeVisible();

 // Click away to close
 await page.click('button:has-text("Options")');
 await page.click("body");
 await expect(page.locator('[role="menu"]')).not.toBeVisible();
});
```

## When to Use Alpine vs Livewire

| Feature          | Alpine | Livewire |
| ---------------- | ------ | -------- |
| Dropdown menus   | ✅     | ❌       |
| Modal show/hide  | ✅     | ❌       |
| Tabs/Accordions  | ✅     | ❌       |
| Form validation  | ❌     | ✅       |
| Data persistence | ❌     | ✅       |
| Search/Filter    | ❌     | ✅       |
| Complex state    | ❌     | ✅       |

**Rule of Thumb:** If it needs the server, use Livewire. If it's just UI state, use Alpine.

---

**Document Version:** 1.0  
**Last Updated:** 2025-11-26  
**Task Reference:** 1.5.1, 1.5.4 (Document Alpine.js patterns and ARIA toggling)
