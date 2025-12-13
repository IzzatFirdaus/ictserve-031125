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
        Schema::create('data_lineage', function (Blueprint $table) {
            $table->id();
            $table->uuid('lineage_id')->unique()->comment('ID unik untuk lineage tracking');
            $table->string('source_type')->comment('Jenis sumber: document, faq, user_input');
            $table->unsignedBigInteger('source_id')->comment('ID sumber');
            $table->string('transformation_type')->comment('Jenis transformasi: embedding, chunking, sanitization');
            $table->json('transformation_metadata')->comment('Metadata transformasi');
            $table->string('destination_type')->comment('Jenis destinasi: embedding, chunk, response');
            $table->unsignedBigInteger('destination_id')->nullable()->comment('ID destinasi');
            $table->timestamp('processed_at')->comment('Masa pemprosesan');
            $table->timestamps();

            // Indices untuk prestasi
            $table->index(['source_type', 'source_id']);
            $table->index('lineage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_lineage');
    }
};
