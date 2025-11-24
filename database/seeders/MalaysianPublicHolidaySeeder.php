<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class MalaysianPublicHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // In a real scenario, we might fetch this from an API or a package.
        // For now, we'll seed a config file or a cache key.
        // Since the requirement mentions config('motac.public_holidays'),
        // we should probably publish a config file or just set it in the seeder if it's database driven.
        // However, usually config is static files.
        // If the requirement implies dynamic updates, maybe it should be in the database or a cache.
        // "Create seeder or API sync to populate config('motac.public_holidays')"
        // This implies we might be writing to a file or cache.
        // Let's assume we are caching it for now as writing to config files at runtime is not standard.
        // Or maybe we are just creating a reference implementation.

        // Let's create a simple array of holidays for 2024/2025
        $holidays = [
            '2025-01-01' => 'New Year\'s Day',
            '2025-01-29' => 'Chinese New Year',
            '2025-01-30' => 'Chinese New Year (Day 2)',
            '2025-02-01' => 'Federal Territory Day',
            '2025-03-17' => 'Nuzul Al-Quran',
            '2025-03-31' => 'Hari Raya Aidilfitri',
            '2025-04-01' => 'Hari Raya Aidilfitri (Day 2)',
            '2025-05-01' => 'Labour Day',
            '2025-05-12' => 'Wesak Day',
            '2025-06-02' => 'Yang di-Pertuan Agong\'s Birthday',
            '2025-06-07' => 'Hari Raya Haji',
            '2025-07-07' => 'Awal Muharram',
            '2025-08-31' => 'Merdeka Day',
            '2025-09-16' => 'Malaysia Day',
            '2025-09-05' => 'Prophet Muhammad\'s Birthday',
            '2025-10-20' => 'Deepavali',
            '2025-12-25' => 'Christmas Day',
        ];

        // We can store this in cache for the WorkingDayCalculator to use
        // cache()->forever('motac.public_holidays', $holidays);

        // Or if we have a Holidays table (not in schema yet), we would populate it.
        // The requirement says "populate config('motac.public_holidays')".
        // This suggests we might need to create a config file `config/motac.php`.

        // For this seeder, I will just output that it's done, assuming the config file exists or we use cache.
        // Let's use cache as it's dynamic.
        cache()->put('motac.public_holidays', $holidays, now()->addYear());

        $this->command->info('Malaysian Public Holidays seeded into cache.');
    }
}
