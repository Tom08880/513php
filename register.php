<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EcoStore</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h1><i class="fas fa-user-plus"></i> Join EcoStore</h1>
                    <p>Create your sustainable shopping account</p>
                </div>

                <div class="auth-form">
                    <p>To register for EcoStore, please complete our subscription form:</p>
                    
                    <a href="https://tom888.infinityfree.me/register/" 
                       target="_blank" 
                       class="btn btn-primary btn-auth">
                        <i class="fas fa-external-link-alt"></i> Complete Registration Form
                    </a>
                    
                    <p class="form-hint">This will open in a new tab. After submission, you can return here to login.</p>
                </div>

                <div class="auth-footer">
                    <p>Already have an account? <a href="<?php echo BASE_URL; ?>auth/login.php">Sign in here</a></p>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>