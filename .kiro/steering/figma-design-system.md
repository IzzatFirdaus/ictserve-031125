---
inclusion: always
description: "Figma MCP Integration Guidelines for ICTServe Laravel Application"
version: "3.6.0"
last_updated: "2025-12-11"
---

# Figma MCP Integration Guidelines for ICTServe

## Design System Structure Analysis

### 1. Token Definitions

**Location**: `resources/css/app.css` (Tailwind v4 @theme directive)
**Format**: CSS Custom Properties with Tailwind v4 theme configuration

```css
@theme {
  --color-primary-50: #eff6ff;
  --color-primary-500: #0056B3;
  --color-primary-600: #004494;
  --radius-xs: 4px;
  --radius-m: 8px;
  --radius-l: 12px;
}
```

**Token Categories**:

- Colors: Primary, Secondary, Success, Warning, Danger, Neutral
- Typography: Poppins (headings), Inter (body), JetBrains Mono (code)
- Spacing: 4px base scale (space-1 to space-16)
- Radius: XS (4px) to Full (9999px)
- Shadows: SM, Button, Card, Dropdown, Modal

### 2. Component Library

**Location**: `resources/views/components/`
**Architecture**: Laravel Blade Components + Livewire v3 + Volt v1

```
resources/views/components/
├── ui/                    # Core UI components
│   ├── button.blade.php
│   ├── card.blade.php
│   ├── modal.blade.php
│   └── stats-card.blade.php
├── form/                  # Form components
│   ├── input.blade.php
│   ├── select.blade.php
│   └── textarea.blade.php
├── layout/                # Layout components
└── accessibility/         # WCAG 2.2 AA components
```

**Usage Pattern**:

```blade
<x-ui.button variant="primary" size="md">
    Submit Ticket
</x-ui.button>

<x-form.input 
    wire:model="title" 
    label="Ticket Title"
    required />
```

### 3. Frameworks & Libraries

**UI Framework**: Laravel Blade + Livewire 3.7 + Volt 1.10
**Styling**: Tailwind CSS 4.1.17 (CSS-first configuration)
**Build System**: Vite 7.0.7
**Admin Panel**: Filament 4.1.10

**Key Integration Points**:

- Livewire components for interactivity
- Volt for single-file components
- Filament for admin interfaces
- Alpine.js (included with Livewire) for client-side behavior

### 4. Asset Management

**Storage**: `public/` directory with Vite asset pipeline
**Images**: `public/images/` for static assets
**Build Output**: `public/build/` for compiled assets

```javascript
// vite.config.js
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### 5. Icon System

**Implementation**: Heroicons (default with Filament)
**Usage**: Via Blade components and Filament icon enums

```blade
{{-- Blade usage --}}
<x-heroicon-o-ticket class="w-5 h-5" />

{{-- Filament usage --}}
use Filament\Support\Icons\Heroicon;
->icon(Heroicon::OUTLINE_TICKET)
```

### 6. Styling Approach

**Methodology**: Utility-first with Tailwind CSS v4
**Responsive**: Mobile-first breakpoint system
**Dark Mode**: Supported via `dark:` prefix
**WCAG Compliance**: 4.5:1 text contrast, 3:1 UI contrast

**Global Styles**: Defined in `resources/css/app.css`

```css
@import "tailwindcss";

/* Global focus ring for WCAG compliance */
*:focus-visible {
  outline: 3px solid var(--color-primary-500);
  outline-offset: 2px;
}
```

### 7. Project Structure

**Laravel 12 Structure** (Streamlined):

```
app/
├── Livewire/              # Livewire components
├── Filament/Resources/    # Admin panel resources
├── Models/                # Eloquent models
└── Services/              # Business logic

resources/
├── views/
│   ├── components/        # Blade components
│   ├── livewire/         # Volt components
│   └── layouts/          # Layout templates
├── css/app.css           # Tailwind + custom styles
└── js/app.js             # JavaScript entry point
```

## Figma-to-Code Workflow

### Step 1: Extract Design from Figma

```bash
# Use Figma MCP to get component design
mcp_figma_get_design_context(nodeId="123:456", fileKey="abc123")
```

### Step 2: Convert to Laravel Patterns

1. **Transform to Blade syntax** from React/JSX
2. **Replace with ICTServe components** (x-ui.*, x-form.*)
3. **Apply Livewire patterns** (wire:model, wire:click)
4. **Ensure WCAG compliance** (contrast, focus, touch targets)

### Step 3: Integration Guidelines

**Component Decision Matrix**:

- **Simple UI**: Blade components (`x-ui.*`)
- **Interactive forms**: Livewire Volt functional
- **Complex workflows**: Livewire class-based
- **Admin interfaces**: Filament resources

**Token Mapping**:

- Figma colors → CSS custom properties
- Figma spacing → Tailwind spacing scale
- Figma typography → Tailwind font classes
- Figma radius → Semantic radius tokens

### Step 4: Quality Assurance

**Required Checks**:

- [ ] Visual parity with Figma design
- [ ] WCAG 2.2 AA compliance (contrast, focus)
- [ ] Touch target minimum 44x44px
- [ ] Responsive behavior across breakpoints
- [ ] Laravel Pint formatting (PSR-12)
- [ ] PHPUnit test coverage

## ICTServe-Specific Patterns

### Hybrid Architecture Support
Components must support both guest and authenticated states:

```blade
<x-ui.card>
    @auth
        <x-ui.button wire:click="submitAsUser">
            Submit as {{ auth()->user()->name }}
        </x-ui.button>
    @else
        <x-ui.button wire:click="submitAsGuest">
            Submit as Guest
        </x-ui.button>
    @endauth
</x-ui.card>
```

### Bahasa Melayu Language Support
All text content must be localized:

```blade
<x-ui.button>
    {{ __('Submit Ticket') }}
</x-ui.button>
```

### Audit Trail Integration
Interactive components must support audit logging:

```php
// In Livewire component
public function submitTicket()
{
    // Business logic
    $ticket = HelpdeskTicket::create($this->form);
    
    // Audit logging (automatic via Auditable trait)
    activity()
        ->performedOn($ticket)
        ->log('Ticket submitted via Figma-generated form');
}
```

## Code Connect Integration

### Mapping Convention

```blade
{{-- 
    Figma Component: Button/Primary
    Figma URL: https://figma.com/file/xxx/component-id
    Last synced: 2025-12-11
--}}
<x-ui.button variant="primary" {{ $attributes }}>
    {{ $slot }}
</x-ui.button>
```

### Automated Sync Workflow

1. Extract component from Figma
2. Generate Laravel Blade equivalent
3. Map to existing component or create new
4. Update Code Connect mapping
5. Run quality checks and tests

## Compliance Requirements

### WCAG 2.2 AA Standards

- **Color Contrast**: 4.5:1 for text, 3:1 for UI elements
- **Focus Management**: Visible focus indicators (3px outline)
- **Touch Targets**: Minimum 44x44px interactive areas
- **Motion**: Respect `prefers-reduced-motion`

### MyGOV Digital Service Standards v2.1.0

- Bahasa Melayu primary language
- Government branding compliance
- Accessibility-first design approach
- Mobile-responsive implementation

### PDPA 2010 Compliance

- No personal data in design tokens
- Sanitized component examples
- Privacy-conscious default states
