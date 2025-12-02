# Frontend Framework Specifications (v3.5.0)

> **Context:** Technical frontend specifications for the ICTServe system. Use this to generate Blade templates, Livewire components, and Tailwind styles.

## 1. Technology Stack & Configuration

* **Stack:** Laravel 12 + Livewire 3.7 + Volt 1.10 + Tailwind 4.1.
* **Real-time:** Laravel Reverb + Echo.
* **Build Tool:** Vite 7.0.

### Tailwind v4 Configuration (MyDS Aligned)
Use these exact CSS variables in `resources/css/app.css` for theming:

```css
@theme {
    /* Primary Palette */
    --color-primary-50: oklch(0.97 0.02 250);
    --color-primary-500: oklch(0.55 0.15 250);
    --color-primary-600: oklch(0.48 0.15 250); /* Main Action Color */
    
    /* Semantic Colors */
    --color-success: oklch(0.55 0.15 145);
    --color-warning: oklch(0.65 0.15 85);
    --color-danger: oklch(0.45 0.2 25);

    /* Spacing System */
    --space-4: 16px;
    --space-6: 24px;
}
```

## 2. Component Implementation Patterns

### Livewire Class Component (Standard)

Use attributes for validation and layout:

```php
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.guest')] 
class TicketForm extends Component {
    #[Validate('required|string|max:255')]
    public string $subject = '';

    public function submit() {
        // Logic...
    }
}
```

### Volt Single-File Component (Reactive)

Use for smaller, interactive widgets like Search or Filters:

```php
<?php
use function Livewire\Volt\{state, computed};
state(['search' => '']);
$results = computed(fn () => Model::where('name', 'like', "%$this->search%")->get());
?>
<div>
    <input wire:model.live.debounce.300ms="search" ...>
    </div>
```

## 3\. Specific UI Modules (v3.5.0)

### Self-Registration Form

* **Location:** `resources/views/auth/register.blade.php`.
* **Fields:** Name, Email (Regex: `.*@motac\.gov\.my$`), Phone, Department (Select), Grade.
* **Logic:** Must enforce `@motac.gov.my` domain validation on the frontend.

### Account Linking Prompt

* **Trigger:** First login after registration if historical guest submissions match the email.
* **UI:** Modal or Banner on Dashboard.
* **Actions:** "Link Submissions" or "Dismiss".

### Notification Bell

* **Technology:** Laravel Echo (Reverb).
* **Channel:** `private-user.{id}`.
* **UI:** Dropdown with "Mark as Read" functionality.

## 4\. Accessibility Standards (WCAG 2.2 AA)

* **Focus Indicators:** All interactive elements must have a visible `3px outline` on focus.
* **Loading States:** Buttons must use `wire:loading` to disable interactions and show a spinner.
* **Error Messages:** Must use `aria-describedby` linking the input to the error text.
