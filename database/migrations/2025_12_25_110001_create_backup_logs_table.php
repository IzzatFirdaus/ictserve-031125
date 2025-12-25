<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create backup_logs table
 *
 * PKS Business Continuity (Requirement 29) - Backup Audit Trail
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.1, 29.3
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->string('backup_id')->unique()->index();
            $table->string('type'); // full, incremental, database, files, config
            $table->string('status'); // pending, in_progress, completed, failed, verified
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->unsignedInteger('file_count')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['status', 'completed_at']);
            $table->index(['type', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};
