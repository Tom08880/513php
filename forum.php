<?php
// File: forum.php

session_start();

// Include required files
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

// Simple admin check (for demo purposes)
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Get posts from database
$posts = [];
$total_posts = 0;
$success_msg = '';
$error_msg = '';

// Clear session messages
if (isset($_SESSION['success'])) {
    $success_msg = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error_msg = $_SESSION['error'];
    unset($_SESSION['error']);
}

try {
    $conn = getDB();
    
    // Create posts table if not exists
    $conn->exec("CREATE TABLE IF NOT EXISTS forum_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        username VARCHAR(100) NOT NULL,
        title VARCHAR(200) NOT NULL,
        content TEXT NOT NULL,
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Get total posts count
    $stmt = $conn->query("SELECT COUNT(*) as count FROM forum_posts");
    $result = $stmt->fetch();
    $total_posts = $result['count'] ?? 0;
    
    // Get recent posts
    $stmt = $conn->query("SELECT * FROM forum_posts ORDER BY created_at DESC LIMIT 50");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error_msg = 'Error loading posts. Please try again later.';
}

// Include header
require_once __DIR__ . '/includes/header.php';
?>

<style>
/* Forum Styles */
.forum-header {
    background: linear-gradient(to right, #2d5a27, #4a7c45);
    color: white;
    padding: 3rem 1rem;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 2rem;
}

.forum-header h1 {
    font-size: 2.2rem;
    margin-bottom: 1rem;
}

.forum-container {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
}

.sidebar {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}

.forum-main {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}

/* Post Form */
.post-form {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    border: 1px solid #eaeaea;
}

.post-form input,
.post-form textarea {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
    margin-bottom: 1rem;
    font-family: inherit;
}

.post-form input:focus,
.post-form textarea:focus {
    outline: none;
    border-color: #4a7c45;
    box-shadow: 0 0 0 3px rgba(74, 124, 69, 0.1);
}

.post-form textarea {
    resize: vertical;
    min-height: 120px;
}

.btn {
    background: #2d5a27;
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn:hover {
    background: #1e4023;
}

.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

/* Posts List */
.posts-list {
    margin-top: 2rem;
}

.post-item {
    background: white;
    border: 1px solid #eaeaea;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.post-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.post-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.post-user {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: #4a7c45;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1rem;
}

.post-meta {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.post-date {
    color: #666;
    font-size: 0.9rem;
}

.post-title {
    color: #2d5a27;
    font-size: 1.2rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.post-content {
    color: #444;
    line-height: 1.6;
    margin-bottom: 1rem;
    white-space: pre-wrap;
}

.post-stats {
    display: flex;
    gap: 1.5rem;
    font-size: 0.9rem;
    color: #666;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.login-prompt {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 2rem;
}

.stats-item {
    padding: 0.8rem 0;
    border-bottom: 1px solid #eee;
}

.empty-state {
    text-align: center;
    color: #666;
    padding: 3rem 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 2px dashed #ddd;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
    color: #4a7c45;
}

/* Admin Panel */
.admin-panel {
    background: #ffc107;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border-left: 4px solid #ff9800;
}

.admin-panel h3 {
    color: #856404;
    margin-bottom: 0.5rem;
}

.admin-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-admin {
    background: #856404;
    color: white;
}

.btn-admin:hover {
    background: #6c5203;
}

/* Character Counter */
.char-counter {
    text-align: right;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .forum-container {
        grid-template-columns: 1fr;
    }
    
    .forum-header h1 {
        font-size: 1.8rem;
    }
    
    .post-header {
        flex-direction: column;
        gap: 0.5rem;
    }
}

@media (max-width: 480px) {
    .forum-main, .sidebar {
        padding: 1rem;
    }
    
    .post-user {
        flex-direction: column;
        text-align: center;
    }
    
    .user-avatar {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }
}
</style>

<!-- Admin Panel (only for admin) -->
<?php if (isAdmin()): ?>
    <div class="admin-panel">
        <h3><i class="fas fa-cogs"></i> Admin Controls</h3>
        <div class="admin-actions">
            <a href="<?php echo BASE_URL; ?>generate_posts.php?password=admin123" class="btn btn-admin btn-small" onclick="return confirm('Generate 20 sample posts?')">
                <i class="fas fa-magic"></i> Generate Sample Posts
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Forum Header -->
<div class="forum-header">
    <h1><i class="fas fa-comments"></i> Community Forum</h1>
    <p>Share your thoughts and discuss sustainable living</p>
</div>

<!-- Display Messages -->
<?php if ($success_msg): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?>
    </div>
<?php endif; ?>

<?php if ($error_msg): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_msg); ?>
    </div>
<?php endif; ?>

<div class="forum-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h3><i class="fas fa-chart-bar"></i> Forum Statistics</h3>
        <div class="stats-item">
            <strong>Total Posts:</strong> <?php echo $total_posts; ?>
        </div>
        <div class="stats-item">
            <strong>Status:</strong> <?php echo isLoggedIn() ? 'Logged In' : 'Guest'; ?>
        </div>
        
        <?php if (isLoggedIn()): ?>
        <div class="stats-item">
            <strong>Your Posts:</strong> 
            <?php 
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM forum_posts WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id'] ?? 0]);
                $result = $stmt->fetch();
                echo $result['count'] ?? 0;
            } catch (Exception $e) {
                echo '0';
            }
            ?>
        </div>
        <?php endif; ?>
        
        <h3 style="margin-top:1.5rem;"><i class="fas fa-info-circle"></i> Forum Rules</h3>
        <div class="stats-item">
            <p style="font-size:0.9rem;margin:0.3rem 0;">✓ Be respectful to others</p>
            <p style="font-size:0.9rem;margin:0.3rem 0;">✓ Stay on topic</p>
            <p style="font-size:0.9rem;margin:0.3rem 0;">✓ No spam or advertising</p>
            <p style="font-size:0.9rem;margin:0.3rem 0;">✓ Keep discussions eco-friendly</p>
        </div>
    </div>
    
    <!-- Main Forum Area -->
    <div class="forum-main">
        <!-- New Post Form -->
        <?php if (isLoggedIn()): ?>
            <div class="post-form">
                <h3><i class="fas fa-edit"></i> Create New Post</h3>
                <form method="POST" action="<?php echo BASE_URL; ?>save_post.php" id="postForm">
                    <input type="text" name="title" id="postTitle" placeholder="Post Title" required maxlength="200">
                    <div class="char-counter" id="titleCounter">0/200 characters</div>
                    
                    <textarea name="content" id="postContent" placeholder="Share your thoughts, questions, or ideas..." required minlength="10"></textarea>
                    <div class="char-counter" id="contentCounter">0 characters</div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-paper-plane"></i> Create Post
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="login-prompt">
                <h3><i class="fas fa-sign-in-alt"></i> Login Required</h3>
                <p>You need to be logged in to create new posts.</p>
                <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn" style="margin-top: 1rem;">
                    <i class="fas fa-sign-in-alt"></i> Login to Continue
                </a>
            </div>
        <?php endif; ?>
        
        <!-- Posts List -->
        <div class="posts-list">
            <h3><i class="fas fa-list"></i> Recent Posts</h3>
            
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <i class="fas fa-comment-alt"></i>
                    <h3 style="color: #4a7c45; margin-bottom: 0.5rem;">No posts yet</h3>
                    <p>Be the first to start a discussion!</p>
                    <?php if (!isLoggedIn()): ?>
                        <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn" style="margin-top: 1rem;">
                            <i class="fas fa-sign-in-alt"></i> Login to Post
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-item">
                        <div class="post-header">
                            <div class="post-user">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($post['username'] ?? 'U', 0, 1)); ?>
                                </div>
                                <div>
                                    <strong style="font-size: 1rem;"><?php echo htmlspecialchars($post['username'] ?? 'Anonymous'); ?></strong>
                                    <p style="font-size: 0.9rem; color: #666;">Member</p>
                                </div>
                            </div>
                            <div class="post-meta">
                                <span class="post-date">
                                    <i class="far fa-clock"></i> 
                                    <?php 
                                    if (isset($post['created_at'])) {
                                        echo date('M j, Y g:i A', strtotime($post['created_at']));
                                    } else {
                                        echo 'Recently';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <h3 class="post-title"><?php echo htmlspecialchars($post['title'] ?? 'Untitled'); ?></h3>
                        
                        <div class="post-content">
                            <?php echo nl2br(htmlspecialchars($post['content'] ?? '')); ?>
                        </div>
                        
                        <div class="post-stats">
                            <span><i class="far fa-eye"></i> <?php echo $post['views'] ?? 0; ?> views</span>
                            <span><i class="fas fa-user"></i> Posted by <?php echo htmlspecialchars($post['username'] ?? 'Anonymous'); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Character counters
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('postTitle');
    const titleCounter = document.getElementById('titleCounter');
    const contentInput = document.getElementById('postContent');
    const contentCounter = document.getElementById('contentCounter');
    
    if (titleInput && titleCounter) {
        titleInput.addEventListener('input', function() {
            const length = this.value.length;
            titleCounter.textContent = length + '/200 characters';
            
            if (length > 200) {
                titleCounter.style.color = '#c62828';
            } else if (length > 180) {
                titleCounter.style.color = '#ff9800';
            } else {
                titleCounter.style.color = '#666';
            }
        });
        
        // Initialize
        titleCounter.textContent = titleInput.value.length + '/200 characters';
    }
    
    if (contentInput && contentCounter) {
        contentInput.addEventListener('input', function() {
            const length = this.value.length;
            contentCounter.textContent = length + ' characters';
            
            if (length < 10) {
                contentCounter.style.color = '#c62828';
            } else {
                contentCounter.style.color = '#666';
            }
        });
        
        // Initialize
        contentCounter.textContent = contentInput.value.length + ' characters';
    }
    
    // Form validation
    const postForm = document.getElementById('postForm');
    if (postForm) {
        postForm.addEventListener('submit', function(e) {
            const title = document.getElementById('postTitle')?.value.trim() || '';
            const content = document.getElementById('postContent')?.value.trim() || '';
            
            if (title.length === 0) {
                e.preventDefault();
                alert('Please enter a title for your post.');
                return false;
            }
            
            if (title.length > 200) {
                e.preventDefault();
                alert('Title cannot exceed 200 characters.');
                return false;
            }
            
            if (content.length < 10) {
                e.preventDefault();
                alert('Content must be at least 10 characters.');
                return false;
            }
            
            return true;
        });
    }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>