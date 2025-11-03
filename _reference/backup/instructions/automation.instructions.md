---
applyTo: '**'
---


# Automation Instructions

**Purpose**  
Defines mandatory standards, traceability, and operational rules for automation scripts, workflows, and CI/CD in ICTServe (BPM MOTAC). This document is normative for developers, DevOps, and maintainers. Traceability: link every automation item to D03 (Requirements), D04/D11 (Design), D07–D08 (Integration & Plan), D10 (Source Code), and D00 System Overview. See D00–D15 for traceability and requirements.

**Scope**  
Applies to repository-level automation including:
- GitHub Actions workflows (`.github/workflows/*.yml`)
- Scripts under `scripts/`, `bin/`, or repository root (e.g. `deploy.sh`, `update.sh`)
- Laravel artisan custom commands (e.g. `php artisan boost:update`)
- CI/CD configuration, release automation, and scheduled jobs (cron/workflow_dispatch)
- Devcontainer and workspace automation (e.g. `.devcontainer`, `.vscode/*`)


**Standards & References (Mandatory)**
- D00–D14 documentation set (use RTM to map items)
- ISO/IEC/IEEE 12207, 15288, 29148, 9001 and ISO 8000 (data), ISO 27701 (privacy)
- BPM / MOTAC internal policies and change-management procedures


**Traceability Requirements**
- Every automation artifact (workflow, script, scheduled job) MUST reference:
  - Requirement IDs from D03 (e.g., SRS-FR-XXX)
  - Design references from D04/D11 (section or diagram IDs)
  - Integration plan points from D07/D08 where applicable
- Add a `trace` header or inline comment at top of the file with: requirement IDs, design doc refs, and author.
  - Example: `# trace: SRS-FR-012; D04 §4.2; D08 §6.1; author: dev-team`


**Mandatory Rules (Summary)**
- Use GitHub Actions for CI/CD by default and place workflows in `.github/workflows/`.
- Name scripts intuitively and store in `scripts/` or `bin/`; add an executable header and usage help (`--help`).
- Use `php artisan boost:update` for system updates where the boost helper exists—document usage and safety checks.
- All automation must be logged and auditable. Workflows must emit logs to Actions Console; critical steps must write to centralized log (e.g., audit log endpoint or append to `storage/logs/automation.log`).
- Include tests or smoke-checks in CI (unit, integration, simple health-check).
- Enforce repository policies with branch protection, required status checks, and code review before merging automation changes.
- Follow security best practices: secret storage in GitHub Secrets, least privilege tokens, and short-lived credentials where possible.
- All changes to automation must go through the change management workflow and be traceable in RTM.


**File & Documentation Requirements**
- Each workflow file MUST start with a short metadata comment block:
  - name, description, author, trace (requirements/design refs), last-updated
  - Example YAML header comment:
    ```yaml
    # name: CI
    # description: Run unit & integration tests + static analysis
    # author: devops@motac.gov.my
    # trace: SRS-FR-001, D04 §4.1, D11 §6
    # last-updated: 2025-10-17
    ```
- Document scripts in `docs/automation/` with:
  - purpose, usage, inputs/outputs, required secrets, traceability links, rollback steps
- Add or update `CONTRIBUTING.md` and PR templates to require:
  - automation changes checklist (Does it include trace refs? secrets? audit logging?)
  - Example PR checklist item: "Does this change add/modify automation? If yes, include trace IDs and update docs under docs/automation/."


**Step-by-Step Workflow for Adding New Automation**
1. Create design note: add entry in `docs/automation/` describing purpose, inputs, outputs, trace refs.
2. Implement script/workflow in the repository (`scripts/` or `.github/workflows/`).
3. Add metadata header and inline `trace` comment.
4. Add tests or a smoke-check job.
5. Create PR referencing requirement/design IDs; include reviewers (DevOps + Security).
6. After merge, update RTM (D03 ↔ artifact) and notify operations per change management process.
7. Monitor first runs and capture logs/metrics for at least 48 hours.


## Examples

1) Minimal GitHub Actions CI example (.github/workflows/ci.yml)
```yaml
# name: CI
# description: Run tests and static analysis
# trace: SRS-FR-001; D04 §4.1; D11 §9

name: CI
on:
  push:
    branches: [ develop, main ]
  pull_request:
    branches: [ develop, main ]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install dependencies
        run: composer install --no-interaction --prefer-dist
      - name: Run static analysis
        run: vendor/bin/phpstan analyse --no-progress
      - name: Run tests
        run: php artisan test --parallel
```

2) Script example: scripts/boost-update.sh
```bash
#!/usr/bin/env bash
# trace: D11 §7; SRS-NFR-002
# purpose: wrapper to run php artisan boost:update safely in CI/ops
set -euo pipefail

if [ -z "$APP_ENV:-" ]; then
  echo "APP_ENV not set; aborting"
  exit 1
fi

echo "Running boost:update in environment $APP_ENV"
php artisan boost:update --backup ||  echo "boost:update failed"; exit 2; 
# append execution record for audit (example)
php artisan automation:log "boost:update executed by $(whoami) on $APP_ENV"
```

3) Using php artisan boost:update (example)
- Ensure backup first:
  - php artisan backup:run
- Run update:
  - php artisan boost:update --no-interaction
- Verify health:
  - curl -fsS http://localhost/health || (echo "health check failed" && exit 1)


## Audit & Logging
- Automation must write auditable events:
  - Workflow runs automatically provide logs in Actions; include structured logs for important outputs (JSON).
  - Scripts should call application audit endpoints or write to an append-only automation audit file configured in `config/automation.php`.
- Retention: keep automation logs for minimum retention per D09/D05 (audit retention policy). Archive logs securely and encrypt at rest.


## Secrets & Credentials
- Store secrets in GitHub Secrets (or enterprise secret store). Do NOT commit credentials or tokens.
- Use minimal scopes: generate tokens scoped to necessary resources only.
- Use ephemeral tokens where supported (OIDC/GitHub Actions OIDC recommended).


## Security & Compliance Checks
- Add mandatory static analysis (`phpstan`), dependency scanning, and SCA (Software Composition Analysis) to CI.
- Ensure workflows include steps to scan for secrets (e.g. trufflehog/secret-scan action).
- For any deployment action, require manual approval step or protected environment in GitHub.


## Auto-open / Developer Experience (Optional)
- If you want users to see this instructions file automatically:
  - Primary approach: maintain README.md with a short link to `.instructions.md` and set `.vscode/settings.json` to `"workbench.startupEditor": "readme"`.
  - Advanced: provide a small VS Code extension (or recommend a workspace-recommended extension) that opens `.instructions.md` on startup. See docs/automation/extension-template.md for example.


## PR & Change Management Template Snippet (add to .github/PULL_REQUEST_TEMPLATE.md)
- "Does this PR change automation/workflows/scripts? If yes:
  - Add traceability IDs in the file headers.
  - Update docs/automation/<name>.md with usage and rollback.
  - Add smoke tests and confirm logs are emitted.
  - Add reviewer: @devops, @security."


## Testing & Validation
- CI must run unit, integration, and smoke tests relevant to automation changes.
- For deployment workflows, include a `dry-run` or `--check` mode to validate steps without applying changes.


### Checklist (Before Merging Automation Changes)
- [ ] Metadata header added (name, description, author, trace)
- [ ] Docs under `docs/automation/` updated
- [ ] Secrets required documented (do not commit)
- [ ] Tests/smoke checks included and passing
- [ ] Audit/logging implemented or confirmed
- [ ] PR includes requirement/design refs and assigned reviewers
- [ ] Rollback steps documented


## Appendix A — Helpful Links
- D00 System Overview (see RTM)
- D03 Software Requirements (trace IDs)
- D04 Software Design, D07 System Integration Plan, D08 Integration Specification
- D09 Database Documentation (retention & logging)
- D11 Technical Design (encryption, key management)
- docs/automation/ (repo folder for automation docs)
- .github/workflows/ (existing workflows)

## Appendix B — Example Metadata Block (copy into the top of scripts/workflows)
```text
# name: <short name>
# description: <one-line purpose>
# author: <team or email>
# trace: <SRS-ID(s) ; D04 § ; D11 § ; D08 §>
# last-updated: YYYY-MM-DD
```


## Contacts
- DevOps / Automation: devops@motac.gov.my
- Security / Compliance: security@motac.gov.my
- Documentation & Traceability: docs@motac.gov.my


## Notes
- This file is normative for automation practices in this repository. All deviations require formal approval via change request and must be recorded in the project's RTM.
- Review and update this document whenever automation patterns or CI/CD policies change (at least annually or after major platform upgrades).
