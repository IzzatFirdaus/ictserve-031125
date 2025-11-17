<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemoryApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_agent_can_push_memory_with_token(): void
    {
        putenv('MEMORY_API_TOKEN=test-token-123');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token-123',
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/memory/import', [
            'title' => 'API Memory Test',
            'content' => 'This is a test from agent',
            'entity_type' => 'analysis_work',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('memory_entities', ['name' => 'API Memory Test']);
    }

    public function test_memory_search_returns_entities_and_observations(): void
    {
        putenv('MEMORY_API_TOKEN=test-token-123');

        // create a memory item
        $this->withHeaders([
            'Authorization' => 'Bearer test-token-123',
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/memory/import', [
            'title' => 'Searchable Memory',
            'content' => 'We will search for this phrase: unique-search-token-abc',
            'entity_type' => 'analysis_work',
        ])->assertStatus(201);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token-123',
        ])->getJson('/api/v1/memory/search?q=unique-search-token-abc');

        $response->assertStatus(200)
            ->assertJsonStructure(['entities', 'observations']);
    }
}
