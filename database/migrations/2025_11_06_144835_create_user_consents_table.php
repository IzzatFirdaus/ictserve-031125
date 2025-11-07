<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Stores user consent records for PDPA compliance (Personal Data Protection Act 2010, Malaysia)
     *
     * @see D03-NFR-005 (PDPA compliance requirements)
     * @see D11 §14.4 (Data protection and privacy)
     */
    public function up(): void
    {
        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('consent_type'); // e.g., 'data_processing', 'marketing', 'analytics'
            $table->text('consent_statement'); // Full text of consent statement shown to user
            $table->string('version')->default('1.0'); // Version of consent statement
            $table->boolean('granted')->default(false); // User granted or revoked consent
            $table->ipAddress('ip_address'); // IP address when consent was recorded
            $table->text('user_agent')->nullable(); // Browser user agent
            $table->timestamp('consented_at')->nullable(); // When consent was granted
            $table->timestamp('revoked_at')->nullable(); // When consent was revoked
            $table->timestamps();

            // Indexes for efficient queries
            $table->index(['user_id', 'consent_type', 'granted']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_consents');
    }
};
