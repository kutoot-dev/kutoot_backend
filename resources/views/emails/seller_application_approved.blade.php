<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seller Approved – Kutoot</title>
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
            background: linear-gradient(135deg, #ff6b35, #f7931e);
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
        .credentials-box {
            background: white;
            border: 2px solid #ff6b35;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-item {
            margin: 10px 0;
        }
        .credential-label {
            font-weight: bold;
            color: #666;
        }
        .credential-value {
            font-size: 18px;
            color: #333;
            font-family: monospace;
            background: #f0f0f0;
            padding: 5px 10px;
            border-radius: 4px;
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
        <h1>Congratulations!</h1>
        <p>Your seller application has been approved</p>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $storeName }}</strong>,</p>
        
        <p>We are pleased to inform you that your seller application has been approved. You can now access your Seller Panel using the credentials below:</p>
        
        <div class="credentials-box">
            <div class="credential-item">
                <span class="credential-label">Username:</span><br>
                <span class="credential-value">{{ $username }}</span>
            </div>
            <div class="credential-item">
                <span class="credential-label">Temporary Password:</span><br>
                <span class="credential-value">{{ $password }}</span>
            </div>
            <div class="credential-item">
                <span class="credential-label">Login URL:</span><br>
                <a href="{{ $loginUrl }}" style="color: #ff6b35;">{{ $loginUrl }}</a>
            </div>
        </div>
        
        <p><strong>Important:</strong> Please change your password after your first login for security purposes.</p>
        
        <center>
            <a href="{{ $loginUrl }}" class="btn">Login to Store Panel</a>
        </center>
        
        <p style="margin-top: 30px;">If you have any questions, please contact our support team.</p>
        
        <p>Best regards,<br>
        <strong>Kutoot Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply directly to this message.</p>
        <p>&copy; {{ date('Y') }} Kutoot. All rights reserved.</p>
    </div>
</body>
</html>

