# Bilingual Support System

## Overview

The ICTServe system provides comprehensive bilingual support for Bahasa Melayu (ms) and English (en) with proper accessibility features, user preference persistence, and RTL language preparation for future expansion.

**Trace**: D12 (UI/UX Design Guide), D14 (Style Guide), D15 (Language Support)  
**Requirements**: 5.1, 5.2, 5.5

## Features

### 1. Language Switching Component

The `<x-bilingual.language-switcher />` component provides an accessible language switcher with:

- **WCAG 2.2 Level AA compliance**: Proper ARIA attributes, keyboard navigation, focus management
- **Minimum touch target size**: 44×44 pixels for all interactive elements
- **Visual feedback**: Current language indication with checkmark icon
- **Responsive design**: Adapts to mobile, tablet, and desktop viewports
- **Dark mode support**: Proper contrast ratios in both light and dark themes

#### Usage

```blade
{{-- Header position (default) --}}
<x-bilingual.language-switcher />

{{-- Footer position --}}
<x-bilingual.language-switcher position="footer" />

{{-- Compact mode (no label) --}}
<x-bilingual.language-switcher compact />

{{-- Without label --}}
<x-bilingual.language-switcher :showLabel="false" />
```

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `position` | string | `'header'` | Position of dropdown menu: `'header'` or `'footer'` |
| `showLabel` | boolean | `true` | Show full language name |
| `compact` | boolean | `false` | Compact mode with minimal UI |

### 2. Language Detection and Fallback

The `SetLocale` middleware automatically detects and applies the user's preferred locale with the following priority:

1. **Authenticated user preference** (stored in `users.locale` column)
2. **Session** (current browsing session)
3. **Cookie** (persistent across sessions, 1 year)
4. **Browser detection** (Accept-Language header for first-time users)
5. **Fallback** (default locale from `config/app.php`)

#### Configuration

```php
// config/app.php
'locale' => env('APP_LOCALE', 'en'),
'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
'available_locales' => ['ms', 'en'],
```

### 3. User Preference Persistence

User language preferences are stored in three locations:

1. **Session**: Immediate effect for current browsing session
2. **Cookie**: Persistence across sessions (1 year expiry)
3. **Database**: Permanent storage for authenticated users

#### User Model

```php
// app/Models/User.php
protected $fillable = [
    // ...
    'locale',
];
```

#### Switching Language

```php
// POST /locale/{locale}
Route::match(['get', 'post'], 'locale/{locale}', [LocaleController::class, 'switch'])
    ->name('locale.switch');

// GET /locale/current
Route::get('locale/current', [LocaleController::class, 'current'])
    ->name('locale.current');
```

### 4. Translation Files

Translation files are organized by locale and feature:

```
lang/
├── en/
│   ├── asset-loan.php
│   ├── common.php
│   ├── notifications.php
│   ├── services.php
│   └── welcome.php
└── ms/
    ├── asset-loan.php
    ├── common.php
    ├── notifications.php
    ├── services.php
    └── welcome.php
```

#### Using Translations

```blade
{{-- Simple translation --}}
{{ __('common.back') }}

{{-- Translation with parameters --}}
{{ __('notifications.ticket_assigned', ['ticket' => $ticket->id]) }}

{{-- Pluralization --}}
{{ trans_choice('common.items', $count) }}

{{-- Translation in attributes --}}
<button aria-label="{{ __('common.change_language') }}">
```

### 5. Bilingual Content Validation

The `BilingualValidationService` provides tools to validate translation coverage and identify missing translations.

#### Validation Command

```bash
# Validate translation coverage
php artisan bilingual:validate

# Scan for hardcoded text
php artisan bilingual:validate --scan-hardcoded

# Check translation file structure
php artisan bilingual:validate --check-structure

# Export results
php artisan bilingual:validate --export=json
php artisan bilingual:validate --export=csv
php artisan bilingual:validate --export=html
```

#### Validation Features

- **Translation coverage**: Percentage of translated keys across all locales
- **Missing keys**: Identifies translation keys missing in specific locales
- **Extra keys**: Identifies translation keys not in base locale
- **Hardcoded text scanner**: Finds potential hardcoded text in Blade templates
- **Structure validation**: Ensures translation file consistency across locales

### 6. RTL Language Support Preparation

The system includes CSS foundation for future RTL (Right-to-Left) language support:

#### RTL CSS Classes

```css
/* Direction support */
[dir="rtl"] { direction: rtl; text-align: right; }
[dir="ltr"] { direction: ltr; text-align: left; }

/* Logical properties (modern approach) */
.logical-margin-start { margin-inline-start: 1rem; }
.logical-padding-start { padding-inline-start: 1rem; }
.logical-text-start { text-align: start; }

/* Helper classes */
.rtl-only { display: none; }
[dir="rtl"] .rtl-only { display: block; }
```

#### Using RTL Support

```blade
{{-- Set direction based on locale --}}
<html dir="{{ __('common.text_direction') }}" lang="{{ app()->getLocale() }}">

{{-- Use logical properties --}}
<div class="logical-margin-start logical-padding-end">
    Content
</div>

{{-- Conditional content --}}
<span class="ltr-only">Left to Right</span>
<span class="rtl-only">Right to Left</span>
```

## Accessibility Features

### Keyboard Navigation

- **Tab**: Navigate between language options
- **Enter/Space**: Select language
- **Arrow Down**: Open dropdown and focus first option
- **Arrow Up/Down**: Navigate between options
- **Home**: Focus first option
- **End**: Focus last option
- **Escape**: Close dropdown

### Screen Reader Support

- Proper ARIA labels for all interactive elements
- `aria-haspopup="true"` for dropdown trigger
- `aria-expanded` state for dropdown
- `aria-current="true"` for current language
- `role="menu"` and `role="menuitem"` for dropdown items
- Screen reader announcements for language changes

### Visual Accessibility

- Minimum 44×44 pixel touch targets
- 4.5:1 color contrast ratio (WCAG 2.2 Level AA)
- Visual focus indicators (3-4px outline with 2px offset)
- Current language indication with icon and text
- Dark mode support with proper contrast

## Best Practices

### 1. Always Use Translation Functions

```blade
{{-- ✓ Good --}}
<h1>{{ __('welcome.title') }}</h1>

{{-- ✗ Bad --}}
<h1>Welcome to ICTServe</h1>
```

### 2. Provide Context for Translations

```php
// lang/en/common.php
return [
    'save' => 'Save',
    'save_changes' => 'Save Changes',
    'save_and_continue' => 'Save and Continue',
];
```

### 3. Use Pluralization

```php
// lang/en/common.php
return [
    'items' => '{0} No items|{1} One item|[2,*] :count items',
];
```

```blade
{{ trans_choice('common.items', $count) }}
```

### 4. Handle Long Translations

```blade
{{-- Use word-break for long words --}}
<p class="break-words">{{ __('long.translation.key') }}</p>

{{-- Use ellipsis for truncation --}}
<p class="truncate">{{ __('long.translation.key') }}</p>
```

### 5. Test Both Languages

Always test your features in both Bahasa Melayu and English to ensure:
- Layouts don't break with longer/shorter text
- All text is properly translated
- No hardcoded text remains
- Proper grammar and context

## Testing

### Manual Testing

1. Switch language using the language switcher
2. Verify language preference persists across page reloads
3. Test as authenticated and guest user
4. Verify all text is properly translated
5. Check layouts in both languages
6. Test keyboard navigation
7. Test with screen reader

### Automated Testing

```php
// tests/Feature/BilingualSupportTest.php
public function test_language_switching(): void
{
    $response = $this->post('/locale/ms');
    $response->assertRedirect();
    $this->assertEquals('ms', session('locale'));
}

public function test_user_locale_preference(): void
{
    $user = User::factory()->create(['locale' => 'ms']);
    $this->actingAs($user);
    $this->assertEquals('ms', app()->getLocale());
}
```

### Validation Testing

```bash
# Run bilingual validation
php artisan bilingual:validate

# Expected output:
# Overall Coverage: 100%
# ✓ No missing translation keys found
# ✓ Translation file structure is consistent
```

## Troubleshooting

### Language Not Switching

1. Check middleware is registered in `bootstrap/app.php`
2. Verify locale is in `config/app.php` available_locales
3. Clear cache: `php artisan cache:clear`
4. Check browser console for JavaScript errors

### Missing Translations

1. Run validation: `php artisan bilingual:validate`
2. Check translation file exists in both locales
3. Verify translation key matches exactly
4. Clear translation cache: `php artisan cache:clear`

### Layout Breaking

1. Test with longer text in both languages
2. Use `break-words` or `truncate` classes
3. Avoid fixed widths for text containers
4. Test responsive design at all breakpoints

## Future Enhancements

### Planned Features

1. **Additional Languages**: Support for Arabic, Chinese, Tamil
2. **RTL Language Support**: Full implementation for Arabic and Hebrew
3. **Translation Management UI**: Admin interface for managing translations
4. **Machine Translation Integration**: Automatic translation suggestions
5. **Translation Memory**: Reuse common translations across modules
6. **Context-Aware Translations**: Different translations based on user role or context

### RTL Implementation Checklist

When implementing RTL languages:

- [ ] Set `dir="rtl"` on `<html>` element
- [ ] Update `text_direction` in translation files
- [ ] Test all layouts in RTL mode
- [ ] Flip icons and images where appropriate
- [ ] Update navigation and menu directions
- [ ] Test form layouts and validation
- [ ] Verify table layouts
- [ ] Test modal and dropdown positions
- [ ] Update animation directions
- [ ] Test with RTL-specific fonts

## References

- [Laravel Localization Documentation](https://laravel.com/docs/localization)
- [WCAG 2.2 Success Criterion 3.1.2 - Language of Parts](https://www.w3.org/WAI/WCAG22/Understanding/language-of-parts.html)
- [D15 Language Support Specification](../D15_LANGUAGE_MS_EN.md)
- [D14 Style Guide](../D14_UI_UX_STYLE_GUIDE.md)
- [D12 UI/UX Design Guide](../D12_UI_UX_DESIGN_GUIDE.md)
