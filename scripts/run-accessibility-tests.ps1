# Run Playwright accessibility tests non-interactively
# This script runs a subset of accessibility tests to verify fixes

$env:CI = "false"
$env:SKIP_PERCY = "true"

Write-Host "Running accessibility tests..." -ForegroundColor Cyan

# Run only the first few accessibility tests to verify fixes
npx playwright test tests/e2e/accessibility.comprehensive.spec.ts `
    --grep "01-1|01-2|01-3" `
    --project=chromium `
    --reporter=line `
    --timeout=120000 `
    --retries=0

Write-Host "Tests completed." -ForegroundColor Green
