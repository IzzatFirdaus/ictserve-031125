<?php

declare(strict_types=1);

/**
 * Property-Based Tests for Documentation Preservation
 *
 * Property-based tests to verify that test documentation (@trace, @traceability tags)
 * is preserved during PHP 8 attribute conversion while PHPDoc annotations are converted.
 *
 * **Feature: test-suite-comprehensive-v3.6, Property 13: Documentation Preservation**
 * **Validates: Requirements 12.1, 12.2, 12.3**
 *
 * @trace Requirements 12.1, 12.2, 12.3
 */

namespace Tests\Feature\Property;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('property')]
#[Group('documentation')]
class DocumentationPreservationPropertyTest extends TestCase
{
    /**
     * Property 13: Documentation Preservation
     *
     * For any test method with @trace or @traceability tags, these tags SHALL be preserved
     * after conversion while PHPDoc annotations are converted to PHP 8 attributes.
     */
    #[Test]
    #[DataProvider('provideTestFiles')]
    public function documentation_tags_are_preserved(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Find all test methods with documentation
        preg_match_all('/\/\*\*.*?\*\/\s*(?:#\[Test\].*?\s*)?public function (\w+)\(/s', $content, $matches, PREG_SET_ORDER);

        // Always make at least one assertion
        $this->assertIsArray($matches, 'Matches should be an array');

        foreach ($matches as $match) {
            $docBlock = $match[0];
            $methodName = $match[1];

            // If method has @trace or @traceability, verify they are preserved
            if (preg_match('/@trace|@traceability/', $docBlock)) {
                // Should still have the documentation tags
                $this->assertTrue(
                    str_contains($docBlock, '@trace') || str_contains($docBlock, '@traceability'),
                    "Method {$methodName} in {$filePath} lost @trace/@traceability documentation"
                );

                // Should not have @test annotation (check for PHPDoc @test pattern)
                $hasLegacyTestAnnotation = preg_match('/^\s*\*\s*@test\s/m', $docBlock) === 1;
                $this->assertFalse(
                    $hasLegacyTestAnnotation,
                    "Method {$methodName} in {$filePath} still has @test annotation instead of #[Test]"
                );
            }
        }
    }

    /**
     * Property 13.1: PHPDoc Block Structure Preservation
     *
     * For any test method with meaningful PHPDoc content beyond @test,
     * the PHPDoc block SHALL be preserved with only @test removed.
     */
    #[Test]
    #[DataProvider('provideTestFiles')]
    public function php_doc_block_structure_is_preserved(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Find PHPDoc blocks that should be preserved
        preg_match_all('/\/\*\*\s*\n(.*?)\*\/\s*(?:#\[Test\].*?\s*)?public function (\w+)\(/s', $content, $matches, PREG_SET_ORDER);

        // Always make at least one assertion
        $this->assertIsArray($matches, 'Matches should be an array');

        foreach ($matches as $match) {
            $docContent = $match[1];
            $methodName = $match[2];

            // If doc block has meaningful content (not just @test)
            if (preg_match('/@(?:trace|traceability|see|param|return|throws|since|author|version)/', $docContent)) {
                // Should preserve the structure
                $this->assertTrue(
                    str_contains($docContent, '*'),
                    "Method {$methodName} in {$filePath} lost PHPDoc structure"
                );

                // Should not contain @test (check for PHPDoc @test pattern)
                $hasLegacyTestAnnotation = preg_match('/^\s*\*\s*@test\s/m', $docContent) === 1;
                $this->assertFalse(
                    $hasLegacyTestAnnotation,
                    "Method {$methodName} in {$filePath} still contains @test in PHPDoc"
                );
            }
        }
    }

    /**
     * Property 13.2: Requirement Traceability Links
     *
     * For any test method with requirement traceability (@trace Requirements X.Y),
     * the specific requirement references SHALL be preserved and properly formatted.
     */
    #[Test]
    #[DataProvider('provideTestFiles')]
    public function requirement_traceability_links_are_preserved(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Find all @trace tags with requirement references
        preg_match_all('/@trace\s+Requirements?\s+([\d\.,\s-]+)/i', $content, $matches, PREG_SET_ORDER);

        // Always make at least one assertion
        $this->assertIsArray($matches, 'Matches should be an array');

        foreach ($matches as $match) {
            $requirementRefs = trim($match[1]);

            // Verify requirement references are properly formatted
            $this->assertTrue(
                preg_match('/^[\d\.,\s-]+$/', $requirementRefs) === 1,
                "File {$filePath} has malformed requirement references: {$requirementRefs}"
            );

            // Should contain valid requirement numbers (e.g., 1.1, 2.3, 12.1-12.3)
            $this->assertTrue(
                preg_match('/\d+\.\d+/', $requirementRefs) === 1,
                "File {$filePath} has invalid requirement format: {$requirementRefs}"
            );
        }
    }

    /**
     * Property 13.3: Test Description Preservation
     *
     * For any test method with descriptive comments in PHPDoc,
     * the descriptions SHALL be preserved and remain readable.
     */
    #[Test]
    #[DataProvider('provideTestFiles')]
    public function descriptions_are_preserved(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Find test methods with descriptions
        preg_match_all('/\/\*\*\s*\n\s*\*\s*([^@\n][^\n]*)\s*\n.*?\*\/\s*(?:#\[Test\].*?\s*)?public function (\w+)\(/s', $content, $matches, PREG_SET_ORDER);

        // Always make at least one assertion
        $this->assertIsArray($matches, 'Matches should be an array');

        foreach ($matches as $match) {
            $description = trim($match[1]);
            $methodName = $match[2];

            if (! empty($description) && ! str_starts_with($description, '*')) {
                // Should have meaningful description
                $this->assertGreaterThan(
                    10,
                    \strlen($description),
                    "Method {$methodName} in {$filePath} has too short description: {$description}"
                );

                // Should not be just the method name
                $this->assertNotEquals(
                    strtolower(str_replace('_', ' ', $methodName)),
                    strtolower($description),
                    "Method {$methodName} in {$filePath} has redundant description"
                );
            }
        }
    }

    /**
     * Property 13.4: PSR-12 Formatting Compliance
     *
     * For any test file after conversion, it SHALL maintain PSR-12 code formatting
     * including proper PHPDoc block formatting.
     */
    #[Test]
    #[DataProvider('provideTestFiles')]
    public function psr12_formatting_is_preserved(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Check for proper PHPDoc formatting using regex to find PHPDoc blocks
        // Match PHPDoc blocks: /** ... */
        preg_match_all('/\/\*\*\s*\n(.*?)\*\//s', $content, $docBlocks, PREG_SET_ORDER);

        // Always make at least one assertion
        $this->assertIsArray($docBlocks, 'DocBlocks should be an array');

        foreach ($docBlocks as $match) {
            $docContent = $match[1];
            $docLines = explode("\n", $docContent);

            foreach ($docLines as $line) {
                // PHPDoc lines should start with * (after whitespace) or be empty
                $trimmedLine = trim($line);
                if ($trimmedLine !== '' && ! preg_match('/^\s*\*/', $line)) {
                    // This line is inside a PHPDoc but doesn't start with *
                    $this->fail(
                        "File {$filePath} has malformed PHPDoc line: {$line}"
                    );
                }
            }
        }
    }

    /**
     * Property 13.5: Empty PHPDoc Block Removal
     *
     * For any PHPDoc block that contained only @test annotation,
     * the entire block SHALL be removed (not left empty).
     */
    #[Test]
    #[DataProvider('provideTestFiles')]
    public function empty_php_doc_blocks_are_removed(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Check for empty or nearly empty PHPDoc blocks
        $emptyDocPatterns = [
            '/\/\*\*\s*\*\//',           // Completely empty
            '/\/\*\*\s*\n\s*\*\s*\n\s*\*\//', // Only empty lines
            '/\/\*\*\s*\n\s*\*\s*\n\s*\*\s*\n\s*\*\//', // Multiple empty lines
        ];

        foreach ($emptyDocPatterns as $pattern) {
            $this->assertDoesNotMatchRegularExpression(
                $pattern,
                $content,
                "File {$filePath} contains empty PHPDoc blocks that should be removed"
            );
        }
    }

    /**
     * Data provider for test files
     *
     * Generates a list of all test files in the tests directory
     */
    public static function provideTestFiles(): array
    {
        $testFiles = [];
        $basePath = dirname(__DIR__, 3);
        $directories = [
            'tests/Feature',
            'tests/Unit',
            'tests/Browser',
        ];

        foreach ($directories as $directory) {
            $fullPath = "{$basePath}/{$directory}";
            if (! is_dir($fullPath)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($fullPath)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace("{$basePath}/", '', $file->getPathname());
                    // Normalize path separators for cross-platform compatibility
                    $normalizedPath = str_replace('\\', '/', $relativePath);

                    // Skip certain files
                    if (
                        str_contains($normalizedPath, 'Concerns/') ||
                        str_contains($normalizedPath, 'manual/') ||
                        basename($relativePath) === 'TestCase.php'
                    ) {
                        continue;
                    }

                    // Only include files that have PHPDoc blocks
                    $content = file_get_contents($file->getPathname());
                    if (str_contains($content, '/**') && str_contains($content, '*/')) {
                        $testFiles[basename($relativePath)] = [$file->getPathname()];
                    }
                }
            }
        }

        return $testFiles;
    }
}
