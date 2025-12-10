# Navigation Redesign - Quick Reference Guide

**For Developers**: Fast lookup for common navigation patterns

---

## 1. Header Structure (All Pages)

### Desktop (≥1024px)

```blade
<header class="sticky top-0 z-50 bg-primary-500 dark:bg-primary-600 shadow-sm">
  <div class="max-w-7xl mx-auto px-6">
    <div class="flex items-center justify-between h-16">
      <!-- Logo + Brand -->
      <div class="flex items-center gap-4">
        <img src="{{ asset('images/jata-negara.png') }}" alt="Jata Negara" class="h-10">
        <span class="text-white font-heading font-semibold text-lg">ICTServe</span>
      </div>
      
      <!-- Navigation -->
      <nav class="hidden lg:flex items-center gap-6" aria-label="Navigasi Utama">
        <!-- Menu items here -->
      </nav>
      
      <!-- Actions -->
      <div class="flex items-center gap-4">
        <livewire:components.theme-toggle />
        <!-- Auth buttons or user menu -->
      </div>
    </div>
  </div>
</header>
```

### Mobile (<1024px)

```blade
<!-- Hamburger Button -->
<button @click="mobileMenuOpen = true" 
        class="lg:hidden p-2 text-white"
        aria-label="Buka menu"
        aria-expanded="false">
  <x-heroicon-o-bars-3 class="w-6 h-6" />
</button>

<!-- Mobile Menu (slide-in) -->
<div x-show="mobileMenuOpen" 
     x-trap.noscroll="mobileMenuOpen"
     class="fixed inset-0 z-50 lg:hidden">
  <!-- Overlay -->
  <div @click="mobileMenuOpen = false" 
       class="absolute inset-0 bg-black/50"></div>
  
  <!-- Menu Panel -->
  <div class="absolute right-0 top-0 h-full w-80 bg-white dark:bg-gray-800 shadow-dropdown">
    <!-- Close button -->
    <button @click="mobileMenuOpen = false" 
            class="absolute top-4 right-4 p-2"
            aria-label="Tutup menu">
      <x-heroicon-o-x-mark class="w-6 h-6" />
    </button>
    
    <!-- Menu items -->
    <nav class="mt-16 px-6 space-y-4">
      <!-- Vertical menu items -->
    </nav>
  </div>
</div>
```

---

## 2. Menu Items by Page Type

### Landing Page (Public)

```blade
<a href="{{ route('welcome') }}" 
   class="text-white hover:text-gray-100 transition-colors"
   @if(request()->routeIs('welcome')) aria-current="page" @endif>
  Laman Utama
</a>

<!-- Dropdown Example -->
<div x-data="{ open: false }" class="relative">
  <button @click="open = !open" 
          class="text-white hover:text-gray-100 flex items-center gap-1"
          aria-expanded="false"
          aria-haspopup="true">
    Perkhidmatan
    <x-heroicon-o-chevron-down class="w-4 h-4" />
  </button>
  
  <div x-show="open" 
       @click.away="open = false"
       class="absolute top-full mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-dropdown">
    <a href="{{ route('helpdesk.create') }}" 
       class="block px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700">
      Aduan ICT
    </a>
    <a href="{{ route('loans.create') }}" 
       class="block px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700">
      Pinjaman Aset
    </a>
  </div>
</div>
```

### Authenticated Portal

```blade
<!-- Role-based visibility -->
@can('view-approvals')
<a href="{{ route('approvals.index') }}" 
   class="text-gray-700 dark:text-gray-200 hover:text-primary-600">
  Kelulusan
</a>
@endcan

@can('access-admin-panel')
<a href="{{ route('filament.admin.pages.dashboard') }}" 
   class="text-gray-700 dark:text-gray-200 hover:text-primary-600">
  Panel Admin
</a>
@endcan
```

---

## 3. Theme Switcher

### Component Usage

```blade
<!-- In header -->
<livewire:components.theme-toggle />

<!-- Or use Blade component -->
<x-theme-toggle />
```

### FOUT Prevention Script (Add to `<head>`)

```blade
<script>
(function() {
  const theme = localStorage.getItem('theme') || 'light';
  if (theme === 'dark') {
    document.documentElement.classList.add('dark');
  }
})();
</script>
```

---

## 4. Responsive Classes

### Show/Hide by Breakpoint

```blade
<!-- Show on mobile only -->
<div class="lg:hidden">Mobile content</div>

<!-- Show on desktop only -->
<div class="hidden lg:block">Desktop content</div>

<!-- Show on tablet and up -->
<div class="hidden md:block">Tablet+ content</div>
```

### Grid System

```blade
<!-- 12-8-4 responsive grid -->
<div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-12 gap-4 md:gap-6">
  <!-- Content -->
</div>
```

---

## 5. Accessibility Patterns

### Focus Indicators (Global)

```css
/* Already in resources/css/accessibility.css */
*:focus-visible {
  outline: 3px solid theme('colors.primary.500');
  outline-offset: 2px;
}

.dark *:focus-visible {
  outline-color: white;
}
```

### ARIA Labels

```blade
<!-- Navigation -->
<nav aria-label="Navigasi Utama">...</nav>

<!-- Current page -->
<a href="..." aria-current="page">Laman Utama</a>

<!-- Dropdown -->
<button aria-expanded="false" aria-haspopup="true">Menu</button>

<!-- Hidden decorative icons -->
<x-heroicon-o-bars-3 class="w-6 h-6" aria-hidden="true" />
```

### Touch Targets

```blade
<!-- Minimum 44×44px -->
<button class="min-h-11 min-w-11 p-2">
  <x-heroicon-o-bars-3 class="w-6 h-6" />
</button>
```

---

## 6. Dark Mode Classes

### Common Patterns

```blade
<!-- Background -->
<div class="bg-white dark:bg-gray-900">

<!-- Text -->
<p class="text-gray-900 dark:text-gray-100">

<!-- Borders -->
<div class="border border-gray-300 dark:border-gray-700">

<!-- Hover states -->
<button class="hover:bg-gray-50 dark:hover:bg-gray-700">

<!-- Primary colors -->
<div class="bg-primary-500 dark:bg-primary-600">
```

### Transitions

```blade
<div class="transition-colors duration-200">
  <!-- Content -->
</div>
```

---

## 7. Common Components

### Breadcrumb

```blade
<nav aria-label="Breadcrumb" class="flex items-center gap-2 text-sm">
  <a href="{{ route('welcome') }}" class="text-white hover:underline">
    Laman Utama
  </a>
  <x-heroicon-o-chevron-right class="w-4 h-4 text-white" />
  <span class="text-white" aria-current="page">Current Page</span>
</nav>
```

### User Menu Dropdown

```blade
<div x-data="{ open: false }" class="relative">
  <button @click="open = !open" 
          class="flex items-center gap-2 min-h-11"
          aria-expanded="false"
          aria-haspopup="true">
    <span>{{ Auth::user()->name }}</span>
    <x-heroicon-o-chevron-down class="w-4 h-4" />
  </button>
  
  <div x-show="open" 
       @click.away="open = false"
       class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-dropdown">
    <a href="{{ route('profile.edit') }}" class="block px-4 py-2">Profil</a>
    <a href="{{ route('settings') }}" class="block px-4 py-2">Tetapan</a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="block w-full text-left px-4 py-2">
        Log Keluar
      </button>
    </form>
  </div>
</div>
```

---

## 8. Testing Checklist

### Quick Tests

```bash
# Keyboard navigation
- Tab through all interactive elements
- Enter/Space to activate
- Escape to close menus

# Screen reader
- NVDA: NVDA+Down to read
- Verify ARIA labels announced

# Touch targets
- Inspect element, verify min-h-11 min-w-11

# Color contrast
- Run axe DevTools
- Check for violations

# Responsive
- Chrome DevTools responsive mode
- Test 375px, 768px, 1024px, 1440px
```

---

## 9. Common Issues & Fixes

### Issue: Menu not closing on mobile

```blade
<!-- Add @click.away -->
<div x-show="open" @click.away="open = false">
```

### Issue: Focus trap not working

```blade
<!-- Add x-trap.noscroll -->
<div x-show="open" x-trap.noscroll="open">
```

### Issue: Dark mode flash on load

```blade
<!-- Add FOUT prevention script in `<head>` BEFORE any CSS -->
<script>
(function() {
  const theme = localStorage.getItem('theme') || 'light';
  if (theme === 'dark') document.documentElement.classList.add('dark');
})();
</script>
```

### Issue: Touch targets too small

```blade
<!-- Add min-h-11 min-w-11 (44×44px) -->
<button class="min-h-11 min-w-11 p-2">
```

---

## 10. File Locations

### Layouts

- `resources/views/layouts/landing.blade.php` - Public pages
- `resources/views/layouts/guest.blade.php` - Auth pages
- `resources/views/layouts/app.blade.php` - Authenticated portal
- `resources/views/layouts/front.blade.php` - Public info pages

### Components

- `resources/views/components/navigation/` - Navigation components
- `resources/views/livewire/components/theme-toggle.blade.php` - Theme switcher
- `resources/views/components/accessibility/` - Accessibility components

### Styles

- `resources/css/app.css` - Main styles with @theme
- `resources/css/accessibility.css` - Focus indicators, ARIA styles

### Documentation

- `.kiro/specs/figma-ui-redesign/NAVIGATION-LAYOUT-REDESIGN.md` - Full specs
- `docs/frontend/00-PREPLANNING-ictserve-3.6.0.md` - v3.6.0 planning
- D12-D15 - UI/UX documentation

---

**Quick Start**: Copy patterns from this guide, test with checklist, refer to full specs for details.
