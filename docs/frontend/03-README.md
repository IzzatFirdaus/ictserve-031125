# Frontend Documentation

**ICTServe v3.6.1**  
**Last Updated**: 2025-12-20

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

### 4. [Updated Files List](04-updated-files-list.md)
Complete list of all 140+ files updated for MyDS v2025.2 compliance.

**Contents**:

- Files organized by category
- Summary statistics
- Changes applied to all files

**Use When**:

- Tracking update progress
- Verifying file coverage
- Planning testing scope
- Documenting changes

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

- ✅ Touch targets ≥44px (min-h-11)
- ✅ Border radius standardized (rounded-lg = 8px)
- ✅ Focus indicators visible (ring-3 = 3px)
- ✅ Semantic color tokens (primary, success, warning, danger, info)
- ✅ Consistent spacing

### WCAG 2.2 AA

- ✅ Color contrast ≥4.5:1 (text)
- ✅ Color contrast ≥3:1 (UI components)
- ✅ Keyboard accessible
- ✅ Focus visible (focus-visible:ring-3)
- ✅ Touch target size ≥44x44px
- ✅ Consistent identification

### Implementation Status

- ✅ **P0 Critical Fixes**: Complete (Livewire components, guest/auth forms)
- ✅ **P1 High Priority**: Complete (helpdesk, loans, AI chat, root components)
- ⏳ **P2 Filament Admin**: Major progress complete (25+ pages, 4 widgets, 4 components, 1 modal)
  - ✅ 25+ Filament pages updated
  - ⏳ Remaining Filament pages (~11) pending audit
  - ✅ 4 Filament widgets updated
  - ✅ 4 Filament components updated
  - ✅ 1 Filament modal updated
  - ✅ Filament resources verified compliant
  - ✅ Duplicate directory cleanup complete
  - ✅ Line-ending fixes applied
- ✅ **P0 Staff Components**: Complete (10/10 files)
  - ✅ `staff/approval-interface.blade.php`
  - ✅ `staff/delegation-manager.blade.php`
  - ✅ `staff/account-linking.blade.php`
  - ✅ `staff/cross-module-search.blade.php`
  - ✅ `staff/authenticated-dashboard.blade.php`
  - ✅ `staff/claim-submissions.blade.php`
  - ✅ `staff/notification-center.blade.php`
  - ✅ `staff/session-manager.blade.php`
  - ✅ `staff/submission-history.blade.php`
  - ✅ `staff/user-profile.blade.php`
- ✅ **P0 Portal Components**: Complete (12/12 files)
  - ✅ `portal/internal-comments.blade.php`
  - ✅ `portal/notification-preferences.blade.php`
  - ✅ `portal/notification-center.blade.php`
  - ✅ `portal/notification-bell.blade.php`
  - ✅ `portal/user-profile.blade.php`
  - ✅ `portal/help-center.blade.php`
  - ✅ `portal/support-message.blade.php`
  - ✅ `portal/welcome-tour.blade.php`
  - ✅ `portal/dashboard/statistics-cards.blade.php`
  - ✅ `portal/help/welcome-tour.blade.php`
  - ✅ `portal/help/help-center.blade.php`
  - ✅ `portal/widgets/personal-stats-widget.blade.php`
- ✅ **P2 Livewire Components**: Complete (9/9 files)
  - ✅ `components/confirm-modal.blade.php`
  - ✅ `components/form-wizard.blade.php`
  - ✅ `components/progress-indicator.blade.php`
  - ✅ `components/saved-filters.blade.php`
  - ✅ `components/search-filter.blade.php`
  - ✅ `components/theme-dropdown-unified.blade.php`
  - ✅ `components/theme-toggle-unified.blade.php`
  - ✅ `components/toast.blade.php`
  - ✅ `components/unified-search.blade.php`
- ✅ **P2 Auth Pages**: Complete (5/5 files)
  - ✅ `pages/auth/confirm-password.blade.php`
  - ✅ `pages/auth/reset-password.blade.php`
  - ✅ `pages/auth/login.blade.php`
  - ✅ `pages/auth/register.blade.php`
  - ✅ `pages/auth/verify-email.blade.php`
- ⏳ **P2 Pages Directory**: Pending (structure check)

---

## Updated Components

### Core Components (`resources/views/components/`)

- [x] `alert.blade.php`
- [x] `activity-item.blade.php`
- [x] `danger-button.blade.php`
- [x] `modal.blade.php`
- [x] `primary-button.blade.php`
- [x] `secondary-button.blade.php`
- [x] `text-input.blade.php`

### Form Components (`resources/views/components/form/`)

- [x] `checkbox.blade.php`
- [x] `input.blade.php`
- [x] `radio.blade.php`
- [x] `select.blade.php`
- [x] `textarea.blade.php`
- [x] `toggle.blade.php`

### UI Components (`resources/views/components/ui/`)

- [x] `alert.blade.php`
- [x] `button.blade.php`
- [x] `card.blade.php`
- [x] `dropdown-item.blade.php`
- [x] `modal.blade.php`
- [x] `user-info-card.blade.php`

### Accessibility Components (`resources/views/components/accessibility/`)

- [x] `button.blade.php`
- [x] `input.blade.php`
- [x] `skip-links.blade.php`

### Layouts (`resources/views/layouts/`)

- [x] `app.blade.php`
- [x] `guest.blade.php`

### Authentication Forms (`resources/views/livewire/pages/auth/`)

- [x] `confirm-password.blade.php`
- [x] `forgot-password.blade.php`
- [x] `login.blade.php`
- [x] `register.blade.php`
- [x] `reset-password.blade.php`
- [x] `verify-email.blade.php`

### Profile Forms (`resources/views/livewire/profile/`)

- [x] `delete-user-form.blade.php`
- [x] `update-password-form.blade.php`
- [x] `update-profile-information-form.blade.php`

### Filament Admin Pages (`resources/views/filament/pages/`)

- [x] `accessibility-compliance.blade.php`
- [x] `admin-dashboard.blade.php`
- [x] `auth/login.blade.php`
- [x] `notification-center.blade.php`
- [x] `unified-search.blade.php`

### Helpdesk Module (`resources/views/livewire/helpdesk/`)

- [x] `guest-ticket-form.blade.php`
- [x] `ticket-form.blade.php`
- [x] `ticket-success.blade.php`

### Loan Module (`resources/views/livewire/loan/`)

- [x] `application-wizard-view.blade.php`
- [x] `approval-page.blade.php`
- [x] `guest-application-form.blade.php`
- [x] `partials/step-3-assets.blade.php`
- [x] `partials/step-4-dates.blade.php`
- [x] `partials/step-5-purpose.blade.php`
- [x] `partials/step-6-acknowledgement.blade.php`

### Staff Portal (`resources/views/livewire/staff/`)

**Completed (10 files)**:

- [x] `account-linking.blade.php` - Semantic tokens applied
- [x] `approval-interface.blade.php` - Semantic tokens applied
- [x] `cross-module-search.blade.php` - Semantic tokens applied
- [x] `delegation-manager.blade.php` - Semantic tokens applied
- [x] `authenticated-dashboard.blade.php` - Already compliant
- [x] `claim-submissions.blade.php` - Already compliant
- [x] `notification-center.blade.php` - Remediated (gray→slate)
- [x] `session-manager.blade.php` - Remediated (gray→slate)
- [x] `submission-history.blade.php` - Remediated (gray→slate, rounded-lg)
- [x] `user-profile.blade.php` - Already compliant

### Portal Components (`resources/views/livewire/portal/`)

**Completed (12 files)**:

- [x] `internal-comments.blade.php` - Semantic tokens applied
- [x] `notification-preferences.blade.php` - Semantic tokens applied
- [x] `notification-center.blade.php` - Already compliant
- [x] `notification-bell.blade.php` - Already compliant
- [x] `user-profile.blade.php` - Remediated (gray→slate)
- [x] `help-center.blade.php` - Already compliant
- [x] `support-message.blade.php` - Remediated (gray→slate, rounded-lg)
- [x] `welcome-tour.blade.php` - Remediated (gray→slate)
- [x] `dashboard/statistics-cards.blade.php` - Remediated (gray→slate, rounded-lg)
- [x] `help/welcome-tour.blade.php` - Remediated (gray→slate, rounded-lg)
- [x] `help/help-center.blade.php` - Remediated (gray→slate)
- [x] `widgets/personal-stats-widget.blade.php` - Remediated (gray→slate)

### Livewire Components (`resources/views/livewire/components/`)

**Completed (9 files)**:

- [x] `confirm-modal.blade.php` - Remediated (gray→slate, rounded-md→rounded-lg)
- [x] `form-wizard.blade.php` - Remediated (gray→slate, rounded-md→rounded-lg)
- [x] `progress-indicator.blade.php` - Remediated (gray→slate)
- [x] `saved-filters.blade.php` - Remediated (gray→slate, rounded-md→rounded-lg)
- [x] `search-filter.blade.php` - Remediated (gray→slate, rounded-md→rounded-lg)
- [x] `theme-dropdown-unified.blade.php` - Remediated (gray→slate)
- [x] `theme-toggle-unified.blade.php` - Remediated (gray→slate)
- [x] `toast.blade.php` - Already compliant
- [x] `unified-search.blade.php` - Remediated (gray→slate, rounded-md→rounded-lg)

### Auth Pages (`resources/views/livewire/pages/auth/`)

**Completed (5 files)**:

- [x] `confirm-password.blade.php` - Remediated
- [x] `reset-password.blade.php` - Remediated
- [x] `login.blade.php` - Remediated
- [x] `register.blade.php` - Remediated
- [x] `verify-email.blade.php` - Remediated

### Navigation & Layout (`resources/views/livewire/`)

- [x] `global-search.blade.php`
- [x] `guest-loan-application.blade.php`
- [x] `layout/navigation.blade.php`
- [x] `navigation/portal-navigation.blade.php`
- [x] `notification-bell.blade.php`
- [x] `submission-detail.blade.php`
- [x] `submission-filters.blade.php`
- [x] `submission-history.blade.php`

**Total Files Updated**: 140+ Blade components and views

**Breakdown**:

- Core components: 17 files
- Authentication forms: 6 files
- Profile forms: 3 files
- Filament admin: 34+ files (25+ pages, 4 widgets, 4 components, 1 modal)
- Helpdesk module: 9 files
- Loan module: 9 files
- Staff portal: 10 files (complete)
- Portal components: 12 files (complete)
- Livewire components: 9 files
- Auth pages: 5 files
- AI chat: 2 files
- Root-level: 10 files
- Pulse/status: 2 files
- Navigation/layout: 8 files
- Errors/emails: 4 files
- UI components: 22 files
- Layouts: 7 files
- Pages: 6 files
- Root views: 2 files
- Admin views: 3 files

---

## Testing

### Automated

```bash
# Build verification
npm run build  # ✅ Passed

# Code style
vendor/bin/pint --dirty  # ✅ Passed

# Lint check
npm run lint  # ✅ Passed

# Accessibility scan
npm run test:accessibility  # ✅ Passed
```

### Manual

- [x] Visual inspection (border radius, heights) - Via proxy
- [x] Keyboard navigation (Tab, Enter, Escape) - Verified
- [ ] Touch testing (mobile devices) - Pending full app access
- [ ] Screen reader testing (NVDA, JAWS) - Recommended
- [x] Color contrast verification - Automated tools passed

### Known Limitations

- **PHP Version**: Local environment (8.2.12) vs required (8.4+)
- **Workaround**: Verified via proxy (404 page, debug components)
- **Full Testing**: Pending PHP upgrade or Docker environment

---

## Common Issues

### Focus Ring Not Visible
**Solution**: Ensure `focus-visible:ring-3` is present and not overridden
**Status**: ✅ Resolved globally

### Touch Target Too Small
**Solution**: Use `min-h-11` instead of fixed heights
**Status**: ✅ Resolved globally

### Wrong Color Token
**Solution**: Use semantic tokens (`primary`, `success`, `warning`, `danger`, `info`)
**Common Replacements**:

- `blue-*` → `primary-*`
- `amber-*/yellow-*` → `warning-*`
- `green-*` → `success-*`
- `red-*` → `danger-*`
**Status**: ✅ Resolved in 75+ files

### Border Radius Inconsistent
**Solution**: Use `rounded-lg` for standard components
**Status**: ✅ Resolved globally

### Dynamic PHP Colors in Widgets
**Issue**: Some Filament widgets use PHP-generated colors
**Files**: `critical-alerts.blade.php`, `quick-actions.blade.php`
**Solution**: Documented as conditionally compliant
**Status**: ✅ No action required (runtime colors are semantic)

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
