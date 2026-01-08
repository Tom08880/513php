<?php
// File: includes/header.php
// Path: /513WEEK7/includes/header.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoStore - Sustainable Living</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset and Base Styles */
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
        
        /* Header Styles */
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
        
        .logo i {
            font-size: 22px;
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
        
        .nav-link:hover {
            background: rgba(255,255,255,0.15);
        }
        
        .nav-link.active {
            background: rgba(255,255,255,0.2);
            font-weight: bold;
        }
        
        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 15px;
            padding-left: 15px;
            border-left: 1px solid rgba(255,255,255,0.2);
        }
        
        .welcome-text {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .main-content {
            flex: 1;
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
            width: 100%;
        }
        
        /* Alert Messages */
        .alert {
            max-width: 1200px;
            margin: 15px auto;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2d5a27;
            border-left: 4px solid #4a7c45;
        }
        
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }
        
        /* Dropdown for analytics */
        .dropdown {
            position: relative;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background: white;
            min-width: 180px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 5px;
            z-index: 1001;
            top: 100%;
            left: 0;
            overflow: hidden;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        .dropdown-link {
            color: #333;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #eee;
        }
        
        .dropdown-link:hover {
            background: #f8f9fa;
        }
        
        .dropdown-link i {
            color: #4a7c45;
            width: 20px;
        }
        
        /* Responsive */
        @media (max-width: 900px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }
            
            .main-nav {
                justify-content: center;
            }
            
            .user-section {
                margin-left: 0;
                padding-left: 0;
                border-left: none;
                border-top: 1px solid rgba(255,255,255,0.2);
                padding-top: 15px;
                width: 100%;
                justify-content: center;
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
            
            .dropdown-content {
                position: static;
                box-shadow: none;
                background: rgba(255,255,255,0.1);
            }
            
            .dropdown-link {
                color: white;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <!-- Logo -->
            <a href="<?php echo BASE_URL; ?>index.php" class="logo">
                <i class="fas fa-leaf"></i> EcoStore
            </a>
            
            <!-- Main Navigation -->
            <nav class="main-nav">
                <a href="<?php echo BASE_URL; ?>index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="<?php echo BASE_URL; ?>products/" class="nav-link">
                    <i class="fas fa-store"></i> Products
                </a>
                <a href="<?php echo BASE_URL; ?>forum.php" class="nav-link">
                    <i class="fas fa-comments"></i> Forum
                </a>
                <a href="<?php echo BASE_URL; ?>customer.php" class="nav-link">
                    <i class="fas fa-users"></i> Customer
                </a>
                
                <!-- Analytics Dropdown -->
                <div class="dropdown">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-bar"></i> Analytics <i class="fas fa-caret-down"></i>
                    </a>
                    <div class="dropdown-content">
                        <a href="<?php echo BASE_URL; ?>analytics/churn_analysis.php" class="dropdown-link">
                            <i class="fas fa-chart-line"></i> Churn Analysis
                        </a>
                        <?php if (isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                        <a href="<?php echo BASE_URL; ?>admin/analytics.php" class="dropdown-link">
                            <i class="fas fa-chart-pie"></i> Admin Analytics
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <a href="<?php echo BASE_URL; ?>careers.php" class="nav-link">
                    <i class="fas fa-briefcase"></i> Careers
                </a>
                <a href="<?php echo BASE_URL; ?>about.php" class="nav-link">
                    <i class="fas fa-info-circle"></i> About
                </a>
                <a href="<?php echo BASE_URL; ?>contact.php" class="nav-link">
                    <i class="fas fa-envelope"></i> Contact
                </a>
                
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo BASE_URL; ?>cart/" class="nav-link">
                        <i class="fas fa-shopping-cart"></i> Cart
                    </a>
                <?php endif; ?>
            </nav>
            
            <!-- User Section -->
            <div class="user-section">
                <?php if (isLoggedIn()): ?>
                    <span class="welcome-text">
                        Hi, <?php echo getUsername(); ?>
                    </span>
                    <a href="<?php echo BASE_URL; ?>user/profile.php" class="nav-link">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                    <a href="<?php echo BASE_URL; ?>admin/" class="nav-link">
                        <i class="fas fa-cog"></i> Admin
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>auth/logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>auth/login.php" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="<?php echo BASE_URL; ?>auth/register.php" class="nav-link">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <!-- Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="main-content">