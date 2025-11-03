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
        Schema::create('helpdesk_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('helpdesk_ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            $table->string('filename');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->string('file_path');
            $table->string('disk')->default('private'); // Storage disk

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('helpdesk_ticket_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpdesk_attachments');
    }
};
