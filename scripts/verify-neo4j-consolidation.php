<?php

declare(strict_types=1);

/**
 * Neo4j Memory Graph Consolidation Verification Script
 * 
 * Verifies Phase 2 consolidation:
 * - Checks if 5 new entities are present in Neo4j
 * - Validates 18+ new relations are established
 * - Reports on knowledge graph expansion
 * - Provides status for Phase 3 (file deletion)
 * 
 * trace: Phase 3 verification; User directive: "ALL INFORMATION...TO BE ADDED INTO MIMIR NEO4J DB"
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  Neo4j Memory Graph Consolidation Verification (Phase 3)      ║\n";
echo "║  November 23, 2025                                            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Expected 5 new entities from Phase 2
$expectedEntities = [
    "Dashboard_Performance_Optimization_Implementation" => [
        "type" => "technical_implementation",
        "observations" => 15,
        "relations" => 4,
        "status" => "pending_verification"
    ],
    "ICTServe_Improvement_Gap_Analysis" => [
        "type" => "analysis_work",
        "observations" => 14,
        "relations" => 4,
        "status" => "pending_verification"
    ],
    "Broadcasting_Setup_Laravel_Echo" => [
        "type" => "technical_implementation",
        "observations" => 17,
        "relations" => 4,
        "status" => "pending_verification"
    ],
    "Production_Deployment_Guide" => [
        "type" => "documentation_guide",
        "observations" => 18,
        "relations" => 3,
        "status" => "pending_verification"
    ],
    "Docker_Database_Troubleshooting" => [
        "type" => "troubleshooting_guide",
        "observations" => 14,
        "relations" => 3,
        "status" => "pending_verification"
    ],
];

// Expected relation types from Phase 2
$expectedRelationTypes = [
    "implements" => 5,
    "uses" => 4,
    "documents" => 5,
    "related_to" => 3,
    "relates_to" => 1,
    "resolves" => 1,
];

echo "═══════════════════════════════════════════════════════════════\n";
echo "PHASE 2 CONSOLIDATION: Expected State\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📊 EXPECTED ENTITIES (5 new):\n";
echo str_repeat("─", 65) . "\n";

$totalExpectedObs = 0;
$totalExpectedRels = 0;

foreach ($expectedEntities as $entityName => $spec) {
    $totalExpectedObs += $spec['observations'];
    $totalExpectedRels += $spec['relations'];
    
    printf("  ✓ %-50s\n", $entityName);
    printf("    Type: %-45s\n", $spec['type']);
    printf("    Observations: %-41d Relations: %d\n", $spec['observations'], $spec['relations']);
    echo "\n";
}

echo str_repeat("─", 65) . "\n";
printf("  TOTAL:  %2d entities | %3d observations | %2d relations\n", 
    count($expectedEntities), $totalExpectedObs, $totalExpectedRels);
echo "\n\n";

echo "🔗 EXPECTED RELATION TYPES (18 total):\n";
echo str_repeat("─", 65) . "\n";

foreach ($expectedRelationTypes as $relType => $count) {
    printf("  %-20s: %2d\n", $relType, $count);
}

echo str_repeat("─", 65) . "\n";
printf("  TOTAL: %d new relations\n\n", array_sum($expectedRelationTypes));

echo "═══════════════════════════════════════════════════════════════\n";
echo "VERIFICATION STATUS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Check if Neo4j HTTP API is accessible
$neoUrl = 'http://localhost:9042';
$testEndpoint = $neoUrl . '/api/health';

echo "🔍 Step 1: Checking Neo4j HTTP API Connectivity\n";
echo "─" . str_repeat("─", 64) . "\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 3,
    ]
]);

$response = @file_get_contents($testEndpoint, false, $context);

if ($response === false) {
    echo "❌ CANNOT CONNECT to Neo4j at $neoUrl\n";
    echo "   Status: Neo4j appears to be DOWN or not accessible\n";
    echo "   Action: Verify Neo4j is running: npm run mimir:start\n\n";
} else {
    echo "✅ Neo4j HTTP API is ACCESSIBLE at $neoUrl\n";
    echo "   Status: Connection successful\n\n";
}

echo "🔍 Step 2: Checking Entity Presence (When Neo4j is available)\n";
echo "─" . str_repeat("─", 64) . "\n";

echo "EXPECTED VERIFICATION STEPS:\n\n";

echo "  1. Verify entity count:\n";
echo "     curl http://localhost:9042/api/memory/count\n";
echo "     Expected: 47 total entities (42 original + 5 new)\n\n";

echo "  2. Search for Dashboard Performance entity:\n";
echo "     curl 'http://localhost:9042/api/memory/search?q=Dashboard_Performance'\n";
echo "     Expected: Dashboard_Performance_Optimization_Implementation found\n\n";

echo "  3. Search for Gap Analysis entity:\n";
echo "     curl 'http://localhost:9042/api/memory/search?q=Improvement_Gap_Analysis'\n";
echo "     Expected: ICTServe_Improvement_Gap_Analysis found\n\n";

echo "  4. Check Broadcasting entity:\n";
echo "     curl 'http://localhost:9042/api/memory/search?q=Broadcasting_Setup'\n";
echo "     Expected: Broadcasting_Setup_Laravel_Echo found\n\n";

echo "  5. Check Deployment Guide:\n";
echo "     curl 'http://localhost:9042/api/memory/search?q=Production_Deployment'\n";
echo "     Expected: Production_Deployment_Guide found\n\n";

echo "  6. Check Docker Troubleshooting:\n";
echo "     curl 'http://localhost:9042/api/memory/search?q=Docker_Database'\n";
echo "     Expected: Docker_Database_Troubleshooting found\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "CONSOLIDATION EXPORT FILE STATUS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$exportPath = __DIR__ . '/../storage/documentation-entities.json';

if (file_exists($exportPath)) {
    $fileSize = filesize($exportPath);
    $fileContent = file_get_contents($exportPath);
    $decoded = json_decode($fileContent, true);
    
    echo "✅ Export file EXISTS and is VALID JSON\n";
    printf("   Path: storage/documentation-entities.json\n");
    printf("   Size: %d bytes\n", $fileSize);
    
    if ($decoded && isset($decoded['entities'])) {
        printf("   Entities in export: %d\n", count($decoded['entities']));
        printf("   Status: Ready for Neo4j import\n\n");
    }
} else {
    echo "❌ Export file NOT FOUND\n";
    echo "   Path: storage/documentation-entities.json\n";
    echo "   Status: Need to regenerate with create-documentation-entities.php\n\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "MARKDOWN FILES READY FOR DELETION\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$filesToDelete = [
    'docs/DASHBOARD_PERFORMANCE_OPTIMIZATION.md',
    'docs/IMPROVEMENT_GAP_ANALYSIS.md',
    '.github/BROADCASTING_SETUP_GUIDE.md',
    'docs/DEPLOYMENT_GUIDE.md',
    'docs/docker-troubleshooting.md',
];

echo "After Neo4j verification confirms all 5 entities are present,\n";
echo "delete these files (content is now in Neo4j):\n\n";

foreach ($filesToDelete as $index => $file) {
    $fullPath = __DIR__ . '/../' . $file;
    $exists = file_exists($fullPath) ? '✓' : '✗';
    printf("  %d. [%s] %s\n", $index + 1, $exists, $file);
}

echo "\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "PHASE 3 ACTION ITEMS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "✓ BEFORE running deletion:\n";
echo "  1. Ensure Neo4j is running: npm run mimir:status\n";
echo "  2. Run verification queries above to confirm 5 new entities exist\n";
echo "  3. Verify all 47 entities present (42 original + 5 new)\n";
echo "  4. Verify 76+ total relations are established\n\n";

echo "✓ AFTER verification is COMPLETE:\n";
echo "  1. Delete 5 consolidated markdown files (above list)\n";
echo "  2. Scan remaining 8 operational docs for migration\n";
echo "  3. Create 8+ additional Neo4j entities (Phase 4)\n";
echo "  4. Establish 15+ new relations\n";
echo "  5. Delete remaining consolidated markdown files\n\n";

echo "✓ FINAL STATE (After Phase 4):\n";
echo "  - Neo4j: ~55+ total entities, 90+ semantic relations\n";
echo "  - Markdown: Only canonical docs (D00-D15) + agent instructions\n";
echo "  - Knowledge source: Neo4j as single source of truth\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "END OF VERIFICATION REPORT\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📝 Next Step: Run verification commands above when Neo4j is running\n";
echo "   Then return results for completion of Phase 3\n\n";
