/**
 * JavaScript version of functions.php
 */

// Session simulation using localStorage
class Session {
    static get(key) {
        try {
            const sessionData = JSON.parse(localStorage.getItem('ecostore_session')) || {};
            return sessionData[key];
        } catch (e) {
            return null;
        }
    }

    static set(key, value) {
        try {
            let sessionData = JSON.parse(localStorage.getItem('ecostore_session')) || {};
            sessionData[key] = value;
            localStorage.setItem('ecostore_session', JSON.stringify(sessionData));
        } catch (e) {
            console.error('Error setting session:', e);
        }
    }

    static remove(key) {
        try {
            let sessionData = JSON.parse(localStorage.getItem('ecostore_session')) || {};
            delete sessionData[key];
            localStorage.setItem('ecostore_session', JSON.stringify(sessionData));
        } catch (e) {
            console.error('Error removing from session:', e);
        }
    }
}

// Base URL
const BASE_URL = '/sanshang/513week7/';

// Check if user is logged in
function isLoggedIn() {
    return Session.get('logged_in') === true;
}

// Get username
function getUsername() {
    return Session.get('user_name') || Session.get('user_email') || 'Guest';
}

// Get user ID
function getUserId() {
    return Session.get('user_id') || 0;
}

// Sanitize input
function sanitize(input) {
    if (Array.isArray(input)) {
        return input.map(item => sanitize(item));
    }
    
    if (typeof input !== 'string') {
        return input;
    }
    
    // Remove whitespace
    let sanitized = input.trim();
    
    // Basic HTML escaping
    sanitized = sanitized
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    
    return sanitized;
}

// Validate email
function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

// Display success message
function displaySuccess() {
    const success = Session.get('success');
    if (success) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-success';
        alert.textContent = success;
        alert.style.cssText = `
            max-width: 1200px;
            margin: 15px auto;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: 500;
            background: #e8f5e9;
            color: #2d5a27;
            border-left: 4px solid #4a7c45;
        `;
        
        document.body.insertBefore(alert, document.body.firstChild);
        Session.remove('success');
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    }
}

// Display error message
function displayError() {
    const error = Session.get('error');
    if (error) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-error';
        alert.textContent = error;
        alert.style.cssText = `
            max-width: 1200px;
            margin: 15px auto;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: 500;
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        `;
        
        document.body.insertBefore(alert, document.body.firstChild);
        Session.remove('error');
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    }
}

// Redirect with message
function redirectWithMessage(url, message = '', type = 'success') {
    if (type === 'success') {
        Session.set('success', message);
    } else {
        Session.set('error', message);
    }
    window.location.href = url;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    displaySuccess();
    displayError();
    
    // Update user section in header if it exists
    const userSection = document.querySelector('.user-section');
    if (userSection) {
        if (isLoggedIn()) {
            userSection.innerHTML = `
                <span class="welcome-text">
                    Hi, ${getUsername()}
                </span>
                <a href="${BASE_URL}user/profile.php" class="nav-link">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="${BASE_URL}auth/logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            `;
        } else {
            userSection.innerHTML = `
                <a href="${BASE_URL}auth/login.php" class="nav-link">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="${BASE_URL}auth/register.php" class="nav-link">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            `;
        }
    }
});

// Export for use in other files
window.EcoStore = {
    Session,
    BASE_URL,
    isLoggedIn,
    getUsername,
    getUserId,
    sanitize,
    validateEmail,
    displaySuccess,
    displayError,
    redirectWithMessage
};