<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Per Requirements 3.1, 3.2, 3.3, 3.4: Auto-reply draft management dengan approval workflow
     * Selaras dengan D09 Database Documentation v3.6.0
     */
    public function up(): void
    {
        Schema::create('auto_reply_drafts', function (Blueprint $table) {
            $table->id();
            // Polymorphic relationship untuk tickets/loan applications
            $table->string('replyable_type');
            $table->unsignedBigInteger('replyable_id');
            $table->text('draft_content');

            // Bedrock AI integration fields
            $table->string('model_used')->nullable();
            $table->decimal('generation_cost', 12, 6)->nullable();

            $table->foreignId('template_id')->nullable()->constrained('auto_reply_templates')->nullOnDelete();
            $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected', 'sent'])->default('draft');
            $table->foreignId('generated_by')->constrained('users'); // Technician
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indices untuk prestasi
            $table->index(['status', 'created_at']);
            $table->index(['replyable_type', 'replyable_id']);
            $table->index('generated_by');
            $table->index('approved_by');
            $table->index('model_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_reply_drafts');
    }
};
