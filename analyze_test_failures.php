<?php

$content = file_get_contents('test_results_2_utf8.txt');

// Extract all test failures with their error messages
preg_match_all('/FAILED\s+(Tests\\\\.*?)\s+>\s+(.*?)\s+(.*?)(?=\n\s+FAILED|\n\s+PASS|\n\s+FAIL\s|\z)/s', $content, $matches, PREG_SET_ORDER);

$failures = [];
foreach ($matches as $match) {
    $class = trim($match[1]);
    $test = trim($match[2]);
    $error = trim($match[3]);
    
    // Extract just the error type/message
    if (preg_match('/(TypeError|QueryException|Error|Failed asserting|Exception|SQLSTATE).*$/s', $error, $errorMatch)) {
        $errorSummary = substr($errorMatch[0], 0, 200);
    } else {
        $errorSummary = substr($error, 0, 200);
    }
    
    $failures[] = [
        'class' => $class,
        'test' => $test,
        'error' => $errorSummary
    ];
}

// Group by error type
$grouped = [];
foreach ($failures as $failure) {
    if (strpos($failure['error'], 'TypeError') !== false) {
        $grouped['TypeError'][] = $failure;
    } elseif (strpos($failure['error'], 'QueryException') !== false || strpos($failure['error'], 'SQLSTATE') !== false) {
        $grouped['Database'][] = $failure;
    } elseif (strpos($failure['error'], 'Failed asserting') !== false) {
        $grouped['Assertion'][] = $failure;
    } elseif (strpos($failure['error'], 'Error') !== false) {
        $grouped['Error'][] = $failure;
    } else {
        $grouped['Other'][] = $failure;
    }
}

echo "TOTAL FAILURES: " . count($failures) . "\n\n";

foreach ($grouped as $type => $items) {
    echo str_repeat('=', 80) . "\n";
    echo "$type Failures: " . count($items) . "\n";
    echo str_repeat('=', 80) . "\n";
    foreach (array_slice($items, 0, 10) as $item) {
        echo "\nTest: {$item['class']} > {$item['test']}\n";
        echo "Error: {$item['error']}\n";
    }
    if (count($items) > 10) {
        echo "\n... and " . (count($items) - 10) . " more\n";
    }
    echo "\n";
}

