<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Standards Compliance Checker
 *
 * Validates frontend components against D00-D15 standards.
 *
 * @trace D03-FR-016.1, D03-FR-017.1, D03-FR-018.1
 * @trace D04 §8.1 (Standards Compliance)
 * @trace D10 §7 (Documentation Standards)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class StandardsComplianceChecker
{
    /**
     * Compliant color palette per D14 §8.2
     *
     * @var array<int, string>
     */
    protected array $compliantColors = [
        '#0056b3', // Primary (6.8:1 contrast)
        '#198754', // Success (4.9:1 contrast)
        '#ff8c00', // Warning (4.5:1 contrast)
        '#b50c0c', // Danger (8.2:1 contrast)
    ];

    /**
     * Deprecated colors to be removed per D14 §8.3
     *
     * @var array<int, string>
     */
    protected array $deprecatedColors = [
        '#F1C40F', // Old warning yellow
        '#E74C3C', // Old danger red
    ];

    /**
     * Check component compliance against all D00-D15 standards
     *
     * @param  array<string, mixed>  $component
     * @return array<string, mixed>
     */
    public function checkCompliance(array $component): array
    {
        $content = $component['content'];
        $type = $component['type'];

        $results = [
            'component' => $component['name'],
            'type' => $type,
            'path' => $component['relative_path'],
            'checks' => [],
            'score' => 0,
            'max_score' => 0,
            'compliance_percentage' => 0,
            'severity' => 'low',
        ];

        // Run all compliance checks
        $results['checks']['metadata'] = $this->checkMetadata($content);
        $results['checks']['accessibility'] = $this->checkAccessibility($content, $type);
        $results['checks']['traceability'] = $this->checkTraceability($content);
        $results['checks']['branding'] = $this->checkBranding($content);
        $results['checks']['bilingual'] = $this->checkBilingualSupport($content);
        $results['checks']['performance'] = $this->checkPerformance($content, $type);

        // Calculate overall score
        $totalScore = 0;
        $maxScore = 0;

        foreach ($results['checks'] as $check) {
            $totalScore += $check['score'];
            $maxScore += $check['max_score'];
        }

        $results['score'] = $totalScore;
        $results['max_score'] = $maxScore;
        $results['compliance_percentage'] = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;

        // Determine severity
        $results['severity'] = $this->determineSeverity($results['compliance_percentage'], $results['checks']);

        return $results;
    }

    /**
     * Check component metadata per D10 §7
     *
     * @return array<string, mixed>
     */
    protected function checkMetadata(string $content): array
    {
        $score = 0;
        $maxScore = 6;
        $issues = [];

        // Check for component name
        if (Str::contains($content, ['Component name:', 'Component Name:', '@component'])) {
            $score++;
        } else {
            $issues[] = 'Missing component name in header';
        }

        // Check for description
        if (Str::contains($content, ['Description:', '@description'])) {
            $score++;
        } else {
            $issues[] = 'Missing component description';
        }

        // Check for author
        if (Str::contains($content, ['Author:', '@author', 'Pasukan BPM MOTAC'])) {
            $score++;
        } else {
            $issues[] = 'Missing author information';
        }

        // Check for trace references
        if (Str::contains($content, ['@trace', 'Requirements:', 'D03-FR-', 'D04 §'])) {
            $score++;
        } else {
            $issues[] = 'Missing requirements traceability (@trace)';
        }

        // Check for version
        if (Str::contains($content, ['@version', 'Version:'])) {
            $score++;
        } else {
            $issues[] = 'Missing version information';
        }

        // Check for timestamps
        if (Str::contains($content, ['@created', '@updated', 'Created:', 'Last Updated:'])) {
            $score++;
        } else {
            $issues[] = 'Missing timestamp information';
        }

        return [
            'name' => 'Metadata Compliance',
            'score' => $score,
            'max_score' => $maxScore,
            'percentage' => round(($score / $maxScore) * 100, 2),
            'issues' => $issues,
            'passed' => $score >= 4, // At least 4/6 required
        ];
    }

    /**
     * Check WCAG 2.2 Level AA accessibility per D12 §9
     *
     * @return array<string, mixed>
     */
    protected function checkAccessibility(string $content, string $type): array
    {
        $score = 0;
        $maxScore = 8;
        $issues = [];

        // Check for ARIA attributes
        if (Str::contains($content, ['aria-', 'role='])) {
            $score++;
        } else {
            $issues[] = 'Missing ARIA attributes for accessibility';
        }

        // Check for semantic HTML
        if (Str::contains($content, ['<header', '<nav', '<main', '<footer', '<article', '<section'])) {
            $score++;
        } else {
            $issues[] = 'Missing semantic HTML5 elements';
        }

        // Check for proper labels
        if (Str::contains($content, ['<label', 'for=', 'aria-label'])) {
            $score++;
        } else {
            $issues[] = 'Missing proper form labels';
        }

        // Check for keyboard navigation support
        if (Str::contains($content, ['tabindex', '@keydown', 'keyboard'])) {
            $score++;
        } else {
            $issues[] = 'Missing keyboard navigation support';
        }

        // Check for focus indicators
        if (Str::contains($content, ['focus:', 'focus-visible', 'ring-'])) {
            $score++;
        } else {
            $issues[] = 'Missing focus indicators';
        }

        // Check for compliant colors
        $hasCompliantColors = false;
        foreach ($this->compliantColors as $color) {
            if (Str::contains($content, $color)) {
                $hasCompliantColors = true;
                break;
            }
        }
        if ($hasCompliantColors) {
            $score++;
        }

        // Check for deprecated colors (negative check)
        $hasDeprecatedColors = false;
        foreach ($this->deprecatedColors as $color) {
            if (Str::contains($content, $color)) {
                $hasDeprecatedColors = true;
                $issues[] = "Contains deprecated color: {$color}";
            }
        }
        if (! $hasDeprecatedColors) {
            $score++;
        }

        // Check for alt text on images
        if (! Str::contains($content, '<img') || Str::contains($content, 'alt=')) {
            $score++;
        } else {
            $issues[] = 'Images missing alt text';
        }

        return [
            'name' => 'WCAG 2.2 AA Accessibility',
            'score' => $score,
            'max_score' => $maxScore,
            'percentage' => round(($score / $maxScore) * 100, 2),
            'issues' => $issues,
            'passed' => $score >= 6, // At least 6/8 required
        ];
    }

    /**
     * Check requirements traceability per D03/D04
     *
     * @return array<string, mixed>
     */
    protected function checkTraceability(string $content): array
    {
        $score = 0;
        $maxScore = 3;
        $issues = [];

        // Check for D03 requirements references
        if (Str::contains($content, ['D03-FR-', 'Requirements:'])) {
            $score++;
        } else {
            $issues[] = 'Missing D03 requirements references';
        }

        // Check for D04 design references
        if (Str::contains($content, ['D04 §', 'Design:'])) {
            $score++;
        } else {
            $issues[] = 'Missing D04 design references';
        }

        // Check for D10/D12 documentation references
        if (Str::contains($content, ['D10 §', 'D12 §', 'D14 §'])) {
            $score++;
        } else {
            $issues[] = 'Missing D10/D12/D14 documentation references';
        }

        return [
            'name' => 'Requirements Traceability',
            'score' => $score,
            'max_score' => $maxScore,
            'percentage' => round(($score / $maxScore) * 100, 2),
            'issues' => $issues,
            'passed' => $score >= 2, // At least 2/3 required
        ];
    }

    /**
     * Check MOTAC branding per D14 §8
     *
     * @return array<string, mixed>
     */
    protected function checkBranding(string $content): array
    {
        $score = 0;
        $maxScore = 3;
        $issues = [];

        // Check for MOTAC branding elements
        if (Str::contains($content, ['MOTAC', 'Ministry of Tourism'])) {
            $score++;
        } else {
            $issues[] = 'Missing MOTAC branding';
        }

        // Check for compliant color usage
        $hasCompliantColors = false;
        foreach ($this->compliantColors as $color) {
            if (Str::contains($content, $color)) {
                $hasCompliantColors = true;
                break;
            }
        }
        if ($hasCompliantColors) {
            $score++;
        } else {
            $issues[] = 'Not using compliant color palette';
        }

        // Check for deprecated colors (should not exist)
        $hasDeprecatedColors = false;
        foreach ($this->deprecatedColors as $color) {
            if (Str::contains($content, $color)) {
                $hasDeprecatedColors = true;
            }
        }
        if (! $hasDeprecatedColors) {
            $score++;
        } else {
            $issues[] = 'Contains deprecated colors that must be removed';
        }

        return [
            'name' => 'MOTAC Branding',
            'score' => $score,
            'max_score' => $maxScore,
            'percentage' => round(($score / $maxScore) * 100, 2),
            'issues' => $issues,
            'passed' => $score >= 2, // At least 2/3 required
        ];
    }

    /**
     * Check bilingual support per D15
     *
     * @return array<string, mixed>
     */
    protected function checkBilingualSupport(string $content): array
    {
        $score = 0;
        $maxScore = 3;
        $issues = [];

        // Check for translation functions
        if (Str::contains($content, ['__(', '@lang', 'trans('])) {
            $score++;
        } else {
            $issues[] = 'Missing translation functions';
        }

        // Check for language references
        if (Str::contains($content, ['lang/en', 'lang/ms', 'locale'])) {
            $score++;
        } else {
            $issues[] = 'Missing language file references';
        }

        // Check for bilingual content structure
        if (Str::contains($content, ['Bahasa Melayu', 'English', 'bilingual'])) {
            $score++;
        } else {
            $issues[] = 'Missing bilingual content structure';
        }

        return [
            'name' => 'Bilingual Support',
            'score' => $score,
            'max_score' => $maxScore,
            'percentage' => round(($score / $maxScore) * 100, 2),
            'issues' => $issues,
            'passed' => $score >= 2, // At least 2/3 required
        ];
    }

    /**
     * Check performance optimization per D19
     *
     * @return array<string, mixed>
     */
    protected function checkPerformance(string $content, string $type): array
    {
        $score = 0;
        $maxScore = 4;
        $issues = [];

        // Check for lazy loading
        if (Str::contains($content, ['loading="lazy"', 'wire:lazy', '#[Lazy]'])) {
            $score++;
        } else {
            $issues[] = 'Missing lazy loading implementation';
        }

        // Check for image optimization
        if (! Str::contains($content, '<img') || Str::contains($content, ['width=', 'height=', 'fetchpriority'])) {
            $score++;
        } else {
            $issues[] = 'Images missing explicit dimensions or fetchpriority';
        }

        // Check for Livewire optimization
        if ($type === 'livewire_component' || $type === 'volt_component') {
            if (Str::contains($content, ['#[Computed]', 'wire:model.live.debounce', 'wire:model.lazy'])) {
                $score++;
            } else {
                $issues[] = 'Missing Livewire optimization patterns';
            }
        } else {
            $score++; // Not applicable
        }

        // Check for inline styles (should be refactored)
        if (! Str::contains($content, 'style=')) {
            $score++;
        } else {
            $issues[] = 'Contains inline styles that should be refactored to external CSS';
        }

        return [
            'name' => 'Performance Optimization',
            'score' => $score,
            'max_score' => $maxScore,
            'percentage' => round(($score / $maxScore) * 100, 2),
            'issues' => $issues,
            'passed' => $score >= 3, // At least 3/4 required
        ];
    }

    /**
     * Determine severity based on compliance percentage and critical issues
     *
     * @param  array<string, mixed>  $checks
     */
    protected function determineSeverity(float $percentage, array $checks): string
    {
        // Critical if accessibility or traceability fails
        if (! $checks['accessibility']['passed'] || ! $checks['traceability']['passed']) {
            return 'critical';
        }

        // High if below 60% compliance
        if ($percentage < 60) {
            return 'high';
        }

        // Medium if below 80% compliance
        if ($percentage < 80) {
            return 'medium';
        }

        // Low otherwise
        return 'low';
    }

    /**
     * Generate compliance report for all components
     *
     * @param  Collection<int, array<string, mixed>>  $components
     * @return array<string, mixed>
     */
    public function generateReport(Collection $components): array
    {
        $results = $components->map(function ($component) {
            return $this->checkCompliance($component);
        });

        $avgCompliance = $results->avg('compliance_percentage');
        $statistics = [
            'total_components' => $results->count(),
            'average_compliance' => is_numeric($avgCompliance) ? round((float) $avgCompliance, 2) : null,
            'by_severity' => $results->groupBy('severity')->map(fn ($group) => $group->count())->toArray(),
            'by_type' => $results->groupBy('type')->map(function ($group) {
                $avgComp = $group->avg('compliance_percentage');
                return [
                    'count' => $group->count(),
                    'average_compliance' => is_numeric($avgComp) ? round((float) $avgComp, 2) : null,
                ];
            })->toArray(),
            'critical_issues' => $results->where('severity', 'critical')->count(),
            'high_issues' => $results->where('severity', 'high')->count(),
            'medium_issues' => $results->where('severity', 'medium')->count(),
            'low_issues' => $results->where('severity', 'low')->count(),
        ];

        return [
            'statistics' => $statistics,
            'results' => $results->toArray(),
            'generated_at' => now()->toIso8601String(),
        ];
    }
}
