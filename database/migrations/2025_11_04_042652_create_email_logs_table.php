<?php

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
        Schema::create('email_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('subject');
            $table->string('mailable_class');
            $table->string('status')->default('queued');
            $table->string('message_id')->nullable();
            $table->text('status_message')->nullable();
            $table->json('meta')->nullable();
            $table->json('channels')->nullable()->comment('Notification channels used for this dispatch');
            $table->string('notification_type', 100)->nullable()->comment('Type from config/notifications.php');
            $table->enum('priority', ['critical', 'high', 'normal', 'low'])->default('normal')->comment('Email priority level');
            $table->timestamp('next_retry_at')->nullable()->comment('Scheduled time for next retry attempt');
            $table->enum('final_status', ['delivered', 'permanently_failed', 'bounced', 'rejected'])->nullable()->comment('Permanent delivery outcome');
            $table->boolean('preference_bypassed')->default(false)->comment('True if user notification preference was overridden');
            $table->timestamp('queued_at')->useCurrent();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            $table->index('notification_type', 'idx_email_logs_notification_type');
            $table->index('priority', 'idx_email_logs_priority');
            $table->index('next_retry_at', 'idx_email_logs_next_retry_at');
            $table->index('final_status', 'idx_email_logs_final_status');
            $table->index(['notification_type', 'status'], 'idx_email_logs_type_status');
            $table->index(['priority', 'status'], 'idx_email_logs_priority_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
