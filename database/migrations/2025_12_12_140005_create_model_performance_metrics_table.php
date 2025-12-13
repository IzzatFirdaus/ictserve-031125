<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('model_id')->index();
            $table->string('metric_type')->index();
            $table->decimal('metric_value', 16, 6);
            $table->timestamp('measurement_time')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['model_id', 'metric_type', 'measurement_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_performance_metrics');
    }
};
