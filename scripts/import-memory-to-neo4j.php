<?php

declare(strict_types=1);

/**
 * Import memory.jsonl into Neo4j Mimir database
 * Creates entities and relationships from MCP Memory Server export
 */

require __DIR__ . '/../vendor/autoload.php';

use Laudis\Neo4j\ClientBuilder;

// Neo4j connection
$client = ClientBuilder::create()
    ->withDriver('bolt', 'bolt://neo4j:MxXhTKH3qntipYLa1e0QOluJ@localhost:7687')
    ->build();

// Load memory.jsonl
$memoryFile = __DIR__ . '/../storage/mcp/memory.jsonl';
if (!file_exists($memoryFile)) {
    die("Error: memory.jsonl not found at {$memoryFile}\n");
}

$memoryData = json_decode(file_get_contents($memoryFile), true);
if (!$memoryData) {
    die("Error: Failed to parse memory.jsonl\n");
}

echo "✅ Loaded memory.jsonl successfully\n";
echo "📊 Processing " . count($memoryData) . " top-level sections\n\n";

// Create entities from structured data
$entities = [];
$relations = [];

// Process each section as an entity
foreach ($memoryData as $key => $value) {
    if ($key === 'metadata' || $key === 'export_metadata') {
        continue; // Skip metadata
    }
    
    $entityName = ucwords(str_replace('_', ' ', $key));
    $entityType = 'knowledge_section';
    
    // Convert value to observations
    $observations = [];
    if (is_array($value)) {
        $observations = extractObservations($value, $key);
    } else {
        $observations[] = (string) $value;
    }
    
    $entities[] = [
        'name' => $entityName,
        'type' => $entityType,
        'observations' => $observations
    ];
}

// Create entities in Neo4j
echo "📝 Creating " . count($entities) . " entities in Neo4j...\n";
foreach ($entities as $entity) {
    $query = <<<CYPHER
    MERGE (e:MemoryEntity {name: \$name})
    SET e.entityType = \$type,
        e.observations = \$observations,
        e.created_at = datetime(),
        e.updated_at = datetime()
    RETURN e
    CYPHER;
    
    try {
        $client->run($query, [
            'name' => $entity['name'],
            'type' => $entity['type'],
            'observations' => $entity['observations']
        ]);
        echo "  ✅ Created: {$entity['name']}\n";
    } catch (\Exception $e) {
        echo "  ❌ Failed: {$entity['name']} - {$e->getMessage()}\n";
    }
}

// Create relationships
echo "\n🔗 Creating relationships...\n";
$relationshipMap = [
    'Knowledge Graph Architecture' => ['documents' => ['Session Initialization Protocol', 'Entity Types Ontology']],
    'Session Initialization Protocol' => ['uses' => ['Knowledge Graph Architecture']],
    'Lifecycle Operations' => ['implements' => ['Session Initialization Protocol']],
    'Real World Workflows' => ['uses' => ['Lifecycle Operations']],
    'ICTServe Specific Implementation' => ['documents' => ['ICTServe Documentation D00 D15']],
];

foreach ($relationshipMap as $from => $targets) {
    foreach ($targets as $relationType => $toList) {
        foreach ($toList as $to) {
            $query = <<<CYPHER
            MATCH (from:MemoryEntity {name: \$from})
            MATCH (to:MemoryEntity {name: \$to})
            MERGE (from)-[r:RELATION {type: \$relationType}]->(to)
            SET r.created_at = datetime()
            RETURN r
            CYPHER;
            
            try {
                $client->run($query, [
                    'from' => $from,
                    'to' => $to,
                    'relationType' => $relationType
                ]);
                echo "  ✅ {$from} --{$relationType}--> {$to}\n";
            } catch (\Exception $e) {
                echo "  ❌ Failed: {$from} -> {$to} - {$e->getMessage()}\n";
            }
        }
    }
}

echo "\n✅ Import complete!\n";
echo "📊 Run this to verify:\n";
echo "   docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ \"MATCH (n:MemoryEntity) RETURN count(n)\"\n";

/**
 * Extract observations from nested array
 */
function extractObservations(array $data, string $prefix = ''): array
{
    $observations = [];
    
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            if (isset($value[0]) && !is_array($value[0])) {
                // Simple array of strings
                $observations[] = "{$key}: " . implode(', ', array_slice($value, 0, 3));
            } else {
                // Nested structure
                $observations[] = "{$key}: " . json_encode($value, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            }
        } else {
            $observations[] = "{$key}: {$value}";
        }
    }
    
    return array_slice($observations, 0, 50); // Limit to 50 observations per entity
}
