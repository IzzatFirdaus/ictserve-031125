<!DOCTYPE html>
<html>
<head>
    <title>Your Notification Digest</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: #f4f4f4; padding: 10px; text-align: center; border-bottom: 1px solid #ddd; }
        .content { padding: 20px; }
        .footer { margin-top: 20px; font-size: 12px; text-align: center; color: #777; }
        .notification { padding: 15px; margin: 10px 0; border-left: 4px solid #007bff; background-color: #f8f9fa; }
        .notification-title { font-weight: bold; margin-bottom: 5px; }
        .notification-time { font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Your Notification Digest</h2>
            <p>{{ now()->format('l, F d, Y') }}</p>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p>Here's a summary of your notifications:</p>

            @foreach($notifications as $notification)
                <div class="notification">
                    <div class="notification-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                    <div>{{ $notification->data['message'] ?? $notification->data['body'] ?? 'No message content.' }}</div>
                    <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
            @endforeach

            <p>You have received a total of <strong>{{ $notifications->count() }}</strong> {{ \Illuminate\Support\Str::plural('notification', $notifications->count()) }}.</p>
            
            <p>You can manage your notification preferences in your profile settings.</p>
            
            <p>Thank you,<br>ICTServe Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} ICTServe. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
