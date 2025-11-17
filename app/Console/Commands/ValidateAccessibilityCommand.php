<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ValidateAccessibilityCommand extends Command
{
    protected $signature = 'accessibility:validate
                            {--url=* : Specific URLs to test (default: predefined routes)}
                            {--format=table : Output format (table, json, html)}
                            {--save-report : Save detailed report to storage/accessibility-report.html}
                            {--fail-on-errors : Exit with error code if violations found}';

    protected $description = 'Validate basic accessibility checks for selected pages.';

    /** @var array<string, array{name: string, contrast: float|null}> */
    protected array $compliantColors = [
        '#0056b3' => ['name' => 'Primary (MOTAC Blue)', 'contrast' => 6.8],
        '#198754' => ['name' => 'Success', 'contrast' => 4.9],
        '#ff8c00' => ['name' => 'Warning', 'contrast' => 4.5],
        '#b50c0c' => ['name' => 'Danger', 'contrast' => 8.2],
    ];

    /** @var array<int, string> */
    protected array $defaultUrls = [
        '/loans/guest/create',
        '/loans/guest/tracking',
        '/loans/dashboard',
        '/loans/history',
        '/loans/approvals',
    ];

    /** @var array<int, array{url: string, issue: string}> */
    protected array $violations = [];

    public function handle(): int
    {
        $this->info('Starting accessibility validation...');

        $urls = $this->option('url');
        $urls = is_array($urls) && $urls !== [] ? $urls : $this->defaultUrls;
        $baseConfig = config('app.url');
        $baseUrl = is_string($baseConfig) ? $baseConfig : '';

        foreach ($urls as $url) {
            $fullUrl = $baseUrl.$url;
            $response = Http::get($fullUrl);

            if (! $response->successful()) {
                $this->addViolation($fullUrl, 'HTTP '.$response->status());

                continue;
            }

            $this->validateHtml($fullUrl, (string) $response->body());
        }

        $this->displayResults();

        if ($this->option('save-report')) {
            File::put(storage_path('accessibility-report.html'), $this->buildReport());
        }

        $violationCount = count($this->violations);

        if ($violationCount > 0 && $this->option('fail-on-errors')) {
            return 1;
        }

        return 0;
    }

    private function validateHtml(string $url, string $html): void
    {
        $lower = strtolower($html);

        if (! str_contains($lower, '<main')) {
            $this->addViolation($url, 'Missing main landmark');
        }

        if (! str_contains($lower, '<h1')) {
            $this->addViolation($url, 'Missing h1 heading');
        }

        if (! str_contains($lower, '<nav') && ! str_contains($lower, 'role="navigation"')) {
            $this->addViolation($url, 'Missing navigation landmark');
        }

        $hasAccessibleName = str_contains($lower, 'aria-label') || str_contains($lower, 'aria-labelledby');
        if (! $hasAccessibleName) {
            $this->addViolation($url, 'No accessible names detected for interactive elements');
        }

        $hasCompliantColor = false;
        foreach (array_keys($this->compliantColors) as $color) {
            if (str_contains($lower, ltrim($color, '#'))) {
                $hasCompliantColor = true;
                break;
            }
        }

        if (! $hasCompliantColor) {
            $this->addViolation($url, 'No compliant brand colors detected');
        }

        if (! str_contains($lower, 'alt=')) {
            $this->addViolation($url, 'Images may be missing alt text');
        }
    }

    private function addViolation(string $url, string $issue): void
    {
        $this->violations[] = [
            'url' => $url,
            'issue' => $issue,
        ];
    }

    private function displayResults(): void
    {
        $count = count($this->violations);

        if ($count === 0) {
            $this->info('? All checks passed for the provided URLs.');

            return;
        }

        $this->error("? Found {$count} accessibility issues:");
        foreach ($this->violations as $violation) {
            $this->line(" - {$violation['url']}: {$violation['issue']}");
        }
    }

    private function buildReport(): string
    {
        $rows = array_map(
            fn (array $violation): string => '<li>'.htmlspecialchars($violation['url'].' - '.$violation['issue'], ENT_QUOTES).'</li>',
            $this->violations
        );

        $list = implode("\n", $rows);

        return '<!doctype html><html><body><h1>Accessibility Report</h1><ul>'.$list.'</ul></body></html>';
    }
}
