# Form Components Quick Reference Guide

**Version**: 1.0.0  
**Last Updated**: 2025-01-06  
**Trace**: D12 §6, D14 §6

---

## Quick Start

### 1. Basic Text Input

```blade
<x-form-field
    label="Nama Penuh"
    name="full_name"
    type="text"
    required
    placeholder="Ahmad bin Abdullah"
/>
```

### 2. Email Input with Helper Text

```blade
<x-form-field
    label="E-mel"
    name="email"
    type="email"
    required
    helper="Gunakan e-mel rasmi @motac.gov.my"
    :error="$errors->first('email')"
/>
```

### 3. Select Dropdown

```blade
<x-form-field
    label="Bahagian"
    name="division_id"
    type="select"
    required
>
    <option value="">-- Pilih Bahagian --</option>
    @foreach($divisions as $division)
        <option value="{{ $division->id }}">{{ $division->name }}</option>
    @endforeach
</x-form-field>
```

### 4. Textarea

```blade
<x-form-field
    label="Keterangan Isu"
    name="description"
    type="textarea"
    required
    placeholder="Terangkan isu dengan terperinci..."
    helper="Minimum 50 aksara"
/>
```

### 5. File Upload

```blade
<div class="form-field">
    <label for="attachment" class="form-label form-label-required">
        Lampiran
    </label>
    <input
        type="file"
        id="attachment"
        name="attachment"
        class="form-file-input"
        accept=".pdf,.jpg,.jpeg,.png"
        required
    />
    <p class="form-helper">Format: PDF, JPG, PNG (Max: 5MB)</p>
</div>
```

---

## Form Layouts

### Two-Column Grid

```blade
<div class="form-grid">
    <x-form-field label="Nama Pertama" name="first_name" required />
    <x-form-field label="Nama Akhir" name="last_name" required />
</div>
```

### Full-Width Field

```blade
<div class="form-grid">
    <x-form-field label="Nama" name="name" required />
    <x-form-field label="E-mel" name="email" type="email" required />
    
    <div class="form-grid-full">
        <x-form-field label="Alamat" name="address" type="textarea" required />
    </div>
</div>
```

---

## Multi-Step Forms

### Stepper Component

```blade
<x-form-stepper
    :steps="[
        __('Maklumat Hubungan'),
        __('Perincian Isu'),
        __('Lampiran'),
        __('Pengesahan')
    ]"
    :currentStep="$currentStep"
/>

<form wire:submit="nextStep" class="form-container">
    @if($currentStep === 1)
        {{-- Step 1 fields --}}
    @elseif($currentStep === 2)
        {{-- Step 2 fields --}}
    @endif

    <div class="flex justify-between mt-6">
        @if($currentStep > 1)
            <button type="button" wire:click="previousStep" class="form-btn-secondary">
                {{ __('common.back') }}
            </button>
        @endif

        <button type="submit" class="form-btn-primary ml-auto">
            {{ $currentStep < 4 ? __('common.next') : __('common.submit') }}
        </button>
    </div>
</form>
```

---

## Buttons

### Primary Button

```blade
<button type="submit" class="form-btn-primary">
    {{ __('common.submit') }}
</button>
```

### Secondary Button

```blade
<button type="button" class="form-btn-secondary">
    {{ __('common.cancel') }}
</button>
```

### Loading State

```blade
<button type="submit" wire:loading.attr="disabled" class="form-btn-primary">
    <span wire:loading.remove>{{ __('common.submit') }}</span>
    <span wire:loading class="flex items-center">
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        {{ __('common.submitting') }}
    </span>
</button>
```

---

## Validation & Errors

### Livewire Validation

```php
// Component
protected $rules = [
    'full_name' => 'required|string|max:255',
    'email' => 'required|email|ends_with:@motac.gov.my',
    'phone' => 'required|regex:/^01[0-9]{8,9}$/',
];

public function submit()
{
    $this->validate();
    // Process form...
}
```

### Display Errors

```blade
<x-form-field
    label="E-mel"
    name="email"
    type="email"
    required
    :error="$errors->first('email')"
/>
```

---

## Accessibility Checklist

- [x] All inputs have associated labels
- [x] Required fields marked with `*` and `aria-required`
- [x] Error messages linked with `aria-describedby`
- [x] Focus indicators visible (4px ring)
- [x] Touch targets ≥ 44x44px
- [x] Color contrast ≥ 4.5:1
- [x] Keyboard navigation works
- [x] Screen reader compatible

---

## CSS Classes Reference

| Class | Purpose | Example |
|-------|---------|---------|
| `.form-container` | Form wrapper | `<form class="form-container">` |
| `.form-grid` | Two-column layout | `<div class="form-grid">` |
| `.form-field` | Field wrapper | `<div class="form-field">` |
| `.form-label` | Input label | `<label class="form-label">` |
| `.form-input` | Text input | `<input class="form-input">` |
| `.form-select` | Dropdown | `<select class="form-select">` |
| `.form-textarea` | Textarea | `<textarea class="form-textarea">` |
| `.form-error` | Error message | `<p class="form-error">` |
| `.form-helper` | Helper text | `<p class="form-helper">` |
| `.form-btn-primary` | Primary button | `<button class="form-btn-primary">` |
| `.form-btn-secondary` | Secondary button | `<button class="form-btn-secondary">` |

---

## Common Patterns

### Checkbox Group

```blade
<fieldset class="form-field">
    <legend class="form-label">Pilih Kategori</legend>
    <div class="space-y-2">
        @foreach($categories as $category)
            <label class="flex items-center gap-2">
                <input
                    type="checkbox"
                    name="categories[]"
                    value="{{ $category->id }}"
                    class="form-checkbox"
                />
                <span class="text-sm">{{ $category->name }}</span>
            </label>
        @endforeach
    </div>
</fieldset>
```

### Radio Group

```blade
<fieldset class="form-field">
    <legend class="form-label form-label-required">Keutamaan</legend>
    <div class="space-y-2">
        @foreach(['low' => 'Rendah', 'medium' => 'Sederhana', 'high' => 'Tinggi'] as $value => $label)
            <label class="flex items-center gap-2">
                <input
                    type="radio"
                    name="priority"
                    value="{{ $value }}"
                    class="form-radio"
                    required
                />
                <span class="text-sm">{{ $label }}</span>
            </label>
        @endforeach
    </div>
</fieldset>
```

---

## Testing

### Manual Testing

```bash
# Run dev server
npm run dev

# Test form at
http://localhost:8000/helpdesk/create
http://localhost:8000/loan/create
```

### Accessibility Testing

```bash
# Lighthouse audit
npx lighthouse http://localhost:8000/helpdesk/create --only-categories=accessibility

# Playwright accessibility tests
npm run test:accessibility
```

---

## Troubleshooting

### Issue: Dark input backgrounds

**Solution**: Ensure `forms.css` is imported in `app.css`

### Issue: Focus ring not visible

**Solution**: Check that global focus styles in `app.css` are not overridden

### Issue: Buttons too small on mobile

**Solution**: Use `.form-btn-primary` or `.form-btn-secondary` classes

---

## Support

- **Documentation**: `docs/frontend/UI_CONSISTENCY_FIXES.md`
- **Style Guide**: `docs/D14_UI_UX_STYLE_GUIDE.md`
- **Design Guide**: `docs/D12_UI_UX_DESIGN_GUIDE.md`

---

**Last Updated**: 2025-01-06  
**Maintained By**: Pasukan BPM MOTAC
