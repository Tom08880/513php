<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include functions
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// Include header
include __DIR__ . '/../includes/header.php';

// Display errors
if (isset($_SESSION['errors'])) {
    echo '<div class="container mt-3">';
    echo '<div class="alert alert-error">';
    foreach ($_SESSION['errors'] as $error) {
        echo '<p>' . $error . '</p>';
    }
    unset($_SESSION['errors']);
    echo '</div>';
    echo '</div>';
}
?>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><i class="fas fa-user-plus"></i> Join EcoStore</h1>
                <p>Create your sustainable shopping account</p>
            </div>

            <form class="auth-form" method="POST" action="<?php echo BASE_URL; ?>auth/process_register.php">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" required 
                               placeholder="John" value="<?php echo isset($_SESSION['form_data']['firstName']) ? escapeOutput($_SESSION['form_data']['firstName']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" required 
                               placeholder="Doe" value="<?php echo isset($_SESSION['form_data']['lastName']) ? escapeOutput($_SESSION['form_data']['lastName']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="your.email@example.com" value="<?php echo isset($_SESSION['form_data']['email']) ? escapeOutput($_SESSION['form_data']['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Create a strong password">
                    <div class="password-requirements">
                        <small>Must be at least 8 characters with uppercase, lowercase, and numbers</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required 
                           placeholder="Confirm your password">
                </div>
                
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="newsletter" checked> 
                        Send me eco-tips and sustainable living updates
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms" required> 
                        I agree to the <a href="<?php echo BASE_URL; ?>terms.php">Terms of Service</a> and <a href="<?php echo BASE_URL; ?>privacy.php">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-auth">Create Account</button>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="<?php echo BASE_URL; ?>login">Sign in here</a></p>
                </div>
            </form>
        </div>
        
        <div class="auth-benefits">
            <h3>Why Join EcoStore?</h3>
            <div class="benefits-list">
                <div class="benefit-item">
                    <i class="fas fa-leaf"></i>
                    <div>
                        <h4>Track Your Impact</h4>
                        <p>See how your purchases help the environment</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-award"></i>
                    <div>
                        <h4>Earn Rewards</h4>
                        <p>Get points for sustainable choices</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-shipping-fast"></i>
                    <div>
                        <h4>Fast Shipping</h4>
                        <p>Carbon-neutral delivery on all orders</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-heart"></i>
                    <div>
                        <h4>Exclusive Access</h4>
                        <p>First look at new sustainable products</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.auth-page {
    background: linear-gradient(135deg, #f0f7f0 0%, #e8f5e8 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding: 2rem 0;
}

.auth-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    padding: 0 2rem;
    align-items: center;
}

.auth-card {
    background: white;
    padding: 3rem;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-header h1 {
    color: #2d5a27;
    margin-bottom: 0.5rem;
    font-size: 2rem;
}

.auth-header p {
    color: #666;
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #333;
}

.form-group input {
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #2d5a27;
    box-shadow: 0 0 0 3px rgba(45, 90, 39, 0.1);
}

.password-requirements {
    margin-top: 0.5rem;
}

.password-requirements small {
    color: #666;
    font-size: 0.8rem;
}

.form-options {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.9rem;
    line-height: 1.4;
}

.checkbox-label input[type="checkbox"] {
    margin-top: 0.2rem;
}

.checkbox-label a {
    color: #2d5a27;
    text-decoration: none;
}

.checkbox-label a:hover {
    text-decoration: underline;
}

.btn-auth {
    padding: 1rem;
    font-size: 1.1rem;
    margin-top: 1rem;
}

.auth-footer {
    text-align: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #eee;
}

.auth-footer a {
    color: #2d5a27;
    font-weight: 600;
    text-decoration: none;
}

.auth-footer a:hover {
    text-decoration: underline;
}

.auth-benefits {
    background: rgba(255,255,255,0.9);
    padding: 2.5rem;
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

.auth-benefits h3 {
    color: #2d5a27;
    margin-bottom: 1.5rem;
    text-align: center;
    font-size: 1.5rem;
}

.benefits-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.benefit-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.benefit-item i {
    color: #2d5a27;
    font-size: 1.2rem;
    margin-top: 0.2rem;
    min-width: 20px;
}

.benefit-item h4 {
    color: #333;
    margin-bottom: 0.3rem;
    font-size: 1rem;
}

.benefit-item p {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.4;
    margin: 0;
}

@media (max-width: 768px) {
    .auth-container {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .auth-card {
        padding: 2rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .auth-benefits {
        padding: 2rem;
    }
}

.alert {
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<?php 
// Clear form data
unset($_SESSION['form_data']);
include __DIR__ . '/../includes/footer.php'; 
?>  