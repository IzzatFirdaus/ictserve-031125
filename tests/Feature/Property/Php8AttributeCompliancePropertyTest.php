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
use Tests\TestCase;

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
    #[DataProvider('testFileProvider')]
    public function test_files_use_php8_attributes(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Verify no @test annotations remain
        $this->assertStringNotContainsString(
            '@test',
            $content,
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
     * instead of @dataProvider annotations.
     */
    #[Test]
    #[DataProvider('testFileProvider')]
    public function test_files_use_data_provider_attributes(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Check if file uses data providers
        if (preg_match('/@dataProvider\s+\w+/', $content)) {
            $this->fail("File {$filePath} still uses @dataProvider annotation instead of #[DataProvider]");
        }

        // If file has #[DataProvider], verify import
        if (str_contains($content, '#[DataProvider')) {
            $this->assertStringContainsString(
                'use PHPUnit\Framework\Attributes\DataProvider;',
                $content,
                "File {$filePath} uses #[DataProvider] but missing import statement"
            );
        }
    }

    /**
     * Property 1.2: Group Attribute Compliance
     *
     * For any test file using groups, it SHALL use #[Group] attributes
     * instead of @group annotations.
     */
    #[Test]
    #[DataProvider('testFileProvider')]
    public function test_files_use_group_attributes(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Check if file uses groups
        if (preg_match('/@group\s+\w+/', $content)) {
            $this->fail("File {$filePath} still uses @group annotation instead of #[Group]");
        }

        // If file has #[Group], verify import
        if (str_contains($content, '#[Group')) {
            $this->assertStringContainsString(
                'use PHPUnit\Framework\Attributes\Group;',
                $content,
                "File {$filePath} uses #[Group] but missing import statement"
            );
        }
    }

    /**
     * Property 1.3: Strict Types Declaration
     *
     * For any test file, it SHALL include declare(strict_types=1) at the top.
     */
    #[Test]
    #[DataProvider('testFileProvider')]
    public function test_files_have_strict_types_declaration(string $filePath): void
    {
        $content = file_get_contents($filePath);

        $this->assertStringContainsString(
            'declare(strict_types=1);',
            $content,
            "File {$filePath} missing declare(strict_types=1) declaration"
        );

        // Verify it's near the top (within first 10 lines)
        $lines = explode("\n", $content);
        $found = false;
        for ($i = 0; $i < min(10, count($lines)); $i++) {
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
    public static function test_file_provider(): array
    {
        $testFiles = [];
        $basePath = dirname(__DIR__, 3); // Go up from tests/Feature/Property to project root
        $directories = [
            'tests/Feature',
            'tests/Unit',
            'tests/Browser',
        ];

        foreach ($directories as $directory) {
            $fullPath = $basePath.'/'.$directory;
            if (! is_dir($fullPath)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($fullPath)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($basePath.'/', '', $file->getPathname());

                    // Skip certain files
                    if (
                        str_contains($relativePath, 'Concerns/') ||
                        str_contains($relativePath, 'manual/') ||
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
        // Check for #[Test] attribute or test_ prefix methods
        return str_contains($content, '#[Test]') ||
            preg_match('/public function test_\w+\(/', $content) === 1;
    }
}
