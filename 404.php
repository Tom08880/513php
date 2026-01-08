<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | EcoStore</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2d5a27 0%, #4a7c45 100%);
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
            color: #2d5a27;
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
            color: #2d5a27;
        }
        
        .eco-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
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
        <div class="eco-icon">🌿</div>
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
    </div>
</body>
</html>