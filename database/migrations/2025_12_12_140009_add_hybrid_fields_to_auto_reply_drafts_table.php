<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auto_reply_drafts', function (Blueprint $table) {
            $table->string('model_used')->nullable()->after('draft_content');
            $table->decimal('generation_cost', 12, 6)->nullable()->after('model_used');
            $table->index('model_used');
        });
    }

    public function down(): void
    {
        Schema::table('auto_reply_drafts', function (Blueprint $table) {
            $table->dropIndex(['model_used']);
            $table->dropColumn(['model_used', 'generation_cost']);
        });
    }
};
