<?php

declare(strict_types=1);

/**
 * Cross-Reference Validation Script
 *
 * Validates document interconnections and version consistency
 * per design.md Property 5 specifications
 */
class CrossReferenceValidator
{
    private array $documentPaths = [
        'D01' => 'docs/KRISA/D01_KRISA_ICTSERVE_PELAN_PEMBANGUNAN_SISTEM.md',
        'D02' => 'docs/KRISA/D02_KRISA_ICTSERVE_SPESIFIKASI_KEPERLUAN_BISNES.md',
        'D03' => 'docs/KRISA/D03_KRISA_ICTSERVE_SPESIFIKASI_KEPERLUAN_SISTEM.md',
        'D04' => 'docs/KRISA/D04_KRISA_ICTSERVE_SPESIFIKASI_REKABENTUK_SISTEM.md',
        'D05' => 'docs/KRISA/D05_KRISA_ICTSERVE_PELAN_MIGRASI_DATA.md',
        'D06' => 'docs/KRISA/D06_KRISA_ICTSERVE_SPESIFIKASI_MIGRASI_DATA.md',
        'D07' => 'docs/KRISA/D07_KRISA_ICTSERVE_PELAN_INTEGRASI_SISTEM.md',
        'D08' => 'docs/KRISA/D08_KRISA_ICTSERVE_SPESIFIKASI_INTEGRASI_DATA.md',
        'D09' => 'docs/KRISA/D09_KRISA_ICTSERVE_DOKUMENTASI_PANGKALAN_DATA.md',
        'D10' => 'docs/KRISA/D10_KRISA_ICTSERVE_DOKUMENTASI_KOD_SUMBER.md',
        'D15' => 'docs/KRISA/D15_KRISA_ICTSERVE_LAPORAN_MIGRASI_DATA.md',
        'D17' => 'docs/KRISA/D17_KRISA_ICTSERVE_MANUAL_PENGGUNA_SISTEM.md',
    ];

    private array $expectedReferences = [
        'D01' => ['D02', 'D03', 'D04', 'D17'], // Development plan references subsequent documents
        'D02' => ['D03'], // BRS requirements traced to SRS specifications
        'D03' => ['D04'], // SRS specifications linked to design
        'D04' => ['D09'], // Design specifications linked to database documentation
        'D17' => ['D01', 'D02', 'D03', 'D04'], // User manuals reflect all system changes
    ];

    public function validateAllReferences(): array
    {
        $results = [];
        $documentVersions = $this->extractAllVersions();

        foreach ($this->documentPaths as $docId => $path) {
            if (file_exists($path)) {
                $results[$docId] = $this->validateDocumentReferences($path, $docId, $documentVersions);
            } else {
                $results[$docId] = [
                    'exists' => false,
                    'violations' => ["Document not found: {$path}"],
                ];
            }
        }

        return [
            'document_results' => $results,
            'document_versions' => $documentVersions,
            'orphaned_references' => $this->findOrphanedReferences($results),
        ];
    }

    private function extractAllVersions(): array
    {
        $versions = [];

        foreach ($this->documentPaths as $docId => $path) {
            if (file_exists($path)) {
                $content = file_get_contents($path);

                // Extract version from document header table
                if (preg_match('/\*\*VERSI\s+DOKUMEN\*\*\s*\|\s*:\s*([v]?\d+\.\d+)/i', $content, $matches)) {
                    $versions[$docId] = $matches[1];
                } elseif (preg_match('/VERSI\s+DOKUMEN.*?([v]?\d+\.\d+)/i', $content, $matches)) {
                    $versions[$docId] = $matches[1];
                } else {
                    $versions[$docId] = 'UNKNOWN';
                }
            }
        }

        return $versions;
    }

    private function validateDocumentReferences(string $path, string $docId, array $documentVersions): array
    {
        $content = file_get_contents($path);
        $violations = [];
        $foundReferences = [];
        $versionMismatches = [];

        // Find all document references in the content
        preg_match_all('/D(\d{2})(?:\s*[v]?(\d+\.\d+))?/i', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $referencedDoc = 'D'.$match[1];
            $referencedVersion = isset($match[2]) ? $match[2] : null;

            if ($referencedDoc !== $docId) { // Don't count self-references
                $foundReferences[] = $referencedDoc;

                // Check version consistency if version is specified
                if ($referencedVersion && isset($documentVersions[$referencedDoc])) {
                    $actualVersion = str_replace('v', '', $documentVersions[$referencedDoc]);
                    if ($actualVersion !== $referencedVersion) {
                        $versionMismatches[] = [
                            'referenced_doc' => $referencedDoc,
                            'referenced_version' => $referencedVersion,
                            'actual_version' => $actualVersion,
                        ];
                    }
                }
            }
        }

        // Remove duplicates
        $foundReferences = array_unique($foundReferences);

        // Check expected references
        if (isset($this->expectedReferences[$docId])) {
            foreach ($this->expectedReferences[$docId] as $expectedRef) {
                if (! in_array($expectedRef, $foundReferences)) {
                    $violations[] = "Missing expected reference to {$expectedRef}";
                }
            }
        }

        // Check for broken references (references to non-existent documents)
        foreach ($foundReferences as $ref) {
            if (! isset($this->documentPaths[$ref])) {
                $violations[] = "Reference to non-existent document: {$ref}";
            } elseif (! file_exists($this->documentPaths[$ref])) {
                $violations[] = "Reference to missing document file: {$ref}";
            }
        }

        // Add version mismatch violations
        foreach ($versionMismatches as $mismatch) {
            $violations[] = "Version mismatch: References {$mismatch['referenced_doc']} v{$mismatch['referenced_version']} but actual version is v{$mismatch['actual_version']}";
        }

        // Check for traceability requirements
        $this->validateTraceability($content, $docId, $violations);

        return [
            'exists' => true,
            'violations' => $violations,
            'found_references' => $foundReferences,
            'version_mismatches' => $versionMismatches,
            'reference_count' => count($foundReferences),
        ];
    }

    private function validateTraceability(string $content, string $docId, array &$violations): void
    {
        // Check for proper traceability statements
        $traceabilityPatterns = [
            '/requirements?\s+\d+\.\d+/i',
            '/section\s+\d+\.\d+/i',
            '/design\s+property\s+\d+/i',
        ];

        $hasTraceability = false;
        foreach ($traceabilityPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $hasTraceability = true;
                break;
            }
        }

        if (! $hasTraceability && in_array($docId, ['D02', 'D03', 'D04'])) {
            $violations[] = 'Missing traceability references to requirements or design properties';
        }

        // Check for complete D01-D17 series reference
        if ($docId === 'D01') {
            $seriesPattern = '/D01.*D17|complete.*documentation.*series/i';
            if (! preg_match($seriesPattern, $content)) {
                $violations[] = 'Missing reference to complete D01-D17 documentation series';
            }
        }
    }

    private function findOrphanedReferences(array $results): array
    {
        $orphaned = [];
        $allReferences = [];

        // Collect all references
        foreach ($results as $docId => $result) {
            if ($result['exists'] && isset($result['found_references'])) {
                foreach ($result['found_references'] as $ref) {
                    $allReferences[$ref][] = $docId;
                }
            }
        }

        // Find documents that are never referenced
        foreach ($this->documentPaths as $docId => $path) {
            if (! isset($allReferences[$docId]) && $docId !== 'D01') { // D01 is typically the root document
                $orphaned[] = $docId;
            }
        }

        return $orphaned;
    }

    public function generateReport(array $validationResults): string
    {
        $results = $validationResults['document_results'];
        $versions = $validationResults['document_versions'];
        $orphaned = $validationResults['orphaned_references'];

        $report = "CROSS-REFERENCE VALIDATION REPORT\n";
        $report .= 'Generated: '.date('Y-m-d H:i:s')."\n";
        $report .= str_repeat('=', 50)."\n\n";

        $totalViolations = 0;
        $totalDocuments = 0;

        // Document-specific results
        foreach ($results as $docId => $result) {
            $report .= "Document: {$docId}\n";
            $report .= str_repeat('-', 20)."\n";

            if (! $result['exists']) {
                $report .= "Status: NOT FOUND\n";
                $report .= 'Issues: '.implode(', ', $result['violations'])."\n\n";

                continue;
            }

            $totalDocuments++;
            $violationCount = count($result['violations']);
            $totalViolations += $violationCount;

            $report .= 'Status: '.($violationCount === 0 ? 'VALID' : 'INVALID')."\n";
            $report .= 'Version: '.($versions[$docId] ?? 'UNKNOWN')."\n";
            $report .= 'References Found: '.implode(', ', $result['found_references'])."\n";
            $report .= 'Reference Count: '.$result['reference_count']."\n";

            if ($violationCount > 0) {
                $report .= "Violations ({$violationCount}):\n";
                foreach ($result['violations'] as $violation) {
                    $report .= "  - {$violation}\n";
                }
            }
            $report .= "\n";
        }

        // Document versions summary
        $report .= "DOCUMENT VERSIONS\n";
        $report .= str_repeat('=', 20)."\n";
        foreach ($versions as $docId => $version) {
            $report .= "{$docId}: {$version}\n";
        }
        $report .= "\n";

        // Orphaned documents
        if (! empty($orphaned)) {
            $report .= "ORPHANED DOCUMENTS (Never Referenced)\n";
            $report .= str_repeat('=', 40)."\n";
            foreach ($orphaned as $docId) {
                $report .= "- {$docId}\n";
            }
            $report .= "\n";
        }

        // Summary
        $report .= "SUMMARY\n";
        $report .= str_repeat('=', 20)."\n";
        $report .= "Total Documents: {$totalDocuments}\n";
        $report .= "Total Violations: {$totalViolations}\n";
        $report .= 'Orphaned Documents: '.count($orphaned)."\n";
        $report .= 'Overall Status: '.($totalViolations === 0 ? 'VALID' : 'INVALID')."\n";

        return $report;
    }
}

// CLI execution
if (php_sapi_name() === 'cli') {
    $validator = new CrossReferenceValidator;
    $results = $validator->validateAllReferences();
    $report = $validator->generateReport($results);

    echo $report;

    // Save report to file
    $reportPath = 'storage/compliance/cross-reference-validation-report-'.date('Y-m-d-H-i-s').'.txt';
    @mkdir(dirname($reportPath), 0755, true);
    file_put_contents($reportPath, $report);

    echo "\nReport saved to: {$reportPath}\n";

    // Exit with error code if violations found
    $hasViolations = array_reduce($results['document_results'], function ($carry, $result) {
        return $carry || (! empty($result['violations']));
    }, false) || ! empty($results['orphaned_references']);

    exit($hasViolations ? 1 : 0);
}
