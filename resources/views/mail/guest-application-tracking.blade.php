<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Your Loan Application / Menjejaki Permohonan Pinjaman Anda</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
        }
        .content {
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .bilingual-divider {
            margin: 20px 0;
            border-top: 1px dashed #ccc;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">ICTServe</div>
        </div>
        
        <div class="content">
            <p>Dear {{ $application->applicant_name }},</p>
            
            <p>Thank you for your ICT Equipment Loan Application. We have received your request and it is currently being processed.</p>
            
            <p><strong>Application Number:</strong> {{ $application->application_number }}</p>
            
            <p>You can track the status of your application at any time by clicking the button below:</p>
            
            <div style="text-align: center;">
                <a href="{{ $trackingUrl }}" class="button">Track Application</a>
            </div>
            
            <div class="bilingual-divider"></div>
            
            <p>Kepada {{ $application->applicant_name }},</p>
            
            <p>Terima kasih atas Permohonan Pinjaman Peralatan ICT anda. Kami telah menerima permohonan anda dan ia sedang diproses.</p>
            
            <p><strong>Nombor Permohonan:</strong> {{ $application->application_number }}</p>
            
            <p>Anda boleh menjejaki status permohonan anda pada bila-bila masa dengan mengklik butang di bawah:</p>
            
            <div style="text-align: center;">
                <a href="{{ $trackingUrl }}" class="button">Jejak Permohonan</a>
            </div>
        </div>
        
        <div class="footer">
            <p>If you did not request this loan, please ignore this email.</p>
            <p>Sekiranya anda tidak membuat permohonan ini, sila abaikan emel ini.</p>
            <p>&copy; {{ date('Y') }} ICTServe. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
