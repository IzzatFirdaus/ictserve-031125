<?php
declare(strict_types=1);

// This script bootstraps Laravel and drops the helpdesk_tickets table if exists.
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('DROP TABLE IF EXISTS helpdesk_tickets');
    echo "Dropped table 'helpdesk_tickets'\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

return 0;
