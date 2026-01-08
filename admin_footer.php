    </div> <!-- Close admin-container -->
    
    <!-- Footer -->
    <footer style="background: #1e4023; color: white; padding: 30px 0; margin-top: 40px;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-bottom: 30px;">
                <div>
                    <h3 style="margin-bottom: 15px; color: white; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-leaf"></i> EcoAdmin
                    </h3>
                    <p style="color: rgba(255,255,255,0.8);">Administration panel for EcoStore management.</p>
                </div>
                
                <div>
                    <h4 style="margin-bottom: 15px; color: white;">Admin Links</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 8px;"><a href="index.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Dashboard</a></li>
                        <li style="margin-bottom: 8px;"><a href="products.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Products</a></li>
                        <li style="margin-bottom: 8px;"><a href="users.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Users</a></li>
                        <li style="margin-bottom: 8px;"><a href="analytics.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Analytics</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 style="margin-bottom: 15px; color: white;">Quick Access</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 8px;"><a href="../index.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Main Store</a></li>
                        <li style="margin-bottom: 8px;"><a href="../contact.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Contact</a></li>
                    </ul>
                </div>
            </div>
            
            <div style="text-align: center; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <p style="color: rgba(255,255,255,0.8);">&copy; <?php echo date('Y'); ?> EcoStore Admin Panel. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    if (alert.parentElement) {
                        alert.remove();
                    }
                }, 500);
            }, 5000);
        });
    });
    </script>
</body>
</html>