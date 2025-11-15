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
                ->label(__('widgets.filter_by_category'))
                ->options(AssetCategory::pluck('name_en', 'id')->toArray())
                ->placeholder('All Categories')
                ->reactive(),
            Select::make('viewMode')
                ->label(__('widgets.view_mode'))
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
            'categories' => AssetCategory::pluck('name', 'id')->toArray(),
        ];
    }

    protected function buildCalendarEvents(Collection $assets): array
    {
        $events = [];

        foreach ($assets as $asset) {
            $statusValue = $asset->status->value; // Convert enum to string
            $color = match ($statusValue) {
                'available' => 'green',
                'loaned' => 'yellow',
                'maintenance' => 'orange',
                'retired' => 'gray',
                'reserved' => 'yellow',
                'damaged' => 'red',
                default => 'blue',
            };

            if ($statusValue === 'loaned' && $asset->currentLoan) {
                $events[] = [
                    'id' => $asset->id,
                    'title' => $asset->name,
                    'start' => $asset->currentLoan->loan_start_date->format('Y-m-d'),
                    'end' => $asset->currentLoan->loan_end_date?->format('Y-m-d'),
                    'color' => $color,
                    'status' => $statusValue,
                    'category' => $asset->category?->name_en,
                ];
            } else {
                // Show current status as all-day event
                $events[] = [
                    'id' => $asset->id,
                    'title' => "{$asset->name} ({$statusValue})",
                    'start' => now()->format('Y-m-d'),
                    'color' => $color,
                    'status' => $statusValue,
                    'category' => $asset->category?->name_en,
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
            ['color' => 'yellow', 'label' => 'Reserved/Loaned', 'status' => 'reserved'],
            ['color' => 'orange', 'label' => 'Maintenance', 'status' => 'maintenance'],
            ['color' => 'red', 'label' => 'Damaged', 'status' => 'damaged'],
            ['color' => 'gray', 'label' => 'Retired', 'status' => 'retired'],
        ];
    }
}
