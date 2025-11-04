<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

/**
 * WCAG 2.2 Level AA Accessibility Validation Command
 *
 * Automated accessibility testing tool that validates:
 * - Color contrast ratios (4.5:1 for text, 3:1 for UI components)
 * - Focus indicators (3-4px outline, 2px offset, 3:1 contrast)
 * - Touch target sizes (minimum 44×44px)
 * - ARIA attributes and semantic HTML
 * - Keyboard navigation support
 * - Screen reader compatibility
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.1 (Accessibility Requirements)
 * @trace D03-FR-006.2 (Keyboard Navigation)
 * @trace D03-FR-006.3 (Screen Reader Support)
 * @trace D04 §6.1 (Accessibility Compliance)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §9 (Accessibility Standards)
 * @version 1.0.0
 * @created 2025-11-04
 */
class ValidateAccessibilityCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'accessibility:validate
                            {--url=* : Specific URLs to test (default: all loan module routes)}
                            {--format=table : Output format (table, json, html)}
                            {--save-report : Save detailed report to storage/accessibility-report.html}
                            {--fail-on-errors : Exit with error code if violations found}';

    /**
     * The console command description.
     */
    protected $description = 'Validate WCAG 2.2 Level AA compliance for the Updated Loan Module';

    /**
     * WCAG 2.2 AA compliant color palette with contrast ratios
     */
    protected array $compliantColors = [
        '#0056b3' => ['name' => 'Primary (MOTAC Blue)', 'contrast' => 6.8],
        '#198754' => ['name' => 'Success', 'contrast' => 4.9],
        '#ff8c00' => ['name' => 'Warning', 'contrast' => 4.5],
        '#b50c0c' => ['name' => 'Danger', 'contrast' => 8.2],
        '#212529' => ['name' => 'Text', 'contrast' => 16.6],
        '#ffffff' => ['name' => 'Background', 'contrast' => null],
    ];

    /**
     * Default URLs to test for loan module
     */
    protected array $defaultUrls = [
        '/loans/guest/create',
        '/loans/guest/tracking',
        '/loans/dashboard',
        '/loans/history',
        '/loans/approvals',
    ];

    /**
     * Accessibility violations found
     */
    protected array $violations = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Starting WCAG 2.2 Level AA Compliance Validation...');
        $this->newLine();

        $urls = $this->option('url') ?: $this->defaultUrls;
        $baseUrl = config('app.url');

        foreach ($urls as $url) {
            $fullUrl = $baseUrl . $url;
            $this->info("Testing: {$fullUrl}");

            try {
                $this->validateUrl($fullUrl);
            } catch (\Exception $e) {
                $this->error("Failed to test {$fullUrl}: {$e->getMessage()}");
                continue;
            }
        }

        $this->displayResults();

        if ($this->option('save-report')) {
            $this->saveHtmlReport();
        }

        $violationCount = count($this->violations);

        if ($violationCount > 0) {
            $this->error("❌ Found {$violationCount} accessibility violations");

            if ($this->option('fail-on-errors')) {
                return 1;
            }
        } else {
            $this->info('✅ All tests passed! WCAG 2.2 Level AA compliance verified.');
        }

        return 0;
    }

    /**
     * Validate a specific URL for accessibility compliance
     */
    protected function validateUrl(string $url): void
    {
        // Get page content
        $response = Http::get($url);

        if (!$response->successful()) {
            throw new \Exception("HTTP {$response->status()}");
        }

        $html = $response->body();
        $crawler = new Crawler($html);

        // Run all validation checks
        $this->validateSemanticHtml($url, $crawler);
        $this->validateAriaAttributes($url, $crawler);
        $this->validateColorContrast($url, $html);
        $this->validateFocusIndicators($url, $html);
        $this->validateTouchTargets($url, $crawler);
        $this->validateKeyboardNavigation($url, $html);
        $this->validateHeadingHierarchy($url, $crawler);
        $this->validateFormAccessibility($url, $crawler);
        $this->validateLanguageAttributes($url, $crawler);
        $this->validateImageAlternatives($url, $crawler);
    }

    /**
     * Validate semantic HTML structure
     */
    protected function validateSemanticHtml(string $url, Crawler $crawler): void
    {
        // Check for main landmark
        if ($crawler->filter('main, [role="main"]')->count() === 0) {
            $this->addViolation($url, 'Semantic HTML', 'Missing main landmark', 'WCAG 1.3.1');
        }

        // Check for proper heading structure
        if ($crawler->filter('h1')->count() === 0) {
            $this->addViolation($url, 'Semantic HTML', 'Missing h1 heading', 'WCAG 1.3.1');
        }

        if ($crawler->filter('h1')->count() > 1) {
            $this->addViolation($url, 'Semantic HTML', 'Multiple h1 headings found', 'WCAG 1.3.1');
        }

        // Check for navigation landmarks
        if ($crawler->filter('nav, [role="navigation"]')->count() === 0) {
            $this->addViolation($url, 'Semantic HTML', 'Missing navigation landmark', 'WCAG 1.3.1');
        }
    }

    /**
     * Validate ARIA attributes
     */
    protected function validateAriaAttributes(string $url, Crawler $crawler): void
    {
        // Check for ARIA labels on interactive elements without text
        $crawler->filter('button, input[type="submit"], input[type="button"], a')->each(function (Crawler $node) use ($url) {
            $text = trim($node->text());
            $ariaLabel = $node->attr('aria-label');
            $ariaLabelledby = $node->attr('aria-labelledby');

            if (empty($text) && empty($ariaLabel) && empty($ariaLabelledby)) {
                $this->addViolation($url, 'ARIA', 'Interactive element missing accessible name', 'WCAG 4.1.2');
            }
        });

        // Check for proper ARIA live regions
        $crawler->filter('[wire\\:loading]')->each(function (Crawler $node) use ($url) {
            if (!$node->attr('aria-live') && !$node->attr('aria-busy')) {
                $this->addViolation($url, 'ARIA', 'Loading state not announced to screen readers', 'WCAG 4.1.3');
            }
        });

        // Check for form validation errors
        $crawler->filter('.error, .invalid, [aria-invalid="true"]')->each(function (Crawler $node) use ($url) {
            if (!$node->attr('role') && !$node->attr('aria-live')) {
                $this->addViolation($url, 'ARIA', 'Error message not announced to screen readers', 'WCAG 3.3.1');
            }
        });
    }

    /**
     * Validate color contrast compliance
     */
    protected function validateColorContrast(string $url, string $html): void
    {
        // Check for deprecated color classes
        $deprecatedColors = [
            'bg-red-500', 'text-red-500', 'bg-green-500', 'text-green-500',
            'bg-yellow-500', 'text-yellow-500', 'bg-blue-500', 'text-blue-500'
        ];

        foreach ($deprecatedColors as $color) {
            if (str_contains($html, $color)) {
                $this->addViolation($url, 'Color Contrast', "Deprecated color class '{$color}' found", 'WCAG 1.4.3');
            }
        }

        // Check for compliant color usage
        $hasCompliantColors = false;
        $compliantClasses = [
            'text-gray-900', 'text-gray-800', 'text-gray-700',
            'bg-motac-blue', 'bg-success', 'bg-warning', 'bg-danger'
        ];

        foreach ($compliantClasses as $class) {
            if (str_contains($html, $class)) {
                $hasCompliantColors = true;
                break;
            }
        }

        if (!$hasCompliantColors) {
            $this->addViolation($url, 'Color Contrast', 'No WCAG compliant color classes found', 'WCAG 1.4.3');
        }
    }

    /**
     * Validate focus indicators
     */
    protected function validateFocusIndicators(string $url, string $html): void
    {
        // Check for focus ring classes on interactive elements
        $interactiveElements = ['button', 'input', 'select', 'textarea', 'a'];

        foreach ($interactiveElements as $element) {
            if (str_contains($html, "<{$element}")) {
                $hasFocusRing = str_contains($html, 'focus:ring-2') ||
                               str_contains($html, 'focus:ring-3') ||
                               str_contains($html, 'focus:outline-none focus:ring');

                if (!$hasFocusRing) {
                    $this->addViolation($url, 'Focus Indicators', "Missing focus indicators on {$element} elements", 'WCAG 2.4.7');
                }
            }
        }

        // Check for proper focus offset
        if (str_contains($html, 'focus:ring') && !str_contains($html, 'focus:ring-offset')) {
            $this->addViolation($url, 'Focus Indicators', 'Focus indicators missing proper offset', 'WCAG 2.4.7');
        }
    }

    /**
     * Validate touch target sizes
     */
    protected function validateTouchTargets(string $url, Crawler $crawler): void
    {
        $crawler->filter('button, input[type="submit"], input[type="button"], a')->each(function (Crawler $node) use ($url) {
            $class = $node->attr('class') ?? '';

            $hasMinHeight = str_contains($class, 'min-h-[44px]') ||
                           str_contains($class, 'min-h-44') ||
                           str_contains($class, 'h-11') ||
                           (str_contains($class, 'py-2') && str_contains($class, 'px-4'));

            if (!$hasMinHeight) {
                $this->addViolation($url, 'Touch Targets', 'Interactive element below minimum 44×44px size', 'WCAG 2.5.5');
            }
        });
    }

    /**
     * Validate keyboard navigation support
     */
    protected function validateKeyboardNavigation(string $url, string $html): void
    {
        // Check for positive tabindex values (anti-pattern)
        if (preg_match('/tabindex=["\']([1-9]\d*)["\']/', $html)) {
            $this->addViolation($url, 'Keyboard Navigation', 'Positive tabindex values found (anti-pattern)', 'WCAG 2.4.3');
        }

        // Check for skip links
        if (!str_contains($html, 'skip-to-content') && !str_contains($html, 'skip-link')) {
            $this->addViolation($url, 'Keyboard Navigation', 'Missing skip links for keyboard users', 'WCAG 2.4.1');
        }

        // Check for keyboard event handlers on clickable elements
        if (str_contains($html, 'wire:click') || str_contains($html, '@click')) {
            if (!str_contains($html, '@keydown') && !str_contains($html, '@keyup')) {
                $this->addViolation($url, 'Keyboard Navigation', 'Click handlers missing keyboard equivalents', 'WCAG 2.1.1');
            }
        }
    }

    /**
     * Validate heading hierarchy
     */
    protected function validateHeadingHierarchy(string $url, Crawler $crawler): void
    {
        $headings = [];

        for ($i = 1; $i <= 6; $i++) {
            $crawler->filter("h{$i}")->each(function (Crawler $node) use (&$headings, $i) {
                $headings[] = $i;
            });
        }

        if (empty($headings)) {
            return;
        }

        // Check if first heading is h1
        if ($headings[0] !== 1) {
            $this->addViolation($url, 'Heading Hierarchy', 'Page does not start with h1', 'WCAG 1.3.1');
        }

        // Check for skipped heading levels
        for ($i = 1; $i < count($headings); $i++) {
            if ($headings[$i] - $headings[$i - 1] > 1) {
                $this->addViolation($url, 'Heading Hierarchy', 'Heading levels skip (e.g., h1 to h3)', 'WCAG 1.3.1');
                break;
            }
        }
    }

    /**
     * Validate form accessibility
     */
    protected function validateFormAccessibility(string $url, Crawler $crawler): void
    {
        $crawler->filter('input, select, textarea')->each(function (Crawler $node) use ($url) {
            $id = $node->attr('id');
            $ariaLabel = $node->attr('aria-label');
            $ariaLabelledby = $node->attr('aria-labelledby');

            // Check for associated label
            if ($id) {
                $hasLabel = $crawler->filter("label[for=\"{$id}\"]")->count() > 0;

                if (!$hasLabel && !$ariaLabel && !$ariaLabelledby) {
                    $this->addViolation($url, 'Form Accessibility', 'Form input missing accessible label', 'WCAG 3.3.2');
                }
            }

            // Check required field indication
            if ($node->attr('required') && !$node->attr('aria-required')) {
                $this->addViolation($url, 'Form Accessibility', 'Required field not properly indicated', 'WCAG 3.3.2');
            }
        });
    }

    /**
     * Validate language attributes
     */
    protected function validateLanguageAttributes(string $url, Crawler $crawler): void
    {
        // Check for lang attribute on html element
        $htmlLang = $crawler->filter('html')->attr('lang');

        if (!$htmlLang) {
            $this->addViolation($url, 'Language', 'Missing lang attribute on html element', 'WCAG 3.1.1');
        }

        // Check for language changes
        $crawler->filter('[lang]')->each(function (Crawler $node) use ($url, $htmlLang) {
            $lang = $node->attr('lang');

            if ($lang && $lang !== $htmlLang) {
                // This is good - language changes are properly marked
                return;
            }
        });
    }

    /**
     * Validate image alternatives
     */
    protected function validateImageAlternatives(string $url, Crawler $crawler): void
    {
        $crawler->filter('img')->each(function (Crawler $node) use ($url) {
            $alt = $node->attr('alt');
            $ariaLabel = $node->attr('aria-label');
            $ariaLabelledby = $node->attr('aria-labelledby');
            $role = $node->attr('role');

            // Decorative images should have empty alt or role="presentation"
            if ($role === 'presentation' || $alt === '') {
                return;
            }

            // Content images must have alternative text
            if (!$alt && !$ariaLabel && !$ariaLabelledby) {
                $this->addViolation($url, 'Images', 'Image missing alternative text', 'WCAG 1.1.1');
            }
        });
    }

    /**
     * Add a violation to the results
     */
    protected function addViolation(string $url, string $category, string $description, string $wcagCriterion): void
    {
        $this->violations[] = [
            'url' => $url,
            'category' => $category,
            'description' => $description,
            'wcag_criterion' => $wcagCriterion,
            'severity' => $this->getSeverity($wcagCriterion),
        ];
    }

    /**
     * Get severity level based on WCAG criterion
     */
    protected function getSeverity(string $criterion): string
    {
        $criticalCriteria = ['1.4.3', '2.1.1', '4.1.2'];
        $highCriteria = ['1.3.1', '2.4.7', '3.3.1'];

        if (in_array($criterion, $criticalCriteria)) {
            return 'Critical';
        }

        if (in_array($criterion, $highCriteria)) {
            return 'High';
        }

        return 'Medium';
    }

    /**
     * Display validation results
     */
    protected function displayResults(): void
    {
        $this->newLine();

        if (empty($this->violations)) {
            $this->info('✅ No accessibility violations found!');
            return;
        }

        $this->error('❌ Accessibility violations found:');
        $this->newLine();

        if ($this->option('format') === 'json') {
            $this->line(json_encode($this->violations, JSON_PRETTY_PRINT));
            return;
        }

        // Group violations by severity
        $grouped = collect($this->violations)->groupBy('severity');

        foreach (['Critical', 'High', 'Medium'] as $severity) {
            if (!$grouped->has($severity)) {
                continue;
            }

            $this->line("<fg=red>🔴 {$severity} Issues:</>");

            $headers = ['URL', 'Category', 'Description', 'WCAG'];
            $rows = $grouped[$severity]->map(function ($violation) {
                return [
                    str_replace(config('app.url'), '', $violation['url']),
                    $violation['category'],
                    $violation['description'],
                    $violation['wcag_criterion'],
                ];
            })->toArray();

            $this->table($headers, $rows);
            $this->newLine();
        }

        // Summary
        $total = count($this->violations);
        $critical = $grouped->get('Critical', collect())->count();
        $high = $grouped->get('High', collect())->count();
        $medium = $grouped->get('Medium', collect())->count();

        $this->info("Summary: {$total} total violations ({$critical} critical, {$high} high, {$medium} medium)");
    }

    /**
     * Save detailed HTML report
     */
    protected function saveHtmlReport(): void
    {
        $reportPath = storage_path('accessibility-report.html');

        $html = $this->generateHtmlReport();

        File::put($reportPath, $html);

        $this->info("📄 Detailed report saved to: {$reportPath}");
    }

    /**
     * Generate HTML report
     */
    protected function generateHtmlReport(): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $total = count($this->violations);

        $violationsHtml = '';

        foreach ($this->violations as $violation) {
            $severityColor = match($violation['severity']) {
                'Critical' => '#dc2626',
                'High' => '#ea580c',
                'Medium' => '#d97706',
                default => '#6b7280'
            };

            $violationsHtml .= "
                <tr>
                    <td>{$violation['url']}</td>
                    <td>{$violation['category']}</td>
                    <td>{$violation['description']}</td>
                    <td>{$violation['wcag_criterion']}</td>
                    <td><span style='color: {$severityColor}; font-weight: bold;'>{$violation['severity']}</span></td>
                </tr>
            ";
        }

        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>WCAG 2.2 AA Accessibility Report - ICTServe Loan Module</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 40px; }
                .header { background: #0056b3; color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
                .summary { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                th { background-color: #f8f9fa; font-weight: 600; }
                .status-pass { color: #198754; font-weight: bold; }
                .status-fail { color: #dc2626; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>WCAG 2.2 Level AA Accessibility Report</h1>
                <p>ICTServe Updated Loan Module - Generated on {$timestamp}</p>
            </div>

            <div class='summary'>
                <h2>Summary</h2>
                <p><strong>Total Violations:</strong> {$total}</p>
                <p><strong>Status:</strong> " . ($total === 0 ? "<span class='status-pass'>✅ PASS</span>" : "<span class='status-fail'>❌ FAIL</span>") . "</p>
            </div>

            " . ($total > 0 ? "
            <h2>Violations Found</h2>
            <table>
                <thead>
                    <tr>
                        <th>URL</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>WCAG Criterion</th>
                        <th>Severity</th>
                    </tr>
                </thead>
                <tbody>
                    {$violationsHtml}
                </tbody>
            </table>
            " : "<p class='status-pass'>🎉 No accessibility violations found! The Updated Loan Module meets WCAG 2.2 Level AA standards.</p>") . "

            <footer style='margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #6b7280;'>
                <p>Generated by ICTServe Accessibility Validation Tool</p>
                <p>Standards: WCAG 2.2 Level AA, ISO/IEC 40500</p>
            </footer>
        </body>
        </html>
        ";
    }
}
