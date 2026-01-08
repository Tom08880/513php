<?php
class EmailNotification {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    // Send order confirmation email
    public function sendOrderConfirmation($order_id, $user_email, $user_name) {
        $subject = "Order Confirmation - #" . $order_id;
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .header { background: #2d5a27; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666; }
                .order-details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>EcoStore</h1>
                <p>Thank you for your order!</p>
            </div>
            <div class='content'>
                <h2>Hello " . htmlspecialchars($user_name) . ",</h2>
                <p>Your order has been confirmed and is being processed.</p>
                
                <div class='order-details'>
                    <h3>Order Details</h3>
                    <p><strong>Order ID:</strong> #" . $order_id . "</p>
                    <p><strong>Order Date:</strong> " . date('F j, Y') . "</p>
                    <p>You can track your order status by logging into your account.</p>
                </div>
                
                <p>We'll send you another email when your order ships.</p>
                <p>Thank you for choosing EcoStore and supporting sustainable living!</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " EcoStore. All rights reserved.</p>
                <p>This is an automated email, please do not reply.</p>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($user_email, $subject, $message);
    }
    
    // Send shipping notification
    public function sendShippingNotification($order_id, $user_email, $user_name, $tracking_number = null) {
        $subject = "Your Order Has Shipped! - #" . $order_id;
        
        $tracking_info = "";
        if ($tracking_number) {
            $tracking_info = "<p><strong>Tracking Number:</strong> " . $tracking_number . "</p>";
        }
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .header { background: #4a7c45; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>EcoStore</h1>
                <p>Your order is on the way!</p>
            </div>
            <div class='content'>
                <h2>Great news, " . htmlspecialchars($user_name) . "!</h2>
                <p>Your order #" . $order_id . " has been shipped and is on its way to you.</p>
                " . $tracking_info . "
                <p>You should receive your package within 3-5 business days.</p>
                <p>Thank you for your patience and for choosing EcoStore!</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " EcoStore. All rights reserved.</p>
                <p>This is an automated email, please do not reply.</p>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($user_email, $subject, $message);
    }
    
    // Send low stock alert to admin
    public function sendLowStockAlert($product_id, $product_name, $current_stock, $min_stock) {
        $subject = "Low Stock Alert: " . $product_name;
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .header { background: #ffc107; color: #856404; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .alert { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Inventory Alert</h1>
            </div>
            <div class='content'>
                <div class='alert'>
                    <h2>Low Stock Warning</h2>
                    <p><strong>Product:</strong> " . htmlspecialchars($product_name) . "</p>
                    <p><strong>Current Stock:</strong> " . $current_stock . " units</p>
                    <p><strong>Minimum Level:</strong> " . $min_stock . " units</p>
                    <p><strong>Product ID:</strong> " . $product_id . "</p>
                </div>
                <p>Please consider restocking this product to avoid running out of inventory.</p>
            </div>
        </body>
        </html>
        ";
        
        // Send to admin email (you can configure this)
        $admin_email = "admin@ecostore.com";
        return $this->sendEmail($admin_email, $subject, $message);
    }
    
    // Send password reset email
    public function sendPasswordReset($user_email, $user_name, $reset_token) {
        $reset_link = "https://yourdomain.com/auth/reset_password.php?token=" . $reset_token;
        
        $subject = "Password Reset Request - EcoStore";
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .header { background: #2d5a27; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .button { background: #2d5a27; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; }
                .footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>EcoStore</h1>
                <p>Password Reset</p>
            </div>
            <div class='content'>
                <h2>Hello " . htmlspecialchars($user_name) . ",</h2>
                <p>We received a request to reset your password for your EcoStore account.</p>
                <p>Click the button below to reset your password:</p>
                <p style='text-align: center;'>
                    <a href='" . $reset_link . "' class='button'>Reset Password</a>
                </p>
                <p>If you didn't request a password reset, you can safely ignore this email.</p>
                <p><strong>This link will expire in 1 hour.</strong></p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " EcoStore. All rights reserved.</p>
                <p>This is an automated email, please do not reply.</p>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($user_email, $subject, $message);
    }
    
    // Generic email sending function
    private function sendEmail($to, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: EcoStore <noreply@ecostore.com>" . "\r\n";
        $headers .= "Reply-To: support@ecostore.com" . "\r\n";
        
        // In a real application, you would use PHPMailer or similar
        // For development, we'll log to file instead of actually sending
        return $this->logEmail($to, $subject, $message);
    }
    
    // Log email to file for development
    private function logEmail($to, $subject, $message) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'to' => $to,
            'subject' => $subject,
            'message' => $message
        ];
        
        $log_file = __DIR__ . '/../logs/email_logs.json';
        $logs = [];
        
        // Create logs directory if it doesn't exist
        if (!file_exists(dirname($log_file))) {
            mkdir(dirname($log_file), 0755, true);
        }
        
        // Read existing logs
        if (file_exists($log_file)) {
            $logs = json_decode(file_get_contents($log_file), true) ?: [];
        }
        
        // Add new log entry
        $logs[] = $log_entry;
        
        // Keep only last 100 entries
        if (count($logs) > 100) {
            $logs = array_slice($logs, -100);
        }
        
        // Write to file
        file_put_contents($log_file, json_encode($logs, JSON_PRETTY_PRINT));
        
        return true;
    }
}

// Initialize email notification system
$email_notifier = new EmailNotification($db);
?>