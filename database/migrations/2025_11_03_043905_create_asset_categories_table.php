<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Asset Categories Migration
 *
 * Defines categories for ICT equipment with custom specification templates.
 *
 * @see D03-FR-018.2 Asset categorization system
 * @see D04 §2.1 Database schema design
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Category name (e.g., Laptops, Projectors)');
            $table->string('code', 10)->unique()->comment('Short code for category');
            $table->text('description')->nullable()->comment('Category description');
            $table->json('specification_template')->nullable()->comment('JSON template for category specifications');
            $table->integer('default_loan_duration_days')->default(7)->comment('Default loan period in days');
            $table->integer('max_loan_duration_days')->default(30)->comment('Maximum loan period in days');
            $table->boolean('requires_approval')->default(true)->comment('Whether loans require approval');
            $table->boolean('is_active')->default(true)->comment('Category active status');
            $table->integer('sort_order')->default(0)->comment('Display sort order');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('code', 'idx_asset_cat_code');
            $table->index('is_active', 'idx_asset_cat_active');
            $table->index('sort_order', 'idx_asset_cat_sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
