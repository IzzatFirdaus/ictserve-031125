<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_id')->unique()->comment('X-Request-ID untuk kebolehkesanan');
            $table->enum('operation_type', [
                'faq_query',
                'document_analysis',
                'auto_reply_generation',
                'auto_reply_approval'
            ])->comment('Jenis operasi AI');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Pengguna (nullable untuk True Hybrid Architecture)');
            $table->text('sanitized_input')->comment('Input yang disanitasi (PII diredaksi)');
            $table->text('response_summary')->nullable()->comment('Ringkasan respons AI');

            // Bedrock AI integration fields
            $table->string('bedrock_model_used')->nullable();
            $table->decimal('bedrock_cost', 12, 6)->nullable();
            $table->json('web_sources_used')->nullable();

            $table->json('metadata')->nullable()->comment('Model, token, masa pemprosesan');
            $table->string('hash', 64)->comment('SHA-256 hash untuk immutability');
            $table->string('previous_hash', 64)->nullable()->comment('Chain of custody');
            $table->timestamp('processed_at')->comment('Masa pemprosesan selesai');
            $table->timestamps();

            // Indices untuk prestasi
            $table->index(['operation_type', 'processed_at']);
            $table->index('request_id');
            $table->index('hash');
            $table->index('bedrock_model_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_logs');
    }
};
