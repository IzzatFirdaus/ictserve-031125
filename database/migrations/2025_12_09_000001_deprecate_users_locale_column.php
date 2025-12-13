<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Deprecate users.locale column for v3.6.0 Bahasa Melayu-only interface
 *
 * This migration adds a deprecation comment to the locale column but does NOT
 * drop it, preserving data for potential future bilingual support restoration.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add deprecation comment to locale column (MySQL specific)
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN locale VARCHAR(10) DEFAULT 'ms' COMMENT 'DEPRECATED v3.6.0: Always ms. Retained for potential future use.'");
        }

        // Set all existing locale values to 'ms' for consistency
        DB::table('users')->update(['locale' => 'ms']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove deprecation comment (MySQL specific)
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN locale VARCHAR(10) DEFAULT 'ms' COMMENT 'User preferred locale (ms or en)'");
        }
    }
};
