<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Increase content column size to support large imported markdown files
        // Use a raw SQL statement to modify column type to avoid requiring doctrine/dbal in the runtime image.
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \DB::statement('ALTER TABLE memory_observations MODIFY COLUMN content LONGTEXT NOT NULL');
        }
    }

    public function down(): void
    {
        // Revert to TEXT
        // Revert back to TEXT (may truncate very long observations)
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \DB::statement('ALTER TABLE memory_observations MODIFY COLUMN content TEXT NOT NULL');
        }
    }
};
