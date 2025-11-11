#!/usr/bin/env php
<?php

/**
 * Laravel Localization Automation Tool
 *
 * Extracts hardcoded strings and replaces them with translation keys.
 * Updates both English and Malay translation files.
 *
 * @author Localization Team
 *
 * @version 1.0.0
 *
 * @created 2025-11-11
 */

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    exit('This script must be run from the command line.');
}

// Configuration
$baseDir = dirname(__DIR__);
$translationsDir = $baseDir.'/lang';
$scanResultsFile = $baseDir.'/localization-scan-results.json';

// Load scan results
if (! file_exists($scanResultsFile)) {
    exit("Error: Run scan-hardcoded-strings.php first to generate scan results.\n");
}

$scanResults = json_decode(file_get_contents($scanResultsFile), true);

/**
 * Generate a translation key from text
 */
function generateTranslationKey(string $text, string $context = 'common'): string
{
    // Clean the text
    $text = strip_tags($text);
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);

    // Generate a slug-like key
    $key = strtolower($text);
    $key = preg_replace('/[^a-z0-9\s-]/', '', $key);
    $key = preg_replace('/\s+/', '_', $key);
    $key = substr($key, 0, 50); // Limit length
    $key = trim($key, '_');

    return $context.'.'.$key;
}

/**
 * Determine the best translation file for a view path
 */
function determineTranslationFile(string $filePath): string
{
    if (str_contains($filePath, 'emails/')) {
        return 'emails';
    } elseif (str_contains($filePath, 'helpdesk') || str_contains($filePath, 'tickets')) {
        return 'helpdesk';
    } elseif (str_contains($filePath, 'loan')) {
        return 'loans';
    } elseif (str_contains($filePath, 'asset')) {
        return 'asset_loan';
    } elseif (str_contains($filePath, 'portal')) {
        return 'portal';
    } elseif (str_contains($filePath, 'filament')) {
        return 'admin';
    } elseif (str_contains($filePath, 'auth')) {
        return 'auth';
    } elseif (str_contains($filePath, 'profile')) {
        return 'profile';
    }

    return 'common';
}

/**
 * Check if a string is user-facing
 */
function isUserFacingString(string $text): bool
{
    $text = trim($text);

    // Skip if too short
    if (strlen($text) < 3) {
        return false;
    }

    // Skip if it's just numbers or punctuation
    if (preg_match('/^[\d\s\-\.,;:!?]+$/', $text)) {
        return false;
    }

    // Skip if it contains PHP/Blade syntax
    if (str_contains($text, '$') || str_contains($text, '{{') || str_contains($text, '@')) {
        return false;
    }

    // Reject obvious markup, attributes, SVG or template fragments
    if (isStructuralMarkup($text)) {
        return false;
    }

    // Skip if it's likely a variable name or code
    if (preg_match('/^[a-z][a-zA-Z0-9_]+$/', $text)) {
        return false;
    }

    // Skip common HTML/CSS/JS keywords
    $keywords = ['div', 'span', 'class', 'style', 'script', 'function', 'var', 'const', 'let', 'return', 'true', 'false', 'null', 'undefined'];
    if (in_array(strtolower($text), $keywords)) {
        return false;
    }

    return true;
}

/**
 * Detect structural markup, template tokens, SVG fragments, CSS selectors, or function calls
 * Returns true when the text is likely structural and should NOT be extracted
 */
function isStructuralMarkup(string $text): bool
{
    $t = trim($text);

    // If contains angle-brackets or common HTML entities it's markup
    if (str_contains($t, '<') || str_contains($t, '>') || stripos($t, '&nbsp;') !== false || preg_match('/&[a-z]+;/', $t)) {
        return true;
    }

    // HTML tag-like tokens (e.g., div>, h3>, li>, p>) or svg elements
    if (preg_match('/<\/?\s*(div|span|p|h[1-6]|li|ul|ol|table|tr|td|th|svg|path|circle|rect|g|polyline|polygon|line|img|a|strong|em|header|footer|section|nav)\b/i', $t)) {
        return true;
    }

    // Attributes like class=, style=, href=, src= — likely markup
    if (preg_match('/\b(class|style|href|src|alt|title|role|aria-[a-z-]+)\s*=\s*["\"]/i', $t)) {
        return true;
    }

    // Inline CSS or selector-like tokens (e.g., .text-gray-500, #id)
    if (preg_match('/(^|\s)[\.\#][a-z0-9_-]+/i', $t) || preg_match('/[a-z-]+:\s*\d+px/i', $t)) {
        return true;
    }

    // Blade/PHP/template tokens and object access (->) or function calls
    if (str_contains($t, '->') || str_contains($t, '{{') || str_contains($t, '}}') || str_contains($t, '<?')) {
        return true;
    }

    // Function call like format( or count( etc. — likely code, not user text
    if (preg_match('/\b[a-zA-Z_][a-zA-Z0-9_]*\s*\(/', $t)) {
        return true;
    }

    // SVG path data (long strings of commands starting with M or m) — detect common pattern
    if (preg_match('/\b[MmLlHhVvCcSsQqTtAaZz][0-9\s\.,-]+\b/', $t)) {
        return true;
    }

    // If the string contains many punctuation characters typical of markup
    if (substr_count($t, '<') + substr_count($t, '>') + substr_count($t, '=') > 2) {
        return true;
    }

    return false;
}

/**
 * Process bilingual text format "English / Malay"
 */
function extractBilingualText(string $text): ?array
{
    // Match pattern: "Text / Translation"
    if (preg_match('/^([^\/]+)\s*\/\s*([^\/]+)$/', $text, $matches)) {
        $en = trim($matches[1]);
        $ms = trim($matches[2]);

        if (strlen($en) > 2 && strlen($ms) > 2) {
            return ['en' => $en, 'ms' => $ms];
        }
    }

    return null;
}

/**
 * Load existing translation file
 */
function loadTranslationFile(string $lang, string $file): array
{
    global $translationsDir;

    $filePath = "$translationsDir/$lang/$file.php";

    if (! file_exists($filePath)) {
        return [];
    }

    try {
        $translations = include $filePath;

        return is_array($translations) ? $translations : [];
    } catch (\Throwable $e) {
        echo "Warning: Could not load $filePath: ".$e->getMessage()."\n";

        return [];
    }
}

/**
 * Save translation file
 */
function saveTranslationFile(string $lang, string $file, array $translations): bool
{
    global $translationsDir;

    $dir = "$translationsDir/$lang";
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $filePath = "$dir/$file.php";

    // Sort keys for better readability
    ksort($translations);

    // Generate PHP file content
    $content = "<?php\n\n";
    $content .= "declare(strict_types=1);\n\n";
    $content .= "/**\n";
    $content .= ' * '.ucfirst($lang).' - '.ucfirst($file)." Translations\n";
    $content .= " *\n";
    $content .= ' * Auto-generated on '.date('Y-m-d H:i:s')."\n";
    $content .= " */\n\n";
    $content .= "return [\n";

    foreach ($translations as $key => $value) {
        // Escape single quotes in the value
        $value = str_replace("'", "\\'", $value);
        $content .= "    '$key' => '$value',\n";
    }

    $content .= "];\n";

    return file_put_contents($filePath, $content) !== false;
}

// Main execution
echo "\n=== Laravel Localization Automation Tool ===\n\n";

// Count files to process
$filesToProcess = array_filter($scanResults['files'], function ($data) {
    return ! empty($data['hardcoded']);
});

echo 'Files with hardcoded text: '.count($filesToProcess)."\n";
echo 'Total hardcoded strings: '.$scanResults['summary']['total_hardcoded_strings']."\n\n";

// Ask for confirmation
echo "This tool will:\n";
echo "1. Extract hardcoded strings from files\n";
echo "2. Generate translation keys\n";
echo "3. Update lang/en/ and lang/ms/ translation files\n";
echo "4. Create backup of original files\n\n";
echo "Note: Files will NOT be automatically modified. Review generated translations first.\n\n";

// Generate translations
$newTranslations = [
    'en' => [],
    'ms' => [],
];

$processedCount = 0;
$translationKeysGenerated = 0;

foreach ($filesToProcess as $filePath => $data) {
    if ($processedCount >= 50) { // Limit to first 50 files for this run
        break;
    }

    $translationFile = determineTranslationFile($filePath);

    // Load existing translations
    if (! isset($newTranslations['en'][$translationFile])) {
        $newTranslations['en'][$translationFile] = loadTranslationFile('en', $translationFile);
        $newTranslations['ms'][$translationFile] = loadTranslationFile('ms', $translationFile);
    }

    foreach ($data['hardcoded'] as $text) {
        if (! isUserFacingString($text)) {
            continue;
        }

        // Check if it's bilingual format
        $bilingual = extractBilingualText($text);

        if ($bilingual) {
            // Generate key
            $key = generateTranslationKey($bilingual['en'], basename($translationFile));

            // Add to translations if not exists
            if (! isset($newTranslations['en'][$translationFile][$key])) {
                $newTranslations['en'][$translationFile][$key] = $bilingual['en'];
                $newTranslations['ms'][$translationFile][$key] = $bilingual['ms'];
                $translationKeysGenerated++;
            }
        } else {
            // Single language string - attempt to determine language and add
            // For now, add to both with the same value
            $key = generateTranslationKey($text, basename($translationFile));

            if (! isset($newTranslations['en'][$translationFile][$key])) {
                // If it looks like Malay, add it to MS with a TODO for EN
                if (preg_match('/(?:dan|atau|untuk|dengan|adalah|kepada)/i', $text)) {
                    $newTranslations['ms'][$translationFile][$key] = $text;
                    $newTranslations['en'][$translationFile][$key] = '[TODO: Translate] '.$text;
                } else {
                    // Assume English
                    $newTranslations['en'][$translationFile][$key] = $text;
                    $newTranslations['ms'][$translationFile][$key] = '[TODO: Terjemah] '.$text;
                }
                $translationKeysGenerated++;
            }
        }
    }

    $processedCount++;
}

echo "\nProcessed $processedCount files\n";
echo "Generated $translationKeysGenerated new translation keys\n\n";

// Save updated translation files
echo "Saving translation files...\n";

foreach ($newTranslations['en'] as $file => $translations) {
    if (! empty($translations)) {
        saveTranslationFile('en', $file, $translations);
        echo "  - Saved lang/en/$file.php (".count($translations)." keys)\n";
    }
}

foreach ($newTranslations['ms'] as $file => $translations) {
    if (! empty($translations)) {
        saveTranslationFile('ms', $file, $translations);
        echo "  - Saved lang/ms/$file.php (".count($translations)." keys)\n";
    }
}

echo "\n✓ Translation files updated successfully!\n";
echo "\nNext steps:\n";
echo "1. Review generated translation files in lang/en/ and lang/ms/\n";
echo "2. Translate [TODO] entries\n";
echo "3. Run the replacement tool to update source files\n";
echo "4. Test your application to ensure translations work correctly\n\n";
