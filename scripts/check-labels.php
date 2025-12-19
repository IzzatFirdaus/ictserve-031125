<?php

/**
 * Check for labels and headings in the rendered content
 */

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Request::create('/admin/login', 'GET');
$response = $kernel->handle($request);

$content = $response->getContent();

// Look for our specific text content
$searches = [
    'Log Masuk Pentadbir' => 'Admin Login Title',
    'Emel atau Nama Pengguna' => 'Flexible Login Label',
    'Kata Laluan' => 'Password Label',
    'Ingat saya' => 'Remember Me Label',
    'Sila log masuk untuk mengakses papan pemuka pentadbir' => 'Subheading',
    'nama@motac.gov.my atau nama' => 'Flexible Login Placeholder',
    'Anda boleh log masuk menggunakan emel penuh atau nama pengguna sahaja' => 'Helper Text',
];

echo "=== CONTENT SEARCH RESULTS ===\n";
foreach ($searches as $search => $description) {
    $found = str_contains($content, $search);
    echo sprintf("%-50s: %s\n", $description, $found ? '✓ FOUND' : '✗ NOT FOUND');
}

// Look for Filament-specific label structures
echo "\n=== FILAMENT LABEL STRUCTURES ===\n";
if (preg_match_all('/data-field-wrapper-label="[^"]*"/i', $content, $matches)) {
    echo 'Found '.count($matches[0])." field wrapper labels:\n";
    foreach ($matches[0] as $match) {
        echo '  '.$match."\n";
    }
} else {
    echo "No field wrapper labels found\n";
}

// Look for any text that might be our labels
echo "\n=== POTENTIAL LABELS ===\n";
if (preg_match_all('/>(Emel|Kata|Ingat|Log)[^<]*</i', $content, $matches)) {
    foreach ($matches[0] as $match) {
        echo $match."\n";
    }
} else {
    echo "No potential labels found\n";
}

// Check if our heading method is being called
echo "\n=== HEADING SEARCH ===\n";
$headingPatterns = [
    '/Log Masuk Pentadbir/',
    '/class="[^"]*heading[^"]*"[^>]*>[^<]*Log[^<]*</i',
    '/h[1-6][^>]*>[^<]*Log[^<]*</i',
];

foreach ($headingPatterns as $pattern) {
    if (preg_match($pattern, $content, $matches)) {
        echo 'Found heading pattern: '.$matches[0]."\n";
    }
}
