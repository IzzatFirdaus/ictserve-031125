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
            if (Schema::hasColumn('loan_applications', 'tracking_token')) {
                $table->dropUnique(['tracking_token']);
                $table->dropIndex(['tracking_token']);
            }

            $columnsToDrop = array_filter([
                'tracking_token',
                'tracking_token_expires_at',
            ], static fn (string $column): bool => Schema::hasColumn('loan_applications', $column));

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
