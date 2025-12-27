<?php

$content = file_get_contents('test_results_2_utf8.txt');

// Extract test summary at the end
if (preg_match('/Tests:\s+(\d+)\s+passed.*?(\d+)\s+failed/s', $content, $summary)) {
    echo "TEST SUMMARY:\n";
    echo "  Passed: {$summary[1]}\n";
    echo "  Failed: {$summary[2]}\n\n";
}

// Category failures by test suite
$categories = [
    'Unit Tests' => [],
    'Feature Tests' => [],
    'Integration Tests' => [],
];

// Get list of failed test classes
preg_match_all('/FAIL\s+(Tests\\\\(Unit|Feature)\\\\.*?)(?=\n|$)/m', $content, $failedClasses, PREG_SET_ORDER);

foreach ($failedClasses as $match) {
    $fullClass = trim($match[1]);
    $type = $match[2];
    
    if ($type === 'Unit') {
        $categories['Unit Tests'][] = $fullClass;
    } else {
        $categories['Feature Tests'][] = $fullClass;
    }
}

foreach ($categories as $cat => $items) {
    if (!empty($items)) {
        echo str_repeat('=', 80) . "\n";
        echo "$cat: " . count($items) . " failed\n";
        echo str_repeat('=', 80) . "\n";
        foreach ($items as $item) {
            echo "  - $item\n";
        }
        echo "\n";
    }
}

// Extract specific error patterns
echo str_repeat('=', 80) . "\n";
echo "ERROR PATTERNS:\n";
echo str_repeat('=', 80) . "\n";

$errorPatterns = [
    'Missing Event Classes' => 'Class "App\\\\Events\\\\.*?" not found',
    'Type Errors' => 'TypeError.*?TicketNotificationService',
    'Database Issues' => 'SQLSTATE|QueryException',
    'Redis/Queue Issues' => 'Failed asserting.*?queue|Queue::|redis',
    'Assertion Failures' => 'Failed asserting',
];

foreach ($errorPatterns as $pattern => $regex) {
    if (preg_match_all("/$regex/i", $content, $matches)) {
        echo "\n$pattern: " . count($matches[0]) . " occurrences\n";
        if ($pattern === 'Missing Event Classes') {
            // Extract unique class names
            preg_match_all('/Class "([^"]+)" not found/', $content, $classes);
            $unique = array_unique($classes[1]);
            echo "  Missing classes:\n";
            foreach ($unique as $class) {
                echo "    - $class\n";
            }
        }
    }
}

