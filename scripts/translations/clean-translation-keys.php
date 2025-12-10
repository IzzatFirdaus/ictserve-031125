<?php

declare(strict_types=1);

/**
 * Clean malformed translation keys from common.php files
 * Removes keys starting with 'common.' that contain code/HTML snippets
 */
$files = [
    __DIR__.'/../lang/en/common.php',
    __DIR__.'/../lang/ms/common.php',
];

foreach ($files as $file) {
    if (! file_exists($file)) {
        echo "File not found: $file\n";

        continue;
    }

    $translations = require $file;
    $cleaned = [];
    $removed = 0;

    foreach ($translations as $key => $value) {
        // Remove keys starting with 'common.' (malformed auto-generated keys)
        if (str_starts_with($key, 'common.')) {
            $removed++;

            continue;
        }
        $cleaned[$key] = $value;
    }

    // Generate clean file content
    $content = "<?php\n\ndeclare(strict_types=1);\n\n";
    $content .= "/**\n * ".basename(dirname($file))." - Common Translations\n";
    $content .= " *\n * Cleaned on ".date('Y-m-d H:i:s')."\n */\n\n";
    $content .= "return [\n";

    foreach ($cleaned as $key => $value) {
        $escapedKey = addslashes($key);
        $escapedValue = addslashes($value);
        $content .= "\t'{$escapedKey}' => '{$escapedValue}',\n";
    }

    $content .= "];\n";

    file_put_contents($file, $content);
    echo "Cleaned {$file}: removed {$removed} malformed keys, kept ".count($cleaned)." valid keys\n";
}

echo "\nDone!\n";
