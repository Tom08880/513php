<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/security.php';

// Prevent direct access to this page
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "contact.php");
    exit();
}

// Validate CSRF token
if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $errors[] = "Invalid request. Please try again.";
}

// Receive and filter form data
$name = trim($_POST['fullName'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');
$orderId = trim($_POST['orderId'] ?? '');

// Validate form data
$errors = [];
if (empty($name)) {
    $errors[] = "Name is required";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email is required";
}
if (empty($subject)) {
    $errors[] = "Subject is required";
}
if (empty($message)) {
    $errors[] = "Message content is required";
}

if (!empty($errors)) {
    // Store form data in session for repopulation
    $_SESSION['form_data'] = [
        'fullName' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'orderId' => $orderId
    ];
    $_SESSION['contact_errors'] = $errors;
    header("Location: " . BASE_URL . "contact.php");
    exit();
}

// Process email sending (simulated)
$emailSent = true; // In real application, this would be actual email sending logic

// Clear form data from session on success
if ($emailSent) {
    unset($_SESSION['form_data']);
    $_SESSION['contact_success'] = "Your message has been sent successfully!";
}

// Include header after processing
include __DIR__ . '/header.php';
?>

<style>
.contact-result-page {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.result-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.result-header h1 {
    color: #2c3e50;
    font-size: 1.8rem;
}

.success-message, .error-message {
    padding: 1.2rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.success-message {
    background: #eafaf1;
    color: #27ae60;
    border: 1px solid #d5e6d5;
}

.error-message {
    background: #fdedeb;
    color: #e74c3c;
    border: 1px solid #f5d6d3;
}

.error-list {
    margin: 1rem 0;
    padding-left: 1.5rem;
}

.action-buttons {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-block;
    padding: 0.8rem 1.5rem;
    background: #2d5a27;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-weight: 600;
    transition: background 0.3s ease;
}

.btn:hover {
    background: #1e4023;
}

.btn-secondary {
    background: #f1f1f1;
    color: #333;
}

.btn-secondary:hover {
    background: #e1e1e1;
}

.form-summary {
    background: #f8f9fa;
    padding: 1.2rem;
    border-radius: 6px;
    margin: 1.5rem 0;
}

.form-summary h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.summary-item {
    margin-bottom: 0.8rem;
}

.summary-label {
    font-weight: 600;
    color: #555;
}
</style>

<div class="contact-result-page">
    <div class="result-header">
        <h1><?php echo $emailSent ? 'Message Sent Successfully!' : 'There Was a Problem'; ?></h1>
    </div>

    <?php if ($emailSent): ?>
        <div class="success-message">
            <p>Thank you, <?php echo htmlspecialchars($name); ?>!</p>
            <p>Your message has been successfully sent. We'll get back to you at <?php echo htmlspecialchars($email); ?> as soon as possible.</p>
        </div>

        <div class="form-summary">
            <h3>Message Summary</h3>
            <div class="summary-item">
                <span class="summary-label">Subject:</span> <?php echo htmlspecialchars($subject); ?>
            </div>
            <?php if (!empty($orderId)): ?>
            <div class="summary-item">
                <span class="summary-label">Order ID:</span> <?php echo htmlspecialchars($orderId); ?>
            </div>
            <?php endif; ?>
            <div class="summary-item">
                <span class="summary-label">Your Message:</span><br>
                <?php echo nl2br(htmlspecialchars($message)); ?>
            </div>
        </div>

    <?php else: ?>
        <div class="error-message">
            <p><?php echo empty($errors) ? 'Failed to send your message. Please try again later.' : 'Please correct the following issues:'; ?></p>
            
            <?php if (!empty($errors)): ?>
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="action-buttons">
        <a href="<?php echo BASE_URL; ?>contact.php" class="btn">
            <?php echo $emailSent ? 'Send Another Message' : 'Try Again'; ?>
        </a>
        <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-secondary">Back to Home</a>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>