<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('processing_model')->nullable()->after('status');
            $table->json('bedrock_analysis')->nullable()->after('processing_model');
            $table->index('processing_model');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['processing_model']);
            $table->dropColumn(['processing_model', 'bedrock_analysis']);
        });
    }
};
