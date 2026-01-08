<?php
require_once 'admin_header.php';

// Sample analytics data
$monthly_revenue = [
    'Jan' => 1200,
    'Feb' => 1800,
    'Mar' => 2200,
    'Apr' => 1900,
    'May' => 2400,
    'Jun' => 2800
];

$top_products = [
    ['name' => 'Bamboo Toothbrush', 'sales' => 156, 'revenue' => 780],
    ['name' => 'Reusable Coffee Cup', 'sales' => 89, 'revenue' => 1157],
    ['name' => 'Organic Cotton Tote', 'sales' => 124, 'revenue' => 1116],
    ['name' => 'Eco Laundry Detergent', 'sales' => 67, 'revenue' => 871],
    ['name' => 'Beeswax Wraps', 'sales' => 93, 'revenue' => 837]
];

$user_activity = [
    'active' => 120,
    'inactive' => 36,
    'new_this_month' => 28
];

// Calculate max revenue for chart scaling
$max_revenue = max($monthly_revenue);
?>

<div class="admin-card">
    <h1>Analytics Dashboard</h1>
    <p>View store performance and insights</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?php echo array_sum($monthly_revenue); ?></div>
        <div>Total Revenue (6 months)</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?php echo $user_activity['active']; ?></div>
        <div>Active Users</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?php echo array_sum(array_column($top_products, 'sales')); ?></div>
        <div>Total Product Sales</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?php echo $user_activity['new_this_month']; ?></div>
        <div>New Users This Month</div>
    </div>
</div>

<div class="admin-card">
    <h2>Revenue Trends (Last 6 Months)</h2>
    <div style="margin-top: 20px;">
        <div style="display: flex; align-items: flex-end; height: 200px; gap: 30px; padding: 20px 0; border-bottom: 1px solid #eee;">
            <?php foreach ($monthly_revenue as $month => $revenue): ?>
                 <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                    <div style="background: #4caf50; width: 40px; border-radius: 4px 4px 0 0; height: <?php echo round(($revenue / $max_revenue) * 150); ?>px; transition: height 300ms ease;"
                        title="<?php echo $month; ?>: $<?php echo $revenue; ?>">
                    </div>
                    <div style="margin-top: 10px; font-weight: bold;"><?php echo $month; ?></div>
                    <div style="font-size: 0.9rem; color: #666;">$<?php echo $revenue; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
    <div class="admin-card">
        <h2>Top Selling Products</h2>
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Product</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Sales</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($top_products as $product): ?>
                <tr>
                    <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo $product['name']; ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo $product['sales']; ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #eee;">$<?php echo $product['revenue']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="admin-card">
        <h2>User Activity</h2>
        <div style="margin-top: 20px;">
            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                <div style="width: 100px;">Active Users:</div>
                <div style="flex: 1; background: #f0f0f0; border-radius: 10px; overflow: hidden;">
                    <div style="width: <?php echo ($user_activity['active'] / ($user_activity['active'] + $user_activity['inactive'])) * 100; ?>%; background: #4caf50; padding: 8px; color: white; text-align: center;">
                        <?php echo $user_activity['active']; ?>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                <div style="width: 100px;">Inactive Users:</div>
                <div style="flex: 1; background: #f0f0f0; border-radius: 10px; overflow: hidden;">
                    <div style="width: <?php echo ($user_activity['inactive'] / ($user_activity['active'] + $user_activity['inactive'])) * 100; ?>%; background: #f44336; padding: 8px; color: white; text-align: center;">
                        <?php echo $user_activity['inactive']; ?>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; align-items: center;">
                <div style="width: 100px;">New This Month:</div>
                <div style="flex: 1; background: #f0f0f0; border-radius: 10px; overflow: hidden;">
                    <div style="width: 100%; background: #2196f3; padding: 8px; color: white; text-align: center;">
                        <?php echo $user_activity['new_this_month']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>