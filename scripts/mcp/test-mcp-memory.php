<?php

declare(strict_types=1);

/**
 * Test MCP Memory Server Compatibility
 *
 * Validates that storage/mcp/memory.jsonl is compatible with
 *
 * @modelcontextprotocol/server-memory
 */
$memoryFile = __DIR__.'/../storage/mcp/memory.jsonl';

echo "=== MCP Memory Server Compatibility Test ===\n\n";

// 1. Check file exists
if (! file_exists($memoryFile)) {
    echo "❌ FAIL: memory.jsonl not found at: $memoryFile\n";
    exit(1);
}
echo "✅ File exists: $memoryFile\n";

// 2. Check file size
$size = filesize($memoryFile);
echo '✅ File size: '.number_format($size).' bytes ('.round($size / 1024, 2)." KB)\n";

// 3. Parse and validate JSONL format
$content = file_get_contents($memoryFile);
$lines = explode("\n", $content);

$stats = [
    'total_lines' => count($lines),
    'empty_lines' => 0,
    'comment_lines' => 0,
    'valid_json' => 0,
    'invalid_json' => 0,
    'entities' => 0,
    'entity_types' => [],
];

$errors = [];

foreach ($lines as $lineNum => $line) {
    $trimmed = trim($line);

    if (empty($trimmed)) {
        $stats['empty_lines']++;

        continue;
    }

    if (str_starts_with($trimmed, '#')) {
        $stats['comment_lines']++;

        continue;
    }

    $decoded = json_decode($trimmed, true);

    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
        $stats['invalid_json']++;
        if (count($errors) < 5) {
            $errors[] = 'Line '.($lineNum + 1).': '.json_last_error_msg().' - '.substr($trimmed, 0, 50).'...';
        }

        continue;
    }

    $stats['valid_json']++;

    // Check if it's an entity (has 'name' and 'entityType')
    if (isset($decoded['name']) && isset($decoded['entityType'])) {
        $stats['entities']++;
        $type = $decoded['entityType'];
        $stats['entity_types'][$type] = ($stats['entity_types'][$type] ?? 0) + 1;
    }
}

echo "\n=== Parsing Results ===\n";
echo "Total lines: {$stats['total_lines']}\n";
echo "Empty lines: {$stats['empty_lines']}\n";
echo "Comment lines: {$stats['comment_lines']}\n";
echo "Valid JSON objects: {$stats['valid_json']}\n";
echo "Invalid JSON lines: {$stats['invalid_json']}\n";
echo "MCP Entities (name+entityType): {$stats['entities']}\n";

if (! empty($stats['entity_types'])) {
    echo "\n=== Entity Types ===\n";
    arsort($stats['entity_types']);
    foreach ($stats['entity_types'] as $type => $count) {
        echo "  $type: $count\n";
    }
}

if (! empty($errors)) {
    echo "\n=== Parse Errors (first 5) ===\n";
    foreach ($errors as $error) {
        echo "  ❌ $error\n";
    }
}

// 4. Compatibility assessment
echo "\n=== MCP Compatibility Assessment ===\n";

$compatible = true;
$warnings = [];

if ($stats['entities'] === 0) {
    $compatible = false;
    echo "❌ No valid MCP entities found (entities need 'name' and 'entityType' fields)\n";
} else {
    echo "✅ Found {$stats['entities']} valid MCP entities\n";
}

if ($stats['invalid_json'] > 0) {
    $warnings[] = "File contains {$stats['invalid_json']} invalid JSON lines (may be metadata blocks)";
}

// Check for MCP-required entity structure
$sampleEntity = null;
foreach ($lines as $line) {
    $trimmed = trim($line);
    if (empty($trimmed) || str_starts_with($trimmed, '#')) {
        continue;
    }
    $decoded = json_decode($trimmed, true);
    if ($decoded && isset($decoded['name']) && isset($decoded['entityType'])) {
        $sampleEntity = $decoded;
        break;
    }
}

if ($sampleEntity) {
    echo "✅ Sample entity structure valid:\n";
    echo "   - name: {$sampleEntity['name']}\n";
    echo "   - entityType: {$sampleEntity['entityType']}\n";
    echo '   - observations: '.(isset($sampleEntity['observations']) ? count($sampleEntity['observations']).' items' : 'none')."\n";
    echo '   - relations: '.(isset($sampleEntity['relations']) ? count($sampleEntity['relations']).' items' : 'none')."\n";
}

if (! empty($warnings)) {
    echo "\n⚠️ Warnings:\n";
    foreach ($warnings as $w) {
        echo "  - $w\n";
    }
}

echo "\n=== Final Result ===\n";
if ($compatible) {
    echo "✅ COMPATIBLE: memory.jsonl is compatible with @modelcontextprotocol/server-memory\n";
    echo "\nTo use with VS Code Copilot, the MCP server config in .mcp.json is:\n";
    echo json_encode([
        'memory' => [
            'command' => 'npx',
            'args' => ['-y', '@modelcontextprotocol/server-memory', $memoryFile],
            'disabled' => false,
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n";
} else {
    echo "❌ NOT COMPATIBLE: Fix the issues above before using with MCP memory server\n";
    exit(1);
}
