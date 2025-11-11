<?php

/**
 * Hardcoded String Scanner for Laravel Localization
 *
 * Scans Blade, PHP, and JS files to identify hardcoded user-facing text
 * that should be moved to translation files.
 *
 * @author Localization Audit Team
 * @version 1.0.0
 * @created 2025-11-11
 */

declare(strict_types=1);

// Configuration
$baseDir = dirname(__DIR__);
$scanDirs = [
    'resources/views',
    'app/Livewire',
    'app/Filament',
    'app/Http/Controllers',
    'resources/js'
];

$excludeDirs = [
    'vendor',
    'node_modules',
    'storage',
    'bootstrap/cache'
];

$fileExtensions = [
    'blade.php',
    'php',
    'js',
    'vue',
    'ts'
];

// Results storage
$results = [
    'summary' => [
        'total_files_scanned' => 0,
        'files_with_hardcoded_text' => 0,
        'total_hardcoded_strings' => 0,
        'already_localized_strings' => 0
    ],
    'files' => []
];

/**
 * Scan a file for hardcoded strings
 */
function scanFile(string $filePath, string $baseDir): array
{
    $content = file_get_contents($filePath);
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $relativePath = str_replace($baseDir . '/', '', $filePath);
    
    $hardcodedStrings = [];
    $localizedStrings = [];
    
    // Skip if file is too large (> 1MB)
    if (strlen($content) > 1048576) {
        return ['hardcoded' => [], 'localized' => [], 'skipped' => true];
    }
    
    // Detect already localized strings
    preg_match_all('/__\([\'"]([^\'"]+)[\'"]\)/', $content, $langMatches);
    preg_match_all('/@lang\([\'"]([^\'"]+)[\'"]\)/', $content, $bladeMatches);
    preg_match_all('/\$t\([\'"]([^\'"]+)[\'"]\)/', $content, $jsMatches);
    
    $localizedStrings = array_merge(
        $langMatches[1] ?? [],
        $bladeMatches[1] ?? [],
        $jsMatches[1] ?? []
    );
    
    // Detect hardcoded strings in Blade templates
    if (str_ends_with($filePath, '.blade.php')) {
        // Look for text between HTML tags (simplified)
        preg_match_all('/>([^<{]+?)</s', $content, $tagMatches);
        foreach ($tagMatches[1] as $text) {
            $text = trim($text);
            if (strlen($text) > 2 && !preg_match('/^[\d\s\-\.,;:]+$/', $text)) {
                // Skip variable references
                if (!str_contains($text, '$') && !str_contains($text, '{')) {
                    $hardcodedStrings[] = $text;
                }
            }
        }
        
        // Look for quoted strings in attributes
        preg_match_all('/(?:title|placeholder|alt|aria-label)=[\'"]([^\'"]+)[\'"]/', $content, $attrMatches);
        foreach ($attrMatches[1] as $text) {
            $text = trim($text);
            if (strlen($text) > 2 && !str_contains($text, '$') && !str_contains($text, '{')) {
                $hardcodedStrings[] = $text;
            }
        }
        
        // Bilingual format detection: "Text / Translation"
        preg_match_all('/([A-Za-z][^\/\n]+)\s*\/\s*([A-Za-z][^\n]+)/', $content, $bilingualMatches);
        foreach ($bilingualMatches[0] as $text) {
            $hardcodedStrings[] = $text;
        }
    }
    
    // Detect hardcoded strings in PHP files
    if (str_ends_with($filePath, '.php') && !str_ends_with($filePath, '.blade.php')) {
        // Look for string literals that might be user-facing
        preg_match_all('/[\'"]([A-Z][a-zA-Z\s]{10,})[\'"]/', $content, $phpMatches);
        foreach ($phpMatches[1] as $text) {
            // Skip if it's likely a class name or constant
            if (!preg_match('/^[A-Z][a-z]+([A-Z][a-z]+)+$/', $text)) {
                $hardcodedStrings[] = $text;
            }
        }
    }
    
    // Detect hardcoded strings in JavaScript files
    if (in_array($extension, ['js', 'ts', 'vue'])) {
        // Look for string literals
        preg_match_all('/[\'"`]([A-Z][a-zA-Z\s]{10,})[\'"`]/', $content, $jsStringMatches);
        $hardcodedStrings = array_merge($hardcodedStrings, $jsStringMatches[1]);
    }
    
    // Filter and deduplicate
    $hardcodedStrings = array_unique($hardcodedStrings);
    $hardcodedStrings = array_filter($hardcodedStrings, function($str) {
        // Remove very short strings or numbers
        return strlen($str) > 3 && !preg_match('/^\d+$/', $str);
    });
    
    return [
        'hardcoded' => array_values($hardcodedStrings),
        'localized' => array_unique($localizedStrings),
        'skipped' => false
    ];
}

/**
 * Recursively scan directories
 */
function scanDirectory(string $dir, array $excludeDirs, array $extensions, string $baseDir, array &$results): void
{
    if (!is_dir($dir)) {
        return;
    }
    
    $items = scandir($dir);
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $path = $dir . '/' . $item;
        
        // Skip excluded directories
        $shouldExclude = false;
        foreach ($excludeDirs as $excludeDir) {
            if (str_contains($path, '/' . $excludeDir . '/') || str_ends_with($path, '/' . $excludeDir)) {
                $shouldExclude = true;
                break;
            }
        }
        
        if ($shouldExclude) {
            continue;
        }
        
        if (is_dir($path)) {
            scanDirectory($path, $excludeDirs, $extensions, $baseDir, $results);
        } elseif (is_file($path)) {
            // Check if file matches extension patterns
            $matches = false;
            foreach ($extensions as $ext) {
                if (str_ends_with($path, '.' . $ext)) {
                    $matches = true;
                    break;
                }
            }
            
            if ($matches) {
                $results['summary']['total_files_scanned']++;
                
                $scanResult = scanFile($path, $baseDir);
                
                if (!$scanResult['skipped']) {
                    $relativePath = str_replace($baseDir . '/', '', $path);
                    
                    if (!empty($scanResult['hardcoded'])) {
                        $results['summary']['files_with_hardcoded_text']++;
                        $results['summary']['total_hardcoded_strings'] += count($scanResult['hardcoded']);
                    }
                    
                    $results['summary']['already_localized_strings'] += count($scanResult['localized']);
                    
                    // Store file results if there are hardcoded strings
                    if (!empty($scanResult['hardcoded']) || !empty($scanResult['localized'])) {
                        $results['files'][$relativePath] = $scanResult;
                    }
                }
            }
        }
    }
}

// Main execution
echo "=== Hardcoded String Scanner ===\n";
echo "Base Directory: $baseDir\n";
echo "Scanning directories: " . implode(', ', $scanDirs) . "\n\n";

foreach ($scanDirs as $scanDir) {
    $fullPath = $baseDir . '/' . $scanDir;
    echo "Scanning: $scanDir ... ";
    scanDirectory($fullPath, $excludeDirs, $fileExtensions, $baseDir, $results);
    echo "Done\n";
}

// Output results
echo "\n=== SCAN RESULTS ===\n";
echo "Total Files Scanned: " . $results['summary']['total_files_scanned'] . "\n";
echo "Files with Hardcoded Text: " . $results['summary']['files_with_hardcoded_text'] . "\n";
echo "Total Hardcoded Strings: " . $results['summary']['total_hardcoded_strings'] . "\n";
echo "Already Localized Strings: " . $results['summary']['already_localized_strings'] . "\n";

// Show top 20 files with most hardcoded strings
echo "\n=== TOP 20 FILES WITH HARDCODED TEXT ===\n";
uasort($results['files'], function($a, $b) {
    return count($b['hardcoded']) - count($a['hardcoded']);
});

$count = 0;
foreach ($results['files'] as $file => $data) {
    if (!empty($data['hardcoded']) && $count < 20) {
        echo "\n$file (" . count($data['hardcoded']) . " strings):\n";
        foreach (array_slice($data['hardcoded'], 0, 5) as $str) {
            $preview = strlen($str) > 60 ? substr($str, 0, 60) . '...' : $str;
            echo "  - " . $preview . "\n";
        }
        if (count($data['hardcoded']) > 5) {
            echo "  ... and " . (count($data['hardcoded']) - 5) . " more\n";
        }
        $count++;
    }
}

// Save detailed results to JSON
$outputFile = $baseDir . '/localization-scan-results.json';
file_put_contents($outputFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\n\nDetailed results saved to: localization-scan-results.json\n";

// Generate summary report
$reportFile = $baseDir . '/LOCALIZATION_SCAN_REPORT.md';
$report = "# Localization Scan Report\n\n";
$report .= "**Generated:** " . date('Y-m-d H:i:s') . "\n\n";
$report .= "## Summary\n\n";
$report .= "- **Total Files Scanned:** " . $results['summary']['total_files_scanned'] . "\n";
$report .= "- **Files with Hardcoded Text:** " . $results['summary']['files_with_hardcoded_text'] . "\n";
$report .= "- **Total Hardcoded Strings:** " . $results['summary']['total_hardcoded_strings'] . "\n";
$report .= "- **Already Localized Strings:** " . $results['summary']['already_localized_strings'] . "\n\n";

$report .= "## Localization Progress\n\n";
$totalStrings = $results['summary']['total_hardcoded_strings'] + $results['summary']['already_localized_strings'];
if ($totalStrings > 0) {
    $percentage = round(($results['summary']['already_localized_strings'] / $totalStrings) * 100, 1);
    $report .= "**Current Progress:** $percentage% localized\n\n";
}

$report .= "## Files Requiring Localization\n\n";
$count = 0;
foreach ($results['files'] as $file => $data) {
    if (!empty($data['hardcoded'])) {
        $report .= "### $file\n";
        $report .= "**Hardcoded Strings:** " . count($data['hardcoded']) . "\n\n";
        $count++;
        if ($count >= 30) {
            $report .= "\n... and " . ($results['summary']['files_with_hardcoded_text'] - 30) . " more files\n";
            break;
        }
    }
}

file_put_contents($reportFile, $report);
echo "Summary report saved to: LOCALIZATION_SCAN_REPORT.md\n";
