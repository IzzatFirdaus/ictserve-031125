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
            if (Schema::hasColumn('bedrock_conversations', 'user_id')) {
                // Drop foreign key first, then index, then column
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                    // Ignore if foreign key does not exist
                }

                try {
                    $table->dropIndex(['user_id']);
                } catch (\Exception $e) {
                    // Ignore if index does not exist
                }

                try {
                    $table->dropColumn('user_id');
                } catch (\Exception $e) {
                    // Ignore if column does not exist
                }
            }
        });
    }
};
