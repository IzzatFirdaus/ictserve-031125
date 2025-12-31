<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Change email_logs recipient columns from VARCHAR to TEXT
 *
 * This migration changes the recipient_email and recipient_name columns
 * from VARCHAR(255) to TEXT to support encrypted data storage.
 * The encrypted cast stores base64-encoded encrypted strings which
 * can exceed 255 characters.
 *
 * @see App\Models\EmailLog - Uses 'encrypted' cast for recipient fields
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
            // Change recipient columns to TEXT to support encrypted data
            $table->text('recipient_email')->change();
            $table->text('recipient_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table): void {
            // Revert to VARCHAR (note: this may truncate encrypted data)
            $table->string('recipient_email')->change();
            $table->string('recipient_name')->nullable()->change();
        });
    }
};
