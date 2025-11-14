<?php
declare(strict_types=1);

// Quick script: fix some markdownlint issues automatically in repo docs
// Safe/limited fixes: MD012 (multiple blank lines), MD031 (blank lines around fenced code), MD032 (blank lines around lists)

$dirs = array_slice($argv, 1);
$dryRun = true;
if (in_array('--apply', $argv, true)) {
    $dryRun = false;
    // remove --apply from dirs
    $dirs = array_filter($dirs, function ($v) {
        return $v !== '--apply';
    });
}

if (empty($dirs)) {
    // default: docs and _reference
    $dirs = ['docs', '_reference', 'tests', 'resources', 'tests/e2e'];
}

$fileCount = 0;
$changed = [];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(getcwd()));
foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }
    $path = $file->getRealPath();
    if (!preg_match('/\\.md$/i', $path)) {
        continue;
    }
    $ok = false;
    // Exclude vendor and node_modules content
    if (stripos($path, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false) {
        continue;
    }
    if (stripos($path, DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR) !== false) {
        continue;
    }

    foreach ($dirs as $dir) {
        // Only match top-level directories under repo root (e.g., docs/*, _reference/*)
        $search = DIRECTORY_SEPARATOR . trim($dir, "\/\\") . DIRECTORY_SEPARATOR;
        if (stripos($path, $search) !== false) {
            $ok = true;
            break;
        }
    }
    if (!$ok) {
        continue; // skip files not in target dirs
    }

    $text = file_get_contents($path);
    $original = $text;

    // 1) MD012: replace 3+ blank lines -> 2 newlines
    $text = preg_replace('/(\r?\n){3,}/', "\n\n", $text);

    // 2) MD031: ensure fenced code block is surrounded by blank lines
    // Add blank line before opening fence if not present
    $text = preg_replace_callback('/(?<!\n)\n?^(```[^\n]*\n)/m', function ($m) {
        return "\n" . $m[1];
    }, $text);
    // Add blank line after closing fence if not present
    $text = preg_replace_callback('/(\n```[^\n]*\n)(?!\n)/m', function ($m) {
        return $m[1] . "\n";
    }, $text);

    // 3) MD032: lists should be surrounded by blank lines
    // We'll add blank line before first list item block and after last list item block
    $lines = preg_split('/\r?\n/', $text);
    $out = [];
    $inList = false;
    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];
        $isList = preg_match('/^\s*([-\*\+]\s|\d+\.?\s)/', $line);
        if ($isList && !$inList) {
            // Start of list block
            // If previous line not blank and previous exists -- insert blank line
            if (!empty($out) && trim(end($out)) !== '') {
                $out[] = '';
            }
            $inList = true;
        }
        $out[] = $line;
        if (!$isList && $inList) {
            // just left list block
            // Ensure next line is blank - add blank line if next line exists and is not blank
            if (isset($lines[$i + 1]) && trim($lines[$i + 1]) !== '') {
                $out[] = '';
            }
            $inList = false;
        }
    }

    $text = implode("\n", $out);

    if ($text !== $original) {
        $fileCount++;
        $changed[] = $path;
        if (!$dryRun) {
            file_put_contents($path, $text);
        }
    }
}

echo "Dry-run: " . ($dryRun ? 'true' : 'false') . "\n";
echo "Files to change: {$fileCount}\n";
foreach ($changed as $p) echo " - $p\n";
