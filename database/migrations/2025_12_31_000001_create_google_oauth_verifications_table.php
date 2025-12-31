<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for Google OAuth Verification tracking
 *
 * Stores OAuth app verification status, test users, and verification documents
 * for managing Google OAuth verification process.
 *
 * @see Requirements 1.1, 4.1
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('google_oauth_verifications', function (Blueprint $table): void {
            $table->id();
            $table->string('client_id')->unique()->comment('Google OAuth Client ID');
            $table->string('verification_status')->default('testing')
                ->comment('Status: verified, pending, testing, rejected');
            $table->json('test_users')->nullable()->comment('Array of test user emails');
            $table->timestamp('verification_submitted_at')->nullable()
                ->comment('When verification was submitted to Google');
            $table->timestamp('verification_approved_at')->nullable()
                ->comment('When verification was approved by Google');
            $table->json('verification_documents')->nullable()
                ->comment('Verification documentation and requirements');
            $table->json('quota_limits')->nullable()
                ->comment('API quota limits and usage tracking');
            $table->timestamp('last_status_check')->nullable()
                ->comment('Last time verification status was checked');
            $table->timestamps();

            // Indexes for common queries
            $table->index('verification_status');
            $table->index('last_status_check');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_oauth_verifications');
    }
};
