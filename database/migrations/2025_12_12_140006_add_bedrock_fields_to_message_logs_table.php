<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('message_logs', function (Blueprint $table) {
            $table->string('bedrock_model_used')->nullable()->after('response_summary');
            $table->decimal('bedrock_cost', 12, 6)->nullable()->after('bedrock_model_used');
            $table->json('web_sources_used')->nullable()->after('bedrock_cost');

            $table->index('bedrock_model_used');
        });
    }

    public function down(): void
    {
        Schema::table('message_logs', function (Blueprint $table) {
            $table->dropIndex(['bedrock_model_used']);
            $table->dropColumn(['bedrock_model_used', 'bedrock_cost', 'web_sources_used']);
        });
    }
};
