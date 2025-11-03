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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name_ms');
            $table->string('name_en');
            $table->integer('level'); // Grade 41, 44, 48, etc.
            $table->boolean('can_approve_loans')->default(false); // Grade 41+ can approve
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('level');
            $table->index('can_approve_loans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
