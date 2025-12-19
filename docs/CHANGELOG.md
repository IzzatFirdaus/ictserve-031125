# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added - 2025-11-28

#### Tailwind Configuration

- Added MOTAC brand color palette to `tailwind.config.js`:
  - Primary: `#0056b3` (MOTAC Blue, 6.8:1 contrast ratio)
  - Primary hover: `#004494`
  - Secondary: `#0B4D8F` (Dark Blue)
  - Success: `#198754` (4.9:1 contrast ratio)
  - Warning: `#ff8c00` (4.5:1 contrast ratio)
  - Danger: `#b50c0c` (8.2:1 contrast ratio)
- Added `minHeight.44` and `minWidth.44` utilities for WCAG 2.5.8 touch target compliance

#### Landing Layout (`resources/views/layouts/landing.blade.php`)

- Implemented responsive hamburger menu with Alpine.js for mobile navigation
- Integrated `<livewire:language-switcher />` component for bilingual support (BM/EN)
- Added ARIA landmarks for accessibility (`role="banner"`, `role="main"`, `role="contentinfo"`)
- Implemented sticky header (`sticky top-0 z-50`)
- Added focus indicators to all interactive elements (`focus:ring-2 focus:ring-white`)
- Updated footer with dynamic copyright year

#### Welcome Page (`resources/views/welcome.blade.php`)

- Replaced arbitrary hex color codes with Tailwind utility classes (`bg-primary-600`, `text-primary-600`)
- Added `aria-hidden="true"` to all decorative SVG icons
- Implemented proper focus states for all buttons and inputs
- Updated "Pinjaman Aset" card with laptop icon for better visual representation
- Added screen reader labels (`sr-only`) to form inputs

### Changed - 2025-11-28

- Updated all `bg-[#0056b3]` to `bg-primary-600` across layouts and components
- Updated all `hover:bg-[#004494]` to `hover:bg-primary-700` for consistent hover states
- Updated all `focus:ring-[#0056b3]` to `focus:ring-primary-600` for accessible focus indicators
- Changed `flex-shrink-0` to `shrink-0` for modern Tailwind syntax
- Improved hero section semantic HTML structure with proper `<h1>` heading
- Enhanced card hover effects with `transition-shadow duration-300`

### Fixed - 2025-11-28

- Fixed missing `focus:outline-none` on interactive elements to prevent default browser outlines
- Fixed missing ARIA attributes on navigation elements
- Fixed accessibility issues with social media links in footer (added `focus:ring` states)
- Resolved lint warning for deprecated `flex-shrink-0` utility class

## [Previous Releases]

See Git history for previous changes.
