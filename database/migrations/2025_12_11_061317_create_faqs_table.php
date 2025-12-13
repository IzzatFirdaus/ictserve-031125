<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Per Requirements 1.1, 1.5, 4.1: FAQ management with True Hybrid Architecture
     * Selaras dengan D09 Database Documentation v3.6.0 (nullable user_id FK pattern)
     */
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question')->index();
            $table->longText('answer');
            $table->json('tags')->nullable();
            $table->float('match_score')->nullable();

            // Bedrock AI integration fields
            $table->string('preferred_model')->nullable();
            $table->float('complexity_score')->nullable();

            // True Hybrid Architecture: nullable FK untuk guest/authenticated access
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Full-text search indices untuk FAQ retrieval (MySQL/MariaDB only)
            if ($this->supportsFulltext()) {
                $table->fullText(['question', 'answer']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }

    /**
     * Check if the database supports fulltext indexes
     */
    private function supportsFulltext(): bool
    {
        return \in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true);
    }
};
