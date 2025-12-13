<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bedrock_model_configs', function (Blueprint $table) {
            $table->id();
            $table->string('model_id')->index();
            $table->string('model_name');
            $table->string('provider')->default('bedrock');
            $table->json('task_types')->nullable();
            $table->decimal('cost_per_token', 12, 8)->nullable();
            $table->unsignedInteger('max_tokens')->nullable();
            $table->boolean('enabled')->default(true);
            $table->json('configuration')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['provider', 'enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bedrock_model_configs');
    }
};
