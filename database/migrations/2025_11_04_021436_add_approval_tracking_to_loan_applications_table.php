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
            $table->string('approval_method', 20)
                ->nullable()
                ->after('approval_token_expires_at')
                ->comment('Approval decision source: email or portal');

            $table->text('approval_remarks')
                ->nullable()
                ->after('approval_method')
                ->comment('Remarks provided during the approval decision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn(['approval_method', 'approval_remarks']);
        });
    }
};
