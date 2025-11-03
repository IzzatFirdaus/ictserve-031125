# Bilingual Support and Livewire/Volt Architecture

**Component Name**: Bilingual Support and Livewire/Volt Architecture  
**Description**: Comprehensive bilingual support system with Livewire 3 and Volt architecture for ICTServe  
**Requirements**: D03-FR-005.4, D03-FR-006.1, D03-FR-014.4, D03-FR-015.1-015.4  
**WCAG Level**: AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.1.2, 3.3.1, 3.3.2)  
**Version**: 1.0.0  
**Author**: Pasukan BPM MOTAC  
**Created**: 2025-11-03  
**Last Updated**: 2025-11-03

## Overview

The ICTServe system implements comprehensive bilingual support (Bahasa Melayu primary, English secondary) using Laravel's localization system integrated with Livewire 3 and Volt components. This architecture provides:

- **Bilingual UI**: Complete translations for all user-facing text
- **Dynamic Components**: Livewire 3 for interactive forms with real-time validation
- **Single-File Components**: Volt for simplified authentication flows
- **WCAG 2.2 AA Compliance**: Accessible translations with proper ARIA attributes
- **Performance Optimization**: Computed properties, debouncing, lazy loading

## Architecture

### Language File Structure

```
lang/
├── en/                          # English translations
│   ├── common.php              # Common UI elements, navigation, actions
│   ├── helpdesk.php            # Helpdesk module translations
│   └── asset_loan.php          # Asset loan module translations
└── ms/                          # Bahasa Melayu translations
    ├── common.php              # Elemen UI biasa, navigasi, tindakan
    ├── helpdesk.php            # Terjemahan modul meja bantuan
    └── asset_loan.php          # Terjemahan modul pinjaman aset
```

### Livewire Component Structure

```
app/Livewire/
├── Actions/                     # Action components (Logout)
├── Forms/                       # Form objects (HelpdeskTicketForm, LoginForm)
└── Helpdesk/                    # Module-specific components (SubmitTicket)

resources/views/livewire/
├── components/                  # Reusable components (language-switcher)
├── helpdesk/                    # Helpdesk views (submit-ticket)
├── pages/                       # Page components
│   └── auth/                    # Volt authentication components
├── profile/                     # Profile management components
└── layout/                      # Layout components (navigation)
```

## Translation System

### Using Translations in Blade Templates

```blade
{{-- Simple translation --}}
<h1>{{ __('helpdesk.submit_ticket') }}</h1>

{{-- Translation with parameters --}}
<p>{{ __('common.welcome_message', ['name' => $user->name]) }}</p>

{{-- Translation in attributes --}}
<input
    type="text"
    placeholder="{{ __('common.search') }}"
    aria-label="{{ __('common.search_label') }}"
/>
```

### Translation Keys Organization

**common.php** - Shared across all modules:

- Navigation: `home`, `dashboard`, `helpdesk`, `asset_loan`
- Actions: `submit`, `cancel`, `save`, `edit`, `delete`
- Status: `open`, `closed`, `pending`, `approved`, `rejected`
- Labels: `name`, `email`, `phone`, `date`, `time`
- Messages: `success`, `error`, `warning`, `loading`
- Accessibility: `skip_to_content`, `main_navigation`, `user_menu`

**helpdesk.php** - Helpdesk module specific:

- Page titles: `submit_ticket`, `ticket_details`, `my_tickets`
- Form labels: `full_name`, `email_address`, `issue_category`
- Help text: `name_help`, `email_help`, `category_help`
- Validation: `name_required`, `email_invalid`, `description_min`

**asset_loan.php** - Asset loan module specific:

- Application forms, approval workflows, asset management

## Livewire 3 Components

### Component Structure

```php
<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Livewire\Forms\HelpdeskTicketForm;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Submit Helpdesk Ticket Component
 *
 * @requirements 1.1, 1.2, 11.1-11.7, 15.1-15.4
 * @wcag-level AA
 */
#[Layout('layouts.guest')]
#[Title('Submit Helpdesk Ticket')]
class SubmitTicket extends Component
{
    use WithFileUploads;

    public HelpdeskTicketForm $form;

    /**
     * Get divisions with caching
     * Uses #[Computed] for performance optimization
     */
    #[Computed]
    public function divisions(): Collection
    {
        return Division::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.helpdesk.submit-ticket');
    }
}
```

### Livewire Optimization Patterns

**1. Computed Properties** - Cache expensive queries:

```php
#[Computed]
public function categories(): Collection
{
    return Category::query()
        ->where('type', 'helpdesk')
        ->select('id', 'name', 'description')
        ->orderBy('name')
        ->get();
}
```

**2. Debounced Input** - Reduce server requests:

```blade
<input
    type="text"
    wire:model.live.debounce.300ms="form.name"
/>
```

**3. Lazy Binding** - Defer updates until blur:

```blade
<textarea
    wire:model.lazy="form.description"
    rows="5"
></textarea>
```

**4. Loading States** - Provide user feedback:

```blade
<button type="submit" wire:loading.attr="disabled">
    <span wire:loading.remove>{{ __('common.submit') }}</span>
    <span wire:loading>{{ __('common.submitting') }}</span>
</button>
```

## Volt Components

### Single-File Component Example

Volt components combine PHP logic and Blade templates in one file:

```blade
<?php

use function Livewire\Volt\{state, rules};

state(['email' => '', 'password' => '']);

rules([
    'email' => 'required|email',
    'password' => 'required|min:8',
]);

$login = function () {
    $this->validate();

    if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
        return redirect()->intended('/dashboard');
    }

    $this->addError('email', __('auth.failed'));
};

?>

<form wire:submit="login">
    <div>
        <label for="email">{{ __('common.email') }}</label>
        <input type="email" id="email" wire:model="email" />
        @error('email') <span>{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="password">{{ __('common.password') }}</label>
        <input type="password" id="password" wire:model="password" />
        @error('password') <span>{{ $message }}</span> @enderror
    </div>

    <button type="submit">{{ __('common.login') }}</button>
</form>
```

### When to Use Volt vs Livewire

**Use Volt for**:

- Simple forms with minimal logic (<100 lines PHP)
- Authentication flows (login, register, password reset)
- Single-purpose components
- Rapid prototyping

**Use Livewire for**:

- Complex business logic
- Multiple methods and computed properties
- File uploads and advanced features
- Reusable form objects

## WCAG 2.2 AA Compliance

### Accessible Translations

All translations include accessibility features:

```php
// lang/en/common.php
return [
    'required_field' => 'This field is required',
    'skip_to_content' => 'Skip to main content',
    'main_navigation' => 'Main navigation',
    'user_menu' => 'User menu',
    'language_switcher' => 'Language switcher',
    'current_language' => 'Current language',
];
```

### ARIA Attributes in Blade

```blade
<label for="name" class="block text-sm font-medium text-gray-900 mb-1">
    {{ __('helpdesk.full_name') }}
    <span class="text-red-600" aria-label="{{ __('common.required_field') }}">*</span>
</label>

<input
    type="text"
    id="name"
    wire:model.live.debounce.300ms="form.name"
    aria-required="true"
    aria-invalid="@error('form.name') true @else false @enderror"
    aria-describedby="name-help @error('form.name') name-error @enderror"
/>

<p id="name-help" class="mt-1 text-sm text-gray-600">
    {{ __('helpdesk.name_help') }}
</p>

@error('form.name')
    <p id="name-error" class="mt-1 text-sm text-red-600" role="alert">
        {{ $message }}
    </p>
@enderror
```

### Screen Reader Announcements

```blade
{{-- ARIA Live Region for Dynamic Updates --}}
<div aria-live="polite" aria-atomic="true" class="sr-only" id="form-announcements"></div>

@push('scripts')
<script>
    Livewire.on('ticket-submitted', (event) => {
        const announcer = document.getElementById('form-announcements');
        if (announcer) {
            announcer.textContent = '{{ __('helpdesk.ticket_submitted') }}. ' +
                                   '{{ __('helpdesk.ticket_number') }}: ' + event.ticketNumber;
        }
    });
</script>
@endpush
```

## Language Switcher Integration

The bilingual system integrates with the language switcher (Task 7.3):

```blade
{{-- Language Switcher Component --}}
<x-accessibility.language-switcher />
```

**Features**:

- Session/cookie-based persistence (no user profile storage)
- WCAG 2.2 AA compliant with 44×44px touch targets
- Alpine.js dropdown with keyboard navigation
- Automatic locale detection (session → cookie → browser → fallback)

## Performance Optimization

### 1. Computed Properties Caching

```php
#[Computed]
public function divisions(): Collection
{
    // Cached for the component lifecycle
    return Division::query()
        ->select('id', 'name')
        ->orderBy('name')
        ->get();
}
```

### 2. Eager Loading

```php
public function mount(): void
{
    // Prevent N+1 queries
    $this->tickets = HelpdeskTicket::query()
        ->with(['user', 'division', 'category'])
        ->where('user_id', auth()->id())
        ->latest()
        ->get();
}
```

### 3. Debouncing

```blade
{{-- Reduce server requests for real-time validation --}}
<input wire:model.live.debounce.300ms="form.email" />
```

### 4. Lazy Loading

```blade
{{-- Defer updates until blur for large text fields --}}
<textarea wire:model.lazy="form.description"></textarea>
```

## Testing

### Unit Tests for Translations

```php
public function test_translations_exist_for_all_keys(): void
{
    $englishKeys = array_keys(trans('common', [], 'en'));
    $malayKeys = array_keys(trans('common', [], 'ms'));

    $this->assertEquals($englishKeys, $malayKeys,
        'English and Malay translation keys must match');
}
```

### Feature Tests for Livewire Components

```php
public function test_guest_can_submit_helpdesk_ticket(): void
{
    Livewire::test(SubmitTicket::class)
        ->set('form.name', 'John Doe')
        ->set('form.email', 'john@motac.gov.my')
        ->set('form.phone', '+60123456789')
        ->set('form.category_id', 1)
        ->set('form.subject', 'Test Issue')
        ->set('form.description', 'Test description with minimum 10 characters')
        ->call('submitTicket')
        ->assertSet('submitted', true)
        ->assertNotNull('ticketNumber');

    $this->assertDatabaseHas('helpdesk_tickets', [
        'guest_name' => 'John Doe',
        'guest_email' => 'john@motac.gov.my',
    ]);
}
```

## Adding New Translations

### 1. Add to Language Files

**lang/en/module.php**:

```php
return [
    'new_key' => 'English translation',
    'with_params' => 'Hello :name, welcome to :app',
];
```

**lang/ms/module.php**:

```php
return [
    'new_key' => 'Terjemahan Bahasa Melayu',
    'with_params' => 'Selamat datang :name ke :app',
];
```

### 2. Use in Blade Templates

```blade
<h1>{{ __('module.new_key') }}</h1>
<p>{{ __('module.with_params', ['name' => $user->name, 'app' => 'ICTServe']) }}</p>
```

### 3. Verify Translations

```bash
# Check for missing translations
php artisan lang:check

# Run translation tests
php artisan test --filter=TranslationTest
```

## Best Practices

### 1. Translation Keys

- Use descriptive, hierarchical keys: `helpdesk.submit_ticket` not `submit`
- Group related translations: `helpdesk.form.*`, `helpdesk.validation.*`
- Keep keys consistent across languages
- Use parameters for dynamic content: `:name`, `:count`

### 2. Livewire Components

- Use `#[Computed]` for expensive queries
- Implement debouncing for real-time validation
- Use lazy binding for large text fields
- Provide loading states for all actions
- Include proper ARIA attributes

### 3. WCAG Compliance

- All form fields must have associated labels
- Error messages must be linked via `aria-describedby`
- Use `aria-invalid` for validation states
- Provide help text for complex fields
- Include screen reader announcements for dynamic updates

### 4. Performance

- Cache computed properties
- Use eager loading to prevent N+1 queries
- Implement debouncing for real-time features
- Lazy load heavy components
- Minimize wire:model.live usage

## Troubleshooting

### Translation Not Showing

**Problem**: Translation key displays instead of translated text

**Solution**:

1. Check language file exists: `lang/en/module.php`
2. Verify key exists in file: `'key' => 'Translation'`
3. Clear cache: `php artisan cache:clear`
4. Check current locale: `App::getLocale()`

### Livewire Component Not Updating

**Problem**: Form changes not reflected in UI

**Solution**:

1. Check wire:model binding: `wire:model.live="form.field"`
2. Verify property is public: `public string $field`
3. Check for JavaScript errors in console
4. Clear Livewire cache: `php artisan livewire:clear`

### Performance Issues

**Problem**: Slow form interactions

**Solution**:

1. Add debouncing: `wire:model.live.debounce.300ms`
2. Use computed properties: `#[Computed]`
3. Implement lazy loading: `wire:model.lazy`
4. Check for N+1 queries with Laravel Debugbar

## Related Documentation

- [Language Switcher Implementation](./language-switcher-implementation.md)
- [Hybrid Forms Implementation](./hybrid-forms-implementation.md)
- [WCAG 2.2 AA Compliance Guide](../frontend/accessibility-guidelines.md)
- [Livewire Optimization Patterns](./livewire-optimization-patterns.md)

## Compliance Verification

- ✅ **D03-FR-005.4**: Bilingual support (Bahasa Melayu primary, English secondary)
- ✅ **D03-FR-006.1**: WCAG 2.2 Level AA compliance
- ✅ **D03-FR-014.4**: Consistent bilingual UI across all modules
- ✅ **D03-FR-015.1**: Livewire 3 for dynamic interactions
- ✅ **D03-FR-015.2**: Volt for simplified components
- ✅ **D03-FR-015.3**: Real-time form validation
- ✅ **D03-FR-015.4**: Optimized component performance

## Version History

| Version | Date       | Author            | Changes                                          |
| ------- | ---------- | ----------------- | ------------------------------------------------ |
| 1.0.0   | 2025-11-03 | Pasukan BPM MOTAC | Initial documentation of existing implementation |

---

**Status**: ✅ VERIFIED COMPLETE  
**Last Verified**: 2025-11-03  
**Next Review**: 2025-12-03
