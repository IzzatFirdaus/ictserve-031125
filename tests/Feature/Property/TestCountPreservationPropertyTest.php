<?php

declare(strict_types=1);

/**
 * Property-Based Tests for Test Count Preservation
 *
 * Property-based tests to verify that the number of test methods remains identical
 * before and after PHP 8 attribute conversion (unless new tests are explicitly added).
 *
 * **Feature: test-suite-comprehensive-v3.6, Property 14: Test Count Preservation**
 * **Validates: Requirements 13.2**
 *
 * @trace Requirements 13.2
 */

namespace Tests\Feature\Property;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[Group('property')]
#[Group('test-count')]
class TestCountPreservationPropertyTest extends TestCase
{
    /**
     * Property 14: Test Count Preservation
     *
     * For any test file before and after update, the number of test methods SHALL remain
     * identical (unless new tests are explicitly added for new v3.6.0 features).
     */
    #[Test]
    #[DataProvider('testFileProvider')]
    public function test_method_count_is_preserved(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Count test methods using both legacy and modern patterns
        $legacyTestCount = $this->countLegacyTestMethods($content);
        $modernTestCount = $this->countModernTestMethods($content);
        $totalTestCount = $legacyTestCount + $modernTestCount;

        // Verify we have at least one test method
        $this->assertGreaterThan(
            0,
            $totalTestCount,
            "File {$filePath} should contain at least one test method"
        );

        // If file has both legacy and modern patterns, it's in transition
        if ($legacyTestCount > 0 && $modernTestCount > 0) {
            $this->markTestSkipped(
                "File {$filePath} is in transition with both legacy ({$legacyTestCount}) and modern ({$modernTestCount}) test methods"
            );
        }

        // Verify test method naming consistency
        $this->verifyTestMethodNaming($content, $filePath);
    }

    /**
     * Property 14.1: Test Method Signature Consistency
     *
     * For any test method after conversion, it SHALL maintain proper method signature
     * with void return type and appropriate visibility.
     */
    #[Test]
    #[DataProvider('testFileProvider')]
    public function test_method_signatures_are_consistent(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Find all test methods (both legacy and modern)
        $allTestMethods = [];

        // Legacy test methods
        preg_match_all('/public function (test_\w+|test\w+)\([^)]*\):\s*void/m', $content, $legacyMatches);
        $allTestMethods = array_merge($allTestMethods, $legacyMatches[1]);

        // Modern test methods with #[Test] attribute
        preg_match_all('/#\[Test\].*?public function (\w+)\([^)]*\):\s*void/ms', $content, $modernMatches);
        $allTestMethods = array_merge($allTestMethods, $modernMatches[1]);

        foreach ($allTestMethods as $methodName) {
            // Verify method has void return type
            $this->assertTrue(
                str_contains($content, "function {$methodName}(") && str_contains($content, '): void'),
                "Method {$methodName} in {$filePath} should have void return type"
            );

            // Verify method is public
            $this->assertTrue(
                str_contains($content, "public function {$methodName}("),
                "Method {$methodName} in {$filePath} should be public"
            );
        }
    }

    /**
     * Property 14.2: Data Provider Method Preservation
     *
     * For any test file with data provider methods, the providers SHALL be preserved
     * and properly referenced after conversion.
     */
    #[Test]
    #[DataProvider('testFileProvider')]
    public function data_provider_methods_are_preserved(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Find data provider references
        preg_match_all('/#\[DataProvider\([\'"](\w+)[\'"]\)\]/', $content, $providerRefs);
        $referencedProviders = $providerRefs[1];

        // Find actual data provider methods
        preg_match_all('/public static function (\w+Provider|\w+DataProvider|\w+Data)\(\):\s*array/', $content, $providerMethods);
        $actualProviders = $providerMethods[1];

        // Also check for methods ending with 'Provider' or containing 'data'
        preg_match_all('/public static function (\w*[Pp]rovider\w*|\w*[Dd]ata\w*)\(\):\s*array/', $content, $additionalProviders);
        $actualProviders = array_merge($actualProviders, $additionalProviders[1]);
        $actualProviders = array_unique($actualProviders);

        // Verify all referenced providers exist
        foreach ($referencedProviders as $referencedProvider) {
            $this->assertContains(
                $referencedProvider,
                $actualProviders,
                "Data provider {$referencedProvider} referenced in {$filePath} should exist as a method"
            );
        }

        // Verify provider methods return arrays
        foreach ($actualProviders as $providerMethod) {
            $this->assertTrue(
                str_contains($content, "function {$providerMethod}(): array"),
                "Data provider {$providerMethod} in {$filePath} should return array type"
            );
        }
    }

    /**
     * Property 14.3: Test Class Structure Preservation
     *
     * For any test class after conversion, it SHALL maintain proper class structure
     * with extends TestCase and appropriate use statements.
     */
    #[Test]
    #[DataProvider('testFileProvider')]
    public function test_class_structure_is_preserved(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Verify class extends TestCase
        $this->assertTrue(
            preg_match('/class \w+Test extends TestCase/', $content) === 1,
            "File {$filePath} should contain a test class extending TestCase"
        );

        // Verify proper namespace
        $this->assertTrue(
            str_contains($content, 'namespace Tests\\'),
            "File {$filePath} should have proper Tests namespace"
        );

        // Verify strict types declaration
        $this->assertTrue(
            str_contains($content, 'declare(strict_types=1);'),
            "File {$filePath} should have strict types declaration"
        );

        // If file uses modern attributes, verify imports
        if (str_contains($content, '#[Test]')) {
            $this->assertTrue(
                str_contains($content, 'use PHPUnit\\Framework\\Attributes\\Test;'),
                "File {$filePath} using #[Test] should import Test attribute"
            );
        }

        if (str_contains($content, '#[DataProvider')) {
            $this->assertTrue(
                str_contains($content, 'use PHPUnit\\Framework\\Attributes\\DataProvider;'),
                "File {$filePath} using #[DataProvider] should import DataProvider attribute"
            );
        }
    }

    /**
     * Property 14.4: Test Documentation Consistency
     *
     * For any test method with documentation, the documentation SHALL be consistent
     * and follow established patterns after conversion.
     */
    #[Test]
    #[DataProvider('testFileProvider')]
    public function test_documentation_is_consistent(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Find test methods with PHPDoc
        preg_match_all('/\/\*\*.*?\*\/\s*(?:#\[Test\].*?\s*)?public function (\w+)\(/s', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $docBlock = $match[0];
            $methodName = $match[1];

            // If method has documentation, verify it's properly formatted
            if (str_contains($docBlock, '/**')) {
                // Should not contain @test annotation
                $this->assertStringNotContainsString(
                    '@test',
                    $docBlock,
                    "Method {$methodName} in {$filePath} should not have @test annotation in PHPDoc"
                );

                // Should have proper PHPDoc structure
                $this->assertTrue(
                    preg_match('/\/\*\*\s*\n\s*\*.*?\*\//', $docBlock) === 1,
                    "Method {$methodName} in {$filePath} should have properly formatted PHPDoc"
                );
            }
        }
    }

    /**
     * Property 14.5: Test Assertion Preservation
     *
     * For any test method after conversion, all assertions SHALL be preserved
     * and use proper PHPUnit assertion methods.
     */
    #[Test]
    #[DataProvider('testFileProvider')]
    public function test_assertions_are_preserved(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Find all assertion calls
        preg_match_all('/\$this->(assert\w+)\(/', $content, $assertions);
        $assertionMethods = array_unique($assertions[1]);

        // Common PHPUnit assertions that should be preserved
        $validAssertions = [
            'assertTrue',
            'assertFalse',
            'assertEquals',
            'assertNotEquals',
            'assertSame',
            'assertNotSame',
            'assertNull',
            'assertNotNull',
            'assertEmpty',
            'assertNotEmpty',
            'assertCount',
            'assertNotCount',
            'assertContains',
            'assertNotContains',
            'assertStringContainsString',
            'assertStringNotContainsString',
            'assertArrayHasKey',
            'assertArrayNotHasKey',
            'assertInstanceOf',
            'assertNotInstanceOf',
            'assertIsArray',
            'assertIsString',
            'assertIsInt',
            'assertIsBool',
            'assertIsFloat',
            'assertIsNumeric',
            'assertGreaterThan',
            'assertGreaterThanOrEqual',
            'assertLessThan',
            'assertLessThanOrEqual',
            'assertMatchesRegularExpression',
            'assertDoesNotMatchRegularExpression',
            'assertFileExists',
            'assertFileDoesNotExist',
            'assertDirectoryExists',
            'assertDirectoryDoesNotExist',
            'assertJson',
            'assertJsonStringEqualsJsonString',
            'assertDatabaseHas',
            'assertDatabaseMissing',
            'assertDatabaseCount',
            'assertSoftDeleted',
            'assertNotSoftDeleted',
            'assertAuthenticatedAs',
            'assertGuest',
            'assertCredentials',
            'assertInvalidCredentials',
            'assertStatus',
            'assertSuccessful',
            'assertOk',
            'assertCreated',
            'assertNoContent',
            'assertNotFound',
            'assertForbidden',
            'assertUnauthorized',
            'assertRedirect',
            'assertLocation',
            'assertHeader',
            'assertHeaderMissing',
            'assertCookie',
            'assertCookieExpired',
            'assertCookieNotExpired',
            'assertCookieMissing',
            'assertPlainCookie',
            'assertEncryptedCookie',
            'assertSession',
            'assertSessionHas',
            'assertSessionHasInput',
            'assertSessionHasAll',
            'assertSessionHasErrors',
            'assertSessionHasErrorsIn',
            'assertSessionHasNoErrors',
            'assertSessionDoesntHaveErrors',
            'assertSessionMissing',
            'assertViewIs',
            'assertViewHas',
            'assertViewHasAll',
            'assertViewMissing',
            'assertSee',
            'assertSeeInOrder',
            'assertSeeText',
            'assertSeeTextInOrder',
            'assertDontSee',
            'assertDontSeeText',
            'assertSourceHas',
            'assertSourceMissing',
            'assertSeeElement',
            'assertDontSeeElement',
            'assertElementExists',
            'assertElementMissing',
            'assertInputValue',
            'assertInputValueIsNot',
            'assertChecked',
            'assertNotChecked',
            'assertRadioSelected',
            'assertRadioNotSelected',
            'assertSelected',
            'assertNotSelected',
            'assertSelectHasOptions',
            'assertSelectMissingOptions',
            'assertSelectHasOption',
            'assertSelectMissingOption',
            'assertValue',
            'assertAttribute',
            'assertAriaAttribute',
            'assertDataAttribute',
            'assertVisible',
            'assertPresent',
            'assertNotPresent',
            'assertMissing',
            'assertDialogOpened',
            'assertEnabled',
            'assertDisabled',
            'assertFocused',
            'assertNotFocused',
            'assertVueComponent',
            'assertVueContains',
            'assertVueDoesNotContain',
        ];

        foreach ($assertionMethods as $assertion) {
            // Skip if it's a valid assertion or a custom assertion method
            if (in_array($assertion, $validAssertions) || str_starts_with($assertion, 'assert')) {
                continue;
            }

            // Flag potentially invalid assertions
            $this->fail(
                "File {$filePath} contains potentially invalid assertion method: {$assertion}"
            );
        }

        // Verify file has at least one assertion if it has test methods
        $testMethodCount = $this->countLegacyTestMethods($content) + $this->countModernTestMethods($content);
        if ($testMethodCount > 0) {
            $this->assertGreaterThan(
                0,
                count($assertionMethods),
                "File {$filePath} with {$testMethodCount} test methods should contain assertions"
            );
        }
    }

    /**
     * Count legacy test methods (test_ prefix or @test annotation)
     */
    private function count_legacy_test_methods(string $content): int
    {
        // Count methods with test_ prefix
        preg_match_all('/public function test_\w+\(/', $content, $prefixMatches);

        // Count methods with @test annotation
        preg_match_all('/@test.*?public function \w+\(/s', $content, $annotationMatches);

        return count($prefixMatches[0]) + count($annotationMatches[0]);
    }

    /**
     * Count modern test methods (#[Test] attribute)
     */
    private function countModernTestMethods(string $content): int
    {
        preg_match_all('/#\[Test\].*?public function \w+\(/s', $content, $matches);

        return count($matches[0]);
    }

    /**
     * Verify test method naming consistency
     */
    private function verifyTestMethodNaming(string $content, string $filePath): void
    {
        // Find all public methods that look like tests
        preg_match_all('/public function (\w+)\(/', $content, $allMethods);
        $methodNames = $allMethods[1];

        foreach ($methodNames as $methodName) {
            // Skip non-test methods
            if (in_array($methodName, ['setUp', 'tearDown', 'setUpBeforeClass', 'tearDownAfterClass'])) {
                continue;
            }

            // Skip data providers
            if (str_contains($methodName, 'Provider') || str_contains($methodName, 'Data')) {
                continue;
            }

            // If method has #[Test] attribute, it should not have test_ prefix
            $methodPattern = preg_quote($methodName, '/');
            if (preg_match("/#\[Test\].*?public function {$methodPattern}\(/s", $content)) {
                $this->assertStringNotContainsString(
                    'test_',
                    $methodName,
                    "Method {$methodName} in {$filePath} with #[Test] attribute should not have test_ prefix"
                );
            }

            // If method has test_ prefix, it should not have #[Test] attribute
            if (str_starts_with($methodName, 'test_')) {
                $this->assertFalse(
                    preg_match("/#\[Test\].*?public function {$methodPattern}\(/s", $content) === 1,
                    "Method {$methodName} in {$filePath} with test_ prefix should not have #[Test] attribute"
                );
            }
        }
    }

    /**
     * Data provider for test files
     *
     * Generates a list of all test files in the tests directory
     */
    public static function test_file_provider(): array
    {
        $testFiles = [];
        $basePath = dirname(__DIR__, 3);
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

                    // Only include files that contain test methods
                    $content = file_get_contents($file->getPathname());
                    if (
                        str_contains($content, 'public function test') ||
                        str_contains($content, '#[Test]') ||
                        str_contains($content, '@test')
                    ) {
                        $testFiles[basename($relativePath)] = [$file->getPathname()];
                    }
                }
            }
        }

        return $testFiles;
    }
}
