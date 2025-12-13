<?php

declare(strict_types=1);

/**
 * name: create_memory_graph_tables
 * description: Introduces core tables for cross-extension agentic memory graph.
 * trace: D03-FR-020; D04 section 6.5; D11 section 5.3
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memory_entities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('entity_type');
            $table->json('labels')->nullable();
            $table->text('summary')->nullable();
            $table->json('metadata')->nullable();
            $table->string('source')->nullable();
            $table->string('source_identifier')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->timestamp('discovered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('entity_type', 'memory_entities_entity_type_idx');
        });

        Schema::create('memory_adapters', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('provider');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('config')->nullable();
            $table->json('capabilities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->string('sync_cursor')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['provider', 'name'], 'memory_adapters_provider_name_unique');
        });

        Schema::create('memory_observations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('memory_entity_id');
            $table->uuid('memory_adapter_id')->nullable();
            $table->string('content_hash')->nullable();
            $table->longText('content')->comment('Large content support for imported markdown files');
            $table->json('metadata')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('memory_entity_id')
                ->references('id')
                ->on('memory_entities')
                ->cascadeOnDelete();

            $table->foreign('memory_adapter_id')
                ->references('id')
                ->on('memory_adapters')
                ->nullOnDelete();

            $table->index('content_hash', 'memory_observations_content_hash_idx');
            $table->unique(['memory_entity_id', 'content_hash'], 'memory_observations_entity_hash_unique');
        });

        Schema::create('memory_relations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('from_entity_id');
            $table->uuid('to_entity_id');
            $table->string('relation_type');
            $table->json('metadata')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->timestamp('discovered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('from_entity_id')
                ->references('id')
                ->on('memory_entities')
                ->cascadeOnDelete();

            $table->foreign('to_entity_id')
                ->references('id')
                ->on('memory_entities')
                ->cascadeOnDelete();

            $table->index('relation_type', 'memory_relations_relation_type_idx');
            $table->unique(['from_entity_id', 'to_entity_id', 'relation_type'], 'memory_relations_unique');
        });

        Schema::create('memory_adapter_syncs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('memory_adapter_id');
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->json('error')->nullable();
            $table->unsignedInteger('synced_entities')->default(0);
            $table->unsignedInteger('synced_relations')->default(0);
            $table->unsignedInteger('synced_observations')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('memory_adapter_id')
                ->references('id')
                ->on('memory_adapters')
                ->cascadeOnDelete();
        });

        Schema::create('memory_adapter_entity', function (Blueprint $table): void {
            $table->uuid('memory_adapter_id');
            $table->uuid('memory_entity_id');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->primary(['memory_adapter_id', 'memory_entity_id'], 'memory_adapter_entity_pk');

            $table->foreign('memory_adapter_id')
                ->references('id')
                ->on('memory_adapters')
                ->cascadeOnDelete();

            $table->foreign('memory_entity_id')
                ->references('id')
                ->on('memory_entities')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memory_adapter_entity');
        Schema::dropIfExists('memory_adapter_syncs');
        Schema::dropIfExists('memory_relations');
        Schema::dropIfExists('memory_observations');
        Schema::dropIfExists('memory_adapters');
        Schema::dropIfExists('memory_entities');
    }
};
