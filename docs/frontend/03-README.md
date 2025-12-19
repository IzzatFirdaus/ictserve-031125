# Frontend Documentation

**ICTServe v3.6.1**  
**Last Updated**: 2025-12-17

## Overview

This directory contains comprehensive frontend documentation for the ICTServe application, covering MyDS v2025.2 compliance, WCAG 2.2 AA accessibility standards, and component implementation guidelines.

## Documentation Index

### 1. [MyDS Compliance Guide](00-myds-compliance-guide.md)
Complete reference for Malaysia Government Design System v2025.2 implementation.

**Contents**:

- Design tokens (touch targets, border radius, focus indicators)
- Semantic color system
- Component standards
- Updated component list
- Verification checklist
- Common patterns
- Troubleshooting

**Use When**:

- Implementing new components
- Updating existing UI elements
- Ensuring design consistency
- Verifying accessibility compliance

---

### 2. [Form Styling Walkthrough](01-form-styling-walkthrough.md)
Detailed walkthrough of form styling updates across the application.

**Contents**:

- Phase-by-phase implementation summary
- Before/after code examples
- Verification results
- Browser compatibility matrix
- Known issues and resolutions
- Compliance checklist

**Use When**:

- Understanding what changed and why
- Troubleshooting form styling issues
- Conducting accessibility audits
- Planning similar updates

---

### 3. [Component Migration Guide](02-component-migration-guide.md)
Technical guide for migrating base Blade components to MyDS v2025.2.

**Contents**:

- Core component updates (text-input, buttons)
- Before/after code comparisons
- Migration checklist
- Testing strategy
- Rollback plan
- Performance impact analysis

**Use When**:

- Updating base components
- Planning component refactoring
- Troubleshooting component issues
- Rolling back changes if needed

---

## Quick Reference

### Design Tokens

| Token | Value | Usage |
|-------|-------|-------|
| Touch Target | `min-h-11` (44px) | All interactive elements |
| Border Radius | `rounded-lg` (8px) | Inputs, buttons, cards |
| Focus Ring | `focus-visible:ring-3` | All focusable elements |
| Primary Color | `primary-500/600` | Primary actions |
| Success Color | `success-500/600` | Success states |
| Warning Color | `warning-500/600` | Warning states |
| Danger Color | `danger-500/600` | Error/destructive actions |
| Info Color | `info-500/600` | Informational messages |

### Component Classes

#### Text Input

```blade
<input class="min-h-11 rounded-lg border-gray-300 
               focus-visible:ring-3 focus-visible:ring-primary-500" />
```

#### Primary Button

```blade
<button class="min-h-11 px-4 rounded-lg bg-primary-600 text-white
                hover:bg-primary-700 focus-visible:ring-3 
                focus-visible:ring-primary-500">
    Submit
</button>
```

#### Danger Button

```blade
<button class="min-h-11 px-4 rounded-lg bg-danger-600 text-white
                hover:bg-danger-700 focus-visible:ring-3 
                focus-visible:ring-danger-500">
    Delete
</button>
```

---

## Standards Compliance

### MyDS v2025.2

- ✅ Touch targets ≥44px
- ✅ Border radius standardized (8px)
- ✅ Focus indicators visible (3px ring)
- ✅ Semantic color tokens
- ✅ Consistent spacing

### WCAG 2.2 AA

- ✅ Color contrast ≥4.5:1 (text)
- ✅ Color contrast ≥3:1 (UI components)
- ✅ Keyboard accessible
- ✅ Focus visible
- ✅ Touch target size ≥44x44px
- ✅ Consistent identification

---

## Updated Components

### Core Components (`resources/views/components/`)

- [x] `text-input.blade.php`
- [x] `primary-button.blade.php`
- [x] `secondary-button.blade.php`
- [x] `danger-button.blade.php`

### Authentication Forms (`resources/views/livewire/pages/auth/`)

- [x] `login.blade.php`
- [x] `register.blade.php`
- [x] `forgot-password.blade.php`
- [x] `reset-password.blade.php`
- [x] `verify-email.blade.php`
- [x] `confirm-password.blade.php`

### Profile Forms (`resources/views/livewire/profile/`)

- [x] `update-profile-information-form.blade.php`
- [x] `update-password-form.blade.php`
- [x] `delete-user-form.blade.php`

### Filament Admin Pages (`resources/views/filament/pages/`)

- [x] `admin-dashboard.blade.php`
- [x] `notification-center.blade.php`
- [x] `unified-search.blade.php`
- [x] `accessibility-compliance.blade.php`
- [x] `submission-history.blade.php`

### Staff Dashboard (`resources/views/livewire/staff/`)

- [x] `authenticated-dashboard.blade.php`
- [x] `account-linking.blade.php`

---

## Testing

### Automated

```bash
# Build verification
npm run build

# Lint check
npm run lint

# Accessibility scan
npm run test:accessibility
```

### Manual

- [ ] Visual inspection (border radius, heights)
- [ ] Keyboard navigation (Tab, Enter, Escape)
- [ ] Touch testing (mobile devices)
- [ ] Screen reader testing (NVDA, JAWS)
- [ ] Color contrast verification

---

## Common Issues

### Focus Ring Not Visible
**Solution**: Ensure `focus-visible:ring-3` is present and not overridden

### Touch Target Too Small
**Solution**: Use `min-h-11` instead of fixed heights

### Wrong Color Token
**Solution**: Use semantic tokens (`primary`, `success`, `warning`, `danger`, `info`)

### Border Radius Inconsistent
**Solution**: Use `rounded-lg` for standard components

---

## Related Documentation

### System Documentation

- **D12**: UI/UX Design Guide
- **D13**: UI/UX Frontend Framework
- **D14**: UI/UX Style Guide
- **D15**: Language Localization (MS/EN)

### External Standards

- **MyDS v2025.2**: [Malaysia Government Design System](https://design.digital.gov.my)
- **WCAG 2.2**: [Web Content Accessibility Guidelines](https://www.w3.org/TR/WCAG22/)

### Code Standards

- **AlpineJS**: `.amazonq/rules/AlpineJS.md`
- **Livewire**: `.amazonq/rules/Livewire.md`
- **TailwindCSS**: `.amazonq/rules/TailwindCSS.md`

---

## Maintenance

### Regular Reviews

- **Quarterly**: Verify compliance with latest MyDS updates
- **Bi-annually**: Conduct full accessibility audit
- **Annually**: Review and update documentation

### Update Process

1. Review MyDS changelog
2. Identify affected components
3. Plan migration strategy
4. Update components
5. Test thoroughly
6. Update documentation

---

## Support

### Questions or Issues?

- **Technical**: Contact BPM MOTAC Development Team
- **Design**: Refer to D12-D14 documentation
- **Accessibility**: Refer to WCAG 2.2 guidelines

### Contributing
When updating frontend components:

1. Follow MyDS v2025.2 standards
2. Ensure WCAG 2.2 AA compliance
3. Update relevant documentation
4. Add tests for new features
5. Submit for review

---

**Maintained By**: BPM MOTAC Development Team  
**Version**: 1.0.0  
**Status**: Active
