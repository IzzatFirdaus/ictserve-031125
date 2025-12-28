<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MemoryApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Set the memory API token in config before any requests
        // This must be done after parent::setUp() to ensure the app is bootstrapped
        $this->app['config']->set('app.memory_api_token', 'test-token-123');
    }

    #[Test]
    public function agent_can_push_memory_with_token(): void
    {
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

    #[Test]
    public function memory_search_returns_entities_and_observations(): void
    {
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
