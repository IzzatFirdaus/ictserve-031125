<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

try {
    $view = view('welcome');
    echo "View created successfully\n";

    $rendered = $view->render();
    echo "View rendered successfully\n";
    echo 'First 100 characters: '.substr($rendered, 0, 100)."\n";
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    echo 'File: '.$e->getFile()."\n";
    echo 'Line: '.$e->getLine()."\n";
}
