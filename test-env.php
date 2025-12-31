<?php

// Test .env loading directly
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "Direct .env test:\n";
echo 'GOOGLE_GMAIL_ENABLED: '.($_ENV['GOOGLE_GMAIL_ENABLED'] ?? 'NOT FOUND')."\n";
echo 'GOOGLE_SERVICE_ACCOUNT_PATH: '.($_ENV['GOOGLE_SERVICE_ACCOUNT_PATH'] ?? 'NOT FOUND')."\n";
echo 'GOOGLE_GMAIL_USER_EMAIL: '.($_ENV['GOOGLE_GMAIL_USER_EMAIL'] ?? 'NOT FOUND')."\n";

echo "\nUsing env() function:\n";
echo 'GOOGLE_GMAIL_ENABLED: '.(env('GOOGLE_GMAIL_ENABLED') ?? 'NOT FOUND')."\n";
echo 'GOOGLE_SERVICE_ACCOUNT_PATH: '.(env('GOOGLE_SERVICE_ACCOUNT_PATH') ?? 'NOT FOUND')."\n";
echo 'GOOGLE_GMAIL_USER_EMAIL: '.(env('GOOGLE_GMAIL_USER_EMAIL') ?? 'NOT FOUND')."\n";
