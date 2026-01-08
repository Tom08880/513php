<?php
require_once 'admin_header.php';

// Database connection parameters
$host = 'sql100.infinityfree.com';
$dbname = 'if0_39943908_wp16';
$username = 'if0_39943908';
$password = 'l3fA9Em7PP';

// Initialize users array
$users = [];

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get users from database
    $sql = "SELECT id, first_name, last_name, email, phone, status, created_at 
            FROM wpv3_fc_subscribers 
            ORDER BY id DESC";
    
    $stmt = $pdo->query($sql);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Combine first and last name
        $full_name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
        if (empty($full_name)) {
            $full_name = 'Unknown User';
        }
        
        // Format date
        $join_date = $row['created_at'] ?? date('Y-m-d');
        
        // For now, we don't have orders count in the database, so we'll set to 0
        $orders = 0;
        
        $users[] = [
            'id' => $row['id'],
            'name' => $full_name,
            'email' => $row['email'] ?? '',
            'phone' => $row['phone'] ?? 'N/A',
            'status' => $row['status'] ?? 'pending',
            'join_date' => $join_date,
            'orders' => $orders
        ];
    }
} catch (PDOException $e) {
    // If database connection fails, use sample data as fallback
    error_log("Database connection failed: " . $e->getMessage());
    
    // Fallback to sample data
    $users = [
        [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123-456-7890',
            'status' => 'active',
            'join_date' => '2023-01-15',
            'orders' => 5
        ],
        [
            'id' => 2,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '098-765-4321',
            'status' => 'active',
            'join_date' => '2023-02-20',
            'orders' => 12
        ],
        [
            'id' => 3,
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'phone' => '555-123-4567',
            'status' => 'inactive',
            'join_date' => '2023-03-10',
            'orders' => 2
        ],
        [
            'id' => 4,
            'name' => 'Alice Williams',
            'email' => 'alice@example.com',
            'phone' => '555-987-6543',
            'status' => 'active',
            'join_date' => '2023-04-05',
            'orders' => 8
        ],
        [
            'id' => 5,
            'name' => 'Charlie Brown',
            'email' => 'charlie@example.com',
            'phone' => '555-456-7890',
            'status' => 'active',
            'join_date' => '2023-05-18',
            'orders' => 3
        ]
    ];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_user'])) {
        $user_id = intval($_POST['user_id']);
        
        try {
            // Update user in database
            $sql = "UPDATE wpv3_fc_subscribers 
                    SET first_name = ?, last_name = ?, email = ?, phone = ?, status = ?, updated_at = NOW() 
                    WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            
            // Parse full name into first and last name
            $full_name = htmlspecialchars($_POST['name']);
            $name_parts = explode(' ', $full_name, 2);
            $first_name = $name_parts[0];
            $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
            
            $stmt->execute([
                $first_name,
                $last_name,
                htmlspecialchars($_POST['email']),
                htmlspecialchars($_POST['phone']),
                htmlspecialchars($_POST['status']),
                $user_id
            ]);
            
            $_SESSION['success'] = 'User updated successfully!';
        } catch (Exception $e) {
            error_log("Update user error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update user. Error: ' . $e->getMessage();
        }
    }
    
    // Redirect to avoid form resubmission
    header('Location: users.php');
    exit();
}
?>

<div class="admin-card">
    <h1>User Management</h1>
    <p>View and manage user accounts</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
    <!-- User Form -->
    <div class="admin-card">
        <h2>Edit User</h2>
        <form method="POST" style="margin-top: 15px;">
            <input type="hidden" name="user_id" id="user_id" value="">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Full Name</label>
                <input type="text" name="name" id="user_name" required 
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email</label>
                <input type="email" name="email" id="user_email" required
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Phone</label>
                <input type="text" name="phone" id="user_phone"
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Status</label>
                <select name="status" id="user_status" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="subscribed">Subscribed</option>
                    <option value="pending">Pending</option>
                    <option value="unsubscribed">Unsubscribed</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <button type="submit" name="update_user" style="padding: 10px 20px; background: #2196f3; color: white; border: none; border-radius: 4px; cursor: pointer;">Update User</button>
        </form>
    </div>
    
    <!-- Users List -->
    <div class="admin-card">
        <h2>Users (<?php echo count($users); ?>)</h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">ID</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Email</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Phone</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Status</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Joined</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): 
                        // Determine status color
                        $status_color = '#2d5a27';
                        $status_bg = '#e8f5e9';
                        if (in_array(strtolower($user['status']), ['inactive', 'unsubscribed', 'pending'])) {
                            $status_color = '#c62828';
                            $status_bg = '#ffebee';
                        }
                    ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo $user['id']; ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($user['name']); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.9rem; 
                                  background: <?php echo $status_bg; ?>; 
                                  color: <?php echo $status_color; ?>;">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <?php 
                            try {
                                $date = new DateTime($user['join_date']);
                                echo $date->format('M j, Y');
                            } catch (Exception $e) {
                                echo htmlspecialchars($user['join_date']);
                            }
                            ?>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <button onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)" 
                                    style="padding: 5px 10px; background: #2196f3; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">
                                Edit
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('user_id').value = user.id;
    document.getElementById('user_name').value = user.name;
    document.getElementById('user_email').value = user.email;
    document.getElementById('user_phone').value = user.phone;
    document.getElementById('user_status').value = user.status;
    
    // Scroll to form
    document.querySelector('.admin-card:first-child').scrollIntoView({ behavior: 'smooth' });
}
</script>

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