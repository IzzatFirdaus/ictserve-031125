<?php
declare(strict_types=1);

// Fix markdown lint rules that are safe to automate
// - MD012: collapse multiple blank lines to single blank line
// - MD007: normalize unordered list indentation (convert 4-space to 2-space indent)
// - MD047: ensure files end with single newline

$opts = getopt('', ['apply', 'help']);
$apply = isset($opts['apply']);

if (isset($opts['help'])) {
    echo "Usage: php scripts/fix-markdown-lint-rules.php [--apply]\n";
    echo "--apply   Apply changes. Omit to run a dry-run and list files that would change.\n";
    exit(0);
}

$cwd = getcwd();
$iterator = new RecursiveIteratorIterator(
    new RecursiveCallbackFilterIterator(
        new RecursiveDirectoryIterator($cwd, FilesystemIterator::SKIP_DOTS),
        function ($current, $key, $iterator) {
            // Exclude vendor, node_modules, and .git
            $path = $current->getPathname();
            if (stripos($path, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false) {
                return false;
            }
            if (stripos($path, DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR) !== false) {
                return false;
            }
            if (stripos($path, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR) !== false) {
                return false;
            }
            return true;
        }
    )
);

$changedFiles = [];
foreach ($iterator as $file) {
    if (! $file->isFile()) {
        continue;
    }
    if (strtolower($file->getExtension()) !== 'md') {
        continue;
    }

    $original = file_get_contents($file->getPathname());
    $lines = preg_split("/\R/u", $original);
    if (!is_array($lines)) {
        $lines = [];
    }

    $inFence = false;
    $lastWasBlank = false;
    $out = [];
    foreach ($lines as $line) {
        // Detect fenced code block start/end
        if (preg_match('/^```/', $line)) {
            $inFence = ! $inFence;
            $out[] = $line;
            $lastWasBlank = false;
            continue;
        }

        // If we are in a fence, leave lines unchanged
        if ($inFence) {
            $out[] = $line;
            $lastWasBlank = false;
            continue;
        }

        // MD012: reduce multiple blanks to single blank line
        if (trim($line) === '') {
            if ($lastWasBlank) {
                // skip this blank
                continue;
            }
            $out[] = '';
            $lastWasBlank = true;
            continue;
        }

        // MD007: normalize unordered list indentation where safe
        // Convert 4-space list indentation to 2-space indentation
        if (preg_match('/^ {4}[-*+] /', $line)) {
            $line = preg_replace('/^ {4}/', '  ', $line);
        }

        $out[] = $line;
        $lastWasBlank = false;
    }

    // MD047: Ensure single trailing newline
    // Rebuild file text
    $newText = implode("\n", $out);
    if ($newText === '') {
        $newText = "\n";
    } else {
        $newText = rtrim($newText, "\n") . "\n";
    }

    if ($newText !== $original) {
        $changedFiles[] = $file->getPathname();
        if ($apply) {
            file_put_contents($file->getPathname(), $newText);
            echo "Applied: " . $file->getPathname() . PHP_EOL;
        } else {
            echo "Would change: " . $file->getPathname() . PHP_EOL;
        }
    }
}

echo sprintf("\nTotal files: %d\n", iterator_count($iterator));
echo sprintf("Files changed: %d\n", count($changedFiles));

if (! $apply) {
    echo "(Dry-run) Use --apply to write the changes.\n";
}

exit(0);
