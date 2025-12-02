<?php

declare(strict_types=1);

/**
 * Export Memory Graph to storage/mcp/memory.jsonl
 *
 * This script combines:
 * 1. Existing storage/mcp/memory.jsonl (MCP documentation)
 * 2. Root memory.jsonl entities (JSONL format)
 *
 * Run: php scripts/export-memory-graph.php
 */

$basePath = dirname(__DIR__);
$rootMemoryFile = $basePath . '/memory.jsonl';
$storageMemoryFile = $basePath . '/storage/mcp/memory.jsonl';
$outputFile = $storageMemoryFile;

echo "=== MCP Memory Graph Export ===\n\n";

// Check if files exist
if (!file_exists($rootMemoryFile)) {
    echo "ERROR: Root memory.jsonl not found at: $rootMemoryFile\n";
    exit(1);
}

if (!file_exists($storageMemoryFile)) {
    echo "WARNING: Storage memory.jsonl not found. Creating new file.\n";
    touch($storageMemoryFile);
}

// Read root memory.jsonl entities
$rootContent = file_get_contents($rootMemoryFile);
$rootLines = explode("\n", $rootContent);

$entities = [];
foreach ($rootLines as $line) {
    $trimmed = trim($line);
    if (str_starts_with($trimmed, '{')) {
        $decoded = json_decode($trimmed, true);
        if ($decoded !== null) {
            $entities[] = $decoded;
        }
    }
}

echo "Found " . count($entities) . " entities in root memory.jsonl\n";

// Read storage file
$storageContent = file_get_contents($storageMemoryFile);
$storageSize = strlen($storageContent);
echo "Storage file size: " . number_format($storageSize) . " bytes\n";

// Check for duplicates by entity name
$existingNames = [];
preg_match_all('/"name"\s*:\s*"([^"]+)"/', $storageContent, $matches);
if (!empty($matches[1])) {
    $existingNames = array_flip($matches[1]);
}

echo "Existing entities in storage: " . count($existingNames) . "\n";

// Find new entities
$newEntities = [];
foreach ($entities as $entity) {
    $name = $entity['name'] ?? null;
    if ($name && !isset($existingNames[$name])) {
        $newEntities[] = $entity;
    }
}

echo "New entities to append: " . count($newEntities) . "\n\n";

if (count($newEntities) === 0) {
    echo "No new entities to add. Storage file is up to date.\n";
    exit(0);
}

// Append new entities
$appendContent = "\n\n# === Appended from root memory.jsonl on " . date('Y-m-d H:i:s') . " ===\n";
foreach ($newEntities as $entity) {
    $appendContent .= json_encode($entity, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
}

file_put_contents($outputFile, $appendContent, FILE_APPEND);

$newSize = filesize($outputFile);
echo "SUCCESS: Appended " . count($newEntities) . " entities\n";
echo "New storage file size: " . number_format($newSize) . " bytes\n";
echo "Output file: $outputFile\n";

// List added entities
echo "\nEntities added:\n";
foreach ($newEntities as $entity) {
    $name = $entity['name'] ?? 'Unknown';
    $type = $entity['entityType'] ?? 'Unknown';
    echo "  - $name ($type)\n";
}
