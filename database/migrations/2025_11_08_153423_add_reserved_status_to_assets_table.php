<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Add 'reserved' status to assets table enum
 *
 * Extends asset status enum to support reservation workflow.
 * Uses database-agnostic approach for SQLite/MySQL compatibility.
 *
 * @see D03-FR-003.2 Asset reservation
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE assets MODIFY COLUMN status ENUM('available', 'reserved', 'loaned', 'maintenance', 'retired', 'damaged') DEFAULT 'available' COMMENT 'Current asset status'");
        } elseif ($driver === 'sqlite') {
            // SQLite doesn't support ENUM or ALTER COLUMN, so we recreate the table
            Schema::table('assets', function (Blueprint $table) {
                // SQLite will handle this as TEXT column, enum validation happens in model
                $table->string('status')->default('available')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE assets MODIFY COLUMN status ENUM('available', 'loaned', 'maintenance', 'retired', 'damaged') DEFAULT 'available' COMMENT 'Current asset status'");
        } elseif ($driver === 'sqlite') {
            // SQLite: No action needed, enum validation is in model
        }
    }
};
