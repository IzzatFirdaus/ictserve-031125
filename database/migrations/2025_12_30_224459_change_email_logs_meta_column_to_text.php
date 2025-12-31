<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Change email_logs meta column from JSON to TEXT
 *
 * This migration changes the meta column from JSON to TEXT to support
 * encrypted data storage. The encrypted:array cast stores base64-encoded
 * encrypted strings which are not valid JSON, causing constraint violations.
 *
 * @see App\Models\EmailLog - Uses 'encrypted:array' cast for meta column
 * @see Requirements 9.1 - Email log data encryption
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table): void {
            // Change meta from JSON to TEXT to support encrypted data
            $table->text('meta')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table): void {
            // Revert to JSON (note: this will fail if encrypted data exists)
            $table->json('meta')->nullable()->change();
        });
    }
};
