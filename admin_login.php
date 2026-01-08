<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $errors = [];

    if (empty($email) || empty($password)) {
        $errors[] = "Please enter both email and password.";
    }

    if (empty($errors)) {
        // Admin credentials
        $admin_email = 'admin@qq.com';
        $admin_password = '123456';
        
        if ($email === $admin_email && $password === $admin_password) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = 'Administrator';
            $_SESSION['user_id'] = md5($email);
            $_SESSION['role'] = 'admin';
            
            header("Location: " . BASE_URL . "admin/index.php");
            exit();
        } else {
            $errors[] = "Invalid admin credentials.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - EcoStore</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-auth-page {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .auth-container {
            max-width: 400px;
            width: 100%;
            padding: 0 1rem;
        }

        .admin-auth-card {
            background: linear-gradient(135deg, #2e7d32 0%, #388e3c 100%);
            color: white;
            padding: 3rem 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid #4caf50;
        }

        .admin-auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ffeb3b, #ffc107);
        }

        .auth-header {
            margin-bottom: 2rem;
        }

        .auth-header h1 {
            color: white;
            margin-bottom: 0.5rem;
            font-size: 2rem;
            font-weight: 700;
        }

        .auth-header p {
            color: #c8e6c9;
            font-size: 1rem;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #e8f5e9;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #4caf50;
            border-radius: 10px;
            font-size: 1rem;
            color: white;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-group input::placeholder {
            color: #a5d6a7;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ffeb3b;
            box-shadow: 0 0 0 3px rgba(255, 235, 59, 0.2);
            background: rgba(255, 255, 255, 0.15);
        }

        .btn-admin-auth {
            background: linear-gradient(135deg, #ffeb3b 0%, #ffc107 100%);
            color: #2e7d32;
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-admin-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 235, 59, 0.3);
        }

        .auth-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #4caf50;
        }

        .auth-footer a {
            color: #ffeb3b;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: #ffc107;
            text-decoration: underline;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            text-align: left;
        }

        .alert-error {
            background: rgba(255, 235, 238, 0.1);
            color: #ffcdd2;
            border: 1px solid rgba(255, 205, 210, 0.3);
        }

        .admin-icon {
            font-size: 3rem;
            color: #ffeb3b;
            margin-bottom: 1rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .security-badge {
            display: inline-block;
            background: rgba(255, 235, 59, 0.2);
            color: #ffeb3b;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 1rem;
            border: 1px solid rgba(255, 235, 59, 0.3);
        }

        .leaf-decoration {
            position: absolute;
            width: 100px;
            height: 100px;
            opacity: 0.1;
            z-index: 0;
            color: #ffeb3b;
        }

        .leaf-1 {
            top: -20px;
            right: -20px;
            transform: rotate(45deg);
        }

        .leaf-2 {
            bottom: -30px;
            left: -30px;
            transform: rotate(-20deg);
        }

        .form-content {
            position: relative;
            z-index: 1;
        }

        .eco-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            z-index: 0;
        }
    </style>
</head>
<body class="admin-auth-page">
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="main-content">
        <div class="auth-container">
            <div class="admin-auth-card">
                <div class="eco-pattern"></div>
                <div class="leaf-decoration leaf-1">
                    <i class="fas fa-leaf" style="font-size: 100px;"></i>
                </div>
                <div class="leaf-decoration leaf-2">
                    <i class="fas fa-leaf" style="font-size: 120px;"></i>
                </div>
                
                <div class="form-content">
                    <div class="admin-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    
                    <div class="auth-header">
                        <h1>Admin Access</h1>
                        <p>EcoStore Administrative Panel</p>
                    </div>

                    <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                        <div class="alert alert-error">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                            <?php unset($_SESSION['errors']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="auth-form">
                        <div class="form-group">
                            <label for="email">Admin Email</label>
                            <input type="email" id="email" name="email" required 
                                   placeholder="admin@qq.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Admin Password</label>
                            <input type="password" id="password" name="password" required 
                                   placeholder="Enter admin password">
                        </div>
                        
                        <button type="submit" class="btn btn-admin-auth">
                            <i class="fas fa-lock"></i> Admin Sign In
                        </button>
                    </form>

                    <div class="auth-footer">
                        <p><a href="<?php echo BASE_URL; ?>auth/login.php">← Back to User Login</a></p>
                    </div>

                    <div class="security-badge">
                        <i class="fas fa-leaf"></i> EcoStore Admin Portal
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>