# oard Manual Testing Guide

## Overview

This guide provides step-by-step instructions for manually testing the staff dashboard after the route fix implementation. All automated tests are passing, but manual verification ensures the user experience meets expectations.

## Prerequisites

- Local development environment running (`php artisan serve` or XAMPP)
- Database seeded with test users
- Browser with developer tools (Chrome/Firefox recommended)

## Test Environment Setup

### 1. Create Test Users

```bash
# Create regular staff user
php artisan tinker
$user = User::factory()->create(['email' => 'staff@test.com', 'password' => bcrypt('password')]);

# Create approver user (Grade 41+)
$approver = User::factory()->create(['email' => 'approver@test.com', 'password' => bcrypt('password')]);
$approver->assignRole('approver');
```

### 2. Create Test Data

```bash
# Create some test tickets
HelpdeskTicket::factory()->count(3)->create(['user_id' => $user->id, 'status' => 'open']);

# Create some test loan applications
LoanApplication::factory()->count(2)->create(['user_id' => $user->id, 'status' => 'submitted']);
```

## Manual Testing Checklist

### ✅ Task 1: Access `/dashboard` as Authenticated User

#### Test 1.1: Dashboard Loads Without Errors

**Steps:**

1. Open browser and navigate to `http://localhost:8000/login`
2. Login with test credentials: `staff@test.com` / `password`
3. After login, verify automatic redirect to `/dashboard`
4. Open browser DevTools (F12) and check Console tab

**Expected Results:**

- ✅ Dashboard loads successfully (HTTP 200)
- ✅ No JavaScript errors in console
- ✅ No PHP errors displayed
- ✅ Page title shows "Papan Pemuka" (Dashboard in Malay)
- ✅ Welcome message displays: "Selamat kembali, [User Name]"

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

---

#### Test 1.2: Statistics Grid Displays with Correct Columns

**Steps:**

1. While logged in as regular staff user, observe the statistics grid
2. Count the number of statistic cards displayed
3. Verify card titles and icons

**Expected Results for Regular Staff (3 cards):**

- ✅ Card 1: "Tiket Terbuka Saya" (My Open Tickets) - Blue clipboard icon
- ✅ Card 2: "Pinjaman Menunggu Saya" (My Pending Loans) - Orange clock icon
- ✅ Card 3: "Item Tertunggak" (Overdue Items) - Red warning icon
- ✅ NO "Kelulusan Menunggu" (Pending Approvals) card visible

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

**Steps for Approver:**

1. Logout and login as approver: `approver@test.com` / `password`
2. Navigate to `/dashboard`
3. Count the number of statistic cards displayed

**Expected Results for Approver (4 cards):**

- ✅ All 3 cards from regular staff PLUS
- ✅ Card 4: "Kelulusan Menunggu" (Pending Approvals) - Green checkmark icon

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

---

#### Test 1.3: Recent Activity Sections Display

**Steps:**

1. Login as staff user with test data
2. Scroll down to "Recent Activity" section
3. Verify both sections are visible

**Expected Results:**

- ✅ "Tiket Terkini Saya" (My Recent Tickets) section visible on left
- ✅ "Pinjaman Terkini Saya" (My Recent Loans) section visible on right
- ✅ Recent tickets display with ticket numbers (e.g., HD2025075708)
- ✅ Recent loans display with application numbers
- ✅ Status badges display with correct colors
- ✅ "Lihat Semua Tiket" (View All Tickets) link at bottom
- ✅ "Lihat Semua Pinjaman" (View All Loans) link at bottom

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

---

#### Test 1.4: Quick Action Buttons are Functional

**Steps:**

1. Locate the "Tindakan Pantas" (Quick Actions) section
2. Verify all three buttons are visible
3. Click each button and verify navigation

**Expected Results:**

- ✅ Button 1: "Tiket Baharu" (New Ticket) - Blue button with plus icon
  - Clicking navigates to `/helpdesk/create`
- ✅ Button 2: "Mohon Pinjaman" (Request Loan) - Blue button with plus icon
  - Clicking navigates to `/loan/apply`
- ✅ Button 3: "Lihat Semua Perkhidmatan" (View All Services) - White button with globe icon
  - Clicking navigates to `/` (welcome page)

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

---

### Additional Verification Tests

#### Test 1.5: Responsive Layout

**Steps:**

1. Open browser DevTools (F12)
2. Toggle device toolbar (Ctrl+Shift+M)
3. Test different viewport sizes

**Expected Results:**

- ✅ Mobile (320px-414px): Statistics cards stack in 1 column
- ✅ Tablet (768px-1024px): Statistics cards display in 2 columns
- ✅ Desktop (1280px+): Statistics cards display in 4 columns
- ✅ All elements remain accessible and readable at all sizes

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

---

#### Test 1.6: Refresh Functionality

**Steps:**

1. On dashboard, locate "Muat Semula" (Refresh) button in top-right
2. Click the refresh button
3. Observe loading indicator

**Expected Results:**

- ✅ Loading overlay appears with spinner
- ✅ "Memuatkan semula..." (Reloading...) message displays
- ✅ Dashboard data refreshes
- ✅ Statistics update if data changed
- ✅ No page reload (Livewire AJAX update)

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

---

#### Test 1.7: Auto-Refresh (wire:poll)

**Steps:**

1. Stay on dashboard for 30+ seconds
2. Observe network activity in DevTools (Network tab)

**Expected Results:**

- ✅ Dashboard automatically polls for updates every 30 seconds
- ✅ XHR requests to Livewire endpoint visible in Network tab
- ✅ No full page reloads

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

---

#### Test 1.8: Guest User Protection

**Steps:**

1. Logout from the application
2. Manually navigate to `http://localhost:8000/dashboard`

**Expected Results:**

- ✅ Redirected to `/login` page
- ✅ Cannot access dashboard without authentication

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

---

## Accessibility Testing (WCAG 2.2 AA)

### Test 1.9: Keyboard Navigation

**Steps:**

1. Login and navigate to dashboard
2. Press Tab key repeatedly
3. Verify focus indicators and tab order

**Expected Results:**

- ✅ Focus indicators visible (3-4px outline, 2px offset)
- ✅ Tab order: Skip links → Refresh button → Statistics cards → Quick actions → Recent activity
- ✅ All interactive elements reachable via keyboard
- ✅ Enter key activates buttons and links

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

---

### Test 1.10: Screen Reader Compatibility

**Steps:**

1. Enable screen reader (NVDA on Windows, VoiceOver on Mac)
2. Navigate through dashboard with screen reader

**Expected Results:**

- ✅ Page title announced: "Papan Pemuka"
- ✅ Statistics cards have proper ARIA labels
- ✅ Loading states announced via ARIA live regions
- ✅ Status badges have proper role attributes
- ✅ Decorative icons have aria-hidden="true"

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

---

### Test 1.11: Color Contrast

**Steps:**

1. Open browser DevTools
2. Inspect text elements
3. Use contrast checker (e.g., Chrome DevTools Accessibility panel)

**Expected Results:**

- ✅ Primary blue (#0056b3): 6.8:1 contrast ratio
- ✅ Success green (#198754): 4.9:1 contrast ratio
- ✅ Warning orange (#ff8c00): 4.5:1 contrast ratio
- ✅ Danger red (#b50c0c): 8.2:1 contrast ratio
- ✅ All text meets 4.5:1 minimum
- ✅ All UI components meet 3:1 minimum

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

---

### Test 1.12: Touch Targets

**Steps:**

1. Use mobile device or browser mobile emulation
2. Measure interactive element sizes

**Expected Results:**

- ✅ Refresh button: Minimum 44×44px
- ✅ Quick action buttons: Minimum 44×44px
- ✅ Statistics card links: Full card clickable
- ✅ Activity item links: Full item clickable

**Actual Results:**

- [ ] Pass
- [ ] Fail (describe issue): _______________

---

## Performance Testing

### Test 1.13: Page Load Performance

**Steps:**

1. Open browser DevTools → Performance tab
2. Reload dashboard page
3. Record performance metrics

**Expected Results:**

- ✅ Dashboard loads within 2 seconds
- ✅ Largest Contentful Paint (LCP) < 2.5s
- ✅ First Input Delay (FID) < 100ms
- ✅ Cumulative Layout Shift (CLS) < 0.1

**Actual Results:**

- LCP: _____ seconds
- FID: _____ ms
- CLS: _____
- [ ] Pass
- [ ] Fail (describe issue): _______________

---

### Test 1.14: Cache Performance

**Steps:**

1. Clear application cache: `php artisan cache:clear`
2. Load dashboard (first load - cache miss)
3. Reload dashboard (second load - cache hit)
4. Compare load times

**Expected Results:**

- ✅ First load: Data fetched from database
- ✅ Second load: Data served from cache (faster)
- ✅ Cache hit rate > 80%

**Actual Results:**

- First load time: _____ ms
- Second load time: _____ ms
- [ ] Pass
- [ ] Fail (describe issue): _______________

---

## Browser Compatibility Testing

### Test 1.15: Cross-Browser Testing

**Browsers to Test:**

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

**Expected Results:**

- ✅ Dashboard displays correctly in all browsers
- ✅ All functionality works in all browsers
- ✅ No browser-specific errors

**Actual Results:**

- Chrome: [ ] Pass [ ] Fail
- Firefox: [ ] Pass [ ] Fail
- Safari: [ ] Pass [ ] Fail
- Edge: [ ] Pass [ ] Fail

---

## Issue Reporting Template

If any test fails, use this template to report the issue:

```
**Test ID:** [e.g., Test 1.2]
**Test Name:** [e.g., Statistics Grid Displays with Correct Columns]
**Status:** FAIL
**Browser:** [e.g., Chrome 120]
**User Role:** [e.g., Regular Staff]

**Steps to Reproduce:**
1. [Step 1]
2. [Step 2]
3. [Step 3]

**Expected Result:**
[What should happen]

**Actual Result:**
[What actually happened]

**Screenshots:**
[Attach screenshots if applicable]

**Console Errors:**
[Copy any JavaScript errors from console]

**Additional Notes:**
[Any other relevant information]
```

---

## Automated Test Results

All automated tests are passing:

```
✓ it counts open tickets including assigned and pending user statuses
✓ it returns recent tickets assigned to the authenticated user
✓ authenticated user can access dashboard route
✓ dashboard displays statistics grid for regular staff
✓ dashboard displays statistics grid with approvals for approver
✓ dashboard displays recent activity sections
✓ dashboard displays quick action buttons
✓ dashboard refresh invalidates cache
✓ guest user cannot access dashboard

Tests: 9 passed (21 assertions)
```

---

## Sign-Off

**Tester Name:** _______________
**Date:** _______________
**Overall Status:** [ ] Pass [ ] Fail
**Notes:** _______________

---

## Next Steps

After completing manual testing:

1. ✅ Mark Task 1 as complete in tasks.md
2. ⏭️ Proceed to Task 2: Test with different user roles
3. ⏭️ Proceed to Task 3: Test with different data states
4. ⏭️ Proceed to Task 4: Test responsive behavior
5. ⏭️ Proceed to Task 5: Test accessibility
6. ⏭️ Proceed to Task 6: Test performance

---

**Document Version:** 1.0  
**Last Updated:** 2025-11-05  
**Status:** Ready for Manual Testing

Staff Das
