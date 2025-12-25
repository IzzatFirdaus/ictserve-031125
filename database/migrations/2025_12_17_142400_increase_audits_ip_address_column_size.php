<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Increase ip_address column size to accommodate SHA-512 hashes (128 characters)
     * as per D09 §4.6 PDPA compliance requirements.
     */
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->string('ip_address', 128)->nullable()->change()->comment('IP address or SHA-512 hash for PDPA compliance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->string('ip_address', 64)->nullable()->change()->comment('IP address or SHA-256 hash for PDPA compliance');
        });
    }
};
