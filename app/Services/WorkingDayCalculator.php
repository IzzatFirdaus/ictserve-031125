<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class WorkingDayCalculator
{
    /**
     * Calculate the number of working days between two dates.
     *
     * @param  Carbon|string  $startDate
     * @param  Carbon|string  $endDate
     */
    public function calculateWorkingDays($startDate, $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($start->gt($end)) {
            return 0;
        }

        $holidays = $this->getHolidays();
        $workingDays = 0;

        while ($start->lte($end)) {
            if ($this->isWorkingDay($start, $holidays)) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    /**
     * Check if a given date is a working day.
     *
     * @param  Carbon|string  $date
     */
    public function isWorkingDay($date, ?array $holidays = null): bool
    {
        $date = Carbon::parse($date);

        // Check if weekend (Saturday or Sunday)
        if ($date->isWeekend()) {
            return false;
        }

        // Check if public holiday
        $holidays = $holidays ?? $this->getHolidays();
        $dateString = $date->format('Y-m-d');

        return ! array_key_exists($dateString, $holidays);
    }

    /**
     * Get the next available working date after a given date (or today).
     *
     * @param  Carbon|string|null  $fromDate
     * @param  int  $daysToAdd  Number of working days to add
     */
    public function getNextAvailableDate($fromDate = null, int $daysToAdd = 3): Carbon
    {
        $date = $fromDate ? Carbon::parse($fromDate) : Carbon::now();
        $holidays = $this->getHolidays();
        $addedDays = 0;

        // If starting date is not a working day, move to next working day first?
        // Requirement says "minimum 3 working days lead time".
        // So we count 3 working days from "submission date".

        while ($addedDays < $daysToAdd) {
            $date->addDay();
            if ($this->isWorkingDay($date, $holidays)) {
                $addedDays++;
            }
        }

        return $date;
    }

    /**
     * Validate if the selected start date meets the minimum lead time requirement.
     *
     * @param  Carbon|string  $submissionDate
     * @param  Carbon|string  $selectedDate
     */
    public function validateLeadTime($submissionDate, $selectedDate, int $minLeadTimeDays = 3): bool
    {
        $submission = Carbon::parse($submissionDate);
        $selected = Carbon::parse($selectedDate);

        // Calculate working days between submission and selected date
        // Note: calculateWorkingDays includes start and end date if they are working days.
        // Lead time usually means "days from now".
        // If I submit on Monday, 3 working days lead time:
        // Tue (1), Wed (2), Thu (3). Earliest start date is Thursday? Or Friday?
        // Usually "3 days notice" means 3 full days.
        // Let's assume getNextAvailableDate logic is the source of truth.

        $minDate = $this->getNextAvailableDate($submission, $minLeadTimeDays);

        return $selected->gte($minDate);
    }

    /**
     * Get holidays from cache or config.
     */
    protected function getHolidays(): array
    {
        return Cache::get('motac.public_holidays', []);
    }
}
