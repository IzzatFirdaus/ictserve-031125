<?php

declare(strict_types=1);

namespace App\Livewire\Assets;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\LoanApplication;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Asset Availability Calendar Component
 *
 * Interactive calendar showing asset booking status and availability.
 *
 * @trace Requirements 2.3, 7.1
 *
 * @see D03-FR-018.3 Asset availability tracking
 * @see D04 §3.2 Livewire components
 */
class AssetAvailabilityCalendar extends Component
{
    public ?int $assetId = null;

    public ?int $categoryId = null;

    public string $currentMonth;

    public string $currentYear;

    public array $calendarData = [];

    public function mount(?int $assetId = null, ?int $categoryId = null): void
    {
        $this->assetId = $assetId;
        $this->categoryId = $categoryId;
        $this->currentMonth = now()->format('m');
        $this->currentYear = now()->format('Y');
        $this->loadCalendarData();
    }

    public function previousMonth(): void
    {
        $date = Carbon::createFromFormat('Y-m', "{$this->currentYear}-{$this->currentMonth}")->subMonth();
        $this->currentMonth = $date->format('m');
        $this->currentYear = $date->format('Y');
        $this->loadCalendarData();
    }

    public function nextMonth(): void
    {
        $date = Carbon::createFromFormat('Y-m', "{$this->currentYear}-{$this->currentMonth}")->addMonth();
        $this->currentMonth = $date->format('m');
        $this->currentYear = $date->format('Y');
        $this->loadCalendarData();
    }

    public function loadCalendarData(): void
    {
        $startDate = Carbon::createFromFormat('Y-m-d', "{$this->currentYear}-{$this->currentMonth}-01");
        $endDate = $startDate->copy()->endOfMonth();

        // Get assets based on filters
        $assetsQuery = Asset::query()
            ->with(['loanItems.loanApplication'])
            ->where('status', '!=', AssetStatus::RETIRED);

        if ($this->assetId) {
            $assetsQuery->where('id', $this->assetId);
        }

        if ($this->categoryId) {
            $assetsQuery->where('category_id', $this->categoryId);
        }

        $assets = $assetsQuery->get();

        // Get loan applications for the month
        $loanApplications = LoanApplication::query()
            ->with(['loanItems.asset'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->whereIn('status', ['approved', 'active'])
            ->get();

        // Build calendar data
        $this->calendarData = $this->buildCalendarGrid($startDate, $endDate, $assets, $loanApplications);
    }

    private function buildCalendarGrid(Carbon $startDate, Carbon $endDate, Collection $assets, Collection $loanApplications): array
    {
        $calendar = [];
        $currentDate = $startDate->copy();

        // Add empty days at the start of the month
        $startDayOfWeek = $currentDate->dayOfWeek;
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $calendar[] = [
                'date' => null,
                'isEmpty' => true,
            ];
        }

        // Add days of the month
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');

            // Count available and loaned assets for this date
            $availableCount = 0;
            $loanedCount = 0;
            $maintenanceCount = 0;

            foreach ($assets as $asset) {
                $isLoaned = $loanApplications->contains(function ($loan) use ($asset, $dateStr) {
                    return $loan->loanItems->contains(function ($item) use ($asset, $dateStr, $loan) {
                        return $item->asset_id === $asset->id
                            && $loan->start_date <= $dateStr
                            && $loan->end_date >= $dateStr;
                    });
                });

                if ($isLoaned) {
                    $loanedCount++;
                } elseif ($asset->status === AssetStatus::MAINTENANCE) {
                    $maintenanceCount++;
                } elseif ($asset->status === AssetStatus::AVAILABLE) {
                    $availableCount++;
                }
            }

            $calendar[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day' => $currentDate->day,
                'isEmpty' => false,
                'isToday' => $currentDate->isToday(),
                'isPast' => $currentDate->isPast(),
                'availableCount' => $availableCount,
                'loanedCount' => $loanedCount,
                'maintenanceCount' => $maintenanceCount,
                'totalAssets' => $assets->count(),
            ];

            $currentDate->addDay();
        }

        return $calendar;
    }

    public function render(): View
    {
        return view('livewire.assets.asset-availability-calendar', [
            'monthName' => Carbon::createFromFormat('Y-m', "{$this->currentYear}-{$this->currentMonth}")->format('F Y'),
        ]);
    }
}
