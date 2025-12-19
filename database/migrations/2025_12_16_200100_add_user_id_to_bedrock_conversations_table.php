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
        Schema::table('bedrock_conversations', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bedrock_conversations', function (Blueprint $table) {
            // Make teardown robust across SQLite and MySQL: SQLite may not create the same
            // index names that MySQL does, so conditionally drop indexes only on non-sqlite
            // drivers and guard against missing columns.
            if (Schema::hasColumn('bedrock_conversations', 'user_id')) {
                $driver = Schema::getConnection()->getDriverName();

                if ($driver !== 'sqlite') {
                    $table->dropIndex(['user_id']);
                } else {
                    // SQLite creates indexes differently; drop by name if present to avoid
                    // "no such index" or "error in index after drop column" issues.
                    try {
                        \Illuminate\Support\Facades\DB::statement('drop index if exists bedrock_conversations_user_id_index');
                    } catch (\Exception $e) {
                        // ignore
                    }
                }

                // Drop foreign key if present - wrap in try/catch to avoid errors on sqlite
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                    // Ignore if foreign key does not exist for this driver
                }

                if ($driver !== 'sqlite') {
                    try {
                        $table->dropIndex(['user_id']);
                    } catch (\Exception $e) {
                        // ignore
                    }
                }

                try {
                    $table->dropColumn('user_id');
                } catch (\Exception $e) {
                    // SQLite may error when dropping columns if indexes still reference them; ignore
                }
            }
        });
    }
};
