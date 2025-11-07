<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Internal Comments Migration
 *
 * Creates the internal_comments table for staff-only comments on helpdesk tickets
 * and asset loan applications with threading support (max depth 3) and @mentions.
 *
 * @version 1.0.0
 *
 * @since 2025-11-06
 *
 * @author ICTServe Development Team
 *
 * Requirements: 7.1, 7.2, 7.3 (staff-dashboard-profile spec)
 * Standards: ISO/IEC 12207, PDPA 2010, WCAG 2.2 AA
 * Traceability: D03 (Software Requirements), D04 (Software Design), D09 (Database Documentation)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('internal_comments', function (Blueprint $table) {
            $table->id();

            // User relationship
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->comment('User who created the comment');

            // Polymorphic relationship to tickets/loans (auto-creates index)
            $table->morphs('commentable');

            // Self-referencing for threading (max depth 3)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('internal_comments')
                ->cascadeOnDelete()
                ->comment('Parent comment ID for threading');

            // Comment content
            $table->text('comment')
                ->comment('Comment text (max 1000 characters enforced at application level)');

            // JSON field for @mentions
            $table->json('mentions')
                ->nullable()
                ->comment('Array of mentioned user IDs');

            $table->timestamps();

            // Additional indexes for performance
            $table->index('parent_id', 'idx_parent_lookup');
            $table->index('user_id', 'idx_user_lookup');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_comments');
    }
};
