<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Widget Deduplicator Service
 *
 * Identifies and removes duplicate widget instances from the dashboard
 * following ICTServe v3.6.1 patterns and Filament v4.3.1 compliance.
 *
 * @trace Requirements: R1 (Widget Deduplication)
 *
 * @see D04 §3.2 Widget Management Architecture
 *
 * @version 3.6.1
 */
class WidgetDeduplicator
{
    /**
     * Detect duplicate widgets in an array
     *
     * @param  array  $widgets  Array of widget configurations
     * @return array Array of duplicate information
     */
    public function detectDuplicates(array $widgets): array
    {
        $duplicates = [];
        $seen = [];

        foreach ($widgets as $widgetClass => $config) {
            $signature = $this->generateWidgetSignature($widgetClass, $config);

            if (isset($seen[$signature])) {
                $duplicates[] = [
                    'original' => $seen[$signature]['class'],
                    'duplicate' => $widgetClass,
                    'signature' => $signature,
                    'config' => $config,
                ];

                Log::warning('Duplicate widget detected', [
                    'original' => $seen[$signature]['class'],
                    'duplicate' => $widgetClass,
                    'signature' => $signature,
                ]);
            } else {
                $seen[$signature] = [
                    'class' => $widgetClass,
                    'config' => $config,
                ];
            }
        }

        return $duplicates;
    }

    /**
     * Remove duplicate widgets from array
     *
     * @param  array  $widgets  Array of widget configurations
     * @return array Array with duplicates removed
     */
    public function removeDuplicates(array $widgets): array
    {
        $duplicates = $this->detectDuplicates($widgets);
        $filtered = $widgets;

        foreach ($duplicates as $duplicate) {
            unset($filtered[$duplicate['duplicate']]);

            Log::info('Duplicate widget removed', [
                'original' => $duplicate['original'],
                'duplicate' => $duplicate['duplicate'],
            ]);
        }

        return $filtered;
    }

    /**
     * Generate unique signature for widget
     *
     * @param  string  $widgetClass  Widget class name
     * @param  array  $config  Widget configuration
     * @return string MD5 hash signature
     */
    private function generateWidgetSignature(string $widgetClass, array $config): string
    {
        $signatureData = [
            'class' => $widgetClass,
            'category' => $config['category'] ?? 'content',
            'sort_order' => $config['sort_order'] ?? 1,
        ];

        return md5(serialize($signatureData));
    }

    /**
     * Get duplicate statistics
     *
     * @param  array  $widgets  Array of widget configurations
     * @return array Statistics about duplicates
     */
    public function getStatistics(array $widgets): array
    {
        $duplicates = $this->detectDuplicates($widgets);

        return [
            'total_widgets' => count($widgets),
            'unique_widgets' => count($widgets) - count($duplicates),
            'duplicate_count' => count($duplicates),
            'duplicate_percentage' => count($widgets) > 0
                ? round((count($duplicates) / count($widgets)) * 100, 2)
                : 0,
            'duplicates' => $duplicates,
        ];
    }

    /**
     * Validate widget uniqueness
     *
     * @param  string  $widgetClass  Widget class to check
     * @param  array  $config  Widget configuration
     * @param  array  $existingWidgets  Existing widget configurations
     * @return bool True if widget is unique
     */
    public function isUnique(string $widgetClass, array $config, array $existingWidgets): bool
    {
        $signature = $this->generateWidgetSignature($widgetClass, $config);

        foreach ($existingWidgets as $existingClass => $existingConfig) {
            if ($existingClass === $widgetClass) {
                continue; // Skip self
            }

            $existingSignature = $this->generateWidgetSignature($existingClass, $existingConfig);

            if ($signature === $existingSignature) {
                return false;
            }
        }

        return true;
    }
}
