<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create personal_access_tokens table for Laravel Sanctum API authentication
 *
 * Standard Sanctum migration for API token authentication with
 * configurable abilities and expiration.
 *
 * @see D03 SRS-API-001 API token authentication
 * @see Laravel Sanctum documentation
 * @see Requirements 37.1, 37.2
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();

            // Polymorphic relationship to tokenable model (usually User)
            $table->morphs('tokenable');

            // Token identification
            $table->string('name')
                ->comment('Human-readable token name');
            $table->string('token', 64)->unique()
                ->comment('SHA-256 hashed token value');

            // Token abilities/permissions
            $table->text('abilities')->nullable()
                ->comment('JSON array of token abilities (read:tickets, write:tickets, etc.)');

            // Usage tracking
            $table->timestamp('last_used_at')->nullable()
                ->comment('Timestamp of last token usage');

            // Expiration
            $table->timestamp('expires_at')->nullable()
                ->comment('Token expiration timestamp (null = never expires)');

            $table->timestamps();

            // Indexes for performance
            $table->index('token', 'idx_pat_token');
            $table->index(['tokenable_type', 'tokenable_id'], 'idx_pat_tokenable');
            $table->index('expires_at', 'idx_pat_expires');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
