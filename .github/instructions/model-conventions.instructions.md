---
applyTo: "app/Models/**,database/migrations/**,database/factories/**,database/seeders/**"
description: "Eloquent model standards: traits, relationships, casts(), fillable, SoftDeletes, Auditable, and migration safety for ICTServe Laravel 12"
---

# Model Conventions

Purpose
Standardises Eloquent model structure, naming, relationships, traits, fillable/casts, migrations, and auditability for ICTServe. Ensures data quality (ISO 8000), maintainability (SQuaRE), security and traceability to D00–D14 documentation. This document is normative for all contributors who add or modify models.

Scope
Applies to all Eloquent models and related artifacts: `app/Models/*`, database migrations, factories, seeders, model tests, and documentation entries in `docs/models/`. Target users: backend developers, reviewers, DevOps, and QA. (See D03, D09, D10, D11)

Standards & References (mandatory)
- D00–D14 documentation set (System overview → UI/UX style guide)
- ISO 8000 (data quality), ISO/IEC/IEEE 12207, 15288, 29148, ISO 9001
- SQuaRE (ISO/IEC/IEEE 25000) for code & quality
- Owen-it/laravel-auditing for audit trail (D10/D11)
- Project code style: PSR-12; static analysis with PHPStan; formatting with Laravel Pint

Traceability (mandatory)
- Every model (and migration that changes data/schema) MUST include a trace metadata block at the top with:
  - name, short description, author/team, requirement IDs (D03), design refs (D04/D11), test refs (D07/D08), and last-updated date.
  - Example header:
    ```php
    // name: Department
    // description: Department model representing organisational units
    // author: dev-team@motac.gov.my
    // trace: SRS-FR-009; D04 §4.2; D11 §6
    // last-updated: 2025-10-21
    ```
- Update the Requirements Traceability Matrix (RTM) (docs/rtm/) for any new model or changed field mapping.

Mandatory rules
- Traits & interfaces:
  - Domain models MUST use: `HasFactory`, `SoftDeletes`, and implement `\OwenIt\Auditing\Contracts\Auditable` with trait `OwenIt\Auditing\Auditable` where auditability is required.
  - Use `\Spatie\Permission\Traits\HasRoles` on `User` where roles/permissions are needed.
- Mass assignment & casts:
  - Define `protected $fillable = [...]` for any writable attributes.
  - Use `protected function casts(): array` (method-form) to return cast map (project convention) rather than `$casts` property.
    - Rationale: centralised override and consistent signatures across models.
- Identifiers & UUIDs:
  - Prefer integer autoincrement primary keys unless requirement says UUID. If UUID is used, set in `boot()` with `Str::uuid()` and document implications in migration and model.
- Relationships:
  - Always type-hint relationship return types (e.g. `public function users(): HasMany`).
  - Use explicit `ForeignId` and constrained FK in migrations.
- Accessors & mutators:
  - Use typed methods and `Attribute` objects for complex getters/setters.
- Authorization:
  - Do not implement business authorization inside models. Use Policies and Gates (D11).
- File & naming conventions:
  - Model class name PascalCase; file name matches class name; singular model names preferred (`Department`, `Asset`, `Loan`).
- No heavy logic:
  - Keep models lean; move business logic to Services/Domain classes. Models may include small helper methods and relationship logic only.
- Soft deletes & auditing:
  - Enable SoftDeletes and auditing for organisational/domain models (User, Department, Asset, Loan, Ticket).
- Migrations:
  - Use `foreignId('division_id')->constrained()->cascadeOnDelete()` where appropriate.
  - Include `softDeletes()` and `timestamps()` where required.
  - Add explicit unique indexes for fields like email, tag_id.
- Documentation:
  - Add/maintain a short docs page under `docs/models/<model-name>.md` describing purpose, attributes, relationships, required secrets, sample payloads, and rollback steps for schema changes.
- Tests:
  - Add model unit tests (factories usage, relationships) and feature tests for behaviours that depend on models. Factories must be provided or updated for every model.

Step-by-step workflow for adding/modifying a model
1. Confirm requirements & design refs: locate SRS IDs (D03) and design entries (D04/D11), update RTM if adding new model/fields.
2. Create migration with clear name and idempotent operations; include indexes, constraints, default values, and softDeletes where applicable.
3. Scaffold model with required traits, `$fillable`, and `protected function casts(): array`.
4. Add metadata trace comment at top of model file.
5. Add or update factory and seeder (database/factories, database/seeders).
6. Add unit tests (model behaviour, scopes, relationships) and feature tests if behaviour affects endpoints.
7. Document model in `docs/models/<model>.md` and update CHANGELOG or design docs.
8. Open PR: include RTM changes, migration plan, rollback steps, sample queries, and required reviewers (Dev, DBA, Security).
9. After merge: run DB migration in staging, run smoke tests, monitor logs, update RTM and release notes.

Model file example (recommended pattern)
```php
<?php
// name: Department
// description: Department model representing organisational unit and relation to users
// author: dev-team@motac.gov.my
// trace: SRS-FR-009; D04 §4.2; D11 §6
// last-updated: 2025-10-21

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'code',
        'manager_id',
    ];

    /**
     * Use method-based casts per project convention.
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relationship: department has many users.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Boot: set defaults such as uuid if required by project policy.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            // if using UUID primary or unique code fields
            if (empty($model->code)) {
                $model->code = strtoupper(substr(md5(uniqid((string) time(), true)), 0, 8));
            }
        });
    }
}
```

Migration guidance (example)
- Use descriptive migration names: `2025_10_21_000001_create_departments_table.php`.
- Include metadata in migration docblock and store rollback instructions in comments.
- Example migration notes:
  - `foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();`
  - `unique('code')` for short codes or tag fields.
  - Use CHECK constraints for enums where DB supports them (MySQL 8+ / PostgreSQL).

Factory & Seeder
- Provide factory with realistic fake data (use faker localisation 'ms_MY' where useful).
- Seeders should reference RTM/test dataset IDs and indicate if they are for demo/testing only.

Testing
- Unit tests:
  - Validate `casts()` output, fillable protections, relationships return types, scopes.
  - Factories create valid instances and obey constraints.
- Feature tests:
  - Migration scripts: test fresh migration + seeding in CI stage.
  - Model-dependent features (e.g., soft-delete behaviour, audit logging) covered by tests.
- CI must run phpunit for every PR and include phpstan & pint static checks.

Audit & Logging
- Models with auditing MUST use Owen-It auditing and write meaningful `auditable` metadata where relevant.
- When model changes represent a business-critical action (loan issued, asset marked lost), ensure application layer writes audit entries with context (actor_id, reason, request_id).

Data quality & privacy
- Enforce ISO 8000 principles: accuracy, completeness, consistency, uniqueness.
- Sensitive fields must use appropriate casts and encryption:
  - Example: `protected function casts(): array { return ['email' => 'encrypted']; }`
- Document retention and anonymisation requirements in `docs/models/<model>.md`.

PR & Review checklist (model changes)
- [ ] Migration name & rollback steps present and reviewed
- [ ] Model metadata trace comment added/updated
- [ ] `$fillable` present and reviewed for mass-assignment surface
- [ ] `casts()` method implemented for date/JSON/encrypted fields
- [ ] Traits: HasFactory, SoftDeletes, Auditable (when applicable) added
- [ ] Relationships typed and documented
- [ ] Factory and seeder added/updated
- [ ] Unit & feature tests added and passing
- [ ] phpstan & pint checks pass
- [ ] Docs updated under `docs/models/` and RTM updated (D03)
- [ ] Reviewer assigned: Backend lead, DBA, Security (if sensitive data), Docs owner

Common pitfalls & guidance
- Do not store secrets or credentials in model attributes or migrations.
- Avoid business logic in models—use Services, Jobs or Domain classes for complex flows.
- Do not change existing column semantics without updating RTM, tests, and migration rollback plans.
- Prefer explicit casts for JSON fields (`'equipment_list' => 'array'`) to avoid repeated json_decode calls.

Contacts & owners
- Data & Models Owner: docs@motac.gov.my
- Backend Lead: dev-team@motac.gov.my
- Security / Compliance: security@motac.gov.my
- DevOps / DBA: devops@motac.gov.my

Notes & governance
- This file is normative. Any deviation that impacts data quality, privacy, or traceability requires formal change management per D01 §9.3 and must be recorded in RTM and change request logs.
- Review and update model conventions periodically (at least annually or after major framework upgrades).
