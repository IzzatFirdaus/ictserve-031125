<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

$app = app();
$db = $app->make('db');

$count = $db->table('migrations')->count();
echo "Migrations table count: $count\n";

if ($count > 0) {
    $migrations = $db->table('migrations')->orderBy('batch', 'DESC')->limit(5)->get();
    echo "\nLast 5 migrations:\n";
    foreach ($migrations as $mig) {
        echo "  {$mig->migration} (Batch: {$mig->batch})\n";
    }
} else {
    echo "No migrations found!\n";
}
