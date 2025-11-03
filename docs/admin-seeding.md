# Admin seeding — ICTServe

This document explains how the default administrator account is created for local development and how to manage it safely.

## Default account

The repository contains `database/seeders/AdminUserSeeder.php` which will create or update a default administrator user.

- Email: `admin@motac.gov.my`
- Password: `Motac.123$` (hashed by the seeder using `Hash::make()`)
- Flag: `is_admin = true`

## Commands

Run the full database seeder (recommended when initially setting up the DB):

```powershell
php artisan migrate --force
php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder
```

Or seed only the admin user:

```powershell
php artisan db:seed --class=Database\\Seeders\\AdminUserSeeder
```

## Security guidance

- Do not reuse the development password in production. Replace it with a secure, random password and rotate it after first use.
- Consider using an environment-driven initial admin password mechanism or require MFA for admin users in production.
- After creating the admin user, log in and set up Multi-Factor Authentication (MFA) and change the password immediately.
- Limit access to admin email addresses in production and use an allowlist or IP restriction if required by policy.

## Idempotence

The seeder uses `updateOrCreate` so running it multiple times is safe — it will update the existing user if the email already exists.

## Audit & traceability

If using the OwenIt auditing package, admin creation will be recorded in the audits table when run from application code. For seeder-run actions, ensure the run is tracked in your release notes or deployment runbook.

---

For questions about operationalizing admin accounts, contact the DevOps or Security team and follow the project's change management process.
