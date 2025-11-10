# Updated Frontend - Design Document

## Introduction

This design document outlines the comprehensive architecture and implementation strategy for the major frontend UI/UX upgrade of the ICTServe system. The design combines frontend modernization with complete page redesign, leveraging Laravel 12.x, Livewire 3.x, Volt 1, Tailwind CSS 4.1, and Alpine.js 3.x to deliver a unified, accessible, and high-performance frontend experience across guest forms, authenticated portal, and admin interfaces.

## Architecture Overview

### System Architecture

The ICTServe frontend implements a three-tier hybrid architecture:

1. **Guest Layer**: Public forms without authentication (helpdesk tickets, asset loan applications)
2. **Portal Layer**: Authenticated staff interface (dashboard, submissions, approvals)
3. **Admin Layer**: Filament 4 administrative panel (system management, reporting)

### Technology Stack

- **Backend Framework**: Laravel 12.x with PHP 8.3+
- **UI Framework**: Livewire 3.x for server-driven reactive components
- **Single-File Components**: Volt 1 for simplified component development
- **CSS Framework**: Tailwind CSS 4.1 with JIT mode
- **JavaScript**: Alpine.js 3.x for client-side interactivity
- **Admin Panel**: Filament 4 for administrative interfaces

### Architectural Principles

1. **Mobile-First Responsive Design**: All interfaces optimized for 320px-1920px viewports
2. **WCAG 2.2 AA Compliance**: Full accessibility with 4.5:1 text contrast, 44×44px touch targets
3. **Performance Optimization**: Core Web Vitals targets (LCP <2.5s, FID <100ms, CLS <0.1)
4. **Component Reusability**: Unified component library across all layers
5. **Progressive Enhancement**: Base functionality without JavaScript, enhanced with Livewire/Alpine

## Component Library Design

### Component Organization

Components are organized into 8 categories:

1. **accessibility/**: Skip links, language switcher, ARIA live regions, focus trap
2. **data/**: Tables, pagination, search filters, statistics cards
3. **form/**: Input, select, textarea, checkbox, radio, file upload, validation
4. **layout/**: Guest layout, portal layout, header, footer, sidebar
5. **navigation/**: Main nav, breadcrumb, tab navigation, mobile menu
6. **responsive/**: Mobile cards, tablet grids, desktop layouts
7. **ui/**: Button, card, modal, alert, badge, dropdown, toast, spinner
8. **alpine/**: Dropdown pattern, modal pattern, accordion, tabs

### Component Metadata Standard

Each component includes standardized metadata for traceability and compliance tracking.

## Tailwind CSS 4.1 Design System

### WCAG-Compliant Color Palette

**Primary Colors**:

- Primary 500: #0056b3 (6.8:1 contrast ratio)
- Primary 600: #004494
- Primary 900: #002147

**Status Colors**:

- Success 500: #198754 (4.9:1 contrast)
- Warning 500: #ff8c00 (4.5:1 contrast)
- Danger 500: #b50c0c (8.2:1 contrast)

**Neutral Colors**:

- Gray 50-900 scale for backgrounds and text

### Typography System

- Font Family: Inter (sans-serif), JetBrains Mono (monospace)
- Scale: xs (0.75rem) to 3xl (1.875rem)
- Line Heights: Optimized for readability (1.5 for body text)

### Responsive Breakpoints

- sm: 640px (mobile landscape)
- md: 768px (tablet portrait)
- lg: 1024px (tablet landscape)
- xl: 1280px (desktop)
- 2xl: 1536px (large desktop)

### Configuration

Tailwind CSS 4.1 configured with:

- JIT mode for instant compilation
- Content scanning: resources/views/**/\*.blade.php, app/Livewire/**/_.php, app/Filament/\*\*/_.php
- Production optimization: <50KB gzipped
- Custom theme extensions for MOTAC branding

## Livewire 3.x Architecture

### OptimizedLivewireComponent Trait

A performance-focused trait providing:

- **Caching**: 5-minute default cache for computed properties
- **Lazy Loading**: Deferred component rendering with placeholders
- **Query Optimization**: Eager loading patterns to prevent N+1 queries
- **Computed Properties**: Cached expensive operations

### Component Patterns

**PHP 8 Attributes**:

- `#[Reactive]`: Real-time reactive properties
- `#[Computed]`: Cached computed properties
- `#[Lazy]`: Lazy-loaded components
- `#[Locked]`: Immutable properties
- `#[Session]`: Session-persisted properties

**Wire Directives**:

- `wire:model.live`: Real-time updates
- `wire:model.lazy`: Deferred updates for large text fields
- `wire:model.live.debounce.300ms`: Debounced search inputs
- `wire:loading`: Loading state indicators
- `wire:key`: Optimal DOM diffing for lists

### Event Handling

- Use `$this->dispatch()` for component events
- Event listeners with `on()` function in Volt
- Browser events with `@notify.window` Alpine directive

## Volt 1 Component Design

### Functional API

Volt 1 provides a simplified single-file component API:

**State Management**:

```php
state(['search' => '', 'category' => '', 'status' => '']);
```

**Computed Properties**:

```php
computed('filteredTickets', function () {
    return Ticket::query()
        ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
        ->paginate(10);
});
```

**Event Listeners**:

```php
on(['ticket-updated' => function () {
    unset($this->filteredTickets);
}]);
```

### Usage Guidelines

- Use Volt for components with <100 lines of PHP logic
- Ideal for: Forms, filters, modals, search interfaces
- Place in: resources/views/livewire/
- Naming: kebab-case (e.g., search-filter.blade.php)

## Alpine.js 3.x Patterns

### Core Directives

- `x-data`: Component state initialization
- `x-show`: Conditional visibility with transitions
- `x-transition`: Smooth enter/leave animations
- `x-trap`: Focus trapping for modals
- `@click.away`: Click outside detection
- `@keydown.escape`: Keyboard navigation

### Common Patterns

**Dropdown Pattern**:

- Toggle state with `x-data="{ open: false }"`
- Keyboard navigation (Escape to close)
- ARIA attributes for accessibility
- Click-away detection

**Modal Pattern**:

- Backdrop with opacity transitions
- Focus trap for keyboard users
- Escape key to close
- Entangle with Livewire for state sync

**Accordion Pattern**:

- Expandable sections with smooth transitions
- ARIA expanded/collapsed states
- Keyboard navigation support

### Integration with Livewire

- Use `@entangle()` for two-way state binding
- Minimize Alpine usage in favor of server-driven Livewire
- Alpine for UI interactions, Livewire for data operations

## Page Layout Design

### Guest Layout

**Structure**:

- Header: MOTAC branding, language switcher
- Main content: Centered max-width container
- Footer: Copyright, links, contact info
- Skip links for accessibility

**Features**:

- No authentication required
- Bilingual support (Bahasa Melayu/English)
- WCAG 2.2 AA compliant
- Mobile-first responsive design

### Portal Layout

**Structure**:

- Sidebar: Navigation menu (collapsible on mobile)
- Header: User profile, notifications, logout
- Main content: Breadcrumb + page content
- Footer: Minimal footer with version info

**Features**:

- Role-based navigation (Staff, Approver, Admin)
- Personalized dashboard
- Real-time notifications
- Responsive sidebar (hamburger on mobile)

### Admin Layout (Filament 4)

**Structure**:

- Filament's default admin panel layout
- Customized with MOTAC branding
- Integrated with ICTServe theme

**Features**:

- Full CRUD interfaces for all models
- Advanced filtering and search
- Bulk operations
- Export functionality

## Form Design Patterns

### Accessible Form Components

All form components include:

- Proper label associations
- ARIA attributes for screen readers
- Visible focus indicators (3px outline, 2px offset)
- Inline validation with error messages
- Help text for complex fields
- Required field indicators

### Multi-Step Form Wizard

**Features**:

- Progress indicator with percentage
- Per-step validation
- Keyboard navigation (Tab, Enter, Escape)
- Review step before submission
- Back/Next navigation
- Mobile-optimized layout

**Implementation**:

- Livewire for state management
- Alpine.js for transitions
- WCAG 2.2 AA compliant
- Real-time validation with debouncing

### Form Validation

- Client-side: Real-time with wire:model.live.debounce
- Server-side: Laravel Form Requests
- Inline error messages with ARIA
- Success notifications
- Accessibility announcements via ARIA live regions

## Performance Optimization Design

### Caching Strategy

**Dashboard Statistics**:

- 5-minute cache for user-specific stats
- Redis-based caching
- Cache invalidation on data updates

**Asset Availability**:

- 5-minute cache for availability calendar
- Eager loading of relationships
- Optimized queries with indexes

**Component Data**:

- Computed properties with caching
- Lazy loading for heavy components
- Placeholder views during loading

### Image Optimization

- WebP format with JPEG fallbacks
- Lazy loading with `loading="lazy"`
- Explicit dimensions to prevent CLS
- `fetchpriority` for above-the-fold images
- Responsive images with srcset

### Code Optimization

- Tailwind CSS purging (<50KB gzipped)
- Vite code splitting
- Livewire asset optimization
- Alpine.js included with Livewire (no separate bundle)

### Database Optimization

- Eager loading to prevent N+1 queries
- Database indexes on frequently queried columns
- Query result caching
- Pagination for large datasets

## Accessibility Implementation

### WCAG 2.2 AA Compliance

**Color Contrast**:

- Text: 4.5:1 minimum contrast ratio
- UI components: 3:1 minimum contrast ratio
- Focus indicators: 3:1 contrast against background

**Touch Targets**:

- Minimum 44×44px for all interactive elements
- Adequate spacing between targets
- Mobile-optimized tap areas

**Keyboard Navigation**:

- All functionality accessible via keyboard
- Visible focus indicators (3-4px outline, 2px offset)
- Logical tab order
- Skip links to main content

### ARIA Implementation

**Landmarks**:

- `<nav>` for navigation
- `<main>` for main content
- `<aside>` for complementary content
- `<footer>` for footer

**Live Regions**:

- Polite announcements for status updates
- Assertive announcements for errors
- Atomic updates for complete messages

**Dynamic Content**:

- ARIA live regions for notifications
- Status messages announced to screen readers
- Loading states with ARIA busy

### Screen Reader Support

- Semantic HTML5 elements
- Proper heading hierarchy (h1-h6)
- Alt text for all images
- Form labels and descriptions
- Table headers and captions

## Responsive Design Strategy

### Mobile-First Approach

Design progression:

1. **Mobile (320px-639px)**: Single column, stacked layout
2. **Tablet (640px-1023px)**: Two-column grid, collapsible sidebar
3. **Desktop (1024px+)**: Multi-column layout, persistent sidebar

### Breakpoint Strategy

- **sm (640px)**: Mobile landscape, small tablets
- **md (768px)**: Tablet portrait
- **lg (1024px)**: Tablet landscape, small desktop
- **xl (1280px)**: Desktop
- **2xl (1536px)**: Large desktop

### Responsive Patterns

**Navigation**:

- Mobile: Hamburger menu
- Tablet: Collapsible sidebar
- Desktop: Persistent sidebar

**Forms**:

- Mobile: Single column, full-width inputs
- Tablet: Two-column grid for related fields
- Desktop: Multi-column layout with optimal field widths

**Tables**:

- Mobile: Card-based layout
- Tablet: Horizontal scroll with sticky columns
- Desktop: Full table with all columns visible

**Dashboards**:

- Mobile: Stacked widgets
- Tablet: 2-column grid
- Desktop: 3-4 column grid

## Bilingual Support Design

### Language Switching

**Implementation**:

- Language switcher in header (guest and portal)
- 44×44px touch targets for accessibility
- Keyboard navigation support
- ARIA labels for screen readers

**Persistence**:

- Session storage (primary)
- Cookie storage (1-year expiration, fallback)
- No database storage (per requirements)

**Detection Priority**:

1. Session value
2. Cookie value
3. Accept-Language header
4. Config default (Bahasa Melayu)

### Translation Management

**Laravel Localization**:

- Language files: resources/lang/en/ and resources/lang/ms/
- Translation keys: `__('key')` in Blade templates
- Pluralization support
- Parameter replacement

**Content Translation**:

- All UI text translated
- Email templates bilingual
- Error messages localized
- Validation messages translated

### RTL Support

Not required for Bahasa Melayu and English (both LTR languages), but architecture supports future RTL languages if needed.

## Security Design

### CSRF Protection

- Laravel's built-in CSRF protection
- Enhanced for AJAX requests
- Token refresh for long-lived sessions
- Validation on all form submissions

### Rate Limiting

- 60 requests per minute for guest forms
- IP-based rate limiting
- Throttle middleware on sensitive endpoints
- Graceful degradation with user feedback

### Input Validation

- Client-side: Real-time validation with Livewire
- Server-side: Laravel Form Requests
- Sanitization of user input
- XSS prevention with Blade escaping

### Authentication & Authorization

- Laravel authentication system
- Email verification required
- Role-based access control (4 roles)
- Policy-based authorization
- Session management with secure cookies

### Data Protection

- PDPA 2010 compliance
- Encryption for sensitive data (AES-256)
- Secure token generation for approvals
- Audit trail with 7-year retention

## Testing Strategy

### Unit Testing

- Business logic in services and traits
- Component methods and computed properties
- Validation rules and form requests
- Helper functions and utilities

### Feature Testing

- User workflows (ticket submission, loan application)
- Authentication and authorization
- Form submissions and validation
- Email notifications
- API endpoints

### Integration Testing

- Cross-module functionality (asset-ticket linking)
- Email workflows
- Approval processes
- Dashboard statistics

### Accessibility Testing

- Lighthouse audits (target: 100 score)
- axe DevTools automated testing
- Manual screen reader testing (NVDA, JAWS)
- Keyboard navigation testing
- Color contrast verification

### Performance Testing

- Core Web Vitals monitoring
- Lighthouse performance audits
- Load testing for concurrent users
- Database query optimization
- Cache effectiveness

### Browser Testing

- Chrome 90+ (primary)
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Deployment Strategy

### Environment Configuration

**Development**:

- Local development with Vite HMR
- SQLite database
- Debug mode enabled
- Detailed error messages

**Staging**:

- Production-like environment
- MySQL database
- Debug mode disabled
- Error logging to files

**Production**:

- Optimized assets (minified, gzipped)
- MySQL database with replication
- Redis caching
- Queue workers for background jobs
- Monitoring and alerting

### Build Process

1. **Asset Compilation**: `npm run build`
2. **CSS Optimization**: Tailwind purging, minification
3. **JavaScript Bundling**: Vite code splitting
4. **Cache Warming**: Route, config, view caching
5. **Database Migration**: Run pending migrations
6. **Queue Workers**: Start background job processors

### Monitoring

- Application performance monitoring (APM)
- Error tracking and logging
- Core Web Vitals monitoring
- User analytics
- Uptime monitoring
- Database performance metrics

## Traceability Matrix

### Requirements to Design Mapping

| Requirement                    | Design Section                             |
| ------------------------------ | ------------------------------------------ |
| R01: Laravel 12.x Foundation   | Architecture Overview, Technology Stack    |
| R02: Livewire 3.x Architecture | Livewire 3.x Architecture                  |
| R03: Volt 1 Components         | Volt 1 Component Design                    |
| R04: Tailwind CSS 4.1          | Tailwind CSS 4.1 Design System             |
| R05: Alpine.js 3.x             | Alpine.js 3.x Patterns                     |
| R06: Component Library         | Component Library Design                   |
| R07: WCAG 2.2 AA Compliance    | Accessibility Implementation               |
| R08: Performance Optimization  | Performance Optimization Design            |
| R09: Guest Forms               | Page Layout Design (Guest Layout)          |
| R10: Authenticated Portal      | Page Layout Design (Portal Layout)         |
| R11: Cross-Module Integration  | Architecture Overview                      |
| R12: Email Workflows           | Security Design                            |
| R13: Bilingual Support         | Bilingual Support Design                   |
| R14: Security & Compliance     | Security Design                            |
| R15: Responsive Design         | Responsive Design Strategy                 |
| R16: Testing & QA              | Testing Strategy                           |
| R17: Documentation             | All sections (comprehensive documentation) |

### Design to D00-D15 Standards

- **D03**: Software Requirements Specification → Requirements Document
- **D04**: Software Design Document → This Document
- **D12**: UI/UX Design Guide → Component Library, Tailwind Design System
- **D13**: UI/UX Frontend Framework → Architecture Overview, Technology Stack
- **D14**: UI/UX Style Guide → Tailwind CSS Design System, Accessibility
- **D15**: Language Support → Bilingual Support Design

## Success Criteria

The design will be considered successful when:

1. **Architecture**: Three-tier hybrid architecture fully implemented and operational
2. **Components**: Unified component library with 95%+ reuse across all interfaces
3. **Accessibility**: 100% WCAG 2.2 AA compliance verified by Lighthouse and manual testing
4. **Performance**: Core Web Vitals targets achieved (LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms)
5. **Responsive**: Seamless experience across all devices (320px-1920px)
6. **Bilingual**: Complete Bahasa Melayu and English support with proper persistence
7. **Security**: Zero critical vulnerabilities, PDPA 2010 compliance verified
8. **Testing**: 80%+ code coverage, 95%+ critical path coverage
9. **Integration**: Seamless cross-module functionality between helpdesk and asset loan
10. **User Experience**: Positive feedback from stakeholders and end users

## Conclusion

This design document provides a comprehensive blueprint for the ICTServe frontend upgrade. The design leverages modern Laravel 12.x, Livewire 3.x, Volt 1, Tailwind CSS 4.1, and Alpine.js 3.x technologies to deliver a unified, accessible, and high-performance frontend experience.

The three-tier hybrid architecture supports guest forms, authenticated portal, and admin interfaces with a shared component library ensuring consistency and maintainability. WCAG 2.2 AA compliance, Core Web Vitals optimization, and comprehensive testing ensure a high-quality user experience for all users.

Implementation will follow the phased approach outlined in the tasks document, with continuous testing and validation against requirements and design specifications.

---

**Document Version**: 1.0  
**Last Updated**: 2025-01-15  
**Author**: Frontend Engineering Team  
**Status**: Design Approved  
**Technology Stack**: Laravel 12.x | Livewire 3.x | Volt 1 | Tailwind CSS 4.1 | Alpine.js 3.x
