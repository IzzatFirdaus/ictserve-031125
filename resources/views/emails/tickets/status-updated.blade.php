<!DOCTYPE html>
<html>
<head>
    <title>Helpdesk Ticket Status Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: #f4f4f4; padding: 10px; text-align: center; border-bottom: 1px solid #ddd; }
        .content { padding: 20px; }
        .footer { margin-top: 20px; font-size: 12px; text-align: center; color: #777; }
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 4px; color: white; font-weight: bold; }
        .status-open { background-color: #17a2b8; }
        .status-in_progress { background-color: #ffc107; color: #333; }
        .status-resolved { background-color: #28a745; }
        .status-closed { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Helpdesk Ticket Update</h2>
        </div>
        <div class="content">
            <p>Dear User,</p>
            
            <p>The status of your helpdesk ticket (<strong>{{ $ticket->ticket_number }}</strong>) has been updated.</p>
            
            <p><strong>New Status:</strong> <span class="status-badge status-{{ strtolower($ticket->status) }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></p>
            
            <p><strong>Ticket Details:</strong></p>
            <ul>
                <li><strong>Subject:</strong> {{ $ticket->subject }}</li>
                <li><strong>Category:</strong> {{ $ticket->category }}</li>
                <li><strong>Created:</strong> {{ $ticket->created_at->format('d M Y H:i') }}</li>
            </ul>

            <p>You can view the full conversation and updates by logging into the portal.</p>
            
            <p>Thank you,<br>ICTServe Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} ICTServe. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
