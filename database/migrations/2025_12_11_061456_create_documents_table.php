<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Per Requirements 2.1, 2.2, 4.1: Document management dengan True Hybrid Architecture
     * Selaras dengan D09 Database Documentation v3.6.0 (nullable user_id FK pattern)
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->json('metadata')->nullable();
            // True Hybrid Architecture: nullable FK untuk guest/authenticated access
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');

            // Bedrock AI integration fields
            $table->string('processing_model')->nullable();
            $table->json('bedrock_analysis')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indices untuk prestasi
            $table->index(['status', 'created_at']);
            $table->index('uploaded_by');
            $table->index('processing_model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
