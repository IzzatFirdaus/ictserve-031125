<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modify audits table ip_address column to accommodate SHA-256 hashed IP addresses.
 *
 * The HashedIpAddressResolver creates 64-character SHA-256 hashes for PDPA compliance,
 * but the original column was varchar(45) which only fits IPv6 addresses.
 *
 * @see App\Resolvers\HashedIpAddressResolver
 * @see D09 §4.6 - Dual Audit System requirements
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            // Change ip_address from varchar(45) to varchar(64) to accommodate SHA-256 hashes
            $table->string('ip_address', 64)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            // Revert to original varchar(45) for IPv6 addresses
            $table->string('ip_address', 45)->nullable()->change();
        });
    }
};
