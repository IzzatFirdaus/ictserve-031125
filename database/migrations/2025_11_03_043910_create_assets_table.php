<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enhanced Assets Migration with Cross-Module Integration
 *
 * Comprehensive asset tracking with maintenance integration and cross-module
 * connectivity with the helpdesk system.
 *
 * @see D03-FR-003.1 Asset inventory management
 * @see D03-FR-016.2 Cross-module integration
 * @see D03-FR-018.1 Asset lifecycle management
 * @see D04 §2.1 Database schema design
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag', 50)->unique()->comment('Unique asset identifier');
            $table->string('name')->comment('Asset name/model');
            $table->string('brand', 100)->comment('Manufacturer/brand');
            $table->string('model', 100)->comment('Model number');
            $table->string('serial_number', 100)->unique()->nullable()->comment('Serial number');
            $table->foreignId('category_id')->constrained('asset_categories')->restrictOnDelete();

            // Asset specifications and details
            $table->json('specifications')->nullable()->comment('Technical specifications');
            $table->date('purchase_date')->comment('Date of purchase');
            $table->decimal('purchase_value', 10, 2)->comment('Original purchase value (RM)');
            $table->decimal('current_value', 10, 2)->comment('Current depreciated value (RM)');
            $table->enum('status', [
                'available',
                'loaned',
                'maintenance',
                'retired',
                'damaged',
            ])->default('available')->comment('Current asset status');
            $table->string('location')->comment('Physical location of asset');
            $table->enum('condition', [
                'excellent',
                'good',
                'fair',
                'poor',
                'damaged',
            ])->default('excellent')->comment('Physical condition');
            $table->json('accessories')->nullable()->comment('List of included accessories');
            $table->date('warranty_expiry')->nullable()->comment('Warranty expiration date');

            // Maintenance tracking
            $table->date('last_maintenance_date')->nullable()->comment('Last maintenance performed');
            $table->date('next_maintenance_date')->nullable()->comment('Next scheduled maintenance');

            // Cross-module integration metrics
            $table->integer('maintenance_tickets_count')->default(0)->comment('Count of helpdesk maintenance tickets');
            $table->json('loan_history_summary')->nullable()->comment('Summary of loan history');
            $table->json('availability_calendar')->nullable()->comment('Booking calendar data');
            $table->json('utilization_metrics')->nullable()->comment('Usage statistics');

            // Audit fields
            $table->timestamps();
            $table->softDeletes();

            // Performance indexes
            $table->index('asset_tag', 'idx_asset_tag');
            $table->index('category_id', 'idx_asset_category');
            $table->index('status', 'idx_asset_status');
            $table->index('condition', 'idx_asset_condition');
            $table->index('location', 'idx_asset_location');
            $table->index(['last_maintenance_date', 'next_maintenance_date'], 'idx_asset_maintenance');
            $table->index('serial_number', 'idx_asset_serial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
