---
applyTo: '**'
---


# Contributing Instructions

**Purpose**  
Defines mandatory contribution standards, developer workflow, and compliance requirements for ICTServe (BPM MOTAC). This document ensures traceability to requirements (D03), design (D04/D11), testing (D08/D10), and UI/UX (D12–D14). It is normative for all contributors, maintainers, and reviewers.

**Scope**  
Applies to all contributions affecting code, scripts, workflows, documentation, assets, and configuration within this repository. Target users: contributors, maintainers, reviewers, QA, security, and DevOps. See D00–D15 for related governance and traceability requirements.

**Standards & References (Mandatory)**
- D00–D15 documentation set (System Overview → UI/UX Style Guide)
- PSR-12 / PHP coding standards
- ISO/IEC/IEEE 12207, 15288, 29148, ISO 9001
- BPM / MOTAC policies (change management, security, PDPA)
- Accessibility: WCAG 2.2 Level AA (D12–D14)

**Traceability Requirements**
- Every PR that adds/changes functionality, workflow, database, or automation MUST include Requirement IDs from D03 and Design refs from D04/D11.
- Maintain the Requirements Traceability Matrix (RTM) when features/bugs map to SRS IDs.
- Add a `trace` metadata block in files added/modified (scripts, workflows, key views). Example:
  `# trace: SRS-FR-012; D04 §4.2; D11 §6; author: team-name`

**Mandatory Rules (Summary)**
- Branching:
  - Fork (if external contributor) and branch from `develop`. Internal contributors create feature branches from `develop`.
  - Branch name format: <type>/<short-description> where type ∈ feature, fix, hotfix, chore, docs, test. Example: `feature/department-crud`, `fix/login-csrf`.
- Commits:
  - Use Conventional Commits style: `<type>(<scope>): <short message>` (e.g., `feat(tickets): add severity field`).
  - Provide meaningful bodies for non-trivial commits.
- Pull Requests:
  - All changes MUST go through PR (no direct push to `develop`/`main`).
  - PR templates must be filled (see PR Template snippet below).
  - Select appropriate reviewers: at least 1 peer dev + 1 owner (DevOps or module lead). Include `@security` and `@accessibility` reviewers when applicable.
- Testing & Validation:
  - Run local test suite before PR: `php artisan test` and any frontend tests; run `npm ci` and `npm run build` if relevant.
  - CI must pass (unit, integration, static analysis, accessibility checks) before merge.
- Quality & Style:
  - Follow PSR-12, use static analysis (PHPStan), and formatter (Pint).
  - Add/Update tests for feature/bugfix.
  - Update docs for user-facing or operational changes (README, docs/ directory).
- Security & Secrets:
  - No secrets or credentials in commits. Use GitHub Secrets, Vault, or OIDC tokens.
  - Security-sensitive changes require security review and may require a CVE/impact assessment.
- Accessibility & UI/UX:
  - UI changes MUST follow D12–D14. Include accessibility checklist and test results for changes affecting UI.
- Auditability:
  - Changes that affect automation, migrations, or data MUST include audit/rollback notes and be traceable in D10/D11.
- Change Management:
  - Major or production-impacting changes MUST follow formal change request process (see D01 §9.3).
  - Emergency hotfixes follow the hotfix policy and must be reconciled to `develop` after deployment.

**Step-by-Step Workflow (Developer)**
1. Sync develop:
   - git checkout develop && git pull origin develop
2. Create branch:
   - git checkout -b feature/short-descriptive-name
3. Implement change with tests and documentation updates.
4. Add metadata/trace headers where applicable (scripts, workflows, migrations).
5. Run local checks:
   - composer install
   - composer test / php artisan test
   - vendor/bin/phpstan analyse
   - vendor/bin/pint
   - npm ci && npm run build (if frontend changes)
6. Push branch & open PR referencing requirements/design IDs and issue(s).
7. Request reviewers (Dev, DevOps, Security, Accessibility as needed).
8. Address review comments and re-run CI.
9. Merge after all checks pass and approvals obtained. Use squash/merge to preserve readable history as per repository policy.
10. Update RTM / D03 traceability artifacts and documentation (docs/ or D10/D11 as appropriate).

**PR Template (snippet to include in .github/PULL_REQUEST_TEMPLATE.md)**
- Title: concise, includes type & scope (e.g., feat(tickets): add severity)
- Description:
  - What & why (short summary)
  - Linked Issue(s): closes #123
  - Traceability: Requirement IDs (D03), Design refs (D04/D11)
  - Checklist:
    - [ ] Tests added / updated
    - [ ] Docs updated (docs/ or README)
    - [ ] Accessibility reviewed (if UI)
    - [ ] Security review (if secrets, credentials, auth logic)
    - [ ] Migration run & rollback steps provided (if DB)
    - [ ] Audit/logging verified (if automation)
  - Reviewer(s): @team/owner, @devops, @security, @accessibility (as applicable)

**Examples & Conventions**
- Branch: `feature/department-crud`
- Commit: `feat(departments): add create and list endpoints`
- PR body trace example:
  - Trace: SRS-FR-012; D04 §4.2; D11 §6 — adds department CRUD to satisfy BR-005
- Migration header (top of migration file):
  ```
  # name: create_departments_table
  # description: Adds departments table used by user profiles
  # author: dev-team@motac.gov.my
  # trace: SRS-FR-009; D09 §4.2; D11 §5
  # last-updated: 2025-10-21
  ```

**Checklist — before merging any PR that modifies code, automation, or docs**
- [ ] CI (tests, PHPStan, Pint, lint, accessibility) passes
- [ ] Tests added/updated and green locally
- [ ] Traceability IDs included in PR and file headers (where relevant)
- [ ] Documentation updated (README, docs/, D10/D11/D12 as appropriate)
- [ ] Secrets/credentials not committed
- [ ] Security review completed (if applicable)
- [ ] Accessibility review completed (if UI changes)
- [ ] Rollback steps / migration safety checks documented
- [ ] RTM and D03 updated for feature/requirement mapping

**Guidance for Specific Contribution Types**
- Feature: include design links, RTM update, tests, performance considerations.
- Bugfix: include reproduction steps, tests that fail before fix, and changelog entry.
- Docs: link to related code/requirements; prefer examples and commands.
- Automation (workflows/scripts): include metadata header, trace refs, logging/audit notes, secrets list, and smoke tests. See automation.instructions.md for more details.
- Hotfix: follow hotfix branch policy and include post-deploy reconciliation steps.

**Repository Governance & Branch Protection**
- Protect `main` and `develop` branches with required status checks:
  - CI success
  - At least one reviewer approval
  - No merge conflicts
- Use semantic versioning for releases; tag releases on `main`.

**Developer Experience (Recommended)**
- Keep CONTRIBUTING.md (this file) and top-level README aligned.
- Recommend workspace settings in `.vscode/extensions.json` to include recommended extensions (PHPStan, Pint, EditorConfig, Live Share).
- Optionally provide a developer onboarding checklist (setup, env, run tests) in docs/developer-onboarding.md.

**Contacts**
- DevOps / Automation: devops@motac.gov.my
- Security / Compliance: security@motac.gov.my
- Documentation & Traceability: docs@motac.gov.my
- Project Owner / BPM: bpm@motac.gov.my

**Appendices**
- Links:
  - D00 System Overview
  - D03 Software Requirements Specification (requirements & SRS IDs)
  - D04 Software Design Document
  - D09 Database Documentation
  - D10 Source Code Documentation
  - D11 Technical Design Documentation
  - D12–D14 UI/UX & Accessibility docs
- Example PR checklist (machine-readable template) available at `.github/PULL_REQUEST_TEMPLATE.md`.
- For automation-specific guidance see `automation.instructions.md`.

**Notes**
- This file is normative for contribution workflow in this repository. Deviations require formal approval and must be recorded in the RTM and change request logs.
- Review and update this document when workflows, CI requirements, or governance change (at least annually).
