<?php
require_once 'admin_header.php';

// Database connection
$host = 'localhost';
$dbname = 'ecostore';
$username = 'root';
$password = '';

// For demo - using sample data
$stats = [
    'total_users' => 156,
    'total_products' => 48,
    'total_orders' => 324,
    'total_revenue' => 8920.75
];

// If database connection fails, use sample data
try {
    // Try to connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Try to get real stats
    $stats_query = "SELECT 
        (SELECT COUNT(*) FROM users) as total_users,
        (SELECT COUNT(*) FROM products) as total_products,
        (SELECT COUNT(*) FROM orders) as total_orders,
        (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled') as total_revenue";
    
    $stmt = $pdo->query($stats_query);
    $db_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($db_stats) {
        $stats = $db_stats;
    }
} catch (Exception $e) {
    // Use sample data if database connection fails
    error_log("Database connection failed: " . $e->getMessage());
}
?>

<div class="admin-card">
    <h1>Welcome back, Admin!</h1>
    <p>Manage your sustainable store with ease.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['total_users']; ?></div>
        <div>Total Users</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['total_products']; ?></div>
        <div>Products</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['total_orders']; ?></div>
        <div>Total Orders</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number">$<?php echo number_format($stats['total_revenue'], 2); ?></div>
        <div>Total Revenue</div>
    </div>
</div>

<div class="admin-card">
    <h2>Quick Actions</h2>
    <div style="display: grid; gap: 15px; margin-top: 20px;">
        <a href="products.php" class="quick-action-link">
            <i class="fas fa-box" style="color: #4caf50;"></i>
            <div>
                <strong>Manage Products</strong>
                <div>Add, edit, or remove products</div>
            </div>
        </a>
        
        <a href="users.php" class="quick-action-link">
            <i class="fas fa-users" style="color: #4caf50;"></i>
            <div>
                <strong>User Management</strong>
                <div>View and manage user accounts</div>
            </div>
        </a>
        
        <a href="analytics.php" class="quick-action-link">
            <i class="fas fa-chart-line" style="color: #4caf50;"></i>
            <div>
                <strong>View Analytics</strong>
                <div>See store performance and insights</div>
            </div>
        </a>
    </div>
</div>

<style>
.quick-action-link {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f8f8;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    transition: transform 0.2s, box-shadow 0.2s;
}

.quick-action-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    background: #f0f0f0;
}

.quick-action-link i {
    font-size: 1.5rem;
}

.quick-action-link div {
    flex: 1;
}

.quick-action-link strong {
    display: block;
    margin-bottom: 5px;
    font-size: 1.1rem;
}

.quick-action-link div div {
    font-size: 0.9rem;
    color: #666;
}
</style>

<?php require_once 'admin_footer.php'; ?>
