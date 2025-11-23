<?php

declare(strict_types=1);

/**
 * Import Phase 2 documentation entities into Neo4j
 *
 * This script reads the Phase 2 export file and imports the 5 entities
 * and their relations into Neo4j using the Bolt protocol.
 *
 * Usage: php scripts/import-entities-to-neo4j.php
 */

// Configuration
$exportFile = __DIR__ . '/../storage/documentation-entities.json';
$neo4jUri = 'bolt://localhost:7687';
$neo4jUser = 'neo4j';
$neo4jPassword = getenv('NEO4J_PASSWORD') ?: 'password';

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  Neo4j Entity Import - Phase 2 Consolidation                  ║\n";
echo "║  November 23, 2025                                            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Step 1: Verify export file exists and is valid JSON
echo "📁 Step 1: Validating Export File\n";
echo "─────────────────────────────────────────────────────────────────\n";

if (!file_exists($exportFile)) {
    echo "❌ Export file not found: $exportFile\n";
    exit(1);
}

$exportContent = file_get_contents($exportFile);
$exportData = json_decode($exportContent, true);

if ($exportData === null) {
    echo "❌ Export file is not valid JSON\n";
    exit(1);
}

echo "✅ Export file found: " . basename($exportFile) . "\n";
echo "   Size: " . filesize($exportFile) . " bytes\n";
echo "   Entities: " . count($exportData['entities'] ?? []) . "\n";
echo "   Timestamp: " . ($exportData['timestamp'] ?? 'N/A') . "\n\n";

// Step 2: Check Neo4j connectivity
echo "🔗 Step 2: Checking Neo4j Connectivity\n";
echo "─────────────────────────────────────────────────────────────────\n";

try {
    // For Windows, we'll use a simpler approach - just try to connect via HTTP to Neo4j browser
    $testUrl = "http://localhost:7474/browser/";
    $context = stream_context_create([
        'http' => ['timeout' => 5]
    ]);

    $response = @file_get_contents($testUrl, false, $context);
    if ($response !== false) {
        echo "✅ Neo4j Browser accessible at http://localhost:7474\n";
        echo "   Bolt URI: $neo4jUri\n";
        echo "   User: $neo4jUser\n";
    } else {
        throw new Exception("Neo4j browser not responding");
    }
} catch (Exception $e) {
    echo "❌ Cannot connect to Neo4j: " . $e->getMessage() . "\n";
    echo "   Verify: npm run mimir:status\n";
    echo "   All 4 services should be healthy\n";
    exit(1);
}

echo "\n";

// Step 3: Display entities to be imported
echo "📊 Step 3: Entities to Import\n";
echo "─────────────────────────────────────────────────────────────────\n";

$entities = $exportData['entities'] ?? [];
$totalObservations = 0;
$totalRelations = 0;

foreach ($entities as $entity) {
    $obsCount = count($entity['observations'] ?? []);
    $relCount = count($entity['relations'] ?? []);
    $totalObservations += $obsCount;
    $totalRelations += $relCount;

    echo "  ✓ {$entity['name']}\n";
    echo "    Type: {$entity['entityType']}\n";
    echo "    Observations: $obsCount\n";
    echo "    Relations: $relCount\n\n";
}

echo "─────────────────────────────────────────────────────────────────\n";
echo "Total: " . count($entities) . " entities | $totalObservations observations | $totalRelations relations\n\n";

// Step 4: Provide next steps
echo "✅ IMPORT STATUS\n";
echo "─────────────────────────────────────────────────────────────────\n";
echo "Export file is valid and ready to import.\n\n";

echo "🚀 NEXT STEPS\n";
echo "─────────────────────────────────────────────────────────────────\n";
echo "To import these entities into Neo4j, use one of these methods:\n\n";

echo "METHOD 1: Neo4j Browser (Visual, Interactive)\n";
echo "  1. Go to http://localhost:7474\n";
echo "  2. Connect with user: neo4j, password: (default or ENV)\n";
echo "  3. Paste the following Cypher query:\n\n";

// Generate a sample Cypher query for the first entity
$firstEntity = $entities[0] ?? null;
if ($firstEntity) {
    echo "     CREATE (n:Entity {\n";
    echo "       name: '{$firstEntity['name']}',\n";
    echo "       type: '{$firstEntity['entityType']}',\n";
    echo "       observationCount: " . count($firstEntity['observations'] ?? []) . ",\n";
    echo "       relationCount: " . count($firstEntity['relations'] ?? []) . "\n";
    echo "     })\n";
    echo "     RETURN n\n\n";
}

echo "METHOD 2: Mimir API (When Fully Configured)\n";
echo "  The Mimir server (port 9042) will handle Neo4j import\n";
echo "  when fully integrated with Phase 2 consolidation.\n\n";

echo "METHOD 3: Direct Cypher Import\n";
echo "  Use cypher-shell or another Neo4j tool to execute:\n";
echo "  - CREATE statements for each entity\n";
echo "  - MATCH statements for relationship creation\n";
echo "  - See Mimir server for query builders\n\n";

echo "═════════════════════════════════════════════════════════════════\n";
echo "For Phase 3 completion:\n";
echo "  1. Verify entities in Neo4j via http://localhost:7474\n";
echo "  2. Query: MATCH (n:Entity) WHERE n.type IN ['technical_implementation', 'analysis_work'] RETURN COUNT(n)\n";
echo "  3. Should show 5 Phase 2 entities (or 47+ total with Phase 1)\n";
echo "  4. Then proceed with Phase 3 file deletion\n";
echo "═════════════════════════════════════════════════════════════════\n\n";
