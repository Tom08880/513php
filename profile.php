<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define BASE_URL
if (!defined('BASE_URL')) {
    define('BASE_URL', '/sanshang/513week7/');
}

// Include functions and check login
require_once __DIR__ . '/../includes/functions.php';
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Please login to access this page.';
    header("Location: " . BASE_URL . "auth/login.php");
    exit();
}

// Get user info from session
$user_id = getUserId();
$username = getUsername();
$user_email = $_SESSION['user_email'] ?? '';
$is_admin = $_SESSION['is_admin'] ?? false;

// Initialize user data
$user = [
    'full_name' => $username,
    'email' => $user_email,
    'total_points' => 0,
    'status' => 'active',
    'created_at' => date('Y-m-d'),
    'eco_level' => 'Novice',
    'carbon_saved' => 0
];

// Try to get additional data from database
try {
    require_once __DIR__ . '/../config/database.php';
    $db = Database::getConnectionStatic();
    
    $stmt = $db->prepare("SELECT first_name, last_name, email, total_points, status, created_at FROM wpv3_fc_subscribers WHERE email = ? OR id = ? LIMIT 1");
    $stmt->execute([$user_email, $user_id]);
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $user['full_name'] = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: $username;
        $user['email'] = $row['email'] ?? $user_email;
        $user['total_points'] = $row['total_points'] ?? 0;
        $user['status'] = $row['status'] ?? 'active';
        $user['created_at'] = $row['created_at'] ?? date('Y-m-d');
        
        // Calculate eco level based on points
        if ($user['total_points'] >= 1000) $user['eco_level'] = 'Eco Champion';
        elseif ($user['total_points'] >= 500) $user['eco_level'] = 'Green Warrior';
        elseif ($user['total_points'] >= 100) $user['eco_level'] = 'Eco Friend';
        
        // Calculate estimated carbon saved (example: 2kg per 100 points)
        $user['carbon_saved'] = round(($user['total_points'] / 100) * 2, 1);
    }
} catch (Exception $e) {
    error_log("Profile DB Error: " . $e->getMessage());
}

// Include header
require_once __DIR__ . '/../includes/header.php';
?>

<style>
/* Eco theme colors */
:root {
    --eco-green: #2d5a27;
    --eco-green-light: #4a7c45;
    --eco-green-bg: #f5f9f4;
    --eco-leaf: #6a994e;
    --eco-earth: #a98467;
    --eco-sky: #84a98c;
}

/* Nature background with subtle leaf pattern */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%232d5a27' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E"),
        linear-gradient(135deg, rgba(245,249,244,0.95) 0%, rgba(232,245,233,0.95) 100%);
    z-index: -1;
    pointer-events: none;
}

/* Floating leaf decorations */
.leaf-decoration {
    position: fixed;
    font-size: 2.5rem;
    opacity: 0.07;
    z-index: -1;
    pointer-events: none;
    animation: floatLeaf 20s infinite linear;
}

.leaf-1 { top: 15%; left: 5%; animation-delay: 0s; }
.leaf-2 { top: 25%; right: 8%; animation-delay: -5s; }
.leaf-3 { bottom: 20%; left: 10%; animation-delay: -10s; }
.leaf-4 { bottom: 30%; right: 5%; animation-delay: -15s; }

@keyframes floatLeaf {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    25% { transform: translateY(-20px) rotate(5deg); }
    50% { transform: translateY(0) rotate(10deg); }
    75% { transform: translateY(20px) rotate(5deg); }
}

/* Main profile container */
.profile-container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 0 20px;
    position: relative;
}

/* Profile header with nature theme */
.profile-header {
    background: linear-gradient(135deg, var(--eco-green) 0%, var(--eco-green-light) 100%);
    color: white;
    padding: 35px;
    border-radius: 20px;
    margin-bottom: 35px;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(45, 90, 39, 0.2);
}

.profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 25px 25px;
    opacity: 0.3;
    animation: moveBackground 60s linear infinite;
}

@keyframes moveBackground {
    0% { transform: translate(0, 0); }
    100% { transform: translate(25px, 25px); }
}

.profile-header h1 {
    font-size: 2.2rem;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    position: relative;
}

.profile-header .welcome-text {
    font-size: 1.2rem;
    opacity: 0.95;
    margin-bottom: 20px;
}

/* Eco badges */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.95rem;
    margin: 0 5px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.badge-admin {
    background: linear-gradient(to right, gold, #ffd700);
    color: #333;
}

.badge-level {
    background: linear-gradient(to right, var(--eco-leaf), var(--eco-sky));
    color: white;
}

/* Content layout */
.profile-content {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 30px;
    margin-bottom: 40px;
}

@media (max-width: 768px) {
    .profile-content {
        grid-template-columns: 1fr;
    }
}

/* Sidebar */
.sidebar {
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(74, 124, 69, 0.15);
    position: relative;
    overflow: hidden;
}

.sidebar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    background: linear-gradient(to bottom, var(--eco-green), var(--eco-leaf));
}

.user-card {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 25px;
    border-bottom: 1px solid rgba(74, 124, 69, 0.2);
}

.user-avatar {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, var(--eco-green-light), var(--eco-green));
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    font-weight: bold;
    margin: 0 auto 20px;
    box-shadow: 0 8px 20px rgba(74, 124, 69, 0.4);
    position: relative;
    transition: transform 0.3s ease;
}

.user-avatar:hover {
    transform: scale(1.05);
}

.user-avatar::after {
    content: '🌱';
    position: absolute;
    bottom: -5px;
    right: -5px;
    font-size: 1.4rem;
    background: white;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 3px 8px rgba(0,0,0,0.2);
}

.user-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 25px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: linear-gradient(135deg, #f8f9fa, #f0f7f0);
    border-radius: 12px;
    border: 1px solid rgba(74, 124, 69, 0.1);
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-color: var(--eco-leaf);
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--eco-green);
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
}

/* Navigation */
.profile-nav {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 25px;
}

.nav-item {
    background: linear-gradient(to right, #f8f9fa, #f0f7f0);
    border: 1px solid rgba(74, 124, 69, 0.15);
    padding: 14px 20px;
    border-radius: 12px;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 15px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.nav-item:hover, .nav-item.active {
    background: linear-gradient(to right, var(--eco-green), var(--eco-green-light));
    color: white;
    transform: translateX(8px);
    border-color: var(--eco-green);
}

.nav-item i {
    width: 24px;
    text-align: center;
    font-size: 1.1rem;
}

/* Main content */
.main-content {
    background: white;
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(74, 124, 69, 0.15);
}

.section-title {
    color: var(--eco-green);
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid rgba(74, 124, 69, 0.2);
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.5rem;
}

.section-title i {
    color: var(--eco-leaf);
}

/* Info cards grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 25px;
    margin-bottom: 35px;
}

.info-card {
    background: linear-gradient(to bottom right, #f8f9fa, #f0f7f0);
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    border: 1px solid rgba(74, 124, 69, 0.15);
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.info-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 12px 25px rgba(74, 124, 69, 0.15);
    border-color: var(--eco-leaf);
}

.info-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(to right, var(--eco-green), var(--eco-leaf));
}

.info-icon {
    font-size: 2.2rem;
    color: var(--eco-leaf);
    margin-bottom: 20px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}

.info-card:hover .info-icon {
    transform: scale(1.2);
}

.info-card h3 {
    margin: 15px 0 12px;
    color: var(--eco-green);
    font-size: 1.2rem;
    font-weight: 600;
}

.info-card p {
    color: #555;
    font-size: 1rem;
    line-height: 1.5;
}

/* Special highlight cards */
.highlight-card {
    grid-column: span 2;
    background: linear-gradient(135deg, rgba(232, 245, 233, 0.9), rgba(212, 237, 218, 0.9));
    border-left: 8px solid var(--eco-leaf);
}

@media (max-width: 768px) {
    .highlight-card {
        grid-column: span 1;
    }
}

/* Eco progress */
.eco-progress {
    background: white;
    padding: 25px;
    border-radius: 15px;
    margin-top: 30px;
    border: 1px solid rgba(74, 124, 69, 0.15);
}

.progress-bar {
    height: 12px;
    background: #e9ecef;
    border-radius: 6px;
    margin: 15px 0;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(to right, var(--eco-leaf), var(--eco-sky));
    border-radius: 6px;
    transition: width 1s ease-in-out;
}

/* Action buttons */
.action-buttons {
    display: flex;
    gap: 20px;
    margin-top: 40px;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    background: var(--eco-green);
    color: white;
    padding: 14px 30px;
    border-radius: 10px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s ease;
    flex: 1;
    min-width: 180px;
    box-shadow: 0 5px 15px rgba(45, 90, 39, 0.2);
}

.btn:hover {
    background: var(--eco-green-light);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(45, 90, 39, 0.3);
}

.btn-logout {
    background: linear-gradient(to right, #dc3545, #c82333);
}

.btn-logout:hover {
    background: linear-gradient(to right, #c82333, #bd2130);
}

.btn-support {
    background: linear-gradient(to right, #17a2b8, #138496);
}

.btn-shop {
    background: linear-gradient(to right, #28a745, #20c997);
}

.btn-admin {
    background: linear-gradient(to right, #ff9800, #ff5722);
}

.btn-admin:hover {
    background: linear-gradient(to right, #f57c00, #e64a19);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .profile-header {
        padding: 25px 20px;
    }
    
    .profile-header h1 {
        font-size: 1.8rem;
    }
    
    .main-content, .sidebar {
        padding: 25px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
    
    .leaf-decoration {
        display: none;
    }
}

@media (max-width: 480px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .user-stats {
        grid-template-columns: 1fr;
    }
    
    .profile-header h1 {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<!-- Floating leaf decorations -->
<div class="leaf-decoration leaf-1">🍃</div>
<div class="leaf-decoration leaf-2">🌿</div>
<div class="leaf-decoration leaf-3">🍂</div>
<div class="leaf-decoration leaf-4">🌱</div>

<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <h1><i class="fas fa-user-circle"></i> My Eco Profile</h1>
        <p class="welcome-text">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
        <?php if ($is_admin): ?>
            <span class="badge badge-admin"><i class="fas fa-shield-alt"></i> Administrator</span>
        <?php endif; ?>
        <span class="badge badge-level"><i class="fas fa-leaf"></i> <?php echo $user['eco_level']; ?></span>
    </div>
    
    <!-- Main Content -->
    <div class="profile-content">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="user-card">
                <div class="user-avatar">
                    <?php 
                    $initials = '';
                    if (!empty($user['full_name'])) {
                        $name_parts = explode(' ', $user['full_name']);
                        foreach ($name_parts as $part) {
                            if (!empty($part)) {
                                $initials .= strtoupper(substr($part, 0, 1));
                                if (strlen($initials) >= 2) break;
                            }
                        }
                    }
                    echo $initials ?: substr(strtoupper($username), 0, 2);
                    ?>
                </div>
                <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
                
                <div class="user-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $user['total_points']; ?></span>
                        <span class="stat-label">Eco Points</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">
                            <i class="fas fa-leaf" style="color: var(--eco-leaf);"></i>
                        </span>
                        <span class="stat-label"><?php echo ucfirst($user['status']); ?></span>
                    </div>
                </div>
            </div>
            
            <nav class="profile-nav">
                <a href="#personal" class="nav-item active">
                    <i class="fas fa-user"></i> Personal Info
                </a>
                <a href="<?php echo BASE_URL; ?>products/" class="nav-item">
                    <i class="fas fa-store"></i> Browse Products
                </a>
                <?php if ($is_admin): ?>
                    <a href="<?php echo BASE_URL; ?>admin/" class="nav-item">
                        <i class="fas fa-cogs"></i> Admin Panel
                    </a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>contact.php" class="nav-item">
                    <i class="fas fa-headset"></i> Contact Support
                </a>
                <a href="<?php echo BASE_URL; ?>auth/logout.php" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>
        
        <!-- Main Content Area -->
        <div class="main-content">
            <h2 class="section-title"><i class="fas fa-user-shield"></i> Personal Information</h2>
            
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <h3>Full Name</h3>
                    <p><?php echo htmlspecialchars($user['full_name']); ?></p>
                </div>
                
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email Address</h3>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Eco Points</h3>
                    <p><?php echo number_format($user['total_points']); ?> points</p>
                    <?php if ($user['total_points'] > 0): ?>
                        <small style="color: var(--eco-leaf);">Making a difference!</small>
                    <?php endif; ?>
                </div>
                
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Member Since</h3>
                    <p><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                </div>
                
                <div class="info-card highlight-card">
                    <div class="info-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>Eco Level</h3>
                    <p><?php echo $user['eco_level']; ?></p>
                    <div class="eco-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo min(($user['total_points'] / 1000) * 100, 100); ?>%;"></div>
                        </div>
                        <small>Progress to next level</small>
                    </div>
                </div>
                
                <?php if ($user['carbon_saved'] > 0): ?>
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-globe-americas"></i>
                    </div>
                    <h3>Carbon Saved</h3>
                    <p><?php echo $user['carbon_saved']; ?> kg CO₂</p>
                    <small style="color: var(--eco-leaf);">Estimated impact</small>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="action-buttons">
                <a href="<?php echo BASE_URL; ?>auth/logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-support">
                    <i class="fas fa-headset"></i> Contact Support
                </a>
                <a href="<?php echo BASE_URL; ?>products/" class="btn btn-shop">
                    <i class="fas fa-shopping-cart"></i> Shop Eco Products
                </a>
                <?php if ($is_admin): ?>
                    <a href="<?php echo BASE_URL; ?>admin/" class="btn btn-admin">
                        <i class="fas fa-cogs"></i> Admin Panel
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize animations and interactions
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats counter
    const statNumber = document.querySelector('.stat-number');
    if (statNumber && statNumber.textContent.match(/^\d+$/)) {
        const target = parseInt(statNumber.textContent);
        let current = 0;
        const increment = target / 50;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                statNumber.textContent = Math.round(current);
                setTimeout(updateCounter, 30);
            } else {
                statNumber.textContent = target;
            }
        };
        
        setTimeout(updateCounter, 800);
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Add hover effect to info cards
    const infoCards = document.querySelectorAll('.info-card');
    infoCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.info-icon');
            if (icon) {
                icon.style.transform = 'scale(1.2) rotate(5deg)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.info-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });
});
</script>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>