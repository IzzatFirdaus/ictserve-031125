<?php

declare(strict_types=1);

/**
 * Script to update PHPUnit test files from doc-comment metadata to attributes
 * For PHPUnit 12 compatibility - VERSION 2
 */
echo "Updating test files to use PHPUnit 12 attributes (v2)...\n\n";

// Find all test files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__.'/../tests')
);

$testFiles = [];
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), 'Test.php')) {
        $testFiles[] = $file->getPathname();
    }
}

echo 'Found '.count($testFiles)." test files to process...\n\n";

$updatedFiles = 0;
$skippedFiles = 0;

foreach ($testFiles as $filePath) {
    echo "Processing: $filePath\n";

    $content = file_get_contents($filePath);
    $originalContent = $content;

    // Check if file has @test annotations
    if (! preg_match('/@test/', $content)) {
        echo "  ✓ No @test annotations found, skipping...\n";
        $skippedFiles++;

        continue;
    }

    // Check if file already has the Test attribute import
    $hasTestImport = str_contains($content, 'use PHPUnit\\Framework\\Attributes\\Test;');

    // Add the Test attribute import if not present
    if (! $hasTestImport) {
        // Find the last use statement before the class declaration
        $pattern = '/(.*use [^;]+;)(\s*)((?:\/\*\*[\s\S]*?\*\/)?\s*(?:final\s+)?class)/s';
        if (preg_match($pattern, $content, $matches)) {
            $beforeLastUse = $matches[1];
            $whitespace = $matches[2];
            $classDeclaration = $matches[3];

            $content = $beforeLastUse."\nuse PHPUnit\\Framework\\Attributes\\Test;".$whitespace.$classDeclaration;
            $content .= substr($originalContent, strlen($matches[0]));

            echo "  ✓ Added Test attribute import\n";
        }
    }

    // Replace /** @test */ patterns (inline single line)
    $content = preg_replace('/\s*\/\*\*\s*@test\s*\*\//', "\n    #[Test]", $content);

    // Replace /* @test */ style
    $content = preg_replace('/^\s*\/\*\s*@test\s*\*\//m', '    #[Test]', $content);

    // Replace @test within doc-comments - simpler approach using line-by-line processing
    $lines = explode("\n", $content);
    $output = [];
    $inDocComment = false;
    $docCommentLines = [];
    $docCommentIndent = '';

    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];

        // Check if we're starting a doc comment
        if (preg_match('/^(\s*)\/\*\*/', $line, $matches)) {
            $inDocComment = true;
            $docCommentIndent = $matches[1];
            $docCommentLines = [$line];

            continue;
        }

        // If we're in a doc comment
        if ($inDocComment) {
            $docCommentLines[] = $line;

            // Check if this ends the doc comment
            if (preg_match('/\*\//', $line)) {
                // Check if any line contains @test
                $hasTest = false;
                $testLineIndex = -1;
                foreach ($docCommentLines as $idx => $docLine) {
                    if (preg_match('/@test\s*$/', $docLine)) {
                        $hasTest = true;
                        $testLineIndex = $idx;
                        break;
                    }
                }

                if ($hasTest) {
                    // Remove the @test line
                    unset($docCommentLines[$testLineIndex]);

                    // Add the doc comment (without @test line)
                    foreach ($docCommentLines as $docLine) {
                        $output[] = $docLine;
                    }

                    // Add #[Test] attribute before the method
                    $output[] = $docCommentIndent.'#[Test]';
                } else {
                    // No @test, add doc comment as-is
                    foreach ($docCommentLines as $docLine) {
                        $output[] = $docLine;
                    }
                }

                $inDocComment = false;
                $docCommentLines = [];

                continue;
            }

            continue;
        }

        // Not in doc comment, add line as-is
        $output[] = $line;
    }

    $content = implode("\n", $output);

    // Save if changed
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        echo "  ✓ Updated successfully!\n";
        $updatedFiles++;
    } else {
        echo "  ✓ No changes needed\n";
        $skippedFiles++;
    }
}

echo "\n========================================\n";
echo "Migration Complete!\n";
echo 'Total files processed: '.count($testFiles)."\n";
echo "Files updated: $updatedFiles\n";
echo "Files skipped: $skippedFiles\n";
echo "========================================\n";
