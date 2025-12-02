# Database & Migration Instructions

**Purpose**
Defines standards for database schema design, migration safety, data integrity, and seeding strategies for ICTServe. Ensures compliance with D09 (Database Documentation) and D11 (Technical Design).

**Scope**
Applies to `database/migrations`, `database/seeders`, `database/factories`, and `app/Models`.

## 1. Schema Standards

- **Engine**: MySQL 8.0+ (InnoDB) / MariaDB 10.6+.
- **Naming**:
  - Tables: Plural, `snake_case` (e.g., `helpdesk_tickets`).
  - Columns: `snake_case` (e.g., `is_active`, `resolved_at`).
  - Foreign Keys: `singular_id` (e.g., `user_id`, `category_id`).
- **Primary Keys**: Use `id` (BigInt Auto Increment) or `uuid` (Char 36) for distributed systems.
- **Audit Columns**: Every table must include `timestamps()` (`created_at`, `updated_at`). Use `softDeletes()` (`deleted_at`) for core entities.

## 2. Migration Safety Rules

### Reversibility
Every migration must have a destructive `down()` method that perfectly reverses the `up()` method.

### Zero-Downtime Deployment
- **Renaming Columns**: Never use `$table->renameColumn()` on production tables.
  1. **Step 1**: Create new column.
  2. **Step 2**: Deploy code that writes to *both* columns.
  3. **Step 3**: Backfill old data to new column (Background Job).
  4. **Step 4**: Deploy code that reads/writes only new column.
  5. **Step 5**: Drop old column in next release.
- **Constraints**: Add Foreign Key constraints *after* ensuring data integrity to avoid locking issues on large tables.

### Idempotency
Migrations should be safe to run multiple times in CI/CD environments (e.g., using `Schema::hasColumn` checks if necessary, though Laravel handles migration tracking automatically).

## 3. Migration Template (Laravel 12)

Use anonymous classes for migrations.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('tag')->unique()->comment('Unique asset control number');
            $table->string('name');
            
            // Foreign Key Best Practice
            $table->foreignId('category_id')
                  ->constrained()
                  ->cascadeOnDelete();
            
            // Status Enum Column
            $table->string('status')->default('available')->index();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
````

## 4\. Seeding & Factories

### Factories (`database/factories`)

Define the *structure* and *default state* of models here. Use PHP 8.4 features where applicable.

```php
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role' => UserRole::STAFF, // Use Enums
        ];
    }
}
```

### Seeders (`database/seeders`)

Use Seeders for **static reference data** (e.g., Roles, States, Categories) that must exist for the app to run.

  - **Idempotency**: Seeders must check if data exists before inserting to prevent duplicates (e.g., `firstOrCreate`).
  - **Environment**:
      - `DatabaseSeeder`: Calls essential config seeders.
      - `DummyDataSeeder`: Optional seeder for local dev/staging (large datasets).

## 5\. Large Data Operations

Do **NOT** use migration files for manipulating massive amounts of data (e.g., \>10k rows). This causes deployment timeouts.

**Pattern**:

1.  Create a Laravel **Job** or **Artisan Command**.
2.  Chunk results to manage memory.
3.  Run manually or via deployment hook after migration success.

<!-- end list -->

```php
// Example Command
Asset::chunk(1000, function ($assets) {
    foreach ($assets as $asset) {
        $asset->update(['new_status' => 'migrated']);
    }
});
```

## 6\. Pre-Commit Checklist

  - [ ] Ran `php artisan migrate:fresh --seed` locally to verify chain integrity.
  - [ ] `down()` method is implemented.
  - [ ] Foreign keys use `constrained()` for integrity.
  - [ ] Indices added for frequently searched columns (`status`, `email`, foreign keys).
