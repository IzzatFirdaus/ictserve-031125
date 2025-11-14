<?php

declare(strict_types=1);

$files = glob(__DIR__ . '/**/*.md');
// glob won't be recursive in Windows with **; use RecursiveIterator
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/..'));
$mdFiles = [];
foreach ($rii as $file) {
    if ($file->isFile() && strtolower($file->getExtension()) === 'md') {
        $mdFiles[] = $file->getPathname();
    }
}

$fixedCount = 0;
$changedFiles = [];
foreach ($mdFiles as $path) {
    $orig = file_get_contents($path);
    // remove trailing spaces at EOL for each line
    $new = preg_replace('/[ \t]+\r?\n/u', "\n", $orig);
    if ($new !== $orig) {
        file_put_contents($path, $new);
        $fixedCount++;
        $changedFiles[] = $path;
    }
}

echo "Fixed trailing spaces in {$fixedCount} files\n";
if ($fixedCount > 0) {
    echo "Files updated:\n";
    foreach ($changedFiles as $f) echo " - $f\n";
}
