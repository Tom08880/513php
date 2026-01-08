<?php
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | EcoStore</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 3px 3px 0 rgba(0,0,0,0.1);
        }
        
        .error-title {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .error-message {
            font-size: 1.125rem;
            margin-bottom: 2rem;
            line-height: 1.6;
            opacity: 0.9;
        }
        
        .error-details {
            background: rgba(255,255,255,0.1);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: left;
        }
        
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: white;
            color: #dc3545;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-secondary:hover {
            background: white;
            color: #dc3545;
        }
        
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .contact-info {
            margin-top: 2rem;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 1.5rem;
            }
            
            .error-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 200px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <div class="error-code">500</div>
        <h1 class="error-title">Internal Server Error</h1>
        <p class="error-message">
            Something went wrong on our end. We're working to fix the issue as soon as possible.
        </p>
        <div class="error-details">
            <p><strong>What you can do:</strong></p>
            <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
                <li>Try refreshing the page</li>
                <li>Wait a few minutes and try again</li>
                <li>Clear your browser cache and cookies</li>
                <li>Contact support if the problem persists</li>
            </ul>
        </div>
        <div class="error-actions">
            <a href="javascript:location.reload()" class="btn">Refresh Page</a>
            <a href="/" class="btn btn-secondary">Go to Homepage</a>
            <a href="/contact.php" class="btn btn-secondary">Contact Support</a>
        </div>
        <div class="contact-info">
            <p>If this error continues, please contact our support team at support@ecostore.com</p>
        </div>
    </div>
</body>
</html>