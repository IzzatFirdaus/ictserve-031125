<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--path=backups}';

    protected $description = 'Create a database backup';

    public function handle(): int
    {
        $filename = 'ictserve_backup_'.date('Y-m-d_H-i-s').'.sql';
        $backupPath = storage_path('app/'.$this->option('path'));

        if (! is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $fullPath = $backupPath.'/'.$filename;

        $this->info('Creating database backup...');

        try {
            $backup = $this->generateBackup();
            file_put_contents($fullPath, $backup);

            $this->info("Backup created successfully: {$filename}");
            $this->info("Location: {$fullPath}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Backup failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function generateBackup(): string
    {
        $backup = "-- ICTServe Database Backup\n";
        $backup .= '-- Generated: '.date('Y-m-d H:i:s')."\n\n";

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');

        foreach ($tables as $table) {
            $tableName = $table->{"Tables_in_{$dbName}"};

            // Get table structure
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
            $backup .= "-- Table: {$tableName}\n";
            $backup .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $backup .= $createTable->{'Create Table'}.";\n\n";

            // Get table data
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $backup .= "-- Data for table: {$tableName}\n";
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        return $value === null ? 'NULL' : "'".addslashes((string) $value)."'";
                    }, (array) $row);

                    $backup .= "INSERT INTO `{$tableName}` VALUES (".implode(', ', $values).");\n";
                }
                $backup .= "\n";
            }
        }

        return $backup;
    }
}
