# Navigation & Layout Comprehensive Redesign

**Version**: 3.6.0 | **Date**: 2025-12-09 | **Priority**: HIGH  
**Scope**: All navigation bars and layouts across ICTServe

## Overview

Complete redesign of navigation and layouts to achieve:

1. **Restructured menu ms** - Logical hierarchy, role-based visibility
2. **Full responsive behavior** - 12-8-4 grid system (D14 §7.4)
3. **WCAG 2.2 AA compliance** - Keyboard nav, focus, ARIA
4. **Dark mode support** - Light default (v3.6.0 policy)
5. **MyDS alignment** - Spacing, typography, colors, shadows

**References**:

- `docs/frontend/00-PREPLANNING-ictserve-3.6.0.md` §3 (Page-by-Page Specs)
- D12 §5-6 (Layout & Navigation)
- D13 §4 (Layout Components)
- D14 §6-7 (Navigation & Responsive Design)

---

## 1. LANDING PAGE NAVIGATION

**File**: `resources/views/layouts/landing.blade.php`

### Current Issues

- Menu items not properly structured
- Missing responsive hamburger menu
- No theme switcher
- Language switcher present (remove per v3.6.0)

### Desktop Navigation (≥1024px)

**Structure**:

```text
[Logo + Brand] [Nav Menu: Laman|Perkhidmatan|Direktori|Hubungi|FAQ] [Theme] [Log Masuk|Daftar]
```

**Implementation**:

- Sticky header: `sticky top-0 z-50`
- Background: `bg-primary-500 dark:bg-primary-600`
- Height: `h-16` (64px)
- Max width: `max-w-7xl mx-auto`
- Padding: `px-6` (24px)

**Menu Items**:

1. Laman Utama → `route('welcome')`
2. Perkhidmatan → Dropdown (Helpdesk, Pinjaman Aset)
3. Direktori ICT → `route('staff-directory')`
4. Hubungi Kami → `route('contact')`
5. Soalan Lazim → `route('faq')`

**Actions**:

- Theme Switcher (44×44px, sun/moon icon)
- Log Masuk button (white bg, primary text)
- Daftar button (white border, white text)

### Mobile/Tablet (<1024px)

**Hamburger Menu**:

- Icon: 44×44px touch target
- Position: Top-right
- Animation: Slide-in from right
- Overlay: Semi-transparent backdrop
- Close: X icon (44×44px)
- Focus trap when open

**Menu Layout**:

- Vertical stack
- Full-width items
- Proper spacing (space-4)
- Dividers between sections

### Accessibility

- `aria-label="Navigasi Utama"` on nav
- `aria-expanded` on hamburger
- `aria-current="page"` on active link
- Focus indicators (3px outline, 2px offset)
- Keyboard: Tab, Enter, Escape

---

## 2. AUTHENTICATION PAGES NAVIGATION

**Files**: `resources/views/layouts/guest.blade.php`

### Current Issues

- Dark background (should be light per v3.6.0)
- No theme switcher
- Minimal navigation

### Redesign

**Header**:

- Light background: `bg-white dark:bg-gray-900`
- Logo centered or left-aligned
- Theme switcher: Top-right (44×44px)
- Back to home link: Optional

**Form Container**:

- White card: `bg-white dark:bg-gray-800`
- Shadow: `shadow-card`
- Max width: `max-w-md`
- Centered: `mx-auto`
- Padding: `p-6` (24px)

**Navigation Links**:

- "Kembali ke Laman Utama" link
- "Belum ada akaun? Daftar" / "Sudah ada akaun? Log Masuk"
- Proper focus states

---

## 3. PUBLIC INFORMATION PAGES NAVIGATION

**Files**: FAQ, Directory, Contact, Accessibility pages

### Header Structure

Match landing page header:

- Same sticky header
- Same menu items
- Same theme switcher
- Same auth buttons (if not logged in)

### Page Title Section

**Structure**:

```text
[Blue Background]
  [Breadcrumb: Laman Utama > Current Page]
  [Page Title (H1, 32px)]
  [Description (16px)]
```

**Implementation**:

- Background: `bg-primary-500 dark:bg-primary-600`
- Text: White
- Padding: `py-8` (32px)
- Breadcrumb: Chevron separators, aria-current

### Content Area

**Layout**:

- Max width: `max-w-4xl mx-auto` (740px for readability)
- Padding: `px-6 py-8`
- Background: `bg-white dark:bg-gray-900`

**Sidebar** (optional):

- Sticky navigation for long pages
- Table of contents
- Quick links

---

## 4. GUEST FORMS NAVIGATION

**Files**: Helpdesk ticket form, Loan application form

### Form Header

**Structure**:

```text
[MOTAC Logo] [Form Title] [ISO Code] [Theme Switcher]
```

**Implementation**:

- Background: `bg-primary-500 dark:bg-primary-600`
- Height: `h-20` (80px)
- Logo: 48px height
- Title: 24px Poppins bold
- ISO code: 14px gray-200

### Step Indicator (Multi-step forms)

**Structure**:

```text
[1 Maklumat] → [2 Aset] → [3 Tarikh] → [4 Tujuan] → [5 Pengesahan]
```

**Implementation**:

- Numbered badges
- Active: Primary color
- Completed: Green checkmark
- Future: Gray
- Connector lines between steps

### Form Navigation

**Buttons**:

- Kembali (Secondary, left)
- Seterusnya / Hantar (Primary, right)
- Simpan Draf (Ghost, center)

**Placement**:

- Bottom of form
- Sticky on mobile
- Proper spacing (space-4)

---

## 5. AUTHENTICATED PORTAL NAVIGATION

**File**: `resources/views/layouts/app.blade.php`

### Current Issues

- Light mode not enforced as default
- Sidebar needs restructuring
- Missing role-based menu items

### Header Structure

**Desktop (≥1024px)**:

```text
[Logo + Brand] [Nav: Dashboard|Helpdesk|Loans|Approvals*] [Notifications] [Theme] [User Menu]
```

**Implementation**:

- Background: `bg-white dark:bg-gray-900`
- Border: `border-b border-gray-200 dark:border-gray-700`
- Height: `h-16` (64px)
- Shadow: `shadow-sm`

**Navigation Items**:

1. Dashboard → `route('dashboard')`
2. Helpdesk → Dropdown (My Tickets, New Ticket, All Tickets*)
3. Pinjaman Aset → Dropdown (My Loans, New Application, All Loans*)
4. Kelulusan → `route('approvals')` (Grade 41+ only)
5. Panel Admin → `route('filament.admin.pages.dashboard')` (Admin/Superuser)

**Right Actions**:

- Notifications bell (unread badge)
- Theme switcher (44×44px)
- User menu dropdown (Name, Profile, Settings, Logout)

### Sidebar Navigation (Optional)

**Desktop**:

- Width: 256px expanded, 64px collapsed
- Toggle button
- Icons + labels (expanded)
- Icons only (collapsed)

**Mobile**:

- Slide-in from left
- Overlay backdrop
- Close button
- Full menu items

**Menu Structure**:

```text
Dashboard
---
Helpdesk
  - Tiket Saya
  - Tiket Baharu
  - Semua Tiket (Admin)
---
Pinjaman Aset
  - Permohonan Saya
  - Permohonan Baharu
  - Semua Permohonan (Admin)
---
Kelulusan (Grade 41+)
---
Panel Admin (Admin/Superuser)
---
Tetapan
Log Keluar
```

### Mobile Navigation (<1024px)

**Bottom Tab Bar** (Alternative to sidebar):

```text
[Dashboard] [Helpdesk] [Loans] [More]
```

**Implementation**:

- Fixed bottom
- 4 main tabs
- Active indicator
- Icon + label
- 44×44px touch targets

---

## 6. RESPONSIVE BREAKPOINTS

**System**: 12-8-4 Grid (D14 §7.4)

| Device | Breakpoint | Columns | Gap | Edge Padding | Max Width |
|--------|-----------|---------|-----|--------------|-----------|
| Mobile | <768px | 4 | 18px | 18px | Full |
| Tablet | 768-1023px | 8 | 24px | 24px | 768-1023px |
| Desktop | ≥1024px | 12 | 24px | 24px | 1280px |

**Tailwind Classes**:

- Mobile: Default (no prefix)
- Tablet: `md:` prefix
- Desktop: `lg:` prefix

---

## 7. ACCESSIBILITY REQUIREMENTS

### Keyboard Navigation

**Keys**:

- Tab / Shift+Tab: Navigate
- Enter / Space: Activate
- Escape: Close menus/modals
- Arrow keys: Navigate dropdowns

**Focus Indicators**:

- Width: 3px solid outline
- Offset: 2px
- Color: `ring-primary-500` (light), `ring-white` (dark)
- Contrast: 3:1 minimum

### ARIA Attributes

**Navigation**:

- `<nav aria-label="Navigasi Utama">`
- `<a aria-current="page">` for active
- `<button aria-expanded="false" aria-haspopup="true">` for dropdowns

**Landmarks**:

- `<header>`, `<nav>`, `<main>`, `<footer>`
- `<aside>` for sidebars
- `<section aria-labelledby="id">` for sections

### Touch Targets

**Minimum Size**: 44×44px (WCAG 2.5.5)

**Implementation**:

- Buttons: `min-h-11 min-w-11` (11 * 4px = 44px)
- Links: Padding to reach 44px
- Icons: 24px icon + 10px padding each side

---

## 8. DARK MODE IMPLEMENTATION

### Color Scheme

**Light Mode** (Default):

- Background: `bg-white`
- Text: `text-gray-900`
- Borders: `border-gray-300`
- Primary: `bg-primary-600`

**Dark Mode** (Opt-in):

- Background: `dark:bg-gray-900`
- Text: `dark:text-gray-100`
- Borders: `dark:border-gray-700`
- Primary: `dark:bg-primary-600`

### Transitions

**Duration**: 200ms (--duration-short)
**Easing**: ease-out (--motion-easeout)

**Implementation**:

```css
.theme-transition {
  transition-property: background-color, border-color, color;
  transition-duration: 200ms;
  transition-timing-function: cubic-bezier(0, 0, 0.58, 1);
}
```

### FOUT Prevention

**Inline Script** (in `<head>`):

```javascript
(function() {
  const theme = localStorage.getItem('theme') || 'light';
  if (theme === 'dark') {
    document.documentElement.classList.add('dark');
  }
})();
```

---

## 9. IMPLEMENTATION CHECKLIST

### Phase 1: Landing Page

- [x] Remove language switcher (v3.6.0 - BM only)
- [x] Add theme switcher (top-right) - using `<livewire:components.theme-toggle />`
- [x] Restructure menu items (add Perkhidmatan dropdown) - 2025-12-09
- [x] Implement hamburger menu (mobile) with Perkhidmatan section
- [x] Add proper ARIA labels
- [x] Remove Kebolehcapaian from main nav (moved to footer only)
- [ ] Test keyboard navigation
- [x] Test dark mode

### Phase 2: Authentication Pages

- [x] Change background to light - already implemented
- [x] Add theme switcher - using `<livewire:components.theme-toggle />`
- [x] Update form container styling - dark mode classes present
- [x] Add navigation links - contact support link present
- [ ] Test responsive behavior

### Phase 3: Public Pages (front.blade.php)

- [x] Add Perkhidmatan dropdown - 2025-12-09
- [x] Update mobile menu with Perkhidmatan section
- [x] Add footer links (Kebolehcapaian, Dasar Privasi)
- [x] Improve touch targets (min-h-11 min-w-11)
- [x] Add proper ARIA labels
- [ ] Test accessibility

### Phase 4: Guest Forms

- [ ] Add form header with ISO code
- [ ] Implement step indicator
- [ ] Add form navigation buttons
- [ ] Add theme switcher
- [ ] Test multi-step flow

### Phase 5: Portal Navigation (auth-header, sidebar-navigation)

- [x] Enforce light mode default
- [x] Add dark mode classes to header
- [x] Add dark mode classes to sidebar
- [x] Update notification dropdown with dark mode
- [x] Update user menu dropdown with dark mode
- [x] Fix touch target classes (min-h-11)
- [ ] Add role-based visibility
- [ ] Test all user roles

### Phase 6: Testing & QA

- [ ] Keyboard navigation (all pages)
- [ ] Screen reader testing (NVDA/JAWS)
- [ ] Touch target verification (44×44px)
- [ ] Color contrast audit (4.5:1 text, 3:1 UI)
- [ ] Responsive testing (mobile, tablet, desktop)
- [ ] Dark mode verification
- [ ] Cross-browser testing

---

## 10. RELATED TASKS

**Figma UI Redesign Spec**:

- Phase 5: Navigation and Layout Components (tasks 5.1-5.3)
- Phase 18: v3.6.0 Policy Implementation (task 18.1-18.4)
- Phase 19: Navigation Link Fixes (task 19.1)

**ICTServe Update v3 Spec**:

- Phase 11: Accessibility and Localization (tasks 39.1-40.3)

---

**Status**: Ready for implementation  
**Next Steps**: Begin Phase 1 (Landing Page) implementation
