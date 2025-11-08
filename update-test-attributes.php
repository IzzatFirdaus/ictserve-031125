<?php

declare(strict_types=1);

/**
 * Script to update PHPUnit test files from doc-comment metadata to attributes
 * For PHPUnit 12 compatibility
 */
echo "Updating test files to use PHPUnit 12 attributes...\n\n";

// Find all test files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__.'/tests')
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
    $hasTestImport = str_contains($content, 'use PHPUnit\Framework\Attributes\Test;');

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

    // Replace multi-line doc-comment with @test
    $content = preg_replace('/^\s*\/\*\*\s*\n\s*\*\s*@test\s*\n\s*\*\/\s*\n/m', "    #[Test]\n", $content);

    // Replace /* @test */ style
    $content = preg_replace('/^\s*\/\*\s*@test\s*\*\//m', '    #[Test]', $content);

    // Replace @test within doc-comments (keeping other doc-comment content)
    // This handles cases like:
    // /**
    //  * Description
    //  * @test
    //  * @traceability ...
    //  */
    $content = preg_replace_callback(
        '/^(\s*)(\/\*\*\s*\n(?:\s*\*.*\n)*?)(\s*\*\s*@test\s*\n)((?:\s*\*.*\n)*\s*\*\/\s*\n)(\s*)(public function)/m',
        function ($matches) {
            $indent = $matches[1];
            $beforeTest = $matches[2];
            $afterTest = $matches[4];
            $methodIndent = $matches[6];
            $methodDecl = $matches[7];

            // Add #[Test] attribute before the method
            return $beforeTest.$afterTest.$indent."#[Test]\n".$indent.$methodDecl;
        },
        $content
    );

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
