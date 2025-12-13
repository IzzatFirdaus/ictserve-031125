# Agile Testing Guide — ICTServe

This document summarizes the agile testing workflow for ICTServe, focused on running small/targeted tests during development (file-by-file) and full-suite validation before merging.

## Fast Loop (Local dev)
- Run a single unit file:
```powershell
php artisan test tests/Unit/Services/FooServiceTest.php
```
- Run a single feature file:
```powershell
php artisan test tests/Feature/FooFeatureTest.php
```
- Run a single test method (filter):
```powershell
php artisan test tests/Feature/FooFeatureTest.php --filter=test_method_name
```
- Run a single Playwright E2E spec:
```powershell
npx playwright test tests/e2e/loan.spec.ts
```

## Shortcut scripts
- `scripts/run-test.ps1` — wrapper to run PHPUnit or Playwright tests quickly.
  - Example: `powershell -File .\scripts\run-test.ps1 -File tests/Feature/ExampleTest.php -Filter the_application_returns_a_successful_response -Kind phpunit`
- `scripts/test-changed.ps1` — runs tests for changed files in a git range. Use in CI or locally to test only affected files.
  - Example: `powershell -File .\scripts\test-changed.ps1 -Range HEAD~1..HEAD`

## E2E (Playwright) Troubleshooting
On Windows, `npm ci` may fail when native modules are being used by other processes; common causes include:
- An editor (VSCode) or language server holding file handles
- Antivirus scanning or locking native files

Suggested steps to resolve EPERM issues:
1. Stop running Node processes that might hold file handles:
```powershell
Get-Process node -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue
```
2. Close VSCode and any other Node-based tool or language service.
3. Delete `node_modules` and clean install:
```powershell
Remove-Item -Recurse -Force .\node_modules -ErrorAction SilentlyContinue
npm ci
npm run build
npx playwright install
```
4. If deletion fails due to permissions, take ownership, grant permissions, and retry:
```powershell
takeown /f "<path>" /r /d y
icacls "<path>" /grant "Users:F" /T
Remove-Item -Recurse -Force .\node_modules
```
5. If all else fails: reboot and re-run the steps as Admin — a reboot clears stuck handles.

## Pre-commit / CI checks
- Full PHPUnit suite (php artisan test) + coverage gate:
```powershell
composer run test
php artisan test --coverage --min=80
```
- Full E2E suite:
```powershell
npm ci
npm run build
npx playwright install
npm run test:e2e
```

## GitHub Action suggestion
Add a workflow to run `scripts/test-changed.ps1` in PRs using a Windows or Ubuntu runner. This provides accurate, fast feedback by executing only the tests that are impacted by the PR.

## Troubleshooting & Other notes
- Some Livewire/Blade tests require built assets — ensure `npm run build` is run for tests that assert CSS or JS behavior.
- Accessibility tests may require Playwright browsers and built CSS/JS; run on GitHub Actions to reproduce consistently if local environment is blocked.

---
Please follow these steps in the order shown for a smooth developer experience.
