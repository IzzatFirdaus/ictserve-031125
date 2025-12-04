<?php

declare(strict_types=1);

/**
 * Translation Verification Script
 * Checks bilingual translation consistency per D15 requirements
 */
$enCommon = require __DIR__.'/../lang/en/common.php';
$msCommon = require __DIR__.'/../lang/ms/common.php';

echo "=== Translation Verification ===\n\n";

echo 'EN common.php keys: '.count($enCommon)."\n";
echo 'MS common.php keys: '.count($msCommon)."\n";

$missingInMs = array_diff(array_keys($enCommon), array_keys($msCommon));
$missingInEn = array_diff(array_keys($msCommon), array_keys($enCommon));

if (count($missingInMs) > 0) {
    echo "\nMissing in MS (first 10): ".implode(', ', array_slice($missingInMs, 0, 10))."\n";
} else {
    echo "\n✓ All EN keys exist in MS\n";
}

if (count($missingInEn) > 0) {
    echo 'Missing in EN (first 10): '.implode(', ', array_slice($missingInEn, 0, 10))."\n";
} else {
    echo "✓ All MS keys exist in EN\n";
}

// Check helpdesk translations
$enHelpdesk = require __DIR__.'/../lang/en/helpdesk.php';
$msHelpdesk = require __DIR__.'/../lang/ms/helpdesk.php';

echo "\nEN helpdesk.php keys: ".count($enHelpdesk)."\n";
echo 'MS helpdesk.php keys: '.count($msHelpdesk)."\n";

// Check loan translations
$enLoan = require __DIR__.'/../lang/en/loan.php';
$msLoan = require __DIR__.'/../lang/ms/loan.php';

echo "\nEN loan.php keys: ".count($enLoan)."\n";
echo 'MS loan.php keys: '.count($msLoan)."\n";

// Check auth translations
$enAuth = require __DIR__.'/../lang/en/auth.php';
$msAuth = require __DIR__.'/../lang/ms/auth.php';

echo "\nEN auth.php keys: ".count($enAuth)."\n";
echo 'MS auth.php keys: '.count($msAuth)."\n";

echo "\n=== Verification Complete ===\n";
