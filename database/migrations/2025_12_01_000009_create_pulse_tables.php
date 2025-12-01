<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Create Laravel Pulse tables for performance monitoring
 *
 * Creates the standard Pulse tables for real-time application
 * performance monitoring. Compatible with Laravel Pulse 1.3.0.
 *
 * Note: Uses conditional logic to support both MySQL (production)
 * and SQLite (testing). MySQL uses virtual columns with md5/unhex,
 * while SQLite uses a simple binary column.
 *
 * @see D03 §8.2 Performance monitoring requirements
 * @see Laravel Pulse documentation
 * @see Requirements 36.1
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        // Pulse values table - stores key-value pairs
        Schema::create('pulse_values', function (Blueprint $table) use ($isSqlite) {
            $table->id();
            $table->unsignedInteger('timestamp');
            $table->string('type');
            $table->mediumText('key');

            if ($isSqlite) {
                // SQLite doesn't support virtual columns with md5/unhex
                $table->binary('key_hash')->nullable();
            } else {
                $table->char('key_hash', 16)->charset('binary')->virtualAs('unhex(md5(`key`))');
            }

            $table->mediumText('value');

            $table->index('timestamp');
            $table->index('type');

            if (! $isSqlite) {
                $table->unique(['type', 'key_hash']);
            }
        });

        // Pulse entries table - stores individual metric entries
        Schema::create('pulse_entries', function (Blueprint $table) use ($isSqlite) {
            $table->id();
            $table->unsignedInteger('timestamp');
            $table->string('type');
            $table->mediumText('key');

            if ($isSqlite) {
                // SQLite doesn't support virtual columns with md5/unhex
                $table->binary('key_hash')->nullable();
            } else {
                $table->char('key_hash', 16)->charset('binary')->virtualAs('unhex(md5(`key`))');
            }

            $table->bigInteger('value')->nullable();

            $table->index('timestamp');
            $table->index('type');
            $table->index('key_hash');

            if (! $isSqlite) {
                $table->index(['timestamp', 'type', 'key_hash', 'value']);
            }
        });

        // Pulse aggregates table - stores pre-computed aggregations
        Schema::create('pulse_aggregates', function (Blueprint $table) use ($isSqlite) {
            $table->id();
            $table->unsignedInteger('bucket');
            $table->unsignedMediumInteger('period');
            $table->string('type');
            $table->mediumText('key');

            if ($isSqlite) {
                // SQLite doesn't support virtual columns with md5/unhex
                $table->binary('key_hash')->nullable();
            } else {
                $table->char('key_hash', 16)->charset('binary')->virtualAs('unhex(md5(`key`))');
            }

            $table->string('aggregate');
            $table->decimal('value', 20, 2);
            $table->unsignedInteger('count')->nullable();

            if (! $isSqlite) {
                $table->unique(['bucket', 'period', 'type', 'aggregate', 'key_hash']);
            }

            $table->index(['period', 'bucket']);
            $table->index('type');
            $table->index(['period', 'type', 'aggregate', 'bucket']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_aggregates');
        Schema::dropIfExists('pulse_entries');
        Schema::dropIfExists('pulse_values');
    }
};
