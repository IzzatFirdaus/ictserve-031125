<?php

// PHPUnit wrapper for VS Code
require_once 'C:/tmp/vendor/autoload.php';

// Change to project directory
chdir(__DIR__);

// Get command line arguments
$args = array_slice($argv, 1);

// Add default configuration if not specified
if (! in_array('--configuration', $args) && ! in_array('-c', $args)) {
    array_unshift($args, '--configuration', 'phpunit.xml');
}

// Run PHPUnit
$application = new PHPUnit\TextUI\Application;
exit($application->run($args));
