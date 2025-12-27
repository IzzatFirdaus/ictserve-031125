# WCAG 2.2 AA Accessibility Implementation Plan
## ICTServe v3.6.1 - Phases 1-3 Execution

**Date**: 2025-12-27  
**Status**: Phase 1 ✅ COMPLETE | Phase 2 🔄 IN PROGRESS | Phase 3 📋 PLANNED  
**Target**: 100% WCAG 2.2 AA Compliance

---

## Executive Summary

**Current State**: 95% WCAG 2.2 AA compliant  
**Remaining Work**: Add `aria-required="true"` to 215+ required form fields across 43 files  
**Timeline**: Phases 1-3 (1-3 weeks total)

---

## Phase 1: Critical WCAG Violations (2-4 hours) ✅ COMPLETE

### Status: **ALL CRITICAL ISSUES RESOLVED**

All 13 critical violations identified in the gap analysis have been FIXED in previous commits:

#### 1.1 Images Missing Alt Text ✅ RESOLVED
- ✅ `helpdesk/ticket-form.blade.php` - MOTAC logo (line 32)
- ✅ `staff/user-profile.blade.php` - Profile pictures (lines 63, 116)

**Verification**:
```bash
grep -n 'alt=' resources/views/livewire/helpdesk/ticket-form.blade.php  # Line 32
grep -n 'alt=' resources/views/livewire/staff/user-profile.blade.php    # Lines 63, 116
```

#### 1.2 Buttons Missing Aria-Label ✅ RESOLVED
- ✅ `notification-preferences.blade.php` - All 6 toggle switches (lines 156, 188, 220, 252, 284, 316)

**Verification**:
```bash
grep -c 'aria-label=' resources/views/livewire/notification-preferences.blade.php  # Returns 6+
```

#### 1.3 Form Inputs Missing Labels ✅ RESOLVED
- ✅ `guest-loan-application.blade.php`:
  - Terms checkbox (lines 466-473) - Has proper `<label for="terms_accepted">`
  - Liability checkbox (lines 477-484) - Has proper `<label for="liability_accepted">`
  - File upload (lines 525-535) - Has proper `<label for="supporting_documents">`
- ✅ `loans/submit-application.blade.php`:
  - Asset selection checkbox (lines 151-155) - Has `aria-label` attribute

**Verification**:
```bash
grep -B 3 -A 3 "terms_accepted" resources/views/livewire/guest-loan-application.blade.php | grep '<label'
grep -B 3 -A 3 "toggleAsset" resources/views/livewire/loans/submit-application.blade.php | grep 'aria-label'
```

### Phase 1 Outcome
✅ **100% of critical WCAG violations RESOLVED**  
✅ No code changes needed - all fixes already in codebase  
✅ Ready to proceed to Phase 2

---

## Phase 2: Enhanced Accessibility (1-2 days) 🔄 IN PROGRESS

### 2.1 Add aria-required="true" to All Required Fields

**Scope**: 215 required fields across 43 Livewire files

**Priority Files** (High-traffic forms):
1. **Auth Forms** (9 files, ~25 fields):
   - `pages/auth/login.blade.php` (2 fields)
   - `pages/auth/register.blade.php` (7 fields)
   - `pages/auth/reset-password.blade.php` (6 fields)
   - `pages/auth/forgot-password.blade.php` (2 fields)
   - `pages/auth/confirm-password.blade.php` (2 fields)
   - `auth/two-factor-authentication.blade.php`
   - `auth/two-factor-challenge.blade.php`

2. **Loan Application Forms** (8 files, ~100 fields):
   - `guest-loan-application.blade.php` (22 fields)
   - `loan/application-wizard.blade.php` (22 fields)
   - `loan/guest-application-form.blade.php` (34 fields)
   - `loan/application-wizard-view.blade.php` (13 fields)
   - `loan/partials/step-4-dates.blade.php` (3 fields)
   - `loan/partials/step-5-purpose.blade.php` (2 fields)
   - `loan/partials/step-6-acknowledgement.blade.php` (3 fields)
   - `loan/approval-page.blade.php` (5 fields)

3. **Public Forms** (2 files, ~8 fields):
   - `contact-form.blade.php` (4 fields)
   - `helpdesk/ticket-form.blade.php` (4 fields - if any)

4. **Staff Portal Forms** (remaining 24 files, ~82 fields)

**Implementation Pattern**:
```blade
<!-- BEFORE -->
<input type="text" id="name" required wire:model="form.name">

<!-- AFTER -->
<input type="text" id="name" required aria-required="true" wire:model="form.name">
```

**Automation Script**:
```bash
#!/bin/bash
# Add aria-required to all required inputs in a file
# Usage: ./add_aria_required.sh <filepath>

FILE=$1
# Create backup
cp "$FILE" "$FILE.bak"

# Replace required inputs without aria-required
sed -i 's/\(required\)\([^a]\)/\1 aria-required="true"\2/g' "$FILE"

echo "✓ Updated $FILE"
```

**Verification Command**:
```bash
# After all changes
required_count=$(grep -r 'required' resources/views/livewire --include="*.blade.php" | wc -l)
aria_required_count=$(grep -r 'aria-required="true"' resources/views/livewire --include="*.blade.php" | wc -l)
echo "Coverage: $aria_required_count / $required_count ($((aria_required_count * 100 / required_count))%)"
```

---

### 2.2 Verify wire:key on All Loops

**Current Status**: 50 `wire:key` implementations found

**Action Required**:
1. Count all `@foreach` loops in Livewire views
2. Ensure each has corresponding `wire:key`
3. Add missing keys

**Verification Script**:
```bash
#!/bin/bash
echo "=== Loop vs wire:key Audit ==="
foreach_count=$(grep -r '@foreach' resources/views/livewire --include="*.blade.php" | wc -l)
wirekey_count=$(grep -r 'wire:key' resources/views/livewire --include="*.blade.php" | wc -l)
echo "Total @foreach loops: $foreach_count"
echo "Total wire:key:      $wirekey_count"
echo "Missing:             $((foreach_count - wirekey_count))"

# Find specific files missing wire:key
find resources/views/livewire -name "*.blade.php" | while read file; do
    loops=$(grep -c '@foreach' "$file" 2>/dev/null || echo 0)
    keys=$(grep -c 'wire:key' "$file" 2>/dev/null || echo 0)
    if [ $loops -gt $keys ]; then
        echo "  $file: $loops loops, $keys keys (missing $((loops - keys)))"
    fi
done | head -20
```

**Fix Pattern**:
```blade
<!-- BEFORE -->
@foreach ($items as $item)
    <div>{{ $item->name }}</div>
@endforeach

<!-- AFTER -->
@foreach ($items as $item)
    <div wire:key="item-{{ $item->id }}">{{ $item->name }}</div>
@endforeach
```

---

## Phase 3: Documentation & Standards (1 day) 📋 PLANNED

### 3.1 Document Volt Usage Policy

**File**: `docs/VOLT_USAGE_POLICY.md`

**Content**:
- Volt is OPTIONAL for new Livewire components
- Class-based Livewire is acceptable and widely used in ICTServe
- Choose based on component complexity:
  - Simple forms/UI: Volt functional API (if team prefers)
  - Complex business logic: Class-based Livewire
- NO requirement to convert existing 93 class-based components

**Rationale**:
- D13 §2 suggests Volt for new components, but doesn't mandate it
- Current codebase has 93 stable class-based components
- Risk of regression outweighs benefits of conversion

---

### 3.2 Create Component Development Guidelines

**File**: `docs/LIVEWIRE_COMPONENT_GUIDELINES.md`

**Sections**:
1. Component Structure (class-based vs Volt)
2. WCAG 2.2 AA Requirements Checklist
3. Accessibility Patterns (forms, buttons, images, focus management)
4. Dark Mode Support (use `dark:` classes)
5. Translation Keys (use `__()` helper)
6. Loading States (use `wire:loading`)
7. Loop Keys (always use `wire:key`)
8. Testing Requirements (Volt::test() for Volt components)

---

### 3.3 Set Up Automated Accessibility CI Checks

**GitHub Actions Workflow**: `.github/workflows/accessibility.yml`

```yaml
name: Accessibility Audit

on: [pull_request]

jobs:
  wcag-check:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - name: Check for missing alt text
        run: |
          ! grep -r '<img' resources/views --include="*.blade.php" | grep -v 'alt='
      
      - name: Check for missing aria-label on buttons
        run: |
          # Count icon buttons without aria-label
          count=$(grep -r '<button' resources/views/livewire --include="*.blade.php" | grep -v '>' | grep -v 'aria-label' | wc -l)
          if [ $count -gt 50 ]; then
            echo "⚠️  Found $count buttons potentially missing aria-label"
            exit 1
          fi
      
      - name: Check for required without aria-required
        run: |
          required_count=$(grep -r 'required' resources/views/livewire --include="*.blade.php" | wc -l)
          aria_required_count=$(grep -r 'aria-required' resources/views/livewire --include="*.blade.php" | wc -l)
          coverage=$((aria_required_count * 100 / required_count))
          echo "aria-required coverage: $coverage%"
          if [ $coverage -lt 95 ]; then
            echo "❌ Coverage below 95% threshold"
            exit 1
          fi
          echo "✅ Coverage above 95%"
```

---

## Implementation Timeline

| Phase | Duration | Start | End | Status |
|-------|----------|-------|-----|--------|
| **Phase 1** | 2-4 hours | 2025-12-20 | 2025-12-20 | ✅ COMPLETE |
| **Phase 2.1** | 1-2 days | 2025-12-27 | 2025-12-28 | 🔄 IN PROGRESS |
| **Phase 2.2** | 2-4 hours | 2025-12-28 | 2025-12-28 | 📋 PLANNED |
| **Phase 3** | 1 day | 2025-12-29 | 2025-12-29 | 📋 PLANNED |

**Total**: 3-4 business days

---

## Testing Strategy

### Manual Testing Checklist
- [ ] Navigate all forms with keyboard only (Tab, Enter, Space)
- [ ] Test with NVDA/VoiceOver screen reader
- [ ] Verify 200% zoom accessibility (no horizontal scroll)
- [ ] Check color contrast with browser DevTools
- [ ] Test all interactive elements for focus indicators

### Automated Testing
- [ ] Run axe-core CLI on all Livewire pages
- [ ] Run Lighthouse accessibility audit (target: 100%)
- [ ] Verify GitHub Actions accessibility workflow passes

---

## Risk Assessment

**Low Risk**: All identified changes are additive (adding attributes, not removing/changing logic)

**Rollback Plan**: If issues arise, targeted git revert of specific file changes

---

## Notes on Language Switcher

**Status**: DEPRECATED in v3.6.1 per user instruction  
**Alternative Focus**: Theme switcher component (dark/light mode)

**Theme Switcher Requirements**:
- Already implemented with `dark:` Tailwind classes (2,349 uses)
- No additional work needed for theme switching
- Documented in existing codebase

---

## Conclusion

**Phase 1**: ✅ All critical WCAG violations already resolved  
**Phase 2**: 🔄 Adding aria-required to 215 fields across 43 files  
**Phase 3**: 📋 Documentation and CI automation setup

**Target Completion**: 2025-12-29  
**Expected Outcome**: 100% WCAG 2.2 AA compliance with automated enforcement

---

**Prepared by**: Copilot AI Agent  
**Date**: 2025-12-27T06:15:12Z  
**Status**: Implementation in progress
