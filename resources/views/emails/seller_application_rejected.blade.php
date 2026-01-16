<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seller Application Rejected – Kutoot</title>
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
            background: #dc3545;
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .reason-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .reason-label {
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
        }
        .reason-text {
            color: #333;
        }
        .btn {
            display: inline-block;
            background: #ff6b35;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Application Status Update</h1>
        <p>Your seller application could not be approved</p>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $storeName }}</strong>,</p>
        
        <p>We regret to inform you that your seller application has been reviewed and could not be approved at this time.</p>
        
        <div class="reason-box">
            <div class="reason-label">Reason for Rejection:</div>
            <div class="reason-text">{{ $reason }}</div>
        </div>
        
        <p>If you believe this decision was made in error or if you have additional documents/information to support your application, you are welcome to reapply.</p>
        
        <p>For any questions or clarifications, please contact our support team.</p>
        
        <p>Best regards,<br>
        <strong>Kutoot Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply directly to this message.</p>
        <p>&copy; {{ date('Y') }} Kutoot. All rights reserved.</p>
    </div>
</body>
</html>

