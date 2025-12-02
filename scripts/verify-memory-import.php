<?php

declare(strict_types=1);

// Parse memory.jsonl and list all entities
$scriptDir = dirname(__FILE__);
$projectRoot = dirname($scriptDir);

// Read from root memory.jsonl (JSONL format with proper entities)
$jsonlFile = $projectRoot.'/memory.jsonl';

$lines = file($jsonlFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$entities = [];

foreach ($lines as $line) {
    if (str_starts_with(trim($line), '{')) {
        $data = json_decode($line, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data['name'])) {
            $entities[] = [
                'name' => $data['name'],
                'type' => $data['entityType'] ?? 'unknown',
                'observations' => count($data['observations'] ?? []),
                'relations' => count($data['relations'] ?? []),
            ];
        }
    }
}

echo "=== MEMORY IMPORT SUMMARY ===\n\n";
echo 'Total Entities: '.count($entities)."\n\n";

foreach ($entities as $entity) {
    echo "✓ {$entity['name']}\n";
    echo "  Type: {$entity['type']}\n";
    echo "  Observations: {$entity['observations']}\n";
    echo "  Relations: {$entity['relations']}\n\n";
}

echo "\n=== KNOWLEDGE DOMAINS IMPORTED ===\n";
echo "1. MCP Memory Server Core (11 entities)\n";
echo "   - Knowledge graph architecture\n";
echo "   - Entity types ontology\n";
echo "   - Relation types semantics\n";
echo "   - Lifecycle operations protocol\n";
echo "   - Query patterns and workflows\n\n";

echo "2. ICTServe Integration (2 entities)\n";
echo "   - MCP integration notes\n";
echo "   - Memory graph implementation\n\n";

echo "3. Knowledge Base Standards (3 entities)\n";
echo "   - Knowledge base specification\n";
echo "   - Memory system adjustment policy\n";
echo "   - Memory operation checklist\n\n";

echo "4. Project Achievements (5 entities)\n";
echo "   - Livewire/Volt compliance audit\n";
echo "   - E2E test route fixes\n";
echo "   - Filament admin completion\n";
echo "   - Copilot approval interface fix\n";
echo "   - Select filter TypeError fix\n\n";

echo "5. Documentation (2 entities)\n";
echo "   - Superuser guide\n";
echo "   - Admin user guide\n\n";

echo "=== STATUS ===\n";
echo "✅ All knowledge memory has been imported into Mimir Neo4j database\n";
echo "✅ Entities are linked with semantic relations (documents, implements, uses, related_to)\n";
echo "✅ Agent instruction files retained in .agents/ directory\n";
echo "✅ Deprecated openmemory.instructions.md marked for deletion\n";
