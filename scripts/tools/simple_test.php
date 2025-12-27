<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Livewire\Dashboard\WidgetCustomizationPanel;
use App\Models\User;
use Livewire\Livewire;

try {
    echo "Setting up test environment...\n";

    // Create a user
    $user = User::factory()->create(['role' => 'admin']);
    echo 'User created with ID: '.$user->id."\n";

    // Authenticate the user
    auth()->login($user);
    echo "User authenticated\n";

    echo "Testing Livewire component...\n";

    // Test the component
    $component = Livewire::test(WidgetCustomizationPanel::class);
    echo "Livewire test component created\n";

    // Check if it can render
    $component->assertStatus(200);
    echo "Component rendered successfully with status 200\n";

    echo "All tests passed!\n";
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    echo 'File: '.$e->getFile().':'.$e->getLine()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}
