<?php
// [file name]: view.php
session_start();
include '../config/database.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header("Location: index.php");
    exit();
}

// Fetch product from database
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM products WHERE product_id = ? AND status = 'active'");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    error_log("Error fetching product: " . $e->getMessage());
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoStore - <?php echo htmlspecialchars($product['product_name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f9f4;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
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
        
        .main-content {
            flex: 1;
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
            width: 100%;
        }
        
        .product-detail-page {
            padding: 20px;
            background-color: #f9f9f9;
            min-height: 80vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .breadcrumb {
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .breadcrumb a {
            color: #2d5a27;
            text-decoration: none;
        }
        
        .product-detail-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        @media (max-width: 900px) {
            .product-detail-container {
                grid-template-columns: 1fr;
            }
        }
        
        .product-gallery {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .main-image {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .main-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .product-info {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .product-header h1 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .category {
            background: #f8f9fa;
            color: #666;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .product-description h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.25rem;
        }
        
        .pricing-section {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .price {
            font-size: 2rem;
            font-weight: bold;
            color: #2d5a27;
        }
        
        .stock-status {
            margin-top: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .in-stock {
            color: #28a745;
        }
        
        .low-stock {
            color: #ffc107;
        }
        
        .out-of-stock {
            color: #dc3545;
        }
        
        .add-to-cart-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        
        .quantity-selector {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 120px;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .quantity-btn {
            background: #f8f9fa;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .quantity-input {
            width: 50px;
            border: none;
            text-align: center;
            padding: 10px 0;
            font-size: 1rem;
        }
        
        .btn {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #2d5a27;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1e4023;
        }
        
        .btn-large {
            padding: 15px 30px;
            font-size: 1.1rem;
            flex: 1;
        }
        
        .btn-disabled {
            background-color: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
        }
        
        footer {
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
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .footer-column h3 {
            margin-bottom: 15px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .footer-column ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-column a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="/sanshang/513week7/index.php" class="logo">
                <i class="fas fa-leaf"></i> EcoStore
            </a>
            
            <nav class="main-nav">
                <a href="/sanshang/513week7/index.php" class="nav-link">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="index.php" class="nav-link">
                    <i class="fas fa-store"></i> Products
                </a>
                <a href="/sanshang/513week7/cart/" class="nav-link">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <?php 
                    $cart_count = 0;
                    if (isset($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $item) {
                            $cart_count += $item['quantity'];
                        }
                    }
                    if ($cart_count > 0): ?>
                        <span class="cart-count">(<?php echo $cart_count; ?>)</span>
                    <?php endif; ?>
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/sanshang/513week7/auth/logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="/sanshang/513week7/auth/login.php" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="/sanshang/513week7/auth/register.php" class="nav-link">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <main class="main-content">
        <div class="product-detail-page">
            <div class="container">
                <div class="breadcrumb">
                    <a href="/sanshang/513week7/index.php">Home</a> &gt; 
                    <a href="index.php">Products</a> &gt; 
                    <span><?php echo htmlspecialchars($product['product_name']); ?></span>
                </div>

                <div class="product-detail-container">
                    <div class="product-gallery">
                        <div class="main-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                 onerror="this.src='https://via.placeholder.com/500x500?text=Image+Error'">
                        </div>
                    </div>

                    <div class="product-info">
                        <div class="product-header">
                            <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                            <div class="product-meta">
                                <span class="category"><?php echo htmlspecialchars($product['category']); ?></span>
                            </div>
                        </div>

                        <div class="product-description">
                            <h3>Description</h3>
                            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                            
                            <?php if (!empty($product['materials'])): ?>
                                <div class="product-detail">
                                    <h4>Materials:</h4>
                                    <p><?php echo htmlspecialchars($product['materials']); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($product['carbon_footprint'])): ?>
                                <div class="product-detail">
                                    <h4>Carbon Footprint:</h4>
                                    <p><?php echo htmlspecialchars($product['carbon_footprint']); ?> kg CO₂</p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($product['sustainability'])): ?>
                                <div class="product-detail">
                                    <h4>Sustainability Rating:</h4>
                                    <div class="sustainability-stars">
                                        <?php
                                        $sustainability = intval($product['sustainability']);
                                        for ($i = 1; $i <= 5; $i++):
                                            if ($i <= $sustainability):
                                                echo '★';
                                            else:
                                                echo '☆';
                                            endif;
                                        endfor;
                                        ?>
                                        <span> (<?php echo $sustainability; ?>/5)</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="pricing-section">
                            <div class="price-display">
                                <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
                            </div>

                            <div class="stock-status">
                                <?php if ($product['stock_quantity'] > 10): ?>
                                    <span class="in-stock">✅ In Stock</span>
                                <?php elseif ($product['stock_quantity'] > 0): ?>
                                    <span class="low-stock">⚠️ Only <?php echo $product['stock_quantity']; ?> left</span>
                                <?php else: ?>
                                    <span class="out-of-stock">❌ Out of Stock</span>
                                <?php endif; ?>
                            </div>

                            <?php if ($product['stock_quantity'] > 0): ?>
                                <form id="addToCartForm" class="add-to-cart-form" method="POST" action="/sanshang/513week7/cart/add_to_cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                                    <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($product['image']); ?>">
                                    
                                    <div class="quantity-selector">
                                        <label for="quantity">Quantity</label>
                                        <div class="quantity-controls">
                                            <button type="button" class="quantity-btn" onclick="adjustQuantity(-1)">-</button>
                                            <input type="number" id="quantity" name="quantity" value="1" min="1" 
                                                   max="<?php echo min($product['stock_quantity'], 99); ?>" class="quantity-input">
                                            <button type="button" class="quantity-btn" onclick="adjustQuantity(1)">+</button>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-large">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="out-of-stock-message">
                                    <p>This product is currently out of stock.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3><i class="fas fa-leaf"></i> EcoStore</h3>
                    <p>Sustainable products for a better tomorrow.</p>
                </div>
                
                <div class="footer-column">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="/sanshang/513week7/index.php">Home</a></li>
                        <li><a href="index.php">Products</a></li>
                        <li><a href="/sanshang/513week7/cart/">Cart</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Help</h4>
                    <ul>
                        <li><a href="/sanshang/513week7/contact.php">Support</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 EcoStore. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
        // Quantity adjustment
        function adjustQuantity(change) {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            const newValue = currentValue + change;
            
            if (newValue >= parseInt(input.min) && newValue <= parseInt(input.max)) {
                input.value = newValue;
            }
        }
        
        // Handle form submission with AJAX
        document.getElementById('addToCartForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification(result.message);
                    updateCartCount(result.cart_count);
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            }
        });
        
        function showNotification(message, type = 'success') {
            // Remove existing notification
            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // Create new notification
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                padding: 15px 20px;
                background: ${type === 'error' ? '#c62828' : '#2d5a27'};
                color: white;
                border-radius: 5px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                z-index: 1000;
                animation: slideIn 0.3s ease;
            `;
            
            document.body.appendChild(notification);
            
            // Add animation styles if not already present
            if (!document.querySelector('#notification-styles')) {
                const style = document.createElement('style');
                style.id = 'notification-styles';
                style.textContent = `
                    @keyframes slideIn {
                        from { transform: translateX(100%); opacity: 0; }
                        to { transform: translateX(0); opacity: 1; }
                    }
                    @keyframes slideOut {
                        from { transform: translateX(0); opacity: 1; }
                        to { transform: translateX(100%); opacity: 0; }
                    }
                `;
                document.head.appendChild(style);
            }
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
        
        function updateCartCount(count) {
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = `(${count})`;
            } else {
                const cartLink = document.querySelector('a[href*="/cart/"]');
                if (cartLink) {
                    const countElement = document.createElement('span');
                    countElement.className = 'cart-count';
                    countElement.textContent = `(${count})`;
                    cartLink.appendChild(countElement);
                }
            }
        }
        
        // Load cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/sanshang/513week7/cart/add_to_cart.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.cart_count > 0) {
                        updateCartCount(data.cart_count);
                    }
                })
                .catch(error => console.log('Cart count load error:', error));
        });
    </script>
</body>
</html>