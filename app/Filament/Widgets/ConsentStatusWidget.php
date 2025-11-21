<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class ConsentStatusWidget extends ChartWidget
{
    protected ?string $heading = 'Status Persetujuan Privasi';

    protected function getData(): array
    {
        $accepted = User::whereNotNull('privacy_policy_accepted_at')->count();
        $pending = User::whereNull('privacy_policy_accepted_at')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Status Persetujuan',
                    'data' => [$accepted, $pending],
                    'backgroundColor' => ['#198754', '#ff8c00'],
                ],
            ],
            'labels' => ['Diterima', 'Belum Diterima'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
