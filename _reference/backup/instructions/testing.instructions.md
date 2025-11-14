---
applyTo: '**'
---

# Testing Instructions

Purpose
Defines mandatory testing standards, traceability, workflows, and compliance for automated and manual testing in ICTServe (Laravel 12, PHPUnit, Livewire/Volt, Filament). This document is normative for developers, QA, and maintainers. Test artifacts MUST be traceable to D03 (Requirements), D04/D11 (Design/Technical), and D07–D08 (Integration & test plans).

Scope
Applies to all automated and manual tests including:

- Unit tests: logic-level (tests/Unit)
- Feature tests: HTTP, database transactions (tests/Feature)
- Livewire / Volt component tests (tests/Feature or tests/Browser)
- Integration & end-to-end tests (integration folder or CI jobs)
- Accessibility tests and UI smoke checks (axe, Lighthouse)
- Test data factories, seeders, and test-specific migration setups
(See D00, D03, D04, D10, D11, D12–D14)

Standards & References (Mandatory)

- D00–D14 documentation set (traceability)
- ISO/IEC/IEEE 29148 (requirements engineering), ISO 9001 (QA)
- SQuaRE / ISO/IEC/IEEE 25000 guidance for quality metrics
- Project-specific code quality: PHPStan (level configured), Pint formatting, PSR-12


Traceability (Mandatory)

- Each test case that proves a requirement or design MUST reference SRS/Design IDs (D03/D04/D11) in the test docblock or test method docstring.
  - Example top-of-file test header:
    ```php

    // test: LoanApprovalTest
    // trace: SRS-FR-005; D04 §4.2; D11 §7
    ```

- Update the RTM and link test identifiers to requirement IDs when adding new tests for a requirement.
- Test run artifacts (CI logs, coverage reports) must be retained and auditable per D10/D11.


Mandatory Rules

- Use PHPUnit via Laravel test runner: php artisan test (CI uses phpunit binary / artisan wrapper).
- Put unit tests in tests/Unit and feature/integration in tests/Feature.
- Livewire tests: use Livewire::test(Component::class). Volt tests: Volt::test(...) where applicable.
- Authenticate in tests when endpoints/components require authorization: actingAs($user) / $this->actingAs($user).
- Use factories and seeders for test data; prefer database transactions or in-memory sqlite for speed where feasible.
- Tests MUST be deterministic: avoid network calls, time-based flakes (use time helpers or fakes).
- Run static analysis & formatting checks as part of test pipeline: vendor/bin/phpstan, vendor/bin/pint.
- Accessibility: include automated accessibility scans (axe/lighthouse) for UI-affecting PRs; attach reports to PR.
- Test metadata: include traceability comment and purpose at top of complex tests.
- Test coverage: aim for targeted coverage (critical paths >= 80%); CI should fail on major regressions.
- All test runs and results are auditable: CI artifacts, coverage reports, and accessibility outputs must be stored for PR and release traceability.


Test File & Documentation Requirements

- Test files should contain a short docblock indicating:
  - test name, description, author, traceability IDs, and last-updated date.
  - Example:
    ```php

    /**

     * tests/Feature/LoanApprovalTest.php
     * Purpose: Verify that division head can approve loans.
     * Trace: SRS-FR-005; D04 §4.2; D11 §7
     * Author: dev-team@motac.gov.my
     * Last-updated: 2025-10-21
     */

    ```

- Add a short human-readable test plan in docs/tests/<feature>.md for complex workflows (purpose, preconditions, data setup, expected outcomes).
- For accessibility and E2E tests, store the generated report files under artifacts/ or CI job artifacts so they are retained with PR.


Step-by-step workflow for adding tests

1. Identify the requirement/design the change implements (D03/D04) and record SRS/design IDs.
2. Add/modify code and implement corresponding tests:
   - Unit tests for pure logic/services.
   - Feature tests for controller/API behavior (including HTTP status and DB state).
   - Livewire/Volt tests for interactive components.
   - Accessibility checks for UI changes.
3. Use factories/seeders to create test data; keep data set minimal and deterministic.
4. Document traceability in test header and PR body.
5. Run locally: composer install, vendor/bin/phpstan, vendor/bin/pint, php artisan test.
6. Open PR with tests attached and CI passing.
7. After merge, update RTM and test plan docs as needed.


CI / Example pipeline (PR checks - recommended)

- Lint & static analysis: vendor/bin/phpstan analyse app/ --level configured
- Formatting: vendor/bin/pint --test
- Install JS deps & build preview (if frontend touched): npm ci && npm run build
- Unit & Feature tests: php artisan test --testsuite=unit, php artisan test --testsuite=feature
- Livewire/Volt tests: run via php artisan test in CI
- Accessibility: run headless axe/lighthouse on preview environment
- Coverage & reports: generate and upload coverage and accessibility artifacts


Example GitHub Actions snippet (minimal)
```yaml

name: CI
on: [pull_request, push]
jobs:
  tests:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8
        env: MYSQL_ROOT_PASSWORD: root
        ports: ['3306:3306']
        options: >-
          --health-cmd="mysqladmin ping --silent"
          --health-interval=10s --health-timeout=5s --health-retries=3
    steps:

      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:

          php-version: 8.2

      - name: Install deps
        run: composer install --no-progress --prefer-dist

      - name: Static analysis & format check
        run: vendor/bin/phpstan analyse && vendor/bin/pint --test

      - name: Run tests
        run: php artisan test --parallel --coverage

      - name: Upload coverage
        uses: actions/upload-artifact@v4

        with:
          name: coverage-report
          path: storage/coverage
```

Testing Types & Guidance

Unit Tests

- Fast, isolated. Mock external services and repositories.
- Place in tests/Unit.
- Use Pest or PHPUnit; keep descriptive test names.
- Example:
  ```php

  test('calculate fine for overdue loan', function ()
      $loan = Loan::factory()->make([...]);
      expect((new LoanCalculator)->calculateFine($loan))->toBe(0);
  );
  ```

Feature Tests

- Test HTTP endpoints, validation, DB state, middleware, response codes.
- Use RefreshDatabase or DatabaseTransactions trait depending on environment.
- Example:
  ```php

  public function test_division_head_can_approve()

      $loan = Loan::factory()->create(['status' => 'PENDING']);
      $approver = User::factory()->asDivisionHead()->create();
      $this->actingAs($approver)
           ->patch(route('loans.approve', $loan), ['remarks' => 'OK'])
           ->assertStatus(302);
      $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => 'APPROVED']);

  ```

Livewire / Volt Component Tests

- Use Livewire::test(Component::class) and assert state, emitted events, rendered HTML.
- Authenticate where necessary.
- Example:
  ```php

  Livewire::test(UserTable::class)
      ->set('search', 'Ali')
      ->assertSee('Ali Bin Ahmad');
  ```

Accessibility & UI Tests

- For UI changes run axe-core or lighthouse in CI (headless).
- Include manual checks for keyboard navigation and screen reader smoke tests before release.
- Attach accessibility report to PR and resolve critical/high violations before merge.


Integration & E2E Tests

- Use a real test environment (staging or ephemeral test environment) when testing cross-service flows (LDAP, email, background jobs).
- Prefer contract/integration tests for API interactions; mock only third-party endpoints when not available.


Test Data & Factories

- Add robust factories in database/factories for domain models.
- Use faker locale 'ms_MY' where language-sensitive fields are relevant.
- Keep seed data small for CI speed; heavy fixtures only in dedicated integration jobs.


Flaky Tests & Time-dependent Tests

- Avoid real timeouts; use Carbon::setTestNow() or time fakes.
- For external HTTP interactions, use Http::fake() or mock client responses.
- Track flaky tests in a Flaky Test log; fix promptly—do not permanently skip without approval.


Coverage & Quality Gates

- Maintain meaningful coverage on critical modules; CI should fail on dropped coverage beyond threshold (configurable).
- Use coverage artifacts for release notes and traceability.


PR Checklist (must be included in PR description)

- [ ] Tests added/updated for new/changed behavior
- [ ] Traceability IDs included in test headers and PR body (D03/D04/D11)
- [ ] phpstan & pint checks pass locally
- [ ] php artisan test passes locally
- [ ] Livewire/Volt tests included for component changes
- [ ] Accessibility scans attached or run in CI for UI changes
- [ ] Test data/factories updated
- [ ] CI passes and artifacts (coverage/accessibility reports) attached
- [ ] Reviewer(s): dev lead, QA, security (if relevant)


Test Reporting & Auditing

- CI must publish:
  - Test results (JUnit or similar format)
  - Coverage reports (HTML + summary)
  - Accessibility reports (axe/lighthouse output)
- Retain artifacts per project retention policy. Link artifacts in PR for audit and traceability.


Test Environments & Execution

- Use ephemeral/staging environments for integration and E2E tests.
- For PR previews, create ephemeral preview environments or run headless accessibility checks against test server.
- Document environment variables and secrets required for integration tests in docs/tests/env.example (do NOT commit secrets).


Failure Handling & Incident Response

- On CI failures: triage by the responsible developer; create ticket with failure logs and attach failing test names, stack traces, and recent failing commit.
- If tests reveal data or security issues, escalate to security@motac.gov.my and devops@motac.gov.my per incident procedure.


Maintenance & Review

- Review and update test suites when requirements/designs change (D03/D04).
- Annual audit of test coverage and accessibility test results; remediate gaps.
- Keep tests fast and focused: prefer many small deterministic tests rather than few huge end-to-end tests.


Contacts & Owners

- QA / Test Owner: qa@motac.gov.my
- DevOps / CI: devops@motac.gov.my
- Documentation & Traceability: docs@motac.gov.my
- Security: security@motac.gov.my


Notes & Governance

- This file is normative. Any deviation that impacts test strategy, security, accessibility, or traceability requires formal change request and RTM update (see D01 §9.3).
- Review and update testing.instructions.md when CI, framework, or testing tools are upgraded (at least annually).
