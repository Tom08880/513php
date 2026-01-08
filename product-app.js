// [file name]: product-app.js
/**
 * EcoStore Product Management System
 * Handles product filtering and cart functionality
 */

class ProductManager {
    constructor() {
        this.products = [];
        this.categories = [];
        this.currentCategory = 'all';
        this.imagePlaceholder = 'https://via.placeholder.com/300x300?text=No+Image';
        this.imageError = 'https://via.placeholder.com/300x300?text=Image+Error';
    }

    init() {
        this.setupEventListeners();
        this.loadCartCount();
    }

    setupEventListeners() {
        // Category filter click
        document.querySelectorAll('.category-filter a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const category = e.target.dataset.category;
                this.filterProducts(category);
            });
        });

        // Add to cart button clicks (event delegation)
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart-btn')) {
                this.addToCart(e.target);
            }
        });
    }

    async loadCartCount() {
        try {
            const response = await fetch('/sanshang/513week7/cart/add_to_cart.php');
            const data = await response.json();
            
            if (data.success && data.cart_count > 0) {
                this.updateCartCount(data.cart_count);
            }
        } catch (error) {
            console.log('Cart count load error:', error);
        }
    }

    updateCartCount(count) {
        const cartCountElement = document.querySelector('.cart-count');
        const cartLink = document.querySelector('a[href*="/cart/"]');
        
        if (cartCountElement) {
            cartCountElement.textContent = `(${count})`;
        } else if (cartLink) {
            const countElement = document.createElement('span');
            countElement.className = 'cart-count';
            countElement.textContent = `(${count})`;
            cartLink.appendChild(countElement);
        }
    }

    filterProducts(category) {
        this.currentCategory = category;
        
        // Update active class on category links
        document.querySelectorAll('.category-filter a').forEach(link => {
            link.classList.remove('active');
            if (link.dataset.category === category) {
                link.classList.add('active');
            }
        });
        
        // Filter products in the grid
        const productCards = document.querySelectorAll('.product-card');
        
        productCards.forEach(card => {
            if (category === 'all') {
                card.style.display = 'block';
            } else {
                const cardCategory = card.querySelector('.product-category').textContent;
                card.style.display = cardCategory === category ? 'block' : 'none';
            }
        });
    }

    async addToCart(button) {
        const productId = button.dataset.productId;
        const productName = button.dataset.productName;
        const price = button.dataset.price;
        const imageUrl = button.dataset.imageUrl;
        
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
                this.showNotification(`${productName} added to cart!`, 'success');
                this.updateCartCount(data.cart_count);
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showNotification('Failed to add item to cart', 'error');
        }
    }

    showNotification(message, type = 'success') {
        // Remove existing notification
        const existingNotification = document.querySelector('.cart-notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = `cart-notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: ${type === 'success' ? '#2d5a27' : '#c62828'};
            color: white;
            padding: 15px 20px;
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
}

// Initialize product manager when page loads
document.addEventListener('DOMContentLoaded', () => {
    window.productManager = new ProductManager();
    window.productManager.init();
});