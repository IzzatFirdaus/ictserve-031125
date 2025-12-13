<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_search_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_id')->index();
            $table->string('search_query');
            $table->string('provider')->default('duckduckgo');
            $table->unsignedInteger('results_count')->default(0);
            $table->json('sources_used')->nullable();
            $table->decimal('cost', 12, 6)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['provider', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_search_logs');
    }
};
