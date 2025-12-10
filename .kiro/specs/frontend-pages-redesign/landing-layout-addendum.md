# Landing Layout D00-D17 Compliance - Requirements Addendum

## Introduction

This addendum extends the Unified Frontend Pages Redesign specification to ensure the landing layout (`resources/views/layouts/landing.blade.php`) fully complies with D00-D17 documentation standards. The landing layout serves as the primary public-facing template for the ICTServe welcome page and must implement MOTAC branding, WCAG 2.2 AA accessibility, MyDS Design System v2025.2 alignment, and optional dark mode support with light mode as the immutable default.

## Glossary

- **Landing_Layout**: Primary public-facing Blade layout template for guest pages (welcome, FAQ, contact, accessibility)
- **MyDS_Design_System**: Malaysian Government Design System v2025.2 with semantic tokens, spacing, typography, shadows, radius
- **FOUT_Prevention**: Flash of Unstyled Text prevention using inline JavaScript in `<head>` to apply saved theme before render
- **Theme_Transition**: CSS class enabling smooth 200ms color transitions when switching between light and dark modes
- **Sticky_Header**: Fixed-position header that remains visible during page scroll with z-index 50
- **MOTAC_Branding**: Ministry of Tourism, Arts and Culture Malaysia visual identity including Jata Negara, MOTAC logo, and official colors

## Requirements

### Requirement L1: Layout Structure and Semantic HTML

**User Story:** As a user accessing ICTServe public pages, I want a well-structured layout with proper semantic HTML landmarks, so that I can navigate efficiently using assistive technologies and understand the page structure.

#### Acceptance Criteria

1. THE Landing_Layout SHALL implement semantic HTML5 structure with `<header role="banner">`, `<nav aria-label>`, `<main role="main">`, and `<footer role="contentinfo">` landmarks
2. THE Landing_Layout SHALL include skip links component (`<x-accessibility.skip-links />`) as the first focusable element after `<body>` opening tag
3. THE Landing_Layout SHALL set document language to Bahasa Melayu (`lang="ms"`) on the `<html>` element per v3.6.0 BM-only policy
4. THE Landing_Layout SHALL include proper meta tags: charset (utf-8), viewport (width=device-width, initial-scale=1), csrf-token, theme-color (#0056B3), and description
5. THE Landing_Layout SHALL load fonts (Poppins for headings, Inter for body) with `font-display: swap` and preconnect hints for performance

### Requirement L2: Header Component with MOTAC Branding

**User Story:** As a user viewing the ICTServe landing page, I want a professional header with MOTAC branding and clear navigation, so that I can identify the government service and access key features easily.

#### Acceptance Criteria

1. THE Landing_Layout header SHALL implement Sticky_Header behavior with `sticky top-0 z-50` positioning and `bg-primary-500 dark:bg-primary-600` background colors
2. THE Landing_Layout header SHALL display MOTAC branding with logo (48px height), "ICTServe" text (Poppins bold, 20px), and "MOTAC BPM" subtitle (12px uppercase tracking-wider)
3. THE Landing_Layout header SHALL provide desktop navigation with links to "Aduan ICT", "Pinjaman Aset", and "Semak Status" with 44×44px minimum touch targets
4. THE Landing_Layout header SHALL include theme toggle component (`<livewire:components.theme-toggle />`) positioned before authentication buttons
5. THE Landing_Layout header SHALL NOT include language switcher per v3.6.0 BM-only policy (removed from all layouts)
6. THE Landing_Layout header SHALL provide authentication buttons: "Daftar" (primary white button) and "Log Masuk" (outline white button) with proper focus indicators

### Requirement L3: Mobile Navigation and Responsive Design

**User Story:** As a mobile user accessing ICTServe, I want a responsive navigation menu that works well on small screens, so that I can access all features without difficulty on my device.

#### Acceptance Criteria

1. WHEN viewport width is below 768px (md breakpoint), THE Landing_Layout SHALL display hamburger menu button with 44×44px touch target and proper ARIA attributes (`aria-controls`, `aria-expanded`)
2. WHEN mobile menu is opened, THE Landing_Layout SHALL display full-width navigation panel with all navigation links, theme toggle, and authentication buttons
3. THE Landing_Layout mobile menu SHALL implement Alpine.js toggle with `x-show`, `x-cloak`, and smooth transitions
4. THE Landing_Layout mobile menu items SHALL have 44×44px minimum touch targets with `min-h-11` class
5. THE Landing_Layout SHALL hide desktop navigation (`hidden md:flex`) and show mobile menu button (`flex md:hidden`) based on viewport

### Requirement L4: Theme System Integration

**User Story:** As a user with visual preferences, I want the landing page to support optional dark mode while defaulting to light mode, so that I can choose my preferred viewing experience.

#### Acceptance Criteria

1. THE Landing_Layout SHALL implement light mode as immutable default with NO `class="dark"` on `<html>` element by default
2. THE Landing_Layout SHALL include FOUT_Prevention script (`<x-theme-init-script />`) in `<head>` before CSS/JS assets
3. THE Landing_Layout SHALL apply `theme-transition` class to `<html>`, `<body>`, and major sections for smooth 200ms color transitions
4. THE Landing_Layout SHALL implement dark mode support using Tailwind `dark:` variants for all color utilities
5. THE Landing_Layout dark mode colors SHALL maintain WCAG 2.2 AA compliance: text (gray-100 on gray-900 = 7:1), borders (gray-700 = 3:1+), focus rings (white = 7:1)

### Requirement L5: Footer Component with Government Compliance

**User Story:** As a user viewing the ICTServe footer, I want comprehensive contact information, service links, and government compliance indicators, so that I can find additional resources and verify the official nature of the service.

#### Acceptance Criteria

1. THE Landing_Layout footer SHALL implement `bg-gray-800 dark:bg-gray-900` background with `text-gray-300` text color
2. THE Landing_Layout footer SHALL display MOTAC branding with Jata Negara image, ministry name, and BPM division name
3. THE Landing_Layout footer SHALL provide contact information: address (No. 2, Menara 1, Jalan P5/6, Presint 5, 62200 PUTRAJAYA), phone (03 8000 8000), fax (03 8891 7100)
4. THE Landing_Layout footer SHALL include service links (Aduan ICT, Pinjaman Aset, Semak Status) and information links (Direktori, FAQ, Hubungi Kami, Kebolehcapaian, Dasar Privasi)
5. THE Landing_Layout footer SHALL display social media links (Facebook, Twitter, Instagram, TikTok) with 44×44px touch targets and proper `aria-label` attributes
6. THE Landing_Layout footer SHALL include system status indicator with animated ping effect and "Sistem beroperasi normal" text
7. THE Landing_Layout footer SHALL display copyright notice and WCAG 2.2 compliance statement: "Mematuhi WCAG 2.2 Tahap AA"

### Requirement L6: Accessibility and Focus Management

**User Story:** As a user with accessibility needs, I want the landing layout to provide full keyboard navigation and screen reader support, so that I can use the service regardless of my abilities.

#### Acceptance Criteria

1. THE Landing_Layout SHALL implement visible focus indicators with 3px outline, 2px offset, and 3:1 minimum contrast ratio
2. THE Landing_Layout focus indicators SHALL use `focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-500` for header elements
3. THE Landing_Layout SHALL provide logical tab order following visual layout: skip links → logo → navigation → theme toggle → auth buttons → main content → footer
4. THE Landing_Layout interactive elements SHALL have proper ARIA labels in Bahasa Melayu (e.g., `aria-label="Buka menu utama"`, `aria-label="Facebook MyMOTAC"`)
5. THE Landing_Layout SHALL support `prefers-reduced-motion: reduce` by disabling animations and transitions for users who prefer reduced motion

### Requirement L7: Performance and Asset Loading

**User Story:** As a user on various network conditions, I want the landing page to load quickly and efficiently, so that I can access services without long wait times.

#### Acceptance Criteria

1. THE Landing_Layout SHALL load CSS and JavaScript assets using Vite with `@vite(['resources/css/app.css', 'resources/js/app.js'])`
2. THE Landing_Layout SHALL include Livewire styles (`@livewireStyles`) in `<head>` and scripts (`@livewireScripts`) before `</body>`
3. THE Landing_Layout SHALL implement font preconnect hints for Google Fonts with `rel="preconnect"` and `crossorigin` attributes
4. THE Landing_Layout images SHALL include explicit `width` and `height` attributes to prevent Cumulative Layout Shift (CLS)
5. THE Landing_Layout non-critical images SHALL use `loading="lazy"` attribute for deferred loading

## Technical Implementation Notes

### Color Palette (D14 §4.1.1)

```css
/* Light Mode */
--color-primary-500: #0056B3;  /* Header background, 7.2:1 contrast */
--color-gray-50: #f9fafb;      /* Page background */
--color-gray-800: #1f2937;     /* Footer background */
--color-gray-900: #111827;     /* Dark text */

/* Dark Mode */
--color-primary-600: #004494;  /* Header background dark */
--color-gray-900: #111827;     /* Page background dark */
--color-gray-100: #f3f4f6;     /* Light text */
--color-gray-700: #374151;     /* Borders dark */
```

### Typography (D13 §2.4)

```css
/* Headings */
font-family: 'Poppins', sans-serif;
font-weight: 600-700;

/* Body */
font-family: 'Inter', sans-serif;
font-weight: 400-500;
```

### Spacing (D13 §2.6)

```css
/* Header height */
h-20 (80px)

/* Container max-width */
max-w-7xl (1280px)

/* Padding */
px-4 sm:px-6 lg:px-8
```

### Touch Targets (WCAG 2.5.8)

```css
/* Minimum 44×44px */
min-h-11 min-w-11 (44px × 44px)
```

## D00-D17 Traceability

| Requirement | D-Document Reference | Section |
|-------------|---------------------|---------|
| L1 | D12 UI/UX Design Guide | §5.1 Layout Structure |
| L2 | D14 UI/UX Style Guide | §3.1 Header Component |
| L3 | D13 Frontend Framework | §2.8-2.9 Responsive Design |
| L4 | D14 UI/UX Style Guide | §2.3 Theme System |
| L5 | D14 UI/UX Style Guide | §3.2 Footer Component |
| L6 | D12 UI/UX Design Guide | §9 WCAG Compliance |
| L7 | D11 Technical Design | §7 Performance |

## Success Criteria

The landing layout D00-D17 compliance will be considered successful when:

1. **Semantic Structure**: All HTML5 landmarks present and properly labeled
2. **MOTAC Branding**: Logo, colors, and typography match official guidelines
3. **Theme System**: Light default with functional dark mode toggle
4. **Accessibility**: 100% WCAG 2.2 Level AA compliance
5. **Responsive**: Works correctly on mobile (320px), tablet (768px), and desktop (1280px+)
6. **Performance**: LCP <2.5s, CLS <0.1, no FOUT
7. **BM-Only**: No language switcher, all text in Bahasa Melayu

---

**Document Version**: 1.0  
**Created**: 2025-12-10  
**Author**: Frontend Engineering Team  
**Status**: Ready for Review  
**Parent Spec**: frontend-pages-redesign
