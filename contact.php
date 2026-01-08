<?php
// File: contact.php

session_start();

// Include required files
require_once __DIR__ . '/includes/functions.php';

// Define BASE_URL if not defined
if (!defined('BASE_URL')) {
    define('BASE_URL', '/sanshang/513week7/');
}

// Process form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $topic = trim($_POST['topic'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Basic validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($topic)) {
        $errors[] = "Please select a topic";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    if (empty($errors)) {
        // Simulate email sending (in real app, use mail() or PHPMailer)
        $to = "support@ecostore.com";
        $subject = "Contact Form: " . htmlspecialchars($topic);
        $email_body = "
        New contact form submission:
        
        Name: " . htmlspecialchars($name) . "
        Email: " . htmlspecialchars($email) . "
        Topic: " . htmlspecialchars($topic) . "
        Message: " . htmlspecialchars($message) . "
        
        Submitted on: " . date('Y-m-d H:i:s');
        
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Log the email (instead of actually sending for now)
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'to' => $to,
            'from' => $email,
            'subject' => $subject,
            'message' => $email_body
        ];
        
        $log_file = __DIR__ . '/logs/contact_logs.json';
        
        // Create logs directory if it doesn't exist
        if (!file_exists(dirname($log_file))) {
            mkdir(dirname($log_file), 0755, true);
        }
        
        $logs = [];
        if (file_exists($log_file)) {
            $logs = json_decode(file_get_contents($log_file), true) ?: [];
        }
        
        $logs[] = $log_entry;
        
        // Keep only last 50 entries
        if (count($logs) > 50) {
            $logs = array_slice($logs, -50);
        }
        
        file_put_contents($log_file, json_encode($logs, JSON_PRETTY_PRINT));
        
        // Set success message
        $_SESSION['success'] = "Thank you, " . htmlspecialchars($name) . "! Your message has been sent successfully.";
        
        // Redirect to clear POST data
        header("Location: " . BASE_URL . "contact.php");
        exit();
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}

// Display success/error messages from session
if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - EcoStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --green: #2d5a27;
            --light-green: #f0f7f0;
            --white: #fff;
            --dark-green: #1e4023;
        }

        /* Contact Layout */
        .contact-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin: 2rem auto;
        }

        @media (max-width: 768px) {
            .contact-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Form */
        .form-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(45, 90, 39, 0.1);
        }

        .form-card h2 {
            color: var(--green);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #444;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e7e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: 0.3s;
            font-family: inherit;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(45, 90, 39, 0.1);
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn {
            background: var(--green);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn:hover {
            background: var(--dark-green);
            transform: translateY(-2px);
        }

        /* Contact Info */
        .contact-info {
            display: grid;
            gap: 1rem;
        }

        .info-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(45, 90, 39, 0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .info-card:hover {
            transform: translateY(-5px);
        }

        .info-card i {
            font-size: 2rem;
            color: var(--green);
            margin-bottom: 1rem;
        }

        .info-card h3 {
            margin-bottom: 0.5rem;
            color: #444;
        }

        .info-card p {
            color: #666;
            margin: 0.3rem 0;
        }

        .info-card a {
            color: var(--green);
            text-decoration: none;
            font-weight: 500;
        }

        .info-card a:hover {
            text-decoration: underline;
        }

        /* Stats */
        .stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--green);
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #666;
        }

        /* Hero */
        .hero {
            background: linear-gradient(rgba(45, 90, 39, 0.9), rgba(74, 124, 69, 0.9));
            color: white;
            text-align: center;
            padding: 4rem 1rem;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Message Box */
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem auto;
            text-align: center;
            max-width: 800px;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success {
            background: #e8f5e9;
            color: #2d5a27;
            border: 1px solid #c8e6c9;
        }
        
        .error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/includes/header.php'; ?>

    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <h1>Get in Touch</h1>
            <p>Our green team is here to help with all your eco-friendly needs</p>
            <div class="stats">
                <div class="stat">
                    <span class="stat-number">24</span>
                    <span class="stat-label">Hour Response</span>
                </div>
                <div class="stat">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Eco-Trained</span>
                </div>
                <div class="stat">
                    <span class="stat-number">10K+</span>
                    <span class="stat-label">Happy Customers</span>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- Display success/error messages -->
        <?php if ($success_message): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="contact-layout">
            <!-- Contact Form -->
            <div class="form-card">
                <h2><i class="fas fa-envelope"></i> Send Message</h2>
                <p style="color: #666; margin-bottom: 1.5rem;">We'll get back to you within 24 hours</p>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Your Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your name" required
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your.email@example.com" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="topic"><i class="fas fa-tag"></i> Topic</label>
                        <select id="topic" name="topic" required>
                            <option value="">Select a topic</option>
                            <option value="products" <?php echo (isset($_POST['topic']) && $_POST['topic'] == 'products') ? 'selected' : ''; ?>>Product Questions</option>
                            <option value="sustainability" <?php echo (isset($_POST['topic']) && $_POST['topic'] == 'sustainability') ? 'selected' : ''; ?>>Sustainability</option>
                            <option value="orders" <?php echo (isset($_POST['topic']) && $_POST['topic'] == 'orders') ? 'selected' : ''; ?>>Order Help</option>
                            <option value="feedback" <?php echo (isset($_POST['topic']) && $_POST['topic'] == 'feedback') ? 'selected' : ''; ?>>Feedback</option>
                            <option value="other" <?php echo (isset($_POST['topic']) && $_POST['topic'] == 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message"><i class="fas fa-comment"></i> Your Message</label>
                        <textarea id="message" name="message" placeholder="How can we help you today?" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
            
            <!-- Contact Info -->
            <div class="contact-info">
                <div class="info-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Email Support</h3>
                    <p>General inquiries:</p>
                    <a href="mailto:hello@ecostore.com">hello@ecostore.com</a>
                    <p style="margin-top: 1rem;">Support:</p>
                    <a href="mailto:support@ecostore.com">support@ecostore.com</a>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-phone"></i>
                    <h3>Call Us</h3>
                    <p>Monday - Friday</p>
                    <p>9 AM - 6 PM EST</p>
                    <a href="tel:+15551234567">+1 (555) 123-4567</a>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Visit Us</h3>
                    <p>123 Green Street</p>
                    <p>EcoVille, EV 12345</p>
                    <a href="#" onclick="getDirections()">Get Directions</a>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-clock"></i>
                    <h3>Business Hours</h3>
                    <p><strong>Store:</strong> Mon-Fri 10am-7pm</p>
                    <p><strong>Support:</strong> 24/7 via email</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Get directions
    function getDirections() {
        const address = "123 Green Street, EcoVille, EV 12345";
        window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`, '_blank');
        return false;
    }

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const requiredFields = this.querySelectorAll('[required]');
                
                // Reset styles
                requiredFields.forEach(field => {
                    field.style.borderColor = '#e0e7e0';
                });
                
                // Check required fields
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.style.borderColor = '#c62828';
                    }
                });
                
                // Email validation
                const emailField = document.getElementById('email');
                if (emailField.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value)) {
                    isValid = false;
                    emailField.style.borderColor = '#c62828';
                }
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields correctly.');
                }
            });
        }
        
        // Auto-focus first field
        const nameInput = document.getElementById('name');
        if (nameInput) {
            nameInput.focus();
        }
    });
    
    // Auto-hide messages after 5 seconds
    setTimeout(() => {
        const messages = document.querySelectorAll('.message');
        messages.forEach(msg => {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(() => {
                if (msg.parentElement) {
                    msg.remove();
                }
            }, 500);
        });
    }, 5000);
    </script>

    <!-- Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>