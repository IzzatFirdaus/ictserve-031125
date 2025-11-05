# Quick Start: Accessibility Testing

## 🚀 Run Tests in 3 Steps

### Step 1: Start Development Server

```bash
# Option A: Full development stack
composer run dev

# Option B: Just Laravel server
php artisan serve
```

### Step 2: Run Accessibility Tests

```bash
# Quick test (console output only)
npm run test:accessibility

# Generate reports (HTML + JSON)
npm run test:accessibility:report

# Complete suite (tests + reports)
npm run test:accessibility:all
```

### Step 3: View Results

```bash
# Open HTML report in browser
start test-results/accessibility-reports/accessibility-report.html
```

## 📊 What Gets Tested

### Guest Pages (6 pages)

- Welcome Page
- Accessibility Statement
- Contact Page
- Services Page
- Helpdesk Ticket Form
- Asset Loan Application Form

### Authenticated Pages (4 pages)

- Staff Dashboard
- User Profile
- Submission History
- Claim Submissions

### Approver Pages (1 page)

- Approval Interface (Grade 41+)

### Admin Pages (4 pages)

- Admin Dashboard
- Helpdesk Tickets Management
- Loan Applications Management
- Assets Management

### Mobile Tests (3 pages)

- Welcome Page (Mobile)
- Helpdesk Form (Mobile)
- Loan Application Form (Mobile)

**Total: 18 comprehensive accessibility tests**

## ✅ Success Criteria

- **Target**: 0 critical and serious violations
- **Goal**: 100/100 Lighthouse accessibility score
- **Standard**: WCAG 2.2 Level AA compliance

## 🔍 What Gets Checked

### WCAG 2.2 Level AA

- ✅ Color contrast (4.5:1 text, 3:1 UI)
- ✅ Keyboard accessibility
- ✅ Focus indicators (3:1 contrast minimum)
- ✅ Touch target size (44x44px minimum)
- ✅ Semantic HTML and ARIA
- ✅ Form labels and error messages
- ✅ Skip links and landmarks
- ✅ Heading hierarchy
- ✅ Alt text for images
- ✅ ARIA live regions

## 📝 Report Contents

### HTML Report

- Summary statistics (violations, passes, compliance rate)
- Per-page results with violation details
- Affected elements with HTML snippets
- Links to WCAG documentation
- Severity classification (critical, serious, moderate, minor)

### JSON Report

- Programmatic access to all test results
- Suitable for CI/CD integration
- Can be parsed with jq or other tools

## 🛠️ Troubleshooting

### Issue: Tests fail to connect to server
**Solution**: Ensure Laravel server is running on <http://localhost:8000>

### Issue: Authentication tests fail
**Solution**: Ensure database is seeded with test users:

```bash
php artisan migrate:fresh --seed
```

### Issue: Reports not generated
**Solution**: Ensure test-results directory exists:

```bash
mkdir -p test-results/accessibility-reports
```

## 📚 Full Documentation

For detailed information, see:

- `test-results/ACCESSIBILITY_TESTING_GUIDE.md` - Complete testing guide
- `test-results/TASK_10_1_COMPLETION_SUMMARY.md` - Implementation details

## 🎯 Next Steps

After automated testing (Task 10.1):

1. **Task 10.2**: Manual testing with screen readers
2. **Task 10.3**: Fix identified issues
3. **Task 10.4**: Cross-browser testing
4. **Task 10.5**: Final validation

---

**Quick Reference** | **Task 10.1** | **WCAG 2.2 Level AA**
