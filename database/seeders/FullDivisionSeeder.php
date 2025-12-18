<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

/**
 * Full Division Seeder
 *
 * Loads the MOTAC canonical division listing from `database/data/motac_divisions.csv`.
 * trace: SRS-FR-009; D04 §3.5; author: ictserve-team
 */
class FullDivisionSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('data/motac_divisions.csv');

        if (! file_exists($file)) {
            $this->command->warn('⚠️ motac_divisions.csv not found, skipping FullDivisionSeeder');

            return;
        }

        if (($handle = fopen($file, 'r')) === false) {
            $this->command->error('Unable to open motac_divisions.csv');

            return;
        }

        $header = fgetcsv($handle);

        if ($header === false) {
            fclose($handle);
            $this->command->error('Unable to read header row from motac_divisions.csv');

            return;
        }

        $headerColumns = array_map(static fn (?string $value): string => (string) $value, $header);

        while (($row = fgetcsv($handle)) !== false) {
            // Decode HTML entities if present
            $row = array_map(fn($value) => html_entity_decode($value ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'), $row);
            
            if (count($row) !== count($headerColumns)) {
                // Skip malformed rows and warn
                $this->command->warn('Skipping malformed CSV row: '.implode(',', $row));

                continue;
            }

            $cols = array_combine($headerColumns, $row);
            Division::updateOrCreate([
                'code' => $cols['code'],
            ], [
                'name_ms' => $cols['name_ms'] ?? $cols['name_en'],
                'name_en' => $cols['name_en'] ?? $cols['name_ms'],
                'description_ms' => $cols['description_ms'] ?? null,
                'description_en' => $cols['description_en'] ?? null,
                'is_active' => filter_var($cols['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ]);
        }

        fclose($handle);

        $this->command->info('✓ Full MOTAC divisions seeded/updated');
    }
}
