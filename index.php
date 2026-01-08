<?php
// [file name]: index.php (updated from index.html)
session_start();
include '../config/database.php';

// Fetch all active products from database
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM products WHERE status = 'active' ORDER BY product_id");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
    error_log("Error fetching products: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoStore - Products</title>
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
        
        .products-page {
            padding: 20px;
            background-color: #f9f9f9;
            min-height: 80vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
        }
        
        .section-header h1 {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .products-container {
            display: flex;
            gap: 30px;
        }
        
        .filter-sidebar {
            width: 250px;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }
        
        .category-filter {
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }
        
        .category-filter li {
            margin-bottom: 10px;
        }
        
        .category-filter a {
            color: #666;
            text-decoration: none;
            padding: 8px;
            display: block;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .category-filter a:hover,
        .category-filter a.active {
            background-color: #e8f5e8;
            color: #2d5a27;
            font-weight: bold;
        }
        
        .products-grid {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-image {
            height: 200px;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-category {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }
        
        .product-name {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.1rem;
            height: 2.4em;
            overflow: hidden;
            position: relative;
            line-height: 1.2em;
        }
        
        .product-name::after {
            content: '...';
            position: absolute;
            right: 0;
            bottom: 0;
            padding-left: 10px;
            background: linear-gradient(to right, transparent, white 30%);
        }
        
        .product-price {
            font-size: 1.2rem;
            color: #2d5a27;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .product-description {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 15px;
            height: 4.2em;
            overflow: hidden;
            position: relative;
        }
        
        .product-description::after {
            content: '...';
            position: absolute;
            right: 0;
            bottom: 0;
            padding-left: 10px;
            background: linear-gradient(to right, transparent, white 30%);
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .btn-secondary {
            background-color: #f8f9fa;
            color: #2d5a27;
            border: 1px solid #2d5a27;
        }
        
        .btn-secondary:hover {
            background-color: #e8f5e8;
        }
        
        .btn-primary {
            background-color: #2d5a27;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1e4023;
        }
        
        .btn-disabled {
            background-color: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
        }
        
        .no-products {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
        
        .footer-column p {
            color: rgba(255,255,255,0.8);
        }
        
        .footer-column h4 {
            margin-bottom: 15px;
            color: white;
        }
        
        .footer-column ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-column li {
            margin-bottom: 8px;
        }
        
        .footer-column a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-column a:hover {
            color: white;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .footer-bottom p {
            color: rgba(255,255,255,0.8);
        }
        
        @media (max-width: 900px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }
            
            .main-nav {
                justify-content: center;
            }
            
            .products-container {
                flex-direction: column;
            }
            
            .filter-sidebar {
                width: 100%;
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
            
            .products-grid {
                grid-template-columns: 1fr;
            }
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
        <div class="products-page">
            <div class="container">
                <div class="section-header">
                    <h1>Eco-Friendly Products</h1>
                    <p>Discover our sustainable collection for a greener lifestyle</p>
                </div>

                <div class="products-container">
                    <div class="filter-sidebar">
                        <div class="sidebar-header">
                            <h3>Categories</h3>
                        </div>
                        <ul class="category-filter" id="categoryFilter">
                            <!-- Categories will be populated by JavaScript -->
                            <li><a href="#" data-category="all" class="active">All Products</a></li>
                            <?php
                            // Get unique categories
                            $categories = [];
                            foreach ($products as $product) {
                                if (!in_array($product['category'], $categories)) {
                                    $categories[] = $product['category'];
                                }
                            }
                            sort($categories);
                            foreach ($categories as $category):
                            ?>
                                <li><a href="#" data-category="<?php echo htmlspecialchars($category); ?>">
                                    <?php echo htmlspecialchars($category); ?>
                                </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="products-grid" id="productsGrid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                         onerror="this.src='https://via.placeholder.com/300x300?text=Image+Error'">
                                </div>
                                <div class="product-info">
                                    <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                                    <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                    <div class="product-actions">
                                        <a href="view.php?id=<?php echo $product['product_id']; ?>" class="btn btn-secondary">Details</a>
                                        <?php if ($product['stock_quantity'] > 0): ?>
                                            <button class="btn btn-primary add-to-cart-btn" 
                                                    data-product-id="<?php echo $product['product_id']; ?>"
                                                    data-product-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                    data-price="<?php echo $product['price']; ?>"
                                                    data-image-url="<?php echo htmlspecialchars($product['image']); ?>">
                                                Add to Cart
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-disabled" disabled>Out of Stock</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
        // Product filtering functionality
        document.addEventListener('DOMContentLoaded', function() {
            const categoryLinks = document.querySelectorAll('.category-filter a');
            const productCards = document.querySelectorAll('.product-card');
            
            // Store all products initially
            const allProducts = Array.from(productCards);
            
            categoryLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all links
                    categoryLinks.forEach(l => l.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');
                    
                    const selectedCategory = this.dataset.category;
                    
                    // Filter products
                    if (selectedCategory === 'all') {
                        // Show all products
                        productCards.forEach(card => {
                            card.style.display = 'block';
                        });
                    } else {
                        // Filter by category
                        productCards.forEach(card => {
                            const category = card.querySelector('.product-category').textContent;
                            if (category === selectedCategory) {
                                card.style.display = 'block';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    }
                });
            });
            
            // Add to cart functionality
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            
            addToCartButtons.forEach(button => {
                button.addEventListener('click', async function() {
                    const productId = this.dataset.productId;
                    const productName = this.dataset.productName;
                    const price = this.dataset.price;
                    const imageUrl = this.dataset.imageUrl;
                    
                    try {
                        const formData = new FormData();
                        formData.append('product_id', productId);
                        formData.append('product_name', productName);
                        formData.append('price', price);
                        formData.append('image_url', imageUrl);
                        formData.append('quantity', 1);
                        
                        const response = await fetch('/sanshang/513week7/cart/add_to_cart.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            showNotification(productName + ' added to cart!');
                            updateCartCount(data.cart_count);
                        } else {
                            showNotification(data.message, 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showNotification('Failed to add item to cart', 'error');
                    }
                });
            });
            
            function showNotification(message, type = 'success') {
                // Remove existing notification
                const existingNotification = document.querySelector('.notification');
                if (existingNotification) {
                    existingNotification.remove();
                }
                
                // Create notification
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
                
                // Add animation styles
                const style = document.createElement('style');
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
                
                // Remove after 3 seconds
                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
            
            function updateCartCount(count) {
                const cartCountElement = document.querySelector('.cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = '(' + count + ')';
                } else {
                    const cartLink = document.querySelector('a[href*="/cart/"]');
                    if (cartLink) {
                        const countElement = document.createElement('span');
                        countElement.className = 'cart-count';
                        countElement.textContent = '(' + count + ')';
                        cartLink.appendChild(countElement);
                    }
                }
            }
            
            // Load cart count on page load
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