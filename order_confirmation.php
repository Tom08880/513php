<?php
session_start();
include __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

if (empty($order_id) || !isset($_SESSION['order']) || $_SESSION['order']['order_id'] != $order_id) {
    header("Location: index.php");
    exit();
}

$order = $_SESSION['order'];
?>

<div class="order-confirmation">
    <div class="confirmation-card">
        <div class="confirmation-header">
            <h1>✅ Order Confirmed!</h1>
        </div>
        
        <div class="confirmation-details">
            <p>Thank you for your order!</p>
            <p>Your order has been confirmed and is being processed.</p>
            
            <div class="order-info">
                <div class="info-item">
                    <strong>Order ID:</strong> <?php echo $order['order_id']; ?>
                </div>
                <div class="info-item">
                    <strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
                </div>
                <div class="info-item">
                    <strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?>
                </div>
            </div>
        </div>

        <div class="order-summary">
            <h2>Order Summary</h2>
            <div class="order-items">
                <?php foreach ($order['items'] as $item): ?>
                    <div class="order-item">
                        <div class="item-image">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['product_name']; ?>">
                            <?php else: ?>
                                <div class="placeholder-img">🌱</div>
                            <?php endif; ?>
                        </div>
                        <div class="item-details">
                            <h3><?php echo $item['product_name']; ?></h3>
                            <p>Quantity: <?php echo $item['quantity']; ?></p>
                            <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                        <div class="item-subtotal">
                            $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="eco-points-earned">
                🌟 You earned <strong><?php echo $order['eco_points_earned']; ?> Eco Points</strong>!
            </div>
        </div>

        <div class="confirmation-actions">
            <a href="index.php" class="btn">Back to Cart</a>
            <a href="../products/" class="btn btn-secondary">Continue Shopping</a>
        </div>
    </div>
</div>

<style>
.order-confirmation {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Arial', sans-serif;
}

.confirmation-card {
    background: #E8F5E8;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
}

.confirmation-header h1 {
    color: #2E7D32;
    margin-bottom: 20px;
}

.order-info {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    text-align: left;
}

.info-item {
    margin: 10px 0;
    font-size: 16px;
}

.order-items {
    margin: 20px 0;
}

.order-item {
    display: flex;
    align-items: center;
    background: white;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
}

.item-image img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 15px;
}

.item-details {
    flex: 1;
    text-align: left;
}

.item-subtotal {
    font-weight: bold;
    color: #2E7D32;
}

.eco-points-earned {
    background: #4CAF50;
    color: white;
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
    font-size: 18px;
}

.confirmation-actions {
    margin-top: 30px;
}

.confirmation-actions .btn {
    margin: 0 10px;
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>