<?php

declare(strict_types=1);

/**
 * Property-Based Tests for PHP 8 Attribute Compliance
 *
 * Property-based tests to verify that all test files use PHP 8 #[Test] attributes
 * instead of legacy @test PHPDoc annotations, and include proper imports.
 *
 * **Feature: test-suite-comprehensive-v3.6, Property 1: PHP 8 Attribute Compliance**
 * **Validates: Requirements 1.1, 1.4, 2.1-2.4**
 *
 * @trace Requirements 1.1, 1.4, 2.1-2.4
 */

namespace Tests\Feature\Property;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('property')]
#[Group('php8-attributes')]
class Php8AttributeCompliancePropertyTest extends TestCase
{
    /**
     * Property 1: PHP 8 Attribute Compliance
     *
     * For any test file after conversion, all test methods SHALL use #[Test] attributes
     * and the file SHALL contain proper PHPUnit attribute imports.
     */
    #[Test]
    #[DataProvider('provideTestFiles')]
    public function files_use_php8_attributes(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Verify no @test annotations remain (check for PHPDoc @test pattern)
        $hasLegacyTestAnnotation = preg_match('/^\s*\*\s*@test\s/m', $content) === 1;
        $this->assertFalse(
            $hasLegacyTestAnnotation,
            "File {$filePath} still contains @test annotation"
        );

        // Verify #[Test] attribute is used if file contains test methods
        if ($this->containsTestMethods($content)) {
            $this->assertStringContainsString(
                '#[Test]',
                $content,
                "File {$filePath} contains test methods but no #[Test] attributes"
            );

            // Verify proper imports
            $this->assertStringContainsString(
                'use PHPUnit\Framework\Attributes\Test;',
                $content,
                "File {$filePath} uses #[Test] but missing import statement"
            );
        }
    }

    /**
     * Property 1.1: DataProvider Attribute Compliance
     *
     * For any test file using data providers, it SHALL use #[DataProvider] attributes
     * instead of legacy PHPDoc dataProvider annotations.
     */
    #[Test]
    #[DataProvider('provideTestFiles')]
    public function files_use_data_provider_attributes(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Check if file uses legacy PHPDoc @dataProvider annotation (must be in a PHPDoc block line starting with *)
        $hasLegacyDataProvider = preg_match('/^\s*\*\s*@dataProvider\s+\w+/m', $content) === 1;
        $this->assertFalse(
            $hasLegacyDataProvider,
            "File {$filePath} still uses @dataProvider annotation instead of #[DataProvider]"
        );

        // If file has #[DataProvider], verify import
        if (str_contains($content, '#[DataProvider')) {
            $this->assertStringContainsString(
                'use PHPUnit\Framework\Attributes\DataProvider;',
                $content,
                "File {$filePath} uses #[DataProvider] but missing import statement"
            );
        } else {
            // File doesn't use DataProvider - that's valid
            $this->assertTrue(true, "File {$filePath} does not use DataProvider attributes");
        }
    }

    /**
     * Property 1.2: Group Attribute Compliance
     *
     * For any test file using groups, it SHALL use #[Group] attributes
     * instead of legacy PHPDoc group annotations.
     */
    #[Test]
    #[DataProvider('provideTestFiles')]
    public function files_use_group_attributes(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Check if file uses legacy PHPDoc @group annotation (must be in a PHPDoc block line starting with *)
        $hasLegacyGroup = preg_match('/^\s*\*\s*@group\s+\w+/m', $content) === 1;
        $this->assertFalse(
            $hasLegacyGroup,
            "File {$filePath} still uses @group annotation instead of #[Group]"
        );

        // If file has #[Group], verify import
        if (str_contains($content, '#[Group')) {
            $this->assertStringContainsString(
                'use PHPUnit\Framework\Attributes\Group;',
                $content,
                "File {$filePath} uses #[Group] but missing import statement"
            );
        } else {
            // File doesn't use Group - that's valid
            $this->assertTrue(true, "File {$filePath} does not use Group attributes");
        }
    }

    /**
     * Property 1.3: Strict Types Declaration
     *
     * For any test file, it SHALL include declare(strict_types=1) at the top.
     */
    #[Test]
    #[DataProvider('provideTestFiles')]
    public function files_have_strict_types_declaration(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        $this->assertStringContainsString(
            'declare(strict_types=1);',
            $content,
            "File {$filePath} missing declare(strict_types=1) declaration"
        );

        // Verify it's near the top (within first 10 lines)
        $lines = explode("\n", $content);
        $found = false;
        $maxLines = min(10, \count($lines));
        for ($i = 0; $i < $maxLines; $i++) {
            if (str_contains($lines[$i], 'declare(strict_types=1)')) {
                $found = true;
                break;
            }
        }

        $this->assertTrue(
            $found,
            "File {$filePath} has declare(strict_types=1) but not near the top"
        );
    }

    /**
     * Data provider for test files
     *
     * Generates a list of all test files in the tests directory
     */
    public static function provideTestFiles(): array
    {
        $testFiles = [];
        $basePath = dirname(__DIR__, 3); // Go up from tests/Feature/Property to project root
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

                    $testFiles[basename($relativePath)] = [$file->getPathname()];
                }
            }
        }

        return $testFiles;
    }

    /**
     * Helper method to check if file contains test methods
     */
    private function containsTestMethods(string $content): bool
    {
        // Check for #[Test] attribute or test prefix methods (both test_ and testMethodName)
        return str_contains($content, '#[Test]') ||
            preg_match('/public function test_\w+\(/', $content) === 1 ||
            preg_match('/public function test[A-Z]\w*\(/', $content) === 1;
    }
}
