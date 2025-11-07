# Playwright Test Migration - TODO List

## 🎯 Current Status: Phase 1 Complete (20% Done)

**Completed**: 3 of 15+ test files refactored  
**Next**: Fix errors, continue refactoring, validate

---

## ⚠️ CRITICAL: Fix Errors FIRST (UNBLOCK TESTING)

### 1. Fix lint.refactored.spec.ts Line 211
**File**: `tests/e2e/loan.refactored.spec.ts`  
**Error**: RegExp not allowed for selectOption label

**Current Code** (Line 211):

```typescript
await statusFilter.selectOption({ label: /pending|menunggu/i });
```

**Fix** (Choose one):

```typescript
// Option A: Use index
await statusFilter.selectOption({ index: 1 });

// Option B: Use English label
await statusFilter.selectOption({ label: 'Pending' });

// Option C: Use Malay label
await statusFilter.selectOption({ label: 'Menunggu' });
```

**Time**: 2 minutes  
**Priority**: 🔴 CRITICAL

---

### 2. Fix TypeScript Errors in ictserve-fixtures.ts
**File**: `tests/e2e/fixtures/ictserve-fixtures.ts`  
**Errors**: Implicit 'any' types on lines 98, 106

**Current Code**:

```typescript
staffDashboardPage: async ({ authenticatedPage }, use) => {
  // 'authenticatedPage' has implicit any
  // 'use' has implicit any
},

staffLoginPage: async ({ page }, use) => {
  // 'page' has implicit any
  // 'use' has implicit any
}
```

**Fix**:

```typescript
staffDashboardPage: async (
  { authenticatedPage }: { authenticatedPage: Page }, 
  use: (value: StaffDashboardPage) => Promise<void>
) => {
  await use(new StaffDashboardPage(authenticatedPage));
},

staffLoginPage: async (
  { page }: { page: Page }, 
  use: (value: StaffLoginPage) => Promise<void>
) => {
  await use(new StaffLoginPage(page));
}
```

**Time**: 5 minutes  
**Priority**: 🔴 CRITICAL

---

### 3. Delete Corrupted staff-flow-final.spec.ts
**File**: `tests/e2e/staff-flow-final.spec.ts`  
**Status**: Corrupted with duplicate content (528 lines, compilation errors)

**Action**: Delete or revert from git

```bash
# Option A: Delete
rm tests/e2e/staff-flow-final.spec.ts

# Option B: Revert from git
git checkout HEAD -- tests/e2e/staff-flow-final.spec.ts
```

**Replacement**: Use `staff-flow-refactored.spec.ts` instead

**Time**: 1 minute  
**Priority**: 🔴 CRITICAL

---

## 🚀 HIGH PRIORITY: Refactor Remaining Test Files

### Phase 2A: Staff Flow Tests (Priority 1)

#### 4. Refactor staff-flow-optimized.spec.ts
**Current**: Uses base Playwright, CSS selectors, no tags  
**Target**: `staff-flow-optimized.refactored.spec.ts`

**Pattern to Apply**:

- ✅ Import from custom fixtures
- ✅ Use POMs (StaffDashboardPage, StaffLoginPage)
- ✅ Add test tags (@smoke, @staff, @optimization)
- ✅ Use web-first assertions
- ✅ Use user-facing locators
- ✅ Add soft assertions where appropriate

**Estimated Lines**: ~200  
**Estimated Time**: 30 minutes  
**Priority**: 🟠 HIGH

---

#### 5. Review staff-flow-debug.spec.ts
**Current**: Debug-specific test file  
**Decision Needed**: Keep or archive after refactoring?

**Options**:
A. Archive (no refactoring needed) if debugging patterns now in other tests  
B. Refactor if unique debugging scenarios exist  
C. Convert to debugging utility script

**Estimated Time**: 10 minutes (review + decision)  
**Priority**: 🟠 HIGH

---

### Phase 2B: Accessibility Tests (Priority 2)

#### 6. Refactor accessibility.comprehensive.spec.ts
**Current**: Uses base Playwright, accessibility checks  
**Target**: `accessibility.refactored.spec.ts`

**Pattern to Apply**:

- ✅ Import from custom fixtures
- ✅ Add test tags (@smoke, @accessibility, @a11y, @wcag)
- ✅ Use web-first assertions
- ✅ Add soft assertions for multi-element checks
- ✅ Use axe-core integration (if available)

**Special Considerations**:

- Preserve all WCAG 2.2 AA checks
- Add console accessibility error monitoring
- Use soft assertions for multiple violations

**Estimated Lines**: ~300  
**Estimated Time**: 45 minutes  
**Priority**: 🟠 HIGH (WCAG compliance critical)

---

#### 7. Refactor dashboard-accessibility.spec.ts
**Current**: Dashboard-specific accessibility checks  
**Target**: `dashboard-accessibility.refactored.spec.ts`

**Pattern to Apply**:

- ✅ Import from custom fixtures
- ✅ Use StaffDashboardPage POM
- ✅ Add test tags (@smoke, @dashboard, @accessibility, @a11y)
- ✅ Use web-first assertions
- ✅ Add soft assertions

**Estimated Lines**: ~200  
**Estimated Time**: 30 minutes  
**Priority**: 🟠 HIGH

---

### Phase 2C: Responsive & Integration Tests (Priority 3)

#### 8. Refactor staff-dashboard.responsive.spec.ts
**Current**: Responsive design validation  
**Target**: `staff-dashboard.responsive.refactored.spec.ts`

**Pattern to Apply**:

- ✅ Import from custom fixtures
- ✅ Add test tags (@dashboard, @responsive, @mobile, @tablet, @desktop)
- ✅ Use web-first assertions
- ✅ Add viewport size variations

**Viewport Tests**:

- Mobile: 375x667
- Tablet: 768x1024
- Desktop: 1920x1080

**Estimated Lines**: ~250  
**Estimated Time**: 40 minutes  
**Priority**: 🟡 MEDIUM

---

#### 9. Refactor devtools.integration.spec.ts
**Current**: DevTools integration tests  
**Target**: `devtools.refactored.spec.ts`

**Pattern to Apply**:

- ✅ Import from custom fixtures
- ✅ Add test tags (@devtools, @debugging, @integration)
- ✅ Use web-first assertions

**Special Considerations**:

- Preserve CDP (Chrome DevTools Protocol) integration
- Keep performance monitoring logic

**Estimated Lines**: ~200  
**Estimated Time**: 35 minutes  
**Priority**: 🟡 MEDIUM

---

### Phase 2D: Guest Flow Tests (Priority 4)

#### 10. Refactor guest-flow-screenshots.spec.ts
**Current**: Guest user journey with screenshots  
**Target**: `guest-flow.refactored.spec.ts`

**Pattern to Apply**:

- ✅ Import from custom fixtures (use unauthenticated page fixture)
- ✅ Add test tags (@smoke, @guest, @flow, @unauthenticated)
- ✅ Use web-first assertions
- ✅ Use user-facing locators
- ✅ Keep screenshot generation

**Estimated Lines**: ~180  
**Estimated Time**: 30 minutes  
**Priority**: 🟡 MEDIUM

---

### Phase 2E: Legacy Module Tests (Archive After Validation)

#### 11. Verify and Archive helpdesk.module.spec.ts
**Current**: Legacy helpdesk test  
**Replacement**: `helpdesk.refactored.spec.ts` (already exists)

**Action**:

1. Run both versions side-by-side
2. Verify refactored version covers all scenarios
3. Archive legacy version to `tests/e2e/legacy/`

**Time**: 15 minutes  
**Priority**: 🟡 MEDIUM

---

#### 12. Verify and Archive loan.module.spec.ts
**Current**: Legacy loan test  
**Replacement**: `loan.refactored.spec.ts` (already exists)

**Action**:

1. Run both versions side-by-side
2. Verify refactored version covers all scenarios
3. Archive legacy version to `tests/e2e/legacy/`

**Time**: 15 minutes  
**Priority**: 🟡 MEDIUM

---

## 🔧 MEDIUM PRIORITY: Infrastructure Improvements

### 13. Implement Per-Worker Database Seeding
**Purpose**: Complete worker data isolation for true parallel execution safety

**Files to Create/Modify**:

**A. Create Laravel Seeder**:  
`database/seeders/WorkerUserSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkerUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = $this->command->option('email') ?? 'staff.worker0@motac.gov.my';
        $workerIndex = substr($email, strpos($email, 'worker') + 6, 1);
        
        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => "Staff Worker {$workerIndex}",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        )->assignRole('staff');
        
        $this->command->info("Created/updated worker user: {$email}");
    }
}
```

**B. Update Fixture**:  
`tests/e2e/fixtures/ictserve-fixtures.ts`

```typescript
workerStorageState: [async ({}, use, workerInfo) => {
  const uniqueEmail = `staff.worker${workerInfo.workerIndex}@motac.gov.my`;
  
  // Seed unique user for this worker
  await exec(`php artisan db:seed --class=WorkerUserSeeder --email=${uniqueEmail}`);
  
  await use(uniqueEmail);
}, { scope: 'worker' }]
```

**Estimated Time**: 30 minutes  
**Priority**: 🟡 MEDIUM (enables true parallel safety)

---

### 14. Create Missing Page Object Models

**A. Create HelpdeskPage POM**:  
`tests/e2e/pages/helpdesk.page.ts`

```typescript
import { Page, Locator } from '@playwright/test';

export class HelpdeskPage {
  readonly ticketTable: Locator;
  readonly createTicketButton: Locator;
  readonly searchInput: Locator;
  readonly statusFilter: Locator;

  constructor(public readonly page: Page) {
    this.ticketTable = page.getByRole('table');
    this.createTicketButton = page.getByRole('button', { name: /create|new ticket/i });
    this.searchInput = page.getByRole('searchbox');
    this.statusFilter = page.getByLabel(/status|filter/i);
  }

  async goto() {
    await this.page.goto('/helpdesk');
  }

  async createTicket(subject: string, description: string) {
    await this.createTicketButton.click();
    await this.page.getByLabel(/subject|tajuk/i).fill(subject);
    await this.page.getByLabel(/description|keterangan/i).fill(description);
    await this.page.getByRole('button', { name: /submit|hantar/i }).click();
  }

  async searchTickets(query: string) {
    await this.searchInput.fill(query);
    await this.page.keyboard.press('Enter');
  }
}
```

**Estimated Time**: 20 minutes  
**Priority**: 🟡 MEDIUM

---

**B. Create LoanPage POM**:  
`tests/e2e/pages/loan.page.ts`

```typescript
import { Page, Locator } from '@playwright/test';

export class LoanPage {
  readonly loanTable: Locator;
  readonly createLoanButton: Locator;
  readonly searchInput: Locator;
  readonly statusFilter: Locator;

  constructor(public readonly page: Page) {
    this.loanTable = page.getByRole('table');
    this.createLoanButton = page.getByRole('button', { name: /create|new loan/i });
    this.searchInput = page.getByRole('searchbox');
    this.statusFilter = page.getByLabel(/status|filter/i);
  }

  async goto() {
    await this.page.goto('/loan');
  }

  async createLoan(assetId: string, startDate: string, endDate: string) {
    await this.createLoanButton.click();
    await this.page.getByLabel(/asset|aset/i).fill(assetId);
    await this.page.getByLabel(/start date|tarikh mula/i).fill(startDate);
    await this.page.getByLabel(/end date|tarikh tamat/i).fill(endDate);
    await this.page.getByRole('button', { name: /submit|hantar/i }).click();
  }

  async searchLoans(query: string) {
    await this.searchInput.fill(query);
    await this.page.keyboard.press('Enter');
  }
}
```

**Estimated Time**: 20 minutes  
**Priority**: 🟡 MEDIUM

---

**C. Create ProfilePage POM**:  
`tests/e2e/pages/profile.page.ts`

```typescript
import { Page, Locator } from '@playwright/test';

export class ProfilePage {
  readonly nameInput: Locator;
  readonly emailInput: Locator;
  readonly saveButton: Locator;

  constructor(public readonly page: Page) {
    this.nameInput = page.getByLabel(/name|nama/i);
    this.emailInput = page.getByLabel(/email|e-mel/i);
    this.saveButton = page.getByRole('button', { name: /save|simpan/i });
  }

  async goto() {
    await this.page.goto('/profile');
  }

  async updateProfile(name: string, email: string) {
    await this.nameInput.fill(name);
    await this.emailInput.fill(email);
    await this.saveButton.click();
  }
}
```

**Estimated Time**: 15 minutes  
**Priority**: 🟡 MEDIUM

---

### 15. Update package.json Scripts
**Purpose**: Add tag-based test execution convenience scripts

**File**: `package.json`

**Add these scripts**:

```json
{
  "scripts": {
    "test:e2e:smoke": "playwright test --grep @smoke",
    "test:e2e:staff": "playwright test --grep @staff",
    "test:e2e:helpdesk": "playwright test --grep @helpdesk",
    "test:e2e:loan": "playwright test --grep @loan",
    "test:e2e:module": "playwright test --grep @module",
    "test:e2e:accessibility": "playwright test --grep @accessibility",
    "test:e2e:responsive": "playwright test --grep @responsive",
    "test:e2e:refactored": "playwright test --grep refactored",
    "test:e2e:headed": "playwright test --headed",
    "test:e2e:debug": "playwright test --debug",
    "test:e2e:ui": "playwright test --ui"
  }
}
```

**Estimated Time**: 5 minutes  
**Priority**: 🟡 MEDIUM

---

## ✅ VALIDATION: Run Tests & Verify Quality

### 16. Run All Refactored Tests Locally
**Commands**:

```bash
# Run all refactored tests
npm run test:e2e -- --grep refactored

# Run smoke tests only (fastest validation)
npm run test:e2e -- --grep @smoke

# Run with headed mode to watch
npm run test:e2e -- --grep refactored --headed

# Run with UI mode for debugging
npm run test:e2e -- --grep refactored --ui
```

**Expected Issues**:

- Some locators may not match actual DOM
- Timing issues may require waitForLoadState adjustments
- POM methods may need updates

**Actions on Failure**:

1. Check trace viewer for exact failure point
2. Update locators to match actual DOM
3. Add wait conditions if needed
4. Re-run specific test

**Estimated Time**: 1 hour (including fixes)  
**Priority**: 🟠 HIGH

---

### 17. Validate CI Pipeline Execution
**Purpose**: Ensure tests pass in CI environment (GitHub Actions)

**Commands** (in CI):

```yaml
- name: Run E2E Tests
  run: npm run test:e2e -- --trace on-first-retry
  
- name: Upload Playwright Report
  if: always()
  uses: actions/upload-artifact@v3
  with:
    name: playwright-report
    path: playwright-report/
```

**Checks**:

- [ ] Tests pass with 2 workers (CI default)
- [ ] Trace artifacts uploaded on failure
- [ ] No flaky tests (run 3 times to verify)
- [ ] Execution time < 15 minutes

**Estimated Time**: 30 minutes  
**Priority**: 🟠 HIGH

---

### 18. Performance Benchmark
**Purpose**: Verify parallelization improvements

**Metrics to Measure**:

- Sequential execution time (1 worker)
- Parallel execution time (2 workers CI, 4 workers local)
- Test stability (pass rate over 10 runs)

**Commands**:

```bash
# Benchmark sequential
npm run test:e2e -- --workers=1 --repeat-each=3

# Benchmark parallel (local)
npm run test:e2e -- --workers=4 --repeat-each=3

# Benchmark parallel (CI simulation)
npm run test:e2e -- --workers=2 --repeat-each=3
```

**Target Metrics**:

- Sequential: ~15 minutes
- Parallel (4 workers): ~8 minutes (50% improvement)
- Parallel (2 workers): ~10 minutes (33% improvement)
- Stability: 100% pass rate

**Estimated Time**: 30 minutes  
**Priority**: 🟡 MEDIUM

---

## 📚 LOW PRIORITY: Documentation & Polish

### 19. Update BEST_PRACTICES.md
**Sections to Add**:

1. **Test Tags and Filtering**

```markdown
## Test Tags

All tests use tags for granular filtering:

- `@smoke` - Critical path tests (run first)
- `@module` - Module-level tests
- `@form` - Form validation tests
- `@accessibility` - WCAG compliance tests

### Running Tagged Tests

```bash
# Run only smoke tests
npm run test:e2e:smoke

# Run specific module
npm run test:e2e:helpdesk

# Exclude slow tests
npm run test:e2e -- --grep-invert @slow
```

```

2. **VS Code Extension Usage** (Research Priority 5)
```markdown
## VS Code Playwright Extension

Install: https://marketplace.visualstudio.com/items?itemName=ms-playwright.playwright

Features:
- Run/debug single test
- Pick locators interactively
- View test results inline
- Generate tests from user actions

### Usage

1. Open test file
2. Click play button next to test
3. Or use Command Palette: "Playwright: Run Test"
```

**Estimated Time**: 30 minutes  
**Priority**: 🟢 LOW

---

### 20. Update README.md
**Section to Add**: E2E Testing

```markdown
## E2E Testing

### Quick Start

```bash
# Run all tests
npm run test:e2e

# Run smoke tests only
npm run test:e2e:smoke

# Run with UI (interactive mode)
npm run test:e2e:ui
```

### Test Organization

- **fixtures/** - Custom fixtures for authenticated testing
- **pages/** - Page Object Models
- **tests/** - Test specifications

### Test Tags

Use tags for granular test execution:

- `@smoke` - Critical path tests
- `@staff` - Staff user flow tests
- `@helpdesk` - Helpdesk module tests
- `@loan` - Loan module tests
- `@accessibility` - WCAG compliance tests

### Debugging Failed Tests

```bash
# Run with trace viewer
npm run test:e2e -- --trace on

# Then view trace
npx playwright show-trace trace.zip
```

See [BEST_PRACTICES.md](tests/e2e/BEST_PRACTICES.md) for detailed guidelines.

```

**Estimated Time**: 20 minutes  
**Priority**: 🟢 LOW

---

### 21. Clean Up Legacy Files
**Purpose**: Remove deprecated test files after validation

**Action Plan**:
1. Create `tests/e2e/legacy/` directory
2. Move deprecated files:
   - `staff-flow-final.spec.ts` (corrupted)
   - `staff-flow-optimized.spec.ts` (after refactoring)
   - `staff-flow-debug.spec.ts` (after review decision)
   - `helpdesk.module.spec.ts` (after validation)
   - `loan.module.spec.ts` (after validation)
3. Update `.gitignore` to exclude legacy directory
4. Document migration in `tests/e2e/MIGRATION_NOTES.md`

**Commands**:
```bash
mkdir tests/e2e/legacy
mv tests/e2e/staff-flow-final.spec.ts tests/e2e/legacy/
# ... move other files after validation ...
```

**Estimated Time**: 15 minutes  
**Priority**: 🟢 LOW (do after full validation)

---

## 📊 Progress Tracking

### Current Sprint (Week 1)

- [x] Research Playwright best practices (Priority 1-5)
- [x] Implement worker data isolation structure
- [x] Refactor staff-flow-refactored.spec.ts
- [x] Refactor helpdesk.refactored.spec.ts
- [x] Refactor loan.refactored.spec.ts
- [ ] Fix critical errors (Tasks 1-3)
- [ ] Run validation tests (Task 16)

### Next Sprint (Week 2)

- [ ] Refactor remaining staff flow tests (Tasks 4-5)
- [ ] Refactor accessibility tests (Tasks 6-7)
- [ ] Implement per-worker database seeding (Task 13)
- [ ] Create missing POMs (Task 14)
- [ ] Update package.json scripts (Task 15)
- [ ] CI validation (Task 17)

### Future Sprint (Week 3)

- [ ] Refactor responsive/integration tests (Tasks 8-9)
- [ ] Refactor guest flow tests (Task 10)
- [ ] Archive legacy files (Tasks 11-12, 21)
- [ ] Performance benchmarking (Task 18)
- [ ] Documentation updates (Tasks 19-20)

---

## 🎯 Success Criteria

**Definition of Done**:

- [ ] All test files migrated to refactored architecture
- [ ] Zero TypeScript/lint errors
- [ ] 100% test pass rate locally (4 workers)
- [ ] 100% test pass rate in CI (2 workers)
- [ ] Execution time < 15 minutes in CI
- [ ] All legacy files archived
- [ ] Documentation updated
- [ ] Team trained on new patterns

**Quality Gates**:

- [ ] All tests use custom fixtures
- [ ] All tests use Page Object Models
- [ ] All tests use web-first assertions
- [ ] All tests use user-facing locators
- [ ] All tests have appropriate tags
- [ ] All tests include soft assertions where appropriate
- [ ] All module tests include console error monitoring

---

## 📝 Notes

**Key Decisions**:

- ✅ Use *.refactored.spec.ts naming for new files (preserve legacy during transition)
- ✅ Create new files instead of in-place edits (safer for large refactorings)
- ✅ Apply all Priority 1-4 improvements in all new tests
- ✅ Use test tags for granular filtering

**Lessons Learned**:

- ❌ In-place edits risky for large refactorings (staff-flow-final corruption)
- ✅ New file creation strategy works reliably
- ✅ Soft assertions valuable for comprehensive validation
- ✅ Console error monitoring catches frontend issues early

**Future Considerations**:

- Visual regression testing (Percy/Applitools)
- API testing with Playwright
- Performance testing integration
- Cross-browser testing (Firefox, WebKit)
- Mobile responsive testing
- Load testing

---

**Last Updated**: November 7, 2025  
**Next Review**: After Task 16 completion (validation)  
**Estimated Completion**: 2-3 sprints (6-9 weeks)
