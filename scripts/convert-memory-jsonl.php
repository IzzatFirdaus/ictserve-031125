<?php

declare(strict_types=1);

/**
 * Convert mixed-format memory.jsonl to pure JSONL format
 *
 * The current file has:
 * 1. A large multi-line JSON documentation block (invalid for JSONL)
 * 2. Valid JSONL entity lines
 *
 * This script extracts only the valid JSONL entities.
 */

$inputFile = __DIR__ . '/../storage/mcp/memory.jsonl';
$outputFile = __DIR__ . '/../storage/mcp/memory-clean.jsonl';
$backupFile = __DIR__ . '/../storage/mcp/memory-backup-' . date('Y-m-d-His') . '.jsonl';

echo "=== Convert to Pure JSONL Format ===\n\n";

if (!file_exists($inputFile)) {
    echo "ERROR: Input file not found: $inputFile\n";
    exit(1);
}

// Backup original
copy($inputFile, $backupFile);
echo "✅ Backup created: $backupFile\n";

$content = file_get_contents($inputFile);
$lines = explode("\n", $content);

$entities = [];
$skipped = 0;

foreach ($lines as $line) {
    $trimmed = trim($line);

    // Skip empty lines and comments
    if (empty($trimmed) || str_starts_with($trimmed, '#')) {
        continue;
    }

    // Try to parse as JSON
    $decoded = json_decode($trimmed, true);

    if ($decoded === null) {
        $skipped++;
        continue;
    }

    // Check if it's a valid MCP entity (has name and entityType)
    if (isset($decoded['name']) && isset($decoded['entityType'])) {
        $entities[] = $decoded;
    }
}

echo "Found {$skipped} non-JSONL lines (documentation block)\n";
echo "Extracted " . count($entities) . " valid MCP entities\n";

// Write clean JSONL file
$output = "# MCP Memory Server Knowledge Graph\n";
$output .= "# Format: JSONL (one JSON object per line)\n";
$output .= "# Generated: " . date('Y-m-d H:i:s') . "\n";
$output .= "# Entities: " . count($entities) . "\n\n";

foreach ($entities as $entity) {
    $output .= json_encode($entity, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
}

file_put_contents($outputFile, $output);
echo "\n✅ Clean JSONL written to: $outputFile\n";
echo "   Size: " . number_format(filesize($outputFile)) . " bytes\n";

// Now replace original with clean version
copy($outputFile, $inputFile);
echo "\n✅ Original file updated with clean JSONL format\n";

// Verify
$verifyContent = file_get_contents($inputFile);
$verifyLines = explode("\n", $verifyContent);
$validCount = 0;
foreach ($verifyLines as $line) {
    $t = trim($line);
    if (empty($t) || str_starts_with($t, '#')) continue;
    $d = json_decode($t, true);
    if ($d !== null && isset($d['name'])) $validCount++;
}

echo "\n=== Verification ===\n";
echo "Valid entities in updated file: $validCount\n";

if ($validCount === count($entities)) {
    echo "✅ SUCCESS: All entities preserved\n";
} else {
    echo "⚠️ WARNING: Entity count mismatch\n";
}
