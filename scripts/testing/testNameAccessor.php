<?php

require __DIR__.'/../vendor/autoload.php';

use App\Models\User;

$user = new User;
$user->setRawAttributes(['name' => ['en' => 'Alice', 'ms' => 'Alicia']], true);
var_dump($user->name);

echo PHP_EOL;
