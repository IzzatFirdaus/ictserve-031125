<?php

declare(strict_types=1);

/**
 * KRISA Template Compliance Validation Script
 *
 * Validates that KRISA documents follow official template structure
 * and formatting requirements per design.md Properties 3, 5
 */
class KRISATemplateValidator
{
    private array $requiredSections = [
        'i. Keterangan Dokumen',
        'ii. Semakan dan Pengesahan Dokumen',
        'iii. Kawalan Dokumen',
        'iv. Kandungan',
        'v. Senarai Gambarajah',
        'vi. Senarai Jadual',
        'vii. Definisi dan Akronim',
        'viii. Sumber Rujukan',
    ];

    private array $requiredHeaders = [
        'NAMA AGENSI',
        'NAMA AGENSI INDUK',
        'TARIKH DOKUMEN',
        'VERSI DOKUMEN',
    ];

    private array $methodologyReferences = [
        'D02' => ['Pemodelan Fungsi Bisnes [F1.3]'],
        'D03' => [
            'Pemodelan Fungsi Sistem [F2.2]',
            'Pemodelan Use Case [F2.1]',
            'Pemodelan Keperluan Data [F2.3]',
        ],
    ];

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

    public function validateAllDocuments(): array
    {
        $results = [];

        foreach ($this->documentPaths as $docId => $path) {
            if (file_exists($path)) {
                $results[$docId] = $this->validateDocument($path, $docId);
            } else {
                $results[$docId] = [
                    'exists' => false,
                    'violations' => ["Document not found: {$path}"],
                ];
            }
        }

        return $results;
    }

    private function validateDocument(string $path, string $docId): array
    {
        $content = file_get_contents($path);
        $violations = [];
        $foundSections = [];

        // Check required KRISA template sections
        foreach ($this->requiredSections as $section) {
            if (preg_match('/^##\s+'.preg_quote($section, '/').'/m', $content)) {
                $foundSections[] = $section;
            } else {
                $violations[] = "Missing required KRISA template section: {$section}";
            }
        }

        // Check required headers in document metadata table
        foreach ($this->requiredHeaders as $header) {
            if (! preg_match('/\*\*'.preg_quote($header, '/').'\*\*/', $content)) {
                $violations[] = "Missing required header: {$header}";
            }
        }

        // Check for proper KRISA methodology references
        if (isset($this->methodologyReferences[$docId])) {
            foreach ($this->methodologyReferences[$docId] as $methodology) {
                if (! preg_match('/'.preg_quote($methodology, '/').'/', $content)) {
                    $violations[] = "Missing required methodology reference: {$methodology}";
                }
            }
        }

        // Check for CRUD indicators in D09 (Database Documentation)
        if ($docId === 'D09') {
            $crudPatterns = ['/\(C\)/', '/\(R\)/', '/\(U\)/', '/\(D\)/'];
            $hasCRUD = false;
            foreach ($crudPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    $hasCRUD = true;
                    break;
                }
            }
            if (! $hasCRUD) {
                $violations[] = 'Missing CRUD indicators (C), (R), (U), (D) in database documentation';
            }
        }

        // Check for proper KRISA notation format (BF-ICT-XX-YY)
        if (in_array($docId, ['D02', 'D03'])) {
            if (! preg_match('/BF-ICT-\d+-\d+/', $content)) {
                $violations[] = 'Missing proper KRISA notation format (BF-ICT-XX-YY)';
            }
        }

        // Check for proper version control format
        if (! preg_match('/v?\d+\.\d+/', $content)) {
            $violations[] = 'Missing or improper version format (should be X.Y format)';
        }

        // Check for "Aktiviti Sebelum" and "Aktiviti Selepas" instead of "Syarat Pasca"
        if (preg_match('/Syarat\s+Pasca/i', $content)) {
            $violations[] = "Found deprecated 'Syarat Pasca' - should use 'Aktiviti Sebelum' and 'Aktiviti Selepas'";
        }

        // Check for proper agency information
        $agencyPatterns = [
            '/BPM.*Bahagian\s+Pengurusan\s+Maklumat/i',
            '/MOTAC.*Kementerian\s+Pelancongan/i',
        ];

        $hasAgencyInfo = false;
        foreach ($agencyPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $hasAgencyInfo = true;
                break;
            }
        }

        if (! $hasAgencyInfo) {
            $violations[] = 'Missing proper agency information (BPM/MOTAC)';
        }

        return [
            'exists' => true,
            'violations' => $violations,
            'found_sections' => $foundSections,
            'compliance_score' => count($foundSections) / count($this->requiredSections) * 100,
        ];
    }

    public function validateCrossReferences(): array
    {
        $violations = [];
        $documentVersions = [];

        // Extract version information from all documents
        foreach ($this->documentPaths as $docId => $path) {
            if (file_exists($path)) {
                $content = file_get_contents($path);
                if (preg_match('/VERSI\s+DOKUMEN.*?(\d+\.\d+)/i', $content, $matches)) {
                    $documentVersions[$docId] = $matches[1];
                }
            }
        }

        // Check for consistent cross-references
        foreach ($this->documentPaths as $docId => $path) {
            if (file_exists($path)) {
                $content = file_get_contents($path);

                // Look for references to other documents
                preg_match_all('/D(\d+).*?v?(\d+\.\d+)/', $content, $matches, PREG_SET_ORDER);

                foreach ($matches as $match) {
                    $referencedDoc = 'D'.str_pad($match[1], 2, '0', STR_PAD_LEFT);
                    $referencedVersion = $match[2];

                    if (isset($documentVersions[$referencedDoc])) {
                        if ($documentVersions[$referencedDoc] !== $referencedVersion) {
                            $violations[] = "Version mismatch in {$docId}: References {$referencedDoc} v{$referencedVersion} but actual version is v{$documentVersions[$referencedDoc]}";
                        }
                    }
                }
            }
        }

        return [
            'violations' => $violations,
            'document_versions' => $documentVersions,
        ];
    }

    public function generateReport(array $results, array $crossRefResults = []): string
    {
        $report = "KRISA TEMPLATE COMPLIANCE VALIDATION REPORT\n";
        $report .= 'Generated: '.date('Y-m-d H:i:s')."\n";
        $report .= str_repeat('=', 60)."\n\n";

        $totalViolations = 0;
        $totalDocuments = 0;

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

            $report .= 'Status: '.($violationCount === 0 ? 'COMPLIANT' : 'NON-COMPLIANT')."\n";
            $report .= 'Template Compliance Score: '.round($result['compliance_score'], 1)."%\n";
            $report .= 'Sections Found: '.count($result['found_sections']).'/'.count($this->requiredSections)."\n";

            if ($violationCount > 0) {
                $report .= "Violations ({$violationCount}):\n";
                foreach ($result['violations'] as $violation) {
                    $report .= "  - {$violation}\n";
                }
            }
            $report .= "\n";
        }

        // Cross-reference validation results
        if (! empty($crossRefResults)) {
            $report .= "CROSS-REFERENCE VALIDATION\n";
            $report .= str_repeat('=', 30)."\n";

            if (! empty($crossRefResults['violations'])) {
                $report .= "Cross-Reference Violations:\n";
                foreach ($crossRefResults['violations'] as $violation) {
                    $report .= "  - {$violation}\n";
                }
                $totalViolations += count($crossRefResults['violations']);
            } else {
                $report .= "All cross-references are consistent.\n";
            }

            $report .= "\nDocument Versions:\n";
            foreach ($crossRefResults['document_versions'] as $docId => $version) {
                $report .= "  {$docId}: v{$version}\n";
            }
            $report .= "\n";
        }

        $report .= "SUMMARY\n";
        $report .= str_repeat('=', 20)."\n";
        $report .= "Total Documents: {$totalDocuments}\n";
        $report .= "Total Violations: {$totalViolations}\n";
        $report .= 'Overall Status: '.($totalViolations === 0 ? 'COMPLIANT' : 'NON-COMPLIANT')."\n";

        return $report;
    }
}

// CLI execution
if (php_sapi_name() === 'cli') {
    $validator = new KRISATemplateValidator;
    $results = $validator->validateAllDocuments();
    $crossRefResults = $validator->validateCrossReferences();
    $report = $validator->generateReport($results, $crossRefResults);

    echo $report;

    // Save report to file
    $reportPath = 'storage/compliance/krisa-template-validation-report-'.date('Y-m-d-H-i-s').'.txt';
    @mkdir(dirname($reportPath), 0755, true);
    file_put_contents($reportPath, $report);

    echo "\nReport saved to: {$reportPath}\n";

    // Exit with error code if violations found
    $hasViolations = array_reduce($results, function ($carry, $result) {
        return $carry || (! empty($result['violations']));
    }, false) || ! empty($crossRefResults['violations']);

    exit($hasViolations ? 1 : 0);
}
