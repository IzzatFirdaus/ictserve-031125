<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class CrossModuleIntegrationWidget extends ChartWidget
{
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
