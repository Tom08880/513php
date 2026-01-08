<?php
class Security {
    
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    public static function validateEmail($email) {
        return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validatePassword($password) {
        if (empty($password)) return false;
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
        return preg_match($pattern, $password);
    }
    
    public static function validatePrice($price) {
        return is_numeric($price) && $price >= 0 && $price <= 999999.99;
    }
    
    public static function validateQuantity($quantity) {
        return is_numeric($quantity) && $quantity >= 0 && $quantity <= 9999;
    }
    
    public static function validateId($id) {
        return is_numeric($id) && $id > 0 && $id <= 999999999;
    }
    
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRFToken($token) {
        return !empty($_SESSION['csrf_token']) && !empty($token) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function escapeOutput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'escapeOutput'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    public static function generateSecureToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    public static function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
?>