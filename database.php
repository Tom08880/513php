<?php
/**
 * Database Configuration for EcoStore
 */
class DatabaseConfig {
    const HOST = 'sql100.infinityfree.com';
    const USERNAME = 'if0_39943908';
    const PASSWORD = 'l3fA9Em7PP';
    const DATABASE = 'if0_39943908_wp16';
    const PORT = 3306;
}

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = 'mysql:host=' . DatabaseConfig::HOST . 
                   ';dbname=' . DatabaseConfig::DATABASE . 
                   ';port=' . DatabaseConfig::PORT . 
                   ';charset=utf8mb4';
            
            $this->connection = new PDO(
                $dsn,
                DatabaseConfig::USERNAME,
                DatabaseConfig::PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            header('Location: /errors/500.php');
            exit();
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public static function getConnectionStatic() {
        return self::getInstance()->getConnection();
    }
}

/**
 * Simple function to get database connection
 */
function getDB() {
    return Database::getConnectionStatic();
}

// Test connection (optional - remove in production)
try {
    $testConn = getDB();
} catch (Exception $e) {
    error_log("Database test failed: " . $e->getMessage());
}
?>