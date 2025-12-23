<?php

declare(strict_types=1);

/**
 * Property-Based Tests for Bahasa Melayu Content Validation
 *
 * Property-based tests to verify that all UI-related tests validate Bahasa Melayu content
 * instead of English, ensuring tests accurately reflect the v3.6.0 language decision.
 *
 * **Feature: test-suite-comprehensive-v3.6, Property 2: Bahasa Melayu Content Validation**
 * **Validates: Requirements 3.1-3.5**
 *
 * @trace Requirements 3.1-3.5
 */

namespace Tests\Feature\Property;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('property')]
#[Group('bahasa-melayu')]
class BahasaMelayuContentValidationPropertyTest extends TestCase
{
    /**
     * Property 2: Bahasa Melayu Content Validation
     *
     * For any UI test that asserts text content (form labels, buttons, messages, error messages),
     * the asserted text SHALL be in Bahasa Melayu, either as literal strings or translation key references from lang/ms/.
     */
    #[Test]
    #[DataProvider('provideUiTestFiles')]
    public function files_use_bahasa_melayu_content(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Skip files that don't contain UI assertions
        if (! $this->containsUiAssertions($content)) {
            $this->assertTrue(true, "File {$filePath} does not contain UI assertions - skipped");

            return;
        }

        // Check for English UI text that should be in Bahasa Melayu
        $englishPatterns = $this->getEnglishUiPatterns();

        $foundEnglishText = [];
        foreach ($englishPatterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $foundEnglishText[] = $matches[0];
            }
        }

        // If English text is found, check if it should use translation keys instead
        if (! empty($foundEnglishText)) {
            // Allow certain technical terms or proper nouns
            $allowedEnglishTerms = $this->getAllowedEnglishTerms();

            $violatingText = [];
            foreach ($foundEnglishText as $text) {
                $cleanText = trim($text, '\'"()');
                if (! \in_array($cleanText, $allowedEnglishTerms, true)) {
                    $violatingText[] = $text;
                }
            }

            if (! empty($violatingText)) {
                $this->fail(
                    "File {$filePath} contains English UI text that should use Bahasa Melayu or translation keys: " .
                        implode(', ', $violatingText)
                );
            }
        }

        // Always make at least one assertion
        $this->assertTrue(true, "File {$filePath} uses Bahasa Melayu content correctly");
    }

    /**
     * Property 2.1: Translation Key Usage
     *
     * For any UI test using translation keys, it SHALL use keys from lang/ms/ directory.
     */
    #[Test]
    #[DataProvider('provideUiTestFiles')]
    public function files_use_correct_translation_keys(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Find all translation key usages
        preg_match_all('/__\([\'"]([^\'"]+)[\'"]/', $content, $matches);

        // Always make at least one assertion
        $this->assertIsArray($matches[1], 'Translation key matches should be an array');

        if (! empty($matches[1])) {
            $basePath = dirname(__DIR__, 3);

            foreach ($matches[1] as $translationKey) {
                // Check if translation key exists in Bahasa Melayu
                $keyParts = explode('.', $translationKey);
                $file = $keyParts[0];

                $bmTranslationFile = "{$basePath}/lang/ms/{$file}.php";

                if (file_exists($bmTranslationFile)) {
                    $translations = include $bmTranslationFile;

                    // Navigate through nested keys
                    $current = $translations;
                    $keyExists = true;

                    for ($i = 1; $i < \count($keyParts); $i++) {
                        if (! isset($current[$keyParts[$i]])) {
                            $keyExists = false;
                            break;
                        }
                        $current = $current[$keyParts[$i]];
                    }

                    if (! $keyExists) {
                        $this->fail(
                            "File {$filePath} uses translation key '{$translationKey}' which doesn't exist in lang/ms/{$file}.php"
                        );
                    }
                } else {
                    // Allow certain system keys that might not have BM translations
                    $systemKeys = ['validation', 'pagination', 'passwords', 'auth'];
                    if (! \in_array($file, $systemKeys, true)) {
                        $this->fail(
                            "File {$filePath} uses translation key '{$translationKey}' but lang/ms/{$file}.php doesn't exist"
                        );
                    }
                }
            }
        }
    }

    /**
     * Property 2.2: Language Switcher Disabled
     *
     * For any test checking language switcher functionality, it SHALL verify the switcher is disabled or hidden.
     */
    #[Test]
    #[DataProvider('provideUiTestFiles')]
    public function language_switcher_is_disabled(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Check for language switcher tests
        if (str_contains($content, 'language') && str_contains($content, 'switch')) {
            // Should verify switcher is disabled/hidden
            $this->assertTrue(
                str_contains($content, 'disabled') ||
                    str_contains($content, 'hidden') ||
                    str_contains($content, 'assertDontSee') ||
                    str_contains($content, 'assertFalse') ||
                    str_contains($content, 'should not exist'),
                "File {$filePath} tests language switcher but doesn't verify it's disabled/hidden"
            );
        } else {
            // File doesn't test language switcher - that's valid
            $this->assertTrue(true, "File {$filePath} does not test language switcher");
        }
    }

    /**
     * Property 2.3: Error Message Validation
     *
     * For any test validating error messages, it SHALL expect Bahasa Melayu validation messages.
     */
    #[Test]
    #[DataProvider('provideUiTestFiles')]
    public function error_messages_use_bahasa_melayu(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $this->assertNotFalse($content, "Could not read file: {$filePath}");

        // Check for validation error assertions
        if (preg_match('/assertSessionHasErrors|assertSee.*error|assertSee.*invalid/i', $content)) {
            // Should use translation keys for error messages
            $englishErrorPatterns = [
                '/The .* field is required/',
                '/The .* must be/',
                '/The .* format is invalid/',
                '/Please enter/',
                '/This field is required/',
                '/Invalid input/',
                '/Error:/',
                '/Failed to/',
                '/Unable to/',
                '/Cannot/',
            ];

            foreach ($englishErrorPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    $this->fail(
                        "File {$filePath} contains English error messages that should use Bahasa Melayu translation keys"
                    );
                }
            }
        }

        // Always make at least one assertion
        $this->assertTrue(true, "File {$filePath} uses Bahasa Melayu error messages correctly");
    }

    /**
     * Get English UI patterns to check
     */
    private function getEnglishUiPatterns(): array
    {
        return [
            '/assertSee\([\'"]Submit[\'"]/',
            '/assertSee\([\'"]Login[\'"]/',
            '/assertSee\([\'"]Register[\'"]/',
            '/assertSee\([\'"]Dashboard[\'"]/',
            '/assertSee\([\'"]Welcome[\'"]/',
            '/assertSee\([\'"]Create[\'"]/',
            '/assertSee\([\'"]Edit[\'"]/',
            '/assertSee\([\'"]Delete[\'"]/',
            '/assertSee\([\'"]Save[\'"]/',
            '/assertSee\([\'"]Cancel[\'"]/',
            '/assertSee\([\'"]Back[\'"]/',
            '/assertSee\([\'"]Next[\'"]/',
            '/assertSee\([\'"]Previous[\'"]/',
            '/assertSee\([\'"]Search[\'"]/',
            '/assertSee\([\'"]Filter[\'"]/',
            '/assertSee\([\'"]Export[\'"]/',
            '/assertSee\([\'"]Import[\'"]/',
            '/assertSee\([\'"]Print[\'"]/',
            '/assertSee\([\'"]Download[\'"]/',
            '/assertSee\([\'"]Upload[\'"]/',
            '/assertSee\([\'"]View[\'"]/',
            '/assertSee\([\'"]Details[\'"]/',
            '/assertSee\([\'"]Settings[\'"]/',
            '/assertSee\([\'"]Profile[\'"]/',
            '/assertSee\([\'"]Logout[\'"]/',
            '/assertSee\([\'"]Home[\'"]/',
        ];
    }

    /**
     * Get allowed English terms (technical terms and proper nouns)
     */
    private function getAllowedEnglishTerms(): array
    {
        return [
            'API',
            'URL',
            'HTTP',
            'HTTPS',
            'JSON',
            'XML',
            'CSV',
            'PDF',
            'HTML',
            'CSS',
            'JS',
            'OAuth',
            'JWT',
            'UUID',
            'CSRF',
            'XSS',
            'SQL',
            'DB',
            'ID',
            'QR',
            'Laravel',
            'Filament',
            'Livewire',
            'PHPUnit',
            'Composer',
            'Artisan',
            'MOTAC',
            'ICTServe',
            'BPM',
            'PDPA',
            'WCAG',
            'MyGOV',
        ];
    }

    /**
     * Data provider for UI test files
     *
     * Generates a list of test files that likely contain UI assertions
     */
    public static function provideUiTestFiles(): array
    {
        $testFiles = [];
        $basePath = dirname(__DIR__, 3);
        $directories = [
            'tests/Feature',
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
                        str_contains($normalizedPath, 'Property/') ||
                        basename($relativePath) === 'TestCase.php'
                    ) {
                        continue;
                    }

                    // Only include files that likely contain UI tests
                    $content = file_get_contents($file->getPathname());
                    if (preg_match('/assertSee|assertDontSee|assertViewHas|assertSessionHas/', $content)) {
                        $testFiles[basename($relativePath)] = [$file->getPathname()];
                    }
                }
            }
        }

        return $testFiles;
    }

    /**
     * Helper method to check if file contains UI assertions
     */
    private function containsUiAssertions(string $content): bool
    {
        return preg_match('/assertSee|assertDontSee|assertViewHas|assertSessionHas|assertRedirect/', $content) === 1;
    }
}
