<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $app_name }} - Gmail Integration Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #1e40af;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 30px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e2e8f0;
        }
        .success-badge {
            background-color: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
        }
        .info-box {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $app_name }}</h1>
        <p>Gmail API Integration Test</p>
    </div>
    
    <div class="content">
        <div class="success-badge">✅ Integration Successful</div>
        
        <h2>Test Message</h2>
        <p>{{ $message }}</p>
        
        <div class="info-box">
            <h3>Integration Details</h3>
            <ul>
                <li><strong>Application:</strong> {{ $app_name }}</li>
                <li><strong>Timestamp:</strong> {{ $timestamp }}</li>
                <li><strong>Transport:</strong> Gmail API</li>
                <li><strong>Status:</strong> Successfully delivered via Google API Client</li>
            </ul>
        </div>
        
        <p>This email confirms that your Gmail API integration is working correctly. The email was sent using the Google API Client library through Laravel's mail system.</p>
        
        <h3>Next Steps</h3>
        <ul>
            <li>Configure your Gmail API credentials</li>
            <li>Set up service account authentication (recommended for production)</li>
            <li>Test with your notification system</li>
            <li>Monitor email delivery metrics</li>
        </ul>
    </div>
    
    <div class="footer">
        <p>This is an automated test email from {{ $app_name }}.<br>
        Generated at {{ $timestamp }}</p>
    </div>
</body>
</html>