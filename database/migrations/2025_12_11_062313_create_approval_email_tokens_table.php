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
        Schema::create('approval_email_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auto_reply_draft_id')
                ->constrained('auto_reply_drafts')
                ->cascadeOnDelete()
                ->comment('Draf auto-reply yang berkaitan');
            $table->string('token', 64)->unique()->comment('Token kelulusan selamat (HMAC)');
            $table->string('action')->comment('Tindakan: approve atau reject');
            $table->timestamp('expires_at')->comment('Masa tamat tempoh (7 hari)');
            $table->boolean('used')->default(false)->comment('Status penggunaan token');
            $table->timestamp('used_at')->nullable()->comment('Masa token digunakan');
            $table->string('used_by_ip')->nullable()->comment('IP address pengguna');
            $table->timestamps();

            // Indeks untuk prestasi dan keselamatan
            $table->index(['token', 'used']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_email_tokens');
    }
};
