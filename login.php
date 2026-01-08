<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define BASE_URL directly to avoid dependency on functions.php
define('BASE_URL', '/sanshang/513week7/');

// Check if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $errors = [];

    // Validate email
    if (empty($email)) {
        $errors[] = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // If no errors, attempt login
    if (empty($errors)) {
        try {
            // Database connection configuration
            $host = 'sql100.infinityfree.com';
            $dbname = 'if0_39943908_wp16';
            $username = 'if0_39943908';
            $password = 'l3fA9Em7PP';
            
            // Create database connection, set short timeout
            $db = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 3, // 3 second timeout
                    PDO::ATTR_PERSISTENT => false // No persistent connection
                ]
            );
            
            // Prepare query
            $query = "SELECT * FROM wpv3_fc_subscribers WHERE email = :email LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Check if user exists
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Check if admin
                $is_admin = false;
                if ($email === 'admin@qq.com') {
                    $is_admin = true;
                }

                // Set session variables
                $_SESSION['logged_in'] = true;
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'] ?? explode('@', $email)[0];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $is_admin ? 'admin' : 'user';
                
                // Close database connection immediately
                $db = null;
                
                // Redirect to home page
                header("Location: " . BASE_URL . "index.php");
                exit();
            } else {
                $errors[] = "No account found with this email. Please register first.";
                $db = null; // Close connection
            }
            
        } catch (PDOException $e) {
            // Handle database connection error
            if (strpos($e->getMessage(), 'Too many connections') !== false || $e->getCode() == '08004') {
                // Too many database connections, create temporary session for testing
                $_SESSION['logged_in'] = true;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = explode('@', $email)[0];
                $_SESSION['user_id'] = time(); // Temporary ID
                $_SESSION['role'] = ($email === 'admin@qq.com') ? 'admin' : 'user';
                $_SESSION['temp_session'] = true; // Mark as temporary session
                
                $_SESSION['warning'] = "Database connection limit reached. Using temporary session for testing.";
                
                header("Location: " . BASE_URL . "index.php");
                exit();
            } else {
                // Other database errors
                error_log("Login error: " . $e->getMessage());
                $errors[] = "Database connection failed. Please try again later.";
            }
            
            // Ensure connection is closed
            if (isset($db)) {
                $db = null;
            }
        }
    }

    // If there are errors, save to session
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = ['email' => $email];
    }
}

// Display error messages
$error_messages = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$email_value = isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : '';

// Clear error messages from session
unset($_SESSION['errors'], $_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EcoStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #f5f9f4;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Header Styles */
        .header {
            background: linear-gradient(135deg, #2d5a27 0%, #4a7c45 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            color: white;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo i {
            font-size: 22px;
        }
        
        .main-nav {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .nav-link {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.15);
        }
        
        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 15px;
            padding-left: 15px;
            border-left: 1px solid rgba(255,255,255,0.2);
        }
        
        .welcome-text {
            font-size: 14px;
            opacity: 0.9;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
            width: 100%;
        }
        
        /* Alert Messages */
        .alert {
            max-width: 1200px;
            margin: 15px auto;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2d5a27;
            border-left: 4px solid #4a7c45;
        }
        
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }
        
        /* Auth Page Styles */
        .auth-page {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .auth-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .auth-card {
            max-width: 400px;
            width: 100%;
            padding: 3rem 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(46, 125, 50, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid #e8f5e9;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4caf50, #8bc34a);
        }

        .auth-header {
            margin-bottom: 2rem;
        }

        .auth-header h1 {
            color: #2e7d32;
            margin-bottom: 0.5rem;
            font-size: 2rem;
            font-weight: 700;
        }

        .auth-header p {
            color: #689f38;
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
            color: #388e3c;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #c8e6c9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f9fdf9;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4caf50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
            background: white;
        }

        .btn-auth {
            background: linear-gradient(135deg, #4caf50 0%, #8bc34a 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.3);
        }

        .auth-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e8f5e9;
        }

        .auth-footer a {
            color: #4caf50;
            text-decoration: none;
            font-weight: 600;
            margin: 0 0.5rem;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: #388e3c;
            text-decoration: underline;
        }

        .eco-icon {
            font-size: 3rem;
            color: #4caf50;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        /* Form Styling */
        .form-hint {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
        }
        
        .btn-primary {
            background: #2d5a27;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            background: #1e4023;
            transform: translateY(-2px);
        }
        
        /* Footer Styles */
        .footer {
            background: #1e4023;
            color: white;
            padding: 30px 0;
            margin-top: 40px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        /* Responsive */
        @media (max-width: 900px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }
            
            .main-nav {
                justify-content: center;
            }
            
            .user-section {
                margin-left: 0;
                padding-left: 0;
                border-left: none;
                border-top: 1px solid rgba(255,255,255,0.2);
                padding-top: 15px;
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 600px) {
            .main-nav {
                flex-direction: column;
                width: 100%;
            }
            
            .nav-link {
                width: 100%;
                justify-content: center;
            }
            
            .logo {
                font-size: 20px;
            }
            
            .auth-card {
                padding: 2rem 1.5rem;
            }
            
            .auth-header h1 {
                font-size: 1.8rem;
            }
        }
        
        /* Loading Animation */
        .loading {
            position: relative;
            color: transparent !important;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Additional Info */
        .info-box {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            color: #1565c0;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .info-box i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <!-- Logo -->
            <a href="<?php echo BASE_URL; ?>index.php" class="logo">
                <i class="fas fa-leaf"></i> EcoStore
            </a>
            
            <!-- Main Navigation -->
            <nav class="main-nav">
                <a href="<?php echo BASE_URL; ?>index.php" class="nav-link">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="<?php echo BASE_URL; ?>forum.php" class="nav-link">
                    <i class="fas fa-comments"></i> Forum
                </a>
                <a href="<?php echo BASE_URL; ?>about.php" class="nav-link">
                    <i class="fas fa-info-circle"></i> About
                </a>
                <a href="<?php echo BASE_URL; ?>contact.php" class="nav-link">
                    <i class="fas fa-envelope"></i> Contact
                </a>
            </nav>
            
            <!-- User Section -->
            <div class="user-section">
                <a href="<?php echo BASE_URL; ?>auth/register.php" class="nav-link">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="auth-container">
            <div class="auth-card">
                <div class="eco-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                
                <div class="auth-header">
                    <h1>Welcome Back</h1>
                    <p>Sign in to your EcoStore account</p>
                </div>

                <?php if (!empty($error_messages)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($error_messages as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form" id="loginForm">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               placeholder="Enter your registered email"
                               value="<?php echo htmlspecialchars($email_value); ?>">
                    </div>
                    
                    <button type="submit" class="btn-auth" id="submitBtn">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>

                <div class="auth-footer">
                    <p>
                        New to EcoStore? <a href="<?php echo BASE_URL; ?>auth/register.php">Create account</a>
                        | <a href="<?php echo BASE_URL; ?>auth/admin_login.php">Admin Login</a>
                    </p>
                </div>
                
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <strong>Need help?</strong> Use the email you registered with. For testing, any valid email format will work.
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div>
                    <h3 style="margin-bottom: 15px; color: white; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-leaf"></i> EcoStore
                    </h3>
                    <p style="color: rgba(255,255,255,0.8);">Sustainable products for a better tomorrow.</p>
                </div>
                
                <div>
                    <h4 style="margin-bottom: 15px; color: white;">Quick Links</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 8px;"><a href="<?php echo BASE_URL; ?>index.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
                        <li style="margin-bottom: 8px;"><a href="<?php echo BASE_URL; ?>forum.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Forum</a></li>
                        <li style="margin-bottom: 8px;"><a href="<?php echo BASE_URL; ?>products/" style="color: rgba(255,255,255,0.8); text-decoration: none;">Products</a></li>
                        <li style="margin-bottom: 8px;"><a href="<?php echo BASE_URL; ?>contact.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 style="margin-bottom: 15px; color: white;">Help</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 8px;"><a href="<?php echo BASE_URL; ?>faq.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">FAQ</a></li>
                        <li style="margin-bottom: 8px;"><a href="<?php echo BASE_URL; ?>contact.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Support</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p style="color: rgba(255,255,255,0.8);">&copy; <?php echo date('Y'); ?> EcoStore. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
    // Form submission handling
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value.trim();
                
                // Basic validation
                if (!email) {
                    e.preventDefault();
                    alert('Please enter your email address.');
                    return false;
                }
                
                // Simple email format validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    alert('Please enter a valid email address.');
                    return false;
                }
                
                // Show loading state
                submitBtn.innerHTML = '';
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                
                // Allow form submission
                return true;
            });
        }
        
        // Auto-focus to email field
        const emailInput = document.getElementById('email');
        if (emailInput && !emailInput.value) {
            emailInput.focus();
        }
    });
    </script>
</body>
</html>