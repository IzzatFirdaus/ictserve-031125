<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Livewire\Dashboard\WidgetCustomizationPanel;
use App\Models\User;

try {
    echo "Creating user...\n";
    $user = User::factory()->create(['role' => 'admin']);
    echo 'User created with ID: '.$user->id."\n";

    echo "Creating component...\n";
    $component = new WidgetCustomizationPanel;
    echo "Component created successfully!\n";

    echo "Testing mount method...\n";
    auth()->login($user);
    $component->mount();
    echo "Mount method executed successfully!\n";
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    echo 'File: '.$e->getFile().':'.$e->getLine()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}
