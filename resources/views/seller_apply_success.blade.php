<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted - Kutoot</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(180deg, #fff5f0 0%, #fff9f5 50%, #fffdf5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .success-card {
            background: white;
            border-radius: 30px;
            padding: 50px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 50px;
            color: white;
        }
        
        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
        }
        
        .application-id {
            background: #f8f9fa;
            padding: 15px 30px;
            border-radius: 12px;
            display: inline-block;
            margin: 20px 0;
        }
        
        .application-id label {
            font-size: 12px;
            color: #666;
            display: block;
            margin-bottom: 5px;
        }
        
        .application-id strong {
            font-size: 24px;
            color: #ff6b35;
            letter-spacing: 2px;
        }
        
        p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        
        .status-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .status-info h3 {
            color: #856404;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .status-info p {
            color: #856404;
            font-size: 13px;
            margin: 0;
        }
        
        .btn-home {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #ff6b35, #e55a2b);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">✓</div>
        
        <h1>Application Submitted!</h1>
        
        <div class="application-id">
            <label>Your Application ID</label>
            <strong>{{ $applicationId }}</strong>
        </div>
        
        <p>Thank you for applying to become a Kutoot seller. Your application has been received successfully.</p>
        
        <div class="status-info">
            <h3>What happens next?</h3>
            <p>Our team will review your application and contact you on your registered mobile number for verification. This usually takes 1-2 business days.</p>
        </div>
        
        <p>Please save your Application ID for future reference.</p>
        
        <a href="{{ url('/') }}" class="btn-home">Back to Home</a>
    </div>
</body>
</html>

