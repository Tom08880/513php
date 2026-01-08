<?php
// [file name]: checkout.php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

$error = '';
$success = '';

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'credit_card';

    if (empty($shipping_address)) {
        $error = "Shipping address is required.";
    } else {
        // Note: order_id is auto-incremented in database, we don't need to generate
        // Just generate an order number for display
        $order_number = 'ORD' . date('YmdHis') . rand(1000, 9999);
        
        try {
            // Database connection
            $host = 'sql100.infinityfree.com';
            $dbname = 'if0_39943908_wp16';
            $username = 'if0_39943908';
            $password = 'l3fA9Em7PP';
            
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Begin transaction
            $pdo->beginTransaction();
            
            // Insert order into database (using correct column names)
            // Note: The orders table does not have shipping_address and payment_method columns
            // We need to modify the table structure or only insert existing columns
            // First check if the table has these columns, if not, need to add them
            $stmt = $pdo->prepare("
                INSERT INTO orders (
                    user_id, 
                    total_amount, 
                    status,
                    created_at
                ) VALUES (?, ?, 'pending', NOW())
            ");
            
            $stmt->execute([
                $_SESSION['user_id'],
                $total
            ]);
            
            // Get auto-incremented order_id
            $order_id = $pdo->lastInsertId();
            
            // Insert order items
            $order_items_stmt = $pdo->prepare("
                INSERT INTO order_items (
                    order_id,
                    product_id,
                    product_name,
                    price,
                    quantity,
                    subtotal,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            foreach ($_SESSION['cart'] as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $order_items_stmt->execute([
                    $order_id, // Use database auto-incremented integer ID
                    $item['product_id'],
                    $item['product_name'],
                    $item['price'],
                    $item['quantity'],
                    $subtotal
                ]);
            }
            
            // Commit transaction
            $pdo->commit();
            
            // Get user information (from session)
            $customer_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
            $customer_email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
            
            // Store order in session for confirmation page
            $_SESSION['order'] = [
                'order_id' => $order_id,
                'order_number' => $order_number, // Order number for display
                'user_id' => $_SESSION['user_id'],
                'customer_name' => $customer_name,
                'customer_email' => $customer_email,
                'total_amount' => $total,
                'shipping_address' => htmlspecialchars($shipping_address),
                'payment_method' => $payment_method,
                'order_date' => date('Y-m-d H:i:s'),
                'eco_points_earned' => floor($total),
                'items' => $_SESSION['cart']
            ];
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            // Redirect to confirmation page
            header("Location: order_confirmation.php?order_id=" . $order_id);
            exit();
            
        } catch (PDOException $e) {
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            $error = "Failed to process order. Please try again. Error: " . $e->getMessage();
            // Debug information
            error_log("Order processing error: " . $e->getMessage());
        }
    }
}

// Now include header
include __DIR__ . '/../includes/header.php';
?>

<div class="checkout-page">
    <h1>🌿 Checkout</h1>

    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" class="checkout-form">
        <div class="checkout-container">
            <div class="checkout-main">
                <section class="customer-info">
                    <h2>Customer Information</h2>
                    <div class="form-group">
                        <label>User ID:</label>
                        <input type="text" value="User #<?php echo $_SESSION['user_id']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>" readonly>
                    </div>
                    <?php if (isset($_SESSION['email'])): ?>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
                    </div>
                    <?php endif; ?>
                </section>

                <section class="shipping-address">
                    <h2>Shipping Address</h2>
                    <div class="form-group">
                        <label for="shipping_address">Full Address *</label>
                        <textarea id="shipping_address" name="shipping_address" rows="4" required 
                                  placeholder="Enter your full shipping address including street, city, state, and zip code..."><?php 
                                  echo isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : ''; 
                                  ?></textarea>
                        <small class="form-text">Please provide your complete shipping address for delivery.</small>
                    </div>
                </section>

                <section class="payment-method">
                    <h2>Payment Method</h2>
                    <div class="form-group">
                        <label for="payment_method">Select Payment Method *</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="credit_card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'credit_card') ? 'selected' : 'selected'; ?>>Credit Card</option>
                            <option value="paypal" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'paypal') ? 'selected' : ''; ?>>PayPal</option>
                            <option value="bank_transfer" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                        </select>
                    </div>
                </section>
            </div>

            <div class="checkout-sidebar">
                <div class="order-summary">
                    <h2>Order Summary</h2>
                    <div class="order-items">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <?php $subtotal = $item['price'] * $item['quantity']; ?>
                            <div class="order-item">
                                <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?> x <?php echo $item['quantity']; ?></div>
                                <div class="item-price">$<?php echo number_format($subtotal, 2); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>

                    <div class="eco-points">
                        🌟 You will earn <?php echo floor($total); ?> Eco Points!
                    </div>

                    <button type="submit" class="btn btn-place-order">Place Order</button>
                    <p class="secure-checkout">🔒 Secure checkout</p>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.checkout-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.error-message {
    background: #ffebee;
    color: #c62828;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    border-left: 4px solid #c62828;
}

.success-message {
    background: #e8f5e8;
    color: #2e7d32;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    border-left: 4px solid #2e7d32;
}

.checkout-container {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.checkout-main {
    flex: 2;
    min-width: 300px;
}

.checkout-sidebar {
    flex: 1;
    min-width: 300px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #2E7D32;
    font-size: 16px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    box-sizing: border-box;
}

.form-group input[readonly] {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.form-text {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 14px;
}

.order-summary {
    background: #E8F5E8;
    padding: 25px;
    border-radius: 8px;
    position: sticky;
    top: 20px;
}

.order-summary h2 {
    margin-top: 0;
    color: #2E7D32;
    padding-bottom: 15px;
    border-bottom: 1px solid #C8E6C9;
}

.order-items {
    margin-bottom: 20px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px dashed #C8E6C9;
}

.order-item:last-child {
    border-bottom: none;
}

.order-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.3em;
    font-weight: bold;
    margin: 20px 0;
    padding-top: 15px;
    border-top: 2px solid #4CAF50;
}

.eco-points {
    background: #4CAF50;
    color: white;
    padding: 15px;
    border-radius: 4px;
    margin: 20px 0;
    text-align: center;
    font-size: 16px;
}

.btn-place-order {
    background: #4CAF50;
    color: white;
    padding: 16px;
    border: none;
    border-radius: 4px;
    font-size: 18px;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s;
    margin-bottom: 15px;
}

.btn-place-order:hover {
    background: #388E3C;
}

.secure-checkout {
    text-align: center;
    color: #666;
    font-size: 14px;
    margin: 0;
}

@media (max-width: 768px) {
    .checkout-container {
        flex-direction: column;
    }
    
    .checkout-sidebar {
        order: -1;
        position: static;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>