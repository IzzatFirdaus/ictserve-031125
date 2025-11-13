# Playwright E2E Tests - Fix Summary

## Problem Statement
The repository contained 4 instances of `test.skip()` in accessibility test files that were conditionally skipping tests when users lacked the required permissions (approver or admin access).

## Root Cause
- Tests were designed to work with different user roles (staff, approver, admin)
- The `authenticatedPage` fixture only authenticated as a staff user
- Tests requiring approver or admin permissions would fail authentication and dynamically skip
- This resulted in unpredictable test execution and incomplete coverage

## Solution Implemented

### 1. Enhanced Test Fixtures
**File**: `tests/e2e/fixtures/ictserve-fixtures.ts`

Added a new `approverPage` fixture that:
- Authenticates as an approver user (`approver@motac.gov.my`)
- Provides proper Grade 41+ permissions for approval-related pages
- Follows the same pattern as existing `authenticatedPage` and `adminPage` fixtures

### 2. Updated Test Files

#### accessibility.comprehensive.refactored.spec.ts
- Changed approver tests (line ~183) to use `approverPage` fixture
- Changed admin tests (line ~227) to use `adminPage` fixture
- **Removed test.skip() calls at lines 212 and 256**

#### accessibility.comprehensive.spec.ts
- Migrated from using `@playwright/test` to custom fixtures
- Updated all authenticated tests to use `authenticatedPage` fixture
- Updated approver tests to use `approverPage` fixture
- Updated admin tests to use `adminPage` fixture
- Removed all manual login logic from beforeEach blocks
- Removed BASE_URL constant and references
- **Removed test.skip() calls at lines 136 and 221**

## Results

### Before
```typescript
// Tests conditionally skipped based on runtime checks
if (currentUrl.includes('/admin')) {
    // run test
} else {
    console.log('⚠️ Skipped - User lacks admin permissions');
    test.skip();
}
```

### After
```typescript
// Tests use appropriate role-based fixtures
test('admin test', async ({ adminPage }) => {
    await adminPage.goto(pageInfo.url);
    // run test - no conditional skipping
});
```

### Test Statistics
- **Total test.skip() calls removed**: 4
- **New fixtures added**: 1 (approverPage)
- **Files modified**: 3
- **Test reliability**: Improved - tests now run with proper permissions
- **Code cleanliness**: Removed 50+ lines of conditional logic

## Testing Requirements

To run these tests successfully, ensure:

1. **Database is seeded** with test users:
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Laravel server is running**:
   ```bash
   php artisan serve
   ```

3. **Test users exist** (created by `RoleUserSeeder`):
   - `userstaff@motac.gov.my` / `password` (staff role)
   - `approver@motac.gov.my` / `password` (approver role)
   - `admin@motac.gov.my` / `password` (admin role)

4. **Run tests**:
   ```bash
   npm run test:e2e
   ```

## Benefits

1. ✅ **No dynamic skipping** - Tests execute deterministically
2. ✅ **Better coverage** - All role-specific pages are tested
3. ✅ **Cleaner code** - Removed conditional logic and manual logins
4. ✅ **Better isolation** - Each fixture manages its own authentication
5. ✅ **Consistent patterns** - All tests follow the same fixture-based approach
6. ✅ **Maintainability** - Easier to add new role-based tests

## Next Steps

The tests are now ready to run. To validate:

1. Set up the database: `php artisan migrate:fresh --seed`
2. Start the server: `php artisan serve` (or it will auto-start via playwright.config.ts)
3. Run the accessibility tests: `npm run test:accessibility`
4. Or run all E2E tests: `npm run test:e2e`

Any failures should now be legitimate test failures (not permission-related skips) and can be addressed based on the specific error messages.
