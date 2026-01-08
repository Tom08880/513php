<?php
// File: includes/footer.php
?>
    </main>
        
    <footer style="background: #1e4023; color: white; padding: 30px 0; margin-top: 40px;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-bottom: 30px;">
                <div>
                    <h3 style="margin-bottom: 15px; color: white; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-leaf"></i> EcoStore
                    </h3>
                    <p style="color: rgba(255,255,255,0.8);">Sustainable products for a better tomorrow.</p>
                </div>
                
                <div>
                    <h4 style="margin-bottom: 15px; color: white;">Quick Links</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 8px;"><a href="<?php echo BASE_URL; ?>index.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
                        <li style="margin-bottom: 8px;"><a href="<?php echo BASE_URL; ?>products/" style="color: rgba(255,255,255,0.8); text-decoration: none;">Products</a></li>
                        <li style="margin-bottom: 8px;"><a href="<?php echo BASE_URL; ?>careers.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Careers</a></li>
                        <li style="margin-bottom: 8px;"><a href="<?php echo BASE_URL; ?>about.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">About</a></li>
                        <li style="margin-bottom: 8px;"><a href="<?php echo BASE_URL; ?>contact.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 style="margin-bottom: 15px; color: white;">Help</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 8px;"><a href="<?php echo BASE_URL; ?>contact.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Support</a></li>
                    </ul>
                </div>
            </div>
            
            <div style="text-align: center; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <p style="color: rgba(255,255,255,0.8);">&copy; <?php echo date('Y'); ?> EcoStore. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
    // Add any global JavaScript here
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alerts after 5 seconds
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