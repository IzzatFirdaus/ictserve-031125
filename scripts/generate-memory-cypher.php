<?php
declare(strict_types=1);

// Lightweight importer: reads storage/mcp/memory.jsonl and generates a cypher
// script that creates nodes and relations for main sections, entity types, relation
// types and canonical documents. This is safe to run idempotently (uses MERGE).

$src = __DIR__ . '/../storage/mcp/memory.jsonl';
$out = __DIR__ . '/import-memory-jsonl.cypher';

if (! is_readable($src)) {
    fwrite(STDERR, "Missing or unreadable file: $src\n");
    exit(1);
}

$json = file_get_contents($src);
$data = json_decode($json, true);
if (! is_array($data)) {
    fwrite(STDERR, "Failed to parse JSON from memory.jsonl - aborting.\n");
    exit(1);
}

$lines = [];
$lines[] = "// Generated cypher script: memory.jsonl -> Neo4j";
$lines[] = "// Run: cat scripts/import-memory-jsonl.cypher | docker exec -i neo4j_db cypher-shell -u neo4j -p PASSWORD";
$lines[] = "\n// Root export node";

$exportName = $data['metadata']['source'] ?? 'MCP Memory Export';
$exportDate = $data['export_metadata']['last_updated'] ?? ($data['metadata']['export_date'] ?? null);

$exportNodeName = addslashes($exportName . ($exportDate ? " ({$exportDate})" : ''));
$lines[] = "MERGE (e:MemoryExport {name: \"$exportNodeName\"})";
$lines[] = "SET e.source = \"{$data['metadata']['source']}\", e.export_date = \"{$data['metadata']['export_date']}\"\n";

// Entity types
if (! empty($data['entity_types_ontology']) && is_array($data['entity_types_ontology'])) {
    $lines[] = "// Entity types (ontology)";
    foreach ($data['entity_types_ontology'] as $etype => $info) {
        $ename = addslashes($etype);
        $label = 'EntityType';
        $lines[] = "MERGE (t:$label {name: \"$ename\"})";
        $purpose = addslashes($info['purpose'] ?? '');
        $lines[] = "SET t.purpose = \"$purpose\"";
        $lines[] = "MERGE (e)-[:CONTAINS_ENTITY_TYPE]->(t)";
    }
    $lines[] = "\n";
}

// Relation types
if (! empty($data['relation_types_semantic']) && is_array($data['relation_types_semantic'])) {
    $lines[] = "// Relation types (semantic)\n";
    foreach ($data['relation_types_semantic'] as $rtype => $info) {
        $rname = addslashes($rtype);
        $label = 'RelationType';
        $meaning = addslashes($info['meaning'] ?? '');
        $example = addslashes($info['example'] ?? '');
        $lines[] = "MERGE (r:$label {name: \"$rname\"})";
        $lines[] = "SET r.meaning = \"$meaning\", r.example = \"$example\"";
        $lines[] = "MERGE (e)-[:DEFINES_RELATION_TYPE]->(r)";
    }
    $lines[] = "\n";
}

// Canonical documents list if present (D00-D15)
if (! empty($data['ictserve_documentation_d00_d15']) && is_array($data['ictserve_documentation_d00_d15'])) {
    $lines[] = "// Canonical D00-D15 documents";
    foreach ($data['ictserve_documentation_d00_d15'] as $docKey => $doc) {
        // Normalize the display name
        $prettyName = strtoupper(str_replace('_', ' ', $docKey));
        $nname = addslashes($prettyName);
        $label = 'CanonicalDocument';
        $version = addslashes($doc['version'] ?? '');
        $updated = addslashes($doc['updated'] ?? '');
        $summary = addslashes(substr(($doc['key_content'][0] ?? ''), 0, 250));

        $lines[] = "MERGE (d:$label {id: \"$docKey\", name: \"$nname\"})";
        $lines[] = "SET d.version = \"$version\", d.updated = \"$updated\", d.summary = \"$summary\"";
        $lines[] = "MERGE (e)-[:DOCUMENTS]->(d)";
    }
    $lines[] = "\n";
}

// Guides / features (docs_guides_and_features)
if (! empty($data['docs_guides_and_features']) && is_array($data['docs_guides_and_features'])) {
    $lines[] = "// Guides & features";
    foreach ($data['docs_guides_and_features'] as $section => $items) {
        if (! is_array($items)) continue;
        foreach ($items as $k => $v) {
            $name = addslashes($k);
            $desc = addslashes(is_string($v) ? $v : json_encode($v));
            $label = 'Guide';
            $lines[] = "MERGE (g:$label {name: \"$name\"})";
            $lines[] = "SET g.description = \"$desc\"";
            $lines[] = "MERGE (e)-[:DOCUMENTS]->(g)";
        }
    }
    $lines[] = "\n";
}

// Implementation-level sections: create nodes for high-level sections
$sections = ['filament_v4_migration_and_fixes', 'ictserve_implementation_progress', 'helpdesk_module_implementation_and_bugs', 'e2e_testing_debugging_and_completion'];
foreach ($sections as $s) {
    if (isset($data[$s]) && is_array($data[$s])) {
        $label = 'ImplementationSection';
        $name = addslashes($s);
        $summary = addslashes(substr(json_encode($data[$s]), 0, 200));
        $lines[] = "MERGE (s:$label {name: \"$name\"})";
        $lines[] = "SET s.summary = \"$summary\"";
        $lines[] = "MERGE (e)-[:DOCUMENTS]->(s)";
    }
}

$payload = implode("\n", $lines) . "\n";
file_put_contents($out, $payload);

fwrite(STDOUT, "Generated cypher script: $out\n");
fwrite(STDOUT, "Lines: " . count($lines) . "\n");

exit(0);
