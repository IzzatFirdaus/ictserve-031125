<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds 'auto_reply_approval' to the operation_type enum in message_logs table
     * Per Requirements 3.4: Approval workflow audit logging
     */
    public function up(): void
    {
        // For SQLite (testing), we need to recreate the table
        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN for enums, so we modify the column type
            Schema::table('message_logs', function (Blueprint $table) {
                $table->string('operation_type', 50)->change();
            });
        } else {
            // For MySQL, modify the enum
            DB::statement("ALTER TABLE message_logs MODIFY COLUMN operation_type ENUM('faq_query', 'document_analysis', 'auto_reply_generation', 'auto_reply_approval') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we can't easily revert enum changes
            // The string type will remain
        } else {
            // For MySQL, revert the enum
            DB::statement("ALTER TABLE message_logs MODIFY COLUMN operation_type ENUM('faq_query', 'document_analysis', 'auto_reply_generation') NOT NULL");
        }
    }
};
