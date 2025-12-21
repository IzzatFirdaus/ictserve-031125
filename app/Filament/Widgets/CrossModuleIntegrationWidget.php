<?php

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use Filament\Widgets\ChartWidget;

class CrossModuleIntegrationWidget extends ChartWidget
{
    use WidgetMetadata;

    /**
     * Documentation reference
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
