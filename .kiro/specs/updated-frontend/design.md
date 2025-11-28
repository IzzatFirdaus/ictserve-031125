# Updated Frontend - Design Document

## Introduction

This design document outlines the comprehensive architecture and implementation strategy for the major frontend UI/UX upgrade of the ICTServe system. The design combines frontend modernization with complete page redesign, leveraging Laravel 12.x, Livewire 3.x, Volt 1, Tailwind CSS 4.1, and Alpine.js 3.x to deliver a unified, accessible, and high-performance frontend experience across guest forms, authenticated portal, and admin interfaces.

## Architecture Overview

### System Architecture

The ICTServe frontend implements a three-tier hybrid architecture:

1. **Guest Layer**: Public forms without authentication (helpdesk tickets, asset loan applications)
2. **Portal Layer**: Authenticated staff interface (dashboard, submissions, approvals)
3. **Admin Layer**: Filament 4 administrative panel (system management, reporting)

### Technology Stack

- **Backend Framework**: Laravel 12.x with PHP 8.3+
- **UI Framework**: Livewire 3.x for server-driven reactive components
- **Single-File Components**: Volt 1 for simplified component development
- **CSS Framework**: Tailwind CSS 4.1 with JIT mode
- **JavaScript**: Alpine.js 3.x for client-side interactivity
- **Admin Panel**: Filament 4 for administrative interfaces

### Architectural Principles

1. **Mobile-First Responsive Design**: All interfaces optimized for 320px-1920px viewports
2. **WCAG 2.2 AA Compliance**: Full accessibility with 4.5:1 text contrast, 44×44px touch targets
3. **Performance Optimization**: Core Web Vitals targets (LCP <2.5s, FID <100ms, CLS <0.1)
4. **Component Reusability**: Unified component library across all layers
5. **Progressive Enhancement**: Base functionality without JavaScript, enhanced with Livewire/Alpine

## Component Library Design

### Component Organization

Components are organized into 8 categories:

1. **accessibility/**: Skip links, language switcher, ARIA live regions, focus trap
2. **data/**: Tables, pagination, search filters, statistics cards
3. **form/**: Input, select, textarea, checkbox, radio, file upload, validation
4. **layout/**: Guest layout, portal layout, header, footer, sidebar
5. **navigation/**: Main nav, breadcrumb, tab navigation, mobile menu
6. **responsive/**: Mobile cards, tablet grids, desktop layouts
7. **ui/**: Button, card, modal, alert, badge, dropdown, toast, spinner
8. **alpine/**: Dropdown pattern, modal pattern, accordion, tabs

### Component Metadata Standard

Each component includes standardized metadata for traceability and compliance tracking.

### Portal-Specific Components (NEW)

**User Info Card Component** (`x-ui.user-info-card`):

```blade
<div class="bg-teal-50 border-l-4 border-teal-500 p-4 rounded-lg">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-teal-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm font-medium text-teal-800">{{ __('portal.verified_user_info') }}</p>
            <div class="mt-2 text-sm text-teal-700 space-y-1">
                <p><strong>{{ __('portal.name') }}:</strong> {{ $user->name }}</p>
                <p><strong>{{ __('portal.grade') }}:</strong> {{ $user->grade }}</p>
                <p><strong>{{ __('portal.department') }}:</strong> {{ $user->department }}</p>
            </div>
        </div>
    </div>
</div>
```

**Purpose**: Standardize user info display across Helpdesk and Asset Loan forms (green/teal card style distinguishes verified system data from editable form data).

**Dynamic Stats Card Component** (`x-ui.stats-card`):

```blade
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $count }}</p>
        </div>
        <div class="flex-shrink-0">
            @if($count == 0)
                <div class="p-3 rounded-full {{ $type === 'danger' ? 'bg-gray-100' : 'bg-green-100' }}">
                    <svg class="h-8 w-8 {{ $type === 'danger' ? 'text-gray-500' : 'text-green-500' }}" fill="currentColor" viewBox="0 0 20 20">
                        {{ $icon }}
                    </svg>
                </div>
            @else
                <div class="p-3 rounded-full bg-{{ $type }}-100">
                    <svg class="h-8 w-8 text-{{ $type }}-500" fill="currentColor" viewBox="0 0 20 20">
                        {{ $icon }}
                    </svg>
                </div>
            @endif
        </div>
    </div>
</div>
```

**Purpose**: Dashboard statistics with conditional styling (green/neutral for 0 count, red for >0 on danger-type cards like "Overdue").

## Tailwind CSS 4.1 Design System

### WCAG-Compliant Color Palette

#### Primary Colors

- Primary 500: #0056b3 (6.8:1 contrast ratio)
- Primary 600: #004494
- Primary 900: #002147

#### Status Colors

- Success 500: #198754 (4.9:1 contrast)
- Warning 500: #ff8c00 (4.5:1 contrast)
- Danger 500: #b50c0c (8.2:1 contrast)

#### Neutral Colors

- Gray 50-900 scale for backgrounds and text

### Typography System

- Font Family: Inter (sans-serif), JetBrains Mono (monospace)
- Scale: xs (0.75rem) to 3xl (1.875rem)
- Line Heights: Optimized for readability (1.5 for body text)

### Responsive Breakpoints

- sm: 640px (mobile landscape)
- md: 768px (tablet portrait)
- lg: 1024px (tablet landscape)
- xl: 1280px (desktop)
- 2xl: 1536px (large desktop)

### Configuration

Tailwind CSS 4.1 configured with:

- **Lightning CSS** engine for instant compilation
- **@theme CSS variables** for MOTAC branding (CSS-first configuration)
- Content scanning: resources/views/**/\*.blade.php, app/Livewire/**/\*.php, app/Filament/\*\*/\_.php
- Production optimization: <50KB gzipped
- **HMR benchmarking** required during setup to validate Vite integration

**MOTAC Theme Variables** (app.css):

```css
@theme {
    --color-primary-500: #0056b3;
    --color-primary-600: #004494;
    --color-primary-900: #002147;
    --color-success-500: #198754;
    --color-warning-500: #ff8c00;
    --color-danger-500: #b50c0c;
    --font-sans: "Inter", sans-serif;
    --font-mono: "JetBrains Mono", monospace;
}
```

**Risk Mitigation**: Early benchmarking of build times and HMR performance required to validate Tailwind 4.0 + Vite integration before full component development.

## Livewire 3.x Architecture

### OptimizedLivewireComponent Trait

A performance-focused trait providing:

- **Caching**: 5-minute default cache for computed properties
- **Lazy Loading**: Deferred component rendering with placeholders
- **Query Optimization**: Eager loading patterns to prevent N+1 queries
- **Computed Properties**: Cached expensive operations

### Component Patterns

#### PHP 8 Attributes

- `#[Reactive]`: Real-time reactive properties
- `#[Computed]`: Cached computed properties
- `#[Lazy]`: Lazy-loaded components
- `#[Locked]`: Immutable properties
- `#[Session]`: Session-persisted properties

#### Wire Directives

- `wire:model.live`: Real-time updates
- `wire:model.lazy`: Deferred updates for large text fields
- `wire:model.live.debounce.300ms`: Debounced search inputs
- `wire:loading`: Loading state indicators
- `wire:key`: Optimal DOM diffing for lists

### Event Handling

- Use `$this->dispatch()` for component events
- Event listeners with `on()` function in Volt
- Browser events with `@notify.window` Alpine directive

## Volt 1 Component Design

### Functional API

Volt 1 provides a simplified single-file component API:

#### State Management

```php
state(['search' => '', 'category' => '', 'status' => '']);
```

#### Computed Properties

```php
computed('filteredTickets', function () {
    return Ticket::query()
        ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
        ->paginate(10);
});
```

#### Event Listeners

```php
on(['ticket-updated' => function () {
    unset($this->filteredTickets);
}]);
```

### Usage Guidelines

**State Complexity Governance** (replaces "lines of code" metric):

**Use Volt for**:

- Presentational components (read-only data display)
- Simple forms with basic validation
- Search and filter interfaces
- Read-only data feeds and lists
- Components with minimal lifecycle requirements

**Use Class-based Livewire for**:

- Complex lifecycle hooks (mount, hydrate, dehydrate)
- Extensive trait usage beyond OptimizedLivewireComponent
- Complex authorization logic with multiple policies
- Components requiring advanced state management
- Multi-step wizards with complex validation

**File Organization**:

- Place in: resources/views/livewire/
- Naming: kebab-case (e.g., search-filter.blade.php)
- Refactor Volt → Class when state complexity increases

## Alpine.js 3.x Patterns

### Core Directives

- `x-data`: Component state initialization
- `x-show`: Conditional visibility with transitions
- `x-transition`: Smooth enter/leave animations
- `x-trap`: Focus trapping for modals
- `@click.away`: Click outside detection
- `@keydown.escape`: Keyboard navigation
- `@keydown.window`: Global keyboard shortcuts

### Keyboard Shortcuts Manager (NEW)

**Global Hotkey Implementation**:

```blade
<div x-data="keyboardShortcuts()" @keydown.window="handleShortcut($event)">
    <!-- Portal content -->
</div>

<script>
function keyboardShortcuts() {
    return {
        shortcuts: {
            'Alt+N': { action: 'newTicket', label: 'New Ticket' },
            'Alt+D': { action: 'dashboard', label: 'Dashboard' },
            'Alt+H': { action: 'help', label: 'Help' },
            'Alt+L': { action: 'newLoan', label: 'New Loan Application' },
            '?': { action: 'showShortcuts', label: 'Show Shortcuts' }
        },

        handleShortcut(event) {
            const key = this.getKeyCombo(event);
            const shortcut = this.shortcuts[key];

            if (shortcut) {
                event.preventDefault();
                this.executeAction(shortcut.action);
            }
        },

        getKeyCombo(event) {
            let combo = '';
            if (event.altKey) combo += 'Alt+';
            if (event.ctrlKey) combo += 'Ctrl+';
            if (event.shiftKey) combo += 'Shift+';
            combo += event.key.toUpperCase();
            return combo;
        },

        executeAction(action) {
            switch(action) {
                case 'newTicket':
                    window.location.href = '/portal/helpdesk/create';
                    break;
                case 'dashboard':
                    window.location.href = '/portal/dashboard';
                    break;
                case 'newLoan':
                    window.location.href = '/portal/loans/create';
                    break;
                case 'showShortcuts':
                    this.$dispatch('show-shortcuts-modal');
                    break;
            }
        }
    }
}
</script>
```

**Shortcuts Help Modal**:

```blade
<x-ui.modal wire:model="showShortcutsModal" x-on:show-shortcuts-modal.window="$wire.showShortcutsModal = true">
    <x-slot:title>{{ __('portal.keyboard_shortcuts') }}</x-slot:title>

    <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <span class="text-sm text-gray-700">{{ __('portal.new_ticket') }}</span>
                <kbd class="px-2 py-1 text-xs font-semibold text-gray-800 bg-white border border-gray-200 rounded">Alt+N</kbd>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <span class="text-sm text-gray-700">{{ __('portal.dashboard') }}</span>
                <kbd class="px-2 py-1 text-xs font-semibold text-gray-800 bg-white border border-gray-200 rounded">Alt+D</kbd>
            </div>
            <!-- More shortcuts -->
        </div>
    </div>
</x-ui.modal>
```

**Accessibility Considerations**:

- Shortcuts use Alt key (not Ctrl) to avoid conflicts with browser shortcuts
- Help modal triggered by ? key (universal convention)
- Screen reader users can access all functions via regular navigation
- Shortcuts are optional enhancement, not required for functionality

### Common Patterns

#### Dropdown Pattern

- Toggle state with `x-data="{ open: false }"`
- Keyboard navigation (Escape to close)
- ARIA attributes for accessibility
- Click-away detection

#### Modal Pattern

- Backdrop with opacity transitions
- Focus trap for keyboard users
- Escape key to close
- Entangle with Livewire for state sync

#### Accordion Pattern

- Expandable sections with smooth transitions
- ARIA expanded/collapsed states
- Keyboard navigation support

### Integration with Livewire

- Use `@entangle()` for two-way state binding
- Minimize Alpine usage in favor of server-driven Livewire
- Alpine for UI interactions, Livewire for data operations

## Contact Form and Service Request Integration

### Contact Form Routing

**Problem**: Separate "Contact Us" forms often send emails to ignored inboxes, while Helpdesk tickets are tracked.

**Solution**: Route Contact form submissions to Helpdesk module as tracked tickets.

**Implementation**:

```php
// ContactController.php
public function submit(ContactFormRequest $request): RedirectResponse
{
    // Create Helpdesk ticket with "General Enquiry" category
    $ticket = HelpdeskTicket::create([
        'category' => 'General Enquiry',
        'title' => $request->subject,
        'description' => $request->message,
        'contact_name' => $request->name,
        'contact_email' => $request->email,
        'contact_phone' => $request->phone,
        'source' => 'contact_form',
    ]);

    // Return ticket ID for tracking
    return redirect()->route('contact.success')
        ->with('ticket_id', $ticket->ticket_number)
        ->with('message', __('contact.ticket_created'));
}
```

**User Feedback**:

```blade
<div class="alert alert-success">
    <p>{{ __('contact.thank_you') }}</p>
    <p>{{ __('contact.ticket_number') }}: <strong>{{ session('ticket_id') }}</strong></p>
    <p>{{ __('contact.track_message') }}</p>
</div>
```

### Service Request Routing

**Problem**: "Permintaan Perkhidmatan" card on Services page needs clear routing logic.

**Solution**: Route to Helpdesk form with pre-filled "Service Request" category.

**Implementation**:

```blade
<!-- Services page card -->
<a href="{{ route('helpdesk.create', ['category' => 'service_request']) }}"
   class="service-card">
    <h3>{{ __('services.service_request') }}</h3>
    <p>{{ __('services.service_request_desc') }}</p>
</a>
```

```php
// HelpdeskController.php
public function create(Request $request): View
{
    $prefilledCategory = $request->query('category');

    return view('helpdesk.create', [
        'category' => $prefilledCategory,
    ]);
}
```

**Benefits**:

- No dead-end landing pages
- All requests tracked as Helpdesk tickets
- Unified workflow for all support requests

## Page Layout Design

### Guest Layout

#### Structure

- Header: MOTAC branding, language switcher
- Main content: Centered max-width container
- Footer: Copyright, links, contact info (WCAG 4.5:1 contrast verified)
- Skip links for accessibility

#### Features

- No authentication required
- Bilingual support (Bahasa Melayu/English)
- WCAG 2.2 AA compliant
- Mobile-first responsive design

**Footer Contrast Fix**: Ensure footer text meets 4.5:1 contrast ratio (current implementation may be low contrast on dark blue background).

### Unified Login Layout

**Problem**: Current implementation has fragmented login screens (Admin vs Staff) with inconsistent styling and missing language toggle.

**Solution**: Single unified login layout with role detection after authentication.

**Implementation**:

```blade
<!-- resources/views/auth/login.blade.php -->
<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full space-y-8">
        <!-- Language Switcher (CRITICAL: Must be visible) -->
        <div class="flex justify-end">
            <x-accessibility.language-switcher />
        </div>

        <!-- Logo and Title -->
        <div class="text-center">
            <img src="{{ asset('images/motac-logo.png') }}" alt="MOTAC" class="mx-auto h-16">
            <h2 class="mt-6 text-3xl font-bold text-gray-900">
                {{ __('auth.login_title') }}
            </h2>
        </div>

        <!-- Login Form (consistent styling) -->
        <form wire:submit="login" class="mt-8 space-y-6">
            <x-form.input
                wire:model="email"
                type="email"
                :label="__('auth.email')"
                required
            />

            <x-form.input
                wire:model="password"
                type="password"
                :label="__('auth.password')"
                required
            />

            <x-ui.button type="primary" class="w-full">
                {{ __('auth.login') }}
            </x-ui.button>
        </form>
    </div>
</div>
```

**Role-Based Redirect** (after authentication):

```php
// LoginController.php
protected function authenticated(Request $request, $user): RedirectResponse
{
    // Detect role and redirect accordingly
    if ($user->hasRole('admin') || $user->hasRole('superuser')) {
        return redirect()->route('filament.admin.pages.dashboard');
    }

    return redirect()->route('portal.dashboard');
}
```

**Benefits**:

- Single entry point (no confusion)
- Consistent styling across all roles
- Bilingual support visible on login screen
- Role detection happens server-side after authentication

### Portal Layout

#### Structure

- Sidebar: Navigation menu (collapsible on mobile)
- Header: User profile, notifications, logout
- Main content: Breadcrumb + page content
- Footer: Minimal footer with version info

#### Features

- Role-based navigation (Staff, Approver, Admin)
- Personalized dashboard
- Real-time notifications
- Responsive sidebar (hamburger on mobile)

### Admin Layout (Filament 4)

#### Structure

- Filament's default admin panel layout
- Customized with MOTAC branding
- Integrated with ICTServe theme

#### Features

- Full CRUD interfaces for all models
- Advanced filtering and search
- Bulk operations
- Export functionality

## ISO Compliance and Legacy Business Logic

### Government Document Standards

**ISO Document IDs** (displayed in top-right corner of forms):

- **Helpdesk Form**: `PK.(S).MOTAC.07.(L1)`
- **Asset Loan Form**: `PK.(S).MOTAC.07.(L3)`

**Implementation**:

- Fixed position header component
- Bilingual display (Malay/English)
- Non-intrusive styling (gray text, small font)
- Visible on all form pages

### Helpdesk Form Compliance

**ISO Header Component** (must match Asset Loan form style):

```blade
<div class="fixed top-4 right-4 text-gray-500 text-sm z-50">
    <span class="font-mono">{{ __('helpdesk.iso_document_id') }}</span>
    <span class="ml-2 font-semibold">PK.(S).MOTAC.07.(L1)</span>
</div>
```

**CRITICAL FIX**: Current implementation is missing the ISO header. This must be added to match the Asset Loan form.

**Mandatory "Perakuan" Gate**:

```blade
<x-form.checkbox
    wire:model.live="perakuan_accepted"
    required
    :label="__('helpdesk.perakuan_text_exact')"
/>

<x-ui.button
    wire:click="submit"
    :disabled="!$perakuan_accepted"
    type="primary">
    {{ __('helpdesk.submit') }}
</x-ui.button>
```

**Perakuan Text** (Exact Legacy Text - Malay):

> "Saya memperakui dan mengesahkan bahawa semua maklumat yang diberikan di dalam eBorang Laporan Kerosakan ini adalah benar dan tepat. Saya faham bahawa sebarang maklumat palsu boleh mengakibatkan tindakan tatatertib."

**CRITICAL FIX**: Current implementation uses generic text ("maklumat yang diberikan adalah benar dan tepat"). Must use exact legacy text including "eBorang Laporan Kerosakan" reference.

**Searchable Division Select**:

- Virtual scrolled combobox for large "Bahagian" list (100+ divisions)
- Real-time search with debouncing (300ms)
- Keyboard navigation support
- ARIA combobox pattern
- **CRITICAL**: Must NOT be native HTML `<select>` - use Livewire/Alpine combobox component

### Asset Loan Form Compliance

**Terms & Conditions Accordion** (11 specific rules from PK.(S).MOTAC.07.(L3)):

1. Peminjam bertanggungjawab sepenuhnya terhadap aset yang dipinjam
2. Aset mesti dikembalikan dalam keadaan baik
3. Sebarang kerosakan akan dikenakan caj penggantian
4. Peminjam mesti hadir sendiri untuk mengambil aset
5. Aset tidak boleh dipinjamkan kepada pihak ketiga
6. Tempoh pinjaman maksimum adalah 14 hari
7. Peminjaman tertakluk kepada kelulusan Pegawai Bertanggungjawab
8. Aset mesti dikembalikan pada tarikh yang ditetapkan
9. Kelewatan pengembalian akan dikenakan denda
10. Peminjam mesti mematuhi SOP penggunaan aset
11. BPM MOTAC berhak membatalkan pinjaman tanpa notis

**"On Behalf" Toggle**:

```php
// Database schema
Schema::table('loan_applications', function (Blueprint $table) {
    $table->json('responsible_officer_details')->nullable();
    $table->boolean('is_delegate')->default(false);
});

// Livewire component
public bool $is_delegate = false;
public array $responsible_officer = [];

public function updatedIsDelegate(): void
{
    if (!$this->is_delegate) {
        $this->responsible_officer = [];
    }
}
```

**WorkingDayCalculator Service**:

```php
class WorkingDayCalculator
{
    public function calculateMinimumPickupDate(Carbon $requestDate): Carbon
    {
        $pickupDate = $requestDate->copy()->addDays(3);

        // Skip weekends
        while ($pickupDate->isWeekend()) {
            $pickupDate->addDay();
        }

        // Skip Malaysian public holidays
        while ($this->isPublicHoliday($pickupDate)) {
            $pickupDate->addDay();

            // Re-check for weekends after skipping holiday
            while ($pickupDate->isWeekend()) {
                $pickupDate->addDay();
            }
        }

        return $pickupDate;
    }

    private function isPublicHoliday(Carbon $date): bool
    {
        // Check against Malaysian public holidays table
        return PublicHoliday::whereDate('date', $date)->exists();
    }
}
```

### Digital Handshake (OTP Verification)

**OTP Generation** (on loan approval):

```php
// Generate 4-digit OTP
$otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

// Store in database with expiration
$loanApplication->update([
    'pickup_otp' => Hash::make($otp),
    'pickup_otp_expires_at' => now()->addHours(24),
]);

// Send via email
Mail::to($loanApplication->user)->send(new LoanApprovedMail($loanApplication, $otp));
```

**OTP Verification Modal** (admin interface):

```blade
<x-ui.modal wire:model="showOtpModal">
    <x-slot:title>{{ __('loan.verify_pickup') }}</x-slot:title>

    <x-form.input
        wire:model.live="otp_input"
        type="text"
        maxlength="4"
        pattern="[0-9]{4}"
        :label="__('loan.enter_otp')"
        required
    />

    <x-slot:footer>
        <x-ui.button wire:click="verifyOtp" type="primary">
            {{ __('loan.verify_and_issue') }}
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>
```

## Form Design Patterns

### Accessible Form Components

All form components include:

- Proper label associations
- ARIA attributes for screen readers
- Visible focus indicators (3px outline, 2px offset)
- Inline validation with error messages
- Help text for complex fields
- Required field indicators

### Multi-Step Form Wizard

#### Features

- Progress indicator with percentage
- Per-step validation
- Keyboard navigation (Tab, Enter, Escape)
- Review step before submission
- Back/Next navigation
- Mobile-optimized layout

#### Implementation

- Livewire for state management
- Alpine.js for transitions
- WCAG 2.2 AA compliant
- Real-time validation with debouncing

### Form Validation

- Client-side: Real-time with wire:model.live.debounce
- Server-side: Laravel Form Requests
- Inline error messages with ARIA
- Success notifications
- Accessibility announcements via ARIA live regions

## Performance Optimization Design

### Caching Strategy

#### Dashboard Statistics

- 5-minute cache for user-specific stats
- Redis-based caching
- Cache invalidation on data updates

#### Asset Availability

- 5-minute cache for availability calendar
- Eager loading of relationships
- Optimized queries with indexes

#### Component Data

- Computed properties with caching
- Lazy loading for heavy components
- Placeholder views during loading

### Image Optimization

- WebP format with JPEG fallbacks
- Lazy loading with `loading="lazy"`
- Explicit dimensions to prevent CLS
- `fetchpriority` for above-the-fold images
- Responsive images with srcset

### Code Optimization

- Tailwind CSS purging (<50KB gzipped)
- Vite code splitting
- Livewire asset optimization
- Alpine.js included with Livewire (no separate bundle)

### Database Optimization

- Eager loading to prevent N+1 queries
- Database indexes on frequently queried columns
- Query result caching
- Pagination for large datasets

## Accessibility Implementation

### WCAG 2.2 AA Compliance

#### Color Contrast

- Text: 4.5:1 minimum contrast ratio
- UI components: 3:1 minimum contrast ratio
- Focus indicators: 3:1 contrast against background

#### Touch Targets

- Minimum 44×44px for all interactive elements
- Adequate spacing between targets
- Mobile-optimized tap areas

#### Keyboard Navigation

- All functionality accessible via keyboard
- Visible focus indicators (3-4px outline, 2px offset)
- Logical tab order
- Skip links to main content

### ARIA Implementation

#### Landmarks

- `<nav>` for navigation
- `<main>` for main content
- `<aside>` for complementary content
- `<footer>` for footer

#### Live Regions

- Polite announcements for status updates
- Assertive announcements for errors
- Atomic updates for complete messages

#### Dynamic Content

- ARIA live regions for notifications
- Status messages announced to screen readers
- Loading states with ARIA busy

### Screen Reader Support

- Semantic HTML5 elements
- Proper heading hierarchy (h1-h6)
- Alt text for all images
- Form labels and descriptions
- Table headers and captions

## Responsive Design Strategy

### Mobile-First Approach

Design progression:

1. **Mobile (320px-639px)**: Single column, stacked layout
2. **Tablet (640px-1023px)**: Two-column grid, collapsible sidebar
3. **Desktop (1024px+)**: Multi-column layout, persistent sidebar

### Breakpoint Strategy

- **sm (640px)**: Mobile landscape, small tablets
- **md (768px)**: Tablet portrait
- **lg (1024px)**: Tablet landscape, small desktop
- **xl (1280px)**: Desktop
- **2xl (1536px)**: Large desktop

### Responsive Patterns

#### Navigation

- Mobile: Hamburger menu
- Tablet: Collapsible sidebar
- Desktop: Persistent sidebar

#### Forms

- Mobile: Single column, full-width inputs
- Tablet: Two-column grid for related fields
- Desktop: Multi-column layout with optimal field widths

#### Tables

- Mobile: Card-based layout
- Tablet: Horizontal scroll with sticky columns
- Desktop: Full table with all columns visible

#### Dashboards

- Mobile: Stacked widgets
- Tablet: 2-column grid
- Desktop: 3-4 column grid

## Bilingual Support Design

### Language Switching

#### Implementation

- Language switcher in header (guest and portal)
- 44×44px touch targets for accessibility
- Keyboard navigation support
- ARIA labels for screen readers

#### Persistence Strategy (Multi-layered)

1. **URL-based locale** (primary): `/ms/ticket/...` or `/en/ticket/...`
   - Ensures shareable links preserve language
   - Improves SEO with language-specific URLs
   - Resilient to session/cookie volatility (in-app browsers, incognito mode)
2. **Session storage** (secondary): User preference within session
3. **Cookie storage** (tertiary): 1-year expiration, fallback
4. **No database storage** (per requirements)

#### Detection Priority

1. URL locale segment (e.g., `/ms/` or `/en/`)
2. Session value
3. Cookie value
4. Accept-Language header
5. Config default (Bahasa Melayu)

**Risk Mitigation**: URL-based locale prevents language loss in volatile session environments (WhatsApp/Telegram in-app browsers, incognito mode).

### Translation Management

#### Laravel Localization

- Language files: resources/lang/en/ and resources/lang/ms/
- Translation keys: `__('key')` in Blade templates
- Pluralization support
- Parameter replacement

#### Content Translation

- All UI text translated
- Email templates bilingual
- Error messages localized
- Validation messages translated

### RTL Support

Not required for Bahasa Melayu and English (both LTR languages), but architecture supports future RTL languages if needed.

## Event-Driven Architecture

### Decoupled Module Integration

**Philosophy**: Modules communicate via Laravel Events & Listeners, not direct coupling.

**Asset-Ticket Linking Pattern**:

```php
// Asset Module fires event (no knowledge of Ticket Module)
event(new AssetReturned($asset, $condition));

// Ticket Module listens for event
class CreateDamageTicketListener
{
    public function handle(AssetReturned $event): void
    {
        if ($event->condition === 'damaged') {
            // Create ticket within 5 seconds (Requirement 11.4)
            CreateHelpdeskTicketJob::dispatch($event->asset)
                ->delay(now());
        }
    }
}
```

**Benefits**:

- **Decoupling**: Asset module doesn't know how to create tickets
- **Testability**: Test event firing and listening independently
- **Maintainability**: Change ticket creation logic without touching asset module
- **Scalability**: Add new listeners (e.g., email notifications) without modifying existing code

### Optimistic UI Pattern

**Philosophy**: Immediate UI feedback while server processes request.

**Email-Based Approval Workflow** (Requirement 12):

```php
// Alpine.js optimistic state
<div x-data="{ approved: false, processing: false }">
    <button
        @click="approved = true; processing = true; $wire.approve()"
        x-show="!approved"
        :disabled="processing">
        Approve
    </button>

    <div x-show="approved" x-transition>
        ✓ Approved (processing...)
    </div>
</div>

// Livewire rollback on failure
public function approve()
{
    try {
        // Process approval (may take up to 60 seconds)
        $this->processApproval();
    } catch (\Exception $e) {
        // Rollback optimistic state
        $this->dispatch('approval-failed');
        throw $e;
    }
}
```

**Benefits**:

- **Perceived Performance**: Users see immediate feedback
- **Error Handling**: Graceful rollback on server failure
- **User Experience**: No 60-second wait for email workflows

## Profile Data Management

### Read-Only Profile Fields

**Problem**: Profile fields (Email, Staff ID, Grade, Department) are read-only but users need correction mechanism.

**Data Source**: Populated from User seeder/Admin input (not editable by staff).

**Implementation**:

```blade
<!-- Profile page -->
<div class="space-y-6">
    <x-form.input
        wire:model="name"
        :label="__('profile.name')"
        required
    />

    <!-- Read-only fields with correction link -->
    <div class="relative">
        <x-form.input
            wire:model="email"
            :label="__('profile.email')"
            readonly
            disabled
        />
        <div class="mt-1 flex items-center text-sm text-gray-500">
            <span>{{ __('profile.read_only_field') }}</span>
            <button
                wire:click="requestCorrection('email')"
                class="ml-2 text-primary-600 hover:text-primary-800 underline">
                {{ __('profile.request_correction') }}
            </button>
        </div>
    </div>

    <!-- Similar for Staff ID, Grade, Department -->
</div>
```

**Correction Workflow**:

```php
public function requestCorrection(string $field): void
{
    // Create Helpdesk ticket with "Profile Data Correction" category
    $ticket = HelpdeskTicket::create([
        'category' => 'Profile Data Correction',
        'title' => __('profile.correction_request_title', ['field' => $field]),
        'description' => __('profile.correction_request_desc', [
            'field' => $field,
            'current_value' => $this->user->{$field},
        ]),
        'user_id' => $this->user->id,
        'priority' => 'normal',
    ]);

    $this->dispatch('notify', [
        'type' => 'success',
        'message' => __('profile.correction_ticket_created', ['ticket_id' => $ticket->ticket_number])
    ]);
}
```

**Benefits**:

- Clear data ownership (Admin controls authoritative data)
- User-friendly correction mechanism
- Audit trail for all profile changes
- Prevents unauthorized grade/department changes

## Ticket Claiming Workflow

### "Tuntut Penyerahan" (Claim Submission)

**Problem**: Staff submit tickets as guests (before login, or from mobile without auth), need to link to their account.

**Security Requirement**: Email verification to prevent unauthorized claiming.

**Implementation**:

```php
// Step 1: Display claimable tickets count on dashboard
public function getClaimableTicketsProperty(): int
{
    return HelpdeskTicket::query()
        ->whereNull('user_id') // Guest tickets
        ->where('contact_email', $this->user->email)
        ->count();
}

// Step 2: Show claimable tickets list
public function showClaimableTickets(): void
{
    $this->claimableTickets = HelpdeskTicket::query()
        ->whereNull('user_id')
        ->where('contact_email', $this->user->email)
        ->with(['category', 'status'])
        ->get();

    $this->showClaimModal = true;
}

// Step 3: Send OTP for verification
public function initiateClaim(array $ticketIds): void
{
    // Generate 6-digit OTP
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // Store in session with expiration
    session([
        'claim_otp' => Hash::make($otp),
        'claim_otp_expires' => now()->addMinutes(10),
        'claim_ticket_ids' => $ticketIds,
    ]);

    // Send OTP via email
    Mail::to($this->user->email)->send(new ClaimVerificationMail($otp));

    $this->showOtpVerification = true;
}

// Step 4: Verify OTP and link tickets
public function verifyClaim(string $otp): void
{
    if (!session()->has('claim_otp') || now()->gt(session('claim_otp_expires'))) {
        $this->addError('otp', __('portal.otp_expired'));
        return;
    }

    if (!Hash::check($otp, session('claim_otp'))) {
        $this->addError('otp', __('portal.otp_invalid'));
        return;
    }

    // Link tickets to user account
    HelpdeskTicket::whereIn('id', session('claim_ticket_ids'))
        ->update(['user_id' => $this->user->id]);

    session()->forget(['claim_otp', 'claim_otp_expires', 'claim_ticket_ids']);

    $this->dispatch('notify', [
        'type' => 'success',
        'message' => __('portal.tickets_claimed_successfully')
    ]);
}
```

**UI Flow**:

```blade
<!-- Dashboard: Show claimable count -->
<x-ui.stats-card
    :title="__('portal.claimable_submissions')"
    :count="$this->claimableTickets"
    type="info"
/>

<button wire:click="showClaimableTickets" class="btn-primary">
    {{ __('portal.claim_submissions') }}
</button>

<!-- Modal: Select tickets to claim -->
<x-ui.modal wire:model="showClaimModal">
    <x-slot:title>{{ __('portal.claimable_tickets') }}</x-slot:title>

    <div class="space-y-4">
        @foreach($claimableTickets as $ticket)
            <label class="flex items-center p-4 border rounded hover:bg-gray-50">
                <input type="checkbox" wire:model="selectedTickets" value="{{ $ticket->id }}">
                <div class="ml-3">
                    <p class="font-medium">{{ $ticket->ticket_number }}</p>
                    <p class="text-sm text-gray-600">{{ $ticket->title }}</p>
                </div>
            </label>
        @endforeach
    </div>

    <x-slot:footer>
        <x-ui.button wire:click="initiateClaim(selectedTickets)">
            {{ __('portal.claim_selected') }}
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>

<!-- Modal: OTP verification -->
<x-ui.modal wire:model="showOtpVerification">
    <x-slot:title>{{ __('portal.verify_email') }}</x-slot:title>

    <p class="text-sm text-gray-600 mb-4">
        {{ __('portal.otp_sent_to_email', ['email' => $user->email]) }}
    </p>

    <x-form.input
        wire:model="otpInput"
        type="text"
        maxlength="6"
        pattern="[0-9]{6}"
        :label="__('portal.enter_otp')"
        required
    />

    <x-slot:footer>
        <x-ui.button wire:click="verifyClaim(otpInput)">
            {{ __('portal.verify_and_claim') }}
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>
```

**Security Benefits**:

- Email verification prevents unauthorized claiming
- OTP expires after 10 minutes
- Audit trail of all claiming actions
- Only tickets with matching email can be claimed

## Security Design

### CSRF Protection

- Laravel's built-in CSRF protection
- Enhanced for AJAX requests
- Token refresh for long-lived sessions
- Validation on all form submissions

### Rate Limiting

- 60 requests per minute for guest forms
- IP-based rate limiting
- Throttle middleware on sensitive endpoints
- Graceful degradation with user feedback

### Input Validation

- Client-side: Real-time validation with Livewire
- Server-side: Laravel Form Requests
- Sanitization of user input
- XSS prevention with Blade escaping

### Authentication & Authorization

- Laravel authentication system
- Email verification required
- Role-based access control (4 roles)
- Policy-based authorization
- Session management with secure cookies

### Data Protection

- PDPA 2010 compliance
- Encryption for sensitive data (AES-256)
- Secure token generation for approvals
- Audit trail with 7-year retention

## Correctness Properties

_A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees._

### Property 1: Component Accessibility Compliance

_For any_ UI component in the Component_Library, the component SHALL maintain WCAG 2.2 AA compliance with 4.5:1 text contrast, 3:1 UI contrast, and 44×44px touch targets across all viewport sizes.

Validates: Requirements 7.1, 7.2

### Property 2: Performance Threshold Consistency

_For any_ page load in the ICTServe_System, Core Web Vitals SHALL remain within targets (LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms) regardless of user role or data volume.

Validates: Requirements 8.1, 8.5

### Property 3: Bilingual Content Completeness

_For any_ user-facing text element, both Bahasa Melayu and English translations SHALL exist and be semantically equivalent.

Validates: Requirements 13.1

### Property 4: Form Validation Consistency

_For any_ form submission (guest or authenticated), client-side validation SHALL match server-side validation rules, preventing submission of invalid data.

Validates: Requirements 9.4

### Property 5: Responsive Layout Integrity

_For any_ viewport size between 320px and 1920px, all interactive elements SHALL remain accessible with minimum 44×44px touch targets and proper spacing.

Validates: Requirements 15.1, 15.2

### Property 6: Component Reusability

_For any_ new interface implementation, at least 95% of UI elements SHALL be composed from existing Component_Library components.

Validates: Requirements 6.1

### Property 7: Cache Invalidation Correctness

_For any_ data update operation, related cached data SHALL be invalidated within 5 seconds to ensure data consistency.

Validates: Requirements 8.2, 8.4

### Property 8: Security Header Presence

_For any_ HTTP response from the ICTServe_System, required security headers (CSRF, CSP, HSTS) SHALL be present and properly configured.

Validates: Requirements 14.4

### Property 9: Email Delivery Timeliness

_For any_ triggered email notification, the email SHALL be queued and dispatched within 60 seconds of the triggering event.

Validates: Requirements 12.2

### Property 10: Cross-Module Data Consistency

_For any_ asset-ticket linking operation, the relationship SHALL be bidirectional and queryable from both helpdesk and asset loan modules.

Validates: Requirements 11.3, 11.4

## Testing Strategy

### Shift-Left Testing Approach

**Philosophy**: Testing integrated into development phases, not deferred to Phase 6.

**Component-Level Quality Gates** (Phase 2):

- A component is NOT "Done" until it passes:
  - axe-core accessibility checks (automated)
  - Visual regression snapshot tests
  - Unit tests for component logic
  - WCAG 2.2 AA manual verification

### Unit Testing

- Business logic in services and traits
- Component methods and computed properties
- Validation rules and form requests
- Helper functions and utilities
- **Integrated into CI/CD**: Blocks PR merges on failure

### Feature Testing

- User workflows (ticket submission, loan application)
- Authentication and authorization
- Form submissions and validation
- Email notifications
- API endpoints
- **Optimistic UI validation**: Test rollback scenarios

### Integration Testing

- Cross-module functionality (asset-ticket linking)
- Email workflows
- Approval processes
- Dashboard statistics
- **Event-driven decoupling**: Test event listeners independently

### Accessibility Testing (Shift-Left)

- **Phase 2 Integration**: axe-core/Pa11y checks in component development
- Lighthouse audits (target: 100 score)
- axe DevTools automated testing in CI/CD
- Manual screen reader testing (NVDA, JAWS)
- Keyboard navigation testing
- Color contrast verification (automated in Phase 2)
- **Blocking Quality Gate**: Components cannot proceed without passing accessibility tests

### Visual Regression Testing (NEW)

- **Snapshot Testing**: HTML snapshots using Spatie's laravel-snapshot-testing or Pest
- **Phase 2 Integration**: Baseline snapshots created during component development
- **Phase 4 Protection**: Styling refactors validated against Phase 3 form snapshots
- **Automated Detection**: CI/CD fails on unexpected visual changes

### Performance Testing

- Core Web Vitals monitoring
- Lighthouse performance audits
- Load testing for concurrent users
- Database query optimization
- Cache effectiveness
- **HMR benchmarking**: Tailwind 4.0 + Vite integration validation

### Browser Testing

- Chrome 90+ (primary)
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)
- **In-app browser testing**: WhatsApp/Telegram browsers for URL locale validation

## Deployment Strategy

### Environment Configuration

#### Development

- Local development with Vite HMR
- SQLite database
- Debug mode enabled
- Detailed error messages

#### Staging

- Production-like environment
- MySQL database
- Debug mode disabled
- Error logging to files

#### Production

- Optimized assets (minified, gzipped)
- MySQL database with replication
- Redis caching
- Queue workers for background jobs
- Monitoring and alerting

### Build Process

1. **Asset Compilation**: `npm run build`
2. **CSS Optimization**: Tailwind purging, minification
3. **JavaScript Bundling**: Vite code splitting
4. **Cache Warming**: Route, config, view caching
5. **Database Migration**: Run pending migrations
6. **Queue Workers**: Start background job processors

### Monitoring

- Application performance monitoring (APM)
- Error tracking and logging
- Core Web Vitals monitoring
- User analytics
- Uptime monitoring
- Database performance metrics

## Traceability Matrix

### Requirements to Design Mapping

| Requirement                    | Design Section                             |
| ------------------------------ | ------------------------------------------ |
| R01: Laravel 12.x Foundation   | Architecture Overview, Technology Stack    |
| R02: Livewire 3.x Architecture | Livewire 3.x Architecture                  |
| R03: Volt 1 Components         | Volt 1 Component Design                    |
| R04: Tailwind CSS 4.1          | Tailwind CSS 4.1 Design System             |
| R05: Alpine.js 3.x             | Alpine.js 3.x Patterns                     |
| R06: Component Library         | Component Library Design                   |
| R07: WCAG 2.2 AA Compliance    | Accessibility Implementation               |
| R08: Performance Optimization  | Performance Optimization Design            |
| R09: Guest Forms               | Page Layout Design (Guest Layout)          |
| R10: Authenticated Portal      | Page Layout Design (Portal Layout)         |
| R11: Cross-Module Integration  | Architecture Overview                      |
| R12: Email Workflows           | Security Design                            |
| R13: Bilingual Support         | Bilingual Support Design                   |
| R14: Security & Compliance     | Security Design                            |
| R15: Responsive Design         | Responsive Design Strategy                 |
| R16: Testing & QA              | Testing Strategy                           |
| R17: Documentation             | All sections (comprehensive documentation) |

### Design to D00-D15 Standards

- **D03**: Software Requirements Specification → Requirements Document
- **D04**: Software Design Document → This Document
- **D12**: UI/UX Design Guide → Component Library, Tailwind Design System
- **D13**: UI/UX Frontend Framework → Architecture Overview, Technology Stack
- **D14**: UI/UX Style Guide → Tailwind CSS Design System, Accessibility
- **D15**: Language Support → Bilingual Support Design

## Success Criteria

The design will be considered successful when:

1. **Architecture**: Three-tier hybrid architecture fully implemented and operational
2. **Components**: Unified component library with 95%+ reuse across all interfaces
3. **Accessibility**: 100% WCAG 2.2 AA compliance verified by Lighthouse and manual testing
4. **Performance**: Core Web Vitals targets achieved (LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms)
5. **Responsive**: Seamless experience across all devices (320px-1920px)
6. **Bilingual**: Complete Bahasa Melayu and English support with proper persistence
7. **Security**: Zero critical vulnerabilities, PDPA 2010 compliance verified
8. **Testing**: 80%+ code coverage, 95%+ critical path coverage
9. **Integration**: Seamless cross-module functionality between helpdesk and asset loan
10. **User Experience**: Positive feedback from stakeholders and end users

## Conclusion

This design document provides a comprehensive blueprint for the ICTServe frontend upgrade. The design leverages modern Laravel 12.x, Livewire 3.x, Volt 1, Tailwind CSS 4.1, and Alpine.js 3.x technologies to deliver a unified, accessible, and high-performance frontend experience.

The three-tier hybrid architecture supports guest forms, authenticated portal, and admin interfaces with a shared component library ensuring consistency and maintainability. WCAG 2.2 AA compliance, Core Web Vitals optimization, and comprehensive testing ensure a high-quality user experience for all users.

Implementation will follow the phased approach outlined in the tasks document, with continuous testing and validation against requirements and design specifications.

---

## Strategic Architectural Improvements

This design incorporates strategic improvements to de-risk implementation:

1. **Tailwind 4.0 De-Risking**: @theme CSS variables, HMR benchmarking requirement
2. **Volt Governance**: State complexity criteria (presentational vs. complex lifecycle)
3. **Shift-Left Testing**: Component-level quality gates, axe-core integration, visual regression
4. **Event-Driven Architecture**: Decoupled module integration via Laravel Events & Listeners
5. **Optimistic UI Pattern**: Immediate feedback for 60-second email workflows
6. **URL-Based Locale**: Multi-layered persistence strategy for volatile session environments
7. **Visual Regression Testing**: HTML snapshot testing to prevent styling refactors from breaking layouts
8. **Approver Separation Architecture**: Filament widgets as read-only monitoring, redirecting to Frontend Portal for actual approvals
9. **Rich Data Visualization**: Widget queries enriched with user/department relationships to eliminate mystery meat navigation
10. **Impersonation Security Framework**: Middleware-based protection with audit logging and action blocking

**Risk Mitigation**: Early validation prevents Phase 6 refactoring. Quality gates enforce WCAG 2.2 AA compliance at component level.

## Admin Panel Architecture Refinement

### Approver Workflow Separation

**Problem**: Filament admin panel should be for IT Admins only, not for standard Grade 41+ approvers. Mixing approval workflows in Filament creates "Admin Panel confusion" for non-IT staff.

**Solution**: Filament widgets display read-only monitoring data. Clicking approval items redirects to Frontend Portal Approval View.

**Implementation**:

```php
// app/Filament/Widgets/LoanApprovalQueue.php
protected function getTableActions(): array
{
    return [
        Tables\Actions\Action::make('review')
            ->label('Review in Portal')
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->url(fn (LoanApplication $record): string =>
                route('portal.loans.approve', $record)
            )
            ->openUrlInNewTab(false),
    ];
}

// Prevent Filament Edit resource access for approval workflows
public static function canEdit(Model $record): bool
{
    // Admins can only view/monitor, not approve via Filament
    return false;
}
```

**Benefits**:

- Clear separation: Filament = IT Admin monitoring, Portal = Approval workflows
- Consistent approval experience for all Grade 41+ users
- Prevents confusion between admin panel and approval interface

### Rich Data Visualization in Widgets

**Problem**: Current widgets display ticket IDs (e.g., "LA2025110011") without context - "mystery meat navigation" requiring clicks to understand content.

**Solution**: Enrich widget queries with eager-loaded relationships to display meaningful information.

**Implementation**:

```php
// app/Filament/Widgets/LoanApprovalQueue.php
protected function getTableQuery(): Builder
{
    return LoanApplication::query()
        ->where('status', 'pending_approval')
        ->with(['user', 'user.department', 'assets']) // Eager load relationships
        ->latest();
}

protected function getTableColumns(): array
{
    return [
        Tables\Columns\TextColumn::make('user.name')
            ->label('Applicant')
            ->searchable()
            ->sortable(),

        Tables\Columns\TextColumn::make('user.department.name')
            ->label('Department')
            ->searchable(),

        Tables\Columns\TextColumn::make('assets.name')
            ->label('Asset Type')
            ->formatStateUsing(fn ($state) => $state ?? 'Multiple Assets'),

        Tables\Columns\BadgeColumn::make('created_at')
            ->label('Time Elapsed')
            ->formatStateUsing(fn ($state) => $state->diffForHumans())
            ->color(fn ($state) => match(true) {
                $state->diffInDays() > 2 => 'danger',
                $state->diffInDays() > 1 => 'warning',
                default => 'success',
            }),

        Tables\Columns\TextColumn::make('ticket_number')
            ->label('Ticket ID')
            ->searchable(),
    ];
}
```

**Widget Display Format**:

- **Primary Text**: User Name (e.g., "Ahmad Albab")
- **Secondary Text**: Department & Asset Type (e.g., "Kewangan - Laptop")
- **Badge**: Time Elapsed with color coding (Red: >2 days, Amber: >1 day, Green: <1 day)
- **Tertiary**: Ticket ID for reference

**Benefits**:

- Immediate context without clicking
- Faster decision-making for admins
- Reduced cognitive load

### Impersonation Security Framework

**Problem**: Admin impersonation allows viewing portal as user, but lacks security controls to prevent unintended actions (e.g., password changes) and audit trails.

**Solution**: Middleware-based impersonation with visual banner, action blocking, and comprehensive audit logging.

**Implementation**:

```php
// app/Http/Middleware/CheckImpersonation.php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckImpersonation
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('impersonate_user_id')) {
            // Inject impersonation banner
            view()->share('is_impersonating', true);
            view()->share('impersonator_name', Auth::user()->name);
            view()->share('impersonated_user', User::find(session('impersonate_user_id')));

            // Block critical actions
            if ($this->isCriticalAction($request)) {
                abort(403, 'Action blocked during impersonation. Please stop impersonating to perform this action.');
            }

            // Audit log all actions
            Log::channel('audit')->info('Impersonation Action', [
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()->name,
                'impersonated_user_id' => session('impersonate_user_id'),
                'action' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip(),
            ]);
        }

        return $next($request);
    }

    protected function isCriticalAction(Request $request): bool
    {
        $blockedRoutes = [
            'portal.profile.update-password',
            'portal.profile.update-email',
            'portal.profile.delete-account',
        ];

        return in_array($request->route()->getName(), $blockedRoutes);
    }
}
```

**Impersonation Banner** (resources/views/components/impersonation-banner.blade.php):

```blade
@if(isset($is_impersonating) && $is_impersonating)
<div class="fixed top-0 left-0 right-0 z-50 bg-yellow-500 text-black px-4 py-2 text-center font-semibold">
    <div class="flex items-center justify-center gap-4">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <span>
            {{ __('portal.impersonating_user', ['name' => $impersonated_user->name]) }}
            ({{ __('portal.logged_in_as') }}: {{ $impersonator_name }})
        </span>
        <a href="{{ route('admin.impersonate.stop') }}"
           class="underline hover:no-underline">
            {{ __('portal.stop_impersonating') }}
        </a>
    </div>
</div>
@endif
```

**Filament Impersonation Action**:

```php
// app/Filament/Resources/UserResource.php
public static function getActions(): array
{
    return [
        Tables\Actions\Action::make('impersonate')
            ->label('View as User')
            ->icon('heroicon-o-user-circle')
            ->action(function (User $record) {
                session(['impersonate_user_id' => $record->id]);

                // Audit log impersonation start
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($record)
                    ->withProperties([
                        'admin_id' => auth()->id(),
                        'impersonated_user_id' => $record->id,
                    ])
                    ->log('Started impersonating user');

                return redirect()->route('portal.dashboard');
            })
            ->requiresConfirmation()
            ->modalHeading('Impersonate User')
            ->modalDescription('You will view the portal as this user. All actions will be logged.')
            ->visible(fn () => auth()->user()->hasRole('admin')),
    ];
}
```

**Security Features**:

- **Visual Banner**: Yellow warning banner at top of all portal pages during impersonation
- **Action Blocking**: Prevents password changes, email updates, account deletion
- **Audit Logging**: All actions logged with admin_id and impersonated_user_id
- **Easy Exit**: "Stop Impersonating" link always visible in banner

**Benefits**:

- Clear visual indication of impersonation state
- Prevents accidental/malicious profile modifications
- Complete audit trail for compliance
- Maintains user trust through transparency

---

**Document Version**: 2.0  
**Last Updated**: 2025-01-21  
**Author**: Frontend Engineering Team  
**Status**: Design Approved - Strategic Improvements Integrated  
**Technology Stack**: Laravel 12.x | Livewire 3.x | Volt 1 | Tailwind CSS 4.1 | Alpine.js 3.x  
**Methodology**: Shift-Left Testing | Event-Driven Architecture | Optimistic UI
