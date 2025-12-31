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
        Schema::create('scheduled_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('schedule_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('notification_class');
            $table->json('notification_data');
            $table->string('notification_type')->nullable();
            $table->string('priority')->default('normal');
            $table->json('channels')->nullable();
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('status')->default('pending'); // pending, sent, cancelled, failed
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable(); // daily, weekly, monthly
            $table->timestamp('next_occurrence_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['scheduled_at', 'status']);
            $table->index('schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_notifications');
    }
};
