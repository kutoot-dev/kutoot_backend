<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OTP Verification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #FFFDF2; /* Main Background */
            padding: 20px;
            line-height: 1.6;
            color: #212529; /* Body Text */
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #2D1C10; /* Dark Accents */
            padding: 40px 30px;
            text-align: center;
        }

        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 20px;
        }

        .header-title {
            color: #ffffff;
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #3B322B; /* Primary Text */
            margin-bottom: 20px;
            font-weight: 500;
        }

        .message {
            font-size: 15px;
            color: #212529; /* Body Text */
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .otp-container {
            background: linear-gradient(135deg, #EA6B1E 0%, #C1272D 100%); /* Primary -> Secondary */
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }

        .otp-label {
            color: #ffffff;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .otp-code {
            font-size: 42px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 0;
        }

        .expiry-notice {
            background-color: #FFF4E9; /* subtle warm background */
            border-left: 4px solid #EA6B1E; /* Primary Brand */
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }

        .expiry-notice p {
            color: #3B322B; /* Primary Text */
            font-size: 14px;
            margin: 0;
        }

        .security-info {
            background-color: #FFFDF2; /* Main Background */
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .security-info h3 {
            color: #3B322B; /* Primary Text */
            font-size: 16px;
            margin-bottom: 10px;
        }

        .security-info p {
            color: #212529; /* Body Text */
            font-size: 14px;
            margin: 0;
        }

        .footer {
            background-color: #2D1C10; /* Dark Accents */
            padding: 30px;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.04);
        }

        .footer-text {
            color: #FFF6EE; /* light on dark */
            font-size: 13px;
            margin: 5px 0;
        }

        .footer-link {
            color: #31D7A9; /* Accent Teal */
            text-decoration: none;
        }

        .footer-link:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .header {
                padding: 30px 20px;
            }

            .content {
                padding: 30px 20px;
            }

            .header-title {
                font-size: 20px;
            }

            .otp-code {
                font-size: 36px;
                letter-spacing: 6px;
            }

            .greeting {
                font-size: 16px;
            }

            .message {
                font-size: 14px;
            }

            .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with Logo -->
        <div class="header">
            <img src="{{ $logo ? asset($logo) : asset('images/logo-white.png') }}" alt="Company Logo" class="logo">
            <h1 class="header-title">Verify Your Account</h1>
        </div>

        <!-- Main Content -->
        <div class="content">
            <p class="greeting">Hello{{ isset($name) ? ' ' . $name : '' }},</p>

            <p class="message">
                We received a request to verify your account. Please use the One-Time Password (OTP) below to complete your verification process.
            </p>

            <!-- OTP Code Box -->
            <div class="otp-container">
                <div class="otp-label">Your OTP Code</div>
                <h2 class="otp-code">{{ $otp }}</h2>
            </div>

            <!-- Expiry Notice -->
            <div class="expiry-notice">
                <p><strong>⏱️ Important:</strong> This code will expire in {{ $expiry ?? '10' }} minutes for security reasons.</p>
            </div>

            <!-- Security Information -->
            <div class="security-info">
                <h3>🔒 Security Tips</h3>
                <p>Never share this code with anyone. Our team will never ask for your OTP. If you didn't request this code, please ignore this email or contact our support team immediately.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">© {{ date('Y') }} {{ config('app.name', 'Your Company') }}. All rights reserved.</p>
            <p class="footer-text">
                Need help? <a href="mailto:{{ $supportEmail ?? 'support@example.com' }}" class="footer-link">Contact Support</a>
            </p>
            <p class="footer-text" style="margin-top: 15px;">
                This is an automated message, please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>

