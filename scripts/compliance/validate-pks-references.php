<?php

declare(strict_types=1);

/**
 * PKS Reference Validation Script
 *
 * Validates that KRISA documents contain proper PKS policy references
 * per sections 5.2.1, 9.2.1, 4.2, and 5.4.3 as specified in requirements.md
 */
class PKSReferenceValidator
{
    private array $requiredPKSSections = [
        '5.2.1' => 'Accountability and Non-repudiation principles',
        '9.2.1' => 'Data transfer procedures and confidentiality protection',
        '4.2' => 'Data sovereignty and jurisdiction requirements',
        '5.4.3' => 'Password policy requirements (8 chars, 90-day expiry, 3 attempts)',
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
        $foundReferences = [];

        // Check for PKS section references
        foreach ($this->requiredPKSSections as $section => $description) {
            $patterns = [
                "/PKS\s+{$section}/i",
                "/Polisi\s+Keselamatan\s+Siber.*{$section}/i",
                "/section\s+{$section}/i",
                "/{$section}.*accountability/i", // For 5.2.1 specifically
            ];

            $found = false;
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                $foundReferences[] = $section;
            } else {
                $violations[] = "Missing PKS section {$section} reference: {$description}";
            }
        }

        // Check for guest access violations (PKS 5.2.1)
        $guestPatterns = [
            '/guest\s+mode/i',
            '/guest\s+access/i',
            '/anonymous\s+user/i',
            '/true\s+hybrid/i',
        ];

        foreach ($guestPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $violations[] = 'PKS 5.2.1 violation: Found guest/anonymous access reference';
            }
        }

        // Check for proper authentication references
        $authPatterns = [
            '/SSO\s+authentication/i',
            '/LDAP.*Active\s+Directory/i',
            '/Walk-in.*Kiosk\s+Mode/i',
        ];

        $hasProperAuth = false;
        foreach ($authPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $hasProperAuth = true;
                break;
            }
        }

        if (! $hasProperAuth) {
            $violations[] = 'Missing proper authentication specification per PKS 5.2.1';
        }

        // Check for data sovereignty references (PKS 9.2.1 & 4.2)
        if (in_array($docId, ['D02', 'D03', 'D04', 'D08'])) {
            $sovereigntyPatterns = [
                '/MyGovCloud/i',
                '/data\s+sovereignty/i',
                '/intranet.*only/i',
                '/local.*processing/i',
            ];

            $hasSovereignty = false;
            foreach ($sovereigntyPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    $hasSovereignty = true;
                    break;
                }
            }

            if (! $hasSovereignty) {
                $violations[] = 'Missing data sovereignty compliance per PKS 9.2.1 & 4.2';
            }
        }

        return [
            'exists' => true,
            'violations' => $violations,
            'found_references' => $foundReferences,
            'compliance_score' => count($foundReferences) / count($this->requiredPKSSections) * 100,
        ];
    }

    public function generateReport(array $results): string
    {
        $report = "PKS COMPLIANCE VALIDATION REPORT\n";
        $report .= 'Generated: '.date('Y-m-d H:i:s')."\n";
        $report .= str_repeat('=', 50)."\n\n";

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
            $report .= 'Compliance Score: '.round($result['compliance_score'], 1)."%\n";
            $report .= 'PKS References Found: '.implode(', ', $result['found_references'])."\n";

            if ($violationCount > 0) {
                $report .= "Violations ({$violationCount}):\n";
                foreach ($result['violations'] as $violation) {
                    $report .= "  - {$violation}\n";
                }
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
    $validator = new PKSReferenceValidator;
    $results = $validator->validateAllDocuments();
    $report = $validator->generateReport($results);

    echo $report;

    // Save report to file
    $reportPath = 'storage/compliance/pks-validation-report-'.date('Y-m-d-H-i-s').'.txt';
    @mkdir(dirname($reportPath), 0755, true);
    file_put_contents($reportPath, $report);

    echo "\nReport saved to: {$reportPath}\n";

    // Exit with error code if violations found
    $hasViolations = array_reduce($results, function ($carry, $result) {
        return $carry || (! empty($result['violations']));
    }, false);

    exit($hasViolations ? 1 : 0);
}
