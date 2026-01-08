<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$message = '';
$total = 0;
$item_count = 0;

// Process form submission (must be before any output)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update cart quantities
    if (isset($_POST['update_cart'])) {
        if (isset($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $index => $quantity) {
                $quantity = intval($quantity);
                if ($quantity <= 0) {
                    unset($_SESSION['cart'][$index]);
                } else {
                    $_SESSION['cart'][$index]['quantity'] = $quantity;
                }
            }
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            $message = "Cart updated successfully!";
        }
    }
    // Clear cart
    elseif (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        $message = "Cart cleared successfully!";
    }
    // Redirect to checkout page
    elseif (isset($_POST['checkout'])) {
        // Ensure cart is not empty
        if (!empty($_SESSION['cart'])) {
            header("Location: checkout.php");
            exit();
        } else {
            $message = "Your cart is empty!";
        }
    }
}

// Calculate cart total
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
    $item_count += $item['quantity'];
}

// Now include header (make sure it's not included after header redirect)
include __DIR__ . '/../includes/header.php';
?>

<div class="cart-page">
    <h1>🛒 Shopping Cart</h1>
    
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['cart'])): ?>
        <form method="POST" action="">
            <div class="cart-items">
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <?php $subtotal = $item['price'] * $item['quantity']; ?>
                    <div class="cart-item">
                        <div class="item-image">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                            <?php else: ?>
                                <div class="placeholder-img">🌱</div>
                            <?php endif; ?>
                        </div>
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                            <p class="item-price">$<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                        <div class="item-quantity">
                            <label>Quantity:</label>
                            <input type="number" name="quantities[<?php echo $index; ?>]" 
                                   value="<?php echo $item['quantity']; ?>" min="1" max="99">
                        </div>
                        <div class="item-subtotal">
                            $<?php echo number_format($subtotal, 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <div class="summary-card">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Items (<?php echo $item_count; ?>):</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="eco-points">
                        🌿 You'll earn <?php echo floor($total); ?> Eco Points!
                    </div>
                    <div class="summary-actions">
                        <button type="submit" name="update_cart" class="btn btn-secondary">Update Cart</button>
                        <button type="submit" name="clear_cart" class="btn btn-secondary" onclick="return confirm('Are you sure you want to clear your cart?')">Clear Cart</button>
                        <button type="submit" name="checkout" class="btn">Proceed to Checkout</button>
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="empty-cart">
            <h2>Your cart is empty</h2>
            <p>Continue shopping to add items to your cart.</p>
            <a href="../products/" class="btn">Continue Shopping</a>
        </div>
    <?php endif; ?>
</div>

<style>
.cart-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Arial', sans-serif;
}

.message {
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 20px;
    text-align: center;
}

.cart-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
    background: #f9f9f9;
    margin-bottom: 10px;
    border-radius: 8px;
}

.item-image img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 15px;
}

.placeholder-img {
    width: 80px;
    height: 80px;
    background: #4CAF50;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    border-radius: 8px;
    margin-right: 15px;
}

.item-details {
    flex: 1;
}

.item-details h3 {
    margin: 0 0 5px 0;
    color: #333;
}

.item-price {
    color: #2E7D32;
    font-weight: bold;
    margin: 0;
}

.item-quantity {
    margin: 0 20px;
}

.item-quantity label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
    color: #666;
}

.item-quantity input {
    width: 70px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

.item-subtotal {
    font-weight: bold;
    color: #2E7D32;
    font-size: 18px;
    min-width: 100px;
    text-align: right;
}

.cart-summary {
    margin-top: 30px;
    padding: 20px;
    background: #E8F5E8;
    border-radius: 8px;
}

.summary-card h3 {
    margin-top: 0;
    color: #2E7D32;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin: 10px 0;
    padding: 5px 0;
}

.summary-row.total {
    font-size: 1.2em;
    font-weight: bold;
    border-top: 2px solid #ddd;
    margin-top: 15px;
    padding-top: 15px;
}

.eco-points {
    background: #4CAF50;
    color: white;
    padding: 10px;
    border-radius: 4px;
    margin: 15px 0;
    text-align: center;
}

.btn {
    background: #4CAF50;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    transition: background-color 0.3s;
}

.btn:hover {
    background: #388E3C;
}

.btn-secondary {
    background: #81C784;
}

.btn-secondary:hover {
    background: #66BB6A;
}

.summary-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.empty-cart {
    text-align: center;
    padding: 50px;
    background: #E8F5E8;
    border-radius: 8px;
    margin-top: 20px;
}

.empty-cart h2 {
    color: #2E7D32;
    margin-bottom: 10px;
}

.empty-cart p {
    color: #666;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .cart-item {
        flex-wrap: wrap;
    }
    
    .item-quantity {
        margin: 10px 0;
        width: 100%;
    }
    
    .summary-actions {
        flex-direction: column;
    }
    
    .summary-actions .btn {
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
