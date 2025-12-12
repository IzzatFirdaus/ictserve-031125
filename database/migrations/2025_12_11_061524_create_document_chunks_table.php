<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Per Requirements 2.1, 2.2: Document chunking untuk vector embeddings
     * Selaras dengan D09 Database Documentation v3.6.0
     */
    public function up(): void
    {
        Schema::create('document_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->text('chunk_text');
            $table->json('embedding')->nullable(); // Vector storage untuk semantic search
            $table->string('source')->nullable();
            $table->integer('chunk_index');
            $table->timestamps();

            // Indeks komposit untuk prestasi
            $table->index(['document_id', 'chunk_index']);
            $table->index('document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_chunks');
    }
};
