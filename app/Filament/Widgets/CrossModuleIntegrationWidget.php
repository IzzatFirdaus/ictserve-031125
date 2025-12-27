<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use Filament\Widgets\ChartWidget;

/**
 * Cross Module Integration Widget
 *
 * Displays integration metrics between Helpdesk and Asset Loan modules.
 * Shows correlation between ticket types and asset requests for system insights.
 *
 * @trace D03-FR-001, D03-FR-008 (Cross-module integration requirements)
 * @trace D04-§3.2 (Dashboard widgets architecture)
 * @trace D12-§6 (Dashboard UI/UX design patterns)
 *
 * @see \App\Filament\Traits\WidgetMetadata
 */
class CrossModuleIntegrationWidget extends ChartWidget
{
    use WidgetMetadata;

    /**
     * Documentation reference for WidgetMetadata trait.
     *
     * @return string
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D03 Cross-module integration';
    }

    protected ?string $heading = 'Cross Module Integration Widget';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
