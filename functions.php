<?php
// File: includes/functions.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define base URL
if (!defined('BASE_URL')) {
    define('BASE_URL', '/sanshang/513week7/');
}

// Simple login check
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Get username safely
function getUsername() {
    return $_SESSION['user_name'] ?? ($_SESSION['user_email'] ?? 'Guest');
}

// Get user ID safely
function getUserId() {
    return $_SESSION['user_id'] ?? 0;
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Please login to access this page.';
        header("Location: " . BASE_URL . "auth/login.php");
        exit();
    }
}

// Simple sanitize function
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input ?? ''), ENT_QUOTES, 'UTF-8');
}

// Display success message
function displaySuccess() {
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">';
        echo htmlspecialchars($_SESSION['success']);
        echo '</div>';
        unset($_SESSION['success']);
    }
}

// Display error message
function displayError() {
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-error">';
        echo htmlspecialchars($_SESSION['error']);
        echo '</div>';
        unset($_SESSION['error']);
    }
}

// Redirect with message
function redirectWithMessage($url, $message = '', $type = 'success') {
    if ($type === 'success') {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = $message;
    }
    header("Location: $url");
    exit();
}

// Simple email validation
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}