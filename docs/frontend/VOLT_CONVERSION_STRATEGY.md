# Volt 1.x Conversion Strategy for ICTServe Livewire Components

## Overview
As part of the Livewire 3.x migration, we're selectively converting **5-8 simple, presentational components** to Volt 1.x functional API to demonstrate modern Laravel patterns and reduce boilerplate.

## Selected Components for Volt Conversion

### ✅ Priority 1: LanguageSwitcher
**File:** `app/Livewire/LanguageSwitcher.php` → `resources/views/livewire/components/language-switcher.blade.php`

**Rationale:**
- Simple state (`currentLocale`)
- Single action (`switchLanguage`)
- No complex dependencies
- Perfect showcase for Volt's concise syntax

### ✅ Priority 2: SessionTimeoutWarning
**File:** `app/Livewire/SessionTimeoutWarning.php` → `resources/views/livewire/session-timeout-warning.blade.php`

**Rationale:**
- Modal component with minimal state
- Simple actions (extend session, logout)
- Good pairing with Alpine.js for UI interactivity

### ✅ Priority 3: Portal\Dashboard\StatisticsCards
**File:** `app/Livewire/Portal/Dashboard/StatisticsCards.php` → `resources/views/livewire/portal/dashboard/statistics-cards.blade.php`

**Rationale:**
- Pure presentational component
- Displays computed statistics
- Minimal user interaction

## Components Staying as Livewire 3.x Classes

**Complex Components (Already Optimized with Attributes):**
- QuickActions (uses `#[Lazy]`, `#[Computed]`, `#[Reactive]`)
- RecentActivity (complex filtering, pagination)
- ActivityTimeline (complex filtering, state machine)
- NotificationBell (Echo integration, real-time updates)

**Form Components:**
- LoginForm (complex validation, rate limiting)
- HelpdeskTicketForm, LoanApplicationForm (file uploads, multi-step validation)

**Business Logic Components:**
- ApprovalInterface (authorization, complex workflows)
- SubmissionHistory (complex queries, exports)

## Volt Conversion Pattern

### Standard Header for Volt Files

```php
<?php
// =============================================================================
// Livewire 3.x + Volt 1.x Functional Component
// =============================================================================
// Original Class: app/Livewire/ComponentName.php
// Migrated To: resources/views/livewire/component-name.blade.php
// Migration Date: 2025-11-24
// PR: fix/livewire-3-updates/comprehensive-audit-2025-11
//
// Reason for Volt Conversion:
// - Simple presentational component with minimal state
// - Benefits from Volt's concise functional syntax
// - Easier maintenance with co-located template and logic
//
// Trace: [original trace references]
// Requirements: [original requirements]
// =============================================================================

use function Livewire\Volt\{state, computed};
// ... rest of component logic
?>

<!-- Blade template follows -->
```

### Volt 1.x Best Practices Applied

1. **Use `state()` for reactive properties**
   ```php
   state(['currentLocale' => fn() => app(BilingualSupportService::class)->getCurrentLocale()]);
   ```

2. **Use `computed()` for derived values**
   ```php
   $locales = computed(fn(BilingualSupportService $service) => $service->getSupportedLocales());
   ```

3. **Define actions as closures**
   ```php
   $switchLanguage = function(BilingualSupportService $service, string $locale) {
       $service->switchLocale($locale);
       $this->currentLocale = $locale;
       $this->redirect(request()->header('Referer') ?? '/');
   };
   ```

4. **Leverage dependency injection in closures**

5. **Maintain all WCAG, accessibility, and trace documentation**

## Migration Process

1. **Backup original class** (keep in Git history)
2. **Create new Volt file** with `.blade.php` extension
3. **Port logic** to Volt functional API
4. **Test thoroughly** with existing unit/feature tests
5. **Update documentation** and trace references
6. **Commit** with descriptive message

## Testing Strategy for Volt Components

Each converted component must have:
- Livewire component tests verifying render and actions
- Browser tests (if applicable) for user interactions
- Accessibility tests for ARIA compliance

## Rollback Strategy

If Volt conversion causes issues:
1. Revert specific commit
2. Restore original class-based component
3. Document reason for rollback
4. Re-evaluate if component is truly suitable for Volt

## Documentation Updates

After conversion, update:
- This document with conversion results
- PR summary with before/after comparisons
- Any developer documentation referencing these components
