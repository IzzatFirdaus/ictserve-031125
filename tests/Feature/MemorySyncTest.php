<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MemorySyncTest extends TestCase
{
    #[Test]
    public function sync_imports_markdown_file(): void
    {
        $filename = base_path('docs/memory_sync_test.md');

        file_put_contents($filename, "# Test Memory Import\n\nThis is a test of the memory import command.");

        // ensure MCP export entity exists for link
        \App\Models\MemoryEntity::create([
            'name' => 'MCP_MemoryServer_Complete_Export_2025-11-08',
            'entity_type' => 'canonical_document',
        ]);

        Artisan::call('memory:sync-markdown', ['--path' => [$filename]]);

        $this->assertDatabaseHas('memory_entities', [
            'name' => 'Test Memory Import',
            'source' => 'file',
        ]);

        // verify work session is created
        $this->assertDatabaseHas('memory_entities', ['entity_type' => 'work_session']);

        // Cleanup
        unlink($filename);
    }
}
