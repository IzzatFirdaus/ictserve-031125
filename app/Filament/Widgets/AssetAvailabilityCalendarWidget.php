<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\AssetCategory;
use Filament\Forms\Components\Select;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

/**
 * Asset Availability Calendar Widget
 *
 * Displays asset availability in calendar view with color-coded status indicators.
 * Supports filtering by asset category and provides monthly/weekly views.
 *
 * @trace Requirements 2.5
 */
class AssetAvailabilityCalendarWidget extends Widget
{
    protected string $view = 'filament.widgets.asset-availability-calendar';

    protected int|string|array $columnSpan = 'full';

    public ?int $categoryFilter = null;

    public string $viewMode = 'month'; // 'month' or 'week'

    protected function getFormSchema(): array
    {
        return [
            Select::make('categoryFilter')
                ->label('Filter by Category')
                ->options(AssetCategory::pluck('name_en', 'id')->toArray())
                ->placeholder('All Categories')
                ->reactive(),
            Select::make('viewMode')
                ->label('View Mode')
                ->options([
                    'month' => 'Monthly View',
                    'week' => 'Weekly View',
                ])
                ->default('month')
                ->reactive(),
        ];
    }

    protected function getViewData(): array
    {
        $query = Asset::query()
            ->with(['currentLoan', 'category'])
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter));

        $assets = $query->get();

        $events = $this->buildCalendarEvents($assets);

        return [
            'events' => $events,
            'legend' => $this->getLegend(),
            'viewMode' => $this->viewMode,
            'categories' => AssetCategory::pluck('name_en', 'id')->toArray(),
        ];
    }

    protected function buildCalendarEvents(Collection $assets): array
    {
        $events = [];

        foreach ($assets as $asset) {
            $color = match ($asset->status) {
                'available' => 'green',
                'on_loan' => 'yellow',
                'maintenance' => 'red',
                'retired' => 'gray',
                default => 'blue',
            };

            if ($asset->status === 'on_loan' && $asset->currentLoan) {
                $events[] = [
                    'id' => $asset->id,
                    'title' => $asset->name,
                    'start' => $asset->currentLoan->loan_date->format('Y-m-d'),
                    'end' => $asset->currentLoan->expected_return_date?->format('Y-m-d'),
                    'color' => $color,
                    'status' => $asset->status,
                    'category' => $asset->category?->name_en,
                    'url' => route('filament.admin.resources.assets.assets.view', $asset),
                ];
            } else {
                // Show current status as all-day event
                $events[] = [
                    'id' => $asset->id,
                    'title' => "{$asset->name} ({$asset->status})",
                    'start' => now()->format('Y-m-d'),
                    'color' => $color,
                    'status' => $asset->status,
                    'category' => $asset->category?->name_en,
                    'url' => route('filament.admin.resources.assets.assets.view', $asset),
                    'allDay' => true,
                ];
            }
        }

        return $events;
    }

    protected function getLegend(): array
    {
        return [
            ['color' => 'green', 'label' => 'Available', 'status' => 'available'],
            ['color' => 'yellow', 'label' => 'On Loan', 'status' => 'on_loan'],
            ['color' => 'red', 'label' => 'Maintenance', 'status' => 'maintenance'],
            ['color' => 'gray', 'label' => 'Retired', 'status' => 'retired'],
        ];
    }
}
