# GitHub Copilot Task: Fix ApprovalInterfaceTest Failures

## Status
**Priority:** HIGH  
**Type:** Test Failures  
**Affected File:** `tests/Feature/Portal/ApprovalInterfaceTest.php`  
**Date:** 2025-11-10  
**Resolution Status:** ✅ FIXED - Code changes implemented

## Implementation Summary

### ✅ Issue 1: Authorization Fixed
**File Modified:** `app/Http/Middleware/EnsureApproverRole.php`

Middleware now checks BOTH role systems:
- `$user->role` column attribute (legacy)
- `$user->hasAnyRole()` Spatie permission system (modern)

### ✅ Issue 2: Blade Variable Fixed
**File Modified:** `resources/views/livewire/staff/approval-interface.blade.php`

Changed variable name from `$pendingApprovals` to `$applications` to match component output.

### Commit Details
- **Branch:** `copilot/vscode1762754175423`
- **Commit:** `5aa090e`
- **Files:** 2 files, 13 lines changed
- **Status:** Pushed to GitHub

---

## Test Results (Expected)

- **Total Tests:** 18
- **Failed:** 16
- **Passed:** 2

## Issues Identified

### Issue 1: Authorization Failure (403 Forbidden)
**Test:** `grade_41_plus_user_can_access_approval_interface`

**Expected:** HTTP 200  
**Actual:** HTTP 403  

**Problem:**

- User with grade 41 and 'approver' role is being denied access to `/staff/approvals`
- Route authorization middleware or policy is rejecting legitimate approvers

**Required Fix:**

- Check route definition in `routes/web.php` for `/staff/approvals`
- Verify middleware applied to this route
- Check if there's a Policy for ApprovalInterface or authorization in the Livewire component
- Ensure Grade 41+ users with 'approver' role can access the route

### Issue 2: Undefined Variable $pendingApprovals (15 tests)
**Error:** `Undefined variable $pendingApprovals`  
**Location:** `resources/views/livewire/staff/approval-interface.blade.php` at line 192

**Problem:**

- The Livewire component `App\Livewire\Staff\ApprovalInterface` is not defining/passing the `$pendingApprovals` variable
- The Blade view attempts to loop over `$pendingApprovals` but it doesn't exist

**Required Fix:**

1. **Check ApprovalInterface Livewire Component** (`app/Livewire/Staff/ApprovalInterface.php`):
   - Add public property: `public $pendingApprovals = [];`
   - OR use Livewire 3 computed property pattern with `#[Computed]` attribute
   - Load pending applications in `mount()` or use computed property

2. **Example Fix Pattern:**

   ```php
   use Livewire\Attributes\Computed;

   class ApprovalInterface extends Component
   {
       #[Computed]
       public function pendingApprovals()
       {
           return LoanApplication::query()
               ->where('status', 'under_review')
               ->where('approver_email', auth()->user()->email)
               ->with(['user', 'assets'])
               ->latest()
               ->get();
       }
   }
   ```

3. **Update Blade View** (`resources/views/livewire/staff/approval-interface.blade.php`):
   - If using computed property, change `$pendingApprovals` to `$this->pendingApprovals`
   - Ensure proper Livewire 3 syntax

## Files to Investigate

### Primary Files

1. `app/Livewire/Staff/ApprovalInterface.php` - Main Livewire component (CRITICAL)
2. `resources/views/livewire/staff/approval-interface.blade.php` - Blade view
3. `routes/web.php` - Route definition for `/staff/approvals`

### Supporting Files

4. `app/Policies/LoanApplicationPolicy.php` or similar - Authorization policy
5. `app/Http/Middleware/*` - Any custom middleware for Grade 41+ checks

## Expected Behavior After Fix

### Authorization

- ✅ Grade 41+ users with 'approver' role can access `/staff/approvals` (HTTP 200)
- ✅ Users below Grade 41 are denied access (HTTP 403)
- ✅ Guests are redirected to login

### Pending Applications Display

- ✅ Component loads pending applications where `status = 'under_review'`
- ✅ Applications are filtered by `approver_email` matching current user
- ✅ Application number and applicant name are visible
- ✅ Approved applications are NOT displayed

### Actions

- ✅ Approver can approve applications
- ✅ Approver can reject applications
- ✅ Approver can select multiple applications
- ✅ Bulk approve/reject works
- ✅ Email notifications sent on approval/rejection
- ✅ Actions are audited in `audits` table

## Testing Command

```bash
php artisan test tests/Feature/Portal/ApprovalInterfaceTest.php
```

## Success Criteria
All 18 tests pass with no failures.

## Additional Context

### Test Setup
The test creates:

- Division: "IT Division"
- Asset: "Test Laptop"
- Approver: User with `grade = 41`, assigned 'approver' role
- Staff: User with `grade = 40` (below threshold)

### Application States Tested

- `under_review` - Should be visible to approver
- `approved` - Should NOT be visible
- `rejected` - Not explicitly tested but should NOT be visible

### Key Requirements

- **Traceability:** D03 SRS-FR-004, D04 §3.4
- **Requirements:** 4.1, 4.2, 4.3, 4.4, 4.5
- **Authorization:** Grade 41+ with 'approver' role
- **Approval Method:** Should be set to 'portal' after approval/rejection

## Notes

- This is a Livewire 3 component (uses `Livewire::actingAs()` in tests)
- Application uses Laravel 12 with Filament 4
- Mail notifications are queued (uses `ShouldQueue`)
- Auditing is via `owen-it/laravel-auditing` package

---

**Task Assigned To:** GitHub Copilot Agent  
**Expected Resolution Time:** Immediate  
**Priority:** Fix authorization first, then undefined variable issue
