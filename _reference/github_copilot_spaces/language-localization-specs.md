# Language & Localization Specifications (v3.5.0)

> **Context:** Technical specifications for the ICTServe Bilingual System (MS/EN). Use this to generate UI labels, validation messages, and notification templates.

## 1. Core Language Rules

* **Primary Language:** Bahasa Melayu (`ms`).
* **Secondary Language:** English (`en`).
* **UI Label Pattern:** MUST use the format `Malay Text <span lang="en">(English Text)</span>` for all labels and instructions.
* **Validation Messages:** Default to Bahasa Melayu. English translations provided as help text where necessary.

## 2. Locale Resolution Logic (Hybrid)
The system determines the locale based on the following priority order:

1. **User Profile (DB):** If `Auth::check()`, use `users.locale`.
2. **Session:** Check `session('locale')`.
3. **Cookie:** Check `ictserve_locale` (12-month expiry for Guests).
4. **Browser:** Auto-detect `Accept-Language` header.
5. **Fallback:** Default to `config('app.locale')` ('ms').

## 3. UI Implementation Patterns

### Standard Form Input

```blade
<label for="full_name">
    Nama Penuh <span lang="en">(Full Name)</span> *
</label>
<input type="text" id="full_name" name="full_name" required>
<div class="invalid-feedback">
    Medan ini wajib diisi. <span lang="en">(This field is required.)</span>
</div>
````

### Self-Registration (v3.5.0)

Specific labels for the `@motac.gov.my` restriction:

```blade
<small class="form-text">
    Hanya e-mel @motac.gov.my dibenarkan
    <span lang="en">(Only @motac.gov.my emails allowed)</span>
</small>
```

### Language Switcher Component

* **Location:** Navbar/Header.
* **Tech:** Livewire Component.
* **Accessibility:** Must use `aria-label`, `role="navigation"`, and keyboard navigation support.

## 4\. Notification Standards (Bilingual)

### Email Templates

Emails must contain both languages clearly separated or inline.
**Example Subject:** `Tiket Diterima / Ticket Received`
**Example Body:**

```html
<p>Permohonan anda telah DILULUSKAN.</p>
<p lang="en" class="text-muted">(Your application has been APPROVED.)</p>
```

### Real-Time (Laravel Reverb)

WebSocket events must respect the recipient's stored locale preference if available, or default to bilingual payloads.

## 5\. Accessibility Compliance (WCAG 2.2 AA)

* **HTML Lang Attribute:** `<html lang="ms">` is default. Use `lang="en"` on spans for English translations to assist screen readers.
* **Contrast:** Ensure text meets 4.5:1 ratio.
* **Focus:** All language toggles must have visible focus indicators.
