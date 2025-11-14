---
applyTo: "database/migrations/**,database/seeders/**,database/factories/**"
description: "Database migration standards, zero-downtime techniques, data backfills, rollback strategies, and CI validation for ICTServe Laravel 12"
---

# Database Migrations Instructions

Purpose
Provides standards, conventions, safety practices, and step‑by‑step guidance for creating, reviewing, testing and deploying database migrations for ICTServe (Laravel 12). This file is normative: follow it for all schema and data-change work to ensure data quality, traceability, auditability and safe deployments.

Scope
Covers:
- New schema migrations (tables, columns, indexes, constraints)
- Safe schema changes for production (zero‑downtime techniques)
- Data migration scripts (backfills, transforms)
- Migrations for views, triggers, and stored procedures
- Rollback planning, testing, CI validation
Relevant locations: `database/migrations/`, `database/seeders/`, `database/factories/`, `docs/models/`, `docs/db/`.

Standards & References (Mandatory)
- D00–D14 canonical docs for traceability
- D09 Database Documentation (ERD, field definitions)
- ISO 8000 (data quality) — ensure accuracy/consistency/uniqueness
- Internal change management: D01 §9.3

Traceability Requirements
- Every migration that adds/changes schema or critical data MUST include a small metadata comment block at top with:
  - name, description, author/team, requirement/design IDs (D03/D04/D11), last-updated date
- Update RTM (Requirements Traceability Matrix) and `docs/models/` or `docs/db/` when a migration adds/changes fields that map to requirements/design.
- Example metadata header (top of migration file):
```php
<?php
// name: create_departments_table
// description: Create departments table used by user profiles
// author: dev-team@motac.gov.my
// trace: SRS-FR-009; D04 §4.2; D11 §6
// last-updated: 2025-10-22
```

Migration Naming & Organization
- File name pattern: YYYY_MM_DD_HHMMSS_action_subject_table.php (Laravel default). Keep descriptive class names: CreateDepartmentsTable, AddStatusToAssetsTable.
- Prefer small, focused migrations — one logical change per migration.
- Group related operations so they are reversible as a unit; avoid mixing unrelated schema + data changes in the same migration unless they are tightly coupled and reversible.

Schema Best Practices
- Add columns (non-nullable) with a NULLable default first, backfill, then make NOT NULL (3-step approach).
  1. Add nullable column
  2. Run background job or one-off script to backfill
  3. Make column not nullable (with default or after verification)
- Avoid destructive operations in one step (dropping columns/tables) without rollback & archive plan.
- Use proper types and constraints. Prefer `foreignId('user_id')->constrained()->cascadeOnDelete()` when appropriate.
- Use method-style casts in models (`protected function casts(): array`) per repo convention and map changes to model updates.

Reversible Migrations & Down Methods
- Always implement a meaningful `down()` that cleanly and safely reverses `up()`.
- If a true reversal is impossible (data-loss), document clearly in the header and create an explicit rollback plan in the PR description and docs.
- Use `Schema::hasColumn()` guards in `down()`/`up()` to make migrations idempotent in development (but tests should exercise expected paths).

Data migrations vs schema migrations
- Prefer to separate schema changes from large, long‑running data transforms:
  - Schema migration: in `database/migrations/` only schema changes and small, safe seed adjustments.
  - Data migration/backfill: implement as Laravel `Command` (`app/Console/Commands/`) or Seeder and run in controlled environment (staging/maintenance window). This makes long-running or chunked processes easier to control, monitor and retry.
- For small data adjustments tied directly to schema change (e.g., default values for a new column), it is acceptable to include quick backfills in the migration if they are fast and safe.

Long‑Running / Large Table Changes (Zero‑Downtime)
- Do NOT run long ALTER TABLE operations during normal business hours without coordination.
- Zero‑downtime pattern (safe column rename or type change):
  1. Add new column (nullable)
  2. Deploy application code that writes both old and new columns (dual-write)
  3. Backfill existing rows via background job (chunked)
  4. Switch reads to new column in app code
  5. Drop old column in a later migration (after verification)
- For big indexes in PostgreSQL use `CREATE INDEX CONCURRENTLY` (cannot be run inside transaction). For MySQL, consider online schema tools (Percona pt-online-schema-change) or ensure `ALTER TABLE` strategies that are supported by your managed DB. Document and get approval for any such operations.
- Avoid `renameColumn()` on MySQL without `doctrine/dbal` installed; prefer add/backfill/drop pattern.

Transactions & Statements
- Laravel runs migrations in transactions by default for databases that support them. Be aware:
  - Some statements (e.g., `CREATE INDEX CONCURRENTLY`, certain `ALTER TABLE` operations, `CREATE VIEW ... WITH CHECK OPTION`) cannot run inside a transaction — you must use `DB::statement()` outside transactions and annotate accordingly.
  - If a migration must run statements outside transaction, set `public $withinTransaction = false;` (Laravel 9+ feature for migrations), and explain why in the header.
- When performing multiple schema changes that must be atomic, rely on default transactional support or explicitly use DB transactions where needed.

Indexes & Performance
- Add indexes for search/join fields (foreign keys, commonly filtered columns). Consider multicolumn indexes for heavy queries.
- Measure before adding indexes; indexes add write overhead and storage.
- For Postgres: create index concurrently for large tables.
- For MySQL: adding an index on very large tables can lock — schedule maintenance or use online DDL.

Foreign Keys & Constraints
- Prefer explicit FK constraints to enforce data integrity.
- When adding FKs to tables with existing data, ensure parent rows exist or run a data-clean step first.
- Avoid disabling foreign key checks in production. If required (carefully), document and run in maintenance window.

Views, Triggers, Stored Procedures
- Keep DDL for views/triggers in migrations and document purpose and rollback.
- Use caution with triggers: they run for every row and may cause unexpected side effects during bulk loads.
- Document any trigger's behavior in `docs/db/` and reference the migration.

Testing Migrations Locally & CI
- Run migrations on a fresh local database: `php artisan migrate:fresh --seed` during development.
- Add CI job step to run migrations and seeders against ephemeral DB (GitHub Actions with MySQL/Postgres service).
- In CI include: `php artisan migrate --force` and run `php artisan migrate:status` to assert migrations applied.
- Use `php artisan migrate --pretend` to show SQL that will execute (useful for review).
- Include migration checks in PR CI: static analysis, run tests that rely on migrated schema, and run `vendor/bin/phpstan` where model changes require type updates.

Migration File Template (example)
```php
<?php
// name: create_departments_table
// description: Create departments table used by user profiles
// author: dev-team@motac.gov.my
// trace: SRS-FR-009; D04 §4.2; D11 §6
// last-updated: 2025-10-22

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 16)->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
```

Data Backfill Example (recommended as Artisan command / job)
```php
// app/Console/Commands/BackfillDepartmentCodes.php (example pattern)
// Use chunking and lightweight queries; log progress and errors; idempotent.

public function handle()
{
    \App\Models\Department::chunk(500, function($rows) {
        foreach ($rows as $row) {
            if (empty($row->code)) {
                $row->code = strtoupper(substr(md5(uniqid((string) $row->id, true)), 0, 8));
                $row->saveQuietly();
            }
        }
    });
}
```

Safety & Quality Checklist (must be completed for PRs that change DB)
- [ ] Migration file has metadata header (name, desc, author, trace refs, last-updated)
- [ ] Migration is small, focused and reversible (or rollback plan documented)
- [ ] Data migrations/backfills are separated or documented as background jobs
- [ ] Local `migrate:fresh --seed` completed successfully
- [ ] CI runs migrations & tests (no failures)
- [ ] RTM / docs/models/ updated for new/changed fields
- [ ] Migration reviewed by DB owner / backend lead; large changes reviewed with DBA/DevOps
- [ ] Deployment plan includes maintenance window if needed (long-running DDL)
- [ ] Rollback steps and post-deploy verification checklist included in PR description

PR & Change Management for Production
- For production-impacting migrations (indexes on big tables, column drops, type changes, triggers):
  - Create a Change Request (CR) per D01 §9.3
  - Schedule migration in maintenance window
  - Take full database backup prior to migration
  - Prefer staged deploy: staging -> canary -> prod
  - Monitor logs and metrics for at least 48 hours post-deploy
  - Keep on-call contact details in PR

Partitioning, Archiving & Retention
- For very large tables, consider partitioning by date or logical key. This must be designed with DBA and documented in D09.
- Implement archival scripts (data retention policies) as scheduled jobs, not inline in migrations.

Migrations for Tests & Factories
- Update or add factories and seeders when adding new models/columns.
- Keep CI seed data light; heavy seed data should be used in separated integration jobs.
- Ensure tests reflect new not-null constraints: update factories to supply required fields.

Common Pitfalls & Guidance
- Avoid `Schema::table(..., function (Blueprint $table) { $table->renameColumn(...); });` on large tables without testing — it may require `doctrine/dbal` and still be blocking.
- Do not run DDL that locks tables for long periods in production without coordination.
- Never rely only on application code to enforce data integrity — use DB constraints where appropriate.
- Document any manual steps that must run before/after migration (external script, backup verification, config changes).

Examples of Problematic vs Preferred Change
- Problematic (in-place rename on huge table):
  - `renameColumn('old', 'new')` — may lock table and is risky.
- Preferred (safe zero-downtime):
  1. Add `new` column (nullable)
  2. Deploy code to write both `old` and `new`
  3. Backfill `new` with chunked job
  4. Deploy code to read from `new`
  5. Later drop `old` column

Monitoring & Post‑Deploy Validation
- After migrations run:
  - Run smoke queries: row counts, sample record checks, integrity checks.
  - Run application health endpoints and run core feature flows manually or via smoke tests.
  - Capture and retain migration logs and CI artifacts for audit.

Contacts & Owners
- DB / Migrations owner: devops@motac.gov.my
- Backend Lead / Reviewer: dev-team@motac.gov.my
- Documentation owner: docs@motac.gov.my
- Security / Compliance: security@motac.gov.my

Appendices
- Appendix A: Example migration files (create table, add column, create index)
- Appendix B: Backfill job examples and chunking pattern
- Appendix C: Zero-downtime rename guide (detailed)
- Appendix D: DB maintenance checklist for large DDL

Notes & Governance
- All schema and data changes must follow the change management process (D01), be traceable to SRS/design items (D03/D04/D11) and be documented under `docs/db/` and the RTM. Non-compliance with these rules requires explicit approval from project owner.
