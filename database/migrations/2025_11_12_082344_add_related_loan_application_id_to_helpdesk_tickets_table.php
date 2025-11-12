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
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            $table->foreignId('related_loan_application_id')
                ->nullable()
                ->constrained('loan_applications')
                ->onDelete('set null')
                ->after('asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['related_loan_application_id']);
            $table->dropColumn('related_loan_application_id');
        });
    }
};
