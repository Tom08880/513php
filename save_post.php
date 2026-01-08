<?php
// File: save_post.php

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['error'] = 'Please login to create posts.';
    header("Location: " . BASE_URL . "forum.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request.';
    header("Location: " . BASE_URL . "forum.php");
    exit();
}

// Get form data
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

// Validate input
if (empty($title) || empty($content)) {
    $_SESSION['error'] = 'Title and content are required.';
    header("Location: " . BASE_URL . "forum.php");
    exit();
}

if (strlen($title) > 200) {
    $_SESSION['error'] = 'Title cannot exceed 200 characters.';
    header("Location: " . BASE_URL . "forum.php");
    exit();
}

if (strlen($content) < 10) {
    $_SESSION['error'] = 'Content must be at least 10 characters.';
    header("Location: " . BASE_URL . "forum.php");
    exit();
}

// Include database config
require_once __DIR__ . '/config/database.php';

// Get database connection
$conn = getDB();
if (!$conn) {
    $_SESSION['error'] = 'Database connection failed.';
    header("Location: " . BASE_URL . "forum.php");
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['user_name'] ?? 'Anonymous';

// Prepare data for insertion
$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
$content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

// Insert post into database
try {
    // Create table if not exists
    $conn->exec("CREATE TABLE IF NOT EXISTS forum_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        username VARCHAR(100) NOT NULL,
        title VARCHAR(200) NOT NULL,
        content TEXT NOT NULL,
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $sql = "INSERT INTO forum_posts (user_id, username, title, content) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $username, $title, $content]);
    
    $_SESSION['success'] = 'Your post has been created successfully!';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to create post. Please try again.';
}

// Redirect back to forum
header("Location: " . BASE_URL . "forum.php");
exit();
?>