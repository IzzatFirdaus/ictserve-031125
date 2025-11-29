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
        Schema::table('assets', function (Blueprint $table) {
            if (! Schema::hasColumn('assets', 'purchase_price')) {
                $table->decimal('purchase_price', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('assets', 'purchase_date')) {
                $table->date('purchase_date')->nullable();
            }
            if (! Schema::hasColumn('assets', 'useful_life_years')) {
                $table->integer('useful_life_years')->default(5);
            }
            if (! Schema::hasColumn('assets', 'current_value')) {
                $table->decimal('current_value', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('assets', 'last_depreciation_calculation')) {
                $table->date('last_depreciation_calculation')->nullable();
            }
            if (! Schema::hasColumn('assets', 'accumulated_depreciation')) {
                $table->decimal('accumulated_depreciation', 10, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_price',
                'purchase_date',
                'useful_life_years',
                'current_value',
                'last_depreciation_calculation',
                'accumulated_depreciation',
            ]);
        });
    }
};
