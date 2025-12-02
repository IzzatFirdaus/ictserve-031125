<?php

namespace Tests\Unit;

use App\Services\WorkingDayCalculator;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WorkingDayCalculatorTest extends TestCase
{
    protected WorkingDayCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new WorkingDayCalculator;

        // Mock holidays
        Cache::shouldReceive('get')
            ->with('motac.public_holidays', [])
            ->andReturn([
                '2025-01-01' => 'New Year',
                '2025-05-01' => 'Labour Day',
            ])
            ->byDefault();
    }

    public function test_calculate_working_days_excludes_weekends_and_holidays()
    {
        // 2025-01-01 is Wed (Holiday)
        // 2025-01-02 is Thu (Working)
        // 2025-01-03 is Fri (Working)
        // 2025-01-04 is Sat (Weekend)
        // 2025-01-05 is Sun (Weekend)
        // 2025-01-06 is Mon (Working)

        $days = $this->calculator->calculateWorkingDays('2025-01-01', '2025-01-06');

        // Should be: Thu, Fri, Mon = 3 days
        $this->assertEquals(3, $days);
    }

    public function test_is_working_day()
    {
        $this->assertFalse($this->calculator->isWorkingDay('2025-01-01')); // Holiday
        $this->assertTrue($this->calculator->isWorkingDay('2025-01-02'));  // Thursday
        $this->assertFalse($this->calculator->isWorkingDay('2025-01-04')); // Saturday
    }

    public function test_get_next_available_date()
    {
        // Start from 2025-01-01 (Holiday)
        // Need 3 working days lead time.
        // 1. 2025-01-02 (Thu) - Working Day 1
        // 2. 2025-01-03 (Fri) - Working Day 2
        // 3. 2025-01-06 (Mon) - Working Day 3

        // So next available date should be 2025-01-06?
        // Or does it mean "after 3 days"?
        // If I submit on 1st, I can start on 6th?
        // Let's check implementation:
        // while addedDays < 3: addDay, if working, increment.
        // 1st -> add 1 day -> 2nd (Working) -> added=1
        // 2nd -> add 1 day -> 3rd (Working) -> added=2
        // 3rd -> add 1 day -> 4th (Weekend)
        // 4th -> add 1 day -> 5th (Weekend)
        // 5th -> add 1 day -> 6th (Working) -> added=3
        // Returns 6th.

        $date = $this->calculator->getNextAvailableDate('2025-01-01', 3);
        $this->assertEquals('2025-01-06', $date->format('Y-m-d'));
    }

    public function test_validate_lead_time()
    {
        // Submission: 2025-01-01
        // Min lead time: 3 days -> Earliest start: 2025-01-06

        $this->assertTrue($this->calculator->validateLeadTime('2025-01-01', '2025-01-06', 3));
        $this->assertFalse($this->calculator->validateLeadTime('2025-01-01', '2025-01-05', 3));
    }
}
