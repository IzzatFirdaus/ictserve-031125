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
        Schema::create('helpdesk_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique(); // HD[YYYY][000001-999999]

            // HYBRID ARCHITECTURE - Nullable user_id for guest submissions
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Guest submission fields (used when user_id is null)
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone', 20)->nullable();

            // Enhanced guest submission fields
            $table->string('guest_grade', 10)->nullable();
            $table->string('guest_division', 100)->nullable();
            $table->string('guest_staff_id', 50)->nullable();

            // Organizational context (for authenticated users)
            $table->string('staff_id', 50)->nullable();
            $table->foreignId('division_id')->nullable()->constrained()->onDelete('set null');

            // Ticket details
            $table->foreignId('category_id')->constrained('ticket_categories')->onDelete('restrict');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->string('subject');
            $table->text('description');
            $table->string('damage_type')->nullable();
            $table->text('internal_notes')->nullable();

            // Status and workflow
            $table->enum('status', ['open', 'assigned', 'in_progress', 'pending_user', 'resolved', 'closed'])->default('open');
            $table->foreignId('assigned_to_division')->nullable()->constrained('divisions')->onDelete('set null');
            $table->string('assigned_to_agency')->nullable(); // External agency name
            $table->foreignId('assigned_to_user')->nullable()->constrained('users')->onDelete('set null');

            // Asset linking for hardware issues
            $table->foreignId('asset_id')->nullable()->constrained()->onDelete('set null');

            // SLA tracking
            $table->timestamp('sla_response_due_at')->nullable();
            $table->timestamp('sla_resolution_due_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('assigned_at')->nullable();

            // Admin notes
            $table->text('admin_notes')->nullable();
            $table->text('resolution_notes')->nullable();

            // Data governance
            $table->timestamp('anonymized_at')->nullable();
            $table->timestamp('claimed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('ticket_number');
            $table->index('user_id');
            $table->index('guest_email');
            $table->index('status');
            $table->index('priority');
            $table->index('category_id');
            $table->index('assigned_to_division');
            $table->index('asset_id');
            $table->index('guest_grade');
            $table->index('guest_division');
            $table->index('guest_staff_id');
            $table->index(['guest_email', 'status']);
            $table->index(['status', 'priority']);
            $table->index(['user_id', 'status']);
            $table->index('anonymized_at');
            $table->index('claimed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpdesk_tickets');
    }
};
