<?php

// Boot Laravel and check that the Theme_Toggle_Implementation entity exists
declare(strict_types=1);
$autoload = __DIR__.'/../vendor/autoload.php';
if (! file_exists($autoload)) {
    echo "autoload not found\n";
    exit(1);
}
require $autoload;
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
try {
    $kernel->bootstrap();
    $exists = \App\Models\MemoryEntity::where('name', 'Theme_Toggle_Implementation_2025-12-08')->exists();
    echo $exists ? "1\n" : "0\n";
} catch (\Throwable $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
    exit(1);
}
