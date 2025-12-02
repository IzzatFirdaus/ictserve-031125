# UI/UX Design System & Standards (v3.5.0)

> **Context:** Visual design and frontend guidelines for the ICTServe Hybrid System. Use this to generate Blade templates, Tailwind classes, and Livewire components.

## 1. Dual Layout Architecture (Hybrid)
The system uses two distinct layouts based on authentication status:

### `app.blade.php` (Authenticated)

* **Target:** Staff, Admin, Superuser.
* **Structure:** Sidebar navigation, User Menu, Dashboard access.
* **Features:** Notification Bell, Profile Settings, "My Submissions" history.

### `guest.blade.php` (Public)

* **Target:** Anonymous Users (Guest access).
* **Structure:** Simple Header (Logo + Lang Toggle), Centered Content.
* **Features:** "Submit Ticket", "Apply Loan", "Check Status" (Token-based).

## 2. Color Palette (WCAG 2.2 AA Compliant)
**Use these exact Hex codes for Tailwind configuration:**

* **Primary Blue:** `#0056B3` (Buttons, Links) - Contrast 7.2:1
* **Secondary Blue:** `#0B4D8F` (Focus Rings, Active States) - Contrast 8.1:1
* **Success Green:** `#1B7C54` (Status: Resolved, Approved)
* **Warning Gold:** `#CC7700` (Status: Pending)
* **Danger Red:** `#B3002D` (Errors, Delete actions)

## 3. Accessibility Standards (Strict)

* **Focus Indicators:** All interactive elements must have a visible `3px outline` in `#0B4D8F` on focus.
* **Touch Targets:** Minimum `44x44px` for mobile interactive elements.
* **Forms:** All inputs must have associated `<label>` elements or `aria-label` attributes.
* **Contrast:** Text must maintain a minimum 4.5:1 contrast ratio against the background.

## 4. Component Implementation Guide

### Status Badges
**Do not use color alone.** Always pair color with text/icon.

```blade
<span class="bg-green-100 text-green-800 ...">
    <x-icon name="check-circle" /> Resolved
</span>
````

### Loading States

Use Livewire's `wire:loading` to provide visual feedback.

```blade
<button wire:loading.attr="disabled">
    <span wire:loading.remove>Submit</span>
    <span wire:loading>Processing...</span>
</button>
```

### Language Switcher

* **Location:** Header/Navbar.
* **Function:** Toggles between `ms` (Bahasa Melayu) and `en` (English).
* **Persistence:** Session \> Cookie \> Browser Preference.

## 5\. Responsive Grid (MyDS Aligned)

* **Desktop:** 12 Columns (Max width 1280px).
* **Tablet:** 8 Columns.
* **Mobile:** 4 Columns (Stacked).
