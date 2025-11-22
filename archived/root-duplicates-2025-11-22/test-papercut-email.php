<?php

declare(strict_types=1);

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;

// Test email configuration
$mailConfig = config('mail');

echo "=== PaperCut SMTP Email Test ===\n\n";
echo 'Mail Driver: '.$mailConfig['default']."\n";
echo 'Mail Host: '.$mailConfig['mailers']['smtp']['host']."\n";
echo 'Mail Port: '.$mailConfig['mailers']['smtp']['port']."\n";
echo 'From Address: '.$mailConfig['from']['address']."\n";
echo 'From Name: '.$mailConfig['from']['name']."\n\n";

try {
    // Test 1: Send a simple test email
    Mail::raw('This is a test email from ICTServe to PaperCut SMTP.', function ($message) {
        $message->to('test@example.com')
            ->subject('Test Email - PaperCut SMTP')
            ->from(config('mail.from.address'), config('mail.from.name'));
    });

    echo "✅ Test email sent successfully to PaperCut SMTP!\n";
    echo "   Recipient: test@example.com\n";
    echo "   Subject: Test Email - PaperCut SMTP\n\n";

    // Test 2: Send a Mailable (using a real notification email)
    $user = \App\Models\User::first();
    if ($user) {
        echo "✅ Found user: {$user->name} ({$user->email})\n\n";
    }

} catch (\Exception $e) {
    echo '❌ Error sending email: '.$e->getMessage()."\n";
    echo '   Code: '.$e->getCode()."\n";
    echo '   File: '.$e->getFile()."\n";
}
