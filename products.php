<?php
require_once 'admin_header.php';

// Database connection
$host = 'sql100.infinityfree.com';
$dbname = 'if0_39943908_wp16';
$username = 'if0_39943908';
$password = 'l3fA9Em7PP';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch products from database
$products = [];
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY product_id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (product_name, price, stock_quantity, category, status, image, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                htmlspecialchars($_POST['name']),
                floatval($_POST['price']),
                intval($_POST['stock']),
                htmlspecialchars($_POST['category']),
                htmlspecialchars($_POST['status']),
                htmlspecialchars($_POST['image']),
                htmlspecialchars($_POST['description'] ?? '')
            ]);
            
            $_SESSION['success'] = 'Product added successfully!';
            header('Location: products.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to add product: ' . $e->getMessage();
        }
    }
    
    if (isset($_POST['update_product'])) {
        try {
            $stmt = $pdo->prepare("UPDATE products SET product_name = ?, price = ?, stock_quantity = ?, category = ?, status = ?, image = ?, description = ? WHERE product_id = ?");
            $stmt->execute([
                htmlspecialchars($_POST['name']),
                floatval($_POST['price']),
                intval($_POST['stock']),
                htmlspecialchars($_POST['category']),
                htmlspecialchars($_POST['status']),
                htmlspecialchars($_POST['image']),
                htmlspecialchars($_POST['description'] ?? ''),
                intval($_POST['product_id'])
            ]);
            
            $_SESSION['success'] = 'Product updated successfully!';
            header('Location: products.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to update product: ' . $e->getMessage();
        }
    }
    
    if (isset($_POST['delete_product'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
            $stmt->execute([intval($_POST['product_id'])]);
            
            $_SESSION['success'] = 'Product deleted successfully!';
            header('Location: products.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to delete product: ' . $e->getMessage();
        }
    }
}

// Get product for editing if edit parameter is set
$editProduct = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->execute([intval($_GET['edit'])]);
        $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$editProduct) {
            $_SESSION['error'] = 'Product not found!';
            header('Location: products.php');
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Failed to fetch product: ' . $e->getMessage();
    }
}
?>

<div class="admin-card">
    <h1>Product Management</h1>
    <p>Add, edit, or remove products from your store</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
    <!-- Product Form -->
    <div class="admin-card">
        <h2><?php echo isset($_GET['edit']) ? 'Edit Product' : 'Add New Product'; ?></h2>
        <form method="POST" style="margin-top: 15px;">
            <?php if (isset($_GET['edit'])): ?>
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($editProduct['product_id'] ?? ''); ?>">
            <?php endif; ?>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Product Name</label>
                <input type="text" name="name" required 
                       value="<?php echo htmlspecialchars($editProduct['product_name'] ?? ''); ?>"
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Description</label>
                <textarea name="description" 
                          style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; height: 80px;"><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Price ($)</label>
                <input type="number" name="price" step="0.01" min="0" required
                       value="<?php echo htmlspecialchars($editProduct['price'] ?? ''); ?>"
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Stock Quantity</label>
                <input type="number" name="stock" min="0" required
                       value="<?php echo htmlspecialchars($editProduct['stock_quantity'] ?? ''); ?>"
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Category</label>
                <select name="category" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="Personal Care" <?php echo (isset($editProduct['category']) && $editProduct['category'] == 'Personal Care') ? 'selected' : ''; ?>>Personal Care</option>
                    <option value="Kitchen" <?php echo (isset($editProduct['category']) && $editProduct['category'] == 'Kitchen') ? 'selected' : ''; ?>>Kitchen</option>
                    <option value="Textiles/Paper" <?php echo (isset($editProduct['category']) && $editProduct['category'] == 'Textiles/Paper') ? 'selected' : ''; ?>>Textiles/Paper</option>
                    <option value="Textiles" <?php echo (isset($editProduct['category']) && $editProduct['category'] == 'Textiles') ? 'selected' : ''; ?>>Textiles</option>
                    <option value="Paper" <?php echo (isset($editProduct['category']) && $editProduct['category'] == 'Paper') ? 'selected' : ''; ?>>Paper</option>
                    <option value="Metal" <?php echo (isset($editProduct['category']) && $editProduct['category'] == 'Metal') ? 'selected' : ''; ?>>Metal</option>
                    <option value="Plastic" <?php echo (isset($editProduct['category']) && $editProduct['category'] == 'Plastic') ? 'selected' : ''; ?>>Plastic</option>
                    <option value="Cleaning" <?php echo (isset($editProduct['category']) && $editProduct['category'] == 'Cleaning') ? 'selected' : ''; ?>>Cleaning</option>
                    <option value="Other" <?php echo (isset($editProduct['category']) && $editProduct['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Image URL</label>
                <input type="text" name="image"
                       value="<?php echo htmlspecialchars($editProduct['image'] ?? ''); ?>"
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                       placeholder="https://example.com/image.jpg">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Status</label>
                <select name="status" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="active" <?php echo (isset($editProduct['status']) && $editProduct['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo (isset($editProduct['status']) && $editProduct['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <?php if (isset($_GET['edit'])): ?>
                    <button type="submit" name="update_product" style="padding: 10px 20px; background: #2196f3; color: white; border: none; border-radius: 4px; cursor: pointer;">Update Product</button>
                    <a href="products.php" style="padding: 10px 20px; background: #f44336; color: white; text-decoration: none; border-radius: 4px;">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add_product" style="padding: 10px 20px; background: #4caf50; color: white; border: none; border-radius: 4px; cursor: pointer;">Add Product</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Products List -->
    <div class="admin-card">
        <h2>Products (<?php echo count($products); ?>)</h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">ID</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Product</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Price</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Stock</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Category</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Status</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($product['product_id']); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php if ($product['image']): ?>
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;"
                                         onerror="this.src='https://via.placeholder.com/150'">
                                <?php endif; ?>
                                <div>
                                    <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                                    <?php if ($product['description']): ?>
                                        <div style="font-size: 0.8rem; color: #666; margin-top: 2px;"><?php echo substr(htmlspecialchars($product['description']), 0, 50); ?>...</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">$<?php echo number_format($product['price'], 2); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($product['stock_quantity']); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($product['category']); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.9rem; 
                                  background: <?php echo $product['status'] == 'active' ? '#e8f5e9' : '#ffebee'; ?>; 
                                  color: <?php echo $product['status'] == 'active' ? '#2d5a27' : '#c62828'; ?>;">
                                <?php echo ucfirst($product['status']); ?>
                            </span>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <div style="display: flex; gap: 5px;">
                                <a href="products.php?edit=<?php echo $product['product_id']; ?>" 
                                   style="padding: 5px 10px; background: #2196f3; color: white; text-decoration: none; border-radius: 4px; font-size: 0.9rem;">Edit</a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <button type="submit" name="delete_product" 
                                            style="padding: 5px 10px; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" style="padding: 20px; text-align: center; color: #666;">
                            No products found. Add your first product using the form on the left.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


    <style>
        .hidden-collapsed { display: none; }
        .show-more-btn { margin-top: 10px; padding: 8px 12px; background: #2196f3; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .show-more-btn.small { font-size: 0.9rem; padding: 6px 10px; }
    </style>

    <script>
    (function(){
        const maxVisible = 8;
        document.querySelectorAll('.admin-card').forEach(function(card){
            const table = card.querySelector('table');
            if(!table) return;
            const tbody = table.querySelector('tbody');
            if(!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr'));
            if(rows.length <= maxVisible) return;
            rows.forEach((r, i) => { if(i >= maxVisible) r.classList.add('hidden-collapsed'); });

            const btn = document.createElement('button');
            btn.className = 'show-more-btn';
            btn.textContent = 'Show more (' + (rows.length - maxVisible) + ')';
            btn.addEventListener('click', function(){
                const hidden = tbody.querySelectorAll('.hidden-collapsed');
                if(hidden.length){
                    hidden.forEach(r => r.classList.remove('hidden-collapsed'));
                    btn.textContent = 'Show less';
                } else {
                    rows.forEach((r, i) => { if(i >= maxVisible) r.classList.add('hidden-collapsed'); });
                    btn.textContent = 'Show more (' + (rows.length - maxVisible) + ')';
                    window.scrollTo({ top: card.getBoundingClientRect().top + window.pageYOffset - 20, behavior: 'smooth' });
                }
            });

            table.parentNode.insertAdjacentElement('afterend', btn);
        });
    })();
    </script>

    <?php require_once 'admin_footer.php'; ?>