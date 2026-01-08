<?php
// File: careers.php

session_start();

// Include required files
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

// Define upload directory
$uploadDir = __DIR__ . '/uploads/resumes/';

// Create upload directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Process job application form submission
$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_application'])) {
    // Get form data
    $formData = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'position' => trim($_POST['position'] ?? ''),
        'cover_letter' => trim($_POST['cover_letter'] ?? '')
    ];
    
    // Validate required fields
    if (empty($formData['full_name'])) $errors[] = "Full name is required";
    if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($formData['phone'])) $errors[] = "Phone number is required";
    if (empty($formData['position'])) $errors[] = "Please select a position";
    
    // Validate file upload
    $resumeFilename = null;
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['application/pdf', 'application/msword', 
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        $fileType = $_FILES['resume']['type'];
        $fileSize = $_FILES['resume']['size'];
        
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Only PDF and Word documents are allowed";
        } elseif ($fileSize > $maxSize) {
            $errors[] = "File size must be less than 5MB";
        } else {
            $extension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
            $resumeFilename = uniqid('resume_', true) . '.' . $extension;
            $uploadPath = $uploadDir . $resumeFilename;
            
            if (!move_uploaded_file($_FILES['resume']['tmp_name'], $uploadPath)) {
                $errors[] = "Failed to upload resume";
                $resumeFilename = null;
            }
        }
    } else {
        $errors[] = "Resume file is required";
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        try {
            $conn = getDB();
            
            // Create table if not exists
            $conn->exec("CREATE TABLE IF NOT EXISTS job_applications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                position VARCHAR(100) NOT NULL,
                cover_letter TEXT,
                resume_filename VARCHAR(255),
                application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status ENUM('pending', 'reviewed', 'rejected', 'accepted') DEFAULT 'pending'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            $stmt = $conn->prepare("INSERT INTO job_applications (full_name, email, phone, position, cover_letter, resume_filename) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $formData['full_name'],
                $formData['email'], 
                $formData['phone'],
                $formData['position'],
                $formData['cover_letter'],
                $resumeFilename
            ]);
            
            $successMessage = "Thank you for your application! We'll review it and get back to you soon.";
            
        } catch (PDOException $e) {
            $errors[] = "An error occurred. Please try again.";
        }
    }
}

// Include header
require_once __DIR__ . '/includes/header.php';
?>

<style>
/* Careers Page Specific Styles */
.careers-hero {
    background: linear-gradient(rgba(45, 90, 39, 0.9), rgba(74, 124, 69, 0.9));
    color: white;
    text-align: center;
    padding: 4rem 1rem;
    margin-bottom: 2rem;
}

.careers-hero h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.careers-hero p {
    font-size: 1.2rem;
    max-width: 800px;
    margin: 0 auto 2rem;
}

.message {
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem auto;
    text-align: center;
    max-width: 800px;
}

.success {
    background: #e8f5e9;
    color: #2d5a27;
    border: 1px solid #c8e6c9;
}

.error {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #ffcdd2;
}

.job-openings {
    padding: 3rem 1rem;
    background: white;
}

.section-title {
    text-align: center;
    margin-bottom: 3rem;
    color: #2d5a27;
}

.section-title h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.jobs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.job-card {
    background: #f9f9f9;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.job-card:hover {
    transform: translateY(-5px);
}

.job-title {
    color: #2d5a27;
    margin-bottom: 1rem;
    font-size: 1.3rem;
}

.application-form {
    padding: 3rem 1rem;
    background: #f0f7f0;
}

.form-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(45, 90, 39, 0.1);
    max-width: 800px;
    margin: 0 auto;
}

.form-card h2 {
    color: #2d5a27;
    margin-bottom: 2rem;
    text-align: center;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #444;
    display: flex;
    align-items: center;
    gap: 8px;
}

.required:after {
    content: " *";
    color: #c62828;
}

.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #e0e7e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: 0.3s;
    font-family: inherit;
}

.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    outline: none;
    border-color: #2d5a27;
    box-shadow: 0 0 0 3px rgba(45, 90, 39, 0.1);
}

.form-group textarea {
    min-height: 120px;
    resize: vertical;
}

.btn {
    background: #2d5a27;
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    transition: 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn:hover {
    background: #1e4023;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Custom file upload button styles */
.file-upload-wrapper {
    position: relative;
    width: 100%;
    margin-bottom: 5px;
}

.file-upload-input {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}

.file-upload-button {
    background: #2d5a27;
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    z-index: 1;
    position: relative;
}

.file-upload-button:hover {
    background: #1e4023;
}

.file-name {
    margin-left: 15px;
    color: #666;
    font-size: 0.95rem;
}

@media (max-width: 768px) {
    .form-card {
        padding: 1.5rem;
    }
    
    .careers-hero h1 {
        font-size: 2rem;
    }
    
    .file-upload-button {
        width: 100%;
        justify-content: center;
    }
    
    .file-name {
        display: block;
        margin-left: 0;
        margin-top: 10px;
    }
}
</style>

<!-- Hero Section -->
<section class="careers-hero">
    <div class="container">
        <h1>Join Our Green Team</h1>
        <p>Help us build a sustainable future while growing your career in an environmentally-conscious workplace.</p>
    </div>
</section>

<!-- Success/Error Messages -->
<?php if ($successMessage): ?>
<div class="container">
    <div class="message success">
        <i class="fas fa-check-circle"></i>
        <?php echo $successMessage; ?>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="container">
    <div class="message error">
        <i class="fas fa-exclamation-circle"></i>
        <h3>Please fix the following errors:</h3>
        <ul style="list-style: none; margin-top: 10px;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<!-- Job Openings -->
<section class="job-openings">
    <div class="container">
        <div class="section-title">
            <h2>Current Openings</h2>
            <p>We're looking for passionate individuals to join our team</p>
        </div>
        
        <div class="jobs-grid">
            <div class="job-card">
                <h3 class="job-title">Sustainability Manager</h3>
                <div class="job-department" style="color: #666; margin-bottom: 1rem; font-weight: 600;">Environmental Department</div>
                <div class="job-description" style="margin-bottom: 1.5rem;">
                    Lead our sustainability initiatives and help develop eco-friendly policies.
                </div>
                <div class="job-requirements">
                    <h4>Requirements:</h4>
                    <ul style="list-style: none; padding-left: 1rem;">
                        <li style="margin-bottom: 0.5rem; position: relative;">✓ Bachelor's degree in Environmental Science</li>
                        <li style="margin-bottom: 0.5rem; position: relative;">✓ 3+ years experience in sustainability</li>
                        <li style="margin-bottom: 0.5rem; position: relative;">✓ Strong knowledge of environmental regulations</li>
                    </ul>
                </div>
            </div>
            
            <div class="job-card">
                <h3 class="job-title">Eco-Product Developer</h3>
                <div class="job-department" style="color: #666; margin-bottom: 1rem; font-weight: 600;">Product Development</div>
                <div class="job-description" style="margin-bottom: 1.5rem;">
                    Research and develop new sustainable products.
                </div>
                <div class="job-requirements">
                    <h4>Requirements:</h4>
                    <ul style="list-style: none; padding-left: 1rem;">
                        <li style="margin-bottom: 0.5rem; position: relative;">✓ Degree in Product Design or related field</li>
                        <li style="margin-bottom: 0.5rem; position: relative;">✓ Experience with sustainable materials</li>
                        <li style="margin-bottom: 0.5rem; position: relative;">✓ Creative problem-solving skills</li>
                    </ul>
                </div>
            </div>
            
            <div class="job-card">
                <h3 class="job-title">Green Marketing Specialist</h3>
                <div class="job-department" style="color: #666; margin-bottom: 1rem; font-weight: 600;">Marketing Department</div>
                <div class="job-description" style="margin-bottom: 1.5rem;">
                    Promote our sustainable products and communicate our environmental mission.
                </div>
                <div class="job-requirements">
                    <h4>Requirements:</h4>
                    <ul style="list-style: none; padding-left: 1rem;">
                        <li style="margin-bottom: 0.5rem; position: relative;">✓ 2+ years marketing experience</li>
                        <li style="margin-bottom: 0.5rem; position: relative;">✓ Knowledge of sustainable practices</li>
                        <li style="margin-bottom: 0.5rem; position: relative;">✓ Digital marketing skills</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Application Form -->
<section class="application-form">
    <div class="container">
        <div class="form-card">
            <h2>Apply Now</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="full_name" class="required"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" id="full_name" name="full_name" 
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                           placeholder="Enter your full name" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="required"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="your.email@example.com" required>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="required"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                           placeholder="+1 (123) 456-7890" required>
                </div>
                
                <div class="form-group">
                    <label for="position" class="required"><i class="fas fa-briefcase"></i> Position Applied For</label>
                    <select id="position" name="position" required>
                        <option value="">Select a position</option>
                        <option value="sustainability_manager" <?php echo (isset($_POST['position']) && $_POST['position'] == 'sustainability_manager') ? 'selected' : ''; ?>>Sustainability Manager</option>
                        <option value="eco_product_developer" <?php echo (isset($_POST['position']) && $_POST['position'] == 'eco_product_developer') ? 'selected' : ''; ?>>Eco-Product Developer</option>
                        <option value="green_marketing_specialist" <?php echo (isset($_POST['position']) && $_POST['position'] == 'green_marketing_specialist') ? 'selected' : ''; ?>>Green Marketing Specialist</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="cover_letter"><i class="fas fa-file-alt"></i> Cover Letter</label>
                    <textarea id="cover_letter" name="cover_letter" 
                              placeholder="Tell us why you're interested in joining our team..."><?php echo htmlspecialchars($_POST['cover_letter'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="resume" class="required"><i class="fas fa-file-upload"></i> Resume/CV (PDF or DOC, max 5MB)</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" class="file-upload-input" required>
                        <button type="button" class="file-upload-button">
                            <i class="fas fa-file-upload"></i> Upload Resume
                        </button>
                        <span id="file-name" class="file-name">No file selected</span>
                    </div>
                    <small style="color: #666; display: block; margin-top: 5px;">Accepted formats: PDF, DOC, DOCX | Maximum size: 5MB</small>
                </div>
                
                <button type="submit" name="submit_application" class="btn">
                    <i class="fas fa-paper-plane"></i> Submit Application
                </button>
            </form>
        </div>
    </div>
</section>

<script>
// Simple form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    // Custom file upload functionality
    const fileInput = document.getElementById('resume');
    const fileNameDisplay = document.getElementById('file-name');
    const uploadButton = document.querySelector('.file-upload-button');
    
    if (fileInput && uploadButton) {
        // Click custom button to trigger file input
        uploadButton.addEventListener('click', function() {
            fileInput.click();
        });
        
        // Update file name display when file is selected
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameDisplay.textContent = this.files[0].name;
                fileNameDisplay.style.color = '#2d5a27';
                fileNameDisplay.style.fontWeight = '600';
            } else {
                fileNameDisplay.textContent = 'No file selected';
                fileNameDisplay.style.color = '#666';
                fileNameDisplay.style.fontWeight = 'normal';
            }
        });
    }
    
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = this.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (field.type === 'file') {
                    if (!field.files || field.files.length === 0) {
                        isValid = false;
                        fileNameDisplay.style.color = '#c62828';
                    } else {
                        fileNameDisplay.style.color = '#2d5a27';
                    }
                } else if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#c62828';
                } else {
                    field.style.borderColor = '#e0e7e0';
                }
            });
            
            // Email validation
            const emailField = document.getElementById('email');
            if (emailField.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value)) {
                isValid = false;
                emailField.style.borderColor = '#c62828';
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields correctly.');
            }
        });
    }
    
    // Auto-hide messages after 5 seconds
    const messages = document.querySelectorAll('.message');
    if (messages.length > 0) {
        setTimeout(() => {
            messages.forEach(message => {
                message.style.opacity = '0';
                message.style.transition = 'all 0.5s ease';
                setTimeout(() => {
                    if (message.parentElement) {
                        message.parentElement.remove();
                    }
                }, 500);
            });
        }, 5000);
    }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>