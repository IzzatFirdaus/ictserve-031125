<?php

declare(strict_types=1);

namespace App\Livewire\Pulse;

use Illuminate\Support\Facades\View;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

/**
 * Web Vitals Pulse Card
 *
 * Displays Core Web Vitals metrics in the Laravel Pulse dashboard
 * - LCP (Largest Contentful Paint): Target <2.5s
 * - FID (First Input Delay): Target <100ms
 * - CLS (Cumulative Layout Shift): Target <0.1
 * - TTFB (Time to First Byte): Target <600ms
 *
 * @see D12 §9 Performance optimization patterns
 * @see D13 §6 Performance monitoring
 *
 * @requirements P2 Performance & UX polish
 * @trace 13.5 Core Web Vitals optimization
 *
 * @version 1.0.0
 *
 * @created 2025-12-07
 *
 * @author Frontend Engineering Team
 */
#[Lazy]
class WebVitalsCard extends Card
{
    /**
     * Render the Web Vitals card
     */
    public function render(): \Illuminate\View\View: mixed
    {
        [$lcpValues, $lcpTime] = $this->aggregate('web_vitals', 'LCP');
        [$fidValues, $fidTime] = $this->aggregate('web_vitals', 'FID');
        [$clsValues, $clsTime] = $this->aggregate('web_vitals', 'CLS');
        [$ttfbValues, $ttfbTime] = $this->aggregate('web_vitals', 'TTFB');

        $metrics = [
            'lcp' => [
                'name' => 'LCP',
                'label' => 'Largest Contentful Paint',
                'value' => $lcpValues->avg() ?? 0,
                'target' => 2500,
                'unit' => 'ms',
                'time' => $lcpTime,
            ],
            'fid' => [
                'name' => 'FID',
                'label' => 'First Input Delay',
                'value' => $fidValues->avg() ?? 0,
                'target' => 100,
                'unit' => 'ms',
                'time' => $fidTime,
            ],
            'cls' => [
                'name' => 'CLS',
                'label' => 'Cumulative Layout Shift',
                'value' => ($clsValues->avg() ?? 0) / 100, // Store as integer, display as decimal
                'target' => 0.1,
                'unit' => '',
                'time' => $clsTime,
            ],
            'ttfb' => [
                'name' => 'TTFB',
                'label' => 'Time to First Byte',
                'value' => $ttfbValues->avg() ?? 0,
                'target' => 600,
                'unit' => 'ms',
                'time' => $ttfbTime,
            ],
        ];

        // Calculate ratings
        foreach ($metrics as &$metric) {
            $metric['rating'] = $this->getRating($metric['name'], $metric['value']);
            $metric['percentage'] = min(100, ($metric['value'] / $metric['target']) * 100);
        }

        return View::make('livewire.pulse.web-vitals', [
            'metrics' => $metrics,
        ]);
    }

    /**
     * Get performance rating for metric
     */
    protected function getRating(string $name, float $value): string
    {
        return match ($name) {
            'LCP' => $value <= 2500 ? 'good' : ($value <= 4000 ? 'needs-improvement' : 'poor'),
            'FID' => $value <= 100 ? 'good' : ($value <= 300 ? 'needs-improvement' : 'poor'),
            'CLS' => $value <= 0.1 ? 'good' : ($value <= 0.25 ? 'needs-improvement' : 'poor'),
            'TTFB' => $value <= 600 ? 'good' : ($value <= 1000 ? 'needs-improvement' : 'poor'),
            default => 'unknown',
        };
    }
}
