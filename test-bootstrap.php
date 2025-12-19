<?php

echo "Starting Laravel bootstrap test...\n";

try {
    echo "1. Loading autoloader...\n";
    require_once 'vendor/autoload.php';
    echo "   ✓ Autoloader loaded\n";

    echo "2. Loading Laravel app...\n";
    $app = require_once 'bootstrap/app.php';
    echo "   ✓ Laravel app loaded\n";

    echo "3. Booting application...\n";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "   ✓ HTTP Kernel created\n";

    echo "4. Creating request...\n";
    $request = Illuminate\Http\Request::capture();
    echo "   ✓ Request created\n";

    echo "5. Handling request...\n";
    $response = $kernel->handle($request);
    echo "   ✓ Request handled successfully\n";
    echo '   Response status: '.$response->getStatusCode()."\n";
} catch (Exception $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
    echo 'File: '.$e->getFile()."\n";
    echo 'Line: '.$e->getLine()."\n";
    echo "Trace:\n".$e->getTraceAsString()."\n";
} catch (Error $e) {
    echo 'FATAL ERROR: '.$e->getMessage()."\n";
    echo 'File: '.$e->getFile()."\n";
    echo 'Line: '.$e->getLine()."\n";
}
