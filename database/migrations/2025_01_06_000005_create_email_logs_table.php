<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('recipient_email');
            $table->string('subject');
            $table->string('email_type'); // ticket_created, loan_approved, etc.
            $table->enum('status', ['pending', 'delivered', 'failed', 'bounced'])->default('pending');
            $table->json('data')->nullable(); // Email template data
            $table->integer('retry_attempts')->default(0);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('last_retry_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['email_type', 'status']);
            $table->index(['recipient_email', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
