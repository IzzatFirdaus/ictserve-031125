---
applyTo:
  - 'resources/views/**'
  - 'resources/js/**'
  - '**/*.blade.php'
description: |
  Alpine.js 3 reactive directives, state management, plugins, and integration patterns
  for ICTServe project. Lightweight JavaScript framework for interactive UI components.
tags:
  - alpinejs
  - javascript
  - reactive
  - frontend
  - directives
version: '1.0.0'
lastUpdated: '2025-01-06'
---

# Alpine.js 3 — ICTServe Interactive UI Standards

## Overview

This rule defines Alpine.js 3 conventions for ICTServe. Alpine.js is a lightweight JavaScript framework for adding interactivity to HTML with minimal overhead. It's included with Livewire 3 and provides reactive behavior through declarative directives.

**Framework**: Alpine.js 3.15.1  
**Applies To**: Blade views, Livewire components, interactive UI elements  
**Traceability**: D13 (UI/UX Frontend Framework), D14 (UI/UX Design Guide)

## Core Principles

1. **Declarative Syntax**: Define behavior directly in HTML with `x-` directives
2. **Reactive State**: Use `x-data` for component state management
3. **Minimal JavaScript**: Avoid external JS files when Alpine suffices
4. **Livewire Integration**: Alpine is included with Livewire 3 (no manual installation)
5. **Progressive Enhancement**: Start with HTML, enhance with Alpine

## Alpine.js Key Features

- ✅ **Reactive Data**: `x-data` for component state
- ✅ **Event Handling**: `x-on` (or `@`) for event listeners
- ✅ **Conditional Rendering**: `x-if`, `x-show` for visibility control
- ✅ **Loops**: `x-for` for rendering lists
- ✅ **Two-Way Binding**: `x-model` for form inputs
- ✅ **Transitions**: `x-transition` for smooth animations
- ✅ **Plugins**: Persist, Intersect, Collapse, Focus (included with Livewire 3)

---

## Installation & Setup

**Alpine.js is included with Livewire 3** — No manual installation required:

```blade
{{-- Alpine.js automatically loaded with Livewire --}}
@livewireScripts
```

**Manual Installation** (if not using Livewire):

```bash
npm install alpinejs
```

```javascript
// resources/js/app.js
import Alpine from 'alpinejs'

window.Alpine = Alpine
Alpine.start()
```

---

## Core Directives

### x-data (Component State)

**Define reactive component state**:

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Content</div>
</div>
```

**Multiple Properties**:

```blade
<div x-data="{ 
    count: 0, 
    name: '', 
    items: ['Item 1', 'Item 2'] 
}">
    <p>Count: <span x-text="count"></span></p>
    <button @click="count++">Increment</button>
</div>
```

**Component Methods**:

```blade
<div x-data="{
    count: 0,
    increment() {
        this.count++
    },
    decrement() {
        this.count--
    }
}">
    <button @click="decrement">-</button>
    <span x-text="count"></span>
    <button @click="increment">+</button>
</div>
```

---

### x-show (Toggle Visibility)

**Show/hide elements** (element remains in DOM):

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">
        Kandungan yang boleh ditoggle
    </div>
</div>
```

**With Transition**:

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open" x-transition>
        Smooth fade in/out
    </div>
</div>
```

---

### x-if (Conditional Rendering)

**Add/remove elements from DOM**:

```blade
<div x-data="{ show: true }">
    <button @click="show = !show">Toggle</button>
    
    <template x-if="show">
        <div>Element added/removed from DOM</div>
    </template>
</div>
```

**Note**: `x-if` must be on `<template>` tag

---

### x-for (Loops)

**Render lists**:

```blade
<div x-data="{ items: ['Laptop', 'Mouse', 'Keyboard'] }">
    <ul>
        <template x-for="item in items" :key="item">
            <li x-text="item"></li>
        </template>
    </ul>
</div>
```

**With Index**:

```blade
<div x-data="{ items: ['First', 'Second', 'Third'] }">
    <ul>
        <template x-for="(item, index) in items" :key="index">
            <li>
                <span x-text="index + 1"></span>: 
                <span x-text="item"></span>
            </li>
        </template>
    </ul>
</div>
```

---

### x-on (Event Handling)

**Listen to events** (shorthand: `@`):

```blade
<!-- Full syntax -->
<button x-on:click="count++">Click Me</button>

<!-- Shorthand (preferred) -->
<button @click="count++">Click Me</button>
```

**Event Modifiers**:

```blade
<!-- Prevent default -->
<form @submit.prevent="handleSubmit">
    <button type="submit">Submit</button>
</form>

<!-- Stop propagation -->
<div @click="outer">
    <button @click.stop="inner">Inner</button>
</div>

<!-- Once (run only once) -->
<button @click.once="initialize">Initialize</button>

<!-- Debounce (wait 500ms) -->
<input @input.debounce.500ms="search">

<!-- Throttle (max once per 1000ms) -->
<div @scroll.throttle.1000ms="handleScroll">
```

**Keyboard Events**:

```blade
<!-- Specific keys -->
<input @keyup.enter="submit">
<input @keyup.escape="close">
<input @keyup.space="toggle">

<!-- Key combinations -->
<input @keyup.ctrl.enter="save">
<input @keyup.shift.tab="previous">
```

---

### x-model (Two-Way Binding)

**Bind form inputs to state**:

```blade
<div x-data="{ name: '' }">
    <input type="text" x-model="name" placeholder="Nama">
    <p>Hello, <span x-text="name"></span>!</p>
</div>
```

**Modifiers**:

```blade
<!-- Lazy (update on change, not input) -->
<input x-model.lazy="name">

<!-- Number (convert to number) -->
<input type="number" x-model.number="age">

<!-- Debounce (wait 500ms) -->
<input x-model.debounce.500ms="search">

<!-- Throttle (max once per 1000ms) -->
<input x-model.throttle.1000ms="query">
```

**Checkboxes & Radio Buttons**:

```blade
<div x-data="{ checked: false }">
    <input type="checkbox" x-model="checked">
    <span x-text="checked ? 'Checked' : 'Unchecked'"></span>
</div>

<div x-data="{ selected: '' }">
    <input type="radio" x-model="selected" value="option1"> Option 1
    <input type="radio" x-model="selected" value="option2"> Option 2
    <p>Selected: <span x-text="selected"></span></p>
</div>
```

**Select Dropdown**:

```blade
<div x-data="{ category: '' }">
    <select x-model="category">
        <option value="">Pilih Kategori</option>
        <option value="hardware">Hardware</option>
        <option value="software">Software</option>
    </select>
    <p>Kategori: <span x-text="category"></span></p>
</div>
```

---

### x-text & x-html

**Set text content**:

```blade
<div x-data="{ message: 'Hello World' }">
    <p x-text="message"></p>
</div>
```

**Set HTML content** (use with caution):

```blade
<div x-data="{ html: '<strong>Bold Text</strong>' }">
    <div x-html="html"></div>
</div>
```

---

### x-bind (Attribute Binding)

**Bind attributes** (shorthand: `:`):

```blade
<!-- Full syntax -->
<img x-bind:src="imageUrl">

<!-- Shorthand (preferred) -->
<img :src="imageUrl">
```

**Common Use Cases**:

```blade
<div x-data="{ 
    isActive: false,
    imageUrl: '/images/logo.png',
    linkUrl: 'https://example.com'
}">
    <!-- Class binding -->
    <div :class="isActive ? 'active' : 'inactive'">Status</div>
    
    <!-- Style binding -->
    <div :style="{ color: isActive ? 'green' : 'red' }">Text</div>
    
    <!-- Attribute binding -->
    <img :src="imageUrl" :alt="'Logo'">
    <a :href="linkUrl">Link</a>
    
    <!-- Disabled state -->
    <button :disabled="!isActive">Submit</button>
</div>
```

**Multiple Classes**:

```blade
<div x-data="{ isActive: true, hasError: false }">
    <div :class="{
        'active': isActive,
        'error': hasError,
        'base-class': true
    }">
        Dynamic classes
    </div>
</div>
```

---

### x-transition (Animations)

**Smooth transitions**:

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    
    <!-- Default transition (fade) -->
    <div x-show="open" x-transition>
        Smooth fade in/out
    </div>
</div>
```

**Custom Transitions**:

```blade
<div x-show="open"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-90"
>
    Custom transition
</div>
```

**Transition Modifiers**:

```blade
<!-- Scale transition -->
<div x-show="open" x-transition.scale>Scale in/out</div>

<!-- Opacity transition -->
<div x-show="open" x-transition.opacity>Fade in/out</div>

<!-- Duration -->
<div x-show="open" x-transition.duration.500ms>500ms transition</div>
```

---

## Advanced Directives

### x-cloak (Hide Until Ready)

**Hide elements until Alpine initializes**:

```blade
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ message: 'Hello' }" x-cloak>
    <p x-text="message"></p>
</div>
```

---

### x-ref (Element References)

**Access DOM elements**:

```blade
<div x-data="{ 
    focusInput() {
        this.$refs.nameInput.focus()
    }
}">
    <input x-ref="nameInput" type="text">
    <button @click="focusInput">Focus Input</button>
</div>
```

---

### x-effect (Side Effects)

**Run code when dependencies change**:

```blade
<div x-data="{ count: 0 }" x-effect="console.log('Count:', count)">
    <button @click="count++">Increment</button>
    <!-- Console logs every time count changes -->
</div>
```

---

### x-ignore

**Prevent Alpine from initializing**:

```blade
<div x-ignore>
    <!-- Alpine won't process this section -->
    <div x-data="{ ignored: true }">
        This won't be reactive
    </div>
</div>
```

---

## Magic Properties

### $el (Current Element)

```blade
<div x-data @click="$el.classList.add('clicked')">
    Click me
</div>
```

---

### $refs (Element References)

```blade
<div x-data>
    <input x-ref="email" type="email">
    <button @click="$refs.email.focus()">Focus Email</button>
</div>
```

---

### $watch (Watch State Changes)

```blade
<div x-data="{ count: 0 }" x-init="$watch('count', value => console.log(value))">
    <button @click="count++">Increment</button>
</div>
```

---

### $dispatch (Dispatch Events)

```blade
<div x-data @custom-event="alert('Event received!')">
    <button @click="$dispatch('custom-event')">Dispatch Event</button>
</div>
```

**With Data**:

```blade
<div x-data @notify="alert($event.detail.message)">
    <button @click="$dispatch('notify', { message: 'Hello!' })">
        Notify
    </button>
</div>
```

---

### $nextTick (Wait for DOM Update)

```blade
<div x-data="{ show: false }">
    <button @click="
        show = true
        $nextTick(() => {
            $refs.input.focus()
        })
    ">Show Input</button>
    
    <input x-show="show" x-ref="input" type="text">
</div>
```

---

## Alpine Plugins (Included with Livewire 3)

### Persist Plugin

**Persist state to localStorage**:

```blade
<div x-data="{ 
    count: $persist(0),
    name: $persist('').as('username')
}">
    <button @click="count++">Count: <span x-text="count"></span></button>
    <input x-model="name" placeholder="Name">
    <!-- State persists across page reloads -->
</div>
```

---

### Intersect Plugin

**Detect when element enters viewport**:

```blade
<div x-data="{ shown: false }" 
     x-intersect="shown = true"
     x-show="shown"
     x-transition>
    Appears when scrolled into view
</div>
```

**With Modifiers**:

```blade
<!-- Once (trigger only once) -->
<div x-intersect.once="loadMore()">Load more</div>

<!-- Half (trigger when 50% visible) -->
<div x-intersect.half="trackView()">Track view</div>

<!-- Full (trigger when 100% visible) -->
<div x-intersect.full="animate()">Animate</div>
```

---

### Collapse Plugin

**Smooth height transitions**:

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    
    <div x-show="open" x-collapse>
        <p>Content with smooth height transition</p>
        <p>Multiple lines of content</p>
    </div>
</div>
```

---

### Focus Plugin

**Manage focus within components**:

```blade
<div x-data="{ open: false }" x-trap="open">
    <button @click="open = true">Open Modal</button>
    
    <div x-show="open">
        <input type="text" x-focus>
        <button @click="open = false">Close</button>
    </div>
</div>
```

---

## Common Patterns

### Dropdown Menu

```blade
<div x-data="{ open: false }" @click.away="open = false">
    <button @click="open = !open">
        Menu
    </button>
    
    <div x-show="open" 
         x-transition
         class="absolute mt-2 bg-white shadow-lg rounded">
        <a href="#" class="block px-4 py-2">Item 1</a>
        <a href="#" class="block px-4 py-2">Item 2</a>
        <a href="#" class="block px-4 py-2">Item 3</a>
    </div>
</div>
```

---

### Modal Dialog

```blade
<div x-data="{ open: false }">
    <button @click="open = true">Open Modal</button>
    
    <div x-show="open" 
         x-transition.opacity
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
         @click="open = false">
        
        <div @click.stop 
             x-show="open"
             x-transition
             class="bg-white rounded-lg p-6 max-w-md">
            <h2 class="text-xl font-bold mb-4">Modal Title</h2>
            <p class="mb-4">Modal content here</p>
            <button @click="open = false" class="btn">Close</button>
        </div>
    </div>
</div>
```

---

### Tabs Component

```blade
<div x-data="{ activeTab: 'tab1' }">
    <div class="flex gap-2 border-b">
        <button @click="activeTab = 'tab1'" 
                :class="activeTab === 'tab1' ? 'border-b-2 border-blue-600' : ''">
            Tab 1
        </button>
        <button @click="activeTab = 'tab2'"
                :class="activeTab === 'tab2' ? 'border-b-2 border-blue-600' : ''">
            Tab 2
        </button>
    </div>
    
    <div class="mt-4">
        <div x-show="activeTab === 'tab1'" x-transition>
            Tab 1 content
        </div>
        <div x-show="activeTab === 'tab2'" x-transition>
            Tab 2 content
        </div>
    </div>
</div>
```

---

### Accordion

```blade
<div x-data="{ open: null }">
    <div class="border-b">
        <button @click="open = open === 1 ? null : 1" class="w-full text-left p-4">
            Section 1
        </button>
        <div x-show="open === 1" x-collapse>
            <div class="p-4">Section 1 content</div>
        </div>
    </div>
    
    <div class="border-b">
        <button @click="open = open === 2 ? null : 2" class="w-full text-left p-4">
            Section 2
        </button>
        <div x-show="open === 2" x-collapse>
            <div class="p-4">Section 2 content</div>
        </div>
    </div>
</div>
```

---

### Search Filter

```blade
<div x-data="{ 
    search: '',
    items: ['Laptop', 'Mouse', 'Keyboard', 'Monitor'],
    get filteredItems() {
        return this.items.filter(item => 
            item.toLowerCase().includes(this.search.toLowerCase())
        )
    }
}">
    <input x-model="search" placeholder="Cari..." class="mb-4">
    
    <ul>
        <template x-for="item in filteredItems" :key="item">
            <li x-text="item"></li>
        </template>
    </ul>
</div>
```

---

### Counter with Limits

```blade
<div x-data="{ 
    count: 0,
    min: 0,
    max: 10,
    increment() {
        if (this.count < this.max) this.count++
    },
    decrement() {
        if (this.count > this.min) this.count--
    }
}">
    <button @click="decrement" :disabled="count <= min">-</button>
    <span x-text="count" class="mx-4"></span>
    <button @click="increment" :disabled="count >= max">+</button>
</div>
```

---

## Alpine + Livewire Integration

**Livewire dispatches events to Alpine**:

```blade
<div x-data="{ show: false }" @notify.window="show = true; setTimeout(() => show = false, 3000)">
    <div x-show="show" x-transition class="alert">
        Notification message
    </div>
</div>

{{-- Livewire component dispatches event --}}
<button wire:click="$dispatch('notify')">Notify</button>
```

**Alpine calls Livewire methods**:

```blade
<div x-data>
    <button @click="$wire.save()">Save (calls Livewire method)</button>
    <button @click="$wire.delete(itemId)">Delete</button>
</div>
```

**Access Livewire properties in Alpine**:

```blade
<div x-data="{ count: $wire.entangle('count') }">
    <button @click="count++">Increment (synced with Livewire)</button>
    <span x-text="count"></span>
</div>
```

---

## Performance Best Practices

1. **Use `x-show` for frequent toggles** (keeps element in DOM)
2. **Use `x-if` for expensive renders** (removes from DOM)
3. **Debounce user input** with `.debounce` modifier
4. **Use `x-cloak`** to prevent flash of unstyled content
5. **Avoid complex expressions** in templates (use methods instead)

---

## Accessibility Considerations

**Keyboard Navigation**:

```blade
<div x-data="{ open: false }" @keydown.escape.window="open = false">
    <button @click="open = !open" @keydown.enter="open = !open">
        Toggle
    </button>
    <div x-show="open">Content</div>
</div>
```

**ARIA Attributes**:

```blade
<div x-data="{ open: false }">
    <button @click="open = !open" 
            :aria-expanded="open"
            aria-controls="content">
        Toggle
    </button>
    <div id="content" x-show="open" :aria-hidden="!open">
        Content
    </div>
</div>
```

**Focus Management**:

```blade
<div x-data="{ open: false }">
    <button @click="open = true">Open</button>
    
    <div x-show="open" x-trap="open">
        <input x-ref="firstInput" type="text">
        <button @click="open = false">Close</button>
    </div>
</div>
```

---

## References & Resources

- **Alpine.js Documentation**: <https://alpinejs.dev>
- **Alpine.js Plugins**: <https://alpinejs.dev/plugins>
- **Livewire + Alpine**: <https://livewire.laravel.com/docs/alpine>
- **ICTServe Traceability**: D13 (UI/UX Frontend Framework), D14 (UI/UX Design Guide)

---

## Compliance Checklist

When using Alpine.js, ensure:

- [ ] Use `x-data` to define component state
- [ ] Use `@` shorthand for event listeners
- [ ] Use `:` shorthand for attribute binding
- [ ] Include `x-cloak` to prevent flash of unstyled content
- [ ] Use `x-show` for frequent toggles, `x-if` for expensive renders
- [ ] Add `.debounce` modifier to search inputs
- [ ] Use `@click.away` to close dropdowns/modals
- [ ] Include ARIA attributes for accessibility
- [ ] Use `x-trap` for focus management in modals
- [ ] Test keyboard navigation (Enter, Escape, Tab)

---

**Status**: ✅ Active for ICTServe Alpine.js 3 development  
**Version**: 1.0.0  
**Last Updated**: 2025-01-06
