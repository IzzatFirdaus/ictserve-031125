<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('module', ['helpdesk', 'loans', 'assets', 'users', 'unified']);
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);
            $table->time('schedule_time')->default('09:00:00');
            $table->tinyInteger('schedule_day_of_week')->nullable(); // 1-7 for weekly
            $table->tinyInteger('schedule_day_of_month')->nullable(); // 1-31 for monthly
            $table->json('recipients'); // Array of email addresses
            $table->json('filters')->nullable(); // Report filters
            $table->enum('format', ['pdf', 'csv', 'excel'])->default('pdf');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'next_run_at']);
            $table->index(['module', 'frequency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_schedules');
    }
};
