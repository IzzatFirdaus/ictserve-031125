<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_template_id')
                ->constrained('email_templates')
                ->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('subject');
            $table->text('body_html');
            $table->text('body_text')->nullable();
            $table->json('variables')->nullable();
            $table->string('change_summary')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(['email_template_id', 'version_number']);
            $table->index(['email_template_id', 'created_at']);
        });

        // Add version tracking columns to email_templates
        Schema::table('email_templates', function (Blueprint $table) {
            $table->unsignedInteger('current_version')->default(1)->after('is_active');
            $table->foreignId('created_by')
                ->nullable()
                ->after('current_version')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->after('created_by')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['current_version', 'created_by', 'updated_by']);
        });

        Schema::dropIfExists('email_template_versions');
    }
};
