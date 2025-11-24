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
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->string('tracking_token', 64)->nullable()->unique()->after('application_number');
            $table->timestamp('tracking_token_expires_at')->nullable()->after('tracking_token');
            
            $table->index('tracking_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropIndex(['tracking_token']);
            $table->dropColumn(['tracking_token', 'tracking_token_expires_at']);
        });
    }
};
