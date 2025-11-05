<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('email', 'superuser@motac.gov.my')->first();
$user->password = Hash::make('password');
$user->save();

echo "Password reset for: {$user->email}\n";
