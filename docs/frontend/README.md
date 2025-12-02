# Frontend Documentation

This directory contains documentation for the ICTServe frontend implementation, including Livewire components, services, and patterns.

## Contents

### Core Documentation

| Document                                                    | Description                           |
| ----------------------------------------------------------- | ------------------------------------- |
| [Guest Loan Application](guest-loan-application.md)         | Multi-step loan application wizard    |
| [Asset Availability Service](asset-availability-service.md) | Real-time asset availability checking |
| [Loan Application Service](loan-application-service.md)     | Loan application business logic       |

### Pattern Guides

| Document                                  | Description                           |
| ----------------------------------------- | ------------------------------------- |
| [Alpine Patterns](alpine-patterns.md)     | Alpine.js integration patterns        |
| [Livewire Patterns](livewire-patterns.md) | Livewire 3.x best practices           |
| [Volt Guidelines](volt-guidelines.md)     | Volt single-file component guidelines |

## Architecture Overview

### Technology Stack

- **Laravel 12.x** - PHP framework
- **Livewire 3.x** - Server-driven UI
- **Volt 1.x** - Single-file components
- **Tailwind CSS 4.1** - Utility-first CSS
- **Alpine.js 3.x** - Client-side interactivity

### Component Structure

```
resources/views/
├── livewire/
│   ├── guest-loan-application.blade.php
│   ├── guest-helpdesk-form.blade.php
│   └── loan/
│       ├── step-1-applicant.blade.php
│       ├── step-2-responsible-officer.blade.php
│       └── ...
├── components/
│   ├── ui/
│   │   ├── button.blade.php
│   │   ├── card.blade.php
│   │   └── ...
│   ├── form/
│   │   ├── input.blade.php
│   │   ├── select.blade.php
│   │   └── ...
│   └── accessibility/
│       ├── skip-links.blade.php
│       └── ...
└── layouts/
    ├── front.blade.php
    └── portal.blade.php
```

### Service Layer

```
app/Services/
├── LoanApplicationService.php      # Loan business logic
├── AssetAvailabilityService.php    # Availability checking
├── WorkingDayCalculator.php        # Lead time validation
├── DualApprovalService.php         # Approval workflows
└── NotificationService.php         # Email notifications
```

## Key Features

### Hybrid Architecture

The frontend supports three access levels:

1. **Guest** - Public forms without authentication
2. **Authenticated** - Staff portal with personalized features
3. **Admin** - Filament 4 admin panel

### WCAG 2.2 AA Compliance

All components meet accessibility standards:

- 4.5:1 text contrast ratio
- 3:1 UI component contrast
- 44×44px minimum touch targets
- Keyboard navigation support
- Screen reader compatibility

### Bilingual Support

Full support for:

- Bahasa Melayu (primary)
- English (secondary)

Language files: `lang/en/` and `lang/ms/`

### Performance Targets

- LCP < 2.5s
- FID < 100ms
- CLS < 0.1
- TTFB < 600ms

## Related Documentation

- [Security Documentation](../security/README.md) - Rate limiting, IP blocking
- [D03 Software Requirements](../D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md)
- [D04 Software Design](../D04_SOFTWARE_DESIGN_DOCUMENT.md)
- [D12 UI/UX Design Guide](../D12_UI_UX_DESIGN_GUIDE.md)

---

**Last Updated**: 2025-11-27
