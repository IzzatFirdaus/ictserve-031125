<?php

/**
 * Debug script to see actual content being rendered
 */

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Request::create('/admin/login', 'GET');
$response = $kernel->handle($request);

$content = $response->getContent();

// Look for form elements
echo "=== FORM ELEMENTS ===\n";
if (preg_match('/<form[^>]*>.*?<\/form>/s', $content, $matches)) {
    echo "Form found:\n";
    echo substr($matches[0], 0, 500)."...\n\n";
} else {
    echo "No form found\n\n";
}

// Look for input elements
echo "=== INPUT ELEMENTS ===\n";
if (preg_match_all('/<input[^>]*>/i', $content, $matches)) {
    foreach ($matches[0] as $input) {
        if (str_contains($input, 'email') || str_contains($input, 'password')) {
            echo $input."\n";
        }
    }
} else {
    echo "No input elements found\n";
}

echo "\n=== LABELS ===\n";
if (preg_match_all('/<label[^>]*>.*?<\/label>/i', $content, $matches)) {
    foreach ($matches[0] as $label) {
        echo $label."\n";
    }
} else {
    echo "No labels found\n";
}

echo "\n=== TITLE/HEADING ===\n";
if (preg_match_all('/<h[1-6][^>]*>.*?<\/h[1-6]>/i', $content, $matches)) {
    foreach ($matches[0] as $heading) {
        echo $heading."\n";
    }
} else {
    echo "No headings found\n";
}

// Check if it's using Filament's default login
echo "\n=== FILAMENT INDICATORS ===\n";
echo "Contains 'filament': ".(str_contains($content, 'filament') ? 'YES' : 'NO')."\n";
echo "Contains 'fi-': ".(str_contains($content, 'fi-') ? 'YES' : 'NO')."\n";
echo "Contains 'livewire': ".(str_contains($content, 'livewire') ? 'YES' : 'NO')."\n";
