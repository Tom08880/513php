<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include required files
include __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';
?>

<div class="home-page">
    <!-- Hero Section with optimized layout -->
    <section class="hero-section">
        <div class="container hero-container">
            <div class="hero-content">
                <h1>Welcome to EcoStore</h1>
                <p>Discover sustainable products for a greener lifestyle</p>
                <a href="<?php echo BASE_URL; ?>products/" class="btn btn-primary">Shop Sustainable Products</a>
            </div>
            <div class="hero-image">
                <img src="https://picsum.photos/seed/eco-hero/800/600" alt="Sustainable Living">
            </div>
        </div>
    </section>

    <!-- Featured Products with responsive grid -->
    <section class="featured-products section-padding">
        <div class="container">
            <div class="section-header">
                <h2>Featured Sustainable Products</h2>
                <p>Handpicked items for eco-conscious living</p>
            </div>
            
            <div class="products-grid">
                <?php
                $featuredProducts = [
                    [
                        'id' => 1, 
                        'name' => 'Bamboo Toothbrush', 
                        'price' => 4.99, 
                        'image' => 'https://img0.baidu.com/it/u=441216865,1123550377&fm=253&fmt=auto&app=138&f=JPEG?w=500&h=500', 
                        'sustainability' => 5
                    ],
                    [
                        'id' => 2, 
                        'name' => 'Reusable Coffee Cup', 
                        'price' => 12.99, 
                        'image' => 'https://img1.baidu.com/it/u=3851877629,2770226317&fm=253&fmt=auto&app=138&f=JPEG?w=500&h=667', 
                        'sustainability' => 4
                    ],
                    [
                        'id' => 3, 
                        'name' => 'Organic Cotton Tote', 
                        'price' => 8.99, 
                        'image' => 'https://img2.baidu.com/it/u=1080267405,2719267036&fm=253&fmt=auto&app=138&f=JPEG?w=500&h=500', 
                        'sustainability' => 5
                    ]
                ];

                foreach ($featuredProducts as $product):
                ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php if ($product['sustainability'] >= 4): ?>
                                <div class="eco-badge">Eco Choice</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-sustainability">
                                <span>Sustainability Score: </span>
                                <div class="score-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?php echo $i <= $product['sustainability'] ? 'filled' : ''; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                            <div class="product-actions">
                                <a href="<?php echo BASE_URL; ?>products/view.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary">View Details</a>
                                <!-- Modified: Changed to form submission -->
                                <form method="POST" action="<?php echo BASE_URL; ?>cart/add_to_cart.php" class="add-to-cart-form" data-product-id="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                    <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                                    <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($product['image']); ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-primary add-to-cart-btn">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section-footer">
                <a href="<?php echo BASE_URL; ?>products/" class="btn btn-outline">View All Products</a>
            </div>
        </div>
    </section>

    <!-- Sustainability Highlights -->
    <section class="sustainability-highlights section-padding bg-light">
        <div class="container">
            <div class="section-header">
                <h2>Our Sustainability Commitment</h2>
                <p>Making eco-friendly choices accessible to everyone</p>
            </div>
            
            <div class="highlights-grid">
                <div class="highlight-card">
                    <div class="highlight-icon">♻️</div>
                    <h3>Recyclable Materials</h3>
                    <p>All products use recycled or biodegradable materials to minimize environmental impact.</p>
                </div>
                <div class="highlight-card">
                    <div class="highlight-icon">🌱</div>
                    <h3>Carbon Neutral</h3>
                    <p>We offset 100% of carbon emissions from production and shipping.</p>
                </div>
                <div class="highlight-card">
                    <div class="highlight-icon">📊</div>
                    <h3>Eco-Impact Tracking</h3>
                    <p>See the environmental savings from your purchases in your account dashboard.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Hero Section Optimization */
.hero-section {
    background-color: #f0f7f0;
    padding: 4rem 0;
}

.hero-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: center;
}

@media (max-width: 768px) {
    .hero-container {
        grid-template-columns: 1fr;
        text-align: center;
    }
}

.hero-content h1 {
    font-size: 2.5rem;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.hero-content p {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 1.5rem;
    max-width: 400px;
}

.hero-image img {
    width: 100%;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Featured Products Optimization */
.featured-products {
    background-color: white;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-image {
    position: relative;
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

.eco-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: #2d5a27;
    color: white;
    padding: 0.3rem 0.6rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.product-info {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-name {
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.product-sustainability {
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.score-stars {
    display: inline-block;
}

.star {
    color: #ddd;
}

.star.filled {
    color: #f39c12;
}

.product-price {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2d5a27;
    margin-bottom: 1.5rem;
    margin-top: auto;
}

.product-actions {
    display: flex;
    gap: 0.5rem;
}

.add-to-cart-form {
    margin: 0;
    padding: 0;
    display: inline;
}

.btn {
    display: inline-block;
    padding: 0.8rem 1.5rem;
    background-color: #2d5a27;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.3s;
    text-align: center;
}

.btn:hover {
    background-color: #1e4023;
}

.btn-primary {
    background-color: #2d5a27;
}

.btn-primary:hover {
    background-color: #1e4023;
}

.btn-secondary {
    background-color: #f8f9fa;
    color: #2d5a27;
    border: 1px solid #2d5a27;
}

.btn-secondary:hover {
    background-color: #e9ecef;
}

.section-footer {
    text-align: center;
    margin-top: 3rem;
}

.btn-outline {
    background: transparent;
    color: #2d5a27;
    border: 1px solid #2d5a27;
    padding: 0.8rem 2rem;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.btn-outline:hover {
    background-color: #2d5a27;
    color: white;
}

/* Sustainability Highlights */
.bg-light {
    background-color: #f9f9f9;
}

.highlights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.highlight-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.highlight-icon {
    font-size: 2.5rem;
    color: #2d5a27;
    margin-bottom: 1rem;
}

.highlight-card h3 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.highlight-card p {
    color: #666;
    line-height: 1.6;
}

.section-padding {
    padding: 4rem 0;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-header h2 {
    font-size: 2.2rem;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.section-header p {
    color: #666;
    font-size: 1.1rem;
}

/* Add shopping cart message style */
.cart-message {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 4px;
    font-weight: 600;
    z-index: 1000;
    animation: slideIn 0.3s ease, fadeOut 0.3s ease 2.7s forwards;
}

.cart-message.success {
    background-color: #4CAF50;
    color: white;
    border-left: 4px solid #2E7D32;
}

.cart-message.error {
    background-color: #ffebee;
    color: #c62828;
    border-left: 4px solid #c62828;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}
</style>

<script>
// Add shopping cart functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add submit event for all add-to-cart forms
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form default submission
            
            const form = this;
            const submitBtn = form.querySelector('.add-to-cart-btn');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.textContent = 'Adding...';
            submitBtn.disabled = true;
            
            // Get form data
            const formData = new FormData(form);
            
            // Send AJAX request
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showCartMessage('Product added to cart successfully!', 'success');
                    
                    // Update cart count (assuming there is an element showing cart count)
                    updateCartCount(data.cart_count);
                    
                    // Restore button
                    submitBtn.textContent = 'Added!';
                    setTimeout(() => {
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                    }, 1000);
                } else {
                    // Show error message
                    showCartMessage(data.message || 'Failed to add product to cart', 'error');
                    
                    // Restore button
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCartMessage('Network error. Please try again.', 'error');
                
                // Restore button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    });
    
    // Get current cart count on page load
    updateCartCountOnLoad();
});

// Display shopping cart message
function showCartMessage(message, type) {
    // Remove existing message
    const existingMessage = document.querySelector('.cart-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create new message
    const messageDiv = document.createElement('div');
    messageDiv.className = `cart-message ${type}`;
    messageDiv.textContent = message;
    
    document.body.appendChild(messageDiv);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 3000);
}

// Update cart count
function updateCartCount(count) {
    // Update cart count in navigation (assuming element with id cart-count)
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
    }
    
    // Update other possible cart count displays
    document.querySelectorAll('.cart-count').forEach(element => {
        element.textContent = count;
    });
}

// Get cart count on page load
function updateCartCountOnLoad() {
    fetch('cart/add_to_cart.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
            }
        })
        .catch(error => console.error('Error fetching cart count:', error));
}
</script>

<?php 
include __DIR__ . '/includes/footer.php'; 
?>
