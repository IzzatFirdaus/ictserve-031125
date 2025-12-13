<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Per Requirements 3.1, 3.2, 3.3, 3.4: Auto-reply template management
     * Selaras dengan D09 Database Documentation v3.6.0
     */
    public function up(): void
    {
        Schema::create('auto_reply_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('template_content');
            $table->json('variables')->nullable(); // Dynamic placeholders
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indices untuk prestasi
            $table->index(['status', 'created_at']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_reply_templates');
    }
};
