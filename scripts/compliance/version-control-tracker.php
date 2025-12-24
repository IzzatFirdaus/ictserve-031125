<?php

declare(strict_types=1);

/**
 * Version Control Tracking System
 *
 * Manages version control tracking following KRISA template guidelines
 * per requirements.md sections 10.1, 10.2, 10.3, 10.4, 10.5
 */
class VersionControlTracker
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

    private array $targetVersions = [
        'D01' => '2.0', // Major compliance update
        'D02' => '4.0', // Major compliance update
        'D03' => '3.0', // Major compliance update
        'D04' => '3.0', // Major compliance update
        'D05' => '2.0',
        'D06' => '2.0',
        'D07' => '2.0',
        'D08' => '2.0',
        'D09' => '2.0',
        'D10' => '2.0',
        'D15' => '2.0',
        'D17' => '2.0',
    ];

    public function getCurrentVersions(): array
    {
        $versions = [];

        foreach ($this->documentPaths as $docId => $path) {
            if (file_exists($path)) {
                $content = file_get_contents($path);
                $version = $this->extractVersion($content);
                $versions[$docId] = [
                    'current' => $version,
                    'target' => $this->targetVersions[$docId],
                    'needs_update' => version_compare($version, $this->targetVersions[$docId], '<'),
                    'path' => $path,
                ];
            } else {
                $versions[$docId] = [
                    'current' => 'NOT_FOUND',
                    'target' => $this->targetVersions[$docId],
                    'needs_update' => true,
                    'path' => $path,
                ];
            }
        }

        return $versions;
    }

    private function extractVersion(string $content): string
    {
        // Try different version patterns
        $patterns = [
            '/\*\*VERSI\s+DOKUMEN\*\*\s*\|\s*:\s*v?(\d+\.\d+)/i',
            '/VERSI\s+DOKUMEN.*?v?(\d+\.\d+)/i',
            '/Version\s*:?\s*v?(\d+\.\d+)/i',
            '/v(\d+\.\d+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                return $matches[1];
            }
        }

        return '0.0';
    }

    public function updateDocumentVersion(string $docId, string $newVersion, string $changeDescription, string $author): bool
    {
        if (! isset($this->documentPaths[$docId])) {
            return false;
        }

        $path = $this->documentPaths[$docId];
        if (! file_exists($path)) {
            return false;
        }

        $content = file_get_contents($path);
        $currentDate = date('d/m/Y');

        // Update version in header table
        $content = preg_replace(
            '/(\*\*VERSI\s+DOKUMEN\*\*\s*\|\s*:\s*)v?(\d+\.\d+)/i',
            '${1}'.$newVersion,
            $content
        );

        // Update date in header table
        $content = preg_replace(
            '/(\*\*TARIKH\s+DOKUMEN\*\*\s*\|\s*:\s*)([^|]+)/i',
            '${1}'.$currentDate,
            $content
        );

        // Add entry to document control section (iii. Kawalan Dokumen)
        $controlEntry = "| {$newVersion} | {$currentDate} | {$changeDescription} | {$author} |";

        // Find the document control table and add the new entry
        if (preg_match('/(## iii\. Kawalan Dokumen.*?\| :--- \| :--- \| :--- \| :--- \|)/s', $content, $matches)) {
            $replacement = $matches[1]."\n".$controlEntry;
            $content = str_replace($matches[1], $replacement, $content);
        }

        // Create backup
        $backupPath = $path.'.backup.'.date('Y-m-d-H-i-s');
        copy($path, $backupPath);

        // Write updated content
        return file_put_contents($path, $content) !== false;
    }

    public function generateChangeLog(string $docId): array
    {
        if (! isset($this->documentPaths[$docId])) {
            return [];
        }

        $path = $this->documentPaths[$docId];
        if (! file_exists($path)) {
            return [];
        }

        $content = file_get_contents($path);
        $changes = [];

        // Extract change log from document control section
        if (preg_match('/## iii\. Kawalan Dokumen.*?\| :--- \| :--- \| :--- \| :--- \|(.*?)(?=##|$)/s', $content, $matches)) {
            $tableContent = $matches[1];

            // Parse table rows
            preg_match_all('/\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|/', $tableContent, $rows, PREG_SET_ORDER);

            foreach ($rows as $row) {
                $version = trim($row[1]);
                $date = trim($row[2]);
                $description = trim($row[3]);
                $author = trim($row[4]);

                if (! empty($version) && $version !== 'No. Versi') {
                    $changes[] = [
                        'version' => $version,
                        'date' => $date,
                        'description' => $description,
                        'author' => $author,
                    ];
                }
            }
        }

        return array_reverse($changes); // Most recent first
    }

    public function validateVersionConsistency(): array
    {
        $violations = [];
        $versions = $this->getCurrentVersions();

        foreach ($versions as $docId => $versionInfo) {
            if ($versionInfo['current'] === 'NOT_FOUND') {
                $violations[] = "Document {$docId} not found";

                continue;
            }

            // Check if version follows KRISA guidelines (X.Y format)
            if (! preg_match('/^\d+\.\d+$/', $versionInfo['current'])) {
                $violations[] = "Document {$docId} has invalid version format: {$versionInfo['current']} (should be X.Y)";
            }

            // Check if version needs update for compliance
            if ($versionInfo['needs_update']) {
                $violations[] = "Document {$docId} version {$versionInfo['current']} needs update to {$versionInfo['target']} for compliance";
            }

            // Validate change log entries
            $changeLog = $this->generateChangeLog($docId);
            if (empty($changeLog)) {
                $violations[] = "Document {$docId} missing change log entries in section iii. Kawalan Dokumen";
            } else {
                // Check if latest version matches document header
                $latestChange = $changeLog[0];
                if ($latestChange['version'] !== $versionInfo['current']) {
                    $violations[] = "Document {$docId} version mismatch: header shows {$versionInfo['current']}, latest change log entry shows {$latestChange['version']}";
                }
            }
        }

        return $violations;
    }

    public function generateComplianceChangeLog(): string
    {
        $changeDescription = 'PKS/PSPM compliance update - Eliminated guest access violations per PKS 5.2.1, implemented data sovereignty controls per PKS 9.2.1 & 4.2, added KRISA template compliance, updated HRMIS integration requirements, and enhanced security documentation per PKS 5.4.3';

        return $changeDescription;
    }

    public function generateReport(): string
    {
        $versions = $this->getCurrentVersions();
        $violations = $this->validateVersionConsistency();

        $report = "VERSION CONTROL TRACKING REPORT\n";
        $report .= 'Generated: '.date('Y-m-d H:i:s')."\n";
        $report .= str_repeat('=', 50)."\n\n";

        // Version status for each document
        foreach ($versions as $docId => $versionInfo) {
            $report .= "Document: {$docId}\n";
            $report .= str_repeat('-', 20)."\n";
            $report .= "Current Version: {$versionInfo['current']}\n";
            $report .= "Target Version: {$versionInfo['target']}\n";
            $report .= 'Status: '.($versionInfo['needs_update'] ? 'NEEDS UPDATE' : 'CURRENT')."\n";

            // Show recent changes
            $changeLog = $this->generateChangeLog($docId);
            if (! empty($changeLog)) {
                $report .= "Recent Changes:\n";
                foreach (array_slice($changeLog, 0, 3) as $change) {
                    $report .= "  {$change['version']} ({$change['date']}): {$change['description']}\n";
                }
            }
            $report .= "\n";
        }

        // Violations
        if (! empty($violations)) {
            $report .= "VERSION CONTROL VIOLATIONS\n";
            $report .= str_repeat('=', 30)."\n";
            foreach ($violations as $violation) {
                $report .= "- {$violation}\n";
            }
            $report .= "\n";
        }

        // Summary
        $needsUpdate = array_filter($versions, fn ($v) => $v['needs_update']);
        $report .= "SUMMARY\n";
        $report .= str_repeat('=', 20)."\n";
        $report .= 'Total Documents: '.count($versions)."\n";
        $report .= 'Documents Needing Update: '.count($needsUpdate)."\n";
        $report .= 'Version Control Violations: '.count($violations)."\n";
        $report .= 'Overall Status: '.(empty($violations) ? 'COMPLIANT' : 'NON-COMPLIANT')."\n";

        return $report;
    }
}

// CLI execution
if (php_sapi_name() === 'cli') {
    $tracker = new VersionControlTracker;

    $command = $argv[1] ?? 'report';

    switch ($command) {
        case 'report':
            $report = $tracker->generateReport();
            echo $report;

            // Save report to file
            $reportPath = 'storage/compliance/version-control-report-'.date('Y-m-d-H-i-s').'.txt';
            @mkdir(dirname($reportPath), 0755, true);
            file_put_contents($reportPath, $report);
            echo "\nReport saved to: {$reportPath}\n";
            break;

        case 'update':
            if (count($argv) < 5) {
                echo "Usage: php version-control-tracker.php update <docId> <version> <author> [description]\n";
                exit(1);
            }

            $docId = $argv[2];
            $version = $argv[3];
            $author = $argv[4];
            $description = $argv[5] ?? $tracker->generateComplianceChangeLog();

            if ($tracker->updateDocumentVersion($docId, $version, $description, $author)) {
                echo "Successfully updated {$docId} to version {$version}\n";
            } else {
                echo "Failed to update {$docId}\n";
                exit(1);
            }
            break;

        case 'changelog':
            if (count($argv) < 3) {
                echo "Usage: php version-control-tracker.php changelog <docId>\n";
                exit(1);
            }

            $docId = $argv[2];
            $changes = $tracker->generateChangeLog($docId);

            echo "Change Log for {$docId}:\n";
            echo str_repeat('=', 30)."\n";
            foreach ($changes as $change) {
                echo "{$change['version']} ({$change['date']}) by {$change['author']}\n";
                echo "  {$change['description']}\n\n";
            }
            break;

        default:
            echo "Available commands: report, update, changelog\n";
            exit(1);
    }
}
