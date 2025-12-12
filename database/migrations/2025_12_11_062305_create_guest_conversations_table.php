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
        Schema::create('guest_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index()->comment('ID sesi tetamu');
            $table->string('email')->nullable()->index()->comment('E-mel tetamu (opsyen)');
            $table->json('conversation_history')->nullable()->comment('Sejarah perbualan (array turns)');
            $table->foreignId('claimed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Pengguna yang menuntut perbualan');
            $table->timestamp('claimed_at')->nullable()->comment('Masa perbualan dituntut');
            $table->timestamp('expires_at')->nullable()->comment('Masa tamat tempoh (30 minit)');
            $table->timestamps();

            // Indeks komposit untuk Account Linking
            $table->index(['email', 'claimed_by_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_conversations');
    }
};
