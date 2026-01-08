<?php
// File: customer.php

session_start();

// Include required files
require_once __DIR__ . '/includes/functions.php';

// Simple escape function for output
function escapeOutput($data) {
    if (is_array($data)) {
        return array_map('escapeOutput', $data);
    }
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// Database connection
try {
    require_once __DIR__ . '/config/database.php';
    $conn = getDB();
    
   
    $sql = "SELECT id, 
                   CONCAT(first_name, ' ', last_name) AS Full_Name, 
                   email AS Email, 
                   contact_type AS Lids, 
                   source AS Tags, 
                   status AS Status, 
                   DATE(created_at) AS reg_date
            FROM wpv3_fc_subscribers 
            ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
    $contacts = [];
}

// Include header
require_once __DIR__ . '/includes/header.php';
?>

<style>
.customer-page {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.page-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #e8f5e8;
}

.page-header h1 {
    color: #2d5a27;
    font-size: 2rem;
    margin-bottom: 1rem;
}

.page-header p {
    color: #666;
    font-size: 1rem;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

.contacts-table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    overflow: hidden;
}

.contacts-table {
    width: 100%;
    border-collapse: collapse;
}

.contacts-table th {
    background: #2d5a27;
    color: white;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.contacts-table td {
    padding: 1rem;
    border-bottom: 1px solid #e8f5e8;
    color: #333;
}

.contacts-table tr:hover {
    background: #f8fdf8;
}

.contacts-table tr:last-child td {
    border-bottom: none;
}

.status-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-subscribed {
    background: #d4edda;
    color: #155724;
}

.status-unsubscribed {
    background: #f8d7da;
    color: #721c24;
}

.status-lead {
    background: #d1ecf1;
    color: #0c5460;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.no-data {
    text-align: center;
    padding: 3rem;
    color: #666;
}

.no-data i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.error-message {
    background: #f8d7da;
    color: #721c24;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 2rem;
    text-align: center;
}

.table-responsive {
    overflow-x: auto;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card i {
    font-size: 2rem;
    color: #2d5a27;
    margin-bottom: 0.5rem;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: bold;
    color: #2d5a27;
    margin: 0.5rem 0;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.email-link {
    color: #2d5a27;
    text-decoration: none;
}

.email-link:hover {
    text-decoration: underline;
}

.db-info {
    background: #e8f5e8;
    padding: 0.8rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #2d5a27;
    text-align: center;
}

.lids-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    background: #e8f5e8;
    color: #2d5a27;
}

.source-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    background: #e3f2fd;
    color: #1565c0;
}

@media (max-width: 768px) {
    .contacts-table th,
    .contacts-table td {
        padding: 0.5rem;
        font-size: 0.8rem;
    }
    
    .page-header h1 {
        font-size: 1.8rem;
    }
    
    .stats-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="customer-page">
    <div class="page-header">
        <h1><i class="fas fa-users"></i> Our Customers</h1>
        <p>Meet our valued customers and community members who support sustainable living</p>
    </div>

    <!-- Database connection info -->
    <div class="db-info">
        <i class="fas fa-database"></i> Connected to remote database: if0_39943908_wp16 (Table: wpv3_fc_subscribers)
    </div>

    <?php if (isset($error)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-triangle"></i>
            <p><?php echo escapeOutput($error); ?></p>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <i class="fas fa-user-friends"></i>
            <div class="stat-number"><?php echo count($contacts); ?></div>
            <div class="stat-label">Total Subscribers</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock"></i>
            <?php
            $pendingCount = count(array_filter($contacts, fn($contact) => 
                ($contact['Status'] ?? '') === 'pending'));
            ?>
            <div class="stat-number"><?php echo $pendingCount; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-envelope"></i>
            <div class="stat-number"><?php echo count(array_unique(array_column($contacts, 'Email'))); ?></div>
            <div class="stat-label">Unique Emails</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-chart-line"></i>
            <?php
            $leadCount = count(array_filter($contacts, fn($contact) => 
                ($contact['Lids'] ?? '') === 'lead'));
            ?>
            <div class="stat-number"><?php echo $leadCount; ?></div>
            <div class="stat-label">Leads</div>
        </div>
    </div>

    <!-- Contacts Table -->
    <div class="contacts-table-container">
        <?php if (!empty($contacts)): ?>
            <div class="table-responsive">
                <table class="contacts-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td><?php echo escapeOutput($contact['id']); ?></td>
                                <td>
                                    <strong><?php echo escapeOutput($contact['Full_Name'] ?? 'N/A'); ?></strong>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo escapeOutput($contact['Email']); ?>" class="email-link">
                                        <i class="fas fa-envelope"></i>
                                        <?php echo escapeOutput($contact['Email']); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="lids-badge">
                                        <?php echo escapeOutput($contact['Lids'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="source-badge">
                                        <?php echo escapeOutput($contact['Tags'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $status = $contact['Status'] ?? '';
                                    $statusClass = strtolower($status);
                                    ?>
                                    <span class="status-badge status-<?php echo escapeOutput($statusClass); ?>">
                                        <?php echo escapeOutput($status); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($contact['reg_date'])) {
                                        echo date('M j, Y', strtotime($contact['reg_date']));
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-users"></i>
                <h3>No Customer Data Available</h3>
                <p>There are currently no subscriber records in the database.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
require_once __DIR__ . '/includes/footer.php';
?>