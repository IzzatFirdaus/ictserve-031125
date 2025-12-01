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
        $tables = [
            'asset_categories',
            'divisions',
            'ticket_categories',
            'departments',
            'roles',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    if (! Schema::hasColumn($table, 'name_en')) {
                        $t->string('name_en')->nullable()->after('name');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'asset_categories',
            'divisions',
            'ticket_categories',
            'departments',
            'roles',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'name_en')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->dropColumn('name_en');
                });
            }
        }
    }
};
