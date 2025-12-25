<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PKS 4.2 Data Sovereignty Compliance - Data Residency Logging
 *
 * Creates table to track all AI operations and their data residency
 * for compliance monitoring and audit purposes.
 *
 * @see D03-FR-025 (Data Sovereignty Requirements)
 * @see PKS 4.2 (Data Residency Requirements)
 *
 * @trace Requirements 26.2, 26.4
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_residency_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('service', 50)->index(); // ollama, bedrock, etc.
            $table->string('operation', 50)->index(); // generate, chat, embeddings
            $table->string('data_classification', 20)->index(); // SENSITIVE, PUBLIC
            $table->string('processing_location', 100); // local, aws-ap-southeast-1, etc.
            $table->boolean('is_local_processing')->default(false);
            $table->boolean('is_compliant')->default(true);
            $table->string('model_id', 100)->nullable();
            $table->integer('input_tokens')->nullable();
            $table->integer('output_tokens')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->json('metadata')->nullable();
            $table->text('compliance_notes')->nullable();
            $table->timestamps();

            // Indexes for compliance reporting
            $table->index(['created_at', 'is_compliant']);
            $table->index(['service', 'data_classification']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_residency_logs');
    }
};
