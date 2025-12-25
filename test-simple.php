<?php

echo "Simple PHP test works\n";
echo 'PHP Version: '.phpversion()."\n";
echo 'Current directory: '.getcwd()."\n";
echo "File exists check:\n";
echo '- bootstrap/app.php: '.(file_exists('bootstrap/app.php') ? 'YES' : 'NO')."\n";
echo '- vendor/autoload.php: '.(file_exists('vendor/autoload.php') ? 'YES' : 'NO')."\n";
echo '- .env: '.(file_exists('.env') ? 'YES' : 'NO')."\n";
