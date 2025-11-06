# Frontend Modernization Task Specifications

**Task ID**: FRONTEND-MOD-001  
**Priority**: HIGH  
**Estimated Effort**: 3-4 weeks  
**Tech Stack**: Laravel 12, Blade, Livewire 3, Volt, Tailwind CSS 3, Alpine.js 3

---

## OBJECTIVE

Modernize ICTServe frontend architecture to align with Laravel 12 + Livewire 3 + Volt best practices, focusing on component patterns, performance optimization, and reusable UI library.

---

## DELIVERABLES

### 1. **Livewire 3 Pattern Migration** (Priority: CRITICAL)

**Scope**: Audit and update all Livewire components to v3 patterns

- [ ] Replace `wire:model.defer` with `wire:model.live` (or plain `wire:model`)
- [ ] Update `$this->emit()` to `$this->dispatch()`
- [ ] Migrate to PHP 8 attributes: `#[Reactive]`, `#[Computed]`, `#[Layout]`, `#[Locked]`, `#[Session]`
- [ ] Add `wire:key` to all `@foreach` loops
- [ ] Verify namespace: `App\Livewire\` (not `App\Http\Livewire\`)

**Files to Audit**:

- `app/Livewire/**/*.php`
- `resources/views/livewire/**/*.blade.php`

**Example Migration**:

```php
// OLD (Livewire v2)
class Counter extends Component {
    public $count = 0;
    public function updated() { $this->emit('countChanged'); }
}

// NEW (Livewire v3)
class Counter extends Component {
    #[Reactive] public $count = 0;
    public function updated() { $this->dispatch('countChanged'); }
}
```

---

### 2. **Volt Single-File Components** (Priority: HIGH)

**Scope**: Create Volt components for simple, reusable UI elements

- [ ] Convert simple forms to Volt SFC (ticket submission, loan request)
- [ ] Create filter components using Volt functional API
- [ ] Implement modal dialogs with Volt
- [ ] Build search components with debounced inputs

**Target Components**:

- `resources/views/components/⚡ticket-form.blade.php`
- `resources/views/components/⚡asset-filter.blade.php`
- `resources/views/components/⚡search-bar.blade.php`

**Example Pattern**:

```php
<?php
use function Livewire\Volt\{state, computed};

state(['search' => '', 'category' => 'all']);

$results = computed(fn() => 
    Asset::when($this->search, fn($q) => 
        $q->where('name', 'like', "%{$this->search}%")
    )->get()
); ?>

<div>
    <input wire:model.live.debounce.300ms="search" 
           placeholder="Search assets...">
    <!-- Results -->
</div>
```

---

### 3. **Performance Optimization** (Priority: HIGH)

**Scope**: Implement performance best practices

- [ ] Add `#[Computed]` to expensive database queries
- [ ] Implement `#[Lazy]` for dashboard widgets
- [ ] Add `wire:loading` states to all forms
- [ ] Optimize with `wire:model.debounce` on search inputs
- [ ] Implement pagination with `wire:key`

**Target Files**:

- Dashboard components (`app/Livewire/Dashboard*.php`)
- Report components (large data tables)
- Search/filter components

**Pattern Example**:

```php
#[Computed]
public function assets()
{
    return Asset::with('category', 'location')
        ->latest()
        ->paginate(20);
}
```

---

### 4. **Tailwind Component Library** (Priority: HIGH)

**Scope**: Create reusable Tailwind-based UI components

- [ ] **Toast Notifications** (`resources/views/components/toast.blade.php`)
  - Success, error, warning, info variants
  - Auto-dismiss functionality
  - Accessibility (ARIA live regions)

- [ ] **Modal Dialogs** (`resources/views/components/modal.blade.php`)
  - Focus trap with Alpine.js `x-trap`
  - Keyboard navigation (Escape to close)
  - Backdrop click-away

- [ ] **Dropdown Menus** (`resources/views/components/dropdown.blade.php`)
  - Keyboard navigation (Arrow keys, Enter, Escape)
  - Click-away behavior
  - Accessible ARIA attributes

- [ ] **Form Wizard** (`resources/views/components/wizard.blade.php`)
  - Multi-step forms with progress indicator
  - Validation per step
  - Keyboard navigation

**Component Template**:

```blade
{{-- resources/views/components/toast.blade.php --}}
@props(['type' => 'success', 'message'])

<div 
    x-data="{ show: true }"
    x-show="show"
    x-transition
    @click="show = false"
    role="alert"
    aria-live="polite"
    {{ $attributes->merge([
        'class' => 'fixed top-4 right-4 p-4 rounded-lg shadow-lg ' . 
                   match($type) {
                       'success' => 'bg-green-500 text-white',
                       'error' => 'bg-red-600 text-white',
                       'warning' => 'bg-orange-500 text-white',
                       default => 'bg-blue-500 text-white'
                   }
    ]) }}>
    {{ $message }}
</div>
```

---

### 5. **Alpine.js Integration Patterns** (Priority: MEDIUM)

**Scope**: Document and implement common Alpine.js patterns

- [ ] **Dropdown Pattern** (with click-away)

  ```blade
  <div x-data="{ open: false }" @click.away="open = false">
      <button @click="open = !open">Menu</button>
      <div x-show="open" x-transition><!-- Items --></div>
  </div>
  ```

- [ ] **Modal Pattern** (with focus trap)

  ```blade
  <div x-data="{ show: false }" @keydown.escape.window="show = false">
      <div x-show="show" x-trap="show"><!-- Modal content --></div>
  </div>
  ```

- [ ] **Accordion Pattern**

  ```blade
  <div x-data="{ open: false }">
      <button @click="open = !open">Toggle</button>
      <div x-show="open" x-collapse><!-- Content --></div>
  </div>
  ```

- [ ] **Tabs Pattern**

  ```blade
  <div x-data="{ tab: 'overview' }">
      <button @click="tab = 'overview'">Overview</button>
      <div x-show="tab === 'overview'"><!-- Panel --></div>
  </div>
  ```

**Location**: Create pattern library in `resources/views/components/alpine/`

---

### 6. **Accessibility Enhancements** (Priority: HIGH)

**Scope**: Ensure WCAG 2.2 Level AA compliance

- [ ] Add `aria-label` to icon-only buttons
- [ ] Implement focus management in modals (focus trap)
- [ ] Add `aria-live="polite"` to toast notifications
- [ ] Ensure all forms have associated labels
- [ ] Add `aria-describedby` for error messages
- [ ] Implement skip links: `<a href="#main-content" class="sr-only focus:not-sr-only">Skip to main content</a>`

**Checklist per Component**:

- ✅ Keyboard navigation (Tab, Enter, Escape, Arrow keys)
- ✅ Focus indicators (visible 3-4px outline)
- ✅ ARIA attributes (role, aria-label, aria-describedby)
- ✅ Color contrast ≥ 4.5:1 (use D14 approved palette)
- ✅ Screen reader announcements (ARIA live regions)

---

### 7. **Tailwind Configuration Optimization** (Priority: MEDIUM)

**Scope**: Optimize `tailwind.config.js` for performance

```js
// tailwind.config.js
export default {
    content: [
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
        './app/Filament/**/*.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                'motac-blue': '#0056b3',
                'motac-yellow': '#FFD700',
                'status-success': '#198754',
                'status-warning': '#ff8c00',
                'status-danger': '#b50c0c',
            },
        },
    },
    plugins: [],
}
```

---

## IMPLEMENTATION PHASES

### **Phase 1: Foundation (Week 1)**

1. Livewire v3 migration audit
2. Update wire directives and PHP attributes
3. Test all interactive components

### **Phase 2: Component Library (Week 2)**

1. Build Toast, Modal, Dropdown components
2. Implement Alpine.js patterns
3. Create accessibility checklist

### **Phase 3: Volt Migration (Week 3)**

1. Convert simple forms to Volt SFC
2. Build filter/search components
3. Implement performance optimizations

### **Phase 4: Polish & Testing (Week 4)**

1. Accessibility audits (Lighthouse, axe)
2. Performance testing (lazy loading, debouncing)
3. Cross-browser testing
4. User acceptance testing

---

## SUCCESS CRITERIA

- [ ] All Livewire components use v3 patterns (no v2 syntax)
- [ ] At least 5 Volt SFC components created and tested
- [ ] Component library includes Toast, Modal, Dropdown, Wizard
- [ ] All components pass WCAG 2.2 AA (Lighthouse score ≥ 90)
- [ ] Performance: Dashboard loads < 2s, forms respond < 200ms
- [ ] All tests pass (`php artisan test`, `npm run build`)

---

## TESTING REQUIREMENTS

**Per Component**:

```php
// tests/Feature/Livewire/ComponentNameTest.php
use Livewire\Livewire;

test('component loads correctly', function () {
    Livewire::test(ComponentName::class)
        ->assertStatus(200)
        ->assertSee('Expected content');
});

test('component handles interaction', function () {
    Livewire::test(ComponentName::class)
        ->set('property', 'value')
        ->call('method')
        ->assertSet('property', 'expected');
});
```

**Accessibility**:

- Run `npm run build` and verify no Tailwind purge issues
- Test with screen reader (NVDA/VoiceOver)
- Lighthouse accessibility score ≥ 90

---

## REFERENCE FILES

**Existing Standards**:

- `docs/D12_UI_UX_DESIGN_GUIDE.md` - Component specifications
- `docs/D14_UI_UX_STYLE_GUIDE.md` - Color palette (WCAG compliant)
- `AGENTS.md` - Laravel Boost guidelines

**Code Locations**:

- Components: `resources/views/components/`
- Livewire: `app/Livewire/`
- Volt: `resources/views/livewire/` or `resources/views/pages/`
- Filament: `app/Filament/Resources/`

---

## CONSTRAINTS & NOTES

- **NO** Bootstrap dependencies (use Tailwind only)
- **NO** jQuery (use Alpine.js + Livewire)
- **MAINTAIN** bilingual support (MS primary, EN secondary)
- **FOLLOW** D14 color palette for WCAG compliance
- **TEST** on Chrome, Firefox, Edge, Safari
- **VERIFY** mobile responsiveness (320px to 2xl breakpoints)

---

## KIRO EXECUTION CONTEXT

**Recommended Agent**: Claudette Coding Agent v5.2.1  
**Execution Mode**: Autonomous implementation with testing  
**Memory Reference**: `.agents/memory.instruction.md`  

**Pre-execution Checks**:

1. Read `AGENTS.md` for Laravel Boost guidelines
2. Review D12/D14 for UI/UX standards
3. Check existing Livewire components for patterns
4. Verify Tailwind config before starting

**Post-execution**:

1. Run full test suite: `php artisan test`
2. Build assets: `npm run build`
3. Lighthouse audit on key pages
4. Document component usage patterns

---

**Task Created**: 2025-11-06  
**Target Completion**: 2025-12-04 (4 weeks)  
**Owner**: Frontend Development Team  
**Reviewer**: UX + Accessibility Team
