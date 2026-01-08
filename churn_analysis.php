<?php
// File: analytics/churn_analysis.php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define base paths
$current_dir = dirname($_SERVER['PHP_SELF']);
$base_dir = dirname(dirname($current_dir));
if ($base_dir === '/') {
    $base_dir = '';
}
define('BASE_URL', 'https://' . $_SERVER['HTTP_HOST'] . $base_dir . '/');

// Set the root directory path
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)) . '/');
}

// Include required files with correct paths
require_once ROOT_PATH . 'includes/functions.php';
require_once ROOT_PATH . 'config/database.php';

// Check if user is logged in (simple version)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // For demo purposes, we'll auto-login if not logged in
    $_SESSION['logged_in'] = true;
    $_SESSION['user_name'] = 'Demo User';
}

// Include header
include ROOT_PATH . 'includes/header.php';

// Generate sample data
$first_names = ['John', 'Jane', 'Robert', 'Emily', 'Michael', 'Sarah', 'David', 'Lisa', 'James', 'Maria'];
$last_names = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];
$customers = [];

for ($i = 1; $i <= 20; $i++) {
    $first = $first_names[array_rand($first_names)];
    $last = $last_names[array_rand($last_names)];
    $email = strtolower($first . '.' . $last . $i . '@example.com');
    
    // Random data
    $total_orders = rand(0, 15);
    $total_spent = $total_orders > 0 ? rand(50, 1500) : 0;
    $days_since_last = $total_orders > 0 ? rand(1, 120) : null;
    
    // Determine churn risk
    if ($total_orders == 0) {
        $churn_risk = 'No Orders';
    } elseif ($days_since_last > 90) {
        $churn_risk = 'High Risk';
    } elseif ($days_since_last > 60) {
        $churn_risk = 'Medium Risk';
    } elseif ($days_since_last > 30) {
        $churn_risk = 'Low Risk';
    } else {
        $churn_risk = 'Active';
    }
    
    $customers[] = [
        'customer_id' => $i,
        'first_name' => $first,
        'last_name' => $last,
        'email' => $email,
        'total_orders' => $total_orders,
        'total_spent' => $total_spent,
        'last_order_date' => $days_since_last ? date('Y-m-d', strtotime("-$days_since_last days")) : null,
        'days_since_last_order' => $days_since_last,
        'churn_risk' => $churn_risk
    ];
}

// Calculate statistics
$total_customers = count($customers);
$high_risk = 0;
$medium_risk = 0;
$low_risk = 0;
$active_customers = 0;
$no_orders = 0;
$total_revenue = 0;

foreach ($customers as $customer) {
    $total_revenue += floatval($customer['total_spent']);
    
    switch ($customer['churn_risk']) {
        case 'High Risk':
            $high_risk++;
            break;
        case 'Medium Risk':
            $medium_risk++;
            break;
        case 'Low Risk':
            $low_risk++;
            break;
        case 'Active':
            $active_customers++;
            break;
        case 'No Orders':
            $no_orders++;
            break;
    }
}

$avg_order_value = $total_customers > 0 ? $total_revenue / $total_customers : 0;
$churn_rate = $total_customers > 0 ? (($high_risk + $medium_risk) / $total_customers) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Churn Analysis - EcoStore</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .churn-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .page-header {
            background: #2d5a27;
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }

        .page-header h1 {
            margin: 0 0 10px 0;
            font-size: 2.2rem;
        }

        .page-header p {
            margin: 0;
            opacity: 0.9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            text-align: center;
            border: 1px solid #e9ecef;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: #2d5a27;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .risk-high { color: #dc3545; }
        .risk-medium { color: #ffc107; }
        .risk-low { color: #28a745; }

        .chart-box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }

        .chart-title {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-title i {
            color: #2d5a27;
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        .customer-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }

        .table-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .table-header h3 {
            margin: 0;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e9ecef;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .risk-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-high { background: #f8d7da; color: #721c24; }
        .badge-medium { background: #fff3cd; color: #856404; }
        .badge-low { background: #d4edda; color: #155724; }
        .badge-active { background: #d1ecf1; color: #0c5460; }
        .badge-none { background: #e2e3e5; color: #383d41; }

        .recommendations {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }

        .rec-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .rec-card {
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .rec-high { border-left-color: #dc3545; background: #f8d7da; }
        .rec-medium { border-left-color: #ffc107; background: #fff3cd; }
        .rec-low { border-left-color: #28a745; background: #d4edda; }

        .rec-title {
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rec-content {
            color: #666;
            line-height: 1.5;
        }

        .search-box {
            margin: 15px 0;
            display: flex;
            gap: 10px;
        }

        .search-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-btn {
            background: #2d5a27;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-btn:hover {
            background: #1e4023;
        }

        @media (max-width: 768px) {
            .churn-container {
                padding: 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            table {
                font-size: 0.9rem;
            }
            
            th, td {
                padding: 10px;
            }
            
            .rec-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="churn-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-chart-line"></i> Customer Churn Analysis</h1>
            <p>Analyze customer behavior and retention patterns</p>
            <p style="font-size: 0.9rem; margin-top: 10px; opacity: 0.8;">
                Last Updated: <?php echo date('F j, Y'); ?>
            </p>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo number_format($total_customers); ?></div>
                <div class="stat-label">Total Customers Analyzed</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-number <?php echo $churn_rate > 20 ? 'risk-high' : ($churn_rate > 10 ? 'risk-medium' : 'risk-low'); ?>">
                    <?php echo number_format($churn_rate, 1); ?>%
                </div>
                <div class="stat-label">Overall Churn Rate</div>
                <div style="font-size: 0.8rem; margin-top: 5px; color: #888;">
                    <?php if ($churn_rate > 20): ?>
                        High Risk
                    <?php elseif ($churn_rate > 10): ?>
                        Medium Risk
                    <?php else: ?>
                        Low Risk
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-number">$<?php echo number_format($total_revenue, 2); ?></div>
                <div class="stat-label">Total Customer Revenue</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-number">$<?php echo number_format($avg_order_value, 2); ?></div>
                <div class="stat-label">Average Customer Value</div>
            </div>
        </div>

        <!-- Risk Distribution Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-radiation"></i>
                </div>
                <div class="stat-number risk-high"><?php echo $high_risk; ?></div>
                <div class="stat-label">High Risk Customers</div>
                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                    No orders in 90+ days
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-number risk-medium"><?php echo $medium_risk; ?></div>
                <div class="stat-label">Medium Risk Customers</div>
                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                    No orders in 60-90 days
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number risk-low"><?php echo $low_risk; ?></div>
                <div class="stat-label">Low Risk Customers</div>
                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                    No orders in 30-59 days
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-number"><?php echo $active_customers; ?></div>
                <div class="stat-label">Active Customers</div>
                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                    Ordered within 30 days
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="chart-box">
            <h3 class="chart-title">
                <i class="fas fa-chart-pie"></i> Customer Risk Distribution
            </h3>
            <div class="chart-container">
                <canvas id="riskChart"></canvas>
            </div>
        </div>

        <!-- Customer Table -->
        <div class="customer-table">
            <div class="table-header">
                <h3><i class="fas fa-table"></i> Customer Details</h3>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9rem;">
                    Showing <?php echo count($customers); ?> customers
                </p>
                
                <!-- Search Box -->
                <div class="search-box">
                    <input type="text" id="searchInput" class="search-input" placeholder="Search customers by name, email, or risk level...">
                    <button class="search-btn" onclick="filterTable()">Search</button>
                    <button class="search-btn" onclick="clearSearch()" style="background: #6c757d;">Clear</button>
                </div>
            </div>
            
            <div style="overflow-x: auto;">
                <table id="customerTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Total Orders</th>
                            <th>Total Spent</th>
                            <th>Last Order</th>
                            <th>Days Since</th>
                            <th>Churn Risk</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <?php
                            $last_order = $customer['last_order_date'] ? 
                                date('M j, Y', strtotime($customer['last_order_date'])) : 'Never';
                            $days_since = $customer['days_since_last_order'] ?? 'N/A';
                            
                            // Determine badge class
                            $badge_class = '';
                            switch ($customer['churn_risk']) {
                                case 'High Risk':
                                    $badge_class = 'badge-high';
                                    break;
                                case 'Medium Risk':
                                    $badge_class = 'badge-medium';
                                    break;
                                case 'Low Risk':
                                    $badge_class = 'badge-low';
                                    break;
                                case 'Active':
                                    $badge_class = 'badge-active';
                                    break;
                                default:
                                    $badge_class = 'badge-none';
                                    break;
                            }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo $customer['total_orders']; ?></td>
                                <td>$<?php echo number_format($customer['total_spent'], 2); ?></td>
                                <td><?php echo $last_order; ?></td>
                                <td><?php echo is_numeric($days_since) ? $days_since . ' days' : $days_since; ?></td>
                                <td>
                                    <span class="risk-badge <?php echo $badge_class; ?>">
                                        <?php echo $customer['churn_risk']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="padding: 15px; background: #f8f9fa; text-align: center; color: #666; border-top: 1px solid #e9ecef;">
                <i class="fas fa-info-circle"></i> Total customers: <?php echo count($customers); ?> | 
                <a href="javascript:void(0)" onclick="exportToCSV()" style="color: #2d5a27; text-decoration: none;">
                    <i class="fas fa-download"></i> Export to CSV
                </a>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="recommendations">
            <h3 class="chart-title">
                <i class="fas fa-lightbulb"></i> Recommendations & Action Plan
            </h3>
            
            <div class="rec-grid">
                <?php if ($high_risk > 0): ?>
                <div class="rec-card rec-high">
                    <div class="rec-title">
                        <i class="fas fa-bullhorn"></i> Win-back Campaign
                    </div>
                    <div class="rec-content">
                        <p><strong><?php echo $high_risk; ?> high-risk customers</strong> haven't ordered in 90+ days.</p>
                        <ul style="margin: 10px 0 0 20px; padding: 0;">
                            <li>Send personalized reactivation emails</li>
                            <li>Offer 20% discount on next purchase</li>
                            <li>Conduct exit surveys</li>
                            <li>Implement loyalty rewards</li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($medium_risk > 0): ?>
                <div class="rec-card rec-medium">
                    <div class="rec-title">
                        <i class="fas fa-exclamation-circle"></i> Re-engagement Strategy
                    </div>
                    <div class="rec-content">
                        <p><strong><?php echo $medium_risk; ?> medium-risk customers</strong> haven't ordered in 60-90 days.</p>
                        <ul style="margin: 10px 0 0 20px; padding: 0;">
                            <li>Send product update newsletters</li>
                            <li>Offer 15% loyalty discount</li>
                            <li>Request feedback on recent purchases</li>
                            <li>Highlight new arrivals</li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="rec-card rec-low">
                    <div class="rec-title">
                        <i class="fas fa-shield-alt"></i> Retention Strategy
                    </div>
                    <div class="rec-content">
                        <p><strong><?php echo $active_customers + $low_risk; ?> active & low-risk customers</strong> need continued engagement.</p>
                        <ul style="margin: 10px 0 0 20px; padding: 0;">
                            <li>Send regular newsletters</li>
                            <li>Implement referral program</li>
                            <li>Request product reviews</li>
                            <li>Offer exclusive early access</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <?php if ($churn_rate > 20): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-top: 20px; border-left: 4px solid #dc3545;">
                <p style="margin: 0; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Alert:</strong> Churn rate is above 20%. Immediate action recommended.
                </p>
            </div>
            <?php elseif ($churn_rate > 10): ?>
            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 6px; margin-top: 20px; border-left: 4px solid #ffc107;">
                <p style="margin: 0; font-weight: 600;">
                    <i class="fas fa-exclamation-circle"></i> 
                    <strong>Notice:</strong> Churn rate is above 10%. Monitor closely.
                </p>
            </div>
            <?php else: ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-top: 20px; border-left: 4px solid #28a745;">
                <p style="margin: 0; font-weight: 600;">
                    <i class="fas fa-check-circle"></i> 
                    <strong>Good:</strong> Churn rate is healthy. Continue current strategies.
                </p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Summary -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 30px; border-left: 4px solid #2d5a27;">
            <h4 style="margin: 0 0 10px 0; color: #2c3e50;">
                <i class="fas fa-file-alt"></i> Analysis Summary
            </h4>
            <p style="margin: 0; color: #666; line-height: 1.6;">
                Based on the analysis of <?php echo number_format($total_customers); ?> customers, 
                <strong><?php echo $high_risk + $medium_risk; ?> customers (<?php echo number_format($churn_rate, 1); ?>%)</strong> 
                are at risk of churning. 
                <?php if ($high_risk > 0): ?>
                Immediate action is recommended for the <?php echo $high_risk; ?> high-risk customers.
                <?php endif; ?>
                Total revenue from analyzed customers: <strong>$<?php echo number_format($total_revenue, 2); ?></strong>.
            </p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing charts...');
        
        // Risk Distribution Chart
        try {
            const riskCtx = document.getElementById('riskChart');
            if (!riskCtx) {
                console.error('Cannot find canvas element with id "riskChart"');
                return;
            }
            
            console.log('Canvas element found, creating chart...');
            
            // Get canvas context
            const ctx = riskCtx.getContext('2d');
            
            // Create chart
            const riskChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['High Risk', 'Medium Risk', 'Low Risk', 'Active', 'No Orders'],
                    datasets: [{
                        data: [
                            <?php echo $high_risk; ?>,
                            <?php echo $medium_risk; ?>,
                            <?php echo $low_risk; ?>,
                            <?php echo $active_customers; ?>,
                            <?php echo $no_orders; ?>
                        ],
                        backgroundColor: [
                            '#dc3545', // High Risk - red
                            '#ffc107', // Medium Risk - yellow
                            '#17a2b8', // Low Risk - teal
                            '#28a745', // Active - green
                            '#6c757d'  // No Orders - gray
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} customers (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
            
            console.log('Chart created successfully');
        } catch (error) {
            console.error('Error creating chart:', error);
            
            // Fallback: Display data as text if chart fails
            const chartContainer = document.querySelector('.chart-container');
            if (chartContainer) {
                chartContainer.innerHTML = `
                    <div style="text-align: center; padding: 40px;">
                        <h4>Chart Data</h4>
                        <p>High Risk: <?php echo $high_risk; ?></p>
                        <p>Medium Risk: <?php echo $medium_risk; ?></p>
                        <p>Low Risk: <?php echo $low_risk; ?></p>
                        <p>Active: <?php echo $active_customers; ?></p>
                        <p>No Orders: <?php echo $no_orders; ?></p>
                        <p><small><em>Chart.js failed to load. Showing data as text.</em></small></p>
                    </div>
                `;
            }
        }

        // Table search functionality
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('customerTable');
            const rows = table.getElementsByTagName('tr');
            
            let visibleCount = 0;
            
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let rowText = '';
                
                for (let j = 0; j < cells.length; j++) {
                    rowText += cells[j].textContent || cells[j].innerText;
                }
                
                if (rowText.toLowerCase().indexOf(filter) > -1) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }
            
            // Update count
            const countElement = document.querySelector('.customer-table .table-header p');
            if (countElement) {
                countElement.textContent = `Showing ${visibleCount} of ${rows.length - 1} customers`;
            }
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            filterTable();
            
            // Reset count
            const countElement = document.querySelector('.customer-table .table-header p');
            if (countElement) {
                countElement.textContent = `Showing <?php echo count($customers); ?> customers`;
            }
        }

        // Enter key to search
        document.getElementById('searchInput').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                filterTable();
            }
        });

        // Export to CSV
        function exportToCSV() {
            const table = document.getElementById('customerTable');
            const rows = table.querySelectorAll('tr');
            const csv = [];
            
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');
                
                for (let j = 0; j < cols.length; j++) {
                    let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ');
                    data = data.replace(/"/g, '""');
                    row.push('"' + data + '"');
                }
                
                csv.push(row.join(','));
            }
            
            const csvString = csv.join('\n');
            const filename = 'churn_analysis_' + new Date().toISOString().slice(0,10) + '.csv';
            
            const link = document.createElement('a');
            link.style.display = 'none';
            link.setAttribute('target', '_blank');
            link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString));
            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Print functionality
        function printReport() {
            window.print();
        }
        
        // Add print button if not exists
        const headerActions = document.querySelector('.table-header');
        if (headerActions) {
            const printBtn = document.createElement('button');
            printBtn.className = 'search-btn';
            printBtn.style.marginLeft = '10px';
            printBtn.innerHTML = '<i class="fas fa-print"></i> Print';
            printBtn.onclick = printReport;
            headerActions.querySelector('.search-box').appendChild(printBtn);
        }
    });
    </script>
</body>
</html>

<?php
// Include footer
include ROOT_PATH . 'includes/footer.php';
?>