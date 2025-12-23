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
use Tests\TestCase;

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
    #[DataProvider('uiTestFileProvider')]
    public function test_files_use_bahasa_melayu_content(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Skip files that don't contain UI assertions
        if (! $this->containsUiAssertions($content)) {
            $this->markTestSkipped("File {$filePath} does not contain UI assertions");
        }

        // Check for English UI text that should be in Bahasa Melayu
        $englishPatterns = [
            // Common English UI text patterns
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
            '/assertSee\([\'"]About[\'"]/',
            '/assertSee\([\'"]Contact[\'"]/',
            '/assertSee\([\'"]Help[\'"]/',
            '/assertSee\([\'"]FAQ[\'"]/',
            '/assertSee\([\'"]Support[\'"]/',
            '/assertSee\([\'"]Terms[\'"]/',
            '/assertSee\([\'"]Privacy[\'"]/',
            '/assertSee\([\'"]Policy[\'"]/',
            '/assertSee\([\'"]Agreement[\'"]/',
            '/assertSee\([\'"]Accept[\'"]/',
            '/assertSee\([\'"]Decline[\'"]/',
            '/assertSee\([\'"]Agree[\'"]/',
            '/assertSee\([\'"]Disagree[\'"]/',
            '/assertSee\([\'"]Yes[\'"]/',
            '/assertSee\([\'"]No[\'"]/',
            '/assertSee\([\'"]OK[\'"]/',
            '/assertSee\([\'"]Close[\'"]/',
            '/assertSee\([\'"]Open[\'"]/',
            '/assertSee\([\'"]Show[\'"]/',
            '/assertSee\([\'"]Hide[\'"]/',
            '/assertSee\([\'"]Enable[\'"]/',
            '/assertSee\([\'"]Disable[\'"]/',
            '/assertSee\([\'"]Active[\'"]/',
            '/assertSee\([\'"]Inactive[\'"]/',
            '/assertSee\([\'"]Online[\'"]/',
            '/assertSee\([\'"]Offline[\'"]/',
            '/assertSee\([\'"]Available[\'"]/',
            '/assertSee\([\'"]Unavailable[\'"]/',
            '/assertSee\([\'"]Loading[\'"]/',
            '/assertSee\([\'"]Please wait[\'"]/',
            '/assertSee\([\'"]Processing[\'"]/',
            '/assertSee\([\'"]Complete[\'"]/',
            '/assertSee\([\'"]Incomplete[\'"]/',
            '/assertSee\([\'"]Success[\'"]/',
            '/assertSee\([\'"]Error[\'"]/',
            '/assertSee\([\'"]Warning[\'"]/',
            '/assertSee\([\'"]Info[\'"]/',
            '/assertSee\([\'"]Notice[\'"]/',
            '/assertSee\([\'"]Alert[\'"]/',
            '/assertSee\([\'"]Message[\'"]/',
            '/assertSee\([\'"]Notification[\'"]/',
            '/assertSee\([\'"]Email[\'"]/',
            '/assertSee\([\'"]Password[\'"]/',
            '/assertSee\([\'"]Username[\'"]/',
            '/assertSee\([\'"]Name[\'"]/',
            '/assertSee\([\'"]Address[\'"]/',
            '/assertSee\([\'"]Phone[\'"]/',
            '/assertSee\([\'"]Date[\'"]/',
            '/assertSee\([\'"]Time[\'"]/',
            '/assertSee\([\'"]Status[\'"]/',
            '/assertSee\([\'"]Type[\'"]/',
            '/assertSee\([\'"]Category[\'"]/',
            '/assertSee\([\'"]Description[\'"]/',
            '/assertSee\([\'"]Title[\'"]/',
            '/assertSee\([\'"]Subject[\'"]/',
            '/assertSee\([\'"]Content[\'"]/',
            '/assertSee\([\'"]Body[\'"]/',
            '/assertSee\([\'"]Text[\'"]/',
            '/assertSee\([\'"]Comment[\'"]/',
            '/assertSee\([\'"]Reply[\'"]/',
            '/assertSee\([\'"]Response[\'"]/',
            '/assertSee\([\'"]Feedback[\'"]/',
            '/assertSee\([\'"]Review[\'"]/',
            '/assertSee\([\'"]Rating[\'"]/',
            '/assertSee\([\'"]Score[\'"]/',
            '/assertSee\([\'"]Total[\'"]/',
            '/assertSee\([\'"]Count[\'"]/',
            '/assertSee\([\'"]Number[\'"]/',
            '/assertSee\([\'"]Amount[\'"]/',
            '/assertSee\([\'"]Price[\'"]/',
            '/assertSee\([\'"]Cost[\'"]/',
            '/assertSee\([\'"]Fee[\'"]/',
            '/assertSee\([\'"]Tax[\'"]/',
            '/assertSee\([\'"]Discount[\'"]/',
            '/assertSee\([\'"]Subtotal[\'"]/',
            '/assertSee\([\'"]Grand Total[\'"]/',
        ];

        $foundEnglishText = [];
        foreach ($englishPatterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $foundEnglishText[] = $matches[0];
            }
        }

        // If English text is found, check if it should use translation keys instead
        if (! empty($foundEnglishText)) {
            // Allow certain technical terms or proper nouns
            $allowedEnglishTerms = [
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

            $violatingText = [];
            foreach ($foundEnglishText as $text) {
                $cleanText = trim($text, '\'"()');
                if (! in_array($cleanText, $allowedEnglishTerms, true)) {
                    $violatingText[] = $text;
                }
            }

            if (! empty($violatingText)) {
                $this->fail(
                    "File {$filePath} contains English UI text that should use Bahasa Melayu or translation keys: ".
                        implode(', ', $violatingText)
                );
            }
        }
    }

    /**
     * Property 2.1: Translation Key Usage
     *
     * For any UI test using translation keys, it SHALL use keys from lang/ms/ directory.
     */
    #[Test]
    #[DataProvider('uiTestFileProvider')]
    public function test_files_use_correct_translation_keys(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Find all translation key usages
        preg_match_all('/__\([\'"]([^\'"]+)[\'"]/', $content, $matches);

        if (! empty($matches[1])) {
            foreach ($matches[1] as $translationKey) {
                // Check if translation key exists in Bahasa Melayu
                $keyParts = explode('.', $translationKey);
                $file = $keyParts[0];

                $bmTranslationFile = base_path("lang/ms/{$file}.php");

                if (file_exists($bmTranslationFile)) {
                    $translations = include $bmTranslationFile;

                    // Navigate through nested keys
                    $current = $translations;
                    $keyExists = true;

                    for ($i = 1; $i < count($keyParts); $i++) {
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
                    if (! in_array($file, $systemKeys, true)) {
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
    #[DataProvider('uiTestFileProvider')]
    public function test_language_switcher_is_disabled(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Check for language switcher tests
        if (str_contains($content, 'language') && str_contains($content, 'switch')) {
            // Should verify switcher is disabled/hidden
            $this->assertTrue(
                str_contains($content, 'disabled') ||
                    str_contains($content, 'hidden') ||
                    str_contains($content, 'assertDontSee'),
                "File {$filePath} tests language switcher but doesn't verify it's disabled/hidden"
            );
        }
    }

    /**
     * Property 2.3: Error Message Validation
     *
     * For any test validating error messages, it SHALL expect Bahasa Melayu validation messages.
     */
    #[Test]
    #[DataProvider('uiTestFileProvider')]
    public function test_error_messages_use_bahasa_melayu(string $filePath): void
    {
        $content = file_get_contents($filePath);

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
    }

    /**
     * Data provider for UI test files
     *
     * Generates a list of test files that likely contain UI assertions
     */
    public static function uiTestFileProvider(): array
    {
        $testFiles = [];
        $basePath = dirname(__DIR__, 3);
        $directories = [
            'tests/Feature',
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
                        str_contains($relativePath, 'Property/') ||
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
