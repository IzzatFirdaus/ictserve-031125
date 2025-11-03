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
        Schema::create('helpdesk_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('helpdesk_ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // For guest comments (if we allow them)
            $table->string('commenter_name')->nullable();
            $table->string('commenter_email')->nullable();

            $table->text('comment');
            $table->boolean('is_internal')->default(false); // Internal comments only visible to staff
            $table->boolean('is_resolution')->default(false); // Mark as resolution comment

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('helpdesk_ticket_id');
            $table->index('user_id');
            $table->index('is_internal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpdesk_comments');
    }
};
